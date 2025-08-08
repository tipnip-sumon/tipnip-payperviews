<?php
// Debug script to check withdrawal conditions
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Include the condition helper
require_once app_path('helpers/ConditionHelper.php');

echo "=== Withdrawal Conditions Debug ===\n\n";

// Get current user (you'll need to modify this with a specific user ID)
echo "Enter a user ID to check conditions for: ";
$userId = trim(fgets(STDIN));

if (empty($userId) || !is_numeric($userId)) {
    echo "Invalid user ID\n";
    exit(1);
}

$user = User::find($userId);
if (!$user) {
    echo "User not found\n";
    exit(1);
}

echo "Checking conditions for user: {$user->username} (ID: {$user->id})\n\n";

// Check withdrawal conditions
$conditionCheck = checkWithdrawalConditions($user);

echo "=== CONDITION CHECK RESULTS ===\n";
echo "Allowed: " . ($conditionCheck['allowed'] ? 'YES' : 'NO') . "\n";
echo "Failures: " . json_encode($conditionCheck['failures'], JSON_PRETTY_PRINT) . "\n";
echo "Requirements: " . json_encode($conditionCheck['requirements'], JSON_PRETTY_PRINT) . "\n";
echo "Conditions: " . json_encode($conditionCheck['conditions'], JSON_PRETTY_PRINT) . "\n";

echo "\n=== USER PROFILE COMPLETION ===\n";
$profileComplete = checkProfileCompletion($user);
echo "Profile Complete: " . ($profileComplete ? 'YES' : 'NO') . "\n";

$requiredFields = ['firstname', 'lastname', 'mobile', 'country', 'address'];
foreach ($requiredFields as $field) {
    $value = $user->$field ?? 'NULL';
    echo "{$field}: " . (empty($value) ? '[EMPTY]' : $value) . "\n";
}

echo "\n=== USER VERIFICATION STATUS ===\n";
echo "KYC Verified (kv): " . ($user->kv == 1 ? 'YES' : 'NO') . "\n";
echo "Email Verified: " . ($user->email_verified_at ? 'YES' : 'NO') . "\n";

echo "\nDone.\n";
