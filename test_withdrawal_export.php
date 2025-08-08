<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Log;

try {
    echo "Testing withdrawal export...\n";
    
    $withdrawals = Withdrawal::with(['user', 'withdrawMethod'])->get();
    echo "Found {$withdrawals->count()} withdrawals\n";
    
    if ($withdrawals->count() > 0) {
        $withdrawal = $withdrawals->first();
        echo "Sample withdrawal ID: {$withdrawal->id}\n";
        echo "User: " . ($withdrawal->user ? $withdrawal->user->username : 'No user') . "\n";
        echo "Method: " . ($withdrawal->withdrawMethod ? $withdrawal->withdrawMethod->name : 'No method') . "\n";
        echo "Raw withdraw_information: " . $withdrawal->getRawOriginal('withdraw_information') . "\n";
        echo "Casted withdraw_information type: " . gettype($withdrawal->withdraw_information) . "\n";
        
        // Test the parsing logic from export function
        $withdrawInfo = null;
        if ($withdrawal->withdraw_information) {
            if (is_string($withdrawal->withdraw_information)) {
                echo "Parsing as string...\n";
                $withdrawInfo = json_decode($withdrawal->withdraw_information, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    echo "JSON decode error: " . json_last_error_msg() . "\n";
                }
            } else {
                echo "Already parsed as: " . gettype($withdrawal->withdraw_information) . "\n";
                $withdrawInfo = $withdrawal->withdraw_information;
            }
        }
        
        echo "Parsed withdrawInfo type: " . gettype($withdrawInfo) . "\n";
        if (is_array($withdrawInfo)) {
            echo "Keys: " . implode(', ', array_keys($withdrawInfo)) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
