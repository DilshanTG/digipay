<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchants - DigiPay Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
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
                <a href="<?php echo url('admin/merchants'); ?>" class="text-blue-400 border-b-2 border-blue-400 py-2">Merchants</a>
                <a href="<?php echo url('admin/payments'); ?>" class="hover:text-blue-400 py-2">Payments</a>
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
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Merchants & API Keys</h2>
                <p class="text-slate-500 mt-1">Manage partner accounts and their integration credentials.</p>
            </div>
            <button onclick="openModal()" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 font-bold flex items-center gap-2 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New Merchant
            </button>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-500 text-left border-b border-slate-100 uppercase tracking-wider text-[10px] font-bold">
                            <th class="px-8 py-5">Merchant Identity</th>
                            <th class="px-8 py-5">Security Keys</th>
                            <th class="px-8 py-5 text-center">Status</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php foreach($merchants as $merchant): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-base"><?php echo htmlspecialchars($merchant->name); ?></div>
                                        <div class="text-slate-400 text-xs mt-0.5"><?php echo $merchant->sandbox_mode ? '🧪 Sandbox Environment' : '💳 Live Environment'; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-2 group/copy cursor-pointer" onclick="copyToClipboard('<?= $merchant->api_key ?>', 'Merchant ID')">
                                        <span class="text-[10px] font-bold text-slate-300 uppercase w-16">Merchant ID</span>
                                        <code class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg font-mono text-xs border border-blue-100 group-hover/copy:bg-blue-100 transition flex items-center gap-2">
                                            <?php echo $merchant->api_key; ?>
                                            <svg class="w-3 h-3 opacity-0 group-hover/copy:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </code>
                                    </div>
                                    <div class="flex items-center gap-2 group/copy cursor-pointer" onclick="copyToClipboard('<?= $merchant->secret_key ?>', 'Secret Key')">
                                        <span class="text-[10px] font-bold text-slate-300 uppercase w-16">Secret</span>
                                        <code class="bg-slate-50 px-3 py-1 rounded-lg font-mono text-xs filter blur-[3px] hover:blur-none transition-all border border-slate-100 group-hover/copy:bg-slate-100 flex items-center gap-2">
                                            <?php echo $merchant->secret_key; ?>
                                            <svg class="w-3 h-3 opacity-0 group-hover/copy:opacity-100 transition text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </code>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <a href="<?= url('admin/merchants/toggle/'.$merchant->id) ?>" class="inline-block">
                                    <span class="px-4 py-1.5 rounded-full text-[10px] font-bold tracking-widest uppercase transition-all hover:scale-105 active:scale-95 <?php echo $merchant->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'; ?>">
                                        <?php echo $merchant->is_active ? 'Active' : 'Disabled'; ?>
                                    </span>
                                </a>
                            </td>
                            <td class="px-8 py-6 text-right space-x-2">
                                <button onclick='editMerchant(<?= json_encode($merchant->toArray()) ?>)' class="text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-xl font-bold transition">Edit</button>
                                <a href="<?= url('admin/merchants/regenerate/'.$merchant->id) ?>" class="text-amber-600 hover:bg-amber-50 px-4 py-2 rounded-xl font-bold transition" onclick="return confirm('Regenerate keys? Existing integrations will break.')">Regenerate</a>
                                <?php if($merchant->id != 1): ?>
                                <a href="<?= url('admin/merchants/delete/'.$merchant->id) ?>" class="text-slate-400 hover:text-rose-600 hover:bg-rose-50 px-4 py-2 rounded-xl font-bold transition" onclick="return confirm('Archive this merchant? This cannot be undone.')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Merchant Modal -->
    <div id="merchantModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
        <div class="bg-white rounded-[2.5rem] w-full max-w-2xl shadow-2xl animate-in zoom-in duration-300 overflow-hidden">
            <div class="px-10 py-8 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 id="modalTitle" class="text-2xl font-extrabold text-slate-900 tracking-tight">Add Merchant</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition p-2 hover:bg-white rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="<?= url('admin/merchants/save') ?>" method="POST" class="p-10">
                <input type="hidden" name="id" id="merchant_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Display Name</label>
                            <input type="text" name="name" id="merchant_name" required class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-3.5 focus:border-blue-600 focus:bg-white transition-all outline-none font-medium" placeholder="My Enterprise Store">
                        </div>
                        <div id="keyFields" class="hidden space-y-6 border-l-4 border-blue-100 pl-4 py-2 bg-blue-50/30 rounded-r-2xl">
                            <div>
                                <label class="block text-xs font-bold text-blue-400 uppercase tracking-wider mb-2">API Key (Read-only)</label>
                                <input type="text" name="api_key" id="merchant_api_key" readonly class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-3.5 transition-all outline-none font-mono text-sm text-slate-400 italic" placeholder="Auto-generated">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-blue-400 uppercase tracking-wider mb-2">Secret Key (Read-only)</label>
                                <input type="text" name="secret_key" id="merchant_secret_key" readonly class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-3.5 transition-all outline-none font-mono text-sm text-slate-400 italic" placeholder="Auto-generated">
                            </div>
                        </div>
                        <div id="newMerchantNote" class="text-xs text-slate-400 italic p-4 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                             API and Secret keys will be <b>automatically generated</b> after you save.
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Allowed Domains</label>
                            <input type="text" name="allowed_domains" id="merchant_domains" class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-3.5 focus:border-blue-600 focus:bg-white transition-all outline-none font-medium" placeholder="*, example.com">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Return URL</label>
                            <input type="url" name="return_url" id="merchant_return_url" class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-3.5 focus:border-blue-600 focus:bg-white transition-all outline-none font-medium text-xs" placeholder="https://site.com/return">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Cancel URL</label>
                            <input type="url" name="cancel_url" id="merchant_cancel_url" class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-3.5 focus:border-blue-600 focus:bg-white transition-all outline-none font-medium text-xs" placeholder="https://site.com/cancel">
                        </div>
                         <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Notify URL (Webhook)</label>
                            <input type="url" name="notify_url" id="merchant_notify_url" class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-3.5 focus:border-blue-600 focus:bg-white transition-all outline-none font-medium text-xs" placeholder="https://site.com/notify">
                        </div>
                        <div class="p-6 bg-slate-50 rounded-2xl border-2 border-slate-200 border-dashed">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative inline-flex items-center">
                                    <input type="checkbox" name="is_active" id="merchant_active" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                </div>
                                <span class="text-sm font-bold text-slate-700">Account Active</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group mt-4">
                                <div class="relative inline-flex items-center">
                                    <input type="checkbox" name="sandbox_mode" id="merchant_sandbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                </div>
                                <span class="text-sm font-bold text-slate-700">Sandbox Mode</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-8 py-3.5 rounded-2xl text-slate-500 font-bold hover:bg-slate-50 transition active:scale-95">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-10 py-3.5 rounded-2xl font-bold transition shadow-xl shadow-blue-100 hover:bg-blue-700 active:scale-95">Save Merchant</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalTitle').innerText = 'Add Merchant';
            document.getElementById('merchant_id').value = '';
            document.getElementById('merchant_name').value = '';
            document.getElementById('merchant_domains').value = '*';
            document.getElementById('merchant_return_url').value = '';
            document.getElementById('merchant_cancel_url').value = '';
            document.getElementById('merchant_notify_url').value = '';
            document.getElementById('merchant_active').checked = true;
            document.getElementById('merchant_sandbox').checked = false;
            
            document.getElementById('keyFields').classList.add('hidden');
            document.getElementById('newMerchantNote').classList.remove('hidden');
            
            document.getElementById('merchantModal').classList.remove('hidden');
            document.getElementById('merchantModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('merchantModal').classList.add('hidden');
            document.getElementById('merchantModal').classList.remove('flex');
        }

        function editMerchant(merchant) {
            document.getElementById('modalTitle').innerText = 'Edit Merchant';
            document.getElementById('merchant_id').value = merchant.id;
            document.getElementById('merchant_name').value = merchant.name;
            document.getElementById('merchant_api_key').value = merchant.api_key;
            document.getElementById('merchant_secret_key').value = merchant.secret_key || '';
            document.getElementById('merchant_domains').value = Array.isArray(merchant.allowed_domains) ? merchant.allowed_domains.join(', ') : (merchant.allowed_domains || '*');
            document.getElementById('merchant_return_url').value = merchant.return_url || '';
            document.getElementById('merchant_cancel_url').value = merchant.cancel_url || '';
            document.getElementById('merchant_notify_url').value = merchant.notify_url || '';
            document.getElementById('merchant_active').checked = merchant.is_active;
            document.getElementById('merchant_sandbox').checked = merchant.sandbox_mode;
            
            document.getElementById('keyFields').classList.remove('hidden');
            document.getElementById('newMerchantNote').classList.add('hidden');
            
            document.getElementById('merchantModal').classList.remove('hidden');
            document.getElementById('merchantModal').classList.add('flex');
        }

        function copyToClipboard(text, label) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-5 right-5 bg-slate-900 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 z-[200] animate-in slide-in-from-bottom duration-300';
                toast.innerHTML = `<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> <span class="font-bold">${label} Copied!</span>`;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'transition-opacity');
                    setTimeout(() => toast.remove(), 300);
                }, 2000);
            });
        }
    </script>
</body>
</html>
