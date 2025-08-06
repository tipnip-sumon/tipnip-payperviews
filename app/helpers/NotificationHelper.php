<?php

use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Notification Helper Functions
 * Specialized functions for handling user notifications
 */

if (!function_exists('notifyUserWelcome')) {
    /**
     * Send welcome notification to a new user
     */
    function notifyUserWelcome($userId, $username)
    {
        try {
            return UserNotification::createForUser($userId, [
                'type' => 'welcome',
                'title' => 'Welcome to Our Platform!',
                'message' => "Welcome {$username}! Thank you for joining our platform. We're excited to have you on board!",
                'icon' => 'fas fa-heart',
                'data' => [
                    'user_type' => 'new_user',
                    'welcome_bonus' => true
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyUser')) {
    /**
     * Send general notification to user
     */
    function notifyUser($userId, $type, $title, $message, $options = [])
    {
        try {
            $data = [
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'icon' => $options['icon'] ?? 'fas fa-info-circle',
                'data' => $options['data'] ?? null,
                'action_url' => $options['action_url'] ?? null,
                'expires_at' => $options['expires_at'] ?? null,
            ];

            return UserNotification::createForUser($userId, $data);
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyUserDeposit')) {
    /**
     * Send deposit notification to user
     */
    function notifyUserDeposit($userId, $amount, $gateway, $status = 'completed')
    {
        $statusText = $status === 'completed' ? 'successful' : 'pending';
        $icon = $status === 'completed' ? 'fas fa-check-circle' : 'fas fa-clock';
        
        return notifyUser($userId, 'deposit', "Deposit {$statusText}", 
            "Your deposit of " . formatCurrency($amount) . " via {$gateway} is {$statusText}.", [
                'icon' => $icon,
                'data' => [
                    'amount' => $amount,
                    'gateway' => $gateway,
                    'status' => $status,
                    'type' => 'deposit'
                ]
            ]
        );
    }
}

if (!function_exists('notifyUserWithdrawal')) {
    /**
     * Send withdrawal notification to user
     */
    function notifyUserWithdrawal($userId, $amount, $method, $status = 'pending')
    {
        $statusText = $status === 'approved' ? 'approved' : ($status === 'rejected' ? 'rejected' : 'pending');
        $icon = $status === 'approved' ? 'fas fa-check-circle' : ($status === 'rejected' ? 'fas fa-times-circle' : 'fas fa-clock');
        
        return notifyUser($userId, 'withdrawal', "Withdrawal {$statusText}", 
            "Your withdrawal of " . formatCurrency($amount) . " via {$method} is {$statusText}.", [
                'icon' => $icon,
                'data' => [
                    'amount' => $amount,
                    'method' => $method,
                    'status' => $status,
                    'type' => 'withdrawal'
                ]
            ]
        );
    }
}

if (!function_exists('notifyUserCommission')) {
    /**
     * Send commission notification to user
     */
    function notifyUserCommission($userId, $amount, $fromUser, $level = 1)
    {
        return notifyUser($userId, 'commission', 'Commission Earned!', 
            "You earned " . formatCurrency($amount) . " commission from {$fromUser} (Level {$level}).", [
                'icon' => 'fas fa-dollar-sign',
                'data' => [
                    'amount' => $amount,
                    'from_user' => $fromUser,
                    'level' => $level,
                    'type' => 'commission'
                ]
            ]
        );
    }
}

if (!function_exists('notifyUserLottery')) {
    /**
     * Send lottery notification to user
     */
    function notifyUserLottery($userId, $message, $type = 'lottery', $data = [])
    {
        $icon = $type === 'winner' ? 'fas fa-trophy' : 'fas fa-ticket-alt';
        $title = $type === 'winner' ? 'Lottery Winner!' : 'Lottery Update';
        
        return notifyUser($userId, 'lottery', $title, $message, [
            'icon' => $icon,
            'data' => array_merge(['type' => 'lottery', 'lottery_type' => $type], $data)
        ]);
    }
}

if (!function_exists('getUnreadNotificationCount')) {
    /**
     * Get unread notification count for user
     */
    function getUnreadNotificationCount($userId)
    {
        try {
            return UserNotification::getUnreadCountForUser($userId);
        } catch (\Exception $e) {
            Log::error('Failed to get unread notification count: ' . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('getUserNotifications')) {
    /**
     * Get recent notifications for user
     */
    function getUserNotifications($userId, $limit = 10, $unreadOnly = false)
    {
        try {
            $query = UserNotification::where('user_id', $userId)
                ->notExpired()
                ->orderBy('created_at', 'desc');
                
            if ($unreadOnly) {
                $query->unread();
            }
            
            return $query->limit($limit)->get();
        } catch (\Exception $e) {
            Log::error('Failed to get user notifications: ' . $e->getMessage());
            return collect();
        }
    }
}

if (!function_exists('markNotificationAsRead')) {
    /**
     * Mark specific notification as read
     */
    function markNotificationAsRead($notificationId, $userId = null)
    {
        try {
            $query = UserNotification::where('id', $notificationId);
            
            if ($userId) {
                $query->where('user_id', $userId);
            }
            
            $notification = $query->first();
            if ($notification) {
                $notification->markAsRead();
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('markAllNotificationsAsRead')) {
    /**
     * Mark all notifications as read for user
     */
    function markAllNotificationsAsRead($userId)
    {
        try {
            return UserNotification::markAllAsReadForUser($userId);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('deleteNotification')) {
    /**
     * Delete a specific notification
     */
    function deleteNotification($notificationId, $userId = null)
    {
        try {
            $query = UserNotification::where('id', $notificationId);
            
            if ($userId) {
                $query->where('user_id', $userId);
            }
            
            return $query->delete() > 0;
        } catch (\Exception $e) {
            Log::error('Failed to delete notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('cleanupExpiredNotifications')) {
    /**
     * Clean up expired notifications
     */
    function cleanupExpiredNotifications()
    {
        try {
            return UserNotification::cleanupExpired();
        } catch (\Exception $e) {
            Log::error('Failed to cleanup expired notifications: ' . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('notifyUserRegistration')) {
    /**
     * Send registration confirmation notification
     */
    function notifyUserRegistration($userId, $username, $email)
    {
        try {
            return UserNotification::createForUser($userId, [
                'type' => 'registration',
                'title' => 'Registration Successful!',
                'message' => "Welcome {$username}! Your account has been successfully created with email {$email}. Please verify your email to access all features.",
                'icon' => 'fas fa-user-check',
                'data' => [
                    'username' => $username,
                    'email' => $email,
                    'registration_time' => now()->toDateTimeString(),
                    'type' => 'registration'
                ],
                'action_url' => route('verification.notice') ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send registration notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyUserEmailVerification')) {
    /**
     * Send email verification notification
     */
    function notifyUserEmailVerification($userId, $verified = true)
    {
        try {
            if ($verified) {
                return UserNotification::createForUser($userId, [
                    'type' => 'verification',
                    'title' => 'Email Verified Successfully!',
                    'message' => 'Your email has been verified! You now have full access to all platform features.',
                    'icon' => 'fas fa-check-circle',
                    'data' => [
                        'verification_status' => 'verified',
                        'verified_at' => now()->toDateTimeString(),
                        'type' => 'email_verification'
                    ]
                ]);
            } else {
                return UserNotification::createForUser($userId, [
                    'type' => 'verification',
                    'title' => 'Email Verification Required',
                    'message' => 'Please verify your email address to access all platform features. Check your inbox for the verification link.',
                    'icon' => 'fas fa-envelope',
                    'data' => [
                        'verification_status' => 'pending',
                        'type' => 'email_verification'
                    ],
                    'action_url' => route('verification.notice') ?? null
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email verification notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyDepositInitiated')) {
    /**
     * Send deposit initiation notification
     */
    function notifyDepositInitiated($userId, $amount, $gateway, $transactionId = null)
    {
        try {
            return UserNotification::createForUser($userId, [
                'type' => 'deposit',
                'title' => 'Deposit Initiated',
                'message' => "Your deposit of " . formatCurrency($amount) . " via {$gateway} has been initiated. We'll notify you once it's confirmed.",
                'icon' => 'fas fa-clock',
                'data' => [
                    'amount' => $amount,
                    'gateway' => $gateway,
                    'transaction_id' => $transactionId,
                    'status' => 'initiated',
                    'type' => 'deposit_initiated',
                    'initiated_at' => now()->toDateTimeString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send deposit initiation notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyDepositConfirmed')) {
    /**
     * Send deposit confirmation notification
     */
    function notifyDepositConfirmed($userId, $amount, $gateway, $transactionId = null, $newBalance = null)
    {
        try {
            $message = "Your deposit of " . formatCurrency($amount) . " via {$gateway} has been confirmed and added to your account.";
            if ($newBalance) {
                $message .= " Your new balance is " . formatCurrency($newBalance) . ".";
            }

            return UserNotification::createForUser($userId, [
                'type' => 'deposit',
                'title' => 'Deposit Confirmed!',
                'message' => $message,
                'icon' => 'fas fa-check-circle',
                'data' => [
                    'amount' => $amount,
                    'gateway' => $gateway,
                    'transaction_id' => $transactionId,
                    'new_balance' => $newBalance,
                    'status' => 'confirmed',
                    'type' => 'deposit_confirmed',
                    'confirmed_at' => now()->toDateTimeString()
                ],
                'action_url' => route('user.transfer_funds') ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send deposit confirmation notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyDepositFailed')) {
    /**
     * Send deposit failure notification
     */
    function notifyDepositFailed($userId, $amount, $gateway, $reason = null, $transactionId = null)
    {
        try {
            $message = "Your deposit of " . formatCurrency($amount) . " via {$gateway} has failed.";
            if ($reason) {
                $message .= " Reason: {$reason}";
            }
            $message .= " Please try again or contact support if the issue persists.";

            return UserNotification::createForUser($userId, [
                'type' => 'deposit',
                'title' => 'Deposit Failed',
                'message' => $message,
                'icon' => 'fas fa-times-circle',
                'data' => [
                    'amount' => $amount,
                    'gateway' => $gateway,
                    'transaction_id' => $transactionId,
                    'reason' => $reason,
                    'status' => 'failed',
                    'type' => 'deposit_failed',
                    'failed_at' => now()->toDateTimeString()
                ],
                'action_url' => route('user.transfer_funds') ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send deposit failure notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyPaymentProcessed')) {
    /**
     * Send payment processing notification
     */
    function notifyPaymentProcessed($userId, $amount, $type, $description = null, $transactionId = null)
    {
        try {
            $message = "Your payment of " . formatCurrency($amount) . " for {$type} has been processed successfully.";
            if ($description) {
                $message .= " {$description}";
            }

            return UserNotification::createForUser($userId, [
                'type' => 'payment',
                'title' => 'Payment Processed',
                'message' => $message,
                'icon' => 'fas fa-credit-card',
                'data' => [
                    'amount' => $amount,
                    'payment_type' => $type,
                    'description' => $description,
                    'transaction_id' => $transactionId,
                    'status' => 'processed',
                    'type' => 'payment_processed',
                    'processed_at' => now()->toDateTimeString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment processing notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyReferralTokenTransferSent')) {
    /**
     * Send notification when user sends referral tokens
     */
    function notifyReferralTokenTransferSent($userId, $recipientUsername, $amount, $transferId = null)
    {
        try {
            return UserNotification::createForUser($userId, [
                'type' => 'referral_transfer',
                'title' => 'Referral Tokens Sent',
                'message' => "You have successfully sent {$amount} referral tokens to {$recipientUsername}.",
                'icon' => 'fas fa-paper-plane',
                'data' => [
                    'amount' => $amount,
                    'recipient' => $recipientUsername,
                    'transfer_id' => $transferId,
                    'direction' => 'sent',
                    'type' => 'referral_transfer_sent',
                    'sent_at' => now()->toDateTimeString()
                ],
                'action_url' => route('special.tickets.outgoing') ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send referral token transfer sent notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyReferralTokenTransferReceived')) {
    /**
     * Send notification when user receives referral tokens
     */
    function notifyReferralTokenTransferReceived($userId, $senderUsername, $amount, $transferId = null)
    {
        try {
            return UserNotification::createForUser($userId, [
                'type' => 'referral_transfer',
                'title' => 'Referral Tokens Received!',
                'message' => "You have received {$amount} referral tokens from {$senderUsername}. Accept the transfer to add them to your account.",
                'icon' => 'fas fa-gift',
                'data' => [
                    'amount' => $amount,
                    'sender' => $senderUsername,
                    'transfer_id' => $transferId,
                    'direction' => 'received',
                    'type' => 'referral_transfer_received',
                    'received_at' => now()->toDateTimeString()
                ],
                'action_url' => route('special.tickets.incoming') ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send referral token transfer received notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyReferralTokenTransferAccepted')) {
    /**
     * Send notification when referral token transfer is accepted
     */
    function notifyReferralTokenTransferAccepted($senderId, $recipientUsername, $amount, $transferId = null)
    {
        try {
            return UserNotification::createForUser($senderId, [
                'type' => 'referral_transfer',
                'title' => 'Transfer Accepted',
                'message' => "{$recipientUsername} has accepted your transfer of {$amount} referral tokens.",
                'icon' => 'fas fa-check-circle',
                'data' => [
                    'amount' => $amount,
                    'recipient' => $recipientUsername,
                    'transfer_id' => $transferId,
                    'status' => 'accepted',
                    'type' => 'referral_transfer_accepted',
                    'accepted_at' => now()->toDateTimeString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send referral token transfer accepted notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyReferralTokenTransferRejected')) {
    /**
     * Send notification when referral token transfer is rejected
     */
    function notifyReferralTokenTransferRejected($senderId, $recipientUsername, $amount, $transferId = null)
    {
        try {
            return UserNotification::createForUser($senderId, [
                'type' => 'referral_transfer',
                'title' => 'Transfer Rejected',
                'message' => "{$recipientUsername} has rejected your transfer of {$amount} referral tokens. The tokens have been returned to your account.",
                'icon' => 'fas fa-times-circle',
                'data' => [
                    'amount' => $amount,
                    'recipient' => $recipientUsername,
                    'transfer_id' => $transferId,
                    'status' => 'rejected',
                    'type' => 'referral_transfer_rejected',
                    'rejected_at' => now()->toDateTimeString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send referral token transfer rejected notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyReferralEarned')) {
    /**
     * Send notification when user earns from referral
     */
    function notifyReferralEarned($userId, $amount, $referredUsername, $level = 1, $source = 'investment')
    {
        try {
            $levelText = $level == 1 ? '1st' : ($level == 2 ? '2nd' : "{$level}th");
            
            return UserNotification::createForUser($userId, [
                'type' => 'referral',
                'title' => 'Referral Earnings!',
                'message' => "You earned " . formatCurrency($amount) . " from {$referredUsername}'s {$source} ({$levelText} level referral).",
                'icon' => 'fas fa-users',
                'data' => [
                    'amount' => $amount,
                    'referred_user' => $referredUsername,
                    'level' => $level,
                    'source' => $source,
                    'type' => 'referral_earned',
                    'earned_at' => now()->toDateTimeString()
                ],
                'action_url' => route('user.refferral-history') ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send referral earned notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyNewReferral')) {
    /**
     * Send notification when user gets a new referral
     */
    function notifyNewReferral($userId, $referredUsername, $referredEmail = null)
    {
        try {
            $message = "Congratulations! {$referredUsername} has joined using your referral link.";
            if ($referredEmail) {
                $message .= " ({$referredEmail})";
            }
            $message .= " You'll earn commissions from their activities.";

            return UserNotification::createForUser($userId, [
                'type' => 'referral',
                'title' => 'New Referral Joined!',
                'message' => $message,
                'icon' => 'fas fa-user-plus',
                'data' => [
                    'referred_user' => $referredUsername,
                    'referred_email' => $referredEmail,
                    'type' => 'new_referral',
                    'joined_at' => now()->toDateTimeString()
                ],
                'action_url' => route('user.sponsor-list') ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send new referral notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifySecurityAlert')) {
    /**
     * Send security alert notification
     */
    function notifySecurityAlert($userId, $type, $details = null, $ipAddress = null)
    {
        try {
            $titles = [
                'login' => 'New Login Detected',
                'password_change' => 'Password Changed',
                'email_change' => 'Email Address Changed',
                'suspicious_activity' => 'Suspicious Activity Detected',
                'account_locked' => 'Account Temporarily Locked'
            ];

            $messages = [
                'login' => 'A new login was detected on your account.',
                'password_change' => 'Your account password has been changed.',
                'email_change' => 'Your account email address has been updated.',
                'suspicious_activity' => 'Suspicious activity has been detected on your account.',
                'account_locked' => 'Your account has been temporarily locked for security reasons.'
            ];

            $message = $messages[$type] ?? 'Security alert for your account.';
            if ($ipAddress) {
                $message .= " IP Address: {$ipAddress}";
            }
            if ($details) {
                $message .= " {$details}";
            }
            $message .= " If this wasn't you, please contact support immediately.";

            return UserNotification::createForUser($userId, [
                'type' => 'security',
                'title' => $titles[$type] ?? 'Security Alert',
                'message' => $message,
                'icon' => 'fas fa-shield-alt',
                'data' => [
                    'security_type' => $type,
                    'ip_address' => $ipAddress,
                    'details' => $details,
                    'type' => 'security_alert',
                    'alert_time' => now()->toDateTimeString()
                ],
                'expires_at' => now()->addDays(30) // Security alerts expire after 30 days
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send security alert notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifySystemMaintenance')) {
    /**
     * Send system maintenance notification
     */
    function notifySystemMaintenance($userIds, $startTime, $endTime, $description = null)
    {
        try {
            $message = "Scheduled system maintenance from {$startTime} to {$endTime}.";
            if ($description) {
                $message .= " {$description}";
            }
            $message .= " Some features may be temporarily unavailable.";

            return notifyMultipleUsers($userIds, 'system', 'Scheduled Maintenance', $message, [
                'icon' => 'fas fa-tools',
                'data' => [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'description' => $description,
                    'type' => 'system_maintenance'
                ],
                'expires_at' => $endTime
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send system maintenance notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyPromotionalOffer')) {
    /**
     * Send promotional offer notification
     */
    function notifyPromotionalOffer($userIds, $title, $message, $actionUrl = null, $expiresAt = null)
    {
        try {
            return notifyMultipleUsers($userIds, 'promotion', $title, $message, [
                'icon' => 'fas fa-gift',
                'data' => [
                    'type' => 'promotional_offer',
                    'sent_at' => now()->toDateTimeString()
                ],
                'action_url' => $actionUrl,
                'expires_at' => $expiresAt
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send promotional offer notification: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('notifyMultipleUsers')) {
    /**
     * Send notification to multiple users
     */
    function notifyMultipleUsers($userIds, $type, $title, $message, $options = [])
    {
        try {
            $data = [
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'icon' => $options['icon'] ?? 'fas fa-info-circle',
                'data' => $options['data'] ?? null,
                'action_url' => $options['action_url'] ?? null,
                'expires_at' => $options['expires_at'] ?? null,
            ];

            return UserNotification::createForUsers($userIds, $data);
        } catch (\Exception $e) {
            Log::error('Failed to send bulk notifications: ' . $e->getMessage());
            return false;
        }
    }
}

// Referral Signup Notification
if (!function_exists('notifyReferralSignup')) {
    /**
     * Send notification when someone joins with referral
     */
    function notifyReferralSignup($referrerId, $newUserUsername, $newUserEmail)
    {
        try {
            return UserNotification::createForUser($referrerId, [
                'type' => 'referral_signup',
                'title' => 'New Referral Signup!',
                'message' => "Great news! {$newUserUsername} has joined using your referral link. You may earn rewards when they become active.",
                'icon' => 'fas fa-users',
                'data' => [
                    'referred_username' => $newUserUsername,
                    'referred_email' => $newUserEmail,
                    'signup_time' => now()->toDateTimeString(),
                    'type' => 'referral_signup'
                ],
                'action_url' => route('user.refferral-history') ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send referral signup notification: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * Send welcome email to newly registered user
 */
if (!function_exists('sendWelcomeEmail')) {
    function sendWelcomeEmail($user)
    {
        try {
            $message = "Welcome to " . siteName() . "! Thank you for joining our platform. We're excited to have you on board!";
            $subject = "Welcome to " . siteName() . "!";
            
            Mail::to($user->email)->send(new \App\Mail\WelcomeEmail($message, $subject, $user)); 
            
            Log::info("Welcome email sent successfully to: {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email to {$user->email}: " . $e->getMessage());
            return false;
        }
    }
}
