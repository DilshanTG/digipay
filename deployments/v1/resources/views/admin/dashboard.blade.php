<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DigiMart Pay</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <nav class="bg-slate-900 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-xl font-bold">DigiMart Pay Admin</h1>
                <div class="space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-300">Dashboard</a>
                    <a href="{{ route('admin.merchants.index') }}" class="hover:text-slate-300">Merchants</a>
                    <a href="{{ route('admin.payments') }}" class="hover:text-slate-300">Payments</a>
                    <a href="{{ route('admin.proofs.index') }}" class="hover:text-slate-300">Proofs</a>
                    <a href="{{ route('admin.settings') }}" class="hover:text-slate-300">Settings</a>
                    <a href="{{ route('admin.docs') }}" class="hover:text-slate-300 border-l pl-4 border-slate-700">API Docs</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container mx-auto p-8">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-800 flex items-center">
                    <span class="mr-2">💰</span> Revenue Statistics
                </h2>
                <form action="{{ route('admin.stats.reset') }}" method="POST" onsubmit="return confirm('CRITICAL: This will permanently delete ALL payment history and reset all stats to zero. This action cannot be undone. Are you absolutely sure?')">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-4 py-2 rounded-lg transition-all border border-rose-100 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Reset All Stats
                    </button>
                </form>
            </div>
            <!-- Revenue Overview -->
            <div>
                <h2 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                    <span class="mr-2">💰</span> Revenue Statistics
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Today -->
                    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-6 rounded-2xl shadow-lg text-white">
                        <div class="text-blue-100 text-sm font-medium uppercase tracking-wider mb-1">Today</div>
                        <div class="text-3xl font-bold">Rs {{ number_format($stats['revenue_today'], 2) }}</div>
                        <div class="mt-4 text-blue-100 text-xs">Since midnight</div>
                    </div>
                    <!-- This Week -->
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-6 rounded-2xl shadow-lg text-white">
                        <div class="text-emerald-100 text-sm font-medium uppercase tracking-wider mb-1">This Week</div>
                        <div class="text-3xl font-bold">Rs {{ number_format($stats['revenue_week'], 2) }}</div>
                        <div class="mt-4 text-emerald-100 text-xs">Last 7 days</div>
                    </div>
                    <!-- This Month -->
                    <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-6 rounded-2xl shadow-lg text-white">
                        <div class="text-amber-100 text-sm font-medium uppercase tracking-wider mb-1">This Month</div>
                        <div class="text-3xl font-bold">Rs {{ number_format($stats['revenue_month'], 2) }}</div>
                        <div class="mt-4 text-amber-100 text-xs">{{ now()->format('F Y') }}</div>
                    </div>
                    <!-- Total Revenue -->
                    <div class="bg-gradient-to-br from-slate-700 to-slate-900 p-6 rounded-2xl shadow-lg text-white">
                        <div class="text-slate-300 text-sm font-medium uppercase tracking-wider mb-1">Total Revenue</div>
                        <div class="text-3xl font-bold">Rs {{ number_format($stats['revenue_total'], 2) }}</div>
                        <div class="mt-4 text-slate-300 text-xs">Lifetime Earnings</div>
                    </div>
                </div>
            </div>

            <!-- Operational Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                    <div class="text-slate-500 text-xs font-bold uppercase mb-1">Total Transactions</div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['total_payments'] }}</div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-emerald-100">
                    <div class="text-emerald-600 text-xs font-bold uppercase mb-1">Success Rate</div>
                    @php
                        $rate = $stats['total_payments'] > 0 ? ($stats['successful'] / $stats['total_payments']) * 100 : 0;
                    @endphp
                    <div class="text-2xl font-bold text-emerald-700">{{ number_format($rate, 1) }}%</div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-yellow-100">
                    <div class="text-yellow-600 text-xs font-bold uppercase mb-1">Pending</div>
                    <div class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-blue-100">
                    <div class="text-blue-600 text-xs font-bold uppercase mb-1">Total Merchants</div>
                    <div class="text-2xl font-bold text-blue-700">{{ $stats['total_merchants'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Merchant Breakdown -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden h-full">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <h2 class="font-bold text-slate-800">Merchant Revenue</h2>
                        </div>
                        <div class="p-0">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-slate-400 text-left border-b border-slate-100">
                                        <th class="px-6 py-3 font-semibold">Merchant</th>
                                        <th class="px-6 py-3 font-semibold text-right">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($merchantBreakdown as $m)
                                    <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 font-medium text-slate-700">{{ $m->name }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="font-bold text-slate-900">Rs {{ number_format($m->total_revenue, 0) }}</div>
                                            <div class="text-xs text-slate-400">{{ $m->payment_count }} sales</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Supabase Sync -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Cloud Sync (Supabase)</h2>
                                <p class="text-slate-500 text-sm mt-1">Synchronize your payment data with external systems.</p>
                            </div>
                            <div class="h-12 w-12 bg-blue-50 rounded-full flex items-center justify-center text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                </svg>
                            </div>
                        </div>
                        
                        <form action="{{ route('admin.sync') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white px-6 py-4 rounded-xl font-bold transition shadow-lg shadow-slate-200 flex items-center justify-center group">
                                <span>Sync All Successful Payments</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:translate-y-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </button>
                        </form>
                        
                        @if(session('success'))
                            <div class="mt-6 bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-xl flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-800">Recent Transactions</h2>
                    <a href="{{ route('admin.payments') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">View All →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50/50 text-slate-500 text-left text-xs uppercase tracking-wider">
                                <th class="px-8 py-4 font-bold">Order Details</th>
                                <th class="px-8 py-4 font-bold">Merchant</th>
                                <th class="px-8 py-4 font-bold text-right">Amount</th>
                                <th class="px-8 py-4 font-bold text-center">Status</th>
                                <th class="px-8 py-4 font-bold">Mode</th>
                                <th class="px-8 py-4 font-bold text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        @foreach($recentPayments as $payment)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-8 py-5">
                                    <div class="font-mono text-sm font-semibold text-slate-900">{{ $payment->order_id }}</div>
                                    <div class="text-xs text-slate-400 mt-1">{{ $payment->customer_email ?? 'No email' }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="font-medium text-slate-700">{{ $payment->merchant->name }}</div>
                                </td>
                                <td class="px-8 py-5 text-right font-bold text-slate-900">
                                    {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase
                                        @if($payment->status == 'SUCCESS') bg-emerald-100 text-emerald-700
                                        @elseif($payment->status == 'PENDING') bg-amber-100 text-amber-700
                                        @else bg-rose-100 text-rose-700 @endif">
                                        {{ $payment->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">{{ ucfirst($payment->mode) }}</span>
                                </td>
                                <td class="px-8 py-5 text-right text-xs text-slate-400">
                                    {{ $payment->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
