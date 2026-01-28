<?php

require __DIR__ . '/../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use Flight;
use App\Database;
use App\Models\{Payment, Merchant, Setting};
use App\Services\{
    PayHereService,
    PaymentService,
    SmsService,
    WhatsAppReceiptService,
    FakeDescriptionService,
    EmailService,
    Logger,
    HttpClient
};

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Initialize logger
Logger::init();

// Initialize database
Database::connect();

// Load configuration
$config = require __DIR__ . '/../config/app.php';
Flight::set('config', $config);

// Flight configuration
Flight::set('flight.views.path', __DIR__ . '/../views');


// Register services
Flight::register('payhere', PayHereService::class);
Flight::register('email', EmailService::class);
Flight::register('sms', SmsService::class);
Flight::register('receipts', WhatsAppReceiptService::class);
Flight::register('paymentService', PaymentService::class, [Flight::sms(), Flight::receipts(), Flight::email()]);
Flight::register('fakeDesc', FakeDescriptionService::class);

// Helper functions
function jsonResponse($data, $statusCode = 200) {
    Flight::json($data, $statusCode);
}

function generateOrderId() {
    return 'ORD-' . strtoupper(bin2hex(random_bytes(6)));
}

function url($path = '') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = Flight::request()->base ?? '';
    
    // Clean up base path (remove trailing slash)
    $base = rtrim($base, '/');
    
    // If base is just /, make it empty
    if ($base === '/') $base = '';

    return $protocol . '://' . $host . $base . '/' . ltrim($path, '/');
}

// ============================================
// API ROUTES (v1)
// ============================================

// DEBUG: routing
// Better Subfolder Detection
$request = Flight::request();
$scriptName = $_SERVER['SCRIPT_NAME'];
$requestUri = $_SERVER['REQUEST_URI'];

// Calculate base path dynamically
$base = str_replace('\\', '/', dirname($scriptName));
if (basename($base) === 'public') {
    $base = dirname($base);
}
// After str_replace, base is always forward-slash based, so check for '/' not DIRECTORY_SEPARATOR
if ($base === '/' || $base === '.') $base = '';

// If the URL matches the base but Flight doesn't see it (common with root .htaccess)
if (!empty($base) && strpos($requestUri, $base) === 0) {
    $request->base = $base;
    $request->url = substr($requestUri, strlen($base));
    if (empty($request->url)) $request->url = '/';
}
Flight::set('flight.base_url', $base);

// POST /api/v1/init - Initialize Payment
Flight::route('POST /api/v1/init', function() {
    $request = Flight::request();
    $data = $request->data->getData();

    // Validation
    $required = ['amount'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            jsonResponse(['status' => 'error', 'message' => "Field {$field} is required"], 422);
            return;
        }
    }

    if ($data['amount'] < 1) {
        jsonResponse(['status' => 'error', 'message' => 'Amount must be at least 1'], 422);
        return;
    }

    // Auth Merchant (Bearer Token)
    $authHeader = $request->headers['Authorization'] ?? $request->headers['authorization'] ?? '';
    preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches);
    $apiKey = $matches[1] ?? null;

    if (!$apiKey) {
        jsonResponse(['status' => 'error', 'message' => 'Missing API Key'], 401);
        return;
    }

    $merchant = Merchant::where('api_key', $apiKey);
    if (!$merchant || !$merchant->is_active) {
        jsonResponse(['status' => 'error', 'message' => 'Invalid API Key'], 401);
        return;
    }

    // Resolve URLs
    $returnUrl = $data['return_url'] ?? $merchant->return_url;
    $notifyUrl = $data['notify_url'] ?? $merchant->notify_url;

    if (!$returnUrl) {
        jsonResponse(['status' => 'error', 'message' => 'Return URL is required'], 422);
        return;
    }

    // Domain Whitelisting
    $origin = $request->headers['Origin'] ?? $request->headers['Referer'] ?? '';
    if ($origin) {
        $domain = parse_url($origin, PHP_URL_HOST) ?? parse_url($returnUrl, PHP_URL_HOST);
        $allowedDomains = $merchant->allowed_domains ?? [];

        if (!in_array('*', $allowedDomains) && !in_array($domain, $allowedDomains)) {
            jsonResponse(['status' => 'error', 'message' => 'Domain not whitelisted'], 403);
            return;
        }
    }

    // Create Payment Record
    try {
        $orderId = generateOrderId();

        // Generate fake description
        $fakeDesc = Flight::fakeDesc();
        $fakeDescription = $fakeDesc->generate(
            $data['amount'],
            $data['first_name'] ?? 'Customer',
            $data['customer_email'] ?? '',
            $data['customer_phone'] ?? '',
            $data['description'] ?? null
        );

        $payment = Payment::create([
            'merchant_id' => $merchant->id,
            'order_id' => $orderId,
            'client_order_id' => $data['client_order_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'LKR',
            'status' => 'PENDING',
            'redirect_url' => $returnUrl,
            'notify_url' => $notifyUrl,
            'mode' => 'api',
            'customer_email' => $data['customer_email'] ?? null,
            'customer_phone' => $data['customer_phone'] ?? null,
            'real_description' => $data['description'] ?? null,
            'fake_description' => $fakeDescription,
            'meta_data' => array_merge($data['meta_data'] ?? [], [
                'first_name' => $data['first_name'] ?? 'Customer',
                'last_name' => $data['last_name'] ?? ''
            ])
        ]);

        $stealthUrl = url('pay/jump/' . $payment->order_id);

        jsonResponse([
            'status' => 'success',
            'data' => [
                'order_id' => $payment->order_id,
                'payment_url' => $stealthUrl
            ]
        ]);
    } catch (Exception $e) {
        Logger::error('Payment Init Error: ' . $e->getMessage());
        jsonResponse(['status' => 'error', 'message' => 'Failed to create payment'], 500);
    }
});

