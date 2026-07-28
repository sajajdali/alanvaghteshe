<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('diet')->group(function() {
    Route::get('/', 'DietController@index');
});

Route::get('sample', [\Modules\Diet\Http\Controllers\DietController::class, 'sample']);
Route::get('dietTest', [\Modules\Diet\Http\Controllers\DietController::class, 'test']);
Route::get('test_diet', [\Modules\Diet\Http\Controllers\DietController::class, 'testDiet']);
    //return the exchangeable meal for specific request diet detaiul
Route::get('changeList', [\Modules\Diet\Http\Controllers\DietController::class, 'changeList']);
