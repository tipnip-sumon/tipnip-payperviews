<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\LotteryTicket;

$ticket = LotteryTicket::where('ticket_number', 'B7AE-3AC1-12D9-1A8C')->first();

if ($ticket) {
    echo "Ticket found:\n";
    echo "ID: " . $ticket->id . "\n";
    echo "Ticket Number: " . $ticket->ticket_number . "\n";
    echo "User ID: " . $ticket->user_id . "\n";
    echo "Status: " . $ticket->status . "\n";
    echo "Token Type: " . $ticket->token_type . "\n";
    echo "Is Valid Token: " . ($ticket->is_valid_token ? 'Yes' : 'No') . "\n";
    echo "Used as Token At: " . $ticket->used_as_token_at . "\n";
    echo "Token Expires At: " . $ticket->token_expires_at . "\n";
    echo "Lottery Draw ID: " . $ticket->lottery_draw_id . "\n";
    echo "Used for Plan ID: " . $ticket->used_for_plan_id . "\n";
    echo "Created At: " . $ticket->created_at . "\n";
    
    if ($ticket->lotteryDraw) {
        echo "Draw Status: " . $ticket->lotteryDraw->status . "\n";
        echo "Draw Date: " . $ticket->lotteryDraw->draw_date . "\n";
    }
    
    // Check why it might still be showing
    echo "\n=== Analysis ===\n";
    echo "Should be hidden because:\n";
    
    if ($ticket->used_as_token_at) {
        echo "- Used as token at: " . $ticket->used_as_token_at . "\n";
    }
    
    if ($ticket->token_expires_at && now()->gt($ticket->token_expires_at)) {
        echo "- Token expired at: " . $ticket->token_expires_at . "\n";
    }
    
    if (!$ticket->is_valid_token) {
        echo "- Token is not valid\n";
    }
    
    if ($ticket->status !== 'active') {
        echo "- Status is not active: " . $ticket->status . "\n";
    }
    
} else {
    echo "Ticket not found in lottery_tickets table\n";
}
