<?php

namespace Modules\Exercise\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Exercise\Entities\Exercise;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;

class ExercisePolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('exercise');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('exercise.create');
    }

    public function delete(User $user, Exercise $exercise): bool
    {
        return $user->hasPermissionTo('exercise.delete');
    }

    public function update(User $user, Exercise $exercise): bool
    {
        return $user->hasPermissionTo('exercise.edit');
    }
}
