<?php

namespace App\Http\Controllers;

use App\UserSession;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Modules\User\Enum\UserMetaEnum;
use Telegram\Bot\Api;
use Telegram\Bot\HttpClients\GuzzleHttpClient;

class TelegramControllerReferral extends Controller
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
        $telegram = new \Telegram\Bot\Api(env('TELEGRAM_BOT_AGENT_TOKEN'), false, $httpClient);



        $update = $telegram->getWebhookUpdate();
        $message = $update->getMessage();
        $callback = $update->getCallbackQuery();

        if ($callback) {
            $callbackData = $callback->getData();

            $chatId = $callback->getMessage()->getChat()->getId();

            switch ($callbackData) {
                case 'balance':
                    // اینجا یعنی کاربر روی دکمه "💰 مشاهده موجودی فعلی شما" زده
                    $telegram->deleteMessage([
                        'chat_id' => $chatId,
                        'message_id' => $callback->getMessage()->getMessageId(),
                    ]);
                    //  invite friends
                    $chatIdMessage = $message->getChat()->getId();
                    $session = UserSession::where('chat_id', $chatIdMessage)->where('type' , 2)->first();
                    $user = $session->user;
                    $inviteFriends = $user->inviteFriends;
                    $benefit = 0;
                    $purchasedUsersCount = 0;
                    if ($inviteFriends->count()){
                        $benefit = $inviteFriends->sum('benefit');
                        $purchasedUsersCount = $inviteFriends->where('benefit' , '>' , 0)->count();
                    }


                    $telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "👥 تعداد افراد معرفی‌شده:  ".$inviteFriends->count()."  نفر\n💳  کاربران پرداخت کننده : ".$purchasedUsersCount." نفر\n💰 موجودی فعلی شما:  ".number_format($benefit)."  تومان\n\n📩 پس از هر پرداخت یک پیغام در ربات میاد.\n⏳ منتظر باشید...",
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [['text' => '🔙 بازگشت', 'callback_data' => 'back_to_main']]
                            ]
                        ])
                    ]);
                    break;

                case 'confirm_exit':
                    $telegram->answerCallbackQuery([
                        'callback_query_id' => $callback->getId(),
                    ]);

                    $session = UserSession::where('chat_id', $chatId)->where('type' , 2)->first();
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
                    break;
                case 'exit_bot':

                    $telegram->deleteMessage([
                        'chat_id' => $chatId,
                        'message_id' => $callback->getMessage()->getMessageId(),
                    ]);
                    // دکمه خروج
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
                    break;

                case 'back_to_main':
                    $telegram->editMessageText([
                        'chat_id' => $chatId,
                        'message_id' => $callback->getMessage()->getMessageId(),
                        'text' => "👥 تعداد افراد معرفی‌شده: ۵ نفر\n💳 پرداخت‌شده‌ها: ۱۲۵٬۰۰۰ تومان\n💰 موجودی فعلی شما: ۱۲٬۰۰۰ تومان\n\n📩 پس از هر پرداخت یک پیغام در ربات میاد.\n⏳ منتظر باشید...",
                    ]);
                    $telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "⚠️ نیازی نیست چیزی بفرستید چون ما همه پرداخت ها رو اینجا میفرستیم \n اگر میخوای موجودیتو ببینی هم از کلید زیر استفاده کن.",
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [['text' => '🚪 میخوای از ربات خارج بشی؟', 'callback_data' => 'exit_bot']],
                                [['text' => '💰 مشاهده موجودی فعلی شما', 'callback_data' => 'balance']]
                            ]
                        ]),
                    ]);
                    break;

                default:
                    $telegram->sendMessage([
                        'chat_id' => $chatId,
                        'text' => "❓ فرمان نامشخص است.",
                    ]);
                    break;
            }

            return response('ok', 200);
        }

        if ($message && $message->getText()) {
            $text = $message->getText();
            $chatId = $message->getChat()->getId();

            try {
                // دریافت یا ساخت UserSession مرتبط با chat_id
                $session = UserSession::firstOrCreate(['chat_id' => $chatId], [
                    'user_id' => null,
                    'type'  => 2,
                    'state' => 'start',
                ]);

                switch ($session->state) {
                    case 'waiting_for_agreement':
                        if (trim($text) === 'balance') {

                            $telegram->sendMessage([
                                'chat_id' => $chatId,
                                'text' => "🚪 شما با موفقیت از ربات خارج شدید. برای ورود مجدد، لطفاً کد یکتا را ارسال کنید.",
                                'reply_markup' => json_encode(['remove_keyboard' => true]),
                            ]);
                            break;
                        }

                        elseif (trim($text) === 'خروج از ربات') {
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
                            'text' => "⚠️ نیازی نیست چیزی بفرستید چون ما همه پرداخت ها رو اینجا میفرستیم \n اگر میخوای مجودیتو ببینی هم از کلید زیر استفاده کن.",
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    [['text' => '🚪 میخوای از ربات خارج بشی؟', 'callback_data' => 'exit_bot']],
                                    [['text' => '💰 مشاهده موجودی فعلی شما', 'callback_data' => 'balance']]
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
                            $session->type = 2;
                            $session->save();

                            $telegram->sendMessage([
                                'chat_id' => $chatId,
                                'text' => "✅ خوش آمدی {$user->name}!\n از این پس همه پرداخت هایی که مربوط به شما باشه اینجا ارسال میشه.\n\n",
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

}
