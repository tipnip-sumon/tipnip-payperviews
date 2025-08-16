# 🚨 URGENT PRODUCTION FIX INSTRUCTIONS

## Current Issue
**Error**: `Class "App\Http\Controllers\User\KycImageController" does not exist`

## Root Cause
The production server has cached routes pointing to the old `KycImageController` which was renamed to `KycController`.

## IMMEDIATE FIX (Run these commands on production server)

### Step 1: Navigate to application directory
```bash
cd /var/www/vhosts/payperviews.net/httpdocs
```

### Step 2: Pull latest changes
```bash
git pull origin main
```

### Step 3: Run immediate hotfix
```bash
php immediate_production_hotfix.php
```

### Alternative Manual Fix (if hotfix script fails):
```bash
# Clear all caches manually
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Restart PHP-FPM (if available)
```bash
# On most systems:
sudo systemctl restart php-fpm
# OR
sudo systemctl restart php8.1-fpm
# OR 
sudo service php-fpm restart
```

### Step 5: Test the fix
1. Navigate to: `https://payperviews.net/user/kyc/create`
2. Try uploading an image
3. Check if the KYC image validation works

## What was fixed:
- ✅ Routes now point to correct `KycController` instead of `KycImageController`
- ✅ Database column reference fixed (`daily_video_limit` instead of `video_limit`)
- ✅ All caches cleared to remove old references

## If error persists:
1. Check PHP error logs: `tail -f /var/log/php/error.log`
2. Check Laravel logs: `tail -f storage/logs/laravel.log`
3. Ensure file permissions are correct:
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

## Files Changed:
- `routes/web.php` - Fixed to use `KycController`
- `app/Http/Controllers/admin/VideoLinkController.php` - Fixed column name
- Added emergency fix scripts

## Emergency Contact:
If this doesn't work, the issue might be at the web server level (nginx/apache configuration).
