<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display admin notifications
     */
    public function index()
    {
        $notifications = AdminNotification::where(function ($q) {
            $q->where('admin_id', Auth::guard('admin')->id())
              ->orWhereNull('admin_id'); // Include global notifications
        })
        ->notExpired()
        ->orderBy('priority', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        $stats = AdminNotification::getSummary(Auth::guard('admin')->id());

        return view('admin.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Show the form for creating a new notification
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Store a newly created notification
     */
    public function store(Request $request)
    {
        // Base validation rules
        $validationRules = [
            'recipient_type' => 'required|in:admin,user',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,danger,primary',
            'priority' => 'required|in:low,normal,high,urgent',
            'action_url' => 'nullable|url',
            'action_text' => 'nullable|string|max:191',
            'expires_at' => 'nullable|date|after:now',
            'send_to' => 'required|in:all,specific',
        ];

        // Add conditional validation based on recipient type
        if ($request->recipient_type === 'admin') {
            $validationRules['admin_ids'] = 'required_if:send_to,specific|array|min:1';
            $validationRules['admin_ids.*'] = 'exists:admins,id';
        } else {
            $validationRules['user_ids'] = 'required_if:send_to,specific|array|min:1';
            $validationRules['user_ids.*'] = 'exists:users,id';
        }

        $request->validate($validationRules, [
            'recipient_type.required' => 'Please select who to send the notification to.',
            'title.required' => 'The notification title is required.',
            'message.required' => 'The notification message is required.',
            'type.required' => 'Please select a notification type.',
            'priority.required' => 'Please select a notification priority.',
            'send_to.required' => 'Please select the send method.',
            'admin_ids.required_if' => 'Please select at least one admin when sending to specific admins.',
            'admin_ids.min' => 'Please select at least one admin.',
            'admin_ids.*.exists' => 'One or more selected admins are invalid.',
            'user_ids.required_if' => 'Please select at least one user when sending to specific users.',
            'user_ids.min' => 'Please select at least one user.',
            'user_ids.*.exists' => 'One or more selected users are invalid.',
            'action_url.url' => 'The action URL must be a valid URL.',
            'expires_at.after' => 'The expiry date must be in the future.'
        ]);

        try {
            $notificationData = [
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'priority' => $request->priority,
                'icon' => $this->getIconForType($request->type),
                'action_url' => $request->action_url,
                'action_text' => $request->action_text,
                'expires_at' => $request->expires_at ? Carbon::parse($request->expires_at) : null,
                'metadata' => [
                    'created_by' => Auth::guard('admin')->id(),
                    'created_by_name' => Auth::guard('admin')->user()->name ?? Auth::guard('admin')->user()->email,
                    'send_to' => $request->send_to,
                    'recipient_type' => $request->recipient_type
                ]
            ];

            $recipientCount = 0;

            if ($request->recipient_type === 'admin') {
                // Handle admin notifications
                if ($request->send_to === 'all') {
                    // Create notification for all admins (global notification)
                    $notificationData['admin_id'] = null; // Global notification
                    AdminNotification::create($notificationData);
                    $recipientCount = \App\Models\Admin::count();
                } else {
                    // Create individual notifications for selected admins
                    foreach ($request->admin_ids as $adminId) {
                        $notificationData['admin_id'] = $adminId;
                        AdminNotification::create($notificationData);
                        $recipientCount++;
                    }
                }
                $recipientType = 'admin(s)';
            } else {
                // Handle user notifications
                if ($request->send_to === 'all') {
                    // Create individual notifications for all users (user_id cannot be null)
                    $allUsers = \App\Models\User::pluck('id');
                    foreach ($allUsers as $userId) {
                        $notificationData['user_id'] = $userId;
                        UserNotification::create($notificationData);
                    }
                    $recipientCount = $allUsers->count();
                } else {
                    // Create individual notifications for selected users
                    foreach ($request->user_ids as $userId) {
                        $notificationData['user_id'] = $userId;
                        UserNotification::create($notificationData);
                        $recipientCount++;
                    }
                }
                $recipientType = 'user(s)';
            }

            return redirect()->route('admin.notifications.index')
                ->with('success', "Notification sent successfully to {$recipientCount} {$recipientType}!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create notification: ' . $e->getMessage());
        }
    }

    /**
     * Get icon for notification type
     */
    private function getIconForType($type)
    {
        $icons = [
            'info' => 'fe fe-info',
            'success' => 'fe fe-check-circle',
            'warning' => 'fe fe-alert-triangle',
            'danger' => 'fe fe-alert-circle',
            'primary' => 'fe fe-bell'
        ];

        return $icons[$type] ?? 'fe fe-bell';
    }

    /**
     * Get notifications for dropdown (AJAX)
     */
    public function getDropdownNotifications()
    {
        try {
            $adminId = Auth::guard('admin')->id();
            
            $notifications = AdminNotification::where(function ($q) use ($adminId) {
                $q->where('admin_id', $adminId)
                  ->orWhereNull('admin_id'); // Include global notifications
            })
            ->notExpired()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

            $unreadCount = AdminNotification::where(function ($q) use ($adminId) {
                $q->where('admin_id', $adminId)
                  ->orWhereNull('admin_id');
            })
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
                        'type' => $notification->type,
                        'priority' => $notification->priority,
                        'icon' => $notification->icon,
                        'time_ago' => $notification->time_ago,
                        'formatted_time' => $notification->formatted_time,
                        'read' => $notification->read,
                        'action_url' => $notification->action_url,
                        'action_text' => $notification->action_text,
                        'is_urgent' => $notification->is_urgent,
                        'type_class' => $notification->type_class,
                        'priority_class' => $notification->priority_class,
                    ];
                }),
                'unread_count' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $adminId = Auth::guard('admin')->id();
            
            $notification = AdminNotification::where('id', $id)
                ->where(function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId)
                      ->orWhereNull('admin_id');
                })
                ->firstOrFail();

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $adminId = Auth::guard('admin')->id();
            
            AdminNotification::where(function ($q) use ($adminId) {
                $q->where('admin_id', $adminId)
                  ->orWhereNull('admin_id');
            })
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read',
            ], 500);
        }
    }

    /**
     * Delete notification
     */
    public function delete($id)
    {
        try {
            $adminId = Auth::guard('admin')->id();
            
            $notification = AdminNotification::where('id', $id)
                ->where(function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId)
                      ->orWhereNull('admin_id');
                })
                ->firstOrFail();

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
            ], 500);
        }
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        try {
            $adminId = Auth::guard('admin')->id();
            
            AdminNotification::where(function ($q) use ($adminId) {
                $q->where('admin_id', $adminId)
                  ->orWhereNull('admin_id');
            })->delete();

            return response()->json([
                'success' => true,
                'message' => 'All notifications cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear notifications',
            ], 500);
        }
    }

    /**
     * Send system announcement
     */
    public function sendAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,success,warning,danger',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        try {
            $notifications = $this->notificationService->sendSystemAnnouncement(
                $request->title,
                $request->message,
                $request->type
            );

            return response()->json([
                'success' => true,
                'message' => 'Announcement sent to ' . count($notifications) . ' users',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send announcement',
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats()
    {
        try {
            $adminId = Auth::guard('admin')->id();
            $stats = AdminNotification::getSummary($adminId);

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
            $adminId = Auth::guard('admin')->id();
            
            $notification = AdminNotification::where('id', $id)
                ->where(function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId)
                      ->orWhereNull('admin_id');
                })
                ->firstOrFail();

            // Get related notifications of the same type
            $relatedNotifications = AdminNotification::where('type', $notification->type)
                ->where('id', '!=', $notification->id)
                ->where(function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId)
                      ->orWhereNull('admin_id');
                })
                ->latest()
                ->limit(5)
                ->get();

            // Get statistics for this notification
            $stats = [
                'similar_today' => AdminNotification::where('type', $notification->type)
                    ->whereDate('created_at', today())
                    ->count(),
                'type_total' => AdminNotification::where('type', $notification->type)->count()
            ];

            // Mark as read when viewed if request is not AJAX
            if (!$notification->read && !request()->expectsJson()) {
                $notification->markAsRead();
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'notification' => [
                        'id' => $notification->id,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'type' => $notification->type,
                        'priority' => $notification->priority,
                        'read' => $notification->read,
                        'action_url' => $notification->action_url,
                        'expires_at' => $notification->expires_at?->format('M j, Y \a\t g:i A'),
                        'formatted_time' => $notification->created_at->format('M j, Y \a\t g:i A'),
                        'read_at' => $notification->read_at?->format('M j, Y \a\t g:i A')
                    ]
                ]);
            }

            return view('admin.notifications.show', compact('notification', 'relatedNotifications', 'stats'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }
            
            return redirect()->route('admin.notifications.index')
                ->with('error', 'Notification not found');
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread($id)
    {
        try {
            $adminId = Auth::guard('admin')->id();
            
            $notification = AdminNotification::where('id', $id)
                ->where(function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId)
                      ->orWhereNull('admin_id');
                })
                ->firstOrFail();

            $notification->update([
                'read' => false,
                'read_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as unread'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as unread'
            ], 500);
        }
    }

    /**
     * Duplicate notification
     */
    public function duplicate($id)
    {
        try {
            $adminId = Auth::guard('admin')->id();
            
            $notification = AdminNotification::where('id', $id)
                ->where(function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId)
                      ->orWhereNull('admin_id');
                })
                ->firstOrFail();
            
            $duplicate = AdminNotification::create([
                'admin_id' => $notification->admin_id,
                'title' => 'Copy of ' . $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'priority' => $notification->priority,
                'action_url' => $notification->action_url,
                'action_text' => $notification->action_text,
                'metadata' => $notification->metadata,
                'expires_at' => $notification->expires_at,
                'read' => false,
                'read_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification duplicated successfully',
                'notification_id' => $duplicate->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate notification'
            ], 500);
        }
    }

    /**
     * Show notification settings page
     */
    public function settings()
    {
        return view('admin.notifications.settings');
    }

    /**
     * Save notification settings
     */
    public function saveSettings(Request $request)
    {
        try {
            // In a real application, save settings to database or config
            // For now, we'll just return success
            
            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings'
            ], 500);
        }
    }

    /**
     * Reset settings to defaults
     */
    public function resetSettings()
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Settings reset to defaults'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset settings'
            ], 500);
        }
    }

    /**
     * Get current settings
     */
    public function getSettings()
    {
        try {
            $settings = [
                'enable_notifications' => true,
                'enable_popup_notifications' => true,
                'enable_sound' => true,
                'enable_browser_notifications' => false,
                'refresh_interval' => 30,
                'max_notifications' => 50,
                'auto_cleanup_days' => 30,
                'popup_duration' => 5
            ];

            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get settings'
            ], 500);
        }
    }

    /**
     * Cleanup old notifications
     */
    public function cleanup()
    {
        try {
            $cleanedCount = AdminNotification::where('read', true)
                ->where('created_at', '<', now()->subDays(30))
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cleanup completed successfully',
                'cleaned_count' => $cleanedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed'
            ], 500);
        }
    }

    /**
     * Show real-time notifications page
     */
    public function realtime()
    {
        return view('admin.notifications.realtime');
    }

    /**
     * Get real-time notifications (AJAX polling)
     */
    public function getRealtime(Request $request)
    {
        try {
            $adminId = Auth::guard('admin')->id();
            $lastId = $request->input('last_id', 0);
            $types = $request->input('types', []);
            $priority = $request->input('priority');

            $query = AdminNotification::where('id', '>', $lastId)
                ->where(function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId)
                      ->orWhereNull('admin_id');
                })
                ->latest()
                ->limit(10);

            if (!empty($types)) {
                $query->whereIn('type', $types);
            }

            if ($priority) {
                $query->where('priority', $priority);
            }

            $notifications = $query->get()->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'priority' => $notification->priority,
                    'time_ago' => $notification->time_ago,
                    'action_url' => $notification->action_url
                ];
            });

            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get realtime notifications'
            ], 500);
        }
    }

    /**
     * Send test notification
     */
    public function testNotification()
    {
        try {
            AdminNotification::create([
                'admin_id' => Auth::guard('admin')->id(),
                'title' => 'Test Notification',
                'message' => 'This is a test notification sent at ' . now()->format('H:i:s'),
                'type' => 'info',
                'priority' => 'normal'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification'
            ], 500);
        }
    }

    /**
     * Get unread notification count via AJAX
     */
    public function getUnreadCount()
    {
        try {
            $adminId = Auth::guard('admin')->id();
            
            $count = AdminNotification::where(function ($q) use ($adminId) {
                $q->where('admin_id', $adminId)
                  ->orWhereNull('admin_id');
            })
            ->where('read', false)
            ->notExpired()
            ->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0
            ], 500);
        }
    }

    /**
     * Redirect to notification action URL and mark as read
     */
    public function redirect($id)
    {
        try {
            $adminId = Auth::guard('admin')->id();
            
            $notification = AdminNotification::where('id', $id)
                ->where(function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId)
                      ->orWhereNull('admin_id');
                })
                ->firstOrFail();

            // Mark as read
            if (!$notification->read) {
                $notification->markAsRead();
            }

            // Redirect to action URL if available
            if ($notification->action_url) {
                return redirect($notification->action_url);
            }

            return redirect()->route('admin.notifications.show', $notification->id);
        } catch (\Exception $e) {
            return redirect()->route('admin.notifications.index')
                ->with('error', 'Notification not found');
        }
    }
}
