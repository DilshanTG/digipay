<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - DigiMart Pay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
</head>
<body class="bg-gray-50">
    <nav class="bg-slate-900 text-white p-4 sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">DigiMart Pay Admin</h1>
            <div class="space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-300">Dashboard</a>
                <a href="{{ route('admin.merchants.index') }}" class="hover:text-slate-300">Merchants</a>
                <a href="{{ route('admin.payments') }}" class="hover:text-slate-300">Payments</a>
                <a href="{{ route('admin.settings') }}" class="hover:text-slate-300">Settings</a>
                <a href="{{ route('admin.docs') }}" class="text-blue-400 font-bold border-b-2 border-blue-400">API Docs</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-8 max-w-5xl">
        <h1 class="text-3xl font-bold mb-2">API Documentation</h1>
        <p class="text-gray-600 mb-8">Integration guide for DigiMart Pay Gateway (Stealth Mode)</p>

        <!-- Base URL -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Base URL</h2>
            <code class="bg-slate-900 text-green-400 px-4 py-2 rounded">POST {{ url('/api/v1/init') }}</code>
        </div>

        <!-- Authentication -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Authentication</h2>
            <p class="mb-4">Include your API Key in the Authorization header.</p>
            <pre><code class="language-bash">Authorization: Bearer sk_live_XXXXXXXXXXXXXXXXXXXXXX</code></pre>
        </div>

        <!-- Create Payment -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">1. Initialize Payment</h2>
            <p class="mb-4 text-gray-600">Create a new payment session. Returns a payment URL to redirect your customer.</p>
            
            <h3 class="font-bold mb-2">Request Body (JSON)</h3>
            <table class="w-full text-sm text-left text-gray-500 mb-4 border rounded">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-6 py-3">Parameter</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Required</th>
                        <th class="px-6 py-3">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white border-b">
                        <td class="px-6 py-4 font-mono font-bold">amount</td>
                        <td class="px-6 py-4">float</td>
                        <td class="px-6 py-4 text-green-600">Yes</td>
                        <td class="px-6 py-4">Payment amount (LKR)</td>
                    </tr>
                    <tr class="bg-white border-b">
                        <td class="px-6 py-4 font-mono font-bold">return_url</td>
                        <td class="px-6 py-4">string</td>
                        <td class="px-6 py-4 text-gray-400">Optional</td>
                        <td class="px-6 py-4">Required if not set in Merchant Settings</td>
                    </tr>
                    <tr class="bg-white border-b">
                        <td class="px-6 py-4 font-mono font-bold">client_order_id</td>
                        <td class="px-6 py-4">string</td>
                        <td class="px-6 py-4 text-green-600">Yes</td>
                        <td class="px-6 py-4">Your internal order ID</td>
                    </tr>
                    <tr class="bg-white border-b">
                        <td class="px-6 py-4 font-mono font-bold">customer_email</td>
                        <td class="px-6 py-4">string</td>
                        <td class="px-6 py-4 text-green-600">Yes</td>
                        <td class="px-6 py-4">Customer's email address</td>
                    </tr>
                    <tr class="bg-white border-b">
                        <td class="px-6 py-4 font-mono font-bold">description</td>
                        <td class="px-6 py-4">string</td>
                        <td class="px-6 py-4 text-gray-400">Optional</td>
                        <td class="px-6 py-4">Real description (Saved in DB, not shown to PayHere)</td>
                    </tr>
                </tbody>
            </table>

            <h3 class="font-bold mb-2">Example Request (cURL)</h3>
            <pre><code class="language-bash">curl -X POST {{ url('/api/v1/init') }} \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 1500,
    "return_url": "https://yourdomain.com/payment/complete",
    "client_order_id": "ORD-1001",
    "customer_email": "customer@example.com",
    "customer_phone": "0771234567",
    "first_name": "Saman",
    "last_name": "Perera",
    "description": "Premium Package",
    "meta_data": {
        "package_id": "PKG_123"
    }
  }'</code></pre>

            <h3 class="font-bold mb-2 mt-4">Success Response</h3>
            <pre><code class="language-json">{
  "status": "success",
  "data": {
    "order_id": "ORD-XXXXXXXX",
    "payment_url": "{{ url('/pay/jump/ORD-XXXXXXXX') }}"
  }
}</code></pre>
        </div>

        <!-- PayHere Emulation (New Section) -->
        <div class="bg-white rounded-lg shadow p-6 mb-8 border-l-4 border-purple-500">
            <h2 class="text-xl font-bold mb-4">✨ PayHere Emulation Mode</h2>
            <p class="mb-4 text-gray-700">DigiMart Pay now acts as a <strong>Drop-in Replacement for PayHere</strong>. The response (Return URL & Notify URL) contains the exact same parameters as PayHere.</p>
            
            <h3 class="font-bold mb-2">Response Parameters (GET/POST)</h3>
            <ul class="list-disc list-inside text-sm mb-4 bg-gray-50 p-4 rounded">
                <li><code>status</code> - <strong>SUCCESS</strong>, <strong>CANCELLED</strong>, <strong>FAILED</strong></li>
                <li><code>status_code</code> - <strong>2</strong> (SUCCESS), <strong>-1</strong> (CANCELLED), <strong>-2</strong> (FAILED)</li>
                <li><code>merchant_id</code> - Your DigiMart API Key</li>
                <li><code>order_id</code> - Your Client Order ID</li>
                <li><code>md5sig</code> - MD5 Signature for verification</li>
                <li><code>payhere_amount</code> - Transaction amount</li>
                <li><code>payhere_currency</code> - Currency (LKR)</li>
            </ul>

            <h3 class="font-bold mb-2 text-purple-700">🛡️ Verifying the Signature</h3>
            <p class="mb-2 text-sm">To verify the request is authentic, generate the MD5 signature using your <strong>DigiMart Merchant Secret</strong> (found in settings).</p>
            
            <pre><code class="language-php">
