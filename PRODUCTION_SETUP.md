# Production Server Setup Instructions

## Required PHP Extensions

The KYC image optimization requires the following PHP extensions:

1. **GD Extension**
   ```bash
   # For Ubuntu/Debian
   sudo apt-get install php-gd
   
   # For CentOS/RHEL
   sudo yum install php-gd
   
   # Enable in php.ini
   extension=gd
   ```

2. **Fileinfo Extension** (usually included)
   ```bash
   extension=fileinfo
   ```

## Composer Dependencies

Make sure Intervention Image is installed:

```bash
composer require intervention/image:^2.7
composer dump-autoload
```

## File Permissions

Ensure storage directories have proper permissions:

```bash
chmod -R 755 storage/
chmod -R 755 storage/app/public/
chmod -R 755 storage/logs/
```

## Error Handling

The code now includes robust fallback mechanisms:

- If GD extension is not available, files will be copied without optimization
- If Intervention Image is not available, original files will be used
- All errors are logged for debugging
- Dynamic class loading prevents fatal errors on missing dependencies

## Common Production Issues

1. **Container Resolution Error** (from your stack trace):
   ```
   Error: Target class does not exist or cannot be resolved
   ```
   
   **Solution**: Clear all caches and ensure dependencies are installed:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. **Missing GD Extension**:
   ```bash
   # Check if GD is installed
   php -m | grep -i gd
   
   # If not found, install it
   sudo apt-get install php-gd
   sudo service apache2 restart
   ```

## Environment Configuration

Add to your `.env` file:

```env
# Image optimization settings
IMAGE_OPTIMIZATION_ENABLED=true
LOG_LEVEL=info
```

## Troubleshooting

1. **Check PHP Extensions**:
   ```bash
   php -m | grep -i gd
   php -m | grep -i fileinfo
   ```

2. **Check Intervention Image**:
   ```bash
   composer show intervention/image
   ```
   
   Expected output should show version 2.7.x:
   ```
   name     : intervention/image
   versions : * 2.7.2
   requires : ext-fileinfo *, guzzlehttp/psr7, php >=5.4.0
   suggests : ext-gd to use GD library based image processing
   ```

3. **Check Error Logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Testing

After deployment, test the KYC form to ensure:
- Images upload successfully
- Files are stored in `storage/app/public/kyc_documents/`
- No errors in the Laravel logs
