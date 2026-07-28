<?php

namespace Modules\Exercise\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Exercise\Entities\ExercisePlanStrategy;
use Modules\User\Entities\User;

class ExercisePlanStrategyPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('exercise_plan_strategy');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('exercise_plan_strategy.create');
    }

    public function delete(User $user, ExercisePlanStrategy $planStrategy): bool
    {
        return $user->hasPermissionTo('exercise_plan_strategy.delete');
    }

    public function update(User $user, ExercisePlanStrategy $planStrategy): bool
    {
        return $user->hasPermissionTo('exercise_plan_strategy.edit');
    }
}
