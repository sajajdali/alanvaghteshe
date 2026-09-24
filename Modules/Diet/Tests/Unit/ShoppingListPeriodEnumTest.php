<?php

use Carbon\CarbonImmutable;
use Modules\Diet\Enum\ShoppingListPeriodEnum;

it('builds the daily range for tomorrow in the application timezone', function () {
    $now = CarbonImmutable::parse('2026-09-09 23:30:00', 'Asia/Tehran');

    $range = ShoppingListPeriodEnum::DAILY->dateRange($now);

    expect($range['start']->toDateString())->toBe('2026-09-10')
        ->and($range['end']->toDateString())->toBe('2026-09-10');
});

it('builds an inclusive seven day weekly range starting tomorrow', function () {
    $now = CarbonImmutable::parse('2026-09-09 10:00:00', 'Asia/Tehran');

    $range = ShoppingListPeriodEnum::WEEKLY->dateRange($now);

    expect($range['start']->toDateString())->toBe('2026-09-10')
        ->and($range['end']->toDateString())->toBe('2026-09-16')
        ->and($range['start']->diffInDays($range['end']) + 1)->toBe(7.0);
});
