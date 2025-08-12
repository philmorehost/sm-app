<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Settings</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; margin: 0; padding: 0; }
        .navbar { background-color: #fff; padding: 10px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .navbar a { text-decoration: none; color: #007bff; margin-left: 15px; }
        .navbar .brand { font-size: 1.5em; font-weight: bold; margin-left: 0; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        h1 { color: #444; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 100px; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #27ae60; color: #fff; border-radius: 5px; text-decoration: none; border: none; cursor: pointer; font-size: 16px; }
        .btn:hover { background-color: #229954; }
        .success-message { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="/dashboard" class="brand">School Admin Panel</a>
            <a href="/dashboard">Dashboard</a>
            <a href="/payment-settings">Payment Settings</a>
        </div>
        <a href="/logout">Logout</a>
    </div>

    <div class="container">
        <h1>Payment Settings</h1>
        <p>Enter your bank account details here. These details will be shown to students/parents for manual payments.</p>

        <div class="card">
            <?php if (isset($_GET['success'])): ?>
                <p class="success-message">Settings saved successfully!</p>
            <?php endif; ?>

            <form action="/payment-settings/update" method="POST">
                <div class="form-group">
                    <label for="bank_name">Bank Name</label>
                    <input type="text" id="bank_name" name="bank_name" value="<?= htmlspecialchars($settings['bank_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="account_name">Account Name</label>
                    <input type="text" id="account_name" name="account_name" value="<?= htmlspecialchars($settings['account_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="account_number">Account Number</label>
                    <input type="text" id="account_number" name="account_number" value="<?= htmlspecialchars($settings['account_number'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="other_details">Other Details (e.g., SWIFT Code, Branch Info)</label>
                    <textarea id="other_details" name="other_details"><?= htmlspecialchars($settings['other_details'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn">Save Settings</button>
            </form>
        </div>
    </div>
</body>
</html>
