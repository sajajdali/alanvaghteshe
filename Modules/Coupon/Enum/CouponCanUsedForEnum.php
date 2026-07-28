<?php

namespace Modules\Coupon\Enum;

enum CouponCanUsedForEnum: int
{
    case DIET = 10;
    case EXERCISE = 20;
    case DIET_AND_EXERCISE = 30;
    case COURSE = 40;

    public function getName(): string
    {
        return match ($this) {
            self::DIET => 'رژیم',
            self::EXERCISE => 'برنامه ورزشی',
            self::DIET_AND_EXERCISE => 'رژیم و برنامه ورزشی',
            self::COURSE => 'دوره',
        };
    }
}
