<?php

namespace Modules\User\Enum;

use App\interface\EnumHasNameInterface;
use ReflectionClass;

enum UserMetaEnum: int implements EnumHasNameInterface
{
    case DIET_TYPE = 6;
    case GENDER = 5;

    case FIRST_NAME = 1;
    case LAST_NAME = 2;
    case AVATAR = 3;

    case CREATOR = 4;

    case BIRTHDAY = 7;
    case TALL = 8;
    case WEIGHT = 9;
    case TARGET_WEIGHT = 10;
    case TARGET_PLAN = 11;
    case BODY_FAT = 12;
    case DAILY_WATER_CONSUMPTION = 13;
    case BODY_PHYSICAL_STYLE = 14;
    case BODY_STYLE = 15;
    case TYPE_DAILY_WORK = 16;
    case DISEASES = 17;
    case FOOD_RESTRICTION = 18;
    case HABITS = 19;
    case WAKE_UP = 20;
    case HOW_MUCH_EXPERIENCE_SPORTS = 21;
    case TARGET_OF_EXERCISE = 22;
    case ACTIVITY_PER_WEEK = 23;
    case HOW_MANY_DAYS_WEEK_EXERCISE = 24;
    case ROUTE = 25;
    case DIET_PLAN = 26;
    case WEAKNESSES_BODY = 27;
    case ATHLETE_OR_NOT = 28;
    case FOOD_ALLERGY = 29;
    case WEIGHT_CHANGE_PER_WEEK = 30;
    case FAVORITE_RECIPE = 31;
    case FAVORITE_BASIC_FOODS = 32;
    case INVITATION_CODE = 33;
    case REQUEST_PAYMENT_INVITING_FRIENDS = 34;
    case NEWLY_REGISTERED = 35;
    case POPUP = 36;
    case CURRENT_WEIGHT = 37;
    case REFERRAL_PERCENTAGE = 38;
    case TELEGRAM_CHAT_ID = 39;
    case LAST_SUPPORTER_CALLED = 40;
    case APPLY_TO_ALL_PAYMENTS = 41;
    case WEIGHT_LOSS_MEDICATION = 42;
    case FOOD_BUDGET = 43;


    public static function keys(): array
    {
        $reflection = new ReflectionClass(__CLASS__);
        return $reflection->getConstants();
    }

    public function getName(): string
    {
        return match ($this) {
            self::DIET_TYPE => 'هدف کلی',
            self::GENDER => 'جنسیت',
            self::ACTIVITY_PER_WEEK => 'میزان فعالیت شما در هفته',
            self::ATHLETE_OR_NOT => 'ورزشکار هستید یا خیر',
            self::BIRTHDAY => 'تاریخ تولد',
            self::FOOD_RESTRICTION => 'محدودیت غذایی',
            self::FOOD_ALLERGY => 'محدودیت غذایی',
            self::TALL => 'قد',
            self::CURRENT_WEIGHT => 'وزن فعلی',
            self::WEIGHT => 'وزن',
            self::TARGET_WEIGHT => 'وزن هدف',
            self::WEIGHT_CHANGE_PER_WEEK => 'میزان تغییر وزن در هفته',
            self::WEIGHT_LOSS_MEDICATION => 'استفاده از داروهای کاهش وزن',
            self::FOOD_BUDGET => 'بودجه برنامه غذایی',

            self::FIRST_NAME => 'نام',
            self::LAST_NAME => 'نام خانوادگی',
            self::AVATAR => 'عکس پروفایل',
            self::TARGET_PLAN => 'روش رسیدن به هدف',
            self::DIET_PLAN => 'رژیم انتخابی',
            self::BODY_FAT => 'درصد چربی',
            self::DAILY_WATER_CONSUMPTION => 'میزن مصرف مایعات',
            self::BODY_PHYSICAL_STYLE => 'استایل بدنی',
            self::BODY_STYLE => 'استایل بدنی',
            self::TYPE_DAILY_WORK => 'نوع کار روزانه',
            self::DISEASES => 'بیماری ها',
            self::HABITS => 'عادت های اشتباه',
            self::WAKE_UP => 'میزان خواب',
            self::HOW_MUCH_EXPERIENCE_SPORTS => 'چند وقت ورزش میکنید',
            self::TARGET_OF_EXERCISE => 'هدف شما از ورش کردن',
            self::HOW_MANY_DAYS_WEEK_EXERCISE => 'چند روز در هفته ورزش میکنید',
            self::WEAKNESSES_BODY => 'نقاط ضعف بدن',
            self::FAVORITE_RECIPE => 'دستور پخت ها مورد علاقه',
            self::FAVORITE_BASIC_FOODS => 'غذاهای  پایه مورد علاقه',
            self::REFERRAL_PERCENTAGE => 'درصد به ازای هر معرفی',
            self::TELEGRAM_CHAT_ID => 'کد یکتای تلگرام',
            self::APPLY_TO_ALL_PAYMENTS => 'دریافت درصد از همه پرداخت ها',
            self::LAST_SUPPORTER_CALLED => 'اخرین پشتیبانی  که تماس پیگیری انجام داده'
        };
    }
    public function getOptionName($value): string
    {
        return $this->getOptions()[$value] ?? $value ?? "";
    }

