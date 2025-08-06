<?php

namespace App\Http\Controllers\admin;

use Exception;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransferWithdrawConditionsController extends Controller
{
    /**
     * Display the transfer and withdrawal conditions settings page.
     */
    public function index()
    {
        try {
            $settings = GeneralSetting::getSettings();
            
            // Get current conditions or set defaults
            $transferConditions = $settings->transfer_conditions ?? [
                'kyc_required' => true,
                'email_verification_required' => true,
                'profile_complete_required' => true,
                'referral_required' => true,
                'referral_conditions' => [
                    'enabled' => true,
                    'minimum_referrals' => 1,
                    'minimum_investment_amount' => 50,
                    'require_active_investment' => true,
                    'allow_multiple_small_investments' => false,
                    'minimum_investment_duration_days' => 0,
                ]
            ];
            
            $withdrawalConditions = $settings->withdrawal_conditions ?? [
                'kyc_required' => true,
                'email_verification_required' => true,
                'profile_complete_required' => true,
                'referral_required' => true,
                'referral_conditions' => [
                    'enabled' => true,
                    'minimum_referrals' => 1,
                    'minimum_investment_amount' => 50,
                    'require_active_investment' => true,
                    'allow_multiple_small_investments' => false,
                    'minimum_investment_duration_days' => 0,
                ]
            ];
            
            $pageTitle = 'Transfer & Withdrawal Conditions';
            
            return view('admin.settings.transfer-withdraw-conditions', compact(
                'settings', 
                'transferConditions', 
                'withdrawalConditions', 
                'pageTitle'
            ));
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load conditions settings: ' . $e->getMessage());
        }
    }

    /**
     * Update transfer and withdrawal conditions.
     */
    public function update(Request $request)
    {
        try {
            // Prepare the transfer conditions data
            $transferConditions = [
                'kyc_required' => $request->has('transfer_kyc_required'),
                'email_verification_required' => $request->has('transfer_email_verification_required'),
                'profile_complete_required' => $request->has('transfer_profile_complete_required'),
                'referral_required' => $request->has('transfer_referral_required'),
                'referral_conditions' => [
                    'enabled' => $request->has('transfer_referral_required'),
                    'minimum_referrals' => (int) $request->input('transfer_minimum_referrals', 1),
                    'minimum_investment_amount' => (float) $request->input('transfer_minimum_investment_amount', 50),
                    'require_active_investment' => $request->has('transfer_require_active_investment'),
                    'allow_multiple_small_investments' => $request->has('transfer_allow_multiple_small_investments'),
                    'minimum_investment_duration_days' => (int) $request->input('transfer_minimum_investment_duration_days', 0),
                ]
            ];
            
            // Prepare the withdrawal conditions data
            $withdrawalConditions = [
                'kyc_required' => $request->has('withdrawal_kyc_required'),
                'email_verification_required' => $request->has('withdrawal_email_verification_required'),
                'profile_complete_required' => $request->has('withdrawal_profile_complete_required'),
                'referral_required' => $request->has('withdrawal_referral_required'),
                'referral_conditions' => [
                    'enabled' => $request->has('withdrawal_referral_required'),
                    'minimum_referrals' => (int) $request->input('withdrawal_minimum_referrals', 1),
                    'minimum_investment_amount' => (float) $request->input('withdrawal_minimum_investment_amount', 50),
                    'require_active_investment' => $request->has('withdrawal_require_active_investment'),
                    'allow_multiple_small_investments' => $request->has('withdrawal_allow_multiple_small_investments'),
                    'minimum_investment_duration_days' => (int) $request->input('withdrawal_minimum_investment_duration_days', 0),
                ]
            ];

            // Update the settings
            GeneralSetting::updateOrCreateSetting([
                'transfer_conditions' => $transferConditions,
                'withdrawal_conditions' => $withdrawalConditions,
            ]);

            return back()->with('success', 'Transfer and withdrawal conditions updated successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'An error occurred while updating conditions: ' . $e->getMessage());
        }
    }

    /**
     * Get conditions summary for API/AJAX requests.
     */
    public function getConditionsSummary()
    {
        try {
            $settings = GeneralSetting::getSettings();
            
            $transferConditions = $settings->transfer_conditions ?? [];
            $withdrawalConditions = $settings->withdrawal_conditions ?? [];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'transfer_conditions' => $transferConditions,
                    'withdrawal_conditions' => $withdrawalConditions,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get conditions summary: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reset conditions to default values.
     */
    public function resetToDefaults()
    {
        try {
            $defaultConditions = [
                'kyc_required' => true,
                'email_verification_required' => true,
                'profile_complete_required' => true,
                'referral_required' => true,
            ];

            GeneralSetting::updateOrCreateSetting([
                'transfer_conditions' => $defaultConditions,
                'withdrawal_conditions' => $defaultConditions,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conditions reset to default values successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset conditions: ' . $e->getMessage()
            ]);
        }
    }
}
