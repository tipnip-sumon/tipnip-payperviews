<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Models\WithdrawMethod;
use App\Models\GeneralSetting;
use Carbon\Carbon;

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

        // Check if user is in OTP verification mode for deposit withdrawal
        $isDepositOtpSession = session('show_deposit_otp_form') === true;
        $depositStoredData = session('deposit_withdrawal_data');

        // Get withdrawal conditions from database
        if (!function_exists('getWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        $withdrawalConditions = getWithdrawalConditions();
        
        // Check individual conditions
        $conditionCheck = checkWithdrawalConditions($user);
        
        $data = [
            'pageTitle' => 'Withdraw Deposit',
            'activeDeposit' => $activeDeposit,
            'withdrawalDetails' => $withdrawalDetails,
            'withdrawMethods' => $withdrawMethods,
            'withdrawalStats' => $withdrawalStats,
            'recentWithdrawals' => $recentWithdrawals,
            'kycVerified' => $user->kv == 1,
            'isDepositOtpSession' => $isDepositOtpSession,
            'depositStoredData' => $depositStoredData,
            'withdrawalConditions' => $withdrawalConditions,
            'conditionCheck' => $conditionCheck
        ];        return view('frontend.withdraw', $data);
    }
    
    /**
     * Process withdrawal request with OTP verification
     */
    public function withdraw(Request $request)
    {
        $user = Auth::user();
        
        // Handle clear OTP request for resending
        if ($request->has('clear_otp')) {
            session()->forget(['deposit_withdrawal_data', 'show_deposit_otp_form']);
            $freshUser = User::find($user->id);
            $freshUser->ver_code = null;
            $freshUser->ver_code_send_at = null;
            $freshUser->save();
            // Continue with normal flow to resend OTP
        }
        
        // Check if user is in OTP verification mode
        if ($request->has('otp_code')) {
            return $this->verifyDepositWithdrawOtp($request);
        }
        
        // Get withdrawal conditions to check if OTP is required
        if (!function_exists('getWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        $withdrawalConditions = getWithdrawalConditions();
        
        // If OTP is NOT required, process withdrawal directly with password
        if (!($withdrawalConditions['deposit_otp_required'] ?? true)) {
            return $this->processDirectDepositWithdrawal($request);
        }
        
        // OTP is required - First step: Basic validation (NO PASSWORD YET) and send OTP
        $request->validate([
            'method_id' => 'required|exists:withdraw_methods,id',
            'account_details' => 'required|string|max:500'
        ], [
            'method_id.required' => 'Please select a withdrawal method',
            'method_id.exists' => 'Invalid withdrawal method selected',
            'account_details.required' => 'Account details are required'
        ]);
        
        // Get the selected withdrawal method for validation
        $withdrawMethod = WithdrawMethod::where('id', $request->method_id)->where('status', 1)->first();
        
        if (!$withdrawMethod) {
            return back()->with('swal_error', [
                'title' => 'Invalid Method!',
                'text' => 'Selected withdrawal method is not available',
                'icon' => 'error'
            ]);
        }
        
        // Check if user has active deposit
        $activeDeposit = $user->invests()->where('status', 1)->first();
        if (!$activeDeposit) {
            return back()->with('swal_error', [
                'title' => 'No Active Deposit!',
                'text' => 'You don\'t have any active deposit to withdraw',
                'icon' => 'warning'
            ]);
        }
        
        // Check for pending withdrawals
        $pendingWithdrawal = Withdrawal::where('user_id', $user->id)
            ->where('status', 2)
            ->exists();
            
        if ($pendingWithdrawal) {
            return back()->with('swal_error', [
                'title' => 'Pending Request Exists!',
                'text' => 'You already have a pending withdrawal request',
                'icon' => 'warning'
            ]);
        }
        
        // Store withdrawal data in session for OTP verification (NO PASSWORD YET)
        session([
            'deposit_withdrawal_data' => [
                'method_id' => $request->method_id,
                'account_details' => $request->account_details
            ],
            'show_deposit_otp_form' => true
        ]);
        
        // Generate and send OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $freshUser = User::find($user->id);
        $freshUser->ver_code = $otpCode;
        $freshUser->ver_code_send_at = now();
        $freshUser->save();
        
        // Send OTP email
        try {
            // Simple email approach using basic HTML
            $subject = 'Deposit Withdrawal OTP Verification - ' . config('app.name');
            $emailBody = $this->createOtpEmailContent($user, $otpCode, 'Deposit Withdrawal Verification');
            
            // Try to send email
            $emailSent = false;
            try {
                Mail::html($emailBody, function($message) use ($user, $subject) {
                    $message->to($user->email)
                            ->subject($subject);
                });
                $emailSent = true;
            } catch (\Exception $e) {
                Log::error('Mail::html failed, trying with send method: ' . $e->getMessage());
                
                // Fallback to view-based email
                try {
                    Mail::send('emails.verification-code', [
                        'user' => $user,
                        'code' => $otpCode,
                        'type' => 'Deposit Withdrawal Verification'
                    ], function($message) use ($user, $subject) {
                        $message->to($user->email)
                                ->subject($subject);
                    });
                    $emailSent = true;
                } catch (\Exception $e2) {
                    Log::error('Mail::send also failed: ' . $e2->getMessage());
                    $emailSent = false;
                }
            }
            
            if ($emailSent) {
                return back()->withInput()->with([
                    'swal_success' => [
                        'title' => 'OTP Sent!',
                        'text' => 'Verification code has been sent to your email address. Please check your email and enter the 6-digit code below.',
                        'icon' => 'success'
                    ]
                ]);
            } else {
                // Email failed, clear OTP and return error
                $user->ver_code = null;
                $user->ver_code_send_at = null;
                $user->save();
                session()->forget(['deposit_withdrawal_data', 'show_deposit_otp_form']);
                
                return back()->withInput()->with('swal_error', [
                    'title' => 'Email Error!',
                    'text' => 'Could not send verification code. Please check your email settings or try again later.',
                    'icon' => 'error'
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('OTP email sending failed: ' . $e->getMessage());
            return back()->with('swal_error', [
                'title' => 'Email Error!',
                'text' => 'Could not send verification code. Please try again.',
                'icon' => 'error'
            ]);
        }
    }
    
    /**
     * Verify OTP and process deposit withdrawal
     */
    private function verifyDepositWithdrawOtp(Request $request)
    {
        $user = Auth::user();
        
        // Validate OTP and password
        $request->validate([
            'otp_code' => 'required|digits:6',
            'password' => 'required|string'
        ], [
            'otp_code.required' => 'Verification code is required',
            'otp_code.digits' => 'Verification code must be 6 digits',
            'password.required' => 'Transaction password is required'
        ]);
        
        // Check OTP first
        if (!$user->ver_code || $user->ver_code != $request->otp_code) {
            return back()->with('swal_error', [
                'title' => 'Invalid OTP!',
                'text' => 'The verification code you entered is incorrect.',
                'icon' => 'error'
            ])->withInput();
        }
        
        // Check OTP expiry (10 minutes)
        if (!$user->ver_code_send_at || Carbon::parse($user->ver_code_send_at)->addMinutes(10)->isPast()) {
            return back()->with('swal_error', [
                'title' => 'OTP Expired!',
                'text' => 'The verification code has expired. Please request a new one.',
                'icon' => 'error'
            ]);
        }
        
        // NOW verify password after OTP is confirmed
        if (!Auth::attempt(['username' => $user->username, 'password' => $request->password])) {
            return back()->with('swal_error', [
                'title' => 'Authentication Failed!',
                'text' => 'Invalid transaction password',
                'icon' => 'error'
            ])->withInput();
        }
        
        // Get withdrawal data from session
        $withdrawalData = session('deposit_withdrawal_data');
        if (!$withdrawalData) {
            return back()->with('swal_error', [
                'title' => 'Session Expired!',
                'text' => 'Please start the withdrawal process again.',
                'icon' => 'error'
            ]);
        }
        
        // NOW check withdrawal conditions after OTP verification
        if (!function_exists('checkWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $conditionCheck = checkWithdrawalConditions($user);
        
        if (!$conditionCheck['allowed']) {
            // Clear OTP and session data
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            session()->forget(['deposit_withdrawal_data', 'show_deposit_otp_form']);
            
            // Check if profile completion is the specific issue
            $failures = $conditionCheck['failures'];
            if (count($failures) === 1 && strpos($failures[0], 'Profile completion') !== false) {
                return redirect()->back()->with('swal_error', [
                    'title' => 'Profile Incomplete!',
                    'text' => 'Please complete your profile information before making withdrawals. Update your profile with all required details including name, mobile, country, and address.',
                    'icon' => 'warning'
                ]);
            }
            
            return redirect()->back()->with('swal_error', [
                'title' => 'Requirements Not Met!',
                'text' => 'Withdrawal requirements not met: ' . implode(', ', $conditionCheck['failures']),
                'icon' => 'error'
            ]);
        }
        
        // Clear OTP and session data after successful verification
        $user->ver_code = null;
        $user->ver_code_send_at = null;
        $user->save();
        session()->forget(['deposit_withdrawal_data', 'show_deposit_otp_form']);
        
        // Process the withdrawal with stored data
        $withdrawMethod = WithdrawMethod::where('id', $withdrawalData['method_id'])->where('status', 1)->first();
        
        if (!$withdrawMethod) {
            return back()->with('swal_error', [
                'title' => 'Invalid Method!',
                'text' => 'Selected withdrawal method is not available',
                'icon' => 'error'
            ]);
        }
        
        // Get user's active deposit
        $activeDeposit = $user->invests()->where('status', 1)->first();
        if (!$activeDeposit) {
            return back()->with('swal_error', [
                'title' => 'No Active Deposit!',
                'text' => 'You don\'t have any active deposit to withdraw',
                'icon' => 'warning'
            ]);
        }
        
        // Check if there's already a pending withdrawal (double-check)
        $pendingWithdrawal = Withdrawal::where('user_id', $user->id)
            ->where('status', 2)
            ->exists();
            
        if ($pendingWithdrawal) {
            return back()->with('swal_error', [
                'title' => 'Pending Request Exists!',
                'text' => 'You already have a pending withdrawal request',
                'icon' => 'warning'
            ]);
        }
        
        try {
            DB::beginTransaction();
            
            // Calculate withdrawal amounts (deposit withdrawals use 20% fee)
            $depositAmount = $activeDeposit->amount;
            $withdrawalFee = $depositAmount * 0.20; // Fixed 20% fee for deposit withdrawals
            $netAmount = $depositAmount - $withdrawalFee;
            
            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'method_id' => $withdrawalData['method_id'],
                'amount' => $netAmount,
                'charge' => $withdrawalFee,
                'final_amount' => $netAmount,
                'currency' => $withdrawMethod->currency ?? 'USD',
                'rate' => $withdrawMethod->rate ?? 1,
                'trx' => getTrx(),
                'withdraw_type' => 'deposit',
                'withdraw_information' => json_encode([
                    'method' => $withdrawMethod->name,
                    'details' => $withdrawalData['account_details'],
                    'deposit_info' => [
                        'plan_name' => $activeDeposit->plan->name ?? 'Unknown Plan',
                        'deposit_amount' => $depositAmount,
                        'withdrawal_fee' => $withdrawalFee,
                        'fee_percentage' => 20
                    ],
                    'method_info' => [
                        'processing_time' => $withdrawMethod->processing_time,
                        'instructions' => $withdrawMethod->instructions,
                        'min_amount' => $withdrawMethod->min_amount,
                        'max_amount' => $withdrawMethod->max_amount
                    ],
                    'otp_verification' => [
                        'verified_at' => now()->toDateTimeString(),
                        'verified_ip' => request()->ip()
                    ]
                ]),
                'status' => 2, // Pending
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
            $transaction->details = 'Withdrawal request for deposit: ' . ($activeDeposit->plan->name ?? 'Unknown Plan') . ' via ' . $withdrawMethod->name . ' (Fee: $' . number_format($withdrawalFee, 2) . ', OTP Verified)';
            $transaction->post_balance = 0; // Will be updated when withdrawal is approved
            $transaction->save();
            
            DB::commit();
            
            return back()->with('swal_success', [
                'title' => 'Withdrawal Requested!',
                'text' => 'OTP verified successfully! Withdrawal request submitted. You will receive $' . number_format($netAmount, 2) . ' via ' . $withdrawMethod->name . ' after admin approval.',
                'icon' => 'success'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Deposit withdrawal request error: ' . $e->getMessage());
            
            return back()->with('swal_error', [
                'title' => 'Processing Error!',
                'text' => 'An error occurred while processing your withdrawal request. Please try again.',
                'icon' => 'error'
            ]);
        }
    }
    
    /**
     * Process direct deposit withdrawal without OTP (when OTP is disabled)
     */
    private function processDirectDepositWithdrawal(Request $request)
    {
        $user = Auth::user();
        
        // Validate form data including password
        $request->validate([
            'method_id' => 'required|exists:withdraw_methods,id',
            'account_details' => 'required|string|max:500',
            'password' => 'required|string'
        ], [
            'method_id.required' => 'Please select a withdrawal method',
            'method_id.exists' => 'Invalid withdrawal method selected',
            'account_details.required' => 'Account details are required',
            'password.required' => 'Transaction password is required'
        ]);
        
        // Verify password first
        if (!Auth::attempt(['username' => $user->username, 'password' => $request->password])) {
            return back()->with('swal_error', [
                'title' => 'Authentication Failed!',
                'text' => 'Invalid transaction password',
                'icon' => 'error'
            ])->withInput();
        }
        
        // Get the selected withdrawal method for validation
        $withdrawMethod = WithdrawMethod::where('id', $request->method_id)->where('status', 1)->first();
        
        if (!$withdrawMethod) {
            return back()->with('swal_error', [
                'title' => 'Invalid Method!',
                'text' => 'Selected withdrawal method is not available',
                'icon' => 'error'
            ]);
        }
        
        // Check if user has active deposit
        $activeDeposit = $user->invests()->where('status', 1)->first();
        if (!$activeDeposit) {
            return back()->with('swal_error', [
                'title' => 'No Active Deposit!',
                'text' => 'You don\'t have any active deposit to withdraw',
                'icon' => 'warning'
            ]);
        }
        
        // Check for pending withdrawals
        $pendingWithdrawal = Withdrawal::where('user_id', $user->id)
            ->where('status', 2)
            ->exists();
            
        if ($pendingWithdrawal) {
            return back()->with('swal_error', [
                'title' => 'Pending Request Exists!',
                'text' => 'You already have a pending withdrawal request',
                'icon' => 'warning'
            ]);
        }
        
        // Check withdrawal conditions
        if (!function_exists('checkWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $conditionCheck = checkWithdrawalConditions($user);
        
        if (!$conditionCheck['allowed']) {
            // Check if profile completion is the specific issue
            $failures = $conditionCheck['failures'];
            if (count($failures) === 1 && strpos($failures[0], 'Profile completion') !== false) {
                return redirect()->back()->with('swal_error', [
                    'title' => 'Profile Incomplete!',
                    'text' => 'Please complete your profile information before making withdrawals. Update your profile with all required details including name, mobile, country, and address.',
                    'icon' => 'warning'
                ]);
            }
            
            return redirect()->back()->with('swal_error', [
                'title' => 'Requirements Not Met!',
                'text' => 'Withdrawal requirements not met: ' . implode(', ', $conditionCheck['failures']),
                'icon' => 'error'
            ]);
        }
        
        try {
            DB::beginTransaction();
            
            // Calculate withdrawal amounts (deposit withdrawals use 20% fee)
            $depositAmount = $activeDeposit->amount;
            $withdrawalFee = $depositAmount * 0.20; // Fixed 20% fee for deposit withdrawals
            $netAmount = $depositAmount - $withdrawalFee;
            
            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'method_id' => $request->method_id,
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
                    ],
                    'method_info' => [
                        'processing_time' => $withdrawMethod->processing_time,
                        'instructions' => $withdrawMethod->instructions,
                        'min_amount' => $withdrawMethod->min_amount,
                        'max_amount' => $withdrawMethod->max_amount
                    ],
                    'direct_withdrawal' => [
                        'processed_at' => now()->toDateTimeString(),
                        'processed_ip' => request()->ip()
                    ]
                ]),
                'status' => 2, // Pending
            ]);
            
            // Update deposit status to withdrawn (status = 2)
            $activeDeposit->status = 2;
            $activeDeposit->save();
            
            // Create transaction record for deposit deactivation
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $depositAmount,
                'main_amo' => $user->balance,
                'charge' => 0,
                'type' => '-',
                'title' => 'Deposit Withdrawn',
                'details' => 'Deposit withdrawal processed - Amount: $' . number_format($netAmount, 2) . ' via ' . $withdrawMethod->name,
                'trx' => $withdrawal->trx,
            ]);
            
            DB::commit();
            
            // Clear any residual session data
            session()->forget(['deposit_withdrawal_data', 'show_deposit_otp_form']);
            
            return redirect()->route('user.withdraw.index')->with('swal_success', [
                'title' => 'Withdrawal Submitted!',
                'text' => 'Withdrawal request submitted successfully! You will receive $' . number_format($netAmount, 2) . ' via ' . $withdrawMethod->name . ' after admin approval.',
                'icon' => 'success'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Direct deposit withdrawal request error: ' . $e->getMessage());
            
            return back()->with('swal_error', [
                'title' => 'Processing Error!',
                'text' => 'An error occurred while processing your withdrawal request. Please try again.',
                'icon' => 'error'
            ])->withInput();
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
        
        // Check if user is currently in an OTP session for wallet withdrawal
        $isWalletOtpSession = session('show_wallet_otp_form') === true;
        $walletStoredData = session('wallet_withdrawal_data');

        // Get withdrawal conditions from database
        if (!function_exists('getWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        $withdrawalConditions = getWithdrawalConditions();
        
        // Check individual conditions
        $conditionCheck = checkWithdrawalConditions($user);

        $data = [
            'pageTitle' => 'Withdraw Wallet Balance',
            'depositWallet' => $depositWallet,
            'interestWallet' => $interestWallet,
            'totalWalletBalance' => $totalWalletBalance,
            'withdrawMethods' => $withdrawMethods,
            'withdrawalStats' => $withdrawalStats,
            'recentWithdrawals' => $recentWithdrawals,
            'kycVerified' => $user->kv == 1,
            'isWalletOtpSession' => $isWalletOtpSession,
            'walletStoredData' => $walletStoredData,
            'withdrawalConditions' => $withdrawalConditions,
            'conditionCheck' => $conditionCheck
        ];
        
        return view('frontend.withdraw-wallet', $data);
    }
    
    /**
     * Process wallet withdrawal request
     */
    public function walletWithdraw(Request $request)
    {
        $user = Auth::user();

        // Check if this is an OTP verification request
        if ($request->has('otp_code')) {
            return $this->verifyWalletWithdrawOtp($request);
        }

        // Get withdrawal conditions to check if OTP is required
        if (!function_exists('getWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        $withdrawalConditions = getWithdrawalConditions();
        
        // If OTP is NOT required, process withdrawal directly with password
        if (!($withdrawalConditions['wallet_otp_required'] ?? true)) {
            return $this->processDirectWalletWithdrawal($request);
        }

        // OTP is required - Initial wallet withdrawal request - validate form data (NO PASSWORD YET)
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method_id' => 'required|exists:withdraw_methods,id',
            'account_details' => 'required|string|max:500'
        ], [
            'amount.required' => 'Withdrawal amount is required',
            'amount.numeric' => 'Withdrawal amount must be a valid number',
            'amount.min' => 'Withdrawal amount must be greater than 0',
            'method_id.required' => 'Please select a withdrawal method',
            'method_id.exists' => 'Invalid withdrawal method selected',
            'account_details.required' => 'Account details are required'
        ]);

        // Get the selected withdrawal method for validation
        $withdrawMethod = WithdrawMethod::findOrFail($request->method_id);

        // Validate amount against method limits
        if ($request->amount < ($withdrawMethod->min_amount ?? 1)) {
            return back()->with('swal_error', [
                'title' => 'Amount Too Low!',
                'text' => 'Minimum withdrawal amount for ' . $withdrawMethod->name . ' is $' . number_format($withdrawMethod->min_amount ?? 1, 2),
                'icon' => 'error'
            ])->withInput();
        }

        if ($request->amount > ($withdrawMethod->max_amount ?? 999999)) {
            return back()->with('swal_error', [
                'title' => 'Amount Too High!',
                'text' => 'Maximum withdrawal amount for ' . $withdrawMethod->name . ' is $' . number_format($withdrawMethod->max_amount ?? 999999, 2),
                'icon' => 'error'
            ])->withInput();
        }

        // Calculate wallet balance
        $depositWallet = $user->deposit_wallet ?? 0;
        $interestWallet = $user->interest_wallet ?? 0;
        $totalWalletBalance = $depositWallet + $interestWallet;
        
        // Check if user has sufficient balance
        if ($request->amount > $totalWalletBalance) {
            return back()->with('swal_error', [
                'title' => 'Insufficient Balance!',
                'text' => 'Insufficient wallet balance. Available: $' . number_format($totalWalletBalance, 2),
                'icon' => 'error'
            ])->withInput();
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
                return back()->with('swal_error', [
                    'title' => 'Daily Limit Exceeded!',
                    'text' => 'Daily withdrawal limit exceeded for ' . $withdrawMethod->name . '. Remaining limit: $' . number_format($remainingLimit, 2),
                    'icon' => 'error'
                ])->withInput();
            }
        }
        
        // Check if there's already a pending wallet withdrawal
        $pendingWithdrawal = Withdrawal::where('user_id', $user->id)
            ->where('withdraw_type', 'wallet')
            ->where('status', 2)
            ->exists();
            
        if ($pendingWithdrawal) {
            return back()->with('swal_error', [
                'title' => 'Pending Request Exists!',
                'text' => 'You already have a pending wallet withdrawal request',
                'icon' => 'warning'
            ])->withInput();
        }

        // Store withdrawal data in session for OTP verification
        session([
            'wallet_withdrawal_data' => [
                'amount' => $request->amount,
                'method_id' => $request->method_id,
                'account_details' => $request->account_details,
                'total_wallet_balance' => $totalWalletBalance,
                'deposit_wallet' => $depositWallet,
                'interest_wallet' => $interestWallet
            ],
            'show_wallet_otp_form' => true
        ]);

        // Generate and send OTP
        $otpCode = random_int(100000, 999999);
        $freshUser = User::find($user->id);
        $freshUser->ver_code = $otpCode;
        $freshUser->ver_code_send_at = now();
        $freshUser->save();

        // Send OTP email
        try {
            // Simple email approach using basic HTML (same as deposit withdrawal)
            $subject = 'Wallet Withdrawal OTP Verification - ' . config('app.name');
            $emailBody = $this->createOtpEmailContent($user, $otpCode, 'Wallet Withdrawal Verification');
            
            // Try to send email
            $emailSent = false;
            try {
                Mail::html($emailBody, function($message) use ($user, $subject) {
                    $message->to($user->email)
                            ->subject($subject);
                });
                $emailSent = true;
            } catch (\Exception $e) {
                Log::error('Mail::html failed, trying with send method: ' . $e->getMessage());
                
                // Fallback to view-based email
                try {
                    Mail::send('emails.verification-code', [
                        'user' => $user,
                        'code' => $otpCode,
                        'type' => 'Wallet Withdrawal Verification'
                    ], function($message) use ($user, $subject) {
                        $message->to($user->email)
                                ->subject($subject);
                    });
                    $emailSent = true;
                } catch (\Exception $e2) {
                    Log::error('Mail::send also failed: ' . $e2->getMessage());
                    $emailSent = false;
                }
            }
            
            if ($emailSent) {
                return redirect()->route('user.withdraw.wallet')->with('swal_success', [
                    'title' => 'OTP Sent!',
                    'text' => 'Verification code has been sent to your email address. Please check your email and enter the 6-digit code below.',
                    'icon' => 'success'
                ]);
            } else {
                // Email failed, clear OTP and return error
                $freshUser->ver_code = null;
                $freshUser->ver_code_send_at = null;
                $freshUser->save();
                session()->forget(['wallet_withdrawal_data', 'show_wallet_otp_form']);
                
                return redirect()->route('user.withdraw.wallet')->withInput()->with('swal_error', [
                    'title' => 'Email Error!',
                    'text' => 'Could not send verification code. Please check your email settings or try again later.',
                    'icon' => 'error'
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Wallet OTP email sending failed: ' . $e->getMessage());
            return redirect()->route('user.withdraw.wallet')->with('swal_error', [
                'title' => 'Email Error!',
                'text' => 'Could not send verification code. Please try again.',
                'icon' => 'error'
            ]);
        }
    }

    /**
     * Verify OTP and process wallet withdrawal
     */
    private function verifyWalletWithdrawOtp(Request $request)
    {
        $user = Auth::user();
        
        // Validate OTP and password
        $request->validate([
            'otp_code' => 'required|digits:6',
            'password' => 'required|string'
        ], [
            'otp_code.required' => 'Verification code is required',
            'otp_code.digits' => 'Verification code must be 6 digits',
            'password.required' => 'Transaction password is required'
        ]);
        
        // Check OTP first
        if (!$user->ver_code || $user->ver_code != $request->otp_code) {
            return redirect()->route('user.withdraw.wallet')->with('swal_error', [
                'title' => 'Invalid OTP!',
                'text' => 'The verification code you entered is incorrect.',
                'icon' => 'error'
            ])->withInput();
        }
        
        // Check OTP expiry (10 minutes)
        if (!$user->ver_code_send_at || Carbon::parse($user->ver_code_send_at)->addMinutes(10)->isPast()) {
            return redirect()->route('user.withdraw.wallet')->with('swal_error', [
                'title' => 'OTP Expired!',
                'text' => 'The verification code has expired. Please request a new one.',
                'icon' => 'error'
            ]);
        }
        
        // NOW verify password after OTP is confirmed
        if (!Auth::attempt(['username' => $user->username, 'password' => $request->password])) {
            return redirect()->route('user.withdraw.wallet')->with('swal_error', [
                'title' => 'Authentication Failed!',
                'text' => 'Invalid transaction password',
                'icon' => 'error'
            ])->withInput();
        }
        
        // Get withdrawal data from session
        $withdrawalData = session('wallet_withdrawal_data');
        if (!$withdrawalData) {
            return redirect()->route('user.withdraw.wallet')->with('swal_error', [
                'title' => 'Session Expired!',
                'text' => 'Please start the withdrawal process again.',
                'icon' => 'error'
            ]);
        }
        
        // NOW check withdrawal conditions after OTP verification
        if (!function_exists('checkWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $conditionCheck = checkWithdrawalConditions($user);
        
        if (!$conditionCheck['allowed']) {
            // Clear OTP and session data
            $freshUser = User::find($user->id);
            $freshUser->ver_code = null;
            $freshUser->ver_code_send_at = null;
            $freshUser->save();
            session()->forget(['wallet_withdrawal_data', 'show_wallet_otp_form']);
            
            // Check if profile completion is the specific issue
            $failures = $conditionCheck['failures'];
            if (count($failures) === 1 && strpos($failures[0], 'Profile completion') !== false) {
                return redirect()->route('user.withdraw.wallet')->with('swal_error', [
                    'title' => 'Profile Incomplete!',
                    'text' => 'Please complete your profile information before making withdrawals. Update your profile with all required details including name, mobile, country, and address.',
                    'icon' => 'warning'
                ]);
            }
            
            return redirect()->route('user.withdraw.wallet')->with('swal_error', [
                'title' => 'Requirements Not Met!',
                'text' => 'Withdrawal requirements not met: ' . implode(', ', $conditionCheck['failures']),
                'icon' => 'error'
            ]);
        }
        
        // Clear OTP and session data after successful verification
        $freshUser = User::find($user->id);
        $freshUser->ver_code = null;
        $freshUser->ver_code_send_at = null;
        $freshUser->save();
        session()->forget(['wallet_withdrawal_data', 'show_wallet_otp_form']);
        
        // Process the withdrawal with stored data
        $withdrawMethod = WithdrawMethod::where('id', $withdrawalData['method_id'])->where('status', 1)->first();
        
        if (!$withdrawMethod) {
            return back()->with('swal_error', [
                'title' => 'Invalid Method!',
                'text' => 'Selected withdrawal method is not available',
                'icon' => 'error'
            ]);
        }
        
        // Re-validate current wallet balance (in case it changed)
        $freshUser = User::find($user->id);
        $currentDepositWallet = $freshUser->deposit_wallet ?? 0;
        $currentInterestWallet = $freshUser->interest_wallet ?? 0;
        $currentTotalBalance = $currentDepositWallet + $currentInterestWallet;
        
        if ($withdrawalData['amount'] > $currentTotalBalance) {
            return back()->with('swal_error', [
                'title' => 'Insufficient Balance!',
                'text' => 'Insufficient wallet balance. Available: $' . number_format($currentTotalBalance, 2),
                'icon' => 'error'
            ]);
        }

        try {
            DB::beginTransaction();
            
            // Calculate withdrawal fees using both fixed_charge and percent_charge
            $withdrawalAmount = $withdrawalData['amount'];
            $fixedCharge = $withdrawMethod->fixed_charge ?? 0;
            $percentCharge = ($withdrawMethod->percent_charge ?? 0) / 100;
            
            // Calculate total charge: fixed charge + percentage charge
            $percentageFee = $withdrawalAmount * $percentCharge;
            $totalCharge = $fixedCharge + $percentageFee;
            $netAmount = $withdrawalAmount - $totalCharge;
            
            // Ensure net amount is not negative
            if ($netAmount <= 0) {
                return back()->with('swal_error', [
                    'title' => 'Amount Too Low!',
                    'text' => 'Withdrawal amount is too low after charges. Minimum required: $' . number_format($totalCharge + 0.01, 2),
                    'icon' => 'error'
                ]);
            }
            
            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'method_id' => $withdrawalData['method_id'],
                'amount' => $withdrawalAmount,
                'charge' => $totalCharge,
                'final_amount' => $netAmount,
                'currency' => $withdrawMethod->currency ?? 'USD',
                'rate' => $withdrawMethod->rate ?? 1,
                'trx' => getTrx(),
                'withdraw_type' => 'wallet',
                'withdraw_information' => json_encode([
                    'method' => $withdrawMethod->name,
                    'details' => $withdrawalData['account_details'],
                    'wallet_breakdown' => [
                        'deposit_wallet' => $currentDepositWallet,
                        'interest_wallet' => $currentInterestWallet,
                        'total_balance' => $currentTotalBalance
                    ],
                    'charges' => [
                        'fixed_charge' => $fixedCharge,
                        'percent_charge' => $withdrawMethod->percent_charge,
                        'total_charge' => $totalCharge
                    ],
                    'method_info' => [
                        'processing_time' => $withdrawMethod->processing_time,
                        'instructions' => $withdrawMethod->instructions,
                        'min_amount' => $withdrawMethod->min_amount,
                        'max_amount' => $withdrawMethod->max_amount
                    ],
                    'otp_verification' => [
                        'verified_at' => now()->toDateTimeString(),
                        'verified_ip' => request()->ip()
                    ]
                ]),
                'status' => 2, // Pending
            ]);
            
            // Deduct from user wallets (temporarily, will be restored if withdrawal is rejected)
            $remainingAmount = $withdrawalAmount;
            
            // Get fresh user instance to ensure we have the latest data
            $freshUser = User::find($user->id);
            
            // First deduct from deposit wallet
            if ($remainingAmount > 0 && $currentDepositWallet > 0) {
                $deductFromDeposit = min($remainingAmount, $currentDepositWallet);
                $freshUser->deposit_wallet -= $deductFromDeposit;
                $remainingAmount -= $deductFromDeposit;
            }
            
            // Then deduct from interest wallet if needed
            if ($remainingAmount > 0 && $currentInterestWallet > 0) {
                $deductFromInterest = min($remainingAmount, $currentInterestWallet);
                $freshUser->interest_wallet -= $deductFromInterest;
                $remainingAmount -= $deductFromInterest;
            }
            
            $freshUser->save();
            
            // Create transaction record
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $withdrawalAmount;
            $transaction->charge = $totalCharge;
            $transaction->trx_type = '-';
            $transaction->trx = $withdrawal->trx;
            $transaction->wallet_type = 'wallet_withdrawal';
            $transaction->remark = 'wallet_withdrawal';
            $transaction->details = 'Wallet withdrawal request: $' . number_format($withdrawalAmount, 2) . ' via ' . $withdrawMethod->name . ' (Charge: $' . number_format($totalCharge, 2) . ', OTP Verified)';
            $transaction->post_balance = ($freshUser->deposit_wallet ?? 0) + ($freshUser->interest_wallet ?? 0);
            $transaction->save();
            
            DB::commit();
            
            return back()->with('swal_success', [
                'title' => 'Withdrawal Requested!',
                'text' => 'OTP verified successfully! Wallet withdrawal request submitted. You will receive $' . number_format($netAmount, 2) . ' via ' . $withdrawMethod->name . ' after admin approval.',
                'icon' => 'success'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Wallet withdrawal request error: ' . $e->getMessage());
            
            return back()->with('swal_error', [
                'title' => 'Processing Error!',
                'text' => 'An error occurred while processing your withdrawal request. Please try again.',
                'icon' => 'error'
            ]);
        }
    }
    
    /**
     * Process direct wallet withdrawal without OTP (when OTP is disabled)
     */
    private function processDirectWalletWithdrawal(Request $request)
    {
        $user = Auth::user();
        
        // Validate form data including password
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method_id' => 'required|exists:withdraw_methods,id',
            'account_details' => 'required|string|max:500',
            'password' => 'required|string'
        ], [
            'amount.required' => 'Withdrawal amount is required',
            'amount.numeric' => 'Withdrawal amount must be a valid number',
            'amount.min' => 'Withdrawal amount must be greater than 0',
            'method_id.required' => 'Please select a withdrawal method',
            'method_id.exists' => 'Invalid withdrawal method selected',
            'account_details.required' => 'Account details are required',
            'password.required' => 'Transaction password is required'
        ]);

        // Verify password first
        if (!Auth::attempt(['username' => $user->username, 'password' => $request->password])) {
            return back()->with('swal_error', [
                'title' => 'Authentication Failed!',
                'text' => 'Invalid transaction password',
                'icon' => 'error'
            ])->withInput();
        }

        // Get the selected withdrawal method for validation
        $withdrawMethod = WithdrawMethod::findOrFail($request->method_id);

        // Validate amount against method limits
        if ($request->amount < ($withdrawMethod->min_amount ?? 1)) {
            return back()->with('swal_error', [
                'title' => 'Amount Too Low!',
                'text' => 'Minimum withdrawal amount for ' . $withdrawMethod->name . ' is $' . number_format($withdrawMethod->min_amount ?? 1, 2),
                'icon' => 'error'
            ])->withInput();
        }

        if ($request->amount > ($withdrawMethod->max_amount ?? 999999)) {
            return back()->with('swal_error', [
                'title' => 'Amount Too High!',
                'text' => 'Maximum withdrawal amount for ' . $withdrawMethod->name . ' is $' . number_format($withdrawMethod->max_amount ?? 999999, 2),
                'icon' => 'error'
            ])->withInput();
        }

        // Calculate wallet balance
        $depositWallet = $user->deposit_wallet ?? 0;
        $interestWallet = $user->interest_wallet ?? 0;
        $totalWalletBalance = $depositWallet + $interestWallet;
        
        // Check if user has sufficient balance
        if ($request->amount > $totalWalletBalance) {
            return back()->with('swal_error', [
                'title' => 'Insufficient Balance!',
                'text' => 'Insufficient wallet balance. Available: $' . number_format($totalWalletBalance, 2),
                'icon' => 'error'
            ])->withInput();
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
                return back()->with('swal_error', [
                    'title' => 'Daily Limit Exceeded!',
                    'text' => 'Daily withdrawal limit exceeded for ' . $withdrawMethod->name . '. Remaining limit: $' . number_format($remainingLimit, 2),
                    'icon' => 'error'
                ])->withInput();
            }
        }
        
        // Check if there's already a pending wallet withdrawal
        $pendingWithdrawal = Withdrawal::where('user_id', $user->id)
            ->where('withdraw_type', 'wallet')
            ->where('status', 2)
            ->exists();
            
        if ($pendingWithdrawal) {
            return back()->with('swal_error', [
                'title' => 'Pending Request Exists!',
                'text' => 'You already have a pending wallet withdrawal request',
                'icon' => 'warning'
            ])->withInput();
        }

        // Check withdrawal conditions
        if (!function_exists('checkWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $conditionCheck = checkWithdrawalConditions($user);
        
        if (!$conditionCheck['allowed']) {
            // Check if profile completion is the specific issue
            $failures = $conditionCheck['failures'];
            if (count($failures) === 1 && strpos($failures[0], 'Profile completion') !== false) {
                return back()->with('swal_error', [
                    'title' => 'Profile Incomplete!',
                    'text' => 'Please complete your profile information before making withdrawals. Update your profile with all required details including name, mobile, country, and address.',
                    'icon' => 'warning'
                ])->withInput();
            }
            
            return back()->with('swal_error', [
                'title' => 'Requirements Not Met!',
                'text' => 'Withdrawal requirements not met: ' . implode(', ', $conditionCheck['failures']),
                'icon' => 'error'
            ])->withInput();
        }

        try {
            DB::beginTransaction();
            
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
                return back()->with('swal_error', [
                    'title' => 'Amount Too Low!',
                    'text' => 'Withdrawal amount is too low after charges. Minimum required: $' . number_format($totalCharge + 0.01, 2),
                    'icon' => 'error'
                ])->withInput();
            }
            
            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'method_id' => $request->method_id,
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
                    ],
                    'method_info' => [
                        'processing_time' => $withdrawMethod->processing_time,
                        'instructions' => $withdrawMethod->instructions,
                        'min_amount' => $withdrawMethod->min_amount,
                        'max_amount' => $withdrawMethod->max_amount
                    ],
                    'direct_withdrawal' => [
                        'processed_at' => now()->toDateTimeString(),
                        'processed_ip' => request()->ip()
                    ]
                ]),
                'status' => 2, // Pending
            ]);
            
            // Deduct from user wallets (temporarily, will be restored if withdrawal is rejected)
            $remainingAmount = $withdrawalAmount;
            
            // Get fresh user instance to ensure we have the latest data
            $freshUser = User::find($user->id);
            
            // First deduct from deposit wallet
            if ($remainingAmount > 0 && $depositWallet > 0) {
                $deductFromDeposit = min($remainingAmount, $depositWallet);
                $freshUser->deposit_wallet -= $deductFromDeposit;
                $remainingAmount -= $deductFromDeposit;
            }
            
            // Then deduct from interest wallet if needed
            if ($remainingAmount > 0 && $interestWallet > 0) {
                $deductFromInterest = min($remainingAmount, $interestWallet);
                $freshUser->interest_wallet -= $deductFromInterest;
                $remainingAmount -= $deductFromInterest;
            }
            
            $freshUser->save();
            
            // Create transaction record
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $withdrawalAmount;
            $transaction->charge = $totalCharge;
            $transaction->trx_type = '-';
            $transaction->trx = $withdrawal->trx;
            $transaction->wallet_type = 'wallet_withdrawal';
            $transaction->remark = 'wallet_withdrawal';
            $transaction->details = 'Direct wallet withdrawal request: $' . number_format($withdrawalAmount, 2) . ' via ' . $withdrawMethod->name . ' (Charge: $' . number_format($totalCharge, 2) . ')';
            $transaction->post_balance = ($freshUser->deposit_wallet ?? 0) + ($freshUser->interest_wallet ?? 0);
            $transaction->save();
            
            DB::commit();
            
            // Clear any residual session data
            session()->forget(['wallet_withdrawal_data', 'show_wallet_otp_form']);
            
            return redirect()->route('user.withdraw.wallet')->with('swal_success', [
                'title' => 'Withdrawal Submitted!',
                'text' => 'Wallet withdrawal request submitted successfully! You will receive $' . number_format($netAmount, 2) . ' via ' . $withdrawMethod->name . ' after admin approval.',
                'icon' => 'success'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Direct wallet withdrawal request error: ' . $e->getMessage());
            
            return back()->with('swal_error', [
                'title' => 'Processing Error!',
                'text' => 'An error occurred while processing your withdrawal request. Please try again.',
                'icon' => 'error'
            ])->withInput();
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

    
    /**
     * Check if wallet OTP is required based on settings
     */
    private function isWalletOtpRequired()
    {
        // Get the general settings or default to true for security
        $generalSetting = GeneralSetting::first();
        return $generalSetting ? ($generalSetting->wallet_otp_verification ?? true) : true;
    }
  
    
    /**
     * Create simple HTML email content for OTP
     */
    private function createOtpEmailContent($user, $otp, $type = 'Verification')
    {
        $appName = config('app.name');
        $userName = $user->name ?? $user->username;
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>{$type} - {$appName}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; }
                .logo { font-size: 24px; font-weight: bold; color: #007bff; }
                .otp-code { background: #007bff; color: white; font-size: 28px; font-weight: bold; padding: 15px 30px; border-radius: 8px; text-align: center; margin: 20px 0; letter-spacing: 3px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>{$appName}</div>
                    <h2>{$type}</h2>
                </div>
                
                <p>Hello {$userName},</p>
                
                <p>You have requested a verification code for your account. Please use the code below to complete your verification:</p>
                
                <div class='otp-code'>{$otp}</div>
                
                <div class='warning'>
                    <strong>Important:</strong>
                    <ul>
                        <li>This code is valid for 10 minutes only</li>
                        <li>Never share this code with anyone</li>
                        <li>Use this code only on our official website</li>
                    </ul>
                </div>
                
                <p>If you did not request this verification code, please ignore this email and contact our support team.</p>
                
                <div class='footer'>
                    <p>This is an automated message from {$appName}. Please do not reply to this email.</p>
                    <p>Generated at: " . now()->format('M d, Y h:i A') . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
