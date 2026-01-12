<!DOCTYPE html>
<html>
<head>
    <title>Edit Merchant - DigiMart Pay</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-slate-900 text-white p-4">
        <div class="container mx-auto">
            <a href="{{ route('admin.merchants.index') }}" class="hover:text-slate-300">← Back to Merchants</a>
        </div>
    </nav>
    <div class="container mx-auto p-8 max-w-2xl">
        <h2 class="text-2xl font-bold mb-6">Edit Merchant</h2>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Validation Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.merchants.update', $merchant) }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium mb-2">Merchant Name</label>
                    <input type="text" name="name" value="{{ $merchant->name }}" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Allowed Domains (comma-separated)</label>
                    <input type="text" name="domains" value="{{ implode(', ', $merchant->allowed_domains ?? []) }}" required class="w-full px-4 py-2 border rounded-lg" placeholder="abc.com, xyz.com">
                    <p class="text-xs text-gray-500 mt-1">Use * for wildcard (all domains)</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Default Return URL (Success)</label>
                        <input type="url" name="return_url" value="{{ $merchant->return_url }}" class="w-full px-4 py-2 border rounded-lg" placeholder="https://abc.com/success">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Default Cancel URL</label>
                        <input type="url" name="cancel_url" value="{{ $merchant->cancel_url }}" class="w-full px-4 py-2 border rounded-lg" placeholder="https://abc.com/cancel">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Default Notify URL (Webhook)</label>
                    <input type="url" name="notify_url" value="{{ $merchant->notify_url }}" class="w-full px-4 py-2 border rounded-lg" placeholder="https://abc.com/api/notify">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" {{ $merchant->is_active ? 'checked' : '' }} class="mr-2">
                    <label for="is_active" class="text-sm font-medium">Active</label>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 w-full font-semibold">
                    Update Merchant
                </button>
            </form>
        </div>
    </div>
</body>
</html>
