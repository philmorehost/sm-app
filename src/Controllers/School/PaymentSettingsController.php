<?php

namespace EduFlex\Controllers\School;

class PaymentSettingsController
{
    /**
     * Show the payment settings form for the school.
     */
    public function index()
    {
        if (!defined('CURRENT_SCHOOL_ID')) {
            die('No school context found.');
        }

        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM payment_settings WHERE school_id = ?");
            $stmt->execute([CURRENT_SCHOOL_ID]);
            $settings = $stmt->fetch() ?: []; // Default to empty array if no settings exist

            require_once __DIR__ . '/../../../views/school/payment_settings.php';

        } catch (\PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    /**
     * Update the school's payment settings.
     */
    public function update()
    {
        if (!defined('CURRENT_SCHOOL_ID')) {
            die('No school context found.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $bank_name = trim($_POST['bank_name'] ?? '');
        $account_name = trim($_POST['account_name'] ?? '');
        $account_number = trim($_POST['account_number'] ?? '');
        $other_details = trim($_POST['other_details'] ?? '');

        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            // Use INSERT...ON DUPLICATE KEY UPDATE to handle both creation and updates
            $sql = "INSERT INTO payment_settings (school_id, bank_name, account_name, account_number, other_details)
                    VALUES (:school_id, :bank_name, :account_name, :account_number, :other_details)
                    ON DUPLICATE KEY UPDATE
                    bank_name = VALUES(bank_name),
                    account_name = VALUES(account_name),
                    account_number = VALUES(account_number),
                    other_details = VALUES(other_details)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':school_id' => CURRENT_SCHOOL_ID,
                ':bank_name' => $bank_name,
                ':account_name' => $account_name,
                ':account_number' => $account_number,
                ':other_details' => $other_details,
            ]);

            header('Location: /payment-settings?success=1');
            exit;

        } catch (\PDOException $e) {
            die('Database error: ' . $e->getMessage());
        }
    }

    /**
     * A placeholder to show how bank details would be displayed to a user.
     */
    public function showPaymentInstructions()
    {
        if (!defined('CURRENT_SCHOOL_ID')) {
            die('No school context found.');
        }

        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM payment_settings WHERE school_id = ?");
            $stmt->execute([CURRENT_SCHOOL_ID]);
            $settings = $stmt->fetch() ?: [];

            require_once __DIR__ . '/../../../views/school/payment_instructions.php';

        } catch (\PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }
}
