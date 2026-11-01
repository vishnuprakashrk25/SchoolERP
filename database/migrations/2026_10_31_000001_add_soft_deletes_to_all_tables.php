<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $systemTables = [
            'migrations',
            'failed_jobs',
            'personal_access_tokens',
            'password_resets',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
        ];

        // Fetch all tables in current database
        $databaseName = DB::getDatabaseName();
        $tables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?", [$databaseName]);

        foreach ($tables as $tbl) {
            $tableName = $tbl->TABLE_NAME;

            // Skip internal Laravel system tables
            if (in_array(strtolower($tableName), $systemTables)) {
                continue;
            }

            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'admission_queries',
            'behaviour_settings',
            'class_rooms',
            'class_routines',
            'class_teachers',
            'complaints',
            'events',
            'expenses',
            'general_settings',
            'generated_id_cards',
            'id_card_templates',
            'model_has_permissions',
            'model_has_roles',
            'optional_subjects',
            'permissions',
            'phone_call_logs',
            'postal_dispatches',
            'postal_receives',
            'roles',
            'role_has_permissions',
            'sidebar_menus',
            'sidebar_sub_menus',
            'student_academics',
            'student_emergency_contacts',
            'todos',
            'user_profiles',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
