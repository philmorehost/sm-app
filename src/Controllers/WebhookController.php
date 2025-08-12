<?php

namespace EduFlex\Controllers;

class WebhookController
{
    /**
     * Handle incoming webhooks from WHMCS.
     */
    public function handleWhmcs()
    {
        // --- 1. Get the raw POST data and headers ---
        $payload = file_get_contents('php://input');
        $headers = getallheaders();

        // --- 2. Log the request for debugging ---
        // In a real app, use a proper logger like Monolog.
        $log_entry = "--- WHMCS Webhook Received ---\n";
        $log_entry .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
        $log_entry .= "Headers: " . json_encode($headers, JSON_PRETTY_PRINT) . "\n";
        $log_entry .= "Payload: " . $payload . "\n\n";
        file_put_contents(__DIR__ . '/../../webhook.log', $log_entry, FILE_APPEND);

        // --- 3. Verify the signature ---
        // This is a critical security step.
        $whmcsConfig = require __DIR__ . '/../../config/whmcs.php';
        $webhookSecret = $whmcsConfig['webhook_secret'] ?? '';
        $signature = $headers['X-Whmcs-Signature'] ?? '';
        if (empty($webhookSecret) || !hash_equals(hash_hmac('sha256', $payload, $webhookSecret), $signature)) {
            http_response_code(401);
            file_put_contents(__DIR__ . '/../../webhook.log', "Signature verification failed.\n", FILE_APPEND);
            echo "Signature verification failed.";
            exit;
        }

        $data = json_decode($payload, true);

        // --- 4. Check for the 'InvoicePaid' event ---
        if (isset($data['event']) && $data['event'] === 'InvoicePaid') {
            $this->handleInvoicePaid($data['attributes']['data']);
        }

        // --- 5. Acknowledge receipt ---
        http_response_code(200);
        echo "Webhook received.";
        exit;
    }

    /**
     * Process the payload for an InvoicePaid event.
     * @param array $data The data associated with the event.
     */
    private function handleInvoicePaid(array $data)
    {
        $invoiceId = $data['invoiceid'] ?? null;
        $userId = $data['userid'] ?? null;

        if (!$invoiceId || !$userId) {
            return; // Not enough data
        }

        try {
            $pdo = \EduFlex\Core\Database::getConnection();
            $stmt = $pdo->prepare("SELECT id FROM schools WHERE whmcs_client_id = ? AND status = 'pending'");
            $stmt->execute([$userId]);
            $school = $stmt->fetch();

            if ($school) {
                $updateStmt = $pdo->prepare("UPDATE schools SET status = 'active' WHERE id = ?");
                $updateStmt->execute([$school['id']]);
                file_put_contents(__DIR__ . '/../../webhook.log', "WHMCS: Activated school ID: {$school['id']}\n", FILE_APPEND);
            }
        } catch (\PDOException $e) {
            file_put_contents(__DIR__ . '/../../webhook.log', "WHMCS DB Error: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }

    /**
     * Handle incoming webhooks from Paystack.
     */
    public function handlePaystack()
    {
        $payload = file_get_contents('php://input');

        // Verify the webhook signature
        $paystackConfig = require __DIR__ . '/../../config/paystack.php';
        if (($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '') !== hash_hmac('sha512', $payload, $paystackConfig['secret_key'])) {
            http_response_code(401);
            echo "Signature verification failed.";
            exit;
        }

        $data = json_decode($payload, true);

        // Log the request
        $log_entry = "--- Paystack Webhook Received ---\nPayload: " . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
        file_put_contents(__DIR__ . '/../../webhook.log', $log_entry, FILE_APPEND);

        if (isset($data['event']) && $data['event'] === 'charge.success') {
            $reference = $data['data']['reference'] ?? null;
            if ($reference) {
                // For security, verify the transaction again with the API
                $paystackApi = new \EduFlex\Core\PaystackApi();
                $verification = $paystackApi->verifyTransaction($reference);

                if (($verification['data']['status'] ?? '') === 'success') {
                    // Extract school ID from reference: 'eduflex-{school_id}-{timestamp}'
                    $parts = explode('-', $reference);
                    if (count($parts) === 3 && $parts[0] === 'eduflex') {
                        $school_id = (int)$parts[1];

                        // Activate the school
                        try {
                            $pdo = \EduFlex\Core\Database::getConnection();
                            $stmt = $pdo->prepare("UPDATE schools SET status = 'active' WHERE id = ? AND status = 'pending'");
                            $stmt->execute([$school_id]);
                            file_put_contents(__DIR__ . '/../../webhook.log', "Paystack: Activated school ID: {$school_id}\n", FILE_APPEND);
                        } catch (\PDOException $e) {
                            file_put_contents(__DIR__ . '/../../webhook.log', "Paystack DB Error: " . $e->getMessage() . "\n", FILE_APPEND);
                        }
                    }
                }
            }
        }

        http_response_code(200);
        echo "Webhook received.";
        exit;
    }
}
