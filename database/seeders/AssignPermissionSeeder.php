<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

// use Spatie\Permission\Contracts\Role;

class AssignPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $admin = Role::findByName('admin', 'web');
        $admin->givePermissionTo('scaleup-compare');
    }
}
