<?php

use Modules\Onboarding\Livewire\Admin\OnboardingSettingForm;

Route::get('onboarding', OnboardingSettingForm::class)
    ->name('onboarding.index')
    ->middleware('can:onboarding');
