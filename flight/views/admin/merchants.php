<div class="container">
    <div class="header">
        <h1>Merchants</h1>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>API Key</th>
                    <th>Status</th>
                    <th>Allowed Domains</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($merchants ?? [] as $merchant): ?>
                <tr>
                    <td><?php echo htmlspecialchars($merchant->name); ?></td>
                    <td><code><?php echo htmlspecialchars(substr($merchant->api_key, 0, 20)) . '...'; ?></code></td>
                    <td><span class="status <?php echo $merchant->is_active ? 'success' : 'failed'; ?>"><?php echo $merchant->is_active ? 'Active' : 'Inactive'; ?></span></td>
                    <td><?php echo htmlspecialchars(implode(', ', $merchant->allowed_domains ?? ['*'])); ?></td>
                    <td><?php echo date('d M Y', strtotime($merchant->created_at ?? 'now')); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="/admin" style="color: #3b82f6; text-decoration: none;">← Back to Dashboard</a>
    </div>
</div>
