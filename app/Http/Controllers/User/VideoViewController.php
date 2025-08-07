<?php

namespace App\Http\Controllers\user;

use App\Models\VideoLink;
use App\Models\VideoView;
use App\Models\Invest;
use App\Models\Plan;
use App\Models\DailyVideoAssignment;
use App\Services\DailyVideoService;
use App\Services\DailyVideoViewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class VideoViewController extends Controller 
{
    /**
     * Redirect to the new daily video gallery
     */
    public function index(Request $request) 
    {
        return redirect()->route('user.video-views.gallery');
    }

    /**
     * Handle video watch request
     */
    public function watch(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to earn money from videos.'
            ], 401);
        }

        $request->validate([
            'video_id' => 'required|exists:video_links,id',
            'watch_duration' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $video = VideoLink::findOrFail($request->video_id);
        
        // Check if user has today's assignment that includes this video (optimized JSON structure)
        $assignment = DailyVideoAssignment::forUser($user->id)
            ->forDate(today())
            ->first();
            
        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'You don\'t have any video assignments for today.'
            ]);
        }
        
        // Parse video IDs from JSON structure
        $videoIds = json_decode($assignment->video_ids ?? '[]', true) ?: [];
        $watchedVideoIds = json_decode($assignment->watched_video_ids ?? '[]', true) ?: [];
        
        // Check if this video is in today's assigned videos
        if (!in_array($video->id, $videoIds)) {
            return response()->json([
                'success' => false,
                'message' => 'This video is not assigned to you for today.'
            ]);
        }
        
        // Check if user has already watched this video today
        if (in_array($video->id, $watchedVideoIds)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already earned from this video today.'
            ]);
        }
        
        // Get user's active investment for earning rate
        $activeInvest = Invest::where('user_id', $user->id)->where('status', 1)->with('plan')->first();
        $earningRate = $activeInvest && $activeInvest->plan 
            ? $activeInvest->plan->video_earning_rate 
            : 0.0001;

        // Calculate minimum watch time based on video duration
        $videoDuration = (int) $video->duration ?: 30;
        $minimumWatchTime = min(max(ceil($videoDuration * 0.8), 15), $videoDuration);
        
        if ($request->watch_duration < $minimumWatchTime) {
            return response()->json([
                'success' => false,
                'message' => "Please watch at least {$minimumWatchTime} seconds of the video to earn money."
            ]);
        }

        try {
            DB::beginTransaction();

            // Use optimized single-row video view system
            $videoViewService = new DailyVideoViewService();
            
            // Check if video was already watched today using optimized method
            if ($videoViewService->hasWatchedVideoToday($user, $video->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already earned from this video today.'
                ]);
            }

            // Record video view in optimized single-row format
            $viewResult = $videoViewService->recordVideoView($user, $video, [
                'ip_address' => $request->ip(),
                'device_info' => $request->userAgent(),
                'earned_amount' => $earningRate,
                'watch_duration' => $request->watch_duration
            ]);

            if (!$viewResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $viewResult['error'] ?? 'Failed to record video view.'
                ]);
            }

            // Mark video as watched in the optimized JSON structure
            $videoService = new \App\Services\DailyVideoService();
            $videoService->markVideoWatched($user, $video->id, $earningRate);

            // Update video statistics
            $video->increment('views_count');
            $video->increment('clicks_count');

            // Add earnings using daily aggregation service (ONE TRANSACTION PER DAY)
            $earningService = new \App\Services\DailyVideoEarningService();
            $earningResult = $earningService->addEarning($user, $earningRate);
            
            if (!$earningResult['success']) {
                throw new \Exception('Failed to process daily earnings: ' . $earningResult['error']);
            }
            
            $currentBalance = $earningResult['current_balance'];

            DB::commit();

            // Get today's stats using optimized methods
            $todaysViewingSummary = $videoViewService->getTodaysViewingSummary($user->id);
            $todayEarnings = $earningService->getTodaysTotalEarnings($user);

            // Send video watching income notification
            try {
                notifyVideoWatchingIncome($user->id, $earningRate, $video->title, $request->watch_duration);
                
                // Check if daily quota is completed and send quota notification
                $assignedVideos = count(json_decode($assignment->video_ids ?? '[]', true) ?: []);
                if ($todaysViewingSummary['total_videos'] >= $assignedVideos) {
                    notifyDailyVideoQuota($user->id, $todaysViewingSummary['total_videos'], $assignedVideos, $todayEarnings);
                }
            } catch (\Exception $e) {
                Log::error("Failed to send video watching notification: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Congratulations! You earned $' . number_format($earningRate, 4) . ' from today\'s video!',
                'earned_amount' => $earningRate,
                'total_earnings' => $currentBalance,
                'user_balance' => $currentBalance,
                'today_earnings' => $todayEarnings,
                'todays_views' => $todaysViewingSummary['total_videos'], // From optimized single row
                'remaining_videos' => max(0, count(json_decode($assignment->video_ids ?? '[]', true) ?: []) - $todaysViewingSummary['total_videos'])
                // 'optimization_info' => [
                //     'single_row_system' => true,
                //     'total_videos_today' => $viewResult['total_videos_today'],
                //     'total_earned_today' => $viewResult['total_earned_today']
                // ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording optimized video view: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your view.'
            ], 500);
        }
    }

    /**
     * Display detailed earnings report
     */
    public function earnings(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $videoViewService = new DailyVideoViewService();
        
        // Get statistics using optimized single-row system
        $totalEarnings = $videoViewService->getUserTotalEarnings($user);
        
        // Get daily summary records for analysis
        $dailySummaries = VideoView::where('user_id', $user->id)
            ->where('view_type', 'daily_summary')
            ->orderBy('view_date', 'desc')
            ->get();
        
        // Overall statistics from optimized records
        $overallStats = [
            'total_videos_watched' => $dailySummaries->sum('total_videos'),
            'total_earnings' => $totalEarnings,
            'average_per_video' => $dailySummaries->sum('total_videos') > 0 ? 
                $totalEarnings / $dailySummaries->sum('total_videos') : 0,
            'highest_earning' => $dailySummaries->max('total_earned') ?? 0,
            'total_days_active' => $dailySummaries->count()
        ];

        // Time-based statistics using optimized records
        $todaySummary = $videoViewService->getTodaysViewingSummary($user->id);
        $yesterdaySummary = $videoViewService->getTodaysViewingSummary($user->id, now()->subDay());
        $weekStats = $videoViewService->getViewingStats($user, now()->startOfWeek(), now()->endOfWeek());
        $monthStats = $videoViewService->getViewingStats($user, now()->startOfMonth(), now()->endOfMonth());
        
        $timeStats = [
            'today' => [
                'videos' => $todaySummary['total_videos'] ?? 0,
                'earnings' => $todaySummary['total_earned'] ?? 0,
            ],
            'yesterday' => [
                'videos' => $yesterdaySummary['total_videos'] ?? 0,
                'earnings' => $yesterdaySummary['total_earned'] ?? 0,
            ],
            'this_week' => [
                'videos' => $weekStats['total_videos'] ?? 0,
                'earnings' => $weekStats['total_earnings'] ?? 0,
            ],
            'this_month' => [
                'videos' => $monthStats['total_videos'] ?? 0,
                'earnings' => $monthStats['total_earnings'] ?? 0,
            ]
        ];

        // Category-wise earnings from JSON data in daily summaries
        $categoryEarnings = [];
        foreach ($dailySummaries as $summary) {
            $videoData = json_decode($summary->video_data ?? '[]', true) ?: [];
            foreach ($videoData as $video) {
                $category = $video['category'] ?? 'general';
                if (!isset($categoryEarnings[$category])) {
                    $categoryEarnings[$category] = [
                        'category' => $category,
                        'video_count' => 0,
                        'total_earnings' => 0
                    ];
                }
                $categoryEarnings[$category]['video_count']++;
                $categoryEarnings[$category]['total_earnings'] += $video['earned_amount'] ?? 0;
            }
        }
        
        // Sort by earnings
        usort($categoryEarnings, function($a, $b) {
            return $b['total_earnings'] <=> $a['total_earnings'];
        });

        // Monthly earnings chart data (last 12 months) from optimized records
        $monthlyEarnings = VideoView::where('user_id', $user->id)
            ->where('view_type', 'daily_summary')
            ->where('view_date', '>=', now()->subMonths(12))
            ->selectRaw('YEAR(view_date) as year, MONTH(view_date) as month, SUM(total_earned) as total_earnings, SUM(total_videos) as total_videos')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Daily earnings for current month from optimized records
        $dailyEarnings = VideoView::where('user_id', $user->id)
            ->where('view_type', 'daily_summary')
            ->whereMonth('view_date', now()->month)
            ->whereYear('view_date', now()->year)
            ->selectRaw('DAY(view_date) as day, total_earned as total_earnings, total_videos')
            ->orderBy('view_date', 'asc')
            ->get();

        // Recent high-earning days from optimized records
        $topEarningDays = VideoView::where('user_id', $user->id)
            ->where('view_type', 'daily_summary')
            ->orderBy('total_earned', 'desc')
            ->limit(10)
            ->get()
            ->map(function($summary) {
                $videoData = json_decode($summary->video_data ?? '[]', true) ?: [];
                return [
                    'date' => $summary->view_date,
                    'total_earned' => $summary->total_earned,
                    'total_videos' => $summary->total_videos,
                    'videos_watched' => array_values($videoData)
                ];
            });

        $data = [
            'pageTitle' => 'Video Earnings Report',
            'overallStats' => $overallStats,
            'timeStats' => $timeStats,
            'categoryEarnings' => $categoryEarnings,
            'monthlyEarnings' => $monthlyEarnings,
            'dailyEarnings' => $dailyEarnings,
            'topEarningDays' => $topEarningDays
            // 'optimization_info' => [
            //     'system_type' => 'Single Row Per Day',
            //     'records_count' => $dailySummaries->count(),
            //     'vs_old_system' => 'Up to 95% fewer database rows'
            // ]
        ];

        return view('frontend.video-views.earnings', $data);
    }
    public function gallery()
    {
        $user = Auth::user();
        
        // Check if user is logged in
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to access videos.');
        }
        
        
        // Check if today's assignments already exist
        $todayAssignments = DailyVideoAssignment::forUser($user->id)
            ->forDate(today())
            ->count();

        $videoService = new DailyVideoService();
        if ($todayAssignments > 0) {
            $result = $videoService->getTodaysVideos($user);
        } else {
            // No assignments for today, create them
            $result = $videoService->assignDailyVideos($user);
        }

        // Get user's total video earnings using optimized system
        $videoViewService = new DailyVideoViewService();
        $userTotalEarnings = $videoViewService->getUserTotalEarnings($user);

        // Check if user has active investment
        $activeInvest = Invest::where('user_id', $user->id)->where('status', 1)->with('plan')->first();
        $hasActiveInvestment = $activeInvest !== null;

        $data = [
            'pageTitle' => 'Daily Video Gallery - Fresh Videos Every Day!',
            'videos' => $result['videos'] ?? [],
            'userStats' => $result['stats'] ?? [],
            'userTotalEarnings' => $userTotalEarnings,
            'hasActiveInvestment' => $hasActiveInvestment, 
            'message' => $result['message'] ?? ''
        ];

        return view('frontend.video-views.gallery', $data);
    }

    /**
     * Record video view and add earnings
     */
    public function recordView(Request $request, $videoId)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to earn money from video views.'
                ], 401);
            }

            $video = VideoLink::findOrFail($videoId);
            $user = Auth::user();
            $ipAddress = $request->ip();

            // Check if user already viewed this video from this IP
            $existingView = VideoView::where([
                'user_id' => $user->id,
                'video_link_id' => $video->id,
                'ip_address' => $ipAddress
            ])->first();

            if ($existingView) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already earned from this video.'
                ]);
            }

            DB::beginTransaction();

            // Create video view record
            $videoView = VideoView::create([
                'user_id' => $user->id,
                'video_link_id' => $video->id,
                'ip_address' => $ipAddress,
                'device_info' => $request->userAgent(),
                'earned_amount' => $video->cost_per_click,
                'viewed_at' => now()
            ]);

            // Update video statistics
            $video->increment('views_count');
            $video->increment('clicks_count');

            // Add earnings to user balance (using deposit_wallet since balance isn't in the table)
            DB::table('users')->where('id', $user->id)->increment('deposit_wallet', $video->cost_per_click);

            DB::commit();

            Log::info('Video view recorded', [
                'user_id' => $user->id,
                'video_id' => $video->id,
                'earned_amount' => $video->cost_per_click,
                'ip_address' => $ipAddress
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Congratulations! You earned $' . number_format($video->cost_per_click, 4),
                'earned_amount' => $video->cost_per_click,
                'total_earnings' => $this->getUserTotalEarnings()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording video view: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your view.'
            ], 500);
        }
    }

    /**
     * Get user's total earnings from video views
     */
    private function getUserTotalEarnings()
    {
        if (!Auth::check()) {
            return 0;
        }

        return VideoView::where('user_id', Auth::id())
            ->sum('earned_amount');
    }

    /**
     * Get user's video viewing history
     */
    // public function viewingHistory()
    // {
    //     if (!Auth::check()) {
    //         return redirect()->route('login');
    //     }

    //     $viewHistory = VideoView::with('videoLink')
    //         ->where('user_id', Auth::id())
    //         ->orderBy('viewed_at', 'desc')
    //         ->paginate(20);

    //     // Calculate stats for the view
    //     $totalEarnings = VideoView::where('user_id', Auth::id())->sum('earned_amount');
    //     $totalVideosWatched = VideoView::where('user_id', Auth::id())->count();
    //     $uniqueVideosWatched = VideoView::where('user_id', Auth::id())->distinct('video_link_id')->count();
    //     $averagePerVideo = $totalVideosWatched > 0 ? $totalEarnings / $totalVideosWatched : 0;
    //     $todayEarnings = VideoView::where('user_id', Auth::id())
    //         ->whereDate('viewed_at', today())
    //         ->sum('earned_amount');
    //     $thisWeekEarnings = VideoView::where('user_id', Auth::id())
    //         ->whereBetween('viewed_at', [now()->startOfWeek(), now()->endOfWeek()])
    //         ->sum('earned_amount');
    //     $thisMonthEarnings = VideoView::where('user_id', Auth::id())
    //         ->whereMonth('viewed_at', now()->month)
    //         ->whereYear('viewed_at', now()->year)
    //         ->sum('earned_amount');
    //     $thisMonthVideos = VideoView::where('user_id', Auth::id())
    //         ->whereMonth('viewed_at', now()->month)
    //         ->whereYear('viewed_at', now()->year)
    //         ->count();
    //     $recentActivity = VideoView::where('user_id', Auth::id())
    //         ->where('viewed_at', '>=', now()->subDays(7))
    //         ->count();
    //     $topEarningVideos = VideoView::where('user_id', Auth::id())
    //         ->with('videoLink')
    //         ->selectRaw('video_link_id, SUM(earned_amount) as total_earned, COUNT(*) as view_count')
    //         ->groupBy('video_link_id')
    //         ->orderByDesc('total_earned')
    //         ->limit(5)
    //         ->get();
    //     $dailyEarnings = VideoView::where('user_id', Auth::id())
    //         ->where('viewed_at', '>=', now()->subDays(30))
    //         ->selectRaw('DATE(viewed_at) as date, SUM(earned_amount) as daily_total, COUNT(*) as videos_count')
    //         ->groupBy('date')
    //         ->orderBy('date', 'desc')
    //         ->get();

    //     $data = [
    //         'pageTitle' => 'Video Viewing History',
    //         'viewHistory' => $viewHistory,
    //         'totalEarnings' => $totalEarnings,
    //         'totalVideosWatched' => $totalVideosWatched,
    //         'uniqueVideosWatched' => $uniqueVideosWatched,
    //         'averagePerVideo' => $averagePerVideo,
    //         'todayEarnings' => $todayEarnings,
    //         'thisWeekEarnings' => $thisWeekEarnings,
    //         'thisMonthEarnings' => $thisMonthEarnings,
    //         'thisMonthVideos' => $thisMonthVideos,
    //         'recentActivity' => $recentActivity,
    //         'topEarningVideos' => $topEarningVideos,
    //         'dailyEarnings' => $dailyEarnings
    //     ];

    //     return view('frontend.video-views.user-video-history', $data);
    // }

    /**
     * Display user's video viewing history with detailed stats
     */
    public function history(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Initialize optimized services
        $dailyVideoViewService = new \App\Services\DailyVideoViewService();
        $dailyVideoEarningService = new \App\Services\DailyVideoEarningService();
        
        // Get optimized video viewing history
        $videoHistory = $dailyVideoViewService->getDetailedViewingHistory($user->id, $request);
        
        // Get optimized statistics using our services
        $todaysSummary = $dailyVideoViewService->getTodaysViewingSummary($user->id);
        $totalEarnings = $dailyVideoEarningService->getTotalEarnings($user->id);
        $weeklyStats = $dailyVideoViewService->getWeeklyStats($user->id);
        
        // Calculate statistics using optimized data
        $stats = [
            'total_videos_watched' => $dailyVideoViewService->getTotalVideosWatched($user->id),
            'total_earnings' => $totalEarnings,
            'today_videos' => $todaysSummary['total_videos'] ?? 0,
            'today_earnings' => $todaysSummary['total_earnings'] ?? 0,
            'this_week_videos' => $weeklyStats['total_videos'] ?? 0,
            'this_week_earnings' => $weeklyStats['total_earnings'] ?? 0,
        ];

        // Get optimized daily earnings for chart (last 30 days)
        $dailyEarnings = $dailyVideoViewService->getDailyEarningsChart($user->id, 30);

        $data = [
            'pageTitle' => 'Video Viewing History',
            'videoHistory' => $videoHistory,
            'stats' => $stats,
            'dailyEarnings' => $dailyEarnings,
            'filters' => $request->only(['date_from', 'date_to'])
            // 'optimization_info' => [
            //     'system_version' => 'v2.0 Optimized',
            //     'efficiency_gain' => '95%',
            //     'performance_boost' => '3x faster'
            // ]
        ];

        return view('frontend.video-views.history', $data);
    }

    /**
     * Display a specific video for watching
     */
    public function show(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $video = VideoLink::where('status', 'active')->findOrFail($id);
        
        // Get today's earnings for display
        $todayEarnings = VideoView::where('user_id', $user->id)
            ->whereDate('viewed_at', today())
            ->sum('earned_amount');

        $data = [
            'pageTitle' => 'Watch Video - ' . ($video->title ?: 'Earn Money'),
            'video' => $video,
            'todayEarnings' => $todayEarnings
        ];

        return view('frontend.video-views.watch', $data);
    }

    /**
     * Get video statistics
     */
    public function videoStats($videoId)
    {
        $video = VideoLink::with(['views' => function($query) {
            $query->orderBy('viewed_at', 'desc')->limit(10);
        }])->findOrFail($videoId);

        return response()->json([
            'video' => $video,
            'recent_views' => $video->views,
            'user_viewed' => Auth::check() ? $video->userViews()->exists() : false
        ]);
    }
    public function publicGallery()
    {
        $videos = VideoLink::where('status', 'active')
            ->orderBy('views_count', 'desc')
            ->paginate(12);

        $data = [
            'pageTitle' => 'Public Video Gallery',
            'videos' => $videos,
            'totalVideos' => VideoLink::where('status', 'active')->count(),
            'totalViews' => VideoLink::sum('views_count'),
            'totalEarningsPaid' => VideoLink::sum('cost_per_click')
        ];

        return view('frontend.public-gallery', $data);
    }
    

}
