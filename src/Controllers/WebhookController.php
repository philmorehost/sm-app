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
            // Not enough data to proceed
            return;
        }

        try {
            $pdo = \EduFlex\Core\Database::getConnection();

            // We need to find the order associated with this invoice to get the school ID.
            // The webhook doesn't give us our internal school ID.
            // We'll find the school via the WHMCS client ID.
            $stmt = $pdo->prepare("SELECT id FROM schools WHERE whmcs_client_id = ? AND status = 'pending'");
            $stmt->execute([$userId]);
            $school = $stmt->fetch();

            if ($school) {
                // Found a matching pending school, let's activate it.
                $updateStmt = $pdo->prepare("UPDATE schools SET status = 'active' WHERE id = ?");
                $updateStmt->execute([$school['id']]);

                // Log success
                file_put_contents(__DIR__ . '/../../webhook.log', "Activated school ID: {$school['id']}\n", FILE_APPEND);
            } else {
                // Log that no matching school was found
                file_put_contents(__DIR__ . '/../../webhook.log', "No pending school found for WHMCS client ID: {$userId}\n", FILE_APPEND);
            }

        } catch (\PDOException $e) {
            // Log the database error
            file_put_contents(__DIR__ . '/../../webhook.log', "Database error during webhook processing: " . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
}
