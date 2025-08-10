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

        $data = [
            'pageTitle' => 'Withdraw Deposit',
            'activeDeposit' => $activeDeposit,
            'withdrawalDetails' => $withdrawalDetails,
            'withdrawMethods' => $withdrawMethods,
            'withdrawalStats' => $withdrawalStats,
            'recentWithdrawals' => $recentWithdrawals,
            'kycVerified' => $user->kv == 1,
            'isDepositOtpSession' => $isDepositOtpSession,
            'depositStoredData' => $depositStoredData
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
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();
            // Continue with normal flow to resend OTP
        }
        
        // Check if user is in OTP verification mode
        if ($request->has('otp_code')) {
            return $this->verifyDepositWithdrawOtp($request);
        }
        
        // First step: Basic validation (NO PASSWORD YET) and send OTP
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
        
        // CHECK ALL WITHDRAWAL CONDITIONS BEFORE SENDING OTP
        if (!function_exists('checkWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $conditionCheck = checkWithdrawalConditions($user);
        
        if (!$conditionCheck['allowed']) {
            $failures = $conditionCheck['failures'];
            
            // Check for specific conditions and provide targeted error messages
            if (count($failures) === 1) {
                $failure = $failures[0];
                
                // KYC Verification Required
                if (strpos($failure, 'KYC verification') !== false) {
                    return redirect()->back()->with('swal_error', [
                        'title' => 'KYC Verification Required!',
                        'text' => 'Please complete your KYC (Know Your Customer) verification before making withdrawals. This includes uploading government-issued ID and completing identity verification.',
                        'icon' => 'warning'
                    ])->withInput();
                }
                
                // Email Verification Required
                if (strpos($failure, 'Email verification') !== false) {
                    return redirect()->back()->with('swal_error', [
                        'title' => 'Email Verification Required!',
                        'text' => 'Please verify your email address before making withdrawals. Check your inbox for the verification link or request a new verification email.',
                        'icon' => 'warning'
                    ])->withInput();
                }
                
                // Profile Completion Required
                if (strpos($failure, 'Profile completion') !== false) {
                    return redirect()->back()->with('swal_error', [
                        'title' => 'Profile Incomplete!',
                        'text' => 'Please complete your profile information before making withdrawals. Update your profile with all required details including name, mobile, country, and address.',
                        'icon' => 'warning'
                    ])->withInput();
                }
                
                // Minimum Investment Duration
                if (strpos($failure, 'Minimum investment duration') !== false) {
                    preg_match('/(\d+)\s+days/', $failure, $matches);
                    $days = $matches[1] ?? 'required';
                    return redirect()->back()->with('swal_error', [
                        'title' => 'Investment Duration Requirement!',
                        'text' => 'Your investment must be active for at least ' . $days . ' days before you can make a withdrawal. Please wait until this requirement is met.',
                        'icon' => 'warning'
                    ])->withInput();
                }
                
                // Referral Requirement
                if (strpos($failure, 'referral') !== false) {
                    return redirect()->back()->with('swal_error', [
                        'title' => 'Referral Requirement!',
                        'text' => $failure . '. Please invite friends to join and ensure they meet the investment requirements.',
                        'icon' => 'warning'
                    ])->withInput();
                }
            }
            
            // Multiple failures - show them all
            return redirect()->back()->with('swal_error', [
                'title' => 'Multiple Requirements Not Met!',
                'text' => 'Please complete the following requirements: ' . implode(', ', $failures),
                'icon' => 'error'
            ])->withInput();
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
        $user->ver_code = $otpCode;
        $user->ver_code_send_at = now();
        $user->save();
        
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
        
        // Conditions already checked before OTP was sent, no need to check again
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
        
        // Get OTP settings for wallet withdrawals
        $withdrawalConditions = json_decode(DB::table('general_settings')->value('withdrawal_conditions'), true) ?? [];
        $walletOtpRequired = $withdrawalConditions['wallet_otp_required'] ?? false;
        
        // Check if user is currently in an OTP session for wallet withdrawal
        $isWalletOtpSession = session('wallet_otp_required') === true;
        $walletStoredData = session('wallet_withdrawal_form_data');

        $data = [
            'pageTitle' => 'Withdraw Wallet Balance',
            'depositWallet' => $depositWallet,
            'interestWallet' => $interestWallet,
            'totalWalletBalance' => $totalWalletBalance,
            'withdrawMethods' => $withdrawMethods,
            'withdrawalStats' => $withdrawalStats,
            'recentWithdrawals' => $recentWithdrawals,
            'kycVerified' => $user->kv == 1,
            'walletOtpRequired' => $walletOtpRequired,
            'isWalletOtpSession' => $isWalletOtpSession,
            'walletStoredData' => $walletStoredData
        ];
        
        return view('frontend.withdraw-wallet', $data);
    }
    
    /**
     * Process wallet withdrawal request
     */
    public function walletWithdraw(Request $request)
    {
        $user = Auth::user();

        // Check if OTP is required and verify it
        $walletOtpRequired = $this->isWalletOtpRequired();
        if ($walletOtpRequired) {
            if (!session('wallet_otp_required')) {
                return redirect()->back()->with('error', 'Please verify your email first by clicking Send Verification Code')->withInput();
            }

            // Validate OTP
            $request->validate([
                'otp' => 'required|string|size:6'
            ], [
                'otp.required' => 'Verification code is required',
                'otp.size' => 'Verification code must be 6 digits'
            ]);

            $sessionOtp = session('wallet_withdrawal_otp');
            $otpExpiry = session('wallet_withdrawal_otp_expiry');

            // Enhanced debug logging
            Log::info('=== OTP Verification Debug ===', [
                'session_otp' => $sessionOtp,
                'otp_expiry' => $otpExpiry ? $otpExpiry->format('Y-m-d H:i:s') : null,
                'current_time' => now()->format('Y-m-d H:i:s'),
                'is_expired' => $otpExpiry ? now() > $otpExpiry : 'no_expiry',
                'user_otp' => $request->otp,
                'session_id' => session()->getId(),
                'wallet_otp_required' => session('wallet_otp_required')
            ]);

            // Check if OTP exists and is not expired
            if (!$sessionOtp || !$otpExpiry || now() > $otpExpiry) {
                // Clear invalid/expired session data
                session()->forget(['wallet_withdrawal_otp', 'wallet_withdrawal_otp_expiry', 'wallet_otp_required']);
                session()->save();
                
                // Add more detailed error message for debugging
                $errorDetails = [];
                if (!$sessionOtp) $errorDetails[] = 'No OTP found in session';
                if (!$otpExpiry) $errorDetails[] = 'No expiry time found in session';
                if ($otpExpiry && now() > $otpExpiry) {
                    $expiredAgo = now()->diffForHumans($otpExpiry);
                    $errorDetails[] = "OTP expired {$expiredAgo}";
                }
                
                Log::error('OTP Verification Failed', [
                    'session_otp' => $sessionOtp,
                    'expiry' => $otpExpiry ? $otpExpiry->format('Y-m-d H:i:s') : null,
                    'current_time' => now()->format('Y-m-d H:i:s'),
                    'details' => $errorDetails
                ]);
                
                $errorMessage = count($errorDetails) > 0 ? implode(', ', $errorDetails) : 'Verification code issue';
                $errorMessage .= '. Please click "Send Verification Code" to get a new code.';
                
                return redirect()->back()->with('error', $errorMessage)->withInput();
            }

            if ($request->otp !== $sessionOtp) {
                return redirect()->back()->with('error', 'Invalid verification code. Please try again.')->withInput();
            }

            // Clear OTP session data after successful verification
            session()->forget(['wallet_withdrawal_otp', 'wallet_withdrawal_otp_expiry', 'wallet_otp_required']);
        }
        
        // Check withdrawal conditions first
        if (!function_exists('checkWithdrawalConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $conditionCheck = checkWithdrawalConditions($user);
        
        if (!$conditionCheck['allowed']) {
            // Check if profile completion is the specific issue
            $failures = $conditionCheck['failures'];
            if (count($failures) === 1 && strpos($failures[0], 'Profile completion') !== false) {
                return redirect()->back()->with('error', 'Please complete your profile information before making withdrawals. Update your profile with all required details including name, mobile, country, and address.')->withInput();
            }
            
            return redirect()->back()->with('error', 'Withdrawal requirements not met: ' . implode(', ', $conditionCheck['failures']))->withInput();
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
     * Send OTP for deposit withdrawal via AJAX
     */
    public function sendDepositWithdrawOtp(Request $request)
    {
        try {
            $user = Auth::user();

            // Check withdrawal conditions
            $conditionCheck = checkWithdrawalConditions($user);
            if (!$conditionCheck['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Withdrawal requirements not met: ' . implode(', ', $conditionCheck['failures'])
                ], 422);
            }

            // Validate the form data
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1',
                'method_id' => 'required|exists:withdraw_methods,id',
                'account_details' => 'required|string|max:1000',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid transaction password'
                ], 422);
            }

            // Store form data in session
            session([
                'deposit_withdrawal_form_data' => [
                    'amount' => $request->amount,
                    'method_id' => $request->method_id,
                    'account_details' => $request->account_details,
                    'password' => $request->password,
                    'type' => 'deposit'
                ]
            ]);

            // Generate and send OTP
            $otp = sprintf('%06d', random_int(0, 999999));
            
            session([
                'deposit_otp_code' => $otp,
                'deposit_otp_expires' => now()->addMinutes(10),
                'deposit_otp_required' => true
            ]);

            // Send OTP email
            $emailSent = $this->sendOtpEmail($user, $otp, 'deposit');

            if (!$emailSent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification code. Please try again.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email. Please check your inbox.'
            ]);

        } catch (\Exception $e) {
            Log::error('Deposit withdraw OTP error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Send OTP for wallet withdrawal via AJAX
     */
    public function sendWalletWithdrawOtp(Request $request)
    {
        // Add immediate debug logging before any processing
        Log::info('=== ROUTE HIT: sendWalletWithdrawOtp ===', [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        try {
            Log::info('=== sendWalletWithdrawOtp method called ===', [
                'user_id' => Auth::id(),
                'request_data' => $request->except(['password']),
                'session_id' => session()->getId(),
                'request_method' => $request->method(),
                'request_uri' => $request->getRequestUri()
            ]);
            
            $user = Auth::user();

            // Check withdrawal conditions
            if (!function_exists('checkWithdrawalConditions')) {
                require_once app_path('helpers/ConditionHelper.php');
            }
            
            $conditionCheck = checkWithdrawalConditions($user);
            if (!$conditionCheck['allowed']) {
                Log::warning('Withdrawal conditions not met', $conditionCheck);
                return response()->json([
                    'success' => false,
                    'message' => 'Withdrawal requirements not met: ' . implode(', ', $conditionCheck['failures'])
                ], 422);
            }

            // Validate the form data
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1',
                'method_id' => 'required|exists:withdraw_methods,id',
                'account_details' => 'required|string|max:1000',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', ['errors' => $validator->errors()->all()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all())
                ], 422);
            }

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                Log::warning('Invalid password for user', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid transaction password'
                ], 422);
            }

            // Store form data in session with explicit session save
            $formData = [
                'amount' => $request->amount,
                'method_id' => $request->method_id,
                'account_details' => $request->account_details,
                'password' => $request->password,
                'type' => 'wallet'
            ];
            
            session()->put('wallet_withdrawal_form_data', $formData);
            session()->save(); // Force session save
            
            Log::info('Form data stored in session', [
                'form_data' => \Illuminate\Support\Arr::except($formData, ['password']),
                'session_after_store' => \Illuminate\Support\Arr::except(session('wallet_withdrawal_form_data', []), ['password'])
            ]);

            // Generate and send OTP
            $otp = sprintf('%06d', random_int(100000, 999999));
            $expiry = now()->addMinutes(10); // 10 minutes expiry
            
            Log::info('Generated OTP details', [
                'otp_length' => strlen($otp),
                'expiry_minutes' => 10,
                'expiry_time' => $expiry->format('Y-m-d H:i:s'),
                'current_time' => now()->format('Y-m-d H:i:s')
            ]);
            
            // Store OTP in session with explicit save
            session()->put('wallet_withdrawal_otp', $otp);
            session()->put('wallet_withdrawal_otp_expiry', $expiry);
            session()->put('wallet_otp_required', true);
            session()->save(); // Force session save

            // Verify session storage immediately
            $storedOtp = session('wallet_withdrawal_otp');
            $storedExpiry = session('wallet_withdrawal_otp_expiry');
            $storedRequired = session('wallet_otp_required');
            
            Log::info('Session storage verification', [
                'otp_stored' => $storedOtp === $otp,
                'expiry_stored' => $storedExpiry != null,
                'required_stored' => $storedRequired === true,
                'stored_otp' => $storedOtp,
                'stored_expiry' => $storedExpiry ? $storedExpiry->format('Y-m-d H:i:s') : null,
                'stored_required' => $storedRequired,
                'session_id_after_store' => session()->getId()
            ]);

            // Send OTP email
            $emailSent = $this->sendOtpEmail($user, $otp, 'wallet');

            if (!$emailSent) {
                Log::error('Failed to send OTP email');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification code. Please try again.'
                ], 500);
            }

            Log::info('OTP email sent successfully', [
                'user_email' => $user->email,
                'otp_expires_at' => $expiry->format('Y-m-d H:i:s')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email (' . substr($user->email, 0, 3) . '***). Code expires in 10 minutes.',
                'expires_at' => $expiry->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('Wallet withdraw OTP error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending verification code. Please try again.'
            ], 500);
        }
    }

    /**
     * Send OTP email to user
     */
    private function sendOtpEmail($user, $otp, $type = 'wallet')
    {
        try {
            $subject = $type === 'wallet' ? 'Wallet Withdrawal OTP' : 'Withdrawal OTP';
            $emailContent = "Your withdrawal verification code is: {$otp}. This code will expire in 10 minutes.";
            
            // Send email using Laravel Mail facade
            Mail::raw($emailContent, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->username)
                        ->subject($subject);
            });
            return true;
            
        } catch (\Exception $e) {
            Log::error('OTP email error: ' . $e->getMessage());
            return false;
        }
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
