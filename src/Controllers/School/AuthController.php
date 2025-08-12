<?php

namespace EduFlex\Controllers\School;

class AuthController
{
    /**
     * Show the login form for the school.
     */
    public function showLoginForm()
    {
        require_once __DIR__ . '/../../../views/school/auth/login.php';
    }

    /**
     * Handle a login attempt for the school.
     */
    public function login()
    {
        session_start();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password) || !defined('CURRENT_SCHOOL_ID')) {
            // Redirect back with an error
            $_SESSION['error_message'] = 'Invalid login attempt.';
            header('Location: /login');
            exit;
        }

        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE school_id = ? AND email = ?");
            $stmt->execute([CURRENT_SCHOOL_ID, $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['school_id'] = $user['school_id'];
                $_SESSION['user_role'] = $user['role'];
                header('Location: /dashboard'); // Redirect to school admin dashboard
                exit;
            } else {
                // Invalid credentials
                $_SESSION['error_message'] = 'Invalid email or password.';
                header('Location: /login');
                exit;
            }
        } catch (\PDOException $e) {
            $_SESSION['error_message'] = 'A database error occurred.';
            header('Location: /login');
            exit;
        }
    }
}
