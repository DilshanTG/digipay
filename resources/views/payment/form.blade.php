<?php
// PHP logic to handle prefilling from URL if needed, similar to legacy index.php
$amount_from_url = request('amount');
if (!$amount_from_url) {
    // Basic check for URL path params if Laravel router passed them, but here we likely rely on query params
    // or we can parse path if needed. For now standard Laravel request('amount') covers ?amount=...
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment - DigiMart Solutions</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Make secure online payments with DigiMart Solutions. Powered by PayHere - Sri Lanka's most trusted payment gateway with 256-bit SSL encryption.">
    <meta name="keywords" content="online payment, secure payment, PayHere, DigiMart Solutions, payment gateway, Sri Lanka">
    <meta name="author" content="DigiMart Solutions">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph Meta Tags (Facebook, WhatsApp, LinkedIn) -->
    <meta property="og:title" content="Secure Payment - DigiMart Solutions">
    <meta property="og:description" content="Make secure online payments with DigiMart Solutions. Powered by PayHere - Sri Lanka's most trusted payment gateway.">
    <meta property="og:image" content="{{ asset('thumb.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="DigiMart Solutions">
    <meta property="og:locale" content="en_US">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Secure Payment - DigiMart Solutions">
    <meta name="twitter:description" content="Make secure online payments with DigiMart Solutions. Powered by PayHere.">
    <meta name="twitter:image" content="{{ asset('thumb.jpg') }}">
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('favicon/android-chrome-512x512.png') }}">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
    
    <!-- Theme Color -->
    <meta name="theme-color" content="#0a2540">
    <meta name="msapplication-TileColor" content="#0a2540">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Font Variables */
            --font-primary: 'Google Sans Flex', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            
            /* Color Variables */
            --color-primary: #0a2540;
            --color-accent: #635bff;
            --color-text-primary: #1a1f36;
            --color-text-secondary: #6b7c93;
            --color-border: #d1d9e0;
            --color-border-light: #e3e8ee;
            --color-bg-page: #f6f9fc;
            --color-bg-card: #ffffff;
            --color-bg-subtle: #fafbfc;
            --color-error: #df1b41;
        }

        body {
            font-family: var(--font-primary);
            background: var(--color-bg-page);
            min-height: 100vh;
            padding: 24px 16px;
            color: var(--color-text-primary);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 470px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            gap: 16px;
        }

        .header-logo {
            flex-shrink: 0;
        }

        .header-logo img {
            height: 50px;
            width: auto;
            display: block;
        }

        .header-text {
            text-align: right;
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            color: #0a2540;
            margin-bottom: 4px;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 15px;
            color: #6b7c93;
            font-weight: 400;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e3e8ee;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .card-header {
            padding: 24px 24px 20px;
            border-bottom: 1px solid #e3e8ee;
            background: #fafbfc;
        }

        .card-title {
            font-size: 17px;
            font-weight: 600;
            color: #0a2540;
            margin-bottom: 4px;
        }

        .card-description {
            font-size: 14px;
            color: #6b7c93;
        }

        .form-container {
            padding: 28px 24px 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group:last-of-type {
            margin-bottom: 28px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0a2540;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }

        .required {
            color: #df1b41;
        }

        input, textarea {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid var(--color-border);
            border-radius: 6px;
            font-size: 15px;
            font-family: var(--font-primary);
            transition: all 0.15s ease;
            background: var(--color-bg-card);
            color: var(--color-primary);
        }

        input:hover {
            border-color: #a3acb9;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #635bff;
            box-shadow: 0 0 0 3px rgba(99, 91, 255, 0.08);
        }

        input::placeholder, textarea::placeholder {
            color: #a3acb9;
        }

        .input-with-currency {
            position: relative;
        }

        .currency-symbol {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 15px;
            font-weight: 600;
            color: #6b7c93;
            pointer-events: none;
        }

        .input-with-currency input {
            padding-left: 40px;
            font-size: 28px;
            font-weight: 600;
            color: #0a2540;
            letter-spacing: -0.02em;
        }

        .price-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .price-tag {
            display: inline-block;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 500;
            color: #6b7c93;
            background: #ffffff;
            border: 1px dotted #d1d9e0;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
            font-family: var(--font-primary);
        }

        .price-tag:hover {
            background: #fafbfc;
            border-color: #635bff;
            color: #635bff;
            transform: translateY(-1px);
        }

        .price-tag:active {
            transform: translateY(0);
        }

        textarea {
            resize: vertical;
            min-height: 76px;
            font-size: 14px;
            line-height: 1.5;
        }

        .hint {
            font-size: 12px;
            color: #6b7c93;
            margin-top: 6px;
        }

        .btn {
            width: 100%;
            padding: 13px 20px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            font-family: var(--font-primary);
            letter-spacing: -0.01em;
        }

        .btn-primary {
            background: #635bff;
            color: white;
            box-shadow: 0 1px 3px rgba(99, 91, 255, 0.3);
        }

        .btn-primary:hover {
            background: #5349e8;
            box-shadow: 0 2px 5px rgba(99, 91, 255, 0.4);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(99, 91, 255, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            padding: 12px;
            background: #fafbfc;
            border-radius: 6px;
            font-size: 12px;
            color: #6b7c93;
            border: 1px solid #e3e8ee;
        }

        .lock-icon {
            width: 14px;
            height: 14px;
            color: #6b7c93;
        }

        .footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #a3acb9;
        }

        .footer-link {
            color: #635bff;
            text-decoration: none;
            margin: 0 8px;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        .error-message {
            background: #fff5f7;
            border: 1px solid #f2b8c6;
            border-left: 3px solid #df1b41;
            color: #9f1239;
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            display: none;
        }
        
        /* Server-side validation errors */
        .server-error {
            background: #fff5f7;
            border: 1px solid #f2b8c6;
            border-left: 3px solid #df1b41;
            color: #9f1239;
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .info-box {
            background: #f0f5ff;
            border: 1px solid #d4e3ff;
            border-left: 3px solid #635bff;
            color: #3c4257;
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: start;
            gap: 8px;
            line-height: 1.5;
        }

        .spinner {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 2px solid white;
            width: 16px;
            height: 16px;
            animation: spin 0.8s linear infinite;
            display: none;
            margin-left: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn-primary.loading {
            pointer-events: none;
        }

        .btn-primary.loading .spinner {
            display: inline-block;
        }

        @media (max-width: 480px) {
            body {
                padding: 16px 12px;
            }

            .container {
                max-width: 100%;
            }

            .form-container {
                padding: 24px 20px 28px;
            }

            .card-header {
                padding: 20px 20px 16px;
            }

            .header {
                margin-bottom: 24px;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .header-text {
                text-align: center;
            }

            .header-logo img {
                height: 40px;
            }
        }

        @media (prefers-color-scheme: dark) {
            /* Optional: Add dark mode support later */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-logo">
                <img src="{{ asset('logo.png') }}" alt="DigiMart Solutions">
            </div>
            <div class="header-text">
                <div class="logo">DigiMart Solutions</div>
                <div class="subtitle">Secure Payment</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">Payment Details</div>
                <div class="card-description">Please provide your information below</div>
            </div>

            <form id="paymentForm" class="form-container" method="POST" action="{{ route('pay.process') }}">
                @csrf
                <div class="info-box">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0; margin-top: 1px;">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span>Your payment is secured with PayHere. We never store your card details.</span>
                </div>

                <div class="error-message" id="errorMessage"></div>
                
                @if ($errors->any())
                    <div class="server-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="full_name">Full Name <span class="required">*</span></label>
                    <input type="text" id="full_name" name="full_name" placeholder="John Doe" required autocomplete="name" value="{{ old('full_name') }}">
                </div>

                <div class="form-group">
                    <label for="whatsapp">WhatsApp Number <span class="required">*</span></label>
                    <div style="display: flex; gap: 8px;">
                        <select id="country_code" name="country_code" style="width: 140px; padding: 11px 13px; border: 1px solid #d1d9e0; border-radius: 6px; font-size: 15px; font-family: 'Google Sans Flex', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: white; color: #0a2540; cursor: pointer;">
                            <option value="+94" selected>🇱🇰 +94</option>
                            <!-- Add other options if needed -->
                        </select>
                        <input 
                            type="text" 
                            inputmode="numeric"
                            id="whatsapp" 
                            name="whatsapp" 
                            placeholder="771234567" 
                            required 
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="off"
                            spellcheck="false"
                            minlength="9"
                            maxlength="10"
                            pattern="[0-9]{9,10}"
                            style="flex: 1;"
                            value="{{ old('whatsapp') }}">
                    </div>
                    <div class="hint">Enter 9-10 digits only (e.g., 771234567)</div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="john@example.com" required autocomplete="email" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="amount">Amount (LKR) <span class="required">*</span></label>
                    <div class="input-with-currency">
                        <span class="currency-symbol">Rs</span>
                        <input type="number" id="amount" name="amount" placeholder="1,000.00" step="0.01" min="1" value="{{ old('amount', $amount_from_url ? number_format($amount_from_url, 2, '.', '') : '') }}" required>
                    </div>
                    <div class="price-suggestions">
                        <span class="price-tag" data-amount="500">Rs 500</span>
                        <span class="price-tag" data-amount="1000">Rs 1,000</span>
                        <span class="price-tag" data-amount="2500">Rs 2,500</span>
                        <span class="price-tag" data-amount="5000">Rs 5,000</span>
                        <span class="price-tag" data-amount="10000">Rs 10,000</span>
                        <span class="price-tag" data-amount="25000">Rs 25,000</span>
                        <span class="price-tag" data-amount="50000">Rs 50,000</span>
                    </div>
                    <div style="font-size: 11px; color: #df1b41; margin-top: 8px; font-weight: 500;">
                        * Transaction fee of 3.9% will be added to the final charge
                    </div>
                </div>

                <div class="form-group">
                    <label for="note">Note or Reference (Optional)</label>
                    <textarea id="note" name="note" placeholder="Enter payment reference or additional notes...">{{ old('note') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span>Pay Now</span>
                    <div class="spinner"></div>
                </button>

                <div style="text-align: center; margin-top: 20px;">
                    <a href="#">
                        <img src="https://www.payhere.lk/downloads/images/payhere_long_banner_dark.png" alt="PayHere" style="max-width: 100%; height: auto; opacity: 0.8;">
                    </a>
                </div>

                <div class="secure-badge">
                    <svg class="lock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Secured by PayHere • 256-bit SSL encryption
                </div>
            </form>
        </div>

        <div class="footer">
            Powered by PayHere
            <a href="#" class="footer-link" target="_blank">Need help?</a>
        </div>
    </div>

    <script>
        const form = document.getElementById('paymentForm');
        const submitBtn = document.getElementById('submitBtn');
        const errorMessage = document.getElementById('errorMessage');

        form.addEventListener('submit', function(e) {
            // Client-side validation logic from legacy file
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';

            const fullName = document.getElementById('full_name').value.trim();
            const countryCode = document.getElementById('country_code').value;
            const whatsappNumber = document.getElementById('whatsapp').value.trim();
            const email = document.getElementById('email').value.trim();
            const amount = parseFloat(document.getElementById('amount').value);

            // We don't modify the input value anymore to avoid "visual jumps" or double-prefixing.
            // The backend will combine countryCode + whatsappNumber and clean it.

            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
            errorMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        const amountInput = document.getElementById('amount');
        amountInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });

        // Quick price selection tags
        const priceTags = document.querySelectorAll('.price-tag');
        priceTags.forEach(tag => {
            tag.addEventListener('click', function() {
                const amount = this.getAttribute('data-amount');
                amountInput.value = parseFloat(amount).toFixed(2);
                amountInput.focus();
                
                // Add visual feedback
                this.style.background = 'var(--color-bg-subtle)';
                this.style.borderColor = 'var(--color-accent)';
                setTimeout(() => {
                    this.style.background = '';
                    this.style.borderColor = '';
                }, 200);
            });
        });

        // WhatsApp number validation - only allow numbers, 9-10 digits
        const whatsappInput = document.getElementById('whatsapp');
        
        // Strip non-numeric characters as user types
        whatsappInput.addEventListener('input', function(e) {
            // Remove all non-numeric characters
            let value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 10 digits
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            
            // Update the field value
            this.value = value;
            saveDetails(); // Auto-save
        });

        // Prevent paste of non-numeric content
        whatsappInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numericOnly = pastedText.replace(/[^0-9]/g, '').substring(0, 10);
            this.value = numericOnly;
            saveDetails(); // Auto-save
        });

        // ==========================================
        // 🧀 CHEESY EASY: Remember Me Feature
        // ==========================================
        
        // Load saved details on page load
        window.addEventListener('DOMContentLoaded', () => {
            const saved = localStorage.getItem('digimart_user_details');
            if (saved) {
                const details = JSON.parse(saved);
                if (details.name) document.getElementById('full_name').value = details.name;
                if (details.email) document.getElementById('email').value = details.email;
                if (details.phone) document.getElementById('whatsapp').value = details.phone;
                if (details.country) document.getElementById('country_code').value = details.country;
            }
        });

        // Save details when inputs change
        const inputsToSave = ['full_name', 'email', 'country_code'];
        inputsToSave.forEach(id => {
            document.getElementById(id).addEventListener('input', saveDetails);
            document.getElementById(id).addEventListener('change', saveDetails);
        });

        function saveDetails() {
            const details = {
                name: document.getElementById('full_name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('whatsapp').value,
                country: document.getElementById('country_code').value
            };
            localStorage.setItem('digimart_user_details', JSON.stringify(details));
        }
    </script>
</body>
</html>
