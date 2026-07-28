<?php

namespace Modules\User\Enum;

use App\interface\EnumHasNameInterface;
use ReflectionClass;

enum PopUpEnum: int implements EnumHasNameInterface
{
    case FREE_PACKAGE = 1;



    public function getName(): string
    {
        return match ($this) {
            self::FREE_PACKAGE => 'پکیج رایگان',
        };
    }

}
