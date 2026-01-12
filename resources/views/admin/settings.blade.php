<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gateway Settings - DigiMart Pay Admin</title>
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
                    <a href="{{ route('admin.settings') }}" class="font-bold text-blue-400">Settings</a>
                    <a href="{{ route('admin.docs') }}" class="hover:text-slate-300 border-l pl-4 border-slate-700">API Docs</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container mx-auto p-8 max-w-4xl">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold">Gateway Configuration</h2>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
                @csrf
                
                <!-- Mode Toggle -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Operational Mode</h3>
                        <p class="text-sm text-gray-500">Enable or disable PayHere Sandbox mode globally.</p>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="payhere_mode" value="sandbox" class="w-4 h-4 text-blue-600" {{ $settings['payhere_mode'] == 'sandbox' ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700 font-medium">Sandbox (Testing)</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="payhere_mode" value="live" class="w-4 h-4 text-red-600" {{ $settings['payhere_mode'] == 'live' ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700 font-medium">Live (Production)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Sandbox Settings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 bg-yellow-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-yellow-800">Sandbox Credentials</h3>
                        <p class="text-sm text-yellow-600">Configure your PayHere sandbox environment.</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sandbox Merchant ID</label>
                            <input type="text" name="payhere_merchant_id_sandbox" value="{{ $settings['payhere_merchant_id_sandbox'] }}" class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-4">
                            <label class="block text-sm font-bold text-gray-800 mb-3">Active Sandbox Domain Secret</label>
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <input type="radio" id="host_local" name="sandbox_domain_selection" value="localhost" class="mt-1" {{ $settings['sandbox_domain_selection'] == 'localhost' ? 'checked' : '' }}>
                                    <div class="ml-3 w-full">
                                        <label for="host_local" class="block text-sm font-medium text-gray-700">localhost (Local Development)</label>
                                        <input type="text" name="payhere_secret_sandbox_localhost" value="{{ $settings['payhere_secret_sandbox_localhost'] }}" class="mt-1 w-full p-2 text-sm border border-gray-300 rounded font-mono">
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <input type="radio" id="host_store" name="sandbox_domain_selection" value="digimartstore" class="mt-1" {{ $settings['sandbox_domain_selection'] == 'digimartstore' ? 'checked' : '' }}>
                                    <div class="ml-3 w-full">
                                        <label for="host_store" class="block text-sm font-medium text-gray-700">digimartstore.lk (Sandbox Store)</label>
                                        <input type="text" name="payhere_secret_sandbox_digimartstore" value="{{ $settings['payhere_secret_sandbox_digimartstore'] }}" class="mt-1 w-full p-2 text-sm border border-gray-300 rounded font-mono">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-800 mb-1">PayHere App Credentials (Retrieval API)</label>
                            <p class="text-xs text-gray-500 mb-4">Required for automatic status checks. Get these from PayHere Settings > API Keys.</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">App ID</label>
                                    <input type="text" name="payhere_app_id_sandbox" value="{{ $settings['payhere_app_id_sandbox'] }}" class="w-full p-2 border border-gray-300 rounded text-sm font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase">App Secret</label>
                                    <input type="password" name="payhere_app_secret_sandbox" value="{{ $settings['payhere_app_secret_sandbox'] }}" class="w-full p-2 border border-gray-300 rounded text-sm font-mono">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Settings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden ring-1 ring-red-50">
                    <div class="p-6 bg-red-50 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-100 rounded-lg text-red-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-red-800 uppercase tracking-tight">Live Credentials (Production)</h3>
                                <p class="text-sm text-red-600">Use with caution. These are your real payment credentials.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Live Merchant ID</label>
                                <input type="text" name="payhere_merchant_id_live" value="{{ $settings['payhere_merchant_id_live'] }}" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-red-50 focus:border-red-200 outline-none transition-all font-medium" placeholder="E.g. 121XXXXX">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Live Merchant Secret</label>
                                <div class="relative">
                                    <input type="password" id="live_secret" name="payhere_secret_live" value="{{ $settings['payhere_secret_live'] }}" class="w-full p-3 pr-12 border border-gray-200 rounded-xl font-mono focus:ring-4 focus:ring-red-50 focus:border-red-200 outline-none transition-all text-sm" placeholder="Your Merchant Secret Key">
                                    <button type="button" onclick="toggleSecret('live_secret')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-100">
                            <label class="block text-xs font-black text-gray-800 uppercase tracking-widest mb-4">Live App Credentials (Retrieval API)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 mb-1 uppercase">App ID</label>
                                    <input type="text" name="payhere_app_id_live" value="{{ $settings['payhere_app_id_live'] }}" class="w-full p-3 border border-gray-200 rounded-xl text-sm font-mono focus:ring-4 focus:ring-red-50 outline-none" placeholder="App ID from Settings > API Keys">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 mb-1 uppercase">App Secret</label>
                                    <div class="relative">
                                        <input type="password" id="live_app_secret" name="payhere_app_secret_live" value="{{ $settings['payhere_app_secret_live'] }}" class="w-full p-3 pr-12 border border-gray-200 rounded-xl text-sm font-mono focus:ring-4 focus:ring-red-50 outline-none" placeholder="App Secret">
                                        <button type="button" onclick="toggleSecret('live_app_secret')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fake Descriptions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 bg-slate-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">🎭 Customizable Fake Descriptions</h3>
                        <p class="text-sm text-gray-500">Protect your PayHere account by randomizing the item descriptions sent to them.</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <h4 class="font-bold text-blue-800 text-sm mb-2">Available Placeholders</h4>
                            <div class="grid grid-cols-2 gap-2 text-xs text-blue-700">
                                <div><code class="font-bold">{name}</code> - Customer First Name</div>
                                <div><code class="font-bold">{email}</code> - Customer Email</div>
                                <div><code class="font-bold">{mobile}</code> - Customer Phone</div>
                                <div><code class="font-bold">CURRENT_MONTH</code> - Current short month (e.g., jan, feb)</div>
                            </div>
                            <p class="text-[10px] text-blue-600 mt-2 italic">* Enter one template per line.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Under 5,000 LKR Templates</label>
                            <textarea name="fake_descriptions_under_5k" rows="6" class="w-full p-2 text-sm border border-gray-300 rounded font-mono focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g., {name} - graphic design">{{ $settings['fake_descriptions_under_5k'] }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">5,000 - 10,000 LKR Templates</label>
                            <textarea name="fake_descriptions_under_10k" rows="6" class="w-full p-2 text-sm border border-gray-300 rounded font-mono focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g., {name} - email campaign">{{ $settings['fake_descriptions_under_10k'] }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Over 10,000 LKR Templates</label>
                            <textarea name="fake_descriptions_over_10k" rows="6" class="w-full p-2 text-sm border border-gray-300 rounded font-mono focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g., {name} - website design">{{ $settings['fake_descriptions_over_10k'] }}</textarea>
                        </div>
                    </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-10 py-3 rounded-lg font-bold shadow-lg transition duration-200">
                        Save Global Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function toggleSecret(id) {
            const input = document.getElementById(id);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    </script>
</body>
</html>
