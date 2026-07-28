<?php

namespace App\Notifications;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Telegram\Bot\Api;

class ChatRoomMessageNotification extends Notification
{
    use Queueable;

    protected string $message;
    protected ?int $ticketNumber;
    protected ?int $ticketChatId;

    public function __construct(string $message , int $ticketNumber = null , int $ticketChatId = null )
    {
        $this->ticketChatId = $ticketChatId;
        $this->message = $message;
        $this->ticketNumber = $ticketNumber;
    }

    public function getTelegramBotToken(): string
    {
        return env('TELEGRAM_BOT_TOKEN');
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
        $responderName = $responder ?? 'ندارد';
        $status = $hasResponse ? 'جواب داده شده' : 'در انتظار پاسخ';
        $timestamp = verta()->format('Y-m-d H:i');

        return [
            'chat_id' => $chatId,
            'text' => "📩 سوال جدید مطرح شده:\n\n" .
                "🗂 وضعیت: سوال جدید\n" .
                "💬 سوال:  $this->message\n" .
                "🎟️ شماره چت: $this->ticketNumber\n" .
                "🕓 زمان ارسال: $timestamp\n" .
                "📌 وضعیت: $status",
            'reply_markup' => $hasResponse ? null : json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => '📝 جواب می‌دم',
                            'callback_data' => 'url_after_answering_' . $notifiable->id . '_' . $this->ticketNumber . '_' . $this->ticketChatId                        ]
                    ]
                ]
            ]),
        ];
    }
}
