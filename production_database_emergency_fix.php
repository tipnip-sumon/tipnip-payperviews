<?php
/**
 * Production Emergency Fix Script for Missing Database Columns
 * Run this on the production server to fix all database schema issues
 * 
 * Usage: php production_database_emergency_fix.php
 */

require_once 'vendor/autoload.php';

// Load environment variables
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;

echo "🚨 PRODUCTION DATABASE EMERGENCY FIX 🚨\n";
echo "=====================================\n\n";

try {
    // Check current database connection
    $dbName = DB::connection()->getDatabaseName();
    echo "📊 Connected to database: {$dbName}\n\n";
    
    $fixes = [];
    $errors = [];
    
    // 1. Fix video_links table - add missing columns
    echo "🔧 Checking video_links table...\n";
    if (!Schema::hasColumn('video_links', 'embed_url')) {
        try {
            Schema::table('video_links', function (Blueprint $table) {
                $table->text('embed_url')->nullable()->after('video_url');
            });
            $fixes[] = "✅ Added embed_url column to video_links table";
        } catch (Exception $e) {
            $errors[] = "❌ Failed to add embed_url column: " . $e->getMessage();
        }
    } else {
        echo "✓ embed_url column already exists\n";
    }
    
    if (!Schema::hasColumn('video_links', 'earning_per_view')) {
        try {
            Schema::table('video_links', function (Blueprint $table) {
                $table->decimal('earning_per_view', 10, 6)->default(0.001000)->after('cost_per_click');
            });
            $fixes[] = "✅ Added earning_per_view column to video_links table";
        } catch (Exception $e) {
            $errors[] = "❌ Failed to add earning_per_view column: " . $e->getMessage();
        }
    } else {
        echo "✓ earning_per_view column already exists\n";
    }
    
    // 2. Fix plans table - check existing columns
    echo "\n🔧 Checking plans table...\n";
    if (!Schema::hasColumn('plans', 'daily_video_limit')) {
        try {
            Schema::table('plans', function (Blueprint $table) {
                $table->integer('daily_video_limit')->default(10)->after('fixed_amount');
            });
            $fixes[] = "✅ Added daily_video_limit column to plans table";
        } catch (Exception $e) {
            $errors[] = "❌ Failed to add daily_video_limit column: " . $e->getMessage();
        }
    } else {
        echo "✓ daily_video_limit column already exists\n";
    }
    
    if (!Schema::hasColumn('plans', 'video_access_enabled')) {
        try {
            Schema::table('plans', function (Blueprint $table) {
                $table->boolean('video_access_enabled')->default(true)->after('daily_video_limit');
            });
            $fixes[] = "✅ Added video_access_enabled column to plans table";
        } catch (Exception $e) {
            $errors[] = "❌ Failed to add video_access_enabled column: " . $e->getMessage();
        }
    } else {
        echo "✓ video_access_enabled column already exists\n";
    }
    
    // 3. Update existing plans with default values if needed
    echo "\n🔧 Ensuring existing plans have proper default values...\n";
    try {
        $updatedPlans = DB::table('plans')
            ->where('daily_video_limit', 0)
            ->orWhereNull('daily_video_limit')
            ->update(['daily_video_limit' => 10]);
        
        $enabledPlans = DB::table('plans')
            ->whereNull('video_access_enabled')
            ->update(['video_access_enabled' => true]);
            
        if ($updatedPlans > 0 || $enabledPlans > 0) {
            $fixes[] = "✅ Updated plans with proper default video settings (daily_video_limit: {$updatedPlans}, video_access_enabled: {$enabledPlans})";
        } else {
            echo "✓ All plans already have proper default values\n";
        }
    } catch (Exception $e) {
        $errors[] = "❌ Failed to update existing plans: " . $e->getMessage();
    }
    
    // 4. Clear all caches
    echo "\n🔧 Clearing caches...\n";
    try {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $fixes[] = "✅ Cleared all Laravel caches";
    } catch (Exception $e) {
        $errors[] = "❌ Failed to clear caches: " . $e->getMessage();
    }
    
    // 5. Test database queries
    echo "\n🔧 Testing critical database queries...\n";
    try {
        // Test plans query
        $plansTest = DB::table('plans')
            ->select(['id', 'name', 'fixed_amount', 'daily_video_limit'])
            ->where('status', true)
            ->where('video_access_enabled', true)
            ->limit(1)
            ->get();
        $fixes[] = "✅ Plans table query test successful";
        
        // Test video_links query
        $videoTest = DB::table('video_links')
            ->select(['id', 'title', 'embed_url', 'earning_per_view'])
            ->limit(1)
            ->get();
        $fixes[] = "✅ Video_links table query test successful";
        
    } catch (Exception $e) {
        $errors[] = "❌ Database query test failed: " . $e->getMessage();
    }
    
    // Summary
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📋 SUMMARY\n";
    echo str_repeat("=", 50) . "\n\n";
    
    echo "✅ SUCCESSFUL FIXES (" . count($fixes) . "):\n";
    foreach ($fixes as $fix) {
        echo "   {$fix}\n";
    }
    
    if (!empty($errors)) {
        echo "\n❌ ERRORS (" . count($errors) . "):\n";
        foreach ($errors as $error) {
            echo "   {$error}\n";
        }
    }
    
    echo "\n🎉 Emergency fix completed!\n";
    echo "⚠️  Please test the KYC system and video gallery functionality.\n";
    
} catch (Exception $e) {
    echo "💥 CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
