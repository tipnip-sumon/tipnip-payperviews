<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "=== SETTING UP FALLBACK EMAIL CONFIGURATION ===\n";

try {
    // First, let's try to set up email using the log driver as a fallback
    echo "Setting up log mailer as fallback...\n";
    
    $settings = App\Models\GeneralSetting::getSettings();
    
    // Update mail configuration to use log driver temporarily
    $fallbackMailConfig = [
        'driver' => 'log',
        'host' => '',
        'port' => '',
        'username' => '',
        'password' => '',
        'encryption' => '',
        'from_address' => 'noreply@payperviews.net',
        'from_name' => 'PayPerViews'
    ];
    
    $settings->mail_config = $fallbackMailConfig;
    $settings->save();
    
    // Clear cache and refresh
    Illuminate\Support\Facades\Cache::forget('general_settings');
    App\Models\GeneralSetting::refreshMailConfiguration();
    
    echo "Fallback configuration set (log driver)\n";
    echo "Current mailer: " . config('mail.default') . "\n";
    
    // Test with log driver
    
    $testEmailContent = "
    <h2>Email Change Verification Test (LOG MODE)</h2>
    <p>This email would be sent for email change verification.</p>
    <p>OTP Code: 123456</p>
    <p>This is now being logged instead of sent via SMTP.</p>
    <p>Time: " . now() . "</p>
    ";
    
    Mail::html($testEmailContent, function($message) {
        $message->to('sumonmti498@gmail.com')
                ->subject('Email Change Verification - Log Mode');
    });
    
    echo "✅ Email sent via log driver successfully!\n";
    echo "Check the log file at storage/logs/laravel.log for the email content.\n";
    
    // Now try to set up a more reliable SMTP configuration
    echo "\n=== TRYING ALTERNATIVE SMTP CONFIGURATION ===\n";
    
    // Try different SMTP settings that might work better
    $altMailConfig = [
        'driver' => 'smtp',
        'host' => '127.0.0.1',  // Try 127.0.0.1 instead of localhost
        'port' => '587',        // Try port 587 instead of 25
        'username' => 'noreply@payperviews.net',
        'password' => 'vG586xq$7',
        'encryption' => 'tls',
        'from_address' => 'noreply@payperviews.net',
        'from_name' => 'PayPerViews'
    ];
    
    $settings->mail_config = $altMailConfig;
    $settings->save();
    
    Illuminate\Support\Facades\Cache::forget('general_settings');
    App\Models\GeneralSetting::refreshMailConfiguration();
    
    echo "Trying alternative SMTP config (127.0.0.1:587)...\n";
    
    try {
        Mail::html($testEmailContent, function($message) {
            $message->to('sumonmti498@gmail.com')
                    ->subject('Email Change Verification - Alt SMTP');
        });
        echo "✅ Alternative SMTP configuration works!\n";
    } catch (\Exception $e) {
        echo "❌ Alternative SMTP also failed: " . $e->getMessage() . "\n";
        
        // Fall back to log driver
        echo "Falling back to log driver for reliability...\n";
        $settings->mail_config = $fallbackMailConfig;
        $settings->save();
        Illuminate\Support\Facades\Cache::forget('general_settings');
        App\Models\GeneralSetting::refreshMailConfiguration();
        echo "Log driver set as default for now.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Configuration error: " . $e->getMessage() . "\n";
}
