<?php

use App\Models\User;
use App\Models\GeneralSetting;
use App\Models\KycVerification;
use App\Models\Invest;

if (!function_exists('checkTransferConditions')) {
    /**
     * Check if user meets all transfer conditions
     *
     * @param User $user
     * @return array
     */
    function checkTransferConditions($user)
    {
        $conditions = getTransferConditions();
        $failures = [];
        $requirements = [];

        // 1. KYC Verification Check
        if ($conditions['kyc_required']) {
            $requirements[] = [
                'name' => 'KYC Verification',
                'icon' => 'fas fa-id-card',
                'description' => 'Complete identity verification',
                'status' => $user->kv == 1,
                'action_url' => route('user.kyc.index'),
                'action_text' => 'Complete KYC'
            ];
            
            if ($user->kv != 1) {
                $failures[] = 'KYC verification is required';
            }
        }

        // 2. Email Verification Check
        if ($conditions['email_verification_required']) {
            $requirements[] = [
                'name' => 'Email Verification',
                'icon' => 'fas fa-envelope-check',
                'description' => 'Verify your email address',
                'status' => $user->email_verified_at !== null,
                'action_url' => route('verification.notice'),
                'action_text' => 'Verify Email'
            ];
            
            if ($user->email_verified_at === null) {
                $failures[] = 'Email verification is required';
            }
        }

        // 3. Profile Completion Check
        if ($conditions['profile_complete_required']) {
            $isProfileComplete = checkProfileCompletion($user);
            
            $requirements[] = [
                'name' => 'Profile Completion',
                'icon' => 'fas fa-user-cog',
                'description' => 'Complete your profile information',
                'status' => $isProfileComplete,
                'action_url' => route('profile.edit'),
                'action_text' => 'Complete Profile'
            ];
            
            if (!$isProfileComplete) {
                $failures[] = 'Profile completion is required';
            }
        }

        // 4. Referral Condition Check
        if ($conditions['referral_required']) {
            $hasActiveReferral = checkReferralCondition($user);
            $referralConditions = $conditions['referral_conditions'] ?? [];
            
            // Build dynamic description based on conditions
            $description = 'At least ' . ($referralConditions['minimum_referrals'] ?? 1) . ' active referral';
            if (($referralConditions['minimum_referrals'] ?? 1) > 1) {
                $description .= 's';
            }
            
            if ($referralConditions['require_active_investment'] ?? true) {
                $minAmount = $referralConditions['minimum_investment_amount'] ?? 50;
                if ($referralConditions['allow_multiple_small_investments'] ?? false) {
                    $description .= ' with total investment of $' . $minAmount . '+';
                } else {
                    $description .= ' with minimum $' . $minAmount . ' investment';
                }
                
                if (($referralConditions['minimum_investment_duration_days'] ?? 0) > 0) {
                    $description .= ' (min ' . $referralConditions['minimum_investment_duration_days'] . ' days old)';
                }
            }
            
            $requirements[] = [
                'name' => 'Referral Requirement',
                'icon' => 'fas fa-users',
                'description' => $description,
                'status' => $hasActiveReferral,
                'action_url' => route('user.team-tree'),
                'action_text' => 'View Referrals'
            ];
            
            if (!$hasActiveReferral) {
                $failures[] = $description . ' is required';
            }
        }

        return [
            'allowed' => empty($failures),
            'failures' => $failures,
            'requirements' => $requirements,
            'conditions' => $conditions
        ];
    }
}

