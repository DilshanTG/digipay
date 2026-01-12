<?php
/**
 * DigiMart Pay - API Test Client (No cURL Required)
 * 
 * This file demonstrates how to integrate with the payment gateway API
 */

// ============================================
// CONFIGURATION
// ============================================
$API_URL = 'http://127.0.0.1:8000/api/v1';
$API_KEY = 'sk_live_9a4443d05794b06855bcc49c063af055'; // Replace with your API key from admin panel

// ============================================
// Helper function to make HTTP requests
// ============================================
function makeRequest($url, $method = 'GET', $data = null, $apiKey = null) {
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json'
    ];
    
    if ($apiKey) {
        $headers[] = 'Authorization: Bearer ' . $apiKey;
    }
    
    $options = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'ignore_errors' => true
        ]
    ];
    
    if ($data && $method === 'POST') {
        $options['http']['content'] = json_encode($data);
    }
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    // Get HTTP response code
    $httpCode = 200;
    if (isset($http_response_header[0])) {
        preg_match('{HTTP\/\S*\s(\d{3})}', $http_response_header[0], $match);
        $httpCode = isset($match[1]) ? (int)$match[1] : 200;
    }
    
    return [
        'code' => $httpCode,
        'body' => $response ? json_decode($response, true) : null,
        'raw' => $response
    ];
}

// ============================================
// EXAMPLE 1: Initialize a Payment
// ============================================
function initializePayment($apiUrl, $apiKey) {
    echo "=== INITIALIZING PAYMENT ===\n\n";
    
    $paymentData = [
        'amount' => 3500,
        'currency' => 'LKR',
        'return_url' => 'http://abc.com/payment-success',
        'client_order_id' => 'ORDER-' . time(),
        'customer_email' => 'john.doe@example.com',
        'customer_phone' => '0771234567',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'description' => 'Premium Logo Design Package', // Real description (saved in your DB)
        'meta_data' => [
            'product_id' => 'LOGO-001',
            'package' => 'premium'
        ]
    ];
    
    echo "Sending payment request...\n";
    echo "Amount: LKR " . number_format($paymentData['amount'], 2) . "\n";
    echo "Real Description: " . $paymentData['description'] . "\n";
    echo "(PayHere will see a random fake description)\n\n";
    
    $response = makeRequest($apiUrl . '/init', 'POST', $paymentData, $apiKey);
    
    echo "HTTP Status: {$response['code']}\n";
    echo "Response:\n";
    print_r($response['body']);
    
    if ($response['code'] === 200 && isset($response['body']['data']['payment_url'])) {
        echo "\n✅ Success! Redirect user to: " . $response['body']['data']['payment_url'] . "\n";
        return $response['body']['data']['order_id'];
    } else {
        echo "\n❌ Failed!\n";
        if (isset($response['body']['message'])) {
            echo "Error: " . $response['body']['message'] . "\n";
        }
        return null;
    }
}

// ============================================
// EXAMPLE 2: Check Payment Status
// ============================================
function checkPaymentStatus($apiUrl, $apiKey, $orderId) {
    echo "\n\n=== CHECKING PAYMENT STATUS ===\n\n";
    echo "Order ID: $orderId\n\n";
    
    $response = makeRequest($apiUrl . '/status/' . $orderId, 'GET', null, $apiKey);
    
    echo "HTTP Status: {$response['code']}\n";
    echo "Response:\n";
    print_r($response['body']);
    
    if ($response['code'] === 200 && isset($response['body']['data']['status'])) {
        echo "\nPayment Status: " . $response['body']['data']['status'] . "\n";
        
        switch ($response['body']['data']['status']) {
            case 'SUCCESS':
                echo "✅ Payment completed successfully!\n";
                if (isset($response['body']['data']['payhere_ref'])) {
                    echo "PayHere Reference: " . $response['body']['data']['payhere_ref'] . "\n";
                }
                break;
            case 'PENDING':
                echo "⏳ Payment is pending...\n";
                break;
            case 'FAILED':
                echo "❌ Payment failed!\n";
                break;
            default:
                echo "ℹ️  Status: " . $response['body']['data']['status'] . "\n";
        }
    }
}

// ============================================
// EXAMPLE 3: Test Different Amount Ranges
// ============================================
function testDifferentAmounts($apiUrl, $apiKey) {
    echo "\n\n=== TESTING FAKE DESCRIPTION GENERATOR ===\n\n";
    
    $tests = [
        [
            'amount' => 2500,
            'description' => 'Social Media Post Design',
            'range' => 'Under 5K (Graphics/SM Posts/Fliers/Logos)'
        ],
        [
            'amount' => 7500,
            'description' => 'Email Marketing Campaign',
            'range' => 'Under 10K (Email/SMS Campaigns)'
        ],
        [
            'amount' => 18000,
            'description' => 'Facebook Ad Campaign',
            'range' => 'Over 15K (Meta Ads/Websites/AI)'
        ]
    ];
    
    foreach ($tests as $index => $test) {
        echo "---\n";
        echo "Test " . ($index + 1) . ": {$test['range']}\n";
        echo "Amount: LKR " . number_format($test['amount'], 2) . "\n";
        echo "Real Description: {$test['description']}\n";
        
        $paymentData = [
            'amount' => $test['amount'],
            'return_url' => 'http://abc.com/success',
            'client_order_id' => 'TEST-' . time() . rand(100, 999),
            'customer_email' => 'test@example.com',
            'customer_phone' => '0771234567',
            'first_name' => 'Test',
            'last_name' => 'User',
            'description' => $test['description']
        ];
        
        $response = makeRequest($apiUrl . '/init', 'POST', $paymentData, $apiKey);
        
        if (isset($response['body']['data']['order_id'])) {
            echo "✅ Generated Order ID: {$response['body']['data']['order_id']}\n";
            echo "   Payment URL: {$response['body']['data']['payment_url']}\n";
        } else {
            echo "❌ Failed to create payment\n";
        }
        
        sleep(1); // Avoid rate limiting
        echo "\n";
    }
}

// ============================================
// RUN TESTS
// ============================================

echo "╔════════════════════════════════════════╗\n";
echo "║  DigiMart Pay - API Test Client       ║\n";
echo "╚════════════════════════════════════════╝\n\n";

echo "API Endpoint: $API_URL\n";
echo "API Key: " . substr($API_KEY, 0, 15) . "...\n\n";

// Test 1: Initialize a payment
$orderId = initializePayment($API_URL, $API_KEY);

// Test 2: Check payment status (if payment was created)
if ($orderId) {
    sleep(1);
    checkPaymentStatus($API_URL, $API_KEY, $orderId);
}

// Test 3: Test different amount ranges (uncomment to run)
echo "\n\n";
echo "Uncomment line 202 in test_api.php to test fake description generator\n";
// testDifferentAmounts($API_URL, $API_KEY);

echo "\n\n╔════════════════════════════════════════╗\n";
echo "║  Test Complete!                        ║\n";
echo "╚════════════════════════════════════════╝\n\n";

if ($orderId) {
    echo "Next Steps:\n";
    echo "1. Open the payment_url shown above in your browser\n";
    echo "2. You'll be redirected to PayHere Sandbox\n";
    echo "3. Use test card: 5111111111111118\n";
    echo "4. CVV: 123, Expiry: Any future date\n";
    echo "5. Complete payment\n";
    echo "6. Run this script again to check updated status\n\n";
}

?>
