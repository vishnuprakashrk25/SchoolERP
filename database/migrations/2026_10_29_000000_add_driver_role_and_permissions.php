<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guards = ['web', 'sanctum'];

        // 1. Create Driver role for web and sanctum guards
        foreach ($guards as $guard) {
            Role::firstOrCreate([
                'name' => 'driver',
                'guard_name' => $guard,
            ]);
        }

        // 2. Transport & Driver permissions
        $transportPermissions = [
            'view_transport',
            'view_assigned_routes',
            'view_vehicles',
            'view_student_transport_list',
            'manage_vehicles',
            'manage_routes',
        ];

        foreach ($guards as $guard) {
            foreach ($transportPermissions as $permName) {
                Permission::firstOrCreate([
                    'name' => $permName,
                    'guard_name' => $guard,
                ]);
            }

            // Assign all transport permissions to super-admin & admin if they exist
            $superAdmin = Role::where('name', 'super-admin')->where('guard_name', $guard)->first();
            if ($superAdmin) {
                $superAdmin->givePermissionTo($transportPermissions);
            }

            $admin = Role::where('name', 'admin')->where('guard_name', $guard)->first();
            if ($admin) {
                $admin->givePermissionTo($transportPermissions);
            }

            // Assign default operational permissions to driver
            $driver = Role::where('name', 'driver')->where('guard_name', $guard)->first();
            if ($driver) {
                $driver->givePermissionTo([
                    'view_transport',
                    'view_assigned_routes',
                    'view_vehicles',
                    'view_student_transport_list',
                ]);
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep roles intact
    }
};
