<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\SidebarMenu;
use App\Models\SidebarSubMenu;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add nullable permission column to sidebar_menus if missing
        if (Schema::hasTable('sidebar_menus')) {
            Schema::table('sidebar_menus', function (Blueprint $table) {
                if (!Schema::hasColumn('sidebar_menus', 'permission')) {
                    $table->string('permission', 100)->nullable()->after('path');
                }
            });
        }

        // 2. Add nullable permission column to sidebar_sub_menus if missing
        if (Schema::hasTable('sidebar_sub_menus')) {
            Schema::table('sidebar_sub_menus', function (Blueprint $table) {
                if (!Schema::hasColumn('sidebar_sub_menus', 'permission')) {
                    $table->string('permission', 100)->nullable()->after('path');
                }
            });
        }

        // 3. Ensure 'manage_academic_years' permission exists in permissions table
        if (Schema::hasTable('permissions')) {
            Permission::firstOrCreate(['name' => 'manage_academic_years', 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => 'manage_academic_years', 'guard_name' => 'sanctum']);
        }

        // 4. Map default permissions for Sub Menus
        $subMenuPermissions = [
            // Student Info
            'Student Category'          => 'manage_student_categories',
            'Add Student'              => 'create_students',
            'Student List'             => 'view_students',
            'Multi Class Student'      => 'view_students',
            'Delete Student Record'    => 'delete_students',
            'Unassigned Student'       => 'view_students',
            'Student Attendance'       => 'view_attendance',
            'Student Group'            => 'view_students',
            'Student Promote'          => 'view_students',
            'Disabled Students'        => 'view_students',
            'Subject Wise Attendance'  => 'view_attendance',
            'Student Export'           => 'view_students',
            'SMS Sending Time'         => 'manage_general_settings',
            'Student Settings'         => 'manage_general_settings',

            // Settings Section
            'General Settings'         => 'manage_general_settings',
            'Academic Year'            => 'manage_academic_years',
            'Users & Access'           => 'manage_roles_permissions',
            'Sidebar'                  => 'manage_general_settings',

            // Admin Section
            'Admission Query'          => 'manage_admission_queries',
            'Complaint'                => 'manage_complaints',
            'Postal Receive'           => 'manage_postal_records',
            'Postal Dispatch'          => 'manage_postal_records',
            'Phone Call Log'           => 'manage_phone_call_logs',
            'Admin Setup'              => 'manage_general_settings',
            'ID Card'                  => 'view_students',
            'Certificate'              => 'view_students',
            'Generate Certificate'     => 'view_students',
            'Generate ID Card'         => 'view_students',

            // Academics
            'Optional Subject'         => 'manage_subjects',
            'Section'                  => 'manage_sections',
            'Class'                    => 'manage_classes',
            'Subjects'                 => 'manage_subjects',
            'Assign Class Teacher'     => 'manage_class_teachers',
            'Assign Subject'           => 'manage_subjects',
            'Class Room'               => 'manage_class_rooms',
            'Class Room '              => 'manage_class_rooms',
            'Class Routine'            => 'manage_timetables',

            // Study Material & Downloads
            'Upload Content'           => 'upload_study_materials',
            'Assignment'               => 'create_assignments',
            'Syllabus'                 => 'upload_study_materials',
            'Other Downloads'          => 'upload_study_materials',
            'Content Type'             => 'upload_study_materials',
            'Content List'             => 'upload_study_materials',
            'Shared Content List'      => 'upload_study_materials',
            'Video List'               => 'upload_study_materials',

            // Remarks Point / Incidents
            'Incidents'                => 'view_events',
            'Assign Incident'          => 'edit_events',
            'Student Incident Report'  => 'view_reports',
            'Remarks Report'           => 'view_reports',
            'Incident Wise report'     => 'view_reports',
            'Settings'                 => 'manage_general_settings',

            // Fees
            'Fees Group'               => 'manage_fee_groups',
            'Fees Type'                => 'view_fees',
            'Fees Invoice'             => 'collect_fees',
            'Bank Payment'             => 'collect_fees',
            'Fees Carry Forward'       => 'manage_fee_structures',

            // Home Work
            'Homework List'            => 'create_assignments',
            'Homework Report'          => 'view_reports',

            // Reports
            'Student Attendance Report'=> 'view_reports',
            'Students Attendance Report'=> 'view_reports',
            'Homework Evaluation Report'=> 'view_reports',

            // Human Resource
            'Add Staff'                => 'create_staff',
            'Staff List'               => 'view_staff',
        ];

        if (Schema::hasTable('sidebar_sub_menus')) {
            foreach ($subMenuPermissions as $title => $perm) {
                SidebarSubMenu::where('title', $title)
                    ->orWhereRaw('TRIM(title) = ?', [trim($title)])
                    ->update(['permission' => $perm]);
            }
        }

        // 5. Map default permissions for Parent Menus
        $parentMenuPermissions = [
            'Admin Section'    => 'manage_admission_queries',
            'Academics'        => 'manage_classes',
            'Study Material'   => 'upload_study_materials',
            'Lesson Plan'      => 'manage_timetables',
            'Bulk Print'       => 'view_students',
            'Download Center'  => 'upload_study_materials',
            'Student Info'     => 'view_students',
            'Remarks Point'    => 'view_events',
            'Fees'             => 'view_fees',
            'Home Work'        => 'create_assignments',
            'Library'          => 'view_students',
            'Transport'        => 'view_transport',
            'YES Garden'       => 'view_students',
            'Student Report'   => 'view_reports',
            'Students Report'  => 'view_reports',
            'Human Resource'   => 'view_staff',
            'Examination'      => 'manage_exams',
            'Exam Plan'        => 'manage_exam_schedules',
            'Online Exam'      => 'manage_exams',
            'Settings Section' => 'manage_general_settings',
            'General Settings' => 'manage_general_settings',
        ];

        if (Schema::hasTable('sidebar_menus')) {
            foreach ($parentMenuPermissions as $title => $perm) {
                SidebarMenu::where('title', $title)
                    ->orWhereRaw('TRIM(title) = ?', [trim($title)])
                    ->update(['permission' => $perm]);
            }
        }

        if (class_exists(SidebarMenu::class) && method_exists(SidebarMenu::class, 'clearMenuCache')) {
            SidebarMenu::clearMenuCache();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sidebar_sub_menus') && Schema::hasColumn('sidebar_sub_menus', 'permission')) {
            Schema::table('sidebar_sub_menus', function (Blueprint $table) {
                $table->dropColumn('permission');
            });
        }

        if (Schema::hasTable('sidebar_menus') && Schema::hasColumn('sidebar_menus', 'permission')) {
            Schema::table('sidebar_menus', function (Blueprint $table) {
                $table->dropColumn('permission');
            });
        }

        if (class_exists(SidebarMenu::class) && method_exists(SidebarMenu::class, 'clearMenuCache')) {
            SidebarMenu::clearMenuCache();
        }
    }
};

