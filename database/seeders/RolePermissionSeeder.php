<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use FontLib\Table\Type\name;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     */



    private $permissions = [
        'scaleup-create',
        'scaleup-edit',
        'scaleup-delete',
        'scaleup-list',
        'scaleup-print',
        'scaleup-approve',

        'formula-create',
        'formula-edit',
        'formula-delete',
        'formula-list',
        'formula-print',
        'formula-approve',

        'user-create',
        'user-edit',
        'user-delete',
        'user-list',

        'approver-create',
        'approver-edit',
        'approver-delete',
        'approver-list',
    ];
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $role = Role::create(['name' => 'admin']);

        $user = User::create([
            "name" => 'Admin',
            'email' => 'admin@test.com',
            'username' => 'admin',
            'password' => Hash::make(1234567890),
            'created_at' => Carbon::now()
        ]);

        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
