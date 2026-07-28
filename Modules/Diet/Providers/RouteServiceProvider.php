<?php

namespace Modules\Diet\Providers;

use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\Meal;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\FoodUnit;
use Modules\Diet\Entities\BasicFood;
use Illuminate\Support\Facades\Route;
use Modules\Diet\Entities\DietRequest;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\Recipe\app\Models\Recipe;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Diet\Http\Controllers';

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
            ->group(module_path('Diet', '/Routes/api.php'));
    }

    protected function adminRoutes(): void
    {
        Route::middleware(['web', 'auth', 'admin'])
            ->prefix('admin')
            ->as('admin.')
            ->group(module_path('Diet', '/Routes/admin.php'));
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
            ->group(module_path('Diet', '/Routes/web.php'));
        Route::middleware('web')
            ->group(module_path('Diet', '/Routes/Livewire.php'));
    }

    public function bindingModel(): void
    {
        Route::model('meal', Meal::class);
        Route::model('diet_request', DietRequest::class);
        Route::model('food', Food::class);
        Route::model('food_unit', FoodUnit::class);
        Route::model('diet_plan', DietPlan::class);
        Route::model('basic_food', BasicFood::class);
        Route::model('recipe', Recipe::class);
    }
}