// GET /api/v1/status/:order_id - Check Payment Status
Flight::route('GET /api/v1/status/@order_id', function($order_id) {
    $payment = Payment::where('order_id', $order_id);

    if (!$payment) {
        jsonResponse(['status' => 'error', 'message' => 'Order not found'], 404);
        return;
    }

    jsonResponse([
        'status' => 'success',
        'data' => [
            'order_id' => $payment->order_id,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'client_order_id' => $payment->client_order_id,
            'payhere_ref' => $payment->payhere_ref,
            'updated_at' => $payment->updated_at
        ]
    ]);
});

// ============================================
// WEB ROUTES
// ============================================

// GET / - Show Direct Payment Form
Flight::route('GET /', function() {
    Flight::render('payment/form');
});

// POST /pay/process - Process Direct Payment Form
Flight::route('POST /pay/process', function() {
    $request = Flight::request();
    $data = $request->data->getData();

    // Validation
    if (!isset($data['full_name']) || !isset($data['email']) || !isset($data['amount'])) {
        Flight::redirect('/');
        return;
    }

    // Split name
    $nameParts = explode(' ', trim($data['full_name']), 2);
    $firstName = $nameParts[0];
    $lastName = $nameParts[1] ?? '';

    // Format Phone
    $phone = ($data['country_code'] ?? '94') . ltrim($data['whatsapp'] ?? '', '0');

    Logger::info("Payment Form: Name={$data['full_name']}, Email={$data['email']}, Phone={$phone}");

    // Calculate Fee
    $originalAmount = $data['amount'];
    $feePercentage = 0.039;
    $feeAmount = $originalAmount * $feePercentage;
    $totalAmount = $originalAmount + $feeAmount;

    $orderId = generateOrderId();
    $paymentDescription = $data['note'] ?? 'Payment for DigiMart Solutions';

    // Get or create merchant
    $merchant = Merchant::find(1);
    if (!$merchant) {
        $merchant = Merchant::where('name', 'DigiMart System');
    }

    if (!$merchant) {
        try {
            $merchant = Merchant::create([
                'name' => 'DigiMart System',
                'api_key' => 'sk_live_' . bin2hex(random_bytes(16)),
                'secret_key' => bin2hex(random_bytes(32)),
                'allowed_domains' => ['*'],
                'is_active' => true
            ]);
        } catch (Exception $e) {
            Logger::error('Default Merchant Creation Failed: ' . $e->getMessage());
            Flight::halt(500, 'System Configuration Error: Default merchant could not be initialized.');
            return;
        }
    }

    // Ensure we have a valid merchant ID
    $merchantId = $merchant->id ?? $merchant->attributes['id'] ?? null;
    if (!$merchantId) {
        Logger::error('Merchant ID is null. Merchant data: ' . json_encode($merchant->toArray()));
        Flight::halt(500, 'System Configuration Error: Invalid merchant configuration.');
        return;
    }

    $payment = Payment::create([
        'merchant_id' => $merchantId,
        'order_id' => $orderId,
        'amount' => $totalAmount,
        'currency' => 'LKR',
        'status' => 'PENDING',
        'mode' => 'direct',
        'redirect_url' => $merchant->return_url,
        'notify_url' => $merchant->notify_url,
        'customer_email' => $data['email'],
        'customer_phone' => $phone,
        'real_description' => $paymentDescription,
        'fake_description' => $paymentDescription,
        'meta_data' => [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'original_amount' => $originalAmount,
            'fee_amount' => $feeAmount,
            'note' => $data['note'] ?? null
        ]
    ]);

    Flight::redirect('/pay/jump/' . $orderId);
});

