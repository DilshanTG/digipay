<?php
require_once 'config.php';
session_start();

// Gateway Mode: Auto-Redirect Logic
require_once 'order_store.php';

// Try to get Order ID from GET or POST (PayHere param)
$order_id_param = $_GET['order_id'] ?? ($_POST['order_id'] ?? null);

// Check File Persistence first (Robust)
$order_context = $order_id_param ? getOrderContext($order_id_param) : null;
$gateway_mode = false;
$return_url = null;

if ($order_context) {
    $gateway_mode = true;
    $return_url = $order_context['return_url'];
} elseif (isset($_SESSION['gateway_mode']) && $_SESSION['gateway_mode'] === true) {
    // Fallback to Session
    $gateway_mode = true;
    $return_url = $_SESSION['client_return_url'];
}

if ($gateway_mode && $return_url) {
    $order_id = $order_id_param ?? ($_SESSION['pending_payment']['order_id'] ?? '');
    
    // Construct Query Params
    $query_params = [
        'status' => 'success',
        'order_id' => $order_id,
    ];
    
    // If we have payhere amount, pass it back
    if (isset($_POST['payhere_amount'])) {
        $query_params['amount'] = $_POST['payhere_amount'];
        $query_params['currency'] = $_POST['payhere_currency'];
    } elseif (isset($_SESSION['pending_payment']['amount'])) {
        $query_params['amount'] = $_SESSION['pending_payment']['amount'];
    }
    
    // Append params to return URL
    $separator = (parse_url($return_url, PHP_URL_QUERY) == NULL) ? '?' : '&';
    $redirect_url = $return_url . $separator . http_build_query($query_params);
    
    // Clear State
    if (isset($order_id)) {
         deleteOrderContext($order_id);
    }
    unset($_SESSION['gateway_mode']);
    unset($_SESSION['client_return_url']);
    unset($_SESSION['client_cancel_url']);
    unset($_SESSION['pending_payment']);
    unset($_SESSION['checkout_prefill']);
    
    // Redirect
    header("Location: " . $redirect_url);
    exit;
}

// Try to get order details from session or URL
$order_id = $_GET['order_id'] ?? ($_SESSION['pending_payment']['order_id'] ?? 'N/A');
$amount = $_SESSION['pending_payment']['amount'] ?? 0;
$currency = $_SESSION['pending_payment']['currency'] ?? 'LKR';

// Also check if we have data from PayHere POST
if (empty($amount) && isset($_POST['payhere_amount'])) {
    $amount = $_POST['payhere_amount'];
}

// Clear pending payment from session
if (isset($_SESSION['pending_payment'])) {
    // Save minimal info for receipt reload
    $_SESSION['last_payment_status'] = [
        'order_id' => $order_id,
        'amount' => $amount,
        'currency' => $currency,
        'timestamp' => time()
    ];
    unset($_SESSION['pending_payment']);
} else if (isset($_SESSION['last_payment_status']) && $_SESSION['last_payment_status']['order_id'] == $order_id) {
    // Restore from last status
    $amount = $_SESSION['last_payment_status']['amount'];
    $currency = $_SESSION['last_payment_status']['currency'];
}

