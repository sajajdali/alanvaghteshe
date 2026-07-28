<?php

use Illuminate\Support\Facades\Route;
use Modules\Reminder\Livewire\ReminderList;
use Modules\Reminder\Livewire\UpdateOrCreate;

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

Route::group([], function () {
   route::get('reminder/create',UpdateOrCreate::class)->name('reminder.create');
   route::get('reminder/edit/{reminder}',UpdateOrCreate::class)->name('reminder.edit');
   route::get('reminder/list',ReminderList::class)->name('reminder.list');
});
