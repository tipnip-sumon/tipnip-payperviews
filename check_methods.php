<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

use App\Models\WithdrawMethod;
use App\Models\Withdrawal;

echo "=== Withdrawal Methods ===\n";
$methods = WithdrawMethod::all();
foreach($methods as $method) {
    echo "ID: {$method->id}, Name: {$method->name}\n";
    echo "  Fixed Charge: {$method->fixed_charge}\n";
    echo "  Percent Charge: {$method->percent_charge}\n";
    echo "  Min Amount: {$method->min_amount}\n";
    echo "  Max Amount: {$method->max_amount}\n";
    echo "  Status: " . ($method->status ? 'Active' : 'Inactive') . "\n";
    echo "---\n";
}

echo "\n=== Recent Withdrawals ===\n";
$withdrawals = Withdrawal::latest()->limit(3)->get();
foreach($withdrawals as $withdrawal) {
    echo "ID: {$withdrawal->id}, TRX: {$withdrawal->trx}\n";
    echo "  Amount: {$withdrawal->amount}\n";
    echo "  Charge: {$withdrawal->charge}\n";
    echo "  Final Amount: {$withdrawal->final_amount}\n";
    echo "  Method ID: {$withdrawal->method_id}\n";
    echo "  Status: {$withdrawal->status}\n";
    echo "---\n";
}
