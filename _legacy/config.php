<?php
// Use sandbox or live
define('USE_SANDBOX', false); // Set to true for testing, false for production

// PayHere Configuration
// PayHere Configuration
if (USE_SANDBOX) {
    // SANDBOX Credentials (Test)
    define('MERCHANT_ID', '1233267');
    
    // Check Domain to select correct secret
    $host = $_SERVER['HTTP_HOST'];
    if (strpos($host, 'localhost') !== false) {
        // Localhost Secret
        define('MERCHANT_SECRET', 'MTY3MjQ3ODAwNDEzMzcyMzEwOTQzNTIzNDM0NTQ2NDA0MzgzNTY4Nw==');
    } else {
        // Production Domain Secret (digimartsolutions.lk)
        define('MERCHANT_SECRET', 'ODI0MDk0MTY3Njk2ODA3MzUyNDE4MDY0MTc2NTI0NDY1MjM1Mw==');
    }
} else {
    // LIVE Credentials (Production)
    define('MERCHANT_ID', '230488'); 
    define('MERCHANT_SECRET', 'MTM2NjY3NjE0MDMzODM2MDczNTMwNDYzMzg2MDkxNjgxMDgzNDg3');
}

// URLs
$base_url = 'https://pay.digimartsolutions.lk';
define('RETURN_URL', $base_url . '/return.php');
define('CANCEL_URL', $base_url . '/cancel.php');
define('NOTIFY_URL', $base_url . '/notify.php');

// PayHere Gateway URLs
define('PAYHERE_SANDBOX_URL', 'https://sandbox.payhere.lk/pay/checkout');
define('PAYHERE_LIVE_URL', 'https://www.payhere.lk/pay/checkout');

// Currency
define('CURRENCY', 'LKR');

// Business Details
define('BUSINESS_NAME', 'DigiMart Solutions');
define('BUSINESS_EMAIL', 'info@digimartsolutions.lk');
define('BUSINESS_PHONE', '+94772503124');

// Database Configuration (Optional - for storing payment records)
define('DB_HOST', 'localhost');
define('DB_NAME', 'digimartpay');
define('DB_USER', 'root');
define('DB_PASS', '');

// Helper function to get PayHere URL
function getPayHereURL() {
    return USE_SANDBOX ? PAYHERE_SANDBOX_URL : PAYHERE_LIVE_URL;
}

// Helper function to generate hash
function generateHash($merchant_id, $order_id, $amount, $currency, $merchant_secret) {
    $formatted_amount = number_format($amount, 2, '.', '');
    $hash = strtoupper(
        md5(
            $merchant_id . 
            $order_id . 
            $formatted_amount . 
            $currency . 
            strtoupper(md5($merchant_secret))
        )
    );
    return $hash;
}

// Helper function to verify payment notification
function verifyPaymentHash($merchant_id, $order_id, $payhere_amount, $payhere_currency, $status_code, $md5sig, $merchant_secret) {
    $formatted_amount = number_format($payhere_amount, 2, '.', '');
    $local_md5sig = strtoupper(
        md5(
            $merchant_id . 
            $order_id . 
            $formatted_amount . 
            $payhere_currency . 
            $status_code . 
            strtoupper(md5($merchant_secret))
        )
    );
    return ($local_md5sig === $md5sig);
}

// SMS API Configuration (SMSAPI.LK)
define('SMS_API_ENDPOINT', 'https://dashboard.smsapi.lk/api/v3/sms/send');
define('SMS_API_TOKEN', '227|EIMSSGYEhncx4oKy3Se3uSDLUQQF06e14GM4Rror');
define('SMS_SENDER_ID', 'DIGIMART'); // Your sender name (max 11 characters)
define('SMS_ENABLED', true); // Set to false to disable SMS notifications

// Supabase Configuration
// NOTE: Use the 'anon' public key (JWT) or 'service_role' secret here.
// The key provided 'sb_publishable...' does not look like a standard Supabase JWT.
// Please check your Supabase API Settings -> Project API Keys.
define('SUPABASE_URL', 'https://sbsbsibiamqbrdzfzylm.supabase.co');
define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InNic2JzaWJpYW1xYnJkemZ6eWxtIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjczMzUzMzksImV4cCI6MjA4MjkxMTMzOX0.CWXQW_JbeEs5unaecT8vDFwGxNO1k4RPOmE3_bJcLJI');
define('SUPABASE_ENABLED', true);

// Helper function to send SMS
function sendSMS($recipient, $message) {
    if (!SMS_ENABLED) {
        return false;
    }
    
    // Clean phone number (remove spaces, dashes)
    $recipient = preg_replace('/[^0-9+]/', '', $recipient);
    
    // Remove + from the beginning if present for SMS API
    $recipient = ltrim($recipient, '+');
    
    $data = [
        'recipient' => $recipient,
        'sender_id' => SMS_SENDER_ID,
        'type' => 'plain',
        'message' => $message
    ];
    
    $ch = curl_init(SMS_API_ENDPOINT);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . SMS_API_TOKEN,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $result = json_decode($response, true);
        return $result['status'] === 'success';
    }
    
    return false;
}
?>
