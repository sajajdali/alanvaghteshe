<?php

namespace Modules\Diet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Diet\app\Models\FoodConsumption;


/**
 * Modules\Diet\Entities\FoodUnit
 *
 * @property int $id
 * @property string $name
 * @property array|null $detail
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\BasicFood> $basicFoods
 * @property-read int|null $basic_foods_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, FoodConsumption> $foodConsumption
 * @property-read int|null $food_consumption_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\Food> $foods
 * @property-read int|null $foods_count
 * @method static \Illuminate\Database\Eloquent\Builder|FoodUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodUnit whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodUnit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodUnit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FoodUnit extends Model
{
    use HasFactory;

    protected $casts = ['detail' => 'json' , 'is_primary' => 'boolean'];
    protected $guarded = ['id'];



    public function foods(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Food::class);
    }

    public function basicFoods(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BasicFood::class);
    }

    public function foodConsumption(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FoodConsumption::class);
    }
    public function isPrimary(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_primary', true);
    }
}
