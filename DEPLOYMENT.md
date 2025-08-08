# PayPerViews Production Deployment Guide

## Pre-Deployment Checklist

### 1. Environment Configuration
- [ ] Copy `.env.production` to `.env` on production server
- [ ] Update database credentials in `.env`
- [ ] Update APP_URL to your production domain
- [ ] Set APP_DEBUG=false
- [ ] Set APP_ENV=production
- [ ] Configure mail settings for production
- [ ] Update Redis/Cache settings if using Redis
- [ ] Verify NowPayments callback URL matches your domain

### 2. Security Settings
- [ ] Generate new APP_KEY for production: `php artisan key:generate`
- [ ] Set SESSION_ENCRYPT=true
- [ ] Set SESSION_SECURE_COOKIE=true (for HTTPS)
- [ ] Configure SANCTUM_STATEFUL_DOMAINS
- [ ] Set proper session domain

### 3. File Permissions (Linux/Unix servers)
```bash
# Set proper permissions for Laravel
sudo chown -R www-data:www-data /path/to/your/app
sudo chmod -R 755 /path/to/your/app
sudo chmod -R 775 /path/to/your/app/storage
sudo chmod -R 775 /path/to/your/app/bootstrap/cache
```

### 4. Required Directories
Ensure these directories exist with proper permissions:
- `storage/logs`
- `storage/framework/cache`
- `storage/framework/sessions`
- `storage/framework/views`
- `storage/framework/testing`
- `storage/app/public`
- `bootstrap/cache`

### 5. Database Setup
```bash
# Run migrations
php artisan migrate --force

# Seed initial data if needed
php artisan db:seed --force

# Create storage symlink
php artisan storage:link
```

### 6. Optimization Commands
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Install dependencies (production only)
composer install --optimize-autoloader --no-dev

# Build assets
npm ci --production
npm run build
```

### 7. Web Server Configuration

#### Apache (.htaccess)
Ensure the `.htaccess` file in public directory exists:
```apache
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

#### Nginx
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com;
    root /path/to/your/app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 8. SSL Certificate
- [ ] Install SSL certificate (Let's Encrypt recommended)
- [ ] Update APP_URL to use https://
- [ ] Set SESSION_SECURE_COOKIE=true

### 9. Cron Jobs
Add to crontab for Laravel scheduler:
```bash
* * * * * cd /path/to/your/app && php artisan schedule:run >> /dev/null 2>&1
```

### 10. Queue Workers (if using queues)
```bash
# Install supervisor for queue workers
sudo apt install supervisor

# Create supervisor config file
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Add this configuration:
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/worker.log
stopwaitsecs=3600
```

### 11. Monitoring & Logging
- [ ] Set up log rotation for Laravel logs
- [ ] Configure error monitoring (Sentry, Bugsnag, etc.)
- [ ] Set up uptime monitoring
- [ ] Configure backup strategy for database and files

### 12. Performance Optimization
- [ ] Enable OPcache in PHP
- [ ] Configure Redis for caching and sessions
- [ ] Set up CDN for static assets
- [ ] Optimize images and assets
- [ ] Enable gzip compression

### 13. Final Testing
- [ ] Test all critical user flows
- [ ] Verify payment processing works
- [ ] Check email sending functionality
- [ ] Test video viewing and earning system
- [ ] Verify admin panel functionality
- [ ] Check mobile responsiveness

## Deployment Commands Summary

```bash
# 1. Upload files to server
# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Set up environment
cp .env.production .env
php artisan key:generate

# 4. Set up database
php artisan migrate --force

# 5. Create storage link
php artisan storage:link

# 6. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Set permissions
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

## Troubleshooting

### Common Issues:
1. **500 Error**: Check Laravel logs in `storage/logs/laravel.log`
2. **Permission Issues**: Ensure storage and bootstrap/cache have write permissions
3. **View Cache Issues**: Clear view cache with `php artisan view:clear`
4. **Config Issues**: Clear config cache with `php artisan config:clear`

### Important Notes:
- Always backup your database before deployment
- Test in a staging environment first
- Keep your `.env` file secure and never commit it to version control
- Monitor logs for the first few hours after deployment
