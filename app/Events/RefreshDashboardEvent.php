<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\User\Entities\User;

class RefreshDashboardEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user)
    {
        //
    }


    /**
     * @return Channel[]
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->user->id),
        ];
    }

    public function broadcastWith(): array
    {
        return  [ 'user_id' => $this->user->id];
    }
    public function broadcastAs()
    {
        return env('PUSHER_CHANEL_ADMIN');
    }
}
