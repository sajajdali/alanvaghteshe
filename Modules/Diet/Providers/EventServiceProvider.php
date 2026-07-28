<?php

namespace Modules\Diet\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Diet\app\Events\DietRequestAdded;
use Modules\Diet\app\Events\UserFoodConsumptionUpdated;
use Modules\Diet\app\Listeners\ClearUserFoodConsumptionCache;
use Modules\Diet\app\Listeners\InvalidateUserMealsCache;
use Modules\Diet\app\Listeners\ShowDietRequestPopup;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserFoodConsumptionUpdated::class => [
            ClearUserFoodConsumptionCache::class,
        ],
        DietRequestAdded::class => [
            InvalidateUserMealsCache::class,
            ShowDietRequestPopup::class
        ]
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
