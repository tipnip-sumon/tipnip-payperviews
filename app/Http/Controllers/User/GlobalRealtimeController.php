<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Transaction;
use App\Models\VideoView;

class GlobalRealtimeController extends Controller
{
    /**
     * Get global real-time data for all dashboard components
     */
    public function getGlobalData(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Use cache for performance
            $cacheKey = "global_realtime_data_user_{$user->id}";
            $cacheTime = 30; // 30 seconds cache
            
            $data = Cache::remember($cacheKey, $cacheTime, function () use ($user) {
                return $this->compileGlobalData($user);
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString(),
                'cache_expires_in' => $cacheTime
            ]);

        } catch (\Exception $e) {
            Log::error('Global realtime data error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch global data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Compile comprehensive global data (simplified version)
     */
    private function compileGlobalData($user)
    {
        try {
            // Get fresh user data
            $user->refresh();
            
            // Basic balance calculations
            $depositWallet = floatval($user->deposit_wallet ?? 0);
            $interestWallet = floatval($user->interest_wallet ?? 0);
            $totalBalance = $depositWallet + $interestWallet;
            
            // Return basic data structure
            return [
                // Main balances
                'total_balance' => $totalBalance,
                'deposit_wallet' => $depositWallet,
                'interest_wallet' => $interestWallet,
                'total_investment' => 0,
                'total_earnings' => $interestWallet,
                'referral_earnings' => 0,
                'video_earnings' => 0,
                'daily_earnings' => 0,
                
                // Quick stats - simplified
                'quick_stats' => [
                    'total_balance' => $totalBalance,
                    'total_investment' => 0,
                    'total_earnings' => $interestWallet,
                    'active_plans' => 0,
                    'pending_withdrawals' => 0,
                    'today_earnings' => 0
                ],
                
                // Notifications - simplified
                'notifications_count' => 0,
                'recent_notifications' => [],
                
                // Investment data - simplified
                'investment_performance' => [
                    'total_invested' => 0,
                    'total_returned' => 0,
                    'roi_percentage' => 0,
                    'profit' => 0
                ],
                'active_investments' => 0,
                'completed_investments' => 0,
                
                // Referral data - simplified
                'total_referrals' => 0,
                'active_referrals' => 0,
                
                // Video data - simplified
                'total_video_views' => 0,
                'today_video_views' => 0,
                
                // User activity
                'last_login' => $user->last_login_at?->diffForHumans() ?? 'Never',
                'member_since' => $user->created_at->diffForHumans(),
                'account_status' => $user->status ?? 'active',
                
                // Performance metrics - simplified
                'monthly_earnings' => 0,
                'growth_percentage' => 0,
                
                // Additional stats for different sections
                'navbar_stats' => [
                    'balance' => $totalBalance,
                    'notifications' => 0,
                    'status' => 'online'
                ],
                
                'sidebar_stats' => [
                    'total_balance' => $totalBalance,
                    'today_earnings' => 0,
                    'active_plans' => 0
                ],
                
                'dashboard_stats' => [
                    'main_balance' => $totalBalance,
                    'video_access_vault' => 0,
                    'earnings_hub' => $interestWallet,
                    'referral_bonus' => 0
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Global data compilation failed: ' . $e->getMessage());
            
            // Return basic fallback data
            return [
                'total_balance' => floatval($user->deposit_wallet ?? 0) + floatval($user->interest_wallet ?? 0),
                'deposit_wallet' => floatval($user->deposit_wallet ?? 0),
                'interest_wallet' => floatval($user->interest_wallet ?? 0),
                'total_investment' => 0,
                'total_earnings' => floatval($user->interest_wallet ?? 0),
                'referral_earnings' => 0,
                'video_earnings' => 0,
                'daily_earnings' => 0,
                'quick_stats' => [],
                'notifications_count' => 0,
                'recent_notifications' => [],
                'investment_performance' => ['total_invested' => 0, 'total_returned' => 0, 'roi_percentage' => 0, 'profit' => 0],
                'active_investments' => 0,
                'completed_investments' => 0,
                'total_referrals' => 0,
                'active_referrals' => 0,
                'total_video_views' => 0,
                'today_video_views' => 0,
                'last_login' => 'Never',
                'member_since' => $user->created_at->diffForHumans(),
                'account_status' => 'active',
                'monthly_earnings' => 0,
                'growth_percentage' => 0,
                'navbar_stats' => ['balance' => floatval($user->deposit_wallet ?? 0) + floatval($user->interest_wallet ?? 0), 'notifications' => 0, 'status' => 'online'],
                'sidebar_stats' => ['total_balance' => floatval($user->deposit_wallet ?? 0) + floatval($user->interest_wallet ?? 0), 'today_earnings' => 0, 'active_plans' => 0],
                'dashboard_stats' => ['main_balance' => floatval($user->deposit_wallet ?? 0) + floatval($user->interest_wallet ?? 0), 'video_access_vault' => 0, 'earnings_hub' => floatval($user->interest_wallet ?? 0), 'referral_bonus' => 0]
            ];
        }
    }

    /**
     * Clear global data cache for user
     */
    public function clearCache(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $cacheKey = "global_realtime_data_user_{$user->id}";
            Cache::forget($cacheKey);
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }
}