// GET /pay/jump/:token - Stealth Jump to PayHere
Flight::route('GET /pay/jump/@token', function($token) {
    $payment = Payment::where('order_id', $token);

    if (!$payment) {
        Flight::halt(404, 'Payment not found');
        return;
    }

    if ($payment->status === 'SUCCESS') {
        handleReturnLogic($payment);
        return;
    }

    // Check if merchant is in sandbox mode
    $merchant = Merchant::find($payment->merchant_id);
    if ($merchant && $merchant->sandbox_mode) {
        // Show sandbox test page instead of redirecting to PayHere
        Flight::render('payment/sandbox_test', ['payment' => $payment]);
        return;
    }

    // Dynamic PayHere configuration based on Merchant settings
    $payhere = Flight::payhere()->forMerchant($payment->merchant());
    $hash = $payhere->generateHash($payment->order_id, $payment->amount, $payment->currency);

    $meta = $payment->meta_data ?? [];

    $data = [
        'merchant_id' => $payhere->getMerchantId(),
        'return_url' => url('return?order_id=' . $payment->order_id),
        'cancel_url' => url('return?order_id=' . $payment->order_id . '&cancelled=1'),
        'notify_url' => url('notify'),
        'order_id' => $payment->order_id,
        'items' => $payment->fake_description ?? 'Payment Order',
        'currency' => $payment->currency,
        'amount' => $payment->amount,
        'first_name' => $meta['first_name'] ?? 'Customer',
        'last_name' => $meta['last_name'] ?? '',
        'email' => $payment->customer_email ?? 'noreply@example.com',
        'phone' => $payment->customer_phone ?? '0777123456',
        'address' => 'Colombo',
        'city' => 'Colombo',
        'country' => 'Sri Lanka',
        'hash' => $hash,
        'payhere_url' => $payhere->getCheckoutUrl()
    ];

    $viewName = ($payment->mode === 'api') ? 'payment/fast_jump' : 'payment/stealth';
    Flight::render($viewName, ['data' => $data, 'payment' => $payment], 'body');
    Flight::render('layout');
});

// POST /notify - PayHere Webhook Handler
Flight::route('POST /notify', function() {
    $request = Flight::request();
    $postData = $request->data->getData();

    Logger::info('PayHere Webhook: ' . json_encode($postData));

    // 1. Lookup Payment First (to find the merchant)
    $payment = Payment::where('order_id', $postData['order_id'] ?? '');

    if (!$payment) {
        Flight::halt(404, 'Payment not found');
        return;
    }

    // 2. Configure PayHere for this Merchant
    $payhere = Flight::payhere()->forMerchant($payment->merchant());

    // 3. Verify Hash using the correct Merchant Secret
    if (!$payhere->verifyHash($postData)) {
        Logger::error('Invalid PayHere Hash for Order: ' . $postData['order_id']);
        Flight::halt(400, 'Invalid Hash');
        return;
    }

    if ($payment->status === 'SUCCESS') {
        echo 'OK';
        return;
    }

    $statusCode = $postData['status_code'] ?? 0;
    if ($statusCode == 2) {
        $paymentService = Flight::paymentService();
        $paymentService->completePayment($payment, $postData['payment_id'] ?? null);
    } elseif ($statusCode < 0) {
        $payment->status = 'FAILED';
        $payment->update();
    }

    echo 'OK';
});

