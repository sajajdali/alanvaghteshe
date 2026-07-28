<?php

namespace Modules\Reminder\Enum;

use App\interface\EnumHasNameInterface;
use App\trait\EnumFunctionTrait;

enum ReminderTypeEnum : int implements EnumHasNameInterface
{
    use EnumFunctionTrait ;

    case DIET = 1;
    case EXERCISE = 2;
    case REGISTRATION = 3;
    case PAYMENT = 4;

    public function getName(): string
    {
        return match($this) {
            self::DIET => 'رژیم',
            self::EXERCISE => 'ورزش‌ها',
            self::REGISTRATION => 'ثبت‌نام',
            self::PAYMENT => ' پرداخت',
        };
    }

    /**
     * نمایش با در نظر گرفتن شماره جلسه (فقط برای رژیم و ورزش)
     */
    public function getDisplayName(?int $sessionNumber = null): string
    {
        return match($this) {
            self::DIET, self::EXERCISE =>
                $this->getName() . ($sessionNumber ? " (جلسه {$sessionNumber})" : ''),
            default => $this->getName(),
        };
    }

    public function getWireModelName(): string
    {
        return match($this) {
            self::DIET => 'diet',
            self::EXERCISE => 'exercise',
            self::REGISTRATION => 'registration',
            self::PAYMENT => 'payment',
        };
    }
}
