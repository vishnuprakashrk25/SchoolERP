-- =========================================================================
-- School ERP: Academic Category vs Student Social Category Separation Script
-- Optimized for phpMyAdmin / MariaDB (No PREPARE/EXECUTE statements)
-- Target Database: dev.cyberduce.com (u605855890_erp)
-- =========================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Create `academic_categories` table
CREATE TABLE IF NOT EXISTS `academic_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Drop extra legacy columns from `academic_categories` if they exist
ALTER TABLE `academic_categories` DROP COLUMN IF EXISTS `class_ids`;
ALTER TABLE `academic_categories` DROP COLUMN IF EXISTS `class_pattern`;
ALTER TABLE `academic_categories` DROP COLUMN IF EXISTS `stream`;
ALTER TABLE `academic_categories` DROP COLUMN IF EXISTS `min_class_level`;
ALTER TABLE `academic_categories` DROP COLUMN IF EXISTS `max_class_level`;
ALTER TABLE `academic_categories` DROP COLUMN IF EXISTS `is_auto_assign`;

-- 3. Add `academic_category_id` to `classes` table if not present
ALTER TABLE `classes` ADD COLUMN IF NOT EXISTS `academic_category_id` bigint(20) unsigned NULL AFTER `category_id`;

-- 4. Add `academic_category_id` to `students` table if not present and ensure first_name and last_name are nullable
ALTER TABLE `students` ADD COLUMN IF NOT EXISTS `academic_category_id` bigint(20) unsigned NULL AFTER `category_id`;
ALTER TABLE `students` MODIFY COLUMN `first_name` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `students` MODIFY COLUMN `last_name` VARCHAR(255) NULL DEFAULT NULL;

-- 5. Copy Academic records (Rainbow, Planet, Galaxy) from `student_categories` into `academic_categories`
INSERT INTO `academic_categories` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`)
VALUES 
  (6, 'Rainbow - 3', 'Only 3rd Class', NOW(), NOW(), NULL),
  (7, 'Rainbow - 4', 'Only 4th Class', NOW(), NOW(), NULL),
  (8, 'Rainbow - 5', 'Only 5th Class', NOW(), NOW(), NULL),
  (9, 'Planet', '6th to 8th Class', NOW(), NOW(), NULL),
  (10, 'Galaxy HS', '9th to 10th Class', NOW(), NOW(), NULL),
  (11, 'Galaxy HSS (Arts)', '11th and 12th Class (Arts Only)', NOW(), NOW(), NULL)
ON DUPLICATE KEY UPDATE 
  `name` = VALUES(`name`),
  `description` = VALUES(`description`),
  `deleted_at` = NULL;

-- 6. Link `classes.academic_category_id` from existing `classes.category_id`
UPDATE `classes` 
SET `academic_category_id` = `category_id` 
WHERE `category_id` IN (6, 7, 8, 9, 10, 11)
  AND (`academic_category_id` IS NULL OR `academic_category_id` = 0);

-- 7. Link `students.academic_category_id` from `students.category_id` where it held academic categories
UPDATE `students` 
SET `academic_category_id` = `category_id`, `category_id` = NULL 
WHERE `category_id` IN (6, 7, 8, 9, 10, 11);

-- 8. Soft delete Academic records from `student_categories` so they no longer appear under /api/student-categories
UPDATE `student_categories` 
SET `deleted_at` = NOW() 
WHERE `id` IN (6, 7, 8, 9, 10, 11) 
   OR `name` LIKE 'Rainbow%' 
   OR `name` LIKE 'Planet%' 
   OR `name` LIKE 'Galaxy%';

-- 9. Ensure Student Social Categories (General, OBC, SC, ST, Other) exist and are active
INSERT INTO `student_categories` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`)
VALUES 
  (2, 'General', 'General Category', NOW(), NOW(), NULL),
  (3, 'OBC', 'Other Backward Class', NOW(), NOW(), NULL),
  (4, 'SC', 'Scheduled Caste', NOW(), NOW(), NULL),
  (5, 'ST', 'Scheduled Tribe', NOW(), NOW(), NULL),
  (12, 'Other', 'Other Category', NOW(), NOW(), NULL)
ON DUPLICATE KEY UPDATE 
  `name` = VALUES(`name`),
  `description` = VALUES(`description`),
  `deleted_at` = NULL;

-- 10. Foreign key constraints
ALTER TABLE `classes` DROP FOREIGN KEY IF EXISTS `classes_academic_category_id_foreign`;
ALTER TABLE `classes` ADD CONSTRAINT `classes_academic_category_id_foreign` FOREIGN KEY (`academic_category_id`) REFERENCES `academic_categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `students` DROP FOREIGN KEY IF EXISTS `students_academic_category_id_foreign`;
ALTER TABLE `students` ADD CONSTRAINT `students_academic_category_id_foreign` FOREIGN KEY (`academic_category_id`) REFERENCES `academic_categories` (`id`) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS = 1;

-- Completed successfully!
