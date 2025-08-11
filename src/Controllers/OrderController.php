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
}
