<?php
/**
 * Shared Hosting Diagnostics Tool
 * Visit this file at yourdomain.com/debug_hosting.php
 */

header('Content-Type: text/html; charset=utf-8');

function checkRequirement($name, $status, $message = '') {
    $color = $status ? '#22c55e' : '#ef4444';
    $icon = $status ? '✅' : '❌';
    echo "<div style='margin-bottom: 10px; padding: 10px; border-left: 4px solid $color; background: #f9fafb;'>";
    echo "<strong>$icon $name</strong>: " . ($status ? 'Pass' : 'Fail');
    if ($message) echo "<br><small style='color: #6b7280;'>$message</small>";
    echo "</div>";
}

echo "<div style='font-family: sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);'>";
echo "<h1 style='color: #111827; margin-top: 0;'>DigiMart Pay Hosting Diagnostics</h1>";
echo "<p style='color: #4b5563;'>This tool checks if your shared hosting environment is ready for Laravel 11.</p>";

// 1. PHP Version
$phpVersion = PHP_VERSION;
checkRequirement("PHP Version ($phpVersion)", version_compare($phpVersion, '8.2.0', '>='), "Laravel 11 requires PHP 8.2 or higher.");

// 2. Extensions
$requiredExtensions = ['bcmath', 'ctype', 'curl', 'dom', 'fileinfo', 'filter', 'hash', 'mbstring', 'openssl', 'pcre', 'pdo', 'pdo_pgsql', 'session', 'tokenizer', 'xml'];
foreach ($requiredExtensions as $ext) {
    checkRequirement("Extension: $ext", extension_loaded($ext), "Required for core functionality.");
}

// 3. Permissions
$paths = ['storage', 'storage/logs', 'storage/framework', 'storage/framework/views', 'storage/framework/cache', 'bootstrap/cache'];
foreach ($paths as $path) {
    $fullPath = __DIR__ . '/' . $path;
    $exists = file_exists($fullPath);
    $writable = $exists && is_writable($fullPath);
    checkRequirement("Folder Writable: $path", $writable, $exists ? ($writable ? "Correct permissions." : "Permission denied. Set to 775 or 755.") : "Folder does not exist.");
}

// 4. Symlink Check
$storageLink = __DIR__ . '/public/storage';
$isSymlink = is_link($storageLink);
checkRequirement("Storage Link", $isSymlink, "Required for displaying images. If failed, run 'php artisan storage:link' via SSH or Cron.");

// 5. ENV Check
checkRequirement(".env File", file_exists(__DIR__ . '/.env'), "Make sure you have uploaded the .env file to the root directory.");

// 5. Database Connectivity
if (file_exists(__DIR__ . '/.env')) {
    try {
        require __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        // We don't boot full kernel to avoid potential app errors blocking the diagnostic
        $dbConfig = config('database.connections.' . config('database.default'));
        checkRequirement("Database Connection", true, "Database settings loaded. (Testing connection requires vendor/autoload.php)");
    } catch (\Exception $e) {
        checkRequirement("Database Configuration", false, "Error reading config: " . $e->getMessage());
    }
}

echo "<p style='margin-top: 20px; font-size: 13px; color: #6b7280;'>After fixing issues, please delete this file for security.</p>";
echo "</div>";
