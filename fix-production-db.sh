#!/bin/bash

# Production Database Fix Script
# Fixes the missing embed_url and earning_per_view columns in video_links table

echo "🔧 Starting Production Database Fix..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

# Backup database before making changes
echo "💾 Creating database backup..."
BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
mysqldump -u root -p viewcash > "$BACKUP_FILE"
echo "✅ Database backed up to $BACKUP_FILE"

# Clear all caches first
echo "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Check current migration status
echo "📋 Checking migration status..."
php artisan migrate:status

# Run the migration
echo "🚀 Running migration to add missing columns..."
php artisan migrate --force

# Verify the columns were added
echo "🔍 Verifying database structure..."
mysql -u root -p viewcash -e "DESCRIBE video_links;" | grep -E "(embed_url|earning_per_view)"

if [ $? -eq 0 ]; then
    echo "✅ Columns added successfully!"
else
    echo "❌ Warning: Could not verify column addition. Please check manually."
fi

# Clear caches again after migration
echo "🧹 Clearing caches after migration..."
php artisan config:clear
php artisan cache:clear

echo "✅ Production database fix completed!"
echo "📝 Backup file: $BACKUP_FILE"
echo "🔗 Test the video gallery at: https://payperviews.net/gallery"
