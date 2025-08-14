<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class ModalService
{
    /**
     * Get modals that should be displayed to the current user
     */
    public function getModalsToShow()
    {
        $user = Auth::user();
        $currentRoute = Request::route() ? Request::route()->getName() : null;
        $isGuest = !$user;
        
        // Get active modals from database
        $activeModals = DB::table('modal_settings')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
        
        $modalsToShow = [];
        
        foreach ($activeModals as $modal) {
            if ($this->shouldShowModal($modal, $user, $currentRoute, $isGuest)) {
                $modalsToShow[] = $this->formatModalData($modal);
            }
        }
        
        return $modalsToShow;
    }
    
    /**
     * Check if a modal should be shown to the current user
     */
    private function shouldShowModal($modal, $user, $currentRoute, $isGuest)
    {
        // Check target users
        if (!$this->checkTargetUsers($modal->target_users, $user, $isGuest)) {
            return false;
        }
        
        // Parse additional settings
        $additionalSettings = json_decode($modal->additional_settings, true) ?? [];
        
        // Check route restrictions
        if (!$this->checkRouteRestrictions($additionalSettings, $currentRoute)) {
            return false;
        }
        
        // Check device restrictions
        if (!$this->checkDeviceRestrictions($additionalSettings)) {
            return false;
        }
        
        // Check session time
        if (!$this->checkSessionTime($additionalSettings)) {
            return false;
        }
        
        // Check show frequency and limits
        if (!$this->checkShowFrequency($modal, $user)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if user matches target criteria
     */
    private function checkTargetUsers($targetUsers, $user, $isGuest)
    {
        switch ($targetUsers) {
            case 'all':
                return true;
            case 'guests':
                return $isGuest;
            case 'new_users':
                return $user && $user->created_at->diffInDays(now()) <= 7;
            case 'verified':
                return $user && $user->email_verified_at !== null;
            case 'unverified':
                return $user && $user->email_verified_at === null;
            default:
                return false;
        }
    }
    
    /**
     * Check route restrictions
     */
    private function checkRouteRestrictions($additionalSettings, $currentRoute)
    {
        $excludeRoutes = $additionalSettings['exclude_routes'] ?? [];
        $includeRoutes = $additionalSettings['include_routes'] ?? [];
        
        // If no current route (e.g., command line), allow all
        if ($currentRoute === null) {
            return true;
        }
        
        // If route is in exclude list, don't show
        if (in_array($currentRoute, $excludeRoutes)) {
            return false;
        }
        
        // If include list is specified and route is not in it, don't show
        if (!empty($includeRoutes) && !in_array($currentRoute, $includeRoutes)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check device restrictions
     */
    private function checkDeviceRestrictions($additionalSettings)
    {
        $isMobile = request()->attributes->get('is_mobile', false);
        
        $showOnMobileOnly = $additionalSettings['show_on_mobile_only'] ?? false;
        $showOnDesktopOnly = $additionalSettings['show_on_desktop_only'] ?? false;
        
        if ($showOnMobileOnly && !$isMobile) {
            return false;
        }
        
        if ($showOnDesktopOnly && $isMobile) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check minimum session time
     */
    private function checkSessionTime($additionalSettings)
    {
        $minimumSessionTime = $additionalSettings['minimum_session_time'] ?? 0;
        
        // Skip session time check if no session exists (e.g., API calls, command line)
        if (!Session::isStarted()) {
            return true;
        }
        
        if ($minimumSessionTime > 0) {
            $sessionStart = Session::get('session_start_time', now());
            $sessionDuration = now()->diffInSeconds($sessionStart);
            
            if ($sessionDuration < $minimumSessionTime) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check show frequency and limits
     */
    private function checkShowFrequency($modal, $user)
    {
        $modalName = $modal->modal_name;
        $showFrequency = $modal->show_frequency;
        $maxShows = $modal->max_shows;
        
        // For guests, use session-based tracking
        if (!$user) {
            return $this->checkGuestFrequency($modalName, $showFrequency, $maxShows);
        }
        
        // For logged-in users, use database tracking
        return $this->checkUserFrequency($modalName, $showFrequency, $maxShows, $user->id);
    }
    
    /**
     * Check show frequency for guest users (session-based)
     */
    private function checkGuestFrequency($modalName, $showFrequency, $maxShows)
    {
        $sessionKey = "modal_shows_{$modalName}";
        $shows = Session::get($sessionKey, []);
        $today = Carbon::today()->toDateString();
        
        switch ($showFrequency) {
            case 'once':
                return empty($shows);
            case 'daily':
                $todayShows = collect($shows)->filter(function($timestamp) use ($today) {
                    return Carbon::parse($timestamp)->toDateString() === $today;
                })->count();
                return $todayShows < $maxShows;
            case 'weekly':
                $weekAgo = Carbon::now()->subWeek();
                $recentShows = collect($shows)->filter(function($timestamp) use ($weekAgo) {
                    return Carbon::parse($timestamp)->isAfter($weekAgo);
                })->count();
                return $recentShows < $maxShows;
            case 'session':
                $sessionShows = Session::get("modal_session_shows_{$modalName}", 0);
                return $sessionShows < $maxShows;
            default:
                return true;
        }
    }
    
    /**
     * Check show frequency for logged-in users (database-based)
     */
    private function checkUserFrequency($modalName, $showFrequency, $maxShows, $userId)
    {
        $today = Carbon::today();
        
        switch ($showFrequency) {
            case 'once':
                $hasShown = DB::table('user_modal_tracking')
                    ->where('user_id', $userId)
                    ->where('modal_type', $modalName)
                    ->exists();
                return !$hasShown;
                
            case 'daily':
                $todayShows = DB::table('user_modal_tracking')
                    ->where('user_id', $userId)
                    ->where('modal_type', $modalName)
                    ->where('current_date', $today)
                    ->value('daily_click_count') ?? 0;
                return $todayShows < $maxShows;
                
            case 'weekly':
                $weekAgo = $today->copy()->subWeek();
                $weeklyShows = DB::table('user_modal_tracking')
                    ->where('user_id', $userId)
                    ->where('modal_type', $modalName)
                    ->where('current_date', '>=', $weekAgo)
                    ->sum('daily_click_count');
                return $weeklyShows < $maxShows;
                
            case 'session':
                $sessionShows = Session::get("modal_session_shows_{$modalName}", 0);
                return $sessionShows < $maxShows;
                
            default:
                return true;
        }
    }
    
    /**
     * Format modal data for frontend
     */
    private function formatModalData($modal)
    {
        $additionalSettings = json_decode($modal->additional_settings, true) ?? [];
        
        return [
            'id' => $modal->id,
            'modal_name' => $modal->modal_name,
            'title' => $modal->title,
            'subtitle' => $modal->subtitle,
            'heading' => $modal->heading,
            'description' => $modal->description,
            'is_active' => (bool) $modal->is_active,
            'show_frequency' => $modal->show_frequency,
            'max_shows' => $modal->max_shows,
            'delay_seconds' => $modal->delay_seconds,
            'minimum_session_time' => $additionalSettings['minimum_session_time'] ?? 0,
            'show_on_mobile_only' => $additionalSettings['show_on_mobile_only'] ?? false,
            'show_on_desktop_only' => $additionalSettings['show_on_desktop_only'] ?? false,
            'include_routes' => $additionalSettings['include_routes'] ?? [],
            'exclude_routes' => $additionalSettings['exclude_routes'] ?? [],
            'custom_css' => $additionalSettings['custom_css'] ?? '',
            'custom_js' => $additionalSettings['custom_js'] ?? '',
            'settings' => $additionalSettings
        ];
    }
    
    /**
     * Record modal show event
     */
    public function recordModalShow($modalName, $userId = null)
    {
        $now = now();
        $today = Carbon::today();
        
        if ($userId) {
            // Update or create tracking record for logged-in users
            DB::table('user_modal_tracking')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'modal_type' => $modalName,
                    'current_date' => $today
                ],
                [
                    'daily_click_count' => DB::raw('daily_click_count + 1'),
                    'last_shown_at' => $now,
                    'updated_at' => $now
                ]
            );
        } else {
            // Update session tracking for guests
            $sessionKey = "modal_shows_{$modalName}";
            $shows = Session::get($sessionKey, []);
            $shows[] = $now->toISOString();
            Session::put($sessionKey, $shows);
            
            // Update session show count
            $sessionShowKey = "modal_session_shows_{$modalName}";
            Session::increment($sessionShowKey);
        }
    }
    
    /**
     * Record modal click event
     */
    public function recordModalClick($modalName, $userId = null)
    {
        $now = now();
        $today = Carbon::today();
        
        if ($userId) {
            DB::table('user_modal_tracking')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'modal_type' => $modalName,
                    'current_date' => $today
                ],
                [
                    'last_clicked_at' => $now,
                    'updated_at' => $now
                ]
            );
        }
        
        // Always record in session as well for immediate tracking
        $sessionKey = "modal_clicks_{$modalName}";
        $clicks = Session::get($sessionKey, []);
        $clicks[] = $now->toISOString();
        Session::put($sessionKey, $clicks);
    }
    
    /**
     * Record modal dismiss event
     */
    public function recordModalDismiss($modalName, $userId = null)
    {
        $today = Carbon::today();
        
        if ($userId) {
            DB::table('user_modal_tracking')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'modal_type' => $modalName,
                    'current_date' => $today
                ],
                [
                    'dismissed_today' => true,
                    'updated_at' => now()
                ]
            );
        }
        
        // Mark as dismissed in session
        Session::put("modal_dismissed_{$modalName}", true);
    }
}
