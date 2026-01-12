<?php
require_once 'config.php';
session_start();

// Gateway Mode: Auto-Redirect Logic
require_once 'order_store.php';

$order_id_param = $_GET['order_id'] ?? null; // Cancel might receive order_id too if we configured PayHere right, or session
// Note: PayHere CANCEL_URL usually doesn't get POST data like return, but we appended order_id? 
// Actually process_payment passes pure URL. PayHere redirects.
// If we want order_id in cancel, we need to append it to the cancel_url passed to PayHere.
// Let's rely on session or if user manually passes order_id in GET if modified.

// Check File Persistence
$order_context = $order_id_param ? getOrderContext($order_id_param) : null;
$gateway_mode = false;
$cancel_url = null;

if ($order_context) {
    $gateway_mode = true;
    $cancel_url = $order_context['cancel_url'];
} elseif (isset($_SESSION['gateway_mode']) && $_SESSION['gateway_mode'] === true) {
    $gateway_mode = true;
    $cancel_url = $_SESSION['client_cancel_url'];
}

if ($gateway_mode && $cancel_url) {
    // Clear State
    if ($order_id_param) {
         deleteOrderContext($order_id_param);
    }
    unset($_SESSION['gateway_mode']);
    unset($_SESSION['client_return_url']);
    unset($_SESSION['client_cancel_url']);
    unset($_SESSION['pending_payment']);
    unset($_SESSION['checkout_prefill']);
    
    $separator = (parse_url($cancel_url, PHP_URL_QUERY) == NULL) ? '?' : '&';
    $redirect_url = $cancel_url . $separator . 'status=cancelled';
    header("Location: " . $redirect_url);
    exit;
}

$order_id = $_GET['order_id'] ?? ($_SESSION['pending_payment']['order_id'] ?? 'N/A');

// Clear pending payment from session
if (isset($_SESSION['pending_payment'])) {
    unset($_SESSION['pending_payment']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - DigiMart Solutions</title>
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

        .cancel-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 24px;
            background: #fef3c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cancel-svg {
            width: 32px;
            height: 32px;
            stroke: #d97706;
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
            padding: 32px 32px 24px;
            text-align: center;
            border-bottom: 1px solid #e3e8ee;
        }

        .status-title {
            font-size: 24px;
            font-weight: 600;
            color: #0a2540;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .status-message {
            font-size: 15px;
            color: #6b7c93;
        }

        .message-section {
            padding: 24px 32px;
            background: #fffbeb;
            border-bottom: 1px solid #fde68a;
        }

        .message-text {
            font-size: 14px;
            color: #78350f;
            line-height: 1.6;
        }

        .details-section {
            padding: 24px 32px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f6f9fc;
            font-size: 14px;
        }

        .detail-row:last-child {
            border-bottom: none;
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

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }

        .actions {
            padding: 24px 32px 32px;
            background: #fafbfc;
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
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-bottom: 12px;
        }

        .btn:last-child {
            margin-bottom: 0;
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
        }

        .btn-secondary:hover {
            background: #f6f9fc;
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

        .footer-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            body {
                padding: 20px 16px;
            }

            .card-header,
            .details-section,
            .message-section {
                padding: 24px 20px;
            }

            .actions {
                padding: 20px;
            }

            .status-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="cancel-icon">
            <svg class="cancel-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="status-title">Payment Cancelled</div>
                <div class="status-message">Your payment request was cancelled</div>
            </div>

            <div class="message-section">
                <div class="message-text">
                    No charges were made to your account. You can try again or contact our support team if you need assistance.
                </div>
            </div>

            <div class="details-section">
                <div class="detail-row">
                    <span class="detail-label">Order ID</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order_id); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value"><?php echo date('d M Y, h:i A'); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="status-badge">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                                <circle cx="6" cy="6" r="6"/>
                            </svg>
                            Cancelled
                        </span>
                    </span>
                </div>
            </div>

            <div class="actions">
                <?php if ($gateway_mode && $cancel_url): ?>
                    <a href="<?php echo htmlspecialchars($cancel_url); ?>" class="btn btn-primary">Try Again</a>
                <?php else: ?>
                    <a href="index.php" class="btn btn-primary">Try Again</a>
                <?php endif; ?>
                <a href="https://wa.me/<?php echo str_replace('+', '', BUSINESS_PHONE); ?>?text=Need help with payment - Order: <?php echo urlencode($order_id); ?>" class="btn btn-secondary" target="_blank">Contact Support</a>
            </div>
        </div>

        <div class="footer">
            <div><?php echo BUSINESS_NAME; ?></div>
            <a href="https://wa.me/<?php echo str_replace('+', '', BUSINESS_PHONE); ?>" class="footer-link" target="_blank">Support</a>
            <span style="color: #cbd5e0;">•</span>
            <a href="https://www.payhere.lk" class="footer-link" target="_blank">Secured by PayHere</a>
        </div>
    </div>
</body>
</html>
