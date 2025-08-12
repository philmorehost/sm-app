<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Dashboard</title>
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
        <div>
            <a href="/dashboard" class="brand">School Admin Panel</a>
            <a href="/dashboard">Dashboard</a>
            <a href="/payment-settings">Payment Settings</a>
        </div>
        <a href="/logout">Logout</a>
    </div>

    <div class="container">
        <div class="welcome-box">
            <h1>Welcome, School Administrator!</h1>
            <p>This is your school's dashboard. You will be able to manage your school's settings, users, and finances from here.</p>
            <p>The next step is to add a "Payment Settings" page where you can configure your bank transfer details.</p>
        </div>
    </div>
</body>
</html>
