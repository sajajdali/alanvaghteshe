<?php

namespace Modules\User\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class UserMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string  $title,
        public string  $excerpt,
        public string  $message,
        public mixed   $params = null,
        public ?string $link = null,
    ) {}

    /**
     * کانال‌های ارسال نوتیفیکیشن
     */
    public function via($notifiable): array
    {
        return [FcmChannel::class];
    }

    /**
     * پیام مخصوص FCM
     */
    public function toFcm($notifiable): FcmMessage
    {
        // فقط برای دیباگ، ببینیم اصلاً این متد صدا می‌خوره یا نه
        Log::info('toFcm called for user', [
            'user_id' => $notifiable->id ?? null,
        ]);

        // اینجا به توکن کاری نداریم؛ خود پکیج از routeNotificationForFcm استفاده می‌کنه
        // فقط اگر خواستی، لاگ کن ببینی چی برمی‌گرده
        $token = $notifiable->routeNotificationForFcm();
        Log::info('FCM token from routeNotificationForFcm', [
            'user_id' => $notifiable->id ?? null,
            'token'   => $token,
        ]);

        // حتماً همه valueها در data باید string باشند
        $data = [
            'title'   => (string) $this->title,
            'excerpt' => (string) $this->excerpt,
            'message' => (string) $this->message,
        ];

        if (!empty($this->link)) {
            $data['link'] = (string) $this->link;
        }

        if ($this->params !== null) {
            if (is_scalar($this->params)) {
                $data['params'] = (string) $this->params;
            } else {
                $data['params'] = json_encode($this->params, JSON_UNESCAPED_UNICODE);
            }
        }

        Log::info('FCM payload data', [
            'user_id' => $notifiable->id ?? null,
            'data'    => $data,
        ]);

        return (new FcmMessage(
            notification: new FcmNotification(
                title: $this->title,
                body:  $this->excerpt,
            )
        ))
            ->data($data);
        // برای ساده شدن فعلاً custom رو حذف می‌کنیم؛
        // بعداً اگر همه‌چیز اوکی بود دوباره android config اضافه می‌کنیم
    }

    /**
     * داده‌هایی که در دیتابیس نوتیفیکیشن ذخیره می‌شود (اختیاری)
     */
    public function toArray($notifiable): array
    {
        return [
            'title'   => $this->title,
            'excerpt' => $this->excerpt,
            'message' => $this->message,
            'link'    => $this->link,
            'params'  => $this->params,
        ];
    }
}
