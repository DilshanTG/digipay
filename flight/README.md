# DigiPay - FlightPHP Payment Gateway

A blazing-fast payment gateway wrapper built with FlightPHP for PayHere (Sri Lanka).

## Features

- **Ultra-Fast Performance**: Built with FlightPHP micro-framework for maximum speed
- **PayHere Integration**: Complete PayHere payment gateway integration
- **API & Direct Modes**: Supports both API integration and direct payment forms
- **SMS Notifications**: Automatic SMS notifications to customers and admin
- **WhatsApp Receipts**: Auto-generated JPG receipts
- **Webhook Support**: PayHere-compatible webhooks for merchant callbacks
- **Admin Dashboard**: Simple admin panel for monitoring payments
- **Security**: Domain whitelisting, API key authentication, hash verification

## Requirements

- PHP 8.2 or higher
- SQLite or MySQL
- Composer
- Apache/Nginx with mod_rewrite

## Installation

1. **Clone and Install Dependencies**
   ```bash
   cd flight
   composer install
   ```

2. **Configure Environment**
   ```bash
   cp .env.example .env
   # Edit .env with your credentials
   ```

3. **Set Permissions**
   ```bash
   chmod -R 755 storage
   chmod -R 755 database
   ```

4. **Configure Web Server**

   **Apache**: Already configured via `.htaccess`

   **Nginx**: Add to your site configuration:
   ```nginx
   location / {
       try_files $uri $uri/ /index.php?$query_string;
   }
   ```

5. **Point Document Root**
   Point your web server document root to `flight/public`

## Directory Structure

```
flight/
├── config/          # Configuration files
├── src/
│   ├── Controllers/ # (Not used - routes in index.php)
│   ├── Models/      # Database models
│   ├── Services/    # Business logic services
│   └── Middleware/  # (Not used - inline auth)
├── public/          # Web root
│   ├── index.php    # Main application file
│   └── .htaccess    # Apache rewrite rules
├── views/           # Templates
├── storage/         # Logs, cache, receipts
├── database/        # SQLite database
└── README.md
```

## API Endpoints

### Initialize Payment
```bash
POST /api/v1/init
Authorization: Bearer YOUR_API_KEY

{
  "amount": 1000,
  "currency": "LKR",
  "return_url": "https://yoursite.com/return",
  "notify_url": "https://yoursite.com/webhook",
  "customer_email": "customer@email.com",
  "customer_phone": "0771234567",
  "first_name": "John",
  "last_name": "Doe",
  "description": "Order #123",
  "client_order_id": "YOUR-ORDER-ID"
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "order_id": "ORD-ABC123XYZ",
    "payment_url": "https://yoursite.com/pay/jump/ORD-ABC123XYZ"
  }
}
```

### Check Payment Status
```bash
GET /api/v1/status/{order_id}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "order_id": "ORD-ABC123XYZ",
    "status": "SUCCESS",
    "amount": 1000,
    "currency": "LKR",
    "payhere_ref": "PY123456",
    "updated_at": "2024-01-20 10:30:00"
  }
}
```

## Web Routes

- `GET /` - Direct payment form
- `POST /pay/process` - Process direct payment
- `GET /pay/jump/{token}` - Redirect to PayHere
- `POST /notify` - PayHere webhook handler
- `GET /return` - Payment return handler
- `GET /admin` - Admin dashboard (Basic Auth)
- `GET /admin/payments` - All payments
- `GET /admin/merchants` - Merchant management
- `GET /admin/settings` - Settings

## Admin Access

Default credentials:
- **Username**: admin
- **Password**: Set in `.env` (`ADMIN_PASSWORD`)

## Configuration

### PayHere Setup

1. Get credentials from PayHere dashboard
2. Update `.env`:
   ```
   PAYHERE_MODE=sandbox or live
   PAYHERE_MERCHANT_ID=your_merchant_id
   PAYHERE_MERCHANT_SECRET=your_secret
   PAYHERE_APP_ID=your_app_id
   PAYHERE_APP_SECRET=your_app_secret
   ```

### SMS Setup

1. Get API token from smsapi.lk
2. Update `.env`:
   ```
   SMS_ENABLED=true
   SMS_API_TOKEN=your_token
   SMS_SENDER_ID=YOURNAME
   ```

## Performance Features

- **No ORM Overhead**: Direct PDO for maximum speed
- **Minimal Dependencies**: Only FlightPHP and essential libraries
- **Optimized Routing**: Direct route matching without middleware stack
- **Lightweight**: ~10x faster than Laravel for simple API requests

## Security

- Bearer token authentication for API
- Domain whitelisting
- PayHere hash signature verification
- Admin Basic Auth
- SQL injection protection via prepared statements

## License

MIT License

## Support

For issues and questions, contact the development team.
