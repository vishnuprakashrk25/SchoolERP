<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'admission_no')) {
            DB::statement("ALTER TABLE `students` MODIFY `admission_no` VARCHAR(255) NULL DEFAULT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'admission_no')) {
            DB::statement("ALTER TABLE `students` MODIFY `admission_no` VARCHAR(255) NOT NULL");
        }
    }
};
