# DigiMart Pay v1 - Production Release Guide

## ✅ System Status: FULLY OPERATIONAL 🚀
The system has been specifically hardened for cPanel shared hosting. 

---

### 🛠️ The "Final Fixers" (What we solved)

If you ever move this project or start a new one, remember these **4 Golden Rules** for Laravel on cPanel:

#### 1. The SMS "Worker" (Queue)
*   **Fix**: `QUEUE_CONNECTION=sync`
*   **Why?**: On cPanel, background workers often don't run. "Sync" makes the SMS send **immediately** while the customer is on the page, instead of waiting in a queue.

#### 2. The PayHere "Door" (CSRF)
*   **Fix**: Added `notify` to the CSRF exclusion list in `bootstrap/app.php`.
*   **Why?**: Laravel blocks all external websites from sending data to your site. This "unlocked the door" specifically for PayHere so it can confirm payments.

#### 3. The "Ghost" Database (SQLite)
*   **Fix**: Forced the system to use **MySQL** by default in `config/database.php`.
*   **Why?**: Laravel tries to look for a local file database (`database.sqlite`) which usually doesn't exist on cPanel, causing the whole site to crash.

#### 4. The Phone "Shield" 
*   **Fix**: Added logic to `SmsService.php` to automatically detect and fix double prefixes (like `+94+94`).
*   **Why?**: Prevents delivery failure if a user types their number with or without the country code.

---

### 📂 Using the "Fixer" Tools

| Tool | Purpose | When to use? |
|------|---------|--------------|
| `fix.php` | Refresh system cache | **Every time** you edit the `.env` file! |
| `log_viewer.php` | Inspect errors | If something stops working (SMS, Webhooks). |
| `test_sms_raw.php` | Test SMS Connection | To check if your SMSAPI.lk balance is high enough. |

---

### 📤 Final Upload Steps (Standard)
1. Upload `v1.zip` to `public_html`.
2. Extract and move files to root.
3. Run `setup.php` (First time only).
4. Update `.env` with your real keys.
5. Run `fix.php` to "activate" the keys.
6. **DELETE** `setup.php`, `fix.php`, and `log_viewer.php` when done for security.

---

### 📋 API Details for Developers
*   **Notify URL**: `https://pay.digimartsolutions.lk/notify` (Unlocked from CSRF)
*   **Return URL**: `https://pay.digimartsolutions.lk/return`
*   **Production DB**: MySQL (`digimart_pay`)

**Enjoy your working Payment Gateway!** ✅💰
