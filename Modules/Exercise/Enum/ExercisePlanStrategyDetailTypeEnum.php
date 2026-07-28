<?php

namespace Modules\Exercise\Enum;

use App\interface\EnumHasDefaultInterface;
use App\interface\EnumHasNameInterface;
use App\trait\EnumFunctionTrait;

enum ExercisePlanStrategyDetailTypeEnum : int implements EnumHasDefaultInterface,EnumHasNameInterface
{
    use EnumFunctionTrait;
    case BASE = 1;

    case COMPLEMENTARY = 2;

    case BEFORE = 3;

    case AFTER = 4;

    case COPY = 5;

    public static function getDefault(): EnumHasDefaultInterface
    {
        return self::BASE;
    }

    public function getName(): string
    {
        return match($this){
          self::BASE => 'مادر',
          self::COMPLEMENTARY => 'مکمل',
          self::BEFORE => 'قبل تمرین',
          self::AFTER => 'بعد از تمرین',
          self::COPY => 'کپی'
        };
    }
}
