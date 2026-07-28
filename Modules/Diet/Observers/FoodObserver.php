<?php

namespace Modules\Diet\Observers;

use Modules\Diet\Entities\Food;
use Modules\Diet\Enum\FoodTypeEnum;

class FoodObserver
{
    public function updated(food $food): void
    {
        // When the food is changed from simple to mixed and as a substitute for a mixed food
        if ($food->wasChanged('type')) {
            if ($food->getOriginal('type') == FoodTypeEnum::SIMPLE) {
                $food->relatedBy()->detach();
            }
        }
        // When the food is changed from simple to mixed and as a substitute for a mixed food

    }
}
