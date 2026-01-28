<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Merchant;
use App\Database;

class PaymentService
{
    protected $sms;
    protected $receipts;
    protected $email;

    public function __construct(SmsService $sms, WhatsAppReceiptService $receipts, EmailService $email)
    {
        $this->sms = $sms;
        $this->receipts = $receipts;
        $this->email = $email;
    }

    public function completePayment(Payment $payment, $payhereRef = null): void
    {
        if ($payment->status === 'SUCCESS') {
            return;
        }

        try {
            Database::getPdo()->beginTransaction();

            $payment->status = 'SUCCESS';
            if ($payhereRef) {
                $payment->payhere_ref = $payhereRef;
            }
            $payment->update();

            $this->sendSuccessNotifications($payment);

            if ($payment->notify_url) {
                $this->sendCallbackWebhook($payment);
            }

            Database::getPdo()->commit();
        } catch (\Exception $e) {
            Database::getPdo()->rollBack();
            Logger::error("Payment completion failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function sendSuccessNotifications(Payment $payment): void
    {
        $meta = $payment->meta_data;
        $customerName = trim(($meta['first_name'] ?? 'Customer') . ' ' . ($meta['last_name'] ?? ''));
        $originalAmount = $meta['original_amount'] ?? $payment->amount;
        $note = $meta['note'] ?? '';

        // 1. SMS Notification
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
            Logger::error("Customer SMS failed: " . $e->getMessage());
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
            Logger::error("Admin SMS failed: " . $e->getMessage());
        }

        // 3. WhatsApp & JPG Receipt
        try {
            $result = $this->receipts->generate($payment);
            if ($result['status'] === 'success') {
                Logger::info("Automated JPG Receipt generated: " . $result['url']);
            }
        } catch (\Exception $e) {
            Logger::error("WhatsApp Receipt failed: " . $e->getMessage());
        }

        // 4. Email Notification (Zoho ZeptoMail)
        try {
            if ($payment->customer_email) {
                $subject = "Payment Receipt: #{$payment->order_id}";
                $html = "<h1>Payment Successful!</h1>";
                $html .= "<p>Hi $customerName, we have received your payment of <b>{$payment->currency} " . number_format($payment->amount, 2) . "</b>.</p>";
                $html .= "<p><b>Order ID:</b> {$payment->order_id}</p>";
                if ($payment->real_description) $html .= "<p><b>Description:</b> {$payment->real_description}</p>";
                $html .= "<p>Thank you for choosing DigiMart Solutions.</p>";
                
                $this->email->send($payment->customer_email, $subject, $html);
            }
        } catch (\Exception $e) {
            Logger::error("Customer Email failed: " . $e->getMessage());
        }
    }

    public function sendCallbackWebhook(Payment $payment): void
    {
        try {
            $merchant = $payment->merchant();
            if (!$merchant && $payment->merchant_id) {
                $merchant = Merchant::find($payment->merchant_id);
            }

            if (!$merchant) return;

            $merchantId = $merchant->api_key;
            $orderId = $payment->client_order_id ?? $payment->order_id;
            $amount = number_format($payment->amount, 2, '.', '');
            $currency = $payment->currency;
            $statusCode = 2;
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

            $http = new HttpClient();
            $response = $http->withOptions([
                'verify' => false,
                'timeout' => 30
            ])->asForm()->post($payment->notify_url, $data);

            if ($response->successful()) {
                Logger::info("Webhook sent SUCCESS to {$payment->notify_url} for Order {$payment->order_id}");
            } else {
                Logger::error("Webhook FAILED to {$payment->notify_url} for Order {$payment->order_id}. Status: " . $response->status());
            }

        } catch (\Exception $e) {
            Logger::error("Webhook Failed for Order {$payment->order_id}: " . $e->getMessage());
        }
    }
}
