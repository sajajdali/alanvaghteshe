<?php

namespace Modules\Diet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Diet\app\Models\FoodConsumption;
use Modules\Diet\Enum\FoodTypeEnum;
use Modules\Recipe\app\Models\Recipe;

/**
 * Modules\Diet\Entities\Food
 *
 * @property int $id
 * @property string $name
 * @property int $carb
 * @property int $protein
 * @property int $fat
 * @property int $fiber
 * @property int $calories
 * @property FoodTypeEnum $type
 * @property string|null $recipe
 * @property array|null $detail
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\BasicFood> $basicFoodPivot
 * @property-read int|null $basic_food_pivot_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\Condition> $conditions
 * @property-read int|null $conditions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\DietRequestDetail> $dietRequests
 * @property-read int|null $diet_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\Meal> $meals
 * @property-read int|null $meals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Food> $relatedBy
 * @property-read int|null $related_by_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Food> $relatedTo
 * @property-read int|null $related_to_count
 * @method static \Illuminate\Database\Eloquent\Builder|Food combined()
 * @method static \Modules\Diet\Database\factories\FoodFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Food newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Food newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Food query()
 * @method static \Illuminate\Database\Eloquent\Builder|Food simple()
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereCalories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereCarb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereFat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereFiber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereProtein($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereRecipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereUpdatedAt($value)
 * @property int|null $parent_id
 * @property float $max_carb
 * @property float $max_protein
 * @property float $max_fat
 * @property float $max_fiber
 * @property int $max_calories
 * @property int|null $max_per_unit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FoodConsumption> $FoodConsumptions
 * @property-read int|null $food_consumptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Food> $children
 * @property-read int|null $children_count
 * @property-read \Modules\Diet\Entities\DietRequestDetail|null $dietRequestDetails
 * @property-read Food|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Recipe> $recipes
 * @property-read int|null $recipes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereMaxCalories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereMaxCarb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereMaxFat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereMaxFiber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereMaxPerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereMaxProtein($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Food withoutParent()
 * @mixin \Eloquent
 */
class Food extends Model
{
    use HasFactory;

    const detail_replace_foods_type = 'replace_foods_type';

    protected $guarded = ['id'];

    protected $table = 'foods';

    protected $casts = [
        'type' => FoodTypeEnum::class,
//        'main_nutrition' => MainNutritionEnum::class,
        'detail' => 'json',
    ];

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Food::class, 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Food::class, 'parent_id');
    }

    public function scopeWithoutParent($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSimple($query)
    {
        return $query->whereType(FoodTypeEnum::SIMPLE);
    }

    public function scopeCombined($query)
    {
        return $query->whereType(FoodTypeEnum::COMBINED);
    }

    public function conditions(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Condition::class, 'conditionable', 'conditions_pivot')->withPivot('options');
    }

    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'foods_meals_pivot');
    }

    public function relatedTo(): BelongsToMany
    {
        return $this->belongsToMany(Food::class, 'foods_related_pivot', 'food_id', 'related_id')->withPivot('main_nutrition' , 'type');
    }

    public function relatedBy(): BelongsToMany
    {
        return $this->belongsToMany(Food::class, 'foods_related_pivot', 'related_id', 'food_id')->withPivot('main_nutrition' , 'type');
    }

    public function dietRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DietRequestDetail::class);
    }

    public function basicFoodPivot(): BelongsToMany
    {
        return $this->belongsToMany(BasicFood::class, 'basic_food_pivot', 'food_id', 'basic_food_id')
            ->withPivot('quantity', 'maximum', 'extend','id')
            ->withTimestamps();

    }

    public function dietRequestDetails()
    {
        return $this->morphOne(DietRequestDetail::class,'foodable');
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


    public function recipes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}
