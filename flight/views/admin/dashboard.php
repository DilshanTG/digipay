<div class="container">
    <div class="header">
        <h1>Admin Dashboard</h1>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="card">
            <h3>Total Revenue</h3>
            <p style="font-size: 32px; font-weight: bold; color: #10b981;">LKR <?php echo number_format($totalAmount ?? 0, 2); ?></p>
        </div>
        <div class="card">
            <h3>Successful Payments</h3>
            <p style="font-size: 32px; font-weight: bold; color: #3b82f6;"><?php echo $successCount ?? 0; ?></p>
        </div>
        <div class="card">
            <h3>Total Payments</h3>
            <p style="font-size: 32px; font-weight: bold; color: #8b5cf6;"><?php echo count($payments ?? []); ?></p>
        </div>
    </div>

    <div class="card">
        <h2>Recent Payments</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Customer</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($payments ?? [], 0, 10) as $payment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($payment->order_id); ?></td>
                    <td>LKR <?php echo number_format($payment->amount, 2); ?></td>
                    <td><span class="status <?php echo strtolower($payment->status); ?>"><?php echo $payment->status; ?></span></td>
                    <td><?php echo htmlspecialchars($payment->customer_email ?? 'N/A'); ?></td>
                    <td><?php echo date('d M Y', strtotime($payment->created_at ?? 'now')); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="/admin/payments" style="color: #3b82f6; text-decoration: none; font-weight: 600;">View All Payments →</a>
        <a href="/admin/merchants" style="color: #3b82f6; text-decoration: none; font-weight: 600; margin-left: 20px;">Manage Merchants →</a>
        <a href="/admin/settings" style="color: #3b82f6; text-decoration: none; font-weight: 600; margin-left: 20px;">Settings →</a>
    </div>
</div>