if (!function_exists('checkWithdrawalConditions')) {
    /**
     * Check if user meets all withdrawal conditions
     *
     * @param User $user
     * @return array
     */
    function checkWithdrawalConditions($user)
    {
        $conditions = getWithdrawalConditions();
        $failures = [];
        $requirements = [];

        // 1. KYC Verification Check
        if ($conditions['kyc_required']) {
            $requirements[] = [
                'name' => 'KYC Verification',
                'icon' => 'fas fa-id-card',
                'description' => 'Complete identity verification',
                'status' => $user->kv == 1,
                'action_url' => route('user.kyc.index'),
                'action_text' => 'Complete KYC'
            ];
            
            if ($user->kv != 1) {
                $failures[] = 'KYC verification is required';
            }
        }

        // 2. Email Verification Check
        if ($conditions['email_verification_required']) {
            $requirements[] = [
                'name' => 'Email Verification',
                'icon' => 'fas fa-envelope-check',
                'description' => 'Verify your email address',
                'status' => $user->email_verified_at !== null,
                'action_url' => route('verification.notice'),
                'action_text' => 'Verify Email'
            ];
            
            if ($user->email_verified_at === null) {
                $failures[] = 'Email verification is required';
            }
        }

        // 3. Profile Completion Check
        if ($conditions['profile_complete_required']) {
            $isProfileComplete = checkProfileCompletion($user);
            
            $requirements[] = [
                'name' => 'Profile Completion',
                'icon' => 'fas fa-user-cog',
                'description' => 'Complete your profile information',
                'status' => $isProfileComplete,
                'action_url' => route('profile.edit'),
                'action_text' => 'Complete Profile'
            ];
            
            if (!$isProfileComplete) {
                $failures[] = 'Profile completion is required';
            }
        }

        // 4. Minimum Investment Duration Check (User's own investment)
        if (($conditions['minimum_investment_duration_days'] ?? 0) > 0) {
            $minDuration = $conditions['minimum_investment_duration_days'];
            $hasValidInvestmentDuration = checkUserInvestmentDuration($user, $minDuration);
            
            $requirements[] = [
                'name' => 'Minimum Investment Duration',
                'icon' => 'fas fa-clock',
                'description' => 'Investment must be at least ' . $minDuration . ' days old',
                'status' => $hasValidInvestmentDuration,
                'action_url' => route('user.invest-history'),
                'action_text' => 'View Investments'
            ];
            
            if (!$hasValidInvestmentDuration) {
                $failures[] = 'Minimum investment duration of ' . $minDuration . ' days is required';
            }
        }

        // 5. Referral Condition Check
        if ($conditions['referral_required']) {
            $hasActiveReferral = checkReferralCondition($user);
            $referralConditions = $conditions['referral_conditions'] ?? [];
            
            // Build dynamic description based on conditions
            $description = 'At least ' . ($referralConditions['minimum_referrals'] ?? 1) . ' active referral';
            if (($referralConditions['minimum_referrals'] ?? 1) > 1) {
                $description .= 's';
            }
            
            if ($referralConditions['require_active_investment'] ?? true) {
                $minAmount = $referralConditions['minimum_investment_amount'] ?? 50;
                if ($referralConditions['allow_multiple_small_investments'] ?? false) {
                    $description .= ' with total investment of $' . $minAmount . '+';
                } else {
                    $description .= ' with minimum $' . $minAmount . ' investment';
                }
                
                if (($referralConditions['minimum_investment_duration_days'] ?? 0) > 0) {
                    $description .= ' (min ' . $referralConditions['minimum_investment_duration_days'] . ' days old)';
                }
            }
            
            $requirements[] = [
                'name' => 'Referral Requirement',
                'icon' => 'fas fa-users',
                'description' => $description,
                'status' => $hasActiveReferral,
                'action_url' => route('user.team-tree'),
                'action_text' => 'View Referrals'
            ];
            
            if (!$hasActiveReferral) {
                $failures[] = $description . ' is required';
            }
        }

        return [
            'allowed' => empty($failures),
            'failures' => $failures,
            'requirements' => $requirements,
            'conditions' => $conditions
        ];
    }
}

if (!function_exists('getTransferConditions')) {
    /**
     * Get transfer conditions from settings
     *
     * @return array
     */
    function getTransferConditions()
    {
        $conditions = GeneralSetting::getSetting('transfer_conditions', []);
        
        // Set default values if not configured
        return array_merge([
            'kyc_required' => true,
            'email_verification_required' => true,
            'profile_complete_required' => true,
            'referral_required' => true,
            'referral_conditions' => [
                'enabled' => true,
                'minimum_referrals' => 1,
                'minimum_investment_amount' => 50,
                'require_active_investment' => true,
                'allow_multiple_small_investments' => false, // If true, allows multiple smaller investments to sum up to minimum
                'minimum_investment_duration_days' => 0, // 0 means no minimum duration
            ]
        ], $conditions);
    }
}

if (!function_exists('getWithdrawalConditions')) {
    /**
     * Get withdrawal conditions from settings
     *
     * @return array
     */
    function getWithdrawalConditions()
    {
        $conditions = GeneralSetting::getSetting('withdrawal_conditions', []);
        
        // Set default values if not configured
        return array_merge([
            'kyc_required' => true,
            'email_verification_required' => true,
            'profile_complete_required' => true,
            'minimum_investment_duration_days' => 0, // User's own investment minimum duration
            'referral_required' => true,
            'referral_conditions' => [
                'enabled' => true,
                'minimum_referrals' => 1,
                'minimum_investment_amount' => 50,
                'require_active_investment' => true,
                'allow_multiple_small_investments' => false, // If true, allows multiple smaller investments to sum up to minimum
                'minimum_investment_duration_days' => 0, // 0 means no minimum duration for referral investments
            ]
        ], $conditions);
    }
}

