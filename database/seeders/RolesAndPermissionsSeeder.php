<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permission
        Permission::create(['name' => 'create scaleup']);
        Permission::create(['name' => 'view scaleup']);
        Permission::create(['name' => 'edit scaleup']);
        Permission::create(['name' => 'delete scaleup']);

        // Create Role

        $role = Role::create(['name' => 'scaleup-creator']);
        $role->givePermissionTo(['create scaleup', 'view scaleup', 'edit scaleup', 'delete scaleup']);
    }
}
