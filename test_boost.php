<?php

// Test active tickets boost update
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING ACTIVE TICKETS BOOST UPDATE ===" . PHP_EOL;
echo "Date: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

// Get current settings
$settings = \App\Models\LotterySetting::getSettings();
echo "Current active_tickets_boost: " . ($settings->active_tickets_boost ?? 'NOT SET') . PHP_EOL;

// Get real and total counts
$realCount = \App\Models\LotterySetting::getRealActiveTicketsCount();
$totalCount = \App\Models\LotterySetting::getTotalActiveTicketsCount();

echo "Real active tickets: " . $realCount . PHP_EOL;
echo "Total displayed tickets: " . $totalCount . PHP_EOL;
echo "Current boost: " . ($totalCount - $realCount) . PHP_EOL . PHP_EOL;

// Test setting boost to 100
echo "Setting boost to 100..." . PHP_EOL;
\App\Models\LotterySetting::updateSettings(['active_tickets_boost' => 100]);

// Check if it was saved
$updatedSettings = \App\Models\LotterySetting::getSettings();
echo "New active_tickets_boost: " . ($updatedSettings->active_tickets_boost ?? 'NOT SET') . PHP_EOL;

// Get new counts
$newTotalCount = \App\Models\LotterySetting::getTotalActiveTicketsCount();
echo "New total displayed tickets: " . $newTotalCount . PHP_EOL;
echo "New boost: " . ($newTotalCount - $realCount) . PHP_EOL . PHP_EOL;

echo "=== TEST COMPLETE ===" . PHP_EOL;
