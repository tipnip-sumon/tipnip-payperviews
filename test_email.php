<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

try {
    echo "Testing email configuration...\n";
    echo "\nEnvironment variables:\n";
    echo "- MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
    echo "- MAIL_HOST: " . env('MAIL_HOST') . "\n";
    echo "- MAIL_PORT: " . env('MAIL_PORT') . "\n";
    echo "- MAIL_USERNAME: " . env('MAIL_USERNAME') . "\n";
    echo "- MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION') . "\n";
    echo "- MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
    
    echo "\nActual config values:\n";
    echo "- default: " . config('mail.default') . "\n";
    echo "- host: " . config('mail.mailers.smtp.host') . "\n";
    echo "- port: " . config('mail.mailers.smtp.port') . "\n";
    echo "- username: " . config('mail.mailers.smtp.username') . "\n";
    echo "- encryption: " . config('mail.mailers.smtp.encryption') . "\n";
    echo "- from.address: " . config('mail.from.address') . "\n";
    
    // Test basic email send
    Mail::raw('This is a test email from Laravel PayPerViews system. If you receive this, the email configuration is working correctly.', function($message) {
        $message->to('sumonmti498@gmail.com')
                ->subject('Email Configuration Test - PayPerViews')
                ->from(config('mail.from.address'), config('mail.from.name'));
    });
    
    echo "✅ Email sent successfully!\n";
    
} catch (\Exception $e) {
    echo "❌ Email sending failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
