<?php

namespace EduFlex\Controllers\SuperAdmin;

class SchoolController
{
    /**
     * Show the form for creating a new school.
     */
    public function create()
    {
        // This will load the view for the create form
        require_once __DIR__ . '/../../../views/super_admin/schools/create.php';
    }

    /**
     * Store a newly created school in the database.
     */
    public function store()
    {
        // 1. Get POST data and sanitize it
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $domain = trim($_POST['domain'] ?? '');

        // 2. Basic validation
        if (empty($name)) {
            // In a real app, you'd redirect back with an error message
            die('School name is required.');
        }

        // 3. Insert into database
        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $sql = "INSERT INTO schools (name, email, phone, address, domain, status) VALUES (:name, :email, :phone, :address, :domain, :status)";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':name' => $name,
                ':email' => !empty($email) ? $email : null,
                ':phone' => !empty($phone) ? $phone : null,
                ':address' => !empty($address) ? $address : null,
                ':domain' => !empty($domain) ? $domain : null,
                ':status' => 'active' // Default to active when created manually
            ]);

            // 4. Redirect back to the dashboard
            header('Location: /super-admin/dashboard');
            exit;

        } catch (\PDOException $e) {
            // In a real app, log this and show a user-friendly error page
            // For now, just display the error for debugging
            die('Database error: ' . $e->getMessage());
        }
    }
}
