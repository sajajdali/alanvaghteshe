<?php

namespace Modules\User\Livewire\Admin\Role;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleCreateOrUpdate extends Component
{
    use AuthorizesRequests;

    public ?Role $role = null;

    public string $roleType = 'user';

    public $isRoleDefault;

    public $permissions;

    public string $name = '';

    public bool $isEdit = false;

    public array $selectedPermissions = [];

    public function mount()
    {
        $permissions = [];
        $modules = \Module::allEnabled();
        foreach ($modules as $module) {
            $modulePermissions = config($module->getLowerName().'.permission') ?? [];
            if (is_array($modulePermissions) && count($modulePermissions) > 0) {
                $permissions[] = $modulePermissions;
            }
        }
        $this->permissions = $permissions;
        $role = request()->route('role');
        if ($role instanceof Role) {
            $this->authorize('update', $role);
            $this->isEdit = true;
            $this->role = $role;
            $this->name = $this->role->name;
            $this->selectedPermissions = $this->role->permissions?->pluck('name')?->toArray();
            $this->roleType = $this->role->hasPermissionTo('ADMIN_ACCESS') ? 'admin' : 'user';
        }
    }

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|unique:roles,name',
        ];
        if ($this->isEdit) {
            $rules['name'] = 'required|unique:roles,name,'.$this->role?->id;
        }

        return $rules;
    }

    public function addItem($value): void
    {
        $this->selectedPermissions[] = $value;
    }

    public function removeItem($value): void
    {
        if (in_array($value, $this->selectedPermissions, false)) {
            $this->selectedPermissions = Arr::flatten(array_diff($this->selectedPermissions, [$value]));
        }
    }

    public function updateOrCreate()
    {
        $this->validate();
        if ($this->role !== null) {
            $role = $this->role;
            $role->update(['name' => $this->name]);
            $role->syncPermissions([]);
        } else {
            $role = \Spatie\Permission\Models\Role::create(['name' => $this->name]);
        }
        if ($this->roleType === 'user') {
            $this->selectedPermissions[] = 'USER_ACCESS';
        } else {
            $this->selectedPermissions[] = 'ADMIN_ACCESS';
        }
        if ($this->roleType === 'user' && $this->isRoleDefault === '1') {
            $this->selectedPermissions[] = 'USER_DEFAULT';
            //change all other roles to not default
            $roleDefaultOld = Role::whereHas('permissions', static function ($permissions) {
                $permissions->where('name', 'USER_DEFAULT');
            })->first();
            $roleDefaultOld->revokePermissionTo('USER_DEFAULT');
        }
        foreach ($this->selectedPermissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }


        $role->syncPermissions($this->selectedPermissions);
        Artisan::call('permission:cache-reset');
        session()->flash('success', 'تغییرات با موفقیت اعمال شد.');

        //create cache for roles

        return redirect()->route('admin.role.index');
    }

    public function render()
    {
        $title = (! is_null($this->role) && $this->role->exists) ? 'ویرایش نقش' : 'ایجاد نقش جدید';

        return view('user::livewire.admin.role.role-create-or-update')->title($title);
    }
}
