<?php

namespace Modules\Exercise\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\Exercise\Entities\ExerciseBodyCategory;
use Modules\Exercise\Entities\ExercisePlanRequest;
use Modules\Exercise\Entities\ExercisePlanStrategy;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Exercise\Http\Controllers';

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
        $this->adminRoutes();
        $this->mapWebRoutes();
        $this->bindingModel();
    }

    public function bindingModel() : void {
        Route::model('exercise_body_category',ExerciseBodyCategory::class);
        Route::model('exercise_plan_request',ExercisePlanRequest::class);
        Route::model('exercise_plan_strategy',ExercisePlanStrategy::class);
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Exercise', '/Routes/web.php'));
    }

    protected function adminRoutes(): void
    {
        Route::middleware(['web', 'auth', 'admin'])
            ->prefix('admin')
            ->as('admin.')
            ->group(module_path('Exercise', '/Routes/admin.php'));
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
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Exercise', '/Routes/api.php'));
    }
}
