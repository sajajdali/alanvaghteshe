<?php

namespace Modules\Chat\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Modules\Chat\app\Events\AdminAnswerChatEvent;
use Modules\Chat\app\Models\Chat;
use Modules\Chat\Enum\ChatDetailTypeEnum;
use Modules\Chat\Enum\ChatStatusEnum;

class ChatView extends Component
{
    #[Url]
    public int $chatId = 0;

    public $chatMessage = '';


    #[Computed]
    public function chatList(){
        Chat::firstWhere('id', $this->chatId)?->update([
            'new_message_by_user' => 0,
        ]);
        return Chat::firstWhere('id', $this->chatId)?->chatDetails()->orderByDesc('id')->get()->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });
    }

    #[Computed]
    public function chat(){
        return Chat::firstWhere('id', $this->chatId);
    }

    public function sendMessage(){
        if(empty($this->chatMessage)){
            $this->dispatch('error', message:'متن پیام خود را وارد کنید.');
            return;
        }
        if($this->chat == null){
            $this->dispatch('error', message:'لطفا گفت و گوی مد نظر خود را انتخاب کنید');
            return;
        }
        $this->chat?->chatDetails()->create([
            'content' => $this->chatMessage,
            'type' => ChatDetailTypeEnum::MESSAGE,
            'user_id' => auth()->id(),
        ]);
        $this->chat?->increment('new_message_by_support');
        $this->chat?->update([
            'status' => ChatStatusEnum::ANSWERED,
            'new_message_by_user' => 0,
        ]);
        //clear input
        $this->chatMessage = '';
        $appEnvironment = env('APP_ENVIRONMENT', 'production');

        if ($appEnvironment !== 'local') {
            AdminAnswerChatEvent::dispatch($this->chat);
        }
    }

    #[On('echo:chat.{chatId},.UserAnswer')]
    public function newMessage(): void
    {
        unset($this->chatList);
    }

    public function selectChatRoom(int $chatId): void
    {
        $this->chatId = $chatId;
        $this->dispatch('chatRoomSelected');
    }

    public function render()
    {
        //get all chats order by type and latest message
//        $chats = Chat::orderBy('status', 'desc')
//            ->orderBy('new_message_by_user', 'desc')
//            ->whereHas('chatDetails')
//            ->get();
        $chats = Chat::orderByDesc('updated_at')
            ->whereHas('chatDetails')
            ->get();
        return view('chat::livewire.chat-view', compact('chats'));
    }
}
