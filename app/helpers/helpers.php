<?php
use App\Lib\ClientInfo;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Include GeneralSettingsHelper
require_once __DIR__ . '/GeneralSettingsHelper.php';

function slug($string){
    return Str::slug($string);
}


function verificationCode($length)
{
    if ($length == 0) {
        return 0;
    }

    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}
function getNumber($length = 8)
{
    $characters       = '1234567890';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}
function getTrx($length = 12)
{
    $characters       = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    return $printAmount;
}
function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}

function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}

function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}
function showDateTime($date, $format = 'Y-m-d h:i A')
{
    // $lang = session()->get('lang');
    // Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}
function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}
function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}
function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}

    if(!function_exists('getIpInfo')) {
        function getIpInfo() {
            $clientInfo = ClientInfo::ipInfo();
            return $clientInfo;
        }
    }

    if(!function_exists('osBrowser')) {
        function osBrowser() {
            $osBrowser = ClientInfo::osBrowser();
            return $osBrowser;
        }
    }
    function getResponse($remark, $status, $message, $data = null)
{
    if (!$data) {
        return response()->json([
            'remark'  => $remark,
            'status'  => $status,
            'message' => [$status => $message],
        ]);
    }

    return response()->json([
        'remark'  => $remark,
        'status'  => $status,
        'message' => [$status => [$message]],
        'data'    => $data,
    ]);
}

// General Settings Helper Functions
if (!function_exists('getSetting')) {
    /**
     * Get a specific setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function getSetting($key, $default = null)
    {
        return \App\Models\GeneralSetting::getSetting($key, $default);
    }
}

if (!function_exists('getSettings')) {
    /**
     * Get all settings.
     *
     * @return \App\Models\GeneralSetting
     */
    function getSettings()
    {
        return \App\Models\GeneralSetting::getSettings();
    }
}

if (!function_exists('siteName')) {
    /**
     * Get site name.
     *
     * @return string
     */
    function siteName()
    {
        return \App\Models\GeneralSetting::getSiteName();
    }
}

if (!function_exists('currencySymbol')) {
    /**
     * Get currency symbol.
     *
     * @return string
     */
    function currencySymbol()
    {
        return \App\Models\GeneralSetting::getCurrencySymbol();
    }
}

if (!function_exists('currencyText')) {
    /**
     * Get currency text.
     *
     * @return string
     */
    function currencyText()
    {
        return \App\Models\GeneralSetting::getCurrencyText();
    }
}

if (!function_exists('isMaintenanceMode')) {
    /**
     * Check if maintenance mode is active.
     *
     * @return bool
     */
    function isMaintenanceMode()
    {
        return \App\Models\GeneralSetting::isMaintenanceMode();
    }
}

if (!function_exists('isRegistrationEnabled')) {
    /**
     * Check if registration is enabled.
     *
     * @return bool
     */
    function isRegistrationEnabled()
    {
        return \App\Models\GeneralSetting::isRegistrationEnabled();
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format currency with symbol.
     *
     * @param float $amount
     * @return string
     */
    function formatCurrency($amount)
    {
        return currencySymbol() . number_format($amount, 2);
    }
}

if (!function_exists('primaryColor')) {
    /**
     * Get primary color.
     *
     * @return string
     */
    function primaryColor()
    {
        return getSetting('base_color', '#007bff');
    }
}

if (!function_exists('secondaryColor')) {
    /**
     * Get secondary color.
     *
     * @return string
     */
    function secondaryColor()
    {
        return getSetting('secondary_color', '#6c757d');
    }
}

if (!function_exists('calculateCharge')) {
    /**
     * Calculate total charge for an amount.
     *
     * @param float $amount
     * @return float
     */
    function calculateCharge($amount)
    {
        $fixedCharge = getSetting('f_charge', 0);
        $percentageCharge = getSetting('p_charge', 0);
        
        return $fixedCharge + ($amount * $percentageCharge / 100);
    }
}

/**
 * Get media URL for logos and other media files.
 */
if (!function_exists('getMediaUrl')) {
    function getMediaUrl($filename, $type = 'logo')
    {
        try {
            return \App\Models\GeneralSetting::getMediaUrl($filename, $type);
        } catch (\Exception $e) {
            // Fallback to default assets
            $fallbacks = [
                'logo' => 'assets/images/brand-logos/desktop-logo.png',
                'admin_logo' => 'assets/images/brand-logos/desktop-logo.png',
                'favicon' => 'assets/images/brand-logos/favicon.ico',
                'meta_image' => 'assets/images/brand-logos/desktop-logo.png',
                'maintenance_image' => 'assets/images/brand-logos/desktop-logo.png',
            ];
            
            return asset($fallbacks[$type] ?? $fallbacks['logo']);
        }
    }
}

/**
 * Get settings safely with fallback.
 */
if (!function_exists('getSettingsSafe')) {
    function getSettingsSafe()
    {
        try {
            return \App\Models\GeneralSetting::getSettings();
        } catch (\Exception $e) {
            return (object) [
                'logo' => null,
                'admin_logo' => null,
                'favicon' => null,
                'site_name' => 'ViewCash',
            ];
        }
    }
}

/**
 * Get logo storage path for a specific type.
 */
if (!function_exists('getLogoStoragePath')) {
    function getLogoStoragePath($type = 'logo')
    {
        return \App\Models\GeneralSetting::getLogoStoragePath($type);
    }
}

/**
 * Send welcome notification to a new user
 */
if (!function_exists('notifyUserWelcome')) {
    function notifyUserWelcome($userId, $username)
    {
        try {
            \App\Models\UserNotification::create([
                'user_id' => $userId,
                'type' => 'welcome',
                'title' => 'Welcome to Our Platform!',
                'message' => "Welcome {$username}! Thank you for joining our platform. We're excited to have you on board!",
                'read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send welcome notification: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * Send notification to user
 */
if (!function_exists('notifyUser')) {
    function notifyUser($userId, $type, $title, $message, $data = null)
    {
        try {
            \App\Models\UserNotification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send notification: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * Get unread notification count for user
 */
if (!function_exists('getUnreadNotificationCount')) {
    function getUnreadNotificationCount($userId)
    {
        try {
            return \App\Models\UserNotification::where('user_id', $userId)
                ->where('read', false)
                ->count();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to get unread notification count: ' . $e->getMessage());
            return 0;
        }
    }
}

/**
 * Get recent notifications for user
 */
if (!function_exists('getUserNotifications')) {
    function getUserNotifications($userId, $limit = 10)
    {
        try {
            return \App\Models\UserNotification::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to get user notifications: ' . $e->getMessage());
            return collect();
        }
    }
}

/**
 * Mark notification as read
 */
if (!function_exists('markNotificationAsRead')) {
    function markNotificationAsRead($notificationId, $userId = null)
    {
        try {
            $query = \App\Models\UserNotification::where('id', $notificationId);
            
            if ($userId) {
                $query->where('user_id', $userId);
            }
            
            return $query->update([
                'read' => true,
                'read_at' => now()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * Mark all notifications as read for user
 */
if (!function_exists('markAllNotificationsAsRead')) {
    function markAllNotificationsAsRead($userId)
    {
        try {
            return \App\Models\UserNotification::where('user_id', $userId)
                ->where('read', false)
                ->update([
                    'read' => true,
                    'read_at' => now()
                ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
            return false;
        }
    }
}