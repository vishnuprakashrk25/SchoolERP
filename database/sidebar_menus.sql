-- --------------------------------------------------------
-- Proper Relational Tables & Default Data SQL Dump
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for table `sidebar_menus` (Parent Menus)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `sidebar_sub_menus`;
DROP TABLE IF EXISTS `sidebar_menus`;

CREATE TABLE `sidebar_menus` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `section` varchar(100) NOT NULL,
  `title` varchar(150) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sidebar_menus_section_index` (`section`),
  KEY `sidebar_menus_order_index` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `sidebar_sub_menus` (Child Submenus)
-- --------------------------------------------------------
CREATE TABLE `sidebar_sub_menus` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sidebar_menu_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sidebar_sub_menus_menu_id_index` (`sidebar_menu_id`),
  KEY `sidebar_sub_menus_order_index` (`order`),
  CONSTRAINT `fk_sidebar_sub_menus_menu_id` FOREIGN KEY (`sidebar_menu_id`) REFERENCES `sidebar_menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 1. Insert Parent Menus
-- --------------------------------------------------------
INSERT INTO `sidebar_menus` (`id`, `section`, `title`, `icon`, `path`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Administration', 'Admin Section', '/sidebar_icons/admin_section.svg', '/admin-section', 1, 1, NOW(), NOW()),
(2, 'Administration', 'Academics', '/sidebar_icons/academics.svg', '/academics', 2, 1, NOW(), NOW()),
(3, 'Administration', 'Study Material', '/sidebar_icons/study_material.svg', '/study-material', 3, 1, NOW(), NOW()),
(4, 'Administration', 'Lesson Plan', '/sidebar_icons/lesson_plan.svg', '/lesson-plan', 4, 1, NOW(), NOW()),
(5, 'Administration', 'Bulk Print', '/sidebar_icons/bulk_print.svg', '/bulk-print', 5, 1, NOW(), NOW()),
(6, 'Administration', 'Download Center', '/sidebar_icons/download_center.svg', '/download-center', 6, 1, NOW(), NOW()),
(7, 'Student', 'Student Info', '/sidebar_icons/student_info.svg', '/student-info', 7, 1, NOW(), NOW()),
(8, 'Student', 'Remarks Point', '/sidebar_icons/behaviour_records.svg', '/behaviour-records', 8, 1, NOW(), NOW()),
(9, 'Student', 'Fees', '/sidebar_icons/fees.svg', '/fees', 9, 1, NOW(), NOW()),
(10, 'Student', 'Home Work', '/sidebar_icons/home_work.svg', '/home-work', 10, 1, NOW(), NOW()),
(11, 'Student', 'Library', '/sidebar_icons/library.svg', '/library', 11, 1, NOW(), NOW()),
(12, 'Student', 'Transport', '/sidebar_icons/transport.svg', '/transport', 12, 1, NOW(), NOW()),
(13, 'Student', 'YES Garden', '/sidebar_icons/dormitory.svg', '/dormitory', 13, 1, NOW(), NOW()),
(14, 'HR', 'Human Resource', '/sidebar_icons/admin_section.svg', '/human-resource', 14, 1, NOW(), NOW()),
(15, 'Exam', 'Examination', '/sidebar_icons/examination.svg', '/examination', 15, 1, NOW(), NOW()),
(16, 'Exam', 'Exam Plan', '/sidebar_icons/exam_plan.svg', '/exam-plan', 16, 1, NOW(), NOW()),
(17, 'Exam', 'Online Exam', '/sidebar_icons/online_exam.svg', '/online_exam', 17, 1, NOW(), NOW()),
(18, 'Settings Section', 'General Settings', '/sidebar_icons/examination.svg', '/settings-section', 18, 1, NOW(), NOW());

-- --------------------------------------------------------
-- 2. Insert Normalized Submenus
-- --------------------------------------------------------
INSERT INTO `sidebar_sub_menus` (`id`, `sidebar_menu_id`, `title`, `path`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
-- Admin Section Submenus (Menu ID 1)
(1, 1, 'Admission Query', NULL, 1, 1, NOW(), NOW()),
(2, 1, 'Complaint', NULL, 2, 1, NOW(), NOW()),
(3, 1, 'Postal Receive', NULL, 3, 1, NOW(), NOW()),
(4, 1, 'Postal Dispatch', NULL, 4, 1, NOW(), NOW()),
(5, 1, 'Phone Call Log', NULL, 5, 1, NOW(), NOW()),
(6, 1, 'Admin Setup', NULL, 6, 1, NOW(), NOW()),
(7, 1, 'ID Card', NULL, 7, 1, NOW(), NOW()),
(8, 1, 'Certificate', NULL, 8, 1, NOW(), NOW()),
(9, 1, 'Generate Certificate', NULL, 9, 1, NOW(), NOW()),
(10, 1, 'Generate ID Card', NULL, 10, 1, NOW(), NOW()),

-- Academics Submenus (Menu ID 2)
(11, 2, 'Optional Subject', NULL, 1, 1, NOW(), NOW()),
(12, 2, 'Section', NULL, 2, 1, NOW(), NOW()),
(13, 2, 'Class', NULL, 3, 1, NOW(), NOW()),
(14, 2, 'Subjects', NULL, 4, 1, NOW(), NOW()),
(15, 2, 'Assign Class Teacher', NULL, 5, 1, NOW(), NOW()),
(16, 2, 'Assign Subject', NULL, 6, 1, NOW(), NOW()),
(17, 2, 'Class Room ', NULL, 7, 1, NOW(), NOW()),
(18, 2, 'Class Routine', NULL, 8, 1, NOW(), NOW()),

-- Study Material Submenus (Menu ID 3)
(19, 3, 'Upload Content', NULL, 1, 1, NOW(), NOW()),
(20, 3, 'Assignment', NULL, 2, 1, NOW(), NOW()),
(21, 3, 'Syllabus', NULL, 3, 1, NOW(), NOW()),
(22, 3, 'Other Downloads', NULL, 4, 1, NOW(), NOW()),

-- Download Center Submenus (Menu ID 6)
(23, 6, 'Content Type', NULL, 1, 1, NOW(), NOW()),
(24, 6, 'Content List', NULL, 2, 1, NOW(), NOW()),
(25, 6, 'Shared Content List', NULL, 3, 1, NOW(), NOW()),
(26, 6, 'Video List', NULL, 4, 1, NOW(), NOW()),

-- Student Info Submenus (Menu ID 7)
(27, 7, 'Student Category', NULL, 1, 1, NOW(), NOW()),
(28, 7, 'Add Student', NULL, 2, 1, NOW(), NOW()),
(29, 7, 'Student List', NULL, 3, 1, NOW(), NOW()),
(30, 7, 'Multi Class Student', NULL, 4, 1, NOW(), NOW()),
(31, 7, 'Delete Student Record', NULL, 5, 1, NOW(), NOW()),
(32, 7, 'Unassigned Student', NULL, 6, 1, NOW(), NOW()),
(33, 7, 'Student Attendance', NULL, 7, 1, NOW(), NOW()),
(34, 7, 'Student Group', NULL, 8, 1, NOW(), NOW()),
(35, 7, 'Student Promote', NULL, 9, 1, NOW(), NOW()),
(36, 7, 'Disabled Students', NULL, 10, 1, NOW(), NOW()),
(37, 7, 'Subject Wise Attendance', NULL, 11, 1, NOW(), NOW()),
(38, 7, 'Student Export', NULL, 12, 1, NOW(), NOW()),
(39, 7, 'SMS Sending Time', NULL, 13, 1, NOW(), NOW()),
(40, 7, 'Student Settings', NULL, 14, 1, NOW(), NOW()),

-- Remarks Point Submenus (Menu ID 8)
(41, 8, 'Incidents', NULL, 1, 1, NOW(), NOW()),
(42, 8, 'Assign Incident', NULL, 2, 1, NOW(), NOW()),
(43, 8, 'Student Incident Report', NULL, 3, 1, NOW(), NOW()),
(44, 8, 'Remarks Report', NULL, 4, 1, NOW(), NOW()),
(45, 8, 'Incident Wise report', NULL, 5, 1, NOW(), NOW()),
(46, 8, 'Settings', NULL, 6, 1, NOW(), NOW()),

-- Fees Submenus (Menu ID 9)
(47, 9, 'Fees Group', NULL, 1, 1, NOW(), NOW()),
(48, 9, 'Fees Type', NULL, 2, 1, NOW(), NOW()),
(49, 9, 'Fees Invoice', NULL, 3, 1, NOW(), NOW()),
(50, 9, 'Bank Payment', NULL, 4, 1, NOW(), NOW()),
(51, 9, 'Fees Carry Forward', NULL, 5, 1, NOW(), NOW()),

-- Human Resource Submenus (Menu ID 14)
(52, 14, 'Add Staff', NULL, 1, 1, NOW(), NOW()),
(53, 14, 'Staff List', NULL, 2, 1, NOW(), NOW()),

-- General Settings Submenus (Menu ID 18)
(54, 18, 'General Settings', NULL, 1, 1, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
