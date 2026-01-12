<?php
require_once 'config.php';

// Log the notification for debugging
$log_file = __DIR__ . '/payment_logs.txt';
$log_data = date('Y-m-d H:i:s') . " - Payment Notification Received\n";
$log_data .= print_r($_POST, true) . "\n\n";
file_put_contents($log_file, $log_data, FILE_APPEND);

// Get POST parameters
$merchant_id = $_POST['merchant_id'] ?? '';
$order_id = $_POST['order_id'] ?? '';
$payhere_amount = $_POST['payhere_amount'] ?? '';
$payhere_currency = $_POST['payhere_currency'] ?? '';
$status_code = $_POST['status_code'] ?? '';
$md5sig = $_POST['md5sig'] ?? '';
$payment_id = $_POST['payment_id'] ?? '';
$method = $_POST['method'] ?? '';
$status_message = $_POST['status_message'] ?? '';
$custom_1 = $_POST['custom_1'] ?? ''; // Note
$custom_2 = $_POST['custom_2'] ?? ''; // WhatsApp

// Card details (if available)
$card_holder_name = $_POST['card_holder_name'] ?? '';
$card_no = $_POST['card_no'] ?? '';
$card_expiry = $_POST['card_expiry'] ?? '';

// Verify the hash
$is_valid = verifyPaymentHash(
    $merchant_id,
    $order_id,
    $payhere_amount,
    $payhere_currency,
    $status_code,
    $md5sig,
    MERCHANT_SECRET
);

$log_data = "Hash Verification: " . ($is_valid ? "VALID" : "INVALID") . "\n";
$log_data .= "Status Code: " . $status_code . "\n";
file_put_contents($log_file, $log_data, FILE_APPEND);

