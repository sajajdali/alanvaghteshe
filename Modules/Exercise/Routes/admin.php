<?php

use Modules\Exercise\Entities\Exercise;
use Modules\Exercise\Entities\ExerciseBodyCategory;
use Modules\Exercise\Entities\ExercisePlanRequest;
use Modules\Exercise\Entities\ExercisePlanStrategy;
use Modules\Exercise\Livewire\Admin\Exercise\ExerciseCreateOrUpdate;
use Modules\Exercise\Livewire\Admin\Exercise\ExerciseList;
use Modules\Exercise\Livewire\Admin\ExerciseBodyCategory\ExerciseBodyCategoryList;
use Modules\Exercise\Livewire\Admin\ExerciseBodyCategory\ExerciseBodyCategoryUpdateOrCreate;
use Modules\Exercise\Livewire\Admin\ExercisePlanRequest\ExercisePlanRequestList;
use Modules\Exercise\Livewire\Admin\ExercisePlanRequest\ExercisePlanRequestUpdate;
use Modules\Exercise\Livewire\Admin\ExercisePlanStrategy\ExercisePlanStrategyCreateOrUpdate;
use Modules\Exercise\Livewire\Admin\ExercisePlanStrategy\ExercisePlanStrategyList;

//body category
Route::get('exercise-body-category', ExerciseBodyCategoryList::class)->name('exercise_body_category.index')->can('viewAny', ExerciseBodyCategory::class);
Route::get('exercise-body-category/create', ExerciseBodyCategoryUpdateOrCreate::class)->name('exercise_body_category.create')->can('create', ExerciseBodyCategory::class);
Route::get('exercise-body-category/edit/{exercise_body_category}', ExerciseBodyCategoryUpdateOrCreate::class)->name('exercise_body_category.edit');//check policy inside component
//exercise
Route::get('exercise', ExerciseList::class)->name('exercise.index')->can('viewAny', Exercise::class);
Route::get('exercise/create', ExerciseCreateOrUpdate::class)->name('exercise.create')->can('create', Exercise::class);
Route::get('exercise/edit/{exercise}', ExerciseCreateOrUpdate::class)->name('exercise.edit');//check policy inside component
//exercise plan strategy
Route::get('exercise-plan-strategy', ExercisePlanStrategyList::class)->name('exercise_plan_strategy.index')->can('viewAny', ExercisePlanStrategy::class);
Route::get('exercise-plan-strategy/create', ExercisePlanStrategyCreateOrUpdate::class)->name('exercise_plan_strategy.create')->can('create', ExercisePlanStrategy::class);
Route::get('exercise-plan-strategy/edit/{exercise_plan_strategy}', ExercisePlanStrategyCreateOrUpdate::class)->name('exercise_plan_strategy.edit');//check policy inside component
//requests
Route::get('exercise-plan-request', ExercisePlanRequestList::class)->name('exercise_plan_request.index')->can('viewAny', ExercisePlanRequest::class);
Route::get('exercise-plan-request/edit/{exercise_plan_request}', ExercisePlanRequestUpdate::class)->name('exercise_plan_request.edit');//check policy inside component
Route::get('exercise-plan-request/print/{exercise_plan_request}', 'Modules\Exercise\Http\Controllers\Admin\ExerciseRequestController@print')->name('exercise_plan_request.print');
Route::get('exercise-plan-request/pdf/{exercise_plan_request}', 'Modules\Exercise\Http\Controllers\Admin\ExerciseRequestController@pdf')->name('exercise_plan_request.pdf');
