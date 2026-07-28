<?php

namespace Modules\Transaction\Entities;

use Modules\User\Entities\User;
use Modules\Coupon\Entities\Coupon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Modules\Transaction\Enum\TransactionPaidEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Modules\Transaction\Enum\TransactionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Transaction\Enum\TransactionPaymentForEnum;

/**
 * Modules\Transaction\Entities\Transaction
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $coupon_id
 * @property string|null $transaction_code
 * @property TransactionPaymentForEnum $payment_for 10 = diet | 20 = exercise | 30 = both them
 * @property TransactionStatusEnum $status 1 = success | 2 = pending | 0 = reject
 * @property TransactionPaidEnum $paid_by 1 = online | 2 card to card | 3 = by admin
 * @property int $cost
 * @property int $total_cost
 * @property int $discount
 * @property array|null $detail
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Coupon|null $coupon
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePaidBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePaymentFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTotalCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTransactionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUserId($value)
 * @method static Builder<static>|Transaction successful()
 * @property-read string $paid_by_badge_class
 * @property-read string $payment_for_badge_class
 * @property-read string $status_badge_class
 * @property-read string|null $subject_description
 * @property-read string $subject_title
 * @mixin \Eloquent
 */
class Transaction extends Model
{

    protected $guarded = ['id'];
    protected $casts = [
        'paid_by'     => TransactionPaidEnum::class,
        'payment_for' => TransactionPaymentForEnum::class,
        'status'      => TransactionStatusEnum::class,
        'detail'      => 'json',
    ];
    const DETAIL_KEY_INTEREST = 'interest';

    public function getPaymentForBadgeClassAttribute(): string
    {
        return match ($this->payment_for) {
            TransactionPaymentForEnum::DIET => 'bg-primary',
            TransactionPaymentForEnum::EXERCISE => 'bg-info',
            TransactionPaymentForEnum::DIET_AND_EXERCISE => 'bg-indigo',
            TransactionPaymentForEnum::COURSE => 'bg-success',
            default => 'bg-secondary',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            TransactionStatusEnum::SUCCESSFUL => 'bg-success',
            TransactionStatusEnum::PENDING => 'bg-warning text-dark',
            TransactionStatusEnum::REJECTED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getPaidByBadgeClassAttribute(): string
    {
        return match ($this->paid_by) {
            TransactionPaidEnum::ONLINE => 'bg-success',
            TransactionPaidEnum::CARD_TO_CARD => 'bg-orange',
            TransactionPaidEnum::BY_ADMIN => 'bg-primary',
            TransactionPaidEnum::BY_BAZAR => 'bg-purple',
            default => 'bg-secondary',
        };
    }

    public function getSubjectTitleAttribute(): string
    {
        if ($this->payment_for === TransactionPaymentForEnum::COURSE && !empty($this->detail['course_title'])) {
            return 'دوره: ' . $this->detail['course_title'];
        }

        return match ($this->payment_for) {
            TransactionPaymentForEnum::DIET => 'رژیم',
            TransactionPaymentForEnum::EXERCISE => 'برنامه ورزشی',
            TransactionPaymentForEnum::DIET_AND_EXERCISE => 'رژیم و برنامه ورزشی',
            TransactionPaymentForEnum::COURSE => 'دوره',
            default => 'نامشخص',
        };
    }

    public function getSubjectDescriptionAttribute(): ?string
    {
        if ($this->payment_for === TransactionPaymentForEnum::COURSE) {
            return $this->detail['source'] ?? null;
        }

        if (!empty($this->detail['package_id'])) {
            return 'بسته #' . $this->detail['package_id'];
        }

        return null;
    }

    #[Scope]
    protected function successful(Builder $query)
    {
        $query->where('status', TransactionStatusEnum::SUCCESSFUL);
    }
    public static function generateTransactionCode(): string
    {
        do {
            $uniqueCode = static::generateUniqueCode();
        } while (static::where('transaction_code', $uniqueCode)->exists());

        // Insert the unique code into the "transaction" table
        return $uniqueCode;
    }


    protected static function generateUniqueCode()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        // Generate a random code
        for ($i = 0; $i < 4; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    // Function to generate a unique code and insert a new transaction

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return  $this->belongsTo(User::class);
    }
    public function coupon(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return  $this->belongsTo(Coupon::class);
    }


    // Function to generate a unique code

    protected function asJson($value, $flags = 0)
    {
        $combinedFlags = $flags | JSON_UNESCAPED_UNICODE;
        $json = json_encode($value, $combinedFlags);
        return $json === false ? '' : $json;
    }
}
