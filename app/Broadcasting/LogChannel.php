<?php

namespace App\Broadcasting;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class LogChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toLog')) {
            $data = $notification->toLog($notifiable);
            Log::channel('sms')->info('Notification Log:', $data);
        } else {
            throw new \Exception('Method toLog not defined in notification');
        }
    }
}
