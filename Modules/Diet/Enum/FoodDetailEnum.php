<?php

namespace Modules\Diet\Enum;

use App\interface\EnumHasDefaultInterface;

enum FoodDetailEnum : int
{

    case can_replace_all_foods = 1;
    case can_not_replace_all_foods = 2;

}
