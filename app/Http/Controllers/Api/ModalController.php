<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ModalController extends Controller
{
    /**
     * Update modal session data for frequency control
     */
    public function updateModalSession(Request $request)
    {
        $request->validate([
            'modal_name' => 'required|string',
            'action' => 'required|string',
            'frequency' => 'required|string|in:once,daily,weekly,session'
        ]);

        $modalName = $request->modal_name;
        $action = $request->action;
        $frequency = $request->frequency;

        try {
            // Session keys
            $shownKey = $modalName . '_shown';
            $lastShownKey = $modalName . '_last_shown';
            $countKey = $modalName . '_count';
            $dismissedKey = $modalName . '_dismissed_permanently';

            // Handle different actions
            switch ($action) {
                case 'shown':
                    // Mark as shown and update timestamp
                    Session::put($shownKey, true);
                    Session::put($lastShownKey, now());
                    
                    // Increment show count
                    $currentCount = Session::get($countKey, 0);
                    Session::put($countKey, $currentCount + 1);
                    break;

                case 'dismissed_permanent':
                    // Mark as permanently dismissed
                    Session::put($dismissedKey, true);
                    break;

                case 'dismissed_later':
                    // Just update the last shown timestamp
                    Session::put($lastShownKey, now());
                    break;
            }

            // Log for debugging
            Log::info('Modal session updated', [
                'modal_name' => $modalName,
                'action' => $action,
                'frequency' => $frequency,
                'session_data' => [
                    'shown' => Session::get($shownKey),
                    'last_shown' => Session::get($lastShownKey),
                    'count' => Session::get($countKey),
                    'dismissed_permanently' => Session::get($dismissedKey)
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Modal session updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Modal session update failed', [
                'error' => $e->getMessage(),
                'modal_name' => $modalName,
                'action' => $action
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update modal session'
            ], 500);
        }
    }

    /**
     * Track modal analytics
     */
    public function trackModalAnalytics(Request $request)
    {
        $request->validate([
            'modal_id' => 'required|integer',
            'modal_name' => 'required|string',
            'action' => 'required|string',
            'user_agent' => 'nullable|string',
            'timestamp' => 'required|string'
        ]);

        try {
            // Prepare analytics data
            $analyticsData = [
                'modal_id' => $request->modal_id,
                'modal_name' => $request->modal_name,
                'action' => $request->action,
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->user_agent,
                'url' => $request->header('referer'),
                'timestamp' => $request->timestamp,
                'created_at' => now()
            ];

            // You can store this in a dedicated analytics table or log it
            // For now, we'll use logs but you can extend this to use a database table
            Log::info('Modal Analytics', $analyticsData);

            // Optional: Store in database if you have a modal_analytics table
            /*
            \DB::table('modal_analytics')->insert($analyticsData);
            */

            return response()->json([
                'success' => true,
                'message' => 'Analytics tracked successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Modal analytics tracking failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to track analytics'
            ], 500);
        }
    }

    /**
     * Get modal settings for a specific modal
     */
    public function getModalSettings(Request $request, $modalName)
    {
        try {
            $modalSettings = DB::table('modal_settings')
                ->where('modal_name', $modalName)
                ->where('is_active', 1)
                ->first();

            if (!$modalSettings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Modal settings not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $modalSettings
            ]);

        } catch (\Exception $e) {
            Log::error('Get modal settings failed', [
                'error' => $e->getMessage(),
                'modal_name' => $modalName
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get modal settings'
            ], 500);
        }
    }

    /**
     * Update modal settings (admin function)
     */
    public function updateModalSettings(Request $request, $modalName)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'heading' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'target_users' => 'string|in:all,guests,new_users,verified,unverified',
            'show_frequency' => 'string|in:once,daily,weekly,session',
            'max_shows' => 'integer|min:1|max:100',
            'delay_seconds' => 'integer|min:0|max:60'
        ]);

        try {
            $updateData = $request->only([
                'title', 'subtitle', 'heading', 'description', 'is_active',
                'target_users', 'show_frequency', 'max_shows', 'delay_seconds'
            ]);
            
            $updateData['updated_at'] = now();

            $updated = DB::table('modal_settings')
                ->where('modal_name', $modalName)
                ->update($updateData);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Modal settings not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Modal settings updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Update modal settings failed', [
                'error' => $e->getMessage(),
                'modal_name' => $modalName,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update modal settings'
            ], 500);
        }
    }

    /**
     * Bulk action for modals (admin function)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:activate_all,deactivate_all,toggle_all',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $action = $request->action;
            $affectedCount = 0;

            switch ($action) {
                case 'activate_all':
                    $affectedCount = DB::table('modal_settings')
                        ->where('is_active', 0)
                        ->update(['is_active' => 1, 'updated_at' => now()]);
                    break;

                case 'deactivate_all':
                    $affectedCount = DB::table('modal_settings')
                        ->where('is_active', 1)
                        ->update(['is_active' => 0, 'updated_at' => now()]);
                    break;

                case 'toggle_all':
                    // First get all modals
                    $modals = DB::table('modal_settings')->get();
                    foreach ($modals as $modal) {
                        DB::table('modal_settings')
                            ->where('id', $modal->id)
                            ->update([
                                'is_active' => !$modal->is_active,
                                'updated_at' => now()
                            ]);
                        $affectedCount++;
                    }
                    break;
            }

            // Log the bulk action
            Log::info('Modal bulk action executed', [
                'action' => $action,
                'affected_count' => $affectedCount,
                'reason' => $request->reason,
                'admin_user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bulk action completed successfully',
                'affected_count' => $affectedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Modal bulk action failed', [
                'error' => $e->getMessage(),
                'action' => $request->action,
                'admin_user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to execute bulk action'
            ], 500);
        }
    }

    /**
     * Get quick statistics for modals
     */
    public function quickStats(Request $request)
    {
        try {
            $stats = [
                'total_modals' => DB::table('modal_settings')->count(),
                'active_modals' => DB::table('modal_settings')->where('is_active', 1)->count(),
                'inactive_modals' => DB::table('modal_settings')->where('is_active', 0)->count(),
                'pwa_modals' => DB::table('modal_settings')->where('modal_name', 'like', '%install%')->count(),
                'recent_modals' => DB::table('modal_settings')->where('created_at', '>=', now()->subDays(7))->count()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get modal quick stats', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics'
            ], 500);
        }
    }
}
