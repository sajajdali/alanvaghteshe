<?php

namespace Modules\Chat\app\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Api\Transformers\Chat\ChatDetailResource;
use Modules\Chat\app\Models\Chat;

class UserAnswerChatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * Create a new event instance.
     */
    public function __construct(public Chat $chat)
    {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('chat.'.$this->chat->id);
    }
    public function broadcastAs()
    {
        return 'UserAnswer';
    }
    public function broadcastWith()
    {
        return (new ChatDetailResource($this->chat->chatDetails()->latest()->first()))->toArray(request());
    }
}
