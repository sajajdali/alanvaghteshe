<?php

namespace Modules\Onboarding\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Coupon\Entities\Coupon;
use Modules\Onboarding\Enum\OnboardingModeEnum;
use Modules\Package\Entities\Package;

/**
 * @property int $id
 * @property bool $is_active
 * @property OnboardingModeEnum $mode
 * @property int|null $package_id
 * @property int|null $coupon_id
 * @property array<array-key, mixed>|null $contents
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Coupon|null $coupon
 * @property-read Package|null $package
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingSetting whereContents($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingSetting whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingSetting whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingSetting whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingSetting wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OnboardingSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OnboardingSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'mode' => OnboardingModeEnum::class,
        'contents' => 'array',
    ];

    public const DEFAULT_CONTENTS = [
        'coupon' => [
            'badge' => 'پیشنهاد ویژه عضویت',
            'title' => '{name} جان، خوش اومدی!',
            'subtitle' => 'چون همین حالا عضو شدی، یه تخفیف ویژه برات فعال کردیم',
            'offer_title' => 'تخفیف روی خرید اشتراک',
            'coupon_label' => 'کد تخفیف اختصاصی تو',
            'scope_text' => 'روی همه رژیم‌ها اعمال می‌شه',
            'timer_text' => 'فرصت باقی‌مانده',
            'primary_button' => 'تخفیفم رو فعال کن',
            'secondary_button' => 'فعلاً نه، ادامه می‌دم',
        ],
        'free_package' => [
            'badge' => 'هدیه شروع مسیر',
            'title' => '{name} جان، خوش اومدی!',
            'offer_suffix' => 'روز استفاده کاملاً رایگان',
            'description' => 'بدون پرداخت، بدون کارت بانکی — اول نتیجه رو ببین، بعد تصمیم بگیر',
            'benefits_title' => 'تو این مدت همه‌چیز بازه:',
            'benefit_1' => 'برنامه غذایی روزانه و سبد خرید',
            'benefit_2' => 'گفتگو با مشاور تغذیه',
            'benefit_3' => 'پیگیری وزن و گزارش هفتگی',
            'timer_text' => 'تا فعال‌سازی رایگان',
            'primary_button' => '{days} روز رایگان رو فعال کن',
            'secondary_button' => 'فعلاً نه، ادامه می‌دم',
        ],
        'none' => [
            'title' => '{name} جان، خوش اومدی! مسیرت از همین‌جا شروع می‌شه',
            'description' => 'چند تا سؤال کوتاه می‌پرسیم تا رژیمی بسازیم که دقیقاً اندازه خودت باشه — نه کپی رژیم کس دیگه‌ای',
            'stat_1_value' => '۹۰ ثانیه',
            'stat_1_label' => 'تا ساخت رژیمت',
            'stat_2_value' => '+۱۲۰ هزار',
            'stat_2_label' => 'کاربر فعال',
            'stat_3_value' => '۴.۸',
            'stat_3_label' => 'امتیاز کاربران',
            'primary_button' => 'بزن بریم',
        ],
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate(
            ['id' => 1],
            [
                'is_active' => false,
                'mode' => OnboardingModeEnum::NONE,
                'contents' => self::DEFAULT_CONTENTS,
            ]
        );
    }

    public function getContentsWithDefaults(): array
    {
        return array_replace_recursive(self::DEFAULT_CONTENTS, $this->contents ?? []);
    }
}
