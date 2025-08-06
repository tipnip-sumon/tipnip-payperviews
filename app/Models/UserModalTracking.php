<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserModalTracking extends Model
{
    use HasFactory;

    protected $table = 'user_modal_tracking';

    protected $fillable = [
        'user_id',
        'modal_type',
        'daily_click_count',
        'last_shown_at',
        'last_clicked_at',
        'current_date',
        'dismissed_today',
        'click_history'
    ];

    protected $casts = [
        'last_shown_at' => 'datetime',
        'last_clicked_at' => 'datetime',
        'current_date' => 'date',
        'dismissed_today' => 'boolean',
        'click_history' => 'array'
    ];

    /**
     * Get the user that owns the modal tracking
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if modal should be shown for user today
     */
    public static function shouldShowModal($userId, $modalType = 'welcome_guide')
    {
        $today = Carbon::today();
        $now = Carbon::now();
        
        $tracking = self::firstOrCreate([
            'user_id' => $userId,
            'modal_type' => $modalType,
            'current_date' => $today,
        ], [
            'daily_click_count' => 0,
            'dismissed_today' => false,
            'click_history' => []
        ]);

        // If dismissed today, don't show
        if ($tracking->dismissed_today) {
            return false;
        }

        // If never shown today, show it
        if (!$tracking->last_shown_at || $tracking->last_shown_at->format('Y-m-d') !== $today->format('Y-m-d')) {
            return true;
        }

        // If clicked twice today, show every 12 hours
        if ($tracking->daily_click_count >= 2) {
            $lastShown = $tracking->last_shown_at;
            $hoursSinceLastShown = $now->diffInHours($lastShown);
            
            return $hoursSinceLastShown >= 12;
        }

        // If clicked once today, show again tomorrow
        if ($tracking->daily_click_count >= 1) {
            return false;
        }

        return true;
    }

    /**
     * Record modal shown
     */
    public static function recordModalShown($userId, $modalType = 'welcome_guide')
    {
        $today = Carbon::today();
        $now = Carbon::now();
        
        $tracking = self::firstOrCreate([
            'user_id' => $userId,
            'modal_type' => $modalType,
            'current_date' => $today,
        ], [
            'daily_click_count' => 0,
            'dismissed_today' => false,
            'click_history' => []
        ]);

        $tracking->update([
            'last_shown_at' => $now
        ]);

        return $tracking;
    }

    /**
     * Record modal click/interaction
     */
    public static function recordModalClick($userId, $modalType = 'welcome_guide', $action = 'clicked')
    {
        $today = Carbon::today();
        $now = Carbon::now();
        
        // Debug logging
        Log::info('Recording modal click', [
            'user_id' => $userId,
            'modal_type' => $modalType,
            'action' => $action,
            'date' => $today->format('Y-m-d'),
            'time' => $now->format('H:i:s')
        ]);
        
        $tracking = self::firstOrCreate([
            'user_id' => $userId,
            'modal_type' => $modalType,
            'current_date' => $today,
        ], [
            'daily_click_count' => 0,
            'dismissed_today' => false,
            'click_history' => []
        ]);

        // Update click history
        $clickHistory = $tracking->click_history ?? [];
        $clickHistory[] = [
            'timestamp' => $now->toISOString(),
            'action' => $action
        ];

        $oldClickCount = $tracking->daily_click_count;
        $newClickCount = $tracking->daily_click_count + 1;
        
        $tracking->update([
            'daily_click_count' => $newClickCount,
            'last_clicked_at' => $now,
            'click_history' => $clickHistory,
            'dismissed_today' => in_array($action, ['dismissed', 'dismissed_today', 'dismissed_permanently'])
        ]);

        // Debug logging
        Log::info('Modal click recorded', [
            'user_id' => $userId,
            'old_click_count' => $oldClickCount,
            'new_click_count' => $newClickCount,
            'dismissed' => $tracking->dismissed_today
        ]);

        return $tracking;
    }

    /**
     * Get modal display schedule for user
     */
    public static function getModalSchedule($userId, $modalType = 'welcome_guide')
    {
        $today = Carbon::today();
        $tracking = self::where([
            'user_id' => $userId,
            'modal_type' => $modalType,
            'current_date' => $today,
        ])->first();

        if (!$tracking) {
            return [
                'should_show' => true,
                'click_count' => 0,
                'next_show_time' => 'now',
                'schedule' => 'first_time'
            ];
        }

        $shouldShow = self::shouldShowModal($userId, $modalType);
        $nextShowTime = null;

        if (!$shouldShow) {
            if ($tracking->dismissed_today) {
                $nextShowTime = 'tomorrow';
            } elseif ($tracking->daily_click_count >= 2) {
                $lastShown = $tracking->last_shown_at;
                $nextShow = $lastShown->addHours(12);
                $nextShowTime = $nextShow->diffForHumans();
            } elseif ($tracking->daily_click_count >= 1) {
                $nextShowTime = 'tomorrow';
            }
        }

        return [
            'should_show' => $shouldShow,
            'click_count' => $tracking->daily_click_count,
            'next_show_time' => $nextShowTime ?: 'now',
            'schedule' => $tracking->daily_click_count >= 2 ? '12_hourly' : 'daily',
            'last_shown' => $tracking->last_shown_at,
            'dismissed_today' => $tracking->dismissed_today
        ];
    }

    /**
     * Reset daily tracking (called by scheduler)
     */
    public static function resetDailyTracking()
    {
        $yesterday = Carbon::yesterday();
        
        // Clean up old records (keep last 7 days)
        self::where('current_date', '<', $yesterday->subDays(7))->delete();
        
        return true;
    }
}