// For receipt display
$payhere_amount = $amount;
$payhere_currency = $currency;
$method = "Visa/Master"; // Default since we don't get this easily
$payment_id = $order_id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - DigiMart Solutions</title>
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

        .success-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 24px;
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkmark {
            width: 32px;
            height: 32px;
            stroke: #16a34a;
            stroke-width: 2.5;
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

        .info-banner {
            padding: 16px 24px;
            background: #fffbeb;
            border-bottom: 1px solid #fde68a;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .info-icon {
            width: 20px;
            height: 20px;
            stroke: #d97706;
            flex-shrink: 0;
        }

        .info-text {
            font-size: 13px;
            color: #92400e;
            line-height: 1.5;
        }

        .amount-section {
            padding: 24px 32px;
            background: #fafbfc;
            border-bottom: 1px solid #e3e8ee;
        }

        .amount-label {
            font-size: 13px;
            font-weight: 600;
            color: #6b7c93;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .amount-value {
            font-size: 36px;
            font-weight: 600;
            color: #0a2540;
            letter-spacing: -0.02em;
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
            background: #f0fdf4;
            color: #16a34a;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }

        .actions {
            padding: 24px 32px 32px;
            background: #fafbfc;
            display: flex;
            flex-direction: column;
            gap: 10px;
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
            .details-section {
                padding: 24px 20px;
            }

            .amount-section,
            .actions {
                padding: 20px;
            }

            .status-title {
                font-size: 20px;
            }

            .amount-value {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg class="checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="status-title">Payment Successful</div>
                <div class="status-message">Your transaction has been processed successfully</div>
            </div>

            <div class="info-banner">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 16v-4"></path>
                    <path d="M12 8h.01"></path>
                </svg>
                <div class="info-text">
                    Your payment is being verified and will be confirmed shortly. You will receive a confirmation SMS once verification is complete.
                </div>
            </div>

            <?php if ($amount): ?>
            <div class="amount-section">
                <div class="amount-label">Amount Paid</div>
                <div class="amount-value">Rs <?php echo number_format($amount, 2); ?></div>
            </div>
            <?php endif; ?>

            <div class="details-section">
                <div class="detail-row">
                    <span class="detail-label">Order ID</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order_id); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date & Time</span>
                    <span class="detail-value"><?php echo date('d M Y, h:i A'); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="status-badge">
                            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor">
                                <circle cx="4" cy="4" r="4"/>
                            </svg>
                            Processing
                        </span>
                    </span>
                </div>
            </div>

            <div class="actions">
                <button type="button" onclick="downloadReceipt()" id="downloadBtn" class="btn btn-primary" style="background: #0a2540; margin-bottom: 10px;">
                    <span style="margin-right: 8px;">⬇️</span> Download Image Receipt
                </button>
                
                <button type="button" onclick="shareOnWhatsApp()" class="btn" style="background: #25D366; color: white; border: none; margin-bottom: 10px;">
                    <span style="margin-right: 8px;">💬</span> Share Text on WhatsApp
                </button>
                
                <a href="index.php" class="btn btn-secondary">Make Another Payment</a>
            </div>
        </div>

        <div class="footer">
            <div><?php echo BUSINESS_NAME; ?></div>
            <a href="https://wa.me/<?php echo str_replace('+', '', BUSINESS_PHONE); ?>" class="footer-link" target="_blank">Support</a>
            <span style="color: #cbd5e0;">•</span>
            <a href="https://www.payhere.lk" class="footer-link" target="_blank">Secured by PayHere</a>
        </div>
    </div>
    <!-- Html2Canvas Library for Image Generation -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <script>
        function shareOnWhatsApp() {
            const message = "Payment Receipt\nReference: <?php echo $order_id; ?>\nAmount: <?php echo $payhere_currency . ' ' . number_format($payhere_amount, 2); ?>\n\nThank you for your payment!";
            const url = "https://wa.me/?text=" + encodeURIComponent(message);
            window.open(url, '_blank');
        }

        function downloadReceipt() {
            const receipt = document.getElementById('receipt-card');
            const btn = document.getElementById('downloadBtn');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = 'Generating...';
            btn.style.opacity = '0.7';
            
            // Show receipt temporarily
            receipt.style.display = 'block';
            
            html2canvas(receipt, {
                scale: 2, // High quality
                backgroundColor: '#ffffff',
                logging: false,
                useCORS: true
            }).then(canvas => {
                // Hide receipt again
                receipt.style.display = 'none';
                
                // create download link
                const link = document.createElement('a');
                link.download = 'Receipt-<?php echo $order_id; ?>.jpg';
                link.href = canvas.toDataURL('image/jpeg', 0.9);
                link.click();
                
                btn.innerHTML = originalText;
                btn.style.opacity = '1';
            }).catch(err => {
                console.error('Receipt generation failed:', err);
                btn.innerHTML = originalText;
                btn.style.opacity = '1';
                alert('Could not generate receipt. Please try again.');
            });
        }
    </script>

    <!-- Hidden Receipt Template for Image Generation -->
    <div id="receipt-card" style="display: none; position: fixed; top: -9999px; left: -9999px; width: 400px; background: white; padding: 40px; border-radius: 12px; font-family: 'Inter', sans-serif; color: #0a2540;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 24px; font-weight: 800; color: #0a2540; letter-spacing: -0.5px; margin-bottom: 4px;">DigiMart Solutions</div>
            <div style="font-size: 14px; color: #6b7c93;">Payment Receipt</div>
        </div>
        
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 36px; font-weight: 700; color: #635bff; letter-spacing: -1px;"><?php echo $payhere_currency . ' ' . number_format($payhere_amount, 2); ?></div>
            <div style="font-size: 13px; color: #10b981; font-weight: 600; background: #d1fae5; display: inline-block; padding: 4px 12px; border-radius: 20px; margin-top: 8px;">✓ Paid Successfully</div>
        </div>
        
        <div style="border-top: 2px dashed #e3e8ee; border-bottom: 2px dashed #e3e8ee; padding: 20px 0; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                <span style="color: #6b7c93;">Order ID</span>
                <span style="font-weight: 600;"><?php echo $order_id; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                <span style="color: #6b7c93;">Date</span>
                <span style="font-weight: 600;"><?php echo date('d M Y, h:i A'); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                <span style="color: #6b7c93;">Method</span>
                <span style="font-weight: 600;"><?php echo $method; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 14px;">
                <span style="color: #6b7c93;">Reference</span>
                <span style="font-weight: 600;"><?php echo $payment_id; ?></span>
            </div>
        </div>
        
        <div style="text-align: center; font-size: 12px; color: #9da8b6; line-height: 1.5;">
            This is an electronically generated receipt.<br>
            Thank you for your business!
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <img src="logo.png" style="height: 24px; opacity: 0.5;">
        </div>
    </div>
</body>
</html>
