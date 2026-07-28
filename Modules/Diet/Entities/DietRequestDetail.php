<?php

namespace Modules\Diet\Entities;

use Modules\Diet\Entities\BasicFood;
use Illuminate\Database\Eloquent\Model;
use Modules\Diet\Enum\MainNutritionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modules\Diet\Entities\DietRequestDetail
 *
 * @property int $id
 * @property int $diet_request_id
 * @property int $meal_id
 * @property string $foodable_type
 * @property int $foodable_id
 * @property int $carb
 * @property int $protein
 * @property int $fat
 * @property int $fiber
 * @property int $calories
 * @property int $day_number
 * @property int|null $replaced_parent_id
 * @property int $is_done
 * @property MainNutritionEnum $main_nutrition
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\Diet\Entities\DietRequest $dietRequest
 * @property-read \Modules\Diet\Entities\Food|null $food
 * @property-read \Modules\Diet\Entities\Meal $meal
 * @method static \Modules\Diet\Database\factories\DietRequestDetailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereCalories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereCarb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereDayNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereDietRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereFat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereFiber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereFoodableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereFoodableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereIsDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereMainNutrition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereMealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereProtein($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereReplacedParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereUpdatedAt($value)
 * @property \Illuminate\Support\Carbon|null $date_of_day
 * @property float|null $calculated_value
 * @property float $number_of_unit
 * @property bool $cheat_meal
 * @property string|null $detail
 * @property-read Model|\Eloquent $foodClass
 * @property-read Model|\Eloquent $foodable
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail cheatMeals()
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereCalculatedValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereCheatMeal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereDateOfDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietRequestDetail whereNumberOfUnit($value)
 * @mixin \Eloquent
 */
class DietRequestDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'main_nutrition' => MainNutritionEnum::class,
        'cheat_meal' => 'boolean',
        'date_of_day' => 'date',
        'is_done' => 'boolean',
    ];

    public function scopeCheatMeals($query)
    {
        return $query->where('cheat_meal', true);
    }

    protected static function newFactory(): \Modules\Diet\Database\factories\DietRequestDetailFactory
    {
        return \Modules\Diet\Database\factories\DietRequestDetailFactory::new();
    }

    public function dietRequest()
    {
        return $this->belongsTo(DietRequest::class , 'diet_request_id');
    }

    public function meal(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }

    public function foodable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('foodable');
    }

    public function foodClass()
    {
        //return the basicFood or food class relation ;
        return $this->morphTo('foodable');
    }

}
