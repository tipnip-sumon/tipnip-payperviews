<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Invest;
use App\Models\Deposit;
use App\Models\Message;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Models\VideoView;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use Yajra\DataTables\DataTables;
use App\Models\ReferralCommission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller   
{
    public function home() 
    {
        // Start performance tracking
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Enable query logging for this request
        DB::enableQueryLog();
        
        // Enhanced authentication check with proper redirect (fixed session clearing)
        if (!Auth::check()) {
            // Log the unauthorized access attempt
            Log::info('Unauthenticated dashboard access attempt', [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'from_url' => request()->fullUrl(),
                'session_id' => session()->getId()
            ]);
            
            // Don't clear session aggressively - let Laravel handle it
            // Just redirect to login which will handle session properly
            return redirect()->route('login', ['from' => 'dashboard', 't' => time()])
                ->with('info', 'Please log in to access your dashboard.')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
        }
        
        $user = Auth::user();
        if (!$user) {
            // Double-check: if Auth::check() passed but user is null
            Log::warning('Auth check passed but user is null', [
                'session_data' => session()->all(),
                'auth_id' => Auth::id()
            ]);
            
            // Only logout, don't flush session as it might be in the process of being established
            Auth::logout();
            
            return redirect()->route('login')->with(['error' => 'Session error. Please log in again.']);
        }

        // Cached concurrent users count (refreshed every 30 seconds)
        $concurrentUsers = Cache::remember('concurrent_users_count', 30, function () {
            return DB::table('users')
                ->where('last_seen', '>=', Carbon::now()->subMinutes(5))
                ->count();
        });
        
        // Detect high traffic mode
        $isHighTraffic = $concurrentUsers > config('scaling.traffic_thresholds.high', 1000);
        
        // Update current user's last seen (with rate limiting) - moved after auth check
        $lastSeenKey = 'last_seen_update_' . $user->id;
        if (!Cache::has($lastSeenKey) || !$isHighTraffic) {
            DB::table('users')->where('id', $user->id)->update(['last_seen' => Carbon::now()]);
            if ($isHighTraffic) {
                Cache::put($lastSeenKey, true, 60); // Update max once per minute in high traffic
            }
        }

        // Get optimized dashboard data using helper functions
        try {
            $dashboardData = getDashboardData($user->id);
        } catch (\Exception $e) {
            // Fallback to optimized basic data if helper fails
            Log::error('Dashboard helper failed: ' . $e->getMessage());
            return $this->getOptimizedFallbackData($user);
        }
        
        // Prepare data for view with improved organization
        $data = [
            'pageTitle' => 'Dashboard',
            'user' => $user,
            
            // Main Balance Data (organized for smooth display)
            'currentBalance' => $dashboardData['current_balance'],
            'deposit_wallet' => $dashboardData['deposit_wallet'],
            'interest_wallet' => $dashboardData['interest_wallet'],
            
            // Team & Referral Data
            'referral_earnings' => $dashboardData['referrals']['referral_earnings'],
            'total_referrals' => $dashboardData['referrals']['total_referrals'],
            'active_referrals' => $dashboardData['referrals']['active_referrals'],
            'monthly_referral_earnings' => $dashboardData['referrals']['monthly_referral_earnings'],
            
            // Investment Data (categorized)
            'totalInvest' => $dashboardData['investments']['total_invested'],
            'runningInvests' => $dashboardData['investments']['running_investments'],
            'completedInvests' => $dashboardData['investments']['completed_investments'],
            'interests' => $dashboardData['investments']['total_interest_earned'],
            'monthly_interest' => $dashboardData['investments']['monthly_interest'],
            
            // Transaction Data (organized)
            'transactions' => $dashboardData['transactions']['recent_transactions'],
            'totalDeposit' => $dashboardData['transactions']['total_deposits'],
            'totalWithdraw' => $dashboardData['transactions']['total_withdrawals'],
            'lastDeposit' => $dashboardData['transactions']['last_deposit'],
            'lastWithdraw' => $dashboardData['transactions']['last_withdrawal'],
            'balance_transfer' => $dashboardData['transactions']['balance_transferred'],
            'balance_received' => $dashboardData['transactions']['balance_received'],
            
            // Video System Data
            'videoEarnings' => $dashboardData['video_system']['video_earnings'],
            'monthly_video_earnings' => $dashboardData['video_system']['monthly_video_earnings'],
            'activePlan' => $dashboardData['video_system']['active_plan'],
            'dailyLimit' => $dashboardData['video_system']['daily_limit'],
            'todayViews' => $dashboardData['video_system']['today_views'],
            'todayEarnings' => $dashboardData['video_system']['today_earnings'] ?? 0,
            'remainingViews' => $dashboardData['video_system']['remaining_views'],
            'totalVideosWatched' => $dashboardData['video_system']['total_videos_watched'],
            'videoRate' => $dashboardData['video_system']['video_rate'],
            'pendingVideoEarnings' => $dashboardData['video_system']['pending_video_earnings'],
            
            // Performance & Statistics
            'growth_percentage' => $dashboardData['statistics']['growth_percentage'],
            'monthly_profit' => $dashboardData['statistics']['monthly_profit'],
            'total_profit' => $dashboardData['statistics']['total_profit'],
            'roi_percentage' => $dashboardData['statistics']['roi_percentage'],
            
            // Chart Data for Performance
            'chartData' => $dashboardData['performance']['daily_earnings'],
            'monthly_comparison' => $dashboardData['performance']['monthly_comparison'],
            'category_breakdown' => $dashboardData['performance']['category_breakdown'],
            
            // Recent Activities
            'recent_activities' => $dashboardData['recent_activities'],
            
            // Quick Stats for Real-time Updates
            'quick_stats' => getDashboardQuickStats($user->id),
        ];

        // Legacy data optimization - Use single queries with calculated fields
        $legacyDepositsData = Deposit::where('user_id', $user->id)
            ->selectRaw('
                SUM(CASE WHEN status = 1 THEN amount ELSE 0 END) as successful_deposits,
                SUM(CASE WHEN status != 0 THEN amount ELSE 0 END) as submitted_deposits,
                SUM(amount) as requested_deposits,
                SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) as initiated_deposits,
                SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as pending_deposits,
                SUM(CASE WHEN status = 3 THEN amount ELSE 0 END) as rejected_deposits
            ')
            ->first();

        $legacyWithdrawalsData = Withdrawal::where('user_id', $user->id)
            ->selectRaw('
                SUM(CASE WHEN status != 0 THEN amount ELSE 0 END) as submitted_withdrawals,
                SUM(CASE WHEN status = 1 THEN amount ELSE 0 END) as successful_withdrawals,
                SUM(CASE WHEN status = 3 THEN amount ELSE 0 END) as rejected_withdrawals,
                SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) as initiated_withdrawals,
                SUM(amount) as requested_withdrawals,
                SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) as pending_withdrawals
            ')
            ->first();

        $legacyInvestData = Invest::where('user_id', $user->id)
            ->selectRaw('
                SUM(CASE WHEN wallet_type = "deposit_wallet" AND status = 1 THEN amount ELSE 0 END) as deposit_wallet_invests,
                SUM(CASE WHEN wallet_type = "interest_wallet" AND status = 1 THEN amount ELSE 0 END) as interest_wallet_invests
            ')
            ->first();

        // Assign legacy data efficiently
        $data['successfulDeposits'] = $legacyDepositsData->successful_deposits ?? 0;
        $data['submittedDeposits'] = $legacyDepositsData->submitted_deposits ?? 0;
        $data['requestedDeposits'] = $legacyDepositsData->requested_deposits ?? 0;
        $data['initiatedDeposits'] = $legacyDepositsData->initiated_deposits ?? 0;
        $data['pendingDeposits'] = $legacyDepositsData->pending_deposits ?? 0;
        $data['rejectedDeposits'] = $legacyDepositsData->rejected_deposits ?? 0;

        $data['submittedWithdrawals'] = $legacyWithdrawalsData->submitted_withdrawals ?? 0;
        $data['successfulWithdrawals'] = $legacyWithdrawalsData->successful_withdrawals ?? 0;
        $data['rejectedWithdrawals'] = $legacyWithdrawalsData->rejected_withdrawals ?? 0;
        $data['initiatedWithdrawals'] = $legacyWithdrawalsData->initiated_withdrawals ?? 0;
        $data['requestedWithdrawals'] = $legacyWithdrawalsData->requested_withdrawals ?? 0;
        $data['pendingWithdrawals'] = $legacyWithdrawalsData->pending_withdrawals ?? 0;

        $data['invests'] = $data['totalInvest'];
        $data['depositWalletInvests'] = $legacyInvestData->deposit_wallet_invests ?? 0;
        $data['interestWalletInvests'] = $legacyInvestData->interest_wallet_invests ?? 0;
        
        // Settings
        $data['settings'] = getSettings();
        
        // Ensure settings is not null - add fallback
        if (!$data['settings']) {
            $data['settings'] = (object) [
                'logo' => null,
                'admin_logo' => null,
                'favicon' => null,
                'site_name' => 'ViewCash',
            ];
        }

        return view('user.dashboard', $data);
    }

    /**
     * Optimized fallback dashboard data if helper functions fail
     */
    private function getOptimizedFallbackData($user)
    {
        // Single comprehensive query for all user stats
        $userStats = DB::table('users')
            ->leftJoin('invests', 'users.id', '=', 'invests.user_id')
            ->leftJoin('transactions as t_ref', function($join) {
                $join->on('users.id', '=', 't_ref.user_id')
                     ->where('t_ref.remark', '=', 'referral_commission');
            })
            ->leftJoin('transactions as t_interest', function($join) {
                $join->on('users.id', '=', 't_interest.user_id')
                     ->where('t_interest.remark', '=', 'interest');
            })
            ->leftJoin('users as referrals', 'users.id', '=', 'referrals.ref_by')
            ->where('users.id', $user->id)
            ->selectRaw('
                users.deposit_wallet,
                users.interest_wallet,
                (users.deposit_wallet + users.interest_wallet) as current_balance,
                COALESCE(SUM(DISTINCT invests.amount), 0) as total_invest,
                COALESCE(SUM(CASE WHEN invests.status = 1 THEN invests.amount ELSE 0 END), 0) as running_invests,
                COALESCE(SUM(CASE WHEN invests.status = 0 THEN invests.amount ELSE 0 END), 0) as completed_invests,
                COALESCE(SUM(DISTINCT t_ref.amount), 0) as referral_earnings,
                COALESCE(SUM(DISTINCT t_interest.amount), 0) as interests,
                COUNT(DISTINCT referrals.id) as total_referrals,
                COUNT(CASE WHEN referrals.status = 1 THEN 1 END) as active_referrals
            ')
            ->first();

        // Monthly earnings in single query
        $monthlyStats = DB::table('transactions')
            ->where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->selectRaw('
                SUM(CASE WHEN remark = "referral_commission" THEN amount ELSE 0 END) as monthly_referral_earnings,
                SUM(CASE WHEN remark = "interest" THEN amount ELSE 0 END) as monthly_interest
            ')
            ->first();

        $data = [
            'pageTitle' => 'Dashboard',
            'user' => $user,
            'currentBalance' => $userStats->current_balance ?? 0,
            'deposit_wallet' => $userStats->deposit_wallet ?? 0,
            'interest_wallet' => $userStats->interest_wallet ?? 0,
            'referral_earnings' => $userStats->referral_earnings ?? 0,
            'total_referrals' => $userStats->total_referrals ?? 0,
            'active_referrals' => $userStats->active_referrals ?? 0,
            'monthly_referral_earnings' => $monthlyStats->monthly_referral_earnings ?? 0,
            'totalInvest' => $userStats->total_invest ?? 0,
            'runningInvests' => $userStats->running_invests ?? 0,
            'completedInvests' => $userStats->completed_invests ?? 0,
            'interests' => $userStats->interests ?? 0,
            'monthly_interest' => $monthlyStats->monthly_interest ?? 0,
            'settings' => getSettings() ?: (object) [
                'logo' => null,
                'admin_logo' => null,
                'favicon' => null,
                'site_name' => 'ViewCash',
            ],
        ];

        return view('user.dashboard', $data);
    }
 
    public function index()
    {
        return view('frontend.home');
    }
    public function transfer_funds()
    {
        $pageTitle = "Transfer Funds";
        return view('frontend.transfer_fund', compact('pageTitle'));
    }
    public function transferBalanceSubmit(Request $request)
    {
        $user = Auth::user();
        
        // Check transfer conditions first
        if (!function_exists('checkTransferConditions')) {
            require_once app_path('helpers/ConditionHelper.php');
        }
        
        $conditionCheck = checkTransferConditions($user);
        
        if (!$conditionCheck['allowed']) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer requirements not met: ' . implode(', ', $conditionCheck['failures']),
                    'requirements' => $conditionCheck['requirements']
                ], 422);
            }
            
            return back()->with('error', 'Transfer requirements not met: ' . implode(', ', $conditionCheck['failures']));
        }
        if (!$user) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You need to login first'], 401);
            }
            return back()->with(['error' => 'You need to login first']);
        }
        if ($user->status != 1) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Your account is not active'], 403);
            }
            return back()->with(['error' => 'Your account is not active']);
        }

        try {
            $request->validate([
                'username' => 'required',
                'amount'   => 'required|numeric|gt:0',
                'wallet'   => 'required|in:deposit_wallet,interest_wallet',
                'note'    => 'nullable|string|max:255',
                'password' => 'required'
            ],
            [
                'username.required' => 'Member ID is required',
                'amount.required'   => 'Amount is required',
                'wallet.required'   => 'Wallet type is required',
                'wallet.in'         => 'Invalid wallet type selected',
                'password.required' => 'Password is required'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
            }
            throw $e;
        }

        $password = $request->password;
        if (!Auth::attempt(['username' => $user->username, 'password' => $password])) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Invalid password'], 422);
            }
            return back()->with(['error' => 'Invalid password']);
        }
        if ($request->amount < 1) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Minimum transfer amount is $1.00'], 422);
            }
            return back()->with(['error' => 'Minimum transfer amount is 1']);
        }
        if ($request->amount > 100000) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Maximum transfer amount is $100,000'], 422);
            }
            return back()->with(['error' => 'Maximum transfer amount is 100000']);
        }

        if ($user->username == $request->username) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot transfer to your own account'], 422);
            }
            return back()->with(['error' => 'You cannot transfer to your own account']);
        }

        $receiver = User::where('username', $request->username)->first();
        if (!$receiver) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Recipient not found'], 422);
            }
            return back()->with(['error' => 'Oops! Receiver not found']);
        }

        $charge      = $request->amount * 5 / 100;
        $afterCharge = $request->amount + $charge;
        $wallet      = $request->wallet;

        if ($user->$wallet < $afterCharge) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Insufficient balance in your ' . str_replace('_', ' ', $wallet)], 422);
            }
            return back()->with(['error'=>'You have no sufficient balance to this wallet']);
        }

        try {
            // Start transaction
            DB::beginTransaction();

            $user = User::find($user->id); // Ensure $user is a model instance
            $user->$wallet -= $afterCharge;
            $user->save();


            $trx1                      = getTrx();
            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = getAmount($afterCharge);
            $transaction->charge       = $charge;
            $transaction->trx_type     = '-';
            $transaction->trx          = $trx1;
            $transaction->wallet_type  = $wallet;
            $transaction->remark       = 'balance_transfer';
            $transaction->note         = $request->note;
            $transaction->details      = 'Balance transfer to ' . $receiver->username;
            $transaction->post_balance = getAmount($user->$wallet);
            $transaction->save();

            $receiver->deposit_wallet += $request->amount;
            $receiver->save();

            $trx2                      = getTrx();
            $transaction               = new Transaction();
            $transaction->user_id      = $receiver->id;
            $transaction->amount       = getAmount($request->amount);
            $transaction->charge       = 0;
            $transaction->trx_type     = '+';
            $transaction->trx          = $trx2;
            $transaction->wallet_type  = 'deposit_wallet';
            $transaction->remark       = 'balance_received';
            $transaction->note         = $request->note;
            $transaction->details      = 'Balance received from ' . $user->username;
            $transaction->post_balance = getAmount($receiver->deposit_wallet);
            $transaction->save();

            // Send notifications to both users
            $this->sendTransferNotifications($user, $receiver, $request->amount, $charge, $wallet, $trx1, $trx2, $request->note);

            DB::commit();

            $successMessage = 'Transfer successful! $' . number_format($request->amount, 2) . ' sent to ' . $receiver->username;

            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => $successMessage,
                    'data' => [
                        'amount' => $request->amount,
                        'fee' => $charge,
                        'total' => $afterCharge,
                        'recipient' => $receiver->username,
                        'transaction_id' => $trx1
                    ]
                ]);
            }
            return back()->with(['success' => 'Balance transferred successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Transfer failed: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Transfer failed due to system error. Please try again.'], 500);
            }
            return back()->with(['error' => 'Transfer failed. Please try again.']);
        }
    }
    
    public function findUser(Request $request)
    {
        $user = User::where('username', $request->username)->first();
        $message = null;
        
        if (!$user) {
            $message = 'User not found with username: ' . $request->username;
        } elseif ($user->username == Auth::user()->username) {
            $message = 'You cannot transfer to your own account';
        } elseif ($user->status != 1) {
            $message = 'This user account is not active';
        }
        
        if ($message) {
            return response()->json(['message' => $message]);
        }
        
        // Return user details for successful search
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->fullname ?? $user->username,
                'username' => $user->username,
                'email' => $user->email
            ]
        ]);
    }
    
    /**
     * Send transfer notifications to both sender and receiver
     */
    private function sendTransferNotifications($sender, $receiver, $amount, $charge, $wallet, $senderTrx, $receiverTrx, $note = null)
    {
        try {
            // Create in-app notifications
            $this->createInAppNotifications($sender, $receiver, $amount, $charge, $wallet, $senderTrx, $receiverTrx, $note);
            
            // Send email notifications
            $this->sendEmailNotifications($sender, $receiver, $amount, $charge, $wallet, $senderTrx, $receiverTrx, $note);
            
        } catch (\Exception $e) {
            Log::error('Failed to send transfer notifications: ' . $e->getMessage());
        }
    }
    
    /**
     * Create in-app notifications for transfer
     */
    private function createInAppNotifications($sender, $receiver, $amount, $charge, $wallet, $senderTrx, $receiverTrx, $note = null)
    {
        $walletName = str_replace('_', ' ', ucfirst($wallet));
        $totalDeducted = $amount + $charge;
        
        // Notification for sender
        $senderMessage = "Transfer Sent: You have successfully sent $" . number_format($amount, 2) . 
                        " to " . $receiver->username . ". Fee: $" . number_format($charge, 2) . 
                        ". Total deducted: $" . number_format($totalDeducted, 2) . 
                        " from your " . $walletName . ". Transaction ID: " . $senderTrx;
        
        if ($note) {
            $senderMessage .= ". Note: " . $note;
        }
        
        // Notification for receiver
        $receiverMessage = "Money Received: You have received $" . number_format($amount, 2) . 
                          " from " . $sender->username . " in your Deposit Wallet. Transaction ID: " . $receiverTrx;
        
        if ($note) {
            $receiverMessage .= ". Note: " . $note;
        }
        
        // Create notification records (if you have a notifications table)
        if (class_exists('\App\Models\UserNotification')) {
            UserNotification::create([
                'user_id' => $sender->id,
                'title' => 'Transfer Sent',
                'message' => $senderMessage,
                'type' => 'transfer_sent',
                'data' => [
                    'amount' => $amount,
                    'charge' => $charge,
                    'recipient' => $receiver->username,
                    'transaction_id' => $senderTrx,
                    'wallet' => $wallet
                ]
            ]);
            
            UserNotification::create([
                'user_id' => $receiver->id,
                'title' => 'Money Received',
                'message' => $receiverMessage,
                'type' => 'money_received',
                'data' => [
                    'amount' => $amount,
                    'sender' => $sender->username,
                    'transaction_id' => $receiverTrx,
                    'note' => $note
                ]
            ]);
        }
    }
    
    /**
     * Send email notifications for transfer
     */
    private function sendEmailNotifications($sender, $receiver, $amount, $charge, $wallet, $senderTrx, $receiverTrx, $note = null)
    {
        // Check if mail is properly configured
        if (!env('MAIL_FROM_ADDRESS') || !env('MAIL_FROM_NAME')) {
            Log::warning('Mail configuration incomplete, skipping email notifications');
            return;
        }
        
        $walletName = str_replace('_', ' ', ucfirst($wallet));
        $totalDeducted = $amount + $charge;
        $currentDateTime = now()->format('F j, Y \a\t g:i A');
        
        // Email to sender
        if ($sender->email && filter_var($sender->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::send('emails.transfer-sent', [
                    'sender_name' => $sender->firstname . ' ' . $sender->lastname,
                    'receiver_name' => $receiver->firstname . ' ' . $receiver->lastname,
                    'receiver_username' => $receiver->username,
                    'amount' => number_format($amount, 2),
                    'charge' => number_format($charge, 2),
                    'total_deducted' => number_format($totalDeducted, 2),
                    'wallet_name' => $walletName,
                    'remaining_balance' => number_format($sender->$wallet, 2),
                    'transaction_id' => $senderTrx,
                    'note' => $note,
                    'transfer_date' => $currentDateTime
                ], function ($mail) use ($sender) {
                    $mail->to($sender->email, $sender->firstname . ' ' . $sender->lastname)
                         ->subject('Transfer Confirmation - Money Sent Successfully');
                });
            } catch (\Exception $e) {
                Log::error('Failed to send transfer email to sender: ' . $e->getMessage());
            }
        } else {
            Log::warning('Sender email is null or invalid, skipping email notification for user: ' . $sender->username);
        }
        
        // Email to receiver
        if ($receiver->email && filter_var($receiver->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::send('emails.money-received', [
                    'receiver_name' => $receiver->firstname . ' ' . $receiver->lastname,
                    'sender_name' => $sender->firstname . ' ' . $sender->lastname,
                    'sender_username' => $sender->username,
                    'amount' => number_format($amount, 2),
                    'wallet_name' => 'Deposit Wallet',
                    'new_balance' => number_format($receiver->deposit_wallet, 2),
                    'transaction_id' => $receiverTrx,
                    'note' => $note,
                    'transfer_date' => $currentDateTime
                ], function ($mail) use ($receiver) {
                    $mail->to($receiver->email, $receiver->firstname . ' ' . $receiver->lastname)
                         ->subject('Money Received - Transfer Notification');
                });
            } catch (\Exception $e) {
                Log::error('Failed to send transfer email to receiver: ' . $e->getMessage());
            }
        } else {
            Log::warning('Receiver email is null or invalid, skipping email notification for user: ' . $receiver->username);
        }
    }
    
    public function transferHistory(Request $request)
    {
        $pageTitle = "Transaction History";
        
        if($request->ajax()){
            $user = Auth::id();
            
            // Build the query with filters
            $query = Transaction::where('user_id', $user)
                ->whereIn('remark', ['balance_transfer', 'balance_received']);
            
            // Apply type filter
            if ($request->type_filter) {
                $query->where('trx_type', $request->type_filter);
            }
            
            // Apply date range filter
            if ($request->from_date) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            
            if ($request->to_date) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
            
            // Apply search filter
            if ($request->search_input) {
                $search = $request->search_input;
                $query->where(function($q) use ($search) {
                    $q->where('trx', 'like', "%{$search}%")
                      ->orWhere('details', 'like', "%{$search}%")
                      ->orWhere('note', 'like', "%{$search}%");
                });
            }
            
            $data = $query->orderBy('id', 'desc')->get();
            
            return Datatables::of($data)
                ->addColumn('created_at', function ($row) {
                    return '<div><p class="mb-0">' . $row->created_at->format('M d, Y') . '</p><small class="text-muted">' . $row->created_at->format('h:i A') . '</small></div>';
                })
                ->addColumn('trx_type', function ($row) {
                    return $row->trx_type == '+' ? '(+) Credit' : '(-) Debit';
                })
                ->addColumn('remark', function ($row) {
                    return $row->details ?: 'Transfer Transaction';
                })
                ->addColumn('trx', function ($row) {
                    return $row->trx;
                })
                ->addColumn('amount', function ($row) {
                    return "$". number_format($row->amount - $row->charge, 2);
                })
                ->addColumn('charge', function ($row) {
                    return "$". number_format($row->charge, 2);
                })
                ->addColumn('post_balance', function ($row) {
                    return "$". number_format($row->post_balance, 2);
                })
                ->addColumn('note', function ($row) {
                    return $row->note ? $row->note : 'N/A';
                })
                ->rawColumns(['created_at'])
                ->addIndexColumn()
                ->make(true);
        }
        
        return view('frontend.transection-history', compact('pageTitle'));
    }
    
    public function getSessionNotifications()
    {
        $user = Auth::user();
        
        $notifications = \Illuminate\Support\Facades\DB::table('user_session_notifications')
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }
    
    public function markNotificationsAsRead()
    {
        $user = Auth::user();
        
        try {
            DB::beginTransaction();
            
            // Mark notifications as read and add a prevention mechanism
            $updated = DB::table('user_session_notifications')
                ->where('user_id', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                    'updated_at' => now()
                ]);
            
            // Store information to prevent re-notifications for the same login sessions
            // This creates a record of acknowledged login IPs to avoid duplicate alerts
            $recentNotifications = DB::table('user_session_notifications')
                ->where('user_id', $user->id)
                ->where('is_read', true)
                ->where('read_at', '>=', now()->subHours(24)) // Last 24 hours
                ->whereNotNull('new_login_ip')
                ->distinct()
                ->pluck('new_login_ip');
            
            if ($recentNotifications->isNotEmpty()) {
                // Store acknowledged IPs in cache to prevent re-notifications
                $cacheKey = "acknowledged_login_ips_user_{$user->id}";
                $existingIps = cache()->get($cacheKey, []);
                $updatedIps = array_unique(array_merge($existingIps, $recentNotifications->toArray()));
                
                // Cache for 7 days to prevent re-notifications for same IPs
                cache()->put($cacheKey, $updatedIps, now()->addDays(7));
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Notifications marked as read and future duplicates prevented',
                'updated_count' => $updated
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to mark notifications as read', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function checkSessionNotifications()
    {
        $user = Auth::user();
        
        try {
            $unreadCount = \Illuminate\Support\Facades\DB::table('user_session_notifications')
                ->where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
            
            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'has_notifications' => $unreadCount > 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Unable to check notifications',
                'unread_count' => 0,
                'has_notifications' => false
            ], 500);
        }
    }
    
    public function cleanupUserTab(Request $request) 
    {
        $user = Auth::user();
        $tabId = $request->input('tab_id') ?: session('tab_id');
        
        if ($tabId) {
            $activeTabKey = "user_active_tab_{$user->id}";
            $activeTabId = cache()->get($activeTabKey);
            
            // Only cleanup if this tab is the active one
            if ($activeTabId === $tabId) {
                cache()->forget($activeTabKey);
                session()->forget('tab_id');
                
                Log::info('User tab cleaned up', [
                    'user_id' => $user->id,
                    'tab_id' => $tabId,
                    'ip' => $request->ip()
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Tab cleanup completed'
        ]);
    }
    
    public function referralIndex()
    {
        $pageTitle = "Referral Dashboard";
        $user = Auth::user();
        
        // Generate referral link (uses username for registration)
        $referralLink = url('/register?ref=' . $user->username);
        
        // Get referral statistics (uses user ID for database queries)
        $totalReferrals = User::where('ref_by', $user->id)->count();
        $activeReferrals = User::where('ref_by', $user->id)->where('status', 1)->count();
        $referralEarnings = Transaction::where('remark', 'referral_commission')
            ->where('user_id', $user->id)
            ->sum('amount');
        
        // Get recent referrals (uses user ID for database queries)
        $recentReferrals = User::where('ref_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('user.referral.index', compact(
            'pageTitle', 
            'user', 
            'referralLink', 
            'totalReferrals', 
            'activeReferrals', 
            'referralEarnings',
            'recentReferrals'
        ));
    }
    
    public function referralEarnings()
    {
        $pageTitle = "Referral Earnings";
        $user = Auth::user();
        $referralEarnings = Transaction::where('remark', 'referral_commission')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();
        if (request()->ajax()) {
            return DataTables::of($referralEarnings)
                ->addColumn('created_at', function ($row) {
                    return showDateTime($row->created_at);
                })
                ->addColumn('trx_type', function ($row) {
                    return $row->trx_type == '+' ? '(+) Credit' : '(-) Debit';
                })
                ->addColumn('amount', function ($row) {
                    return "$". $row->amount;
                })
                ->addColumn('remark', function ($row) {
                    return $row->details;
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('frontend.refferral-history', compact('pageTitle'));
    }
    /**
     * Display sponsor list with enhanced DataTables functionality
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function sponsorList(Request $request)
    {
        $pageTitle = "Sponsor List";
        $user = Auth::user();
        
        // Validate user authentication
        if (!$user) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Unauthorized access'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to access this page');
        }
        
        // Handle AJAX request for DataTables
        if ($request->ajax()) {
            try {
                Log::info('Sponsor List AJAX Request', [
                    'user_id' => $user->id,
                    'user_username' => $user->username,
                    'request_data' => $request->all(),
                    'status_filter' => $request->status_filter,
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'search_value' => $request->search['value'] ?? null
                ]);

                // Get sponsors (users referred by current user) with better query
                $query = User::where('ref_by', $user->id)
                    ->select([
                        'id', 'username', 'firstname', 'lastname', 'email', 
                        'mobile', 'country', 'status', 'created_at', 
                        'deposit_wallet', 'interest_wallet'
                    ]);
                
                Log::info('Base query built for user', ['user_id' => $user->id]);
                
                // Apply search filters if provided
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $query->where(function($q) use ($searchValue) {
                        $q->where('username', 'LIKE', "%{$searchValue}%")
                          ->orWhere('firstname', 'LIKE', "%{$searchValue}%")
                          ->orWhere('lastname', 'LIKE', "%{$searchValue}%")
                          ->orWhere('email', 'LIKE', "%{$searchValue}%")
                          ->orWhere('mobile', 'LIKE', "%{$searchValue}%")
                          ->orWhere('country', 'LIKE', "%{$searchValue}%");
                    });
                }
                
                // Apply status filter if provided (handle both string and numeric values)
                if ($request->has('status_filter') && $request->status_filter !== '' && $request->status_filter !== null && $request->status_filter !== 'null') {
                    Log::info('Applying status filter', ['status_filter' => $request->status_filter]);
                    $query->where('status', $request->status_filter);
                }
                
                // Apply date range filter if provided (ensure not null/empty)
                if ($request->has('date_from') && !empty($request->date_from) && $request->date_from !== 'null') {
                    Log::info('Applying date_from filter', ['date_from' => $request->date_from]);
                    $query->whereDate('created_at', '>=', $request->date_from);
                }
                
                if ($request->has('date_to') && !empty($request->date_to) && $request->date_to !== 'null') {
                    Log::info('Applying date_to filter', ['date_to' => $request->date_to]);
                    $query->whereDate('created_at', '<=', $request->date_to);
                }
                
                Log::info('Final query about to execute', [
                    'user_id' => $user->id,
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings()
                ]);
                
                $sponsors = $query->orderBy('created_at', 'desc')->get();
                
                Log::info('Sponsors found', [
                    'count' => $sponsors->count(),
                    'user_id' => $user->id,
                    'first_few' => $sponsors->take(3)->toArray()
                ]);
                
                return DataTables::of($sponsors)
                    ->addIndexColumn()
                    ->addColumn('created_at', function ($row) {
                        try {
                            return '<div class="text-nowrap">' . 
                                   '<div>' . $row->created_at->format('M d, Y') . '</div>' .
                                   '<small class="text-muted">' . $row->created_at->format('h:i A') . '</small>' .
                                   '</div>';
                        } catch (\Exception $e) {
                            return '<div class="text-nowrap">' . 
                                   '<div>Invalid Date</div>' .
                                   '<small class="text-muted">N/A</small>' .
                                   '</div>';
                        }
                    })
                    ->addColumn('username', function ($row) {
                        return '<span class="badge bg-info font-weight-bold">' . $row->username . '</span>';
                    })
                    ->addColumn('name', function ($row) {
                        $firstname = $row->firstname ?? '';
                        $lastname = $row->lastname ?? '';
                        $fullName = trim($firstname . ' ' . $lastname);
                        return '<div class="user-info">' . 
                               '<strong>' . ($fullName ?: 'N/A') . '</strong>' .
                               '</div>';
                    })
                    ->addColumn('email', function ($row) {
                        if (!empty($row->email) && filter_var($row->email, FILTER_VALIDATE_EMAIL)) {
                            return '<a href="mailto:' . $row->email . '" class="text-primary">' . 
                                   $row->email . '</a>';
                        }
                        return '<span class="text-muted">N/A</span>';
                    })
                    ->addColumn('phone', function ($row) {
                        if (!empty($row->mobile)) {
                            return '<span class="text-nowrap">' . $row->mobile . '</span>';
                        }
                        return '<span class="text-muted">N/A</span>';
                    })
                    ->addColumn('status', function ($row) {
                        $statusMap = [
                            1 => '<span class="badge bg-success">Active</span>',
                            0 => '<span class="badge bg-warning">Inactive</span>',
                            2 => '<span class="badge bg-danger">Banned</span>'
                        ];
                        return $statusMap[$row->status] ?? '<span class="badge bg-secondary">Unknown</span>';
                    })
                    ->addColumn('country', function ($row) {
                        return !empty($row->country) ? 
                               '<span class="text-capitalize">' . $row->country . '</span>' : 
                               '<span class="text-muted">N/A</span>';
                    })
                    ->addColumn('actions', function ($row) use ($user) {
                        try {
                            $actions = '<div class="btn-group" role="group">';
                            
                            // View details button
                            $actions .= '<button type="button" class="btn btn-sm btn-info view-sponsor" ' .
                                       'data-id="' . $row->id . '" ' .
                                       'data-username="' . ($row->username ?? '') . '" ' .
                                       'data-name="' . trim(($row->firstname ?? '') . ' ' . ($row->lastname ?? '')) . '" ' .
                                       'data-email="' . ($row->email ?? '') . '" ' .
                                       'data-mobile="' . ($row->mobile ?? '') . '" ' .
                                       'data-country="' . ($row->country ?? '') . '" ' .
                                       'data-status="' . $row->status . '" ' .
                                       'data-balance="' . number_format(($row->deposit_wallet ?? 0) + ($row->interest_wallet ?? 0), 2) . '" ' .
                                       'data-joined="' . $row->created_at->format('M d, Y') . '" ' .
                                       'title="View Details">' .
                                       '<i class="fas fa-eye"></i>' .
                                       '</button>';
                            
                            // Contact button
                            if (!empty($row->email) && filter_var($row->email, FILTER_VALIDATE_EMAIL)) {
                                $actions .= '<button type="button" class="btn btn-sm btn-primary contact-sponsor" ' .
                                           'data-id="' . $row->id . '" ' .
                                           'data-email="' . $row->email . '" ' .
                                           'data-name="' . trim(($row->firstname ?? '') . ' ' . ($row->lastname ?? '')) . '" ' .
                                           'title="Contact Sponsor">' .
                                           '<i class="fas fa-envelope"></i>' .
                                           '</button>';
                            }
                            
                            // Performance button (if sponsor has referrals) - using ID now
                            try {
                                $referralCount = User::where('ref_by', $row->id)->count();
                                if ($referralCount > 0) {
                                    $actions .= '<button type="button" class="btn btn-sm btn-success view-performance" ' .
                                               'data-id="' . $row->id . '" ' .
                                               'data-name="' . trim(($row->firstname ?? '') . ' ' . ($row->lastname ?? '')) . '" ' .
                                               'data-referrals="' . $referralCount . '" ' .
                                               'title="View Performance">' .
                                               '<i class="fas fa-chart-line"></i>' .
                                               '</button>';
                                }
                            } catch (\Exception $e) {
                                // Skip performance button if query fails
                                Log::warning('Failed to get referral count for user ' . $row->id . ': ' . $e->getMessage());
                            }
                            
                            $actions .= '</div>';
                            return $actions;
                        } catch (\Exception $e) {
                            Log::error('Error generating actions for user ' . $row->id . ': ' . $e->getMessage());
                            return '<div class="btn-group"><span class="text-muted">Error</span></div>';
                        }
                    })
                    ->rawColumns(['created_at', 'username', 'name', 'email', 'phone', 'status', 'country', 'actions'])
                    ->make(true);
                    
            } catch (\Exception $e) {
                Log::error('Sponsor List DataTables Error: ' . $e->getMessage());
                Log::error('Sponsor List Error Details: ' . $e->getTraceAsString());
                return response()->json([
                    'error' => 'Failed to load sponsor data',
                    'message' => 'An error occurred while fetching sponsor information: ' . $e->getMessage(),
                    'debug' => env('APP_DEBUG') ? $e->getTraceAsString() : null
                ], 500);
            }
        }
        
        // Calculate sponsor statistics for the page
        $sponsorStats = $this->calculateSponsorStats($user);
        
        return view('frontend.sponsor-list', compact('pageTitle', 'sponsorStats'));
    }
    
    /**
     * Calculate sponsor statistics
     * 
     * @param User $user
     * @return array
     */
    private function calculateSponsorStats($user)
    {
        try {
            $totalSponsors = User::where('ref_by', $user->id)->count();
            $activeSponsors = User::where('ref_by', $user->id)->where('status', 1)->count();
            $inactiveSponsors = User::where('ref_by', $user->id)->where('status', 0)->count();
            $bannedSponsors = User::where('ref_by', $user->id)->where('status', 2)->count();
            
            // Get total business from sponsors
            $sponsorsBusiness = User::where('ref_by', $user->id)
                ->selectRaw('SUM(deposit_wallet + interest_wallet) as total_business')
                ->first();
            
            // Get recent joins (last 30 days)
            $recentJoins = User::where('ref_by', $user->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
            
            // Get commission earned from sponsors
            $totalCommission = Transaction::where('user_id', $user->id)
                ->where('remark', 'referral_commission')
                ->sum('amount');
            
            return [
                'total_sponsors' => $totalSponsors,
                'active_sponsors' => $activeSponsors,
                'inactive_sponsors' => $inactiveSponsors,
                'banned_sponsors' => $bannedSponsors,
                'total_business' => $sponsorsBusiness->total_business ?? 0,
                'recent_joins' => $recentJoins,
                'total_commission' => $totalCommission,
                'activity_rate' => $totalSponsors > 0 ? round(($activeSponsors / $totalSponsors) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating sponsor stats: ' . $e->getMessage());
            return [
                'total_sponsors' => 0,
                'active_sponsors' => 0,
                'inactive_sponsors' => 0,
                'banned_sponsors' => 0,
                'total_business' => 0,
                'recent_joins' => 0,
                'total_commission' => 0,
                'activity_rate' => 0
            ];
        }
    }
    
    /**
     * Get sponsor details for modal view
     * 
     * @param Request $request
     * @param int $sponsorId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSponsorDetails(Request $request, $sponsorId)
    {
        try {
            $user = Auth::user();
            
            // Find sponsor and verify ownership
            $sponsor = User::where('id', $sponsorId)
                ->where('ref_by', $user->id)
                ->first();
            
            if (!$sponsor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sponsor not found or access denied'
                ], 404);
            }
            
            // Get sponsor statistics
            $sponsorStats = [
                'total_deposits' => Deposit::where('user_id', $sponsor->id)->where('status', 1)->sum('amount'),
                'total_withdrawals' => Withdrawal::where('user_id', $sponsor->id)->where('status', 1)->sum('amount'),
                'total_investments' => Invest::where('user_id', $sponsor->id)->where('status', 1)->sum('amount'),
                'referral_count' => User::where('ref_by', $sponsor->id)->count(),
                'referral_earnings' => Transaction::where('user_id', $sponsor->id)
                    ->where('remark', 'referral_commission')->sum('amount'),
                'video_earnings' => Transaction::where('user_id', $sponsor->id)
                    ->where('remark', 'video_earning')->sum('amount'),
                'last_login' => 'Not available', // Column doesn't exist
                'account_age' => $sponsor->created_at->diffInDays(now()),
                'total_balance' => $sponsor->deposit_wallet + $sponsor->interest_wallet
            ];
            
            return response()->json([
                'success' => true,
                'sponsor' => [
                    'id' => $sponsor->id,
                    'username' => $sponsor->username,
                    'firstname' => $sponsor->firstname,
                    'lastname' => $sponsor->lastname,
                    'email' => $sponsor->email,
                    'mobile' => $sponsor->mobile,
                    'country' => $sponsor->country,
                    'status' => $sponsor->status,
                    'deposit_wallet' => number_format($sponsor->deposit_wallet, 2),
                    'interest_wallet' => number_format($sponsor->interest_wallet, 2),
                    'created_at' => $sponsor->created_at->format('M d, Y h:i A'),
                    'last_login' => 'Not available' // Column doesn't exist
                ],
                'statistics' => $sponsorStats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting sponsor details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sponsor details'
            ], 500);
        }
    }
    
    /**
     * Get sponsor performance data
     * 
     * @param Request $request
     * @param int $sponsorId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSponsorPerformance(Request $request, $sponsorId)
    {
        try {
            $user = Auth::user();
            
            // Find sponsor and verify ownership
            $sponsor = User::where('id', $sponsorId)
                ->where('ref_by', $user->id)
                ->first();
            
            if (!$sponsor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sponsor not found or access denied'
                ], 404);
            }
            
            // Get performance data for last 12 months
            $monthlyData = [];
            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();
                
                $deposits = Deposit::where('user_id', $sponsor->id)
                    ->where('status', 1)
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('amount');
                    
                $investments = Invest::where('user_id', $sponsor->id)
                    ->where('status', 1)
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('amount');
                    
                $referralEarnings = Transaction::where('user_id', $sponsor->id)
                    ->where('remark', 'referral_commission')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('amount');
                
                $monthlyData[] = [
                    'month' => $month->format('M Y'),
                    'deposits' => $deposits,
                    'investments' => $investments,
                    'referral_earnings' => $referralEarnings,
                    'total_activity' => $deposits + $investments + $referralEarnings
                ];
            }
            
            // Get referral tree data
            $referralTree = $this->getSponsorReferralTree($sponsor);
            
            return response()->json([
                'success' => true,
                'sponsor' => [
                    'username' => $sponsor->username,
                    'name' => $sponsor->firstname . ' ' . $sponsor->lastname
                ],
                'monthly_performance' => $monthlyData,
                'referral_tree' => $referralTree
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting sponsor performance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sponsor performance data'
            ], 500);
        }
    }
    
    /**
     * Get sponsor's referral tree
     * 
     * @param User $sponsor
     * @return array
     */
    private function getSponsorReferralTree($sponsor)
    {
        $directReferrals = User::where('ref_by', $sponsor->id)
            ->select('id', 'username', 'firstname', 'lastname', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $treeData = [];
        foreach ($directReferrals as $referral) {
            $treeData[] = [
                'username' => $referral->username,
                'name' => $referral->firstname . ' ' . $referral->lastname,
                'status' => $referral->status,
                'joined_at' => $referral->created_at->format('M d, Y'),
                'sub_referrals' => User::where('ref_by', $referral->id)->count()
            ];
        }
        
        return [
            'direct_referrals' => count($treeData),
            'total_network' => $this->countTotalNetwork($sponsor->id),
            'referrals' => $treeData
        ];
    }
    
    /**
     * Count total network size recursively
     * 
     * @param int $userId
     * @param int $depth
     * @return int
     */
    private function countTotalNetwork($userId, $depth = 0)
    {
        if ($depth > 10) { // Prevent infinite recursion
            return 0;
        }
        
        $directReferrals = User::where('ref_by', $userId)->pluck('id');
        $total = $directReferrals->count();
        
        foreach ($directReferrals as $referralId) {
            $total += $this->countTotalNetwork($referralId, $depth + 1);
        }
        
        return $total;
    }
    
    /**
     * Send message to sponsor
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function contactSponsor(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'sponsor_id' => 'required|integer',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:1000'
            ]);
            
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You need to be logged in to send messages'
                ], 401);
            }
            
            // Find the sponsor - Note: This should be someone who referred the current user
            // The current user is trying to contact their sponsor (who referred them)
            $sponsor = User::where('id', $request->sponsor_id)->first();
            
            if (!$sponsor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sponsor not found'
                ], 404);
            }
            
            // Verify that the sponsor actually referred the current user
            // OR that the current user has sponsored this person (both should be allowed)
            $isValidRelationship = ($user->ref_by == $sponsor->id) || 
                                   (User::where('id', $request->sponsor_id)->where('ref_by', $user->id)->exists());
            
            if (!$isValidRelationship) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only contact users in your referral network'
                ], 403);
            }
            
            // Log the message attempt
            Log::info('Contact sponsor message attempt', [
                'from_user_id' => $user->id,
                'to_user_id' => $sponsor->id,
                'subject' => $request->subject,
                'sender_username' => $user->username,
                'sponsor_username' => $sponsor->username
            ]);
            
            // Create message record using the correct table structure
            $messageCreated = false;
            
            // Try to create using Message model with correct fields
            try {
                $message = Message::create([
                    'from_user_id' => $user->id,
                    'to_user_id' => $sponsor->id,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'priority' => 'normal', // Use valid enum value
                    'message_type' => 'private', // Use correct field name and valid enum value
                    'status' => 'active', // Use valid enum value
                    'is_read' => false,
                    'is_starred' => false
                ]);
                
                if ($message) {
                    $messageCreated = true;
                    Log::info('Message saved to database via Message model', [
                        'message_id' => $message->id,
                        'from_user' => $user->username,
                        'to_user' => $sponsor->username
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to save via Message model: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                // Fallback: try direct database insert with correct fields
                try {
                    DB::table('messages')->insert([
                        'from_user_id' => $user->id,
                        'to_user_id' => $sponsor->id,
                        'subject' => $request->subject,
                        'message' => $request->message,
                        'priority' => 'normal',
                        'message_type' => 'private',
                        'status' => 'active',
                        'is_read' => false,
                        'is_starred' => false,
                        'read_at' => null,
                        'reply_to_id' => null,
                        'attachment_path' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $messageCreated = true;
                    
                    Log::info('Message saved to database via direct insert');
                } catch (\Exception $e2) {
                    Log::error('Failed to save via direct insert: ' . $e2->getMessage());
                    Log::error('Direct insert stack trace: ' . $e2->getTraceAsString());
                }
            }
            
            // Create transaction record for message activity
            try {
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = 0;
                $transaction->charge = 0;
                $transaction->trx_type = '+';
                $transaction->trx = getTrx();
                $transaction->wallet_type = 'system';
                $transaction->remark = 'message_sent';
                $transaction->details = 'Message sent to ' . $sponsor->username . ': ' . $request->subject;
                $transaction->post_balance = $user->deposit_wallet;
                $transaction->save();
            } catch (\Exception $e) {
                Log::warning('Failed to create transaction record: ' . $e->getMessage());
            }
            
            // Send email notification if sponsor has valid email
            if ($sponsor->email && filter_var($sponsor->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $emailData = [
                        'sponsor_name' => trim($sponsor->firstname . ' ' . $sponsor->lastname) ?: $sponsor->username,
                        'sender_name' => trim($user->firstname . ' ' . $user->lastname) ?: $user->username,
                        'sender_username' => $user->username,
                        'subject' => $request->subject,
                        'message_content' => $request->message,
                        'sent_date' => now()->format('F j, Y \a\t g:i A')
                    ];
                    
                    // Send actual email (simplified version)
                    Mail::send('emails.sponsor-message', $emailData, function ($mail) use ($sponsor, $request) {
                        $mail->to($sponsor->email)
                             ->subject('New Message: ' . $request->subject)
                             ->from(config('mail.from.address'), config('mail.from.name'));
                    });
                    
                    Log::info('Email notification sent to sponsor', [
                        'sponsor_email' => $sponsor->email,
                        'sponsor_id' => $sponsor->id,
                        'sender_id' => $user->id,
                        'subject' => $request->subject
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error('Failed to send email to sponsor: ' . $e->getMessage());
                    // Don't fail the entire operation if email fails
                }
            }
            
            // Create notification for the sponsor
            try {
                if (class_exists('\App\Models\UserNotification')) {
                    UserNotification::create([
                        'user_id' => $sponsor->id,
                        'title' => 'New Message from ' . $user->username,
                        'message' => 'Subject: ' . $request->subject . '. From: ' . $user->username,
                        'type' => 'message_received',
                        'data' => [
                            'from_user_id' => $user->id,
                            'from_username' => $user->username,
                            'subject' => $request->subject,
                            'message_preview' => substr($request->message, 0, 100)
                        ]
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to create notification: ' . $e->getMessage());
            }
            
            // Check if message was actually created
            if (!$messageCreated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save message to database. Please check the logs and try again.',
                    'debug' => env('APP_DEBUG') ? 'Message creation failed' : null
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully to ' . $sponsor->username . '!',
                'data' => [
                    'sponsor_username' => $sponsor->username,
                    'sponsor_name' => trim($sponsor->firstname . ' ' . $sponsor->lastname) ?: $sponsor->username,
                    'subject' => $request->subject,
                    'sent_at' => now()->format('Y-m-d H:i:s'),
                    'message_saved' => $messageCreated
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all()),
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error contacting sponsor: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }
     
    public function teamTree()
    {
        // Display team tree visualization page
        $pageTitle = "Team Tree";
        return view('frontend.my-newwork', compact('pageTitle'));
    }
    public function teamTreeData()
    {
        // Return JSON data for team tree (for AJAX)
        $userId = Auth::id();
        $teamData = $this->buildTeamTree($userId);
        $teamStats = $this->getTeamStats($userId);
        
        return response()->json([
            'team_tree' => $teamData,
            'statistics' => $teamStats,
            'status' => 'success'
        ]);
    }

    public function getTeamByLevel(Request $request)
    {
        try {
            $level = $request->input('level', 1);
            $user = Auth::user();
            $username = $user->username;
            
            Log::info("Getting team by level: $level for user: $username (ID: {$user->id})");
            
            $teamMembers = $this->getTeamMembersByLevel($username, $level);
            $levelStats = $this->getLevelStats($username, $level);
            
            Log::info("Found " . count($teamMembers) . " members at level $level");
            
            return response()->json([
                'level' => $level,
                'members' => $teamMembers,
                'statistics' => $levelStats,
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error("Error in getTeamByLevel: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch team data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available levels with member counts
     */
    public function getAvailableLevels()
    {
        try {
            $user = Auth::user();
            $availableLevels = [];
            
            // Check levels 1-10 and see which ones have members
            for ($level = 1; $level <= 10; $level++) {
                $members = $this->getTeamMembersByLevel($user->username, $level);
                if (!empty($members)) {
                    $availableLevels[] = [
                        'level' => $level,
                        'count' => count($members)
                    ];
                }
            }
            
            Log::info("Available levels for user {$user->username}: " . json_encode($availableLevels));
            
            return response()->json([
                'status' => 'success',
                'available_levels' => $availableLevels,
                'total_levels' => count($availableLevels)
            ]);
        } catch (\Exception $e) {
            Log::error("Error in getAvailableLevels: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch available levels: ' . $e->getMessage(),
                'available_levels' => [],
                'total_levels' => 0
            ], 500);
        }
    }
    private function buildTeamTree($userId, $depth = 0, $maxDepth = 5)
    {
        // Prevent infinite recursion and limit depth
        if ($depth > $maxDepth) {
            return [];
        }
        
        // If this is the root call (depth = 0), we don't include the current user
        // Instead, we return an array of their direct referrals
        if ($depth === 0) {
            $directReferrals = User::where('ref_by', $userId)
                ->select('id', 'username', 'firstname', 'lastname', 'status', 'created_at', 'deposit_wallet', 'interest_wallet', 'email')
                ->get();
            
            $teamData = [];
            foreach ($directReferrals as $referral) {
                $member = [
                    'id' => $referral->id,
                    'username' => $referral->username,
                    'name' => $referral->firstname . ' ' . $referral->lastname,
                    'email' => $referral->email,
                    'status' => $referral->status,
                    'created_at' => $referral->created_at->format('Y-m-d'),
                    'total_balance' => $referral->deposit_wallet + $referral->interest_wallet,
                    'level' => 1, // Level 1 for direct referrals
                    'referral_count' => User::where('ref_by', $referral->id)->count(),
                    'children' => []
                ];
                
                // Get children recursively - use user ID
                $children = $this->buildTeamTreeRecursive($referral->id, 2, $maxDepth);
                $member['children'] = $children;
                
                $teamData[] = $member;
            }
            return $teamData;
        }
        
        return $this->buildTeamTreeRecursive($userId, $depth, $maxDepth);
    }
    
    private function buildTeamTreeRecursive($userIdentifier, $depth, $maxDepth)
    {
        if ($depth > $maxDepth) {
            return [];
        }
        
        // Get user ID if userIdentifier is a username
        if (is_string($userIdentifier) && !is_numeric($userIdentifier)) {
            $user = User::where('username', $userIdentifier)->first();
            $userId = $user ? $user->id : null;
        } else {
            $userId = $userIdentifier;
        }
        
        if (!$userId) {
            return [];
        }
        
        $directReferrals = User::where('ref_by', $userId)
            ->select('id', 'username', 'firstname', 'lastname', 'status', 'created_at', 'deposit_wallet', 'interest_wallet', 'email')
            ->get();
        
        $children = [];
        foreach ($directReferrals as $referral) {
            $member = [
                'id' => $referral->id,
                'username' => $referral->username,
                'name' => $referral->firstname . ' ' . $referral->lastname,
                'email' => $referral->email,
                'status' => $referral->status,
                'created_at' => $referral->created_at->format('Y-m-d'),
                'total_balance' => $referral->deposit_wallet + $referral->interest_wallet,
                'level' => $depth,
                'referral_count' => User::where('ref_by', $referral->id)->count(),
                'children' => $this->buildTeamTreeRecursive($referral->id, $depth + 1, $maxDepth)
            ];
            
            $children[] = $member;
        }
        
        return $children;
    }
    
    /**
     * Get team statistics
     */
    private function getTeamStats($userId)
    {
        $stats = [
            'total_team_members' => 0,
            'active_members' => 0,
            'inactive_members' => 0,
            'total_team_business' => 0,
            'levels' => []
        ];
        
        // Get all team members (recursive)
        $allTeamMembers = $this->getAllTeamMembers($userId);
        
        $stats['total_team_members'] = count($allTeamMembers);
        
        foreach ($allTeamMembers as $member) {
            if ($member['status'] == 1) {
                $stats['active_members']++;
            } else {
                $stats['inactive_members']++;
            }
            
            $stats['total_team_business'] += $member['total_balance'];
            
            // Count members by level
            $level = $member['level'];
            if (!isset($stats['levels'][$level])) {
                $stats['levels'][$level] = 0;
            }
            $stats['levels'][$level]++;
        }
        
        return $stats;
    }
    
    /**
     * Get all team members in flat array
     */
    private function getAllTeamMembers($userIdentifier, $currentLevel = 1, $maxLevel = 10)
    {
        $allMembers = [];
        
        if ($currentLevel > $maxLevel) {
            return $allMembers;
        }
        
        $directReferrals = User::where('ref_by', $userIdentifier)
            ->select('id', 'username', 'firstname', 'lastname', 'status', 'created_at', 'deposit_wallet', 'interest_wallet')
            ->get();
        
        foreach ($directReferrals as $referral) {
            $memberData = [
                'id' => $referral->id,
                'username' => $referral->username,
                'name' => $referral->firstname . ' ' . $referral->lastname,
                'status' => $referral->status,
                'level' => $currentLevel,
                'total_balance' => $referral->deposit_wallet + $referral->interest_wallet,
                'created_at' => $referral->created_at->format('Y-m-d')
            ];
            
            $allMembers[] = $memberData;
            
            // Get children recursively
            $children = $this->getAllTeamMembers($referral->username, $currentLevel + 1, $maxLevel);
            $allMembers = array_merge($allMembers, $children);
        }
        
        return $allMembers;
    }

    /**
     * Get team members by specific level
     */
    private function getTeamMembersByLevel($userIdentifier, $targetLevel, $currentLevel = 1)
    {
        $members = [];
        
        if ($currentLevel > 10) {
            return $members;
        }
        
        // Get user ID if userIdentifier is a username
        if (is_string($userIdentifier) && !is_numeric($userIdentifier)) {
            $user = User::where('username', $userIdentifier)->first();
            $userId = $user ? $user->id : null;
        } else {
            $userId = $userIdentifier;
        }
        
        if (!$userId) {
            return $members;
        }
        
        $directReferrals = User::where('ref_by', $userId)
            ->select('id', 'username', 'firstname', 'lastname', 'status', 'created_at', 'deposit_wallet', 'interest_wallet', 'email', 'mobile', 'country')
            ->get();
        
        foreach ($directReferrals as $referral) {
            if ($currentLevel == $targetLevel) {
                // This is the level we want
                $members[] = [
                    'id' => $referral->id,
                    'username' => $referral->username,
                    'name' => $referral->firstname . ' ' . $referral->lastname,
                    'firstname' => $referral->firstname,
                    'lastname' => $referral->lastname,
                    'email' => $referral->email,
                    'mobile' => $referral->mobile,
                    'country' => $referral->country ?? 'N/A',
                    'status' => $referral->status,
                    'level' => $currentLevel,
                    'total_balance' => $referral->deposit_wallet + $referral->interest_wallet,
                    'deposit_wallet' => $referral->deposit_wallet,
                    'interest_wallet' => $referral->interest_wallet,
                    'created_at' => $referral->created_at->format('Y-m-d H:i'),
                    'formatted_date' => $referral->created_at->format('M d, Y'),
                    'days_since_join' => $referral->created_at->diffInDays(now()),
                    'referral_count' => User::where('ref_by', $referral->id)->count()
                ];
            } elseif ($currentLevel < $targetLevel) {
                // Go deeper to find the target level - use user ID
                $childMembers = $this->getTeamMembersByLevel($referral->id, $targetLevel, $currentLevel + 1);
                $members = array_merge($members, $childMembers);
            }
        }
        
        return $members;
    }

    /**
     * Get statistics for a specific level
     */
    private function getLevelStats($userIdentifier, $level)
    {
        $levelMembers = $this->getTeamMembersByLevel($userIdentifier, $level);
        
        $stats = [
            'level' => $level,
            'total_members' => count($levelMembers),
            'active_members' => 0,
            'inactive_members' => 0,
            'total_balance' => 0,
            'total_deposit_wallet' => 0,
            'total_interest_wallet' => 0,
            'average_balance' => 0,
            'total_referrals' => 0
        ];
        
        foreach ($levelMembers as $member) {
            if ($member['status'] == 1) {
                $stats['active_members']++;
            } else {
                $stats['inactive_members']++;
            }
            
            $stats['total_balance'] += $member['total_balance'];
            $stats['total_deposit_wallet'] += $member['deposit_wallet'];
            $stats['total_interest_wallet'] += $member['interest_wallet'];
            $stats['total_referrals'] += $member['referral_count'];
        }
        
        if ($stats['total_members'] > 0) {
            $stats['average_balance'] = $stats['total_balance'] / $stats['total_members'];
        }
        
        return $stats;
    }
    
    public function sendMessage(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'in:normal,high,urgent',
            'send_email' => 'boolean'
        ]);

        // Create message record
        $message = Message::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $request->user_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority ?: 'normal',
        ]);

        // Send email if requested
        if ($request->send_email) {
            $recipient = User::find($request->user_id);
            $sender = Auth::user();
            
            if ($recipient && $recipient->email && filter_var($recipient->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    // Send email notification
                    Mail::send('emails.message-notification', [
                        'recipient_name' => $recipient->firstname . ' ' . $recipient->lastname,
                        'sender_name' => $sender->firstname . ' ' . $sender->lastname,
                        'subject' => $request->subject,
                        'message_content' => $request->message,
                        'priority' => $request->priority ?: 'normal',
                        'sent_date' => now()->format('F j, Y \a\t g:i A')
                    ], function ($mail) use ($recipient, $request) {
                        $mail->to($recipient->email, $recipient->firstname . ' ' . $recipient->lastname)
                             ->subject('New Message: ' . $request->subject);
                    });
                    
                } catch (\Exception $e) {
                    // Log email error but don't fail the message sending
                    Log::error('Failed to send email notification: ' . $e->getMessage());
                }
            } else {
                Log::warning('Recipient email is null or invalid, skipping email notification for user ID: ' . $request->user_id);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully!'
        ]);
    }

    public function getUserDetails(Request $request, $userId)
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            // Check if the requested user is in the current user's referral network
            $currentUser = Auth::user();
            $isInNetwork = $this->isUserInReferralNetwork($currentUser, $userId);
            
            if (!$isInNetwork) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied: User not in your referral network'
                ], 403);
            }

            // Get user statistics
            $totalInvest = Invest::where('user_id', $userId)->sum('amount');
            $totalWithdraw = Withdrawal::where('user_id', $userId)->whereIn('status', [1])->sum('amount');
            $totalDeposit = Deposit::where('user_id', $userId)->where('status', 1)->sum('amount');
            $referralCount = User::where('ref_by', $userId)->count();
            $referralEarnings = Transaction::where('remark', 'referral_commission')->where('user_id', $userId)->sum('amount');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'country' => $user->country ?? 'N/A',
                    'city' => $user->city ?? 'N/A',
                    'address' => $user->address,
                    'status' => $user->status,
                    'email_verified' => $user->ev,
                    'mobile_verified' => $user->sv,
                    'kyc_verified' => $user->kv,
                    'deposit_wallet' => number_format($user->deposit_wallet, 2),
                    'interest_wallet' => number_format($user->interest_wallet, 2),
                    'total_balance' => number_format($user->deposit_wallet + $user->interest_wallet, 2),
                    'created_at' => $user->created_at->format('F j, Y'),
                    'last_login' => $user->loginLogs()->latest()->first()?->created_at?->format('F j, Y g:i A') ?? 'Never',
                    'referrer' => $user->referrer ? [
                        'username' => $user->referrer->username,
                        'name' => $user->referrer->firstname . ' ' . $user->referrer->lastname
                    ] : null,
                    'statistics' => [
                        'total_invest' => number_format($totalInvest, 2),
                        'total_withdraw' => number_format($totalWithdraw, 2),
                        'total_deposit' => number_format($totalDeposit, 2),
                        'referral_count' => $referralCount,
                        'referral_earnings' => number_format($referralEarnings, 2)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user details: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching user details'
            ], 500);
        }
    }

    private function isUserInReferralNetwork($currentUser, $targetUserId)
    {
        // Check if target user is in current user's referral network
        $allReferrals = $this->getAllReferrals($currentUser); 
        return in_array($targetUserId, $allReferrals);
    }

    private function getAllReferrals($user)
    {
        // Get all referral IDs recursively (similar to getAllTeamMembers but returns just IDs)
        $allReferralIds = [];
        $this->collectReferralIds($user->id, $allReferralIds);
        return $allReferralIds;
    }

    private function collectReferralIds($userId, &$allReferralIds, $currentLevel = 1, $maxLevel = 10)
    {
        // Prevent infinite recursion
        if ($currentLevel > $maxLevel) {
            return;
        }

        // Get direct referrals
        $directReferrals = User::where('ref_by', $userId)
            ->select('id')
            ->get();

        foreach ($directReferrals as $referral) {
            $allReferralIds[] = $referral->id;
            
            // Get children recursively
            $this->collectReferralIds($referral->id, $allReferralIds, $currentLevel + 1, $maxLevel);
        }
    }

    /**
     * Display user's deposit history
     */
    public function depositHistory(Request $request)
    {
        $user = Auth::user();
        $pageTitle = 'Deposit History';

        // Build query with filters
        $query = Deposit::where('user_id', $user->id)->with(['gateway']);

        // Apply filters
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('trx', 'like', '%' . $request->search . '%')
                  ->orWhere('amount', 'like', '%' . $request->search . '%');
            });
        }

        // Get deposits with pagination
        $deposits = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $stats = [
            'total_deposits' => Deposit::where('user_id', $user->id)->count(),
            'successful_deposits' => Deposit::where('user_id', $user->id)->where('status', 1)->count(),
            'pending_deposits' => Deposit::where('user_id', $user->id)->where('status', 2)->count(),
            'rejected_deposits' => Deposit::where('user_id', $user->id)->where('status', 3)->count(),
            'total_amount' => Deposit::where('user_id', $user->id)->sum('amount'),
            'successful_amount' => Deposit::where('user_id', $user->id)->where('status', 1)->sum('amount'),
            'pending_amount' => Deposit::where('user_id', $user->id)->where('status', 2)->sum('amount'),
        ];

        $data = compact('pageTitle', 'deposits', 'stats');

        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => view('frontend.deposit-history-table', $data)->render(),
                'pagination' => $deposits->links()->render()
            ]);
        }

        return view('frontend.deposit-history', $data);
    }

    /**
     * Dismiss install suggestion modal
     */
    public function dismissInstallSuggestion(Request $request)
    {
        try {
            $dismissType = $request->input('dismiss_type', 'daily');
            $date = $request->input('date', now()->format('Y-m-d'));
            
            if ($dismissType === 'permanent') {
                // Store permanent dismissal
                session(['install_suggestion_dismissed_permanently' => true]);
                
                // Log permanent dismissal for analytics
                \Illuminate\Support\Facades\Log::info('User permanently dismissed install suggestion', [
                    'user_id' => Auth::id(),
                    'username' => Auth::user()->username,
                    'date' => $date
                ]);
            } elseif ($dismissType === 'daily') {
                // Store daily dismissal
                session(['install_suggestion_last_shown_date' => $date]);
                
                // Log daily dismissal
                \Illuminate\Support\Facades\Log::info('User dismissed install suggestion for today', [
                    'user_id' => Auth::id(),
                    'username' => Auth::user()->username,
                    'date' => $date
                ]);
            } else {
                // Just mark as shown today (when user closes without checking any option)
                session(['install_suggestion_last_shown_date' => $date]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Install suggestion dismissed successfully',
                'dismiss_type' => $dismissType,
                'date' => $date
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to dismiss install suggestion', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to dismiss install suggestion'
            ], 500);
        }
    }
    public function generationHistory(Request $request) 
    {
        $user = Auth::user();

        // Get comprehensive referral commission data
        $referralEarnings = ReferralCommission::where('referrer_user_id', $user->id)
            ->with(['earner:id,username,email', 'referrer:id,username'])
            ->orderBy('distributed_at', 'desc')
            ->paginate(20);

        // Get summary statistics
        $stats = [
            'total_earned' => ReferralCommission::where('referrer_user_id', $user->id)->sum('commission_amount'),
            'today_earned' => ReferralCommission::where('referrer_user_id', $user->id)
                ->whereDate('distributed_at', today())->sum('commission_amount'),
            'this_month_earned' => ReferralCommission::where('referrer_user_id', $user->id)
                ->whereMonth('distributed_at', now()->month)
                ->whereYear('distributed_at', now()->year)
                ->sum('commission_amount'),
            'total_commissions' => ReferralCommission::where('referrer_user_id', $user->id)->count(),
        ];

        // Get level-wise breakdown
        $levelStats = [];
        for ($level = 1; $level <= 7; $level++) {
            $levelData = ReferralCommission::where('referrer_user_id', $user->id)
                ->where('level', $level)
                ->selectRaw('COUNT(*) as count, SUM(commission_amount) as total, AVG(commission_amount) as average')
                ->first();
            
            $levelStats[$level] = [
                'count' => $levelData->count ?? 0,
                'total' => $levelData->total ?? 0,
                'average' => $levelData->average ?? 0,
            ];
        }

        // Get recent activity (last 30 days)
        $recentActivity = ReferralCommission::where('referrer_user_id', $user->id)
            ->where('distributed_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(distributed_at) as date, COUNT(*) as count, SUM(commission_amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Get top earning days
        $topEarningDays = ReferralCommission::where('referrer_user_id', $user->id)
            ->selectRaw('DATE(distributed_at) as date, SUM(commission_amount) as total')
            ->groupBy('date')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return view('user.generation-history', compact(
            'referralEarnings', 
            'stats', 
            'levelStats', 
            'recentActivity', 
            'topEarningDays'
        ));
    }
    
    /**
     * Display messages dashboard
     */
    public function messages(Request $request)
    {
        $pageTitle = "Messages";
        $user = Auth::user();
        
        // Get message statistics
        $totalMessages = Message::where(function($query) use ($user) {
            $query->where('from_user_id', $user->id)
                  ->orWhere('to_user_id', $user->id);
        })->count();
        
        $unreadMessages = Message::where('to_user_id', $user->id)
            ->where('is_read', false)
            ->count();
            
        $sentMessages = Message::where('from_user_id', $user->id)->count();
        $receivedMessages = Message::where('to_user_id', $user->id)->count();
        
        // Get recent messages (last 10)
        $recentMessages = Message::where(function($query) use ($user) {
            $query->where('from_user_id', $user->id)
                  ->orWhere('to_user_id', $user->id);
        })
        ->with(['sender:id,username,firstname,lastname', 'recipient:id,username,firstname,lastname'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
        
        $stats = [
            'total_messages' => $totalMessages,
            'unread_messages' => $unreadMessages,
            'sent_messages' => $sentMessages,
            'received_messages' => $receivedMessages
        ];
        
        return view('frontend.messages', compact('pageTitle', 'stats', 'recentMessages')); 
    }
    
    /**
     * Display inbox messages
     */
    public function inbox(Request $request)
    {
        $pageTitle = "Inbox";
        $user = Auth::user();
        
        if ($request->ajax()) {
            $messages = Message::where('to_user_id', $user->id)
                ->with(['sender:id,username,firstname,lastname'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            return DataTables::of($messages)
                ->addIndexColumn()
                ->addColumn('sender', function ($row) {
                    $senderName = $row->sender ? 
                        (trim($row->sender->firstname . ' ' . $row->sender->lastname) ?: $row->sender->username) : 
                        'Unknown';
                    return '<span class="badge bg-info">' . $senderName . '</span>';
                })
                ->addColumn('subject', function ($row) {
                    $subject = $row->subject ?: 'No Subject';
                    $badge = $row->is_read ? '' : '<span class="badge bg-warning ms-1">New</span>';
                    return '<strong>' . $subject . '</strong>' . $badge;
                })
                ->addColumn('message_preview', function ($row) {
                    return '<small class="text-muted">' . Str::limit($row->message, 50) . '</small>';
                })
                ->addColumn('created_at', function ($row) {
                    return '<div><p class="mb-0">' . $row->created_at->format('M d, Y') . '</p><small class="text-muted">' . $row->created_at->format('h:i A') . '</small></div>';
                })
                ->addColumn('status', function ($row) {
                    return $row->is_read ? 
                        '<span class="badge bg-success">Read</span>' : 
                        '<span class="badge bg-warning">Unread</span>';
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="btn-group">
                        <button class="btn btn-sm btn-primary view-message" data-id="' . $row->id . '" title="View Message">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-success reply-message" data-id="' . $row->id . '" title="Reply">
                            <i class="fas fa-reply"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-message" data-id="' . $row->id . '" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['sender', 'subject', 'message_preview', 'created_at', 'status', 'actions'])
                ->make(true);
        }
        
        return view('frontend.messages-inbox', compact('pageTitle')); 
    }
    
    /**
     * Display sent messages
     */
    public function sentMessages(Request $request)
    {
        $pageTitle = "Sent Messages";
        $user = Auth::user();
        
        if ($request->ajax()) {
            try {
                $messages = Message::where('from_user_id', $user->id)
                    ->with(['recipient:id,username,firstname,lastname'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                    
                return DataTables::of($messages)
                    ->addIndexColumn()
                    ->addColumn('receiver', function ($row) {
                        $receiverName = $row->recipient ? 
                            (trim($row->recipient->firstname . ' ' . $row->recipient->lastname) ?: $row->recipient->username) : 
                            'Unknown';
                        return '<span class="badge bg-info">' . $receiverName . '</span>';
                    })
                    ->addColumn('subject', function ($row) {
                        return '<strong>' . ($row->subject ?: 'No Subject') . '</strong>';
                    })
                    ->addColumn('message_preview', function ($row) {
                        return '<small class="text-muted">' . Str::limit($row->message, 50) . '</small>';
                    })
                    ->addColumn('created_at', function ($row) {
                        return '<div><p class="mb-0">' . $row->created_at->format('M d, Y') . '</p><small class="text-muted">' . $row->created_at->format('h:i A') . '</small></div>';
                    })
                    ->addColumn('status', function ($row) {
                        return $row->recipient && $row->is_read ? 
                            '<span class="badge bg-success">Read</span>' : 
                            '<span class="badge bg-warning">Delivered</span>';
                    })
                    ->addColumn('actions', function ($row) {
                        return '<div class="btn-group">
                            <button class="btn btn-sm btn-primary view-message" data-id="' . $row->id . '" title="View Message">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-message" data-id="' . $row->id . '" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>';
                    })
                    ->rawColumns(['receiver', 'subject', 'message_preview', 'created_at', 'status', 'actions'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('Error in sentMessages: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                return response()->json([
                    'error' => 'Failed to load sent messages: ' . $e->getMessage()
                ], 500);
            }
        }
        
        return view('frontend.messages-sent', compact('pageTitle')); 
    }
    
    /**
     * View specific message
     */
    public function viewMessage(Request $request, $messageId)
    {
        $user = Auth::user();
        
        $message = Message::where('id', $messageId)
            ->where(function($query) use ($user) {
                $query->where('from_user_id', $user->id)
                      ->orWhere('to_user_id', $user->id);
            })
            ->with(['sender:id,username,firstname,lastname', 'recipient:id,username,firstname,lastname'])
            ->first();
            
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found or access denied'
            ], 404);
        }
        
        // Mark as read if user is the receiver
        if ($message->to_user_id == $user->id && !$message->is_read) {
            $message->is_read = true;
            $message->read_at = now();
            $message->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'subject' => $message->subject ?: 'No Subject',
                'message' => $message->message,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => trim($message->sender->firstname . ' ' . $message->sender->lastname) ?: $message->sender->username,
                    'username' => $message->sender->username
                ],
                'receiver' => [
                    'id' => $message->recipient->id,
                    'name' => trim($message->recipient->firstname . ' ' . $message->recipient->lastname) ?: $message->recipient->username,
                    'username' => $message->recipient->username
                ],
                'created_at' => $message->created_at->format('F j, Y \a\t g:i A'),
                'is_read' => $message->is_read,
                'can_reply' => $message->to_user_id == $user->id
            ]
        ]);
    }
    
    /**
     * Mark message as read
     */
    public function markMessageAsRead(Request $request, $messageId)
    {
        $user = Auth::user();
        
        $message = Message::where('id', $messageId)
            ->where('to_user_id', $user->id)
            ->first();
            
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found or access denied'
            ], 404);
        }
        
        $message->is_read = true;
        $message->read_at = now();
        $message->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Message marked as read'
        ]);
    }
    
    /**
     * Reply to a message
     */
    public function replyToMessage(Request $request, $messageId)
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:1000'
            ]);
            
            $user = Auth::user();
            
            // Find the original message
            $originalMessage = Message::where('id', $messageId)
                ->where('to_user_id', $user->id)
                ->with('sender')
                ->first();
                
            if (!$originalMessage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Original message not found or access denied'
                ], 404);
            }
            
            // Create reply message
            $reply = Message::create([
                'from_user_id' => $user->id,
                'to_user_id' => $originalMessage->from_user_id,
                'subject' => $request->subject,
                'message' => $request->message,
                'reply_to_id' => $messageId,
                'status' => 'active' // Use valid enum value
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error replying to message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reply. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Delete a message
     */
    public function deleteMessage(Request $request, $messageId)
    {
        $user = Auth::user();
        
        $message = Message::where('id', $messageId)
            ->where(function($query) use ($user) {
                $query->where('from_user_id', $user->id)
                      ->orWhere('to_user_id', $user->id);
            })
            ->first();
            
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found or access denied'
            ], 404);
        }
        
        $message->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully!'
        ]);
    }
    
    /**
     * Get real-time performance metrics
     */
    public function getPerformanceMetrics()
    {
        $startTime = microtime(true);
        
        // Enable query logging
        DB::enableQueryLog();
        
        // Count concurrent users (active in last 5 minutes)
        $concurrentUsers = DB::table('users')
            ->where('last_seen', '>=', Carbon::now()->subMinutes(5))
            ->count();
        
        // Calculate metrics
        $endTime = microtime(true);
        $loadingTime = round(($endTime - $startTime) * 1000, 2);
        $queryCount = count(DB::getQueryLog());
        
        return response()->json([
            'success' => true,
            'metrics' => [
                'loading_time' => $loadingTime,
                'query_count' => $queryCount,
                'concurrent_users' => $concurrentUsers,
                'load_timestamp' => Carbon::now()->format('H:i:s')
            ]
        ]);
    }
}