<?php

namespace Modules\Onboarding\Enum;

enum OnboardingModeEnum: string
{
    case NONE = 'none';
    case FREE_PACKAGE = 'free_package';
    case COUPON = 'coupon';

    public function getName(): string
    {
        return match ($this) {
            self::NONE => 'ادامه عادی (بدون پیشنهاد ویژه)',
            self::FREE_PACKAGE => 'پکیج رایگان',
            self::COUPON => 'کد تخفیف',
        };
    }
}