// POST /sandbox/callback - Sandbox Test Payment Callback
Flight::route('POST /sandbox/callback', function() {
    $request = Flight::request();
    $postData = $request->data->getData();

    $orderId = $postData['order_id'] ?? '';
    $statusCode = intval($postData['status_code'] ?? 0);

    $payment = Payment::where('order_id', $orderId);

    if (!$payment) {
        Flight::halt(404, 'Payment not found');
        return;
    }

    // Verify merchant is in sandbox mode
    $merchant = Merchant::find($payment->merchant_id);
    if (!$merchant || !$merchant->sandbox_mode) {
        Flight::halt(403, 'Sandbox mode not enabled');
        return;
    }

    Logger::info("Sandbox Payment Test: Order={$orderId}, StatusCode={$statusCode}");

    // Generate a fake PayHere payment ID
    $fakePaymentId = 'PY-SANDBOX-' . strtoupper(bin2hex(random_bytes(4)));

    // Update payment based on status code
    if ($statusCode == 2) {
        // Success
        $paymentService = Flight::paymentService();
        $paymentService->completePayment($payment, $fakePaymentId);
    } elseif ($statusCode == -1) {
        // Cancelled
        $payment->status = 'CANCELLED';
        $payment->update();
    } elseif ($statusCode == -2) {
        // Failed
        $payment->status = 'FAILED';
        $payment->update();
    } elseif ($statusCode == 0) {
        // Keep pending (do nothing)
        Logger::info("Payment kept in PENDING state for testing");
    }

    // Redirect to return URL
    Flight::redirect('/return?order_id=' . $orderId . ($statusCode == -1 ? '&cancelled=1' : ''));
});

// GET /return - Return Handler
Flight::route('GET /return', function() {
    $request = Flight::request();
    $orderId = $request->query['order_id'] ?? '';

    $payment = Payment::where('order_id', $orderId);

    if (!$payment) {
        Flight::halt(404, 'Payment not found');
        return;
    }

    // Handle cancellation
    if (isset($request->query['cancelled'])) {
        if ($payment->status === 'PENDING') {
            $payment->status = 'CANCELLED';
            $payment->update();
        }
    }

    // Try auto-verify if still pending
    if ($payment->status === 'PENDING') {
        $payhere = Flight::payhere()->forMerchant($payment->merchant());
        $result = $payhere->retrieveOrder($payment->order_id);

        if ($result['status'] === 'success' && !empty($result['data'])) {
            $latest = $result['data'][0];
            if ($latest['status'] === 'RECEIVED') {
                $paymentService = Flight::paymentService();
                $paymentService->completePayment($payment, $latest['payment_id']);
            }
        }
    }

    // If API mode and still PENDING
    if ($payment->mode === 'api' && $payment->status === 'PENDING') {
        Flight::render('payment/api_sync', ['payment' => $payment], 'body');
        Flight::render('layout');
        return;
    }

    handleReturnLogic($payment);
});

// GET /pay/sync/:token - Manual Sync (Dev Testing)
Flight::route('GET /pay/sync/@token', function($token) {
    $payment = Payment::where('order_id', $token);

    if (!$payment) {
        Flight::halt(404, 'Payment not found');
        return;
    }

    if ($payment->status === 'PENDING') {
        $paymentService = Flight::paymentService();
        $paymentService->completePayment($payment, 'PY-TEST-' . rand(100000, 999999));
    }

    Flight::redirect('/return?order_id=' . $token);
});

