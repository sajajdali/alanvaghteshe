<?php

use Modules\Diet\Service\DietShoppingListInputHasher;

function shoppingListSnapshot(array $overrides = []): array
{
    return array_replace_recursive([
        'diet_request_id' => 10,
        'period' => 'daily',
        'start_date' => '2026-09-10',
        'end_date' => '2026-09-10',
        'meals' => [[
            'diet_request_detail_id' => 100,
            'date' => '2026-09-10',
            'meal_id' => 1,
            'meal_name' => 'صبحانه',
            'food' => [
                'food_id' => 20,
                'food_name' => 'املت با نان',
                'basic_foods' => [[
                    'basic_food_id' => 30,
                    'name' => 'املت',
                    'quantity' => 2.0,
                    'unit' => 'عدد',
                    'unit_id' => 5,
                    'grams' => 120.0,
                ]],
            ],
        ]],
    ], $overrides);
}

it('returns the same hash for the same shopping input', function () {
    $hasher = new DietShoppingListInputHasher();

    expect($hasher->hash(shoppingListSnapshot()))
        ->toBe($hasher->hash(shoppingListSnapshot()));
});

it('changes the hash when a prescribed food snapshot changes', function () {
    $hasher = new DietShoppingListInputHasher();
    $changed = shoppingListSnapshot([
        'meals' => [[
            'food' => [
                'food_name' => 'تخم مرغ آب‌پز با نان',
            ],
        ]],
    ]);

    expect($hasher->hash(shoppingListSnapshot()))
        ->not->toBe($hasher->hash($changed));
});
