<?php

namespace Modules\User\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('role');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('role.create');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('role.delete');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('role.edit');
    }
}
