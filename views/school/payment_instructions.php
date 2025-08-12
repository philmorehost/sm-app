<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Instructions</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 700px; margin: 80px auto; background: #fff; padding: 30px 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #444; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .details-box { margin-top: 20px; padding: 20px; background-color: #f9f9f9; border-left: 4px solid #007bff; }
        .details-box p { margin: 10px 0; font-size: 1.1em; }
        .details-box strong { display: inline-block; width: 150px; }
        .empty-state { text-align: center; padding: 40px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bank Transfer Details</h1>

        <?php if (empty($settings) || empty($settings['bank_name'])): ?>
            <div class="empty-state">
                <p>The school has not configured their bank transfer details yet. Please check back later or contact the school administrator.</p>
            </div>
        <?php else: ?>
            <p>Please make your payment to the following bank account. Use your invoice number or student ID as the payment reference.</p>
            <div class="details-box">
                <p><strong>Bank Name:</strong> <?= htmlspecialchars($settings['bank_name']) ?></p>
                <p><strong>Account Name:</strong> <?= htmlspecialchars($settings['account_name']) ?></p>
                <p><strong>Account Number:</strong> <?= htmlspecialchars($settings['account_number']) ?></p>
                <?php if (!empty($settings['other_details'])): ?>
                    <p><strong>Other Details:</strong></p>
                    <p><?= nl2br(htmlspecialchars($settings['other_details'])) ?></p>
                <?php endif; ?>
            </div>
            <p style="margin-top: 20px;">After making the payment, please upload your proof of payment on the invoice page.</p>
        <?php endif; ?>
    </div>
</body>
</html>
