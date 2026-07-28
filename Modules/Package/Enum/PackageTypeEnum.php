<?php

namespace Modules\Package\Enum;

use App\interface\EnumHasAdminBadgeInterface;
use App\interface\EnumHasNameInterface;
use App\trait\EnumFunctionTrait;

enum PackageTypeEnum: int implements EnumHasNameInterface, EnumHasAdminBadgeInterface
{
    use EnumFunctionTrait;

    case DIET = 10;
    case DIET_WITH_SUPPORT = 11;
    case EXERCISE = 20;

    case DIET_AND_EXERCISE = 30;
    case DIET_AND_EXERCISE_WITH_SUPPORT = 31;

    public function getName(): string
    {
        return match ($this) {
            self::DIET => 'رژیم',
            self::DIET_WITH_SUPPORT => 'رژیم + پشتیبانی اختصاصی',
            self::EXERCISE => 'برنامه ورزشی',
            self::DIET_AND_EXERCISE => 'رژیم و برنامه ورزشی',
            self::DIET_AND_EXERCISE_WITH_SUPPORT => 'رژیم و برنامه ورزشی + پشتیبانی اختصاصی',
        };
    }

    public function getAdminBadgeClass(): string
    {
        return match ($this) {
            self::DIET => 'bg-blue',
            self::DIET_WITH_SUPPORT => 'bg-blue',
            self::EXERCISE => 'bg-green',
            self::DIET_AND_EXERCISE => 'bg-yellow',
            self::DIET_AND_EXERCISE_WITH_SUPPORT => 'bg-yellow',
        };
    }

    public function getAdminBadge(): string
    {
        return '<span class="badge rounded-pill ' . $this->getAdminBadgeClass() . ' my-1">' . $this->getName() . '</span>';
    }
}
