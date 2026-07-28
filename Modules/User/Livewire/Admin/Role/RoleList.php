<?php

namespace Modules\User\Livewire\Admin\Role;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[title('مدیریت نقش‌ها')]
class RoleList extends Component
{
    use AuthorizesRequests;


    public $roles;

    public function mount()
    {
        $this->roles = \Spatie\Permission\Models\Role::all();
    }

    #[On('delete')]
    public function delete(Role $model)
    {
        $this->authorize('delete', $model);
        //get users has $role role
        $users = $model->users;
        //get default role
        $defaultRole = Role::whereHas('permissions', static function ($permissions) {
            $permissions->where('name', 'USER_DEFAULT');
        })->first();
        //assign default role to users and remove $role
        foreach ($users as $user) {
            $user->removeRole($model);
            $user->assignRole($defaultRole);
        }
        $model->delete();

        return redirect()->route('admin.role.index')->with('success', 'نقش با موفقیت حذف شد.');
    }

    public function render()
    {
        return view('user::livewire.admin.role.role-list');
    }
}
