<?php

namespace Modules\Diet\Enum;

use App\interface\EnumHasNameInterface;

enum ConditionApplyItemEnum: string implements EnumHasNameInterface
{
    case FOOD = 'food';
    case BASIC_FOOD = 'basic_food';
    case DIET_PLAN = 'diet_plan';
    case DIET_REQUEST = 'diet_request';

    public static function make(
        $food = false,
        $diet_plan = false,
        $diet_request = false,
        $basic_food = false
    ): array {
        return [
            self::FOOD->value => $food,
            self::DIET_PLAN->value => $diet_plan,
            self::DIET_REQUEST->value => $diet_request,
            self::BASIC_FOOD->value => $basic_food,
        ];
    }

    public static function getNameForCondition(string $name): string
    {
        foreach (self::cases() as $enum) {
            if ($name === $enum->value) {
                return $enum->getName();
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum ".self::class);
    }

    public function getName(): string
    {
        return match ($this) {
            self::FOOD => 'غذاها',
            self::DIET_PLAN => 'برنامه غذایی',
            self::DIET_REQUEST => 'درخواست رژیم',
            self::BASIC_FOOD => 'غذاهای پایه',
        };
    }
}
