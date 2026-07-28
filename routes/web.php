<?php

use App\Http\Controllers\PaypalController;
use App\Http\Controllers\TelegramController;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\app\Notifications\UserMessageNotification;
use Modules\User\Entities\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/send-google-analytics-event', function () {
    $client = new Client();

    $clientId = '125';  // client_id را مشخص کنید
    $eventName = 'test_event_backend3';
    $params = [
        'source' => 'backend',
        'status' => 'success',
    ];

    $url = 'https://www.google-analytics.com/mp/collect';
    $data = [
        'client_id' => $clientId,
        'events' => [
            [
                'name' => $eventName,
                'params' => array_merge([
                    'timestamp' => time(),
                ], $params),
            ]
        ]
    ];

    try {
        $response = $client->post($url, [
            'query' => [
                'measurement_id' => 'G-SX5YZRKC1B',  // Measurement ID شما
                'api_secret' => 'Q0UMJDxkRpWTtcfsqmCaiQ',  // API Secret شما
            ],
            'json' => $data,  // ارسال داده‌ها به صورت JSON
        ]);

        // بررسی وضعیت درخواست
        if ($response->getStatusCode() === 204) {
            Log::info('Event sent successfully to Google Analytics');
        } else {
            Log::error('Failed to send event to Google Analytics', [
                'status_code' => $response->getStatusCode(),
            ]);
        }

        return 'Event sent successfully to Google Analytics';
    } catch (\Exception $e) {
        Log::error('Error sending event to Google Analytics', [
            'message' => $e->getMessage(),
        ]);

        return 'Failed to send event';
    }
});
Route::get('test_notification/{mobile}', function ($id) {

    $user = User::find($id);
//    $googleAnalyticsService = new  \App\Services\GoogleAnalyticService();
//    $googleAnalyticsService->sendGA4Event($user->id, 'test_by_sajjad', ['method' => 'email']);
//
//    dd($googleAnalyticsService);

    $appMetrica = app(\App\Services\AppMetricaService::class)->sendEvent(
        profileId: $user->id,
        eventName: 'test_by_sajjad_v2',
        params: [
            'name' => $user->full_name,
            'mobile' => $user->mobile,
        ]);
    dd($appMetrica);

    // یوزر را بر اساس موبایل پیدا کن + userDevices
    $user = User::with('userDevices')
        ->where('id', $id)
        ->first();

    if (!$user) {
        return "❌ User not found for id: {$id}";
    }

    try {
        $user->notify(new UserMessageNotification(
            title: "تیکت شما جواب داده شد",
            excerpt: "به تیکت ارسالی شما پاسخ دادیم",
            message: "salam"
        ));

        return "✅ Notification sent to: {$user->mobile} (User ID: {$user->id})";

    } catch (\Throwable $e) {

        \Log::error('Error on notify (test route)', [
            'user_id' => $user->id,
            'mobile'  => $user->mobile,
            'error'   => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return "❌ Error: " . $e->getMessage();
    }
});

Route::get('test' , [\Modules\Api\Http\Controllers\Profile\DashboardController::class , 'test']);
Route::get('seeder' , [\Modules\Api\Http\Controllers\Profile\DashboardController::class , 'seeder']);
Route::get('seeder_pivot' , [\Modules\Api\Http\Controllers\Profile\DashboardController::class , 'seederPivot']);
Route::get('referral/{referral}' , \Modules\Admin\Livewire\ReferralUser::class)->name('referral.user');

Route::get('referral_test' , \Modules\Admin\Livewire\Payment::class)->name('referral.test');

Route::get('create-payment', [PaypalController::class, 'createPayment'])->name('create.payment');
Route::get('payment/success', [PaypalController::class, 'paymentSuccess'])->name('payment.success');
Route::get('payment/cancel', [PaypalController::class, 'paymentCancel'])->name('payment.cancel');

Route::post('/telegram/webhook', [TelegramController::class, 'handle']);
Route::post('/telegram/webhook_referral', [\App\Http\Controllers\TelegramControllerReferral::class, 'handle']);

Route::get('set_proxy' , [TelegramController::class , 'setProxy']);
Route::get('set_proxy_referral' , [TelegramController::class , 'setProxyReferral']);
Route::get('/telegram/set-commands', function () {
    $useProxy = filter_var(env('ACTIVE_PROXY'), FILTER_VALIDATE_BOOLEAN);

    $options = [];

    if ($useProxy) {
        $options['proxy'] = env('PROXY_URL');
    }

    $guzzle = new \GuzzleHttp\Client($options);
    $httpClient = new \Telegram\Bot\HttpClients\GuzzleHttpClient($guzzle);
    $telegram = new \Telegram\Bot\Api(env('TELEGRAM_BOT_AGENT_TOKEN'), false, $httpClient);

    $telegram->setMyCommands([
        ['command' => 'start', 'description' => 'شروع ربات'],
        ['command' => 'balance', 'description' => 'مشاهده موجودی 💰'],
        ['command' => 'help', 'description' => 'راهنما 📖'],
    ]);

    return response()->json(['status' => 'Commands set']);
});
Route::get('call_fire', function () {

    $result = Process::run("ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -i /home/www/.ssh/id_ed25519 root@5.202.89.41 -p8787 'php /var/saman/ivr/amiri/voip/cal_fire.php 13:58 09122978167 98417792 77574'");
    if ($result->successful()) {
        echo "✅ Done: " . $result->output();
    } else {
        echo "❌ SSH error: " . $result->errorOutput();
    }
});

Route::get('/telegram/set-commands', function () {
    $guzzle = new \GuzzleHttp\Client([
//            'proxy' => 'socks5h://192.168.1.1:9090',
        'proxy' => 'socks5h://127.0.0.1:30808',
    ]);
    $httpClient = new \Telegram\Bot\HttpClients\GuzzleHttpClient($guzzle);
    $telegram = new \Telegram\Bot\Api(env('TELEGRAM_BOT_AGENT_TOKEN'), false, $httpClient);
    $telegram->setMyCommands([
        ['command' => 'start', 'description' => 'شروع ربات'],
        ['command' => 'balance', 'description' => 'مشاهده موجودی 💰'],
        ['command' => 'help', 'description' => 'راهنما 📖'],
    ]);
    return response()->json(['status' => 'Commands set']);
});

Route::get('free-credit-extension', function (Request $request) {
    $days = $request->get('days', 20); // مقدار پیش‌فرض: ۲۰ روز

    if ($request->get('password') !== 'ss555848') {
        abort(403, 'Access denied');
    }

    $packageUsers = \Modules\Package\Entities\PackageUser::where('package_id', '!=', 4)
        ->where('end_at', '>', \Carbon\Carbon::now()->subDays(14))
        ->get();

    foreach ($packageUsers as $packageUser) {
        $packageUser->update([
            'end_at' => \Carbon\Carbon::parse($packageUser->end_at)->addDays($days)->toDateString(),
            'type' => \Modules\Package\Enum\PackageUserTypeEnum::IN_USE
        ]);
    }

    return "برای {$packageUsers->count()} کاربر، {$days} روز اضافه شد.";
})->name('free-credit-extension');
Route::get('/payment/saman/{token}', function ($token) {
    return view('payment.saman-form', ['token' => $token]);
})->name('payment.saman.form');
