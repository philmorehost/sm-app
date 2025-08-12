<?php

namespace EduFlex\Controllers\SuperAdmin;

class SchoolController
{
    /**
     * Show the form for editing a school.
     */
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            die('Error: School ID is required.');
        }

        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM schools WHERE id = ?");
            $stmt->execute([$id]);
            $school = $stmt->fetch();

            if (!$school) {
                http_response_code(404);
                die('Error: School not found.');
            }

            // Load the view and pass the school data to it
            require_once __DIR__ . '/../../../views/super_admin/schools/edit.php';

        } catch (\PDOException $e) {
            die('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new school.
     */
    public function create()
    {
        // This will load the view for the create form
        require_once __DIR__ . '/../../../views/super_admin/schools/create.php';
    }

    /**
     * Update a school's details in the database.
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        // 1. Get POST data
        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $domain = trim($_POST['domain'] ?? '');
        $status = trim($_POST['status'] ?? '');

        // 2. Basic validation
        if (empty($id) || empty($name) || empty($status)) {
            die('ID, Name, and Status are required.');
        }

        // 3. Update database
        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $sql = "UPDATE schools SET name = :name, email = :email, phone = :phone, address = :address, domain = :domain, status = :status WHERE id = :id";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':email' => !empty($email) ? $email : null,
                ':phone' => !empty($phone) ? $phone : null,
                ':address' => !empty($address) ? $address : null,
                ':domain' => !empty($domain) ? $domain : null,
                ':status' => $status
            ]);

            // 4. Redirect back to the dashboard
            header('Location: /super-admin/dashboard');
            exit;

        } catch (\PDOException $e) {
            die('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Approve a pending school and set its status to active.
     */
    public function approve()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $id = $_POST['id'] ?? null;
        if (empty($id)) {
            die('ID is required.');
        }

        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $stmt = $pdo->prepare("UPDATE schools SET status = 'active' WHERE id = ?");
            $stmt->execute([$id]);

            header('Location: /super-admin/dashboard');
            exit;

        } catch (\PDOException $e) {
            die('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Delete a school from the database.
     */
    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $id = $_POST['id'] ?? null;
        if (empty($id)) {
            die('ID is required.');
        }

        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $stmt = $pdo->prepare("DELETE FROM schools WHERE id = ?");
            $stmt->execute([$id]);

            header('Location: /super-admin/dashboard');
            exit;

        } catch (\PDOException $e) {
            die('Database error: ' . $e->getMessage());
        }
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
