<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING EMAIL SENDING ===\n";

// Test actual email sending
use Illuminate\Support\Facades\Mail;

try {
    echo "Testing email send with updated configuration...\n";
    
    $testEmailContent = "
    <h2>Email Configuration Test - FIXED</h2>
    <p>This is a test email to verify the email configuration is now working properly.</p>
    <p>Configuration used:</p>
    <ul>
        <li>Host: " . config('mail.mailers.smtp.host') . "</li>
        <li>Port: " . config('mail.mailers.smtp.port') . "</li>
        <li>Username: " . config('mail.mailers.smtp.username') . "</li>
        <li>From: " . config('mail.from.address') . "</li>
    </ul>
    <p>Sent at: " . now() . "</p>
    ";
    
    Mail::html($testEmailContent, function($message) {
        $message->to('sumonmti498@gmail.com')
                ->subject('Email Configuration Test - FIXED - ' . config('app.name'));
    });
    
    echo "✅ Test email sent successfully!\n";
    echo "Email should be delivered to sumonmti498@gmail.com\n";
    
    // Also test the ProfileController email method
    echo "\n=== TESTING PROFILE CONTROLLER EMAIL METHOD ===\n";
    
    $user = new stdClass();
    $user->name = 'Test User';
    $user->username = 'testuser';
    $user->email = 'sumonmti498@gmail.com';
    
    $testOtp = '123456';
    $newEmail = 'newemail@example.com';
    
    $subject = 'Email Change Security Verification - ' . config('app.name');
    $emailBody = "
    <h2>Email Change Security Verification</h2>
    <p>Hello {$user->name},</p>
    <p>You have requested to change your email address from <strong>{$user->email}</strong> to <strong>{$newEmail}</strong></p>
    <p>Your verification code is: <strong style='font-size: 24px; color: #dc3545;'>{$testOtp}</strong></p>
    <p>This code is valid for 10 minutes.</p>
    <p>Best regards,<br>" . config('app.name') . "</p>
    ";
    
    Mail::html($emailBody, function($message) use ($user, $subject) {
        $message->to($user->email)->subject($subject);
    });
    
    echo "✅ ProfileController-style email sent successfully!\n";
    echo "Both email types should now work properly.\n";
    
} catch (\Exception $e) {
    echo "❌ Email sending failed: " . $e->getMessage() . "\n";
    echo "Error details: " . $e->getTraceAsString() . "\n";
}
