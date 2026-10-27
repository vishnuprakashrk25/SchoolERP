-- --------------------------------------------------------
-- SaaS Super Admin Setup SQL Script
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Ensure 'super-admin' role exists in roles table
INSERT INTO `roles` (`name`, `guard_name`, `created_at`, `updated_at`) 
SELECT 'super-admin', 'web', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `roles` WHERE `name` = 'super-admin' AND `guard_name` = 'web'
);

-- 2. Create or Update SaaS Super Admin User (branch_id = NULL for global platform scope)
INSERT INTO `users` (`name`, `full_name`, `email`, `password`, `branch_id`, `is_active`, `created_at`, `updated_at`)
VALUES (
    'SaaS Super Admin', 
    'SaaS Super Admin', 
    'superadmin@erp.com', 
    '$2y$12$hg2M16IDvz6iJEoKP0iis.j3/vDc7r5HdbBtJ0IbVl2T1nHOcxmYy', -- password123
    NULL, -- Global SaaS scope (not tied to any specific branch/school)
    1,
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE 
    `name` = VALUES(`name`),
    `full_name` = VALUES(`full_name`),
    `password` = VALUES(`password`),
    `branch_id` = NULL,
    `is_active` = 1,
    `updated_at` = NOW();

-- 3. Assign 'super-admin' role in model_has_roles
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`)
SELECT r.id, 'App\\Models\\User', u.id
FROM `roles` r, `users` u
WHERE r.name = 'super-admin' 
  AND r.guard_name = 'web'
  AND u.email = 'superadmin@erp.com'
  AND NOT EXISTS (
      SELECT 1 FROM `model_has_roles` mhr 
      WHERE mhr.role_id = r.id 
        AND mhr.model_type = 'App\\Models\\User' 
        AND mhr.model_id = u.id
  );

SET FOREIGN_KEY_CHECKS = 1;
