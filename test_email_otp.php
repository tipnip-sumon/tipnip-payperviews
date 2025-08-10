<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== EMAIL OTP SYSTEM TEST ===\n\n";

try {
    echo "📧 Testing Email Configuration...\n";
    
    // Test mail configuration
    $mailConfig = config('mail');
    echo "Mail Driver: " . $mailConfig['default'] . "\n";
    echo "SMTP Host: " . $mailConfig['mailers']['smtp']['host'] . "\n";
    echo "SMTP Port: " . $mailConfig['mailers']['smtp']['port'] . "\n";
    echo "From Address: " . $mailConfig['from']['address'] . "\n";
    echo "From Name: " . $mailConfig['from']['name'] . "\n\n";
    
    // Generate test OTP
    $testOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    echo "🔢 Generated Test OTP: $testOtp\n\n";
    
    // Test email address
    $testEmail = 'sumonmti498@gmail.com'; // Using the email from your config
    
    echo "📤 Sending test OTP email to: $testEmail\n";
    
    // Create simple HTML email content
    $emailContent = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Test OTP - PayPerViews</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background: #f4f4f4; }
            .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            .header { text-align: center; margin-bottom: 30px; }
            .logo { font-size: 24px; font-weight: bold; color: #007bff; }
            .otp-code { background: #007bff; color: white; font-size: 28px; font-weight: bold; padding: 15px 30px; border-radius: 8px; text-align: center; margin: 20px 0; letter-spacing: 3px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='logo'>PayPerViews</div>
                <h2>Withdrawal OTP Test</h2>
            </div>
            
            <p>Hello,</p>
            
            <p>This is a test email for the OTP system. Your verification code is:</p>
            
            <div class='otp-code'>$testOtp</div>
            
            <p>This is a test message sent at: " . now()->format('Y-m-d H:i:s') . "</p>
        </div>
    </body>
    </html>
    ";
    
    // Try to send the email
    $emailSent = false;
    try {
        Mail::html($emailContent, function($message) use ($testEmail) {
            $message->to($testEmail)
                    ->subject('Test OTP - PayPerViews Withdrawal System');
        });
        $emailSent = true;
        echo "✅ Email sent successfully!\n";
    } catch (\Exception $e) {
        echo "❌ Email sending failed: " . $e->getMessage() . "\n";
        echo "📋 Error details: " . $e->getFile() . ':' . $e->getLine() . "\n";
    }
    
    echo "\n=== TEST RESULTS ===\n";
    echo "Email Configuration: " . ($mailConfig['default'] === 'smtp' ? '✅ SMTP' : '❌ Not SMTP') . "\n";
    echo "Email Sending: " . ($emailSent ? '✅ Success' : '❌ Failed') . "\n";
    
    if ($emailSent) {
        echo "\n🎉 EMAIL SYSTEM IS WORKING!\n";
        echo "📧 Check your Mailtrap inbox at: https://mailtrap.io/inboxes\n";
        echo "🔑 Test OTP Code: $testOtp\n";
    } else {
        echo "\n❌ EMAIL SYSTEM NEEDS FIXING\n";
        echo "💡 Check your Mailtrap credentials and Laravel mail configuration\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "📋 Error in: " . $e->getFile() . ':' . $e->getLine() . "\n";
}

echo "\n=== END TEST ===\n";
