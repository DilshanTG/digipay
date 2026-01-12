<?php

namespace App\Services;

use App\Models\Payment;
use App\Jobs\SyncPaymentToSupabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $sms;
    protected $receipts;

    public function __construct(SmsService $sms, WhatsAppReceiptService $receipts)
    {
        $this->sms = $sms;
        $this->receipts = $receipts;
    }

    /**
     * Mark a payment as successful and trigger all side effects
     */
    public function completePayment(Payment $payment, $payhereRef = null)
    {
        if ($payment->status === 'SUCCESS') {
            return;
        }

        DB::transaction(function() use ($payment, $payhereRef) {
            $payment->status = 'SUCCESS';
            if ($payhereRef) {
                $payment->payhere_ref = $payhereRef;
            }
            $payment->save();

            // 1. Sync to Supabase (Async)
            try {
                SyncPaymentToSupabase::dispatch($payment);
            } catch (\Exception $e) {
                Log::error("Supabase Dispatch Failed: " . $e->getMessage());
            }

            // 2. Trigger Notifications
            $this->sendSuccessNotifications($payment);
            
            // 3. Send Webhook to Merchant (PayHere Emulation)
            if ($payment->notify_url) {
                $this->sendCallbackWebhook($payment);
            }
        });
    }

    /**
     * Send SMS and Generate JPG Receipt
     */
    public function sendSuccessNotifications(Payment $payment)
    {
        $meta = $payment->meta_data;
        $customerName = trim(($meta['first_name'] ?? 'Customer') . ' ' . ($meta['last_name'] ?? ''));
        $originalAmount = $meta['original_amount'] ?? $payment->amount;
        $note = $meta['note'] ?? '';

        // 1. Customer SMS
        try {
            if ($payment->customer_phone) {
                $msg = "Dear $customerName,\n\n";
                $msg .= "Your payment of Rs. " . number_format($originalAmount, 2) . " has been received successfully.\n\n";
                $msg .= "Total Charged: Rs. " . number_format($payment->amount, 2) . "\n";
                if ($payment->real_description) $msg .= "Description: {$payment->real_description}\n";
                if ($payment->payhere_ref) $msg .= "Ref ID: {$payment->payhere_ref}\n";
                if ($note) $msg .= "Note: $note\n";
                $msg .= "\nThank you!\n- DigiMart Solutions";

                $this->sms->send($payment->customer_phone, $msg);
            }
        } catch (\Exception $e) {
            Log::error("Customer SMS failed: " . $e->getMessage());
        }

        // 2. Admin SMS
        try {
            $adminPhone = '94774665742';
            $adminMsg = "New Payment Received!\n\n";
            $adminMsg .= "Customer: $customerName\n";
            $adminMsg .= "Amount: Rs " . number_format($payment->amount, 2) . "\n";
            if ($payment->real_description) $adminMsg .= "Desc: {$payment->real_description}\n";
            if ($note) $adminMsg .= "Note: $note\n";
            $adminMsg .= "Ref: {$payment->order_id}";

            $this->sms->send($adminPhone, $adminMsg);
        } catch (\Exception $e) {
            Log::error("Admin SMS failed: " . $e->getMessage());
        }

        // 3. Automated JPG Receipt (Direct Mode Only)
        if ($payment->mode === 'direct') {
            $result = $this->receipts->generate($payment);
            if ($result['status'] === 'success') {
                Log::info("Automated JPG Receipt generated for direct payment: " . $result['url']);
            }
        }
    }
    /**
     * Send PayHere-compatible Webhook to Merchant
     */
    public function sendCallbackWebhook(Payment $payment)
    {
        try {
            $merchant = $payment->merchant; 
            if (!$merchant) { 
                $merchant = \App\Models\Merchant::find($payment->merchant_id);
            }
            
            if (!$merchant) return;

            // Generate Signature
            // MD5(merchant_id + order_id + payhere_amount + payhere_currency + status_code + MD5(secret))
            $merchantId = $merchant->api_key;
            $orderId = $payment->client_order_id ?? $payment->order_id;
            $amount = number_format($payment->amount, 2, '.', '');
            $currency = $payment->currency;
            $statusCode = 2; // Success
            $secretHash = strtoupper(md5($merchant->secret_key));
            
            $hashString = $merchantId . $orderId . $amount . $currency . $statusCode . $secretHash;
            $md5sig = strtoupper(md5($hashString));

            $data = [
                'merchant_id' => $merchantId,
                'order_id' => $orderId,
                'payment_id' => $payment->order_id,
                'payhere_amount' => $amount,
                'payhere_currency' => $currency,
                'status_code' => $statusCode,
                'md5sig' => $md5sig,
                'custom_1' => '',
                'custom_2' => ''
            ];

            // Send POST Request (Robust - no SSL verify + timeout)
            $response = \Illuminate\Support\Facades\Http::withOptions([
                'verify' => false,
                'timeout' => 30
            ])->asForm()->post($payment->notify_url, $data);
            
            if ($response->successful()) {
                Log::info("Webhook sent SUCCESS to {$payment->notify_url} for Order {$payment->order_id}");
            } else {
                Log::error("Webhook FAILED to {$payment->notify_url} for Order {$payment->order_id}. Status: " . $response->status());
            }

        } catch (\Exception $e) {
            Log::error("Webhook Failed for Order {$payment->order_id}: " . $e->getMessage());
        }
    }
}
