<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'KYC Pending Reminder',
                'slug' => 'kyc_reminder',
                'subject' => 'Complete Your KYC Verification - {{user_name}}',
                'content' => $this->getKycReminderTemplate(),
                'variables' => ['{{user_name}}', '{{company_name}}', '{{kyc_url}}', '{{support_email}}']
            ],
            [
                'name' => 'Inactive User Reminder',
                'slug' => 'inactive_user',
                'subject' => 'We Miss You - Start Investing Today!',
                'content' => $this->getInactiveUserTemplate(),
                'variables' => ['{{user_name}}', '{{company_name}}', '{{balance}}', '{{invest_url}}']
            ],
            [
                'name' => 'Password Reset Reminder',
                'slug' => 'password_reset',
                'subject' => 'Security Reminder: Update Your Password',
                'content' => $this->getPasswordResetTemplate(),
                'variables' => ['{{user_name}}', '{{company_name}}', '{{reset_url}}', '{{days_since_change}}']
            ],
            [
                'name' => 'Investment Congratulations',
                'slug' => 'congratulations',
                'subject' => 'Congratulations on Your First Investment!',
                'content' => $this->getCongratulationsTemplate(),
                'variables' => ['{{user_name}}', '{{investment_amount}}', '{{plan_name}}', '{{dashboard_url}}']
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }

    private function getKycReminderTemplate()
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Verification Required</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 300;">KYC Verification Required</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Complete your identity verification</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #333; margin: 0 0 20px 0; font-size: 24px;">Hi {{user_name}},</h2>
            <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                We noticed that your KYC verification is still pending. To unlock all features and ensure the security of your account, please complete your identity verification.
            </p>
            
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <strong style="color: #856404;">⚠️ Action Required:</strong>
                <p style="margin: 5px 0 0 0; color: #856404;">Your account access may be limited until verification is completed.</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{kyc_url}}" style="background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                    Complete KYC Verification
                </a>
            </div>
            
            <p style="color: #999; font-size: 14px; line-height: 1.6;">
                Need help? Contact our support team at {{support_email}} or visit our FAQ section for more information about the verification process.
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="margin: 0; color: #6c757d; font-size: 12px;">
                © {{current_year}} {{company_name}}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>';
    }

    private function getInactiveUserTemplate()
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We Miss You!</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 300;">We Miss You!</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Come back and continue your investment journey</p>
        </div>
        <div style="padding: 40px 30px;">
            <h2 style="color: #333; margin: 0 0 20px 0; font-size: 24px;">Hi {{user_name}},</h2>
            <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                We noticed you haven\'t been active lately. You have {{balance}} in your account ready to be invested in profitable opportunities.
            </p>
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{invest_url}}" style="background: #f5576c; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                    Start Investing Now
                </a>
            </div>
        </div>
        <div style="background: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="margin: 0; color: #6c757d; font-size: 12px;">
                © {{current_year}} {{company_name}}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>';
    }

    private function getPasswordResetTemplate()
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Reminder</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 300;">🔒 Security Reminder</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Time to update your password</p>
        </div>
        <div style="padding: 40px 30px;">
            <h2 style="color: #333; margin: 0 0 20px 0; font-size: 24px;">Hi {{user_name}},</h2>
            <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                For security purposes, we recommend updating your password. It\'s been over {{days_since_change}} days since your last password change.
            </p>
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{reset_url}}" style="background: #00f2fe; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                    Update Password
                </a>
            </div>
        </div>
        <div style="background: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="margin: 0; color: #6c757d; font-size: 12px;">
                © {{current_year}} {{company_name}}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>';
    }

    private function getCongratulationsTemplate()
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Congratulations!</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 300;">🎉 Congratulations!</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.8;">Welcome to your investment journey</p>
        </div>
        <div style="padding: 40px 30px;">
            <h2 style="color: #333; margin: 0 0 20px 0; font-size: 24px;">Hi {{user_name}},</h2>
            <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                Congratulations on your first investment of {{investment_amount}} in the {{plan_name}} plan! You\'ve taken a great step towards building your financial future.
            </p>
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{dashboard_url}}" style="background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                    View My Investments
                </a>
            </div>
        </div>
        <div style="background: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="margin: 0; color: #6c757d; font-size: 12px;">
                © {{current_year}} {{company_name}}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>';
    }
}
