<?php
namespace Modules\Api\Enum;

use function PHPUnit\Framework\matches;

enum PopupEnum: string
{
    case FREE_ACCOUNT = 'free_account';
    case SHOW_INFORMATION = 'show_information';
    case STARTING_TOMORROW = 'starting_tomorrow';
    case REJECTED_DIET = 'rejected_diet';
    case DIET_IS_OVER = 'diet_is_over';
    case STARTING_THE_DIET_TOMORROW = 'starting_the_diet_tomorrow';

    public function getName(): string
    {
        return match ($this) {
            self::FREE_ACCOUNT => 'اکانت رایگان',
            self::STARTING_TOMORROW => 'شروع رژیم از فردا',
            self::REJECTED_DIET => 'رژیم شما رد شده است',
            self::DIET_IS_OVER => 'رژیم تمام شده است',
            self::SHOW_INFORMATION => 'نمایش توضیحات',
            self::STARTING_THE_DIET_TOMORROW => 'شروع رژیم از فردا',
        };
    }






}
