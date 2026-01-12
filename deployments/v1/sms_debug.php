<?php
/**
 * SMS Debug & Order Status Checker
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Payment;
use App\Services\SmsService;

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>SMS Debugger</title>
    <style>
        body { font-family: 'Outfit', sans-serif; padding: 40px; background: #f8fafc; color: #1e293b; }
        .box { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        h1 { color: #6366f1; }
        .status { padding: 10px; border-radius: 8px; font-weight: bold; display: inline-block; margin-bottom: 20px; }
        .SUCCESS { background: #dcfce7; color: #166534; }
        .PENDING { background: #fef9c3; color: #854d0e; }
        .FAILED { background: #fef2f2; color: #991b1b; }
        pre { background: #1e293b; color: #cbd5e1; padding: 15px; border-radius: 10px; overflow-x: auto; }
        .btn { background: #6366f1; color: white; padding: 12px 24px; border: none; border-radius: 10px; cursor: pointer; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
    <div class='box'>
        <h1>🔍 Order & SMS Debugger</h1>";

$orderId = $_GET['order_id'] ?? null;

if ($orderId) {
    $payment = Payment::where('order_id', $orderId)->first();
    
    if ($payment) {
        echo "<h2>Order Details: $orderId</h2>";
        $className = $payment->status;
        echo "<div class='status $className'>Status: {$payment->status}</div>";
        echo "<pre>";
        echo "Amount: {$payment->amount} {$payment->currency}\n";
        echo "Customer Phone: {$payment->customer_phone}\n";
        echo "Customer Email: {$payment->customer_email}\n";
        echo "Created At: {$payment->created_at}\n";
        echo "PayHere Ref: " . ($payment->payhere_ref ?: 'None') . "\n";
        echo "</pre>";
        
        if ($payment->status === 'PENDING') {
            echo "<p style='color: #854d0e;'>⚠️ This order is still PENDING. Notifications are only sent for SUCCESSful orders.</p>";
            echo "<p>If the user actually paid, the system might not have received the webhook from PayHere yet.</p>";
        } else {
            echo "<p style='color: #166534;'>✅ This order is marked as SUCCESS. Notifications should have been sent.</p>";
        }
    } else {
        echo "<div class='status FAILED'>Order Not Found: $orderId</div>";
    }
}

echo "<h2>🔧 SMS Configuration Check</h2>";
$smsToken = env('SMS_API_TOKEN');
$hiddenToken = substr($smsToken, 0, 5) . str_repeat('*', 15) . substr($smsToken, -5);

echo "<pre>";
echo "SMS Enabled: " . (env('SMS_ENABLED', true) ? 'YES' : 'NO') . "\n";
echo "SMS Token: $hiddenToken\n";
echo "SMS Sender ID: " . env('SMS_SENDER_ID') . "\n";
echo "Is using placeholder token? " . ($smsToken === 'your_sms_token' ? 'YES (NEEDS FIX!)' : 'NO') . "\n";
echo "</pre>";

if ($smsToken === 'your_sms_token') {
    echo "<p style='color: #ef4444; font-weight: bold;'>🚨 CRITICAL: You are using 'your_sms_token'. Please edit your .env file and put your real SMS token!</p>";
}

echo "<h2>🧪 Test SMS</h2>";
echo "<form method='POST'>
    <p>Send a test SMS to Admin (94774665742):</p>
    <button type='submit' name='test_sms' class='btn'>Send Test SMS</button>
</form>";

if (isset($_POST['test_sms'])) {
    $sms = new SmsService();
    echo "<p>Sending test SMS...</p>";
    $result = $sms->send('94774665742', "Test from DigiMart Pay Debugger at " . date('H:i:s'));
    
    if ($result) {
        echo "<div class='status SUCCESS'>✓ Test SMS Sent Successfully!</div>";
    } else {
        echo "<div class='status FAILED'>✗ SMS Failed. Check Laravel logs for error details.</div>";
    }
}

echo "<h2>📝 Recent Logs</h2>";
$logFile = '/home/digimart/pay.digimartsolutions.lk/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last20 = array_slice($lines, -20);
    echo "<pre>";
    foreach ($last20 as $line) {
        echo htmlspecialchars($line);
    }
    echo "</pre>";
}

echo "</div></body></html>";