if ($is_valid) {
    // Store payment in database (you can implement this)
    // For now, we'll just log it to a file
    
    $payment_record = [
        'timestamp' => date('Y-m-d H:i:s'),
        'order_id' => $order_id,
        'payment_id' => $payment_id,
        'amount' => $payhere_amount,
        'currency' => $payhere_currency,
        'status_code' => $status_code,
        'status_message' => $status_message,
        'method' => $method,
        'note' => $custom_1,
        'whatsapp' => $custom_2,
        'card_holder' => $card_holder_name,
        'card_no' => $card_no
    ];
    
    $payment_file = __DIR__ . '/payments.json';
    $payments = [];
    
    if (file_exists($payment_file)) {
        $payments = json_decode(file_get_contents($payment_file), true) ?: [];
    }
    
    $payments[] = $payment_record;
    file_put_contents($payment_file, json_encode($payments, JSON_PRETTY_PRINT));
    
    // ==========================================
    // ☁️ SUPABASE RECORDING
    // ==========================================
    require_once 'supabase_helper.php';
    try {
        $supabase = new SupabaseHelper();
        
        // Determine App Name (check order context if available, otherwise Direct)
        $app_name = 'Direct';
        require_once 'order_store.php';
        // Note: notify.php only has order_id. We updated process_payment to save context with order_id key too.
        $context = getOrderContext($order_id);
        if ($context && isset($context['app_name'])) {
            $app_name = $context['app_name'];
        }
        
        $sb_data = [
            'order_id' => $order_id,
            'amount' => $payhere_amount,
            'currency' => $payhere_currency,
            'status' => ($status_code == 2) ? 'success' : 'failed',
            'customer_name' => $card_holder_name ?: trim(($context['prefill']['full_name'] ?? '')), // Fallback to context name
            'customer_email' => $context['prefill']['email'] ?? '',
            'customer_phone' => $custom_2, // WhatsApp
            'payment_method' => $method ?: 'PayHere',
            'transaction_id' => $payment_id,
            'app_name' => $app_name,
            'meta_data' => [
                'status_code' => $status_code,
                'status_message' => $status_message,
                'note' => $custom_1,
                'mode' => isset($context['mode']) ? $context['mode'] : 'standalone'
            ]
        ];
        
        $supabase->recordPayment($sb_data);
        
    } catch (Exception $e) {
        $log_data = "Supabase Exception: " . $e->getMessage() . "\n";
        file_put_contents($log_file, $log_data, FILE_APPEND);
    }
    
    // Status codes: 2 = success, 0 = pending, -1 = canceled, -2 = failed, -3 = chargedback
    if ($status_code == 2) {
        // Payment successful
        $log_data = "Payment SUCCESSFUL - Order: {$order_id}, Amount: {$payhere_amount} {$payhere_currency}\n\n";
        file_put_contents($log_file, $log_data, FILE_APPEND);
        
        // ==========================================
        // 🚀 CLIENT NOTIFICATION FORWARDING
        // ==========================================
        require_once 'order_store.php';
        $contex_data = getOrderContext($order_id);
        
        if ($contex_data && isset($contex_data['notify_url'])) {
             $client_notify_url = $contex_data['notify_url'];
             
             // Forward the exact same POST data to the client
             $ch = curl_init($client_notify_url);
             curl_setopt($ch, CURLOPT_POST, 1);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST); // Pass through all PayHere data
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Don't hang forever
             // In local environments, SSL might fail
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
             curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
             
             $response = curl_exec($ch);
             $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
             $curl_error = curl_error($ch);
             curl_close($ch);
             
             $log_data = "Forwarding to Client: {$client_notify_url}\n";
             $log_data .= "Response: HTTP {$http_code} - " . ($response ?: $curl_error) . "\n\n";
             file_put_contents($log_file, $log_data, FILE_APPEND);
        }
        
        // Send SMS notification to customer (only for Sri Lanka +94 numbers)
        if (!empty($custom_2)) { // custom_2 contains WhatsApp number
            // Check if number starts with +94 (Sri Lanka)
            $is_sri_lanka = (strpos($custom_2, '+94') === 0 || strpos($custom_2, '94') === 0);
            
            if ($is_sri_lanka) {
                // Parse custom_1 to separate Original Amount and Note
                // Format: "AMT:1000|Note text" or "AMT:1000"
                $original_amount_display = $payhere_amount; // Fallback to total if parse fails
                $user_note = "";
                
                if (strpos($custom_1, 'AMT:') === 0) {
                    $parts = explode('|', $custom_1, 2);
                    $original_amount_display = substr($parts[0], 4); // Remove 'AMT:'
                    if (isset($parts[1])) {
                        $user_note = $parts[1];
                    }
                } else {
                    $user_note = $custom_1; // Fallback for old format
                }

                $sms_message = "Dear Customer,\n\n";
                $sms_message .= "Your payment of Rs. " . number_format((float)$original_amount_display, 2) . " (+ 3.9% fee) has been received successfully.\n\n";
                $sms_message .= "Total Charged: Rs. " . number_format($payhere_amount, 2) . "\n";
                $sms_message .= "Order ID: {$order_id}\n";
                $sms_message .= "Payment ID: {$payment_id}\n";
                $sms_message .= "Payment Method: {$method}\n\n";
                if (!empty($user_note)) {
                    $sms_message .= "Note: {$user_note}\n\n";
                }
                $sms_message .= "Thank you for your payment!\n";
                $sms_message .= "- " . BUSINESS_NAME;
                
                $sms_sent = sendSMS($custom_2, $sms_message);
                
                $log_data = "SMS Notification: " . ($sms_sent ? "SENT" : "FAILED") . " to {$custom_2}\n\n";
                file_put_contents($log_file, $log_data, FILE_APPEND);
            } else {
                $log_data = "SMS Notification: SKIPPED (Non-Sri Lanka number: {$custom_2})\n\n";
                file_put_contents($log_file, $log_data, FILE_APPEND);
            }
        }

        // ==========================================
        // 🔒 ADMIN NOTIFICATION (New Feature)
        // ==========================================
        $admin_phone = '94774665742'; // Admin Number
        
        // get customer name
        $customer_first_name = $_POST['first_name'] ?? 'Customer';
        $customer_last_name = $_POST['last_name'] ?? '';
        $customer_full_name = trim($customer_first_name . ' ' . $customer_last_name);
        
        // Parse note again if needed (it was parsed in customer block but scope might be an issue if customer sms skipped)
        // Re-parsing to be safe since variables above are inside the if(!empty($custom_2)) block
        $admin_note = "";
        $admin_original_amount = $payhere_amount;
        
        if (strpos($custom_1, 'AMT:') === 0) {
            $parts = explode('|', $custom_1, 2);
            $parsed_amount = substr($parts[0], 4);
            if (isset($parts[1])) {
                $admin_note = $parts[1];
            }
        } else {
            $admin_note = $custom_1;
        }

        $admin_msg = "New Payment Received!\n\n";
        $admin_msg .= "Customer: {$customer_full_name}\n";
        $admin_msg .= "Amount: Rs " . number_format($payhere_amount, 2) . "\n";
        if (!empty($admin_note)) {
            $admin_msg .= "Desc: {$admin_note}\n";
        }
        $admin_msg .= "Ref: {$order_id}";

        $admin_sms_sent = sendSMS($admin_phone, $admin_msg);
        
        $log_data = "Admin SMS: " . ($admin_sms_sent ? "SENT" : "FAILED") . " to {$admin_phone}\n\n";
        file_put_contents($log_file, $log_data, FILE_APPEND);
        
        // TODO: Update your database, send confirmation email, etc.
        
    } else {
        // Payment not successful
        $status_text = [
            '0' => 'PENDING',
            '-1' => 'CANCELED',
            '-2' => 'FAILED',
            '-3' => 'CHARGEDBACK'
        ];
        
        $log_data = "Payment " . ($status_text[$status_code] ?? 'UNKNOWN') . " - Order: {$order_id}\n\n";
        file_put_contents($log_file, $log_data, FILE_APPEND);
    }
    
    echo "OK"; // Always respond with OK to acknowledge receipt
} else {
    $log_data = "INVALID HASH - Possible fraud attempt!\n\n";
    file_put_contents($log_file, $log_data, FILE_APPEND);
    
    echo "INVALID";
}
?>
