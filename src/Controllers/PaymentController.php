<?php

namespace EduFlex\Controllers;

class PaymentController
{
    /**
     * Show the Paystack payment page for a specific order.
     */
    public function showPaystackForm()
    {
        $school_id = $_GET['school_id'] ?? null;
        if (!$school_id) {
            die('Invalid Order.');
        }

        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM schools WHERE id = ?");
            $stmt->execute([$school_id]);
            $school = $stmt->fetch();

            if (!$school) {
                die('School record not found.');
            }

            // For now, use a fixed price. This should come from a plan later.
            // Paystack amount is in the lowest currency unit (kobo for NGN).
            $amount_in_kobo = 50000 * 100; // 50,000 NGN

            $paystack_config = require __DIR__ . '/../../config/paystack.php';
            $paystack_public_key = $paystack_config['public_key'];

            // A unique reference for this transaction
            $reference = 'eduflex-' . $school_id . '-' . time();

            require_once __DIR__ . '/../../views/payment/paystack.php';

        } catch (\PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }
}
