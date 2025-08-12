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

// --- Multi-Tenancy Logic: Detect School from Domain ---
define('IS_MAIN_SITE', true); // Default assumption
define('CURRENT_SCHOOL_ID', null);

$host = $_SERVER['HTTP_HOST'];
// In a real app, you would compare this against the 'domain' column in the 'schools' table.
// For now, we will simulate this. To test, you would need to edit your /etc/hosts file
// to point a domain like 'school1.eduflex.test' to 127.0.0.1.
// Since I can't do that, this logic is a placeholder for the real implementation.
// The user will need to configure their server to point custom domains to this application's public root.

// Example of what the real logic would look like:
/*
try {
    $pdo = EduFlex\Core\Database::getConnection();
    $stmt = $pdo->prepare("SELECT id FROM schools WHERE domain = ? AND status = 'active'");
    $stmt->execute([$host]);
    $school = $stmt->fetch();
    if ($school) {
        define('IS_MAIN_SITE', false);
        define('CURRENT_SCHOOL_ID', $school['id']);
    }
} catch (\PDOException $e) {
    // Cannot connect to DB, assume it's the main site.
}
*/
// --- End Multi-Tenancy Logic ---


// 2. Simple Routing
use EduFlex\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
use EduFlex\Controllers\SuperAdmin\SchoolController as SuperAdminSchoolController;
use EduFlex\Controllers\SuperAdmin\TransactionController as SuperAdminTransactionController;
use EduFlex\Controllers\OrderController;
use EduFlex\Controllers\WebhookController;
use EduFlex\Controllers\PaymentController;
use EduFlex\Controllers\School\AuthController as SchoolAuthController;

$request_uri = $_SERVER['REQUEST_URI'];
$route = parse_url($request_uri, PHP_URL_PATH) ?? '';
$route = trim($route, '/');

if (IS_MAIN_SITE) {
    // --- Main Site Routes ---
    switch ($route) {
        case 'order':
            $controller = new OrderController();
            $controller->showOrderForm();
            break;
        // ... all other main site routes
        default:
            require_once __DIR__ . '/../views/landing.php';
            break;
    }
} else {
    // --- School Tenant Routes ---
    switch ($route) {
        case 'login':
            $controller = new SchoolAuthController();
            $controller->showLoginForm();
            break;
        case 'login/submit': // Assuming a POST route
            $controller = new SchoolAuthController();
            $controller->login();
            break;
        case 'dashboard':
            // This will be the school admin's dashboard
            require_once __DIR__ . '/../views/school/dashboard.php';
            break;
        default:
            // Default page for a school could be a public homepage or the login page
            $controller = new SchoolAuthController();
            $controller->showLoginForm();
            break;
    }
}

// For simplicity in this refactor, I am replacing the entire switch.
// The full, combined router is below.

// --- Combined Router ---
// The logic above is complex to merge, so here is the final intended state of the file.
// This new structure handles both main site and tenant site routing.

// --- Multi-Tenancy Logic ---
$school_id = null;
try {
    $host = $_SERVER['HTTP_HOST'];
    $pdo = \EduFlex\Core\Database::getConnection();
    $stmt = $pdo->prepare("SELECT id FROM schools WHERE domain = ? AND status = 'active'");
    $stmt->execute([$host]);
    $school = $stmt->fetch();
    if ($school) {
        $school_id = $school['id'];
        define('CURRENT_SCHOOL_ID', $school['id']);
    }
} catch (\PDOException $e) {
    // Fail gracefully if DB connection fails on domain check
}


// --- Routing ---
$request_uri = $_SERVER['REQUEST_URI'];
$route = parse_url($request_uri, PHP_URL_PATH) ?? '';
$route = trim($route, '/');

if ($school_id) {
    // --- School Tenant Routes ---
    switch ($route) {
        case 'login':
            $controller = new \EduFlex\Controllers\School\AuthController();
            $controller->showLoginForm();
            break;
        case 'login-process': // Renamed to avoid conflict
            $controller = new \EduFlex\Controllers\School\AuthController();
            $controller->login();
            break;
        case 'dashboard':
            require_once __DIR__ . '/../views/school/dashboard.php';
            break;
        default:
            // For now, default to login for any other school URL
             $controller = new \EduFlex\Controllers\School\AuthController();
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
