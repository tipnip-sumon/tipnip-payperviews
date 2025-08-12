<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class BalanceController extends Controller
{
    /**
     * Get current user's balance data for real-time updates
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getBalanceData(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get fresh user data from database
            $freshUser = \App\Models\User::find($user->id);
            
            if (!$freshUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $balances = [
                'deposit_wallet' => number_format($freshUser->deposit_wallet ?? 0, 2, '.', ''),
                'interest_wallet' => number_format($freshUser->interest_wallet ?? 0, 2, '.', ''),
                'total_balance' => number_format(($freshUser->deposit_wallet ?? 0) + ($freshUser->interest_wallet ?? 0), 2, '.', ''),
                'formatted' => [
                    'deposit_wallet' => '$' . number_format($freshUser->deposit_wallet ?? 0, 2),
                    'interest_wallet' => '$' . number_format($freshUser->interest_wallet ?? 0, 2),
                    'total_balance' => '$' . number_format(($freshUser->deposit_wallet ?? 0) + ($freshUser->interest_wallet ?? 0), 2)
                ]
            ];

            return response()->json([
                'success' => true,
                'balances' => $balances,
                'timestamp' => now()->toISOString(),
                'user_id' => $freshUser->id
            ]);

        } catch (Exception $e) {
            Log::error('Balance data fetch error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch balance data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Force refresh user balance from database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshBalance(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Force fresh data from database
            DB::connection()->getPdo()->exec('SELECT 1'); // Wake up connection
            
            return $this->getBalanceData($request);

        } catch (Exception $e) {
            Log::error('Balance refresh error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to refresh balance data'
            ], 500);
        }
    }
}