$merchantId = $_REQUEST['merchant_id'];
$orderId = $_REQUEST['order_id'];
$amount = $_REQUEST['payhere_amount'];
$currency = $_REQUEST['payhere_currency'];
$statusCode = $_REQUEST['status_code'];

// YOUR DIGIMART SECRET KEY
$secret = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx'; 

// 1. Hash the Secret
$secretHash = strtoupper(md5($secret));

// 2. Create the String
$hashString = $merchantId . $orderId . $amount . $currency . $statusCode . $secretHash;

// 3. Generate Signature
$localHash = strtoupper(md5($hashString));

if ($localHash === $_REQUEST['md5sig']) {
    // ✅ Valid Payment
}
            </code></pre>
        </div>

        <!-- Integration Helper -->
        <div class="bg-white rounded-lg shadow p-6 mb-8 border-l-4 border-blue-500">
            <h2 class="text-xl font-bold mb-4">🚀 Developer: Instant Integration Class</h2>
            <p class="mb-4 text-gray-700">Don't want to code from scratch? Copy this PHP class to integrate DigiMart Pay in 2 minutes.</p>

            <pre><code class="language-php">
/**
 * DigiMart Pay - Integration Helper
 * Save as: DigiMartAPI.php
 */
class DigiMartAPI {
    private $apiKey = 'YOUR_API_KEY'; // From Settings -> Merchants
    private $baseUrl = '{{ url('/api/v1') }}'; 

    public function createPayment($amount, $orderId, $customerEmail) {
        $url = $this->baseUrl . '/init';
        
        $data = [
            'amount' => $amount,
            'client_order_id' => $orderId,
            'customer_email' => $customerEmail,
            // 'return_url' => 'http://yoursite.com/success', // Optional: Overrides default
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}

// usage:
$api = new DigiMartAPI();
$res = $api->createPayment(1500, 'ORD-001', 'user@gmail.com');

if (isset($res['status']) && $res['status'] === 'success') {
    header("Location: " . $res['data']['payment_url']);
    exit;
} else {
    echo "Error: " . $res['message'] ?? 'Unknown Error';
}
            </code></pre>
        </div>

        <!-- Status Check -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">2. Check Status</h2>
            <p class="mb-4 text-gray-600">Check the status of a payment using the order ID returned from the init call.</p>
            
            <code class="bg-slate-900 text-green-400 px-4 py-2 rounded block w-full mb-4">GET {{ url('/api/v1/status/{order_id}') }}</code>

            <h3 class="font-bold mb-2 text-gray-700">The "Truth Check" API</h3>
            <p class="text-xs mb-4 p-3 bg-amber-50 text-amber-800 rounded border border-amber-200">
                <strong>🛡️ Security Standard:</strong> Do not trust the <code>status</code> parameter in the <code>return_url</code>. Always verify the status using this server-side API before delivering any digital product.
            </p>

            <h3 class="font-bold mb-2">Example Response</h3>
            <pre><code class="language-json">{
  "status": "success",
  "data": {
    "order_id": "ORD-N3K8J2... ",
    "status": "SUCCESS",   // PENDING, SUCCESS, CANCELLED, FAILED
    "amount": 2500.00,
    "currency": "LKR",
    "client_order_id": "MYSTORE-123", // Your internal reference
    "payhere_ref": "3200XXXXX",        // Official PayHere payment ID
    "updated_at": "2026-01-04T..."
  }
}</code></pre>
        </div>

        <!-- Fake Description Logic -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">🎭 Fake Description Output</h2>
            <p class="text-gray-600 mb-4">To protect your PayHere account, we automatically replace the description sent to PayHere with a "safe" human-like alternative. You can see the real description in the admin panel.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border p-4 rounded">
                    <h3 class="font-bold text-sm text-gray-500 mb-2">Under 5,000 LKR</h3>
                    <ul class="list-disc list-inside text-sm">
                        <li>Graphics Design</li>
                        <li>Social Media Posts</li>
                        <li>Flyers / Logos</li>
                    </ul>
                </div>
                <div class="border p-4 rounded">
                    <h3 class="font-bold text-sm text-gray-500 mb-2">Under 10,000 LKR</h3>
                    <ul class="list-disc list-inside text-sm">
                        <li>Email Templates</li>
                        <li>SMS Campaigns</li>
                        <li>Signatures</li>
                    </ul>
                </div>
                <div class="border p-4 rounded">
                    <h3 class="font-bold text-sm text-gray-500 mb-2">Over 15,000 LKR</h3>
                    <ul class="list-disc list-inside text-sm">
                        <li>Meta Ad Campaigns</li>
                        <li>Websites (5 pages)</li>
                        <li>AI Video Creation</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
