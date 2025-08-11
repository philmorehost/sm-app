<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; margin: 0; padding: 0; }
        .navbar { background-color: #fff; padding: 10px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .navbar a { text-decoration: none; color: #007bff; }
        .navbar .brand { font-size: 1.5em; font-weight: bold; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        .header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h1 { color: #444; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #27ae60; color: #fff; border-radius: 5px; text-decoration: none; border: none; cursor: pointer; }
        .btn:hover { background-color: #229954; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .empty-state { text-align: center; padding: 40px; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="/super-admin/dashboard" class="brand">EduFlex Super Admin</a>
        <a href="/super-admin/logout">Logout</a>
    </div>

    <div class="container">
        <div class="header-row">
            <h1>School Management</h1>
            <a href="/super-admin/schools/create" class="btn">Create New School</a>
        </div>

        <div class="card">
            <?php if (empty($schools)): ?>
                <div class="empty-state">
                    <p>No schools have been created yet.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Domain</th>
                            <th>Date Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schools as $school): ?>
                            <tr>
                                <td><?= htmlspecialchars($school['name']) ?></td>
                                <td><?= htmlspecialchars($school['email']) ?></td>
                                <td><?= htmlspecialchars($school['status']) ?></td>
                                <td><?= htmlspecialchars($school['domain']) ?></td>
                                <td><?= date('M j, Y', strtotime($school['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
