<?php

namespace App\Services;

use App\Models\Payment;

class WhatsAppReceiptService
{
    protected $apiKey;
    protected $adminNumber;
    protected $enabled;

    public function __construct()
    {
        $this->apiKey = $_ENV['WHATSAPP_API_KEY'] ?? '';
        $this->adminNumber = $_ENV['WHATSAPP_ADMIN_NUMBER'] ?? '94774665742';
        $this->enabled = !empty($this->apiKey);

        if ($this->enabled) {
            Logger::info("WhatsApp Service Initialized: AdminNumber={$this->adminNumber}");
        }
    }

    public function generate(Payment $payment): array
    {
        try {
            $width = 600;
            $height = 800;
            $image = imagecreatetruecolor($width, $height);

            $white = imagecolorallocate($image, 255, 255, 255);
            $bg_soft = imagecolorallocate($image, 248, 250, 252);
            $primary = imagecolorallocate($image, 10, 37, 64);
            $accent = imagecolorallocate($image, 99, 91, 255);
            $slate_500 = imagecolorallocate($image, 100, 116, 139);
            $emerald_500 = imagecolorallocate($image, 16, 185, 129);
            $emerald_100 = imagecolorallocate($image, 209, 250, 229);

            imagefilledrectangle($image, 0, 0, $width, $height, $white);
            imagefilledrectangle($image, 0, 0, $width, 200, $bg_soft);

            $font = 5;
            $title = "DIGIMART SOLUTIONS";
            $subtitle = "Official Payment Receipt";

            imagestring($image, $font + 1, ($width - strlen($title) * 9) / 2, 50, $title, $primary);
            imagestring($image, $font, ($width - strlen($subtitle) * 7) / 2, 85, $subtitle, $slate_500);

            $amountText = $payment->currency . " " . number_format($payment->amount, 2);
            imagestring($image, $font + 3, ($width - strlen($amountText) * 12) / 2, 130, $amountText, $accent);

            $this->imagefilledroundedrectangle($image, ($width - 120) / 2, 230, ($width + 120) / 2, 275, 15, $emerald_100);
            imagestring($image, $font + 1, ($width - 40) / 2, 243, "PAID", $emerald_500);

            $y = 350;
            $left_margin = 80;
            $right_target = 520;

            $details = [
                'Order ID' => $payment->order_id,
                'Customer' => $payment->customer_email ?? 'Valued Customer',
                'Date' => date('d M Y, h:i A', strtotime($payment->updated_at ?? 'now')),
                'Status' => $payment->status,
                'Method' => 'Visa/Master/LankaQR',
                'Reference' => $payment->payhere_ref ?? 'N/A'
            ];

            foreach ($details as $label => $value) {
                imagestring($image, $font, $left_margin, $y, $label . ":", $slate_500);
                imagestring($image, $font, $right_target - (strlen($value) * 7), $y, $value, $primary);

                for ($ix = $left_margin; $ix < $right_target; $ix += 5) {
                    imagesetpixel($image, $ix, $y + 25, $bg_soft);
                }

                $y += 50;
            }

            if ($payment->amount > 10000) {
                $gold = imagecolorallocate($image, 218, 165, 32);
                imagestring($image, $font, ($width - strlen("PREMIUM TRANSACTION") * 7) / 2, $height - 150, "PREMIUM TRANSACTION", $gold);
            }

            $footer1 = "Thank you for your business!";
            $footer2 = "DigiMart Solutions - Sri Lanka's Leading Tech Hub";
            imagestring($image, $font, ($width - strlen($footer1) * 7) / 2, $height - 80, $footer1, $slate_500);
            imagestring($image, $font - 1, ($width - strlen($footer2) * 6) / 2, $height - 50, $footer2, $slate_500);

            $receiptDir = __DIR__ . '/../../public/storage/receipts';
            if (!file_exists($receiptDir)) {
                mkdir($receiptDir, 0777, true);
            }

            $filename = 'receipt_' . $payment->order_id . '.jpg';
            $path = $receiptDir . '/' . $filename;
            imagejpeg($image, $path, 100);
            imagedestroy($image);

            $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost';
            $url = rtrim($baseUrl, '/') . '/storage/receipts/' . $filename;

            // Send to WhatsApp if enabled
            if ($this->enabled) {
                $this->sendToWhatsApp($payment, $url);
            }

            return [
                'status' => 'success',
                'path' => $path,
                'url' => $url
            ];

        } catch (\Exception $e) {
            Logger::error("Receipt Generation Failed: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    protected function sendToWhatsApp(Payment $payment, $imageUrl): void
    {
        try {
            $customerPhone = $payment->customer_phone;
            if (!$customerPhone) return;

            $meta = $payment->meta_data;
            $customerName = $meta['first_name'] ?? 'Customer';

            $message = "🌟 *Payment Successful!*\n\n";
            $message .= "Hi $customerName, your payment of *{$payment->currency} " . number_format($payment->amount, 2) . "* for order #{$payment->order_id} has been received.\n\n";
            $message .= "📎 *View Your Receipt:* $imageUrl\n\n";
            $message .= "Thank you for choosing DigiMart Solutions! 🚀";

            $http = new HttpClient();
            $response = $http->post('https://api.manycontacts.com/v1/messages', [
                'apiKey' => $this->apiKey,
                'to' => $customerPhone,
                'message' => $message,
                'mediaUrl' => $imageUrl
            ]);

            if ($response->successful()) {
                Logger::info("WhatsApp Receipt sent to $customerPhone");
            } else {
                Logger::error("WhatsApp Sending Failed: " . $response->body());
            }

            // Also notify Admin
            $http->post('https://api.manycontacts.com/v1/messages', [
                'apiKey' => $this->apiKey,
                'to' => $this->adminNumber,
                'message' => "🔔 *New Sale!* \n\nAmt: {$payment->currency} {$payment->amount}\nCust: $customerName\nOrder: {$payment->order_id}"
            ]);

        } catch (\Exception $e) {
            Logger::error("WhatsApp API Exception: " . $e->getMessage());
        }
    }

    private function imagefilledroundedrectangle($img, $x1, $y1, $x2, $y2, $radius, $color)
    {
        // Draw the main rectangle body
        imagefilledrectangle($img, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($img, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);

        // Draw the four corners as filled arcs
        imagefilledellipse($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }
}
