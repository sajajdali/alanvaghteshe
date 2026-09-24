<?php

namespace Modules\Onboarding\Providers;

use Illuminate\Support\ServiceProvider;

class OnboardingServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Onboarding';

    protected string $moduleNameLower = 'onboarding';

    public function boot(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(
            module_path($this->moduleName, 'Resources/views'),
            $this->moduleNameLower
        );
    }
}
