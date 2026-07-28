<?php

namespace Modules\Exercise\Enum;

use App\interface\EnumHasAdminBadgeInterface;
use App\interface\EnumHasDefaultInterface;
use App\interface\EnumHasNameInterface;
use App\trait\EnumFunctionTrait;

enum ExercisePlanStrategyLevelEnum: int implements EnumHasNameInterface, EnumHasAdminBadgeInterface, EnumHasDefaultInterface
{
    use EnumFunctionTrait;
    case BEGINNER = 1;
    case ADVANCE = 2;

    public static function map($target): self
    {
        return  match ($target) {
            1  => self::BEGINNER,
            2  => self::ADVANCE,
            default => self::BEGINNER,
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::BEGINNER => 'آماتور',
            self::ADVANCE => 'حرفه‌ای'
        };
    }

    public function getAdminBadgeClass(): string
    {
        return match ($this) {
            self::BEGINNER => 'bg-danger-gradient',
            self::ADVANCE => 'bg-light-gradient'
        };
    }

    public function getAdminBadge(): string
    {
        return '<span class="badge ' . $this->getAdminBadgeClass() . ' my-1">' . $this->getName() . '</span>';
    }

    public static function getDefault(): EnumHasDefaultInterface
    {
        return self::BEGINNER;
    }
}
