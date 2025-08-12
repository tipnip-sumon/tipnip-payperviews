<?php

// Check lottery draws and their tickets
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== LOTTERY DRAWS AND ACTIVE TICKETS ===" . PHP_EOL;
echo "Date: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

// Get all draws with active tickets
echo "DRAWS WITH ACTIVE TICKETS:" . PHP_EOL;
$drawsWithActiveTickets = DB::table('lottery_draws as ld')
    ->join('lottery_tickets as lt', 'ld.id', '=', 'lt.lottery_draw_id')
    ->where('lt.status', 'active')
    ->select('ld.id', 'ld.status as draw_status', 'ld.draw_date', DB::raw('COUNT(lt.id) as active_tickets_count'))
    ->groupBy('ld.id', 'ld.status', 'ld.draw_date')
    ->orderBy('ld.id')
    ->get();

foreach ($drawsWithActiveTickets as $draw) {
    echo "Draw ID: {$draw->id} | Status: {$draw->draw_status} | Date: {$draw->draw_date} | Active Tickets: {$draw->active_tickets_count}" . PHP_EOL;
}

echo PHP_EOL . "TOTAL ACTIVE TICKETS: " . DB::table('lottery_tickets')->where('status', 'active')->count() . PHP_EOL;

// Check current pending/active draws
echo PHP_EOL . "ALL LOTTERY DRAWS:" . PHP_EOL;
$allDraws = DB::table('lottery_draws')
    ->select('id', 'status', 'draw_date', 'total_tickets_sold')
    ->orderBy('id')
    ->get();

foreach ($allDraws as $draw) {
    $activeTicketsForDraw = DB::table('lottery_tickets')->where('lottery_draw_id', $draw->id)->where('status', 'active')->count();
    echo "Draw ID: {$draw->id} | Status: {$draw->status} | Date: {$draw->draw_date} | Total Sold: {$draw->total_tickets_sold} | Active: {$activeTicketsForDraw}" . PHP_EOL;
}

echo PHP_EOL . "=== END CHECK ===" . PHP_EOL;
