<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\ReferralUserBenefit;
use App\Models\ReferralBonusTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReferralBenefitsController extends Controller
{
    /**
     * Display the referral benefits settings page
     */
    public function index()
    {
        // Get current referral benefits settings or set defaults
        $referralBenefitsSettings = GeneralSetting::getSetting('referral_benefits_settings', [
            'enabled' => true,
            'minimum_referrals' => 15,
            'minimum_investment_per_referral' => 50,
            'transfer_bonus_min' => 1,
            'transfer_bonus_max' => 5,
            'receive_bonus_min' => 1,
            'receive_bonus_max' => 5,
            'withdraw_reduction_min' => 1,
            'withdraw_reduction_max' => 5
        ]);

        // Ensure it's an array (in case it's stored as JSON string)
        if (is_string($referralBenefitsSettings)) {
            $referralBenefitsSettings = json_decode($referralBenefitsSettings, true) ?: [
                'enabled' => true,
                'minimum_referrals' => 15,
                'minimum_investment_per_referral' => 50,
                'transfer_bonus_min' => 1,
                'transfer_bonus_max' => 5,
                'receive_bonus_min' => 1,
                'receive_bonus_max' => 5,
                'withdraw_reduction_min' => 1,
                'withdraw_reduction_max' => 5
            ];
        }

        // Get statistics
        $stats = [
            'total_qualified_users' => ReferralUserBenefit::where('is_qualified', true)->count(),
            'total_users_with_benefits' => ReferralUserBenefit::count(),
            'total_bonus_transactions' => ReferralBonusTransaction::count(),
            'total_bonuses_given' => ReferralBonusTransaction::sum('amount')
        ];

        // Get recent qualified users
        $recentQualified = ReferralUserBenefit::with('user')
            ->where('is_qualified', true)
            ->orderBy('qualified_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.settings.referral-benefits', compact(
            'referralBenefitsSettings',
            'stats',
            'recentQualified'
        ));
    }

    /**
     * Update referral benefits settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'minimum_referrals' => 'required|integer|min:1|max:100',
            'minimum_investment_per_referral' => 'required|numeric|min:1|max:10000',
            'transfer_bonus_min' => 'required|numeric|min:0|max:10',
            'transfer_bonus_max' => 'required|numeric|min:0|max:10|gte:transfer_bonus_min',
            'receive_bonus_min' => 'required|numeric|min:0|max:10',
            'receive_bonus_max' => 'required|numeric|min:0|max:10|gte:receive_bonus_min',
            'withdraw_reduction_min' => 'required|numeric|min:0|max:10',
            'withdraw_reduction_max' => 'required|numeric|min:0|max:10|gte:withdraw_reduction_min'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $settings = [
                'enabled' => $request->has('enabled'),
                'minimum_referrals' => (int) $request->minimum_referrals,
                'minimum_investment_per_referral' => (float) $request->minimum_investment_per_referral,
                'transfer_bonus_min' => (float) $request->transfer_bonus_min,
                'transfer_bonus_max' => (float) $request->transfer_bonus_max,
                'receive_bonus_min' => (float) $request->receive_bonus_min,
                'receive_bonus_max' => (float) $request->receive_bonus_max,
                'withdraw_reduction_min' => (float) $request->withdraw_reduction_min,
                'withdraw_reduction_max' => (float) $request->withdraw_reduction_max
            ];

            GeneralSetting::updateOrCreateSetting(['referral_benefits_settings' => $settings]);

            return back()->with('success', 'Referral benefits settings updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating settings: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate all user qualifications
     */
    public function recalculateQualifications(Request $request)
    {
        try {
            $users = User::whereHas('referrals')->get();
            $updated = 0;
            $newlyQualified = 0;

            foreach ($users as $user) {
                $wasQualified = ReferralUserBenefit::where('user_id', $user->id)
                    ->where('is_qualified', true)
                    ->exists();

                $isNowQualified = ReferralUserBenefit::checkAndUpdateQualification($user->id);

                if ($isNowQualified && !$wasQualified) {
                    $newlyQualified++;
                }
                $updated++;
            }

            return back()->with('success', "Recalculated qualifications for {$updated} users. {$newlyQualified} newly qualified users found.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error recalculating qualifications: ' . $e->getMessage());
        }
    }

    /**
     * View qualified users
     */
    public function qualifiedUsers()
    {
        $qualifiedUsers = ReferralUserBenefit::with('user')
            ->where('is_qualified', true)
            ->orderBy('qualified_at', 'desc')
            ->paginate(20);

        return view('admin.referral-benefits.qualified-users', compact('qualifiedUsers'));
    }

    /**
     * View user's detailed benefits
     */
    public function userDetails($userId)
    {
        $user = User::findOrFail($userId);
        $benefits = ReferralUserBenefit::where('user_id', $userId)->first();
        $bonusHistory = ReferralBonusTransaction::getUserBonusHistory($userId);
        
        // Get referral stats
        $referralStats = [
            'total_referrals' => $user->referrals()->count(),
            'qualified_referrals' => $user->referrals()
                ->whereHas('invests', function($query) {
                    $settings = GeneralSetting::getSetting('referral_benefits_settings', []);
                    $minInvestment = $settings['minimum_investment_per_referral'] ?? 50;
                    $query->where('status', 1)->where('amount', '>=', $minInvestment);
                })
                ->count(),
            'total_bonuses_earned' => ReferralBonusTransaction::getTotalBonuses($userId)
        ];

        return view('admin.referral-benefits.user-details', compact(
            'user',
            'benefits',
            'bonusHistory',
            'referralStats'
        ));
    }

    /**
     * Update individual user's bonus percentages
     */
    public function updateUserBonuses(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'transfer_bonus_percentage' => 'required|numeric|min:0|max:10',
            'balance_receive_bonus_percentage' => 'required|numeric|min:0|max:10',
            'withdraw_charge_reduction_percentage' => 'required|numeric|min:0|max:10'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $benefits = ReferralUserBenefit::where('user_id', $userId)->first();
            if (!$benefits) {
                return back()->with('error', 'User benefits record not found.');
            }

            $benefits->update([
                'transfer_bonus_percentage' => $request->transfer_bonus_percentage,
                'balance_receive_bonus_percentage' => $request->balance_receive_bonus_percentage,
                'withdraw_charge_reduction_percentage' => $request->withdraw_charge_reduction_percentage,
                'last_updated_at' => now()
            ]);

            return back()->with('success', 'User bonus percentages updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating user bonuses: ' . $e->getMessage());
        }
    }

    /**
     * Show bonus transactions page
     */
    public function bonusTransactions(Request $request)
    {
        // Get statistics for the page
        $stats = [
            'total_bonuses' => ReferralBonusTransaction::sum('amount'),
            'total_transactions' => ReferralBonusTransaction::count(),
            'transfer_bonuses' => ReferralBonusTransaction::where('type', 'transfer_bonus')->sum('amount'),
            'receive_bonuses' => ReferralBonusTransaction::where('type', 'receive_bonus')->sum('amount'),
            'withdraw_reductions' => ReferralBonusTransaction::where('type', 'withdraw_reduction')->sum('amount')
        ];

        // Build query with filters
        $query = ReferralBonusTransaction::with('userBenefit.user');

        if ($request->filled('user_search')) {
            $query->whereHas('userBenefit.user', function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->user_search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('admin.referral-benefits.bonus-transactions', compact('stats', 'transactions'));
    }

    /**
     * Toggle user qualification status
     */
    public function toggleUserStatus($userId)
    {
        try {
            $benefits = ReferralUserBenefit::where('user_id', $userId)->first();
            
            if (!$benefits) {
                return back()->with('error', 'User benefits record not found.');
            }

            $benefits->update([
                'is_qualified' => !$benefits->is_qualified,
                'last_updated_at' => now()
            ]);

            $status = $benefits->is_qualified ? 'qualified' : 'disqualified';
            return back()->with('success', "User has been {$status} successfully!");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating user status: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate individual user qualification
     */
    public function recalculateUser($userId)
    {
        try {
            $wasQualified = ReferralUserBenefit::where('user_id', $userId)
                ->where('is_qualified', true)
                ->exists();

            $isNowQualified = ReferralUserBenefit::checkAndUpdateQualification($userId);

            if ($isNowQualified && !$wasQualified) {
                $message = 'User is now qualified for referral benefits!';
            } elseif (!$isNowQualified && $wasQualified) {
                $message = 'User no longer qualifies for referral benefits.';
            } elseif ($isNowQualified) {
                $message = 'User remains qualified. Benefits updated.';
            } else {
                $message = 'User does not qualify for referral benefits.';
            }

            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error recalculating user qualification: ' . $e->getMessage());
        }
    }

    /**
     * Show transaction details
     */
    public function transactionDetails($transactionId)
    {
        $transaction = ReferralBonusTransaction::with('userBenefit.user')->findOrFail($transactionId);
        
        return view('admin.referral-benefits.transaction-details', compact('transaction'));
    }

    /**
     * Get bonus transactions for DataTables
     */
    public function getBonusTransactions(Request $request)
    {
        $query = ReferralBonusTransaction::with('userBenefit.user');
        
        if ($request->has('user_id') && $request->user_id) {
            $query->whereHas('userBenefit', function($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }
        
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        return datatables()->of($transactions)
            ->addColumn('user', function ($row) {
                return $row->userBenefit && $row->userBenefit->user ? $row->userBenefit->user->username : 'N/A';
            })
            ->addColumn('type', function ($row) {
                $badges = [
                    'transfer_bonus' => '<span class="badge bg-success">Transfer Bonus</span>',
                    'receive_bonus' => '<span class="badge bg-info">Receive Bonus</span>',
                    'withdraw_reduction' => '<span class="badge bg-warning">Withdraw Reduction</span>'
                ];
                return $badges[$row->type] ?? $row->type;
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('M d, Y H:i');
            })
            ->rawColumns(['type'])
            ->make(true);
    }

    /**
     * Export qualified users to CSV
     */
    public function exportQualifiedUsers()
    {
        $qualifiedUsers = ReferralUserBenefit::with('user')
            ->where('is_qualified', true)
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="qualified_users_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() use ($qualifiedUsers) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'User ID',
                'Username', 
                'Email',
                'Qualified Referrals Count',
                'Transfer Bonus %',
                'Receive Bonus %',
                'Withdraw Reduction %',
                'Qualified Date',
                'Last Updated'
            ]);
            
            // CSV Data
            foreach ($qualifiedUsers as $benefit) {
                fputcsv($file, [
                    $benefit->user_id,
                    $benefit->user->username ?? 'N/A',
                    $benefit->user->email ?? 'N/A',
                    $benefit->qualified_referrals_count,
                    $benefit->transfer_bonus_percentage . '%',
                    $benefit->balance_receive_bonus_percentage . '%',
                    $benefit->withdraw_charge_reduction_percentage . '%',
                    $benefit->qualified_at ? $benefit->qualified_at->format('Y-m-d H:i:s') : 'N/A',
                    $benefit->last_updated_at ? $benefit->last_updated_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export bonus transactions to CSV
     */
    public function exportTransactions(Request $request)
    {
        $query = ReferralBonusTransaction::with('userBenefit.user');

        // Apply filters if provided
        if ($request->filled('user_search')) {
            $query->whereHas('userBenefit.user', function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->user_search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bonus_transactions_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Transaction ID',
                'User ID',
                'Username',
                'Type',
                'Original Amount',
                'Percentage Used',
                'Bonus/Reduction Amount',
                'Related Transaction ID',
                'Description',
                'Created At'
            ]);
            
            // CSV Data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->userBenefit->user_id ?? 'N/A',
                    $transaction->userBenefit->user->username ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $transaction->type)),
                    '$' . number_format($transaction->original_amount, 2),
                    $transaction->percentage_used . '%',
                    '$' . number_format($transaction->amount, 2),
                    $transaction->related_transaction_id ?? 'N/A',
                    $transaction->description,
                    $transaction->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
