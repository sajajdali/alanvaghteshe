<?php

namespace App\Http\Controllers;

use App\UserSession;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Modules\User\Enum\UserMetaEnum;
use Telegram\Bot\Api;
use Telegram\Bot\HttpClients\GuzzleHttpClient;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        $useProxy = filter_var(env('ACTIVE_PROXY'), FILTER_VALIDATE_BOOLEAN);

        $options = [];

        if ($useProxy) {
            $options['proxy'] = env('PROXY_URL');
        }

        $guzzle = new \GuzzleHttp\Client($options);
        $httpClient = new \Telegram\Bot\HttpClients\GuzzleHttpClient($guzzle);
        $telegram = new \Telegram\Bot\Api(env('TELEGRAM_BOT_TOKEN'), false, $httpClient);

        $update = $telegram->getWebhookUpdate();

        $callback = $update->getCallbackQuery();
        if ($callback) {
            $chatId = $callback->getMessage()->getChat()->getId();
            $data = $callback->getData();

            if ($data === 'exit_bot') {
                // حذف دکمه اولیه خروج از ربات
                $telegram->editMessageReplyMarkup([
                    'chat_id' => $chatId,
                    'message_id' => $callback->getMessage()->getMessageId(),
                    'reply_markup' => json_encode(['inline_keyboard' => []]),
                ]);

                $telegram->answerCallbackQuery([
                    'callback_query_id' => $callback->getId(),
                ]);

                // ارسال پیام تایید خروج
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "آیا مطمئنی که می‌خوای از ربات خارج بشی؟",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [['text' => '✅ بله، خارج شو', 'callback_data' => 'confirm_exit']],
                            [['text' => '❌ نه، منصرف شدم', 'callback_data' => 'cancel_exit']],
                        ]
                    ]),
                ]);
                return response('ok', 200);
            }


            if ($callback && str_starts_with($callback->getData(), 'url_after_answering_')) {
                $chatId = $callback->getMessage()->getChat()->getId();
                $messageId = $callback->getMessage()->getMessageId();

                // استخراج userId و ticketNumber
                $payload = str_replace('url_after_answering_', '', $callback->getData());
                [$userId, $ticketNumber, $ticketChatId] = explode('_', $payload);

                // ویرایش متن پیام برای تغییر وضعیت
                $oldText = $callback->getMessage()->getText();
                $newText = str_replace('📌 وضعیت: در انتظار پاسخ', '📌 وضعیت: پاسخ داده شده', $oldText);

                $telegram->editMessageText([
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'text' => $newText,
                ]);

                // ارسال لینک پاسخ‌دهی با استفاده از ticket number
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "برای پاسخ دادن کلیک کن:\nhttps://crm.alanvaghteshe.com/admin/chat?chatId=$ticketNumber"
                ]);

                return response('ok', 200);
            }

            if ($data === 'confirm_exit') {
                $telegram->answerCallbackQuery([
                    'callback_query_id' => $callback->getId(),
                ]);

                $session = UserSession::where('chat_id', $chatId)->first();
                if ($session) {
                    $session->state = 'start';
                    $session->user_id = null;
                    $session->data = null;
                    $session->save();
                }

                // حذف پیام تایید خروج
                $telegram->deleteMessage([
                    'chat_id' => $chatId,
                    'message_id' => $callback->getMessage()->getMessageId(),
                ]);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "✅ با موفقیت از ربات خارج شدی. برای ورود مجدد، لطفاً کد یکتا رو بفرست.",
                    'reply_markup' => json_encode(['remove_keyboard' => true]),
                ]);

                return response('ok', 200);
            }

            if ($data === 'cancel_exit') {
                $telegram->answerCallbackQuery([
                    'callback_query_id' => $callback->getId(),
                ]);

                // حذف پیام تایید خروج
                $telegram->deleteMessage([
                    'chat_id' => $chatId,
                    'message_id' => $callback->getMessage()->getMessageId(),
                ]);

                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => "عملیات خروج لغو شد ✅",
                ]);
                return response('ok', 200);
            }
        }

        $message = $update->getMessage();

        if ($message && $message->getText()) {
            $text = $message->getText();
            $chatId = $message->getChat()->getId();

            try {
                // دریافت یا ساخت UserSession مرتبط با chat_id
                $session = UserSession::firstOrCreate(['chat_id' => $chatId], [
                    'user_id' => null,
                    'state' => 'start',
                ]);

                switch ($session->state) {
                    case 'waiting_for_agreement':
                        if (trim($text) === 'خروج از ربات') {
                            $session->state = 'start';
                            $session->user_id = null;
                            $session->data = null;
                            $session->save();

                            $telegram->sendMessage([
                                'chat_id' => $chatId,
                                'text' => "🚪 شما با موفقیت از ربات خارج شدید. برای ورود مجدد، لطفاً کد یکتا را ارسال کنید.",
                                'reply_markup' => json_encode(['remove_keyboard' => true]),
                            ]);
                            break;
                        }

                        $telegram->sendMessage([
                            'chat_id' => $chatId,
                            'text' => "⚠️ لطفاً چیزی ارسال نکن. ما به‌زودی پیام‌ها را برایت می‌فرستیم.",
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    [['text' => '🚪 میخوای از ربات خارج بشی؟', 'callback_data' => 'exit_bot']]
                                ]
                            ]),
                        ]);
                        break;

                    case 'waiting_for_phone':
                        $session->data = array_merge($session->data ?? [], ['phone' => $text]);
                        $session->state = 'waiting_for_agreement';
                        $session->save();

                        $telegram->sendMessage([
                            'chat_id' => $chatId,
                            'text' => "✅ ثبت‌نامت کامل شد!",
                        ]);
                        break;

                    default:
                        // فرض بر این است که state فعلی "start" است و کاربر کد یکتا وارد می‌کند
                        if (!isset($session->data['waiting_for_code'])) {
                            $session->data = array_merge($session->data ?? [], ['waiting_for_code' => true]);
                            $session->save();

                            $telegram->sendMessage([
                                'chat_id' => $chatId,
                                'text' => "👋 لطفاً کد یکتای خود را وارد کن:",
                            ]);
                            break;
                        }


                        // چک کردن کد وارد شده در جدول کاربران
                        $user = User::whereHas('metas', function ($q) use ($text) {
                            $q->where([
                                ['meta_key', UserMetaEnum::TELEGRAM_CHAT_ID],
                                ['meta_value', $text],
                            ]);
                        })->first();

                        if ($user) {
                            $session->user_id = $user->id;
                            $session->state = 'waiting_for_agreement';
                            $session->data = null;
                            $session->save();

                            $telegram->sendMessage([
                                'chat_id' => $chatId,
                                'text' => "✅ خوش آمدی {$user->name}!\nاز این به بعد همه چت ها برای شما در گروه ارسال میشه و میتونید جواب بدید.\n\n",
                            ]);
                        } else {
                            $telegram->sendMessage([
                                'chat_id' => $chatId,
                                'text' => "❌ کد وارد شده نادرست است. لطفاً دوباره تلاش کن.",
                            ]);
                        }

                        break;
                }

            } catch (\Exception $e) {
                \Log::error('Telegram handle error: ' . $e->getMessage());
            }
        }

        return response('ok', 200);
    }

    public function setProxy()
    {

        $options = [];

        if (env('ACTIVE_PROXY') === true || env('ACTIVE_PROXY') === 'true') {
            $options['proxy'] = env('PROXY_URL');
        }

        $guzzle = new \GuzzleHttp\Client($options);
        $httpClient = new \Telegram\Bot\HttpClients\GuzzleHttpClient($guzzle);
        $telegram = new \Telegram\Bot\Api(env('TELEGRAM_BOT_TOKEN'), false, $httpClient);

        $telegram->setWebhook([
            'url' => 'https://crm.alanvaghteshe.com/telegram/webhook'
        ]);
    }

    public function setProxyReferral()
    {
        $useProxy = filter_var(env('ACTIVE_PROXY'), FILTER_VALIDATE_BOOLEAN);

        $options = [];

        if ($useProxy) {
            $options['proxy'] = env('PROXY_URL');
        }

        $guzzle = new \GuzzleHttp\Client($options);
        $httpClient = new \Telegram\Bot\HttpClients\GuzzleHttpClient($guzzle);
        $telegram = new \Telegram\Bot\Api(env('TELEGRAM_BOT_AGENT_TOKEN'), false, $httpClient);

        $telegram->setWebhook([
            'url' => 'https://crm.alanvaghteshe.com/telegram/webhook_referral'
        ]);

        dd("done");
    }
}
