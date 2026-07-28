<?php

namespace Modules\Diet\Enum;

use App\interface\EnumHasNameInterface;

enum ConditionKeyEnum: int implements EnumHasNameInterface
{
    case NONE = 0;
    case SEASON = 1;
    case DIET_TYPE = 2;
    case FOOD_RESTRICTION = 3;

    case DISEASE = 4;
    case ATHLETE_TYPE = 5;
    case BODY_STYLE = 6;
    case SPECIAL_CONDITION = 7;
    case GENDER = 8;
    case DIET_PATTERN = 9;
    case FOOD_ALLERGY = 10;

    public static function getNameForId(int $id): string
    {
        foreach (self::cases() as $enum) {
            if ($id === $enum->value) {
                return $enum->getName();
            }
        }
        throw new \ValueError("$id is not a valid backing value for enum ".self::class);
    }

    public function getName(): string
    {
        return match ($this) {
            self::NONE => 'هیچ کدام',
            self::SEASON => 'فصل',
            self::DIET_TYPE => 'نوع رژیم',
            self::FOOD_RESTRICTION => 'محدودیت غذایی',
            self::DISEASE => 'بیماری ها',
            self::ATHLETE_TYPE => 'ورزشکار یا غیر ورزشکار',
            self::BODY_STYLE => 'تیپ بدنی',
            self::SPECIAL_CONDITION => 'شرایط خاص',
            self::GENDER => 'جنسیت',
            self::DIET_PATTERN => 'الگوی رژیم',
            self::FOOD_ALLERGY => 'حساسیت غذایی',
        };
    }


}
