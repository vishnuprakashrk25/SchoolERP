-- ======================================================================
-- Spatie Permission & Roles Tables Setup for Hostinger phpMyAdmin / MySQL
-- Disables Foreign Key Checks to avoid Error #1005 (errno: 150)
-- ======================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Create permissions table
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create roles table
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`, `guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create model_has_permissions table
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`, `model_id`, `model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`, `model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create model_has_roles table
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`, `model_id`, `model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`, `model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create role_has_permissions table
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`, `role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================================
-- Seed Core Roles (Super Admin, Admin, Principal, Teacher, Parents)
-- ======================================================================
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', NOW(), NOW()),
(2, 'admin', 'web', NOW(), NOW()),
(3, 'principal', 'web', NOW(), NOW()),
(4, 'teacher', 'web', NOW(), NOW()),
(5, 'parent', 'web', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ======================================================================
-- Seed Default School ERP Permissions
-- ======================================================================
INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES
('view_students', 'web', NOW(), NOW()),
('create_students', 'web', NOW(), NOW()),
('edit_students', 'web', NOW(), NOW()),
('delete_students', 'web', NOW(), NOW()),
('manage_admissions', 'web', NOW(), NOW()),
('manage_student_documents', 'web', NOW(), NOW()),
('manage_student_categories', 'web', NOW(), NOW()),
('manage_classes', 'web', NOW(), NOW()),
('manage_sections', 'web', NOW(), NOW()),
('manage_subjects', 'web', NOW(), NOW()),
('manage_class_teachers', 'web', NOW(), NOW()),
('manage_class_rooms', 'web', NOW(), NOW()),
('manage_timetables', 'web', NOW(), NOW()),
('view_attendance', 'web', NOW(), NOW()),
('mark_student_attendance', 'web', NOW(), NOW()),
('mark_staff_attendance', 'web', NOW(), NOW()),
('manage_exams', 'web', NOW(), NOW()),
('manage_exam_schedules', 'web', NOW(), NOW()),
('enter_exam_marks', 'web', NOW(), NOW()),
('view_exam_results', 'web', NOW(), NOW()),
('view_fees', 'web', NOW(), NOW()),
('collect_fees', 'web', NOW(), NOW()),
('manage_fee_structures', 'web', NOW(), NOW()),
('manage_fee_groups', 'web', NOW(), NOW()),
('view_expenses', 'web', NOW(), NOW()),
('view_staff', 'web', NOW(), NOW()),
('create_staff', 'web', NOW(), NOW()),
('edit_staff', 'web', NOW(), NOW()),
('delete_staff', 'web', NOW(), NOW()),
('manage_departments', 'web', NOW(), NOW()),
('manage_admission_queries', 'web', NOW(), NOW()),
('manage_complaints', 'web', NOW(), NOW()),
('manage_postal_records', 'web', NOW(), NOW()),
('manage_phone_call_logs', 'web', NOW(), NOW()),
('view_events', 'web', NOW(), NOW()),
('create_events', 'web', NOW(), NOW()),
('edit_events', 'web', NOW(), NOW()),
('delete_events', 'web', NOW(), NOW()),
('view_reports', 'web', NOW(), NOW()),
('manage_general_settings', 'web', NOW(), NOW()),
('manage_roles_permissions', 'web', NOW(), NOW()),
('view_assigned_classes', 'web', NOW(), NOW()),
('submit_student_attendance', 'web', NOW(), NOW()),
('submit_exam_marks', 'web', NOW(), NOW()),
('upload_study_materials', 'web', NOW(), NOW()),
('create_assignments', 'web', NOW(), NOW()),
('view_child_attendance', 'web', NOW(), NOW()),
('view_child_fees', 'web', NOW(), NOW()),
('view_child_results', 'web', NOW(), NOW()),
('view_child_timetable', 'web', NOW(), NOW()),
('view_child_homework', 'web', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

SET FOREIGN_KEY_CHECKS = 1;
