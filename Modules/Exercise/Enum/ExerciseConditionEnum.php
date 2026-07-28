<?php

namespace Modules\Exercise\Enum;

enum ExerciseConditionEnum : string
{
    case IS_FOR_MEN = 'MEN';
    case IS_FOR_WOMEN = 'WOMEN';

    case IS_FOR_BEGINNERS = 'BEGINNERS';
    case IS_FOR_ADVANCED = 'ADVANCED';

    case IS_EXERCISE_BASED = 'EXERCISE_BASED';

    case IS_EXERCISE_COMPLEMENTARY = 'EXERCISE_COMPLEMENTARY';

    case IS_MAIN_EXERCISE = 'MAIN_EXERCISE';

    case IS_BEFORE_AFTER_EXERCISE = 'BEFORE_AFTER_EXERCISE';

    public static function make(
        bool $isForMen = false,
        bool $isForWomen = false,
        bool $isForBeginners = false,
        bool $isForAdvanced = false,
        bool $isExerciseBased = false,
        bool $isExerciseComplementary = false,
        bool $isMainExercise = false,
        bool $isBeforeAfterExercise = false
    ) : array {
        return [
            self::IS_FOR_MEN->value => $isForMen,
            self::IS_FOR_WOMEN->value => $isForWomen,
            self::IS_FOR_BEGINNERS->value => $isForBeginners,
            self::IS_FOR_ADVANCED->value => $isForAdvanced,
            self::IS_EXERCISE_BASED->value => $isExerciseBased,
            self::IS_EXERCISE_COMPLEMENTARY->value => $isExerciseComplementary,
            self::IS_MAIN_EXERCISE->value => $isMainExercise,
            self::IS_BEFORE_AFTER_EXERCISE->value => $isBeforeAfterExercise,
        ];
    }
}
