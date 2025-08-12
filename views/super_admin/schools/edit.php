<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit School</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; margin: 0; padding: 0; }
        .navbar { background-color: #fff; padding: 10px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .navbar a { text-decoration: none; color: #007bff; }
        .navbar .brand { font-size: 1.5em; font-weight: bold; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        h1 { color: #444; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], textarea, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 80px; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #27ae60; color: #fff; border-radius: 5px; text-decoration: none; border: none; cursor: pointer; font-size: 16px; }
        .btn:hover { background-color: #229954; }
        .btn-secondary { background-color: #7f8c8d; }
        .btn-secondary:hover { background-color: #6c7a7d; }
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
        <h1>Edit School: <?= htmlspecialchars($school['name']) ?></h1>

        <div class="card">
            <form action="/super-admin/schools/update" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($school['id']) ?>">

                <div class="form-group">
                    <label for="name">School Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($school['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Contact Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($school['email']) ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($school['phone']) ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?= htmlspecialchars($school['address']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="domain">Domain</label>
                    <input type="text" id="domain" name="domain" value="<?= htmlspecialchars($school['domain']) ?>">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active" <?= $school['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="pending" <?= $school['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="suspended" <?= $school['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>

                <button type="submit" class="btn">Update School</button>
                <a href="/super-admin/dashboard" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
