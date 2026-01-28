<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment - DigiMart Solutions</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?php echo url('favicon/favicon.ico'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo url('favicon/favicon-16x16.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo url('favicon/favicon-32x32.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo url('favicon/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo url('favicon/android-chrome-192x192.png'); ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?php echo url('favicon/android-chrome-512x512.png'); ?>">
    <link rel="manifest" href="<?php echo url('favicon/site.webmanifest'); ?>">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .processing-card {
            text-align: center;
            max-width: 420px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #ff4b2b;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 0.6s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h2 {
            color: #1a1a1a;
            font-size: 16px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="processing-card">
        <div class="spinner"></div>
        <h2>Securely connecting to PayHere...</h2>
    </div>

    <!-- PayHere Form -->
    <form id="payhereForm" method="POST" action="<?php echo $data['payhere_url']; ?>">
        <?php foreach($data as $key => $value): ?>
            <?php if($key !== 'payhere_url'): ?>
                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
            <?php endif; ?>
        <?php endforeach; ?>
        <noscript>
            <button type="submit" style="padding: 10px 20px; background: #635bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Click here if you are not redirected...
            </button>
        </noscript>
    </form>

    <script>
        // Submit IMMEDIATELY (milliseconds)
        document.getElementById('payhereForm').submit();
    </script>
</body>
</html>
