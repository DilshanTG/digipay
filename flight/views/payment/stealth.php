<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to Payment...</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background: #f8fafc; }
        .loader { border: 5px solid #f3f3f3; border-top: 5px solid #3b82f6; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <h2>Redirecting to PayHere...</h2>
    <div class="loader"></div>
    <p>Please wait while we redirect you to the payment gateway.</p>

    <form id="payhere_form" method="POST" action="<?php echo htmlspecialchars($data['payhere_url']); ?>">
        <?php foreach ($data as $key => $value): ?>
            <?php if ($key !== 'payhere_url'): ?>
                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
            <?php endif; ?>
        <?php endforeach; ?>
    </form>

    <script>
        setTimeout(function() {
            document.getElementById('payhere_form').submit();
        }, 1500);
    </script>
</body>
</html>
