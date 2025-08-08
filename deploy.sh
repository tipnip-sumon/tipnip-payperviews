#!/bin/bash

# PayPerViews Production Deployment Script
# Run this script on your production server after uploading the files

echo "🚀 Starting PayPerViews Production Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

print_status "Setting up environment..."

# Copy production environment file
if [ -f ".env.production" ]; then
    cp .env.production .env
    print_status "Environment file copied from .env.production"
else
    print_warning "No .env.production file found. Please configure .env manually."
fi

# Install Composer dependencies
print_status "Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Generate application key if not set
print_status "Generating application key..."
php artisan key:generate --force

# Create required directories
print_status "Creating required directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/testing
mkdir -p storage/app/public
mkdir -p bootstrap/cache

# Set permissions (Linux/Unix only)
if [[ "$OSTYPE" == "linux-gnu"* ]] || [[ "$OSTYPE" == "darwin"* ]]; then
    print_status "Setting file permissions..."
    
    # Find the web server user
    if id "www-data" &>/dev/null; then
        WEB_USER="www-data"
    elif id "apache" &>/dev/null; then
        WEB_USER="apache"
    elif id "nginx" &>/dev/null; then
        WEB_USER="nginx"
    else
        WEB_USER=$(whoami)
        print_warning "Could not determine web server user. Using current user: $WEB_USER"
    fi
    
    sudo chown -R $WEB_USER:$WEB_USER .
    sudo chmod -R 755 .
    sudo chmod -R 775 storage
    sudo chmod -R 775 bootstrap/cache
    print_status "Permissions set for user: $WEB_USER"
fi

# Clear all caches
print_status "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run database migrations
print_status "Running database migrations..."
read -p "Do you want to run database migrations? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    print_status "Database migrations completed"
else
    print_warning "Skipped database migrations"
fi

# Create storage symlink
print_status "Creating storage symlink..."
php artisan storage:link

# Optimize for production
print_status "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build frontend assets if Node.js is available
if command -v npm &> /dev/null; then
    print_status "Building frontend assets..."
    npm ci --production
    npm run build
    print_status "Frontend assets built successfully"
else
    print_warning "npm not found. Please build frontend assets manually with 'npm run build'"
fi

# Final checks
print_status "Performing final checks..."

# Check if .env exists and has required values
if [ ! -f ".env" ]; then
    print_error ".env file missing!"
    exit 1
fi

# Check for APP_KEY
if ! grep -q "^APP_KEY=base64:" .env; then
    print_error "APP_KEY not properly set in .env file!"
    exit 1
fi

# Check if storage directory is writable
if [ ! -w "storage" ]; then
    print_error "Storage directory is not writable!"
    exit 1
fi

print_status "Deployment completed successfully! 🎉"
echo
echo "📋 Final checklist:"
echo "   • Configure your web server to point to the 'public' directory"
echo "   • Update database credentials in .env file"
echo "   • Update APP_URL in .env file to match your domain"
echo "   • Set up SSL certificate"
echo "   • Configure cron job: * * * * * php $(pwd)/artisan schedule:run"
echo "   • Test the application thoroughly"
echo
echo "🔗 Important URLs:"
echo "   • Admin Panel: https://your-domain.com/admin"
echo "   • Videos Page: https://your-domain.com/videos"
echo "   • API Docs: https://your-domain.com/api/docs (if available)"
echo
print_warning "Remember to:"
echo "   • Update .env with production database credentials"
echo "   • Set APP_DEBUG=false in .env"
echo "   • Set APP_ENV=production in .env"
echo "   • Configure mail settings for production"
