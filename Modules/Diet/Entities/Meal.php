<?php

namespace Modules\Diet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Diet\app\Models\FoodConsumption;

/**
 * Modules\Diet\Entities\Meal
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\DietPlan> $dietPlans
 * @property-read int|null $diet_plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\DietRequestDetail> $dietRequests
 * @property-read int|null $diet_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\Food> $foods
 * @property-read int|null $foods_count
 * @property-read \Modules\Diet\Entities\FoodUnit|null $unit
 * @method static \Modules\Diet\Database\factories\MealFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Meal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Meal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Meal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Meal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meal whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meal whereUpdatedAt($value)
 * @property int $priority
 * @property string|null $icon_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\BasicFood> $basicFoods
 * @property-read int|null $basic_foods_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FoodConsumption> $foodConsumptions
 * @property-read int|null $food_consumptions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Meal whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Meal wherePriority($value)
 * @mixin \Eloquent
 */
class Meal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function maxOrder(): int
    {
        return self::max('priority') + 1;
    }

    protected static function newFactory()
    {
        return \Modules\Diet\Database\factories\MealFactory::new();
    }

    public function unit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FoodUnit::class);
    }

    public function foods(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Food::class,'foods_meals_pivot');
    }

    public function basicFoods(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(BasicFood::class,'basic_food_meals');
    }

    public function dietPlans(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(DietPlan::class, 'diet_plans_meals_pivot')
            ->withPivot('carb', 'protein', 'fat', 'fiber', 'calorie_percent');
    }

    public function foodConsumptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FoodConsumption::class);
    }

    public function dietRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DietRequestDetail::class);
    }
}
