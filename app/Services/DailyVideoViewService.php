<?php

namespace App\Services;

use App\Models\User;
use App\Models\VideoView;
use App\Models\VideoLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyVideoViewService
{
    /**
     * Record a video view in optimized single-row format
     */
    public function recordVideoView(User $user, VideoLink $video, array $viewData = [])
    {
        $date = today();
        
        // Get or create today's video view record
        $videoView = VideoView::firstOrCreate(
            [
                'user_id' => $user->id,
                'view_date' => $date,
                'view_type' => 'daily_summary'
            ],
            [
                'ip_address' => $viewData['ip_address'] ?? request()->ip(),
                'device_info' => $viewData['device_info'] ?? request()->userAgent(),
                'video_data' => json_encode([]),
                'total_earned' => 0,
                'total_videos' => 0,
                'viewed_at' => now()
            ]
        );

        // Parse existing video data
        $videoData = json_decode($videoView->video_data ?? '[]', true) ?: [];
        
        // Check if this video was already watched today
        $videoKey = (string) $video->id;
        if (isset($videoData[$videoKey])) {
            return [
                'success' => false,
                'error' => 'Video already watched today',
                'already_watched' => true
            ];
        }

        // Add new video view data
        $earnedAmount = $viewData['earned_amount'] ?? 0;
        $videoData[$videoKey] = [
            'video_id' => $video->id,
            'video_title' => $video->title,
            'earned_amount' => $earnedAmount,
            'watch_duration' => $viewData['watch_duration'] ?? 0,
            'watched_at' => now()->toISOString(),
            'category' => $video->category ?? 'general'
        ];

        // Update totals
        $newTotalEarned = $videoView->total_earned + $earnedAmount;
        $newTotalVideos = $videoView->total_videos + 1;

        // Update the record
        $videoView->update([
            'video_data' => json_encode($videoData),
            'total_earned' => $newTotalEarned,
            'total_videos' => $newTotalVideos,
            'earned_amount' => $newTotalEarned, // For compatibility with existing queries
            'video_link_id' => $video->id, // Latest video for compatibility
            'viewed_at' => now()
        ]);

        Log::info('Optimized video view recorded', [
            'user_id' => $user->id,
            'video_id' => $video->id,
            'total_videos_today' => $newTotalVideos,
            'total_earned_today' => $newTotalEarned
        ]);

        return [
            'success' => true,
            'video_view' => $videoView,
            'total_earned_today' => $newTotalEarned,
            'total_videos_today' => $newTotalVideos,
            'earned_amount' => $earnedAmount
        ];
    }

    /**
     * Check if user has watched a specific video today
     */
    public function hasWatchedVideoToday(User $user, int $videoId, ?Carbon $date = null): bool
    {
        $date = $date ?? today();
        
        $videoView = VideoView::where('user_id', $user->id)
            ->where('view_date', $date)
            ->where('view_type', 'daily_summary')
            ->first();

        if (!$videoView) {
            return false;
        }

        $videoData = json_decode($videoView->video_data ?? '[]', true) ?: [];
        return isset($videoData[(string) $videoId]);
    }

    /**
     * Get today's video viewing summary for user
     */
    public function getTodaysViewingSummary($userId, ?Carbon $date = null): array
    {
        $date = $date ?? today();
        
        $videoView = VideoView::where('user_id', $userId)
            ->where('view_date', $date)
            ->where('view_type', 'daily_summary')
            ->first();

        if (!$videoView) {
            return [
                'total_videos' => 0,
                'total_earned' => 0,
                'videos_watched' => [],
                'last_watched' => null
            ];
        }

        $videoData = json_decode($videoView->video_data ?? '[]', true) ?: [];

        return [
            'total_videos' => $videoView->total_videos,
            'total_earned' => $videoView->total_earned,
            'videos_watched' => array_values($videoData),
            'last_watched' => $videoView->viewed_at,
            'view_record' => $videoView
        ];
    }

    /**
     * Get user's total earnings from optimized video views
     */
    public function getUserTotalEarnings(User $user): float
    {
        return VideoView::where('user_id', $user->id)
            ->where('view_type', 'daily_summary')
            ->sum('total_earned');
    }

    /**
     * Get viewing statistics for date range
     */
    public function getViewingStats(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $videoViews = VideoView::where('user_id', $user->id)
            ->where('view_type', 'daily_summary')
            ->whereBetween('view_date', [$startDate, $endDate])
            ->orderBy('view_date', 'desc')
            ->get();

        $stats = [
            'total_days' => $videoViews->count(),
            'total_videos' => $videoViews->sum('total_videos'),
            'total_earnings' => $videoViews->sum('total_earned'),
            'average_videos_per_day' => $videoViews->count() > 0 ? $videoViews->avg('total_videos') : 0,
            'average_earnings_per_day' => $videoViews->count() > 0 ? $videoViews->avg('total_earned') : 0,
            'highest_earning_day' => $videoViews->max('total_earned') ?? 0,
            'most_videos_day' => $videoViews->max('total_videos') ?? 0,
            'daily_breakdown' => $videoViews->toArray()
        ];

        return $stats;
    }

    /**
     * Get all videos watched by user on a specific date
     */
    public function getVideosWatchedOnDate(User $user, Carbon $date): array
    {
        $videoView = VideoView::where('user_id', $user->id)
            ->where('view_date', $date)
            ->where('view_type', 'daily_summary')
            ->first();

        if (!$videoView) {
            return [];
        }

        $videoData = json_decode($videoView->video_data ?? '[]', true) ?: [];
        return array_values($videoData);
    }

    /**
     * Migrate existing video views to optimized format (for one-time migration)
     */
    public function migrateExistingVideoViews(int $batchSize = 100): array
    {
        $migrated = 0;
        $errors = 0;

        try {
            // Get all old-format video views
            $oldViews = VideoView::whereNull('view_type')
                ->orWhere('view_type', '!=', 'daily_summary')
                ->orderBy('user_id')
                ->orderBy('viewed_at')
                ->limit($batchSize)
                ->get();

            $groupedViews = $oldViews->groupBy('user_id');

            foreach ($groupedViews as $userId => $userViews) {
                $user = User::find($userId);
                if (!$user) continue;

                $dayGroups = $userViews->groupBy(function($view) {
                    return Carbon::parse($view->viewed_at)->toDateString();
                });

                foreach ($dayGroups as $date => $dayViews) {
                    try {
                        $this->migrateDayViews($user, $date, $dayViews);
                        $migrated += $dayViews->count();
                    } catch (\Exception $e) {
                        $errors++;
                        Log::error('Error migrating video views for user ' . $userId . ' date ' . $date, [
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Error in video view migration', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'migrated' => $migrated,
                'errors' => $errors
            ];
        }

        return [
            'success' => true,
            'migrated' => $migrated,
            'errors' => $errors,
            'has_more' => $oldViews->count() === $batchSize
        ];
    }

    /**
     * Migrate video views for a specific user and date
     */
    private function migrateDayViews(User $user, string $date, $dayViews)
    {
        $carbonDate = Carbon::parse($date);
        $totalEarned = $dayViews->sum('earned_amount');
        $totalVideos = $dayViews->count();

        // Build video data array
        $videoData = [];
        foreach ($dayViews as $view) {
            $videoData[(string) $view->video_link_id] = [
                'video_id' => $view->video_link_id,
                'video_title' => $view->videoLink->title ?? 'Unknown',
                'earned_amount' => $view->earned_amount,
                'watch_duration' => 0, // Not available in old format
                'watched_at' => $view->viewed_at,
                'category' => $view->videoLink->category ?? 'general'
            ];
        }

        // Create or update optimized record
        VideoView::updateOrCreate(
            [
                'user_id' => $user->id,
                'view_date' => $carbonDate,
                'view_type' => 'daily_summary'
            ],
            [
                'video_data' => json_encode($videoData),
                'total_earned' => $totalEarned,
                'total_videos' => $totalVideos,
                'earned_amount' => $totalEarned,
                'ip_address' => $dayViews->first()->ip_address,
                'device_info' => $dayViews->first()->device_info,
                'video_link_id' => $dayViews->last()->video_link_id, // Latest video
                'viewed_at' => $dayViews->last()->viewed_at
            ]
        );

        // Mark old records as migrated (don't delete immediately for safety)
        VideoView::whereIn('id', $dayViews->pluck('id'))
            ->update(['view_type' => 'migrated_old']);
    }

    /**
     * Get detailed viewing history with pagination
     */
    public function getDetailedViewingHistory($userId, $request = null)
    {
        $query = VideoView::where('user_id', $userId)
            ->where('view_type', 'daily_summary')
            ->orderBy('view_date', 'desc');

        // Filter by date range if provided
        if ($request && $request->has('date_from') && $request->date_from) {
            $query->whereDate('view_date', '>=', $request->date_from);
        }
        
        if ($request && $request->has('date_to') && $request->date_to) {
            $query->whereDate('view_date', '<=', $request->date_to);
        }

        return $query->paginate(15);
    }

    /**
     * Get total videos watched by user
     */
    public function getTotalVideosWatched($userId)
    {
        return VideoView::where('user_id', $userId)
            ->where('view_type', 'daily_summary')
            ->sum('total_videos');
    }

    /**
     * Get weekly statistics
     */
    public function getWeeklyStats($userId)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $weeklyData = VideoView::where('user_id', $userId)
            ->where('view_type', 'daily_summary')
            ->whereBetween('view_date', [$startOfWeek, $endOfWeek])
            ->selectRaw('SUM(total_videos) as total_videos, SUM(total_earned) as total_earnings')
            ->first();

        return [
            'total_videos' => $weeklyData->total_videos ?? 0,
            'total_earnings' => $weeklyData->total_earnings ?? 0
        ];
    }

    /**
     * Get daily earnings chart data
     */
    public function getDailyEarningsChart($userId, $days = 30)
    {
        $startDate = now()->subDays($days);
        
        return VideoView::where('user_id', $userId)
            ->where('view_type', 'daily_summary')
            ->where('view_date', '>=', $startDate)
            ->selectRaw('view_date as date, total_earned as total_earnings, total_videos as total_views')
            ->orderBy('view_date', 'asc')
            ->get();
    }
}
