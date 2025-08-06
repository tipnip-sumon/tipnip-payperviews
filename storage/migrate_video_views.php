<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\DailyVideoViewService;
use App\Models\VideoView;
use Illuminate\Support\Facades\DB;

echo "=== VIDEO VIEW SYSTEM MIGRATION TOOL ===\n\n";

$videoViewService = new DailyVideoViewService();

// Check existing data
$oldRecordsCount = VideoView::whereNull('view_type')
    ->orWhere('view_type', '!=', 'daily_summary')
    ->count();

$optimizedRecordsCount = VideoView::where('view_type', 'daily_summary')->count();

echo "Current system status:\n";
echo "- Old format records: " . $oldRecordsCount . "\n";
echo "- Optimized records: " . $optimizedRecordsCount . "\n\n";

if ($oldRecordsCount === 0) {
    echo "✅ No old records to migrate. System is already optimized!\n";
    exit(0);
}

echo "Starting migration of old video view records...\n";

$batchSize = 500;
$totalMigrated = 0;
$totalErrors = 0;

try {
    while (true) {
        echo "Processing batch of {$batchSize} records...\n";
        
        $result = $videoViewService->migrateExistingVideoViews($batchSize);
        
        if (!$result['success']) {
            echo "❌ Migration error: " . $result['error'] . "\n";
            break;
        }
        
        $totalMigrated += $result['migrated'];
        $totalErrors += $result['errors'];
        
        echo "✅ Migrated: " . $result['migrated'] . " records\n";
        if ($result['errors'] > 0) {
            echo "⚠️ Errors: " . $result['errors'] . "\n";
        }
        
        // Check if there are more records to process
        if (!$result['has_more']) {
            echo "✅ All records processed!\n";
            break;
        }
        
        // Brief pause to prevent overwhelming the database
        usleep(100000); // 0.1 second
    }
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== MIGRATION COMPLETE ===\n";
echo "Total records migrated: " . $totalMigrated . "\n";
echo "Total errors: " . $totalErrors . "\n";

// Verify the migration results
$finalOldCount = VideoView::where('view_type', '!=', 'daily_summary')
    ->whereNull('view_type', 'or')
    ->count();
    
$finalOptimizedCount = VideoView::where('view_type', 'daily_summary')->count();
$migratedOldCount = VideoView::where('view_type', 'migrated_old')->count();

echo "\nFinal system status:\n";
echo "- Unmigrated old records: " . $finalOldCount . "\n";
echo "- Migrated old records: " . $migratedOldCount . "\n";
echo "- Optimized records: " . $finalOptimizedCount . "\n";

// Calculate efficiency gains
if ($totalMigrated > 0) {
    $spaceReduction = (($totalMigrated - $finalOptimizedCount) / $totalMigrated) * 100;
    echo "\nEfficiency gains:\n";
    echo "- Space reduction: " . round($spaceReduction, 1) . "%\n";
    echo "- Records reduced from " . $totalMigrated . " to " . $finalOptimizedCount . "\n";
}

echo "\n✅ Migration completed successfully!\n";
echo "Your video view system is now optimized for single-row storage.\n";
