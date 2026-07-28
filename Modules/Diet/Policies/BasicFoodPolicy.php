<?php

namespace Modules\Diet\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Diet\Entities\BasicFood;
use Modules\User\Entities\User;

class BasicFoodPolicy
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
        return $user->hasPermissionTo('basic_food');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('basic_food.create');
    }

    public function delete(User $user, BasicFood $basicFood): bool
    {
        return $user->hasPermissionTo('basic_food.delete');
    }

    public function update(User $user, BasicFood $basicFood): bool
    {
        return $user->hasPermissionTo('basic_food.edit');
    }
}
