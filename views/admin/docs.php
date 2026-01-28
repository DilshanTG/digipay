<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - DigiPay Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        pre { background: #1e293b; color: #f8fafc; border-radius: 1rem; padding: 1.5rem; overflow-x: auto; font-size: 0.825rem; line-height: 1.6; }
        code { font-family: 'Fira Code', 'JetBrains Mono', monospace; }
        .token.string { color: #7dd3fc; }
        .token.number { color: #f472b6; }
        .token.property { color: #94a3b8; }
        .method { padding: 4px 12px; border-radius: 6px; font-weight: 800; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body class="bg-white min-h-screen">
    <nav class="bg-slate-900 text-white p-4 sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold flex items-center gap-2">
                <span class="bg-blue-600 p-1.5 rounded-lg text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </span>
                DigiPay Admin
            </h1>
            <div class="space-x-4 flex items-center text-sm font-medium">
                <a href="<?php echo url('admin'); ?>" class="hover:text-blue-400 py-2">Dashboard</a>
                <a href="<?php echo url('admin/merchants'); ?>" class="hover:text-blue-400 py-2">Merchants</a>
                <a href="<?php echo url('admin/payments'); ?>" class="hover:text-blue-400 py-2">Payments</a>
                <a href="<?php echo url('admin/settings'); ?>" class="hover:text-blue-400 py-2">Settings</a>
                <a href="<?php echo url('admin/docs'); ?>" class="text-blue-400 border-b-2 border-blue-400 py-2">Docs</a>
                <div class="h-4 w-px bg-slate-700 mx-2"></div>
                <a href="<?php echo url('admin/logout'); ?>" class="text-rose-400 hover:text-rose-300 font-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-12 max-w-5xl">
        <div class="mb-16">
            <div class="inline-block px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-xs font-bold uppercase tracking-widest mb-4">Developer Hub</div>
            <h2 class="text-5xl font-black text-slate-900 tracking-tighter leading-tight">100% Seamless<br>API Integration</h2>
            <p class="text-slate-500 mt-6 text-xl max-w-2xl leading-relaxed">Migrate your Laravel apps to our high-performance API with zero code changes. Use your existing endpoints and structures.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-28 space-y-2">
                    <a href="#auth" class="block px-4 py-2 rounded-lg bg-slate-50 font-bold text-slate-900 transition hover:bg-blue-50 hover:text-blue-600">Authentication</a>
                    <a href="#init" class="block px-4 py-2 rounded-lg text-slate-500 font-bold tracking-tight hover:bg-slate-50 transition">Initialize Payment</a>
                    <a href="#status" class="block px-4 py-2 rounded-lg text-slate-500 font-bold tracking-tight hover:bg-slate-50 transition">Check Status</a>
                    <a href="#webhooks" class="block px-4 py-2 rounded-lg text-slate-500 font-bold tracking-tight hover:bg-slate-50 transition">Webhooks</a>
                    <a href="#errors" class="block px-4 py-2 rounded-lg text-slate-500 font-bold tracking-tight hover:bg-slate-50 transition">Error Codes</a>
                </div>
            </div>

            <div class="lg:col-span-3 space-y-24">
                <!-- Authentication -->
                <section id="auth">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-lg">01</span>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight">Security & Auth</h3>
                    </div>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed mb-8">
                        Our API uses Bearer Tokens for authentication. Pass your <span class="font-bold text-slate-900 border-b-2 border-blue-100">Merchant API Key</span> in the Authorization header of every request.
                    </div>
                    <pre><code><span class="token property">Authorization</span>: Bearer sk_live_your_merchant_key</code></pre>
                </section>

                <!-- Initialize -->
                <section id="init">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <span class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-lg">02</span>
                            <h3 class="text-3xl font-black text-slate-900 tracking-tight">Initialize Checkout</h3>
                        </div>
                        <span class="method bg-emerald-500 text-white">POST</span>
                    </div>
                    <div class="mb-8 p-4 bg-slate-900 rounded-xl">
                        <code class="text-emerald-400 text-sm">https://pay.digimartsolutions.lk/v2/api/v1/init</code>
                    </div>
                    
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Request Parameters (JSON Body)</h4>
                    <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden mb-8 shadow-sm">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 font-black text-slate-400 uppercase tracking-tighter text-[10px]">
                                <tr>
                                    <th class="px-6 py-4 text-left">Property</th>
                                    <th class="px-6 py-4 text-left">Type</th>
                                    <th class="px-6 py-4 text-left">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-slate-600">
                                <tr><td class="px-6 py-4 font-bold text-slate-900">amount</td><td class="px-6 py-4 text-xs font-mono">numeric</td><td class="px-6 py-4">Required. Minimum 1.00</td></tr>
                                <tr><td class="px-6 py-4 font-bold text-slate-900">return_url</td><td class="px-6 py-4 text-xs font-mono">url</td><td class="px-6 py-4">Optional. User goes here after payment.</td></tr>
                                <tr><td class="px-6 py-4 font-bold text-slate-900">notify_url</td><td class="px-6 py-4 text-xs font-mono">url</td><td class="px-6 py-4">Optional. Override default webhook endpoint.</td></tr>
                                <tr><td class="px-6 py-4 font-bold text-slate-900">client_order_id</td><td class="px-6 py-4 text-xs font-mono">string</td><td class="px-6 py-4">Your internal reference number.</td></tr>
                                <tr><td class="px-6 py-4 font-bold text-slate-900">customer_email</td><td class="px-6 py-4 text-xs font-mono">email</td><td class="px-6 py-4">For digital receipt delivery.</td></tr>
                                <tr><td class="px-6 py-4 font-bold text-slate-900">first_name</td><td class="px-6 py-4 text-xs font-mono">string</td><td class="px-6 py-4">Customer's identity for personalized receipts.</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Sample Response</h4>
                    <pre><code>{
  <span class="token string">"status"</span>: <span class="token string">"success"</span>,
  <span class="token string">"data"</span>: {
    <span class="token string">"order_id"</span>: <span class="token string">"ORD-8A39BD04F1"</span>,
    <span class="token string">"payment_url"</span>: <span class="token string">"https://pay.digimartsolutions.lk/v2/pay/jump/ORD-8A39BD04F1"</span>
  }
}</code></pre>
                </section>

                <!-- Check Status -->
                <section id="status">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <span class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-black text-lg">03</span>
                            <h3 class="text-3xl font-black text-slate-900 tracking-tight">Verify Transaction</h3>
                        </div>
                        <span class="method bg-amber-500 text-white">GET</span>
                    </div>
                    <div class="mb-8 p-4 bg-slate-900 rounded-xl">
                        <code class="text-amber-400 text-sm">https://pay.digimartsolutions.lk/v2/api/v1/status/{order_id}</code>
                    </div>
                
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Status Values</h4>
                    <div class="flex gap-2 mb-8">
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold">SUCCESS</span>
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-bold">PENDING</span>
                        <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-lg text-xs font-bold">FAILED</span>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">CANCELLED</span>
                    </div>

                    <pre><code>{
  <span class="token string">"status"</span>: <span class="token string">"success"</span>,
  <span class="token string">"data"</span>: {
    <span class="token string">"order_id"</span>: <span class="token string">"ORD-89F123"</span>,
    <span class="token string">"status"</span>: <span class="token string">"SUCCESS"</span>,
    <span class="token string">"amount"</span>: <span class="token number">1500.00</span>,
    <span class="token string">"payhere_ref"</span>: <span class="token string">"320011223344"</span>
  }
}</code></pre>
                </section>

                <!-- Webhooks -->
                <section id="webhooks">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-lg">04</span>
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight">Webhook Security</h3>
                    </div>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed mb-8">
                        We send a POST request to your <code>notify_url</code> upon completion. Verify authenticity by hashing your <span class="font-bold text-slate-900">Secret Key</span>.
                    </div>
                    <div class="p-6 bg-slate-50 border-2 border-slate-100 rounded-2xl mb-8">
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">HMAC Signature Recipe</h4>
                        <code class="text-slate-800 text-xs font-mono break-all font-bold">hash = md5( merchant_id + order_id + amount + currency + status_code + md5(secret_key) )</code>
                    </div>
                </section>

            </div>
        </div>

        <footer class="mt-32 pt-10 border-t border-slate-100 flex justify-between items-center text-slate-400 text-sm font-medium">
            <p>&copy; <?= date('Y') ?> DigiMart Solutions. Built for High Velocity.</p>
            <div class="flex gap-4">
                <a href="#" class="hover:text-blue-600">Privacy</a>
                <a href="#" class="hover:text-blue-600">Terms</a>
            </div>
        </footer>
    </div>
</body>
</html>
