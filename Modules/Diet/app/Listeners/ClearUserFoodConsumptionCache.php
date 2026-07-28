<?php

namespace Modules\Diet\app\Listeners;

use Cache;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Diet\app\Events\UserFoodConsumptionUpdated;

class ClearUserFoodConsumptionCache
{
    public function handle(UserFoodConsumptionUpdated $event)
    {
        $user = $event->user;
        $cacheTag = 'user_' . $user->id;

        // Clear all related caches for this user
        Cache::tags($cacheTag)->flush();
    }
}