// Helper function for return logic
function handleReturnLogic($payment) {
    if ($payment->mode === 'api') {
        $status = 'failed';
        $statusCode = -2;

        if ($payment->status === 'SUCCESS') {
            $status = 'SUCCESS';
            $statusCode = 2;
        } elseif ($payment->status === 'CANCELLED') {
            $status = 'CANCELLED';
            $statusCode = -1;
        } else {
            $status = 'FAILED';
            $statusCode = -2;
        }

        $merchant = $payment->merchant();
        $merchantId = $merchant->api_key;
        $orderId = $payment->client_order_id ?? $payment->order_id;
        $payhereAmount = number_format($payment->amount, 2, '.', '');
        $payhereCurrency = $payment->currency;
        $merchantSecret = $merchant->secret_key;

        $secretHash = strtoupper(md5($merchantSecret));
        $hashString = $merchantId . $orderId . $payhereAmount . $payhereCurrency . $statusCode . $secretHash;
        $md5sig = strtoupper(md5($hashString));

        $separator = (parse_url($payment->redirect_url, PHP_URL_QUERY) == NULL) ? '?' : '&';

        $meta = $payment->meta_data ?? [];
        $params = http_build_query([
            'status' => $status,
            'merchant_id' => $merchantId,
            'order_id' => $orderId,
            'payment_id' => $payment->order_id,
            'payhere_amount' => $payhereAmount,
            'payhere_currency' => $payhereCurrency,
            'status_code' => $statusCode,
            'md5sig' => $md5sig,
            'custom_1' => $meta['custom_1'] ?? '',
            'custom_2' => $meta['custom_2'] ?? ''
        ]);

        $finalUrl = $payment->redirect_url . $separator . $params;
        Flight::redirect($finalUrl);
    } else {
        Flight::render('payment/receipt', ['payment' => $payment], 'body');
        Flight::render('layout');
    }
}

// ============================================
// ADMIN ROUTES (Session Auth)
// ============================================

// Admin Login Route
Flight::route('GET /admin/login', function() {
    if (isset($_SESSION['admin_logged_in'])) {
        Flight::redirect('/admin');
        return;
    }
    Flight::render('admin/login');
});

// Admin Login Process
Flight::route('POST /admin/login', function() {
    $password = Flight::request()->data->password;
    $config = Flight::get('config');
    $adminPassword = $config['admin_password'] ?? 'admin123';

    if ($password === $adminPassword) {
        $_SESSION['admin_logged_in'] = true;
        Flight::redirect('/admin');
    } else {
        Flight::render('admin/login', ['error' => 'Invalid system password']);
    }
});

// Admin Logout
Flight::route('GET /admin/logout', function() {
    unset($_SESSION['admin_logged_in']);
    Flight::redirect('/admin/login');
});

// Admin authentication middleware
Flight::route('/admin*', function() {
    $url = Flight::request()->url;
    
    // Exclude login page from filter
    if (strpos($url, '/admin/login') === 0) {
        return true;
    }

    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        Flight::redirect('/admin/login');
        return false;
    }

    return true;
}, true);

// GET /admin - Dashboard
Flight::route('GET /admin', function() {
    $payments = Payment::all();
    $merchants = Merchant::all();
    
    $stats = [
        'revenue_today' => 0,
        'revenue_week' => 0,
        'revenue_month' => 0,
        'revenue_total' => 0,
        'total_payments' => count($payments),
        'successful' => 0,
        'pending' => 0,
        'failed' => 0,
        'total_merchants' => count($merchants)
    ];

    $today = date('Y-m-d');
    $week_ago = date('Y-m-d', strtotime('-7 days'));
    $month_start = date('Y-m-01');

    $merchantBreakdown = [];
    foreach ($merchants as $m) {
        $merchantBreakdown[$m->id] = (object)[
            'name' => $m->name,
            'total_revenue' => 0,
            'payment_count' => 0
        ];
    }

    foreach ($payments as $p) {
        if ($p->status === 'SUCCESS') {
            $stats['revenue_total'] += $p->amount;
            $stats['successful']++;
            
            $created_at = date('Y-m-d', strtotime($p->created_at));
            if ($created_at === $today) $stats['revenue_today'] += $p->amount;
            if ($created_at >= $week_ago) $stats['revenue_week'] += $p->amount;
            if ($created_at >= $month_start) $stats['revenue_month'] += $p->amount;

            if (isset($merchantBreakdown[$p->merchant_id])) {
                $merchantBreakdown[$p->merchant_id]->total_revenue += $p->amount;
                $merchantBreakdown[$p->merchant_id]->payment_count++;
            }
        } elseif ($p->status === 'PENDING') {
            $stats['pending']++;
        } else {
            $stats['failed']++;
        }
    }

    // Sort recent payments
    usort($payments, fn($a, $b) => strcmp($b->created_at, $a->created_at));
    $recentPayments = array_slice($payments, 0, 10);

    Flight::render('admin/dashboard', [
        'stats' => $stats,
        'merchantBreakdown' => array_values($merchantBreakdown),
        'recentPayments' => $recentPayments
    ]);
});

