<?php

namespace App\Http\Controllers\admin;

use Exception;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GeneralSettingController extends Controller
{
    /**
     * Display the general settings page.
     */
    public function index()
    {
        try {
            $settings = GeneralSetting::getSettings();
            
            // If no settings exist, create default ones
            if (!$settings || !$settings->exists) {
                $settings = GeneralSetting::create([
                    'site_name' => 'ViewCash',
                    'cur_text' => 'USD',
                    'cur_sym' => '$',
                    'email_from' => 'admin@viewcash.com',
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
            $pageTitle = 'General Settings';
            
            return view('admin.settings.general', compact('settings', 'pageTitle'))
                ->with('pageTitle', 'General Settings');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load settings: ' . $e->getMessage());
        }
    }

    /**
     * Update general settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:40',
            'cur_text' => 'required|string|max:40',
            'cur_sym' => 'required|string|max:10',
            'email_from' => 'required|email|max:40',
            'base_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'signup_bonus_amount' => 'nullable|numeric|min:0',
            'f_charge' => 'nullable|numeric|min:0',
            'p_charge' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->only([
                'site_name',
                'cur_text',
                'cur_sym',
                'email_from',
                'email_template',
                'sms_body',
                'sms_from',
                'base_color',
                'secondary_color',
                'signup_bonus_amount',
                'f_charge',
                'p_charge',
                'active_template',
            ]);

            // Handle boolean fields
            $booleanFields = [
                'kv', 'ev', 'en', 'sv', 'sn', 'force_ssl', 'maintenance_mode',
                'secure_password', 'agree', 'registration', 'deposit_commission',
                'invest_commission', 'invest_return_commission', 'signup_bonus_control',
                'promotional_tool', 'push_notify', 'b_transfer', 'holiday_withdraw',
                'language_switch'
            ];

            foreach ($booleanFields as $field) {
                $data[$field] = $request->has($field) ? 1 : 0;
            }

            // Handle JSON fields
            if ($request->has('mail_config')) {
                $data['mail_config'] = json_encode($request->mail_config);
            }

            if ($request->has('sms_config')) {
                $data['sms_config'] = json_encode($request->sms_config);
            }

            if ($request->has('firebase_config')) {
                $data['firebase_config'] = json_encode($request->firebase_config);
            }

            if ($request->has('off_day')) {
                $data['off_day'] = json_encode($request->off_day);
            }

            GeneralSetting::updateOrCreateSetting($data);

            return back()->with('success', 'General settings updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating settings: ' . $e->getMessage());
        }
    }

    /**
     * Update mail configuration.
     */
    public function updateMailConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mail_driver' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'nullable|string',
            'from_address' => 'required|email',
            'from_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $mailConfig = [
                'driver' => $request->mail_driver,
                'host' => $request->mail_host,
                'port' => $request->mail_port,
                'username' => $request->mail_username,
                'password' => $request->mail_password,
                'encryption' => $request->mail_encryption,
                'from_address' => $request->from_address,
                'from_name' => $request->from_name,
            ];

            GeneralSetting::updateMailConfig($mailConfig); 
            
            // Also update email_from in general settings if provided
            if ($request->from_address) {
                GeneralSetting::updateOrCreateSetting([
                    'email_from' => $request->from_address,
                ]);
            }

            return back()->with('success', 'Mail configuration updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating mail configuration: ' . $e->getMessage());
        }
    }

    /**
     * Update SMS configuration.
     */
    public function updateSmsConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sms_gateway' => 'required|string',
            'sms_api_key' => 'required|string',
            'sms_sender_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $smsConfig = [
                'gateway' => $request->sms_gateway,
                'api_key' => $request->sms_api_key,
                'sender_id' => $request->sms_sender_id,
            ];

            GeneralSetting::updateOrCreateSetting([
                'sms_config' => json_encode($smsConfig)
            ]);

            return back()->with('success', 'SMS configuration updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating SMS configuration: ' . $e->getMessage());
        }
    }

    /**
     * Clear cache.
     */
    public function clearCache()
    {
        try {
            Cache::flush();
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return back()->with('success', 'Cache cleared successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Toggle maintenance mode.
     */
    public function toggleMaintenanceMode()
    {
        try {
            $settings = GeneralSetting::getSettings();
            $newMode = !$settings->maintenance_mode;
            
            GeneralSetting::updateOrCreateSetting(['maintenance_mode' => $newMode]);
            
            $message = $newMode ? 'Maintenance mode enabled!' : 'Maintenance mode disabled!';
            return back()->with('success', $message);
        } catch (Exception $e) {
            return back()->with('error', 'Failed to toggle maintenance mode: ' . $e->getMessage());
        }
    }

    /**
     * Display mail configuration page.
     */
    public function mailConfig()
    {
        try {
            $settings = GeneralSetting::getSettings();
            // Get mail configuration, decode JSON if it exists
            $mailConfigJson = $settings->mail_config ?? null;
            $mailConfig = [];
            $mailConfig = $settings->mail_config ?? [];
            if ($mailConfigJson && is_string($mailConfigJson)) {
                $mailConfig = json_decode($mailConfigJson, true) ?? [];
            }
            
            // Set defaults if config is empty or invalid
            $mailConfig = array_merge([
                'driver' => $mailConfig['driver'] ?? 'smtp',
                'host' => $mailConfig['host'] ?? 'localhost',
                'port' => $mailConfig['port'] ?? 25,
                'username' => $mailConfig['username'] ?? '',
                'password' => $mailConfig['password'] ?? '',
                'encryption' => $mailConfig['encryption'] ?? 'tls',
                'from_address' => $mailConfig['from_address'] ?? 'info@payperviews.net',
                'from_name' => $mailConfig['from_name'] ?? $settings->site_name ?? 'ViewCash',
            ], $mailConfig);
            
            $pageTitle = 'Mail Configuration';
            $configStatus = GeneralSetting::getMailConfigStatus();

            return view('admin.settings.mail-config', compact('settings', 'mailConfig', 'pageTitle', 'configStatus'))
                ->with('pageTitle', 'Mail Configuration');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load mail configuration: ' . $e->getMessage());
        }
    }

    /**
     * Test email configuration.
     */
    public function testEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_email' => 'required|email',
            'test_subject' => 'nullable|string|max:255',
            'test_message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $settings = GeneralSetting::getSettings();
            
            // Refresh mail configuration
            GeneralSetting::refreshMailConfiguration();
            
            // Get custom subject and message from request
            $subject = $request->test_subject ?: ('Test Email from ' . $settings->site_name);
            $message = $request->test_message ?: ('This is a test email from ' . $settings->site_name . '. If you receive this email, your mail configuration is working correctly.');
            
            Mail::raw($message, function ($mail) use ($request, $subject) {
                $mail->to($request->test_email)
                     ->subject($subject);
            });
            
            $successMessage = 'Test email sent successfully to ' . $request->test_email;
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'email' => $request->test_email
                ]);
            }
            
            return back()->with('success', $successMessage);
        } catch (Exception $e) {
            $errorMessage = 'Failed to send test email: ' . $e->getMessage();
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Get system information.
     */
    public function getSystemInfo()
    {
        try {
            $systemInfo = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'database_version' => DB::select('select version() as version')[0]->version ?? 'Unknown',
                'max_execution_time' => ini_get('max_execution_time'),
                'memory_limit' => ini_get('memory_limit'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'disk_free_space' => $this->formatBytes(disk_free_space('.')),
                'disk_total_space' => $this->formatBytes(disk_total_space('.')),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $systemInfo
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get system information: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export settings.
     */
    public function exportSettings()
    {
        try {
            $settings = GeneralSetting::getSettings();
            $filename = 'settings_backup_' . date('Y-m-d_H-i-s') . '.json';
            
            return response()->json($settings->toArray())
                   ->header('Content-Type', 'application/json')
                   ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to export settings: ' . $e->getMessage());
        }
    }

    /**
     * Import settings.
     */
    public function importSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings_file' => 'required|file|mimes:json|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('settings_file');
            $content = file_get_contents($file->getPathname());
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'Invalid JSON file format.');
            }
            
            // Remove fields that shouldn't be imported
            unset($data['id'], $data['created_at'], $data['updated_at']);
            
            GeneralSetting::updateOrCreateSetting($data);
            
            return back()->with('success', 'Settings imported successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to import settings: ' . $e->getMessage());
        }
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'KB', 'MB', 'GB', 'TB');
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    /**
     * Display the media settings page.
     */
    public function mediaSettings()
    {
        try {
            $settings = GeneralSetting::getSettings();
            $pageTitle = 'Media Settings';
            
            return view('admin.settings.media', compact('settings', 'pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load media settings: ' . $e->getMessage());
        }
    }

    /**
     * Update media settings.
     */
    public function updateMediaSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'admin_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'maintenance_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = [];

            // Handle logo uploads
            if ($request->hasFile('logo')) {
                $logoName = GeneralSetting::updateLogo($request->file('logo'), 'logo');
                if ($logoName) {
                    $data['logo'] = $logoName;
                } else {
                    return back()->with('error', 'Failed to upload logo. Please try again.');
                }
            }

            if ($request->hasFile('admin_logo')) {
                $adminLogoName = GeneralSetting::updateLogo($request->file('admin_logo'), 'admin_logo');
                if ($adminLogoName) {
                    $data['admin_logo'] = $adminLogoName;
                } else {
                    return back()->with('error', 'Failed to upload admin logo. Please try again.');
                }
            }

            if ($request->hasFile('favicon')) {
                $faviconName = GeneralSetting::updateLogo($request->file('favicon'), 'favicon');
                if ($faviconName) {
                    $data['favicon'] = $faviconName;
                } else {
                    return back()->with('error', 'Failed to upload favicon. Please try again.');
                }
            }

            if ($request->hasFile('meta_image')) {
                $metaImageName = GeneralSetting::updateMetaImage($request->file('meta_image'));
                if ($metaImageName) {
                    $data['meta_image'] = $metaImageName;
                } else {
                    return back()->with('error', 'Failed to upload meta image. Please try again.');
                }
            }

            if ($request->hasFile('maintenance_image')) {
                $maintenanceImageName = GeneralSetting::updateMaintenanceImage($request->file('maintenance_image'));
                if ($maintenanceImageName) {
                    $data['maintenance_image'] = $maintenanceImageName;
                } else {
                    return back()->with('error', 'Failed to upload maintenance image. Please try again.');
                }
            }

            if (!empty($data)) {
                GeneralSetting::updateOrCreateSetting($data);
                return back()->with('success', 'Media settings updated successfully!');
            }

            return back()->with('info', 'No files were uploaded.');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating media settings: ' . $e->getMessage());
        }
    }

    /**
     * Display the SEO settings page.
     */
    public function seoSettings()
    {
        try {
            $settings = GeneralSetting::getSettings();
            $pageTitle = 'SEO Settings';
            
            return view('admin.settings.seo', compact('settings', 'pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load SEO settings: ' . $e->getMessage());
        }
    }

    /**
     * Update SEO settings.
     */
    public function updateSeoSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->only([
                'meta_title',
                'meta_description',
                'meta_keywords'
            ]);

            GeneralSetting::updateOrCreateSetting($data);

            return back()->with('success', 'SEO settings updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating SEO settings: ' . $e->getMessage());
        }
    }

    /**
     * Display the content settings page.
     */
    public function contentSettings()
    {
        try {
            $settings = GeneralSetting::getSettings();
            $pageTitle = 'Content Settings';
            
            return view('admin.settings.content', compact('settings', 'pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load content settings: ' . $e->getMessage());
        }
    }

    /**
     * Update content settings.
     */
    public function updateContentSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'header_content' => 'nullable|string',
            'footer_content' => 'nullable|string',
            'copyright_text' => 'nullable|string|max:255',
            'home_page_content' => 'nullable|string',
            'about_us_content' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
            'maintenance_message' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->only([
                'header_content',
                'footer_content',
                'copyright_text',
                'home_page_content',
                'about_us_content',
                'terms_conditions',
                'privacy_policy',
                'maintenance_message'
            ]);

            GeneralSetting::updateOrCreateSetting($data);

            return back()->with('success', 'Content settings updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating content settings: ' . $e->getMessage());
        }
    }

    /**
     * Display the theme settings page.
     */
    public function themeSettings()
    {
        try {
            $settings = GeneralSetting::getSettings();
            $pageTitle = 'Theme Settings';
            
            return view('admin.settings.theme', compact('settings', 'pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load theme settings: ' . $e->getMessage());
        }
    }

    /**
     * Update theme settings.
     */
    public function updateThemeSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'header_background_color' => 'nullable|string|max:7',
            'header_text_color' => 'nullable|string|max:7',
            'footer_background_color' => 'nullable|string|max:7',
            'footer_text_color' => 'nullable|string|max:7',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->only([
                'header_background_color',
                'header_text_color',
                'footer_background_color',
                'footer_text_color',
                'custom_css',
                'custom_js'
            ]);

            // Handle theme settings JSON
            if ($request->has('theme_settings')) {
                $data['theme_settings'] = $request->theme_settings;
            }

            GeneralSetting::updateOrCreateSetting($data);

            return back()->with('success', 'Theme settings updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating theme settings: ' . $e->getMessage());
        }
    }

    /**
     * Display the social media settings page.
     */
    public function socialMediaSettings()
    {
        try {
            $settings = GeneralSetting::getSettings();
            $pageTitle = 'Social Media Settings';
            
            return view('admin.settings.social-media', compact('settings', 'pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load social media settings: ' . $e->getMessage());
        }
    }

    /**
     * Update social media settings.
     */
    public function updateSocialMediaSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'social_media_links' => 'nullable|array',
            'social_media_links.*' => 'nullable|url',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->only([
                'contact_email',
                'contact_phone',
                'contact_address'
            ]);

            if ($request->has('social_media_links')) {
                $data['social_media_links'] = array_filter($request->social_media_links);
            }

            GeneralSetting::updateOrCreateSetting($data);

            return back()->with('success', 'Social media settings updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating social media settings: ' . $e->getMessage());
        }
    }

    /**
     * Display SMS configuration form.
     */
    public function smsConfig()
    {
        try {
            $settings = GeneralSetting::getSettings();
            
            // Get SMS configuration, decode JSON if it exists
            $smsConfigJson = $settings->sms_config ?? null;
            $smsConfig = [];
            
            if ($smsConfigJson && is_string($smsConfigJson)) {
                $smsConfig = json_decode($smsConfigJson, true) ?? [];
            } elseif (is_array($smsConfigJson)) {
                $smsConfig = $smsConfigJson;
            }
            
            // Set defaults if config is empty or invalid
            $smsConfig = array_merge([
                'gateway' => $smsConfig['gateway'] ?? 'twilio',
                'api_key' => $smsConfig['api_key'] ?? '',
                'api_secret' => $smsConfig['api_secret'] ?? '',
                'sender_id' => $smsConfig['sender_id'] ?? '',
                'from_number' => $smsConfig['from_number'] ?? '',
                'enabled' => $smsConfig['enabled'] ?? false,
            ], $smsConfig);
            
            $pageTitle = 'SMS Configuration';

            return view('admin.settings.sms-config', compact('settings', 'smsConfig', 'pageTitle'))
                ->with('pageTitle', 'SMS Configuration');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load SMS configuration: ' . $e->getMessage());
        }
    }

    /**
     * Display security settings.
     */
    public function securitySettings()
    {
        try {
            $settings = GeneralSetting::getSettings();
            $pageTitle = 'Security Settings';
            
            // Get security settings from the database
            $securitySettings = $settings->security_settings ?? [];
            
            // Set defaults if config is empty or invalid
            $securitySettings = array_merge([
                'max_login_attempts' => 5,
                'lockout_duration' => 15, // minutes
                'password_min_length' => 8,
                'password_require_uppercase' => false,
                'password_require_lowercase' => false,
                'password_require_numbers' => false,
                'password_require_symbols' => false,
                'session_timeout' => 120, // minutes
                'force_https' => false,
                'two_factor_enabled' => false,
                'ip_whitelist_enabled' => false,
                'ip_whitelist' => [],
                'failed_login_notifications' => true,
                'login_history_days' => 30,
                'auto_logout_inactive' => true,
                'password_expiry_days' => 0, // 0 = never expire
                'prevent_concurrent_sessions' => false,
                'audit_log_enabled' => true,
                'security_headers_enabled' => true,
            ], $securitySettings);

            return view('admin.settings.security', compact('settings', 'securitySettings', 'pageTitle'))
                ->with('pageTitle', 'Security Settings');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load security settings: ' . $e->getMessage());
        }
    }

    /**
     * Update security settings.
     */
    public function updateSecuritySettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'max_login_attempts' => 'required|integer|min:1|max:10',
            'lockout_duration' => 'required|integer|min:1|max:1440',
            'password_min_length' => 'required|integer|min:6|max:50',
            'session_timeout' => 'required|integer|min:5|max:1440',
            'login_history_days' => 'required|integer|min:1|max:365',
            'password_expiry_days' => 'required|integer|min:0|max:365',
            'ip_whitelist' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Process IP whitelist
            $ipWhitelist = [];
            if ($request->ip_whitelist) {
                $ips = explode("\n", $request->ip_whitelist);
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
                        $ipWhitelist[] = $ip;
                    }
                }
            }

            $securitySettings = [
                'max_login_attempts' => $request->max_login_attempts,
                'lockout_duration' => $request->lockout_duration,
                'password_min_length' => $request->password_min_length,
                'password_require_uppercase' => $request->has('password_require_uppercase'),
                'password_require_lowercase' => $request->has('password_require_lowercase'),
                'password_require_numbers' => $request->has('password_require_numbers'),
                'password_require_symbols' => $request->has('password_require_symbols'),
                'session_timeout' => $request->session_timeout,
                'force_https' => $request->has('force_https'),
                'two_factor_enabled' => $request->has('two_factor_enabled'),
                'ip_whitelist_enabled' => $request->has('ip_whitelist_enabled'),
                'ip_whitelist' => $ipWhitelist,
                'failed_login_notifications' => $request->has('failed_login_notifications'),
                'login_history_days' => $request->login_history_days,
                'auto_logout_inactive' => $request->has('auto_logout_inactive'),
                'password_expiry_days' => $request->password_expiry_days,
                'prevent_concurrent_sessions' => $request->has('prevent_concurrent_sessions'),
                'audit_log_enabled' => $request->has('audit_log_enabled'),
                'security_headers_enabled' => $request->has('security_headers_enabled'),
            ];

            GeneralSetting::updateOrCreateSetting([
                'security_settings' => $securitySettings
            ]);

            return back()->with('success', 'Security settings updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating security settings: ' . $e->getMessage());
        }
    }
}
