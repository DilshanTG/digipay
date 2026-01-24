<!DOCTYPE html>
<html>
<head>
    <title>Redirecting...</title>
</head>
<body>
    <form id="payhere_form" method="POST" action="<?php echo htmlspecialchars($data['payhere_url']); ?>">
        <?php foreach ($data as $key => $value): ?>
            <?php if ($key !== 'payhere_url'): ?>
                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
            <?php endif; ?>
        <?php endforeach; ?>
    </form>
    <script>document.getElementById('payhere_form').submit();</script>
</body>
</html>
