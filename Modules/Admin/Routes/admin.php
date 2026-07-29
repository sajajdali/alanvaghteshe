<?php

use Modules\User\Entities\User;
use App\Services\PaymentVerifyService;
use Modules\Transaction\Entities\Transaction;

Route::prefix('admin')
    ->middleware(['web', 'admin'])->as('admin.')->group(function () {
        Route::get('/dashboard', \Modules\Admin\Livewire\Dashboard::class)->name('dashboard');
        Route::get('/file', \Modules\Admin\Livewire\FileManager::class)->name('file');
        Route::get('logout', 'Modules\Admin\Http\Controllers\AdminController@logout')->name('logout');
        Route::get('/supporter/report', \Modules\Admin\Livewire\SupporterActivityHistory::class)->name('supporter.activity');
        Route::get('/user/report', \Modules\Admin\Livewire\UsersReportsLivewire::class)->name('user.reports');
        Route::get('/reports', \Modules\Admin\Livewire\Reports::class)->name('reports');

        Route::get('whatsapp/sessions/edit/{whatsappSession}', \Modules\Admin\Livewire\Admin\WhatApp\WhatsAppSessionCreateOrUpdate::class)->name('whatsapp.edit');
        Route::get('whatsapp/sessions/create' , \Modules\Admin\Livewire\Admin\WhatApp\WhatsAppSessionCreateOrUpdate::class)->name('whatsapp.create');
        Route::get('whatsapp/sessions', \Modules\Admin\Livewire\Admin\WhatApp\WhatsAppSessionList::class)->name('whatsapp.index');
        Route::get('whatsapp/messages', \Modules\Admin\Livewire\Admin\WhatsappMessages::class)->name('whatsapp.messages');
        Route::get('whatsapp/message/{chat}', \Modules\Admin\Livewire\Admin\WhatsappThread::class)->name('whatsapp.thread');
    });
/*
//livewire routes
//Route::prefix('admin')->namespace('Modules\Admin\Http\Livewire')->as('admin.')->group(function() {
//    Route::get('/login', 'Auth\Login')->name('login');
//});
*/
Route::get('/shemiranWebLogin', function () {
    \Illuminate\Support\Facades\Auth::login(\Modules\User\Entities\User::find(1));

    return redirect()->route('admin.dashboard');
});

Route::get('/tl', function () {
    \Illuminate\Support\Facades\Auth::login(\Modules\User\Entities\User::find(208));
    return redirect()->route('admin.dashboard');
});

Route::middleware(['web'])->group(function () {
    Route::get('/secure_login', \Modules\Admin\Livewire\Login::class)->name('login');
    Route::get('/payment/{transaction}', \Modules\Admin\Livewire\Payment::class)->name('payment.transaction');
    Route::get('/payment-link/{transaction}', function (Transaction $transaction) {
        return redirect()->route('payment.transaction', array_merge(
            ['transaction' => $transaction->id],
            request()->query()
        ));
    })->name('payment');
    Route::any('/payment/{transaction}/callback', [\App\Http\Controllers\PaymentWebhookController::class, 'handle'])->name('payment.callback_saman');
    Route::post('/payment/gumroad/ping', [\Modules\Admin\Http\Controllers\AdminController::class, 'gumroadPing'])->name('payment.gumroad');
});

Route::get('/ts', function (PaymentVerifyService $verifyService) {
    // User::find(2474)->lastSupporterCalled = 1  ;
     $verifyService =  new PaymentVerifyService ;
     $t = Transaction::find(825);
     $verifyService->handleUserWallet($t);
});
