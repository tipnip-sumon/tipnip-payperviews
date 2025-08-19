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
    protected $redirectTo = '/user/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm(Request $request)
    {
        return view('auth.login_fresh');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Simple session regeneration for security
        $request->session()->regenerate();
        
        // Reset any login attempts on successful authentication
        if (method_exists($user, 'resetLoginAttempts')) {
            $user->resetLoginAttempts();
        }
        
        // Update user login information
        try {
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Could not update user login info: ' . $e->getMessage());
        }

        \Illuminate\Support\Facades\Log::info('User authenticated successfully', [
            'user_id' => $user->id,
            'login_attempts_reset' => true
        ]);

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username'; // Use username field for both username and email
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:1',
        ], [
            'username.required' => 'Please enter your username or email address.',
            'password.required' => 'Please enter your password.',
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Login attempt', [
            'username_field' => $request->input('username'),
            'has_password' => $request->filled('password'),
            'ip' => $request->ip()
        ]);

        $this->validateLogin($request);

        // Get the user by username or email first
        $loginField = $request->input('username'); // Use 'username' field from form
        
        // Determine if the input is an email or username
        $isEmail = filter_var($loginField, FILTER_VALIDATE_EMAIL);
        
        // Query user by either email or username
        $user = User::where(function($query) use ($loginField, $isEmail) {
            if ($isEmail) {
                $query->where('email', $loginField)
                      ->orWhere('username', $loginField);
            } else {
                $query->where('username', $loginField)
                      ->orWhere('email', $loginField);
            }
        })->first();

        if (!$user) {
            \Illuminate\Support\Facades\Log::info('User not found', [
                'login_field' => $loginField,
                'is_email' => $isEmail
            ]);
            
            $errorMessage = $isEmail 
                ? 'No account found with this email address.'
                : 'Username not found.';
            
            return $this->sendFailedLoginResponse($request, $errorMessage);
        }

        // Check if account is locked BEFORE attempting login
        if ($user->isLocked()) {
            $lockDuration = now()->diffInMinutes($user->locked_until);
            
            \Illuminate\Support\Facades\Log::info('Login attempt on locked account', [
                'user_id' => $user->id,
                'login_attempts' => $user->login_attempts,
                'locked_until' => $user->locked_until,
                'minutes_remaining' => $lockDuration
            ]);
            
            return $this->sendFailedLoginResponse($request, 
                "Account is locked due to too many failed login attempts. Please try again in {$lockDuration} minutes.");
        }

        \Illuminate\Support\Facades\Log::info('User found', [
            'user_id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'status' => $user->status,
            'login_attempts' => $user->login_attempts ?? 0,
            'locked_until' => $user->locked_until,
            'remaining_attempts' => $user->getRemainingAttempts()
        ]);

        // Check if user is active (allow status 1, or if status field doesn't exist)
        if (isset($user->status) && $user->status != 1) {
            \Illuminate\Support\Facades\Log::info('User account inactive', [
                'user_id' => $user->id,
                'status' => $user->status
            ]);
            
            // Don't increment attempts for inactive accounts - not a login failure
            return $this->sendFailedLoginResponse($request, 'Your account has been suspended.');
        }

        // Attempt to authenticate with the found user's credentials
        $credentials = [
            'email' => $user->email, // Always use email for authentication
            'password' => $request->input('password')
        ];

        if ($this->guard()->attempt($credentials, $request->filled('remember'))) {
            // SUCCESS: Reset login attempts on successful login
            $user->resetLoginAttempts();
            
            \Illuminate\Support\Facades\Log::info('Login successful - attempts reset', [
                'user_id' => $user->id,
                'username' => $user->username
            ]);
            
            return $this->sendLoginResponse($request);
        }

        // FAILED: Increment login attempts and potentially lock account
        $user->addFailedLoginAttempt();
        
        $remainingAttempts = $user->getRemainingAttempts();
        $isNowLocked = $user->isLocked();
        
        \Illuminate\Support\Facades\Log::info('Login failed - password incorrect', [
            'user_id' => $user->id,
            'total_attempts' => $user->fresh()->login_attempts,
            'remaining_attempts' => $remainingAttempts,
            'is_locked' => $isNowLocked,
            'locked_until' => $user->fresh()->locked_until
        ]);

        if ($isNowLocked) {
            return $this->sendFailedLoginResponse($request, 
                'Too many failed login attempts. Your account has been locked for 10 minutes.');
        } elseif ($remainingAttempts <= 1) {
            return $this->sendFailedLoginResponse($request, 
                "Invalid password. Warning: {$remainingAttempts} attempt remaining before account lock.");
        } else {
            return $this->sendFailedLoginResponse($request, 
                "Invalid password. You have {$remainingAttempts} attempts remaining.");
        }
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('username', 'password');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $credentials
     * @return bool
     */
    protected function attemptLogin(Request $request, array $credentials = null)
    {
        $credentials = $credentials ?: $this->credentials($request);
        
        return $this->guard()->attempt(
            $credentials, $request->filled('remember')
        );
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $message
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request, $message = null, $user = null)
    {
        $message = $message ?: 'These credentials do not match our records.';
        
        $responseData = [
            'success' => false,
            'message' => $message
        ];

        // Add attempt information if user is provided
        if ($user) {
            $responseData['login_attempts'] = $user->login_attempts ?? 0;
            $responseData['remaining_attempts'] = $user->getRemainingAttempts();
            $responseData['is_locked'] = $user->isLocked();
            
            if ($user->isLocked() && $user->locked_until) {
                $responseData['locked_until'] = $user->locked_until->toISOString();
                $responseData['unlock_time_human'] = $user->locked_until->diffForHumans();
            }
        }
        
        if ($request->expectsJson()) {
            return response()->json($responseData, 401);
        }

        return redirect()->back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors([
                'username' => $message,
            ])
            ->with('login_attempts_info', $responseData);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out successfully']);
        }

        return redirect('/');
    }
}