<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to EduFlex</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #fff; margin: 0; padding: 0; }
        .hero { background-color: #f4f7fc; text-align: center; padding: 100px 20px; }
        .hero h1 { font-size: 3em; color: #333; }
        .hero p { font-size: 1.2em; color: #555; max-width: 600px; margin: 20px auto; }
        .btn { display: inline-block; padding: 15px 30px; background-color: #007bff; color: #fff; border-radius: 5px; text-decoration: none; font-size: 1.1em; }
        .btn:hover { background-color: #0056b3; }
        .footer { text-align: center; padding: 20px; margin-top: 50px; background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Welcome to EduFlex</h1>
        <p>The all-in-one SAAS School Management System to streamline your institution's operations.</p>
        <a href="/order" class="btn">Get Started Now</a>
    </div>

    <div class="footer">
        <p>&copy; <?= date('Y') ?> EduFlex. All rights reserved.</p>
    </div>
</body>
</html>
