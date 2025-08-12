<?php

namespace EduFlex\Controllers\SuperAdmin;

class TransactionController
{
    /**
     * Display a list of transactions/orders from WHMCS.
     */
    public function index()
    {
        $whmcsApi = new \EduFlex\Core\WhmcsApi();
        $result = $whmcsApi->getOrders();

        $orders = [];
        if (isset($result['orders']['order'])) {
            $orders = $result['orders']['order'];
        }

        // Load the view and pass the orders data to it
        require_once __DIR__ . '/../../../views/super_admin/transactions/index.php';
    }
}
