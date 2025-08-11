-- Initial database schema for EduFlex

-- Table for Super Administrators
CREATE TABLE `super_admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- You can add a default super admin user for initial setup,
-- but be sure to change the password in a real environment.
-- Example: INSERT INTO `super_admins` (`name`, `email`, `password`) VALUES ('Super Admin', 'admin@eduflex.com', PASSWORD_HASH('password123', PASSWORD_DEFAULT));
