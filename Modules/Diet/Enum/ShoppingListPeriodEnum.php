<?php

namespace Modules\Diet\Enum;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Container\Container;

enum ShoppingListPeriodEnum: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';

    /**
     * @return array{start: CarbonImmutable, end: CarbonImmutable}
     */
    public function dateRange(?CarbonInterface $now = null): array
    {
        $timezone = Container::getInstance()->bound('config')
            ? config('app.timezone', 'Asia/Tehran')
            : 'Asia/Tehran';

        $today = $now
            ? CarbonImmutable::instance($now)->setTimezone($timezone)
            : CarbonImmutable::now($timezone);

        $start = $today->startOfDay()->addDay();

        return [
            'start' => $start,
            'end' => $this === self::DAILY ? $start : $start->addDays(6),
        ];
    }
}
