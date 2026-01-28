<?php

namespace App\Services;

class SmsService
{
    protected $endpoint;
    protected $token;
    protected $senderId;
    protected $enabled;

    public function __construct()
    {
        $this->endpoint = $_ENV['SMS_API_ENDPOINT'] ?? 'https://dashboard.smsapi.lk/api/v3/sms/send';
        $this->token = $_ENV['SMS_API_TOKEN'] ?? '';
        $this->senderId = $_ENV['SMS_SENDER_ID'] ?? 'DIGIMART';

        // SMS is enabled only if explicitly enabled AND token is configured
        $envEnabled = isset($_ENV['SMS_ENABLED'])
            ? filter_var($_ENV['SMS_ENABLED'], FILTER_VALIDATE_BOOLEAN)
            : true;
        $this->enabled = $envEnabled && !empty($this->token);

        $maskedToken = $this->token ? substr($this->token, 0, 5) . '...' . substr($this->token, -5) : 'MISSING';
        Logger::info("SmsService Initialized: Endpoint={$this->endpoint}, SenderID={$this->senderId}, Enabled=" . ($this->enabled ? 'YES' : 'NO') . ", Token={$maskedToken}");
    }

    public function send($recipient, $message): bool
    {
        if (!$this->enabled) {
            Logger::info("SMS Disabled. Message to $recipient: $message");
            return false;
        }

        $recipient = preg_replace('/[^0-9]/', '', $recipient);

        if (substr($recipient, 0, 4) === '9494') {
            $recipient = substr($recipient, 2);
        }

        if (strlen($recipient) == 9) {
            $recipient = '94' . $recipient;
        } elseif (strlen($recipient) == 10 && substr($recipient, 0, 1) === '0') {
            $recipient = '94' . substr($recipient, 1);
        }

        try {
            $http = new HttpClient();
            $response = $http->withToken($this->token)
                ->acceptJson()
                ->post($this->endpoint, [
                    'recipient' => $recipient,
                    'sender_id' => $this->senderId,
                    'type' => 'plain',
                    'message' => $message
                ]);

            if ($response->successful()) {
                Logger::info("SMS Sent successfully to $recipient. Response: " . $response->body());
                return true;
            }

            Logger::error("SMS Sending Failed to $recipient. HTTP Code: " . $response->status() . " Response: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Logger::error("SMS Exception sending to $recipient: " . $e->getMessage());
            return false;
        }
    }
}
