<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            // Add transfer_conditions JSON column if it doesn't exist
            if (!Schema::hasColumn('general_settings', 'transfer_conditions')) {
                $table->json('transfer_conditions')->nullable()->after('security_settings');
            }
            
            // Add withdrawal_conditions JSON column if it doesn't exist
            if (!Schema::hasColumn('general_settings', 'withdrawal_conditions')) {
                $table->json('withdrawal_conditions')->nullable()->after('transfer_conditions');
            }
        });

        // Set default values for the new columns
        $this->setDefaultValues();
    }

    /**
     * Set default values for transfer and withdrawal conditions
     */
    private function setDefaultValues(): void
    {
        // Default Transfer Conditions
        $defaultTransferConditions = [
            'kyc_required' => true,
            'email_verification_required' => true,
            'otp_required' => false,
            'profile_complete_required' => true,
            'referral_required' => true,
            'referral_conditions' => [
                'enabled' => true,
                'minimum_referrals' => 1,
                'minimum_investment_amount' => 50,
                'require_active_investment' => true,
                'allow_multiple_small_investments' => false,
                'minimum_investment_duration_days' => 0
            ]
        ];

        // Default Withdrawal Conditions
        $defaultWithdrawalConditions = [
            'deposit_withdrawal_enabled' => true,
            'wallet_withdrawal_enabled' => true,
            'kyc_required' => true,
            'email_verification_required' => true,
            'deposit_otp_required' => true,
            'wallet_otp_required' => true,
            'profile_complete_required' => true,
            'referral_required' => true,
            'referral_conditions' => [
                'enabled' => true,
                'minimum_referrals' => 1,
                'minimum_investment_amount' => 50,
                'require_active_investment' => true,
                'allow_multiple_small_investments' => false,
                'minimum_investment_duration_days' => 0
            ]
        ];

        // Update all general_settings records with default values if columns are null
        DB::table('general_settings')
            ->whereNull('transfer_conditions')
            ->orWhereNull('withdrawal_conditions')
            ->get()
            ->each(function ($setting) use ($defaultTransferConditions, $defaultWithdrawalConditions) {
                $updateData = [];
                
                if (is_null($setting->transfer_conditions)) {
                    $updateData['transfer_conditions'] = json_encode($defaultTransferConditions);
                }
                
                if (is_null($setting->withdrawal_conditions)) {
                    $updateData['withdrawal_conditions'] = json_encode($defaultWithdrawalConditions);
                }
                
                if (!empty($updateData)) {
                    $updateData['updated_at'] = now();
                    DB::table('general_settings')
                        ->where('id', $setting->id)
                        ->update($updateData);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            // Drop the columns if they exist
            if (Schema::hasColumn('general_settings', 'withdrawal_conditions')) {
                $table->dropColumn('withdrawal_conditions');
            }
            
            if (Schema::hasColumn('general_settings', 'transfer_conditions')) {
                $table->dropColumn('transfer_conditions');
            }
        });
    }
};
