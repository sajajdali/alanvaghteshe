<?php

namespace Modules\Course\app\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Course\app\Models\CourseAppBanner;
use Modules\Course\app\Models\Course;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     */
    protected string $moduleNamespace = 'Modules\\Course\\Http\\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->adminRoutes();

        $this->mapWebRoutes();
        $this->bindingModel();
    }

    protected function adminRoutes(): void
    {
        Route::middleware(['web', 'auth', 'admin'])
            ->prefix('admin')
            ->as('admin.')
            ->group(module_path('Course', '/routes/admin.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Course', '/routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Course', '/routes/api.php'));
    }

    public function bindingModel(): void
    {
        Route::model('course', Course::class);
        Route::model('banner', CourseAppBanner::class);
    }
}
