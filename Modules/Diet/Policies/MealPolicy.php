<?php

namespace Modules\Diet\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Diet\Entities\Meal;
use Modules\User\Entities\User;

class MealPolicy
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
        return $user->hasPermissionTo('meal');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('meal.create');
    }

    public function delete(User $user, Meal $meal): bool
    {
        return $user->hasPermissionTo('meal.delete');
    }

    public function update(User $user, Meal $meal): bool
    {
        return $user->hasPermissionTo('meal.edit');
    }
}
