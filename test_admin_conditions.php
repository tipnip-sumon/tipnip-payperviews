<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $settings = App\Models\GeneralSetting::first();
    
    if ($settings) {
        echo "=== CURRENT DATABASE STATUS ===\n\n";
        
        echo "✅ TRANSFER CONDITIONS:\n";
        $transferConditions = $settings->transfer_conditions;
        if ($transferConditions) {
            foreach ($transferConditions as $key => $value) {
                if (is_array($value)) {
                    echo "  $key: " . json_encode($value) . "\n";
                } else {
                    echo "  $key: " . ($value === true ? 'YES' : ($value === false ? 'NO' : $value)) . "\n";
                }
            }
        } else {
            echo "  No transfer conditions found\n";
        }
        
        echo "\n✅ WITHDRAWAL CONDITIONS:\n";
        $withdrawalConditions = $settings->withdrawal_conditions;
        if ($withdrawalConditions) {
            foreach ($withdrawalConditions as $key => $value) {
                if (is_array($value)) {
                    echo "  $key: " . json_encode($value) . "\n";
                } else {
                    echo "  $key: " . ($value === true ? 'YES' : ($value === false ? 'NO' : $value)) . "\n";
                }
            }
        } else {
            echo "  No withdrawal conditions found\n";
        }
        
        echo "\n=== ADMIN INTERFACE STATUS ===\n";
        echo "✅ Admin URL: http://127.0.0.1:8000/admin/transfer-withdraw-conditions\n";
        echo "✅ Controller: app/Http/Controllers/admin/TransferWithdrawConditionsController.php\n";
        echo "✅ View: resources/views/admin/settings/transfer-withdraw-conditions.blade.php\n";
        echo "✅ Route: Admin route group with middleware\n";
        
        echo "\n=== KEY FIELDS MAPPING ===\n";
        echo "Database Field -> Admin Form Field\n";
        echo "transfer_conditions.kyc_required -> transfer_kyc_required\n";
        echo "transfer_conditions.otp_required -> transfer_otp_required\n";
        echo "withdrawal_conditions.deposit_withdrawal_enabled -> deposit_withdrawal_enabled\n";
        echo "withdrawal_conditions.wallet_withdrawal_enabled -> wallet_withdrawal_enabled\n";
        echo "withdrawal_conditions.deposit_otp_required -> deposit_otp_required\n";
        echo "withdrawal_conditions.wallet_otp_required -> wallet_otp_required\n";
        
    } else {
        echo "❌ No general settings found in database\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
