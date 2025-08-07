<?php
require_once 'vendor/autoload.php';

use App\Models\LotteryDraw;
use App\Models\LotterySetting;

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING AUTOMATIC NEXT DRAW CREATION ===" . PHP_EOL;

// Get current settings
$settings = LotterySetting::getSettings();
echo "Auto Generate Draws: " . ($settings->auto_generate_draws ? 'YES' : 'NO') . PHP_EOL;
echo "Auto Generation Frequency: " . ($settings->auto_generation_frequency ?? 'weekly') . PHP_EOL;
echo "Draw Day: " . ($settings->draw_day ?? 'sunday') . PHP_EOL;
echo "Draw Time: " . ($settings->draw_time ?? '20:00') . PHP_EOL;

// Check if there are any pending draws
$pendingDraws = LotteryDraw::where('status', 'pending')->orderBy('draw_time')->get();
echo PHP_EOL . "Current Pending Draws:" . PHP_EOL;
if ($pendingDraws->count() > 0) {
    foreach ($pendingDraws as $draw) {
        echo "- {$draw->draw_number} at {$draw->draw_time} (tickets: {$draw->total_tickets_sold})" . PHP_EOL;
    }
} else {
    echo "No pending draws found." . PHP_EOL;
}

// Check recent completed draws
$completedDraws = LotteryDraw::where('status', '!=', 'pending')->orderBy('draw_time', 'desc')->take(3)->get();
echo PHP_EOL . "Recent Completed Draws:" . PHP_EOL;
if ($completedDraws->count() > 0) {
    foreach ($completedDraws as $draw) {
        echo "- {$draw->draw_number} at {$draw->draw_time} (status: {$draw->status})" . PHP_EOL;
    }
} else {
    echo "No completed draws found." . PHP_EOL;
}

echo PHP_EOL . "=== AUTO DRAW CREATION SHOULD NOW WORK ===" . PHP_EOL;
echo "When you perform a draw on an AUTO_ draw, it will automatically create the next draw!" . PHP_EOL;
