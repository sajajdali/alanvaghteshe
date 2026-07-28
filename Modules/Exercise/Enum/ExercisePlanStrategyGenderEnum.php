<?php

namespace Modules\Exercise\Enum;

use App\interface\EnumHasAdminBadgeInterface;
use App\interface\EnumHasDefaultInterface;
use App\interface\EnumHasNameInterface;
use App\trait\EnumFunctionTrait;

enum ExercisePlanStrategyGenderEnum : int implements EnumHasNameInterface,EnumHasAdminBadgeInterface,EnumHasDefaultInterface
{
    use EnumFunctionTrait;
    case MALE = 1;
    case FEMALE = 2;

    function getName(): string
    {
        return match($this){
            self::MALE => 'مرد',
            self::FEMALE => 'زن'
        };
    }

    public function getAdminBadgeClass(): string
    {
        return match($this){
            self::MALE => 'bg-info',
            self::FEMALE => 'bg-warning'
        };
    }

    public function getAdminBadge(): string
    {
        return '<span class="badge rounded-pill '.$this->getAdminBadgeClass().' my-1">'.$this->getName().'</span>';
    }
    public static function map($gender) : self {
        return match($gender){
                  1 => self::FEMALE,
            default => self::MALE
        };
    }
    public static function getDefault(): EnumHasDefaultInterface
    {
        return self::MALE;
    }
}
