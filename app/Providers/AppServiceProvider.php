<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Modules\Setting\Enum\SettingKeyEnum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        $activeGatway = setting(SettingKeyEnum::PAYMEN_ACTIVE_DRIVER);

        $payStarSign = setting(SettingKeyEnum::PAYMENT_PAYSTAR_SIGN);
        $payStarToken = setting(SettingKeyEnum::PAYMENT_PAYSTAR_TOKEN);

        if ($activeGatway == 'saman') {
            config([
                'payment.default' => 'saman',
                'payment.drivers.saman.merchantId' => $payStarToken,
            ]);
        } else if ($activeGatway == 'paystar') {
            config([
                'payment.default' => 'paystar',
                'payment.drivers.paystar.gatewayId' => $payStarToken,
                'payment.drivers.paystar.signKey' => $payStarSign,
            ]);
        }
        else  {
            config([
                'payment.default' => 'zarinpal',
                'payment.drivers.zarinpal.merchantId' => setting(SettingKeyEnum::PAYMENT_ZARINPAL_MERCHENT),
            ]);
        }

    }
}
