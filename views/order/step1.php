<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order a New School Site - Step 1</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 50px auto; background: #fff; padding: 30px 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #444; }
        .form-group { margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        .domain-checker { display: flex; }
        #domain { flex-grow: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px 0 0 4px; }
        #check-availability { padding: 10px 20px; background-color: #007bff; color: #fff; border-radius: 0 4px 4px 0; border: none; cursor: pointer; }
        #check-availability:hover { background-color: #0056b3; }
        #domain-result { margin-top: 15px; padding: 10px; border-radius: 5px; text-align: center; font-weight: bold; }
        .result-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .result-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .spinner { display: none; margin: 10px auto; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 24px; height: 24px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="container">
        <h1>School Ordering Process</h1>
        <h2>Step 1: Choose Your Domain</h2>
        <p>Enter the domain name you wish to use for your school. This will be your school's unique web address.</p>
        <div class="form-group">
            <label for="domain">Domain Name</label>
            <div class="domain-checker">
                <input type="text" id="domain" name="domain" placeholder="e.g., myschool.com" required>
                <button id="check-availability">Check Availability</button>
            </div>
            <div class="spinner" id="spinner"></div>
            <div id="domain-result"></div>
        </div>
        <!-- Next steps of the form will go here -->
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

            // Show spinner and clear previous results
            spinner.style.display = 'block';
            resultDiv.innerHTML = '';
            resultDiv.className = '';

            fetch('/api/domain-check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ domain: domain })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                spinner.style.display = 'none';
                if (data.result === 'success' && data.status === 'available') {
                    resultDiv.innerHTML = `Congratulations! <strong>${data.domain}</strong> is available.`;
                    resultDiv.className = 'result-success';
                } else if (data.result === 'success' && data.status === 'unavailable') {
                    resultDiv.innerHTML = `Sorry, <strong>${data.domain}</strong> is already taken.`;
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
