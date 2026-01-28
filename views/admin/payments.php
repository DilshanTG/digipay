<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - DigiPay Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <nav class="bg-slate-900 text-white p-4 sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold flex items-center gap-2">
                <span class="bg-blue-600 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </span>
                DigiPay Admin
            </h1>
            <div class="space-x-4 flex items-center text-sm font-medium">
                <a href="<?php echo url('admin'); ?>" class="hover:text-blue-400 py-2">Dashboard</a>
                <a href="<?php echo url('admin/merchants'); ?>" class="hover:text-blue-400 py-2">Merchants</a>
                <a href="<?php echo url('admin/payments'); ?>" class="text-blue-400 border-b-2 border-blue-400 py-2">Payments</a>
                <a href="<?php echo url('admin/settings'); ?>" class="hover:text-blue-400 py-2">Settings</a>
                <a href="<?php echo url('admin/docs'); ?>" class="hover:text-blue-400 py-2">Docs</a>
                <div class="h-4 w-px bg-slate-700 mx-2"></div>
                <a href="<?php echo url('admin/logout'); ?>" class="text-rose-400 hover:text-rose-300 font-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Transaction Ledger</h2>
                <p class="text-slate-500 mt-1">Real-time monitoring of all incoming and outgoing payment requests.</p>
            </div>
            <div class="flex gap-2">
                <button class="bg-white text-slate-700 px-6 py-3 rounded-2xl border border-slate-200 font-bold hover:bg-slate-50 transition active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                <button class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold hover:bg-slate-800 transition active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV
                </button>
            </div>
        </div>
        
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-left border-b border-slate-100 uppercase tracking-wider text-[10px] font-bold">
                            <th class="px-8 py-5">Order Reference</th>
                            <th class="px-8 py-5">Merchant / Platform</th>
                            <th class="px-8 py-5">Customer Profile</th>
                            <th class="px-8 py-5 text-right">Settlement</th>
                            <th class="px-8 py-5 text-center">Authorization</th>
                            <th class="px-8 py-5 text-right">Timestamp</th>
                            <th class="px-8 py-5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php foreach(array_reverse($payments) as $payment): ?>
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-8 py-6">
                                <div class="font-mono text-xs font-bold text-slate-400 group-hover:text-blue-600 transition tracking-tighter"><?php echo $payment->order_id; ?></div>
                                <?php if($payment->client_order_id): ?>
                                    <div class="text-[10px] text-slate-300 mt-0.5">Ref: <?= $payment->client_order_id ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-900"><?php echo $payment->merchant() ? htmlspecialchars($payment->merchant()->name) : 'Unknown System'; ?></div>
                                <div class="text-[10px] uppercase font-bold text-slate-300 tracking-widest mt-0.5"><?= $payment->mode ?> GATEWAY</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-slate-900 font-bold"><?php echo htmlspecialchars($payment->meta_data['first_name'] ?? 'Guest'); ?></div>
                                <div class="text-slate-400 text-xs mt-0.5"><?php echo htmlspecialchars($payment->customer_email ?: 'No email cached'); ?></div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="font-black text-slate-900 text-base"><?php echo $payment->currency; ?> <?php echo number_format($payment->amount, 2); ?></div>
                                <div class="text-[10px] text-slate-300 font-mono"><?= $payment->payment_method ?: 'Credit/Debit' ?></div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="px-4 py-1.5 rounded-full text-[10px] font-black tracking-widest uppercase
                                    <?php
                                        if($payment->status == 'SUCCESS') echo 'bg-emerald-100 text-emerald-700';
                                        elseif($payment->status == 'PENDING') echo 'bg-amber-100 text-amber-700';
                                        elseif($payment->status == 'CANCELLED') echo 'bg-slate-100 text-slate-500';
                                        else echo 'bg-rose-100 text-rose-700';
                                    ?>">
                                    <?php echo $payment->status; ?>
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="text-xs font-bold text-slate-900"><?= date('M j, Y', strtotime($payment->created_at)) ?></div>
                                <div class="text-[10px] text-slate-400 mt-0.5"><?= date('h:i A', strtotime($payment->created_at)) ?></div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <a href="<?= url('admin/payments/delete/'.$payment->order_id) ?>" class="text-slate-300 hover:text-rose-500 transition tooltip" title="Delete Record" onclick="return confirm('Permanently delete this payment record?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(empty($payments)): ?>
            <div class="text-center py-20 bg-slate-50/30">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">No Transactions Recorded</h3>
                <p class="text-slate-400 text-sm mt-1">Ready to receive your first payment integration.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
