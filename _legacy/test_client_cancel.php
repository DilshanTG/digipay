<?php
// test_client_cancel.php
$status = $_GET['status'] ?? 'unknown';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Cancelled - My App</title>
    <style>
        body { font-family: sans-serif; padding: 40px; text-align: center; }
        .error-box { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; display: inline-block; }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>Payment Cancelled</h1>
        <p>You cancelled the payment process.</p>
        <br>
        <a href="test_client.php">Try Again</a>
    </div>
</body>
</html>
