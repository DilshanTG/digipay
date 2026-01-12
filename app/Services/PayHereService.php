<?php

namespace App\Services;

class PayHereService
{
    protected $merchantId;
    protected $merchantSecret;
    protected $isSandbox;
    protected $appId;
    protected $appSecret;

    public function __construct()
    {
        $mode = \App\Models\Setting::get('payhere_mode', env('PAYHERE_MODE', 'live'));
        $this->isSandbox = $mode === 'sandbox';

        if ($this->isSandbox) {
            $this->merchantId = \App\Models\Setting::get('payhere_merchant_id_sandbox', env('PAYHERE_MERCHANT_ID'));
            
            $domainSelection = \App\Models\Setting::get('sandbox_domain_selection', 'localhost');
            if ($domainSelection === 'digimartstore') {
                $this->merchantSecret = \App\Models\Setting::get('payhere_secret_sandbox_digimartstore');
            } else {
                $this->merchantSecret = \App\Models\Setting::get('payhere_secret_sandbox_localhost');
            }
        } else {
            $this->merchantId = \App\Models\Setting::get('payhere_merchant_id_live', env('PAYHERE_MERCHANT_ID'));
            $this->merchantSecret = \App\Models\Setting::get('payhere_secret_live', env('PAYHERE_MERCHANT_SECRET'));
        }

        // App Credentials for Retrieval API
        if ($this->isSandbox) {
            $this->appId = \App\Models\Setting::get('payhere_app_id_sandbox');
            $this->appSecret = \App\Models\Setting::get('payhere_app_secret_sandbox');
        } else {
            $this->appId = \App\Models\Setting::get('payhere_app_id_live');
            $this->appSecret = \App\Models\Setting::get('payhere_app_secret_live');
        }
    }

    public function getCheckoutUrl()
    {
        return $this->isSandbox 
            ? 'https://sandbox.payhere.lk/pay/checkout' 
            : 'https://www.payhere.lk/pay/checkout';
    }

    public function getMerchantId()
    {
        return $this->merchantId;
    }

    public function generateHash($orderId, $amount, $currency = 'LKR')
    {
        $amountFormatted = number_format($amount, 2, '.', '');
        
        $hashObj = strtoupper(
            md5(
                $this->merchantId . 
                $orderId . 
                $amountFormatted . 
                $currency . 
                strtoupper(md5($this->merchantSecret)) 
            )
        );

        return $hashObj;
    }

    public function verifyHash($postData)
    {
        $merchantId = $postData['merchant_id'] ?? '';
        $orderId = $postData['order_id'] ?? '';
        $amount = $postData['payhere_amount'] ?? '';
        $currency = $postData['payhere_currency'] ?? '';
        $statusCode = $postData['status_code'] ?? '';
        $md5sig = $postData['md5sig'] ?? '';

        $amountFormatted = number_format($amount, 2, '.', '');

        $localMd5sig = strtoupper(
            md5(
                $merchantId . 
                $orderId . 
                $amountFormatted . 
                $currency . 
                $statusCode . 
                strtoupper(md5($this->merchantSecret))
            )
        );

        return $localMd5sig === $md5sig;
    }

    /**
     * Get OAuth Access Token for Retrieval API
     */
    public function getAccessToken()
    {
        if (!$this->appId || !$this->appSecret) {
            return null;
        }

        $url = $this->isSandbox 
            ? 'https://sandbox.payhere.lk/merchant/v1/oauth/token' 
            : 'https://www.payhere.lk/merchant/v1/oauth/token';

        $authCode = base64_encode($this->appId . ':' . $this->appSecret);

        $domain = request()->getHost() ?: 'localhost';
        $referer = (request()->isSecure() ? 'https://' : 'http://') . $domain;

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Basic ' . $authCode,
            'Referer' => $referer,
            'Origin' => $referer
        ])->asForm()->post($url, [
            'grant_type' => 'client_credentials'
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'] ?? null;
        }

        \Illuminate\Support\Facades\Log::error('PayHere OAuth Error: ' . $response->body());
        return null;
    }

    /**
     * Retrieve Order Status from PayHere API
     */
    public function retrieveOrder($orderId)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['status' => 'error', 'message' => 'Failed to obtain access token'];
        }

        $url = $this->isSandbox
            ? "https://sandbox.payhere.lk/merchant/v1/payment/search?order_id={$orderId}"
            : "https://www.payhere.lk/merchant/v1/payment/search?order_id={$orderId}";

        $domain = request()->getHost() ?: 'localhost';
        $referer = (request()->isSecure() ? 'https://' : 'http://') . $domain;

        $response = \Illuminate\Support\Facades\Http::withToken($token)
            ->withHeaders([
                'Referer' => $referer,
                'Origin' => $referer
            ])
            ->acceptJson()
            ->get($url);

        if ($response->successful()) {
            $data = $response->json();
            
            // PayHere returns an array of payments for that order_id
            if (isset($data['status']) && $data['status'] == 1 && !empty($data['data'])) {
                return ['status' => 'success', 'data' => $data['data']];
            }
            
            return ['status' => 'error', 'message' => 'No payment found for this order ID in PayHere'];
        }

        return ['status' => 'error', 'message' => 'PayHere API Error: ' . $response->status()];
    }
}
