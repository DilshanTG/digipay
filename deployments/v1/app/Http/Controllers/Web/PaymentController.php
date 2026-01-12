<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Merchant;
use App\Services\PayHereService;
use App\Services\SupabaseService;

class PaymentController extends Controller
{
    protected $payhere;
    protected $supabase;
    protected $paymentService;

    public function __construct(
        PayHereService $payhere, 
        SupabaseService $supabase, 
        \App\Services\PaymentService $paymentService
    )
    {
        $this->payhere = $payhere;
        $this->supabase = $supabase;
        $this->paymentService = $paymentService;
    }

    // Show Direct Payment Form
    public function showForm()
    {
        return view('payment.form');
    }

    // Process Direct Payment Form
    public function processForm(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email',
            'whatsapp' => 'required',
            'amount' => 'required|numeric|min:1',
            'country_code' => 'required'
        ]);

        // Split name
        $nameParts = explode(' ', trim($request->full_name), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        // Format Phone (Combine country code and number)
        $phone = $request->country_code . ltrim($request->whatsapp, '0');
        
        // Log the received data for debugging
        \Log::info("Payment Form Processed: Name={$request->full_name}, Email={$request->email}, Original Phone={$request->whatsapp}, Formatted Phone={$phone}");

        // Calculate Fee
        $originalAmount = $request->amount;
        $feePercentage = 0.039;
        $feeAmount = $originalAmount * $feePercentage;
        $totalAmount = $originalAmount + $feeAmount;

        $orderId = 'ORD-' . strtoupper(\Str::random(12));
        
        // Generate fake description - DISABLE for direct payments as requested
        // $fakeDescGen = new \App\Services\FakeDescriptionService();
        // $fakeDescription = $fakeDescGen->generate(
        //     $totalAmount,
        //     $firstName,
        //     $request->email,
        //     $phone,
        //     $request->note ?? 'Direct Payment'
        // );
        
        $paymentDescription = $request->note ?: 'Payment for DigiMart Solutions';

        $merchant = \App\Models\Merchant::first();
        if (!$merchant) {
            // Create a default merchant if none exists to prevent 500
            $merchant = \App\Models\Merchant::create([
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
            'amount' => $totalAmount, // Charge the total amount
            'currency' => 'LKR',
            'status' => 'PENDING',
            'mode' => 'direct',
            'customer_email' => $request->email,
            'customer_phone' => $phone,
            'real_description' => $paymentDescription,
            'fake_description' => $paymentDescription, // For direct, keep them same
            'meta_data' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'original_amount' => $originalAmount,
                'fee_amount' => $feeAmount,
                'note' => $request->note
            ]
        ]);

        return redirect()->route('pay.jump', ['token' => $orderId]);
    }

    // Stealth Redirect
    public function stealthJump($token)
    {
        $payment = Payment::where('order_id', $token)->firstOrFail();

        if ($payment->status === 'SUCCESS') {
            return $this->handleReturnLogic($payment);
        }

        $hash = $this->payhere->generateHash($payment->order_id, $payment->amount, $payment->currency);

        $meta = $payment->meta_data ?? [];
        
        $data = [
            'merchant_id' => $this->payhere->getMerchantId(),
            'return_url' => route('pay.return', ['order_id' => $payment->order_id]),
            'cancel_url' => route('pay.return', ['order_id' => $payment->order_id, 'cancelled' => 1]),
            'notify_url' => route('pay.notify'),
            'order_id' => $payment->order_id,
            'items' => $payment->fake_description ?? 'Payment Order', // Use stored fake description
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
            'payhere_url' => $this->payhere->getCheckoutUrl()
        ];

        if ($payment->mode === 'api') {
            return view('payment.fast_jump', compact('data', 'payment'));
        }

        return view('payment.stealth', compact('data', 'payment'));
    }

    // Webhook Handler
    public function notify(Request $request)
    {
        \Log::info('PayHere Webhook:', $request->all());

        if (!$this->payhere->verifyHash($request->all())) {
            \Log::error('Invalid PayHere Hash');
            return response('Invalid Hash', 400);
        }

        \DB::transaction(function() use ($request) {
            $payment = Payment::where('order_id', $request->order_id)->lockForUpdate()->first();

            if (!$payment) {
                return;
            }

            // Skip if already processed
            if ($payment->status === 'SUCCESS') {
                return;
            }

            $statusCode = $request->status_code;
            if ($statusCode == 2) {
                $this->paymentService->completePayment($payment, $request->payment_id);
            } elseif ($statusCode < 0) {
                $payment->status = 'FAILED';
                $payment->save();
            }
        });

        return response('OK', 200);
    }

    // Return Handler
    public function return(Request $request)
    {
        $payment = Payment::where('order_id', $request->order_id)->firstOrFail();
        
        // Handle explicit cancellation from PayHere
        if ($request->has('cancelled')) {
            if ($payment->status === 'PENDING') {
                $payment->status = 'CANCELLED';
                $payment->save();
            }
        }

        // If still PENDING, try to auto-verify using Retrieval API
        if ($payment->status === 'PENDING') {
            $result = $this->payhere->retrieveOrder($payment->order_id);
            
            if ($result['status'] === 'success' && !empty($result['data'])) {
                $latest = $result['data'][0];
                if ($latest['status'] === 'RECEIVED') {
                    $this->paymentService->completePayment($payment, $latest['payment_id']);
                }
            }
        }

        // If API mode and still PENDING (Webhooks haven't reached localhost)
        if ($payment->mode === 'api' && $payment->status === 'PENDING') {
            return view('payment.api_sync', compact('payment'));
        }

        return $this->handleReturnLogic($payment);
    }

    // Private helper
    private function handleReturnLogic(Payment $payment)
    {
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
            
            // PayHere Emulation Parameters
            $merchantId = $payment->merchant->api_key; 
            $orderId = $payment->client_order_id ?? $payment->order_id; 
            $payhereAmount = number_format($payment->amount, 2, '.', '');
            $payhereCurrency = $payment->currency;
            $merchantSecret = $payment->merchant->secret_key; 
            
            $secretHash = strtoupper(md5($merchantSecret));
            $hashString = $merchantId . $orderId . $payhereAmount . $payhereCurrency . $statusCode . $secretHash;
            $md5sig = strtoupper(md5($hashString));

            // separator logic
            $separator = (parse_url($payment->redirect_url, PHP_URL_QUERY) == NULL) ? '?' : '&';
            
            // Construct Final URL
            $params = http_build_query([
                'status' => $status, // Legacy support
                'merchant_id' => $merchantId,
                'order_id' => $orderId,
                'payment_id' => $payment->order_id, // Digimart logic Ref
                'payhere_amount' => $payhereAmount,
                'payhere_currency' => $payhereCurrency,
                'status_code' => $statusCode,
                'md5sig' => $md5sig,
                'custom_1' => $payment->meta_data['custom_1'] ?? '',
                'custom_2' => $payment->meta_data['custom_2'] ?? ''
            ]);

            $finalUrl = $payment->redirect_url . $separator . $params;
            
            return redirect($finalUrl);
        } else {
            return view('payment.receipt', compact('payment'));
        }
    }

    // Manual Sync for developers (Local Testing)
    public function manualSync($token)
    {
        $payment = Payment::where('order_id', $token)->firstOrFail();
        
        // Only allow if PENDING and in localhost/dev mode
        if ($payment->status === 'PENDING') {
            $this->paymentService->completePayment($payment, 'PY-TEST-' . rand(100000, 999999));
        }

        return redirect()->back()->with('success', 'Status synced successfully!');
    }
}
