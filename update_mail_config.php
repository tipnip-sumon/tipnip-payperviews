<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== UPDATING DATABASE MAIL CONFIGURATION ===\n";

try {
    // Get the settings
    $settings = App\Models\GeneralSetting::getSettings();
    
    // Update mail configuration to match .env file
    $newMailConfig = [
        'driver' => 'smtp',
        'host' => 'localhost',
        'port' => '25',
        'username' => 'noreply@payperviews.net',
        'password' => 'vG586xq$7',
        'encryption' => 'tls',
        'from_address' => 'noreply@payperviews.net',
        'from_name' => 'PayPerViews'
    ];
    
    echo "Old mail config: ";
    var_dump($settings->mail_config);
    
    // Update the database
    $settings->mail_config = $newMailConfig;
    $settings->email_from = 'noreply@payperviews.net';
    $settings->save();
    
    echo "\nNew mail config saved to database\n";
    echo "New mail config: ";
    var_dump($newMailConfig);
    
    // Clear cache and refresh configuration
    Illuminate\Support\Facades\Cache::forget('general_settings');
    App\Models\GeneralSetting::refreshMailConfiguration();
    
    echo "\nConfiguration refreshed!\n";
    echo "Current Laravel config after update:\n";
    echo "Default mailer: " . config('mail.default') . "\n";
    echo "SMTP host: " . config('mail.mailers.smtp.host') . "\n";
    echo "SMTP username: " . config('mail.mailers.smtp.username') . "\n";
    echo "From address: " . config('mail.from.address') . "\n";
    
} catch (\Exception $e) {
    echo "Error updating mail config: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
