<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proofs - DigiMart Pay Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
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
                    <a href="{{ route('admin.settings') }}" class="hover:text-slate-300">Settings</a>
                    <a href="{{ route('admin.docs') }}" class="hover:text-slate-300 border-l pl-4 border-slate-700">API Docs</a>
                </div>
            </div>
        </nav>

        <div class="container mx-auto p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Proof Generation</h2>
                    <p class="text-slate-500">Search transactions to generate invoices and delivery proofs.</p>
                </div>
                
                <form action="{{ route('admin.proofs.index') }}" method="GET" class="flex w-full md:w-96 shadow-sm">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by PayHere ID, Order ID..." 
                           class="flex-1 px-4 py-2 rounded-l-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-r-lg font-semibold hover:bg-blue-700 transition">
                        Search
                    </button>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50/50 text-slate-500 text-left text-xs uppercase tracking-wider">
                                <th class="px-8 py-4 font-bold">Order ID</th>
                                <th class="px-8 py-4 font-bold">PayHere Ref</th>
                                <th class="px-8 py-4 font-bold">Customer</th>
                                <th class="px-8 py-4 font-bold text-right">Amount</th>
                                <th class="px-8 py-4 font-bold text-center">Status</th>
                                <th class="px-8 py-4 font-bold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-8 py-5">
                                    <div class="font-mono text-sm font-semibold text-slate-900">{{ $payment->order_id }}</div>
                                    <div class="text-xs text-slate-400 mt-1">{{ $payment->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-medium text-slate-700">{{ $payment->payhere_ref ?? 'N/A' }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-medium text-slate-900">{{ $payment->customer_email ?? 'No email' }}</div>
                                    <div class="text-xs text-slate-400">{{ $payment->customer_phone ?? '' }}</div>
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
                                <td class="px-8 py-5 text-right space-x-2">
                                    <a href="{{ route('admin.proofs.invoice', $payment) }}" target="_blank" 
                                       class="inline-flex items-center px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition">
                                        📝 Invoice
                                    </a>
                                    <a href="{{ route('admin.proofs.email', $payment) }}" target="_blank" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-100 transition">
                                        📧 Email Proof
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-10 text-center text-slate-500">
                                    No transactions found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($payments->hasPages())
                    <div class="px-8 py-4 border-t border-slate-100">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
