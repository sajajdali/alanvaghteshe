<?php

namespace Modules\Api\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Api\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {

        Route::prefix('v1')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Api', '/Routes/api_v1_public.php'));

        Route::prefix('v1')
            ->middleware(['api', 'auth:sanctum'])
            ->namespace($this->moduleNamespace)
            ->group(module_path('Api', '/Routes/api_v1_user.php'));
    }
}
