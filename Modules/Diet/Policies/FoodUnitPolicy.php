<?php

namespace Modules\Diet\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Diet\Entities\FoodUnit;
use Modules\User\Entities\User;

class FoodUnitPolicy
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
        return $user->hasPermissionTo('food_unit');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('food_unit.create');
    }

    public function delete(User $user, FoodUnit $foodUnit): bool
    {
        return $user->hasPermissionTo('food_unit.delete');
    }

    public function update(User $user, FoodUnit $foodUnit): bool
    {
        return $user->hasPermissionTo('food_unit.edit');
    }
}
