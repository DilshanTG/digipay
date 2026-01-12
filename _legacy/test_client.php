<?php
// Simulate an external application (e.g., Filesta)
$gateway_url = "http://localhost/digimartpay/checkout.php"; // Update to actual URL
$current_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$base_dir = dirname($current_url);

$return_url = $base_dir . "/test_client_success.php";
$cancel_url = $base_dir . "/test_client_cancel.php";

$order_id = "REF" . time();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Client App</title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        .btn { padding: 10px 20px; background: #635bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>My Awesome App (Test Client)</h1>
    <p>This page simulates "Filesta" or any other app.</p>
    
    <div style="background: #f0f0f0; padding: 20px; border-radius: 8px;">
        <h3>Invoice #<?php echo $order_id; ?></h3>
        <p>Total: LKR 100.00</p>
        
        <p>
            <!-- Manual Flow (User enters details) -->
            <a href="<?php echo $gateway_url; ?>?amount=100&order_id=<?php echo $order_id; ?>&return_url=<?php echo urlencode($return_url); ?>&cancel_url=<?php echo urlencode($cancel_url); ?>&app_name=TestApp" class="btn">
                Pay Now (Enter Details manually)
            </a>
        </p>
        
        <p>
            <!-- Auto Flow (User details pre-filled) -->
            <a href="<?php echo $gateway_url; ?>?amount=100&order_id=<?php echo $order_id; ?>&return_url=<?php echo urlencode($return_url); ?>&cancel_url=<?php echo urlencode($cancel_url); ?>&app_name=TestApp&full_name=Test+User&email=test@example.com&phone=0771234567" class="btn" style="background: #008000;">
                Pay Now (Auto-Fill)
            </a>
        </p>
    </div>
</body>
</html>
