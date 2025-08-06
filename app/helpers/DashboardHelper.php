<?php

use App\Models\User;
use App\Models\Invest;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Models\VideoView;
use App\Services\DailyVideoViewService;
use App\Services\DailyVideoEarningService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

if (!function_exists('getDashboardData')) {
    /**
     * Get comprehensive dashboard data with caching
     */
    function getDashboardData($userId = null, $useCache = true)
    {
        $userId = $userId ?? Auth::id();
        $cacheKey = "dashboard_data_{$userId}";
        $cacheDuration = 300; // 5 minutes

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        $data = [
            // User Basic Info
            'user' => $user,
            'current_balance' => $user->deposit_wallet + $user->interest_wallet,
            'deposit_wallet' => $user->deposit_wallet,
            'interest_wallet' => $user->interest_wallet,
            
            // Investment Data
            'investments' => getInvestmentData($userId),
            
            // Transaction Data
            'transactions' => getTransactionData($userId),
            
            // Referral Data
            'referrals' => getReferralData($userId),
            
            // Video System Data
            'video_system' => getVideoSystemData($userId),
            
            // Recent Activities
            'recent_activities' => getRecentActivities($userId),
            
            // Statistics
            'statistics' => getDashboardStatistics($userId),
            
            // Performance Metrics
            'performance' => getPerformanceMetrics($userId),
        ];

        if ($useCache) {
            Cache::put($cacheKey, $data, $cacheDuration);
        }

        return $data;
    }
}

if (!function_exists('getInvestmentData')) {
    /**
     * Get investment related data with optimized queries
     */
    function getInvestmentData($userId)
    {
        // Single query with calculated columns to reduce database hits
        $investmentStats = DB::table('invests')
            ->where('user_id', $userId)
            ->selectRaw('
                SUM(amount) as total_invested,
                SUM(CASE WHEN status = 1 THEN amount ELSE 0 END) as running_investments,
                SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as completed_investments,
                SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) as pending_investments,
                COUNT(*) as investment_count
            ')
            ->first();

        // Get interest earnings in a separate optimized query
        $interestStats = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('remark', 'interest')
            ->selectRaw('
                SUM(amount) as total_interest_earned,
                SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) THEN amount ELSE 0 END) as monthly_interest
            ')
            ->first();

        return [
            'total_invested' => $investmentStats->total_invested ?? 0,
            'running_investments' => $investmentStats->running_investments ?? 0,
            'completed_investments' => $investmentStats->completed_investments ?? 0,
            'pending_investments' => $investmentStats->pending_investments ?? 0,
            'total_interest_earned' => $interestStats->total_interest_earned ?? 0,
            'monthly_interest' => $interestStats->monthly_interest ?? 0,
            'active_plans' => getActivePlans($userId),
            'investment_count' => $investmentStats->investment_count ?? 0,
        ];
    }
}

