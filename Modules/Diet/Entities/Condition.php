<?php

namespace Modules\Diet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Diet\Enum\ConditionApplyItemEnum;
use Modules\Diet\Enum\ConditionKeyEnum;

/**
 * Modules\Diet\Entities\Condition
 *
 * @property int $id
 * @property ConditionKeyEnum $key
 * @property array|null $options
 * @property array|null $apply_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\BasicFood> $basicFoods
 * @property-read int|null $basic_foods_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\DietPlan> $dietPlans
 * @property-read int|null $diet_plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\DietRequest> $dietRequests
 * @property-read int|null $diet_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\Food> $foods
 * @property-read int|null $foods_count
 * @method static \Illuminate\Database\Eloquent\Builder|Condition basicFood()
 * @method static \Illuminate\Database\Eloquent\Builder|Condition dietPlan()
 * @method static \Illuminate\Database\Eloquent\Builder|Condition dietRequest()
 * @method static \Modules\Diet\Database\factories\ConditionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Condition food()
 * @method static \Illuminate\Database\Eloquent\Builder|Condition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Condition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Condition query()
 * @method static \Illuminate\Database\Eloquent\Builder|Condition whereApplyTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Condition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Condition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Condition whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Condition whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Condition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Condition extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'key' => ConditionKeyEnum::class,
        'options' => 'array',
        'apply_to' => 'array',
    ];

    public static function coditionValue($key,$itemsKey) :string
    {
        $options =  Condition::where('key', $key)?->first()?->options;
        if (isset($options['items']['values'][$itemsKey])) {
            return $options['items']['values'][$itemsKey];
        }else {
            return 'مقدار یافت نشد' ;
        }
    }

    public static function foodRestriction(): \Illuminate\Database\Eloquent\Builder|Condition
    {
        return self::where('key', ConditionKeyEnum::FOOD_RESTRICTION->value);
    }

    public static function bodyStyle(): \Illuminate\Database\Eloquent\Builder|Condition
    {
        return self::where('key', ConditionKeyEnum::BODY_STYLE->value);
    }

    protected static function newFactory(): \Modules\Diet\Database\factories\ConditionFactory
    {
        return \Modules\Diet\Database\factories\ConditionFactory::new();
    }

    public function scopeFood($query)
    {
        return $query->whereJsonContains('apply_to->' . ConditionApplyItemEnum::FOOD->value, true);
    }

    public function scopeBasicFood($query)
    {
        return $query->whereJsonContains('apply_to->' . ConditionApplyItemEnum::FOOD->value, true);
    }

    public function scopeDietPlan($query)
    {
        return $query->whereJsonContains('apply_to->' . ConditionApplyItemEnum::DIET_PLAN->value, true);
    }

    public function scopeDietRequest($query)
    {
        return $query->whereJsonContains('apply_to->' . ConditionApplyItemEnum::DIET_REQUEST->value, true);
    }

    public function foods(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Food::class, 'conditionable', 'conditions_pivot')->withPivot('options');
    }

    public function basicFoods(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(BasicFood::class, 'conditionable', 'conditions_pivot')->withPivot('options');
    }

    public function dietPlans(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(DietPlan::class, 'conditionable', 'conditions_pivot')->withPivot('options');
    }

    public function dietRequests(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(DietRequest::class, 'conditionable', 'conditions_pivot')->withPivot('options');
    }

    protected function asJson($value, $flags = 0)
    {
        $combinedFlags = $flags | JSON_UNESCAPED_UNICODE;
        $json = json_encode($value, $combinedFlags);
        return $json === false ? '' : $json;
    }
}
