<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Display user notifications
     */
    public function index()
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->notExpired()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = $this->notificationService->getNotificationStats(Auth::id());

        return view('user.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Get notifications for dropdown (AJAX)
     */
    public function getDropdownNotifications()
    {
        try {
            $userId = Auth::id();
            
            $notifications = UserNotification::where('user_id', $userId)
                ->notExpired()
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $unreadCount = UserNotification::where('user_id', $userId)
                ->unread()
                ->notExpired()
                ->count();

            return response()->json([
                'success' => true,
                'notifications' => $notifications->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'type' => $notification->type ?? 'info',
                        'priority' => $notification->priority ?? 'normal',
                        'icon' => $notification->icon ?? $this->getTypeIcon($notification->type ?? 'info'),
                        'time_ago' => $notification->time_ago,
                        'formatted_time' => $notification->created_at->format('M j, Y g:i A'),
                        'read' => $notification->read,
                        'action_url' => $notification->action_url,
                        'action_text' => $notification->action_text,
                        'is_urgent' => ($notification->priority ?? 'normal') === 'urgent',
                        'type_class' => $this->getTypeClass($notification->type ?? 'info'),
                        'priority_class' => $this->getPriorityClass($notification->priority ?? 'normal'),
                        'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'unread_count' => $unreadCount,
                'total_count' => $notifications->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load dropdown notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load notifications',
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $notification = UserNotification::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $this->notificationService->markAsRead($notification->id);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            UserNotification::where('user_id', Auth::id())
                ->unread()
                ->update([
                    'read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read'
            ], 500);
        }
    }

    /**
     * Delete notification
     */
    public function delete($id)
    {
        try {
            $notification = UserNotification::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification'
            ], 500);
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        try {
            $count = UserNotification::where('user_id', Auth::id())
                ->unread()
                ->notExpired()
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        try {
            UserNotification::where('user_id', Auth::id())->delete();

            return response()->json([
                'success' => true,
                'message' => 'All notifications cleared'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear notifications'
            ], 500);
        }
    }

    /**
     * Redirect to notification action URL
     */
    public function redirect($id)
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return redirect()->route('login')
                    ->with('message', 'Please login to view this notification.');
            }

            $notification = UserNotification::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$notification) {
                return redirect()->route('user.notifications.index')
                    ->with('error', 'Notification not found or you do not have permission to access it.');
            }

            // Mark as read
            if (!$notification->read) {
                $this->notificationService->markAsRead($notification->id);
            }

            // Redirect to action URL if exists
            if ($notification->action_url) {
                // Validate the action URL
                if (filter_var($notification->action_url, FILTER_VALIDATE_URL) || 
                    str_starts_with($notification->action_url, '/')) {
                    
                    return redirect($notification->action_url);
                } else {
                    return redirect()->route('user.notifications.index')
                        ->with('error', 'Invalid notification link.');
                }
            }

            return redirect()->route('user.notifications.index')
                ->with('success', 'Notification viewed successfully.');
                
        } catch (\Exception $e) {
            Log::error('Error in notification redirect', [
                'notification_id' => $id,
                'user_id' => Auth::id() ?? 'guest',
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('user.notifications.index')
                ->with('error', 'Failed to process notification. Please try again.');
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats()
    {
        try {
            $stats = $this->notificationService->getNotificationStats(Auth::id());

            return response()->json([
                'success' => true,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get notification statistics',
            ], 500);
        }
    }

    /**
     * Show notification details
     */
    public function show($id)
    {
        try {
            $notification = UserNotification::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Mark as read when viewed
            if (!$notification->read) {
                $this->notificationService->markAsRead($notification->id);
                $notification->refresh();
            }

            return view('user.notifications.show', compact('notification'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Notification not found');
        }
    }

    /**
     * Get notification settings
     */
    public function settings()
    {
        $user = Auth::user();
        $globalSettings = \App\Models\GeneralSetting::getNotificationSettings();
        
        return view('user.notifications.settings', compact('user', 'globalSettings'));
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'browser_notifications' => 'boolean',
            'marketing_notifications' => 'boolean',
            'transaction_notifications' => 'boolean',
            'security_notifications' => 'boolean',
            'lottery_notifications' => 'boolean',
            'referral_notifications' => 'boolean',
            'maintenance_notifications' => 'boolean',
            'system_notifications' => 'boolean',
        ]);

        try {
            $user = Auth::user();
            $globalSettings = \App\Models\GeneralSetting::getNotificationSettings();
            
            // Get the settings to update (only include checked ones)
            $settings = [];
            $settingsKeys = [
                'email_notifications',
                'sms_notifications', 
                'browser_notifications',
                'marketing_notifications',
                'transaction_notifications',
                'security_notifications',
                'lottery_notifications',
                'referral_notifications',
                'maintenance_notifications',
                'system_notifications'
            ];

            // For checkboxes, we need to handle unchecked as false
            // Also check against global settings
            foreach ($settingsKeys as $key) {
                $userWants = $request->has($key) ? (bool)$request->input($key) : false;
                
                // Check if this type of notification is globally enabled
                $globallyEnabled = true;
                if ($key === 'email_notifications' && !$globalSettings['email_enabled']) {
                    $globallyEnabled = false;
                }
                if ($key === 'sms_notifications' && !$globalSettings['sms_enabled']) {
                    $globallyEnabled = false;
                }
                if ($key === 'browser_notifications' && !$globalSettings['browser_enabled']) {
                    $globallyEnabled = false;
                }
                
                // User can only enable if globally enabled
                $settings[$key] = $userWants && $globallyEnabled;
            }

            // Update user notification settings
            $updatedSettings = $user->updateNotificationSettings($settings);

            // Log the settings update for security
            Log::info('User notification settings updated', [
                'user_id' => $user->id,
                'username' => $user->username,
                'settings' => $settings,
                'global_settings_checked' => $globalSettings,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return redirect()->back()->with('success', 'Notification settings updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update notification settings', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Failed to update notification settings');
        }
    }

    /**
     * Send test notification
     */
    public function sendTestNotification(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Create a test notification
            $notification = UserNotification::create([
                'user_id' => $user->id,
                'title' => 'Test Notification',
                'message' => 'This is a test notification to verify your notification settings are working correctly.',
                'type' => 'test',
                'priority' => 'normal',
                'icon' => 'fas fa-vial',
                'action_url' => route('user.notifications.settings'),
                'action_text' => 'View Settings',
                'expires_at' => now()->addDays(7),
            ]);

            // Log the test notification
            Log::info('Test notification sent', [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send test notification', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification'
            ], 500);
        }
    }

    /**
     * Get icon for notification type
     */
    private function getTypeIcon($type)
    {
        $icons = [
            'welcome' => 'fas fa-hand-wave',
            'investment' => 'fas fa-chart-line',
            'withdrawal' => 'fas fa-money-bill-wave',
            'referral' => 'fas fa-users',
            'lottery' => 'fas fa-gift',
            'support' => 'fas fa-headset',
            'security' => 'fas fa-shield-alt',
            'system' => 'fas fa-cog',
            'promotion' => 'fas fa-megaphone',
            'success' => 'fas fa-check-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'danger' => 'fas fa-exclamation-circle',
            'info' => 'fas fa-info-circle',
        ];

        return $icons[$type] ?? 'fas fa-bell';
    }

    /**
     * Get CSS class for notification type
     */
    private function getTypeClass($type)
    {
        $classes = [
            'welcome' => 'text-info',
            'investment' => 'text-success',
            'withdrawal' => 'text-warning',
            'referral' => 'text-primary',
            'lottery' => 'text-purple',
            'support' => 'text-info',
            'security' => 'text-danger',
            'system' => 'text-secondary',
            'promotion' => 'text-warning',
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            'info' => 'text-info',
        ];

        return $classes[$type] ?? 'text-secondary';
    }

    /**
     * Get CSS class for notification priority
     */
    private function getPriorityClass($priority)
    {
        $classes = [
            'low' => 'border-left-secondary',
            'normal' => 'border-left-primary',
            'high' => 'border-left-warning',
            'urgent' => 'border-left-danger',
        ];

        return $classes[$priority] ?? 'border-left-secondary';
    }
}
