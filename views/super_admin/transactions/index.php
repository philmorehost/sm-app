<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; margin: 0; padding: 0; }
        .navbar { background-color: #fff; padding: 10px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .navbar a { text-decoration: none; color: #007bff; margin-left: 15px; }
        .navbar .brand { font-size: 1.5em; font-weight: bold; margin-left: 0; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        h1 { color: #444; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .empty-state { text-align: center; padding: 40px; }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="/super-admin/dashboard" class="brand">EduFlex Super Admin</a>
            <a href="/super-admin/dashboard">Schools</a>
            <a href="/super-admin/transactions">Transactions</a>
        </div>
        <a href="/super-admin/logout">Logout</a>
    </div>

    <div class="container">
        <h1>Transaction History</h1>

        <div class="card">
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <p>No transactions found.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Client Name</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['id']) ?></td>
                                <td><?= htmlspecialchars($order['firstname'] . ' ' . $order['lastname']) ?></td>
                                <td><?= htmlspecialchars($order['amount']) ?></td>
                                <td><?= htmlspecialchars($order['paymentmethod']) ?></td>
                                <td><?= htmlspecialchars($order['status']) ?></td>
                                <td><?= date('M j, Y', strtotime($order['date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
