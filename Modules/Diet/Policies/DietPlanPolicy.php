<?php

namespace Modules\Diet\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Diet\Entities\DietPlan;
use Modules\User\Entities\User;

class DietPlanPolicy
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
        return $user->hasPermissionTo('diet_plan');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('diet_plan.create');
    }

    public function delete(User $user, DietPlan $dietPlan): bool
    {
        return $user->hasPermissionTo('diet_plan.delete');
    }

    public function update(User $user, DietPlan $dietPlan): bool
    {
        return $user->hasPermissionTo('diet_plan.edit');
    }
}
