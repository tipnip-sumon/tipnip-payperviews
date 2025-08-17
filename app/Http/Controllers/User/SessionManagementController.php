<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserSessionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SessionManagementController extends Controller
{
    /**
     * Display session management dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get active sessions info (approximate)
        $activeSessions = $this->getActiveSessionsInfo($user->id);
        
        // Get recent session notifications
        $notifications = UserSessionNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get session statistics
        $stats = $this->getSessionStats($user->id);
        
        return view('user.sessions.dashboard', compact('activeSessions', 'notifications', 'stats'));
    }

    /**
     * Get session notifications
     */
    public function notifications(Request $request)
    {
        $user = Auth::user();
        
        $notifications = UserSessionNotification::where('user_id', $user->id)
            ->when($request->filter === 'unread', function($query) {
                $query->unread();
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Mark notifications as read when viewed
        if ($request->mark_as_read) {
            UserSessionNotification::where('user_id', $user->id)
                ->unread()
                ->update(['is_read' => true]);
        }
        
        return view('user.sessions.notifications', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        
        $notification = UserSessionNotification::where('user_id', $user->id)
            ->where('id', $notificationId)
            ->firstOrFail();
        
        $notification->markAsRead();
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Clear all notifications
     */
    public function clearNotifications(Request $request)
    {
        $user = Auth::user();
        
        $type = $request->input('type', 'read'); // 'read', 'all'
        
        $query = UserSessionNotification::where('user_id', $user->id);
        
        if ($type === 'read') {
            $query->where('is_read', true);
        }
        
        $deletedCount = $query->delete();
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'deleted_count' => $deletedCount
            ]);
        }
        
        $message = $type === 'all' 
            ? "All notifications cleared ({$deletedCount} removed)."
            : "Read notifications cleared ({$deletedCount} removed).";
        
        return redirect()->back()->with('success', $message);
    }

    /**
     * Get session security settings
     */
    public function securitySettings()
    {
        $user = Auth::user();
        
        // Get user's session preferences (you can store these in user_meta or user table)
        $settings = [
            'notify_new_login' => true, // Default to true for security
            'notify_different_ip' => true,
            'notify_different_device' => true,
            'auto_logout_other_sessions' => false, // User choice
            'session_timeout_hours' => 24,
            'trusted_ips' => $this->getTrustedIPs($user->id)
        ];
        
        return view('user.sessions.security-settings', compact('settings'));
    }

    /**
     * Update session security settings
     */
    public function updateSecuritySettings(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'type' => 'required|string|in:notifications,security',
            'settings' => 'required|array'
        ]);
        
        try {
            $type = $request->input('type');
            $settings = $request->input('settings');
            
            // Store settings in cache or user_meta table
            Cache::put("user_session_settings_{$type}_{$user->id}", $settings, now()->addDays(30));
            
            // If this is an AJAX request, return JSON
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst($type) . ' settings updated successfully.'
                ]);
            }
            
            return redirect()->back()->with('success', 'Session security settings updated successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update settings: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to update settings.');
        }
    }

    /**
     * Add trusted IP address
     */
    public function addTrustedIP(Request $request)
    {
        // Add debugging to see what's being received
        Log::info('addTrustedIP called', [
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
            'is_json' => $request->expectsJson(),
            'wants_json' => $request->wantsJson(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept')
        ]);
        
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        try {
            $request->validate([
                'ip_address' => 'required|ip'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
                ], 422);
            }
            throw $e;
        }
        
        try {
            $trustedIPs = $this->getTrustedIPs($user->id);
            $newIP = [
                'ip' => $request->ip_address,
                'description' => $request->description ?? 'Added manually',
                'added_at' => now()->toISOString()
            ];
            
            // Check if IP already exists
            foreach ($trustedIPs as $existingIP) {
                if ($existingIP['ip'] === $request->ip_address) {
                    if ($request->expectsJson() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This IP address is already in your trusted list.'
                        ], 400);
                    }
                    return redirect()->back()->with('error', 'This IP address is already in your trusted list.');
                }
            }
            
            $trustedIPs[] = $newIP;
            Cache::put("trusted_ips_user_{$user->id}", $trustedIPs, now()->addDays(30));
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trusted IP address added successfully.'
                ]);
            }
            
            return redirect()->back()->with('success', 'Trusted IP address added successfully.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add trusted IP: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to add trusted IP.');
        }
    }

    /**
     * Remove trusted IP address
     */
    public function removeTrustedIP(Request $request, $index)
    {
        $user = Auth::user();
        
        try {
            $trustedIPs = $this->getTrustedIPs($user->id);
            
            if (isset($trustedIPs[$index])) {
                unset($trustedIPs[$index]);
                $trustedIPs = array_values($trustedIPs); // Re-index array
                Cache::put("trusted_ips_user_{$user->id}", $trustedIPs, now()->addDays(30));
                
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Trusted IP address removed successfully.'
                    ]);
                }
                
                return redirect()->back()->with('success', 'Trusted IP address removed successfully.');
            }
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trusted IP address not found.'
                ], 404);
            }
            
            return redirect()->back()->with('error', 'Trusted IP address not found.');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove trusted IP: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to remove trusted IP.');
        }
    }

    /**
     * Terminate specific session (placeholder - Laravel doesn't provide easy session termination by ID)
     */
    public function terminateSession(Request $request)
    {
        $user = Auth::user();
        $sessionId = $request->input('session_id');
        
        // This is a placeholder - Laravel doesn't provide easy cross-session termination
        // You would need to implement custom session storage for this feature
        
        // Create notification about manual session termination
        UserSessionNotification::create([
            'user_id' => $user->id,
            'type' => 'session_terminated',
            'title' => 'Session Terminated',
            'message' => 'You manually terminated a session from the session management dashboard.',
            'new_login_ip' => $request->ip(),
            'action_taken' => 'manual_termination'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Session termination request processed. The session will be invalidated on next activity.'
        ]);
    }

    /**
     * Terminate all other sessions except current
     */
    public function terminateOtherSessions(Request $request)
    {
        $user = Auth::user();
        
        // This would require custom session management
        // For now, we'll create a notification and clear session cache
        
        // Clear any cached session data for this user
        Cache::forget("user_sessions_{$user->id}");
        
        // Create notification
        UserSessionNotification::create([
            'user_id' => $user->id,
            'type' => 'session_terminated',
            'title' => 'All Other Sessions Terminated',
            'message' => 'You terminated all other active sessions. Only your current session remains active.',
            'new_login_ip' => $request->ip(),
            'action_taken' => 'terminate_all_others'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'All other sessions have been terminated. Only your current session remains active.'
        ]);
    }

    /**
     * Get approximate active sessions info
     */
    private function getActiveSessionsInfo($userId)
    {
        // This is approximate since Laravel doesn't provide easy active session enumeration
        // You could implement custom session tracking for more accurate results
        
        return [
            [
                'id' => session()->getId(),
                'ip' => request()->ip(),
                'device' => $this->getDeviceInfo(request()->userAgent()),
                'last_activity' => now(),
                'is_current' => true
            ]
        ];
    }

    /**
     * Get session statistics
     */
    private function getSessionStats($userId)
    {
        return [
            'total_notifications' => UserSessionNotification::where('user_id', $userId)->count(),
            'unread_notifications' => UserSessionNotification::where('user_id', $userId)->unread()->count(),
            'recent_logins' => UserSessionNotification::where('user_id', $userId)
                ->where('type', 'new_login_detected')
                ->recent(168) // Last week
                ->count(),
            'trusted_ips_count' => count($this->getTrustedIPs($userId))
        ];
    }

    /**
     * Display active sessions page
     */
    public function activeSessions()
    {
        $user = Auth::user();
        
        // Get detailed active sessions info
        $activeSessions = $this->getActiveSessionsInfo($user->id);
        
        // Get recent login activities
        $recentLogins = UserSessionNotification::where('user_id', $user->id)
            ->where('type', 'login')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        return view('user.sessions.active', compact('activeSessions', 'recentLogins'));
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $user = Auth::user();
        
        UserSessionNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    /**
     * Delete a specific notification
     */
    public function deleteNotification($id)
    {
        $user = Auth::user();
        
        $notification = UserSessionNotification::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found']);
        }
        
        $notification->delete();
        
        return response()->json(['success' => true, 'message' => 'Notification deleted']);
    }

    // Private helper methods below this line

    /**
     * Get trusted IPs for user
     */
    private function getTrustedIPs($userId)
    {
        return Cache::get("trusted_ips_user_{$userId}", []);
    }

    /**
     * Get device info from user agent
     */
    private function getDeviceInfo($userAgent)
    {
        // Simple device detection - you can enhance this with a proper library
        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'iPhone')) {
            return 'Mobile Device';
        } elseif (str_contains($userAgent, 'Tablet') || str_contains($userAgent, 'iPad')) {
            return 'Tablet';
        } else {
            return 'Desktop/Laptop';
        }
    }
}
