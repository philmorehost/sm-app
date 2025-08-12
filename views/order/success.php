<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Redirect to the invoice page after 5 seconds -->
    <meta http-equiv="refresh" content="5;url=<?= htmlspecialchars($invoice_url) ?>">
    <title>Order Received</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: 80px auto; background: #fff; padding: 30px 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #27ae60; }
        p { font-size: 1.1em; }
        .spinner { margin: 20px auto; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        a { color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Received!</h1>
        <p>Thank you for your order. Your account and order have been created successfully.</p>
        <p>You will now be redirected to your invoice to complete the payment.</p>
        <div class="spinner"></div>
        <p><small>If you are not redirected automatically in 5 seconds, <a href="<?= htmlspecialchars($invoice_url) ?>">click here</a>.</small></p>
    </div>
</body>
</html>
