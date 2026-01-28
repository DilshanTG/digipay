<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandbox Payment Simulator - PayHere Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .payment-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }

        .payment-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .sandbox-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.3);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .payment-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .payment-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .payment-body {
            padding: 30px;
        }

        .payment-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .info-row:last-child {
            margin-bottom: 0;
            padding-top: 12px;
            border-top: 2px dashed #dee2e6;
            font-weight: 600;
            font-size: 16px;
        }

        .info-label {
            color: #6c757d;
        }

        .info-value {
            color: #212529;
            font-weight: 500;
        }

        .amount-highlight {
            color: #f5576c;
            font-size: 20px;
        }

        .test-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .test-btn {
            border: none;
            padding: 16px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .test-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .test-btn:active {
            transform: translateY(0);
        }

        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .btn-cancelled {
            background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);
            color: white;
        }

        .btn-failed {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }

        .btn-pending {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }

        .icon {
            width: 20px;
            height: 20px;
        }

        .help-text {
            text-align: center;
            color: #6c757d;
            font-size: 13px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .help-text strong {
            color: #212529;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        .sandbox-badge {
            animation: pulse 2s infinite;
        }

        /* Status code display */
        .status-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        .code-badge {
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <div class="sandbox-badge">🧪 SANDBOX MODE</div>
            <h1>Payment Test Simulator</h1>
            <p>Choose a payment outcome to test your integration</p>
        </div>

        <div class="payment-body">
            <div class="payment-info">
                <div class="info-row">
                    <span class="info-label">Order ID</span>
                    <span class="info-value"><?php echo htmlspecialchars($payment->order_id); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Description</span>
                    <span class="info-value"><?php echo htmlspecialchars($payment->fake_description ?? 'Payment Order'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer</span>
                    <span class="info-value"><?php echo htmlspecialchars($payment->customer_email ?? 'N/A'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Amount</span>
                    <span class="info-value amount-highlight">
                        <?php echo htmlspecialchars($payment->currency); ?>
                        <?php echo number_format($payment->amount, 2); ?>
                    </span>
                </div>
            </div>

            <div class="test-buttons">
                <form action="/sandbox/callback" method="POST" style="width: 100%;">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($payment->order_id); ?>">
                    <input type="hidden" name="status_code" value="2">
                    <button type="submit" class="test-btn btn-success">
                        <span>✓</span>
                        <span>Payment Success</span>
                    </button>
                    <div class="status-info">
                        <span>Simulates successful payment</span>
                        <span class="code-badge">status_code: 2</span>
                    </div>
                </form>

                <form action="/sandbox/callback" method="POST" style="width: 100%; margin-top: 12px;">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($payment->order_id); ?>">
                    <input type="hidden" name="status_code" value="-1">
                    <button type="submit" class="test-btn btn-cancelled">
                        <span>↺</span>
                        <span>Payment Cancelled</span>
                    </button>
                    <div class="status-info">
                        <span>Simulates user cancelled payment</span>
                        <span class="code-badge">status_code: -1</span>
                    </div>
                </form>

                <form action="/sandbox/callback" method="POST" style="width: 100%; margin-top: 12px;">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($payment->order_id); ?>">
                    <input type="hidden" name="status_code" value="-2">
                    <button type="submit" class="test-btn btn-failed">
                        <span>✗</span>
                        <span>Payment Failed</span>
                    </button>
                    <div class="status-info">
                        <span>Simulates payment gateway error</span>
                        <span class="code-badge">status_code: -2</span>
                    </div>
                </form>

                <form action="/sandbox/callback" method="POST" style="width: 100%; margin-top: 12px;">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($payment->order_id); ?>">
                    <input type="hidden" name="status_code" value="0">
                    <button type="submit" class="test-btn btn-pending">
                        <span>⏱</span>
                        <span>Keep Pending</span>
                    </button>
                    <div class="status-info">
                        <span>Payment stays pending (useful for testing auto-verification)</span>
                        <span class="code-badge">status_code: 0</span>
                    </div>
                </form>
            </div>

            <div class="help-text">
                <strong>How it works:</strong><br>
                Click any button to simulate that payment outcome. Your webhook and return URLs will receive the appropriate PayHere-compatible response.
            </div>
        </div>
    </div>
</body>
</html>
