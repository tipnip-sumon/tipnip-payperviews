<?php

use App\Models\User;
use App\Models\Referral;
use App\Models\ReferralCommission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

/**
 * User Helper Functions
 * Specialized functions for user management and referral operations
 */

if (!function_exists('createUserReferral')) {
    /**
     * Create referral relationship and process commission
     */
    function createUserReferral($userId, $referrerId, $level = 1)
    {
        try {
            $referral = Referral::create([
                'user_id' => $userId,
                'referrer_id' => $referrerId,
                'level' => $level,
                'status' => 'active',
                'created_at' => now(),
            ]);

            // Trigger welcome notification for referrer
            $user = User::find($userId);
            $referrer = User::find($referrerId);
            
            if ($user && $referrer) {
                notifyUser($referrerId, 'referral', 'New Referral!', 
                    "Congratulations! {$user->username} joined using your referral link.", [
                        'icon' => 'fas fa-users',
                        'data' => [
                            'type' => 'new_referral',
                            'referred_user' => $user->username,
                            'level' => $level
                        ]
                    ]
                );
            }

            return $referral;
        } catch (\Exception $e) {
            Log::error('Failed to create user referral: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('processReferralCommission')) {
    /**
     * Process referral commission for multiple levels
     */
    function processReferralCommission($userId, $amount, $type = 'deposit')
    {
        try {
            $user = User::find($userId);
            if (!$user || !$user->ref_by) {
                return false;
            }

            $commissionSettings = getCommissionSettings();
            $processedLevels = [];

            // Get referral chain
            $currentUser = $user;
            $level = 1;

            while ($currentUser && $currentUser->ref_by && $level <= $commissionSettings['max_levels']) {
                $referrer = User::find($currentUser->ref_by);
                if (!$referrer) break;

                $commissionRate = $commissionSettings['level_' . $level] ?? 0;
                if ($commissionRate > 0) {
                    $commissionAmount = ($amount * $commissionRate) / 100;

                    // Create commission record
                    $commission = ReferralCommission::create([
                        'user_id' => $referrer->id,
                        'referred_user_id' => $user->id,
                        'level' => $level,
                        'amount' => $commissionAmount,
                        'commission_rate' => $commissionRate,
                        'original_amount' => $amount,
                        'type' => $type,
                        'status' => 'completed',
                    ]);

                    // Credit commission to referrer
                    creditUser($referrer->id, $commissionAmount, 'Referral Commission', [
                        'commission_id' => $commission->id,
                        'from_user' => $user->username,
                        'level' => $level,
                        'type' => 'referral_commission'
                    ]);

                    // Send notification
                    notifyUserCommission($referrer->id, $commissionAmount, $user->username, $level);

                    $processedLevels[] = [
                        'level' => $level,
                        'referrer_id' => $referrer->id,
                        'amount' => $commissionAmount,
                        'rate' => $commissionRate
                    ];
                }

                $currentUser = $referrer;
                $level++;
            }

            return $processedLevels;
        } catch (\Exception $e) {
            Log::error('Failed to process referral commission: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('getUserReferrals')) {
    /**
     * Get user's direct and indirect referrals
     */
    function getUserReferrals($userId, $level = null)
    {
        try {
            $query = User::where('ref_by', $userId);

            if ($level) {
                // For specific level, we need to traverse the chain
                $referrals = collect();
                $currentLevel = 1;
                $currentUsers = [$userId];

                while ($currentLevel <= $level && !empty($currentUsers)) {
                    $levelReferrals = User::whereIn('ref_by', $currentUsers)->get();
                    
                    if ($currentLevel == $level) {
                        $referrals = $levelReferrals;
                        break;
                    }

                    $currentUsers = $levelReferrals->pluck('id')->toArray();
                    $currentLevel++;
                }

                return $referrals;
            }

            return $query->get();
        } catch (\Exception $e) {
            Log::error('Failed to get user referrals: ' . $e->getMessage());
            return collect();
        }
    }
}

if (!function_exists('getReferralStats')) {
    /**
     * Get comprehensive referral statistics for user
     */
    function getReferralStats($userId)
    {
        try {
            $directReferrals = User::where('ref_by', $userId)->count();
            
            $totalCommissions = ReferralCommission::where('user_id', $userId)
                ->where('status', 'completed')
                ->sum('amount');

            $monthlyCommissions = ReferralCommission::where('user_id', $userId)
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount');

            $levelStats = ReferralCommission::where('user_id', $userId)
                ->where('status', 'completed')
                ->selectRaw('level, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('level')
                ->get()
                ->keyBy('level');

            return [
                'direct_referrals' => $directReferrals,
                'total_commissions' => $totalCommissions,
                'monthly_commissions' => $monthlyCommissions,
                'level_stats' => $levelStats,
                'commission_count' => ReferralCommission::where('user_id', $userId)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get referral stats: ' . $e->getMessage());
            return [
                'direct_referrals' => 0,
                'total_commissions' => 0,
                'monthly_commissions' => 0,
                'level_stats' => collect(),
                'commission_count' => 0,
            ];
        }
    }
}

if (!function_exists('generateReferralCode')) {
    /**
     * Generate unique referral code for user
     */
    function generateReferralCode($userId, $length = 8)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('User not found');
            }

            // Generate code based on username + random
            $baseCode = strtoupper(substr($user->username, 0, 4));
            $randomCode = strtoupper(getNumber(4));
            $code = $baseCode . $randomCode;

            // Ensure uniqueness
            $attempts = 0;
            while (User::where('referral_code', $code)->exists() && $attempts < 10) {
                $randomCode = strtoupper(getNumber(4));
                $code = $baseCode . $randomCode;
                $attempts++;
            }

            $user->referral_code = $code;
            $user->save();

            return $code;
        } catch (\Exception $e) {
            Log::error('Failed to generate referral code: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('validateUserCredentials')) {
    /**
     * Validate user login credentials
     */
    function validateUserCredentials($username, $password)
    {
        try {
            $user = User::where('username', $username)
                       ->orWhere('email', $username)
                       ->first();

            if (!$user) {
                return ['valid' => false, 'error' => 'User not found'];
            }

            if (!Hash::check($password, $user->password)) {
                return ['valid' => false, 'error' => 'Invalid password'];
            }

            if ($user->status == 0) {
                return ['valid' => false, 'error' => 'Account is disabled'];
            }

            return ['valid' => true, 'user' => $user];
        } catch (\Exception $e) {
            Log::error('Failed to validate user credentials: ' . $e->getMessage());
            return ['valid' => false, 'error' => 'Validation failed'];
        }
    }
}

if (!function_exists('updateUserProfile')) {
    /**
     * Update user profile with validation
     */
    function updateUserProfile($userId, $data)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('User not found');
            }

            $allowedFields = ['firstname', 'lastname', 'address', 'city', 'state', 'zip', 'country_name'];
            $updateData = array_intersect_key($data, array_flip($allowedFields));

            $user->update($updateData);

            return ['success' => true, 'user' => $user->fresh()];
        } catch (\Exception $e) {
            Log::error('Failed to update user profile: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

if (!function_exists('getUserByReferral')) {
    /**
     * Find user by referral code or username
     */
    function getUserByReferral($referralCode)
    {
        try {
            return User::where('referral_code', $referralCode)
                      ->orWhere('username', $referralCode)
                      ->first();
        } catch (\Exception $e) {
            Log::error('Failed to get user by referral: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('getCommissionSettings')) {
    /**
     * Get commission settings from database or config
     */
    function getCommissionSettings()
    {
        try {
            return [
                'max_levels' => getSetting('max_referral_levels', 5),
                'level_1' => getSetting('referral_commission_level_1', 10),
                'level_2' => getSetting('referral_commission_level_2', 5),
                'level_3' => getSetting('referral_commission_level_3', 3),
                'level_4' => getSetting('referral_commission_level_4', 2),
                'level_5' => getSetting('referral_commission_level_5', 1),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get commission settings: ' . $e->getMessage());
            return [
                'max_levels' => 5,
                'level_1' => 10,
                'level_2' => 5,
                'level_3' => 3,
                'level_4' => 2,
                'level_5' => 1,
            ];
        }
    }
}
