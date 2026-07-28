<?php

namespace App\Notifications;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\User\Entities\User;
use Telegram\Bot\Api;

class OrderTelegramMessageNotification extends Notification
{
    use Queueable;

    protected User $user;
    protected ?string $benefit;

    public function getTelegramBotToken(): string
    {
        return env('TELEGRAM_BOT_AGENT_TOKEN');
    }
    public function __construct(User $user , string $benefit =   '' )
    {
        $this->user = $user;
        $this->benefit = $benefit;
    }

    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    public function toTelegram($notifiable)
    {
        $chatId = $notifiable->userSession->chat_id ?? null;
        if (!$chatId) return null;

        $responder = $notifiable->last_responder_name ?? null;
        $hasResponse = !empty($responder);
        $timestamp = verta()->format('Y-m-d H:i');

        $newBenefit = number_format($this->benefit);
        $fullName = $this->user->full_name;
        return [
            'chat_id' => $chatId,
            'text' => "یک پرداخت جدید انجام شد:\n\n" .
                "💬 مبلغ پرداختی:  $newBenefit ریال \n" .
                "🎟️ نام کاربر: $fullName \n" .
                "🕓 زمان سفارش: $timestamp\n",
        ];
    }
}
