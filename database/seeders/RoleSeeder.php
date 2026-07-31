<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

public function run()
{
    $admin   = Role::create(['name' => 'admin']);
    $teacher = Role::create(['name' => 'teacher']);
    $staff   = Role::create(['name' => 'staff']);

    Permission::create(['name' => 'manage_admission']);
    Permission::create(['name' => 'view_reports']);

    $admin->givePermissionTo(Permission::all());
}
}
