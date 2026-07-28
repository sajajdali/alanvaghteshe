<?php

namespace App\Broadcasting;

use Illuminate\Notifications\Notification;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\User\Entities\User;

class SmsChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user): array|bool
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function send($notifiable, Notification $notification): void
    {
        $smsSandbox = env('SMS_SEND_SANDBOX');
        $apiToken = setting(SettingKeyEnum::SMS_API_TOKEN);
        $data = $notification->toArray($notifiable);
        //data should have receptor and template and at least one params
        if ($smsSandbox !== true && $apiToken != '' && isset($data['receptor']) && isset($data['template']) && isset($data['params']) && count($data['params']) > 0) {
            $condition = [
                'receptor' => $data['receptor'],
                'template' => $data['template'],
            ];
            foreach ($data['params'] as $parameter) {
                $condition['param'][] = $parameter;
            }
            \Illuminate\Support\Facades\Http::withToken($apiToken)->get('https://shsms.ir/api/v1/sendms', $condition);
        }
    }
}
