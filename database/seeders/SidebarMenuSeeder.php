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
                        'icon'  => '/sidebar_icons/admin_section.svg',
                        'path'  => '/admin-section',
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
                        'icon'  => '/sidebar_icons/academics.svg',
                        'path'  => '/academics',
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
                        'icon'  => '/sidebar_icons/study_material.svg',
                        'path'  => '/study-material',
                        'sub_menus' => [
                            'Upload Content',
                            'Assignment',
                            'Syllabus',
                            'Other Downloads',
                        ],
                    ],
                    [
                        'title' => 'Lesson Plan',
                        'icon'  => '/sidebar_icons/lesson_plan.svg',
                        'path'  => '/lesson-plan',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'Bulk Print',
                        'icon'  => '/sidebar_icons/bulk_print.svg',
                        'path'  => '/bulk-print',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'Download Center',
                        'icon'  => '/sidebar_icons/download_center.svg',
                        'path'  => '/download-center',
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
                        'icon'  => '/sidebar_icons/student_info.svg',
                        'path'  => '/student-info',
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
                        'icon'  => '/sidebar_icons/behaviour_records.svg',
                        'path'  => '/behaviour-records',
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
                        'icon'  => '/sidebar_icons/fees.svg',
                        'path'  => '/fees',
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
                        'icon'  => '/sidebar_icons/home_work.svg',
                        'path'  => '/home-work',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'Library',
                        'icon'  => '/sidebar_icons/library.svg',
                        'path'  => '/library',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'Transport',
                        'icon'  => '/sidebar_icons/transport.svg',
                        'path'  => '/transport',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'YES Garden',
                        'icon'  => '/sidebar_icons/dormitory.svg',
                        'path'  => '/dormitory',
                        'sub_menus' => [],
                    ],
                ],
            ],
            [
                'title' => 'HR',
                'menus' => [
                    [
                        'title' => 'Human Resource',
                        'icon'  => '/sidebar_icons/admin_section.svg',
                        'path'  => '/human-resource',
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
                        'icon'  => '/sidebar_icons/examination.svg',
                        'path'  => '/examination',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'Exam Plan',
                        'icon'  => '/sidebar_icons/exam_plan.svg',
                        'path'  => '/exam-plan',
                        'sub_menus' => [],
                    ],
                    [
                        'title' => 'Online Exam',
                        'icon'  => '/sidebar_icons/online_exam.svg',
                        'path'  => '/online_exam',
                        'sub_menus' => [],
                    ],
                ],
            ],
            [
                'title' => 'Settings Section',
                'menus' => [
                    [
                        'title' => 'General Settings',
                        'icon'  => '/sidebar_icons/examination.svg',
                        'path'  => '/settings-section',
                        'sub_menus' => [
                            'General Settings',
                        ],
                    ],
                ],
            ],
        ];

        SidebarSubMenu::truncate();
        SidebarMenu::truncate();

        $menuOrder = 1;
        foreach ($sidebarData as $sectionData) {
            $section = $sectionData['title'];
            foreach ($sectionData['menus'] as $menuItem) {
                $menu = SidebarMenu::create([
                    'section'   => $section,
                    'title'     => $menuItem['title'],
                    'icon'      => $menuItem['icon'] ?? null,
                    'path'      => $menuItem['path'] ?? null,
                    'order'     => $menuOrder++,
                    'is_active' => true,
                ]);

                if (!empty($menuItem['sub_menus'])) {
                    $subOrder = 1;
                    foreach ($menuItem['sub_menus'] as $sub) {
                        $subTitle = is_array($sub) ? ($sub['title'] ?? '') : (string)$sub;
                        $isActive = is_array($sub) ? ($sub['is_active'] ?? true) : true;

                        SidebarSubMenu::create([
                            'sidebar_menu_id' => $menu->id,
                            'title'           => $subTitle,
                            'order'           => $subOrder++,
                            'is_active'       => $isActive,
                        ]);
                    }
                }
            }
        }
    }
}
