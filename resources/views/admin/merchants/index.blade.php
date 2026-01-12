<!DOCTYPE html>
<html>
<head>
    <title>Merchants -DigiMart Pay Admin</title>
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
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Merchants & API Keys</h2>
            <a href="{{ route('admin.merchants.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Add Merchant</a>
        </div>
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">API Key</th>
                        <th class="px-4 py-3 text-left">Allowed Domains</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($merchants as $merchant)
                    <tr class="border-b">
                        <td class="px-4 py-3">{{ $merchant->name }}</td>
                        <td class="px-4 py-3"><code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $merchant->api_key }}</code></td>
                        <td class="px-4 py-3 text-sm">{{ implode(', ', $merchant->allowed_domains ?? []) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs {{ $merchant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $merchant->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.merchants.edit', $merchant) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.merchants.regenerate', $merchant) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-orange-600 hover:underline" onclick="return confirm('Regenerate API key?')">New Key</button>
                            </form>
                            <form action="{{ route('admin.merchants.destroy', $merchant) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
