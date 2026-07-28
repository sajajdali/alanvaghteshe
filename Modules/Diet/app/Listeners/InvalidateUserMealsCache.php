<?php

namespace Modules\Diet\app\Listeners;

use Cache;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Diet\app\Events\DietRequestAdded;
use Modules\Diet\Entities\DietRequest;

class InvalidateUserMealsCache
{
    public function handle(DietRequestAdded $event)
    {
        $user = $event->dietRequest->user;
        $cacheKey = 'user_meals_' . $user->id;
        $cacheTag = 'user_' . $user->id;

        // Remove the specific cache key
        Cache::tags($cacheTag)->forget($cacheKey);

    }
}
