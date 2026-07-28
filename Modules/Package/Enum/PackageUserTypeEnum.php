<?php

namespace Modules\Package\Enum;

use App\interface\EnumHasNameInterface;
use App\trait\EnumFunctionTrait;

enum PackageUserTypeEnum: int implements EnumHasNameInterface
{
    use EnumFunctionTrait;

    case PENDING = 0;
    case IN_USE = 1;

    case END = 2;
    case CANCEL = 3;

    public function getName(): string
    {
        return match ($this) {
            self::PENDING   => 'نامشخص',
            self::IN_USE    => 'در حال استفاده',
            self::END       => 'تمام شده',
            self::CANCEL    => 'کنسل شده',
        };
    }

    public function getAdminBadgeClass(): string
    {
        return match ($this) {
            self::PENDING   => 'bg-info',
            self::IN_USE    => 'bg-green',
            self::END       => 'bg-yellow',
            self::CANCEL    => 'bg-red',
        };
    }

    public function getAdminBadge(): string
    {
        return '<span class="badge text-bold ' . $this->getAdminBadgeClass() . ' my-1">' . $this->getName() . '</span>';
    }
}
