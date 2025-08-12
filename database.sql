-- Initial database schema for EduFlex

-- Table for Super Administrators
CREATE TABLE IF NOT EXISTS `super_admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Schema migration to add the invoice ID column for older installations.
-- You can add a default super admin user for initial setup,
-- but be sure to change the password in a real environment.
-- Example: INSERT INTO `super_admins` (`name`, `email`, `password`) VALUES ('Super Admin', 'admin@eduflex.com', PASSWORD_HASH('password123', PASSWORD_DEFAULT));


-- Table for Schools
CREATE TABLE IF NOT EXISTS `schools` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NULL,
  `phone` VARCHAR(50) NULL,
  `address` TEXT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending', -- e.g., pending, active, suspended
  `domain` VARCHAR(255) NULL UNIQUE,
  `whmcs_client_id` INT UNSIGNED NULL,
  `whmcs_order_id` INT UNSIGNED NULL,
  `whmcs_invoice_id` INT UNSIGNED NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
