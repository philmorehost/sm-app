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
        .welcome-box { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="/super-admin/dashboard" class="brand">EduFlex Super Admin</a>
        <a href="/super-admin/logout">Logout</a>
    </div>

    <div class="container">
        <div class="welcome-box">
            <h1>Welcome!</h1>
            <p>You have successfully logged in to the Super Admin Dashboard.</p>
            <p>From here, you will be able to manage schools, subscriptions, and system settings.</p>
        </div>
    </div>
</body>
</html>
