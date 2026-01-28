<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifying Payment - DigiMart Solutions</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?php echo url('favicon/favicon.ico'); ?>">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f6f9fc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .sync-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #635bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h2 {
            color: #0a2540;
            font-size: 20px;
            margin-bottom: 12px;
        }

        p {
            color: #6b7c93;
            font-size: 15px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="sync-card">
        <div class="spinner"></div>
        <h2>Verifying your payment...</h2>
        <p>Please wait while we confirm your transaction. This will only take a moment.</p>
    </div>

    <script>
        // Check status periodically
        let checks = 0;
        const maxChecks = 10;
        const orderId = "<?php echo $payment->order_id; ?>";

        function checkStatus() {
            checks++;
            fetch("<?php echo url('api/v1/status/'); ?>" + orderId)
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success' && res.data.status === 'SUCCESS') {
                        window.location.reload();
                    } else if (checks < maxChecks) {
                        setTimeout(checkStatus, 3000);
                    } else {
                        // After max checks, just reload which will show current status
                        window.location.reload();
                    }
                })
                .catch(() => {
                    if (checks < maxChecks) setTimeout(checkStatus, 3000);
                });
        }

        setTimeout(checkStatus, 3000);
    </script>
</body>
</html>
