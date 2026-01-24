<?php

require __DIR__ . '/../../vendor/autoload.php';

use Flight;
use App\Database;
use App\Models\{Payment, Merchant, Setting};
use App\Services\{
    PayHereService,
    PaymentService,
    SmsService,
    WhatsAppReceiptService,
    FakeDescriptionService,
    Logger,
    HttpClient
};

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->safeLoad();

// Initialize logger
Logger::init();

// Initialize database
Database::connect();

// Load configuration
$config = require __DIR__ . '/../config/app.php';
Flight::set('config', $config);

// Set views path
Flight::set('flight.views.path', __DIR__ . '/../views');

// Register services
Flight::register('payhere', PayHereService::class);
Flight::register('sms', SmsService::class);
Flight::register('receipts', WhatsAppReceiptService::class);
Flight::register('paymentService', PaymentService::class, [Flight::sms(), Flight::receipts()]);
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
    return $protocol . '://' . $host . '/' . ltrim($path, '/');
}

// ============================================
// API ROUTES (v1)
// ============================================

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
    Flight::render('payment/form', [], 'body');
    Flight::render('layout');
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
        $merchant = Merchant::create([
            'name' => 'DigiMart System',
            'api_key' => 'sk_live_' . bin2hex(random_bytes(16)),
            'secret_key' => bin2hex(random_bytes(32)),
            'allowed_domains' => ['*'],
            'is_active' => true
        ]);
    }

    $payment = Payment::create([
        'merchant_id' => $merchant->id,
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

    $payhere = Flight::payhere();
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

    $payhere = Flight::payhere();
    if (!$payhere->verifyHash($postData)) {
        Logger::error('Invalid PayHere Hash');
        Flight::halt(400, 'Invalid Hash');
        return;
    }

    $payment = Payment::where('order_id', $postData['order_id'] ?? '');

    if (!$payment) {
        Flight::halt(404, 'Payment not found');
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
        $payhere = Flight::payhere();
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

        $params = http_build_query([
            'status' => $status,
            'merchant_id' => $merchantId,
            'order_id' => $orderId,
            'payment_id' => $payment->order_id,
            'payhere_amount' => $payhereAmount,
            'payhere_currency' => $payhereCurrency,
            'status_code' => $statusCode,
            'md5sig' => $md5sig,
            'custom_1' => $payment->meta_data['custom_1'] ?? '',
            'custom_2' => $payment->meta_data['custom_2'] ?? ''
        ]);

        $finalUrl = $payment->redirect_url . $separator . $params;
        Flight::redirect($finalUrl);
    } else {
        Flight::render('payment/receipt', ['payment' => $payment], 'body');
        Flight::render('layout');
    }
}

// ============================================
// ADMIN ROUTES (Basic Auth)
// ============================================

// Admin authentication middleware
Flight::route('GET /admin*', function() {
    $config = Flight::get('config');
    $adminPassword = $config['admin_password'] ?? 'admin123';

    if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_PW'] !== $adminPassword) {
        header('WWW-Authenticate: Basic realm="Admin Area"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Authentication required';
        return false;
    }

    return true;
}, true);

// GET /admin - Dashboard
Flight::route('GET /admin', function() {
    $payments = Payment::all();
    $totalAmount = array_sum(array_map(fn($p) => $p->status === 'SUCCESS' ? $p->amount : 0, $payments));
    $successCount = count(array_filter($payments, fn($p) => $p->status === 'SUCCESS'));

    Flight::render('admin/dashboard', [
        'payments' => $payments,
        'totalAmount' => $totalAmount,
        'successCount' => $successCount
    ], 'body');
    Flight::render('layout');
});

// GET /admin/payments - Payment List
Flight::route('GET /admin/payments', function() {
    $payments = Payment::all();
    Flight::render('admin/payments', ['payments' => $payments], 'body');
    Flight::render('layout');
});

// GET /admin/merchants - Merchant List
Flight::route('GET /admin/merchants', function() {
    $merchants = Merchant::all();
    Flight::render('admin/merchants', ['merchants' => $merchants], 'body');
    Flight::render('layout');
});

// GET /admin/settings - Settings
Flight::route('GET /admin/settings', function() {
    $settings = Setting::all();
    Flight::render('admin/settings', ['settings' => $settings], 'body');
    Flight::render('layout');
});

// Start the application
Flight::start();
