<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Entities\User;
use Modules\User\Entities\UserMeta;
use Modules\User\Enum\UserMetaEnum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create first user factory
        $user = User::create([
            'mobile' => '09197729101',
            'email' => 'info@alanvaghteshe.com',
            'password' => '123',
        ]);

        $user->metas()->saveMany([
            new UserMeta([
                'meta_key' => UserMetaEnum::FIRST_NAME,
                'meta_value' => 'مدیر',
            ]),
            new UserMeta([
                'meta_key' => UserMetaEnum::LAST_NAME,
                'meta_value' => 'کل',
            ]),
        ]);
        //create مدیر Role
        /** @var Role $role */
        $role = Role::create(['name' => 'مدیر']);
        /** @var Role $userDefaultRole */
        $userDefaultRole = Role::create(['name' => 'کاربران']);
        //create ADMIN and SUPER_ADMIN Permissions
        $admin = Permission::create(['name' => 'ADMIN_ACCESS']);
        $superAdmin = Permission::create(['name' => 'SUPER_ADMIN']);
        //create User essential permissions
        $userPermission = Permission::create(['name' => 'USER_ACCESS']);
        $userDefaultPermission = Permission::create(['name' => 'USER_DEFAULT']);
        //assign ADMIN and SUPER_ADMIN Permissions to مدیر Role
        $role->givePermissionTo($admin);
        $role->givePermissionTo($superAdmin);
        //assign User essential permissions to کاربران Role
        $userDefaultRole->givePermissionTo($userPermission);
        $userDefaultRole->givePermissionTo($userDefaultPermission);
        //assign مدیر Role to first user factory
        $user->syncRoles($role);
    }
}
