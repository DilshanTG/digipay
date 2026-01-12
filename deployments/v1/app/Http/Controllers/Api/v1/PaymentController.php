<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merchant;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function init(Request $request)
    {
        // 1. Validate Request
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'return_url' => 'nullable|url',
            'notify_url' => 'nullable|url',
            'client_order_id' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'currency' => 'nullable|string|in:LKR,USD',
            'description' => 'nullable|string|max:255', // Real description
            'meta_data' => 'nullable|array'
        ]);

        // 2. Auth Merchant (Bearer Token with Caching)
        $apiKey = $request->bearerToken();
        if (!$apiKey) {
            return response()->json(['status' => 'error', 'message' => 'Missing API Key'], 401);
        }

        // Cache API key lookup for 1 hour
        $merchant = \Cache::remember("merchant:{$apiKey}", 3600, function() use ($apiKey) {
            return Merchant::where('api_key', $apiKey)->where('is_active', true)->first();
        });

        if (!$merchant) {
            return response()->json(['status' => 'error', 'message' => 'Invalid API Key'], 401);
        }

        // 2.5 Resolve URLs
        $returnUrl = $request->return_url ?? $merchant->return_url;
        $notifyUrl = $request->notify_url ?? $merchant->notify_url;

        if (!$returnUrl) {
            return response()->json(['status' => 'error', 'message' => 'Return URL is required (not set in request or merchant settings)'], 422);
        }

        // 3. Domain Whitelisting Check
        $origin = $request->header('Origin') ?? $request->header('Referer');
        if ($origin) {
            $domain = parse_url($origin, PHP_URL_HOST) ?? parse_url($request->return_url, PHP_URL_HOST);
            
            // Check if domain is whitelisted (unless merchant has wildcard)
            if (!in_array('*', $merchant->allowed_domains ?? []) && 
                !in_array($domain, $merchant->allowed_domains ?? [])) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Domain not whitelisted for this API key'
                ], 403);
            }
        }

        // 4. Create Payment Record (Atomic Transaction)
        try {
            $payment = \DB::transaction(function() use ($request, $merchant, $returnUrl, $notifyUrl) {
                $orderId = 'ORD-' . strtoupper(Str::random(12));
                
                // Generate fake description
                $fakeDescGen = new \App\Services\FakeDescriptionService();
                $fakeDescription = $fakeDescGen->generate(
                    $request->amount,
                    $request->first_name ?? 'Customer',
                    $request->customer_email,
                    $request->customer_phone,
                    $request->description
                );

                return Payment::create([
                    'merchant_id' => $merchant->id,
                    'order_id' => $orderId,
                    'client_order_id' => $request->client_order_id,
                    'amount' => $request->amount,
                    'currency' => $request->currency ?? 'LKR',
                    'status' => 'PENDING',
                    'redirect_url' => $returnUrl,
                    'notify_url' => $notifyUrl,
                    'mode' => 'api',
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'real_description' => $request->description,
                    'fake_description' => $fakeDescription, // Persist fake description
                    'meta_data' => array_merge($request->meta_data ?? [], [
                        'first_name' => $request->first_name ?? 'Customer',
                        'last_name' => $request->last_name ?? ''
                    ])
                ]);
            });

            // 5. Return The Stealth Link
            $stealthUrl = route('pay.jump', ['token' => $payment->order_id]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'order_id' => $payment->order_id,
                    'payment_url' => $stealthUrl
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment Init Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create payment'
            ], 500);
        }
    }

    public function status($order_id)
    {
        $payment = Payment::where('order_id', $order_id)->first();

        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        return response()->json([
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
    }
}
