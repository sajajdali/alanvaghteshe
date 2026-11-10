<?php

use Carbon\Carbon;
use Modules\Api\Http\Controllers\Profile\ProfileController;

it('keeps a stable target plan structure when there is no active diet', function () {
    $controller = new ProfileController();
    $method = new ReflectionMethod($controller, 'getEmptyTargetPage');

    $target = $method->invoke($controller);

    expect($target['calculate_target_plan'])->toHaveCount(3);

    foreach ($target['calculate_target_plan'] as $plan) {
        expect($plan)->toMatchArray([
            'title' => null,
            'total_days' => null,
            'status' => null,
            'milestones' => [],
        ]);
    }
});

it('keeps title and status after the target weight has been reached', function () {
    $controller = new ProfileController();

    $plan = $controller->calculateWeightLossPlan(
        currentWeight: 70,
        targetWeight: 70,
        weeklyWeightLoss: 1,
        startDate: Carbon::parse('2026-01-01'),
        title: '۱ کیلو',
        default: true,
    );

    expect($plan['title'])->toBe('۱ کیلو')
        ->and($plan['status'])->toBeTrue()
        ->and($plan['total_days'])->toBe(0);
});
