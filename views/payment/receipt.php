<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php if($payment->status === 'SUCCESS'): ?> Payment Successful
        <?php elseif($payment->status === 'CANCELLED'): ?> Payment Cancelled
        <?php else: ?> Payment Failed <?php endif; ?> - DigiMart Solutions
    </title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?php echo url('favicon/favicon.ico'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo url('favicon/favicon-16x16.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo url('favicon/favicon-32x32.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo url('favicon/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo url('favicon/android-chrome-192x192.png'); ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?php echo url('favicon/android-chrome-512x512.png'); ?>">
    <link rel="manifest" href="<?php echo url('favicon/site.webmanifest'); ?>">

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
        <div class="success-icon" style="background: <?php echo $payment->status === 'SUCCESS' ? '#f0fdf4' : ($payment->status === 'CANCELLED' ? '#fff7ed' : '#fef2f2'); ?>; border-color: <?php echo $payment->status === 'SUCCESS' ? '#86efac' : ($payment->status === 'CANCELLED' ? '#fed7aa' : '#fecaca'); ?>;">
            <?php if($payment->status === 'SUCCESS'): ?>
                <svg class="checkmark" viewBox="0 0 24 24" fill="none" stroke="#16a34a">
                    <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
                </svg>
            <?php elseif($payment->status === 'CANCELLED'): ?>
                <svg class="checkmark" viewBox="0 0 24 24" fill="none" stroke="#ea580c">
                    <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
                </svg>
            <?php else: ?>
                <svg class="checkmark" viewBox="0 0 24 24" fill="none" stroke="#dc2626">
                    <path d="M12 9v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 17c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/>
                </svg>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="status-title">
                    <?php if($payment->status === 'SUCCESS'): ?> Payment Successful
                    <?php elseif($payment->status === 'CANCELLED'): ?> Payment Cancelled
                    <?php else: ?> Payment Failed <?php endif; ?>
                </div>
                <div class="status-message">
                    <?php if($payment->status === 'SUCCESS'): ?> Your transaction has been processed successfully
                    <?php elseif($payment->status === 'CANCELLED'): ?> The payment process was cancelled by the user
                    <?php else: ?> There was an error processing your transaction <?php endif; ?>
                </div>
            </div>

            <div class="info-banner">
                <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 16v-4"></path>
                    <path d="M12 8h.01"></path>
                </svg>
                <div class="info-text">
                    Your payment is being verified and will be confirmed shortly. You will receive a confirmation SMS once verification is complete.
                    
                    <?php
                        $host = $_SERVER['HTTP_HOST'] ?? '';
                        $isLocalHost = in_array($host, ['localhost', '127.0.0.1']);
                        $isLocalEnv = ($_ENV['APP_ENV'] ?? 'production') === 'local';
                    ?>
                    <?php if($payment->status === 'PENDING' && ($isLocalEnv || $isLocalHost)): ?>
                    <div style="margin-top: 10px; border-top: 1px solid #fde68a; pt-2;">
                        <p style="font-weight: bold; color: #b45309;">🛠️ Developer Note (Localhost):</p>
                        <p>Webhooks cannot reach localhost. Click below to simulate success.</p>
                        <a href="<?php echo url('pay/sync/' . $payment->order_id); ?>" class="btn" style="background: #f59e0b; color: white; margin-top: 8px; padding: 6px 12px; font-size: 12px; width: auto;">Confirm Payment Manually</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="amount-section">
                <div class="amount-label">Amount Paid</div>
                <div class="amount-value"><?php echo htmlspecialchars($payment->currency); ?> <?php echo number_format($payment->amount, 2); ?></div>
            </div>

            <div class="details-section">
                <div class="detail-row">
                    <span class="detail-label">Order ID</span>
                    <span class="detail-value"><?php echo htmlspecialchars($payment->order_id); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date & Time</span>
                    <span class="detail-value"><?php echo $payment->updated_at; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="status-badge" style="background: <?php echo $payment->status === 'SUCCESS' ? '#f0fdf4' : ($payment->status === 'CANCELLED' ? '#fff7ed' : '#fef2f2'); ?>; color: <?php echo $payment->status === 'SUCCESS' ? '#16a34a' : ($payment->status === 'CANCELLED' ? '#ea580c' : '#dc2626'); ?>;">
                            <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor">
                                <circle cx="4" cy="4" r="4"/>
                            </svg>
                            <?php echo ucfirst(strtolower($payment->status)); ?>
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
                
                <a href="<?php echo url('/'); ?>" class="btn btn-secondary">Make Another Payment</a>
            </div>
        </div>

        <div class="footer">
            <div>DigiMart Solutions</div>
            <a href="#" class="footer-link" target="_blank">Support</a>
            <span style="color: #cbd5e0;">•</span>
            <a href="https://www.payhere.lk" class="footer-link" target="_blank">Secured by PayHere</a>
        </div>
    </div>

    <!-- Html2Canvas Library for Image Generation -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <script>
        function shareOnWhatsApp() {
            const message = "Payment Receipt\nReference: <?php echo $payment->order_id; ?>\nAmount: <?php echo $payment->currency; ?> <?php echo number_format($payment->amount, 2); ?>\n\nThank you for your payment!";
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
                link.download = 'Receipt-<?php echo $payment->order_id; ?>.jpg';
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
            <div style="font-size: 36px; font-weight: 700; color: #635bff; letter-spacing: -1px;"><?php echo htmlspecialchars($payment->currency); ?> <?php echo number_format($payment->amount, 2); ?></div>
            <div style="font-size: 13px; color: #10b981; font-weight: 600; background: #d1fae5; display: inline-block; padding: 4px 12px; border-radius: 20px; margin-top: 8px;">✓ Paid Successfully</div>
        </div>
        
        <div style="border-top: 2px dashed #e3e8ee; border-bottom: 2px dashed #e3e8ee; padding: 20px 0; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                <span style="color: #6b7c93;">Order ID</span>
                <span style="font-weight: 600;"><?php echo htmlspecialchars($payment->order_id); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                <span style="color: #6b7c93;">Date</span>
                <span style="font-weight: 600;"><?php echo $payment->updated_at; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
                <span style="color: #6b7c93;">Method</span>
                <span style="font-weight: 600;">Visa/Master</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 14px;">
                <span style="color: #6b7c93;">Reference</span>
                <span style="font-weight: 600;"><?php echo htmlspecialchars($payment->order_id); ?></span>
            </div>
        </div>
        
        <div style="text-align: center; font-size: 12px; color: #9da8b6; line-height: 1.5;">
            This is an electronically generated receipt.<br>
            Thank you for your business!
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <img src="<?php echo url('logo.png'); ?>" style="height: 24px; opacity: 0.5;">
        </div>
    </div>
</body>
</html>
