<?php

namespace Modules\Package\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Package\Entities\Package;
use Modules\User\Entities\User;

class PackagePolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('package');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('package.create');
    }

    public function delete(User $user, Package $package): bool
    {
        return $user->hasPermissionTo('package.delete');
    }

    public function update(User $user, Package $package): bool
    {
        return $user->hasPermissionTo('package.edit');
    }
}
