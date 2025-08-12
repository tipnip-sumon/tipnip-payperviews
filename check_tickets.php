<?php

// Check lottery tickets with active status
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== LOTTERY TICKETS STATUS CHECK ===" . PHP_EOL;
echo "Date: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

// Count tickets by status
echo "TICKET COUNT BY STATUS:" . PHP_EOL;
$statuses = ['active', 'expired', 'winner', 'claimed', 'refunded'];

foreach ($statuses as $status) {
    $count = DB::table('lottery_tickets')->where('status', $status)->count();
    echo "- {$status}: {$count}" . PHP_EOL;
}

// Total tickets
$totalTickets = DB::table('lottery_tickets')->count();
echo "- TOTAL: {$totalTickets}" . PHP_EOL . PHP_EOL;

// Active tickets details
echo "ACTIVE TICKETS DETAILS:" . PHP_EOL;
$activeTickets = DB::table('lottery_tickets')
    ->where('status', 'active')
    ->limit(10)
    ->get(['id', 'ticket_number', 'user_id', 'lottery_draw_id', 'status', 'purchased_at']);

if ($activeTickets->count() > 0) {
    foreach ($activeTickets as $ticket) {
        echo "ID: {$ticket->id} | Ticket: {$ticket->ticket_number} | User: {$ticket->user_id} | Draw: {$ticket->lottery_draw_id} | Purchased: {$ticket->purchased_at}" . PHP_EOL;
    }
} else {
    echo "No active tickets found." . PHP_EOL;
}

echo PHP_EOL . "=== END CHECK ===" . PHP_EOL;
