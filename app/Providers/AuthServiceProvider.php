<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Chat\app\Models\Chat;
use Modules\Chat\app\Policies\ChatPolicy;
use Modules\Course\app\Models\Course;
use Modules\Course\Policies\CoursePolicy;
use Modules\Core\Entities\Faq;
use Modules\Core\Policies\FaqPolicy;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\Food;
use Modules\Diet\Entities\FoodUnit;
use Modules\Diet\Entities\Meal;
use Modules\Diet\Policies\ConditionPolicy;
use Modules\Diet\Policies\DietPlanPolicy;
use Modules\Diet\Policies\FoodPolicy;
use Modules\Diet\Policies\FoodUnitPolicy;
use Modules\Diet\Policies\MealPolicy;
use Modules\Exercise\Entities\Exercise;
use Modules\Exercise\Entities\ExerciseBodyCategory;
use Modules\Exercise\Entities\ExercisePlanRequest;
use Modules\Exercise\Entities\ExercisePlanStrategy;
use Modules\Exercise\Policies\ExerciseBodyCategoryPolicy;
use Modules\Exercise\Policies\ExercisePlanRequestPolicy;
use Modules\Exercise\Policies\ExercisePlanStrategyPolicy;
use Modules\Exercise\Policies\ExercisePolicy;
use Modules\Package\Entities\Package;
use Modules\Package\Policies\PackagePolicy;
use Modules\Setting\Entities\Setting;
use Modules\Setting\Policies\SettingPolicy;
use Modules\User\Entities\User;
use Modules\User\Policies\RolePolicy;
use Modules\User\Policies\UserPolicy;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
        Exercise::class => ExercisePolicy::class,
        ExerciseBodyCategory::class => ExerciseBodyCategoryPolicy::class,
        ExercisePlanStrategy::class => ExercisePlanStrategyPolicy::class,
        ExercisePlanRequest::class => ExercisePlanRequestPolicy::class,
        Setting::class => SettingPolicy::class,
        Faq::class => FaqPolicy::class,
        Package::class => PackagePolicy::class,
        Food::class => FoodPolicy::class,
        FoodUnit::class => FoodUnitPolicy::class,
        Condition::class => ConditionPolicy::class,
        DietPlan::class => DietPlanPolicy::class,
        Meal::class => MealPolicy::class,
        Chat::class => ChatPolicy::class,
        Course::class => CoursePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(static function ($user, $ability) {
            if ($user->hasPermissionTo('SUPER_ADMIN')) {
                return true;
            }

            return null;
        });
    }
}
