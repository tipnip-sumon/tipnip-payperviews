<?php

namespace App\Http\Controllers\admin; 

use App\Models\Admin;
use App\Mail\WelcomeEmail;
use Illuminate\Http\Request;
use App\Models\AdminTransReceive;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Mail\AdminPasswordChangeNotification;
use App\Models\User;
use App\Models\Invest;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\VideoView;
use App\Models\VideoLink;
use App\Models\KycVerification;
use App\Models\Transaction;

class AdminController extends Controller
{
    public function index(Request $request){ 
        // Check for session expiration message
        if ($request->has('session_expired')) {
            $reason = $request->get('reason', 'unknown');
            $message = 'Your session has expired due to inactivity. Please log in again.';
            
            if ($reason === 'timeout') {
                $message = 'Your admin session has timed out for security reasons. Please log in again.';
            } elseif ($reason === 'server_session_expired') {
                $message = 'Your session has expired on the server. Please log in again.';
            } elseif ($reason === 'not_authenticated') {
                $message = 'Authentication required. Please log in to access the admin panel.';
            }
            
            return view('admin.index')->with('error', $message);
        }
        
        return view('admin.index');
    }

    public function login(Request $request){
        if($request->input()){
            $credentials = $request->validate([
                'email' => 'required|string', // Changed from 'username' to 'email' for consistency
                'password' => 'required|string',
            ]);

            // Find admin by email or username (supporting both for flexibility)
            $admin = Admin::where('email', $credentials['email'])
                         ->orWhere('username', $credentials['email'])
                         ->first();

            if (!$admin) {
                // Return JSON response for AJAX requests
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Invalid email or username.',
                        'errors' => ['email' => ['Invalid email or username.']]
                    ], 422);
                }
                return redirect()->route('admin.index')->with('error', 'Invalid email or username.');
            }

            // Check if admin is active
            if (!$admin->is_active) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your account has been deactivated. Please contact support.',
                        'errors' => ['email' => ['Account deactivated.']]
                    ], 403);
                }
                return redirect()->route('admin.index')->with('error', 'Your account has been deactivated. Please contact support.');
            }

            // Check if account is locked
            if ($admin->isLocked()) {
                $lockExpiry = $admin->locked_until->diffForHumans();
                $message = "Account is locked. Try again {$lockExpiry}.";
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $message,
                        'errors' => ['email' => [$message]]
                    ], 403);
                }
                return redirect()->route('admin.index')->with('error', $message);
            }
            
            // Attempt authentication with either email or username
            $loginField = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            
            if (Auth::guard('admin')->attempt([
                $loginField => $credentials['email'],
                'password' => $credentials['password']
            ], $request->filled('remember'))) {
                // Record successful login
                $admin->recordLogin($request->ip(), $request->userAgent());
                
                // Set session data
                $request->session()->put('admin', '1');
                $request->session()->put('admin_id', $admin->id);
                $request->session()->put('name', $admin->name);
                $request->session()->put('username', $admin->username);
                $request->session()->put('role', $admin->role);
                $request->session()->put('is_super_admin', $admin->is_super_admin);
                
                // Regenerate session for security
                $request->session()->regenerate();
                
                $successMessage = 'Login successful! Welcome back, ' . $admin->name . '.';
                
                // Return JSON response for AJAX requests
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $successMessage,
                        'redirect_url' => route('admin.dashboard')
                    ], 200);
                }
                
                return redirect()->route('admin.dashboard')->with('success', $successMessage);
            } else {
                // Record failed login attempt
                $admin->incrementLoginAttempts();
                
                $remainingAttempts = 5 - $admin->login_attempts;
                $errorMessage = '';
                
                if ($remainingAttempts > 0) {
                    $errorMessage = "Invalid credentials. {$remainingAttempts} attempts remaining.";
                } else {
                    $errorMessage = 'Account locked due to multiple failed attempts. Please try again later.';
                }
                
                // Return JSON response for AJAX requests
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $errorMessage,
                        'errors' => ['password' => [$errorMessage]]
                    ], 422);
                }
                
                return redirect()->route('admin.index')->with('error', $errorMessage);
            }
        } else {
            // If no input, show the login form (this shouldn't happen with the new routing)
            return view('admin.login_fresh');
        }
    }
    public function dashboard(Request $request){
        if(Auth::guard('admin')->check()){
            // Start performance tracking
            $startTime = microtime(true);
            $startMemory = memory_get_usage();
            
            // Enable query logging for this request
            DB::enableQueryLog();
            
            $admin = Auth::guard('admin')->user();
            
            // Check if admin is still active
            if (!$admin->is_active) {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.index')->with('error', 'Your account has been deactivated.');
            }
            
            // Cached concurrent users count (refreshed every 30 seconds)
            $concurrentUsers = Cache::remember('concurrent_users_count', 30, function () {
                return DB::table('users')
                    ->where('last_seen', '>=', Carbon::now()->subMinutes(5))
                    ->count();
            });
            
            $transfers = AdminTransReceive::where('admin_id', $admin->id)->get();
            
            // Get comprehensive dashboard data
            $data = [
                'admin' => $admin,
                'transfers' => $transfers,
                'totalUsers' => User::count(),
                'activeUsers' => User::where('status', 1)->count(),
                'totalInvestments' => Invest::sum('amount'),
                'totalDeposits' => Deposit::where('status', 1)->sum('amount'),
                'totalWithdrawals' => Withdrawal::where('status', 1)->sum('amount'),
                'pendingWithdrawals' => Withdrawal::where('status', 0)->count(),
                'pendingDeposits' => Deposit::where('status', 0)->count(),
                'totalVideoViews' => VideoView::count(),
                'totalVideoEarnings' => VideoView::sum('earned_amount'),
                'totalVideoLinks' => VideoLink::count(),
                'activeVideoLinks' => VideoLink::where('status', 1)->count(),
                'pendingKyc' => KycVerification::where('status', 'pending')->count(),
                'todayUsers' => User::whereDate('created_at', today())->count(),
                'todayEarnings' => VideoView::whereDate('viewed_at', today())->sum('earned_amount'),
                'todayDeposits' => Deposit::whereDate('created_at', today())->where('status', 1)->sum('amount'),
                'todayWithdrawals' => Withdrawal::whereDate('created_at', today())->where('status', 1)->sum('amount'),
                'recentTransactions' => Transaction::with('user')->latest()->limit(10)->get(),
                'recentUsers' => User::latest()->limit(10)->get(),
                'recentDeposits' => Deposit::with('user')->latest()->limit(5)->get(),
                'recentWithdrawals' => Withdrawal::with('user')->latest()->limit(5)->get(),
                // Monthly statistics
                'monthlyStats' => [
                    'users' => User::whereMonth('created_at', now()->month)->count(),
                    'deposits' => Deposit::whereMonth('created_at', now()->month)->where('status', 1)->sum('amount'),
                    'withdrawals' => Withdrawal::whereMonth('created_at', now()->month)->where('status', 1)->sum('amount'),
                    'video_views' => VideoView::whereMonth('viewed_at', now()->month)->count(),
                    'earnings' => VideoView::whereMonth('viewed_at', now()->month)->sum('earned_amount'),
                ],
                // Quick stats for admin
                'adminStats' => [
                    'balance' => $admin->balance,
                    'total_transferred' => $admin->total_transferred,
                    'last_login' => $admin->last_login_at,
                    // 'permissions_count' => $admin->permissions ? count($admin->permissions) : 0,
                ]
            ];
            
            // Calculate performance metrics for admin dashboard
            $endTime = microtime(true);
            $loadingTime = round(($endTime - $startTime) * 1000, 2); // Convert to milliseconds
            $queryCount = count(DB::getQueryLog());
            $memoryUsage = round((memory_get_usage() - $startMemory) / 1024 / 1024, 2); // Convert to MB
            
            $data['performance_metrics'] = [
                'loading_time' => $loadingTime,
                'query_count' => $queryCount,
                'concurrent_users' => $concurrentUsers,
                'memory_usage' => $memoryUsage,
                'load_timestamp' => Carbon::now()->format('H:i:s')
            ];
            
            return view('admin.dashboard', $data);
        } else {
            return redirect()->route('admin.index')->with('error', 'Please login first.');
        }
    }
    /**
     * Admin logout with enhanced security and cache clearing
     */
    public function logout(Request $request)
    {
        try {
            // Handle CSRF token expiration gracefully
            if (!$request->hasValidSignature() && !$request->session()->token() === $request->input('_token')) {
                Log::warning('CSRF token mismatch on admin logout', [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'session_token' => $request->session()->token() ? 'present' : 'missing',
                    'request_token' => $request->input('_token') ? 'present' : 'missing'
                ]);
                
                // Regenerate token and force logout anyway for security
                $request->session()->regenerateToken();
            }

            if (Auth::guard('admin')->check()) {
                $adminUser = Auth::guard('admin')->user();
                $adminId = $adminUser->id;
                
                // Log the logout activity
                Log::info('Admin logout initiated', [
                    'admin_id' => $adminId,
                    'username' => $adminUser->username,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'logout_time' => now()
                ]);
                
                // Clear all admin session data
                $request->session()->forget([
                    'admin', 
                    'admin_id', 
                    'name', 
                    'username', 
                    'role', 
                    'is_super_admin',
                    'last_activity'
                ]);
                
                // Logout from admin guard
                Auth::guard('admin')->logout();
                
                // Invalidate the session
                $request->session()->invalidate();
                
                // Regenerate CSRF token for security
                $request->session()->regenerateToken();
                
            } else {
                // Force clear session even if not authenticated
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                Log::info('Admin logout attempted without authentication', [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }
            
            // Add cache clearing headers for browser cache management
            $response = redirect()->route('admin.index');
            
            // Set comprehensive cache control headers
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
            
            // Clear site data (modern browsers)
            $response->headers->set('Clear-Site-Data', '"cache", "storage", "executionContexts"');
            
            // Add cache version headers for JavaScript detection
            $cacheVersion = time(); // Generate new cache version
            $response->headers->set('X-Cache-Version', $cacheVersion);
            $response->headers->set('X-Admin-Logout', 'true');
            $response->headers->set('X-Cache-Bust', uniqid('admin_logout_', true));
            
            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Admin logout successful. Please login again.',
                    'redirect' => route('admin.index'),
                    'cache_cleared' => true,
                    'cache_version' => $cacheVersion,
                    'timestamp' => time(),
                    'csrf_token' => csrf_token() // Provide new CSRF token
                ])->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => 'Thu, 01 Jan 1970 00:00:00 GMT',
                    'Clear-Site-Data' => '"cache", "storage"',
                    'X-Cache-Version' => $cacheVersion,
                    'X-Admin-Logout' => 'true'
                ]);
            }
            
            // Flash success message
            $request->session()->flash('success', 'You have been successfully logged out. Please login again.');
            
            return $response;
        } catch (\Exception $e) {
            // Log any errors that occur during logout
            Log::error('Admin logout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Force logout even if there's an error
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('admin.index')->with('warning', 'Logout completed, but an error occurred. Browser cache cleared for security.');
        }
    }
    
    /**
     * Emergency logout for expired sessions (no CSRF verification needed)
     */
    public function emergencyLogout(Request $request)
    {
        try {
            Log::info('Admin emergency logout initiated', [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'reason' => 'session_expired_or_csrf_mismatch',
                'timestamp' => now()
            ]);
            
            // Force logout regardless of session state
            if (Auth::guard('admin')->check()) {
                $adminUser = Auth::guard('admin')->user();
                Log::info('Emergency logout for admin user', [
                    'admin_id' => $adminUser->id,
                    'username' => $adminUser->username
                ]);
            }
            
            // Force logout from admin guard
            Auth::guard('admin')->logout();
            
            // Clear all session data
            $request->session()->invalidate();
            
            // Regenerate token for security
            $request->session()->regenerateToken();
            
            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Session expired. Please login again.',
                    'redirect' => route('admin.index'),
                    'reason' => 'session_expired',
                    'csrf_token' => csrf_token()
                ])->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'X-Admin-Emergency-Logout' => 'true'
                ]);
            }
            
            // Regular redirect with message
            return redirect()->route('admin.index')
                ->with('warning', 'Your session has expired. Please login again.')
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache'
                ]);
                
        } catch (\Exception $e) {
            Log::error('Emergency logout error', [
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ]);
            
            // Force minimal logout
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            
            return redirect()->route('admin.index')
                ->with('error', 'Session cleanup completed. Please login again.');
        }
    }
    
    /**
     * Simple admin logout (no CSRF verification, no middleware issues)
     */
    public function simpleLogout(Request $request)
    {
        try {
            // Log the simple logout attempt
            Log::info('Admin simple logout initiated', [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
            
            // Force logout from admin guard
            Auth::guard('admin')->logout();
            
            // Clear all session data
            $request->session()->invalidate();
            
            // Regenerate token for security
            $request->session()->regenerateToken();
            
            // Add cache clearing headers
            $response = redirect()->route('admin.index');
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
            $response->headers->set('Clear-Site-Data', '"cache", "storage", "executionContexts"');
            
            // Flash success message
            $request->session()->flash('success', 'You have been successfully logged out.');
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Simple admin logout error', [
                'error' => $e->getMessage(),
                'ip_address' => $request->ip()
            ]);
            
            // Force minimal logout
            Auth::guard('admin')->logout();
            session()->invalidate();
            
            return redirect()->route('admin.index')
                ->with('warning', 'Logout completed. Please login again.');
        }
    }
    
    /**
     * Extend admin session
     */
    public function extendSession(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            // Update session activity
            $request->session()->put('last_activity', time());
            
            Log::info('Admin session extended', [
                'admin_id' => Auth::guard('admin')->id(),
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Session extended successfully',
                'timestamp' => time()
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Session expired'
        ], 401);
    }
    
    /**
     * Get fresh CSRF token
     */
    public function getCsrfToken(Request $request)
    {
        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        return response()->json([
            'token' => csrf_token(),
            'timestamp' => time()
        ]);
    }
    
    /**
     * Check session status for admin
     */
    public function getSessionStatus(Request $request)
    {
        // Check if admin is authenticated
        $isAuthenticated = Auth::guard('admin')->check();
        
        if (!$isAuthenticated) {
            return response()->json([
                'authenticated' => false,
                'message' => 'Not authenticated'
            ], 401);
        }
        
        $admin = Auth::guard('admin')->user();
        
        return response()->json([
            'authenticated' => true,
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'session_id' => session()->getId(),
            'last_activity' => session()->get('last_activity', time()),
            'timestamp' => time()
        ]);
    }
    
    /**
     * Show admin profile
     */
    public function profile()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }
    
    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $admin->id . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/images/admins/'), $filename);
            $validated['image'] = $filename;
            
            // Delete old image if exists and not default
            if ($admin->image && $admin->image !== 'default.png') {
                $oldImagePath = public_path('assets/images/admins/' . $admin->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }
        
        $admin->update($validated);
        
        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }
    
    /**
     * Change admin password
     */
    public function changePassword(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 401);
                }
                return redirect()->route('admin.index')->with('error', 'Unauthorized access.');
            }
            
            $validated = $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
            ]);
            
            // Check current password
            if (!Hash::check($validated['current_password'], $admin->password)) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Current password is incorrect.']);
                }
                return redirect()->back()->with('error', 'Current password is incorrect.');
            }
            
            // Update password
            $admin->update([
                'password' => Hash::make($validated['password']),
                'password_changed_at' => now(),
            ]);
            
            // Send email notification
            try {
                Mail::to($admin->email)->send(new AdminPasswordChangeNotification(
                    $admin,
                    $request->ip(),
                    $request->userAgent(),
                    now()
                ));
            } catch (\Exception $e) {
                Log::error('Failed to send password change email notification', [
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage(),
                    'email' => $admin->email
                ]);
            }
            
            // Log the password change
            Log::info('Admin password changed', [
                'admin_id' => $admin->id,
                'admin_username' => $admin->username,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
                'email_sent' => true
            ]);            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Password changed successfully! A security notification has been sent to your email.'
                ]);
            }
            
            return redirect()->route('admin.profile')->with('success', 'Password changed successfully. A security notification has been sent to your email.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                $errors = $e->validator->errors()->all();
                return response()->json(['success' => false, 'message' => implode(' ', $errors)]);
            }
            return redirect()->back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            Log::error('Password change error', [
                'admin_id' => $admin->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'An error occurred while changing password. Please try again.']);
            }
            return redirect()->back()->with('error', 'An error occurred while changing password. Please try again.');
        }
    }
    
    /**
     * Show admin settings
     */
    public function settings()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.settings', compact('admin'));
    }
    
    /**
     * Update admin settings
     */
    public function updateSettings(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'notifications' => 'nullable|array',
        ]);
        
        $admin->update($validated);
        
        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }
    
    /**
     * Test email notification (for development/testing purposes)
     */
    public function testPasswordChangeEmail(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();
            
            if (!$admin) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 401);
            }
            
            // Send test email
            Mail::to($admin->email)->send(new AdminPasswordChangeNotification(
                $admin,
                $request->ip(),
                $request->userAgent(),
                now()
            ));
            
            return response()->json([
                'success' => true,
                'message' => 'Test password change email sent successfully to ' . $admin->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send test password change email', [
                'admin_id' => $admin->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Check admin permissions for middleware
     */
    public function checkPermission($permission)
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin || !$admin->is_active) {
            return false;
        }
        
        return $admin->canDo($permission);
    }
    
    /**
     * Get admin dashboard statistics
     */
    public function getStats()
    {
        $admin = Auth::guard('admin')->user();
        
        return response()->json([
            'admin_balance' => $admin->balance,
            'total_transferred' => $admin->total_transferred,
            'last_login' => $admin->last_login_at?->format('Y-m-d H:i:s'),
            'login_attempts' => $admin->login_attempts,
            'is_locked' => $admin->isLocked(),
        ]);
    }
    
    /**
     * Reset admin login attempts (for super admin)
     */
    public function resetLoginAttempts(Request $request, $adminId)
    {
        $currentAdmin = Auth::guard('admin')->user();
        
        if (!$currentAdmin->is_super_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $admin = Admin::findOrFail($adminId);
        $admin->update([
            'login_attempts' => 0,
            'locked_until' => null,
        ]);
        
        return response()->json(['success' => 'Login attempts reset successfully']);
    }
}
