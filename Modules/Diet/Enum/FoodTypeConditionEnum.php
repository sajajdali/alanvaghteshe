<?php

namespace Modules\Diet\Enum;

use App\interface\EnumHasDefaultInterface;
use App\interface\EnumHasNameInterface;

enum FoodTypeConditionEnum : int implements EnumHasDefaultInterface,EnumHasNameInterface
{
    case SELECTABLE_FOR_REPLACEMENT = 1;
    case SELECTED_FOR_MAIN_FOOD = 2;

    public static function getDefault(): EnumHasDefaultInterface
    {
        return self::SELECTABLE_FOR_REPLACEMENT;
    }

    public function getName(): string
    {
        return match ($this) {
            self::SELECTABLE_FOR_REPLACEMENT => 'غذا برای جایگزینی است',
            self::SELECTED_FOR_MAIN_FOOD => 'غذایی که به عنوان جایگزین انتخاب شده است',
        };
    }
}
