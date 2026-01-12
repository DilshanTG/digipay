<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $endpoint;
    protected $token;
    protected $senderId;
    protected $enabled;

    public function __construct()
    {
        // Try config() first (preferred for cached production)
        $this->endpoint = config('services.sms.endpoint') ?: env('SMS_API_ENDPOINT', 'https://dashboard.smsapi.lk/api/v3/sms/send');
        $this->token = config('services.sms.token') ?: env('SMS_API_TOKEN');
        $this->senderId = config('services.sms.sender_id') ?: env('SMS_SENDER_ID', 'DIGIMART');
        $this->enabled = config('services.sms.enabled', env('SMS_ENABLED', true));

        // Hidden logging for debugging
        $maskedToken = $this->token ? substr($this->token, 0, 5) . '...' . substr($this->token, -5) : 'MISSING';
        Log::info("SmsService Initialized: Endpoint={$this->endpoint}, SenderID={$this->senderId}, Enabled=" . ($this->enabled ? 'YES' : 'NO') . ", Token={$maskedToken}");
    }

    /**
     * Send SMS via SMSAPI.LK
     */
    public function send($recipient, $message)
    {
        if (!$this->enabled) {
            Log::info("SMS Disabled. Message to $recipient: $message");
            return false;
        }

        // Clean phone number (remove all non-numeric)
        $recipient = preg_replace('/[^0-9]/', '', $recipient);
        
        // Prevent double '94' (e.g., 949477...)
        if (substr($recipient, 0, 4) === '9494') {
            $recipient = substr($recipient, 2);
        }

        // Ensure it has 94 prefix if it's a local number starting with 0 or just 9 digits
        if (strlen($recipient) == 9) {
            $recipient = '94' . $recipient;
        } elseif (strlen($recipient) == 10 && substr($recipient, 0, 1) === '0') {
            $recipient = '94' . substr($recipient, 1);
        }

        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->post($this->endpoint, [
                    'recipient' => $recipient,
                    'sender_id' => $this->senderId,
                    'type' => 'plain',
                    'message' => $message
                ]);

            if ($response->successful()) {
                Log::info("SMS Sent successfully to $recipient. Response: " . $response->body());
                return true;
            }

            Log::error("SMS Sending Failed to $recipient. HTTP Code: " . $response->status() . " Response: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("SMS Exception sending to $recipient: " . $e->getMessage());
            return false;
        }
    }
}
