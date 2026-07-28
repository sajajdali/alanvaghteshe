<?php

namespace Modules\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Core\Entities\Faq;
use Modules\User\Entities\User;

class FaqPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('faq');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('faq.create');
    }

    public function delete(User $user, Faq $faq): bool
    {
        return $user->hasPermissionTo('faq.delete');
    }

    public function update(User $user, Faq $faq): bool
    {
        return $user->hasPermissionTo('faq.edit');
    }
}
