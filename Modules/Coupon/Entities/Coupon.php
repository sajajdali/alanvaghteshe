<?php

namespace Modules\Coupon\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Coupon\Enum\CouponCanUsedForEnum;
use Modules\Transaction\Entities\Transaction;

/**
 * Modules\Coupon\Entities\Coupon
 *
 * @property int $id
 * @property string $title
 * @property string $code
 * @property int $value
 * @property int $is_percent 0 => fixed , 1=> perecent
 * @property int|null $usable_count
 * @property int|null $minimum_spend
 * @property int|null $maximum_spend
 * @property int|null $used
 * @property bool $active
 * @property CouponCanUsedForEnum $can_used_for diet = 10 | exercise = 20 |diet and  exercise = 30
 * @property \Illuminate\Support\Carbon|null $start_at
 * @property \Illuminate\Support\Carbon|null $end_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Transaction> $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon active()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCanUsedFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereIsPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereMaximumSpend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereMinimumSpend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUsableCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon withoutTrashed()
 * @mixin \Eloquent
 */
class Coupon extends Model
{
    use HasFactory , SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
        'is_present' => 'boolean',
        'start_at' => 'date',
        'end_at' => 'date',
        'active' => 'boolean',
        'can_used_for'  => CouponCanUsedForEnum::class
    ];


    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }


}
