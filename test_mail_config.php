<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== EMAIL CONFIGURATION TEST ===\n";
echo "Mailer: " . config('mail.default') . "\n";
echo "Host: " . config('mail.mailers.smtp.host') . "\n";
echo "Port: " . config('mail.mailers.smtp.port') . "\n";
echo "Username: " . config('mail.mailers.smtp.username') . "\n";
echo "Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
echo "From Address: " . config('mail.from.address') . "\n";
echo "From Name: " . config('mail.from.name') . "\n";

// Test actual email sending
use Illuminate\Support\Facades\Mail;

try {
    echo "\n=== TESTING EMAIL SEND ===\n";
    
    $testEmailContent = "
    <h2>Test Email from ProfileController Fix</h2>
    <p>This is a test email to verify the email configuration is working.</p>
    <p>Sent at: " . now() . "</p>
    ";
    
    Mail::html($testEmailContent, function($message) {
        $message->to('sumonmti498@gmail.com')
                ->subject('Email Configuration Test - ' . config('app.name'));
    });
    
    echo "✅ Test email sent successfully!\n";
    
} catch (\Exception $e) {
    echo "❌ Email sending failed: " . $e->getMessage() . "\n";
    echo "Error details: " . $e->getTraceAsString() . "\n";
}
