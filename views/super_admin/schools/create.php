<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New School</title>
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
        input[type="text"], input[type="email"], textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 80px; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #27ae60; color: #fff; border-radius: 5px; text-decoration: none; border: none; cursor: pointer; font-size: 16px; }
        .btn:hover { background-color: #229954; }
        .btn-secondary { background-color: #7f8c8d; }
        .btn-secondary:hover { background-color: #6c7a7d; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="/super-admin/dashboard" class="brand">EduFlex Super Admin</a>
        <a href="/super-admin/logout">Logout</a>
    </div>

    <div class="container">
        <h1>Create a New School</h1>

        <div class="card">
            <form action="/super-admin/schools/store" method="POST">
                <div class="form-group">
                    <label for="name">School Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Contact Email</label>
                    <input type="email" id="email" name="email">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"></textarea>
                </div>
                <div class="form-group">
                    <label for="domain">Domain</label>
                    <input type="text" id="domain" name="domain" placeholder="e.g., school.eduflex.com">
                </div>

                <button type="submit" class="btn">Save School</button>
                <a href="/super-admin/dashboard" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
