<?php

namespace Modules\Chat\Enum;

use App\interface\EnumHasNameInterface;
use App\trait\EnumFunctionTrait;

enum ChatDetailTypeEnum : int implements EnumHasNameInterface
{
    use EnumFunctionTrait;
    case MESSAGE = 0;
    case ATTACH = 10;


    public function getName(): string
    {
        return match ($this) {
            self::MESSAGE => 'text',
            self::ATTACH => 'file',
        };
    }
}
