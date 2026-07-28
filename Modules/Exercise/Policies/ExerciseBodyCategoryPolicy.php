<?php

namespace Modules\Exercise\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Exercise\Entities\ExerciseBodyCategory;
use Modules\User\Entities\User;

class ExerciseBodyCategoryPolicy
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
        return $user->hasPermissionTo('exercise_body_category');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('exercise_body_category.create');
    }

    public function delete(User $user, ExerciseBodyCategory $bodyCategory): bool
    {
        return $user->hasPermissionTo('exercise_body_category.delete');
    }

    public function update(User $user, ExerciseBodyCategory $bodyCategory): bool
    {
        return $user->hasPermissionTo('exercise_body_category.edit');
    }
}
