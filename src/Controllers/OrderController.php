<?php

namespace EduFlex\Controllers;

class OrderController
{
    public function showOrderForm()
    {
        // This will render the first step of the order form
        require_once __DIR__ . '/../../views/order/step1.php';
    }

    public function checkDomain()
    {
        header('Content-Type: application/json');

        // Get the posted JSON
        $json_str = file_get_contents('php://input');
        $json_obj = json_decode($json_str);

        $domain = trim($json_obj->domain ?? '');

        if (empty($domain)) {
            echo json_encode(['result' => 'error', 'message' => 'Domain name is required.']);
            return;
        }

        // Basic domain validation
        if (!preg_match('/^([a-zA-Z0-9][a-zA-Z0-9-]*\.)+[a-zA-Z]{2,}$/', $domain)) {
            echo json_encode(['result' => 'error', 'message' => 'Invalid domain name format.']);
            return;
        }

        $whmcsApi = new \EduFlex\Core\WhmcsApi();
        $result = $whmcsApi->domainCheck($domain);

        // Also pass back the domain that was checked, so the JS can display it.
        $result['domain'] = $domain;

        // The WHMCS DomainWhois API returns a 'status' of 'available' or 'unavailable'.
        // We will pass this through.
        echo json_encode($result);
    }

    public function processOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        // --- 1. Collect and Validate Data ---
        $domain = trim($_POST['domain'] ?? '');
        $school_name = trim($_POST['school_name'] ?? '');
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Basic validation
        if (empty($domain) || empty($school_name) || empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
            // In a real app, redirect back with errors
            die('All fields are required.');
        }

        $whmcsApi = new \EduFlex\Core\WhmcsApi();
        $pdo = \EduFlex\Core\Database::getConnection();

        // --- 2. Create Pending School Record ---
        try {
            $stmt = $pdo->prepare("INSERT INTO schools (name, email, domain, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$school_name, $email, $domain, 'pending']);
            $school_id = $pdo->lastInsertId();
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') { // Integrity constraint violation (duplicate entry)
                session_start();
                $_SESSION['error_message'] = 'A school with this domain name or email address already exists in our system.';
                header('Location: /order');
                exit;
            }
            // For any other database error, show the detailed message for debugging.
            die("Error creating local school record: " . $e->getMessage());
        }

        // --- 3. Create WHMCS Client ---
        $clientData = [
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'email'     => $email,
            'password2' => $password,
            'country'   => 'US', // Placeholder, should be collected in form
            'phonenumber' => '1234567890', // Placeholder
            'address1'  => '123 Main St', // Placeholder
            'city'      => 'Anytown', // Placeholder
            'state'     => 'CA', // Placeholder
            'postcode'  => '90210', // Placeholder
        ];
        $clientResult = $whmcsApi->addClient($clientData);

        if ($clientResult['result'] !== 'success' || empty($clientResult['clientid'])) {
            session_start();
            // Check for the specific duplicate email error message from WHMCS
            if (strpos($clientResult['message'], 'A user already exists with that email address') !== false) {
                $_SESSION['error_message'] = 'A user account with this email address already exists. Please use a different email.';
            } else {
                $_SESSION['error_message'] = "Could not create your account: " . ($clientResult['message'] ?? 'An unknown error occurred.');
            }
            header('Location: /order');
            exit;
        }
        $clientId = $clientResult['clientid'];

        // --- 4. Update local record with client ID ---
        $stmt = $pdo->prepare("UPDATE schools SET whmcs_client_id = ? WHERE id = ?");
        $stmt->execute([$clientId, $school_id]);

        // --- 5. Create WHMCS Order ---
        $orderData = [
            'clientid' => $clientId,
            'pid' => [1], // Placeholder Product ID for the school plan
            'domain' => [$domain],
            'domaintype' => ['register'],
            'regperiod' => [1], // 1 year
            'paymentmethod' => 'banktransfer', // Set to a method supported by the user's WHMCS
        ];
        $orderResult = $whmcsApi->addOrder($orderData);

        if ($orderResult['result'] !== 'success' || empty($orderResult['orderid'])) {
            die("Could not create order in WHMCS: " . ($orderResult['message'] ?? 'Unknown error'));
        }
        $orderId = $orderResult['orderid'];
        $invoiceId = $orderResult['invoiceid'];

        // --- 6. Update local record with order and invoice ID ---
        $stmt = $pdo->prepare("UPDATE schools SET whmcs_order_id = ?, whmcs_invoice_id = ? WHERE id = ?");
        $stmt->execute([$orderId, $invoiceId, $school_id]);

        // --- 7. Create SSO Token and Redirect to Invoice ---
        $destination = 'clientarea.php?action=viewinvoice&id=' . $invoiceId;
        $ssoResult = $whmcsApi->createSsoToken($clientId, $destination);

        if ($ssoResult['result'] !== 'success' || empty($ssoResult['redirect_url'])) {
            // If SSO fails, fall back to the direct invoice URL. The user will have to log in manually.
            $whmcsConfig = require __DIR__ . '/../../config/whmcs.php';
            $whmcsBaseUrl = rtrim(dirname($whmcsConfig['url']), '/includes');
            $invoiceUrl = $whmcsBaseUrl . '/viewinvoice.php?id=' . $invoiceId;
            header('Location: /order/success?invoice_url=' . urlencode($invoiceUrl));
            exit;
        }

        $ssoUrl = $ssoResult['redirect_url'];
        header('Location: /order/success?invoice_url=' . urlencode($ssoUrl));
        exit;
    }

    public function showSuccess()
    {
        $invoice_url = $_GET['invoice_url'] ?? '/';
        require_once __DIR__ . '/../../views/order/success.php';
    }
}
