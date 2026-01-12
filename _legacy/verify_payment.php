<?php
require_once 'config.php';

// Get order ID from URL
$order_id = $_GET['order_id'] ?? null;
$payment_found = false;
$payment_data = null;

if ($order_id) {
    // Try to find payment in our records
    $payment_file = __DIR__ . '/payments.json';
    
    if (file_exists($payment_file)) {
        $payments = json_decode(file_get_contents($payment_file), true) ?: [];
        
        foreach ($payments as $payment) {
            if ($payment['order_id'] === $order_id) {
                $payment_found = true;
                $payment_data = $payment;
                break;
            }
        }
    }
}

// Map status codes to readable text
$status_map = [
    '2' => ['text' => 'Success', 'color' => '#10b981', 'bg' => '#f0fdf4', 'icon' => '✓'],
    '0' => ['text' => 'Pending', 'color' => '#f59e0b', 'bg' => '#fffbeb', 'icon' => '⏱'],
    '-1' => ['text' => 'Cancelled', 'color' => '#6b7c93', 'bg' => '#f6f9fc', 'icon' => '⚠'],
    '-2' => ['text' => 'Failed', 'color' => '#ef4444', 'bg' => '#fef2f2', 'icon' => '✕'],
    '-3' => ['text' => 'Chargedback', 'color' => '#ef4444', 'bg' => '#fef2f2', 'icon' => '↩'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Payment - DigiMart Solutions</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f6f9fc;
            min-height: 100vh;
            padding: 40px 20px;
            color: #1a1f36;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 580px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            color: #0a2540;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 15px;
            color: #6b7c93;
        }

        .card {
            background: white;
            border: 1px solid #e3e8ee;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            padding: 28px 32px 24px;
            border-bottom: 1px solid #e3e8ee;
        }

        .card-title {
            font-size: 19px;
            font-weight: 600;
            color: #0a2540;
            margin-bottom: 6px;
        }

        .card-description {
            font-size: 14px;
            color: #6b7c93;
        }

        .form-container {
            padding: 28px 32px 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0a2540;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #d1d9e0;
            border-radius: 6px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.15s ease;
            background: #ffffff;
            color: #0a2540;
        }

        input:hover {
            border-color: #a3acb9;
        }

        input:focus {
            outline: none;
            border-color: #635bff;
            box-shadow: 0 0 0 3px rgba(99, 91, 255, 0.08);
        }

        .btn {
            width: 100%;
            padding: 13px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: #0a2540;
            color: white;
        }

        .btn-primary:hover {
            background: #1a3a5f;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: white;
            color: #0a2540;
            border: 1px solid #e3e8ee;
            margin-top: 12px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-secondary:hover {
            background: #f6f9fc;
        }

        .result-section {
            padding: 28px 32px 32px;
        }

        .status-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .status-icon {
            font-size: 24px;
            line-height: 1;
        }

        .status-info h3 {
            font-size: 17px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .status-info p {
            font-size: 13px;
            opacity: 0.9;
        }

        .details-grid {
            display: grid;
            gap: 1px;
            background: #e3e8ee;
            border-radius: 6px;
            overflow: hidden;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 14px 16px;
            font-size: 14px;
            background: white;
        }

        .detail-label {
            color: #6b7c93;
            font-weight: 500;
        }

        .detail-value {
            color: #0a2540;
            font-weight: 600;
            text-align: right;
        }

        .not-found {
            padding: 40px 32px;
            text-align: center;
        }

        .not-found-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            opacity: 0.3;
        }

        .not-found h3 {
            font-size: 18px;
            color: #0a2540;
            margin-bottom: 8px;
        }

        .not-found p {
            color: #6b7c93;
            font-size: 14px;
            line-height: 1.6;
        }

        .actions {
            padding: 24px 32px 32px;
            background: #fafbfc;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 32px;
            font-size: 13px;
            color: #6b7c93;
        }

        .footer-link {
            color: #635bff;
            text-decoration: none;
            margin: 0 8px;
        }

        @media (max-width: 640px) {
            body {
                padding: 20px 16px;
            }

            .card-header,
            .form-container,
            .result-section {
                padding: 24px 20px;
            }

            .actions {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">DigiMart Solutions</div>
            <div class="subtitle">Payment Verification</div>
        </div>

        <?php if (!$order_id): ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Verify Payment</div>
                    <div class="card-description">Enter your Order ID to check payment status</div>
                </div>

                <form method="GET" class="form-container">
                    <div class="form-group">
                        <label for="order_id">Order ID</label>
                        <input type="text" id="order_id" name="order_id" placeholder="DM17664097116539" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary">Check Status</button>
                </form>
            </div>

            <div class="footer">
                <a href="index.php" class="footer-link">New Payment</a>
                <span style="color: #cbd5e0;">•</span>
                <a href="https://wa.me/<?php echo str_replace('+', '', BUSINESS_PHONE); ?>" class="footer-link" target="_blank">Support</a>
            </div>

        <?php elseif ($payment_found && $payment_data): ?>
            <?php 
            $status_code = $payment_data['status_code'];
            $status_info = $status_map[$status_code] ?? ['text' => 'Unknown', 'color' => '#6b7c93', 'bg' => '#f6f9fc', 'icon' => '?'];
            ?>
            
            <div class="card">
                <div class="result-section">
                    <div class="status-header" style="background: <?php echo $status_info['bg']; ?>; color: <?php echo $status_info['color']; ?>;">
                        <div class="status-icon"><?php echo $status_info['icon']; ?></div>
                        <div class="status-info">
                            <h3 style="color: <?php echo $status_info['color']; ?>;"><?php echo $status_info['text']; ?></h3>
                            <p style="color: <?php echo $status_info['color']; ?>;">Payment status confirmed</p>
                        </div>
                    </div>
                    
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Order ID</span>
                            <span class="detail-value"><?php echo htmlspecialchars($payment_data['order_id']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Payment ID</span>
                            <span class="detail-value"><?php echo htmlspecialchars($payment_data['payment_id']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Amount</span>
                            <span class="detail-value"><?php echo htmlspecialchars($payment_data['currency']); ?> <?php echo number_format($payment_data['amount'], 2); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Method</span>
                            <span class="detail-value"><?php echo htmlspecialchars($payment_data['method']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Date & Time</span>
                            <span class="detail-value"><?php echo htmlspecialchars($payment_data['timestamp']); ?></span>
                        </div>
                        <?php if (!empty($payment_data['note'])): ?>
                        <div class="detail-item">
                            <span class="detail-label">Note</span>
                            <span class="detail-value"><?php echo htmlspecialchars($payment_data['note']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="actions">
                    <a href="verify_payment.php" class="btn btn-secondary">Check Another Payment</a>
                    <a href="index.php" class="btn btn-secondary">New Payment</a>
                </div>
            </div>

        <?php else: ?>
            <div class="card">
                <div class="not-found">
                    <svg class="not-found-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                    <h3>Payment Not Found</h3>
                    <p>No payment record found for Order ID: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>
                    <p style="margin-top: 12px; font-size: 13px;">The payment may not have been processed yet, or the Order ID is incorrect.</p>
                </div>

                <div class="actions">
                    <a href="verify_payment.php" class="btn btn-secondary">Try Again</a>
                    <a href="https://wa.me/<?php echo str_replace('+', '', BUSINESS_PHONE); ?>?text=Need help verifying payment - Order ID: <?php echo urlencode($order_id); ?>" class="btn btn-secondary" target="_blank">Contact Support</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="footer">
            <div><?php echo BUSINESS_NAME; ?></div>
            <a href="https://www.payhere.lk" class="footer-link" target="_blank">Secured by PayHere</a>
        </div>
    </div>
</body>
</html>
