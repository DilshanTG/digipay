<?php
require_once 'config.php';
session_start();

/**
 * CHECKOUT API ENTRY POINT
 * 
 * Usage:
 * Redirect users here with the following query parameters:
 * - amount: (Required) Payment amount in LKR
 * - order_id: (Optional) Your internal order reference. If not set, one will be generated.
 * - return_url: (Optional) URL to redirect on success. REQUIRED for Gateway Mode.
 * - cancel_url: (Optional) URL to redirect on cancel.
 * - app_name: (Optional) Name of your app to display on payment page.
 * - full_name: (Optional) Customer name
 * - email: (Optional) Customer email
 * - phone: (Optional) Customer phone
 * - note: (Optional) Payment note
 */

// 1. Clear previous session to avoid pollution
if (isset($_SESSION['gateway_mode'])) {
    unset($_SESSION['gateway_mode']);
    unset($_SESSION['client_return_url']);
    unset($_SESSION['client_cancel_url']);
    unset($_SESSION['client_notify_url']); // Clear notify_url from session
    unset($_SESSION['client_app_name']);
    unset($_SESSION['client_order_id']);
}

// 2. Capture Parameters
$amount = isset($_REQUEST['amount']) ? floatval($_REQUEST['amount']) : null;
$return_url = isset($_REQUEST['return_url']) ? trim($_REQUEST['return_url']) : null;
$cancel_url = isset($_REQUEST['cancel_url']) ? trim($_REQUEST['cancel_url']) : null;
$notify_url = isset($_REQUEST['notify_url']) ? trim($_REQUEST['notify_url']) : null; // Added notify_url
$app_name = isset($_REQUEST['app_name']) ? trim($_REQUEST['app_name']) : 'External App';
$order_id = isset($_REQUEST['order_id']) ? trim($_REQUEST['order_id']) : null;

// Pre-fill data
$full_name = isset($_REQUEST['full_name']) ? trim($_REQUEST['full_name']) : '';
$email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : '';
$phone = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : '';
$note = isset($_REQUEST['note']) ? trim($_REQUEST['note']) : '';

// 3. Determine Mode
$gateway_mode = !empty($return_url);

// 4. Validate Critical Data for API usage
if ($gateway_mode && (!$amount || $amount <= 0)) {
    die("Error: Amount is required for API checkout.");
}

// 5. Generate Transaction Token (Stateless Architecture)
// Instead of PHP Sessions (which are sticky and buggy for this use case),
// we generate a unique Token for this specific transaction attempt.
$token = bin2hex(random_bytes(16)); // 32-char unique string

// 6. Persist Context
require_once 'order_store.php';

// If order_id isn't provided, generate one
if (!$order_id) {
    $order_id = 'DM' . time() . rand(1000, 9999);
}

$context = [
    'mode' => 'gateway',
    'token' => $token,
    'amount' => $amount,
    'return_url' => $return_url,
    'cancel_url' => $cancel_url,
    'notify_url' => $notify_url,
    'app_name' => $app_name,
    'internal_order_id' => $order_id, // The ID used by the client app
    'prefill' => [
        'full_name' => $full_name,
        'email' => $email,
        'phone' => $phone,
        'note' => $note
    ],
    'created_at' => time()
];

// Save context using the TOKEN as the key (not order_id, to allow multiple attempts)
saveOrderContext($token, $context);

// 7. Auto-Process Check
$can_auto_process = $gateway_mode && 
                   $amount > 0 && 
                   !empty($full_name) && 
                   !empty($email) && 
                   !empty($phone);

if ($can_auto_process) {
    // Forward to process_payment.php with the Token
    ?>
    <!DOCTYPE html>
    <html>
    <body>
        <form id="autoForm" action="process_payment.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="hidden" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="whatsapp" value="<?php echo htmlspecialchars($phone); ?>">
            <input type="hidden" name="amount" value="<?php echo $amount; ?>">
            <input type="hidden" name="note" value="<?php echo htmlspecialchars($note); ?>">
        </form>
        <script>document.getElementById('autoForm').submit();</script>
    </body>
    </html>
    <?php
    exit;
}

// 8. Manual Process - Redirect to UI with Token
header("Location: index.php?token=" . $token);
exit;
?>
