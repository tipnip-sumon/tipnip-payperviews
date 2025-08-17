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
        // Allow multiple sessions - Clear only conflict-prone session data
        session()->forget(['login_attempts', 'lockout_time', 'temp_session_data']);
        
        // Regenerate session ID to prevent session fixation
        session()->regenerate();
        
        // Set session data with unique browser identifier to avoid conflicts
        $browserFingerprint = md5($request->userAgent() . $request->ip() . time());
        session([
            'auth_user_id' => $user->id, // For session security validation
            'login_timestamp' => time(),
            'last_activity' => time(),
            'browser_session_id' => $browserFingerprint, // Unique per browser/device
            'allow_concurrent_sessions' => true // Flag to indicate multiple sessions are allowed
        ]);
        
        // Check for previous sessions and create notification if needed
        $this->handleSessionNotification($user, $request);
        
        // Update user login information - store last login, not current session
        try {
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Could not update user login info: ' . $e->getMessage());
        }
        
        // Log multiple session info for monitoring (optional)
        \Illuminate\Support\Facades\Log::info('User login - concurrent sessions allowed', [
            'user_id' => $user->id,
            'username' => $user->username,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'browser_fingerprint' => $browserFingerprint
        ]);
        
        // Clean redirect without excessive headers
        return redirect()->intended($this->redirectPath());
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
        
        // Check if there's a user input from previous attempt to show remaining attempts
        $userLoginAttempts = null;
        $remainingAttempts = null;
        $accountLocked = false;
        $lockExpiry = null;
        
        if ($request->has('check_attempts') && $request->input('username')) {
            $loginField = $request->input('username');
            
            // Find user by username or email
            $user = \App\Models\User::where(function($query) use ($loginField) {
                $isEmail = filter_var($loginField, FILTER_VALIDATE_EMAIL);
                if ($isEmail) {
                    $query->where('email', $loginField)->orWhere('username', $loginField);
                } else {
                    $query->where('username', $loginField)->orWhere('email', $loginField);
                }
            })->first();
            
            if ($user) {
                $userLoginAttempts = $user->login_attempts;
                if ($user->isLocked()) {
                    $accountLocked = true;
                    $lockExpiry = $user->locked_until->diffForHumans();
                    $remainingAttempts = 0;
                } else {
                    $remainingAttempts = max(0, 5 - $user->login_attempts);
                }
            }
        }
        
        return view('auth.login_fresh', compact(
            'sessionNotifications', 
            'rememberedLogin',
            'userLoginAttempts',
            'remainingAttempts',
            'accountLocked',
            'lockExpiry'
        ));
    } 

    /**
     * Check if current session is valid for the authenticated user
     * This handles multiple concurrent sessions gracefully
     */
    public function validateUserSession(\Illuminate\Http\Request $request)
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return response()->json(['valid' => false, 'reason' => 'not_authenticated']);
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        $sessionUserId = session('auth_user_id');
        $allowConcurrent = session('allow_concurrent_sessions', false);

        // If concurrent sessions are allowed, just verify basic session integrity
        if ($allowConcurrent) {
            if ($sessionUserId && $sessionUserId == $user->id) {
                return response()->json(['valid' => true, 'concurrent_allowed' => true]);
            }
        }

        // Additional session validation if needed
        $sessionValid = $sessionUserId === $user->id;
        
        return response()->json([
            'valid' => $sessionValid,
            'concurrent_allowed' => $allowConcurrent,
            'session_browser_id' => session('browser_session_id'),
            'user_id' => $user->id
        ]);
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
            $lockExpiry = $user->locked_until ? $user->locked_until->diffForHumans() : 'later';
            $lockExpiryMinutes = $user->locked_until ? $user->locked_until->diffInMinutes(now()) : 10;
            
            // Handle expired locks (negative minutes) - should unlock automatically
            if ($lockExpiryMinutes <= 0) {
                // Lock has expired, unlock the user
                $user->update([
                    'locked_until' => null,
                    'login_attempts' => 0
                ]);
                
                // Continue with login attempt since lock has expired
            } else {
                // Account is still locked
                $durationText = $lockExpiryMinutes < 60 
                    ? round($lockExpiryMinutes) . ' minute' . ($lockExpiryMinutes != 1 ? 's' : '')
                    : round($lockExpiryMinutes / 60, 1) . ' hour' . ($lockExpiryMinutes >= 120 ? 's' : '');
                
                $lockMessage = "Your account is temporarily locked due to multiple failed login attempts. Please try again {$lockExpiry} or contact support for immediate assistance.";
                
                if ($request->expectsJson() || $request->wantsJson()) {
                    \Illuminate\Support\Facades\Log::info('Returning JSON response for locked account', [
                        'error_type' => 'account_locked',
                        'locked_until' => $user->locked_until ? $user->locked_until->toISOString() : null,
                        'unlock_time_human' => $lockExpiry,
                        'unlock_time_minutes' => $lockExpiryMinutes,
                        'duration_text' => $durationText
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => $lockMessage,
                        'error_type' => 'account_locked',
                        'locked_until' => $user->locked_until ? $user->locked_until->toISOString() : null,
                        'unlock_time_human' => $lockExpiry,
                        'unlock_time_minutes' => $lockExpiryMinutes,
                        'duration_text' => $durationText,
                        'remaining_attempts' => 0
                    ], 401);
                }
                
                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors([
                        $this->username() => $lockMessage,
                    ])
                    ->with('error_type', 'account_locked')
                    ->with('unlock_time', $lockExpiry)
                    ->with('unlock_time_minutes', $lockExpiryMinutes)
                    ->with('duration_text', $durationText)
                    ->with('remaining_attempts', 0);
            }
        }

        // If user is not locked but has failed attempts, let them know remaining attempts  
        if ($user->login_attempts > 0 && $user->login_attempts < 5) {
            $remainingAttempts = 5 - $user->login_attempts;
            session()->flash('warning_attempts', "You have {$remainingAttempts} login attempt" . ($remainingAttempts > 1 ? 's' : '') . " remaining before your account will be temporarily locked.");
            session()->flash('remaining_attempts', $remainingAttempts);
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
        
        // Refresh the user to get updated login_attempts count
        $user->refresh();
        
        $remainingAttempts = max(0, 5 - $user->login_attempts);
        
        // Create more specific error messages
        if ($remainingAttempts > 0) {
            $errorMessage = "Invalid password. Please check your password and try again. You have {$remainingAttempts} attempt" . ($remainingAttempts > 1 ? 's' : '') . " remaining before your account is temporarily locked.";
            $errorType = 'invalid_password';
        } else {
            $lockMinutes = 10; // Lock for 10 minutes
            $errorMessage = "Account temporarily locked for {$lockMinutes} minutes due to multiple failed login attempts. Please try again later or contact support for immediate assistance.";
            $errorType = 'account_locked_now';
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            \Illuminate\Support\Facades\Log::info('Returning JSON response for failed login', [
                'error_type' => $errorType,
                'remaining_attempts' => $remainingAttempts,
                'user_locked' => $user->isLocked(),
                'lock_duration_minutes' => $remainingAttempts <= 0 ? 10 : null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_type' => $errorType,
                'remaining_attempts' => $remainingAttempts,
                'user_found' => true,
                'password_issue' => true,
                'locked_until' => $user->locked_until ? $user->locked_until->toISOString() : null,
                'lock_duration_minutes' => $remainingAttempts <= 0 ? 10 : null
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
        try {
            if (config('session.driver') === 'database') {
                // Clear sessions specifically belonging to this user
                $userSessionsDeleted = \Illuminate\Support\Facades\DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->delete();
                    
                // ALSO clean up old guest sessions (older than 1 hour) to prevent accumulation
                $guestSessionsDeleted = \Illuminate\Support\Facades\DB::table('sessions')
                    ->whereNull('user_id')
                    ->where('last_activity', '<', time() - 3600) // Older than 1 hour
                    ->delete();
                    
                // Log the action
                \Illuminate\Support\Facades\Log::info('Cleared database sessions for user and old guest sessions', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'user_sessions_deleted' => $userSessionsDeleted,
                    'guest_sessions_deleted' => $guestSessionsDeleted
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
     * Clean up orphaned and old guest sessions to prevent database bloat.
     * This method should be called periodically (e.g., via scheduled task).
     *
     * @return array Statistics about cleaned sessions
     */
    public static function cleanupOrphanedSessions()
    {
        $stats = [
            'old_guest_sessions' => 0,
            'orphaned_user_sessions' => 0,
            'expired_sessions' => 0
        ];

        try {
            if (config('session.driver') === 'database') {
                // 1. Clean old guest sessions (older than 2 hours)
                $stats['old_guest_sessions'] = \Illuminate\Support\Facades\DB::table('sessions')
                    ->whereNull('user_id')
                    ->where('last_activity', '<', time() - 7200) // 2 hours ago
                    ->delete();

                // 2. Clean orphaned user sessions (user_id doesn't exist in users table)
                $stats['orphaned_user_sessions'] = \Illuminate\Support\Facades\DB::table('sessions')
                    ->whereNotNull('user_id')
                    ->whereNotIn('user_id', function($query) {
                        $query->select('id')->from('users');
                    })
                    ->delete();

                // 3. Clean expired sessions based on session lifetime
                $sessionLifetime = config('session.lifetime', 120) * 60; // Convert minutes to seconds
                $stats['expired_sessions'] = \Illuminate\Support\Facades\DB::table('sessions')
                    ->where('last_activity', '<', time() - $sessionLifetime)
                    ->delete();

                \Illuminate\Support\Facades\Log::info('Session cleanup completed', $stats);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Session cleanup failed', [
                'error' => $e->getMessage(),
                'stats' => $stats
            ]);
        }

        return $stats;
    }

    /**
     * Log the user out of the application with complete session destruction.
     * Handles both GET and POST requests without requiring authentication middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(\Illuminate\Http\Request $request)
    {
        // Check if user is actually logged in
        if (!\Illuminate\Support\Facades\Auth::check()) {
            // User is not logged in, clear any remaining session data and redirect
            $this->completeSessionDestruction($request);
            return redirect()->route('login', ['t' => time()])
                ->with('info', 'You were already logged out.');
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        $userId = $user->id;
        $username = $user->username ?? 'Unknown';
        $sessionId = session()->getId();

        // Log the logout attempt for security
        \Illuminate\Support\Facades\Log::info('User logout initiated', [
            'user_id' => $userId,
            'username' => $username,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'session_id' => $sessionId
        ]);

        // Store user info before logout for logging
        $userInfo = [
            'user_id' => $userId,
            'username' => $username,
            'ip' => $request->ip(),
            'session_id' => $sessionId
        ];

        // Create logout notification (before session destruction)
        try {
            if (class_exists('\App\Models\UserSessionNotification')) {
                \App\Models\UserSessionNotification::create([
                    'user_id' => $userId,
                    'type' => 'logout',
                    'title' => 'User Logged Out',
                    'message' => 'You have successfully logged out from your account.',
                    'new_login_ip' => $request->ip(),
                    'action_taken' => 'manual_logout'
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Could not create logout notification: ' . $e->getMessage());
        }

        // STEP 1: Perform the actual logout (removes authentication)
        $this->guard()->logout();

        // STEP 2: Complete session destruction
        $this->completeSessionDestruction($request);

        // STEP 3: Clear user-specific cache AFTER session invalidation
        try {
            $this->clearUserCache($userId);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Cache cleanup warning during logout: ' . $e->getMessage());
        }

        // STEP 4: Clear any remember tokens if they exist
        try {
            if ($user && method_exists($user, 'setRememberToken')) {
                $user->setRememberToken(null);
                $user->save();
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Remember token cleanup warning: ' . $e->getMessage());
        }

        // Log successful logout
        \Illuminate\Support\Facades\Log::info('User logged out successfully - session completely destroyed', $userInfo);

        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully - session destroyed',
                'redirect_url' => route('login'),
                'clear_cache' => true,
                'session_destroyed' => true,
                'cache_version' => time()
            ]);
        }

        // Create response with complete cache invalidation headers
        $redirectUrl = route('login');
        $response = redirect($redirectUrl)->with([
            'success' => 'You have been logged out successfully. Your session has been completely destroyed.',
            'logout_completed' => true,
            'session_destroyed' => true
        ]);
        
        // Add the most comprehensive cache control headers possible
        return $this->addSessionDestructionHeaders($response);
    }

    /**
     * Complete session destruction - destroys all session data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function completeSessionDestruction(\Illuminate\Http\Request $request)
    {
        try {
            // Get session ID before destruction for logging
            $sessionId = session()->getId();
            
            // CRITICAL: Completely invalidate the session
            $request->session()->invalidate();
            
            // CRITICAL: Regenerate CSRF token
            $request->session()->regenerateToken();
            
            // CRITICAL: Flush all session data (Laravel's most aggressive session clearing)
            $request->session()->flush();
            
            // CRITICAL: Start fresh session to prevent any lingering data
            $request->session()->regenerate(true);
            
            // CRITICAL: Clear any session cookies
            if ($request->hasSession()) {
                $sessionName = config('session.cookie');
                if ($sessionName) {
                    setcookie($sessionName, '', time() - 3600, '/', '', false, true);
                }
            }
            
            \Illuminate\Support\Facades\Log::info('Session completely destroyed', [
                'old_session_id' => $sessionId,
                'new_session_id' => session()->getId(),
                'ip' => $request->ip()
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Critical error during session destruction: ' . $e->getMessage());
            
            // Emergency fallback - try to clear session manually
            try {
                session()->flush();
                session()->regenerate(true);
            } catch (\Exception $fallbackException) {
                \Illuminate\Support\Facades\Log::critical('Emergency session cleanup also failed: ' . $fallbackException->getMessage());
            }
        }
    }

    /**
     * Clear all user-specific cache data
     *
     * @param  int  $userId
     * @return void
     */
    private function clearUserCache($userId)
    {
        $cacheKeys = [
            'user_' . $userId,
            'user_session_' . $userId,
            'user_data_' . $userId,
            'user_preferences_' . $userId,
            'user_settings_' . $userId,
            'user_permissions_' . $userId,
            'trusted_ips_user_' . $userId,
            'user_session_settings_notifications_' . $userId,
            'user_session_settings_security_' . $userId
        ];

        foreach ($cacheKeys as $key) {
            try {
                \Illuminate\Support\Facades\Cache::forget($key);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Could not clear cache key {$key}: " . $e->getMessage());
            }
        }
    }

    /**
     * Add comprehensive headers to prevent any caching after logout
     *
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Http\Response
     */
    private function addSessionDestructionHeaders($response)
    {
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s T'));
        
        // Additional headers to ensure complete cache invalidation
        $response->headers->set('X-Logout-Completed', 'true');
        $response->headers->set('X-Session-Destroyed', 'true');
        $response->headers->set('X-Cache-Control', 'no-cache');
        $response->headers->set('Vary', 'Accept-Encoding');
        
        // Set cookies to expire immediately
        $response->withCookie(cookie('logout_timestamp', time(), -1));
        
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

    /**
     * Simple login method that handles CSRF issues gracefully
     */
    public function simpleLogin(Request $request)
    {
        try {
            // Validate input
            $credentials = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);

            // Allow login with email or username
            $loginField = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $loginCredentials = [
                $loginField => $credentials['email'],
                'password' => $credentials['password']
            ];

            // Find user first to check status
            $user = User::where($loginField, $credentials['email'])->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email/username or password.',
                    'error_type' => 'invalid_credentials'
                ], 401);
            }

            // Check if user is active
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been deactivated. Please contact support.',
                    'error_type' => 'account_deactivated'
                ], 401);
            }

            // Check for account locks
            if ($user->isLocked()) {
                $lockExpiry = $user->locked_until->diffForHumans();
                return response()->json([
                    'success' => false,
                    'message' => "Account is temporarily locked. Try again {$lockExpiry}.",
                    'error_type' => 'account_locked',
                    'lock_expiry' => $user->locked_until
                ], 401);
            }

            // Attempt authentication
            if (\Illuminate\Support\Facades\Auth::attempt($loginCredentials, $request->filled('remember'))) {
                $user = \Illuminate\Support\Facades\Auth::user();
                
                // Reset login attempts on successful login using direct database update
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'login_attempts' => 0,
                        'locked_until' => null
                    ]);
                
                // Check email verification
                if (!$user->email_verified_at) {
                    \Illuminate\Support\Facades\Auth::logout();
                    return response()->json([
                        'success' => false,
                        'message' => 'Please verify your email address before logging in.',
                        'error_type' => 'email_not_verified',
                        'email' => $user->email
                    ], 401);
                }

                // Regenerate session for security
                $request->session()->regenerate();
                
                // Set success session data
                session([
                    'login_success' => true,
                    'fresh_login' => true,
                    'user_id' => $user->id,
                    'login_timestamp' => time()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful! Redirecting to dashboard...',
                    'redirect' => route('user.dashboard'),
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email
                    ]
                ]);

            } else {
                // Invalid credentials - increment attempts
                $user->incrementLoginAttempts();
                
                $remainingAttempts = 5 - $user->login_attempts;
                $isLastAttempt = $remainingAttempts <= 1;
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email/username or password.',
                    'error_type' => 'invalid_password',
                    'remaining_attempts' => max(0, $remainingAttempts),
                    'is_last_attempt' => $isLastAttempt
                ], 401);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill in all required fields.',
                'errors' => $e->errors(),
                'error_type' => 'validation_error'
            ], 422);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Simple login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->only(['email'])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login. Please try again.',
                'error_type' => 'server_error'
            ], 500);
        }
    }

    /**
     * Handle session notification with enhanced features
     */
    private function handleSessionNotification($user, $request)
    {
        try {
            // Get user's notification preferences
            $settings = \Illuminate\Support\Facades\Cache::get("user_session_settings_{$user->id}", [
                'notify_new_login' => true,
                'notify_different_ip' => true,
                'notify_different_device' => true,
                'auto_logout_other_sessions' => false
            ]);
            
            // Skip notifications if user has disabled them
            if (!$settings['notify_new_login']) {
                return;
            }
            
            $currentIp = $request->ip();
            $currentDevice = $this->getDeviceInfo($request->userAgent());
            
            // Check if this IP is in trusted IPs
            $trustedIPs = \Illuminate\Support\Facades\Cache::get("trusted_ips_user_{$user->id}", []);
            $isTrustedIP = collect($trustedIPs)->pluck('ip')->contains($currentIp);
            
            if ($isTrustedIP && !$settings['notify_different_device']) {
                return; // Skip notification for trusted IP
            }
            
            // Check recent login from different IP or device
            $shouldNotify = false;
            $notificationMessage = '';
            
            // Check if last login was from different IP
            if ($settings['notify_different_ip'] && $user->last_login_ip && $user->last_login_ip !== $currentIp) {
                $shouldNotify = true;
                $notificationMessage = "New login detected from IP: {$currentIp}. Previous login was from: {$user->last_login_ip}";
            }
            
            // Check for different device (simplified check)
            $lastDevice = \Illuminate\Support\Facades\Cache::get("last_device_user_{$user->id}");
            if ($settings['notify_different_device'] && $lastDevice && $lastDevice !== $currentDevice) {
                $shouldNotify = true;
                $notificationMessage .= $notificationMessage ? " Also detected different device: {$currentDevice}" : "New device detected: {$currentDevice}";
            }
            
            // Store current device for next time
            \Illuminate\Support\Facades\Cache::put("last_device_user_{$user->id}", $currentDevice, now()->addDays(30));
            
            if ($shouldNotify) {
                // Check if we already notified about this IP in last 24 hours
                $recentNotification = \App\Models\UserSessionNotification::where('user_id', $user->id)
                    ->where('new_login_ip', $currentIp)
                    ->where('created_at', '>=', now()->subHours(24))
                    ->exists();
                
                if (!$recentNotification) {
                    \App\Models\UserSessionNotification::create([
                        'user_id' => $user->id,
                        'type' => 'new_login_detected',
                        'title' => 'New Login Detected',
                        'message' => $notificationMessage,
                        'new_login_ip' => $currentIp,
                        'new_login_device' => $currentDevice,
                        'new_login_location' => $this->getLocationFromIP($currentIp),
                        'old_session_ip' => $user->last_login_ip,
                        'is_read' => false,
                        'session_id' => session()->getId()
                    ]);
                    
                    // Add flash message for immediate user notification
                    session()->flash('session_notification', [
                        'type' => 'info',
                        'title' => 'New Login Detected',
                        'message' => 'We detected a login from a new location or device. Check your session notifications for details.',
                        'action_url' => route('user.sessions.notifications'),
                        'action_text' => 'View Details'
                    ]);
                }
            }
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to handle session notification: ' . $e->getMessage());
        }
    }
}
