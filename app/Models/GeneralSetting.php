<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GeneralSetting extends Model
{
    use HasFactory;

    /** 
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'general_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_name',
        'cur_text',
        'cur_sym',
        'email_from',
        'email_template',
        'sms_body',
        'sms_from',
        'base_color',
        'secondary_color',
        'mail_config',
        'sms_config',
        'global_shortcodes',
        'kv',
        'ev',
        'en',
        'sv',
        'sn',
        'force_ssl',
        'maintenance_mode',
        'secure_password',
        'agree',
        'registration',
        'active_template',
        'system_info',
        'deposit_commission',
        'invest_commission',
        'invest_return_commission',
        'signup_bonus_amount',
        'signup_bonus_control',
        'promotional_tool',
        'firebase_config',
        'firebase_template',
        'push_notify',
        'off_day',
        'last_cron',
        'b_transfer',
        'f_charge',
        'p_charge',
        'holiday_withdraw',
        'language_switch',
        // Media and Logo Settings
        'logo',
        'favicon',
        'loader_image',
        'admin_logo',
        // Header Settings
        'header_content',
        'header_scripts',
        'header_background_color',
        'header_text_color',
        // Footer Settings
        'footer_content',
        'footer_scripts',
        'footer_background_color',
        'footer_text_color',
        'copyright_text',
        // SEO Settings
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_image',
        // Social Media
        'social_media_links',
        // Contact Information
        'contact_email',
        'contact_phone',
        'contact_address',
        // Content Areas
        'home_page_content',
        'about_us_content',
        'terms_conditions',
        'privacy_policy',
        // Custom CSS/JS
        'custom_css',
        'custom_js',
        // Settings JSON fields
        'notification_settings',
        'theme_settings',
        'widget_settings',
        // Maintenance
        'maintenance_message',
        'maintenance_image',
        // API Settings
        'api_settings',
        // Localization
        'timezone',
        'date_format',
        'time_format',
        // File Upload
        'file_upload_settings',
        // Security
        'security_settings',
        // Transfer and Withdrawal Conditions
        'transfer_conditions',
        'withdrawal_conditions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'kv' => 'boolean',
        'ev' => 'boolean',
        'en' => 'boolean',
        'sv' => 'boolean',
        'sn' => 'boolean',
        'force_ssl' => 'boolean',
        'maintenance_mode' => 'boolean',
        'secure_password' => 'boolean',
        'agree' => 'boolean',
        'registration' => 'boolean',
        'deposit_commission' => 'boolean',
        'invest_commission' => 'boolean',
        'invest_return_commission' => 'boolean',
        'signup_bonus_control' => 'boolean',
        'promotional_tool' => 'boolean',
        'push_notify' => 'boolean',
        'b_transfer' => 'boolean',
        'holiday_withdraw' => 'boolean',
        'language_switch' => 'boolean',
        'signup_bonus_amount' => 'decimal:2',
        'f_charge' => 'decimal:8',
        'p_charge' => 'decimal:2',
        'last_cron' => 'datetime',
        'mail_config' => 'array',
        'sms_config' => 'array',
        'global_shortcodes' => 'array',
        'firebase_config' => 'array',
        'firebase_template' => 'array',
        'off_day' => 'array',
        'system_info' => 'array',
        'header_scripts' => 'array',
        'footer_scripts' => 'array',
        'social_media_links' => 'array',
        'notification_settings' => 'array',
        'theme_settings' => 'array',
        'widget_settings' => 'array',
        'api_settings' => 'array',
        'file_upload_settings' => 'array',
        'security_settings' => 'array',
        'transfer_conditions' => 'array',
        'referral_benefits_settings' => 'array',
        'withdrawal_conditions' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when settings are updated
        static::saved(function () {
            Cache::forget('general_settings');
        });

        static::deleted(function () {
            Cache::forget('general_settings');
        });
    }

    /**
     * Get the general settings instance.
     */
    public static function getSettings()
    {
        return Cache::rememberForever('general_settings', function () {
            $settings = self::first();
            
            // If no settings exist, return a new instance with default values
            if (!$settings) {
                $settings = new self([
                    'site_name' => 'ViewCash',
                    'cur_text' => 'USD',
                    'cur_sym' => '$',
                    'email_from' => 'info@payperviews.net',
                    'base_color' => '#007bff',
                    'secondary_color' => '#6c757d',
                    'registration' => true,
                    'ev' => false,
                    'sv' => false,
                    'kv' => false,
                    'en' => true,
                    'sn' => false,
                    'force_ssl' => false,
                    'maintenance_mode' => false,
                    'secure_password' => false,
                    'agree' => false,
                    'deposit_commission' => true,
                    'invest_commission' => true,
                    'invest_return_commission' => true,
                    'signup_bonus_control' => false,
                    'promotional_tool' => false,
                    'push_notify' => false,
                    'b_transfer' => false,
                    'holiday_withdraw' => false,
                    'language_switch' => false,
                    'signup_bonus_amount' => 0,
                    'f_charge' => 0,
                    'p_charge' => 0,
                    'active_template' => 'default',
                ]);
            }
            
            return $settings;
        });
    }

    /**
     * Update or create general settings.
     */
    public static function updateOrCreateSetting(array $data)
    {
        $setting = self::first();
        
        if ($setting) {
            $setting->update($data);
        } else {
            $setting = self::create($data);
        }

        return $setting;
    }

    /**
     * Get a specific setting value.
     */
    public static function getSetting($key, $default = null)
    {
        $settings = self::getSettings();
        return $settings->$key ?? $default;
    }

    /**
     * Check if maintenance mode is enabled.
     */
    public static function isMaintenanceMode()
    {
        return self::getSetting('maintenance_mode', false);
    }

    /**
     * Check if registration is enabled.
     */
    public static function isRegistrationEnabled()
    {
        return self::getSetting('registration', false);
    }

    /**
     * Get currency symbol.
     */
    public static function getCurrencySymbol()
    {
        return self::getSetting('cur_sym', '$');
    }

    /**
     * Get currency text.
     */
    public static function getCurrencyText()
    {
        return self::getSetting('cur_text', 'USD');
    }

    /**
     * Get site name.
     */
    public static function getSiteName()
    {
        return self::getSetting('site_name', 'ViewCash');
    }

    /**
     * Update mail configuration and refresh config cache.
     */
    public static function updateMailConfig($mailConfig)
    {
        $settings = self::getSettings();
        $settings->mail_config = $mailConfig;
        $settings->save();
        
        // Clear cache to force refresh
        Cache::forget('general_settings');
        
        // Refresh mail configuration
        self::refreshMailConfiguration();
        
        return $settings;
    }

    /**
     * Refresh mail configuration.
     */
    public static function refreshMailConfiguration()
    {
        // Check if .env has MAIL_MAILER set to 'log' - if so, respect it
        if (env('MAIL_MAILER') === 'log') {
            config(['mail.default' => 'log']);
            return;
        }
        
        $settings = self::getSettings();
        
        if ($settings && isset($settings->mail_config) && is_array($settings->mail_config)) {
            $mailConfig = $settings->mail_config;
            // Decode mail configuration if it exists
            if (is_string($mailConfig)) {
                $mailConfig = json_decode($mailConfig, true) ?? [];
            }
            
            // Configure mail settings dynamically
            if (!empty($mailConfig['host']) && !empty($mailConfig['username'])) { 
                config([
                    'mail.default' => $mailConfig['driver'] ?? 'smtp',
                    'mail.mailers.smtp.transport' => 'smtp',
                    'mail.mailers.smtp.host' => $mailConfig['host'],
                    'mail.mailers.smtp.port' => $mailConfig['port'] ?? 25,
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
    }

    /**
     * Get mail configuration status.
     */
    public static function getMailConfigStatus()
    {
        $settings = self::getSettings();
        $mailConfig = $settings->mail_config ?? [];
        // Decode mail configuration if it exists
        if (is_string($mailConfig)) {
            $mailConfig = json_decode($mailConfig, true) ?? [];
        }
        
        return [
            'configured' => !empty($mailConfig['host']) && !empty($mailConfig['username']),
            'host' => !empty($mailConfig['host']),
            'username' => !empty($mailConfig['username']),
            'password' => !empty($mailConfig['password']),
            'encryption' => !empty($mailConfig['encryption']),
            'from_address' => !empty($mailConfig['from_address']) || !empty($settings->email_from),
            'from_name' => !empty($mailConfig['from_name']) || !empty($settings->site_name),
        ];
    }

    /**
     * Get logo URL.
     */
    public static function getLogo()
    {
        $logo = self::getSetting('logo');
        return $logo ? asset('storage/images/logos/' . $logo) : asset('assets/images/logo/payperviews-logo.svg');
    }

    /**
     * Get admin logo URL.
     */
    public static function getAdminLogo()
    {
        $logo = self::getSetting('admin_logo');
        return $logo ? asset('storage/images/logos/admin/' . $logo) : self::getLogo();
    }

    /**
     * Get favicon URL.
     */
    public static function getFavicon()
    {
        $favicon = self::getSetting('favicon');
        return $favicon ? asset('storage/images/favicons/' . $favicon) : asset('assets_custom/images/default-favicon.ico');
    }

    /**
     * Get meta image URL.
     */
    public static function getMetaImage()
    {
        $metaImage = self::getSetting('meta_image');
        return $metaImage ? asset('storage/images/meta/' . $metaImage) : self::getLogo();
    }

    /**
     * Get social media links.
     */
    public static function getSocialMediaLinks()
    {
        return self::getSetting('social_media_links', []);
    }

    /**
     * Get contact information.
     */
    public static function getContactInfo()
    {
        return [
            'email' => self::getSetting('contact_email', self::getSetting('email_from')),
            'phone' => self::getSetting('contact_phone'),
            'address' => self::getSetting('contact_address'),
        ];
    }

    /**
     * Get SEO meta data.
     */
    public static function getSeoMeta()
    {
        return [
            'title' => self::getSetting('meta_title', self::getSiteName()),
            'description' => self::getSetting('meta_description', 'ViewCash - Earn money by watching videos'),
            'keywords' => self::getSetting('meta_keywords', 'earn money, watch videos, online earning'),
            'image' => self::getMetaImage(),
        ];
    }

    /**
     * Get header configuration.
     */
    public static function getHeaderConfig()
    {
        return [
            'content' => self::getSetting('header_content'),
            'scripts' => self::getSetting('header_scripts', []),
            'background_color' => self::getSetting('header_background_color', '#ffffff'),
            'text_color' => self::getSetting('header_text_color', '#000000'),
        ];
    }

    /**
     * Get footer configuration.
     */
    public static function getFooterConfig()
    {
        return [
            'content' => self::getSetting('footer_content'),
            'scripts' => self::getSetting('footer_scripts', []),
            'background_color' => self::getSetting('footer_background_color', '#343a40'),
            'text_color' => self::getSetting('footer_text_color', '#ffffff'),
            'copyright' => self::getSetting('copyright_text', '© ' . date('Y') . ' ' . self::getSiteName() . '. All rights reserved.'),
        ];
    }

    /**
     * Get theme settings.
     */
    public static function getThemeSettings()
    {
        return self::getSetting('theme_settings', [
            'primary_color' => self::getSetting('base_color', '#007bff'),
            'secondary_color' => self::getSetting('secondary_color', '#6c757d'),
            'success_color' => '#28a745',
            'danger_color' => '#dc3545',
            'warning_color' => '#ffc107',
            'info_color' => '#17a2b8',
            'light_color' => '#f8f9fa',
            'dark_color' => '#343a40',
        ]);
    }

    /**
     * Get custom CSS.
     */
    public static function getCustomCss()
    {
        return self::getSetting('custom_css', '');
    }

    /**
     * Get custom JavaScript.
     */
    public static function getCustomJs()
    {
        return self::getSetting('custom_js', '');
    }

    /**
     * Get maintenance page data.
     */
    public static function getMaintenancePageData()
    {
        return [
            'enabled' => self::isMaintenanceMode(),
            'message' => self::getSetting('maintenance_message', 'We are currently performing maintenance. Please check back later.'),
            'image' => self::getSetting('maintenance_image') ? asset('storage/images/maintenance/' . self::getSetting('maintenance_image')) : null,
        ];
    }

    /**
     * Get notification settings.
     */
    public static function getNotificationSettings()
    {
        return self::getSetting('notification_settings', [
            'email_enabled' => true,
            'sms_enabled' => false,
            'push_enabled' => false,
            'browser_enabled' => true,
        ]);
    }

    /**
     * Get file upload settings.
     */
    public static function getFileUploadSettings()
    {
        return self::getSetting('file_upload_settings', [
            'max_file_size' => 5, // MB
            'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'allowed_document_types' => ['pdf', 'doc', 'docx', 'txt'],
            'image_quality' => 80,
            'create_thumbnails' => true,
            'thumbnail_size' => [150, 150],
        ]);
    }

    /**
     * Get security settings.
     */
    public static function getSecuritySettings()
    {
        return self::getSetting('security_settings', [
            'max_login_attempts' => 5,
            'lockout_duration' => 15, // minutes
            'password_min_length' => 8,
            'require_special_chars' => false,
            'session_timeout' => 120, // minutes
            'force_https' => false,
        ]);
    }

    /**
     * Get transfer conditions.
     */
    public static function getTransferConditions()
    {
        return self::getSetting('transfer_conditions', [
            'kyc_required' => true,
            'email_verification_required' => true,
            'profile_complete_required' => true,
            'referral_required' => true,
        ]);
    }

    /**
     * Get withdrawal conditions.
     */
    public static function getWithdrawalConditions()
    {
        return self::getSetting('withdrawal_conditions', [
            'kyc_required' => true,
            'email_verification_required' => true,
            'profile_complete_required' => true,
            'referral_required' => true,
        ]);
    }

    /**
     * Get logo storage path for a specific type.
     */
    public static function getLogoStoragePath($type = 'logo')
    {
        // Get path from configuration file
        $configPath = config("media.paths.{$type}");
        
        if ($configPath) {
            return $configPath;
        }
        
        // Fallback to default paths if not found in config
        $defaultPaths = [
            'logo' => 'images/logos',
            'admin_logo' => 'images/logos/admin',
            'favicon' => 'images/favicons',
            'meta_image' => 'images/meta',
            'maintenance_image' => 'images/maintenance',
        ];
        
        return $defaultPaths[$type] ?? 'images/logos';
    }

    /**
     * Get storage disk for media files.
     */
    public static function getMediaStorageDisk()
    {
        return config('media.disk', 'public');
    }

    /**
     * Get the URL for a media file.
     */
    public static function getMediaUrl($filename, $type = 'logo')
    {
        if (!$filename) {
            return asset('assets/images/brand-logos/desktop-logo.png'); // Default fallback
        }

        $storagePath = self::getLogoStoragePath($type);
        $storageDisk = self::getMediaStorageDisk();
        $baseUrl = config('media.url.base_url', '/storage');
        $cdnUrl = config('media.url.cdn_url');

        // Use CDN URL if configured
        if ($cdnUrl) {
            return rtrim($cdnUrl, '/') . '/' . $storagePath . '/' . $filename;
        }

        // Use local storage URL
        if ($storageDisk === 'public') {
            return rtrim($baseUrl, '/') . '/' . $storagePath . '/' . $filename;
        }

        // For other disks, try to generate URL
        try {
            return Storage::disk($storageDisk)->path($storagePath . '/' . $filename);
        } catch (\Exception $e) {
            return asset('assets/images/brand-logos/desktop-logo.png'); // Fallback on error
        }
    }

    /**
     * Update logo.
     */
    public static function updateLogo($logoFile, $type = 'logo')
    {
        if ($logoFile && $logoFile->isValid()) {
            try {
                // Get dynamic storage path and disk for this logo type
                $storagePath = self::getLogoStoragePath($type);
                $storageDisk = self::getMediaStorageDisk();
                
                // Generate filename
                $filename = time() . '_' . $type . '.' . $logoFile->getClientOriginalExtension();
                
                // Store new logo using Storage facade
                $path = Storage::disk($storageDisk)->putFileAs($storagePath, $logoFile, $filename);
                
                if ($path) {
                    // Get fresh settings instance from database (not cached)
                    $settings = self::first();
                    
                    // Delete old logo if exists (only after successful upload)
                    if ($settings && $settings->$type) {
                        Storage::disk($storageDisk)->delete($storagePath . '/' . $settings->$type); 
                    }
                    
                    // Update setting using direct database operation
                    if ($settings) {
                        $settings->$type = $filename;
                        $settings->save();
                    } else {
                        // Create new settings record if none exists
                        self::create([$type => $filename]);
                    }
                    
                    // Clear the cache to ensure fresh data next time
                    Cache::forget('general_settings');
                    
                    return $filename;
                } else {
                    throw new \Exception('Failed to store file');
                }
            } catch (\Exception $e) {
                Log::error("Failed to upload {$type}: " . $e->getMessage());
                return null;
            }
        }
        
        return null;
    }

    /**
     * Update meta image.
     */
    public static function updateMetaImage($imageFile)
    {
        if ($imageFile && $imageFile->isValid()) {
            try {
                // Get dynamic storage path and disk for meta images
                $storagePath = self::getLogoStoragePath('meta_image');
                $storageDisk = self::getMediaStorageDisk();
                
                // Generate filename
                $filename = time() . '_meta.' . $imageFile->getClientOriginalExtension();
                
                // Store new meta image using Storage facade
                $path = Storage::disk($storageDisk)->putFileAs($storagePath, $imageFile, $filename);
                
                if ($path) {
                    // Get fresh settings instance from database (not cached)
                    $settings = self::first();
                    
                    // Delete old meta image if exists (only after successful upload)
                    if ($settings && $settings->meta_image) {
                        Storage::disk($storageDisk)->delete($storagePath . '/' . $settings->meta_image);
                    }
                    
                    // Update setting using direct database operation
                    if ($settings) {
                        $settings->meta_image = $filename;
                        $settings->save();
                    } else {
                        // Create new settings record if none exists
                        self::create(['meta_image' => $filename]);
                    }
                    
                    // Clear the cache to ensure fresh data next time
                    Cache::forget('general_settings');
                    
                    return $filename;
                } else {
                    throw new \Exception('Failed to store meta image file');
                }
            } catch (\Exception $e) {
                Log::error("Failed to upload meta image: " . $e->getMessage());
                return null;
            }
        }
        
        return null;
    }

    /**
     * Update maintenance image.
     */
    public static function updateMaintenanceImage($imageFile)
    {
        if ($imageFile && $imageFile->isValid()) {
            try {
                // Get dynamic storage path and disk for maintenance images
                $storagePath = self::getLogoStoragePath('maintenance_image');
                $storageDisk = self::getMediaStorageDisk();
                
                // Generate filename
                $filename = time() . '_maintenance.' . $imageFile->getClientOriginalExtension();
                
                // Store new maintenance image using Storage facade
                $path = Storage::disk($storageDisk)->putFileAs($storagePath, $imageFile, $filename);
                
                if ($path) {
                    // Get fresh settings instance from database (not cached)
                    $settings = self::first();
                    
                    // Delete old maintenance image if exists (only after successful upload)
                    if ($settings && $settings->maintenance_image) {
                        Storage::disk($storageDisk)->delete($storagePath . '/' . $settings->maintenance_image);
                    }
                    
                    // Update setting using direct database operation
                    if ($settings) {
                        $settings->maintenance_image = $filename;
                        $settings->save();
                    } else {
                        // Create new settings record if none exists
                        self::create(['maintenance_image' => $filename]);
                    }
                    
                    // Clear the cache to ensure fresh data next time
                    Cache::forget('general_settings');
                    
                    return $filename;
                } else {
                    throw new \Exception('Failed to store maintenance image file');
                }
            } catch (\Exception $e) {
                Log::error("Failed to upload maintenance image: " . $e->getMessage());
                return null;
            }
        }
        
        return null;
    }

    /**
     * Get timezone list.
     */
    public static function getTimezoneList()
    {
        return [
            'UTC' => 'UTC',
            'America/New_York' => 'Eastern Time (US & Canada)',
            'America/Chicago' => 'Central Time (US & Canada)',
            'America/Denver' => 'Mountain Time (US & Canada)',
            'America/Los_Angeles' => 'Pacific Time (US & Canada)',
            'Europe/London' => 'London',
            'Europe/Paris' => 'Paris',
            'Europe/Berlin' => 'Berlin',
            'Asia/Tokyo' => 'Tokyo',
            'Asia/Shanghai' => 'Shanghai',
            'Asia/Kolkata' => 'India Standard Time',
            'Asia/Dubai' => 'Dubai',
            'Australia/Sydney' => 'Sydney',
        ];
    }
}
