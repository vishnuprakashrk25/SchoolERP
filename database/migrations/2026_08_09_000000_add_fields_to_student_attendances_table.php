<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('student_attendances', 'class_id')) {
                $table->foreignId('class_id')->nullable()->after('student_id')->constrained('classes')->nullOnDelete();
            }
            if (!Schema::hasColumn('student_attendances', 'section_id')) {
                $table->foreignId('section_id')->nullable()->after('class_id')->constrained('sections')->nullOnDelete();
            }
            if (!Schema::hasColumn('student_attendances', 'academic_year_id')) {
                $table->foreignId('academic_year_id')->nullable()->after('section_id')->constrained('academic_years')->nullOnDelete();
            }
            if (!Schema::hasColumn('student_attendances', 'note')) {
                $table->text('note')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('student_attendances', 'class_id')) {
                $table->dropForeign(['class_id']);
                $table->dropColumn('class_id');
            }
            if (Schema::hasColumn('student_attendances', 'section_id')) {
                $table->dropForeign(['section_id']);
                $table->dropColumn('section_id');
            }
            if (Schema::hasColumn('student_attendances', 'academic_year_id')) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            }
            if (Schema::hasColumn('student_attendances', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
