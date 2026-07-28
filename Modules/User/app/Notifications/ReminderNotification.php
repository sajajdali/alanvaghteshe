<?php

namespace Modules\User\app\Notifications;

use App\Broadcasting\LogChannel;
use App\Broadcasting\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Reminder\Enum\ReminderParametersEnum;

class ReminderNotification extends Notification
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

    public function toLog($notifiable): array
    {
        return [
            'template' => $this->template,
            'receptor' => $notifiable->mobile,
            'params' => $this->getParams($notifiable),
        ];
    }

    private function getParams($notifiable)
    {
        $assignEachParameter = [];
        if (isset($this->params)) {
            foreach ($this->params as $key => $eachPram) {
                $assignEachParameter[] = match ($eachPram) {
                    ReminderParametersEnum::ID => $notifiable->id ,
                    ReminderParametersEnum::FIRST_NAME => $notifiable->first_name ?? 'کاربر',
                    ReminderParametersEnum::LAST_NAME => $notifiable->last_name ?? 'کاربر',
                    default => '',
                };
            }
        }
        return $assignEachParameter;
    }

    public function toSms($notifiable)
    {
        return $notifiable->user->mobile;
    }

    public function toArray($notifiable): array
    {
        $smsSandbox = env('SMS_SEND_SANDBOX');
        if ($smsSandbox !== true) {
            $params = $this->getParams($notifiable);
            return [
                'template' => $this->template,
                'receptor' => $notifiable->mobile,
                'params' => $params,
            ];
        }else{
            return [];
        }
    }
}
