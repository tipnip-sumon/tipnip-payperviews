<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModalManagementController extends Controller
{
    /**
     * Display a listing of modal settings
     */
    public function index()
    {
        try {
            $modalSettings = DB::table('modal_settings')
                ->orderBy('modal_name')
                ->get();

            return view('admin.modal.index', compact('modalSettings'));
        } catch (\Exception $e) {
            Log::error('Error loading modal settings: ' . $e->getMessage());
            return back()->with('error', 'Error loading modal settings');
        }
    }

    /**
     * Show the form for creating a new modal
     */
    public function create()
    {
        return view('admin.modal.create');
    }

    /**
     * Store a newly created modal in database
     */
    public function store(Request $request)
    {
        $request->validate([
            'modal_name' => 'required|string|max:255|unique:modal_settings,modal_name',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'heading' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'target_users' => 'required|string|in:all,guests,new_users,verified,unverified',
            'show_frequency' => 'required|string|in:once,daily,weekly,session',
            'max_shows' => 'required|integer|min:1|max:100',
            'delay_seconds' => 'required|integer|min:0|max:60'
        ]);

        try {
            $additionalSettings = [
                'show_on_mobile_only' => $request->boolean('show_on_mobile_only'),
                'show_on_desktop_only' => $request->boolean('show_on_desktop_only'),
                'minimum_session_time' => $request->input('minimum_session_time', 30),
                'exclude_routes' => array_filter(explode(',', $request->input('exclude_routes', ''))),
                'include_routes' => array_filter(explode(',', $request->input('include_routes', ''))),
                'custom_css' => $request->input('custom_css', ''),
                'custom_js' => $request->input('custom_js', '')
            ];

            DB::table('modal_settings')->insert([
                'modal_name' => $request->modal_name,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'heading' => $request->heading,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'target_users' => $request->target_users,
                'show_frequency' => $request->show_frequency,
                'max_shows' => $request->max_shows,
                'delay_seconds' => $request->delay_seconds,
                'additional_settings' => json_encode($additionalSettings),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('admin.modal.index')
                ->with('success', 'Modal settings created successfully');

        } catch (\Exception $e) {
            Log::error('Error creating modal settings: ' . $e->getMessage());
            return back()->with('error', 'Error creating modal settings')->withInput();
        }
    }

    /**
     * Display the specified modal
     */
    public function show($id)
    {
        try {
            $modalSetting = DB::table('modal_settings')->where('id', $id)->first();
            
            if (!$modalSetting) {
                return redirect()->route('admin.modal.index')
                    ->with('error', 'Modal setting not found');
            }

            return view('admin.modal.show', compact('modalSetting'));
        } catch (\Exception $e) {
            Log::error('Error loading modal setting: ' . $e->getMessage());
            return back()->with('error', 'Error loading modal setting');
        }
    }

    /**
     * Show the form for editing the specified modal
     */
    public function edit($id)
    {
        try {
            $modalSetting = DB::table('modal_settings')->where('id', $id)->first();
            
            if (!$modalSetting) {
                return redirect()->route('admin.modal.index')
                    ->with('error', 'Modal setting not found');
            }

            return view('admin.modal.edit', compact('modalSetting'));
        } catch (\Exception $e) {
            Log::error('Error loading modal setting for edit: ' . $e->getMessage());
            return back()->with('error', 'Error loading modal setting');
        }
    }

    /**
     * Update the specified modal in database
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'heading' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'target_users' => 'required|string|in:all,guests,new_users,verified,unverified',
            'show_frequency' => 'required|string|in:once,daily,weekly,session',
            'max_shows' => 'required|integer|min:1|max:100',
            'delay_seconds' => 'required|integer|min:0|max:60'
        ]);

        try {
            $modalSetting = DB::table('modal_settings')->where('id', $id)->first();
            
            if (!$modalSetting) {
                return redirect()->route('admin.modal.index')
                    ->with('error', 'Modal setting not found');
            }

            $additionalSettings = [
                'show_on_mobile_only' => $request->boolean('show_on_mobile_only'),
                'show_on_desktop_only' => $request->boolean('show_on_desktop_only'),
                'minimum_session_time' => $request->input('minimum_session_time', 30),
                'exclude_routes' => array_filter(explode(',', $request->input('exclude_routes', ''))),
                'include_routes' => array_filter(explode(',', $request->input('include_routes', ''))),
                'custom_css' => $request->input('custom_css', ''),
                'custom_js' => $request->input('custom_js', '')
            ];

            DB::table('modal_settings')->where('id', $id)->update([
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'heading' => $request->heading,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
                'target_users' => $request->target_users,
                'show_frequency' => $request->show_frequency,
                'max_shows' => $request->max_shows,
                'delay_seconds' => $request->delay_seconds,
                'additional_settings' => json_encode($additionalSettings),
                'updated_at' => now()
            ]);

            return redirect()->route('admin.modal.index')
                ->with('success', 'Modal settings updated successfully');

        } catch (\Exception $e) {
            Log::error('Error updating modal settings: ' . $e->getMessage());
            return back()->with('error', 'Error updating modal settings')->withInput();
        }
    }

    /**
     * Remove the specified modal from database
     */
    public function destroy($id)
    {
        try {
            $modalSetting = DB::table('modal_settings')->where('id', $id)->first();
            
            if (!$modalSetting) {
                return redirect()->route('admin.modal.index')
                    ->with('error', 'Modal setting not found');
            }

            DB::table('modal_settings')->where('id', $id)->delete();

            return redirect()->route('admin.modal.index')
                ->with('success', 'Modal settings deleted successfully');

        } catch (\Exception $e) {
            Log::error('Error deleting modal settings: ' . $e->getMessage());
            return back()->with('error', 'Error deleting modal settings');
        }
    }

    /**
     * Toggle modal active status
     */
    public function toggleStatus($id)
    {
        try {
            $modalSetting = DB::table('modal_settings')->where('id', $id)->first();
            
            if (!$modalSetting) {
                return response()->json(['error' => 'Modal setting not found'], 404);
            }

            $newStatus = !$modalSetting->is_active;
            
            DB::table('modal_settings')->where('id', $id)->update([
                'is_active' => $newStatus,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'is_active' => $newStatus,
                'message' => 'Modal status updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling modal status: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating modal status'], 500);
        }
    }

    /**
     * Get modal analytics
     */
    public function analytics()
    {
        try {
            // This would be enhanced when you have a modal_analytics table
            $modalSettings = DB::table('modal_settings')
                ->select(['id', 'modal_name', 'title', 'is_active', 'target_users', 'show_frequency'])
                ->get();

            // Mock analytics data for now
            $analytics = $modalSettings->map(function ($modal) {
                return [
                    'modal_name' => $modal->modal_name,
                    'title' => $modal->title,
                    'is_active' => $modal->is_active,
                    'shows_today' => rand(10, 100),
                    'clicks_today' => rand(5, 50),
                    'dismissals_today' => rand(2, 20),
                    'conversion_rate' => rand(15, 45) . '%'
                ];
            });

            return view('admin.modal.analytics', compact('modalSettings', 'analytics'));

        } catch (\Exception $e) {
            Log::error('Error loading modal analytics: ' . $e->getMessage());
            return back()->with('error', 'Error loading modal analytics');
        }
    }

    /**
     * Handle bulk actions on modals
     */
    public function bulkAction(Request $request)
    {
        try {
            $action = $request->input('action');
            $reason = $request->input('reason', '');

            $affectedCount = 0;

            switch ($action) {
                case 'activate_all':
                    $affectedCount = DB::table('modal_settings')->update(['is_active' => true]);
                    break;
                
                case 'deactivate_all':
                    $affectedCount = DB::table('modal_settings')->update(['is_active' => false]);
                    break;
                
                case 'toggle_all':
                    // Toggle each modal's status
                    $modals = DB::table('modal_settings')->get();
                    foreach ($modals as $modal) {
                        DB::table('modal_settings')
                            ->where('id', $modal->id)
                            ->update(['is_active' => !$modal->is_active]);
                        $affectedCount++;
                    }
                    break;
                
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid action specified'
                    ], 400);
            }

            Log::info("Bulk action performed: {$action}", [
                'reason' => $reason,
                'affected_count' => $affectedCount,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bulk action completed successfully',
                'affected_count' => $affectedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error performing bulk action: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk action: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quick statistics for modals
     */
    public function quickStats()
    {
        try {
            $totalModals = DB::table('modal_settings')->count();
            $activeModals = DB::table('modal_settings')->where('is_active', true)->count();
            $inactiveModals = DB::table('modal_settings')->where('is_active', false)->count();
            $pwaModals = DB::table('modal_settings')->where('modal_name', 'like', '%install%')->count();
            
            // Mock some additional stats (replace with real data when tracking is implemented)
            $todayViews = rand(100, 1000);
            $todayClicks = rand(50, 500);
            $conversionRate = $todayViews > 0 ? round(($todayClicks / $todayViews) * 100, 2) : 0;

            $stats = [
                'total_modals' => $totalModals,
                'active_modals' => $activeModals,
                'inactive_modals' => $inactiveModals,
                'pwa_modals' => $pwaModals,
                'today_views' => $todayViews,
                'today_clicks' => $todayClicks,
                'conversion_rate' => $conversionRate . '%',
                'most_active' => DB::table('modal_settings')
                    ->where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->first()?->modal_name ?? 'None'
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting quick stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics'
            ], 500);
        }
    }
}
