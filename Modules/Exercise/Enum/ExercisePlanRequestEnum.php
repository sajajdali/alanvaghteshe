<?php

namespace Modules\Exercise\Enum;

use App\interface\EnumHasAdminBadgeInterface;
use App\interface\EnumHasDefaultInterface;
use App\interface\EnumHasNameInterface;
use App\interface\EnumHasToArrayInterface;
use App\trait\EnumFunctionTrait;

enum ExercisePlanRequestEnum: int implements EnumHasNameInterface, EnumHasAdminBadgeInterface, EnumHasDefaultInterface,EnumHasToArrayInterface
{
    use EnumFunctionTrait;
    case REQUESTED = 0;

    case COMPUTING = 1;

    case COMPLETED = 2;

    case FAILED = 3;

    case ENDED = 4;

    case PENDING = 5;

    public function getAdminBadgeClass(): string
    {
        return match ($this) {
            self::REQUESTED, self::PENDING => 'bg-warning',
            self::COMPUTING => 'bg-info',
            self::COMPLETED => 'bg-success',
            self::FAILED => 'bg-danger',
            self::ENDED => 'bg-secondary'
        };
    }

    public function getAdminBadge(): string
    {
        return '<span class="badge rounded-pill '.$this->getAdminBadgeClass().' my-1">'.$this->getName().'</span>';
    }

    public static function getDefault(): EnumHasDefaultInterface
    {
        return self::REQUESTED;
    }

    public function getName(): string
    {
        return match ($this) {
            self::REQUESTED => 'درخواست شده',
            self::COMPUTING => 'در حال برنامه‌دهی',
            self::COMPLETED => 'برنامه آماده',
            self::ENDED => 'پایان یافته',
            self::PENDING => 'در انتظار تایید',
            self::FAILED => 'ناموفق'
        };
    }

    public static function toArray(): array
    {
        $ret = [];
        foreach (self::cases() as $case) {
            $ret[$case->value] = $case->getName();
        }
        return $ret;
    }
}
