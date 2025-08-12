<?php
use Illuminate\Support\Facades\DB;

// Create test guest sessions
for($i = 0; $i < 15; $i++) {
    DB::table('sessions')->insert([
        'id' => 'guest_session_' . $i,
        'user_id' => null,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Browser',
        'payload' => 'test_payload',
        'last_activity' => time() - 1800 - ($i * 300), // Different ages
    ]);
}

echo 'Created 15 test guest sessions' . "\n";
echo 'Total sessions: ' . DB::table('sessions')->count() . "\n";
echo 'Guest sessions: ' . DB::table('sessions')->whereNull('user_id')->count() . "\n";

// Now test the cleanup
$stats = \App\Http\Controllers\Auth\LoginController::cleanupOrphanedSessions();
echo "Cleanup stats: " . json_encode($stats) . "\n";
echo 'Sessions after cleanup: ' . DB::table('sessions')->count() . "\n";
