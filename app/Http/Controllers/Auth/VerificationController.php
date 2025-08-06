<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/user/dashboard';
    // protected $redirectTo = '/home'; //RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Only require auth for show and resend, not for verify
        $this->middleware('auth')->only(['show', 'resend']);
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                        ? redirect($this->redirectTo)
                        : view('auth.verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $userId = $request->route('id');
        $hash = $request->route('hash');
        
        // Find user by ID with better error handling
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            \Illuminate\Support\Facades\Log::warning('Email verification attempted for non-existent user', [
                'user_id' => $userId,
                'hash' => $hash,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);
            
            return $this->redirectToVerificationPageWithError('The verification link is invalid. The user account may have been deleted or the link is corrupted.');
        }
        
        // Verify the hash matches
        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            \Illuminate\Support\Facades\Log::warning('Email verification failed due to hash mismatch', [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'provided_hash' => $hash,
                'expected_hash' => sha1($user->getEmailForVerification()),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return $this->redirectToVerificationPageWithError('The verification link is invalid or has expired. Please request a new verification email.');
        }

        // Check if email is already verified
        if ($user->hasVerifiedEmail()) {
            // If email is already verified, still log them in for convenience
            if (!\Illuminate\Support\Facades\Auth::check()) {
                \Illuminate\Support\Facades\Auth::login($user, true);
                
                // Log the auto-login for already verified user
                \Illuminate\Support\Facades\Log::info('Auto-login for already verified user', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'already_verified' => true
                ]);
                
                return redirect()->intended($this->redirectTo)->with('info', 'Welcome back! Your email was already verified and you are now logged in.');
            }
            
            // If already authenticated, just redirect with message
            return redirect()->intended($this->redirectTo)->with('info', 'Your email is already verified! Welcome back.');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) { 
            event(new Verified($user));
            
            // Update the ev (email verification) status in the database
            $user->update(['ev' => 1]);
            
            // Send notification about successful email verification
            try {
                if (function_exists('notifyUserEmailVerification')) {
                    notifyUserEmailVerification($user->id, true);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send email verification notification: ' . $e->getMessage());
            }
            
            // Automatically log in the user after email verification
            \Illuminate\Support\Facades\Auth::login($user, true); // true for "remember me" 
            
            // Log the verification and auto-login for security
            \Illuminate\Support\Facades\Log::info('Email verified successfully and user auto-logged in', [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'auto_login' => true
            ]);
            
            // Redirect to home/dashboard with success message
            return redirect()->intended($this->redirectTo)->with('success', 'Welcome! Your email has been verified and you are now logged in.');
        }

        return $this->redirectToLoginWithSuccess('Email verified successfully! You can now log in to your account.');
    }
    
    /**
     * Redirect to login page with success message
     */
    private function redirectToLoginWithSuccess($message)
    {
        return redirect()->route('login')->with('success', $message);
    }
    
    /**
     * Redirect to verification notice or login with error
     */
    private function redirectToVerificationPageWithError($message)
    {
        // If user is authenticated, redirect to verification notice
        if (\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('verification.notice')->with('error', $message);
        }
        
        // If not authenticated, redirect to login
        return redirect()->route('login')->with('error', $message);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectTo)->with('info', 'Email already verified!');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent successfully!');
    }
    
    /**
     * Resend verification email for non-authenticated users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resendPublic(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
        
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }
        
        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'This email address is already verified! You can log in now.');
        }
        
        // Send verification email
        $user->sendEmailVerificationNotification();
        
        // Log the resend request for security
        \Illuminate\Support\Facades\Log::info('Email verification resent', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return back()->with('success', 'Verification link has been sent to your email address!');
    }
}
