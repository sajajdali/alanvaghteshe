<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Log;
use Telegram\Bot\Api;

class TelegramChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toTelegram')) {
            return;
        }

        $message = $notification->toTelegram($notifiable);

        if (!$message || !isset($message['chat_id'], $message['text'])) {
            return;
        }

        try {
            $useProxy = filter_var(env('ACTIVE_PROXY'), FILTER_VALIDATE_BOOLEAN);

            $options = [];

            if ($useProxy) {
                $options['proxy'] = env('PROXY_URL');
            }

            $guzzle = new \GuzzleHttp\Client($options);
            $httpClient = new \Telegram\Bot\HttpClients\GuzzleHttpClient($guzzle);

            $token = method_exists($notification, 'getTelegramBotToken')
                ? $notification->getTelegramBotToken()
                : env('TELEGRAM_BOT_TOKEN');

            $telegram = new \Telegram\Bot\Api($token, false, $httpClient);

            $telegram->sendMessage($message);
        } catch (\Exception $e) {
            \Log::error('TelegramChannel error: ' . $e->getMessage());
        }
    }
}
