<?php

namespace Modules\Diet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Diet\Enum\FoodTypeEnum;

/**
 * Modules\Diet\Entities\DietPlan
 *
 * @property int $id
 * @property string $name
 * @property bool $status
 * @property int $day_count
 * @property int $reduced_calories
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\Condition> $conditions
 * @property-read int|null $conditions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\DietRequest> $dietRequests
 * @property-read int|null $diet_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\Meal> $meals
 * @property-read int|null $meals_count
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan active()
 * @method static \Modules\Diet\Database\factories\DietPlanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan whereDayCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan whereReducedCalories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan whereUpdatedAt($value)
 * @property int $general_pattern
 * @property FoodTypeEnum $food_type 10 = simple | 20 = combined
 * @property array|null $detail
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan generalPattern()
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan specificPattern()
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan whereFoodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DietPlan whereGeneralPattern($value)
 * @mixin \Eloquent
 */
class DietPlan extends Model
{
    use HasFactory;
    const DETAIL_SPECIAL_BASIC_FOODS = 'special_basic_foods';
    const DETAIL_HANDWRITTEN = 'handwritten';
    const DETAIL_GENERAL_PATTERN = 'general_pattern';
    const DETAIL_APPLICATION = 'application';
    const DETAIL_FASTING = 'fasting';
    const IS_ATHLETE_MEAL = 'is_athlete_meal';

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
        'food_type' => FoodTypeEnum::class,
        'detail' => 'json'
    ];

    protected static function newFactory(): \Modules\Diet\Database\factories\DietPlanFactory
    {
        return \Modules\Diet\Database\factories\DietPlanFactory::new();
    }

    public function scopeActive($query)
    {
        return $query->whereStatus(true);
    }

    public function scopeGeneralPattern($query)
    {
        return $query->where('general_pattern', 1);
    }

    /**
     * Scope for general_pattern = 0
     */
    public function scopeSpecificPattern($query)
    {
        return $query->where('general_pattern', 0);
    }
    public function meals(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'diet_plans_meals_pivot')
            ->withPivot('carb', 'protein', 'fat', 'fiber', 'calorie_percent');
    }

    public function dietRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DietRequest::class);
    }

    public function conditions(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Condition::class,'conditionable','conditions_pivot')->withPivot('options' , 'condition_id');
    }
    protected function asJson($value, $flags = 0)
    {
        $combinedFlags = $flags | JSON_UNESCAPED_UNICODE;
        $json = json_encode($value, $combinedFlags);
        return $json === false ? '' : $json;
    }
}
