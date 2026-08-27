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
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'full_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('full_name')->nullable()->after('name');
            });
            DB::statement("UPDATE users SET full_name = name WHERE full_name IS NULL OR full_name = ''");
        }

        if (Schema::hasTable('students') && !Schema::hasColumn('students', 'full_name')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('full_name')->nullable()->after('last_name');
            });
            DB::statement("UPDATE students SET full_name = TRIM(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) WHERE full_name IS NULL OR full_name = ''");
        }

        if (Schema::hasTable('staff') && !Schema::hasColumn('staff', 'full_name')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->string('full_name')->nullable()->after('last_name');
            });
            if (Schema::hasColumn('staff', 'name')) {
                DB::statement("UPDATE staff SET full_name = COALESCE(name, TRIM(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')))) WHERE full_name IS NULL OR full_name = ''");
            } else {
                DB::statement("UPDATE staff SET full_name = TRIM(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))) WHERE full_name IS NULL OR full_name = ''");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'full_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('full_name');
            });
        }

        if (Schema::hasTable('students') && Schema::hasColumn('students', 'full_name')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('full_name');
            });
        }

        if (Schema::hasTable('staff') && Schema::hasColumn('staff', 'full_name')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropColumn('full_name');
            });
        }
    }
};
