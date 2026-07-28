<?php

namespace Modules\Admin\app\Enums;

use Modules\Admin\app\Enums\ActivityEventEnum;

enum UserConditionFiltersEnum: int
{
    case PACKAGE_END_ONE_DAY = 1;
    case PACKAGE_END = 2;
    case PACKAGE_END_TEN_DAYS_AGO = 3;
    case PACKAGE_END_TEN_MONTH_AGO = 4;

    //  diets

    case DIET_END_ONE_DAY = 20;
    case DIET_END_YESTERDAY = 23;
    case DIET_START_SEVEN_DAYS_AGO = 21;
    case DIET_START_FOURTEEN_DAYS_AGO = 22;
    case DIET_END_ONE_WEEK_AGO = 24;
//    case DIET_END_TEN_DAYS_AGO = 23;


    public function getName(): string
    {
        return match ($this) {
            self::PACKAGE_END_ONE_DAY => 'کسانی که ۱ روز مانده پکیجشون تموم بشه',
            self::PACKAGE_END => 'کسانی که پکیجشون تمام شده و تمدید نکردن',
            self::PACKAGE_END_TEN_DAYS_AGO => 'کسانی که ۱۰ روز پیش پکیجشان تمام شده  و تمدید نکردن',
            self::PACKAGE_END_TEN_MONTH_AGO => 'کسانی که ۱ ماه پیش پکیجشان تمام شده و تمدید نکردن',


            // diet
            self::DIET_END_ONE_DAY => 'کسانی که امروز رژیمشان تمام میشود',
            self::DIET_START_SEVEN_DAYS_AGO => 'کسانی که ۷ روز از رژیمشان گذشته است',
            self::DIET_START_FOURTEEN_DAYS_AGO => 'کسانی که ۱۴ روز از رژیمشان گذشته است',
            self::DIET_END_YESTERDAY => 'کسانی که دیروز رژیمشان تمام شده است',
            self::DIET_END_ONE_WEEK_AGO => 'کسانی که یک هفته است که رژیمشان تمام شده است',
        };
    }
    public function getEvent(): ActivityEventEnum
    {
        return match ($this) {
            self::PACKAGE_END_ONE_DAY => ActivityEventEnum::CALL_ONEDAY_LEFT_PACKAGE_EXPIRED,
            self::PACKAGE_END => ActivityEventEnum::CALL_EX_PACKAGE,
            self::PACKAGE_END_TEN_DAYS_AGO => ActivityEventEnum::CALL_EX_PACKAGE_TEN_DAYS_PASS,
            self::PACKAGE_END_TEN_MONTH_AGO => ActivityEventEnum::CALL_EX_PACKAGE_MONTH_DAYS_PASS,

            // diets
            self::DIET_END_ONE_DAY => ActivityEventEnum::DIET_END_ONE_DAY,
            self::DIET_START_SEVEN_DAYS_AGO => ActivityEventEnum::DIET_START_SEVEN_DAYS_AGO,
            self::DIET_START_FOURTEEN_DAYS_AGO => ActivityEventEnum::DIET_START_SEVEN_DAYS_AGO,
        };
    }
}
