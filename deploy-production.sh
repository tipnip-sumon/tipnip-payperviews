#!/bin/bash

# Production Deployment Script for KYC System
# Run this script on the production server after pulling updates

echo "🚀 Starting KYC System Production Deployment..."

# Check PHP extensions
echo "📋 Checking PHP Extensions..."
if ! php -m | grep -i gd > /dev/null; then
    echo "❌ GD extension not found. Installing..."
    sudo apt-get update
    sudo apt-get install -y php-gd
    echo "✅ GD extension installed"
else
    echo "✅ GD extension is available"
fi

if ! php -m | grep -i fileinfo > /dev/null; then
    echo "❌ Fileinfo extension not found"
    exit 1
else
    echo "✅ Fileinfo extension is available"
fi

# Install/Update composer dependencies
echo "📦 Installing Composer Dependencies..."
composer install --no-dev --optimize-autoloader

# Check Intervention Image
echo "🖼️ Checking Intervention Image..."
if composer show intervention/image > /dev/null 2>&1; then
    echo "✅ Intervention Image is installed"
    composer show intervention/image | grep "versions"
else
    echo "❌ Intervention Image not found. Installing..."
    composer require intervention/image:^2.7
fi

# Clear all caches
echo "🧹 Clearing Laravel Caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Set proper permissions
echo "🔒 Setting File Permissions..."
chmod -R 755 storage/
chmod -R 755 storage/app/public/
chmod -R 755 storage/logs/
chmod -R 755 bootstrap/cache/

# Create symlink for storage if it doesn't exist
if [ ! -L "public/storage" ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link
fi

# Create KYC directories
echo "📁 Creating KYC Directories..."
mkdir -p storage/app/public/kyc_documents
chmod -R 755 storage/app/public/kyc_documents

# Test the system
echo "🧪 Testing System..."
echo "Testing PHP Extensions:"
php -r "
if (extension_loaded('gd')) {
    echo '✅ GD Extension: Available\n';
} else {
    echo '❌ GD Extension: Missing\n';
}

if (class_exists('Intervention\Image\ImageManagerStatic')) {
    echo '✅ Intervention Image: Available\n';
} else {
    echo '❌ Intervention Image: Missing\n';
}

if (is_writable('storage/app/public')) {
    echo '✅ Storage Directory: Writable\n';
} else {
    echo '❌ Storage Directory: Not Writable\n';
}
"

echo "✅ Production deployment completed!"
echo "🔍 Check the Laravel logs for any issues:"
echo "tail -f storage/logs/laravel.log"
