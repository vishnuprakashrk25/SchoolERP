-- --------------------------------------------------------
-- Behaviour Records & Remarks Suite Setup (Hostinger Live Server)
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for table `incidents` (Master Remarks/Incidents)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `incidents` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `point` int(11) NOT NULL DEFAULT 0,
  `is_negative` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incidents_branch_id_index` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `student_incidents` (Assigned Behaviour/Remarks)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `student_incidents` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `incident_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `point` int(11) NOT NULL DEFAULT 0,
  `is_negative` tinyint(1) NOT NULL DEFAULT 0,
  `date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_incidents_branch_id_index` (`branch_id`),
  KEY `student_incidents_student_id_index` (`student_id`),
  KEY `student_incidents_incident_id_index` (`incident_id`),
  KEY `student_incidents_assigned_by_index` (`assigned_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `behaviour_settings` (Toggles)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `behaviour_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_comment` tinyint(1) NOT NULL DEFAULT 0,
  `parent_comment` tinyint(1) NOT NULL DEFAULT 1,
  `student_view` tinyint(1) NOT NULL DEFAULT 1,
  `parent_view` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `behaviour_settings_branch_id_index` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Default Behaviour Settings Row
-- --------------------------------------------------------
INSERT INTO `behaviour_settings` (`id`, `branch_id`, `student_comment`, `parent_comment`, `student_view`, `parent_view`, `created_at`, `updated_at`)
VALUES (1, NULL, 0, 1, 1, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE `student_view` = VALUES(`student_view`);

-- --------------------------------------------------------
-- Sample Standard Behaviour Incidents
-- --------------------------------------------------------
INSERT INTO `incidents` (`id`, `branch_id`, `title`, `point`, `is_negative`, `description`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Good Discipline', 10, 0, 'Exemplary attitude, discipline, and conduct in class and campus', NOW(), NOW()),
(2, NULL, 'Helping Classmates', 5, 0, 'Voluntarily assisted other students in learning and school tasks', NOW(), NOW()),
(3, NULL, 'Late Attendance', 5, 1, 'Arrived late to class without a valid permission slip', NOW(), NOW()),
(4, NULL, 'Incomplete Homework', 5, 1, 'Did not submit assigned homework on the scheduled date', NOW(), NOW()),
(5, NULL, 'Excellence in Sports / Extracurricular', 15, 0, 'Outstanding performance and sportsmanship in school activities', NOW(), NOW())
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- --------------------------------------------------------
-- Update Sidebar Submenu Paths for Menu ID 8 (Behaviour Records)
-- --------------------------------------------------------
UPDATE `sidebar_sub_menus` SET `path` = '/incidents' WHERE `title` = 'Incidents' AND `sidebar_menu_id` = 8;
UPDATE `sidebar_sub_menus` SET `path` = '/assign-incident' WHERE `title` = 'Assign Incident' AND `sidebar_menu_id` = 8;
UPDATE `sidebar_sub_menus` SET `path` = '/student-incident-report' WHERE `title` = 'Student Incident Report' AND `sidebar_menu_id` = 8;
UPDATE `sidebar_sub_menus` SET `path` = '/remarks-report' WHERE `title` = 'Remarks Report' AND `sidebar_menu_id` = 8;
UPDATE `sidebar_sub_menus` SET `path` = '/incident-wise-report' WHERE `title` = 'Incident Wise report' AND `sidebar_menu_id` = 8;
UPDATE `sidebar_sub_menus` SET `path` = '/behaviour-settings' WHERE `title` = 'Settings' AND `sidebar_menu_id` = 8;

SET FOREIGN_KEY_CHECKS = 1;
