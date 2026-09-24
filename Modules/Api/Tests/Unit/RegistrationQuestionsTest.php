<?php

use Modules\Api\Support\RegistrationQuestions;

it('exposes the new registration questions with stable values', function () {
    $questions = collect(RegistrationQuestions::all())->keyBy('key');

    expect($questions->keys()->all())->toBe([
        'weight_loss_medication',
        'food_budget',
        'weight_change_per_week',
    ])->and(array_column($questions['weight_loss_medication']['options'], 'value'))->toBe([0, 1])
        ->and(array_column($questions['food_budget']['options'], 'value'))->toBe([0, 1, 2])
        ->and(array_column($questions['weight_change_per_week']['options'], 'value'))->toBe([0, 1, 2]);
});
