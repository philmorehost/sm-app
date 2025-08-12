<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order a New School Site</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 50px auto; background: #fff; padding: 30px 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #444; }
        .form-group { margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .domain-checker { display: flex; }
        #domain { flex-grow: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px 0 0 4px; }
        #check-availability { padding: 10px 20px; background-color: #007bff; color: #fff; border-radius: 0 4px 4px 0; border: none; cursor: pointer; white-space: nowrap; }
        #check-availability:hover { background-color: #0056b3; }
        #domain-result { margin-top: 15px; padding: 10px; border-radius: 5px; text-align: center; font-weight: bold; }
        .result-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .result-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .spinner { display: none; margin: 10px auto; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 24px; height: 24px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        #step2 { display: none; margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px; }
        .btn-submit { padding: 12px 25px; background-color: #27ae60; color: #fff; border-radius: 5px; text-decoration: none; border: none; cursor: pointer; font-size: 16px; }
        .btn-submit:hover { background-color: #229954; }
    </style>
</head>
<body>
    <div class="container">
        <h1>School Ordering Process</h1>

        <?php
        session_start();
        if (isset($_SESSION['error_message'])):
        ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
        <?php
            unset($_SESSION['error_message']);
        endif;
        ?>

        <form action="/order/submit" method="POST">
            <!-- Step 1: Domain Selection -->
            <div id="step1">
                <h2>Step 1: Choose Your Domain</h2>
                <p>Enter the domain name you wish to use for your school. This will be your school's unique web address.</p>
                <div class="form-group">
                    <label for="domain">Domain Name</label>
                    <div class="domain-checker">
                        <input type="text" id="domain" name="domain_search" placeholder="e.g., myschool.com" required>
                        <button type="button" id="check-availability">Check Availability</button>
                    </div>
                    <div class="spinner" id="spinner"></div>
                    <div id="domain-result"></div>
                </div>
            </div>

            <!-- Step 2: School and Admin Details -->
            <div id="step2">
                <h2>Step 2: Your Details</h2>
                <p>Please provide the following details to set up your school and administrator account.</p>

                <input type="hidden" id="selected_domain" name="domain">

                <div class="form-group">
                    <label for="school_name">School Name</label>
                    <input type="text" id="school_name" name="school_name" required>
                </div>
                <div class="form-group">
                    <label for="firstname">Your First Name</label>
                    <input type="text" id="firstname" name="firstname" required>
                </div>
                <div class="form-group">
                    <label for="lastname">Your Last Name</label>
                    <input type="text" id="lastname" name="lastname" required>
                </div>
                <div class="form-group">
                    <label for="email">Your Email Address (This will be your login)</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Create a Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Choose a Payment Method</label>
                    <div>
                        <input type="radio" id="paystack" name="payment_method" value="paystack" checked required>
                        <label for="paystack" style="display: inline-block; font-weight: normal;">Pay with Card (Paystack)</label>
                    </div>
                    <div>
                        <input type="radio" id="banktransfer" name="payment_method" value="banktransfer" required>
                        <label for="banktransfer" style="display: inline-block; font-weight: normal;">Pay via Bank Transfer</label>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Complete Order & Proceed</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('check-availability').addEventListener('click', function() {
            const domainInput = document.getElementById('domain');
            const resultDiv = document.getElementById('domain-result');
            const spinner = document.getElementById('spinner');
            const domain = domainInput.value.trim();

            if (!domain) {
                resultDiv.innerHTML = 'Please enter a domain name.';
                resultDiv.className = 'result-error';
                return;
            }

            spinner.style.display = 'block';
            resultDiv.innerHTML = '';
            resultDiv.className = '';
            document.getElementById('step2').style.display = 'none';

            fetch('/api/domain-check', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ domain: domain })
            })
            .then(response => response.ok ? response.json() : Promise.reject('Network response was not ok'))
            .then(data => {
                spinner.style.display = 'none';
                if (data.result === 'success' && data.status === 'available') {
                    resultDiv.innerHTML = `Congratulations! <strong>${data.domain}</strong> is available. Please fill in your details below.`;
                    resultDiv.className = 'result-success';

                    // Show step 2 and populate domain
                    document.getElementById('step2').style.display = 'block';
                    document.getElementById('selected_domain').value = data.domain;

                    // Disable domain input to prevent changes
                    domainInput.readOnly = true;
                    document.getElementById('check-availability').disabled = true;

                } else if (data.result === 'success' && data.status === 'unavailable') {
                    resultDiv.innerHTML = `Sorry, <strong>${data.domain}</strong> is already taken. Please try another.`;
                    resultDiv.className = 'result-error';
                } else {
                    resultDiv.innerHTML = data.message || 'An unknown error occurred.';
                    resultDiv.className = 'result-error';
                }
            })
            .catch(error => {
                spinner.style.display = 'none';
                resultDiv.innerHTML = 'An error occurred while checking the domain. Please try again.';
                resultDiv.className = 'result-error';
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
