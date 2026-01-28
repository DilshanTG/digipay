<?php

namespace App\Services;

class EmailService
{
    protected $apiKey;
    protected $fromEmail;
    protected $fromName;

    public function __construct()
    {
        $this->apiKey = $_ENV['ZOHO_API_KEY'] ?? '';
        $this->fromEmail = $_ENV['MAIL_FROM_ADDRESS'] ?? 'info@digimartsolutions.lk';
        $this->fromName = $_ENV['MAIL_FROM_NAME'] ?? 'DigiMart Pay';
    }

    public function send($to, $subject, $htmlContent): bool
    {
        if (empty($this->apiKey) || empty($to)) {
            Logger::error("Email skipped: Missing API key or recipient ($to)");
            return false;
        }

        try {
            $http = new HttpClient();
            $response = $http->withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.zeptomail.com/v1.1/email', [
                'from' => [
                    'address' => $this->fromEmail,
                    'name' => $this->fromName
                ],
                'to' => [
                    [
                        'email_address' => [
                            'address' => $to,
                            'name' => 'Customer'
                        ]
                    ]
                ],
                'subject' => $subject,
                'htmlbody' => $htmlContent
            ]);

            if ($response->successful()) {
                Logger::info("Email sent successfully to $to");
                return true;
            }

            Logger::error("Email failed to $to: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Logger::error("Email Exception: " . $e->getMessage());
            return false;
        }
    }
}
