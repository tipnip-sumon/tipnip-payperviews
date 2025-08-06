<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Withdrawal;
use App\Models\Transaction;

class WithdrawController extends Controller
{
    /**
     * Display withdrawal page with user's deposit info
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's current active deposit
        $activeDeposit = $user->invests()->where('status', 1)->first();
        
        // Get withdrawal statistics
        $withdrawalStats = [
            'total_withdrawals' => Withdrawal::where('user_id', $user->id)->count(),
            'total_withdrawn' => Withdrawal::where('user_id', $user->id)->where('status', 1)->sum('amount'),
            'pending_withdrawals' => Withdrawal::where('user_id', $user->id)->where('status', 0)->count(),
            'pending_amount' => Withdrawal::where('user_id', $user->id)->where('status', 0)->sum('amount'),
        ];
        
        // Calculate withdrawal details if user has active deposit
        $withdrawalDetails = null;
        if ($activeDeposit) {
            $depositAmount = $activeDeposit->amount;
            $withdrawalFee = $depositAmount * 0.20; // 20% fee
            $netAmount = $depositAmount - $withdrawalFee;
            
            $withdrawalDetails = [
                'deposit_amount' => $depositAmount,
                'withdrawal_fee' => $withdrawalFee,
                'fee_percentage' => 20,
                'net_amount' => $netAmount,
                'plan_name' => $activeDeposit->plan->name ?? 'Unknown Plan'
            ];
        }
        
        // Get recent withdrawal history
        $recentWithdrawals = Withdrawal::where('user_id', $user->id)
            ->with('withdrawMethod')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $data = [
            'pageTitle' => 'Withdraw Deposit',
            'activeDeposit' => $activeDeposit,
            'withdrawalDetails' => $withdrawalDetails,
            'withdrawalStats' => $withdrawalStats,
            'recentWithdrawals' => $recentWithdrawals,
            'kycVerified' => $user->kv == 1
        ];
        
        return view('frontend.withdraw', $data);
    }
    
    /**
     * Process withdrawal request
     */
    public function withdraw(Request $request)
    {
        $user = Auth::user();
        
        // Check withdrawal conditions first
        if (!function_exists('checkWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $conditionCheck = checkWithdrawalConditions($user);
        
        if (!$conditionCheck['allowed']) {
            return back()->with('error', 'Withdrawal requirements not met: ' . implode(', ', $conditionCheck['failures']));
        }
        
        // Validate request
        $request->validate([
            'password' => 'required',
            'withdraw_method' => 'required|string',
            'account_details' => 'required|string|max:500'
        ], [
            'password.required' => 'Transaction password is required',
            'withdraw_method.required' => 'Please select a withdrawal method',
            'account_details.required' => 'Account details are required'
        ]);
        
        // Verify password
        if (!Auth::attempt(['username' => $user->username, 'password' => $request->password])) {
            return back()->with(['error' => 'Invalid transaction password']);
        }
        
        // Check if user has active deposit
        $activeDeposit = $user->invests()->where('status', 1)->first();
        if (!$activeDeposit) {
            return back()->with(['error' => 'You don\'t have any active deposit to withdraw']);
        }
        
        // Check if there's already a pending withdrawal
        $pendingWithdrawal = Withdrawal::where('user_id', $user->id)
            ->where('status', 0)
            ->exists();
            
        if ($pendingWithdrawal) {
            return back()->with(['error' => 'You already have a pending withdrawal request']);
        }
        
        try {
            DB::beginTransaction();
            
            // Calculate withdrawal amounts
            $depositAmount = $activeDeposit->amount;
            $withdrawalFee = $depositAmount * 0.20; // 20% fee
            $netAmount = $depositAmount - $withdrawalFee;
            
            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'method_id' => 1, // We'll use a default method for now
                'amount' => $netAmount,
                'charge' => $withdrawalFee,
                'final_amount' => $netAmount,
                'currency' => 'USD',
                'rate' => 1,
                'trx' => getTrx(),
                'withdraw_type' => 'deposit',
                'withdraw_information' => json_encode([
                    'method' => $request->withdraw_method,
                    'details' => $request->account_details
                ]),
                'status' => 0, // Pending
            ]);
            
            // Update deposit status to withdrawn (status = 2)
            $activeDeposit->update(['status' => 2]);
            
            // Create transaction record
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $netAmount;
            $transaction->charge = $withdrawalFee;
            $transaction->trx_type = '-';
            $transaction->trx = $withdrawal->trx;
            $transaction->wallet_type = 'deposit_withdrawal';
            $transaction->remark = 'deposit_withdrawal';
            $transaction->details = 'Withdrawal request for deposit: ' . $activeDeposit->plan->name . ' (Fee: $' . number_format($withdrawalFee, 2) . ')';
            $transaction->post_balance = 0; // Will be updated when withdrawal is approved
            $transaction->save();
            
            DB::commit();
            
            return back()->with(['success' => 'Withdrawal request submitted successfully! You will receive $' . number_format($netAmount, 2) . ' after admin approval.']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal request error: ' . $e->getMessage());
            
            return back()->with(['error' => 'An error occurred while processing your withdrawal request. Please try again.']);
        }
    }
    
    /**
     * Display withdrawal history (deposit withdrawals only)
     */
    public function history()
    {
        $user = Auth::user();
        
        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->where(function($query) {
                $query->where('withdraw_type', 'deposit')
                      ->orWhereNull('withdraw_type'); // For backward compatibility with existing records
            })
            ->with('withdrawMethod')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $stats = [
            'total_requests' => Withdrawal::where('user_id', $user->id)
                ->where(function($query) {
                    $query->where('withdraw_type', 'deposit')
                          ->orWhereNull('withdraw_type');
                })->count(),
            'approved_requests' => Withdrawal::where('user_id', $user->id)
                ->where(function($query) {
                    $query->where('withdraw_type', 'deposit')
                          ->orWhereNull('withdraw_type');
                })->where('status', 1)->count(),
            'pending_requests' => Withdrawal::where('user_id', $user->id)
                ->where(function($query) {
                    $query->where('withdraw_type', 'deposit')
                          ->orWhereNull('withdraw_type');
                })->where('status', 0)->count(),
            'rejected_requests' => Withdrawal::where('user_id', $user->id)
                ->where(function($query) {
                    $query->where('withdraw_type', 'deposit')
                          ->orWhereNull('withdraw_type');
                })->where('status', 2)->count(),
            'total_withdrawn' => Withdrawal::where('user_id', $user->id)
                ->where(function($query) {
                    $query->where('withdraw_type', 'deposit')
                          ->orWhereNull('withdraw_type');
                })->where('status', 1)->sum('final_amount'),
            'total_fees_paid' => Withdrawal::where('user_id', $user->id)
                ->where(function($query) {
                    $query->where('withdraw_type', 'deposit')
                          ->orWhereNull('withdraw_type');
                })->where('status', 1)->sum('charge'),
        ];
        
        $data = [
            'pageTitle' => 'Deposit Withdrawal History',
            'withdrawals' => $withdrawals,
            'stats' => $stats
        ];
        
        return view('frontend.withdrawal-history', $data);
    }
    
    /**
     * Display wallet withdrawal page
     */
    public function walletIndex()
    {
        $user = Auth::user();
        
        // Calculate wallet balance
        $depositWallet = $user->deposit_wallet ?? 0;
        $interestWallet = $user->interest_wallet ?? 0;
        $totalWalletBalance = $depositWallet + $interestWallet;
        
        // Get wallet withdrawal statistics
        $withdrawalStats = [
            'total_wallet_withdrawals' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->count(),
            'total_wallet_withdrawn' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 1)->sum('amount'),
            'pending_wallet_withdrawals' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 0)->count(),
            'pending_wallet_amount' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 0)->sum('amount'),
        ];
        
        // Get recent wallet withdrawal history
        $recentWithdrawals = Withdrawal::where('user_id', $user->id)
            ->where('withdraw_type', 'wallet')
            ->with('withdrawMethod')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $data = [
            'pageTitle' => 'Withdraw Wallet Balance',
            'depositWallet' => $depositWallet,
            'interestWallet' => $interestWallet,
            'totalWalletBalance' => $totalWalletBalance,
            'withdrawalStats' => $withdrawalStats,
            'recentWithdrawals' => $recentWithdrawals,
            'kycVerified' => $user->kv == 1
        ];
        
        return view('frontend.withdraw-wallet', $data);
    }
    
    /**
     * Process wallet withdrawal request
     */
    public function walletWithdraw(Request $request)
    {
        $user = Auth::user();
        
        // Check withdrawal conditions first
        if (!function_exists('checkWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $conditionCheck = checkWithdrawalConditions($user);
        
        if (!$conditionCheck['allowed']) {
            return back()->with('error', 'Withdrawal requirements not met: ' . implode(', ', $conditionCheck['failures']));
        }
        
        // Validate request
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'password' => 'required',
            'withdraw_method' => 'required|string',
            'account_details' => 'required|string|max:500'
        ], [
            'amount.required' => 'Withdrawal amount is required',
            'amount.min' => 'Minimum withdrawal amount is $1',
            'password.required' => 'Transaction password is required',
            'withdraw_method.required' => 'Please select a withdrawal method',
            'account_details.required' => 'Account details are required'
        ]);
        
        // Verify password
        if (!Auth::attempt(['username' => $user->username, 'password' => $request->password])) {
            return back()->with(['error' => 'Invalid transaction password']);
        }
        
        // Calculate wallet balance
        $depositWallet = $user->deposit_wallet ?? 0;
        $interestWallet = $user->interest_wallet ?? 0;
        $totalWalletBalance = $depositWallet + $interestWallet;
        
        // Check if user has sufficient balance
        if ($request->amount > $totalWalletBalance) {
            return back()->with(['error' => 'Insufficient wallet balance. Available: $' . number_format($totalWalletBalance, 2)]);
        }
        
        // Check if there's already a pending wallet withdrawal
        $pendingWithdrawal = Withdrawal::where('user_id', $user->id)
            ->where('withdraw_type', 'wallet')
            ->where('status', 0)
            ->exists();
            
        if ($pendingWithdrawal) {
            return back()->with(['error' => 'You already have a pending wallet withdrawal request']);
        }
        
        try {
            DB::beginTransaction();
            
            // No fee for wallet withdrawals (or you can add a small fee if needed)
            $withdrawalAmount = $request->amount;
            $withdrawalFee = 0; // No fee for wallet withdrawals
            $netAmount = $withdrawalAmount - $withdrawalFee;
            
            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'method_id' => 1, // Default method
                'amount' => $withdrawalAmount,
                'charge' => $withdrawalFee,
                'final_amount' => $netAmount,
                'currency' => 'USD',
                'rate' => 1,
                'trx' => getTrx(),
                'withdraw_type' => 'wallet',
                'withdraw_information' => json_encode([
                    'method' => $request->withdraw_method,
                    'details' => $request->account_details,
                    'wallet_breakdown' => [
                        'deposit_wallet' => $depositWallet,
                        'interest_wallet' => $interestWallet,
                        'total_balance' => $totalWalletBalance
                    ]
                ]),
                'status' => 0, // Pending
            ]);
            
            // Deduct from user wallets (temporarily, will be restored if withdrawal is rejected)
            $remainingAmount = $withdrawalAmount;
            
            // First deduct from deposit wallet
            if ($remainingAmount > 0 && $depositWallet > 0) {
                $deductFromDeposit = min($remainingAmount, $depositWallet);
                $user->deposit_wallet -= $deductFromDeposit;
                $remainingAmount -= $deductFromDeposit;
            }
            
            // Then deduct from interest wallet if needed
            if ($remainingAmount > 0 && $interestWallet > 0) {
                $deductFromInterest = min($remainingAmount, $interestWallet);
                $user->interest_wallet -= $deductFromInterest;
                $remainingAmount -= $deductFromInterest;
            }
            
            $user->save();
            
            // Create transaction record
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $withdrawalAmount;
            $transaction->charge = $withdrawalFee;
            $transaction->trx_type = '-';
            $transaction->trx = $withdrawal->trx;
            $transaction->wallet_type = 'wallet_withdrawal';
            $transaction->remark = 'wallet_withdrawal';
            $transaction->details = 'Wallet withdrawal request: $' . number_format($withdrawalAmount, 2);
            $transaction->post_balance = ($user->deposit_wallet ?? 0) + ($user->interest_wallet ?? 0);
            $transaction->save();
            
            DB::commit();
            
            return back()->with(['success' => 'Wallet withdrawal request submitted successfully! You will receive $' . number_format($netAmount, 2) . ' after admin approval.']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet withdrawal request error: ' . $e->getMessage());
            
            return back()->with(['error' => 'An error occurred while processing your wallet withdrawal request. Please try again.']);
        }
    }
    
    /**
     * Display wallet withdrawal history
     */
    public function walletHistory()
    {
        $user = Auth::user();
        
        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->where('withdraw_type', 'wallet')
            ->with('withdrawMethod')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $stats = [
            'total_requests' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->count(),
            'approved_requests' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 1)->count(),
            'pending_requests' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 0)->count(),
            'rejected_requests' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 2)->count(),
            'total_withdrawn' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 1)->sum('final_amount'),
            'total_fees_paid' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 1)->sum('charge'),
        ];
        
        $data = [
            'pageTitle' => 'Wallet Withdrawal History',
            'withdrawals' => $withdrawals,
            'stats' => $stats
        ];
        
        return view('frontend.wallet-withdrawal-history', $data);
    }
}
