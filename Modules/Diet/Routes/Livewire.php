<?php

use Modules\Diet\Livewire\Admin\PrescribedDiets\Details\Index;
use Modules\Diet\Livewire\Admin\PrescribedDiets\PresCribedDietsList;

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

Route::prefix('admin')
    ->middleware(['web', 'admin'])->as('admin.')->group(function () {
        Route::get('/prescribed-diets', PresCribedDietsList::class)->name('presCribed-diets');
        Route::get('/prescribed-diets/detail/{diet_request}', Index::class)->name('presCribed-diets.detail');

    });

