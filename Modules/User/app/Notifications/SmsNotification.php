<?php

namespace Modules\User\app\Notifications;

use App\Broadcasting\LogChannel;
use App\Broadcasting\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SmsNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ?string $template, public ?array $params)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        if (env('SMS_SEND_SANDBOX', false)) {
            return [LogChannel::class]; // Use logging channel
        } else {
            return [SmsChannel::class]; // Use your custom SMS channel
        }
    }

    public function toSms($notifiable)
    {
        return $notifiable->user->mobile;
    }

    public function toLog($notifiable): array
    {
        return [
            'template' => $this->template,
            'receptor' => $notifiable->mobile,
            'params' => $this->params,
        ];
    }


    public function toArray($notifiable): array
    {

        return [
            'template' => $this->template,
            'receptor' => $notifiable->mobile,
            'params' => $this->params,
        ];
    }
}
