<?php

use Illuminate\Support\Facades\Mail;

// Test email sending
try {
    Mail::raw('This is a test email from PayPerViews to verify email configuration is working.', function ($message) {
        $message->to('test@example.com')
               ->subject('Test Email from PayPerViews');
    });
    
    echo "✅ Email sent successfully!\n";
    echo "📧 Email configuration is working properly.\n";
    
} catch (\Exception $e) {
    echo "❌ Email sending failed: " . $e->getMessage() . "\n";
    echo "🔧 Please check your email configuration in .env file\n";
}
