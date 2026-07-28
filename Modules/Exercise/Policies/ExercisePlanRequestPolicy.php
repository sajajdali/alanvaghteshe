<?php

namespace Modules\Exercise\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Exercise\Entities\ExercisePlanRequest;
use Modules\User\Entities\User;

class ExercisePlanRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission('exercise_plan_request', 'exercise_plan_request.own');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('exercise_plan_request.create');
    }

    public function delete(User $userAdmin, ExercisePlanRequest $exercisePlanRequest): bool
    {
        if ($userAdmin->hasPermissionTo('exercise_plan_request.delete') && $userAdmin->hasPermissionTo('exercise_plan_request')) {
            return true;
        }
        if ($userAdmin->hasPermissionTo('exercise_plan_request.delete') && $userAdmin->hasPermissionTo('exercise_plan_request.own') && in_array($exercisePlanRequest->user?->id, $userAdmin->my->pluck('id')->toArray(), false)) {
            return true;
        }

        return false;
    }

    public function update(User $userAdmin, ExercisePlanRequest $exercisePlanRequest): bool
    {
        if ($userAdmin->hasPermissionTo('exercise_plan_request.edit') && $userAdmin->hasPermissionTo('exercise_plan_request')) {
            return true;
        }
        if ($userAdmin->hasPermissionTo('exercise_plan_request.edit') && $userAdmin->hasPermissionTo('exercise_plan_request.own') && in_array($exercisePlanRequest->user?->id, $userAdmin->my->pluck('id')->toArray(), false)) {
            return true;
        }

        return false;
    }
}
