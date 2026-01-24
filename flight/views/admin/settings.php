<div class="container">
    <div class="header">
        <h1>Settings</h1>
    </div>

    <div class="card">
        <h2>Application Settings</h2>
        <table>
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($settings ?? [] as $setting): ?>
                <tr>
                    <td><?php echo htmlspecialchars($setting->key); ?></td>
                    <td><?php echo htmlspecialchars(substr($setting->value ?? '', 0, 100)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="/admin" style="color: #3b82f6; text-decoration: none;">← Back to Dashboard</a>
    </div>
</div>
