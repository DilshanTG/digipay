<div class="container">
    <div class="card">
        <h2>Payment Pending Verification</h2>
        <p>Your payment is being verified. Please wait...</p>

        <div class="loader" style="margin: 30px auto;"></div>

        <p style="text-align: center;">Order ID: <strong><?php echo htmlspecialchars($payment->order_id); ?></strong></p>

        <script>
            setTimeout(function() {
                location.reload();
            }, 5000);
        </script>
    </div>
</div>
