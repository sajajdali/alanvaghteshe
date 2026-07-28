<?php

namespace Modules\Diet\Enum;

use App\interface\EnumHasNameInterface;


enum FoodFactEnum : string implements EnumHasNameInterface
{
    case CALORIE = 'calorie';
    case PROTEIN = 'protein';
    case FAT = 'fat';
    case CARBOHYDRATE = 'carbohydrate';
    case SUGAR = 'sugar';
    case FIBER = 'fiber';
    case SODIUM = 'sodium';
    case POTASSIUM = 'potassium';
    case CALCIUM = 'calcium';
    case MAGNESIUM = 'magnesium';
    case IRON = 'iron';
    case CHOLESTEROL = 'cholesterol';
    case PHOSPHOR = 'phosphor';
    case SATURATED_FAT = 'saturatedFat';
    case POLY_UNSATURATED_FAT = 'polyunsaturatedFat';
    case TRANS_FAT = 'transFat';
    case MONOUNSATURATED_FAT = 'monounsaturatedFat';


    public function getName(): string
    {
        return match ($this) {
            self::CALORIE => 'کالری',
            self::PROTEIN => 'پروتئین',
            self::FAT => 'چربی',
            self::CARBOHYDRATE => 'کربوهیدرات',
            self::SUGAR => 'شکر',
            self::FIBER => 'فیبر',
            self::SODIUM => 'سدیم',
            self::POTASSIUM => 'پتاسیم',
            self::CALCIUM => 'کلسیوم',
            self::MAGNESIUM => 'منیزیوم',
            self::IRON => 'آهن',
            self::CHOLESTEROL => 'کلسترول',
            self::PHOSPHOR => 'فسفر',
            self::SATURATED_FAT => 'چربی های اشباع شده',
            self::POLY_UNSATURATED_FAT => 'چربی های غیر اشباع',
            self::TRANS_FAT => 'چربی ترانس',
            self::MONOUNSATURATED_FAT => 'چربی اشباع',
        };
    }

}
