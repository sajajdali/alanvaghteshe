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

Route::middleware(['web', 'admin'])->as('admin.')->prefix('coupon')->group(function() {
    Route::get('/', 'CouponController@index')->name('coupon.index');
    Route::get('/create', 'CouponController@create')->name('coupon.create');
    Route::get('/edit/{coupon}', 'CouponController@edit')->name('coupon.edit');
    Route::post('/store', 'CouponController@store')->name('coupon.store');
    Route::post('/update/{coupon}', 'CouponController@update')->name('coupon.update');
});
