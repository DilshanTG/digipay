<?php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Get form data
$full_name = trim($_POST['full_name'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$email = trim($_POST['email'] ?? '');
$amount = floatval($_POST['amount'] ?? 0);
$note = trim($_POST['note'] ?? '');

// Validate inputs
$errors = [];

if (empty($full_name)) {
    $errors[] = 'Full name is required';
}

if (empty($whatsapp)) {
    $errors[] = 'WhatsApp number is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email address is required';
}

if ($amount < 1) {
    $errors[] = 'Amount must be at least Rs 1.00';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(', ', $errors);
    header('Location: index.php');
    exit;
}

// Split name into first and last name
$name_parts = explode(' ', $full_name, 2);
$first_name = $name_parts[0];
$last_name = isset($name_parts[1]) ? $name_parts[1] : '';

// ----------------------------------------------------
// TOKEN-BASED GATEWAY LOGIC
// ----------------------------------------------------
$token = $_POST['token'] ?? null;
$order_id = null;

if ($token) {
    // 1. Load Context using Token
    require_once 'order_store.php';
    $context = getOrderContext($token);
    
    if ($context && isset($context['internal_order_id'])) {
        $order_id = $context['internal_order_id'];
        
        // 2. IMPORTANT: Re-Save Context keyed by Order ID
        // notify.php will only know the Order ID, not the Token.
        // So we must link OrderID -> Context for notify.php to find the Client URLs.
        saveOrderContext($order_id, $context);
    }
}

// Fallback: Generate local Order ID if not provided via Token or Force ID
if (!$order_id) {
    if (isset($_POST['force_order_id']) && !empty($_POST['force_order_id'])) {
        $order_id = $_POST['force_order_id'];
    } elseif (isset($_SESSION['client_order_id']) && !empty($_SESSION['client_order_id'])) {
         $order_id = $_SESSION['client_order_id']; // Legacy Session Support
    } else {
        $order_id = 'DM' . time() . rand(1000, 9999);
    }
}

// Calculate transaction fee (3.9%)
$original_amount = $amount;
$fee_percentage = 0.039;
$fee_amount = $original_amount * $fee_percentage;
$total_amount = $original_amount + $fee_amount;

// PayHere expects amount to 2 decimal places
$payhere_amount = number_format($total_amount, 2, '.', '');

// Generate hash
$hash = generateHash(MERCHANT_ID, $order_id, $total_amount, CURRENCY, MERCHANT_SECRET);

// Store payment details in session for verification later (UI display only)
$_SESSION['pending_payment'] = [
    'order_id' => $order_id,
    'amount' => $total_amount,
    'original_amount' => $original_amount,
    'fee' => $fee_amount,
    'whatsapp' => $whatsapp,
    'email' => $email,
    'full_name' => $full_name,
    'note' => $note,
    'timestamp' => time()
];

// Combine Original Amount and Note into custom_1 for SMS retrieval
// Format: AMT:1000|Note Text
$custom_data = "AMT:" . $original_amount;
if (!empty($note)) {
    $custom_data .= "|" . $note;
}

// Payment description
// Payment description (Publicly visible on PayHere)
$items_description = 'Payment for Digimart Solutions';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment - DigiMart Solutions</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f6f9fc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .processing-card {
            background: white;
            border: 1px solid #e3e8ee;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            max-width: 420px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .spinner {
            border: 3px solid #e3e8ee;
            border-top: 3px solid #635bff;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 24px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h2 {
            color: #0a2540;
            margin-bottom: 8px;
            font-size: 20px;
            font-weight: 600;
        }

        p {
            color: #6b7c93;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .payment-details {
            background: #fafbfc;
            border: 1px solid #e3e8ee;
            border-radius: 6px;
            padding: 20px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            color: #6b7c93;
        }

        .detail-value {
            color: #0a2540;
            font-weight: 600;
        }

        .amount-row {
            border-top: 1px solid #e3e8ee;
            padding-top: 12px;
            margin-top: 12px;
        }

        .amount-row .detail-value {
            color: #635bff;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="processing-card">
        <div class="spinner"></div>
        <h2>Redirecting to Payment Gateway</h2>
        <p>Please wait while we connect you to PayHere...</p>

        <div class="payment-details">
            <div class="detail-row">
                <span class="detail-label">Order ID</span>
                <span class="detail-value"><?php echo htmlspecialchars($order_id); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Name</span>
                <span class="detail-value"><?php echo htmlspecialchars($full_name); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email</span>
                <span class="detail-value"><?php echo htmlspecialchars($email); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount</span>
                <span class="detail-value">Rs <?php echo number_format($original_amount, 2); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Fee (3.9%)</span>
                <span class="detail-value">Rs <?php echo number_format($fee_amount, 2); ?></span>
            </div>
            <div class="detail-row amount-row">
                <span class="detail-label">Total Charge</span>
                <span class="detail-value">Rs <?php echo number_format($total_amount, 2); ?></span>
            </div>
        </div>
    </div>

    <!-- PayHere Form -->
    <form id="payhereForm" method="POST" action="<?php echo getPayHereURL(); ?>" style="display: none;">
        <input type="hidden" name="merchant_id" value="<?php echo MERCHANT_ID; ?>">
        <input type="hidden" name="return_url" value="<?php echo RETURN_URL . '?order_id=' . urlencode($order_id); ?>">
        <input type="hidden" name="cancel_url" value="<?php echo CANCEL_URL . '?order_id=' . urlencode($order_id); ?>">
        <input type="hidden" name="notify_url" value="<?php echo NOTIFY_URL; ?>">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <input type="hidden" name="items" value="<?php echo htmlspecialchars($items_description); ?>">
        <input type="hidden" name="currency" value="<?php echo CURRENCY; ?>">
        <input type="hidden" name="amount" value="<?php echo $payhere_amount; ?>">
        <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>">
        <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <input type="hidden" name="phone" value="<?php echo htmlspecialchars($whatsapp); ?>">
        <input type="hidden" name="address" value="Online Payment">
        <input type="hidden" name="city" value="Colombo">
        <input type="hidden" name="country" value="Sri Lanka">
        <input type="hidden" name="hash" value="<?php echo $hash; ?>">
        <input type="hidden" name="custom_1" value="<?php echo htmlspecialchars($custom_data); ?>">
        <input type="hidden" name="custom_2" value="<?php echo htmlspecialchars($whatsapp); ?>">
    </form>

    <script>
        // Auto-submit form after 2 seconds
        setTimeout(function() {
            document.getElementById('payhereForm').submit();
        }, 2000);
    </script>
</body>
</html>
