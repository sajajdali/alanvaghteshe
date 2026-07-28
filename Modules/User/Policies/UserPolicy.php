<?php

namespace Modules\User\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\User\Entities\User;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission('user', 'user.own');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('user.create');
    }

    public function delete(User $userAdmin, User $user): bool
    {
        if ($userAdmin->hasPermissionTo('user.delete') && $userAdmin->hasPermissionTo('user')) {
            return true;
        }
        if ($userAdmin->hasPermissionTo('user.delete') && $userAdmin->hasPermissionTo('user.own') && in_array($user->id, $userAdmin->my->pluck('id')->toArray(),false)){
            return true;
        }

        return false;
    }

    public function update(User $userAdmin, User $user): bool
    {
        if ($userAdmin->hasPermissionTo('user.edit') && $userAdmin->hasPermissionTo('user')) {
            return true;
        }
        if ($userAdmin->hasPermissionTo('user.edit') && $userAdmin->hasPermissionTo('user.own') && in_array($user->id, $userAdmin->my->pluck('id')->toArray(),false)){
            return true;
        }

        return false;
    }
}
