<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $payment->order_id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Outfit', sans-serif;
            -webkit-print-color-adjust: exact;
        }
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .print-shadow { box-shadow: none !important; border: 1px solid #e2e8f0; }
        }
        .editable:hover {
            background-color: #f8fafc;
            outline: 2px dashed #cbd5e1;
            outline-offset: 4px;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen p-4 md:p-12">

    <!-- Actions -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print">
        <a href="{{ route('admin.proofs.index') }}" class="text-slate-500 hover:text-slate-800 flex items-center gap-2 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Back to Proofs
        </a>
        
        <form action="{{ route('admin.proofs.invoice.download', $payment) }}" method="POST" id="downloadForm">
            @csrf
            <input type="hidden" name="description" id="hiddenDescription" value="{{ $payment->real_description ?? 'Creative Design Services' }}">
            <input type="hidden" name="sub_description" id="hiddenSubDescription" value="Service Component: Digital Transformation">
            <button type="submit" class="bg-slate-900 text-white px-6 py-2 rounded-xl font-bold hover:bg-slate-800 transition flex items-center gap-2 shadow-lg shadow-slate-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download Real PDF
            </button>
        </form>
    </div>

    <!-- Invoice Card -->
    <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-2xl shadow-slate-200 overflow-hidden print-shadow">
        <!-- Top Header -->
        <div class="bg-slate-900 px-8 py-12 md:px-16 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="text-white space-y-4 text-center md:text-left">
                <img src="{{ asset('logo.png') }}" alt="DigiMart" class="h-10 w-auto brightness-0 invert mx-auto md:mx-0">
                <div class="space-y-1">
                    <div class="text-xl font-black tracking-tighter uppercase italic">DIGIMART SOLUTIONS</div>
                    <div class="text-slate-400 text-[10px] font-bold tracking-widest uppercase">(Pvt) Ltd</div>
                </div>
            </div>
            <div class="text-center md:text-right">
                <h1 class="text-4xl font-extrabold text-white uppercase tracking-tight">Invoice</h1>
                <p class="text-blue-400 font-bold">#{{ $payment->order_id }}</p>
            </div>
        </div>
        
        <div class="p-8 md:p-16">
            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-16">
                <div>
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3">Company Registration</p>
                    <p class="text-slate-900 font-bold">PV00336398</p>
                    <div class="text-slate-500 text-sm mt-2 leading-relaxed">
                        288, Boathura,<br>
                        Gampola, Sri Lanka.
                    </div>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3">Billed To</p>
                    <p class="text-slate-900 font-bold text-lg">{{ $payment->customer_name }}</p>
                    <p class="text-slate-600 font-medium">{{ $payment->customer_email }}</p>
                    <p class="text-slate-900 font-bold mt-1">{{ $payment->customer_phone ?? '' }}</p>
                </div>
                <div class="md:text-right">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3">Date of Issue</p>
                    <p class="text-slate-900 font-bold mb-4">{{ $payment->created_at->format('F d, Y') }}</p>
                    
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Transaction Ref</p>
                    <p class="text-slate-500 font-bold text-xs">{{ $payment->payhere_ref ?? 'INTERNAL-SYNC' }}</p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-12">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100 italic">
                            <th class="text-left py-4 text-slate-400 font-bold uppercase text-[10px] tracking-widest">Description</th>
                            <th class="text-center py-4 text-slate-400 font-bold uppercase text-[10px] tracking-widest">Qty</th>
                            <th class="text-right py-4 text-slate-400 font-bold uppercase text-[10px] tracking-widest">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-10">
                                <div id="editableDescription" class="text-slate-900 font-bold text-xl uppercase tracking-tight editable outline-none border-b border-transparent focus:border-blue-500" contenteditable="true">
                                    {{ $payment->real_description ?? 'Creative Design Services' }}
                                </div>
                                <div id="editableSubDescription" class="text-slate-400 text-xs mt-2 uppercase font-bold editable outline-none focus:text-blue-500" contenteditable="true">
                                    Service Component: Digital Transformation
                                </div>
                            </td>
                            <td class="py-10 text-center text-slate-900 font-bold">01</td>
                            <td class="py-10 text-right text-slate-900 font-black text-xl">
                                {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer Section -->
            <div class="flex flex-col md:flex-row justify-between items-end gap-12 pt-12 border-t border-slate-50">
                <div class="max-w-md space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-1.5 bg-blue-600 rounded-full"></div>
                        <p class="text-slate-500 text-xs font-medium">Payment secured and verified via PayHere</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Support & Verification</p>
                        <div class="flex gap-4">
                            <p class="text-slate-900 font-bold text-xs">info@digimartsolutions.lk</p>
                            <p class="text-slate-900 font-bold text-xs">+94 77 250 3124</p>
                        </div>
                    </div>
                </div>
                
                <div class="w-full md:w-80 space-y-4">
                    <div class="flex justify-between items-center bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <span class="font-bold text-slate-400 uppercase text-[10px] tracking-widest">Grand Total</span>
                        <span class="text-2xl font-black text-slate-900">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="text-center mt-20 text-slate-300 text-[10px] font-bold uppercase tracking-[0.2em] italic">
                Authorized Digital Signature — Digimart Solutions (Pvt) Ltd
            </div>
        </div>
    </div>

    <script>
        const editable = document.getElementById('editableDescription');
        const hiddenInput = document.getElementById('hiddenDescription');
        
        editable.addEventListener('input', function() {
            hiddenInput.value = this.innerText;
        });

        const editableSub = document.getElementById('editableSubDescription');
        const hiddenSubInput = document.getElementById('hiddenSubDescription');
        
        editableSub.addEventListener('input', function() {
            hiddenSubInput.value = this.innerText;
        });
    </script>

    <p class="text-center text-slate-400 text-xs mt-8 no-print italic">
        Tip: You can click on the product description to edit it before downloading.
    </p>

</body>
</html>
