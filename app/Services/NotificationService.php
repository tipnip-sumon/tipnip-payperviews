<?php

namespace App\Services;

use App\Models\UserNotification;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public function createUserNotification(array $data)
    {
        try {
            $notification = UserNotification::create([
                'user_id' => $data['user_id'],
                'title' => $data['title'],
                'message' => $data['message'],
                'type' => $data['type'] ?? 'info',
                'priority' => $data['priority'] ?? 'normal',
                'icon' => $data['icon'] ?? 'fas fa-bell',
                'read' => false,
                'action_url' => $data['action_url'] ?? null,
                'action_text' => $data['action_text'] ?? null,
                'expires_at' => isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null,
                'metadata' => $data['metadata'] ?? null,
            ]);

            // Real-time notification broadcast could be added here
            // broadcast(new NotificationCreated($notification))->toOthers();

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create user notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a notification for an admin
     */
    public function createAdminNotification(array $data)
    {
        try {
            $notification = AdminNotification::create([
                'admin_id' => $data['admin_id'] ?? null,
                'title' => $data['title'],
                'message' => $data['message'],
                'type' => $data['type'] ?? 'info',
                'priority' => $data['priority'] ?? 'normal',
                'icon' => $data['icon'] ?? 'fas fa-bell',
                'read' => false,
                'action_url' => $data['action_url'] ?? null,
                'action_text' => $data['action_text'] ?? null,
                'expires_at' => isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null,
                'metadata' => $data['metadata'] ?? null,
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create admin notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create notifications for multiple users
     */
    public function createBulkUserNotifications(array $userIds, array $data)
    {
        try {
            $notifications = [];
            foreach ($userIds as $userId) {
                $notificationData = array_merge($data, ['user_id' => $userId]);
                $notification = $this->createUserNotification($notificationData);
                if ($notification) {
                    $notifications[] = $notification;
                }
            }
            return $notifications;
        } catch (\Exception $e) {
            Log::error('Failed to create bulk user notifications: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Send welcome notification to new user
     */
    public function sendWelcomeNotification($userId)
    {
        return $this->createUserNotification([
            'user_id' => $userId,
            'title' => 'Welcome to PayperViews!',
            'message' => 'Thank you for joining PayperViews! Start watching videos and earning money today.',
            'type' => 'success',
            'priority' => 'high',
            'icon' => 'fas fa-user-plus',
            'action_url' => route('user.video-views.gallery'),
            'action_text' => 'Start Watching Videos',
        ]);
    }

    /**
     * Send investment notification
     */
    public function sendInvestmentNotification($userId, $planName, $amount)
    {
        return $this->createUserNotification([
            'user_id' => $userId,
            'title' => 'Investment Successful!',
            'message' => "You have successfully invested ${amount} in {$planName}. Your earnings will start accumulating soon.",
            'type' => 'success',
            'priority' => 'high',
            'icon' => 'fas fa-chart-line',
            'action_url' => route('invests'),
            'action_text' => 'View Investments',
            'metadata' => json_encode(['plan' => $planName, 'amount' => $amount]),
        ]);
    }

    /**
     * Send withdrawal notification
     */
    public function sendWithdrawalNotification($userId, $amount, $status)
    {
        $types = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
        $type = $types[$status] ?? 'info';
        
        return $this->createUserNotification([
            'user_id' => $userId,
            'title' => 'Withdrawal ' . ucfirst($status),
            'message' => "Your withdrawal request of ${amount} has been {$status}.",
            'type' => $type,
            'priority' => 'high',
            'icon' => 'fas fa-money-bill-wave',
            'action_url' => route('withdrawals'),
            'action_text' => 'View Withdrawals',
            'metadata' => json_encode(['amount' => $amount, 'status' => $status]),
        ]);
    }

    /**
     * Send referral notification
     */
    public function sendReferralNotification($userId, $referredUsername, $commission = null)
    {
        $message = "Congratulations! {$referredUsername} has joined using your referral link.";
        if ($commission) {
            $message .= " You earned ${commission} commission!";
        }

        return $this->createUserNotification([
            'user_id' => $userId,
            'title' => 'New Referral!',
            'message' => $message,
            'type' => 'success',
            'priority' => 'high',
            'icon' => 'fas fa-users',
            'action_url' => route('referrals'),
            'action_text' => 'View Referrals',
            'metadata' => json_encode(['referred_user' => $referredUsername, 'commission' => $commission]),
        ]);
    }

    /**
     * Send lottery notification
     */
    public function sendLotteryNotification($userId, $type, $data = [])
    {
        $notifications = [
            'ticket_purchased' => [
                'title' => 'Lottery Ticket Purchased!',
                'message' => "You have successfully purchased lottery ticket #{$data['ticket_number']}.",
                'type' => 'success',
                'icon' => 'fas fa-ticket-alt',
            ],
            'draw_won' => [
                'title' => 'Congratulations! You Won!',
                'message' => "You won ${data['amount']} in the lottery draw! Ticket #{$data['ticket_number']}",
                'type' => 'success',
                'priority' => 'urgent',
                'icon' => 'fas fa-trophy',
            ],
            'draw_upcoming' => [
                'title' => 'Lottery Draw Coming Soon!',
                'message' => "The next lottery draw is in 24 hours. Good luck!",
                'type' => 'info',
                'icon' => 'fas fa-clock',
            ],
        ];

        $config = $notifications[$type] ?? [];
        if (empty($config)) return false;

        return $this->createUserNotification(array_merge([
            'user_id' => $userId,
            'priority' => 'normal',
            'action_url' => route('lottery.index'),
            'action_text' => 'View Lottery',
            'metadata' => json_encode($data),
        ], $config));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId = null)
    {
        try {
            $query = UserNotification::where('id', $notificationId);
            if ($userId) {
                $query->where('user_id', $userId);
            }
            
            return $query->update(['read' => true, 'read_at' => now()]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId)
    {
        try {
            return UserNotification::where('user_id', $userId)
                ->where('read', false)
                ->update(['read' => true, 'read_at' => now()]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification($notificationId, $userId = null)
    {
        try {
            $query = UserNotification::where('id', $notificationId);
            if ($userId) {
                $query->where('user_id', $userId);
            }
            
            return $query->delete();
        } catch (\Exception $e) {
            Log::error('Failed to delete notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear all notifications for user
     */
    public function clearAllNotifications($userId)
    {
        try {
            return UserNotification::where('user_id', $userId)->delete();
        } catch (\Exception $e) {
            Log::error('Failed to clear all notifications: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats($userId)
    {
        try {
            return [
                'total' => UserNotification::where('user_id', $userId)->count(),
                'unread' => UserNotification::where('user_id', $userId)->unread()->count(),
                'urgent' => UserNotification::where('user_id', $userId)->where('priority', 'urgent')->unread()->count(),
                'today' => UserNotification::where('user_id', $userId)->whereDate('created_at', today())->count(),
                'this_week' => UserNotification::where('user_id', $userId)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get notification stats: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Clean up expired notifications
     */
    public function cleanupExpiredNotifications()
    {
        try {
            $deleted = UserNotification::expired()->delete();
            Log::info("Cleaned up {$deleted} expired notifications");
            return $deleted;
        } catch (\Exception $e) {
            Log::error('Failed to cleanup expired notifications: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Send system-wide announcement
     */
    public function sendSystemAnnouncement($title, $message, $type = 'info', $excludeUserIds = [])
    {
        try {
            $userIds = User::whereNotIn('id', $excludeUserIds)->pluck('id');
            
            return $this->createBulkUserNotifications($userIds->toArray(), [
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'priority' => 'high',
                'icon' => 'fas fa-bullhorn',
                'expires_at' => now()->addDays(7), // Expire in 7 days
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send system announcement: ' . $e->getMessage());
            return [];
        }
    }
}
