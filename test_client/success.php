// Simulated Success Page on Client Website
$status_from_url = $_GET['status'] ?? 'unknown';
$order_id = $_GET['ref'] ?? ($_GET['order_id'] ?? '-'); // This is the Gateway Order ID (e.g. ORD-xxx)

// --- 🛡️ SECURITY CHECK START ---
// We don't trust the URL! We ask the Gateway for the REAL status.
$gateway_verify_url = "http://localhost:8000/api/v1/status/" . $order_id;
$ch = curl_init($gateway_verify_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$real_status = $result['data']['status'] ?? 'NOT_FOUND';
// --- 🛡️ SECURITY CHECK END ---
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Result</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; max-width: 400px; }
        .success { color: #28a745; }
        .failed { color: #dc3545; }
        .warning { color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 5px; font-size: 13px; margin-top: 15px; border: 1px solid #ffeeba;}
    </style>
</head>
<body>
    <div class="card">
        <?php if ($real_status === 'SUCCESS'): ?>
            <h1 class="success">✅ Payment Verified!</h1>
            <p>Our server checked with DigiMart Pay and confirmed your payment is REAL.</p>
        <?php elseif ($status_from_url === 'success' && $real_status !== 'SUCCESS'): ?>
             <h1 class="failed">🛑 SECURITY ALERT!</h1>
             <p>The URL says "Success", but the Gateway says the status is actually <strong><?php echo $real_status; ?></strong>.</p>
             <div class="warning">Nice try! You cannot trick the system by editing the URL. 😉</div>
        <?php else: ?>
            <h1 class="failed">❌ Payment Failed</h1>
            <p>Your transaction was not completed.</p>
        <?php endif; ?>
        
        <p style="margin-top: 20px; color: #666;">Gateway ID: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>
        <a href="index.php" style="display: inline-block; margin-top: 20px; color: #007bff; text-decoration: none;">Back to Store</a>
    </div>
</body>
</html>
