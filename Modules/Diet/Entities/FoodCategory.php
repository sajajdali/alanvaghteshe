<?php

namespace Modules\Diet\Entities;

use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Api\Enum\CacheEnum;

/**
 * Modules\Diet\Entities\FoodCategory
 *
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\Diet\Entities\BasicFood> $basicFoods
 * @property-read int|null $basic_foods_count
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FoodCategory whereUuid($value)
 * @mixin \Eloquent
 */
class FoodCategory extends Model
{
    use HasFactory;

    protected $table = 'food_categories';

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::created(function ($foodCategory) {
            Cache::forget(CacheEnum::FOOD_CATEGORY->value);
        });

        static::updated(function ($foodCategory) {
            Cache::forget(CacheEnum::FOOD_CATEGORY->value);
        });

        static::deleted(function ($foodCategory) {
            Cache::forget(CacheEnum::FOOD_CATEGORY->value);
        });
    }

    public function basicFoods(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(BasicFood::class , 'basic_food_category' , 'food_category_id' , 'basic_food_id');
    }
}
