<?php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;

class UserMessageNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public string $title, public string $excerpt, public string $message)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     */
    public function via($notifiable): array
    {
        if (method_exists($notifiable,
                'routeNotificationForFcm') && is_array($notifiable->routeNotificationForFcm()) && count($notifiable->routeNotificationForFcm()) > 0) {
            return ['database', FcmChannel::class];
        }
        return ['database'];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->data([
                'title' => $this->title,
                'excerpt' => $this->excerpt,
                'message' => $this->message,
            ])
            ->custom([
                'android' => [
                    'notification' => [
                        'color' => '#0A0A0A',
                    ],
                    'fcm_options' => [
                        'analytics_label' => 'message',
                    ],
                ],
            ])
            ->notification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->title($this->title)
                ->body($this->excerpt)
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'message' => $this->message,
        ];
    }
}
