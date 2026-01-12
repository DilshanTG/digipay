<?php
// Simulated Client Website (e.g., your store)
// Run this on port 8001: php -S localhost:8001

$gateway_url = 'http://localhost:8000/api/v1/init';
$api_key = 'sk_live_9a4443d05794b06855bcc49c063af055'; // Use a valid key from your admin panel

if (isset($_POST['buy'])) {
    $data = [
        'amount' => 2500,
        'currency' => 'LKR',
        'return_url' => 'http://localhost:8001/success.php',
        'client_order_id' => 'MYSTORE-' . time(),
        'customer_email' => 'client@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'description' => 'Adobe Premiere Pro Subscription'
    ];

    $ch = curl_init($gateway_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($http_code == 200 && isset($result['data']['payment_url'])) {
        header('Location: ' . $result['data']['payment_url']);
        exit;
    } else {
        echo "<h1>API Error</h1>";
        echo "<pre>" . print_r($result, true) . "</pre>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Test Store</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
        .btn { background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Adobe Premiere Pro</h1>
        <p>Price: Rs. 2,500.00</p>
        <form method="POST">
            <button type="submit" name="buy" class="btn">Buy Now (Test API)</button>
        </form>
        <p style="margin-top: 20px; font-size: 12px; color: #666;">This page is running on Port 8001</p>
    </div>
</body>
</html>
