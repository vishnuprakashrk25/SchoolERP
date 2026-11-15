<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SidebarMenu;
use App\Models\SidebarSubMenu;

class SidebarMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sidebarData = [
            [
                'title' => 'Administration',
                'menus' => [
                    [
                        'title' => 'Admin Section',
                        'icon' => '/sidebar_icons/admin_section.svg',
                        'path' => '/admin-section',
                        'sub_menus' => [
                            'Admission Query',
                            'Complaint',
                            'Postal Receive',
                            'Postal Dispatch',
                            'Phone Call Log',
                            'Admin Setup',
                            'ID Card',
                            'Certificate',
                            'Generate Certificate',
                            'Generate ID Card',
                        ],
                    ],
                    [
                        'title' => 'Academics',
                        'icon' => '/sidebar_icons/academics.svg',
                        'path' => '/academics',
                        'sub_menus' => [
                            'Optional Subject',
                            'Section',
                            'Class',
                            'Subjects',
                            'Assign Class Teacher',
                            'Assign Subject',
                            'Class Room ',
                            'Class Routine',
                        ],
                    ],
                    [
                        'title' => 'Study Material',
                        'icon' => '/sidebar_icons/study_material.svg',
                        'path' => '/study-material',
                        'sub_menus' => [
                            'Upload Content',
                            'Assignment',
                            'Syllabus',
                            'Other Downloads',
                        ],
                    ],
                    [
                        'title' => 'Lesson Plan',
                        'icon' => '/sidebar_icons/lesson_plan.svg',
                        'path' => '/lesson-plan',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'Bulk Print',
                        'icon' => '/sidebar_icons/bulk_print.svg',
                        'path' => '/bulk-print',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'Download Center',
                        'icon' => '/sidebar_icons/download_center.svg',
                        'path' => '/download-center',
                        'sub_menus' => [
                            'Content Type',
                            'Content List',
                            'Shared Content List',
                            'Video List',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Student',
                'menus' => [
                    [
                        'title' => 'Student Info',
                        'icon' => '/sidebar_icons/student_info.svg',
                        'path' => '/student-info',
                        'sub_menus' => [
                            'Student Category',
                            'Add Student',
                            'Student List',
                            'Multi Class Student',
                            'Delete Student Record',
                            'Unassigned Student',
                            'Student Attendance',
                            'Student Group',
                            'Student Promote',
                            'Disabled Students',
                            'Subject Wise Attendance',
                            'Student Export',
                            'SMS Sending Time',
                            'Student Settings',
                        ],
                    ],
                    [
                        'title' => 'Remarks Point',
                        'icon' => '/sidebar_icons/behaviour_records.svg',
                        'path' => '/behaviour-records',
                        'sub_menus' => [
                            'Incidents',
                            'Assign Incident',
                            'Student Incident Report',
                            'Remarks Report',
                            'Incident Wise report',
                            'Settings',
                        ],
                    ],
                    [
                        'title' => 'Fees',
                        'icon' => '/sidebar_icons/fees.svg',
                        'path' => '/fees',
                        'sub_menus' => [
                            'Fees Group',
                            'Fees Type',
                            'Fees Invoice',
                            'Bank Payment',
                            'Fees Carry Forward',
                        ],
                    ],
                    [
                        'title' => 'Home Work',
                        'icon' => '/sidebar_icons/home_work.svg',
                        'path' => '/home-work',
                        'sub_menus' => [
                            'Homework List',
                            'Homework Report',
                        ],
                    ],
                    [
                        'title' => 'Library',
                        'icon' => '/sidebar_icons/library.svg',
                        'path' => '/library',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'Transport',
                        'icon' => '/sidebar_icons/transport.svg',
                        'path' => '/transport',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'YES Garden',
                        'icon' => '/sidebar_icons/dormitory.svg',
                        'path' => '/dormitory',
                        'sub_menus' => [],
                    ],
                ],
            ],
            [
                'title' => 'Report Section',
                'menus' => [
                    [
                        'title' => 'Student Report',
                        'icon' => '/sidebar_icons/admin_section.svg',
                        'path' => '/student-report',
                        'sub_menus' => [
                            'Student Attendance Report',
                            'Homework Evaluation Report',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Human Resource',
                'menus' => [
                    [
                        'title' => 'Human Resource',
                        'icon' => '/sidebar_icons/admin_section.svg',
                        'path' => '/human-resource',
                        'sub_menus' => [
                            'Add Staff',
                            'Staff List',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Exam',
                'menus' => [
                    [
                        'title' => 'Examination',
                        'icon' => '/sidebar_icons/examination.svg',
                        'path' => '/examination',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'Exam Plan',
                        'icon' => '/sidebar_icons/exam_plan.svg',
                        'path' => '/exam-plan',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'Online Exam',
                        'icon' => '/sidebar_icons/online_exam.svg',
                        'path' => '/online_exam',
                        'sub_menus' => [],
                    ],
                ],
            ],
            [
                'title' => 'Settings Section',
                'menus' => [
                    [
                        'title' => 'Settings Section',
                        'icon' => '/sidebar_icons/examination.svg',
                        'path' => '/settings-section',
                        'sub_menus' => [
                            'General Settings',
                            'Academic Year',
                            'Users & Access',
                            'Sidebar',
                        ],
                    ],
                ],
            ],
        ];

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        SidebarSubMenu::truncate();
        SidebarMenu::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $defaultSubPermissions = [
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
            'General Settings'         => 'manage_general_settings',
            'Academic Year'            => 'manage_academic_years',
            'Users & Access'           => 'manage_roles_permissions',
            'Sidebar'                  => 'manage_general_settings',
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
            'Optional Subject'         => 'manage_subjects',
            'Section'                  => 'manage_sections',
            'Class'                    => 'manage_classes',
            'Subjects'                 => 'manage_subjects',
            'Assign Class Teacher'     => 'manage_class_teachers',
            'Assign Subject'           => 'manage_subjects',
            'Class Room'               => 'manage_class_rooms',
            'Class Room '              => 'manage_class_rooms',
            'Class Routine'            => 'manage_timetables',
            'Upload Content'           => 'upload_study_materials',
            'Assignment'               => 'create_assignments',
            'Syllabus'                 => 'upload_study_materials',
            'Other Downloads'          => 'upload_study_materials',
            'Content Type'             => 'upload_study_materials',
            'Content List'             => 'upload_study_materials',
            'Shared Content List'      => 'upload_study_materials',
            'Video List'               => 'upload_study_materials',
            'Incidents'                => 'view_events',
            'Assign Incident'          => 'edit_events',
            'Student Incident Report'  => 'view_reports',
            'Remarks Report'           => 'view_reports',
            'Incident Wise report'     => 'view_reports',
            'Settings'                 => 'manage_general_settings',
            'Fees Group'               => 'manage_fee_groups',
            'Fees Type'                => 'view_fees',
            'Fees Invoice'             => 'collect_fees',
            'Bank Payment'             => 'collect_fees',
            'Fees Carry Forward'       => 'manage_fee_structures',
            'Homework List'            => 'create_assignments',
            'Homework Report'          => 'view_reports',
            'Student Attendance Report'=> 'view_reports',
            'Students Attendance Report'=> 'view_reports',
            'Homework Evaluation Report'=> 'view_reports',
            'Add Staff'                => 'create_staff',
            'Staff List'               => 'view_staff',
        ];

        $defaultMenuPermissions = [
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

        $menuOrder = 1;
        foreach ($sidebarData as $sectionData) {
            $section = $sectionData['title'];
            foreach ($sectionData['menus'] as $menuItem) {
                $menuTitle = $menuItem['title'];
                $menuPerm = is_array($menuItem) ? ($menuItem['permission'] ?? null) : null;
                if (!$menuPerm && isset($defaultMenuPermissions[$menuTitle])) {
                    $menuPerm = $defaultMenuPermissions[$menuTitle];
                }

                $menu = SidebarMenu::create([
                    'section' => $section,
                    'title' => $menuTitle,
                    'icon' => $menuItem['icon'] ?? null,
                    'path' => $menuItem['path'] ?? null,
                    'permission' => $menuPerm,
                    'order' => $menuOrder++,
                    'is_active' => true,
                ]);

                if (!empty($menuItem['sub_menus'])) {
                    $subOrder = 1;
                    foreach ($menuItem['sub_menus'] as $sub) {
                        $subTitle = is_array($sub) ? ($sub['title'] ?? '') : (string) $sub;
                        $subPerm = is_array($sub) ? ($sub['permission'] ?? null) : null;
                        if (!$subPerm && isset($defaultSubPermissions[trim($subTitle)])) {
                            $subPerm = $defaultSubPermissions[trim($subTitle)];
                        }
                        $isActive = is_array($sub) ? ($sub['is_active'] ?? true) : true;

                        SidebarSubMenu::create([
                            'sidebar_menu_id' => $menu->id,
                            'title' => $subTitle,
                            'permission' => $subPerm,
                            'order' => $subOrder++,
                            'is_active' => $isActive,
                        ]);
                    }
                }
            }
        }
    }
}
