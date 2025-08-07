<?php

use App\Services\NotificationService;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Comprehensive Activity Notifications Helper
 * Handles all kinds of necessary activity notifications including:
 * - Sponsor ticket rewards
 * - Bonuses
 * - Fund requests  
 * - Fund success
 * - Video watching income
 * - And more...
 */

if (!function_exists('notifyUser')) {
    /**
     * Send notification to a user
     */
    function notifyUser($userId, $title, $message, $type = 'info', $options = [])
    {
        try {
            $notificationService = new NotificationService();
            
            $data = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'priority' => $options['priority'] ?? 'normal',
                'icon' => $options['icon'] ?? getNotificationIcon($type),
                'action_url' => $options['action_url'] ?? null,
                'action_text' => $options['action_text'] ?? null,
                'metadata' => $options['metadata'] ?? null,
            ];
            
            return $notificationService->createUserNotification($data);
        } catch (\Exception $e) {
            Log::error("Failed to send notification to user {$userId}: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('getNotificationIcon')) {
    /**
     * Get appropriate icon for notification type
     */
    function getNotificationIcon($type)
    {
        return match($type) {
            'sponsor_ticket' => 'fas fa-ticket-alt',
            'bonus' => 'fas fa-gift',
            'fund_request' => 'fas fa-money-bill-wave',
            'fund_success' => 'fas fa-check-circle',
            'video_income' => 'fas fa-play-circle',
            'lottery_win' => 'fas fa-trophy',
            'lottery_refund' => 'fas fa-undo',
            'commission' => 'fas fa-percent',
            'withdrawal' => 'fas fa-credit-card',
            'investment' => 'fas fa-chart-line',
            'referral' => 'fas fa-users',
            'warning' => 'fas fa-exclamation-triangle',
            'error' => 'fas fa-times-circle',
            'success' => 'fas fa-check-circle',
            default => 'fas fa-bell'
        };
    }
}

// =================
// SPONSOR NOTIFICATIONS
// =================

if (!function_exists('notifySponsorTicketReceived')) {
    /**
     * Notify sponsor when they receive lottery tickets
     */
    function notifySponsorTicketReceived($sponsorUserId, $ticketCount, $investorUserId, $investmentAmount)
    {
        $investor = User::find($investorUserId);
        $investorName = $investor ? $investor->username : "User #{$investorUserId}";
        
        $title = "🎫 You Received {$ticketCount} Sponsor Lottery Tickets!";
        $message = "You received {$ticketCount} special lottery tickets as a sponsor reward from {$investorName}'s investment of \${$investmentAmount}. Each ticket gives you a chance to win prizes, and non-winning tickets will refund \$1 each.";
        
        notifyUser($sponsorUserId, $title, $message, 'sponsor_ticket', [
            'priority' => 'high',
            'action_url' => '/user/lottery/my-tickets',
            'action_text' => 'View My Tickets',
            'metadata' => [
                'ticket_count' => $ticketCount,
                'investor_id' => $investorUserId,
                'investment_amount' => $investmentAmount,
                'refund_per_ticket' => 1.00
            ]
        ]);
        
        Log::info("Sponsor ticket notification sent", [
            'sponsor_id' => $sponsorUserId,
            'ticket_count' => $ticketCount,
            'investor_id' => $investorUserId,
            'investment_amount' => $investmentAmount
        ]);
    }
}

if (!function_exists('notifySponsorTicketRefund')) {
    /**
     * Notify sponsor when they receive refund for non-winning tickets
     */
    function notifySponsorTicketRefund($sponsorUserId, $refundAmount, $ticketNumber)
    {
        $title = "💰 Lottery Ticket Refund Received";
        $message = "Your lottery ticket #{$ticketNumber} didn't win the draw, but you received a \${$refundAmount} refund as promised. The refund has been added to your deposit wallet.";
        
        notifyUser($sponsorUserId, $title, $message, 'lottery_refund', [
            'priority' => 'normal',
            'action_url' => '/user/transactions',
            'action_text' => 'View Transactions',
            'metadata' => [
                'refund_amount' => $refundAmount,
                'ticket_number' => $ticketNumber,
                'wallet_type' => 'deposit_wallet'
            ]
        ]);
    }
}

// =================
// BONUS NOTIFICATIONS
// =================

if (!function_exists('notifyBonusReceived')) {
    /**
     * Notify user when they receive any kind of bonus
     */
    function notifyBonusReceived($userId, $bonusAmount, $bonusType, $description = null)
    {
        $title = "🎁 Bonus Received: \${$bonusAmount}";
        $message = $description ?: "You have received a {$bonusType} bonus of \${$bonusAmount}. The bonus has been added to your account.";
        
        notifyUser($userId, $title, $message, 'bonus', [
            'priority' => 'high',
            'action_url' => '/user/transactions',
            'action_text' => 'View Transactions',
            'metadata' => [
                'bonus_amount' => $bonusAmount,
                'bonus_type' => $bonusType
            ]
        ]);
    }
}

if (!function_exists('notifyReferralBonus')) {
    /**
     * Notify user when they receive referral bonus
     */
    function notifyReferralBonus($userId, $bonusAmount, $referredUserId, $level = 1)
    {
        $referredUser = User::find($referredUserId);
        $referredName = $referredUser ? $referredUser->username : "User #{$referredUserId}";
        
        $title = "👥 Referral Bonus: \${$bonusAmount}";
        $message = "You earned a Level {$level} referral bonus of \${$bonusAmount} from {$referredName}'s investment. Keep referring to earn more!";
        
        notifyUser($userId, $title, $message, 'referral', [
            'priority' => 'high',
            'action_url' => '/user/referrals',
            'action_text' => 'View Referrals',
            'metadata' => [
                'bonus_amount' => $bonusAmount,
                'referred_user_id' => $referredUserId,
                'level' => $level
            ]
        ]);
    }
}

// =================
// FUND NOTIFICATIONS
// =================

if (!function_exists('notifyFundRequest')) {
    /**
     * Notify admin when user requests fund deposit
     */
    function notifyFundRequest($userId, $amount, $method, $depositId)
    {
        $user = User::find($userId);
        $userName = $user ? $user->username : "User #{$userId}";
        
        // Notify user
        $userTitle = "📝 Deposit Request Submitted";
        $userMessage = "Your deposit request of \${$amount} via {$method} has been submitted and is pending admin approval. You will be notified once it's processed.";
        
        notifyUser($userId, $userTitle, $userMessage, 'fund_request', [
            'priority' => 'normal',
            'action_url' => '/user/deposits',
            'action_text' => 'View Deposits',
            'metadata' => [
                'amount' => $amount,
                'method' => $method,
                'deposit_id' => $depositId,
                'status' => 'pending'
            ]
        ]);
        
        // Also notify admin (if admin notification system exists)
        Log::info("Fund request notification sent", [
            'user_id' => $userId,
            'amount' => $amount,
            'method' => $method,
            'deposit_id' => $depositId
        ]);
    }
}

if (!function_exists('notifyFundSuccess')) {
    /**
     * Notify user when their deposit is successful
     */
    function notifyFundSuccess($userId, $amount, $method, $depositId)
    {
        $title = "✅ Deposit Successful: \${$amount}";
        $message = "Your deposit of \${$amount} via {$method} has been successfully processed and added to your account. You can now start investing!";
        
        notifyUser($userId, $title, $message, 'fund_success', [
            'priority' => 'high',
            'action_url' => '/user/dashboard',
            'action_text' => 'View Dashboard',
            'metadata' => [
                'amount' => $amount,
                'method' => $method,
                'deposit_id' => $depositId,
                'status' => 'completed'
            ]
        ]);
    }
}

// =================
// VIDEO WATCHING NOTIFICATIONS
// =================

if (!function_exists('notifyVideoWatchingIncome')) {
    /**
     * Notify user when they earn from watching videos
     */
    function notifyVideoWatchingIncome($userId, $income, $videoTitle = null, $watchDuration = null)
    {
        $title = "📺 Video Watching Income: \${$income}";
        $message = $videoTitle 
            ? "You earned \${$income} for watching '{$videoTitle}'. Keep watching to earn more!"
            : "You earned \${$income} from video watching. Keep it up!";
        
        notifyUser($userId, $title, $message, 'video_income', [
            'priority' => 'normal',
            'action_url' => '/user/videos',
            'action_text' => 'Watch More Videos',
            'metadata' => [
                'income' => $income,
                'video_title' => $videoTitle,
                'watch_duration' => $watchDuration
            ]
        ]);
    }
}

if (!function_exists('notifyDailyVideoQuota')) {
    /**
     * Notify user about their daily video watching quota
     */
    function notifyDailyVideoQuota($userId, $videosWatched, $quotaLimit, $totalEarnings)
    {
        if ($videosWatched >= $quotaLimit) {
            $title = "🎯 Daily Video Quota Completed!";
            $message = "Congratulations! You've watched all {$quotaLimit} videos for today and earned \${$totalEarnings}. Come back tomorrow for more videos!";
        } else {
            $remaining = $quotaLimit - $videosWatched;
            $title = "📊 Daily Video Progress";
            $message = "You've watched {$videosWatched}/{$quotaLimit} videos today and earned \${$totalEarnings}. {$remaining} videos remaining to complete your daily quota!";
        }
        
        notifyUser($userId, $title, $message, 'video_income', [
            'priority' => 'normal',
            'action_url' => '/user/videos',
            'action_text' => 'Continue Watching',
            'metadata' => [
                'videos_watched' => $videosWatched,
                'quota_limit' => $quotaLimit,
                'total_earnings' => $totalEarnings,
                'remaining' => $quotaLimit - $videosWatched
            ]
        ]);
    }
}

// =================
// INVESTMENT NOTIFICATIONS
// =================

if (!function_exists('notifyInvestmentSuccess')) {
    /**
     * Notify user when their investment is successful
     */
    function notifyInvestmentSuccess($userId, $planName, $amount, $expectedReturn, $duration)
    {
        $title = "📈 Investment Successful: {$planName}";
        $message = "You have successfully invested \${$amount} in {$planName}. Expected return: \${$expectedReturn} over {$duration} days. Your investment is now active!";
        
        notifyUser($userId, $title, $message, 'investment', [
            'priority' => 'high',
            'action_url' => '/user/investments',
            'action_text' => 'View Investments',
            'metadata' => [
                'plan_name' => $planName,
                'amount' => $amount,
                'expected_return' => $expectedReturn,
                'duration' => $duration
            ]
        ]);
    }
}

if (!function_exists('notifyInvestmentMatured')) {
    /**
     * Notify user when their investment matures
     */
    function notifyInvestmentMatured($userId, $planName, $principalAmount, $profitAmount, $totalAmount)
    {
        $title = "🎉 Investment Matured: {$planName}";
        $message = "Your investment in {$planName} has matured! Principal: \${$principalAmount}, Profit: \${$profitAmount}, Total: \${$totalAmount}. The amount has been added to your account.";
        
        notifyUser($userId, $title, $message, 'success', [
            'priority' => 'high',
            'action_url' => '/user/investments',
            'action_text' => 'View Investments',
            'metadata' => [
                'plan_name' => $planName,
                'principal_amount' => $principalAmount,
                'profit_amount' => $profitAmount,
                'total_amount' => $totalAmount
            ]
        ]);
    }
}

// =================
// LOTTERY NOTIFICATIONS
// =================

if (!function_exists('notifyLotteryWin')) {
    /**
     * Notify user when they win lottery
     */
    function notifyLotteryWin($userId, $prizeAmount, $position, $ticketNumber)
    {
        $positionText = match($position) {
            1 => '1st Place (First Prize)',
            2 => '2nd Place (Second Prize)', 
            3 => '3rd Place (Third Prize)',
            default => "Position {$position}"
        };
        
        $title = "🏆 Lottery Winner - {$positionText}!";
        $message = "Congratulations! Your lottery ticket #{$ticketNumber} won {$positionText} and you've received \${$prizeAmount}! The prize has been added to your account.";
        
        notifyUser($userId, $title, $message, 'lottery_win', [
            'priority' => 'high',
            'action_url' => '/user/lottery/my-tickets',
            'action_text' => 'View My Tickets',
            'metadata' => [
                'prize_amount' => $prizeAmount,
                'position' => $position,
                'ticket_number' => $ticketNumber
            ]
        ]);
    }
}

if (!function_exists('notifyLotteryTicketPurchase')) {
    /**
     * Notify user when they purchase lottery tickets
     */
    function notifyLotteryTicketPurchase($userId, $ticketCount, $totalCost, $bonusTickets = 0, $drawDate = null)
    {
        $title = "🎫 Lottery Tickets Purchased!";
        $message = "You successfully purchased {$ticketCount} lottery ticket(s) for \${$totalCost}";
        
        if ($drawDate) {
            $message .= " for the {$drawDate} draw";
        }
        
        if ($bonusTickets > 0) {
            $message .= ". Plus you received {$bonusTickets} bonus ticket(s) free!";
        }
        
        $message .= ". Good luck in the draw!";
        
        notifyUser($userId, $title, $message, 'success', [
            'priority' => 'normal',
            'action_url' => '/user/lottery/my-tickets',
            'action_text' => 'View My Tickets',
            'metadata' => [
                'tickets_purchased' => $ticketCount,
                'bonus_tickets' => $bonusTickets,
                'total_cost' => $totalCost,
                'draw_date' => $drawDate
            ]
        ]);
    }
}

// =================
// WITHDRAWAL NOTIFICATIONS  
// =================

if (!function_exists('notifyWithdrawalRequest')) {
    /**
     * Notify user when withdrawal request is submitted
     */
    function notifyWithdrawalRequest($userId, $amount, $method, $withdrawalId)
    {
        $title = "💳 Withdrawal Request Submitted";
        $message = "Your withdrawal request of \${$amount} via {$method} has been submitted and is pending admin approval. You will be notified once it's processed.";
        
        notifyUser($userId, $title, $message, 'withdrawal', [
            'priority' => 'normal',
            'action_url' => '/user/withdrawals',
            'action_text' => 'View Withdrawals',
            'metadata' => [
                'amount' => $amount,
                'method' => $method,
                'withdrawal_id' => $withdrawalId,
                'status' => 'pending'
            ]
        ]);
    }
}

if (!function_exists('notifyWithdrawalSuccess')) {
    /**
     * Notify user when withdrawal is successful
     */
    function notifyWithdrawalSuccess($userId, $amount, $method, $withdrawalId)
    {
        $title = "✅ Withdrawal Successful: \${$amount}";
        $message = "Your withdrawal of \${$amount} via {$method} has been successfully processed and sent to your account.";
        
        notifyUser($userId, $title, $message, 'success', [
            'priority' => 'high',
            'action_url' => '/user/withdrawals',
            'action_text' => 'View Withdrawals',
            'metadata' => [
                'amount' => $amount,
                'method' => $method,
                'withdrawal_id' => $withdrawalId,
                'status' => 'completed'
            ]
        ]);
    }
}

// =================
// COMMISSION NOTIFICATIONS
// =================

if (!function_exists('notifyCommissionEarned')) {
    /**
     * Notify user when they earn commission
     */
    function notifyCommissionEarned($userId, $commissionAmount, $fromUserId, $commissionType, $level = null)
    {
        $fromUser = User::find($fromUserId);
        $fromUserName = $fromUser ? $fromUser->username : "User #{$fromUserId}";
        
        $levelText = $level ? " (Level {$level})" : "";
        $title = "💰 Commission Earned{$levelText}: \${$commissionAmount}";
        $message = "You earned \${$commissionAmount} {$commissionType} commission from {$fromUserName}'s activity. Commission has been added to your account.";
        
        notifyUser($userId, $title, $message, 'commission', [
            'priority' => 'high',
            'action_url' => '/user/commissions',
            'action_text' => 'View Commissions',
            'metadata' => [
                'commission_amount' => $commissionAmount,
                'from_user_id' => $fromUserId,
                'commission_type' => $commissionType,
                'level' => $level
            ]
        ]);
    }
}

// =================
// GENERAL SYSTEM NOTIFICATIONS
// =================

if (!function_exists('notifySystemUpdate')) {
    /**
     * Notify user about system updates or maintenance
     */
    function notifySystemUpdate($userId, $title, $message, $urgency = 'normal')
    {
        notifyUser($userId, $title, $message, 'info', [
            'priority' => $urgency,
            'icon' => 'fas fa-cog'
        ]);
    }
}

if (!function_exists('notifyAccountSecurity')) {
    /**
     * Notify user about account security events
     */
    function notifyAccountSecurity($userId, $event, $details = null)
    {
        $title = "🔒 Account Security Alert";
        $message = $details ?: "Security event detected: {$event}. Please review your account activity.";
        
        notifyUser($userId, $title, $message, 'warning', [
            'priority' => 'high',
            'action_url' => '/user/security',
            'action_text' => 'Review Security',
            'metadata' => [
                'security_event' => $event,
                'details' => $details
            ]
        ]);
    }
}
