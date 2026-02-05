# Update Deployment Guide

This guide explains how to safely update your ArthaVidhi application on the server without affecting your existing database or user data.

---

## Prerequisites

- SSH access to your server
- Git installed on the server (if using Git deployment)
- Backup access to your database

---

## ⚠️ Important: Before You Start

### 1. Backup Your Database (Recommended)

Always backup your database before any update:

```bash
# For MySQL
mysqldump -u your_username -p your_database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# For PostgreSQL
pg_dump -U your_username your_database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Backup Uploaded Files

If you have user-uploaded files (like product images), back them up:

```bash
cp -r /path/to/your/app/storage/app/public /path/to/backup/storage_backup
```

---

## Method 1: Git-Based Deployment (Recommended)

If your server is connected to Git:

### Step 1: SSH into Your Server

```bash
ssh user@your-server-ip
```

### Step 2: Navigate to Your Application Directory

```bash
cd /path/to/your/application
```

### Step 3: Put Application in Maintenance Mode

```bash
php artisan down
```

This shows a "503 Service Unavailable" page to users while you update.

### Step 4: Pull Latest Changes

```bash
git pull origin main
```

> Replace `main` with your branch name (e.g., `master`, `production`)

### Step 5: Install/Update Dependencies (If composer.json changed)

```bash
composer install --no-dev --optimize-autoloader
```

### Step 6: Run Migrations (If there are new migrations)

```bash
php artisan migrate --force
```

> The `--force` flag is required in production

### Step 7: Clear and Rebuild Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild caches for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 8: Bring Application Back Online

```bash
php artisan up
```

---

## Method 2: Manual File Upload (FTP/SFTP)

If you're not using Git:

### Step 1: Identify Changed Files

Only upload files that have changed. Based on recent changes, these are:

```
resources/views/layouts/app.blade.php
resources/views/pdf/bill.blade.php
resources/views/pdf/quotation.blade.php
app/Http/Middleware/PreserveTabParameter.php
bootstrap/app.php
```

### Step 2: Put Application in Maintenance Mode

SSH into server and run:

```bash
php artisan down
```

Or create a file named `down` in `storage/framework/`:

```bash
touch storage/framework/down
```

### Step 3: Upload Changed Files via SFTP

Use an SFTP client (FileZilla, WinSCP, etc.):

1. Connect to your server
2. Navigate to your application directory
3. Upload only the changed files to their respective locations
4. Maintain the same folder structure

### Step 4: Clear Caches on Server

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 5: Bring Application Back Online

```bash
php artisan up
```

Or remove the down file:

```bash
rm storage/framework/down
```

---

## Method 3: Using Deployment Script

Create a deployment script on your server for easy updates:

### Create the Script

```bash
nano ~/deploy.sh
```

Add this content:

```bash
#!/bin/bash

# Configuration
APP_DIR="/path/to/your/application"
BRANCH="main"

echo "🚀 Starting deployment..."

cd $APP_DIR

# Maintenance mode
echo "📴 Entering maintenance mode..."
php artisan down

# Pull latest changes
echo "📥 Pulling latest changes..."
git pull origin $BRANCH

# Install dependencies (uncomment if needed)
# echo "📦 Installing dependencies..."
# composer install --no-dev --optimize-autoloader

# Run migrations (uncomment if needed)
# echo "🗃️ Running migrations..."
# php artisan migrate --force

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild caches
echo "🔧 Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Back online
echo "✅ Bringing application online..."
php artisan up

echo "🎉 Deployment complete!"
```

### Make it Executable

```bash
chmod +x ~/deploy.sh
```

### Run Deployment

```bash
~/deploy.sh
```

---

## What Gets Updated vs What Stays Safe

### ✅ SAFE - Will NOT be affected:

| Item | Location | Why It's Safe |
|------|----------|---------------|
| Database | MySQL/PostgreSQL | Stored separately, not in code |
| Uploaded files | `storage/app/public/` | Not tracked by Git |
| Environment config | `.env` file | Not tracked by Git |
| User sessions | `storage/framework/sessions/` | Not tracked by Git |
| Logs | `storage/logs/` | Not tracked by Git |

### 🔄 UPDATED - Will be replaced:

| Item | Location |
|------|----------|
| PHP code | `app/` |
| Blade views | `resources/views/` |
| Routes | `routes/` |
| Config files | `config/` |
| Public assets | `public/` (except uploads) |

---

## Specific Files Changed in This Update

For this specific update (tabbed layout + PDF improvements), upload these files:

```
✅ resources/views/layouts/app.blade.php          (Tabbed layout)
✅ resources/views/pdf/bill.blade.php             (New bill PDF format)
✅ resources/views/pdf/quotation.blade.php        (New quotation PDF format)
✅ app/Http/Middleware/PreserveTabParameter.php   (New middleware)
✅ bootstrap/app.php                               (Middleware registration)
```

---

## Troubleshooting

### Error: "Class not found"

```bash
composer dump-autoload
php artisan config:clear
```

### Error: "View not found"

```bash
php artisan view:clear
php artisan cache:clear
```

### Error: "Permission denied"

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Application Stuck in Maintenance Mode

```bash
php artisan up
# OR
rm storage/framework/down
```

### Changes Not Reflecting

```bash
# Clear all caches
php artisan optimize:clear

# If using OPcache, restart PHP
sudo systemctl restart php8.2-fpm
```

---

## Quick Command Reference

```bash
# Check current status
php artisan about

# View recent logs
tail -f storage/logs/laravel.log

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# List all routes
php artisan route:list

# Check migrations status
php artisan migrate:status
```

---

## Rollback (If Something Goes Wrong)

### Restore from Git

```bash
git checkout HEAD~1  # Go back one commit
php artisan cache:clear
php artisan config:clear
```

### Restore Database from Backup

```bash
# MySQL
mysql -u your_username -p your_database_name < backup_file.sql

# PostgreSQL
psql -U your_username your_database_name < backup_file.sql
```

---

## Summary: Quick Update Steps

```bash
# 1. SSH into server
ssh user@your-server

# 2. Go to app directory
cd /path/to/app

# 3. Maintenance mode
php artisan down

# 4. Pull changes
git pull origin main

# 5. Clear caches
php artisan optimize:clear

# 6. Rebuild caches (optional but recommended)
php artisan config:cache
php artisan route:cache

# 7. Back online
php artisan up
```

Your database and uploaded files will remain completely untouched! 🎉
