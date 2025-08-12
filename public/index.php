<?php

// --- DEBUGGING: TEMPORARILY ENABLE ERROR REPORTING ---
// This should be removed in a production environment.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- END DEBUGGING ---

// Front Controller

// 1. Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Import all necessary controllers
use EduFlex\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
use EduFlex\Controllers\SuperAdmin\SchoolController as SuperAdminSchoolController;
use EduFlex\Controllers\SuperAdmin\TransactionController as SuperAdminTransactionController;
use EduFlex\Controllers\OrderController;
use EduFlex\Controllers\WebhookController;
use EduFlex\Controllers\PaymentController;
use EduFlex\Controllers\School\AuthController as SchoolAuthController;
use EduFlex\Controllers\School\PaymentSettingsController as SchoolPaymentSettingsController;


// --- 3. Multi-Tenancy Logic ---
$school_id = null;
try {
    $host = $_SERVER['HTTP_HOST'];
    $pdo = \EduFlex\Core\Database::getConnection();
    $stmt = $pdo->prepare("SELECT id FROM schools WHERE domain = ? AND status = 'active'");
    $stmt->execute([$host]);
    $school = $stmt->fetch();
    if ($school) {
        $school_id = $school['id'];
        // Define a global constant for the school ID so it can be accessed in controllers
        define('CURRENT_SCHOOL_ID', $school['id']);
    }
} catch (\PDOException $e) {
    // Fail gracefully if DB connection fails during the domain check.
    // The site will be treated as the main site.
}


// --- 4. Routing ---
$request_uri = $_SERVER['REQUEST_URI'];
$route = parse_url($request_uri, PHP_URL_PATH) ?? '';
$route = trim($route, '/');

if ($school_id) {
    // --- School Tenant Routes ---
    switch ($route) {
        case 'login':
            $controller = new SchoolAuthController();
            $controller->showLoginForm();
            break;
        case 'login-process':
            $controller = new SchoolAuthController();
            $controller->login();
            break;
        case 'dashboard':
            // This will be the school admin's dashboard
            require_once __DIR__ . '/../views/school/dashboard.php';
            break;
        case 'payment-settings':
            $controller = new SchoolPaymentSettingsController();
            $controller->index();
            break;
        case 'payment-settings/update':
            $controller = new SchoolPaymentSettingsController();
            $controller->update();
            break;
        case 'payment-instructions':
            $controller = new SchoolPaymentSettingsController();
            $controller->showPaymentInstructions();
            break;
        default:
            // For now, default to login for any other school URL
             $controller = new SchoolAuthController();
            $controller->showLoginForm();
            break;
    }
} else {
    // --- Main Site Routes ---
    switch ($route) {
        case 'order':
            $controller = new OrderController();
            $controller->showOrderForm();
            break;
        case 'order/submit':
            $controller = new OrderController();
            $controller->processOrder();
            break;
        case 'order/success':
            $controller = new OrderController();
            $controller->showSuccess();
            break;
        case 'order/thank-you':
            require_once __DIR__ . '/../views/order/thank-you.php';
            break;
        case 'payment/paystack':
            $controller = new PaymentController();
            $controller->showPaystackForm();
            break;
        case 'api/domain-check':
            $controller = new OrderController();
            $controller->checkDomain();
            break;
        case 'webhooks/whmcs':
            $controller = new WebhookController();
            $controller->handleWhmcs();
            break;
        case 'webhooks/paystack':
            $controller = new WebhookController();
            $controller->handlePaystack();
            break;
        case 'super-admin/login':
            $controller = new SuperAdminAuthController();
            $controller->showLoginForm();
            break;
        case 'super-admin/login/submit':
            $controller = new SuperAdminAuthController();
            $controller->login();
            break;
        case 'super-admin/dashboard':
            $controller = new SuperAdminAuthController();
            $controller->dashboard();
            break;
        case 'super-admin/transactions':
            $controller = new SuperAdminTransactionController();
            $controller->index();
            break;
        case 'super-admin/schools/create':
            $controller = new SuperAdminSchoolController();
            $controller->create();
            break;
        case 'super-admin/schools/store':
            $controller = new SuperAdminSchoolController();
            $controller->store();
            break;
        case 'super-admin/schools/edit':
            $controller = new SuperAdminSchoolController();
            $controller->edit();
            break;
        case 'super-admin/schools/update':
            $controller = new SuperAdminSchoolController();
            $controller->update();
            break;
        case 'super-admin/schools/delete':
            $controller = new SuperAdminSchoolController();
            $controller->destroy();
            break;
        case 'super-admin/schools/approve':
            $controller = new SuperAdminSchoolController();
            $controller->approve();
            break;
        case 'super-admin/logout':
            $controller = new SuperAdminAuthController();
            $controller->logout();
            break;
        case '':
        case 'home':
            require_once __DIR__ . '/../views/landing.php';
            break;
        default:
            http_response_code(404);
            echo "404 Not Found";
            break;
    }
}
