<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow,noarchive,nosnippet">
    <title>Sent - Zoho Mail (info@digimartsolutions.lk)</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon/zoho.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #ffffff; margin: 0; overflow: hidden; height: 100vh; font-size: 13px; }
        
        /* Layout Colors */
        :root {
            --zoho-dark-header: #1e293b;
            --zoho-black-sidebar: #09090b;
            --zoho-folder-sidebar: #1e293b;
            --zoho-active-blue: #3b82f6;
            --zoho-border: #eef2f6;
            --zoho-text-gray: #94a3b8;
        }

        /* Top Header */
        .top-header { height: 48px; background: var(--zoho-dark-header); border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: space-between; padding: 0 16px; flex-shrink: 0; z-index: 50; }
        .zoho-logo-area { display: flex; align-items: center; gap: 8px; color: white; font-weight: 700; font-size: 16px; width: 230px; }
        .search-area { background: rgba(255,255,255,0.1); border-radius: 6px; padding: 6px 12px; width: 440px; display: flex; align-items: center; gap: 10px; color: #94a3b8; font-size: 13px; }

        /* Leftmost Icon Bar (Black) */
        .icon-bar { width: 52px; background: var(--zoho-black-sidebar); display: flex; flex-direction: column; align-items: center; padding-top: 10px; flex-shrink: 0; }
        .app-icon { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; color: #71717a; border-radius: 8px; margin-bottom: 8px; cursor: pointer; transition: 0.2s; }
        .app-icon:hover { background: rgba(255,255,255,0.1); color: white; }
        .app-icon.active { color: white; background: rgba(255,255,255,0.1); position: relative; }
        .app-icon.active::after { content: ''; position: absolute; left: -12px; top: 8px; bottom: 8px; width: 3px; background: var(--zoho-active-blue); border-radius: 0 4px 4px 0; }

        /* Nav Sidebar (Folders) */
        .nav-sidebar { width: 230px; background: var(--zoho-folder-sidebar); display: flex; flex-direction: column; flex-shrink: 0; color: var(--zoho-text-gray); border-right: 1px solid rgba(255,255,255,0.05); }
        .new-mail-wrap { padding: 16px; }
        .new-mail-btn { background: var(--zoho-active-blue); color: white; border-radius: 6px; font-weight: 600; font-size: 13px; display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; width: 100%; box-shadow: 0 4px 12px -4px rgba(59,130,246,0.5); }
        
        .nav-section-title { font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; padding: 14px 20px 6px; display: flex; align-items: center; justify-content: space-between; }
        .nav-item { padding: 8px 20px; font-size: 13px; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: 0.1s; }
        .nav-item:hover { background: rgba(255,255,255,0.05); color: #e2e8f0; }
        .nav-item.active { background: rgba(255,255,255,0.08); color: white; font-weight: 600; }

        /* Middle Pane (Email List) */
        .list-pane { width: 320px; background: white; border-right: 1px solid var(--zoho-border); display: flex; flex-direction: column; flex-shrink: 0; }
        .list-header { height: 44px; border-bottom: 1px solid var(--zoho-border); display: flex; align-items: center; padding: 0 16px; font-weight: 600; gap: 8px; }
        .list-search { padding: 10px 16px; border-bottom: 1px solid var(--zoho-border); }
        .email-card { padding: 12px 16px; border-bottom: 1px solid var(--zoho-border); cursor: pointer; position: relative; }
        .email-card.active { background: #f0f7ff; }
        .email-card.active::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--zoho-active-blue); }

        /* Right Content Pane */
        .content-pane { flex: 1; display: flex; flex-direction: column; background: white; overflow: hidden; position: relative; }
        .content-toolbar { height: 44px; border-bottom: 1px solid var(--zoho-border); display: flex; align-items: center; padding: 0 24px; gap: 16px; color: #64748b; }
        .toolbar-btn { display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 500; font-size: 12px; }
        .toolbar-btn:hover { color: #0f172a; }

        /* Main Email View */
        .main-view { flex: 1; overflow-y: auto; background: #ffffff; padding: 40px; }
        .email-header-info { margin-bottom: 40px; }
        .sender-block { display: flex; align-items: flex-start; gap: 16px; }
        
        /* Rightmost Utility Bar */
        .utility-bar { width: 44px; background: white; border-left: 1px solid var(--zoho-border); display: flex; flex-direction: column; align-items: center; padding-top: 16px; flex-shrink: 0; }

        /* Bottom Status Bar */
        .status-bar { height: 28px; background: #f8fafc; border-top: 1px solid var(--zoho-border); display: flex; align-items: center; justify-content: space-between; padding: 0 16px; font-size: 11px; color: #94a3b8; }

        /* Editable Enhancement */
        .editable:hover { outline: 1px dashed var(--zoho-active-blue); background: #f0f9ff; }
        .editable:focus { outline: 2px solid var(--zoho-active-blue); background: white; }

        /* Logic for Screenshot Mode */
        .screenshot-mode .admin-controls { display: none !important; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; overflow: visible; }
            .view-pane { height: auto !important; }
        }
    </style>
</head>
<body class="flex flex-col">

    <!-- Admin Settings Box (Hidden in SS Mode) -->
    <div class="fixed top-24 right-20 z-[100] admin-controls no-print">
        <div class="bg-white p-5 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-slate-100 w-72 space-y-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Delivery Designer</h3>
            </div>
            
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-500 uppercase">Service Category</label>
                <select id="serviceSelector" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-4 focus:ring-blue-100 outline-none transition-all">
                    <option value="graphics">🖌️ Graphics Design</option>
                    <option value="web">💻 Web Development</option>
                    <option value="seo">📈 SEO & Marketing</option>
                    <option value="domain">🔒 Domain & Security</option>
                </select>
            </div>

            <div class="grid grid-cols-1 gap-2 pt-2">
                <button onclick="document.body.classList.toggle('screenshot-mode')" class="w-full bg-slate-900 text-white py-3 rounded-xl text-xs font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                    Toggle Screenshot Mode
                </button>
                <button onclick="window.print()" class="w-full bg-blue-600 text-white py-3 rounded-xl text-xs font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                    Export Proof
                </button>
            </div>
            <p class="text-[9px] text-slate-400 text-center font-medium leading-tight">All mail content is live-editable. Click any text to modify before screenshot.</p>
        </div>
    </div>

    <!-- Zoho Top Header -->
    <header class="top-header no-print">
        <div class="zoho-logo-area">
            <img src="{{ asset('favicon/zoho.png') }}" alt="Zoho" class="h-6 w-auto">
            <span>Mail</span>
        </div>
        <div class="search-area">
            <svg class="h-4 w-4 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <span>Search Mail (/ + m)</span>
        </div>
        <div class="flex items-center gap-5">
            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white border-2 border-slate-700">S</div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="flex flex-1 overflow-hidden">
        
        <!-- Icon Side Bar (Black) -->
        <aside class="icon-bar no-print">
            <div class="app-icon active">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            </div>
            <div class="app-icon">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div class="app-icon">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
            </div>
            <div class="app-icon">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" /></svg>
            </div>
            <div class="mt-auto pb-4">
                <div class="app-icon"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg></div>
            </div>
        </aside>

        <!-- Nav Sidebar (Folders) -->
        <nav class="nav-sidebar">
            <div class="new-mail-wrap no-print">
                <button class="new-mail-btn">
                    <span>New Mail</span>
                    <svg class="h-4 w-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto pb-4">
                <div class="nav-section-title">Streams</div>
                <div class="nav-item">🏠 Home</div>
                
                <div class="nav-section-title">Folders <svg class="h-3 w-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg></div>
                <div class="nav-item">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    <span>Inbox</span>
                    <span class="ml-auto text-[10px] bg-red-500 text-white px-1.5 rounded-full font-bold">7</span>
                </div>
                <div class="nav-item">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    <span>Drafts</span>
                </div>
                <div class="nav-item active">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                    <span>Sent</span>
                </div>
                <div class="nav-item">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    <span>Trash</span>
                </div>
                
                <div class="nav-section-title">Views</div>
                <div class="nav-item">🔖 Unread <span class="ml-auto text-xs opacity-50">8</span></div>
                <div class="nav-item">🚩 Flagged</div>
            </div>
        </nav>

        <!-- List Pane (Middle) -->
        <div class="list-pane no-print">
            <div class="list-header">
                <svg class="h-4 w-4 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                <span>Sent</span>
            </div>
            <div id="emailListContainer" class="flex-1 overflow-y-auto">
                <!-- Dynamic Emails Loaded Here -->
            </div>
        </div>

        <!-- Content Pane -->
        <main class="content-pane">
            <!-- Toolbar -->
            <div class="content-toolbar no-print">
                <div class="toolbar-btn">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                    Reply
                </div>
                <div class="toolbar-btn">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    Forward
                </div>
                <div class="w-px h-4 bg-slate-100"></div>
                <div class="toolbar-btn">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    Move to
                </div>
                <div class="toolbar-btn">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                    Tag as
                </div>
                <div class="toolbar-btn hover:text-red-500">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Delete
                </div>
                <svg class="h-4 w-4 ml-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
            </div>

            <!-- Main Stream -->
            <div class="main-view">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-start justify-between mb-8">
                        <h1 id="emailSubject" class="text-2xl font-bold text-slate-800 editable" contenteditable="true">
                            Partnership Inquiry - Digital Solutions Provider in Sri Lanka
                        </h1>
                        <div class="flex items-center gap-4 no-print opacity-40">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                    </div>

                    <div class="sender-block mb-10">
                        <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center p-2.5 shadow-lg shadow-blue-100 flex-shrink-0">
                            <img src="{{ asset('favicon/zoho.png') }}" alt="S" class="w-full h-auto brightness-0 invert">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-slate-900 editable" contenteditable="true">Me</span>
                                <span class="text-slate-400 text-[11px] font-medium editable" contenteditable="true">info@digimartsolutions.lk</span>
                            </div>
                            <div class="text-[11px] text-slate-400 flex items-center gap-2">
                                <span class="editable" contenteditable="true">To: {{ $payment->customer_email }}</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-[11px] font-bold text-slate-900 editable" contenteditable="true">
                                {{ $deliveryDate->format('M d, Y, h:i A') }}
                            </div>
                            <div class="text-[9px] font-black text-slate-300 uppercase tracking-widest mt-1">Delivered via DigiMart</div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed space-y-6" id="serviceContent">
                        <!-- Dynamic Content -->
                    </div>

                    <div class="mt-16 pt-10 border-t border-slate-50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-100 flex-shrink-0 border-2 border-white shadow-sm">
                                <img src="https://ui-avatars.com/api/?name=Dilshan+Gunasekara&background=0066ff&color=fff" alt="Avatar">
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-slate-900 font-bold text-sm editable" contenteditable="true">Dilshan Gunasekara</p>
                                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest editable" contenteditable="true">Head of Operations</p>
                                <p class="text-blue-600 font-bold text-[10px] uppercase editable" contenteditable="true">DigiMart Solutions (Pvt) Ltd</p>
                            </div>
                        </div>
                        
                        <div class="mt-8 text-[10px] text-slate-300 font-bold uppercase tracking-[0.3em] flex items-center gap-4">
                            <span>Electronic Receipt - {{ $payment->id }}</span>
                            <div class="h-px flex-1 bg-slate-50"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Bar -->
            <div class="status-bar no-print">
                <div class="flex items-center gap-4">
                    <span>Smart Chat (Ctrl+Space)</span>
                </div>
                <div class="flex items-center gap-4">
                    <span>Storage: 1.2 GB / 5 GB (24%)</span>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span>Online</span>
                    </div>
                </div>
            </div>
        </main>

        <!-- Rightmost Side bar -->
        <aside class="utility-bar no-print">
            <div class="right-icon p-2 hover:bg-slate-50 cursor-pointer text-slate-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div class="right-icon p-2 hover:bg-slate-50 cursor-pointer text-slate-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </div>
            <div class="right-icon p-2 hover:bg-slate-50 cursor-pointer text-slate-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
            <div class="mt-auto pb-4">
                <svg class="h-5 w-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </aside>

    </div>

    <script>
        const contents = {
            graphics: {
                subject: "Project Completed: Brand Assets - #{{ $payment->order_id }} 🎨",
                html: `
                    <p class="text-lg font-semibold text-slate-900">Dear <span class="editable" contenteditable="true">{{ $payment->customer_name }}</span>,</p>
                    <p class="editable" contenteditable="true">We are pleased to inform you that your graphic design project has been successfully completed and the final assets are now ready for delivery.</p>
                    <p class="editable" contenteditable="true">Our team has ensured all branding guidelines were strictly followed to deliver high-quality visual assets for your business. You can access your files via the secure link below:</p>
                    
                    <div class="py-6">
                        <a href="#" class="inline-block bg-blue-600 text-white px-8 py-3.5 rounded-xl font-bold hover:scale-105 transition shadow-xl shadow-blue-100 no-underline editable" contenteditable="true">
                            View Project Deliverables
                        </a>
                    </div>

                    <div class="bg-slate-50 p-8 rounded-2xl border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Included Assets</p>
                        <ul class="space-y-4 text-sm font-medium text-slate-700 m-0 p-0 list-none">
                            <li class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                                <span class="editable" contenteditable="true">Vector Source Files (AI, SVG, EPS)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                                <span class="editable" contenteditable="true">Social Media Ready Formats (PNG, JPG)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="w-1.5 h-1.5 bg-blue-600 rounded-full"></span>
                                <span class="editable" contenteditable="true">Full Identity Branding Guide (PDF)</span>
                            </li>
                        </ul>
                    </div>
                `
            },
            web: {
                subject: "Deployment Success: Your Website is Live! - #{{ $payment->order_id }} 🌐",
                html: `
                    <p class="text-lg font-semibold text-slate-900">Dear <span class="editable" contenteditable="true">{{ $payment->customer_name }}</span>,</p>
                    <p class="editable" contenteditable="true">Great news! Your website is now fully deployed and live on the production server. Our technical team has completed the final migration and security hardening.</p>
                    
                    <div class="py-6">
                        <a href="#" class="inline-block bg-slate-900 text-white px-8 py-3.5 rounded-xl font-bold hover:scale-105 transition shadow-xl shadow-slate-100 no-underline editable" contenteditable="true">
                            Access Admin Portal
                        </a>
                    </div>

                    <div class="bg-indigo-50 p-8 rounded-2xl border border-indigo-100">
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-4">Deployment Log</p>
                        <div class="space-y-3 font-mono text-[11px] text-indigo-900">
                            <p class="m-0 editable" contenteditable="true">> SSL Certificate activated (HTTPS)</p>
                            <p class="m-0 editable" contenteditable="true">> Database migration complete</p>
                            <p class="m-0 editable" contenteditable="true">> Admin credentials provisioned</p>
                        </div>
                    </div>
                `
            },
            seo: {
                subject: "Performance Update: Organic Visibility Growth - #{{ $payment->order_id }} 📊",
                html: `
                    <p class="text-lg font-semibold text-slate-900">Dear <span class="editable" contenteditable="true">{{ $payment->customer_name }}</span>,</p>
                    <p class="editable" contenteditable="true">We are excited to share the latest performance metrics for your digital visibility campaign. We've seen significant growth in keyword rankings over the last 30 days.</p>
                    
                    <div class="py-6">
                        <a href="#" class="inline-block bg-emerald-600 text-white px-8 py-3.5 rounded-xl font-bold hover:scale-105 transition shadow-xl shadow-emerald-100 no-underline editable" contenteditable="true">
                            View SEO Reports
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-6 bg-emerald-50 rounded-2xl border border-emerald-100 text-center">
                            <h4 class="text-3xl font-black text-emerald-600 m-0 editable" contenteditable="true">+42%</h4>
                            <p class="text-[10px] uppercase font-bold text-slate-400 m-0">Traffic Growth</p>
                        </div>
                        <div class="p-6 bg-emerald-50 rounded-2xl border border-emerald-100 text-center">
                            <h4 class="text-3xl font-black text-emerald-600 m-0 editable" contenteditable="true">#1</h4>
                            <p class="text-[10px] uppercase font-bold text-slate-400 m-0">Avg Ranking</p>
                        </div>
                    </div>
                `
            },
            domain: {
                subject: "Domain Activation & Authentication Set - #{{ $payment->order_id }} 📧",
                html: `
                    <p class="text-lg font-semibold text-slate-900">Dear <span class="editable" contenteditable="true">{{ $payment->customer_name }}</span>,</p>
                    <p class="editable" contenteditable="true">Your business email infrastructure is now fully active. We have implemented advanced SPF, DKIM, and DMARC records to ensure 100% deliverability.</p>
                    
                    <div class="py-6">
                        <a href="#" class="inline-block bg-indigo-600 text-white px-8 py-3.5 rounded-xl font-bold hover:scale-105 transition shadow-xl shadow-indigo-100 no-underline editable" contenteditable="true">
                            Download Login Guide
                        </a>
                    </div>

                    <p class="text-sm text-slate-500 editable" contenteditable="true">Please ensure you change your temporary password upon first login to maintain account security.</p>
                `
            }
        };

        const selector = document.getElementById('serviceSelector');
        const contentDiv = document.getElementById('serviceContent');
        const subjectTitle = document.getElementById('emailSubject');
        const listContainer = document.getElementById('emailListContainer');

        const activeMailFullLabel = "{{ $deliveryDate->format('M d, h:i A') }}";
        const deliveryISO = "{{ $deliveryDate->toIso8601String() }}";

        const fakeSentMails = [
            { sender: "Me", subject: "Partnership Proposal: Digital Solutions", preview: "Hi team, find the proposal attached for..." },
            { sender: "Me", subject: "Invoice #DM-99218 - Paid", preview: "Thank you for the payment. Your receipt is..." },
            { sender: "Me", subject: "Meeting Minutes - Q4 Planning", preview: "Summary of today's meeting regarding the..." },
            { sender: "Me", subject: "Affiliate Onboarding Documents", preview: "Welcome to our partner program! Please sign..." },
            { sender: "Me", subject: "Server Migration - Status Update", preview: "The migration to the new AWS cluster is..." },
            { sender: "Me", subject: "License Key: Adobe CC Pack", preview: "Your license key for the Adobe Creative..." },
            { sender: "Me", subject: "Logo Design Concepts - Round 1", preview: "Please review the initial concepts for your..." },
            { sender: "Me", subject: "Website Audit Report - Q3", preview: "Final SEO audit report for the third quarter..." },
            { sender: "Me", subject: "Domain Verification Successful", preview: "Your domain digimartsolutions.lk has been..." },
            { sender: "Me", subject: "New Project Inquiry - E-commerce", preview: "I received a new inquiry from a client in..." },
            { sender: "Me", subject: "Monthly Security Patch Report", preview: "All production servers have been patched..." },
            { sender: "Me", subject: "Feedback Request: Brand Identity", preview: "How do you like the new brand identity we..." },
            { sender: "Me", subject: "Final Deliverables: UI Kit", preview: "The UI kit for the project is ready for..." },
            { sender: "Me", subject: "Q1 Marketing Strategy Call", preview: "Lets jump on a quick call to discuss the..." },
            { sender: "Me", subject: "Creative Cloud License Renewed", preview: "Success! Your subscription has been renewed..." },
            { sender: "Me", subject: "API Integration Doc - V2.1", preview: "The updated API documentation is now live..." }
        ];

        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }

        function updateContent() {
            const val = selector.value;
            contentDiv.innerHTML = contents[val].html;
            subjectTitle.innerText = contents[val].subject;
            renderMailList(contents[val].subject);
        }

        function renderMailList(activeSubject) {
            listContainer.innerHTML = '';
            
            const now = new Date();
            const pool = shuffleArray([...fakeSentMails]);
            const listSize = 15;
            const activePos = Math.floor(Math.random() * listSize); // Random position 0-14

            // Generate 15 logical timestamps sorted descending
            // We'll base them around the deliveryISO, but ensure none > now
            let timestamps = [];
            const anchor = new Date(deliveryISO);

            // Create a spread of timestamps
            for (let i = 0; i < listSize; i++) {
                const diff = i - activePos;
                let t = new Date(anchor);
                
                if (diff < 0) {
                    // Mails above active (newer)
                    // Gap of 5-15 mins
                    t.setMinutes(t.getMinutes() + (Math.abs(diff) * (5 + Math.floor(Math.random() * 10))));
                } else if (diff > 0) {
                    // Mails below active (older)
                    // Gap of 1-6 hours
                    t.setHours(t.getHours() - (diff * (1 + Math.floor(Math.random() * 5))));
                }
                
                // CRITICAL: Cap at current real time to avoid "future" dates
                if (t > now) {
                    t = new Date(now.getTime() - (i * 60000)); // Stagger slightly in the past
                }
                timestamps.push(t);
            }

            // Render
            for (let i = 0; i < listSize; i++) {
                const mailDate = timestamps[i];
                const isToday = mailDate.toDateString() === now.toDateString();
                
                let label = "";
                if (isToday) {
                    label = mailDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                } else {
                    label = mailDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                }

                if (i === activePos) {
                    // Render Active Item
                    listContainer.innerHTML += `
                        <div class="email-card active">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-slate-900">Me</span>
                                <span class="text-[9px] text-blue-600 font-bold uppercase tracking-tighter">${activeMailFullLabel}</span>
                            </div>
                            <div class="text-xs font-semibold text-slate-800 truncate mb-1">${activeSubject}</div>
                            <div class="text-[11px] text-slate-400 line-clamp-1">Hi {{ explode(' ', $payment->customer_name)[0] }}, please find your deliverables linked...</div>
                        </div>
                    `;
                } else {
                    const mail = pool.pop() || { sender: "Me", subject: "Follow up", preview: "Just checking in..." };
                    listContainer.innerHTML += `
                        <div class="email-card">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-slate-500">${mail.sender}</span>
                                <span class="text-[10px] text-slate-400">${label}</span>
                            </div>
                            <div class="text-xs text-slate-500 truncate mb-1">${mail.subject}</div>
                            <div class="text-[11px] text-slate-300 line-clamp-1">${mail.preview}</div>
                        </div>
                    `;
                }
            }
        }

        selector.addEventListener('change', updateContent);
        updateContent(); // Initialize
    </script>
</body>
</html>
