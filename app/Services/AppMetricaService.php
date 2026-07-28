<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AppMetricaService
{
    protected string $postApiKey;
    protected int $applicationId;
    protected Client $client;

    public function __construct()
    {
        $this->postApiKey    = config('services.appmetrica.post_api_key');
        $this->applicationId = (int) config('services.appmetrica.application_id');

        $this->client = new Client([
            'base_uri' => 'https://api.appmetrica.yandex.com',
            'timeout'  => 5,
        ]);
    }

    /**
     * Event ساده مثل signup / login / purchase
     */
    public function sendEvent(
        string $profileId,
        string $eventName,
        array $params = [],
        ?int $timestamp = null
    ): bool {
        $timestamp = $timestamp ?? time(); // یونیکس‌تایم بر حسب ثانیه

        try {
            $response = $this->client->post('/logs/v1/import/events', [
                'query' => [
                    'post_api_key'    => $this->postApiKey,
                    'application_id'  => $this->applicationId,
                    'profile_id'      => $profileId,
                    'event_name'      => $eventName,
                    'event_timestamp' => $timestamp,
                    'event_json'      => !empty($params) ? json_encode($params, JSON_UNESCAPED_UNICODE) : null,
                    'session_type'    => 'foreground',
                ],
            ]);

            $this->sendGoogleAnalyticsEvent($profileId,$eventName, $params);
            return $response->getStatusCode() === 200;
        } catch (\Throwable $e) {
            \Log::error('AppMetrica sendEvent failed', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function sendGoogleAnalyticsEvent(string $userId, string $eventName, array $params = []): void
    {
        // پارامترهای پیش‌فرض برای source و status
        $defaultParams = [
            'source' => 'backend',
            'status' => 'success',
        ];

        // ادغام پارامترهای پیش‌فرض با پارامترهای اضافی
        $params = array_merge($defaultParams, $params);

        $url = 'https://www.google-analytics.com/mp/collect';
        $data = [
            'client_id' => $userId,  // استفاده از userId به جای client_id
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
            // ارسال درخواست به Google Analytics
            $response = $this->client->post($url, [
                'query' => [
//                    'measurement_id' => 'G-SX5YZRKC1B',  // Measurement ID شما
                    'measurement_id' => config('services.analytics.measurementId'),  // Measurement ID شما
//                    'api_secret' => 'Q0UMJDxkRpWTtcfsqmCaiQ',  // API Secret شما
                    'api_secret' => config('services.analytics.apiSecret'),  // API Secret شما
                ],
                'json' => $data,  // ارسال داده‌ها به صورت JSON
            ]);

            // بررسی وضعیت درخواست
            if (!$response->getStatusCode() === 204) {
                \Log::error('Failed to send event to Google Analytics', [
                    'status_code' => $response->getStatusCode(),
                ]);
            }
        } catch (\Throwable $e) {
            \Log::error('Google Analytics sendEvent failed', [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
