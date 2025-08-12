<?php
// A simple, self-contained installer script for EduFlex.
// WARNING: This file should be deleted immediately after a successful installation.

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// --- Configuration ---
$default_admin_email = 'admin@example.com';
$default_admin_pass = 'password';
$db_config_path = __DIR__ . '/config/database.php';
$db_schema_path = __DIR__ . '/database.sql';

// --- State Management ---
$step = $_GET['step'] ?? 'welcome';
$errors = [];
$success_messages = [];

// --- Business Logic ---

// Pre-installation checks
$config_exists = file_exists($db_config_path);
$pdo = null;
$db_connection_error = '';

if ($config_exists) {
    try {
        $config = require $db_config_path;
        $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    } catch (PDOException $e) {
        $db_connection_error = "Database connection failed: " . $e->getMessage();
        $pdo = null;
    }
}

// Installation logic
if ($step === 'install' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$config_exists) {
        $errors[] = "Database configuration file not found. Please create `config/database.php`.";
    } elseif ($pdo === null) {
        $errors[] = "Could not connect to the database. Please check your credentials in `config/database.php`.";
    } else {
        // 1. Create tables
        try {
            $sql_script = file_get_contents($db_schema_path);

            // Remove single-line and multi-line comments from the SQL script
            $sql_script = preg_replace('/--.*/', '', $sql_script);
            $sql_script = preg_replace('!/\*.*?\*/!s', '', $sql_script);

            // Split the script into individual statements, filtering out empty ones.
            $statements = array_filter(array_map('trim', explode(';', $sql_script)));

            foreach ($statements as $index => $statement) {
                if (!empty($statement)) {
                    try {
                        $pdo->exec($statement . ';');
                    } catch (PDOException $e) {
                        $failing_statement = substr($statement, 0, 150);
                        throw new Exception("Error executing SQL statement #" . ($index + 1) . ": " . $e->getMessage() . " (Statement starts with: '{$failing_statement}...')", 0, $e);
                    }
                }
            }

            $success_messages[] = "Database tables created successfully.";

            // 1b. Handle schema migration for existing tables
            $db_name = $config['name'];
            $stmt = $pdo->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'whmcs_invoice_id'");
            $stmt->execute([$db_name]);
            if ($stmt->fetch() === false) {
                // Column does not exist, so add it.
                $pdo->exec("ALTER TABLE `schools` ADD COLUMN `whmcs_invoice_id` INT UNSIGNED NULL AFTER `whmcs_order_id`");
                $success_messages[] = "Database schema updated successfully (added missing column).";
            }

        } catch (Exception $e) {
            $errors[] = "Error during database setup: " . $e->getMessage();
        }

        // 2. Create admin user
        if (empty($errors)) {
            try {
                // Check if admin already exists
                $stmt = $pdo->prepare("SELECT id FROM super_admins WHERE email = ?");
                $stmt->execute([$default_admin_email]);
                if ($stmt->fetch()) {
                    $success_messages[] = "Default admin user already exists.";
                } else {
                    $hashed_password = password_hash($default_admin_pass, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO super_admins (name, email, password) VALUES (?, ?, ?)");
                    $stmt->execute(['Super Admin', $default_admin_email, $hashed_password]);
                    $success_messages[] = "Default admin user created successfully.";
                }
                // Redirect to success step to prevent re-submission
                header("Location: install.php?step=success");
                exit;
            } catch (Exception $e) {
                $errors[] = "Error creating admin user: " . $e->getMessage();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlex Installer</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f7fc; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: 50px auto; background: #fff; padding: 30px 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #3498db; color: #fff; border-radius: 5px; text-decoration: none; border: none; cursor: pointer; font-size: 16px; transition: background-color 0.3s; }
        .btn:hover { background-color: #2980b9; }
        .btn-disabled { background-color: #bdc3c7; cursor: not-allowed; }
        .status-list { list-style: none; padding: 0; }
        .status-list li { padding: 10px; border-left: 4px solid #ccc; margin-bottom: 10px; }
        .status-list .success { border-color: #27ae60; background-color: #f0fdf5; }
        .status-list .error { border-color: #c0392b; background-color: #fdf0f0; }
        .alert { padding: 15px; margin-top: 20px; border-radius: 5px; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-error { background-color: #f8d7da; color: #721c24; }
        .alert-warning { background-color: #fff3cd; color: #856404; font-weight: bold; text-align: center; }
        code { background-color: #ecf0f1; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>EduFlex Web Installer</h1>

        <?php if ($step === 'welcome'): ?>
            <h2>Step 1: Pre-Installation Checks</h2>
            <p>This script will guide you through setting up the application. Below are the pre-installation checks:</p>
            <ul class="status-list">
                <?php if ($config_exists): ?>
                    <li class="success">Configuration file (<code>config/database.php</code>) found.</li>
                <?php else: ?>
                    <li class="error">Configuration file (<code>config/database.php</code>) not found. Please create it by copying `config/database.php.example` and filling in your database details.</li>
                <?php endif; ?>

                <?php if ($config_exists): ?>
                    <?php if ($pdo): ?>
                        <li class="success">Database connection successful.</li>
                    <?php else: ?>
                        <li class="error"><?= htmlspecialchars($db_connection_error) ?></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <?php if ($config_exists && $pdo): ?>
                <p>All checks passed! You can now proceed with the installation.</p>
                <form action="install.php?step=install" method="POST">
                    <button type="submit" class="btn">Install Now</button>
                </form>
            <?php else: ?>
                <p>Please resolve the issues above before you can proceed.</p>
                <button class="btn btn-disabled" disabled>Install Now</button>
            <?php endif; ?>

        <?php elseif ($step === 'install' && !empty($errors)): ?>
            <h2>Installation Failed</h2>
            <div class="alert alert-error">
                <p>The following errors occurred during installation:</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="install.php" class="btn">Retry</a>

        <?php elseif ($step === 'success'): ?>
            <h2>Installation Successful!</h2>
            <div class="alert alert-success">
                <p>EduFlex has been installed successfully.</p>
            </div>

            <h4>Default Login Credentials:</h4>
            <p><strong>Email:</strong> <code><?= htmlspecialchars($default_admin_email) ?></code></p>
            <p><strong>Password:</strong> <code><?= htmlspecialchars($default_admin_pass) ?></code></p>

            <p>Please log in and change your password immediately.</p>

            <div class="alert alert-warning">
                <strong>SECURITY WARNING:</strong> Please delete this <code>install.php</code> file from your server immediately!
            </div>

            <a href="/super-admin/login" class="btn">Go to Super Admin Login</a>

        <?php endif; ?>
    </div>
</body>
</html>
