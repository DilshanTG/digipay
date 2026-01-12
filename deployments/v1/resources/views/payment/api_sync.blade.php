<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting for Confirmation - DigiMart Pay</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center border border-slate-200">
        <div class="mb-6">
            <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-850">Verifying Payment...</h1>
            <p class="text-slate-600 mt-2">We've checked the PayHere status, but it's still being processed. Please wait a moment.</p>
        </div>

        @php
            $isLocalHost = in_array(request()->getHost(), ['localhost', '127.0.0.1']);
        @endphp

        @if(app()->isLocal() || $isLocalHost)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8 text-left">
            <h3 class="font-bold text-amber-800 text-sm mb-1">🛠️ Developer Note (Localhost)</h3>
            <p class="text-amber-700 text-xs leading-relaxed">
                On <strong>localhost</strong>, PayHere cannot send the success signal to your computer. In production, this page will redirect automatically in seconds.
            </p>
            <div class="mt-4">
                <a href="{{ route('pay.sync', $payment->order_id) }}" class="inline-block bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    Confirm Success Manually
                </a>
            </div>
        </div>
        @else
        <div class="mb-8">
            <p class="text-sm text-slate-500">Redirecting you back to the store shortly...</p>
        </div>
        <script>
            // Refresh every 5 seconds in production to check for status update
            setTimeout(() => { window.location.reload(); }, 5000);
        </script>
        @endif

        <div class="pt-6 border-t border-slate-100">
            <p class="text-xs text-slate-400 font-mono">Order ID: {{ $payment->order_id }}</p>
        </div>
    </div>
</body>
</html>
