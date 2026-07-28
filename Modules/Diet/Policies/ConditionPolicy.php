<?php

namespace Modules\Diet\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Diet\Entities\Condition;
use Modules\User\Entities\User;

class ConditionPolicy
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
        return $user->hasPermissionTo('condition');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('condition.create');
    }

    public function delete(User $user, Condition $condition): bool
    {
        return $user->hasPermissionTo('condition.delete');
    }

    public function update(User $user, Condition $condition): bool
    {
        return $user->hasPermissionTo('condition.edit');
    }
}
