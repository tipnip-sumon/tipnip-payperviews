<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Models\WithdrawMethod;

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
        
        // Get available withdrawal methods
        $withdrawMethods = WithdrawMethod::where('status', 1)->get();
        
        // Get withdrawal statistics
        $withdrawalStats = [
            'total_withdrawals' => Withdrawal::where('user_id', $user->id)->count(),
            'total_withdrawn' => Withdrawal::where('user_id', $user->id)->where('status', 1)->sum('amount'),
            'pending_withdrawals' => Withdrawal::where('user_id', $user->id)->where('status', 2)->count(),
            'pending_amount' => Withdrawal::where('user_id', $user->id)->where('status', 2)->sum('amount'),
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
            'withdrawMethods' => $withdrawMethods,
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
            'method_id' => 'required|exists:withdraw_methods,id',
            'account_details' => 'required|string|max:500'
        ], [
            'password.required' => 'Transaction password is required',
            'method_id.required' => 'Please select a withdrawal method',
            'method_id.exists' => 'Invalid withdrawal method selected',
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
            ->where('status', 2)
            ->exists();
            
        if ($pendingWithdrawal) {
            return back()->with(['error' => 'You already have a pending withdrawal request']);
        }
        
        try {
            DB::beginTransaction();
            
            // Get the selected withdrawal method
            $withdrawMethod = WithdrawMethod::findOrFail($request->method_id);
            
            // Calculate withdrawal amounts (deposit withdrawals use 20% fee)
            $depositAmount = $activeDeposit->amount;
            $withdrawalFee = $depositAmount * 0.20; // 20% fee for deposit withdrawals
            $netAmount = $depositAmount - $withdrawalFee;
            
            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'method_id' => $request->method_id, // Use selected method
                'amount' => $netAmount,
                'charge' => $withdrawalFee,
                'final_amount' => $netAmount,
                'currency' => $withdrawMethod->currency ?? 'USD',
                'rate' => $withdrawMethod->rate ?? 1,
                'trx' => getTrx(),
                'withdraw_type' => 'deposit',
                'withdraw_information' => json_encode([
                    'method' => $withdrawMethod->name,
                    'details' => $request->account_details,
                    'deposit_info' => [
                        'plan_name' => $activeDeposit->plan->name ?? 'Unknown Plan',
                        'deposit_amount' => $depositAmount,
                        'withdrawal_fee' => $withdrawalFee,
                        'fee_percentage' => 20
                    ]
                ]),
                'status' => 2, // Pending (correct status code)
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
                })->where('status', 2)->count(),
            'rejected_requests' => Withdrawal::where('user_id', $user->id)
                ->where(function($query) {
                    $query->where('withdraw_type', 'deposit')
                          ->orWhereNull('withdraw_type');
                })->where('status', 3)->count(),
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
        
        // Get available withdrawal methods
        $withdrawMethods = WithdrawMethod::where('status', 1)->get();
        
        // Check if this is a test request
        if (request()->get('test') === 'simple') {
            return view('test-withdrawal', compact('withdrawMethods'));
        }
        
        // Get wallet withdrawal statistics
        $withdrawalStats = [
            'total_wallet_withdrawals' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->count(),
            'total_wallet_withdrawn' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 1)->sum('amount'),
            'pending_wallet_withdrawals' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 2)->count(),
            'pending_wallet_amount' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 2)->sum('amount'),
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
            'withdrawMethods' => $withdrawMethods,
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
        
        // Debug logging
        Log::info('Withdrawal attempt started', [
            'user_id' => $user->id,
            'request_data' => $request->only(['amount', 'method_id']),
            'user_kv' => $user->kv,
            'email_verified' => $user->email_verified_at ? 'yes' : 'no'
        ]);
        
        // Check withdrawal conditions first
        if (!function_exists('checkWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $conditionCheck = checkWithdrawalConditions($user);
        
        // Debug log condition check results
        Log::info('Withdrawal condition check', [
            'user_id' => $user->id,
            'allowed' => $conditionCheck['allowed'],
            'failures' => $conditionCheck['failures'],
            'conditions' => $conditionCheck['conditions'] ?? 'unknown'
        ]);
        
        if (!$conditionCheck['allowed']) {
            Log::warning('Withdrawal blocked by conditions', [
                'user_id' => $user->id,
                'failures' => $conditionCheck['failures']
            ]);
            
            // Check if profile completion is the specific issue
            $failures = $conditionCheck['failures'];
            if (count($failures) === 1 && strpos($failures[0], 'Profile completion') !== false) {
                $message = 'Please complete your profile information before making withdrawals. Update your profile with all required details including name, mobile, country, and address.';
                Log::info('Returning profile completion error', ['message' => $message]);
                return redirect()->back()->with('error', $message)->withInput();
            }
            
            $message = 'Withdrawal requirements not met: ' . implode(', ', $conditionCheck['failures']);
            Log::info('Returning general condition error', ['message' => $message]);
            return redirect()->back()->with('error', $message)->withInput();
        }
        
        // Get the selected withdrawal method for validation
        $withdrawMethod = WithdrawMethod::findOrFail($request->method_id);
        
        // Validate request with dynamic min/max from withdrawal method
        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:' . ($withdrawMethod->min_amount ?? 1),
                'max:' . ($withdrawMethod->max_amount ?? 999999)
            ],
            'password' => 'required',
            'method_id' => 'required|exists:withdraw_methods,id',
            'account_details' => 'required|string|max:500'
        ], [
            'amount.required' => 'Withdrawal amount is required',
            'amount.min' => 'Minimum withdrawal amount for ' . $withdrawMethod->name . ' is $' . number_format($withdrawMethod->min_amount ?? 1, 2),
            'amount.max' => 'Maximum withdrawal amount for ' . $withdrawMethod->name . ' is $' . number_format($withdrawMethod->max_amount ?? 999999, 2),
            'amount.numeric' => 'Withdrawal amount must be a valid number',
            'password.required' => 'Transaction password is required',
            'method_id.required' => 'Please select a withdrawal method',
            'method_id.exists' => 'Invalid withdrawal method selected',
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
        
        // Check daily withdrawal limit for this method
        if ($withdrawMethod->daily_limit && $withdrawMethod->daily_limit > 0) {
            $todayWithdrawals = Withdrawal::where('user_id', $user->id)
                ->where('method_id', $request->method_id)
                ->where('withdraw_type', 'wallet')
                ->where('status', '!=', 3) // Exclude rejected withdrawals
                ->whereDate('created_at', today())
                ->sum('amount');
                
            $totalTodayAmount = $todayWithdrawals + $request->amount;
            
            if ($totalTodayAmount > $withdrawMethod->daily_limit) {
                $remainingLimit = max(0, $withdrawMethod->daily_limit - $todayWithdrawals);
                return back()->with(['error' => 'Daily withdrawal limit exceeded for ' . $withdrawMethod->name . '. Remaining limit: $' . number_format($remainingLimit, 2)]);
            }
        }
        
        // Check if there's already a pending wallet withdrawal
        $pendingWithdrawal = Withdrawal::where('user_id', $user->id)
            ->where('withdraw_type', 'wallet')
            ->where('status', 2)
            ->exists();
            
        if ($pendingWithdrawal) {
            return back()->with(['error' => 'You already have a pending wallet withdrawal request']);
        }
        
        try {
            DB::beginTransaction();
            
            // Note: withdrawMethod already retrieved above for validation
            // Calculate withdrawal fees using both fixed_charge and percent_charge
            $withdrawalAmount = $request->amount;
            $fixedCharge = $withdrawMethod->fixed_charge ?? 0;
            $percentCharge = ($withdrawMethod->percent_charge ?? 0) / 100;
            
            // Calculate total charge: fixed charge + percentage charge
            $percentageFee = $withdrawalAmount * $percentCharge;
            $totalCharge = $fixedCharge + $percentageFee;
            $netAmount = $withdrawalAmount - $totalCharge;
            
            // Ensure net amount is not negative
            if ($netAmount <= 0) {
                return back()->with(['error' => 'Withdrawal amount is too low after charges. Minimum required: $' . number_format($totalCharge + 0.01, 2)]);
            }
            
            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'method_id' => $request->method_id, // Use selected method
                'amount' => $withdrawalAmount,
                'charge' => $totalCharge,
                'final_amount' => $netAmount,
                'currency' => $withdrawMethod->currency ?? 'USD',
                'rate' => $withdrawMethod->rate ?? 1,
                'trx' => getTrx(),
                'withdraw_type' => 'wallet',
                'withdraw_information' => json_encode([
                    'method' => $withdrawMethod->name,
                    'details' => $request->account_details,
                    'wallet_breakdown' => [
                        'deposit_wallet' => $depositWallet,
                        'interest_wallet' => $interestWallet,
                        'total_balance' => $totalWalletBalance
                    ],
                    'charges' => [
                        'fixed_charge' => $fixedCharge,
                        'percent_charge' => $withdrawMethod->percent_charge,
                        'total_charge' => $totalCharge
                    ]
                ]),
                'status' => 2, // Pending (correct status code)
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
            $transaction->charge = $totalCharge;
            $transaction->trx_type = '-';
            $transaction->trx = $withdrawal->trx;
            $transaction->wallet_type = 'wallet_withdrawal';
            $transaction->remark = 'wallet_withdrawal';
            $transaction->details = 'Wallet withdrawal request: $' . number_format($withdrawalAmount, 2) . ' via ' . $withdrawMethod->name . ' (Charge: $' . number_format($totalCharge, 2) . ')';
            $transaction->post_balance = ($user->deposit_wallet ?? 0) + ($user->interest_wallet ?? 0);
            $transaction->save();
            
            DB::commit();
            
            Log::info('Withdrawal request successful', [
                'user_id' => $user->id,
                'withdrawal_id' => $withdrawal->id,
                'amount' => $withdrawalAmount,
                'net_amount' => $netAmount,
                'trx' => $withdrawal->trx
            ]);
            
            return back()->with(['success' => 'Wallet withdrawal request submitted successfully! You will receive $' . number_format($netAmount, 2) . ' after admin approval.']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet withdrawal request error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
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
            'pending_requests' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 2)->count(),
            'rejected_requests' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 3)->count(),
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
