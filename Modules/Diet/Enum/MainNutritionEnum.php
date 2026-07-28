<?php

namespace Modules\Diet\Enum;

use App\interface\EnumHasNameInterface;

enum MainNutritionEnum : string implements EnumHasNameInterface
{
    case carb = 'carb';
    case protein = 'protein';
    case fat = 'fat';
    case fiber = 'fiber';
    case special = 'special';
    case all = 'all';

    public function getName(): string
    {
        return match ($this) {
            self::carb => 'کربوهیدرات',
            self::protein => 'پروتئین',
            self::fat => 'چربی',
            self::fiber => 'فیبر',
            self::special => 'ویژه',
            self::all => 'همه',
        };
    }
}
