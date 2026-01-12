<?php
/**
 * Quick Fix - Clear All Caches & Refresh Config
 * Upload this file and run it once
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Restore .env if missing (cPanel hide issue)
if (!file_exists(__DIR__ . '/.env') && file_exists(__DIR__ . '/env.txt')) {
    copy(__DIR__ . '/env.txt', __DIR__ . '/.env');
}

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>DigiMart Quick Fix</title>
    <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap' rel='stylesheet'>
    <style>
        body { font-family: 'Outfit', sans-serif; padding: 40px; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .box { max-width: 500px; width: 100%; background: white; padding: 40px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; }
        h1 { color: #6366f1; font-weight: 800; margin-bottom: 20px; }
        .ok { color: #16a34a; padding: 12px; background: #f0fdf4; margin: 10px 0; border-radius: 12px; font-weight: 600; border: 1px solid #dcfce7; }
        .btn { background: #6366f1; color: white; padding: 16px 32px; border: none; border-radius: 16px; font-size: 18px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 600; width: 100%; }
        p { color: #64748b; line-height: 1.6; }
    </style>
</head>
<body>
    <div class='box'>
        <h1>🔧 Quick Fix</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<p>Resetting system cache...</p>";
    
    // Clear all caches
    shell_exec('php artisan config:clear 2>&1');
    shell_exec('php artisan cache:clear 2>&1');
    shell_exec('php artisan route:clear 2>&1');
    shell_exec('php artisan view:clear 2>&1');
    
    echo "<div class='ok'>✓ All caches cleared</div>";
    
    // Re-cache for production
    shell_exec('php artisan config:cache 2>&1');
    shell_exec('php artisan route:cache 2>&1');
    
    echo "<div class='ok'>✓ New configuration cached</div>";

    // Debug current SMS config
    $smsToken = config('services.sms.token');
    $maskedToken = $smsToken ? substr($smsToken, 0, 5) . '...' . substr($smsToken, -5) : 'MISSING';
    echo "<div style='text-align: left; background: #f8fafc; padding: 15px; border-radius: 12px; font-size: 13px; margin: 20px 0;'>";
    echo "<strong>🔍 Loaded SMS Config:</strong><br>";
    echo "Enabled: " . (config('services.sms.enabled') ? 'YES' : 'NO') . "<br>";
    echo "Sender ID: " . config('services.sms.sender_id') . "<br>";
    echo "Token: $maskedToken";
    echo "</div>";
    
    // Fix common permission issues
    @chmod(__DIR__ . '/storage', 0775);
    @chmod(__DIR__ . '/bootstrap/cache', 0775);
    
    echo "<div class='ok'>✓ Permissions refreshed</div>";
    
    echo "<p style='margin-top: 30px;'><strong>Done! Your real .env keys are now active.</strong></p>";
    echo "<p><a href='/' class='btn'>Back to Dashboard</a></p>";
    echo "<p style='margin-top: 20px; font-size: 11px; color: #ef4444;'>You can delete this fix.php file now for security.</p>";
    
} else {
    echo "<p>Run this after you update your <code>.env</code> file to make the new keys work.</p>";
    echo "<form method='POST' style='margin-top:20px;'><button type='submit' class='btn'>Apply .env Changes</button></form>";
}

echo "    </div>
</body>
</html>";
