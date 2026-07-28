<?php

namespace Modules\Diet\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Diet\Entities\Food;
use Modules\User\Entities\User;

class FoodPolicy
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
        return $user->hasPermissionTo('food');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('food.create');
    }

    public function delete(User $user, Food $food): bool
    {
        return $user->hasPermissionTo('food.delete');
    }

    public function update(User $user, Food $food): bool
    {
        return $user->hasPermissionTo('food.edit');
    }
}
