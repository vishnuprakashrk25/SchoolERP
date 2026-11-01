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
    $admin   = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $teacher = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
    $staff   = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
    $driver  = Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'web']);

    Permission::firstOrCreate(['name' => 'manage_admission', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'view_reports', 'guard_name' => 'web']);

    $admin->givePermissionTo(Permission::all());
}
}