    public  function getOptions(): ?array
    {
        return match ($this) {
            self::DIET_TYPE => ['0' => 'کاهش وزن' ,'1' => 'افزایش وزن','2' => 'تثبیت وزن'],
            self::GENDER => ['0' => 'مرد', '1' => 'زن'],
            self::ATHLETE_OR_NOT => ['0' => 'ورزشکار نیستم', '1' => 'ورزشکار هستم'],
            self::TARGET_PLAN => ['10' => 'رژیم' ,'20' => 'ورزش','30' => 'رژیم + ورزش'],
            self::BODY_PHYSICAL_STYLE => ['0' => 'اکتومورف', '1' => 'مزومرف' , '2' => 'اندومرف'],
            self::TYPE_DAILY_WORK => ['0' => 'اکثر مواقع نشته ام', '1' => 'اکثر مواقع ایستاده ام' , '2' => 'در خانه کار میکنم', '3' => 'سیار فعال هستم'],
            self::DAILY_WATER_CONSUMPTION => ['0' => 'فقط چای و قهوه', '1' => '۲ تا ۴ لیوان آب' , '2' => '۴ تا ۶ لیوان آب', '3' => ' بیشتر از ۶ لیوان'],
            self::FOOD_RESTRICTION => ['0' => 'گیاه خواری', '1' => 'وگان' , '2' => 'بدون لاکتوز', '3' => 'بدون ماهی' ,  '5' => 'کتوژنیک' , '4' => 'من تقریبا همه چیز میخورم' ],
            self::FOOD_ALLERGY => ['0' => 'ماهی', '1' => 'سویا' , '2' => 'باقلا', '3' => 'کبد چرب' ,  '5' => 'بادمجان' , '4' => 'بادام زمینی' ],
            self::WEAKNESSES_BODY => ['0' => 'سینه ها', '1' => 'بازو ها' , '2' => 'پاهای لاغر', '3' => 'شکم چاق' , '4' => ' هیچ کدام از مشکالات بالا را ندارم'],
            self::HABITS => ['0' => 'دخانیات', '1' => 'نوشابه' , '2' => 'غذای شور', '3' => 'غذای چرب' , '4' => ' الکل' , '5' => 'شیرینی جات' , '6' => 'شب بیداری' , '7' => 'هیچدام را استفاده نمیکنم'],
            self::WAKE_UP => ['0' => ' کمتر از ۵', '1' => 'بین ۵ تا ۷' , '2' => ' بین ۷ تا ۸', '3' => 'بیشتر از ۸'],
            self::HOW_MUCH_EXPERIENCE_SPORTS => ['0' => 'تازه میخوام شروع کنم', '1' => 'کمتر از ۳ ماه' , '2' => 'بیشتر از ۳ ماه'],
            self::TARGET_OF_EXERCISE => ['0' => 'کات و تفکیک عضلات', '1' => 'افزایش توده عضلات' , '2' => 'افزایش قدرت و حجم عضلات'],
            self::ACTIVITY_PER_WEEK => ['0' =>  'فعالیت فیزیکی بسیار کم', '1' => 'فعالیت فیزیکی کم' , '2' => 'فعالیت فیزیکی متوسط' , '3' => ' فعالیت فیزیکی زیاد' , '4' =>  'فعالیت فیزیکی بسیار زیاد'],
            self::HOW_MANY_DAYS_WEEK_EXERCISE => ['0' => '۱ بار', '1' => ' ۲ بار' , '2' => ' ۳ بار' , '3' => ' ۴ بار' , '4' =>  '۵ بار' , '6' => ' ۶ بار'] ,
            self::DIET_PLAN => ['0' => ' کاهش وزن', '1' => 'افزایش وزن' , '2' => 'تثبیت وزن'] ,
            self::DISEASES => [ '' =>'بدون مقدار'] ,
            self::WEIGHT_LOSS_MEDICATION => ['0' => 'استفاده نمی‌کنم', '1' => 'از داروهای کاهش وزن استفاده می‌کنم'],
            self::FOOD_BUDGET => ['0' => 'اقتصادی و به‌صرفه', '1' => 'متعادل و متنوع', '2' => 'هزینه برام مهم نیست'],
            self::WEIGHT_CHANGE_PER_WEEK => ['0' => 'استاندارد', '1' => 'سریع‌تر', '2' => 'خیلی سریع'],
            default => null
        };
    }
}
