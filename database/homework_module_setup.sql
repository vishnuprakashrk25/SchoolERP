-- --------------------------------------------------------
-- Homework Module Database Tables & Submenus Setup
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Create `homeworks` table
CREATE TABLE IF NOT EXISTS `homeworks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `academic_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `evaluated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `homework_date` date NOT NULL,
  `submission_date` date NOT NULL,
  `evaluation_date` date DEFAULT NULL,
  `marks` decimal(8,2) NOT NULL DEFAULT 100.00,
  `description` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `homeworks_class_id_index` (`class_id`),
  KEY `homeworks_section_id_index` (`section_id`),
  KEY `homeworks_subject_id_index` (`subject_id`),
  KEY `homeworks_created_by_index` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create `homework_submissions` table
CREATE TABLE IF NOT EXISTS `homework_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `homework_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `submission_date` datetime DEFAULT NULL,
  `submission_file` varchar(255) DEFAULT NULL,
  `student_notes` text DEFAULT NULL,
  `marks` decimal(8,2) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `evaluated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `evaluation_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `homework_student_unique` (`homework_id`, `student_id`),
  KEY `homework_submissions_homework_id_index` (`homework_id`),
  KEY `homework_submissions_student_id_index` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Add Home Work submenus to `sidebar_sub_menus` (Parent Menu ID 10)
INSERT INTO `sidebar_sub_menus` (`sidebar_menu_id`, `title`, `path`, `order`, `is_active`, `created_at`, `updated_at`)
SELECT 10, 'Add Homework', '/add-homework', 1, 1, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `sidebar_sub_menus` WHERE `sidebar_menu_id` = 10 AND `title` = 'Add Homework');

INSERT INTO `sidebar_sub_menus` (`sidebar_menu_id`, `title`, `path`, `order`, `is_active`, `created_at`, `updated_at`)
SELECT 10, 'Homework List', '/homework-list', 2, 1, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `sidebar_sub_menus` WHERE `sidebar_menu_id` = 10 AND `title` = 'Homework List');

INSERT INTO `sidebar_sub_menus` (`sidebar_menu_id`, `title`, `path`, `order`, `is_active`, `created_at`, `updated_at`)
SELECT 10, 'Homework Report', '/homework-report', 3, 1, NOW(), NOW()
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `sidebar_sub_menus` WHERE `sidebar_menu_id` = 10 AND `title` = 'Homework Report');

SET FOREIGN_KEY_CHECKS = 1;
