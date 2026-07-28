<?php

namespace Modules\User\Notifications;

use PgSql\Lob;
use Illuminate\Bus\Queueable;
use App\Broadcasting\SmsChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserSmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public ?string $template) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     */
    public function via($notifiable): array
    {
        return [SmsChannel::class];
    }
    public function toSms($notifiable)
    {
        return $notifiable->user->mobile;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     */
    public function toArray($notifiable): array
    {
        return [
            'template' => $this->template,
            'receptor' => $notifiable->mobile,
           'params' => [
                $notifiable->firstName ?? ' ',
            ],
        ];
    }
}
