# Sandbox Mode Guide 🧪

Sandbox mode allows you to test payment integrations without connecting to PayHere. Perfect for development and testing!

## What is Sandbox Mode?

When enabled for a merchant, instead of redirecting to PayHere's payment gateway, users see a **fake payment simulator** with buttons to test different payment outcomes:

- ✅ **Payment Success** (status_code: 2)
- ↺ **Payment Cancelled** (status_code: -1)
- ✗ **Payment Failed** (status_code: -2)
- ⏱ **Keep Pending** (status_code: 0)

All webhooks and return URLs work exactly like real PayHere payments!

## How to Enable Sandbox Mode

### Option 1: Direct Database Update

```bash
cd flight
sqlite3 database/database.sqlite
```

```sql
-- Enable sandbox mode for merchant ID 1
UPDATE merchants SET sandbox_mode = 1 WHERE id = 1;

-- Check current status
SELECT id, name, sandbox_mode FROM merchants;
```

### Option 2: Migration Script (for existing databases)

If you already have a database without the `sandbox_mode` column:

```bash
cd flight
php migrate_sandbox.php
```

Then update via SQL as shown in Option 1.

### Option 3: Via PHP Code

Add this to your admin panel or create a simple script:

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\Models\Merchant;

Database::connect();

$merchant = Merchant::find(1); // Your merchant ID
$merchant->sandbox_mode = true;
$merchant->update();

echo "Sandbox mode enabled!\n";
```

## Testing Your Integration

### 1. Enable Sandbox Mode
```bash
sqlite3 flight/database/database.sqlite "UPDATE merchants SET sandbox_mode = 1 WHERE id = 1"
```

### 2. Create a Test Payment

**Via API:**
```bash
curl -X POST http://localhost:8000/api/v1/init \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 1000,
    "return_url": "http://localhost:3000/payment/return",
    "notify_url": "http://localhost:3000/payment/webhook",
    "customer_email": "test@example.com",
    "first_name": "Test User",
    "description": "Test Payment"
  }'
```

**Via Direct Form:**
Visit `http://localhost:8000` and fill the payment form.

### 3. You'll See the Sandbox Simulator

Instead of PayHere, you'll see a beautiful payment simulator page with 4 buttons.

### 4. Test Different Scenarios

**Test Success Flow:**
1. Click "Payment Success" button
2. Your webhook will receive: `status_code=2`
3. Payment marked as SUCCESS in database
4. SMS notifications sent (if configured)
5. Redirect to your return URL with success parameters

**Test Cancelled Flow:**
1. Click "Payment Cancelled" button
2. Payment marked as CANCELLED
3. Redirect to return URL with `status_code=-1`

**Test Failed Flow:**
1. Click "Payment Failed" button
2. Payment marked as FAILED
3. Redirect to return URL with `status_code=-2`

**Test Pending State:**
1. Click "Keep Pending" button
2. Payment stays PENDING (useful for testing auto-verification)
3. Redirect to return URL with status still PENDING

## Webhook Testing

Sandbox mode triggers **real webhooks** to your notify URL!

Example webhook payload (Success):
```json
{
  "merchant_id": "YOUR_API_KEY",
  "order_id": "YOUR_ORDER_ID",
  "payment_id": "ORD-ABC123",
  "payhere_amount": "1000.00",
  "payhere_currency": "LKR",
  "status_code": 2,
  "md5sig": "ABC123...",
  "custom_1": "",
  "custom_2": ""
}
```

The webhook signature (`md5sig`) is calculated exactly like PayHere!

## Return URL Parameters

Your return URL receives the same parameters as PayHere:

```
https://yoursite.com/return?status=SUCCESS&merchant_id=...&order_id=...&payment_id=...&payhere_amount=1000.00&status_code=2&md5sig=...
```

## Integration Testing Checklist

Use sandbox mode to test:

- ✅ Success payment flow
- ✅ Cancelled payment handling
- ✅ Failed payment handling
- ✅ Webhook signature verification
- ✅ Return URL parameter parsing
- ✅ Database status updates
- ✅ SMS notifications (if enabled)
- ✅ Email notifications (if enabled)
- ✅ Receipt generation
- ✅ UI state changes based on payment status

## Switching to Production

When ready for production:

```sql
-- Disable sandbox mode
UPDATE merchants SET sandbox_mode = 0 WHERE id = 1;
```

Now payments will redirect to real PayHere gateway!

## Pro Tips

### Test Multiple Merchants

```sql
-- Enable sandbox for specific merchant
UPDATE merchants SET sandbox_mode = 1 WHERE api_key = 'sk_test_...';

-- Check all merchant sandbox statuses
SELECT name, api_key, sandbox_mode FROM merchants;
```

### Monitor Sandbox Tests

Check logs for sandbox activity:
```bash
tail -f flight/storage/logs/app.log | grep Sandbox
```

### Quick Toggle Script

Create `toggle_sandbox.php`:
```php
<?php
require __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\Models\Merchant;

Database::connect();

$merchantId = $argv[1] ?? 1;
$merchant = Merchant::find($merchantId);

$merchant->sandbox_mode = !$merchant->sandbox_mode;
$merchant->update();

echo "Sandbox mode: " . ($merchant->sandbox_mode ? "ENABLED" : "DISABLED") . "\n";
```

Usage:
```bash
php toggle_sandbox.php 1  # Toggle for merchant ID 1
```

## Admin Panel View

Visit `/admin/merchants` to see sandbox mode status for all merchants.

Merchants in sandbox mode show: 🧪 **ENABLED**

## FAQs

**Q: Does sandbox mode affect real payments?**
A: No! It only affects the specific merchant where it's enabled.

**Q: Can I use sandbox mode in production?**
A: Yes, but only for testing accounts. Real customer payments should have sandbox_mode=0.

**Q: Are webhooks sent in sandbox mode?**
A: Yes! Webhooks work exactly like production.

**Q: What about SMS notifications?**
A: SMS is sent in sandbox mode if SMS_ENABLED=true in .env

**Q: Can I test pending → success transitions?**
A: Yes! Click "Keep Pending", then use manual sync or retrieval API testing.

## Troubleshooting

**Sandbox page not showing?**

1. Check merchant sandbox_mode: `SELECT sandbox_mode FROM merchants WHERE id=1`
2. Check logs: `tail flight/storage/logs/app.log`
3. Clear any caches

**Webhooks not received?**

1. Check notify_url is accessible
2. Monitor logs for webhook attempts
3. Use tools like webhook.site for testing

**Return URL not working?**

1. Verify return_url in payment record
2. Check for URL encoding issues
3. Monitor browser network tab

---

Happy testing! 🚀 Sandbox mode makes integration testing a breeze!
