-- ==============================================================================
-- School ERP - Fees & Finance Module (Phase 1 & Phase 2 Schema Upgrade)
-- Database Script: MariaDB / MySQL 8.0+ compatible (with IF NOT EXISTS)
-- Target Server: dev.cyberduce.com (u605855890_erp) / Local XAMPP
-- ==============================================================================

-- 1. Upgrade fee_groups table
ALTER TABLE `fee_groups`
  ADD COLUMN IF NOT EXISTS `academic_year_id` BIGINT UNSIGNED NULL AFTER `academic_id`,
  ADD COLUMN IF NOT EXISTS `branch_id` BIGINT UNSIGNED NULL AFTER `school_id`,
  ADD COLUMN IF NOT EXISTS `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `description`;

-- 2. Upgrade fee_types table
ALTER TABLE `fee_types`
  ADD COLUMN IF NOT EXISTS `fee_group_id` BIGINT UNSIGNED NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `code` VARCHAR(100) NULL AFTER `name`,
  ADD COLUMN IF NOT EXISTS `description` TEXT NULL AFTER `amount`,
  ADD COLUMN IF NOT EXISTS `billing_cycle` VARCHAR(50) NOT NULL DEFAULT 'one_time' AFTER `description`,
  ADD COLUMN IF NOT EXISTS `school_id` BIGINT UNSIGNED NULL AFTER `billing_cycle`,
  ADD COLUMN IF NOT EXISTS `academic_year_id` BIGINT UNSIGNED NULL AFTER `school_id`,
  ADD COLUMN IF NOT EXISTS `created_by` BIGINT UNSIGNED NULL AFTER `academic_year_id`,
  ADD COLUMN IF NOT EXISTS `updated_by` BIGINT UNSIGNED NULL AFTER `created_by`;

-- 3. Upgrade fee_structures table
ALTER TABLE `fee_structures`
  ADD COLUMN IF NOT EXISTS `name` VARCHAR(255) NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `academic_year_id` BIGINT UNSIGNED NULL AFTER `name`,
  ADD COLUMN IF NOT EXISTS `section_id` BIGINT UNSIGNED NULL AFTER `class_id`,
  ADD COLUMN IF NOT EXISTS `category_id` BIGINT UNSIGNED NULL AFTER `section_id`,
  ADD COLUMN IF NOT EXISTS `academic_category_id` BIGINT UNSIGNED NULL AFTER `category_id`,
  ADD COLUMN IF NOT EXISTS `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `amount`,
  ADD COLUMN IF NOT EXISTS `description` TEXT NULL AFTER `total_amount`,
  ADD COLUMN IF NOT EXISTS `school_id` BIGINT UNSIGNED NULL AFTER `description`,
  ADD COLUMN IF NOT EXISTS `created_by` BIGINT UNSIGNED NULL AFTER `school_id`,
  ADD COLUMN IF NOT EXISTS `updated_by` BIGINT UNSIGNED NULL AFTER `created_by`,
  ADD COLUMN IF NOT EXISTS `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `updated_by`;

-- 4. Create fee_structure_items table
CREATE TABLE IF NOT EXISTS `fee_structure_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `fee_structure_id` BIGINT UNSIGNED NOT NULL,
  `fee_type_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `due_date` DATE NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_fee_structure_id` (`fee_structure_id`),
  INDEX `idx_fee_type_id` (`fee_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create fee_installments table
CREATE TABLE IF NOT EXISTS `fee_installments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `fee_structure_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(255) NOT NULL,
  `due_date` DATE NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `late_fee_type` VARCHAR(50) NOT NULL DEFAULT 'fixed',
  `late_fee_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `grace_period_days` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_installment_fee_structure` (`fee_structure_id`),
  INDEX `idx_installment_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Upgrade student_fees table
ALTER TABLE `student_fees`
  ADD COLUMN IF NOT EXISTS `academic_year_id` BIGINT UNSIGNED NULL AFTER `student_id`,
  ADD COLUMN IF NOT EXISTS `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `paid_amount`,
  ADD COLUMN IF NOT EXISTS `fine_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `discount_amount`,
  ADD COLUMN IF NOT EXISTS `balance_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `fine_amount`,
  ADD COLUMN IF NOT EXISTS `remarks` TEXT NULL AFTER `balance_amount`,
  ADD COLUMN IF NOT EXISTS `created_by` BIGINT UNSIGNED NULL AFTER `remarks`,
  ADD COLUMN IF NOT EXISTS `updated_by` BIGINT UNSIGNED NULL AFTER `created_by`;

-- 7. Create student_fee_items table
CREATE TABLE IF NOT EXISTS `student_fee_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `student_fee_id` BIGINT UNSIGNED NOT NULL,
  `fee_structure_item_id` BIGINT UNSIGNED NULL,
  `fee_type_id` BIGINT UNSIGNED NOT NULL,
  `fee_installment_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `fine_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `balance_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `due_date` DATE NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_sfi_student_fee_id` (`student_fee_id`),
  INDEX `idx_sfi_fee_type_id` (`fee_type_id`),
  INDEX `idx_sfi_installment_id` (`fee_installment_id`),
  INDEX `idx_sfi_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Upgrade payments table
ALTER TABLE `payments`
  ADD COLUMN IF NOT EXISTS `payment_no` VARCHAR(100) NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `receipt_no` VARCHAR(100) NULL AFTER `payment_no`,
  ADD COLUMN IF NOT EXISTS `student_id` BIGINT UNSIGNED NULL AFTER `receipt_no`,
  ADD COLUMN IF NOT EXISTS `academic_year_id` BIGINT UNSIGNED NULL AFTER `student_id`,
  ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(50) NOT NULL DEFAULT 'cash' AFTER `payment_date`,
  ADD COLUMN IF NOT EXISTS `payment_reference` VARCHAR(255) NULL AFTER `payment_method`,
  ADD COLUMN IF NOT EXISTS `bank_name` VARCHAR(255) NULL AFTER `payment_reference`,
  ADD COLUMN IF NOT EXISTS `cheque_date` DATE NULL AFTER `bank_name`,
  ADD COLUMN IF NOT EXISTS `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `amount`,
  ADD COLUMN IF NOT EXISTS `fine_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `discount_amount`,
  ADD COLUMN IF NOT EXISTS `total_paid` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `fine_amount`,
  ADD COLUMN IF NOT EXISTS `note` TEXT NULL AFTER `total_paid`,
  ADD COLUMN IF NOT EXISTS `collected_by` BIGINT UNSIGNED NULL AFTER `note`,
  ADD COLUMN IF NOT EXISTS `status` VARCHAR(50) NOT NULL DEFAULT 'success' AFTER `collected_by`,
  ADD COLUMN IF NOT EXISTS `reversal_reason` TEXT NULL AFTER `status`,
  ADD COLUMN IF NOT EXISTS `reversed_at` DATETIME NULL AFTER `reversal_reason`,
  ADD COLUMN IF NOT EXISTS `reversed_by` BIGINT UNSIGNED NULL AFTER `reversed_at`;

-- 9. Create fee_payment_items table
CREATE TABLE IF NOT EXISTS `fee_payment_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `payment_id` BIGINT UNSIGNED NOT NULL,
  `student_fee_item_id` BIGINT UNSIGNED NULL,
  `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `fine_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `note` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_fpi_payment_id` (`payment_id`),
  INDEX `idx_fpi_student_fee_item_id` (`student_fee_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- End of Phase 1 & 2 Setup Script
