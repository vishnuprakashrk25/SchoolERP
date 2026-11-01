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
        // 1. sections.class_id nullable
        if (Schema::hasTable('sections') && Schema::hasColumn('sections', 'class_id')) {
            DB::statement("ALTER TABLE `sections` MODIFY `class_id` BIGINT UNSIGNED NULL");
        }

        // 2. student_academics.section_id nullable
        if (Schema::hasTable('student_academics') && Schema::hasColumn('student_academics', 'section_id')) {
            DB::statement("ALTER TABLE `student_academics` MODIFY `section_id` BIGINT UNSIGNED NULL");
        }

        // 3. class_teachers.section_id nullable
        if (Schema::hasTable('class_teachers') && Schema::hasColumn('class_teachers', 'section_id')) {
            DB::statement("ALTER TABLE `class_teachers` MODIFY `section_id` BIGINT UNSIGNED NULL");
        }

        // 4. class_routines.section_id nullable
        if (Schema::hasTable('class_routines') && Schema::hasColumn('class_routines', 'section_id')) {
            DB::statement("ALTER TABLE `class_routines` MODIFY `section_id` BIGINT UNSIGNED NULL");
        }

        // 5. homeworks.section_id nullable
        if (Schema::hasTable('homeworks') && Schema::hasColumn('homeworks', 'section_id')) {
            DB::statement("ALTER TABLE `homeworks` MODIFY `section_id` BIGINT UNSIGNED NULL");
        }

        // 6. student_multi_class.section_id nullable
        if (Schema::hasTable('student_multi_class') && Schema::hasColumn('student_multi_class', 'section_id')) {
            DB::statement("ALTER TABLE `student_multi_class` MODIFY `section_id` BIGINT UNSIGNED NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_multi_class') && Schema::hasColumn('student_multi_class', 'section_id')) {
            DB::statement("ALTER TABLE `student_multi_class` MODIFY `section_id` BIGINT UNSIGNED NOT NULL");
        }

        if (Schema::hasTable('homeworks') && Schema::hasColumn('homeworks', 'section_id')) {
            DB::statement("ALTER TABLE `homeworks` MODIFY `section_id` BIGINT UNSIGNED NOT NULL");
        }

        if (Schema::hasTable('class_routines') && Schema::hasColumn('class_routines', 'section_id')) {
            DB::statement("ALTER TABLE `class_routines` MODIFY `section_id` BIGINT UNSIGNED NOT NULL");
        }

        if (Schema::hasTable('class_teachers') && Schema::hasColumn('class_teachers', 'section_id')) {
            DB::statement("ALTER TABLE `class_teachers` MODIFY `section_id` BIGINT UNSIGNED NOT NULL");
        }

        if (Schema::hasTable('student_academics') && Schema::hasColumn('student_academics', 'section_id')) {
            DB::statement("ALTER TABLE `student_academics` MODIFY `section_id` BIGINT UNSIGNED NOT NULL");
        }

        if (Schema::hasTable('sections') && Schema::hasColumn('sections', 'class_id')) {
            DB::statement("ALTER TABLE `sections` MODIFY `class_id` BIGINT UNSIGNED NOT NULL");
        }
    }
};
