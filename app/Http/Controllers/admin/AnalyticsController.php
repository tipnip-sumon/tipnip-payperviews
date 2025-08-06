<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VideoLink;
use App\Models\Invest;
use App\Models\Plan;
use App\Models\Deposit;
use App\Models\Withdraw;
use App\Models\Transaction;
use App\Models\UserVideoView;
use App\Models\LotteryTicket;
use App\Models\LotteryDraw;
use App\Models\AdminNotification;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['ok-user', 'prevent-back']);
    }

    /**
     * Main analytics dashboard
     */
    public function index()
    {
        $data = [
            'overview' => $this->getOverviewStats(),
            'userStats' => $this->getUserStats(),
            'revenueStats' => $this->getRevenueStats(),
            'videoStats' => $this->getVideoStats(),
            'investmentStats' => $this->getInvestmentStats(),
            'chartData' => $this->getChartData(),
        ];

        return view('admin.analytics.index', compact('data'));
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', $today)->count(),
            'active_users_today' => User::whereDate('last_login_at', $today)->count(),
            'total_revenue' => Deposit::where('status', 1)->sum('amount'),
            'revenue_today' => Deposit::where('status', 1)->whereDate('created_at', $today)->sum('amount'),
            'total_withdrawals' => Withdraw::where('status', 1)->sum('amount'),
            'pending_withdrawals' => Withdraw::where('status', 0)->sum('amount'),
            'total_videos' => VideoLink::count(),
            'videos_watched_today' => UserVideoView::whereDate('created_at', $today)->count(),
            'total_investments' => Invest::where('status', 1)->sum('amount'),
            'active_investments' => Invest::where('status', 1)->count(),
            'lottery_tickets_sold' => LotteryTicket::count(),
            'lottery_revenue' => LotteryTicket::sum('ticket_price'),
            'support_tickets_open' => SupportTicket::whereIn('status', ['open', 'pending'])->count(),
            'user_growth_rate' => $this->calculateGrowthRate(
                User::whereDate('created_at', $yesterday)->count(),
                User::whereDate('created_at', $today)->count()
            ),
            'revenue_growth_rate' => $this->calculateGrowthRate(
                Deposit::where('status', 1)->whereDate('created_at', $yesterday)->sum('amount'),
                Deposit::where('status', 1)->whereDate('created_at', $today)->sum('amount')
            ),
        ];
    }

    /**
     * Get user statistics
     */
    private function getUserStats()
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'total_users' => User::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'kyc_verified_users' => User::where('kv', 1)->count(),
            'active_users_30_days' => User::where('last_login_at', '>=', Carbon::now()->subDays(30))->count(),
            'users_by_country' => User::select('country', DB::raw('count(*) as total'))
                ->groupBy('country')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get(),
            'user_registrations_trend' => User::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as count')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'user_activity_levels' => [
                'highly_active' => User::where('last_login_at', '>=', Carbon::now()->subDays(7))->count(),
                'moderately_active' => User::whereBetween('last_login_at', [Carbon::now()->subDays(30), Carbon::now()->subDays(7)])->count(),
                'inactive' => User::where('last_login_at', '<', Carbon::now()->subDays(30))->orWhereNull('last_login_at')->count(),
            ],
        ];
    }

    /**
     * Get revenue statistics
     */
    private function getRevenueStats()
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth();

        return [
            'total_revenue' => Deposit::where('status', 1)->sum('amount'),
            'revenue_this_month' => Deposit::where('status', 1)->where('created_at', '>=', $thisMonth)->sum('amount'),
            'revenue_last_month' => Deposit::where('status', 1)
                ->whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
                ->sum('amount'),
            'total_withdrawals' => Withdraw::where('status', 1)->sum('amount'),
            'pending_withdrawals' => Withdraw::where('status', 0)->sum('amount'),
            'net_profit' => Deposit::where('status', 1)->sum('amount') - Withdraw::where('status', 1)->sum('amount'),
            'revenue_by_gateway' => Deposit::select('gateways.name as gateway_name', DB::raw('sum(deposits.amount) as total'))
                ->leftJoin('gateways', 'deposits.method_code', '=', 'gateways.code')
                ->where('deposits.status', 1)
                ->groupBy('gateways.name')
                ->get(),
            'daily_revenue_trend' => Deposit::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('sum(amount) as total')
                )
                ->where('status', 1)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'monthly_comparison' => [
                'deposits' => [
                    'this_month' => Deposit::where('status', 1)->where('created_at', '>=', $thisMonth)->sum('amount'),
                    'last_month' => Deposit::where('status', 1)
                        ->whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
                        ->sum('amount'),
                ],
                'withdrawals' => [
                    'this_month' => Withdraw::where('status', 1)->where('created_at', '>=', $thisMonth)->sum('amount'),
                    'last_month' => Withdraw::where('status', 1)
                        ->whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
                        ->sum('amount'),
                ],
            ],
        ];
    }

    /**
     * Get video statistics
     */
    private function getVideoStats()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();

        return [
            'total_videos' => VideoLink::count(),
            'active_videos' => VideoLink::where('status', 1)->count(),
            'total_views' => UserVideoView::count(),
            'views_today' => UserVideoView::whereDate('created_at', $today)->count(),
            'views_this_week' => UserVideoView::where('created_at', '>=', $thisWeek)->count(),
            'total_earnings_paid' => UserVideoView::sum('earning_amount'),
            'most_watched_videos' => VideoLink::select('video_links.id', 'video_links.title', 'video_links.video_url', 'video_links.cost_per_click', DB::raw('count(user_video_views.id) as view_count'))
                ->leftJoin('user_video_views', 'video_links.id', '=', 'user_video_views.video_link_id')
                ->groupBy('video_links.id', 'video_links.title', 'video_links.video_url', 'video_links.cost_per_click')
                ->orderBy('view_count', 'desc')
                ->limit(10)
                ->get(),
            'video_views_trend' => UserVideoView::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as views'),
                    DB::raw('sum(earning_amount) as earnings')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'video_categories_performance' => VideoLink::select('category', 
                    DB::raw('count(*) as video_count'),
                    DB::raw('avg(cost_per_click) as avg_earning')
                )
                ->groupBy('category')
                ->get(),
        ];
    }

    /**
     * Get investment statistics
     */
    private function getInvestmentStats()
    {
        return [
            'total_investments' => Invest::where('status', 1)->sum('amount'),
            'active_investments' => Invest::where('status', 1)->count(),
            'completed_investments' => Invest::where('status', 2)->count(),
            'total_returns_paid' => Invest::where('status', 2)->sum('should_pay'),
            'investments_by_plan' => Plan::select('plans.id', 'plans.name', 'plans.minimum', 'plans.maximum', 'plans.fixed_amount', 'plans.interest', 'plans.interest_type', 'plans.status', 
                    DB::raw('count(invests.id) as investment_count'),
                    DB::raw('sum(invests.amount) as total_invested')
                )
                ->leftJoin('invests', 'plans.id', '=', 'invests.plan_id')
                ->where('invests.status', 1)
                ->groupBy('plans.id', 'plans.name', 'plans.minimum', 'plans.maximum', 'plans.fixed_amount', 'plans.interest', 'plans.interest_type', 'plans.status')
                ->get(),
            'investment_trend' => Invest::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as count'),
                    DB::raw('sum(amount) as total')
                )
                ->where('status', 1)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'plan_popularity' => Plan::select('name', 
                    DB::raw('count(invests.id) as popularity_score')
                )
                ->leftJoin('invests', 'plans.id', '=', 'invests.plan_id')
                ->groupBy('plans.id', 'plans.name')
                ->orderBy('popularity_score', 'desc')
                ->get(),
        ];
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData($period = 'month')
    {
        // Determine number of days based on period
        $days = match($period) {
            'today' => 1,
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 30
        };
        
        $dates = collect();
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates->push(Carbon::now()->subDays($i)->format('Y-m-d'));
        }

        // User registrations chart
        $userRegistrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->pluck('count', 'date');

        // Revenue chart
        $dailyRevenue = Deposit::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('sum(amount) as total')
            )
            ->where('status', 1)
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->pluck('total', 'date');

        // Video views chart
        $videoViews = UserVideoView::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->pluck('count', 'date');

        // Investment chart
        $dailyInvestments = Invest::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count'),
                DB::raw('sum(amount) as total')
            )
            ->where('status', 1)
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Withdrawals chart
        $dailyWithdrawals = Withdraw::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count'),
                DB::raw('sum(amount) as total')
            )
            ->where('status', 1)
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Video earnings chart
        $videoEarnings = UserVideoView::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('sum(earning_amount) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->pluck('total', 'date');

        // Active users chart
        $activeUsers = User::select(
                DB::raw('DATE(last_login_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('last_login_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->pluck('count', 'date');

        return [
            'dates' => $dates->values()->toArray(),
            'user_registrations' => $dates->map(fn($date) => $userRegistrations->get($date, 0))->values()->toArray(),
            'daily_revenue' => $dates->map(fn($date) => $dailyRevenue->get($date, 0))->values()->toArray(),
            'video_views' => $dates->map(fn($date) => $videoViews->get($date, 0))->values()->toArray(),
            'daily_investments_count' => $dates->map(fn($date) => optional($dailyInvestments->get($date))->count ?? 0)->values()->toArray(),
            'daily_investments_amount' => $dates->map(fn($date) => optional($dailyInvestments->get($date))->total ?? 0)->values()->toArray(),
            'daily_withdrawals_count' => $dates->map(fn($date) => optional($dailyWithdrawals->get($date))->count ?? 0)->values()->toArray(),
            'daily_withdrawals_amount' => $dates->map(fn($date) => optional($dailyWithdrawals->get($date))->total ?? 0)->values()->toArray(),
            'video_earnings' => $dates->map(fn($date) => $videoEarnings->get($date, 0))->values()->toArray(),
            'active_users' => $dates->map(fn($date) => $activeUsers->get($date, 0))->values()->toArray(),
            'summary' => [
                'total_period_users' => $userRegistrations->sum(),
                'total_period_revenue' => $dailyRevenue->sum(),
                'total_period_views' => $videoViews->sum(),
                'total_period_investments' => $dailyInvestments->sum('total'),
                'total_period_withdrawals' => $dailyWithdrawals->sum('total'),
                'total_period_earnings' => $videoEarnings->sum(),
                'avg_daily_users' => round($userRegistrations->avg(), 2),
                'avg_daily_revenue' => round($dailyRevenue->avg(), 2),
                'avg_daily_views' => round($videoViews->avg(), 2),
            ]
        ];
    }

    /**
     * Calculate growth rate
     */
    private function calculateGrowthRate($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Get detailed analytics by date range
     */
    public function getAnalyticsByDateRange(Request $request)
    {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        return response()->json([
            'users' => [
                'registrations' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
                'active' => User::whereBetween('last_login_at', [$startDate, $endDate])->count(),
            ],
            'revenue' => [
                'deposits' => Deposit::where('status', 1)->whereBetween('created_at', [$startDate, $endDate])->sum('amount'),
                'withdrawals' => Withdraw::where('status', 1)->whereBetween('created_at', [$startDate, $endDate])->sum('amount'),
            ],
            'videos' => [
                'views' => UserVideoView::whereBetween('created_at', [$startDate, $endDate])->count(),
                'earnings' => UserVideoView::whereBetween('created_at', [$startDate, $endDate])->sum('earning_amount'),
            ],
            'investments' => [
                'count' => Invest::where('status', 1)->whereBetween('created_at', [$startDate, $endDate])->count(),
                'amount' => Invest::where('status', 1)->whereBetween('created_at', [$startDate, $endDate])->sum('amount'),
            ]
        ]);
    }

    /**
     * Export analytics data
     */
    public function exportAnalytics(Request $request)
    {
        $format = $request->format ?? 'csv';
        $data = $this->getOverviewStats();
        
        if ($format === 'pdf') {
            return $this->exportToPdf($data);
        } else {
            return $this->exportToCsv($data);
        }
    }

    /**
     * Real-time analytics data
     */
    public function realtimeData()
    {
        $today = Carbon::today();
        
        return response()->json([
            'online_users' => User::where('last_activity', '>=', Carbon::now()->subMinutes(5))->count(),
            'users_today' => User::whereDate('created_at', $today)->count(),
            'revenue_today' => Deposit::where('status', 1)->whereDate('created_at', $today)->sum('amount'),
            'views_today' => UserVideoView::whereDate('created_at', $today)->count(),
            'investments_today' => Invest::where('status', 1)->whereDate('created_at', $today)->count(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get performance metrics
     */
    public function performanceMetrics()
    {
        $data = [
            'user_retention' => $this->calculateUserRetention(),
            'conversion_rates' => $this->calculateConversionRates(),
            'average_session_duration' => $this->calculateAverageSessionDuration(),
            'bounce_rate' => $this->calculateBounceRate(),
            'lifetime_value' => $this->calculateCustomerLifetimeValue(),
        ];

        return view('admin.analytics.performance', compact('data'));
    }

    /**
     * Calculate user retention rate
     */
    private function calculateUserRetention()
    {
        $totalUsers = User::count();
        $activeUsersLastWeek = User::where('last_login_at', '>=', Carbon::now()->subWeek())->count();
        $activeUsersLastMonth = User::where('last_login_at', '>=', Carbon::now()->subMonth())->count();
        
        return [
            'weekly' => $totalUsers > 0 ? round(($activeUsersLastWeek / $totalUsers) * 100, 2) : 0,
            'monthly' => $totalUsers > 0 ? round(($activeUsersLastMonth / $totalUsers) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate conversion rates
     */
    private function calculateConversionRates()
    {
        $totalUsers = User::count();
        $investingUsers = User::whereHas('invests')->count();
        $activeVideoWatchers = User::whereHas('videoViews')->count();
        
        return [
            'user_to_investor' => $totalUsers > 0 ? round(($investingUsers / $totalUsers) * 100, 2) : 0,
            'user_to_video_watcher' => $totalUsers > 0 ? round(($activeVideoWatchers / $totalUsers) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate average session duration (placeholder)
     */
    private function calculateAverageSessionDuration()
    {
        // This would require session tracking implementation
        return [
            'minutes' => 25, // Placeholder
            'trend' => 'up'
        ];
    }

    /**
     * Calculate bounce rate (placeholder)
     */
    private function calculateBounceRate()
    {
        // This would require page view tracking
        return [
            'rate' => 35.6, // Placeholder
            'trend' => 'down'
        ];
    }

    /**
     * Calculate customer lifetime value
     */
    private function calculateCustomerLifetimeValue()
    {
        $avgUserLifespan = 365; // days (placeholder)
        $avgDailyRevenue = Deposit::where('status', 1)->sum('amount') / max(1, User::count());
        
        return [
            'amount' => round($avgDailyRevenue * $avgUserLifespan, 2),
            'currency' => 'USD'
        ];
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($data)
    {
        $filename = 'analytics_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Metric', 'Value']);
            
            // Data
            foreach ($data as $key => $value) {
                if (is_numeric($value)) {
                    fputcsv($file, [ucwords(str_replace('_', ' ', $key)), $value]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get lottery analytics
     */
    public function lotteryAnalytics()
    {
        $data = [
            'total_tickets_sold' => LotteryTicket::count(),
            'total_revenue' => LotteryTicket::sum('ticket_price'),
            'total_draws' => LotteryDraw::count(),
            'active_draws' => LotteryDraw::where('status', 'active')->count(),
            'tickets_by_draw' => LotteryDraw::select('lottery_draws.id', 'lottery_draws.draw_number', 'lottery_draws.draw_date', 'lottery_draws.status', 'lottery_draws.total_prize_pool', 'lottery_draws.total_tickets_sold',
                    DB::raw('count(lottery_tickets.id) as ticket_count'),
                    DB::raw('sum(lottery_tickets.ticket_price) as revenue')
                )
                ->leftJoin('lottery_tickets', 'lottery_draws.id', '=', 'lottery_tickets.lottery_draw_id')
                ->groupBy('lottery_draws.id', 'lottery_draws.draw_number', 'lottery_draws.draw_date', 'lottery_draws.status', 'lottery_draws.total_prize_pool', 'lottery_draws.total_tickets_sold')
                ->orderBy('revenue', 'desc')
                ->get(),
            'sales_trend' => LotteryTicket::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as tickets'),
                    DB::raw('sum(price) as revenue')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return view('admin.analytics.lottery', compact('data'));
    }

    /**
     * Save analytics settings
     */
    public function saveSettings(Request $request)
    {
        try {
            $settings = $request->validate([
                'enable_realtime' => 'boolean',
                'enable_notifications' => 'boolean',
                'enable_auto_refresh' => 'boolean',
                'chart_data_period' => 'integer|min:1|max:365',
                'refresh_interval' => 'integer|min:10|max:300',
                'export_users' => 'boolean',
                'export_revenue' => 'boolean',
                'export_videos' => 'boolean'
            ]);

            // Store settings in cache or database
            cache()->put('analytics_settings', $settings, now()->addDays(30));

            return response()->json([
                'success' => true,
                'message' => 'Analytics settings saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * User analytics page
     */
    public function userAnalytics()
    {
        $data = [
            'userStats' => $this->getUserStats(),
            'chartData' => $this->getChartData(),
        ];

        return view('admin.analytics.users', compact('data'));
    }

    /**
     * Revenue analytics page
     */
    public function revenueAnalytics()
    {
        $data = [
            'revenueStats' => $this->getRevenueStats(),
            'chartData' => $this->getChartData(),
        ];

        return view('admin.analytics.revenue', compact('data'));
    }

    /**
     * Video analytics page
     */
    public function videoAnalytics()
    {
        $data = [
            'videoStats' => $this->getVideoStats(),
            'chartData' => $this->getChartData(),
        ];

        return view('admin.analytics.videos', compact('data'));
    }

    /**
     * Investment analytics page
     */
    public function investmentAnalytics()
    {
        $data = [
            'investmentStats' => $this->getInvestmentStats(),
            'chartData' => $this->getChartData(),
        ];

        return view('admin.analytics.investments', compact('data'));
    }

    /**
     * Chart data endpoint
     */
    public function chartData(Request $request)
    {
        $period = $request->get('period', 'month');
        return response()->json($this->getChartData($period));
    }

    /**
     * Chart data page view
     */
    public function chartPage()
    {
        $pageTitle = 'Analytics Chart Data';
        // Get initial chart data
        $chartData = $this->getChartData('month');
        return view('admin.analytics.chart-data', compact('pageTitle', 'chartData'));
    }

    /**
     * Chart dashboard page
     */
    public function chartDashboard()
    {
        $pageTitle = 'Analytics Dashboard';
        
        // Get initial dashboard data
        $gs = new \stdClass();
        $gs->totalUsers = \App\Models\User::count();
        $gs->totalRevenue = \App\Models\Deposit::where('status', 1)->sum('amount');
        $gs->totalVideos = \App\Models\VideoLink::count();
        $gs->activePlans = \App\Models\Plan::where('video_access_enabled', true)->count();
        
        return view('admin.analytics.dashboard', compact('pageTitle', 'gs'));
    }
}
