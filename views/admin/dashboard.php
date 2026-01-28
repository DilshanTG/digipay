<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DigiPay</title>
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
                <a href="<?php echo url('admin'); ?>" class="text-blue-400 border-b-2 border-blue-400 py-2">Dashboard</a>
                <a href="<?php echo url('admin/merchants'); ?>" class="hover:text-blue-400 py-2">Merchants</a>
                <a href="<?php echo url('admin/payments'); ?>" class="hover:text-blue-400 py-2">Payments</a>
                <a href="<?php echo url('admin/settings'); ?>" class="hover:text-blue-400 py-2">Settings</a>
                <a href="<?php echo url('admin/docs'); ?>" class="hover:text-blue-400 py-2">Docs</a>
                <div class="h-4 w-px bg-slate-700 mx-2"></div>
                <a href="<?php echo url('admin/logout'); ?>" class="text-rose-400 hover:text-rose-300 font-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-8">
        <div class="mb-10">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">System Overview</h2>
            <p class="text-slate-500 mt-1">Global performance and transaction health metrics.</p>
        </div>
        
        <!-- Key Performance Indicators -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <!-- Today -->
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200">
                <div class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">Today (Net)</div>
                <div class="text-3xl font-black text-slate-900">Rs <?= number_format($stats['revenue_today'], 2); ?></div>
                <div class="mt-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-slate-400">Live feed monitoring</span>
                </div>
            </div>
            <!-- Week -->
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200">
                <div class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">Last 7 Days</div>
                <div class="text-3xl font-black text-slate-900">Rs <?= number_format($stats['revenue_week'], 2); ?></div>
                <div class="mt-4">
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-blue-600 h-full w-[65%]"></div>
                    </div>
                </div>
            </div>
            <!-- Month -->
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200">
                <div class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2"><?= date('F') ?> Gross</div>
                <div class="text-3xl font-black text-slate-900">Rs <?= number_format($stats['revenue_month'], 2); ?></div>
                <div class="mt-4 text-[10px] font-bold text-blue-600 uppercase italic">Goal: Dynamic</div>
            </div>
            <!-- Success Rate -->
            <div class="bg-slate-900 p-8 rounded-[2rem] shadow-xl text-white">
                <div class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">Approval Rate</div>
                <?php $rate = $stats['total_payments'] > 0 ? ($stats['successful'] / $stats['total_payments']) * 100 : 0; ?>
                <div class="text-3xl font-black"><?= number_format($rate, 1); ?>%</div>
                <div class="mt-4 text-emerald-400 text-[10px] font-bold uppercase tracking-tighter">Healthy System Status</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mb-12">
            <!-- Merchant Distribution -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full">
                    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <h2 class="font-black text-slate-900 text-sm uppercase tracking-widest">Merchant Yield</h2>
                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    </div>
                    <div class="flex-grow">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-slate-50">
                                <?php foreach($merchantBreakdown as $m): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="font-bold text-slate-800"><?php echo htmlspecialchars($m->name); ?></div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5"><?= $m->payment_count ?> Successes</div>
                                    </td>
                                    <td class="px-8 py-5 text-right font-black text-slate-900">
                                        Rs <?= number_format($m->total_revenue, 0); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-8 border-t border-slate-100 bg-slate-50/30">
                        <a href="<?= url('admin/merchants') ?>" class="block text-center text-xs font-black text-blue-600 uppercase tracking-widest hover:text-blue-700 transition">Manage All Merchants →</a>
                    </div>
                </div>
            </div>

            <!-- Global Activity -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-10 py-8 border-b border-slate-100 flex items-center justify-between">
                        <h2 class="text-lg font-black text-slate-900 uppercase tracking-tighter">Live Activity Ledger</h2>
                        <a href="<?php echo url('admin/payments'); ?>" class="text-blue-600 hover:text-blue-700 text-xs font-black uppercase tracking-widest">Full History →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-slate-100">
                            <?php foreach($recentPayments as $payment): ?>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-10 py-6">
                                        <div class="font-mono text-[10px] font-bold text-slate-300"><?php echo $payment->order_id; ?></div>
                                        <div class="font-bold text-slate-800 mt-1"><?php echo htmlspecialchars($payment->meta_data['first_name'] ?? 'Guest'); ?></div>
                                    </td>
                                    <td class="px-10 py-6 text-right">
                                        <div class="font-black text-slate-900">Rs <?php echo number_format($payment->amount, 2); ?></div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase mt-1"><?= $payment->currency ?></div>
                                    </td>
                                    <td class="px-10 py-6 text-center">
                                        <span class="px-4 py-1.5 rounded-full text-[10px] font-black tracking-widest uppercase
                                            <?php
                                                if($payment->status == 'SUCCESS') echo 'bg-emerald-100 text-emerald-700';
                                                elseif($payment->status == 'PENDING') echo 'bg-amber-100 text-amber-700';
                                                else echo 'bg-rose-100 text-rose-700';
                                            ?>">
                                            <?php echo $payment->status; ?>
                                        </span>
                                    </td>
                                    <td class="px-10 py-6 text-right text-[10px] font-bold text-slate-300 uppercase italic">
                                        <?= date('h:i A', strtotime($payment->created_at)) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
