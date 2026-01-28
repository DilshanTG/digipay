<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigiMart Pay - API Tester</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-slate-900 text-white p-6">
                <h1 class="text-2xl font-bold">API Integration Tester</h1>
                <p class="text-slate-400">Run live tests against your Payment Gateway API</p>
            </div>
            
            <div class="p-6">
                <?php
                // Detect Current Host for API calls
                $currentHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $API_URL = $currentHost . '/api/v1';
                    $API_KEY = $_POST['api_key'];
                    $AMOUNT = $_POST['amount'];
                    $DESCRIPTION = $_POST['description'];

                    echo '<div class="bg-slate-900 text-green-400 p-4 rounded-lg font-mono text-sm mb-6 overflow-x-auto">';
                    echo "🚀 Starting Test...\n";
                    echo "Target: $API_URL\n";
                    
                    // 1. Prepare Data
                    $data = [
                        'amount' => $AMOUNT,
                        'return_url' => $currentHost . '/test_success',
                        'client_order_id' => 'TEST-' . time(),
                        'customer_email' => 'test@example.com',
                        'customer_phone' => '0771234567',
                        'first_name' => 'Tester',
                        'last_name' => 'User',
                        'description' => $DESCRIPTION
                    ];
                    
                    echo "\n📤 Sending Payload:\n";
                    print_r($data);
                    
                    // 2. Send Request
                    $ch = curl_init($API_URL . '/init');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Bearer ' . $API_KEY,
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ]);
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    // 3. Rate Limit Warning
                    if ($httpCode === 429) {
                        echo "\n⚠️ Rate Limit Exceeded! Waiting 60s...\n";
                    }

                    echo "\n📥 Response (Status: $httpCode):\n";
                    $result = json_decode($response, true);
                    print_r($result);
                    
                    if (isset($result['data']['payment_url'])) {
                        $url = $result['data']['payment_url'];
                        echo "\n✅ Test Passed!\n";
                        echo "</div>";
                        
                        echo '<div class="bg-green-50 border border-green-200 p-4 rounded-lg text-center">';
                        echo '<p class="text-green-800 font-bold mb-2">Payment Session Created Successfully!</p>';
                        echo '<a href="'.$url.'" target="_blank" class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-bold">Pay Now (Opens PayHere) &rarr;</a>';
                        echo '</div>';
                    } else {
                        echo "\n❌ Test Failed.\n";
                        echo "</div>";
                    }
                } else {
                    // Default values
                    // Use a more robust way to get a merchant key if possible without full bootstrap
                    $defaultKey = 'YOUR_API_KEY';
                ?>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-2">API Key</label>
                        <input type="text" name="api_key" placeholder="sk_live_..." class="w-full border p-2 rounded font-mono text-sm" required>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-2">Amount (LKR)</label>
                            <input type="number" name="amount" value="3500" class="w-full border p-2 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Real Description</label>
                            <input type="text" name="description" value="Premium Logo Design" class="w-full border p-2 rounded">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700">
                        Run Test Request
                    </button>
                </form>
                <?php } ?>
            </div>
        </div>
        
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>This tool runs on your server and connects to <code><?php echo $currentHost; ?>/api/v1</code></p>
        </div>
    </div>
</body>
</html>
