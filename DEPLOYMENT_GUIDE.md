# ArthaVidhi Billing System - cPanel Deployment Guide

Complete guide to deploy this Laravel 11 application on cPanel subdomain: **arthavidhi.haitomnsgroups.com**

---

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Prepare Project for Production](#prepare-project-for-production)
3. [Create Database on cPanel](#create-database-on-cpanel)
4. [Create Subdomain on cPanel](#create-subdomain-on-cpanel)
5. [Upload Files to cPanel](#upload-files-to-cpanel)
6. [Configure Environment](#configure-environment)
7. [Run Migrations](#run-migrations)
8. [Set Permissions](#set-permissions)
9. [SSL Certificate](#ssl-certificate)
10. [Troubleshooting](#troubleshooting)

---

## Prerequisites

Before deploying, ensure your hosting meets these requirements:

| Requirement | Minimum Version |
|-------------|-----------------|
| PHP | 8.2 or higher (this project uses 8.4) |
| MySQL | 5.7+ or MariaDB 10.3+ |
| Composer | Available via SSH or locally |
| Extensions | BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, cURL, GD |

### Check PHP Version on cPanel
1. Go to **cPanel → Select PHP Version**
2. Select PHP 8.2 or 8.4
3. Enable required extensions: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `curl`, `xml`

---

## Prepare Project for Production

### Step 1: On Your Local Machine

Open PowerShell and run:

```powershell
# Navigate to backend folder
cd D:\billing\backend

# Install production dependencies only
composer install --optimize-autoloader --no-dev

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Generate optimized files for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 2: Create Production .env File

Create a new file called `.env.production` in `D:\billing\backend\` with these settings:

```env
APP_NAME="ArthaVidhi Billing"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_TIMEZONE=Asia/Kathmandu
APP_URL=https://arthavidhi.haitomnsgroups.com

# Database (update with your cPanel credentials)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=haitomns_arthavidhi
DB_USERNAME=haitomns_arthavidhi
DB_PASSWORD=YOUR_DATABASE_PASSWORD

# Session & Cache
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=single
LOG_LEVEL=error

# Mail (configure if needed)
MAIL_MAILER=smtp
MAIL_HOST=mail.haitomnsgroups.com
MAIL_PORT=465
MAIL_USERNAME=noreply@haitomnsgroups.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@haitomnsgroups.com
MAIL_FROM_NAME="ArthaVidhi Billing"
```

### Step 3: Get Your App Key

Run this command and copy the key:

```powershell
php artisan key:generate --show
```

Paste the generated key (e.g., `base64:xxxxx...`) into the `.env.production` file.

---

## Create Database on cPanel

### Step 1: Login to cPanel
Go to: `https://haitomnsgroups.com:2083` (or your cPanel URL)

### Step 2: Create MySQL Database
1. Go to **MySQL® Databases**
2. Under "Create New Database":
   - Enter database name: `arthavidhi`
   - This will become: `haitomns_arthavidhi` (cPanel adds prefix)
   - Click **Create Database**

### Step 3: Create Database User
1. Under "MySQL Users → Add New User":
   - Username: `arthavidhi` (becomes `haitomns_arthavidhi`)
   - Password: Generate a strong password and **SAVE IT**
   - Click **Create User**

### Step 4: Add User to Database
1. Under "Add User To Database":
   - Select user: `haitomns_arthavidhi`
   - Select database: `haitomns_arthavidhi`
   - Click **Add**
2. On privileges page, check **ALL PRIVILEGES**
3. Click **Make Changes**

---

## Create Subdomain on cPanel

### Step 1: Create the Subdomain
1. Go to **cPanel → Domains** (or **Subdomains** in older cPanel)
2. Click **Create A New Domain**
3. Enter: `arthavidhi.haitomnsgroups.com`
4. **Document Root**: `/home/haitomns/arthavidhi.haitomnsgroups.com`
   - Or it may auto-set to `/home/haitomns/public_html/arthavidhi`
5. Click **Submit**

**Note:** Remember the document root path - you'll need it!

---

## Upload Files to cPanel

### File Structure for Subdomain

Your cPanel file structure should look like this:

```
/home/haitomns/
│
├── arthavidhi.haitomnsgroups.com/    ← Subdomain document root (PUBLIC)
│   ├── index.php                     ← Modified Laravel index.php
│   ├── .htaccess                     ← Laravel's public .htaccess
│   ├── favicon.ico
│   ├── robots.txt
│   └── storage/                      ← Symlink to ../arthavidhi-app/storage/app/public
│
└── arthavidhi-app/                   ← Laravel application (PRIVATE - outside subdomain)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env
    ├── artisan
    └── composer.json
```

### Step-by-Step Upload Process

#### Step 1: Create ZIP of Backend Folder

On your local machine, compress the `D:\billing\backend` folder into a ZIP file named `arthavidhi-app.zip`.

**Using Windows:**
1. Right-click on `D:\billing\backend` folder
2. Select "Send to → Compressed (zipped) folder"
3. Rename to `arthavidhi-app.zip`

#### Step 2: Upload to cPanel

1. Go to **cPanel → File Manager**
2. Navigate to `/home/haitomns/` (your home directory)
3. Click **Upload** in the toolbar
4. Upload `arthavidhi-app.zip`
5. Once uploaded, right-click the ZIP file → **Extract**
6. Rename extracted folder from `backend` to `arthavidhi-app`

#### Step 3: Create Modified index.php

1. In File Manager, navigate to `/home/haitomns/arthavidhi.haitomnsgroups.com/`
2. Delete any existing files (like default index.html)
3. Click **+ File** → Create new file named `index.php`
4. Right-click `index.php` → **Edit** → Paste this code:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../arthavidhi-app/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../arthavidhi-app/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../arthavidhi-app/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

5. Click **Save Changes**

#### Step 4: Create .htaccess File

1. In the same folder (`arthavidhi.haitomnsgroups.com/`)
2. Click **+ File** → Create `.htaccess`
3. Edit and paste:

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

4. Click **Save Changes**

---

## Configure Environment

### Step 1: Upload .env File

1. Go to File Manager → `/home/haitomns/arthavidhi-app/`
2. If `.env` file exists, right-click → **Edit**
3. If not, click **+ File** → Create `.env`
4. Paste your production environment settings:

```env
APP_NAME="ArthaVidhi Billing"
APP_ENV=production
APP_KEY=base64:YOUR_KEY_FROM_STEP_3
APP_DEBUG=false
APP_TIMEZONE=Asia/Kathmandu
APP_URL=https://arthavidhi.haitomnsgroups.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=haitomns_arthavidhi
DB_USERNAME=haitomns_arthavidhi
DB_PASSWORD=YOUR_DATABASE_PASSWORD_HERE

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

LOG_CHANNEL=single
LOG_LEVEL=error
```

5. Click **Save Changes**

---

## Run Migrations

### Method 1: Using cPanel Terminal (Recommended)

1. Go to **cPanel → Terminal**
2. Run these commands:

```bash
# Navigate to Laravel folder
cd ~/arthavidhi-app

# Run migrations
php artisan migrate --force

# Create storage link (may need manual method below)
php artisan storage:link

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Method 2: Using SSH

```bash
# SSH into server
ssh haitomns@haitomnsgroups.com

# Navigate to Laravel folder
cd ~/arthavidhi-app

# Run same commands as above
php artisan migrate --force
php artisan storage:link
php artisan config:cache
```

### Method 3: Without SSH (PHP Script)

If Terminal/SSH is not available, create `migrate.php` in the subdomain folder:

1. Go to File Manager → `/home/haitomns/arthavidhi.haitomnsgroups.com/`
2. Create new file `migrate.php`:

```php
<?php
// ⚠️ DELETE THIS FILE IMMEDIATELY AFTER USE - SECURITY RISK! ⚠️

require __DIR__.'/../arthavidhi-app/vendor/autoload.php';
$app = require_once __DIR__.'/../arthavidhi-app/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre style='font-family: monospace; background: #1e1e1e; color: #0f0; padding: 20px;'>";
echo "=== ArthaVidhi Migration Script ===\n\n";

// Run migrations
echo "Running migrations...\n";
$kernel->call('migrate', ['--force' => true]);
echo $kernel->output();

// Cache config
echo "\nCaching configuration...\n";
$kernel->call('config:cache');
echo $kernel->output();

// Cache routes
echo "\nCaching routes...\n";
$kernel->call('route:cache');
echo $kernel->output();

echo "\n\n✅ DONE!\n";
echo "\n⚠️ NOW DELETE THIS FILE (migrate.php) FROM YOUR SERVER! ⚠️";
echo "</pre>";
```

3. Visit: `https://arthavidhi.haitomnsgroups.com/migrate.php`
4. **DELETE `migrate.php` IMMEDIATELY after running!**

---

## Set Permissions

### Via cPanel Terminal:

```bash
cd ~/arthavidhi-app

# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Make storage and cache writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Ensure storage directories exist
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p storage/app/public/products
```

### Via File Manager:
1. Navigate to `/home/haitomns/arthavidhi-app/storage/`
2. Right-click → **Change Permissions**
3. Set to `775` (check all boxes for owner and group)
4. Check **"Recurse into subdirectories"**
5. Click **Change Permissions**
6. Repeat for `bootstrap/cache` folder

---

## Storage Link (For Product Images)

### Method 1: Via Terminal
```bash
cd ~/arthavidhi-app
php artisan storage:link
```

This creates a link from `arthavidhi.haitomnsgroups.com/storage` → `arthavidhi-app/storage/app/public`

### Method 2: Manual Symlink (If artisan doesn't work)

In cPanel Terminal:
```bash
cd ~/arthavidhi.haitomnsgroups.com
ln -s ../arthavidhi-app/storage/app/public storage
```

### Method 3: PHP Script (If symlinks don't work on shared hosting)

Create `storage-link.php` in subdomain folder:

```php
<?php
// DELETE AFTER USE!
$target = dirname(__DIR__) . '/arthavidhi-app/storage/app/public';
$link = __DIR__ . '/storage';

echo "<pre>";
echo "Target: $target\n";
echo "Link: $link\n\n";

if (file_exists($link)) {
    echo "⚠️ Link/folder already exists!\n";
    echo "Remove it first if you want to recreate.";
} else {
    if (symlink($target, $link)) {
        echo "✅ Storage link created successfully!";
    } else {
        echo "❌ Failed. Try manual symlink or copy method.";
    }
}
echo "</pre>";
// DELETE THIS FILE AFTER RUNNING!
```

Visit `https://arthavidhi.haitomnsgroups.com/storage-link.php` then delete the file.

---

## SSL Certificate

### Enable Free SSL (Let's Encrypt)

1. Go to **cPanel → SSL/TLS Status**
2. Find `arthavidhi.haitomnsgroups.com` in the list
3. Click **Run AutoSSL** or **Issue**
4. Wait a few minutes for certificate to be issued

### Verify HTTPS is Working
Visit: `https://arthavidhi.haitomnsgroups.com`

You should see a padlock icon in the browser.

---

## Final Checklist

After deployment, verify:

- [ ] Site loads at `https://arthavidhi.haitomnsgroups.com`
- [ ] Login page works
- [ ] Can register a new account
- [ ] Can create bills, quotations, products
- [ ] Product images upload and display correctly
- [ ] PDF download works for bills
- [ ] Dark mode toggle works
- [ ] No errors in `arthavidhi-app/storage/logs/laravel.log`

---

## Troubleshooting

### Error: 500 Internal Server Error

1. **Check Laravel logs:**
   - File Manager → `arthavidhi-app/storage/logs/laravel.log`
   
2. **Enable debug temporarily:**
   - Edit `.env` → Set `APP_DEBUG=true`
   - Refresh page to see actual error
   - **Set back to `false` after fixing!**

3. **Check permissions:**
   ```bash
   chmod -R 775 ~/arthavidhi-app/storage
   chmod -R 775 ~/arthavidhi-app/bootstrap/cache
   ```

### Error: Class not found

```bash
cd ~/arthavidhi-app
composer dump-autoload
php artisan config:cache
```

### Error: Database Connection Refused

1. Verify database credentials in `.env`
2. Check username format: `haitomns_arthavidhi` (with cPanel prefix)
3. Ensure user has privileges on the database

### Error: Storage Link / Images Not Showing

1. Check if `storage` folder exists in subdomain directory
2. Check if it's a proper symlink:
   ```bash
   ls -la ~/arthavidhi.haitomnsgroups.com/
   ```
3. If not a symlink, manually copy uploaded files periodically

### Error: CSRF Token Mismatch

1. Clear browser cookies for the domain
2. Verify `SESSION_DRIVER=file` in `.env`
3. Ensure `storage/framework/sessions` is writable

### Error: Mixed Content (HTTP/HTTPS)

1. Verify `APP_URL=https://arthavidhi.haitomnsgroups.com` in `.env`
2. Clear config cache: `php artisan config:cache`

---

## Maintenance Commands

### Clear All Caches
```bash
cd ~/arthavidhi-app
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Rebuild Caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Put Site in Maintenance Mode
```bash
php artisan down --secret="your-secret-key"
# Access via: https://arthavidhi.haitomnsgroups.com/your-secret-key
```

### Bring Site Back Up
```bash
php artisan up
```

---

## Quick Reference

| Item | Value |
|------|-------|
| **URL** | https://arthavidhi.haitomnsgroups.com |
| **Subdomain Root** | /home/haitomns/arthavidhi.haitomnsgroups.com/ |
| **Laravel App** | /home/haitomns/arthavidhi-app/ |
| **Database** | haitomns_arthavidhi |
| **DB User** | haitomns_arthavidhi |
| **Logs** | /home/haitomns/arthavidhi-app/storage/logs/laravel.log |
| **Uploads** | /home/haitomns/arthavidhi-app/storage/app/public/products/ |

---

**Deployment Guide Version:** 2.0  
**Target Domain:** arthavidhi.haitomnsgroups.com  
**Last Updated:** February 2026  
**Laravel Version:** 11.x  
**PHP Version:** 8.4.x
