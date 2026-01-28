<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Redirecting...</title>
</head>
<body onload="document.getElementById('payhereForm').submit();">
    <form id="payhereForm" method="POST" action="<?php echo $data['payhere_url']; ?>">
        <?php foreach($data as $key => $value): ?>
            <?php if($key !== 'payhere_url'): ?>
                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
            <?php endif; ?>
        <?php endforeach; ?>
        <noscript>
            <p>Processing payment... If you are not redirected, click below:</p>
            <button type="submit">Proceed to Payment</button>
        </noscript>
    </form>
</body>
</html>
