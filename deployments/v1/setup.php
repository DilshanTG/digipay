<?php
/**
 * DigiMart Pay - Bulletproof Setup Wizard
 * Fixes hidden files, keys, and permissions automatically
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Restore .env from env.txt if missing
if (!file_exists(__DIR__ . '/.env') && file_exists(__DIR__ . '/env.txt')) {
    copy(__DIR__ . '/env.txt', __DIR__ . '/.env');
}

if (file_exists(__DIR__ . '/.setup_lock')) {
    echo "<h1>✅ Setup already completed!</h1><p><a href='/'>Visit site</a> | <a href='/admin'>Admin Panel</a></p>";
    exit;
}

$output = [];
function run($cmd) {
    global $output;
    $res = shell_exec($cmd . ' 2>&1');
    $output[] = ['cmd' => $cmd, 'res' => $res];
    return $res;
}

?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Setup Wizard</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 500px; text-align: center; }
        h1 { color: #6366f1; margin-bottom: 20px; }
        .btn { background: #6366f1; color: white; border: none; padding: 15px 30px; border-radius: 10px; font-size: 18px; cursor: pointer; width: 100%; font-weight: bold; }
        .log { text-align: left; background: #1e293b; color: #cbd5e1; padding: 15px; border-radius: 10px; margin-top: 20px; font-size: 12px; overflow-x: auto; }
        .success { color: #16a34a; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🚀 DigiMart Pay Setup</h1>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p>Processing...</p>
            <?php
            // Run commands
            run('php artisan migrate --force');
            run('php artisan db:seed --force');
            
            // Fix storage link manually if symlink() fails
            if (!file_exists(__DIR__ . '/storage/app/public')) {
                mkdir(__DIR__ . '/storage/app/public', 0755, true);
            }
            run('php artisan storage:link');
            
            run('php artisan config:cache');
            run('php artisan route:cache');
            
            touch(__DIR__ . '/.setup_lock');
            ?>
            <div class="success">✅ Successfully Installed!</div>
            <div class="log">
                <details>
                    <summary>View detailed logs</summary>
                    <?php foreach($output as $o): ?>
                        <strong><?php echo $o['cmd']; ?></strong>
                        <pre><?php echo $o['res']; ?></pre>
                    <?php endforeach; ?>
                </details>
            </div>
            <p style="margin-top:20px;">
                <a href="/" style="color:#6366f1; text-decoration:none; font-weight:bold;">Go to Homepage →</a>
            </p>
            <p style="font-size:11px; color:red; margin-top:20px;">REMINDER: Delete setup.php and env.txt now.</p>
        <?php else: ?>
            <p>Ensure your Database is created in cPanel before clicking.</p>
            <form method="POST"><button type="submit" class="btn">Start Installation</button></form>
        <?php endif; ?>
    </div>
</body>
</html>
