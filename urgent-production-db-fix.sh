#!/bin/bash

# URGENT: Production Database Fix for Column Missing Error
# This script fixes the SQLSTATE[42S22] error for missing embed_url column

echo "🚨 URGENT: Fixing production database schema..."

# Backup current database first
echo "📦 Creating database backup..."
mysqldump -u root -p viewcash > "backup_urgent_$(date +%Y%m%d_%H%M%S).sql"

if [ $? -eq 0 ]; then
    echo "✅ Database backup created successfully"
else
    echo "❌ Database backup failed! Aborting migration."
    exit 1
fi

# Run the specific migration that adds the missing columns
echo "🔧 Running database migration..."
php artisan migrate --path=database/migrations/2025_08_16_190745_add_embed_url_to_video_links_table.php --force

if [ $? -eq 0 ]; then
    echo "✅ Migration completed successfully"
else
    echo "❌ Migration failed! Check logs."
    exit 1
fi

# Verify the columns were added
echo "🔍 Verifying columns exist..."
mysql -u root -p viewcash -e "DESCRIBE video_links;" | grep -E "(embed_url|earning_per_view)"

if [ $? -eq 0 ]; then
    echo "✅ Columns verified successfully"
    echo "🎉 Production database fix complete!"
else
    echo "❌ Column verification failed"
    exit 1
fi

# Clear all caches after database change
echo "🧹 Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "🚀 Production database fix completed successfully!"
echo "📋 The VideoLinkController should now work without column errors."
