<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sponsor Qualification Settings
    |--------------------------------------------------------------------------
    |
    | These settings determine the requirements for sponsors to receive
    | special tokens/tickets when their referrals make investments.
    |
    */

    'enabled' => env('SPONSOR_QUALIFICATION_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Minimum Investment Requirements
    |--------------------------------------------------------------------------
    |
    | The minimum total investment amount a sponsor must have to qualify
    | for receiving special tokens from referral investments.
    |
    */
    'minimum_total_investment' => env('SPONSOR_MIN_INVESTMENT', 50.00),

    /*
    |--------------------------------------------------------------------------
    | Active Investment Requirement
    |--------------------------------------------------------------------------
    |
    | Whether the sponsor must have at least one currently active investment
    | (still earning/not matured) to receive special tokens.
    |
    */
    'require_active_investment' => env('SPONSOR_REQUIRE_ACTIVE', true),

    /*
    |--------------------------------------------------------------------------
    | Account Status Requirement
    |--------------------------------------------------------------------------
    |
    | Whether the sponsor account must be active (status = 1) to receive
    | special tokens from referral investments.
    |
    */
    'require_active_account' => env('SPONSOR_REQUIRE_ACTIVE_ACCOUNT', true),

    /*
    |--------------------------------------------------------------------------
    | KYC Verification Requirement
    |--------------------------------------------------------------------------
    |
    | Whether the sponsor must have completed KYC verification to receive
    | special tokens from referral investments.
    |
    */
    'require_kyc_verification' => env('SPONSOR_REQUIRE_KYC', false),

    /*
    |--------------------------------------------------------------------------
    | Minimum Referral Investment
    |--------------------------------------------------------------------------
    |
    | The minimum investment amount from referrals to trigger special
    | token creation for sponsors.
    |
    */
    'minimum_referral_investment' => env('SPONSOR_MIN_REFERRAL_INVESTMENT', 25.00),

    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    |
    | Whether to log sponsor qualification checks and results.
    |
    */
    'enable_logging' => env('SPONSOR_QUALIFICATION_LOGGING', true),

    /*
    |--------------------------------------------------------------------------
    | Grace Period Settings
    |--------------------------------------------------------------------------
    |
    | Grace period (in days) after investment maturity where sponsors
    | are still considered "active" for qualification purposes.
    |
    */
    'active_investment_grace_period_days' => env('SPONSOR_GRACE_PERIOD_DAYS', 7),
];
