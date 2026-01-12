<!DOCTYPE html>
<html>
<head>
    <title>Add Merchant - DigiMart Pay</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-slate-900 text-white p-4">
        <div class="container mx-auto">
            <a href="{{ route('admin.merchants.index') }}" class="hover:text-slate-300">← Back to Merchants</a>
        </div>
    </nav>
    <div class="container mx-auto p-8 max-w-2xl">
        <h2 class="text-2xl font-bold mb-6">Add New Merchant</h2>
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.merchants.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-2">Merchant Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg" placeholder="e.g., ABC Company">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Domain (will be auto-whitelisted)</label>
                    <input type="text" name="domain" required class="w-full px-4 py-2 border rounded-lg" placeholder="abc.com">
                    <p class="text-xs text-gray-500 mt-1">You can add more domains later by editing the merchant</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Default Return URL (Success)</label>
                        <input type="url" name="return_url" class="w-full px-4 py-2 border rounded-lg" placeholder="https://abc.com/success">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Default Cancel URL</label>
                        <input type="url" name="cancel_url" class="w-full px-4 py-2 border rounded-lg" placeholder="https://abc.com/cancel">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Default Notify URL (Webhook)</label>
                    <input type="url" name="notify_url" class="w-full px-4 py-2 border rounded-lg" placeholder="https://abc.com/api/notify">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 w-full font-semibold">
                    Create Merchant & Generate API Key
                </button>
            </form>
        </div>
    </div>
</body>
</html>
