<?php

namespace Modules\Exercise\Enum;

use App\interface\EnumHasNameInterface;
use App\trait\EnumFunctionTrait;

enum ExerciseSetTypeEnum : int implements EnumHasNameInterface
{
    use EnumFunctionTrait;
    case MAIN_SET = 1;
    case SUPER_SET = 2;
    case BEFORE_SET = 3;
    case AFTER_SET = 4;
    case COPY_SET = 5;

    public function getName(): string
    {
        return match($this){
          self::BEFORE_SET => 'قبل از تمرین',
          self::AFTER_SET => 'بعد از تمرین',
          self::MAIN_SET => 'ست اصلی',
          self::SUPER_SET => 'سوپر ست',
          self::COPY_SET => 'ست کپی'
        };
    }
}
