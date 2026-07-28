<?php

namespace Modules\Exercise\Events;

use Illuminate\Queue\SerializesModels;

class ExercisePlanRequestCompletedEvent
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public int $exercisePlanRequestId)
    {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
