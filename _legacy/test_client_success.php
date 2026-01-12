<?php
// test_client_success.php
$status = $_GET['status'] ?? 'unknown';
$order_id = $_GET['order_id'] ?? 'unknown';
$amount = $_GET['amount'] ?? '0.00';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Success - My App</title>
    <style>
        body { font-family: sans-serif; padding: 40px; text-align: center; }
        .success-box { background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; display: inline-block; }
    </style>
</head>
<body>
    <div class="success-box">
        <h1>Payment Successful!</h1>
        <p>Your payment has been processed.</p>
        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?></p>
        <p><strong>Amount:</strong> Rs <?php echo htmlspecialchars($amount); ?></p>
        <br>
        <a href="test_client.php">Go Back to Shop</a>
    </div>
</body>
</html>
