<?php

namespace App\Providers;

use App\Models\GeneralSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            // Only configure mail if the general_settings table exists
            if (Schema::hasTable('general_settings')) {
                $this->configureMailSettings();
            }
        } catch (\Exception $e) {
            // Silently handle database connection errors during boot
            Log::info('MailConfigServiceProvider: Database not available during boot', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Configure mail settings from database.
     */
    private function configureMailSettings(): void
    {
        try {
            $settings = GeneralSetting::getSettings();
            
            if ($settings && isset($settings->mail_config) && is_array($settings->mail_config)) {
                $mailConfig = $settings->mail_config ?? [];
                // Decode mail configuration if it exists
                if (is_string($mailConfig)) {
                    $mailConfig = json_decode($mailConfig, true) ?? [];
                }
                
                // Configure mail settings dynamically
                if (!empty($mailConfig['host']) && !empty($mailConfig['username'])) {
                    Config::set([
                        'mail.default' => $mailConfig['driver'] ?? 'smtp',
                        'mail.mailers.smtp.transport' => 'smtp',
                        'mail.mailers.smtp.host' => $mailConfig['host'],
                        'mail.mailers.smtp.port' => $mailConfig['port'] ?? 587,
                        'mail.mailers.smtp.encryption' => $mailConfig['encryption'] ?? 'tls',
                        'mail.mailers.smtp.username' => $mailConfig['username'],
                        'mail.mailers.smtp.password' => $mailConfig['password'],
                        'mail.mailers.smtp.timeout' => 60,
                        'mail.mailers.smtp.auth_mode' => 'login',
                        'mail.from.address' => $mailConfig['from_address'] ?? $settings->email_from ?? 'noreply@example.com',
                        'mail.from.name' => $mailConfig['from_name'] ?? $settings->site_name ?? 'ViewCash',
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't break the application
            Log::info('MailConfigServiceProvider: Failed to configure mail settings', ['error' => $e->getMessage()]);
        }
    }
}
