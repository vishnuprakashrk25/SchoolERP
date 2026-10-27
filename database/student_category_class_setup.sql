-- ========================================================
-- STUDENT CATEGORY & CLASS DYNAMIC SETUP SCRIPT
-- Compatible with Hostinger phpMyAdmin / MySQL 5.7+ / MariaDB
-- ========================================================

-- STEP 1: Add `category_id` column to `classes` table if it does not already exist
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'classes' 
      AND COLUMN_NAME = 'category_id'
);

SET @sql_add_col = IF(
    @col_exists = 0,
    'ALTER TABLE `classes` ADD COLUMN `category_id` BIGINT UNSIGNED NULL AFTER `academic_year_id`',
    'SELECT "Column category_id already exists" AS info'
);
PREPARE stmt_col FROM @sql_add_col;
EXECUTE stmt_col;
DEALLOCATE PREPARE stmt_col;


-- STEP 2: Add Foreign Key Constraint if it does not already exist
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'classes' 
      AND CONSTRAINT_NAME = 'classes_category_id_foreign'
);

SET @sql_add_fk = IF(
    @fk_exists = 0,
    'ALTER TABLE `classes` ADD CONSTRAINT `classes_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `student_categories` (`id`) ON DELETE SET NULL',
    'SELECT "Foreign key constraint already exists" AS info'
);
PREPARE stmt_fk FROM @sql_add_fk;
EXECUTE stmt_fk;
DEALLOCATE PREPARE stmt_fk;


-- STEP 3: Insert the Handwritten Categories (Safe: avoids duplicate errors)
INSERT INTO `student_categories` (`name`, `description`, `created_at`, `updated_at`)
SELECT 'Rainbow - 3', 'Only 3rd Class', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `student_categories` WHERE `name` = 'Rainbow - 3');

INSERT INTO `student_categories` (`name`, `description`, `created_at`, `updated_at`)
SELECT 'Rainbow - 4', 'Only 4th Class', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `student_categories` WHERE `name` = 'Rainbow - 4');

INSERT INTO `student_categories` (`name`, `description`, `created_at`, `updated_at`)
SELECT 'Rainbow - 5', 'Only 5th Class', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `student_categories` WHERE `name` = 'Rainbow - 5');

INSERT INTO `student_categories` (`name`, `description`, `created_at`, `updated_at`)
SELECT 'Planet', '6th to 8th Class', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `student_categories` WHERE `name` = 'Planet');

INSERT INTO `student_categories` (`name`, `description`, `created_at`, `updated_at`)
SELECT 'Galaxy HS', '9th to 10th Class', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `student_categories` WHERE `name` = 'Galaxy HS');

INSERT INTO `student_categories` (`name`, `description`, `created_at`, `updated_at`)
SELECT 'Galaxy HSS (Arts)', '11th and 12th Class (Arts Only)', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `student_categories` WHERE `name` = 'Galaxy HSS (Arts)');


-- STEP 4: Link Classes to their Corresponding Categories dynamically
-- Rainbow - 3 -> 3rd Class
UPDATE `classes` 
SET `category_id` = (SELECT `id` FROM `student_categories` WHERE `name` = 'Rainbow - 3' LIMIT 1)
WHERE `name` = 'Class 3' OR `name` LIKE 'Class 3 %' OR `name` LIKE 'Grade 3%' OR `name` LIKE '3rd%';

-- Rainbow - 4 -> 4th Class
UPDATE `classes` 
SET `category_id` = (SELECT `id` FROM `student_categories` WHERE `name` = 'Rainbow - 4' LIMIT 1)
WHERE `name` = 'Class 4' OR `name` LIKE 'Class 4 %' OR `name` LIKE 'Grade 4%' OR `name` LIKE '4th%';

-- Rainbow - 5 -> 5th Class
UPDATE `classes` 
SET `category_id` = (SELECT `id` FROM `student_categories` WHERE `name` = 'Rainbow - 5' LIMIT 1)
WHERE `name` = 'Class 5' OR `name` LIKE 'Class 5 %' OR `name` LIKE 'Grade 5%' OR `name` LIKE '5th%';

-- Planet -> 6th to 8th Classes
UPDATE `classes` 
SET `category_id` = (SELECT `id` FROM `student_categories` WHERE `name` = 'Planet' LIMIT 1)
WHERE `name` LIKE 'Class 6%' OR `name` LIKE 'Grade 6%' OR `name` LIKE '6th%'
   OR `name` LIKE 'Class 7%' OR `name` LIKE 'Grade 7%' OR `name` LIKE '7th%'
   OR `name` LIKE 'Class 8%' OR `name` LIKE 'Grade 8%' OR `name` LIKE '8th%';

-- Galaxy HS -> 9th and 10th Classes
UPDATE `classes` 
SET `category_id` = (SELECT `id` FROM `student_categories` WHERE `name` = 'Galaxy HS' LIMIT 1)
WHERE `name` LIKE 'Class 9%' OR `name` LIKE 'Grade 9%' OR `name` LIKE '9th%'
   OR `name` LIKE 'Class 10%' OR `name` LIKE 'Grade 10%' OR `name` LIKE '10th%'
   OR `name` = 'Class X' OR `name` LIKE 'Class X %';

-- Galaxy HSS (Arts) -> 11th and 12th Classes (Arts)
UPDATE `classes` 
SET `category_id` = (SELECT `id` FROM `student_categories` WHERE `name` = 'Galaxy HSS (Arts)' LIMIT 1)
WHERE `name` LIKE '%11%Arts%' OR `name` LIKE '%12%Arts%'
   OR `name` LIKE '%+1%Arts%' OR `name` LIKE '%+2%Arts%';


-- STEP 5: Sync category_id to all existing students based on their class
UPDATE `students` s
JOIN `classes` c ON s.class_id = c.id
SET s.category_id = c.category_id
WHERE s.category_id IS NULL AND c.category_id IS NOT NULL;
