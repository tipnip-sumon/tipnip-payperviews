<?php

namespace App\Services;

use App\Models\User;
use App\Models\VideoLink;
use App\Models\DailyVideoAssignment;
use App\Models\VideoView;
use App\Models\Invest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DailyVideoService 
{
    /** 
     * Assign fresh videos to a user for today 
     */
    public function assignDailyVideos(User $user, ?Carbon $date = null): array
    {
        $date = $date ?? today();
        
        // Check if user already has assignments for this date
        $existingAssignments = DailyVideoAssignment::forUser($user->id)
            ->forDate($date)
            ->count();
        // Get user's active investment plan
        $activeInvest = Invest::where('user_id', $user->id)
            ->where('status', 1)
            ->with('plan')
            ->first();

        // Determine daily limit based on plan
        $dailyLimit = $this->getDailyLimit($activeInvest);
            
        if ($existingAssignments > $dailyLimit) {
            return $this->getTodaysVideos($user, $date);
        }
        if ($dailyLimit <= 0) {
            return [
                'videos' => collect([]),
                'message' => 'No videos available for your current plan.',
                'stats' => $this->getUserStats($user, $activeInvest)
            ];
        }
        
        // Get videos to assign (excluding recently watched videos)
        $assignableVideos = $this->getAssignableVideos($user, $dailyLimit, $date); 
        
        if ($assignableVideos->isEmpty()) {
            return [
                'videos' => collect([]),
                'message' => 'No new videos available today. Check back tomorrow!',
                'stats' => $this->getUserStats($user, $activeInvest)
            ];
        }
        
        // Create single assignment record with JSON video IDs
        $videoIds = $assignableVideos->pluck('id')->toArray();
        
        try {
            // Check if assignment already exists for today
            $existingAssignment = DailyVideoAssignment::forUser($user->id)->forDate($date)->first();
            
            if (!$existingAssignment) {
                // Insert single row with all video IDs in JSON format
                DailyVideoAssignment::create([
                    'user_id' => $user->id,
                    'video_ids' => json_encode($videoIds), // Store all video IDs as JSON
                    'watched_video_ids' => json_encode([]), // Track watched videos as JSON array
                    'assignment_date' => $date,
                    'total_videos' => count($videoIds),
                    'watched_count' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                Log::info('Daily videos assigned (optimized)', [
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                    'video_count' => count($videoIds)
                ]);
            }
            
            return $this->getTodaysVideos($user, $date);
            
        } catch (\Exception $e) {
            Log::error('Failed to assign daily videos', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'videos' => collect([]),
                'message' => 'Error assigning videos. Please try again later.',
                'stats' => $this->getUserStats($user, $activeInvest)
            ];
        }
    }
    
    /**
     * Get today's assigned videos for a user (optimized version)
     */
    public function getTodaysVideos(User $user, ?Carbon $date = null): array
    {
        $date = $date ?? today();
        
        // Get single assignment record for today
        $assignment = DailyVideoAssignment::forUser($user->id)
            ->forDate($date)
            ->first();
            
        $activeInvest = Invest::where('user_id', $user->id)
            ->where('status', 1)
            ->with('plan') 
            ->first();

        // Check if plan allows video access
        $videoAccessEnabled = false;
        if ($activeInvest && $activeInvest->plan && isset($activeInvest->plan->video_access_enabled)) {
            $videoAccessEnabled = (bool)$activeInvest->plan->video_access_enabled;
        }
        if (!$videoAccessEnabled) {
            return [
                'videos' => collect([]),
                'message' => 'Your current plan does not allow video access. Please upgrade your plan to access daily videos.',
                'stats' => $this->getUserStats($user, $activeInvest)
            ];
        }
        
        if (!$assignment) {
            return [
                'videos' => collect([]),
                'message' => 'No videos assigned for today.',
                'stats' => $this->getUserStats($user, $activeInvest)
            ];
        }
            
        $stats = $this->getUserStats($user, $activeInvest);
        
        // Parse video IDs from JSON
        $videoIds = json_decode($assignment->video_ids ?? '[]', true);
        $watchedVideoIds = json_decode($assignment->watched_video_ids ?? '[]', true);
        
        if (empty($videoIds)) {
            return [
                'videos' => collect([]),
                'message' => 'No videos assigned for today.',
                'stats' => $stats
            ];
        }
        
        // Filter out watched videos - only show unwatched videos
        $unwatchedVideoIds = array_diff($videoIds, $watchedVideoIds);
        
        // Get only unwatched video details
        $videos = VideoLink::whereIn('id', $unwatchedVideoIds)->get()->map(function ($video) use ($watchedVideoIds) {
            // Generate thumbnail for YouTube videos
            $thumbnailUrl = $video->thumbnail_url;
            if (!$thumbnailUrl && (str_contains($video->video_url, 'youtube.com') || str_contains($video->video_url, 'youtu.be'))) {
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $video->video_url, $matches)) {
                    $thumbnailUrl = "https://img.youtube.com/vi/{$matches[1]}/mqdefault.jpg";
                }
            }
            
            $video->user_has_viewed = false; // All videos in this list are unwatched
            $video->assignment_id = $video->id; // Use video ID as assignment reference
            $video->thumbnail_url = $thumbnailUrl;
            
            return $video;
        });
        
        $watchedCount = count($watchedVideoIds);
        $totalCount = count($videoIds);
        $remainingCount = count($unwatchedVideoIds);
        
        $message = null;
        if ($watchedCount >= $totalCount && $totalCount > 0) {
            $message = "Excellent! You've watched all {$totalCount} videos for today and earned money from each one. New videos will be available tomorrow!";
        } elseif ($totalCount === 0) {
            $message = "No videos assigned for today. This might be due to insufficient available videos.";
        } elseif ($remainingCount === 0 && $watchedCount > 0) {
            $message = "Great job! You've completed all your videos for today. Come back tomorrow for fresh content!";
        } elseif ($remainingCount > 0) {
            $message = "You have {$remainingCount} video" . ($remainingCount > 1 ? 's' : '') . " remaining to watch today. Each video you complete earns you money!";
        }
        
        return [
            'videos' => $videos,
            'message' => $message,
            'stats' => $stats
        ];
    }
    
    /**
     * Get videos that can be assigned to user (excluding recently watched)
     */
    private function getAssignableVideos(User $user, int $limit, Carbon $date): \Illuminate\Support\Collection
    {
        $weekAgo = $date->copy()->subDays(3); // Change to 3 days for weekly exclusion
        
        // Get video IDs that user has watched in the past week
        $recentlyWatchedIds = VideoView::where('user_id', $user->id)
            ->where('viewed_at', '>=', $weekAgo)
            ->pluck('video_link_id')
            ->toArray();
            
        // Get video IDs from recent assignments (both old and new format)
        $recentAssignments = DailyVideoAssignment::forUser($user->id)
            ->where('assignment_date', '>=', $weekAgo)
            ->get();
            
        $recentlyAssignedIds = [];
        foreach ($recentAssignments as $assignment) {
            // Handle new JSON format
            if ($assignment->video_ids) {
                $videoIds = json_decode($assignment->video_ids, true) ?: [];
                $recentlyAssignedIds = array_merge($recentlyAssignedIds, $videoIds);
            }
            // Handle old format for backward compatibility
            if ($assignment->video_link_id) {
                $recentlyAssignedIds[] = $assignment->video_link_id;
            }
        }
            
        // Combine both arrays to exclude
        $excludeIds = array_unique(array_merge($recentlyWatchedIds, $recentlyAssignedIds));
        
        // Get available videos
        $query = VideoLink::where('status', 'active');
        
        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }
        
        // Get videos in a deterministic order based on date and user
        $dateSeed = $date->format('Ymd') . $user->id;
        
        return $query->orderByRaw("RAND(" . crc32($dateSeed) . ")")
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get daily video limit based on user's plan
     */
    private function getDailyLimit($activeInvest): int
    {
        // Use the plan's daily_video_limit column if set and valid
        $limit = (int) ($activeInvest->plan->daily_video_limit ?? 0);
        if ($limit > 0) {
            return $limit;
        }

        // If not set or invalid, fallback to 0
        return 0;
    }
    
    /**
     * Get user's video statistics
     */
    private function getUserStats(User $user, $activeInvest): array
    {
        $dailyLimit = $this->getDailyLimit($activeInvest);
        
        // Get today's assignment (single record with JSON data)
        $todaysAssignment = DailyVideoAssignment::forUser($user->id)
            ->forDate(today())
            ->first();
            
        $todaysWatched = 0;
        $todaysTotal = 0;
        $todayEarnings = 0;
        
        if ($todaysAssignment) {
            $todaysWatched = $todaysAssignment->watched_count ?? 0;
            $todaysTotal = $todaysAssignment->total_videos ?? 0;
            
            // For earnings, we might need to calculate differently since we're not storing
            // individual earning amounts in the JSON structure
            $earningRate = $activeInvest && $activeInvest->plan 
                ? $activeInvest->plan->video_earning_rate 
                : 0.0001;
            $todayEarnings = $todaysWatched * $earningRate;
        }
            
        $planName = $activeInvest && $activeInvest->plan 
            ? $activeInvest->plan->name 
            : 'Free Plan';
        
        return [
            'plan_name' => $planName,
            'earning_rate' => $earningRate ?? 0.0001,
            'daily_limit' => $dailyLimit,
            'todays_views' => $todaysWatched,
            'todays_assigned' => $todaysTotal,
            'remaining_views' => max(0, $todaysTotal - $todaysWatched),
            'total_videos_watched' => VideoView::where('user_id', $user->id)->count(),
            'total_earnings' => VideoView::where('user_id', $user->id)->sum('earned_amount'),
            'today_earnings' => $todayEarnings
        ];
    }
    
    /**
     * Mark a video assignment as watched
     */
    public function markVideoWatched(User $user, int $videoId, float $earningAmount): bool
    {
        $assignment = DailyVideoAssignment::forUser($user->id)
            ->forDate(today())
            ->first();
            
        if (!$assignment) {
            return false;
        }
        
        // Parse current watched videos
        $watchedVideoIds = json_decode($assignment->watched_video_ids ?? '[]', true);
        $videoIds = json_decode($assignment->video_ids ?? '[]', true);
        
        // Check if video is in assigned videos and not already watched
        if (!in_array($videoId, $videoIds) || in_array($videoId, $watchedVideoIds)) {
            return false;
        }
        
        // Add to watched videos
        $watchedVideoIds[] = $videoId;
        
        // Update assignment
        $assignment->watched_video_ids = json_encode($watchedVideoIds);
        $assignment->watched_count = count($watchedVideoIds);
        $assignment->save();
        
        // Record the earning (you may need to implement this separately)
        // This could be handled by a separate transaction service
        
        return true;
    }
    
    /**
     * Clean up old assignments (older than specified days)
     */
    public function cleanupOldAssignments(int $days = 30): int
    {
        $cutoffDate = now()->subDays($days);
        
        return DailyVideoAssignment::where('assignment_date', '<', $cutoffDate)->delete();
    }
}
