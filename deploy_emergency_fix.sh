#!/bin/bash

# Production Emergency echo "4. Clear caches:"
echo "   php artisan cache:clear"
echo "   php artisan config:clear"
echo ""
echo "5. Test the KYC system and video gallery"yment Script
# This script will push the emergency fixes to production

echo "🚀 PRODUCTION EMERGENCY DEPLOYMENT"
echo "=================================="
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this from the project root."
    exit 1
fi

echo "📝 Staging emergency fixes..."
git add .
git commit -m "Emergency fix: Correct database column references and clean up test files"

echo "🚀 Pushing to production..."
git push origin main

echo ""
echo "✅ Emergency fixes pushed to production!"
echo ""
echo "⚠️  IMPORTANT: Run the following commands on the production server:"
echo ""
echo "1. Navigate to the application directory:"
echo "   cd /var/www/vhosts/payperviews.net/httpdocs"
echo ""
echo "2. Pull the latest changes:"
echo "   git pull origin main"
echo ""
echo "3. Run the emergency database fix (if needed):"
echo "   php production_database_emergency_fix.php"
echo ""
echo "4. Clear caches:"
echo "   php artisan cache:clear"
echo "   php artisan config:clear"
echo ""
echo "6. Test the KYC system and video gallery"
echo ""
