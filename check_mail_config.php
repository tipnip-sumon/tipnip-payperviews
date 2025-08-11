<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING MAIL CONFIGURATION ===\n";

// Check .env file mail settings
echo "Environment MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
echo "Environment MAIL_HOST: " . env('MAIL_HOST') . "\n";
echo "Environment MAIL_USERNAME: " . env('MAIL_USERNAME') . "\n";

// Check database mail settings
try {
    $settings = App\Models\GeneralSetting::getSettings();
    echo "\nDatabase mail_config: ";
    var_dump($settings->mail_config);
    echo "Database email_from: " . $settings->email_from . "\n";
} catch (\Exception $e) {
    echo "Error getting database settings: " . $e->getMessage() . "\n";
}

// Check current Laravel mail configuration
echo "\nCurrent Laravel mail config:\n";
echo "Default mailer: " . config('mail.default') . "\n";
echo "SMTP host: " . config('mail.mailers.smtp.host') . "\n";
echo "SMTP username: " . config('mail.mailers.smtp.username') . "\n";
echo "From address: " . config('mail.from.address') . "\n";

// Call the refresh method manually
echo "\n=== CALLING REFRESH MAIL CONFIGURATION ===\n";
try {
    App\Models\GeneralSetting::refreshMailConfiguration();
    echo "Mail configuration refreshed\n";
    
    // Check again after refresh
    echo "After refresh - Default mailer: " . config('mail.default') . "\n";
    echo "After refresh - SMTP host: " . config('mail.mailers.smtp.host') . "\n";
    echo "After refresh - SMTP username: " . config('mail.mailers.smtp.username') . "\n";
    echo "After refresh - From address: " . config('mail.from.address') . "\n";
} catch (\Exception $e) {
    echo "Error refreshing mail config: " . $e->getMessage() . "\n";
}
