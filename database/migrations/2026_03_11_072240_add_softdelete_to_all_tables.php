<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        $tables = [

            // Core
            'users',
            'branches',
            'academic_years',

            // Academics
            'classes',
            'sections',
            'subjects',
            'class_subjects',
            'timetables',

            // Student
            'student_categories',
            'students',
            'student_parents',
            'student_documents',
            'student_attendance',
            'student_multi_class',

            // Staff
            'staff_departments',
            'staff',
            'staff_attendance',

            // Exams
            'exams',
            'exam_schedules',
            'exam_results',

            // Fees
            'fee_types',
            'fee_groups',
            'fee_structures',
            'student_fees',
            'payments',

            // Certificates
            'certificate_templates',
            'generated_certificates',

            // Study Material
            'assignments',
            'assignment_submissions',
            'study_materials',

            // Download Center
            'download_content_types',
            'download_contents',
            'shared_contents',
            'videos',

            // Communication
            'notifications',
            'messages',
            'message_threads',

            // Utility
            'bulk_print_logs',

            // Files
            'files'
        ];

        foreach ($tables as $table) {

            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {

                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });

            }

        }

    }

    public function down(): void
    {

        $tables = [

            'users','branches','academic_years','classes','sections','subjects','class_subjects','timetables',
            'student_categories','students','student_parents','student_documents','student_attendance','student_multi_class',
            'staff_departments','staff','staff_attendance',
            'exams','exam_schedules','exam_results',
            'fee_types','fee_groups','fee_structures','student_fees','payments',
            'certificate_templates','generated_certificates',
            'assignments','assignment_submissions','study_materials',
            'download_content_types','download_contents','shared_contents','videos',
            'notifications','messages','message_threads',
            'bulk_print_logs','files'

        ];

        foreach ($tables as $table) {

            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {

                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });

            }

        }

    }
};