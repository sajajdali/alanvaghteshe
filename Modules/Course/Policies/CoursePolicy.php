<?php

namespace Modules\Course\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Course\app\Models\Course;
use Modules\User\Entities\User;

class CoursePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('course');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('course.create');
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('course.edit');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('course.delete');
    }
}
