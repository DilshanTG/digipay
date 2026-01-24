<div class="container">
    <div class="card">
        <h1 style="text-align: center; color: <?php echo $payment->status === 'SUCCESS' ? '#10b981' : '#ef4444'; ?>">
            <?php if ($payment->status === 'SUCCESS'): ?>
                ✓ Payment Successful
            <?php else: ?>
                ✗ Payment <?php echo $payment->status; ?>
            <?php endif; ?>
        </h1>

        <table>
            <tr>
                <th>Order ID</th>
                <td><?php echo htmlspecialchars($payment->order_id); ?></td>
            </tr>
            <tr>
                <th>Amount</th>
                <td><?php echo htmlspecialchars($payment->currency); ?> <?php echo number_format($payment->amount, 2); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><span class="status <?php echo strtolower($payment->status); ?>"><?php echo $payment->status; ?></span></td>
            </tr>
            <?php if ($payment->payhere_ref): ?>
            <tr>
                <th>Reference</th>
                <td><?php echo htmlspecialchars($payment->payhere_ref); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th>Date</th>
                <td><?php echo date('d M Y, h:i A', strtotime($payment->updated_at ?? 'now')); ?></td>
            </tr>
        </table>

        <?php if ($payment->status === 'SUCCESS'): ?>
        <p style="text-align: center; margin-top: 30px; color: #64748b;">
            A confirmation has been sent to your email and SMS.
        </p>
        <?php endif; ?>
    </div>
</div>
