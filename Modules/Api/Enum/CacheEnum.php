<?php

namespace Modules\Api\Enum;

enum CacheEnum: string
{
    case FOOD_CATEGORY = 'food_categories_all';
    case BASIC_FOODS = 'basic_food_list_category_{category}';
    case FAVORITE_FOODS = 'favorite_foods_user_{user_id}';
    case SHOW_DIET = 'diet/{route}';
    case INVITE_FRIENDS = 'invite';


    public function parameter(string $replacement = ''): string
    {
        return match ($this) {
            self::BASIC_FOODS => str_replace('{category}', $replacement, $this->value),
            self::FAVORITE_FOODS => str_replace('{user_id}', $replacement, $this->value),
            default => $this->value,
        };
    }

    public function getTagName()
    {
        return match ($this) {
            self::BASIC_FOODS => str_replace('_{category}', '', $this->value),
        };
    }


}
