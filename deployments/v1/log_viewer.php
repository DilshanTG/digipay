<?php
/**
 * DigiMart Log Viewer
 * Inspect laravel.log from your browser
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security: Check for a simple secret in URL to prevent public access
$secret = 'digi_debug';
if (($_GET['s'] ?? '') !== $secret) {
    die("Access Denied. Usage: log_viewer.php?s=digi_debug");
}

$logFile = __DIR__ . '/storage/logs/laravel.log';

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Laravel Log Viewer</title>
    <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&family=JetBrains+Mono&display=swap' rel='stylesheet'>
    <style>
        body { font-family: 'Outfit', sans-serif; background: #0f172a; color: #e2e8f0; margin: 0; padding: 40px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #6366f1; margin-bottom: 30px; }
        .log-box { background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155; font-family: 'JetBrains Mono', monospace; font-size: 13px; line-height: 1.5; overflow-x: auto; white-space: pre-wrap; height: 70vh; overflow-y: scroll; }
        .controls { margin-top: 20px; display: flex; gap: 10px; }
        .btn { background: #334155; color: #f8fafc; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: 600; }
        .btn:hover { background: #475569; }
        .btn-red { background: #991b1b; }
        .btn-red:hover { background: #b91c1c; }
        .entry { border-bottom: 1px solid #334155; padding: 10px 0; }
        .INFO { color: #22c55e; }
        .ERROR { color: #ef4444; }
        .WARNING { color: #f59e0b; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📋 Laravel Log Viewer</h1>
        
        <div class='log-box' id='logBox'>";

if (isset($_POST['clear'])) {
    file_put_contents($logFile, "");
    echo "<div class='entry INFO'>[System] Log cleared at " . date('Y-m-d H:i:s') . "</div>";
} elseif (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    if (empty($content)) {
        echo "<div class='entry'>Log is empty.</div>";
    } else {
        $lines = explode("\n", $content);
        // Show last 100 lines
        $lines = array_slice($lines, -100);
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $class = '';
            if (strpos($line, '.INFO:') !== false) $class = 'INFO';
            if (strpos($line, '.ERROR:') !== false) $class = 'ERROR';
            if (strpos($line, '.WARNING:') !== false) $class = 'WARNING';
            
            echo "<div class='entry $class'>" . htmlspecialchars($line) . "</div>";
        }
    }
} else {
    echo "<div class='entry ERROR'>Log file not found at: $logFile</div>";
}

echo "        </div>
        
        <div class='controls'>
            <a href='?s=$secret' class='btn'>🔄 Refresh Logs</a>
            <form method='POST'><button type='submit' name='clear' class='btn btn-red'>🗑️ Clear Log File</button></form>
            <a href='/' class='btn'>🏠 Back to Site</a>
        </div>
        
        <script>
            var logBox = document.getElementById('logBox');
            logBox.scrollTop = logBox.scrollHeight;
        </script>
    </div>
</body>
</html>";
