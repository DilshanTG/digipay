<?php

namespace App\Services;

use App\Models\Setting;

class PayHereService
{
    protected $merchantId;
    protected $merchantSecret;
    protected $isSandbox;
    protected $appId;
    protected $appSecret;

    public function __construct()
    {
        $mode = Setting::get('payhere_mode', $_ENV['PAYHERE_MODE'] ?? 'live');
        $this->isSandbox = $mode === 'sandbox';

        if ($this->isSandbox) {
            $this->merchantId = Setting::get('payhere_merchant_id_sandbox', $_ENV['PAYHERE_MERCHANT_ID'] ?? '');

            $domainSelection = Setting::get('sandbox_domain_selection', 'localhost');
            if ($domainSelection === 'digimartstore') {
                $this->merchantSecret = Setting::get('payhere_secret_sandbox_digimartstore');
            } else {
                $this->merchantSecret = Setting::get('payhere_secret_sandbox_localhost');
            }
        } else {
            $this->merchantId = Setting::get('payhere_merchant_id_live', $_ENV['PAYHERE_MERCHANT_ID'] ?? '');
            $this->merchantSecret = Setting::get('payhere_secret_live', $_ENV['PAYHERE_MERCHANT_SECRET'] ?? '');
        }

        if ($this->isSandbox) {
            $this->appId = Setting::get('payhere_app_id_sandbox');
            $this->appSecret = Setting::get('payhere_app_secret_sandbox');
        } else {
            $this->appId = Setting::get('payhere_app_id_live');
            $this->appSecret = Setting::get('payhere_app_secret_live');
        }
    }

    public function getCheckoutUrl(): string
    {
        return $this->isSandbox
            ? 'https://sandbox.payhere.lk/pay/checkout'
            : 'https://www.payhere.lk/pay/checkout';
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function generateHash($orderId, $amount, $currency = 'LKR'): string
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

    public function verifyHash($postData): bool
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

    public function getAccessToken(): ?string
    {
        if (!$this->appId || !$this->appSecret) {
            return null;
        }

        $url = $this->isSandbox
            ? 'https://sandbox.payhere.lk/merchant/v1/oauth/token'
            : 'https://www.payhere.lk/merchant/v1/oauth/token';

        $authCode = base64_encode($this->appId . ':' . $this->appSecret);

        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $referer = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $domain;

        $http = new HttpClient();
        $response = $http->withHeaders([
            'Authorization' => 'Basic ' . $authCode,
            'Referer' => $referer,
            'Origin' => $referer
        ])->asForm()->post($url, [
            'grant_type' => 'client_credentials'
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'] ?? null;
        }

        Logger::error('PayHere OAuth Error: ' . $response->body());
        return null;
    }

    public function retrieveOrder($orderId): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['status' => 'error', 'message' => 'Failed to obtain access token'];
        }

        $url = $this->isSandbox
            ? "https://sandbox.payhere.lk/merchant/v1/payment/search?order_id={$orderId}"
            : "https://www.payhere.lk/merchant/v1/payment/search?order_id={$orderId}";

        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $referer = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $domain;

        $http = new HttpClient();
        $response = $http->withToken($token)
            ->withHeaders([
                'Referer' => $referer,
                'Origin' => $referer
            ])
            ->acceptJson()
            ->get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['status']) && $data['status'] == 1 && !empty($data['data'])) {
                return ['status' => 'success', 'data' => $data['data']];
            }

            return ['status' => 'error', 'message' => 'No payment found for this order ID in PayHere'];
        }

        return ['status' => 'error', 'message' => 'PayHere API Error: ' . $response->status()];
    }
}
