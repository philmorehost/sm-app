<?php

namespace EduFlex\Controllers\SuperAdmin;

class AuthController
{
    public function showLoginForm()
    {
        // This will render the login view
        require_once __DIR__ . '/../../../views/super_admin/login.php';
    }

    public function login()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = "Email and password are required.";
            require_once __DIR__ . '/../../../views/super_admin/login.php';
            return;
        }

        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM super_admins WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Password is correct, user is authenticated
                $_SESSION['super_admin_id'] = $user['id'];
                header("Location: /super-admin/dashboard");
                exit;
            } else {
                // Invalid credentials
                $error = "Invalid email or password.";
                require_once __DIR__ . '/../../../views/super_admin/login.php';
            }
        } catch (\PDOException $e) {
            // In a real app, log this error.
            $error = "An error occurred. Please try again later.";
            require_once __DIR__ . '/../../../views/super_admin/login.php';
        }
    }

    public function dashboard()
    {
        session_start();
        if (!isset($_SESSION['super_admin_id'])) {
            header('Location: /super-admin/login');
            exit;
        }

        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $stmt = $pdo->query("SELECT * FROM schools ORDER BY created_at DESC");
            $schools = $stmt->fetchAll();
        } catch (\PDOException $e) {
            // For simplicity, we'll just show an empty array on error.
            // In a real app, you'd want to log this and show an error message.
            $schools = [];
        }

        require_once __DIR__ . '/../../../views/super_admin/dashboard.php';
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /super-admin/login');
        exit;
    }
}
