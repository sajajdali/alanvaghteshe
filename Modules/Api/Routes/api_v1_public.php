<?php

Route::get('faq', 'FaqController@index');
Route::get('disease', 'DiseaseController@index');
Route::post('login', 'AuthController@login');
Route::post('verify', 'AuthController@verify');
Route::post('register', [\Modules\Api\Http\Controllers\AuthController::class , 'verify']);
//Route::get('quote', [\Modules\Api\Http\Controllers\QuoteController::class , 'index'])->name('api.qoute');
// payment
Route::get('payment/redirect_to_bank', [\Modules\Api\Http\Controllers\PaymentController::class, 'redirectToBank'])->name('api.payment.redirect_to_bank');
Route::any('payment/verify', [\Modules\Api\Http\Controllers\PaymentController::class, 'verify'])->name('api.payment.verify');
Route::post('/whatsapp/webhook', [\Modules\Api\Http\Controllers\WhatsappWebhookController::class, 'handle']);

// payment
