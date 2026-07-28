<?php

namespace Modules\Reminder\Enum;

use App\interface\EnumHasNameInterface;
use App\trait\EnumFunctionTrait;

enum ActiveEnum: int implements EnumHasNameInterface
{
    use EnumFunctionTrait;

    case ACTIVE = 1;
    case DEACTIVE = 2;


    public function getName(): string
    {
        return match ($this) {
            self::ACTIVE => 'فعال',
            self::DEACTIVE => 'غیر فعال',
            default => "",
        };
    }
    public function getbadgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'badge bg-success rounded-pill',
            self::DEACTIVE => 'badge bg-danger rounded-pill',
            default => "",
        };
    }
}
