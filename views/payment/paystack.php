<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Payment</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: 80px auto; background: #fff; padding: 30px 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #444; }
        .summary { text-align: left; margin: 30px 0; padding: 20px; border: 1px solid #eee; border-radius: 5px; }
        .summary p { margin: 5px 0; }
        .btn-pay { display: inline-block; padding: 15px 35px; background-color: #27ae60; color: #fff; border-radius: 5px; text-decoration: none; border: none; cursor: pointer; font-size: 18px; }
        .btn-pay:hover { background-color: #229954; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Complete Your Payment</h1>
        <p>Please review your order details below and click the button to pay securely with Paystack.</p>

        <div class="summary">
            <p><strong>School Name:</strong> <?= htmlspecialchars($school['name']) ?></p>
            <p><strong>Domain:</strong> <?= htmlspecialchars($school['domain']) ?></p>
            <p><strong>Contact Email:</strong> <?= htmlspecialchars($school['email']) ?></p>
            <p><strong>Amount:</strong> NGN <?= htmlspecialchars(number_format($amount_in_kobo / 100, 2)) ?></p>
        </div>

        <button type="button" onclick="payWithPaystack()" class="btn-pay">Pay Now</button>
    </div>

    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        function payWithPaystack() {
            const handler = PaystackPop.setup({
                key: '<?= htmlspecialchars($paystack_public_key) ?>',
                email: '<?= htmlspecialchars($school['email']) ?>',
                amount: <?= (int)$amount_in_kobo ?>,
                ref: '<?= htmlspecialchars($reference) ?>',
                // The callback is where you verify the transaction on your server.
                // Paystack will redirect to this URL with the reference.
                // We will handle the verification via a separate webhook for reliability.
                onClose: function() {
                    // User closed the popup
                    alert('Transaction was not completed.');
                },
                callback: function(response) {
                    // This callback is called after a successful transaction.
                    // We will redirect to a thank you page and verify via webhook.
                    window.location = '/order/thank-you'; // A generic thank you page
                }
            });
            handler.openIframe();
        }
    </script>
</body>
</html>
