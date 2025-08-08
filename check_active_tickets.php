<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\LotteryTicket;

echo "=== Current User 3 Ticket Status ===\n";

// Total tickets
$totalTickets = LotteryTicket::where('user_id', 3)->count();
echo "Total tickets: " . $totalTickets . "\n";

// Active tickets (old way - incorrect)
$activeTicketsOld = LotteryTicket::where('user_id', 3)
    ->whereHas('lotteryDraw', function($q) {
        $q->where('status', 'pending');
    })->count();
echo "Active tickets (old calculation): " . $activeTicketsOld . "\n";

// Active tickets (new way - correct)
$activeTicketsNew = LotteryTicket::where('user_id', 3)
    ->whereHas('lotteryDraw', function($q) {
        $q->where('status', 'pending');
    })
    ->where('status', '!=', 'expired')
    ->whereNull('used_as_token_at')
    ->count();
echo "Active tickets (new calculation): " . $activeTicketsNew . "\n";

echo "\n=== Breakdown ===\n";

// Used as token
$usedAsToken = LotteryTicket::where('user_id', 3)
    ->whereNotNull('used_as_token_at')
    ->count();
echo "Used as token: " . $usedAsToken . "\n";

// Expired status
$expiredStatus = LotteryTicket::where('user_id', 3)
    ->where('status', 'expired')
    ->count();
echo "Expired status: " . $expiredStatus . "\n";

// Pending draw tickets
$pendingDraw = LotteryTicket::where('user_id', 3)
    ->whereHas('lotteryDraw', function($q) {
        $q->where('status', 'pending');
    })->count();
echo "Tickets in pending draws: " . $pendingDraw . "\n";

echo "\n=== Individual Tickets ===\n";
$tickets = LotteryTicket::where('user_id', 3)->with('lotteryDraw')->get();
foreach ($tickets as $ticket) {
    echo "Ticket {$ticket->ticket_number}: status={$ticket->status}, draw_status={$ticket->lotteryDraw->status}, used_as_token=" . ($ticket->used_as_token_at ? 'YES' : 'NO') . "\n";
}
