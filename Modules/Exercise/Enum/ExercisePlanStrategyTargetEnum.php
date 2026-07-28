<?php

namespace Modules\Exercise\Enum;

use App\interface\EnumHasAdminBadgeInterface;
use App\interface\EnumHasDefaultInterface;
use App\interface\EnumHasNameInterface;

enum ExercisePlanStrategyTargetEnum: int implements EnumHasAdminBadgeInterface, EnumHasDefaultInterface, EnumHasNameInterface
{
    case TARGET_INCREASE_WEIGHT = 1;
    case TARGET_DECREASE_WEIGHT = 2;

    case MAKE_MUSCLE = 3;



    public static function getDefault(): EnumHasDefaultInterface
    {
        return self::TARGET_DECREASE_WEIGHT;
    }

    public function getAdminBadge(): string
    {
        return '<span class="badge '.$this->getAdminBadgeClass().' my-1">'.$this->getName().'</span>';
    }

    public function getAdminBadgeClass(): string
    {
        return match ($this) {
            self::TARGET_INCREASE_WEIGHT => 'bg-primary',
            self::TARGET_DECREASE_WEIGHT => 'bg-secondary',
            self::MAKE_MUSCLE => 'bg-success'
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::TARGET_INCREASE_WEIGHT => 'حجم',
            self::TARGET_DECREASE_WEIGHT => 'کات',
            self::MAKE_MUSCLE => 'عضله سازی'
        };
    }
    public static function map($target) :self {
       return  match($target) {
            1  => self::TARGET_INCREASE_WEIGHT ,
            2 => self::MAKE_MUSCLE ,
            default => self::TARGET_DECREASE_WEIGHT ,
        };
    }
}
