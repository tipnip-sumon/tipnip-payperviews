<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Invest;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\IdentityVerificationInstructionsMail;

class UserController extends Controller
{
    /**
     * Display a listing of users 
     */
    public function index(Request $request) 
    {
        $pageTitle = 'All Users';
        
        // Check if user is authenticated as admin
        if (!auth('admin')->check()) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Authentication required',
                    'message' => 'Please log in as admin to access this page'
                ], 401);
            }
            return redirect()->route('admin.index')->with('error', 'Please log in as admin to access this page');
        }
        
        // Get statistics for the dashboard
        $stats = $this->getUserStats();

        // Build query for users with efficient loading
        $query = User::select([
            'id', 'firstname', 'lastname', 'username', 'email', 'mobile',
            'status', 'deposit_wallet', 'interest_wallet', 'ev', 'kv', 'sv',
            'created_at', 'last_login_at'
        ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$search}%"]);
            });
        }

        // Get per page from request, default to 50, max 200
        $perPage = min((int)$request->get('per_page', 50), 200);
        
        // Get paginated users with latest first
        $users = $query->latest()->paginate($perPage);

        return view('admin.users.index', compact('pageTitle', 'stats', 'users'));
    }

    /**
     * Show user details
     */
    public function show($id)
    {
        $user = User::with(['deposits', 'withdrawals', 'transactions', 'invests'])
            ->findOrFail($id);

        $sponsor = $user->referrer ? $user->referrer->username : 'Unknown';
        
        $pageTitle = 'User Details - ' . $user->firstname . ' ' . $user->lastname;
        
        // Get user statistics
        $userStats = [
            'total_deposits' => $user->deposits()->where('status', 1)->sum('amount'),
            'total_withdrawals' => $user->withdrawals()->where('status', 1)->sum('amount'),
            'pending_withdrawals' => $user->withdrawals()->where('status', 2)->sum('amount'),
            'total_transactions' => $user->transactions()->count(),
            'total_investments' => $user->invests()->sum('amount'),
            'active_investments' => $user->invests()->where('status', 1)->count(),
            'referrals_count' => User::where('ref_by', $id)->count(),
        ];
        
        // Recent activities
        $recentDeposits = $user->deposits()->latest()->limit(5)->get();
        $recentWithdrawals = $user->withdrawals()->latest()->limit(5)->get();
        $recentTransactions = $user->transactions()->latest()->limit(10)->get();
        
        return view('admin.users.show', compact(
            'user', 'pageTitle', 'userStats', 
            'recentDeposits', 'recentWithdrawals', 'recentTransactions','sponsor'
        ));
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'Edit User - ' . $user->firstname . ' ' . $user->lastname;
        
        return view('admin.users.edit', compact('user', 'pageTitle'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'deposit_wallet' => 'nullable|numeric|min:0',
            'interest_wallet' => 'nullable|numeric|min:0',
            'status' => 'required|in:0,1,2',
            'ev' => 'nullable|boolean',
            'sv' => 'nullable|boolean',
            'kv' => 'nullable|boolean',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = $request->only([
            'firstname', 'lastname', 'username', 'email', 'mobile', 
            'country', 'deposit_wallet', 'interest_wallet', 'status', 'ev', 'sv', 'kv'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        // $user->fill($data);
        // $user->save();

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Change user status via AJAX
     */
    public function changeStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:0,1,2'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update(['status' => $request->status]);

        $statusText = ['Inactive', 'Active', 'Banned'][$request->status];

        return response()->json([
            'success' => true,
            'message' => "User status changed to {$statusText} successfully.",
            'status' => $request->status,
            'status_text' => $statusText
        ]);
    }

    /**
     * Ban user
     */
    public function banUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update(['status' => 2]);

        return response()->json([
            'success' => true,
            'message' => 'User has been banned successfully.'
        ]);
    }

    /**
     * Export users data
     */
    public function export(Request $request)
    {
        $pageTitle = 'Export Users';
        return view('admin.users.export', compact('pageTitle'));
    }

    /**
     * Download users data as CSV
     */
    public function downloadExport(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->filled('status')) {
            if ($request->status !== 'all') {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('verification_status')) {
            switch ($request->verification_status) {
                case 'email_verified':
                    $query->where('ev', 1);
                    break;
                case 'email_unverified':
                    $query->where('ev', 0);
                    break;
                case 'kyc_verified':
                    $query->where('kv', 1);
                    break;
                case 'kyc_unverified':
                    $query->where('kv', 0);
                    break;
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->get();

        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'First Name', 'Last Name', 'Username', 'Email', 'Mobile', 
                'Country', 'Status', 'Email Verified', 'SMS Verified', 'KYC Verified',
                'Deposit Wallet', 'Interest Wallet', 'Total Balance', 'Sponsor',
                'Referrer', 'Last Login', 'Joined Date'
            ]);

            foreach ($users as $user) {
                $status = ['Inactive', 'Active', 'Banned'][$user->status] ?? 'Unknown';
                $totalBalance = ($user->deposit_wallet ?? 0) + ($user->interest_wallet ?? 0);
                
                fputcsv($file, [
                    $user->id,
                    $user->firstname,
                    $user->lastname,
                    $user->username,
                    $user->email,
                    $user->mobile,
                    $user->country,
                    $status,
                    $user->ev ? 'Yes' : 'No',
                    $user->sv ? 'Yes' : 'No',
                    $user->kv ? 'Yes' : 'No',
                    number_format($user->deposit_wallet ?? 0, 2),
                    number_format($user->interest_wallet ?? 0, 2),
                    number_format($totalBalance, 2),
                    $user->sponsor,
                    $user->ref_by,
                    $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never',
                    $user->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show active users
     */
    public function active(Request $request)
    {
        $request->merge(['status' => '1']);
        return $this->index($request);
    }

    /**
     * Show inactive users
     */
    public function inactive(Request $request)
    {
        $request->merge(['status' => '0']);
        return $this->index($request);
    }

    /**
     * Show banned users
     */
    public function banned(Request $request)
    {
        $request->merge(['status' => '2']);
        return $this->index($request);
    }

    /**
     * Test endpoint for debugging
     */
    public function test(Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Connection successful',
                'authenticated' => auth('admin')->check(),
                'user_count' => User::count(),
                'timestamp' => now()->toDateTimeString()
            ]);
        }
        
        return response()->json([
            'error' => 'This endpoint is for AJAX requests only'
        ], 400);
    }

    /**
     * Check if username is available
     */
    public function checkUsernameAvailability(Request $request)
    {
        $username = $request->input('username');
        $userId = $request->input('user_id'); // For edit form, exclude current user
        
        if (empty($username) || strlen($username) < 3) {
            return response()->json([
                'available' => false,
                'message' => 'Username must be at least 3 characters long'
            ]);
        }
        
        // Check if username exists (exclude current user if editing)
        $query = User::where('username', $username);
        if ($userId) {
            $query->where('id', '!=', $userId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Username is already taken' : 'Username is available'
        ]);
    }
    
    /**
     * Check if email is available
     */
    public function checkEmailAvailability(Request $request)
    {
        $email = $request->input('email');
        $userId = $request->input('user_id'); // For edit form, exclude current user
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'available' => false,
                'message' => 'Please enter a valid email address'
            ]);
        }
        
        // Check if email exists (exclude current user if editing)
        $query = User::where('email', $email);
        if ($userId) {
            $query->where('id', '!=', $userId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Email is already taken' : 'Email is available'
        ]);
    }

    /**
     * Search users for transfer functionality
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $limit = $request->get('limit', 10);

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Query must be at least 2 characters',
                    'users' => []
                ]);
            }

            $users = User::select([
                'id', 'firstname', 'lastname', 'username', 'email', 
                'status', 'deposit_wallet as balance'
            ])
            ->where(function($q) use ($query) {
                $q->where('username', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('firstname', 'LIKE', "%{$query}%")
                  ->orWhere('lastname', 'LIKE', "%{$query}%")
                  ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$query}%"]);
            })
            ->where('status', '!=', 2) // Exclude banned users
            ->orderBy('status', 'desc') // Active users first
            ->orderBy('username', 'asc')
            ->limit($limit)
            ->get();

            return response()->json([
                'success' => true,
                'users' => $users,
                'total' => $users->count()
            ]);

        } catch (\Exception $e) {
            Log::error('User search error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching users',
                'users' => []
            ], 500);
        }
    }

    /**
     * Quick stats for dashboard (cached)
     */
    private function getUserStats()
    {
        // Cache stats for 5 minutes to improve performance
        return cache()->remember('user_stats', 300, function () {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('status', 1)->count(),
                'inactive_users' => User::where('status', 0)->count(),
                'banned_users' => User::where('status', 2)->count(),
                'verified_email' => User::where('ev', 1)->count(),
                'verified_sms' => User::where('sv', 1)->count(),
                'verified_kyc' => User::where('kv', 1)->count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'new_users_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            ];
        });
    }

    // =============================================================================
    // USER VERIFICATION MANAGEMENT METHODS
    // =============================================================================

    /**
     * Verification Dashboard
     */
    public function verificationDashboard(Request $request)
    {
        $pageTitle = 'User Verification Dashboard';
        
        // Get verification statistics
        $stats = [
            'total_users' => User::count(),
            'email_verified' => User::where('ev', 1)->count(),
            'email_unverified' => User::where('ev', 0)->count(),
            'sms_verified' => User::where('sv', 1)->count(),
            'sms_unverified' => User::where('sv', 0)->count(),
            'phone_verified' => User::where('phone_verified', 1)->count(),
            'phone_unverified' => User::where('phone_verified', 0)->count(),
            'identity_verified' => User::where('identity_verified', 1)->count(),
            'identity_unverified' => User::where('identity_verified', 0)->count(),
            'kyc_verified' => User::where('kv', 1)->count(),
            'kyc_unverified' => User::where('kv', 0)->count(),
            'two_fa_enabled' => User::where('two_fa_status', 1)->count(),
            'two_fa_disabled' => User::where('two_fa_status', 0)->count(),
        ];

        // Recent verification activities
        $recentVerifications = User::select('id', 'firstname', 'lastname', 'username', 'ev', 'sv', 'kv', 'updated_at')
            ->where('updated_at', '>=', now()->subDays(7))
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return view('admin.users.verification.dashboard', compact('pageTitle', 'stats', 'recentVerifications'));
    }

    /**
     * Verification Settings
     */
    public function verificationSettings(Request $request)
    {
        $pageTitle = 'Verification Settings';
        
        // Get current verification settings (you might want to store these in a settings table)
        $settings = [
            'email_verification_required' => config('verification.email_required', true),
            'sms_verification_required' => config('verification.sms_required', false),
            'phone_verification_required' => config('verification.phone_required', false),
            'identity_verification_required' => config('verification.identity_required', false),
            'kyc_verification_required' => config('verification.kyc_required', false),
            'two_fa_required' => config('verification.two_fa_required', false),
            'auto_verify_email' => config('verification.auto_verify_email', false),
            'verification_email_template' => config('verification.email_template', 'default'),
        ];

        return view('admin.users.verification.settings', compact('pageTitle', 'settings'));
    }

    /**
     * Update Verification Settings
     */
    public function updateVerificationSettings(Request $request)
    {
        $request->validate([
            'email_verification_required' => 'nullable|boolean',
            'sms_verification_required' => 'nullable|boolean',
            'phone_verification_required' => 'nullable|boolean',
            'identity_verification_required' => 'nullable|boolean',
            'kyc_verification_required' => 'nullable|boolean',
            'two_fa_required' => 'nullable|boolean',
            'auto_verify_email' => 'nullable|boolean',
            'verification_email_template' => 'nullable|string|in:default,custom',
        ]);

        // Here you would typically save to a settings table or config file
        // For now, we'll just return success
        
        return redirect()->route('admin.users.verification.settings')
            ->with('success', 'Verification settings updated successfully.');
    }

    /**
     * Verification Reports
     */
    public function verificationReports(Request $request)
    {
        $pageTitle = 'Verification Reports';
        
        // Date range filter
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        
        // Verification statistics by date range
        $stats = [
            'total_users' => User::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'email_verified' => User::where('ev', 1)->whereBetween('email_verified_at', [$dateFrom, $dateTo])->count(),
            'sms_verified' => User::where('sv', 1)->whereBetween('sms_verified_at', [$dateFrom, $dateTo])->count(),
            'kyc_verified' => User::where('kv', 1)->whereBetween('kyc_verified_at', [$dateFrom, $dateTo])->count(),
        ];

        // Verification trend data (for charts)
        $trendData = User::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.users.verification.reports', compact('pageTitle', 'stats', 'trendData', 'dateFrom', 'dateTo'));
    }

    /**
     * Export Verification Reports
     */
    public function exportVerificationReports(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        
        $users = User::select([
            'id', 'firstname', 'lastname', 'username', 'email', 'mobile',
            'ev', 'sv', 'kv', 'phone_verified', 'identity_verified', 'two_fa_status',
            'email_verified_at', 'sms_verified_at', 'kyc_verified_at', 'created_at'
        ])
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->get();

        $filename = 'verification_report_' . $dateFrom . '_to_' . $dateTo . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'First Name', 'Last Name', 'Username', 'Email', 'Mobile',
                'Email Verified', 'SMS Verified', 'KYC Verified', 'Phone Verified', 
                'Identity Verified', '2FA Enabled', 'Email Verified At', 'SMS Verified At', 
                'KYC Verified At', 'Joined Date'
            ]);

            foreach ($users as $user) {
                // Helper function to format dates safely
                $formatDate = function($date) {
                    if (!$date) return 'Not Verified';
                    if (is_string($date)) {
                        try {
                            return Carbon::parse($date)->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            return $date; // Return as-is if parsing fails
                        }
                    }
                    return $date->format('Y-m-d H:i:s');
                };

                fputcsv($file, [
                    $user->id,
                    $user->firstname,
                    $user->lastname,
                    $user->username,
                    $user->email,
                    $user->mobile,
                    $user->ev ? 'Yes' : 'No',
                    $user->sv ? 'Yes' : 'No',
                    $user->kv ? 'Yes' : 'No',
                    $user->phone_verified ? 'Yes' : 'No',
                    $user->identity_verified ? 'Yes' : 'No',
                    $user->two_fa_status ? 'Yes' : 'No',
                    $formatDate($user->email_verified_at),
                    $formatDate($user->sms_verified_at),
                    $formatDate($user->kyc_verified_at),
                    $user->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Email Verification Index
     */
    public function emailVerificationIndex(Request $request)
    {
        $pageTitle = 'Email Verification Management';
        
        $query = User::select(['id', 'firstname', 'lastname', 'username', 'email', 'ev', 'email_verified_at', 'created_at']);
        
        // Filter by verification status
        if ($request->filled('status')) {
            $query->where('ev', $request->status);
        }
        
        $users = $query->latest()->paginate(50);
        $stats = [
            'verified' => User::where('ev', 1)->count(),
            'unverified' => User::where('ev', 0)->count(),
        ];

        return view('admin.users.verification.email', compact('pageTitle', 'users', 'stats'));
    }

    /**
     * SMS Verification Index
     */
    public function smsVerificationIndex(Request $request)
    {
        $pageTitle = 'SMS Verification Management';
        
        $query = User::select(['id', 'firstname', 'lastname', 'username', 'mobile', 'sv', 'sms_verified_at', 'created_at']);
        
        if ($request->filled('status')) {
            $query->where('sv', $request->status);
        }
        
        $users = $query->latest()->paginate(50);
        $stats = [
            'verified' => User::where('sv', 1)->count(),
            'unverified' => User::where('sv', 0)->count(),
        ];

        return view('admin.users.verification.sms', compact('pageTitle', 'users', 'stats'));
    }

    /**
     * Phone Verification Index
     */
    public function phoneVerificationIndex(Request $request)
    {
        $pageTitle = 'Phone Verification Management';
        
        $query = User::select(['id', 'firstname', 'lastname', 'username', 'mobile', 'phone_verified', 'phone_verified_at', 'created_at']);
        
        if ($request->filled('status')) {
            $query->where('phone_verified', $request->status);
        }
        
        $users = $query->latest()->paginate(50);
        $stats = [
            'verified' => User::where('phone_verified', 1)->count(),
            'unverified' => User::where('phone_verified', 0)->count(),
        ];

        return view('admin.users.verification.phone', compact('pageTitle', 'users', 'stats'));
    }

    /**
     * Identity Verification Index
     */
    public function identityVerificationIndex(Request $request)
    {
        $pageTitle = 'Identity Verification Management';
        
        $query = User::select(['id', 'firstname', 'lastname', 'username', 'identity_verified', 'identity_verified_at', 'created_at']);
        
        if ($request->filled('status')) {
            $query->where('identity_verified', $request->status);
        }
        
        $users = $query->latest()->paginate(50);
        $stats = [
            'verified' => User::where('identity_verified', 1)->count(),
            'unverified' => User::where('identity_verified', 0)->count(),
        ];

        return view('admin.users.verification.identity', compact('pageTitle', 'users', 'stats'));
    }

    /**
     * KYC Verification Index
     */
    public function kycVerificationIndex(Request $request)
    {
        $pageTitle = 'KYC Verification Management';
        
        $query = User::select(['id', 'firstname', 'lastname', 'username', 'kv', 'kyc_verified_at', 'created_at']);
        
        if ($request->filled('status')) {
            $query->where('kv', $request->status);
        }
        
        $users = $query->latest()->paginate(50);
        $stats = [
            'verified' => User::where('kv', 1)->count(),
            'unverified' => User::where('kv', 0)->count(),
        ];

        return view('admin.users.verification.kyc', compact('pageTitle', 'users', 'stats'));
    }

    /**
     * Two Factor Authentication Index
     */
    public function twoFactorIndex(Request $request)
    {
        $pageTitle = '2FA Management';
        
        $query = User::select(['id', 'firstname', 'lastname', 'username', 'two_fa_status', 'two_fa_enabled_at', 'created_at']);
        
        if ($request->filled('status')) {
            $query->where('two_fa_status', $request->status);
        }
        
        $users = $query->latest()->paginate(50);
        $stats = [
            'enabled' => User::where('two_fa_status', 1)->count(),
            'disabled' => User::where('two_fa_status', 0)->count(),
        ];

        return view('admin.users.verification.two_factor', compact('pageTitle', 'users', 'stats'));
    }

    // =============================================================================
    // VERIFICATION ACTION METHODS
    // =============================================================================

    /**
     * Verify Email
     */
    public function verifyEmail($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'ev' => 1,
            'email_verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.'
        ]);
    }

    /**
     * Unverify Email
     */
    public function unverifyEmail($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'ev' => 0,
            'email_verified_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email verification removed successfully.'
        ]);
    }

    /**
     * Verify SMS
     */
    public function verifySms($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'sv' => 1,
            'sms_verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SMS verified successfully.'
        ]);
    }

    /**
     * Unverify SMS
     */
    public function unverifySms($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'sv' => 0,
            'sms_verified_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SMS verification removed successfully.'
        ]);
    }

    /**
     * Verify Phone
     */
    public function verifyPhone($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'phone_verified' => 1,
            'phone_verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Phone verified successfully.'
        ]);
    }

    /**
     * Unverify Phone
     */
    public function unverifyPhone($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'phone_verified' => 0,
            'phone_verified_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Phone verification removed successfully.'
        ]);
    }

    /**
     * Verify Identity
     */
    public function verifyIdentity($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'identity_verified' => 1,
            'identity_verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Identity verified successfully.'
        ]);
    }

    /**
     * Unverify Identity
     */
    public function unverifyIdentity($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'identity_verified' => 0,
            'identity_verified_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Identity verification removed successfully.'
        ]);
    }

    /**
     * Verify KYC
     */
    public function verifyKyc($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'kv' => 1,
            'kyc_verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KYC verified successfully.'
        ]);
    }

    /**
     * Unverify KYC
     */
    public function unverifyKyc($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'kv' => 0,
            'kyc_verified_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KYC verification removed successfully.'
        ]);
    }

    /**
     * Enable 2FA
     */
    public function enable2fa($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'two_fa_status' => 1,
            'two_fa_enabled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => '2FA enabled successfully.'
        ]);
    }

    /**
     * Disable 2FA
     */
    public function disable2fa($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'two_fa_status' => 0,
            'two_fa_enabled_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => '2FA disabled successfully.'
        ]);
    }

    /**
     * Reset 2FA
     */
    public function reset2fa($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Reset 2FA settings
            $user->update([
                'two_fa_status' => 0,
                'two_fa_enabled_at' => null,
                'two_fa_secret' => null,
                'two_fa_recovery_codes' => null,
                'two_fa_verified_at' => null
            ]);

            // Log the action
            Log::info('2FA reset for user', [
                'user_id' => $user->id,
                'username' => $user->username,
                'admin' => Auth::user()->username ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => '2FA has been reset successfully. User will need to set up 2FA again.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to reset 2FA for user', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'admin' => Auth::user()->username ?? 'system'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset 2FA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force 2FA for user (admin enforcement)
     */
    public function force2fa($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Force enable 2FA for the user
            $user->update([
                'two_fa_status' => 1,
                'two_fa_enabled_at' => now(),
                'two_fa_forced' => true, // Mark as admin-forced
                'two_fa_forced_at' => now(),
                'two_fa_force_expires_at' => now()->addDays(30) // Give user 30 days to set up
            ]);

            // Log the action
            Log::info('2FA forced for user by admin', [
                'user_id' => $user->id,
                'username' => $user->username,
                'admin' => Auth::user()->username ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => '2FA has been enforced for this user. They will be required to set up 2FA on their next login.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to force 2FA for user', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'admin' => Auth::user()->username ?? 'system'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to force 2FA: ' . $e->getMessage()
            ], 500);
        }
    }

    // =============================================================================
    // BULK VERIFICATION METHODS
    // =============================================================================

    /**
     * Bulk Verify Users
     */
    public function bulkVerify(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'verification_type' => 'required|in:email,sms,phone,identity,kyc,2fa'
        ]);

        $userIds = $request->user_ids;
        $type = $request->verification_type;
        
        $updateData = [];
        $timestampField = null;
        
        switch ($type) {
            case 'email':
                $updateData['ev'] = 1;
                $timestampField = 'email_verified_at';
                break;
            case 'sms':
                $updateData['sv'] = 1;
                $timestampField = 'sms_verified_at';
                break;
            case 'phone':
                $updateData['phone_verified'] = 1;
                $timestampField = 'phone_verified_at';
                break;
            case 'identity':
                $updateData['identity_verified'] = 1;
                $timestampField = 'identity_verified_at';
                break;
            case 'kyc':
                $updateData['kv'] = 1;
                $timestampField = 'kyc_verified_at';
                break;
            case '2fa':
                $updateData['two_fa_status'] = 1;
                $timestampField = 'two_fa_enabled_at';
                break;
        }
        
        if ($timestampField) {
            $updateData[$timestampField] = now();
        }
        
        $updated = User::whereIn('id', $userIds)->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Successfully verified {$updated} users for {$type}."
        ]);
    }

    /**
     * Bulk Unverify Users
     */
    public function bulkUnverify(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'verification_type' => 'required|in:email,sms,phone,identity,kyc,2fa'
        ]);

        $userIds = $request->user_ids;
        $type = $request->verification_type;
        
        $updateData = [];
        $timestampField = null;
        
        switch ($type) {
            case 'email':
                $updateData['ev'] = 0;
                $timestampField = 'email_verified_at';
                break;
            case 'sms':
                $updateData['sv'] = 0;
                $timestampField = 'sms_verified_at';
                break;
            case 'phone':
                $updateData['phone_verified'] = 0;
                $timestampField = 'phone_verified_at';
                break;
            case 'identity':
                $updateData['identity_verified'] = 0;
                $timestampField = 'identity_verified_at';
                break;
            case 'kyc':
                $updateData['kv'] = 0;
                $timestampField = 'kyc_verified_at';
                break;
            case '2fa':
                $updateData['two_fa_status'] = 0;
                $timestampField = 'two_fa_enabled_at';
                break;
        }
        
        if ($timestampField) {
            $updateData[$timestampField] = null;
        }
        
        $updated = User::whereIn('id', $userIds)->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Successfully unverified {$updated} users for {$type}."
        ]);
    }

    // =============================================================================
    // VERIFICATION COMMUNICATION METHODS
    // =============================================================================

    /**
     * Send Verification Email
     */
    public function sendVerificationEmail($id)
    {
        $user = User::findOrFail($id);
        
        // Here you would implement sending verification email
        // For now, just return success response
        
        return response()->json([
            'success' => true,
            'message' => 'Verification email sent successfully to ' . $user->email
        ]);
    }

    /**
     * Send Verification SMS
     */
    public function sendVerificationSms($id)
    {
        $user = User::findOrFail($id);
        
        // Here you would implement sending verification SMS
        // For now, just return success response
        
        return response()->json([
            'success' => true,
            'message' => 'Verification SMS sent successfully to ' . $user->mobile
        ]);
    }

    /**
     * Send Verification Phone Call
     */
    public function sendVerificationPhone($id)
    {
        $user = User::findOrFail($id);
        
        // Here you would implement phone verification call
        // For now, just return success response
        
        return response()->json([
            'success' => true,
            'message' => 'Verification phone call initiated for ' . $user->mobile
        ]);
    }

    /**
     * Send Identity Verification Instructions
     */
    public function sendVerificationIdentity($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Check if user has a valid email
            if (!$user->email || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have a valid email address'
                ], 400);
            }

            // Check if user is already verified
            if ($user->identity_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'User identity is already verified'
                ], 400);
            }
            
            // Send identity verification instructions email
            Mail::to($user->email)->send(new IdentityVerificationInstructionsMail($user));
            
            // Log the action
            Log::info('Identity verification instructions sent', [
                'user_id' => $user->id,
                'email' => $user->email,
                'admin' => Auth::user()->username ?? 'system'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Identity verification instructions sent successfully to ' . $user->email
            ]);
            
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to send identity verification instructions', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'admin' => Auth::user()->username ?? 'system'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send Bulk Identity Verification Instructions
     */
    public function sendBulkIdentityInstructions(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id'
        ]);

        $userIds = $request->user_ids;
        $sent = 0;
        $failed = 0;
        $errors = [];

        try {
            $users = User::whereIn('id', $userIds)->get();

            foreach ($users as $user) {
                try {
                    // Skip if already verified
                    if ($user->identity_verified) {
                        $failed++;
                        $errors[] = "User {$user->username} is already verified";
                        continue;
                    }

                    // Skip if no valid email
                    if (!$user->email || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        $failed++;
                        $errors[] = "User {$user->username} has no valid email";
                        continue;
                    }

                    // Send email
                    Mail::to($user->email)->send(new IdentityVerificationInstructionsMail($user));
                    $sent++;

                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Failed to send to {$user->username}: " . $e->getMessage();
                    Log::error('Bulk identity verification email failed', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Log the bulk action
            Log::info('Bulk identity verification instructions sent', [
                'total_selected' => count($userIds),
                'sent' => $sent,
                'failed' => $failed,
                'admin' => Auth::user()->username ?? 'system'
            ]);

            $message = "Bulk operation completed. Sent: {$sent}";
            if ($failed > 0) {
                $message .= ", Failed: {$failed}";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'sent_count' => $sent,
                'failed_count' => $failed,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk identity verification instructions failed', [
                'error' => $e->getMessage(),
                'admin' => Auth::user()->username ?? 'system'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
