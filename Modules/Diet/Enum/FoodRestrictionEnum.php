<?php

namespace Modules\Diet\Enum;

use App\interface\EnumHasNameInterface;

enum FoodRestrictionEnum: string implements EnumHasNameInterface
{
    case omnivorous = 'omnivorous';
    case vegan = 'vegan';
    case vegetarian = 'vegetarian';
    case Lactose_free = 'Lactose_free';
    case no_fish = 'no_fish';

    public function getName(): string
    {
        return match ($this) {
            self::omnivorous => 'همه چیز خوار',
            self::vegan => 'وگن',
            self::vegetarian => 'گیاه خوار',
            self::Lactose_free => 'بدون لاکتوز',
            self::no_fish => 'بدون ماهی',
        };
    }
}
