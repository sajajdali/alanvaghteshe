<?php

namespace Modules\Package\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Package\Enum\PackageTypeEnum;

/**
 * Modules\Package\Entities\Package
 *
 * @property int $id
 * @property string|null $name
 * @property int $days
 * @property int $price
 * @property int $special_price
 * @property bool $is_active
 * @property PackageTypeEnum $type
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $has_special_price
 * @property-read string $month
 * @method static \Illuminate\Database\Eloquent\Builder|Package active()
 * @method static \Illuminate\Database\Eloquent\Builder|Package diet()
 * @method static \Illuminate\Database\Eloquent\Builder|Package dietAndExercise()
 * @method static \Illuminate\Database\Eloquent\Builder|Package exercise()
 * @method static \Modules\Package\Database\factories\PackageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package priority()
 * @method static \Illuminate\Database\Eloquent\Builder|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereSpecialPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereUpdatedAt($value)
 * @method dietWithSupport()
 * @property int $support_price
 * @property array|null $detail
 * @method static \Illuminate\Database\Eloquent\Builder|Package dietAndExerciseWithSupport()
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereSupportPrice($value)
 * @mixin \Eloquent
 */
class Package extends Model
{
    use HasFactory;

    const JSON_DETAIL_SUGGESTED = 'suggested';
    protected $guarded = ['id'];

    protected $casts = [
        'type' => PackageTypeEnum::class,
        'is_active' => 'boolean',
        'detail' => 'json'
    ];

    protected static function newFactory(): \Modules\Package\Database\factories\PackageFactory
    {
        return \Modules\Package\Database\factories\PackageFactory::new();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDiet($query)
    {
        return $query->where('type', PackageTypeEnum::DIET);
    }

    public function scopeDietWithSupport($query)
    {
        return $query->where('type', PackageTypeEnum::DIET_WITH_SUPPORT);
    }

    public function scopeExercise($query)
    {
        return $query->where('type', PackageTypeEnum::EXERCISE);
    }

    public function scopeDietAndExercise($query)
    {
        return $query->where('type', PackageTypeEnum::DIET_AND_EXERCISE);
    }
    public function scopeDietAndExerciseWithSupport($query)
    {
        return $query->where('type', PackageTypeEnum::DIET_AND_EXERCISE_WITH_SUPPORT);
    }

    public function scopePriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    public function getPrice()
    {
        if ($this->getHasSpecialPriceAttribute()){
            return $this->special_price;
        }
        return  $this->price;
    }

    public function getHasSpecialPriceAttribute(): bool
    {
        return $this->special_price > 0;
    }

    public function getOriginalPrice()
    {
        return  $this->price;
    }

    public function getDiscountPackage(): int
    {
        if ($this->getHasSpecialPriceAttribute() && $this->price > $this->special_price){
            return $this->price - $this->special_price ;
        }
        return 0;
    }

    public function getMonthAttribute(): string
    {
        return $this->days / 30;
    }
    protected function asJson($value, $flags = 0)
    {
        $combinedFlags = $flags | JSON_UNESCAPED_UNICODE;
        $json = json_encode($value, $combinedFlags);
        return $json === false ? '' : $json;
    }

}
