<?php

namespace Modules\Admin\Tests\Unit;

use Tests\TestCase;

uses(TestCase::class);

it('generates correct URL for admin_default_asset', function () {
    // Set the environment variable to simulate APP_DEBUG
    config()->set('app.debug', true);

    $file = 'example.css';
    $expectedUrl = asset('default/admin/'.$file).'?v='.date('Y-m-d H');

    $url = admin_default_asset($file);

    expect($url)->toBe($expectedUrl);
});

it('generates correct URL for admin_asset', function () {
    // Set the environment variable to simulate APP_DEBUG
    config()->set('app.debug', true);

    $file = 'example.js';
    $expectedUrl = asset('assets/admin/'.$file).'?v='.date('Y-m-d H');

    $url = admin_asset($file);

    expect($url)->toBe($expectedUrl);
});

it('generates correct URL for admin_default_asset with APP_DEBUG=false', function () {
    // Set the environment variable to simulate APP_DEBUG=false
    config()->set('app.debug', false);
    // Mock the config() function to return a specific value for config('app.admin_version')
    config()->set('app.admin_version', '12345');

    $file = 'example.css';
    $expectedUrl = asset('default/admin/'.$file).'?v=12345';

    $url = admin_default_asset($file);

    expect($url)->toBe($expectedUrl);
});

it('generates correct URL for admin_asset with APP_DEBUG=false', function () {
    // Set the environment variable to simulate APP_DEBUG=false
    config()->set('app.debug', false);

    // Mock the config() function to return a specific value for config('app.admin_version')
    config()->set('app.admin_version', '12345');

    $file = 'example.js';
    $expectedUrl = asset('assets/admin/'.$file).'?v=12345';

    $url = admin_asset($file);

    expect($url)->toBe($expectedUrl);
});
