<?php

namespace Modules\User\Enum;

use App\interface\EnumHasDefaultInterface;

enum UserRouteEnum: string implements EnumHasDefaultInterface
{
    case PAYMENT = 'payment';
    case PROFILE = 'profile';


    public static function getDefault(): EnumHasDefaultInterface
    {
        return self::PAYMENT;
    }
}
