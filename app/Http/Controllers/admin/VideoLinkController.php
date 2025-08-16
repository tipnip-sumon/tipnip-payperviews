<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VideoLink;
use App\Models\VideoView;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VideoLinkController extends Controller
{
    /**
     * Display video gallery 
     */
    public function gallery()
    {
        $videos = VideoLink::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'pageTitle' => 'Video Gallery - Earn Money by Watching',
            'videos' => $videos,
            'userTotalEarnings' => $this->getUserTotalEarnings()
        ];

        return view('frontend.gallery', $data);
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
                'user_agent' => $request->userAgent(),
                'earned_amount' => $video->cost_per_click,
                'viewed_at' => now()
            ]);

            // Update video statistics
            $video->increment('views_count');
            $video->increment('clicks_count');

            // Add earnings to user balance
            DB::table('users')->where('id', $user->id)->increment('balance', $video->cost_per_click);

            // Get the updated user balance
            $updatedUser = User::find($user->id);

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
                'total_earnings' => $this->getUserTotalEarnings(),
                'user_balance' => $updatedUser->balance
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

    // /**
    //  * Get user's video viewing history
    //  */
    // public function viewingHistory(Request $request)
    // {
    //     if (!Auth::check()) {
    //         return redirect()->route('login');
    //     }

    //     $userId = Auth::id();
        
    //     // Base query for video views
    //     $query = VideoView::with(['videoLink'])
    //         ->where('user_id', $userId)
    //         ->orderBy('viewed_at', 'desc');

    //     // Apply filters
    //     if ($request->filled('date_from')) {
    //         $query->whereDate('viewed_at', '>=', $request->date_from);
    //     }

    //     if ($request->filled('date_to')) {
    //         $query->whereDate('viewed_at', '<=', $request->date_to);
    //     }

    //     if ($request->filled('search')) {
    //         $query->whereHas('videoLink', function($q) use ($request) {
    //             $q->where('title', 'like', '%' . $request->search . '%')
    //               ->orWhere('description', 'like', '%' . $request->search . '%');
    //         });
    //     }

    //     // Handle CSV export
    //     if ($request->get('export') === 'csv') {
    //         return $this->exportHistoryToCsv($query->get());
    //     }

    //     // Get paginated results
    //     $viewHistory = $query->paginate(20);
        
    //     // Calculate comprehensive statistics
    //     $totalEarnings = VideoView::where('user_id', $userId)->sum('earned_amount');
    //     $totalVideosWatched = VideoView::where('user_id', $userId)->count();
    //     $uniqueVideosWatched = VideoView::where('user_id', $userId)->distinct('video_link_id')->count();
        
    //     // Time-based statistics
    //     $todayEarnings = VideoView::where('user_id', $userId)
    //         ->whereDate('viewed_at', today())
    //         ->sum('earned_amount');
            
    //     $thisWeekEarnings = VideoView::where('user_id', $userId)
    //         ->whereBetween('viewed_at', [now()->startOfWeek(), now()->endOfWeek()])
    //         ->sum('earned_amount');
            
    //     $thisMonthEarnings = VideoView::where('user_id', $userId)
    //         ->whereMonth('viewed_at', now()->month)
    //         ->whereYear('viewed_at', now()->year)
    //         ->sum('earned_amount');
            
    //     $thisMonthVideos = VideoView::where('user_id', $userId)
    //         ->whereMonth('viewed_at', now()->month)
    //         ->whereYear('viewed_at', now()->year)
    //         ->count();

    //     // Average earnings per video
    //     $averagePerVideo = $totalVideosWatched > 0 ? $totalEarnings / $totalVideosWatched : 0;
        
    //     // Recent activity (last 7 days)
    //     $recentActivity = VideoView::where('user_id', $userId)
    //         ->where('viewed_at', '>=', now()->subDays(7))
    //         ->count();
            
    //     // Top earning videos
    //     $topEarningVideos = VideoView::where('user_id', $userId)
    //         ->with('videoLink')
    //         ->selectRaw('video_link_id, SUM(earned_amount) as total_earned, COUNT(*) as view_count')
    //         ->groupBy('video_link_id')
    //         ->orderByDesc('total_earned')
    //         ->limit(5)
    //         ->get();

    //     // Daily earnings for chart (last 30 days)
    //     $dailyEarnings = VideoView::where('user_id', $userId)
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
    //         'dailyEarnings' => $dailyEarnings,
    //         'filters' => [
    //             'date_from' => $request->get('date_from'),
    //             'date_to' => $request->get('date_to'),
    //             'search' => $request->get('search')
    //         ]
    //     ];

    //     return view('frontend.video-history', $data);
    // }

    /**
     * Export history to CSV
     */
    private function exportHistoryToCsv($viewHistory)
    {
        $filename = 'video-history-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($viewHistory) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Video Title',
                'Watched Date',
                'Earned Amount',
                'IP Address',
                'User Agent'
            ]);

            // CSV data
            foreach ($viewHistory as $view) {
                fputcsv($file, [
                    $view->videoLink->title ?? 'N/A',
                    $view->viewed_at->format('Y-m-d H:i:s'),
                    number_format($view->earned_amount, 4),
                    $view->ip_address,
                    $view->user_agent ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

    /**
     * Show user's video earnings
     */
    public function videoEarnings()
    {
        $user = Auth::user();
        $userId = $user->id;
        
        $totalEarnings = VideoView::where('user_id', $userId)->sum('earned_amount');
        $todayEarnings = VideoView::where('user_id', $userId)->whereDate('viewed_at', today())->sum('earned_amount');
        $thisWeekEarnings = VideoView::where('user_id', $userId)->whereBetween('viewed_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('earned_amount');
        $thisMonthEarnings = VideoView::where('user_id', $userId)->whereMonth('viewed_at', now()->month)->sum('earned_amount');
        
        $earningsByVideo = VideoView::where('user_id', $userId)
            ->with('videoLink')
            ->selectRaw('video_link_id, SUM(earned_amount) as total_earned, COUNT(*) as view_count')
            ->groupBy('video_link_id')
            ->orderByDesc('total_earned')
            ->get();
        
        $dailyEarnings = VideoView::where('user_id', $userId)
            ->selectRaw('DATE(viewed_at) as date, SUM(earned_amount) as daily_total')
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        $data = [
            'pageTitle' => 'Video Earnings Report',
            'totalEarnings' => $totalEarnings,
            'todayEarnings' => $todayEarnings,
            'thisWeekEarnings' => $thisWeekEarnings,
            'thisMonthEarnings' => $thisMonthEarnings,
            'earningsByVideo' => $earningsByVideo,
            'dailyEarnings' => $dailyEarnings,
            'totalVideosWatched' => VideoView::where('user_id', $userId)->count(),
            'uniqueVideosWatched' => VideoView::where('user_id', $userId)->distinct('video_link_id')->count()
        ];

        return view('frontend.video-earnings', $data);
    }

    /**
     * Show daily video earnings report
     */
    public function dailyReport()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login');
            }

            $today = now()->toDateString();
            
            // Get today's video views with error handling
            $todayViews = collect();
            if (method_exists($user, 'videoViews')) {
                $todayViews = $user->videoViews()
                    ->whereDate('created_at', $today)
                    ->with('videoLink')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            // Calculate today's statistics
            $todayVideoCount = $todayViews->count();
            $todayEarnings = $todayViews->sum('earned_amount');
            
            // Get weekly data (last 7 days)
            $weeklyData = collect();
            try {
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $dayViews = collect();
                    
                    if (method_exists($user, 'videoViews')) {
                        $dayViews = $user->videoViews()
                            ->whereDate('created_at', $date->toDateString())
                            ->get();
                    }
                    
                    $weeklyData->push([
                        'date' => $date->toDateString(),
                        'day_name' => $date->format('D'),
                        'earnings' => $dayViews->sum('earned_amount') ?? 0,
                        'videos' => $dayViews->count() ?? 0
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Weekly data error: ' . $e->getMessage());
                $weeklyData = collect();
            }
            
            // Calculate average daily earnings
            $averageDailyEarnings = $weeklyData->count() > 0 ? $weeklyData->avg('earnings') : 0;
            
            return view('frontend.video-daily-report', [
                'pageTitle' => 'Daily Video Report',
                'todayViews' => $todayViews,
                'todayVideoCount' => $todayVideoCount,
                'todayEarnings' => $todayEarnings,
                'weeklyData' => $weeklyData->toArray(),
                'averageDailyEarnings' => $averageDailyEarnings
            ]);
            
        } catch (\Exception $e) {
            Log::error('Daily report error: ' . $e->getMessage());
            
            return view('frontend.video-daily-report', [
                'pageTitle' => 'Daily Video Report',
                'todayViews' => collect(),
                'todayVideoCount' => 0,
                'todayEarnings' => 0,
                'weeklyData' => [],
                'averageDailyEarnings' => 0
            ]);
        }
    }

    /**
     * Public video gallery (for non-logged users)
     */
    public function publicGallery(Request $request)
    {
        // Reduce initial load to 6 videos for faster page loading
        $perPage = $request->ajax() ? 6 : 6; // Keep consistent for both initial and AJAX loads
        
        $videos = VideoLink::where('status', 'active')
            ->select(['id', 'title', 'description', 'embed_url', 'views_count', 'cost_per_click', 'earning_per_view', 'created_at']) // Only select needed fields
            ->orderBy('views_count', 'desc')
            ->paginate($perPage);

        // If this is an AJAX request, return only the video items
        if ($request->ajax()) {
            return response()->json([
                'html' => view('frontend.partials.video-items', compact('videos'))->render(),
                'hasMore' => $videos->hasMorePages(),
                'nextPageUrl' => $videos->nextPageUrl()
            ]);
        }

        // For initial page load, get minimal data for better performance
        $totalVideos = Cache::remember('public_gallery_total_videos', 300, function() {
            return VideoLink::where('status', 'active')->count();
        });
        
        $totalViews = Cache::remember('public_gallery_total_views', 300, function() {
            return VideoLink::sum('views_count');
        });
        
        $totalEarningsPaid = Cache::remember('public_gallery_total_earnings', 300, function() {
            return VideoLink::sum('cost_per_click');
        });

        // Get only 3 featured plans for the hero section
        $displayPlans = Cache::remember('public_gallery_display_plans', 300, function() {
            return \App\Models\Plan::where('status', true)
                ->where('video_access_enabled', true)
                ->select(['id', 'name', 'fixed_amount', 'daily_video_limit'])
                ->orderBy('fixed_amount', 'asc')
                ->limit(3)
                ->get();
        });

        $data = [
            'pageTitle' => 'Public Video Gallery',
            'videos' => $videos,
            'displayPlans' => $displayPlans,
            'totalVideos' => $totalVideos,
            'totalViews' => $totalViews,
            'totalEarningsPaid' => $totalEarningsPaid
        ];

        return view('frontend.public-gallery', $data);
    }

    /**
     * Get video leaderboard
     */
    public function leaderboard()
    {
        $topEarners = User::withSum('videoViews', 'earned_amount')
            ->orderByDesc('video_views_sum_earned_amount')
            ->limit(10)
            ->get();
        
        $topVideos = VideoLink::orderByDesc('views_count')
            ->limit(10)
            ->get();

        $data = [
            'pageTitle' => 'Video Leaderboard',
            'topEarners' => $topEarners,
            'topVideos' => $topVideos,
            'userRank' => $this->getUserRank()
        ];

        return view('admin.video-leaderboard', $data);
    }

    /**
     * Get user's rank among all video watchers
     */
    private function getUserRank()
    {
        if (!Auth::check()) {
            return null;
        }

        $userEarnings = VideoView::where('user_id', Auth::id())->sum('earned_amount');
        
        return User::withSum('videoViews', 'earned_amount')
            ->having('video_views_sum_earned_amount', '>', $userEarnings)
            ->count() + 1;
    }

    // ===== ADMIN METHODS =====

    /**
     * Display admin video links management page
     */
    public function index(Request $request)
    {
        $query = VideoLink::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('video_url', 'like', '%' . $request->search . '%')
                  ->orWhere('source_platform', 'like', '%' . $request->search . '%');
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $videoLinks = $query->paginate(15)->withQueryString();

        // Get statistics
        $stats = [
            'total_videos' => VideoLink::count(),
            'active_videos' => VideoLink::where('status', 'active')->count(),
            'inactive_videos' => VideoLink::where('status', 'inactive')->count(),
            'total_views' => VideoLink::sum('views_count'),
            'total_earnings_paid' => VideoView::sum('earned_amount'),
            'total_clicks' => VideoLink::sum('clicks_count'),
        ];

        // Get categories for filter
        $categories = VideoLink::distinct('category')->pluck('category')->filter();

        return view('admin.video-links.index', compact('videoLinks', 'stats', 'categories'));
    }

    /**
     * Show form to create new video link
     */
    public function create()
    {
        return view('admin.video-links.create');
    }

    /**
     * Store new video link
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'video_url' => 'required|url|max:1000',
            'duration' => 'nullable|integer|min:1|max:7200', // Max 2 hours
            'ads_type' => 'nullable|string|max:100',
            'category' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'source_platform' => 'nullable|string|max:100',
            'cost_per_click' => 'required|numeric|min:0|max:999.9999',
            'status' => 'required|in:active,inactive,paused,completed'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Auto-detect source platform from URL
            $sourcePlatform = $this->detectSourcePlatform($request->video_url);

            VideoLink::create([
                'title' => $request->title,
                'video_url' => $request->video_url,
                'duration' => $request->duration,
                'ads_type' => $request->ads_type,
                'category' => $request->category,
                'country' => $request->country,
                'source_platform' => $request->source_platform ?: $sourcePlatform,
                'cost_per_click' => $request->cost_per_click,
                'status' => $request->status,
                'views_count' => 0,
                'clicks_count' => 0,
            ]);

            return redirect()->route('admin.video-links.index')
                ->with('success', 'Video link created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating video link: ' . $e->getMessage());
            return back()->with('error', 'Failed to create video link. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show specific video link details
     */
    public function show($id)
    {
        $videoLink = VideoLink::with(['views' => function($query) {
            $query->with('user')->latest()->limit(50);
        }])->findOrFail($id);

        // Get analytics data
        $analytics = [
            'total_views' => $videoLink->views->count(),
            'total_earnings' => $videoLink->views->sum('earned_amount'),
            'unique_viewers' => $videoLink->views->unique('user_id')->count(),
            'avg_earning_per_view' => $videoLink->views->count() > 0 
                ? $videoLink->views->sum('earned_amount') / $videoLink->views->count() 
                : 0,
            'recent_views' => $videoLink->views->take(10),
        ];

        // Daily views for the last 30 days
        $dailyViews = VideoView::where('video_link_id', $id)
            ->where('viewed_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as views, SUM(earned_amount) as earnings')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.video-links.show', compact('videoLink', 'analytics', 'dailyViews'));
    }

    /**
     * Show form to edit video link
     */
    public function edit($id)
    {
        $videoLink = VideoLink::findOrFail($id);
        return view('admin.video-links.edit', compact('videoLink'));
    }

    /**
     * Update video link
     */
    public function update(Request $request, $id)
    {
        $videoLink = VideoLink::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'video_url' => 'required|url|max:1000',
            'duration' => 'nullable|integer|min:1|max:7200',
            'ads_type' => 'nullable|string|max:100',
            'category' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'source_platform' => 'nullable|string|max:100',
            'cost_per_click' => 'required|numeric|min:0|max:999.9999',
            'status' => 'required|in:active,inactive,paused,completed'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Auto-detect source platform if not provided
            $sourcePlatform = $request->source_platform ?: $this->detectSourcePlatform($request->video_url);

            $videoLink->update([
                'title' => $request->title,
                'video_url' => $request->video_url,
                'duration' => $request->duration,
                'ads_type' => $request->ads_type,
                'category' => $request->category,
                'country' => $request->country,
                'source_platform' => $sourcePlatform,
                'cost_per_click' => $request->cost_per_click,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.video-links.index')
                ->with('success', 'Video link updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating video link: ' . $e->getMessage());
            return back()->with('error', 'Failed to update video link. Please try again.')
                ->withInput();
        }
    }

    /**
     * Delete video link
     */
    public function destroy($id)
    {
        try {
            $videoLink = VideoLink::findOrFail($id);
            
            // Check if video has views
            $hasViews = VideoView::where('video_link_id', $id)->exists();
            
            if ($hasViews) {
                return back()->with('warning', 'Cannot delete video link that has viewer data. Consider deactivating it instead.');
            }

            $videoLink->delete();

            return redirect()->route('admin.video-links.index')
                ->with('success', 'Video link deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error deleting video link: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete video link. Please try again.');
        }
    }

    /**
     * Bulk actions for video links
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,pause,delete',
            'video_ids' => 'required|array|min:1',
            'video_ids.*' => 'exists:video_links,id'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $videoIds = $request->video_ids;
            $action = $request->action;
            $count = 0;

            switch ($action) {
                case 'activate':
                    $count = VideoLink::whereIn('id', $videoIds)->update(['status' => 'active']);
                    break;
                case 'deactivate':
                    $count = VideoLink::whereIn('id', $videoIds)->update(['status' => 'inactive']);
                    break;
                case 'pause':
                    $count = VideoLink::whereIn('id', $videoIds)->update(['status' => 'paused']);
                    break;
                case 'delete':
                    // Check if any video has views
                    $hasViews = VideoView::whereIn('video_link_id', $videoIds)->exists();
                    if ($hasViews) {
                        return back()->with('warning', 'Cannot delete videos that have viewer data.');
                    }
                    $count = VideoLink::whereIn('id', $videoIds)->delete();
                    break;
            }

            return back()->with('success', "Bulk action completed successfully! {$count} videos processed.");

        } catch (\Exception $e) {
            Log::error('Error in bulk action: ' . $e->getMessage());
            return back()->with('error', 'Failed to perform bulk action. Please try again.');
        }
    }

    /**
     * Export video links to CSV
     */
    public function export(Request $request)
    {
        try {
            // Set memory limit and execution time for large exports
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes
            
            $query = VideoLink::query();

            // Apply same filters as index page
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('video_url', 'like', '%' . $request->search . '%')
                      ->orWhere('source_platform', 'like', '%' . $request->search . '%');
                });
            }

            // Count total records
            $totalRecords = $query->count();
            
            // Limit export to prevent memory issues
            if ($totalRecords > 10000) {
                return back()->with('error', 'Export limited to 10,000 records. Please apply filters to reduce the dataset.');
            }

            // Use chunking for large datasets
            $filename = 'video-links-' . date('Y-m-d-H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            $callback = function() use ($query) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // CSV headers matching import format exactly
                fputcsv($file, [
                    'Title',
                    'Video URL',
                    'Duration (seconds)',
                    'Ads Type',
                    'Category',
                    'Country',
                    'Source Platform',
                    'Cost Per Click',
                    'Status'
                ]);

                // Process data in chunks to prevent memory issues
                $query->chunk(1000, function($videoLinks) use ($file) {
                    foreach ($videoLinks as $video) {
                        fputcsv($file, [
                            $video->title ?: '',
                            $video->video_url ?: '',
                            $video->duration ?: '',
                            $video->ads_type ?: '',
                            $video->category ?: '',
                            $video->country ?: '',
                            $video->source_platform ?: '',
                            $video->cost_per_click ?: '',
                            $video->status ?: 'active'
                        ]);
                    }
                });

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting video links: ' . $e->getMessage());
            return back()->with('error', 'Failed to export video links: ' . $e->getMessage());
        }
    }

    /**
     * Advanced Export with analytics data
     */
    public function advancedExport(Request $request)
    {
        try {
            $query = VideoLink::query();

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('video_url', 'like', '%' . $request->search . '%')
                      ->orWhere('source_platform', 'like', '%' . $request->search . '%');
                });
            }

            // Get video links with analytics
            $videoLinks = $query->with(['views'])->get();

            $filename = 'video-links-advanced-' . date('Y-m-d-H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($videoLinks) {
                $file = fopen('php://output', 'w');
                
                // CSV headers with analytics
                fputcsv($file, [
                    'ID',
                    'Title',
                    'Video URL',
                    'Duration (seconds)',
                    'Ads Type',
                    'Category',
                    'Country',
                    'Source Platform',
                    'Views Count',
                    'Clicks Count',
                    'Cost Per Click',
                    'Status',
                    'Total Earnings Paid',
                    'Unique Viewers',
                    'Average Views per Day',
                    'Last View Date',
                    'Created At',
                    'Updated At'
                ]);

                // CSV data with analytics
                foreach ($videoLinks as $video) {
                    $totalEarnings = $video->views->sum('earned_amount');
                    $uniqueViewers = $video->views->unique('user_id')->count();
                    $daysSinceCreated = $video->created_at->diffInDays(now()) ?: 1;
                    $avgViewsPerDay = $video->views_count / $daysSinceCreated;
                    $lastViewDate = $video->views->max('viewed_at');

                    fputcsv($file, [
                        $video->id,
                        $video->title,
                        $video->video_url,
                        $video->duration,
                        $video->ads_type,
                        $video->category,
                        $video->country,
                        $video->source_platform,
                        $video->views_count,
                        $video->clicks_count,
                        $video->cost_per_click,
                        $video->status,
                        number_format($totalEarnings, 4),
                        $uniqueViewers,
                        number_format($avgViewsPerDay, 2),
                        $lastViewDate ? date('Y-m-d H:i:s', strtotime($lastViewDate)) : 'Never',
                        $video->created_at,
                        $video->updated_at
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error in advanced export: ' . $e->getMessage());
            return back()->with('error', 'Failed to export video links. Please try again.');
        }
    }

    /**
     * Detect source platform from video URL
     */
    private function detectSourcePlatform($url)
    {
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'YouTube';
        } elseif (str_contains($url, 'vimeo.com')) {
            return 'Vimeo';
        } elseif (str_contains($url, 'dailymotion.com')) {
            return 'Dailymotion';
        } elseif (str_contains($url, 'facebook.com')) {
            return 'Facebook';
        } elseif (str_contains($url, 'instagram.com')) {
            return 'Instagram';
        } elseif (str_contains($url, 'tiktok.com')) {
            return 'TikTok';
        } else {
            return 'Other';
        }
    }

    /**
     * Import video links from CSV
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->with('error', 'Please select a valid CSV file (max 2MB).');
        }

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            
            // Read CSV file with proper encoding handling
            $csvContent = file_get_contents($path);
            
            // Convert encoding if needed
            if (!mb_check_encoding($csvContent, 'UTF-8')) {
                $csvContent = mb_convert_encoding($csvContent, 'UTF-8', 'auto');
            }
            
            // Parse CSV data
            $lines = str_getcsv($csvContent, "\n");
            $csvData = [];
            
            foreach ($lines as $line) {
                if (!empty(trim($line))) {
                    $csvData[] = str_getcsv($line);
                }
            }
            
            if (empty($csvData)) {
                return back()->with('error', 'The CSV file is empty or could not be parsed.');
            }
            
            // Remove header row (if exists)
            $header = array_shift($csvData);
            
            // Log the header for debugging
            Log::info('CSV Import Header:', ['header' => $header]);
            
            $imported = 0;
            $errors = [];
            $skipped = 0;

            DB::beginTransaction();

            foreach ($csvData as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and array is 0-indexed
                
                try {
                    // Log the row data for debugging
                    Log::info("Processing CSV row {$rowNumber}:", ['row_data' => $row]);
                    
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        $skipped++;
                        Log::info("Skipped empty row {$rowNumber}");
                        continue;
                    }

                    // Map CSV columns to database fields
                    // Expected format: Title, Video URL, Duration (seconds), Ads Type, Category, Country, Source Platform, Cost Per Click, Status
                    $videoData = [
                        'title' => !empty($row[0]) ? trim($row[0]) : 'Imported Video ' . $rowNumber,
                        'video_url' => !empty($row[1]) ? trim($row[1]) : '',
                        'duration' => !empty($row[2]) && is_numeric($row[2]) ? intval($row[2]) : null,
                        'ads_type' => !empty($row[3]) ? trim($row[3]) : null,
                        'category' => !empty($row[4]) ? trim($row[4]) : 'general',
                        'country' => !empty($row[5]) ? trim($row[5]) : null,
                        'source_platform' => !empty($row[6]) ? trim($row[6]) : null,
                        'cost_per_click' => !empty($row[7]) && is_numeric($row[7]) ? floatval($row[7]) : 0.01,
                        'status' => !empty($row[8]) && in_array(strtolower($row[8]), ['active', 'inactive', 'paused', 'completed']) 
                                   ? strtolower($row[8]) : 'active',
                    ];
                    
                    Log::info("Mapped video data for row {$rowNumber}:", ['video_data' => $videoData]);

                    // Validate required fields
                    if (empty($videoData['video_url'])) {
                        $errors[] = "Row {$rowNumber}: Video URL is required";
                        continue;
                    }

                    // More flexible URL validation
                    $videoUrl = $videoData['video_url'];
                    
                    // Clean the URL - remove extra spaces and quotes
                    $videoUrl = trim($videoUrl, " \t\n\r\0\x0B\"'");
                    $videoData['video_url'] = $videoUrl;
                    
                    // Check if URL starts with http:// or https://
                    if (!preg_match('/^https?:\/\//', $videoUrl)) {
                        // If not, try adding https://
                        $videoUrl = 'https://' . $videoUrl;
                        $videoData['video_url'] = $videoUrl;
                    }
                    
                    // Validate URL format (more lenient than filter_var)
                    if (!preg_match('/^https?:\/\/[^\s]+\.[^\s]+/i', $videoUrl)) {
                        $errors[] = "Row {$rowNumber}: Invalid video URL format. URL: '{$videoData['video_url']}'";
                        continue;
                    }
                    
                    // Additional check for common video platforms
                    $isValidVideoUrl = false;
                    $videoPlatforms = [
                        'youtube.com', 'youtu.be', 'vimeo.com', 'dailymotion.com',
                        'facebook.com', 'instagram.com', 'tiktok.com', 'twitch.tv',
                        'rumble.com', 'bitchute.com', 'odysee.com'
                    ];
                    
                    foreach ($videoPlatforms as $platform) {
                        if (str_contains(strtolower($videoUrl), $platform)) {
                            $isValidVideoUrl = true;
                            break;
                        }
                    }
                    
                    // If it's not a known video platform, still allow it but log a warning
                    if (!$isValidVideoUrl) {
                        Log::warning("Importing URL from unknown platform: {$videoUrl}");
                    }

                    // Check for duplicate URLs
                    $existingVideo = VideoLink::where('video_url', $videoData['video_url'])->first();
                    if ($existingVideo) {
                        $errors[] = "Row {$rowNumber}: Video URL already exists (ID: {$existingVideo->id})";
                        continue;
                    }

                    // Auto-detect source platform if not provided
                    if (empty($videoData['source_platform'])) {
                        $videoData['source_platform'] = $this->detectSourcePlatform($videoData['video_url']);
                    }

                    // Validate numeric fields
                    if ($videoData['duration'] !== null && ($videoData['duration'] < 1 || $videoData['duration'] > 7200)) {
                        $errors[] = "Row {$rowNumber}: Duration must be between 1 and 7200 seconds";
                        continue;
                    }

                    if ($videoData['cost_per_click'] < 0 || $videoData['cost_per_click'] > 999.9999) {
                        $errors[] = "Row {$rowNumber}: Cost per click must be between 0 and 999.9999";
                        continue;
                    }

                    // Set default values for tracking
                    $videoData['views_count'] = 0;
                    $videoData['clicks_count'] = 0;

                    // Create video link
                    VideoLink::create($videoData);
                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                    Log::error("Import error for row {$rowNumber}: " . $e->getMessage());
                }
            }

            DB::commit();

            // Prepare success message
            $message = "Import completed successfully! {$imported} video(s) imported.";
            
            if ($skipped > 0) {
                $message .= " {$skipped} empty row(s) skipped.";
            }
            
            if (!empty($errors)) {
                $message .= " " . count($errors) . " error(s) occurred.";
                
                // Log errors for admin review
                Log::warning('CSV Import Errors', [
                    'total_errors' => count($errors),
                    'errors' => $errors,
                    'imported' => $imported,
                    'skipped' => $skipped
                ]);
            }

            return back()->with('success', $message)
                         ->with('import_errors', $errors)
                         ->with('import_stats', [
                             'imported' => $imported,
                             'errors' => count($errors),
                             'skipped' => $skipped
                         ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CSV Import failed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to import video links. Error: ' . $e->getMessage());
        }
    }

    /**
     * Generate and download a sample CSV file for import
     */
    public function downloadSampleCsv()
    {
        $filename = 'video-links-sample-import.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Title',
                'Video URL', 
                'Duration (seconds)',
                'Ads Type',
                'Category',
                'Country',
                'Source Platform',
                'Cost Per Click',
                'Status'
            ]);

            // Sample data
            fputcsv($file, [
                'Sample Video 1',
                'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                '210',
                'Pre-roll',
                'entertainment',
                'US',
                'YouTube',
                '0.0050',
                'active'
            ]);
            
            fputcsv($file, [
                'Sample Video 2',
                'https://vimeo.com/123456789',
                '180',
                'Mid-roll',
                'education',
                'UK',
                'Vimeo',
                '0.0075',
                'active'
            ]);
            
            fputcsv($file, [
                'Sample Video 3',
                'https://www.facebook.com/watch/video123',
                '240',
                'Post-roll',
                'technology',
                'CA',
                'Facebook',
                '0.0100',
                'inactive'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Debug CSV import - temporary method for troubleshooting
     */
    public function debugCsvImport(Request $request)
    {
        if (!$request->hasFile('csv_file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        // Read first few lines of the CSV
        $lines = array_slice(file($path), 0, 5);
        $parsedLines = [];
        
        foreach ($lines as $index => $line) {
            $parsedLines[$index] = [
                'raw' => $line,
                'parsed' => str_getcsv($line),
                'length' => strlen($line),
                'encoding' => mb_detect_encoding($line)
            ];
        }
        
        return response()->json([
            'file_info' => [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ],
            'first_5_lines' => $parsedLines
        ]);
    }
}
