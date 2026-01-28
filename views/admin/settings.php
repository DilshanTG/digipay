<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gateway Settings - DigiPay Admin</title>
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
                <a href="<?php echo url('admin'); ?>" class="hover:text-blue-400 py-2">Dashboard</a>
                <a href="<?php echo url('admin/merchants'); ?>" class="hover:text-blue-400 py-2">Merchants</a>
                <a href="<?php echo url('admin/payments'); ?>" class="hover:text-blue-400 py-2">Payments</a>
                <a href="<?php echo url('admin/settings'); ?>" class="text-blue-400 border-b-2 border-blue-400 py-2">Settings</a>
                <a href="<?php echo url('admin/docs'); ?>" class="hover:text-blue-400 py-2">Docs</a>
                <div class="h-4 w-px bg-slate-700 mx-2"></div>
                <a href="<?php echo url('admin/logout'); ?>" class="text-rose-400 hover:text-rose-300 font-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-8 max-w-4xl">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">System Configuration</h2>
            <p class="text-slate-500 mt-2 text-lg">Manage how DigiPay connects to the payment network.</p>
        </div>

        <form action="<?= url('admin/config/update') ?>" method="POST">
            
            <!-- Live Environment -->
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-900/5 overflow-hidden mb-12 border border-slate-200">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-white flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/20 shadow-inner">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold">Live Environment</h3>
                        <p class="text-blue-100 opacity-90 text-sm">Real money transactions. Used by active merchants.</p>
                    </div>
                </div>
                
                <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="col-span-full">
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">PayHere Merchant ID (Live)</label>
                         <input type="text" name="settings[payhere_merchant_id_live]" value="<?= htmlspecialchars($settings['payhere_merchant_id_live'] ?? '') ?>" 
                                class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-4 focus:border-blue-600 focus:bg-white transition-all outline-none font-mono font-medium text-lg" placeholder="2XXXXX">
                    </div>
                    <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Merchant Secret (Live)</label>
                         <input type="password" name="settings[payhere_secret_live]" value="<?= htmlspecialchars($settings['payhere_secret_live'] ?? '') ?>" 
                                class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-4 focus:border-blue-600 focus:bg-white transition-all outline-none font-mono" placeholder="••••••••••••••••">
                    </div>
                    <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">App ID (Live)</label>
                         <input type="text" name="settings[payhere_app_id_live]" value="<?= htmlspecialchars($settings['payhere_app_id_live'] ?? '') ?>" 
                                class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-4 focus:border-blue-600 focus:bg-white transition-all outline-none font-mono" placeholder="4XXXXXXXXXX">
                    </div>
                    <div class="col-span-full">
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">App Secret (Live)</label>
                         <input type="password" name="settings[payhere_app_secret_live]" value="<?= htmlspecialchars($settings['payhere_app_secret_live'] ?? '') ?>" 
                                class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-4 focus:border-blue-600 focus:bg-white transition-all outline-none font-mono" placeholder="••••••••••••••••••••••••••••••••">
                    </div>
                </div>
            </div>

            <!-- Sandbox Environment -->
             <div class="bg-white rounded-[2.5rem] shadow-xl shadow-amber-900/5 overflow-hidden mb-12 border border-slate-200 relative">
                <div class="bg-gradient-to-r from-amber-400 to-orange-500 p-8 text-white flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/20 shadow-inner">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold">Sandbox Environment</h3>
                        <p class="text-white/90 text-sm">Testing credentials for merchants in 'Sandbox Mode'.</p>
                    </div>
                </div>
                
                <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                     <div class="col-span-full">
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">PayHere Merchant ID (Sandbox)</label>
                         <input type="text" name="settings[payhere_merchant_id_sandbox]" value="<?= htmlspecialchars($settings['payhere_merchant_id_sandbox'] ?? '') ?>" 
                                class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-4 focus:border-amber-400 focus:bg-white transition-all outline-none font-mono font-medium text-lg" placeholder="SB-XXXXX">
                    </div>
                    
                    <div class="col-span-full border-t border-dashed border-slate-200 pt-6">
                        <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                             Domain Secret Selection
                             <span class="text-[10px] bg-slate-100 px-2 py-1 rounded text-slate-500 uppercase tracking-wide">Advanced</span>
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                            <!-- Localhost Secret -->
                            <div class="relative group">
                                <input type="radio" name="settings[sandbox_domain_selection]" value="localhost" id="sel_local" class="peer sr-only" 
                                    <?= ($settings['sandbox_domain_selection'] ?? 'localhost') === 'localhost' ? 'checked' : '' ?>>
                                <label for="sel_local" class="block p-5 bg-slate-50 border-2 border-slate-200 rounded-2xl cursor-pointer peer-checked:border-amber-400 peer-checked:bg-amber-50/10 transition-all hover:bg-white hover:shadow-md">
                                    <div class="font-bold text-slate-800">Localhost Secret</div>
                                    <div class="text-xs text-slate-500 mt-1 mb-3">For local development (::1)</div>
                                    <input type="password" name="settings[payhere_secret_sandbox_localhost]" value="<?= htmlspecialchars($settings['payhere_secret_sandbox_localhost'] ?? '') ?>" 
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:border-amber-400 outline-none" placeholder="Secret for Localhost">
                                </label>
                            </div>

                            <!-- Digimart Store Secret -->
                            <div class="relative group">
                                <input type="radio" name="settings[sandbox_domain_selection]" value="digimartstore" id="sel_digi" class="peer sr-only"
                                     <?= ($settings['sandbox_domain_selection'] ?? '') === 'digimartstore' ? 'checked' : '' ?>>
                                <label for="sel_digi" class="block p-5 bg-slate-50 border-2 border-slate-200 rounded-2xl cursor-pointer peer-checked:border-amber-400 peer-checked:bg-amber-50/10 transition-all hover:bg-white hover:shadow-md">
                                    <div class="font-bold text-slate-800">Digimart Store Secret</div>
                                    <div class="text-xs text-slate-500 mt-1 mb-3">For production server testing</div>
                                     <input type="password" name="settings[payhere_secret_sandbox_digimartstore]" value="<?= htmlspecialchars($settings['payhere_secret_sandbox_digimartstore'] ?? '') ?>" 
                                        class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:border-amber-400 outline-none" placeholder="Secret for Server">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-full pt-4">
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sandbox App ID (OAuth)</label>
                         <input type="text" name="settings[payhere_app_id_sandbox]" value="<?= htmlspecialchars($settings['payhere_app_id_sandbox'] ?? '') ?>" 
                                class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-4 focus:border-amber-400 focus:bg-white transition-all outline-none font-mono" placeholder="4XXXXXXXXXX">
                    </div>
                     <div class="col-span-full">
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sandbox App Secret (OAuth)</label>
                         <input type="password" name="settings[payhere_app_secret_sandbox]" value="<?= htmlspecialchars($settings['payhere_app_secret_sandbox'] ?? '') ?>" 
                                class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-4 focus:border-amber-400 focus:bg-white transition-all outline-none font-mono" placeholder="••••••••••••••••">
                    </div>
                </div>
            </div>

            <!-- Fake Description Configuration -->
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 overflow-hidden mb-12 border border-slate-200">
                <div class="bg-slate-900 p-8 text-white flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/20 shadow-inner">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold">Privacy Descriptions</h3>
                        <p class="text-slate-400 text-sm">Randomized statement descriptors based on transaction amount.</p>
                    </div>
                </div>
                
                <div class="p-10 grid grid-cols-1 gap-8">
                    <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Under 5,000 LKR</label>
                         <textarea name="settings[fake_descriptions_under_5k]" rows="3" class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-4 focus:border-slate-900 focus:bg-white transition-all outline-none font-medium text-sm leading-relaxed" placeholder="Grocery Items, Mobile Reload..."><?= htmlspecialchars($settings['fake_descriptions_under_5k'] ?? '') ?></textarea>
                         <p class="text-[10px] text-slate-400 mt-2 font-medium">Separate multiple options with commas.</p>
                    </div>
                    <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Under 10,000 LKR</label>
                         <textarea name="settings[fake_descriptions_under_10k]" rows="3" class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-4 focus:border-slate-900 focus:bg-white transition-all outline-none font-medium text-sm leading-relaxed" placeholder="Electronics Accessories, Fashion Wear..."><?= htmlspecialchars($settings['fake_descriptions_under_10k'] ?? '') ?></textarea>
                    </div>
                    <div>
                         <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Over 10,000 LKR</label>
                         <textarea name="settings[fake_descriptions_over_10k]" rows="3" class="w-full bg-slate-50 border-slate-200 border-2 rounded-2xl px-6 py-4 focus:border-slate-900 focus:bg-white transition-all outline-none font-medium text-sm leading-relaxed" placeholder="Web Development Service, Annual Subscription..."><?= htmlspecialchars($settings['fake_descriptions_over_10k'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pb-20">
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-lg px-12 py-5 rounded-3xl font-bold shadow-2xl shadow-slate-300 transition-all hover:scale-105 active:scale-95 flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Save System Settings
                </button>
            </div>
            
        </form>
    </div>
</body>
</html>
