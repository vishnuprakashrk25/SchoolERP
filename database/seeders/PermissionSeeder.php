<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Ensure core system roles exist (Super admin, Admin, Principal, Teacher, Parents)
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $adminRole      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $principalRole  = Role::firstOrCreate(['name' => 'principal', 'guard_name' => 'web']);
        $teacherRole    = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $parentRole     = Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        $staffRole      = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $studentRole    = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // 2. Comprehensive School ERP Permissions
        $permissions = [
            // Student & Admissions
            'view_students',
            'create_students',
            'edit_students',
            'delete_students',
            'manage_admissions',
            'manage_student_documents',
            'manage_student_categories',

            // Academics
            'manage_classes',
            'manage_sections',
            'manage_subjects',
            'manage_class_teachers',
            'manage_class_rooms',
            'manage_timetables',

            // Attendance
            'view_attendance',
            'mark_student_attendance',
            'mark_staff_attendance',

            // Examinations & Marks
            'manage_exams',
            'manage_exam_schedules',
            'enter_exam_marks',
            'view_exam_results',

            // Fees & Finance
            'view_fees',
            'collect_fees',
            'manage_fee_structures',
            'manage_fee_groups',
            'view_expenses',

            // Staff & HR
            'view_staff',
            'create_staff',
            'edit_staff',
            'delete_staff',
            'manage_departments',

            // Front Office
            'manage_admission_queries',
            'manage_complaints',
            'manage_postal_records',
            'manage_phone_call_logs',

            // Events & Calendar
            'view_events',
            'create_events',
            'edit_events',
            'delete_events',

            // Reports & Settings
            'view_reports',
            'manage_general_settings',
            'manage_roles_permissions',

            // Teacher Specific Portal Permissions
            'view_assigned_classes',
            'submit_student_attendance',
            'submit_exam_marks',
            'upload_study_materials',
            'create_assignments',

            // Parent Specific Portal Permissions
            'view_child_attendance',
            'view_child_fees',
            'view_child_results',
            'view_child_timetable',
            'view_child_homework',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        $allPermissions = Permission::all();

        // 3. Super Admin & Admin get full access
        $superAdminRole->syncPermissions($allPermissions);
        $adminRole->syncPermissions($allPermissions);

        // 4. Principal Default Permissions (Academic & Operational Leadership)
        $principalRole->syncPermissions([
            'view_students',
            'create_students',
            'edit_students',
            'manage_admissions',
            'manage_classes',
            'manage_sections',
            'manage_subjects',
            'manage_class_teachers',
            'manage_class_rooms',
            'manage_timetables',
            'view_attendance',
            'mark_student_attendance',
            'mark_staff_attendance',
            'manage_exams',
            'manage_exam_schedules',
            'enter_exam_marks',
            'view_exam_results',
            'view_fees',
            'view_expenses',
            'view_staff',
            'manage_departments',
            'manage_admission_queries',
            'manage_complaints',
            'view_events',
            'create_events',
            'edit_events',
            'view_reports',
        ]);

        // 5. Default permissions for Teacher
        $teacherRole->syncPermissions([
            'view_events',
            'view_assigned_classes',
            'submit_student_attendance',
            'submit_exam_marks',
            'upload_study_materials',
            'create_assignments',
            'view_attendance',
            'view_exam_results',
        ]);

        // 6. Default permissions for Parents
        $parentRole->syncPermissions([
            'view_events',
            'view_child_attendance',
            'view_child_fees',
            'view_child_results',
            'view_child_timetable',
            'view_child_homework',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
