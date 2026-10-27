<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = ['super-admin', 'admin', 'teacher', 'staff', 'student', 'parent'];
        $guards = ['web', 'sanctum'];

        foreach ($roles as $roleName) {
            foreach ($guards as $guard) {
                Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => $guard,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep roles intact
    }
};
