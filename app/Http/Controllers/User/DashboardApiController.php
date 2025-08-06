<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class DashboardApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get real-time dashboard data
     */
    public function getDashboardData(Request $request)
    {
        try {
            $user = Auth::user();
            $data = getDashboardData($user->id, false); // No cache for real-time data

            return Response::json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get quick stats for dashboard cards
     */
    public function getQuickStats(Request $request)
    {
        try {
            $user = Auth::user();
            $stats = getDashboardQuickStats($user->id);

            return Response::json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Failed to fetch quick stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get investment data
     */
    public function getInvestmentData(Request $request)
    {
        try {
            $user = Auth::user();
            $data = getInvestmentData($user->id);

            return Response::json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Failed to fetch investment data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get transaction data
     */
    public function getTransactionData(Request $request)
    {
        try {
            $user = Auth::user();
            $data = getTransactionData($user->id);

            return Response::json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Failed to fetch transaction data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get referral data
     */
    public function getReferralData(Request $request)
    {
        try {
            $user = Auth::user();
            $data = getReferralData($user->id);

            return Response::json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Failed to fetch referral data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get video system data
     */
    public function getVideoSystemData(Request $request)
    {
        try {
            $user = Auth::user();
            $data = getVideoSystemData($user->id);

            return Response::json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Failed to fetch video system data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(Request $request)
    {
        try {
            $user = Auth::user();
            $data = getPerformanceMetrics($user->id);

            return Response::json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Failed to fetch performance metrics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 10);
            $data = getRecentActivities($user->id, $limit);

            return Response::json([
                'success' => true,
                'activities' => $data,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Failed to fetch recent activities',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache(Request $request)
    {
        try {
            $user = Auth::user();
            clearDashboardCache($user->id);

            return Response::json([
                'success' => true,
                'message' => 'Dashboard cache cleared successfully',
            ]);
        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update referral share count
     */
    public function updateShareCount(Request $request)
    {
        try {
            $action = $request->input('action');

            if ($action === 'increment_share') {
                // Get current share count from session
                $currentCount = session('referral_shares_today', 0);
                $newCount = $currentCount + 1;

                // Update session
                session(['referral_shares_today' => $newCount]);

                // Optionally, you can also store in database for permanent tracking
                // $user = Auth::user();
                // $user->increment('total_shares');

                return Response::json([
                    'success' => true,
                    'new_count' => $newCount,
                    'message' => 'Share count updated successfully',
                ]);
            }

            return Response::json([
                'success' => false,
                'message' => 'Invalid action provided',
            ], 400);

        } catch (\Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Failed to update share count',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
