<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Notifications\ChatRoomMessageNotification;
use App\UserSession;
use Illuminate\Http\Request;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Api\Transformers\Chat\ChatResource;
use Modules\Chat\app\Events\UserAnswerChatEvent;
use Modules\Chat\app\Models\Chat;
use Modules\Chat\Enum\ChatDetailTypeEnum;
use Modules\Chat\Enum\ChatStatusEnum;

class UserChatController extends Controller
{
    use ApiHandlerTrait;

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {

        $chat = Chat::getRoom(user_id: $request->user()->id);
        return $this->ok(ChatResource::make($chat));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $chatId = $request->input('chat_id');
        $chat = Chat::find($chatId);
        if($chat == null || $chat->user_id !== $request->user()->id){
            return $this->notFound();
        }
        if($chat->ban){
            return $this->requestException(
                data: [
                    'status' => false,
                    'message' => 'شما از ارسال پیام محروم هستید'
                ]
            );
        }
        $message = $request->input('message');
        if($message == null){
            return $this->requestException(
                data: [
                    'status' => false,
                    'message' => 'پیام نمیتواند خالی باشد'
                ]
            );
        }

        $chatDetail = $chat->chatDetails()->create([
            'user_id' => $request->user()->id,
            'type' => ChatDetailTypeEnum::MESSAGE,
            'content' => $message,
        ]);
        $chat->increment('new_message_by_user');
        $chat->update([
            'status' => ChatStatusEnum::USER_SEND_QUESTION
        ]);
        UserAnswerChatEvent::dispatch($chat);

        $sessions = UserSession::with('user')
            ->whereNotNull('user_id')
            ->where('type', "!=", '2')
            ->where('state', 'waiting_for_agreement')
            ->get();

        foreach ($sessions as $session) {
            if ($session->user && $session->user->telegram_chat_id) {
                $session->user->notify(new ChatRoomMessageNotification($message  , ticketNumber: $chat->id));
            }
        }

        return $this->created(ChatResource::make(Chat::find($chatId)));
    }

}
