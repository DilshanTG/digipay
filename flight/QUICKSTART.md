# Quick Start Guide

## Get Started in 3 Steps

### Step 1: Install Dependencies
```bash
cd flight
composer install
```

### Step 2: Configure Environment
```bash
cp .env.example .env
nano .env  # Edit with your credentials
```

Minimum required settings:
```env
ADMIN_PASSWORD=your_secure_password
PAYHERE_MERCHANT_ID=your_merchant_id
PAYHERE_MERCHANT_SECRET=your_secret
```

### Step 3: Initialize Database
```bash
php init_db.php
```

This will create the database and a default merchant account.

## Test It!

### Local Development (PHP Built-in Server)
```bash
cd public
php -S localhost:8000
```

Visit: `http://localhost:8000`

### Test API
```bash
# Get your API key from the init_db.php output, then:

curl -X POST http://localhost:8000/api/v1/init \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 100,
    "return_url": "https://example.com/return",
    "customer_email": "test@example.com",
    "first_name": "Test",
    "description": "Test Payment"
  }'
```

### Access Admin Panel
- URL: `http://localhost:8000/admin`
- Username: `admin`
- Password: (from your `.env` ADMIN_PASSWORD)

## Production Deployment

### Apache
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/flight/public

    <Directory /path/to/flight/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/flight/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## What's Next?

1. Configure PayHere credentials in admin settings
2. Set up SMS API for notifications
3. Create merchant accounts for your clients
4. Test payment flow end-to-end
5. Monitor payments in admin dashboard

## Troubleshooting

**Permission Issues:**
```bash
chmod -R 755 storage database
chown -R www-data:www-data storage database  # For Apache
```

**Database Not Found:**
```bash
php init_db.php
```

**Routes Not Working:**
- Check `.htaccess` exists in `public/`
- Enable `mod_rewrite` for Apache: `sudo a2enmod rewrite`
- Restart web server

## Performance Tips

- Enable OPcache in production
- Use MySQL instead of SQLite for high traffic
- Set `APP_DEBUG=false` in production
- Configure proper caching headers

Enjoy blazing-fast payments with FlightPHP! 🚀