// GET /admin/payments - Payment List
Flight::route('GET /admin/payments', function() {
    $payments = Payment::all();
    Flight::render('admin/payments', ['payments' => $payments]);
});

// GET /admin/payments/check/@order_id - Manual PayHere Status Check
Flight::route('GET /admin/payments/check/@order_id', function($order_id) {
    $payment = Payment::where('order_id', $order_id);
    if (!$payment) {
        Flight::redirect('/admin/payments');
        return;
    }

    $payhere = Flight::payhere()->forMerchant($payment->merchant());
    $result = $payhere->retrieveOrder($payment->order_id);

    if ($result['status'] === 'success' && !empty($result['data'])) {
        $latest = $result['data'][0];
        if ($latest['status'] === 'RECEIVED' && $payment->status !== 'SUCCESS') {
            $paymentService = Flight::paymentService();
            $paymentService->completePayment($payment, $latest['payment_id']);
        }
    }

    Flight::redirect('/admin/payments');
});

// GET /admin/payments/invoice/@order_id - View Invoice/Receipt
Flight::route('GET /admin/payments/invoice/@order_id', function($order_id) {
    $payment = Payment::where('order_id', $order_id);
    if (!$payment) {
        Flight::halt(404, 'Invoice not found');
        return;
    }
    Flight::render('payment/receipt', ['payment' => $payment]);
});

// GET /admin/payments/delete/@order_id - Delete Payment
Flight::route('GET /admin/payments/delete/@order_id', function($order_id) {
    $payment = Payment::where('order_id', $order_id);
    if ($payment) {
        $payment->delete();
    }
    Flight::redirect('/admin/payments');
});

// GET /admin/merchants - Merchant List
Flight::route('GET /admin/merchants', function() {
    $merchants = Merchant::all();
    Flight::render('admin/merchants', ['merchants' => $merchants]);
});

// POST /admin/merchants/save - Create or Update Merchant
Flight::route('POST /admin/merchants/save', function() {
    $data = Flight::request()->data->getData();

    $isUpdate = (isset($data['id']) && !empty($data['id']));

    // Debug: log incoming checkbox states
    Logger::info("Merchant Save - sandbox_mode in data: " . (isset($data['sandbox_mode']) ? 'YES='.$data['sandbox_mode'] : 'NO'));
    Logger::info("Merchant Save - is_active in data: " . (isset($data['is_active']) ? 'YES='.$data['is_active'] : 'NO'));

    $merchantData = [
        'name' => $data['name'] ?? '',
        'allowed_domains' => array_map('trim', explode(',', $data['allowed_domains'] ?? '*')),
        'return_url' => $data['return_url'] ?? null,
        'cancel_url' => $data['cancel_url'] ?? null,
        'notify_url' => $data['notify_url'] ?? null,
        'is_active' => isset($data['is_active']) ? 1 : 0,
        'sandbox_mode' => isset($data['sandbox_mode']) ? 1 : 0
    ];

    Logger::info("Merchant Save - Final sandbox_mode value: " . $merchantData['sandbox_mode']);

    if ($isUpdate) {
        $merchant = Merchant::find($data['id']);
        if ($merchant) {
            Logger::info("Merchant Update - ID: {$data['id']}, Current sandbox_mode in DB: " . var_export($merchant->sandbox_mode, true));

            // Only update keys if explicitly provided (though usually handled via regenerate)
            if (!empty($data['api_key'])) $merchantData['api_key'] = $data['api_key'];
            if (!empty($data['secret_key'])) $merchantData['secret_key'] = $data['secret_key'];

            $result = $merchant->update($merchantData);
            Logger::info("Merchant Update - Result: " . ($result ? 'SUCCESS' : 'FAILED'));

            // Verify the update worked
            $verify = Merchant::find($data['id']);
            Logger::info("Merchant Update - Verification after reload: sandbox_mode = " . var_export($verify->sandbox_mode, true));
        }
    } else {
        // Auto-generate keys for new merchant (Matching Laravel exactly)
        $merchantData['api_key'] = 'sk_live_' . bin2hex(random_bytes(16));
        $merchantData['secret_key'] = bin2hex(random_bytes(32));
        
        Merchant::create($merchantData);
    }

    Flight::redirect('/admin/merchants');
});

