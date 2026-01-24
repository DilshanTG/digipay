<div class="container">
    <div class="header">
        <h1>All Payments</h1>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Mode</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments ?? [] as $payment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($payment->order_id); ?></td>
                    <td>LKR <?php echo number_format($payment->amount, 2); ?></td>
                    <td><span class="status <?php echo strtolower($payment->status); ?>"><?php echo $payment->status; ?></span></td>
                    <td><?php echo strtoupper($payment->mode); ?></td>
                    <td><?php echo htmlspecialchars($payment->customer_email ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($payment->customer_phone ?? 'N/A'); ?></td>
                    <td><?php echo date('d M Y H:i', strtotime($payment->created_at ?? 'now')); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="/admin" style="color: #3b82f6; text-decoration: none;">← Back to Dashboard</a>
    </div>
</div>
