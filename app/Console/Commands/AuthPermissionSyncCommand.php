<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AuthPermissionSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:permission-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all module permissions (include systems permissions)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Start creating permissions');

        $permissions = [];
        $modules = \Module::allEnabled();
        foreach ($modules as $module) {
            $modulePermissions = config($module->getLowerName().'.permission') ?? [];
            if (is_array($modulePermissions) && count($modulePermissions) > 0) {
                $permissions[] = $modulePermissions;
            }
        }
        $itemShouldInsert = [];
        foreach ($permissions as $permissionGroup) {
            foreach ($permissionGroup as $index => $permission) {
                $itemShouldInsert[] = [...array_keys($permission['gate'])];
                $itemShouldInsert[] = [...array_keys($permission['permissions'])];
            }
        }
        $itemShouldInsert = array_unique(array_merge(...$itemShouldInsert));
        foreach ($itemShouldInsert as $pItem) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $pItem]);
            $this->info('Permission created: '.$pItem);
        }
        Artisan::call('permission:cache-reset');
        $this->info('Permission cache cleared');

        return 0;
    }
}
