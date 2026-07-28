<?php

namespace Modules\Diet\Entities;

use Cache;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use JsonSerializable;
use Modules\Api\Enum\CacheEnum;
use Modules\Diet\app\Models\FoodConsumption;
use Modules\Diet\Enum\BasicFoodTypeEnum;
use Modules\Diet\Enum\MainNutritionEnum;
use Modules\Recipe\app\Models\Recipe;


/**
 * Modules\Diet\Entities\BasicFood
 *
 * @property int $id
 * @property int|null $food_unit_id
 * @property string $name
 * @property int $carb
 * @property int $protein
 * @property int $fat
 * @property int $fiber
 * @property int $calories
 * @property BasicFoodTypeEnum $type
 * @property int $calories_per_gram
 * @property int $calories_per_unit
 * @property int $quantity_per_unit
 * @property MainNutritionEnum $main_nutrition
 * @property int $active
 * @property string|null $recipe
 * @property array|null $detail
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\Condition> $conditions
 * @property-read int|null $conditions_count
 * @property-read \Modules\Diet\Entities\FoodUnit|null $foodUnit
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood active()
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood query()
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereCalories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereCaloriesPerGram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereCaloriesPerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereCarb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereFat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereFiber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereFoodUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereMainNutrition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereProtein($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereQuantityPerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereRecipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereUpdatedAt($value)
 * @property array|null $food_fact
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FoodConsumption> $FoodConsumptions
 * @property-read int|null $food_consumptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\FoodCategory> $category
 * @property-read int|null $category_count
 * @property-read \Modules\Diet\Entities\DietRequestDetail|null $dietRequestDetails
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Recipe> $recipes
 * @property-read int|null $recipes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\FoodUnit> $units
 * @property-read int|null $units_count
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood carb()
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood fat()
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood fiber()
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood mainNutritionIs($mainNutrition)
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood protein()
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood simple()
 * @method static \Illuminate\Database\Eloquent\Builder|BasicFood whereFoodFact($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\Food> $foods
 * @property-read int|null $foods_count
 * @mixin \Eloquent
 */
class BasicFood extends Model implements JsonSerializable
{

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($basicFood) {
            // Clean up related tables before deleting the basic food
            try {
                DB::table('recipe_basic_foods')->where('basic_food_id', $basicFood->id)->delete();
                DB::table('basic_food_pivot')->where('basic_food_id', $basicFood->id)->delete();
                DB::table('basic_food_units')->where('basic_food_id', $basicFood->id)->delete();
            } catch (\Exception $e) {
                // Log the exception or handle it if necessary
                \Log::error('Failed to delete related data for BasicFood ID ' . $basicFood->id . ': ' . $e->getMessage());
            }
        });
    }

    public function foods(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            \Modules\Diet\Entities\Food::class,
            'basic_food_pivot',
            'basic_food_id',
            'food_id'
        );
    }
    const JSON_DETAIL_KEY_KARAFS = 'karafs';

    protected $casts = [
        'type' => BasicFoodTypeEnum::class,
        'main_nutrition' => MainNutritionEnum::class,
        'detail' => 'json',
        'food_fact' => 'json',
    ];
    protected $table = 'basic_foods';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::created(function ($basicFood) {
            self::clearCategoryCaches();
        });

        static::updated(function ($basicFood) {
            self::clearCategoryCaches();
        });

        static::deleted(function ($basicFood) {
            self::clearCategoryCaches();
        });
    }

    protected static function clearCategoryCaches(): void
    {
        Cache::tags([
            CacheEnum::BASIC_FOODS->getTagName(),
            'diet_food_page',
        ])->flush();
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('active', true);
    }

    public function scopeSimple(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where('type', BasicFoodTypeEnum::SIMPLE);
    }

    public function scopeProtein($query)
    {
        return $query->where('main_nutrition', 'protein');
    }

    public function scopeCarb($query)
    {
        return $query->where('main_nutrition', 'carb');
    }

    public function scopeFat($query)
    {
        return $query->where('main_nutrition', 'fat');
    }

    public function scopeFiber($query)
    {
        return $query->where('main_nutrition', 'fat');
    }

    public function conditions(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Condition::class, 'conditionable', 'conditions_pivot')->withPivot('options');
    }

    public function dietRequestDetails()
    {
        return $this->morphOne(DietRequestDetail::class,'foodable');
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(FoodUnit::class, 'basic_food_units', 'basic_food_id', 'food_unit_id')->withPivot('quantity_per_unit', 'max_allowed','is_primary')->withTimestamps();
    }

//    public function unitsPivot(): BelongsToMany
//    {
//        return $this->belongsToMany(FoodUnit::class, 'basic_food_units', 'basic_food_id', 'food_unit_id')->withPivot('quantity_per_unit', 'max_allowed')->withTimestamps();
//    }

    public function category(): BelongsToMany
    {
        return $this->belongsToMany(FoodCategory::class , 'basic_food_category' )->withTimestamps();
    }
    public function scopeMainNutritionIs($query, $mainNutrition)
    {
        return $query->where('main_nutrition', $mainNutrition);
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_basic_foods', 'basic_food_id', 'recipe_id')->withPivot('food_unit_id' , 'quantity_per_unit')->withTimestamps();
    }

    public function FoodConsumptions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(FoodConsumption::class, 'consumable');
    }

    protected function asJson($value, $flags = 0)
    {
        $combinedFlags = $flags | JSON_UNESCAPED_UNICODE;
        $json = json_encode($value, $combinedFlags);
        return $json === false ? '' : $json;
    }
}
