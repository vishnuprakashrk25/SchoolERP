-- ====================================================================
-- School ERP - Latest Incremental SQL Updates
-- Date: 2026-11-01
-- Run this script on your live/production database to apply all latest schema changes.
-- ====================================================================

-- 1. DRIVER ROLE & PERMISSIONS (Spatie Permission Tables)
INSERT IGNORE INTO `roles` (`name`, `guard_name`, `created_at`, `updated_at`) 
VALUES ('driver', 'web', NOW(), NOW()), ('driver', 'sanctum', NOW(), NOW());

INSERT IGNORE INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES
('view_transport', 'web', NOW(), NOW()),
('view_transport', 'sanctum', NOW(), NOW()),
('view_routes', 'web', NOW(), NOW()),
('view_routes', 'sanctum', NOW(), NOW()),
('view_vehicles', 'web', NOW(), NOW()),
('view_vehicles', 'sanctum', NOW(), NOW()),
('view_my_attendance', 'web', NOW(), NOW()),
('view_my_attendance', 'sanctum', NOW(), NOW());

-- 2. CLASS & SECTION NULLABILITY UPDATES
-- Allows standalone sections without a class, and classes without sections
ALTER TABLE `sections` MODIFY `class_id` BIGINT UNSIGNED NULL;
ALTER TABLE `student_academics` MODIFY `section_id` BIGINT UNSIGNED NULL;
ALTER TABLE `class_teachers` MODIFY `section_id` BIGINT UNSIGNED NULL;
ALTER TABLE `class_routines` MODIFY `section_id` BIGINT UNSIGNED NULL;
ALTER TABLE `homeworks` MODIFY `section_id` BIGINT UNSIGNED NULL;
ALTER TABLE `student_multi_class` MODIFY `section_id` BIGINT UNSIGNED NULL;

-- 3. STUDENT CASTE & SECTION NULLABLE
ALTER TABLE `students` MODIFY `caste` VARCHAR(255) NULL;
ALTER TABLE `students` MODIFY `section_id` BIGINT UNSIGNED NULL;

-- 4. SOFT DELETES (deleted_at) ON ALL TABLES
-- Adds deleted_at timestamp column if not already present
SET @dbname = DATABASE();

DROP PROCEDURE IF EXISTS AddSoftDeletesColumn;
DELIMITER //
CREATE PROCEDURE AddSoftDeletesColumn(IN tblName VARCHAR(255))
BEGIN
    IF EXISTS (SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = tblName) THEN
        IF NOT EXISTS (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = tblName AND COLUMN_NAME = 'deleted_at') THEN
            SET @query = CONCAT('ALTER TABLE `', tblName, '` ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL');
            PREPARE stmt FROM @query;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        END IF;
    END IF;
END //
DELIMITER ;

CALL AddSoftDeletesColumn('admission_queries');
CALL AddSoftDeletesColumn('behaviour_settings');
CALL AddSoftDeletesColumn('class_rooms');
CALL AddSoftDeletesColumn('class_routines');
CALL AddSoftDeletesColumn('class_teachers');
CALL AddSoftDeletesColumn('complaints');
CALL AddSoftDeletesColumn('events');
CALL AddSoftDeletesColumn('expenses');
CALL AddSoftDeletesColumn('general_settings');
CALL AddSoftDeletesColumn('generated_id_cards');
CALL AddSoftDeletesColumn('id_card_templates');
CALL AddSoftDeletesColumn('model_has_permissions');
CALL AddSoftDeletesColumn('model_has_roles');
CALL AddSoftDeletesColumn('optional_subjects');
CALL AddSoftDeletesColumn('permissions');
CALL AddSoftDeletesColumn('phone_call_logs');
CALL AddSoftDeletesColumn('postal_dispatches');
CALL AddSoftDeletesColumn('postal_receives');
CALL AddSoftDeletesColumn('roles');
CALL AddSoftDeletesColumn('role_has_permissions');
CALL AddSoftDeletesColumn('sidebar_menus');
CALL AddSoftDeletesColumn('sidebar_sub_menus');
CALL AddSoftDeletesColumn('student_academics');
CALL AddSoftDeletesColumn('student_emergency_contacts');
CALL AddSoftDeletesColumn('todos');
CALL AddSoftDeletesColumn('user_profiles');

DROP PROCEDURE IF EXISTS AddSoftDeletesColumn;

-- 5. SYNC ROLL NO FROM ACADEMICS TABLE TO STUDENTS TABLE
UPDATE `students` s
INNER JOIN (
    SELECT student_id, roll_no
    FROM student_academics
    WHERE roll_no IS NOT NULL AND roll_no != ''
    ORDER BY id DESC
) sa ON s.id = sa.student_id
SET s.roll_no = sa.roll_no
WHERE s.roll_no IS NULL OR s.roll_no = '';

-- 6. RECORD MIGRATIONS IN LARAVEL MIGRATIONS TABLE
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2026_10_29_000000_add_driver_role_and_permissions', (SELECT IFNULL(MAX(batch), 0) + 1 FROM migrations m)),
('2026_10_31_000000_sync_student_roll_no_from_academics', (SELECT IFNULL(MAX(batch), 0) + 1 FROM migrations m)),
('2026_10_31_000001_add_soft_deletes_to_all_tables', (SELECT IFNULL(MAX(batch), 0) + 1 FROM migrations m)),
('2026_11_01_000000_make_class_and_section_nullable', (SELECT IFNULL(MAX(batch), 0) + 1 FROM migrations m));