if (!function_exists('getTransactionData')) {
    /**
     * Get transaction related data with optimized queries
     */
    function getTransactionData($userId)
    {
        // Get recent transactions with eager loading
        $transactions = Transaction::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Single query for deposit statistics
        $depositStats = DB::table('deposits')
            ->where('user_id', $userId)
            ->selectRaw('
                SUM(CASE WHEN status = 1 THEN amount ELSE 0 END) as total_deposits,
                SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) as pending_deposits
            ')
            ->first();

        // Single query for withdrawal statistics  
        $withdrawalStats = DB::table('withdrawals')
            ->where('user_id', $userId)
            ->selectRaw('
                SUM(CASE WHEN status = 1 THEN amount ELSE 0 END) as total_withdrawals,
                SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) as pending_withdrawals
            ')
            ->first();

        // Get balance transfer data in single query
        $balanceStats = DB::table('transactions')
            ->where('user_id', $userId)
            ->whereIn('remark', ['balance_transfer', 'balance_received'])
            ->selectRaw('
                SUM(CASE WHEN remark = "balance_transfer" THEN amount ELSE 0 END) as balance_transferred,
                SUM(CASE WHEN remark = "balance_received" THEN amount ELSE 0 END) as balance_received
            ')
            ->first();

        // Get last transactions efficiently
        $lastDeposit = DB::table('deposits')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->first();

        $lastWithdrawal = DB::table('withdrawals')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->first();

        return [
            'recent_transactions' => $transactions,
            'total_deposits' => $depositStats->total_deposits ?? 0,
            'total_withdrawals' => $withdrawalStats->total_withdrawals ?? 0,
            'pending_deposits' => $depositStats->pending_deposits ?? 0,
            'pending_withdrawals' => $withdrawalStats->pending_withdrawals ?? 0,
            'last_deposit' => $lastDeposit,
            'last_withdrawal' => $lastWithdrawal,
            'balance_transferred' => $balanceStats->balance_transferred ?? 0,
            'balance_received' => $balanceStats->balance_received ?? 0,
        ];
    }
}

if (!function_exists('getReferralData')) {
    /**
     * Get referral related data with optimized queries
     */
    function getReferralData($userId)
    {
        $user = User::find($userId);
        
        // Single query for referral counts and earnings
        $referralStats = DB::table('users as referrals')
            ->leftJoin('transactions', function($join) use ($userId) {
                $join->on('transactions.user_id', '=', DB::raw($userId))
                     ->where('transactions.remark', '=', 'referral_commission');
            })
            ->where('referrals.ref_by', $userId)
            ->selectRaw('
                COUNT(referrals.id) as total_referrals,
                COUNT(CASE WHEN referrals.status = 1 THEN 1 END) as active_referrals,
                COALESCE(SUM(transactions.amount), 0) as referral_earnings,
                COALESCE(SUM(CASE WHEN MONTH(transactions.created_at) = MONTH(NOW()) AND YEAR(transactions.created_at) = YEAR(NOW()) THEN transactions.amount ELSE 0 END), 0) as monthly_referral_earnings
            ')
            ->first();

        // Get recent referrals with limited data
        $recentReferrals = DB::table('users')
            ->where('ref_by', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->select(['id', 'firstname', 'lastname', 'username', 'status', 'created_at'])
            ->get();

        return [
            'total_referrals' => $referralStats->total_referrals ?? 0,
            'active_referrals' => $referralStats->active_referrals ?? 0,
            'referral_earnings' => $referralStats->referral_earnings ?? 0,
            'monthly_referral_earnings' => $referralStats->monthly_referral_earnings ?? 0,
            'referral_link' => $user->getReferralLink(),
            'recent_referrals' => $recentReferrals,
            'referral_levels' => getReferralLevels($userId),
        ];
    }
}

if (!function_exists('getVideoSystemData')) {
    /**
     * Get video system related data with optimized queries
     */
    function getVideoSystemData($userId)
    {
        $user = User::find($userId);
        $activePlan = $user->getHighestActivePlan();
        $dailyLimit = $user->getDailyVideoLimit();
        
        // Use optimized single-row video view system
        $videoViewService = new \App\Services\DailyVideoViewService();
        $earningService = new \App\Services\DailyVideoEarningService();
        
        // Get today's viewing summary from optimized system
        $todaysSummary = $videoViewService->getTodaysViewingSummary($userId);
        $todayViews = $todaysSummary['total_videos'] ?? 0;
        $todayEarnings = $todaysSummary['total_earned'] ?? 0;
        
        // Get total video statistics using optimized queries
        $totalEarnings = $videoViewService->getUserTotalEarnings($user);
        
        // Get total videos watched from optimized daily summary records
        $totalVideosWatched = $videoViewService->getTotalVideosWatched($userId);
        
        // Get monthly earnings from daily summary records
        $monthlyVideoEarnings = DB::table('video_views')
            ->where('user_id', $userId)
            ->where('view_type', 'daily_summary')
            ->whereMonth('view_date', now()->month)
            ->whereYear('view_date', now()->year)
            ->sum('total_earned');
        
        // Get earnings from transaction aggregation system
        $transactionVideoEarnings = $earningService->getTodaysTotalEarnings($user);
        
        // Use the higher value between video_views and transactions for accuracy
        $combinedVideoEarnings = max($totalEarnings, $transactionVideoEarnings);

        return [
            'activePlan' => $activePlan,
            'active_plan' => $activePlan,
            'dailyLimit' => $dailyLimit,
            'daily_limit' => $dailyLimit,
            'todayViews' => $todayViews,
            'today_views' => $todayViews,
            'todayEarnings' => $todayEarnings,
            'today_earnings' => $todayEarnings,
            'remainingViews' => max(0, $dailyLimit - $todayViews),
            'remaining_views' => max(0, $dailyLimit - $todayViews),
            'totalVideosWatched' => $totalVideosWatched ?: 0,
            'total_videos_watched' => $totalVideosWatched ?: 0,
            'videoEarnings' => $combinedVideoEarnings,
            'video_earnings' => $combinedVideoEarnings,
            'monthlyVideoEarnings' => $monthlyVideoEarnings ?: 0,
            'monthly_video_earnings' => $monthlyVideoEarnings ?: 0,
            'videoRate' => $activePlan ? $activePlan->video_earning_rate : 0,
            'video_rate' => $activePlan ? $activePlan->video_earning_rate : 0,
            'pendingVideoEarnings' => $todayEarnings,
            'pending_video_earnings' => $todayEarnings
            // 'optimization_info' => [
            //     'system_type' => 'Single-Row Optimized',
            //     'today_summary' => $todaysSummary,
            //     'space_efficiency' => '95% database reduction'
            // ]
        ];
    }
}

if (!function_exists('getRecentActivities')) {
    /**
     * Get recent user activities with optimized queries
     */
    function getRecentActivities($userId, $limit = 10)
    {
        $activities = [];

        // Recent investments - get with plan name in single query
        $investments = DB::table('invests')
            ->join('plans', 'invests.plan_id', '=', 'plans.id')
            ->where('invests.user_id', $userId)
            ->orderBy('invests.created_at', 'desc')
            ->take(3)
            ->select([
                'invests.amount',
                'invests.status',
                'invests.created_at',
                'plans.name as plan_name'
            ])
            ->get()
            ->map(function($invest) {
                return [
                    'type' => 'investment',
                    'amount' => $invest->amount,
                    'description' => "Invested in {$invest->plan_name}",
                    'created_at' => $invest->created_at,
                    'status' => $invest->status,
                ];
            });

        // Recent transactions - optimized query
        $transactions = DB::table('transactions')
            ->where('user_id', $userId)
            ->whereIn('remark', ['deposit', 'withdrawal', 'interest', 'referral_commission'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->select(['amount', 'remark', 'trx_type', 'created_at'])
            ->get()
            ->map(function($transaction) {
                return [
                    'type' => 'transaction',
                    'amount' => $transaction->amount,
                    'description' => ucfirst(str_replace('_', ' ', $transaction->remark)),
                    'created_at' => $transaction->created_at,
                    'trx_type' => $transaction->trx_type,
                ];
            });

        $activities = collect($investments)->merge($transactions)
            ->sortByDesc('created_at')
            ->take($limit)
            ->values();

        return $activities;
    }
}

if (!function_exists('getDashboardStatistics')) {
    /**
     * Get dashboard statistics with optimized queries
     */
    function getDashboardStatistics($userId)
    {
        $user = User::find($userId);
        
        // Single comprehensive query for all earnings and profits
        $earningsStats = DB::table('transactions')
            ->where('user_id', $userId)
            ->whereIn('remark', ['interest', 'referral_commission', 'video_earning'])
            ->selectRaw('
                SUM(amount) as total_profit,
                SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) THEN amount ELSE 0 END) as monthly_profit,
                SUM(CASE WHEN MONTH(created_at) = MONTH(NOW() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(NOW() - INTERVAL 1 MONTH) THEN amount ELSE 0 END) as last_month_profit
            ')
            ->first();

        // Get total invested for ROI calculation
        $totalInvested = DB::table('invests')
            ->where('user_id', $userId)
            ->sum('amount');

        $monthlyProfit = $earningsStats->monthly_profit ?? 0;
        $lastMonthProfit = $earningsStats->last_month_profit ?? 0;
        $totalProfit = $earningsStats->total_profit ?? 0;

        // Calculate growth percentage
        $growthPercentage = 0;
        if ($lastMonthProfit > 0) {
            $growthPercentage = round((($monthlyProfit - $lastMonthProfit) / $lastMonthProfit) * 100, 2);
        } elseif ($monthlyProfit > 0) {
            $growthPercentage = 100;
        }

        // Calculate ROI percentage
        $roiPercentage = 0;
        if ($totalInvested > 0) {
            $roiPercentage = round(($totalProfit / $totalInvested) * 100, 2);
        }

        return [
            'growth_percentage' => $growthPercentage,
            'monthly_profit' => $monthlyProfit,
            'total_profit' => $totalProfit,
            'account_age_days' => $user->created_at->diffInDays(now()),
            'last_login' => $user->last_login_at ?? $user->updated_at,
            'roi_percentage' => $roiPercentage,
        ];
    }
}

if (!function_exists('getPerformanceMetrics')) {
    /**
     * Get performance metrics for charts with optimized queries
     */
    function getPerformanceMetrics($userId)
    {
        $last30Days = Carbon::now()->subDays(30);
        
        return [
            'daily_earnings' => DB::table('transactions')
                ->where('user_id', $userId)
                ->whereIn('remark', ['interest', 'referral_commission', 'video_earning'])
                ->where('created_at', '>=', $last30Days)
                ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date")
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'monthly_comparison' => getMonthlyComparison($userId),
            'category_breakdown' => getCategoryBreakdown($userId),
        ];
    }
}

if (!function_exists('calculateGrowthPercentage')) {
    /**
     * Calculate growth percentage
     */
    function calculateGrowthPercentage($userId)
    {
        $currentMonth = Transaction::where('user_id', $userId)
            ->whereIn('remark', ['interest', 'referral_commission', 'video_earning'])
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $lastMonth = Transaction::where('user_id', $userId)
            ->whereIn('remark', ['interest', 'referral_commission', 'video_earning'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('amount');

        if ($lastMonth == 0) return $currentMonth > 0 ? 100 : 0;

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 2);
    }
}

if (!function_exists('calculateROI')) {
    /**
     * Calculate Return on Investment
     */
    function calculateROI($userId)
    {
        $totalInvested = Invest::where('user_id', $userId)->sum('amount');
        $totalEarned = Transaction::where('user_id', $userId)
            ->whereIn('remark', ['interest', 'referral_commission', 'video_earning'])
            ->sum('amount');

        if ($totalInvested == 0) return 0;

        return round(($totalEarned / $totalInvested) * 100, 2);
    }
}

if (!function_exists('getActivePlans')) {
    /**
     * Get user's active investment plans
     */
    function getActivePlans($userId)
    {
        return Invest::with('plan')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->get()
            ->groupBy('plan_id')
            ->map(function($investments) {
                $plan = $investments->first()->plan;
                return [
                    'plan' => $plan,
                    'total_invested' => $investments->sum('amount'),
                    'count' => $investments->count(),
                ];
            });
    }
}

if (!function_exists('getReferralLevels')) {
    /**
     * Get referral levels data
     */
    function getReferralLevels($userId)
    {
        $user = User::find($userId);
        $levels = [];

        // For now, let's implement a simple approach without the getReferralsByLevel method
        // We'll calculate based on direct referrals and their referrals
        
        // Level 1: Direct referrals
        $level1Users = $user->referrals;
        $levels[1] = [
            'count' => $level1Users->count(),
            'earnings' => Transaction::where('user_id', $userId)
                ->where('remark', 'referral_commission')
                ->where('details', 'like', '%Level 1%')
                ->sum('amount'),
        ];

        // Level 2: Referrals of referrals
        $level2Count = 0;
        foreach ($level1Users as $level1User) {
            $level2Count += $level1User->referrals()->count();
        }
        
        $levels[2] = [
            'count' => $level2Count,
            'earnings' => Transaction::where('user_id', $userId)
                ->where('remark', 'referral_commission')
                ->where('details', 'like', '%Level 2%')
                ->sum('amount'),
        ];

        // For levels 3-5, we'll use basic calculations or set to 0 for now
        for ($i = 3; $i <= 5; $i++) {
            $levels[$i] = [
                'count' => 0,
                'earnings' => Transaction::where('user_id', $userId)
                    ->where('remark', 'referral_commission')
                    ->where('details', 'like', "%Level {$i}%")
                    ->sum('amount'),
            ];
        }

        return $levels;
    }
}

if (!function_exists('getMonthlyComparison')) {
    /**
     * Get monthly comparison data
     */
    function getMonthlyComparison($userId)
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $earnings = Transaction::where('user_id', $userId)
                ->whereIn('remark', ['interest', 'referral_commission', 'video_earning'])
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('amount');

            $months[] = [
                'month' => $month->format('M Y'),
                'earnings' => $earnings,
            ];
        }

        return $months;
    }
}

if (!function_exists('getCategoryBreakdown')) {
    /**
     * Get earnings category breakdown
     */
    function getCategoryBreakdown($userId)
    {
        return [
            'investment_interest' => Transaction::where('user_id', $userId)
                ->where('remark', 'interest')->sum('amount'),
            'referral_commission' => Transaction::where('user_id', $userId)
                ->where('remark', 'referral_commission')->sum('amount'),
            'video_earnings' => Transaction::where('user_id', $userId)
                ->where('remark', 'video_earning')->sum('amount'),
            'bonus_earnings' => Transaction::where('user_id', $userId)
                ->where('remark', 'bonus')->sum('amount'),
        ];
    }
}

if (!function_exists('clearDashboardCache')) {
    /**
     * Clear dashboard cache for a user
     */
    function clearDashboardCache($userId)
    {
        Cache::forget("dashboard_data_{$userId}");
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format currency with proper decimals
     */
    function formatCurrency($amount, $currency = '$')
    {
        return $currency . number_format($amount, 2);
    }
}

if (!function_exists('getPercentageChange')) {
    /**
     * Calculate percentage change between two values
     */
    function getPercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
    }
}

if (!function_exists('getDashboardQuickStats')) {
    /**
     * Get quick stats for dashboard cards
     */
    function getDashboardQuickStats($userId)
    {
        $cacheKey = "quick_stats_{$userId}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $user = User::find($userId);
        $stats = [
            'current_balance' => $user->deposit_wallet + $user->interest_wallet,
            'team_bonus' => Transaction::where('user_id', $userId)
                ->where('remark', 'referral_commission')->sum('amount'),
            'interest_wallet' => $user->interest_wallet,
            'total_investment' => Invest::where('user_id', $userId)->sum('amount'),
            'today_earnings' => Transaction::where('user_id', $userId)
                ->whereIn('remark', ['interest', 'referral_commission', 'video_earning'])
                ->whereDate('created_at', today())
                ->sum('amount'),
            'monthly_earnings' => Transaction::where('user_id', $userId)
                ->whereIn('remark', ['interest', 'referral_commission', 'video_earning'])
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        Cache::put($cacheKey, $stats, 60); // Cache for 1 minute
        return $stats;
    }
}
