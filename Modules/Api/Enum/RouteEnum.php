<?php
namespace Modules\Api\Enum;

enum RouteEnum: string
{
    case LOGOUT = '/logout';
    case DIETS = '/diets';
    case SHOW_DIET = '/diet/{route}';
    case INVITE_FRIENDS = '/invite';
    case REGISTER = '/statement';
    case PROFILE = '/profile';
    case ORDER_PACKAGE = '/package';
    case NEW_DIET = '/new_diet';
    case CLOSE = '/close';
    case AGREE = '/AGREE';
    case DASHBOARD = '/';
    case ADD_WEIGHT = '/add_weight';
    case COURSE_LIST = '/courses';
    case COURSE_DETAIL = '/course';


    public function getName(): string
    {
        return match ($this) {
            self::DIETS => 'رژیم ها',
            self::SHOW_DIET => 'مشاهده رژیم',
            self::CLOSE => 'بستن',
            self::AGREE => 'تایید',
            self::ADD_WEIGHT => 'اضافه کردن وزن جدید',
            self::NEW_DIET => 'رژیم جدید',
            self::COURSE_LIST => 'لیست دوره‌ها',
            self::COURSE_DETAIL => 'جزئیات دوره',
        };
    }

    public function parameter(string $replacement = ''): string
    {
        return match ($this) {
            self::SHOW_DIET => str_replace('{route}', $replacement, $this->value),
            default => $this->value,
        };
    }
}
