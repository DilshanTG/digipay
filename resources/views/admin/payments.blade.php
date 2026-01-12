<!DOCTYPE html>
<html>
<head>
    <title>Payments - DigiMart Pay Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-slate-900 text-white p-4">
        <div class="container mx-auto flex justify-between">
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
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                {{ session('info') }}
            </div>
        @endif
        <h2 class="text-2xl font-bold mb-6">All Payments</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Order ID</th>
                        <th class="px-4 py-3 text-left">Merchant</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Contact</th>
                        <th class="px-4 py-3 text-left">Amount</th>
                        <th class="px-4 py-3 text-left text-xs uppercase">Description (Real / Fake)</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left text-xs">PayHere Ref</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($payments as $payment)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-sm">{{ $payment->order_id }}</td>
                        <td class="px-4 py-3 text-sm font-semibold">{{ $payment->merchant->name }}</td>
                        <td class="px-4 py-3 text-sm">
                            {{ $payment->meta_data['first_name'] ?? 'Customer' }} {{ $payment->meta_data['last_name'] ?? '' }}
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <div class="font-medium">{{ $payment->customer_email ?? '-' }}</div>
                            <div class="text-gray-500">{{ $payment->customer_phone ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-blue-600">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                        <td class="px-4 py-3 text-xs">
                            <div class="text-gray-900 truncate max-w-xs">{{ $payment->real_description ?? '-' }}</div>
                            <div class="text-gray-400 italic text-[10px] truncate max-w-xs">{{ $payment->fake_description ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase
                                @if($payment->status == 'SUCCESS') bg-green-100 text-green-800
                                @elseif($payment->status == 'PENDING') bg-yellow-100 text-yellow-800
                                @elseif($payment->status == 'CANCELLED') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $payment->status }}
                            </span>
                            <div class="text-[10px] text-gray-400 mt-1">{{ ucfirst($payment->mode) }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 leading-tight">
                            {{ $payment->created_at->format('M d, Y') }}<br>
                            {{ $payment->created_at->format('H:i') }}
                        </td>
                        <td class="px-4 py-3 text-[10px] font-mono text-gray-400">{{ $payment->payhere_ref ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.payments.check-status', $payment->order_id) }}" 
                                   title="Check PayHere API Status"
                                   class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded transition duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.sync') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                    <button type="submit" title="Sync to Supabase" class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded transition duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $payments->links() }}
        </div>
    </div>
</body>
</html>
