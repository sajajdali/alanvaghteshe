<?php

namespace Modules\Package\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Package\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(): void
    {

        $this->adminRoutes();
    }


    protected function adminRoutes(): void
    {
        Route::middleware(['web', 'auth', 'admin'])
            ->prefix('admin')
            ->as('admin.')
            ->group(module_path('Package', '/Routes/admin.php'));
    }

}
