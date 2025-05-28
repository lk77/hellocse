<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = Role::query()->firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);

        $admin->syncPermissions([
            Permission::query()->firstOrCreate(['name' => 'profile.create', 'guard_name' => 'api']),
            Permission::query()->firstOrCreate(['name' => 'profile.update', 'guard_name' => 'api']),
            Permission::query()->firstOrCreate(['name' => 'profile.delete', 'guard_name' => 'api']),
            Permission::query()->firstOrCreate(['name' => 'profile.comment.store', 'guard_name' => 'api']),
        ]);
    }
}
