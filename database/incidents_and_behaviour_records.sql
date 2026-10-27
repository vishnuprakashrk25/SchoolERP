-- --------------------------------------------------------
-- Incidents & Student Behaviour Records (Remarks) SQL
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for table `incidents`
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
-- Table structure for table `student_incidents` (Behaviour Records)
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
-- Sample Default Incidents
-- --------------------------------------------------------

INSERT INTO `incidents` (`id`, `branch_id`, `title`, `point`, `is_negative`, `description`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Good Discipline', 10, 0, 'Exemplary attitude, discipline, and conduct in class and campus', NOW(), NOW()),
(2, NULL, 'Helping Classmates', 5, 0, 'Voluntarily assisted other students in learning and school tasks', NOW(), NOW()),
(3, NULL, 'Late Attendance', 5, 1, 'Arrived late to class without a valid permission slip', NOW(), NOW()),
(4, NULL, 'Incomplete Homework', 5, 1, 'Did not submit assigned homework on the scheduled date', NOW(), NOW()),
(5, NULL, 'Excellence in Sports / Extracurricular', 15, 0, 'Outstanding performance and sportsmanship in school activities', NOW(), NOW())
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

SET FOREIGN_KEY_CHECKS = 1;
