<?php

if (!function_exists('shouldNotifyLoginIP')) {
    /**
     * Check if a login IP should trigger a notification
     * 
     * @param int $userId
     * @param string $loginIp
     * @param int $hoursSinceLastCheck Hours to look back for recent notifications
     * @return bool
     */
    function shouldNotifyLoginIP($userId, $loginIp, $hoursSinceLastCheck = 24)
    {
        // Check cache for acknowledged IPs first
        $cacheKey = "acknowledged_login_ips_user_{$userId}";
        $acknowledgedIps = cache()->get($cacheKey, []);
        
        if (in_array($loginIp, $acknowledgedIps)) {
            return false; // Don't notify for acknowledged IPs
        }
        
        // Check database for recent notifications from this IP
        $recentNotification = \Illuminate\Support\Facades\DB::table('user_session_notifications')
            ->where('user_id', $userId)
            ->where('new_login_ip', $loginIp)
            ->where('created_at', '>=', now()->subHours($hoursSinceLastCheck))
            ->exists();
        
        return !$recentNotification; // Don't notify if recent notification exists
    }
}

if (!function_exists('createSessionNotification')) {
    /**
     * Create a session notification with duplicate prevention
     * 
     * @param int $userId
     * @param array $notificationData
     * @return bool
     */
    function createSessionNotification($userId, $notificationData)
    {
        // Check if we should notify for this IP
        if (isset($notificationData['new_login_ip']) && 
            !shouldNotifyLoginIP($userId, $notificationData['new_login_ip'])) {
            return false; // Skip notification
        }
        
        try {
            \Illuminate\Support\Facades\DB::table('user_session_notifications')->insert([
                'user_id' => $userId,
                'type' => $notificationData['type'] ?? 'new_login_detected',
                'title' => $notificationData['title'],
                'message' => $notificationData['message'],
                'new_login_ip' => $notificationData['new_login_ip'] ?? null,
                'new_login_device' => $notificationData['new_login_device'] ?? null,
                'new_login_location' => $notificationData['new_login_location'] ?? null,
                'old_session_ip' => $notificationData['old_session_ip'] ?? null,
                'old_session_duration' => $notificationData['old_session_duration'] ?? null,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create session notification', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'data' => $notificationData
            ]);
            return false;
        }
    }
}

if (!function_exists('cleanupUserNotificationCache')) {
    /**
     * Clean up notification cache for a specific user
     * 
     * @param int $userId
     * @return void
     */
    function cleanupUserNotificationCache($userId)
    {
        $cacheKey = "acknowledged_login_ips_user_{$userId}";
        cache()->forget($cacheKey);
    }
}

if (!function_exists('getNotificationStats')) {
    /**
     * Get notification statistics for a user
     * 
     * @param int $userId
     * @return array
     */
    function getNotificationStats($userId)
    {
        $stats = [
            'total_notifications' => 0,
            'unread_notifications' => 0,
            'read_notifications' => 0,
            'acknowledged_ips_count' => 0,
            'recent_notifications' => 0
        ];
        
        try {
            $stats['total_notifications'] = \Illuminate\Support\Facades\DB::table('user_session_notifications')
                ->where('user_id', $userId)
                ->count();
            
            $stats['unread_notifications'] = \Illuminate\Support\Facades\DB::table('user_session_notifications')
                ->where('user_id', $userId)
                ->where('is_read', false)
                ->count();
            
            $stats['read_notifications'] = $stats['total_notifications'] - $stats['unread_notifications'];
            
            // Get acknowledged IPs count from cache
            $cacheKey = "acknowledged_login_ips_user_{$userId}";
            $acknowledgedIps = cache()->get($cacheKey, []);
            $stats['acknowledged_ips_count'] = count($acknowledgedIps);
            
            // Get recent notifications count (last 7 days)
            $stats['recent_notifications'] = \Illuminate\Support\Facades\DB::table('user_session_notifications')
                ->where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to get notification stats', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
        
        return $stats;
    }
}
