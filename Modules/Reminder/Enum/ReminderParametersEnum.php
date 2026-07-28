<?php

namespace Modules\Reminder\Enum;

use App\interface\EnumHasNameInterface;

enum ReminderParametersEnum: int implements EnumHasNameInterface
{

    case FIRST_NAME = 1;
    case LAST_NAME = 2;
    case FULL_NAME = 3;
    case ID = 4;


    public function getName(): string
    {
        return match ($this) {
            self::ID => 'شماره کاربری',
            self::FULL_NAME => 'نام و نام خانوادگی',
            self::FIRST_NAME => 'نام',
            self::LAST_NAME => 'نام خانوادگی',
            default => "",
        };
    }
}
