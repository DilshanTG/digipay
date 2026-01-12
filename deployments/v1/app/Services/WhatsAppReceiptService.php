<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class WhatsAppReceiptService
{
    /**
     * Generate a JPG receipt for a payment
     */
    public function generate(Payment $payment)
    {
        try {
            // Receipt Settings
            $width = 600;
            $height = 800;
            $image = imagecreatetruecolor($width, $height);

            // Colors
            $white = imagecolorallocate($image, 255, 255, 255);
            $bg_soft = imagecolorallocate($image, 248, 250, 252);
            $primary = imagecolorallocate($image, 10, 37, 64);
            $accent = imagecolorallocate($image, 99, 91, 255);
            $slate_500 = imagecolorallocate($image, 100, 116, 139);
            $emerald_500 = imagecolorallocate($image, 16, 185, 129);
            $emerald_100 = imagecolorallocate($image, 209, 250, 229);

            // Background
            imagefilledrectangle($image, 0, 0, $width, $height, $white);
            imagefilledrectangle($image, 0, 0, $width, 200, $bg_soft);

            // Header - DigiMart Solutions
            $font = 5; // Built-in font
            $title = "DIGIMART SOLUTIONS";
            $subtitle = "Official Payment Receipt";
            
            imagestring($image, $font + 1, ($width - strlen($title) * 9) / 2, 50, $title, $primary);
            imagestring($image, $font, ($width - strlen($subtitle) * 7) / 2, 85, $subtitle, $slate_500);

            // Amount Section
            $amountText = $payment->currency . " " . number_format($payment->amount, 2);
            imagestring($image, $font + 3, ($width - strlen($amountText) * 12) / 2, 130, $amountText, $accent);

            // "PAID" Stamp
            imagefilledroundedrectangle($image, ($width - 120) / 2, 230, ($width + 120) / 2, 275, 15, $emerald_100);
            imagestring($image, $font + 1, ($width - 40) / 2, 243, "PAID", $emerald_500);

            // Transaction Details
            $y = 350;
            $left_margin = 80;
            $right_target = 520;

            $details = [
                'Order ID' => $payment->order_id,
                'Customer' => $payment->customer_email ?? 'Valued Customer',
                'Date' => $payment->updated_at->format('d M Y, h:i A'),
                'Status' => $payment->status,
                'Method' => 'Visa/Master/LankaQR',
                'Reference' => $payment->payhere_ref ?? 'N/A'
            ];

            foreach ($details as $label => $value) {
                imagestring($image, $font, $left_margin, $y, $label . ":", $slate_500);
                imagestring($image, $font, $right_target - (strlen($value) * 7), $y, $value, $primary);
                
                // Dotted line
                for($ix = $left_margin; $ix < $right_target; $ix += 5) {
                    imagesetpixel($image, $ix, $y + 25, $bg_soft);
                }
                
                $y += 50;
            }

            // High Value Check (Optional Decoration)
            if ($payment->amount > 10000) {
                $gold = imagecolorallocate($image, 218, 165, 32);
                imagestring($image, $font, ($width - strlen("PREMIUM TRANSACTION") * 7) / 2, $height - 150, "PREMIUM TRANSACTION", $gold);
            }

            // Footer
            $footer1 = "Thank you for your business!";
            $footer2 = "DigiMart Solutions - Sri Lanka's Leading Tech Hub";
            imagestring($image, $font, ($width - strlen($footer1) * 7) / 2, $height - 80, $footer1, $slate_500);
            imagestring($image, $font - 1, ($width - strlen($footer2) * 6) / 2, $height - 50, $footer2, $slate_500);

            // Save Image
            $filename = 'receipts/receipt_' . $payment->order_id . '.jpg';
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/public/receipts'))) {
                mkdir(storage_path('app/public/receipts'), 0755, true);
            }

            $path = storage_path('app/public/' . $filename);
            imagejpeg($image, $path, 100);
            imagedestroy($image);

            return [
                'status' => 'success',
                'path' => $path,
                'url' => asset('storage/' . $filename)
            ];

        } catch (\Exception $e) {
            Log::error("Receipt Generation Failed: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Helper for rounded rectangles (if GD supports, or manual fallback)
     */
}

// Global scope helper for imagefilledroundedrectangle if missing or just use rectangle
if (!function_exists('imagefilledroundedrectangle')) {
    function imagefilledroundedrectangle($img, $x1, $y1, $x2, $y2, $radius, $color) {
        imagefilledrectangle($img, $x1, $y1, $x2, $y2, $color);
    }
}
