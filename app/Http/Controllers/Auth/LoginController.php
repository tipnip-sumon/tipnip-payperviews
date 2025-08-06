<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/user/dashboard'; // Redirect to dashboard after successful login
    // protected $redirectTo = '/home'; // Avoid /home to prevent 419 errors after logout

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        // Add login success flag for cache clearing
        return redirect()->intended($this->redirectPath())->with([
            'login_success' => true,
            'fresh_login' => true,
            'user_id' => $user->id
        ]);
    }

    /**
     * Show the application's login form with session notifications.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm(\Illuminate\Http\Request $request)
    {
        $sessionNotifications = [];
        
        // Check for recent session notifications for all users (last 5 minutes)
        try {
            $recentNotifications = \Illuminate\Support\Facades\DB::table('user_session_notifications')
                ->join('users', 'user_session_notifications.user_id', '=', 'users.id')
                ->where('user_session_notifications.is_read', false)
                ->where('user_session_notifications.created_at', '>=', now()->subMinutes(5))
                ->select(
                    'user_session_notifications.*', 
                    'users.username'
                )
                ->orderBy('user_session_notifications.created_at', 'desc')
                ->limit(10)
                ->get();

            // Group notifications by user for better display
            $sessionNotifications = $recentNotifications->groupBy('username');
            
        } catch (\Exception $e) {
            // If table doesn't exist, notifications will be empty
            $sessionNotifications = collect();
        }
        
        // Get remembered login value from cookie (7-day cookie)
        $rememberedLogin = $request->cookie('remembered_login', '');
        
        return view('auth.login', compact('sessionNotifications', 'rememberedLogin'));
    } 

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(\Illuminate\Http\Request $request)
    {
        $this->validateLogin($request);

        // Get the user by username or email - enhanced logic for dual login
        $loginField = $request->input($this->username());
        
        // Determine if the input is an email or username
        $isEmail = filter_var($loginField, FILTER_VALIDATE_EMAIL);
        
        // Query user by either email or username
        $user = \App\Models\User::where(function($query) use ($loginField, $isEmail) {
            if ($isEmail) {
                // If input looks like email, prioritize email field but also check username
                $query->where('email', $loginField)
                      ->orWhere('username', $loginField);
            } else {
                // If input doesn't look like email, prioritize username but also check email
                $query->where('username', $loginField)
                      ->orWhere('email', $loginField);
            }
        })->first();

        if (!$user) {
            // More specific error message based on input type
            $errorMessage = $isEmail 
                ? 'No account found with this email address. Please check your email or register for a new account.'
                : 'Username not found. Please check your username or register for a new account.';
            
            // Check if this is an app request
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_type' => 'user_not_found',
                    'input_type' => $isEmail ? 'email' : 'username'
                ], 401);
            }
            
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => $errorMessage,
                ])
                ->with('error_type', 'user_not_found')
                ->with('input_type', $isEmail ? 'email' : 'username');
        }

        // Check if user is active
        if ($user->status != 1) {
            $statusMessage = 'Your account has been suspended or deactivated. Please contact our support team for assistance.';
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $statusMessage,
                    'error_type' => 'account_inactive',
                    'user_status' => $user->status
                ], 401);
            }
            
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => $statusMessage,
                ])
                ->with('error_type', 'account_inactive')
                ->with('user_status', $user->status);
        }

        // Check if account is locked
        if ($user->isLocked()) {
            $lockExpiry = $user->locked_until->diffForHumans();
            $lockMessage = "Your account is temporarily locked due to multiple failed login attempts. Please try again {$lockExpiry} or contact support for immediate assistance.";
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $lockMessage,
                    'error_type' => 'account_locked',
                    'locked_until' => $user->locked_until->toISOString(),
                    'unlock_time_human' => $lockExpiry
                ], 401);
            }
            
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => $lockMessage,
                ])
                ->with('error_type', 'account_locked')
                ->with('unlock_time', $lockExpiry);
        }

        // Check if email is verified (only if user implements MustVerifyEmail)
        if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail()) {
            $verificationMessage = 'Your email address is not verified yet. Please check your email for the verification link or request a new one below.';
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $verificationMessage,
                    'error_type' => 'email_not_verified',
                    'needs_verification' => true,
                    'user_email' => $user->email,
                    'can_resend' => true
                ], 403);
            }
            
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => $verificationMessage,
                ])
                ->with('error_type', 'email_not_verified')
                ->with('show_resend_verification', true)
                ->with('user_email', $user->email)
                ->with('user_id', $user->id);
        }

        // Attempt authentication - we need to temporarily override the credentials
        // to work with Laravel's built-in authentication which expects specific field names
        if ($this->attemptLoginWithUsernameOrEmail($request, $user)) {
            // CRITICAL: Check if user already has an active session and notify them
            $this->notifyExistingSessionBeforeLogout($user, $request);
            
            // CRITICAL: Force logout any existing sessions BEFORE processing new login
            $this->forceLogoutExistingSessions($user);
            
            // Reset login attempts on successful login
            $user->update([
                'login_attempts' => 0,
                'locked_until' => null,
            ]);

            // Update last login info and create new session (invalidates previous sessions)
            $user->updateLastLogin();

            // Handle Remember Me functionality - set 7-day cookie for username/email only
            $this->handleRememberMeCookie($request, $user);

            // Handle successful login - always redirect normally (no JSON complications)
            return $this->sendLoginResponse($request);
        }

        // Record failed login attempt
        $user->incrementLoginAttempts();
        
        $remainingAttempts = 5 - $user->login_attempts;
        
        // Create more specific error messages
        if ($remainingAttempts > 0) {
            $errorMessage = "Invalid password. Please check your password and try again. You have {$remainingAttempts} attempt" . ($remainingAttempts > 1 ? 's' : '') . " remaining before your account is temporarily locked.";
            $errorType = 'invalid_password';
        } else {
            $errorMessage = 'Account temporarily locked due to multiple failed login attempts. Please try again later or contact support for immediate assistance.';
            $errorType = 'account_locked_now';
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_type' => $errorType,
                'remaining_attempts' => max(0, $remainingAttempts),
                'user_found' => true,
                'password_issue' => true
            ], 401);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                'password' => $errorMessage,
            ])
            ->with('error_type', $errorType)
            ->with('remaining_attempts', max(0, $remainingAttempts));
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(\Illuminate\Http\Request $request)
    {
        $request->validate([
            $this->username() => 'required|string|min:3|max:255',
            'password' => 'required|string|min:8',
        ], [
            $this->username() . '.required' => 'Username or email address is required.',
            $this->username() . '.min' => 'Username or email must be at least 3 characters long.',
            $this->username() . '.max' => 'Username or email must not exceed 255 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.'
        ]);
    }

    /**
     * Attempt to log the user into the application with username or email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return bool
     */
    protected function attemptLoginWithUsernameOrEmail(\Illuminate\Http\Request $request, $user)
    {
        // Get the login credentials
        $credentials = [
            'password' => $request->input('password'),
        ];
        
        // Try to authenticate using the user's actual username first
        $credentials['username'] = $user->username;
        
        if (\Illuminate\Support\Facades\Auth::attempt(
            $credentials, 
            $request->filled('remember')
        )) {
            return true;
        }
        
        // If username didn't work, try with email
        unset($credentials['username']);
        $credentials['email'] = $user->email;
        
        if (\Illuminate\Support\Facades\Auth::attempt(
            $credentials, 
            $request->filled('remember')
        )) {
            return true;
        }
        
        return false;
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username'; // Keep as 'username' for form field name, but accept email too
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        // Remove auth middleware from logout to allow GET logout without session issues
        // $this->middleware('auth')->only('logout');
    }    /**
     * Notify existing session before logout about new login attempt.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function notifyExistingSessionBeforeLogout($user, $request)
    {
        // Check if user has an active session
        if ($user->current_session_id && $user->session_created_at) {
            $currentLoginInfo = [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'location' => $this->getLocationFromIP($request->ip()),
                'device' => $this->getDeviceInfo($request->userAgent()),
                'time' => now()->format('M d, Y \a\t h:i A T')
            ];
            
            $existingSessionInfo = [
                'ip' => $user->session_ip_address,
                'user_agent' => $user->session_user_agent,
                'started' => $user->session_created_at->format('M d, Y \a\t h:i A T'),
                'duration' => $user->session_created_at->diffForHumans(now(), true)
            ];
            
            // Create a notification record for the existing session
            $this->createSessionNotification($user, $currentLoginInfo, $existingSessionInfo);
            
            // Log the security event
            \Illuminate\Support\Facades\Log::warning('Multiple login attempt detected', [
                'user_id' => $user->id,
                'username' => $user->username,
                'existing_session' => $existingSessionInfo,
                'new_login_attempt' => $currentLoginInfo,
                'action' => 'notification_sent_before_logout'
            ]);
        }
    }

    /**
     * Create a session notification for the user.
     */
    protected function createSessionNotification($user, $newLoginInfo, $existingSessionInfo)
    {
        try {
            $newLoginIp = $newLoginInfo['ip'];
            $oldSessionIp = $existingSessionInfo['ip'];
            
            // Check if IPs are the same - if so, don't create notification
            if ($newLoginIp === $oldSessionIp) {
                \Illuminate\Support\Facades\Log::info('Same IP login detected, skipping notification', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'ip' => $newLoginIp,
                    'action' => 'notification_skipped_same_ip'
                ]);
                return;
            }
            
            // Clean up old notifications for this user, keeping only the most recent one
            $this->cleanupUserNotifications($user->id);
            
            // Store new notification in database since IPs are different
            \Illuminate\Support\Facades\DB::table('user_session_notifications')->insert([
                'user_id' => $user->id,
                'type' => 'new_login_detected',
                'title' => 'New Login Detected',
                'message' => "Your account was accessed from a new device/location. Previous session from {$oldSessionIp} (active for {$existingSessionInfo['duration']}) will be terminated.",
                'new_login_ip' => $newLoginIp,
                'new_login_device' => $newLoginInfo['device'],
                'new_login_location' => $newLoginInfo['location'],
                'old_session_ip' => $oldSessionIp,
                'old_session_duration' => $existingSessionInfo['duration'],
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            \Illuminate\Support\Facades\Log::info('Session notification created for different IP login', [
                'user_id' => $user->id,
                'username' => $user->username,
                'new_ip' => $newLoginIp,
                'old_ip' => $oldSessionIp,
                'action' => 'notification_created'
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to create session notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            // If notifications table doesn't exist, create session flash instead
            session()->flash('security_alert', [
                'type' => 'new_login_detected',
                'title' => 'New Login Detected',
                'message' => "Your account was accessed from a new device/location.",
                'details' => $newLoginInfo
            ]);
        }
    }
    
    /**
     * Clean up old notifications for the user, keeping only the most recent one.
     *
     * @param  int  $userId
     * @return void
     */
    protected function cleanupUserNotifications($userId)
    {
        try {
            // Get the most recent notification ID for this user
            $latestNotification = \Illuminate\Support\Facades\DB::table('user_session_notifications')
                ->where('user_id', $userId)
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($latestNotification) {
                // Delete all other notifications for this user except the latest one
                $deletedCount = \Illuminate\Support\Facades\DB::table('user_session_notifications')
                    ->where('user_id', $userId)
                    ->where('id', '!=', $latestNotification->id)
                    ->delete();
                
                if ($deletedCount > 0) {
                    \Illuminate\Support\Facades\Log::info('Cleaned up old session notifications', [
                        'user_id' => $userId,
                        'deleted_count' => $deletedCount,
                        'kept_notification_id' => $latestNotification->id
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to cleanup old notifications', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle Remember Me functionality - set 7-day cookie for username/email only
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function handleRememberMeCookie(\Illuminate\Http\Request $request, $user)
    {
        try {
            if ($request->filled('remember')) {
                // Determine what the user entered (username or email)
                $loginField = $request->input($this->username());
                $isEmail = filter_var($loginField, FILTER_VALIDATE_EMAIL);
                
                // Store what they actually entered for convenience
                $cookieValue = $loginField;
                
                // Set 7-day cookie (7 * 24 * 60 = 10080 minutes)
                $cookie = cookie('remembered_login', $cookieValue, 10080, '/', null, false, true);
                
                // Queue the cookie to be sent with the response
                \Illuminate\Support\Facades\Cookie::queue($cookie);
                
                \Illuminate\Support\Facades\Log::info('Remember Me cookie set', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'cookie_value' => $cookieValue,
                    'is_email' => $isEmail,
                    'expires_in' => '7 days'
                ]);
            } else {
                // User unchecked remember me, clear the cookie
                \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('remembered_login'));
                
                \Illuminate\Support\Facades\Log::info('Remember Me cookie cleared', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'reason' => 'remember_not_checked'
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to handle Remember Me cookie', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get device info from user agent.
     */
    protected function getDeviceInfo($userAgent)
    {
        $device = 'Unknown Device';
        
        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
            $device = 'Mobile Device';
        } elseif (strpos($userAgent, 'iPad') !== false || strpos($userAgent, 'Tablet') !== false) {
            $device = 'Tablet';
        } elseif (strpos($userAgent, 'Windows') !== false) {
            $device = 'Windows PC';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            $device = 'Mac Computer';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $device = 'Linux Computer';
        }
        
        // Add browser info
        if (strpos($userAgent, 'Chrome') !== false) {
            $device .= ' (Chrome)';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $device .= ' (Firefox)';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $device .= ' (Safari)';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $device .= ' (Edge)';
        }
        
        return $device;
    }

    /**
     * Get approximate location from IP (basic implementation).
     */
    protected function getLocationFromIP($ip)
    {
        // Basic local IP detection
        if ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
            return 'Local Network';
        }
        
        // You can integrate with IP geolocation services here
        // For now, return a basic response
        return 'External IP: ' . $ip;
    }

    /**
     * Force logout any existing sessions for the user before new login.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function forceLogoutExistingSessions($user)
    {
        // Clear any existing session tracking for THIS user only
        // Removed method check as it may not exist
        
        // Only clear sessions for this specific user - be very conservative
        try {
            if (config('session.driver') === 'database') {
                // Only clear sessions specifically belonging to this user
                \Illuminate\Support\Facades\DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->delete();
                    
                // Log the action
                \Illuminate\Support\Facades\Log::info('Cleared database sessions for user', [
                    'user_id' => $user->id,
                    'username' => $user->username
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to clear database sessions', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
        
        // Removed aggressive payload-based session clearing to prevent affecting other users
        
        // Log the forced logout for security monitoring
        \Illuminate\Support\Facades\Log::info('Forced logout of existing sessions for new login', [
            'user_id' => $user->id,
            'username' => $user->username,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log the user out of the application.
     * Handles both GET and POST requests without requiring authentication middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(\Illuminate\Http\Request $request)
    {
        // Check if user is actually logged in
        if (!\Illuminate\Support\Facades\Auth::check()) {
            // User is not logged in, redirect to login with info message
            return redirect()->route('login', ['t' => time()])
                ->with('info', 'You were already logged out.');
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        $userId = $user->id;
        $username = $user->username ?? 'Unknown';

        // Log the logout attempt for security
        \Illuminate\Support\Facades\Log::info('User logout initiated', [
            'user_id' => $userId,
            'username' => $username,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method()
        ]);

        // Clear user session tracking in database
        try {
            // Only clear sessions for THIS specific user to avoid affecting other users
            \Illuminate\Support\Facades\DB::table('sessions')
                ->where('user_id', $userId)
                ->delete();
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Session cleanup warning during logout: ' . $e->getMessage());
        }

        // Perform the actual logout
        $this->guard()->logout();

        // Simple session invalidation without regeneration (prevents 419 errors)
        $request->session()->invalidate();

        // Log successful logout
        \Illuminate\Support\Facades\Log::info('User logged out successfully', [
            'user_id' => $userId,
            'username' => $username,
            'ip' => $request->ip()
        ]);

        // Check if this is an AJAX request for cache clearing
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
                'redirect_url' => route('login', ['from_logout' => '1', 't' => time()]),
                'clear_cache' => true,
                'cache_version' => time()
            ]);
        }

        // Determine redirect based on request type
        $redirectUrl = route('login', [
            'from_logout' => '1', 
            't' => time()
        ]);

        // Add simple logout success message
        $response = redirect($redirectUrl)->with([
            'success' => 'You have been logged out successfully.',
            'logout_completed' => true
        ]);
        
        // Add basic cache control headers
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Resend email verification for unverified users during login attempt
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function resendVerification(\Illuminate\Http\Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found with this email address.'
                    ], 404);
                }
                
                return redirect()->back()->withErrors([
                    'email' => 'User not found with this email address.'
                ]);
            }

            // Check if user already verified
            if ($user->hasVerifiedEmail()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email address is already verified. You can now log in.'
                    ], 400);
                }
                
                return redirect()->back()->with('success', 'Email address is already verified. You can now log in.');
            }

            // Check rate limiting (prevent spam)
            $cacheKey = 'verification_email_sent_' . $user->id;
            if (cache()->has($cacheKey)) {
                $remainingTime = cache()->get($cacheKey) - now()->timestamp;
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Please wait {$remainingTime} seconds before requesting another verification email."
                    ], 429);
                }
                
                return redirect()->back()->withErrors([
                    'email' => "Please wait {$remainingTime} seconds before requesting another verification email."
                ]);
            }

            // Send verification email
            $user->sendEmailVerificationNotification();

            // Set rate limiting (60 seconds)
            cache()->put($cacheKey, now()->addSeconds(60)->timestamp, 60);

            \Illuminate\Support\Facades\Log::info('Email verification resent', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Verification email sent successfully! Please check your email and click the verification link.'
                ]);
            }

            return redirect()->back()->with('success', 'Verification email sent successfully! Please check your email and click the verification link.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email address.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to resend verification email', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send verification email. Please try again later.'
                ], 500);
            }

            return redirect()->back()->withErrors([
                'email' => 'Failed to send verification email. Please try again later.'
            ]);
        }
    }
}
