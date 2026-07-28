<?php

namespace Modules\Admin\app\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class WhatsappMessageReceived implements ShouldBroadcast
{
    use SerializesModels;

    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function broadcastOn()
    {
        return new Channel('whatsapp');
    }

    public function broadcastAs()
    {
        return 'message.received';
    }
}
