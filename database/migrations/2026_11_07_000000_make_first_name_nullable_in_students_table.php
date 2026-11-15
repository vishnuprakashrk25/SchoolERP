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
        if (Schema::hasTable('students')) {
            if (Schema::hasColumn('students', 'first_name')) {
                DB::statement("ALTER TABLE `students` MODIFY `first_name` VARCHAR(255) NULL DEFAULT NULL");
            }
            if (Schema::hasColumn('students', 'last_name')) {
                DB::statement("ALTER TABLE `students` MODIFY `last_name` VARCHAR(255) NULL DEFAULT NULL");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'first_name')) {
            DB::statement("ALTER TABLE `students` MODIFY `first_name` VARCHAR(255) NOT NULL");
        }
    }
};