// GET /admin/merchants/regenerate/@id - Regenerate Merchant Keys
Flight::route('GET /admin/merchants/regenerate/@id', function($id) {
    $merchant = Merchant::find($id);
    if ($merchant) {
        $merchant->api_key = 'sk_live_' . bin2hex(random_bytes(16));
        $merchant->secret_key = bin2hex(random_bytes(32));
        $merchant->update();
    }
    Flight::redirect('/admin/merchants');
});

// GET /admin/merchants/toggle/@id - Toggle Merchant Status
Flight::route('GET /admin/merchants/toggle/@id', function($id) {
    $merchant = Merchant::find($id);
    if ($merchant) {
        $merchant->is_active = !$merchant->is_active;
        $merchant->update();
    }
    Flight::redirect('/admin/merchants');
});

// GET /admin/merchants/delete/@id - Delete Merchant
Flight::route('GET /admin/merchants/delete/@id', function($id) {
    if ($id != 1) { // Prevent deleting default merchant
        $merchant = Merchant::find($id);
        if ($merchant) {
            $merchant->delete();
        }
    }
    Flight::redirect('/admin/merchants');
});

// GET /admin/settings - Settings
Flight::route('GET /admin/settings', function() {
    $allSettings = Setting::all();
    $dbSettings = [];
    foreach ($allSettings as $s) {
        $dbSettings[$s->key] = $s->value;
    }

    $settings = [
        'payhere_mode' => $dbSettings['payhere_mode'] ?? 'sandbox',
        'payhere_merchant_id_sandbox' => $dbSettings['payhere_merchant_id_sandbox'] ?? '',
        'payhere_secret_sandbox_localhost' => $dbSettings['payhere_secret_sandbox_localhost'] ?? '',
        'payhere_secret_sandbox_digimartstore' => $dbSettings['payhere_secret_sandbox_digimartstore'] ?? '',
        'sandbox_domain_selection' => $dbSettings['sandbox_domain_selection'] ?? 'localhost',
        'payhere_merchant_id_live' => $dbSettings['payhere_merchant_id_live'] ?? '',
        'payhere_secret_live' => $dbSettings['payhere_secret_live'] ?? '',
        'payhere_app_id_sandbox' => $dbSettings['payhere_app_id_sandbox'] ?? '',
        'payhere_app_secret_sandbox' => $dbSettings['payhere_app_secret_sandbox'] ?? '',
        'payhere_app_id_live' => $dbSettings['payhere_app_id_live'] ?? '',
        'payhere_app_secret_live' => $dbSettings['payhere_app_secret_live'] ?? '',
        'fake_descriptions_under_5k' => $dbSettings['fake_descriptions_under_5k'] ?? '',
        'fake_descriptions_under_10k' => $dbSettings['fake_descriptions_under_10k'] ?? '',
        'fake_descriptions_over_10k' => $dbSettings['fake_descriptions_over_10k'] ?? '',
    ];

    Flight::render('admin/settings', ['settings' => $settings]);
});

// POST /admin/config/update - Update Settings
Flight::route('POST /admin/config/update', function() {
    $data = Flight::request()->data->getData();
    
    if (isset($data['settings']) && is_array($data['settings'])) {
        foreach ($data['settings'] as $key => $value) {
            Setting::set($key, $value);
        }
    }
    
    Flight::redirect('/admin/settings');
});

// GET /admin/docs - API Documentation
Flight::route('GET /admin/docs', function() {
    Flight::render('admin/docs');
});

// Start the application
Flight::start();
