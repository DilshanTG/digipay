# DigiMart Pay - PayHere Integration

Modern, mobile-friendly payment gateway integration for accepting online payments.

## 🚀 Features

- Modern Stripe-like UI design
- 100% Mobile responsive
- Manual amount entry
- WhatsApp number collection
- Custom payment notes
- Complete payment flow (Success, Cancel, Failed)
- PayHere Sandbox & Live support
- Secure hash verification
- Payment status verification
- Payment logs & records

## 📋 Pages Included

1. **index.php** - Main payment form
2. **process_payment.php** - Payment processing & PayHere redirect
3. **notify.php** - Payment notification callback handler
4. **return.php** - Payment success page
5. **cancel.php** - Payment cancelled page
6. **verify_payment.php** - Payment status verification
7. **config.php** - Configuration & helper functions

## ⚙️ Configuration

Edit `config.php` and update the following:

```php
define('MERCHANT_ID', 'YOUR_MERCHANT_ID');
define('MERCHANT_SECRET', 'YOUR_MERCHANT_SECRET');
```

### Important URLs to Configure:

- **Return URL**: `http://pay.digimartsolutions.lk/return.php`
- **Cancel URL**: `http://pay.digimartsolutions.lk/cancel.php`
- **Notify URL**: `http://pay.digimartsolutions.lk/notify.php`

## 🔧 Setup Instructions

### 1. Get PayHere Credentials

1. Login to your PayHere account
2. Go to **Side Menu > Integrations**
3. Copy your **Merchant ID**
4. Click **'Add Domain/App'**
5. Enter `pay.digimartsolutions.lk`
6. Wait for approval (up to 24 hours)
7. Copy the **Merchant Secret**

### 2. Update Configuration

Edit `config.php`:

- Replace `MERCHANT_ID` with your actual Merchant ID
- Replace `MERCHANT_SECRET` with your actual Merchant Secret
- Update `BUSINESS_NAME`, `BUSINESS_EMAIL`, `BUSINESS_PHONE` with your details

### 3. Set Sandbox Mode

For testing, keep:
```php
define('USE_SANDBOX', true);
```

For live payments, change to:
```php
define('USE_SANDBOX', false);
```

### 4. File Permissions

Make sure the following files are writable:
```bash
chmod 666 payments.json
chmod 666 payment_logs.txt
```

## 🧪 Testing

1. Visit `http://pay.digimartsolutions.lk/`
2. Fill in the payment form with test data
3. Use PayHere sandbox test card details
4. Verify payment status on return page

### PayHere Sandbox Test Cards

**Successful Payment:**
- Card: 5xxx xxxx xxxx 4564
- Expiry: 12/25
- CVV: 123

**Failed Payment:**
- Card: 4xxx xxxx xxxx 0341
- Expiry: 12/25
- CVV: 123

## 📱 Mobile Optimized

All pages are fully responsive and optimized for:
- iOS Safari
- Android Chrome
- Desktop browsers
- Tablets

## 🔒 Security Features

- ✅ Server-side hash generation
- ✅ Payment verification with md5sig
- ✅ Merchant secret never exposed to client
- ✅ Secure PayHere SSL encryption
- ✅ Payment notification logging
- ✅ Input validation & sanitization

## 📊 Payment Records

Payments are stored in `payments.json` with the following data:
- Order ID
- Payment ID
- Amount & Currency
- Status Code
- Payment Method
- Customer WhatsApp
- Notes
- Timestamp

## 🆘 Support

For issues or questions:
- WhatsApp: +94772503124
- Email: info@digimartsolutions.lk

## 📝 Payment Status Codes

- `2` - Success
- `0` - Pending
- `-1` - Cancelled
- `-2` - Failed
- `-3` - Chargedback

## 🎨 Customization

All pages use inline CSS for easy customization. The color scheme uses:
- Primary: Purple gradient (#667eea to #764ba2)
- Success: Green (#10b981)
- Warning: Orange (#f59e0b)
- Error: Red (#ef4444)

## ⚠️ Important Notes

1. **Notify URL** must be publicly accessible (not localhost)
2. Hash must be generated server-side for security
3. Always verify the payment notification signature
4. Keep `MERCHANT_SECRET` confidential
5. Test thoroughly in sandbox before going live

## 📄 License

© 2024 DigiMart Solutions. All rights reserved.
