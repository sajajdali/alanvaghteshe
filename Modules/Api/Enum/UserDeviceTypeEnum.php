<?php

namespace Modules\Api\Enum;

use App\interface\EnumHasDefaultInterface;

enum UserDeviceTypeEnum: string implements EnumHasDefaultInterface
{
    case ANDROID = 'android';
    case IOS = 'ios';
    case WEB = 'web';

    public static function getDefault(): EnumHasDefaultInterface
    {
        return self::ANDROID;
    }
}