if (!function_exists('checkProfileCompletion')) {
    /**
     * Check if user profile is complete
     *
     * @param User $user
     * @return bool
     */
    function checkProfileCompletion($user)
    {
        $requiredFields = [
            'firstname',
            'lastname',
            'mobile',
            'country',
            'address'
        ];

        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('checkReferralCondition')) {
    /**
     * Check if user has referrals that meet the configured requirements
     *
     * @param User $user
     * @param array|null $referralConditions Optional specific conditions to check
     * @return bool
     */
    function checkReferralCondition($user, $referralConditions = null)
    {
        // Get referral conditions from settings if not provided
        if ($referralConditions === null) {
            $transferConditions = getTransferConditions();
            $referralConditions = $transferConditions['referral_conditions'] ?? [];
        }
        
        // Default values
        $referralConditions = array_merge([
            'enabled' => true,
            'minimum_referrals' => 1,
            'minimum_investment_amount' => 50,
            'require_active_investment' => true,
            'allow_multiple_small_investments' => false,
            'minimum_investment_duration_days' => 0,
        ], $referralConditions);
        
        if (!$referralConditions['enabled']) {
            return true; // If referral conditions are disabled, always pass
        }
        
        // Get all direct referrals
        $referrals = User::where('ref_by', $user->id)->get();
        
        if ($referrals->count() < $referralConditions['minimum_referrals']) {
            return false; // Not enough referrals
        }
        
        $validReferrals = 0;
        
        foreach ($referrals as $referral) {
            $referralValid = false;
            
            if ($referralConditions['require_active_investment']) {
                // Build investment query
                $investmentQuery = Invest::where('user_id', $referral->id)
                    ->where('status', true); // Active investments
                
                // Add minimum duration check if specified
                if ($referralConditions['minimum_investment_duration_days'] > 0) {
                    $minimumDate = now()->subDays($referralConditions['minimum_investment_duration_days']);
                    $investmentQuery->where('created_at', '<=', $minimumDate);
                }
                
                if ($referralConditions['allow_multiple_small_investments']) {
                    // Allow multiple smaller investments to sum up to minimum amount
                    $totalInvestment = $investmentQuery->sum('amount');
                    $referralValid = $totalInvestment >= $referralConditions['minimum_investment_amount'];
                } else {
                    // Require at least one investment meeting the minimum amount
                    $referralValid = $investmentQuery
                        ->where('amount', '>=', $referralConditions['minimum_investment_amount'])
                        ->exists();
                }
            } else {
                // If active investment is not required, just check if referral exists
                $referralValid = true;
            }
            
            if ($referralValid) {
                $validReferrals++;
            }
        }
        
        return $validReferrals >= $referralConditions['minimum_referrals'];
    }
}

if (!function_exists('getRequirementsSummary')) {
    /**
     * Get a summary of requirement completion for both transfer and withdrawal
     *
     * @param User $user
     * @return array
     */
    function getRequirementsSummary($user)
    {
        $transferCheck = checkTransferConditions($user);
        $withdrawalCheck = checkWithdrawalConditions($user);
        
        return [
            'transfer' => $transferCheck,
            'withdrawal' => $withdrawalCheck,
            'overall_status' => [
                'transfer_allowed' => $transferCheck['allowed'],
                'withdrawal_allowed' => $withdrawalCheck['allowed'],
                'total_requirements' => count($transferCheck['requirements']),
                'completed_requirements' => count(array_filter($transferCheck['requirements'], function($req) {
                    return $req['status'];
                }))
            ]
        ];
    }
}

if (!function_exists('checkUserInvestmentDuration')) {
    /**
     * Check if user has an investment that meets minimum duration requirement
     *
     * @param User $user
     * @param int $minDurationDays
     * @return bool
     */
    function checkUserInvestmentDuration($user, $minDurationDays)
    {
        if ($minDurationDays <= 0) {
            return true; // No duration requirement
        }
        
        $minDate = now()->subDays($minDurationDays);
        
        // Check if user has any active investment that is older than the minimum duration
        $validInvestment = Invest::where('user_id', $user->id)
            ->where('status', 1) // Active investment
            ->where('created_at', '<=', $minDate)
            ->exists();
            
        return $validInvestment;
    }
}
