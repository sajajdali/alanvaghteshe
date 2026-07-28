<?php

namespace Modules\Diet\Enum;

use App\interface\EnumHasAdminBadgeInterface;
use App\interface\EnumHasDefaultInterface;
use App\interface\EnumHasNameInterface;

enum BasicFoodTypeEnum : int implements EnumHasDefaultInterface , EnumHasNameInterface , EnumHasAdminBadgeInterface
{
    case SIMPLE = 10;
    case COMBINED = 20;

    public static function getDefault(): EnumHasDefaultInterface
    {
        return self::SIMPLE;
    }

    public function getAdminBadge(): string
    {
        return '<span class="badge rounded-pill '.$this->getAdminBadgeClass().' my-1">'.$this->getName().'</span>';
    }

    public function getAdminBadgeClass(): string
    {
        return match ($this) {
            self::SIMPLE => 'bg-success',
            self::COMBINED => 'bg-danger',
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::SIMPLE => 'غذای ساده',
            self::COMBINED => 'غذای ترکیبی',
        };
    }
}
