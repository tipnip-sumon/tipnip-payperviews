<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Safe method to get video views count
        $videoViewsCount = 0;
        $totalEarnings = 0;
        
        try {
            if (method_exists($user, 'videoViews')) {
                $videoViewsCount = $user->videoViews()->count();
                $totalEarnings = $user->videoViews()->sum('earned_amount');
            }
        } catch (\Exception $e) {
            Log::error('Video views error: ' . $e->getMessage());
        }
        
        $profileStats = [
            'total_videos_watched' => $videoViewsCount,
            'total_earnings' => $totalEarnings,
            'total_deposits' => $user->total_deposits ?? 0,
            'total_withdrawals' => $user->total_withdrawals ?? 0,
            'referral_earnings' => $user->referral_earnings ?? 0,
            'account_balance' => $user->balance ?? 0,
            'join_date' => $user->created_at,
            'last_login' => $user->last_login_at ?? null,
        ];

        return view('frontend.profile.index', [
            'pageTitle' => 'My Profile',
            'user' => $user,
            'profileStats' => $profileStats
        ]);
    }

    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        
        return view('frontend.profile.edit', [
            'pageTitle' => 'Edit Profile',
            'user' => $user
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        try {
            // Get the authenticated user ID
            $userId = Auth::id();
            
            // Find the user in the database
            $user = User::findOrFail($userId);
            
            // Validate the request
                    $user = Auth::user();
        
        $request->validate([                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                
                // Store new avatar
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }

            // Update user profile fields
            $user->firstname = $request->input('firstname');
            $user->lastname = $request->input('lastname');
            $user->mobile = $request->input('phone');
            $user->country = $request->input('country');
            
            // Save the changes
            $user->save();

            // Log the successful update
            Log::info('Profile updated successfully for user: ' . $user->id, [
                'user_id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'mobile' => $user->mobile,
                'country' => $user->country,
                'avatar' => $user->avatar
            ]);

            return redirect()->route('profile.edit')
                ->with('success', 'Profile updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Show the password change form.
     */
    public function showPasswordForm()
    {
        return view('frontend.profile.password', [
            'pageTitle' => 'Change Password'
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([ 
                'current_password' => 'required|string',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'different:current_password'
                ],
            ]);
            
            $userId = Auth::id();
            $user = User::findOrFail($userId);
            
            // Check if current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['current_password' => ['Current password is incorrect.']]
                    ], 422);
                }
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            
            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password updated successfully!'
                ]);
            }

            return redirect()->route('profile.password')
                ->with('success', 'Password updated successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating password.'
                ], 500);
            }
            return back()->withErrors(['error' => 'An error occurred while updating password.']);
        }
    }

    /**
     * Show the security settings.
     */
    public function security()
    {
        $user = Auth::user();
        
        return view('frontend.profile.security', [
            'pageTitle' => 'Security Settings',
            'user' => $user
        ]);
    }

    /**
     * Delete user avatar.
     */
    public function deleteAvatar()
    {
        try {
            $userId = Auth::id();
            $user = User::findOrFail($userId);
            
            Log::info('Attempting to delete avatar for user: ' . $user->id);
            
            if ($user->avatar) {
                Log::info('Current avatar path: ' . $user->avatar);
                
                // Check if file exists and delete it
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                    Log::info('Avatar file deleted from storage');
                } else {
                    Log::warning('Avatar file not found in storage: ' . $user->avatar);
                }
                
                // Update user record to remove avatar
                $user->avatar = null;
                $user->save();
                Log::info('User avatar field updated to null');
            } else {
                Log::info('No avatar to delete');
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Avatar deleted successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Avatar deleted successfully!');
            
        } catch (\Exception $e) {
            Log::error('Avatar deletion error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to delete avatar: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete avatar. Please try again.');
        }
    }

    /**
     * Request paid email change with two-step verification
     * Step 1: Send OTP to current email for identity verification
     */
    public function requestEmailChange(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'new_email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'required|string'
        ], [
            'new_email.required' => 'New email address is required',
            'new_email.email' => 'Please enter a valid email address',
            'new_email.unique' => 'This email address is already in use',
            'password.required' => 'Password is required for verification'
        ]);

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid password. Please try again.');
        }

        // Check if user has sufficient balance
        $emailChangeFee = 2.00;
        $userBalance = ($user->deposit_wallet ?? 0) + ($user->interest_wallet ?? 0);
        
        if ($userBalance < $emailChangeFee) {
            return back()->with('error', 'Insufficient balance. You need $' . number_format($emailChangeFee, 2) . ' to change your email address.');
        }

        // Check if there's already a pending email change
        if ($user->pending_email_change && $user->email_change_step !== 'initial') {
            return back()->with('error', 'You already have a pending email change request. Please complete or cancel it first.');
        }

        try {
            // Generate OTP for current email verification (Step 1)
            $currentEmailOtp = random_int(100000, 999999);
            
            // Store pending change data
            $user->pending_email_change = $request->new_email;
            $user->current_email_otp = $currentEmailOtp;
            $user->current_email_otp_sent_at = now();
            $user->current_email_verified = false;
            // Keep email_change_step = 'initial' until OTP is verified
            $user->email_change_requested_at = now();
            $user->save();

            // Send OTP to CURRENT email address for identity verification
            $this->sendCurrentEmailVerification($user->email, $currentEmailOtp, $user, $request->new_email);

            return back()->with('success', 'Security verification required! We have sent a 6-digit verification code to your current email address (' . $user->email . '). Please check your email and enter the code to verify your identity before we can proceed with the email change.');
            
        } catch (\Exception $e) {
            // Clear pending change data since email failed
            $this->clearEmailChangeData($user);
            
            Log::error('Email change request error: ' . $e->getMessage());
            
            // Check if it's an email-specific error
            $errorMessage = 'Unable to send verification code to your current email address (' . $user->email . '). ';
            
            if (str_contains($e->getMessage(), 'Connection') || str_contains($e->getMessage(), 'timeout')) {
                $errorMessage .= 'Email server connection failed. Please check your internet connection and try again.';
            } elseif (str_contains($e->getMessage(), 'authentication') || str_contains($e->getMessage(), 'login')) {
                $errorMessage .= 'Email authentication failed. Please contact support for assistance.';
            } elseif (str_contains($e->getMessage(), 'invalid') || str_contains($e->getMessage(), 'rejected')) {
                $errorMessage .= 'Email address appears to be invalid or rejected by the email server.';
            } else {
                $errorMessage .= 'Email service is temporarily unavailable. Please try again in a few minutes or contact support if the problem persists.';
            }
            
            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Verify email change - handles both steps of verification
     */
    public function verifyEmailChange(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'verification_code' => 'required|string|min:6'
        ]);

        if (!$user->pending_email_change) {
            return back()->with('error', 'No pending email change found.');
        }

        // Determine which step we're in based on database values
        if ($user->email_change_step === 'initial' && $user->pending_email_change && $user->current_email_otp) {
            // Step 1: Verify current email OTP
            return $this->verifyCurrentEmail($request, $user);
        } elseif ($user->email_change_step === 'current_verified' && $user->new_email_verification_token) {
            // Step 2: This should be handled by the email link, not here
            return back()->with('error', 'Please check your new email and click the verification link to complete the process.');
        } else {
            return back()->with('error', 'Invalid email change state. Please start the process again.');
        }
    }

    /**
     * Step 1: Verify current email OTP
     */
    private function verifyCurrentEmail(Request $request, $user)
    {
        // Check if OTP is correct
        if ($user->current_email_otp !== $request->verification_code) {
            return back()->with('error', 'Invalid verification code for current email.');
        }

        // Check if OTP is not expired (valid for 10 minutes)
        $otpSentTime = $user->current_email_otp_sent_at;
        if (!$otpSentTime || Carbon::parse($otpSentTime)->addMinutes(10)->isPast()) {
            $this->clearEmailChangeData($user);
            return back()->with('error', 'Verification code has expired. Please start the process again.');
        }

        try {
            // Generate verification token for new email (Step 2)
            $newEmailToken = hash('sha256', $user->id . $user->pending_email_change . now()->timestamp . random_bytes(32));
            
            // Update user state to Step 2
            $user->current_email_verified = true;
            $user->new_email_verification_token = $newEmailToken;
            $user->new_email_token_sent_at = now();
            $user->email_change_step = 'current_verified'; // Step 2: waiting for new email verification
            $user->save();

            // Send verification link to NEW email address
            $this->sendNewEmailVerification($user->pending_email_change, $newEmailToken, $user);

            return back()->with('success', 'Current email verified successfully! 🎉 We have now sent a verification link to your new email address (' . $user->pending_email_change . '). Please check your new email and click the verification link to complete the email change process.');
            
        } catch (\Exception $e) {
            Log::error('New email verification sending failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send verification to new email. Please try again.');
        }
    }

    /**
     * Step 2: Verify new email via verification link (this will be called from email link)
     * This route is accessible without authentication since users might click the link from a different device
     */
    public function verifyNewEmailLink($token)
    {
        // Debug logging
        Log::info('Email verification link accessed', [
            'token' => $token,
            'timestamp' => now()
        ]);

        $user = User::where('new_email_verification_token', $token)
                  ->where('email_change_step', 'current_verified')
                  ->first();

        if (!$user) {
            Log::warning('Email verification failed - user not found', [
                'token' => $token,
                'searched_step' => 'current_verified'
            ]);
            
            // Check if token exists with any step
            $userAnyStep = User::where('new_email_verification_token', $token)->first();
            if ($userAnyStep) {
                Log::info('Token found but wrong step', [
                    'user_id' => $userAnyStep->id,
                    'current_step' => $userAnyStep->email_change_step,
                    'expected_step' => 'current_verified'
                ]);
            }
            
            return redirect()->route('login')->with('error', 'Invalid or expired verification link. Please log in to your account and start the email change process again.');
        }

        // Check if token is not expired (valid for 24 hours)
        $tokenSentTime = $user->new_email_token_sent_at;
        if (!$tokenSentTime || Carbon::parse($tokenSentTime)->addHours(24)->isPast()) {
            Log::warning('Email verification failed - token expired', [
                'user_id' => $user->id,
                'token_sent_at' => $tokenSentTime,
                'current_time' => now()
            ]);
            
            $this->clearEmailChangeData($user);
            return redirect()->route('login')->with('error', 'Verification link has expired. Please log in and start the email change process again.');
        }

        Log::info('Email verification successful', [
            'user_id' => $user->id,
            'old_email' => $user->email,
            'new_email' => $user->pending_email_change
        ]);

        // If user is not logged in, log them in automatically for this verification
        if (!Auth::check()) {
            Auth::login($user);
        }

        // Verify that the logged-in user matches the user whose email is being verified
        if (Auth::id() !== $user->id) {
            Auth::logout();
            Auth::login($user);
        }

        return $this->completeEmailChange($user);
    }

    /**
     * Complete the email change process
     */
    private function completeEmailChange($user)
    {
        try {
            DB::beginTransaction();
            
            $emailChangeFee = 2.00;
            $oldEmail = $user->email;
            $newEmail = $user->pending_email_change;

            // Deduct fee from user's balance
            $freshUser = User::find($user->id);
            $depositWallet = $freshUser->deposit_wallet ?? 0;
            $interestWallet = $freshUser->interest_wallet ?? 0;
            
            if ($depositWallet >= $emailChangeFee) {
                $freshUser->deposit_wallet -= $emailChangeFee;
            } else {
                $interestWallet -= ($emailChangeFee - $depositWallet);
                $freshUser->deposit_wallet = 0;
                $freshUser->interest_wallet = $interestWallet;
            }

            // Update email and clear all pending change data
            $freshUser->email = $newEmail;
            $freshUser->email_verified_at = now(); // Mark new email as verified
            $this->clearEmailChangeData($freshUser);
            $freshUser->save();

            // Create transaction record
            $transaction = new \App\Models\Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $emailChangeFee;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->trx = getTrx();
            $transaction->wallet_type = 'email_change';
            $transaction->remark = 'email_change';
            $transaction->details = 'Email change fee: ' . $oldEmail . ' → ' . $newEmail;
            $transaction->post_balance = ($freshUser->deposit_wallet ?? 0) + ($freshUser->interest_wallet ?? 0);
            $transaction->save();

            DB::commit();

            return redirect()->route('profile.security')->with('success', '🎉 Email address changed successfully! Your new email (' . $newEmail . ') is now active and verified. Fee of $' . number_format($emailChangeFee, 2) . ' has been deducted from your account.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Email change completion error: ' . $e->getMessage());
            return redirect()->route('profile.security')->with('error', 'Failed to complete email change. Please contact support.');
        }
    }

    /**
     * Request paid username change with verification
     */
    public function requestUsernameChange(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'new_username' => 'required|string|min:3|max:50|unique:users,username,' . $user->id . '|regex:/^[a-zA-Z0-9_]+$/',
            'password' => 'required|string'
        ], [
            'new_username.required' => 'New username is required',
            'new_username.min' => 'Username must be at least 3 characters',
            'new_username.max' => 'Username cannot exceed 50 characters',
            'new_username.unique' => 'This username is already taken',
            'new_username.regex' => 'Username can only contain letters, numbers, and underscores',
            'password.required' => 'Password is required for verification'
        ]);

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid password. Please try again.');
        }

        // Check if user has sufficient balance
        $usernameChangeFee = 5.00;
        $userBalance = ($user->deposit_wallet ?? 0) + ($user->interest_wallet ?? 0);
        
        if ($userBalance < $usernameChangeFee) {
            return back()->with('error', 'Insufficient balance. You need $' . number_format($usernameChangeFee, 2) . ' to change your username.');
        }

        // Check if there's already a pending username change
        if ($user->pending_username_change) {
            return back()->with('error', 'You already have a pending username change request. Please complete or cancel it first.');
        }

        try {
            // Generate verification code
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store pending change data
            $user->pending_username_change = $request->new_username;
            $user->username_change_token = $verificationCode;
            $user->username_change_requested_at = now();
            $user->save();

            // Send verification email
            $this->sendUsernameChangeVerification($user->email, $verificationCode, $user, $request->new_username);

            return back()->with('success', 'Verification code sent to your email address. Please check your email and enter the code to complete the username change. Fee of $' . number_format($usernameChangeFee, 2) . ' will be deducted upon verification.');
            
        } catch (\Exception $e) {
            // Clear pending change data since email failed
            $user->pending_username_change = null;
            $user->username_change_token = null;
            $user->username_change_requested_at = null;
            $user->save();
            
            Log::error('Username change request error: ' . $e->getMessage());
            
            // Check if it's an email-specific error
            $errorMessage = 'Unable to send verification email to ' . $user->email . '. ';
            
            if (str_contains($e->getMessage(), 'Connection') || str_contains($e->getMessage(), 'timeout')) {
                $errorMessage .= 'Email server connection failed. Please check your internet connection and try again.';
            } elseif (str_contains($e->getMessage(), 'authentication') || str_contains($e->getMessage(), 'login')) {
                $errorMessage .= 'Email authentication failed. Please contact support for assistance.';
            } elseif (str_contains($e->getMessage(), 'invalid') || str_contains($e->getMessage(), 'rejected')) {
                $errorMessage .= 'Email address appears to be invalid or rejected by the email server.';
            } else {
                $errorMessage .= 'Email service is temporarily unavailable. Please try again in a few minutes or contact support if the problem persists.';
            }
            
            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Verify and complete username change
     */
    public function verifyUsernameChange(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'verification_code' => 'required|digits:6'
        ]);

        if (!$user->pending_username_change || !$user->username_change_token) {
            return back()->with('error', 'No pending username change found.');
        }

        // Check if verification code is correct
        if ($user->username_change_token !== $request->verification_code) {
            return back()->with('error', 'Invalid verification code.');
        }

        // Check if token is not expired (valid for 30 minutes)
        $requestTime = $user->username_change_requested_at;
        if (!$requestTime || Carbon::parse($requestTime)->addMinutes(30)->isPast()) {
            // Clear expired token
            $user->pending_username_change = null;
            $user->username_change_token = null;
            $user->username_change_requested_at = null;
            $user->save();
            
            return back()->with('error', 'Verification code has expired. Please request a new one.');
        }

        try {
            DB::beginTransaction();
            
            $usernameChangeFee = 5.00;
            $oldUsername = $user->username;
            $newUsername = $user->pending_username_change;

            // Deduct fee from user's balance
            $freshUser = User::find($user->id);
            $depositWallet = $freshUser->deposit_wallet ?? 0;
            $interestWallet = $freshUser->interest_wallet ?? 0;
            
            if ($depositWallet >= $usernameChangeFee) {
                $freshUser->deposit_wallet -= $usernameChangeFee;
            } else {
                $interestWallet -= ($usernameChangeFee - $depositWallet);
                $freshUser->deposit_wallet = 0;
                $freshUser->interest_wallet = $interestWallet;
            }

            // Update username and clear pending change
            $freshUser->username = $newUsername;
            $freshUser->pending_username_change = null;
            $freshUser->username_change_token = null;
            $freshUser->username_change_requested_at = null;
            $freshUser->save();

            // Create transaction record
            $transaction = new \App\Models\Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $usernameChangeFee;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->trx = getTrx();
            $transaction->wallet_type = 'username_change';
            $transaction->remark = 'username_change';
            $transaction->details = 'Username change fee: ' . $oldUsername . ' → ' . $newUsername;
            $transaction->post_balance = ($freshUser->deposit_wallet ?? 0) + ($freshUser->interest_wallet ?? 0);
            $transaction->save();

            DB::commit();

            return back()->with('success', 'Username changed successfully! Fee of $' . number_format($usernameChangeFee, 2) . ' has been deducted from your account.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Username change verification error: ' . $e->getMessage());
            return back()->with('error', 'Failed to change username. Please try again.');
        }
    }

    /**
     * Cancel pending email change
     */
    public function cancelEmailChange()
    {
        $user = Auth::user();
        $this->clearEmailChangeData($user);
        $user->save();

        return back()->with('success', 'Email change request cancelled.');
    }

    /**
     * Clear all email change related data
     */
    private function clearEmailChangeData($user)
    {
        $user->pending_email_change = null;
        $user->email_change_token = null;
        $user->email_change_requested_at = null;
        $user->current_email_otp = null;
        $user->current_email_otp_sent_at = null;
        $user->current_email_verified = false;
        $user->new_email_verification_token = null;
        $user->new_email_token_sent_at = null;
        $user->email_change_step = 'initial';
    }

    /**
     * Cancel pending username change
     */
    public function cancelUsernameChange()
    {
        $user = Auth::user();
        
        $user->pending_username_change = null;
        $user->username_change_token = null;
        $user->username_change_requested_at = null;
        $user->save();

        return back()->with('success', 'Username change request cancelled.');
    }

    /**
     * Send OTP to current email for identity verification (Step 1)
     */
    private function sendCurrentEmailVerification($email, $otp, $user, $newEmail)
    {
        try {
            $subject = 'Email Change Security Verification - ' . config('app.name');
            $emailBody = $this->createCurrentEmailOtpContent($user, $otp, $newEmail);
            
            // Use the same proven approach as WithdrawController
            $emailSent = false;
            try {
                Mail::html($emailBody, function($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
                $emailSent = true;
                Log::info('Email change verification sent successfully to: ' . $email);
            } catch (\Exception $e) {
                Log::error('Mail::html failed for email change verification, trying with send method: ' . $e->getMessage());
                
                // Fallback to view-based email
                try {
                    Mail::send('emails.verification-code', [
                        'user' => $user,
                        'code' => $otp,
                        'type' => 'Email Change Security Verification'
                    ], function($message) use ($email, $subject) {
                        $message->to($email)->subject($subject);
                    });
                    $emailSent = true;
                    Log::info('Email change verification sent via fallback method to: ' . $email);
                } catch (\Exception $e2) {
                    Log::error('Mail::send also failed for email change verification: ' . $e2->getMessage());
                    $emailSent = false;
                }
            }
            
            if (!$emailSent) {
                Log::error('Both email methods failed for email change verification to: ' . $email);
                throw new \Exception('Email delivery failed - both primary and fallback methods unsuccessful.');
            }
            
        } catch (\Exception $e) {
            Log::error('Current email verification sending failed: ' . $e->getMessage());
            throw new \Exception('Email service temporarily unavailable: ' . $e->getMessage());
        }
    }

    /**
     * Send verification link to new email (Step 2)
     */
    private function sendNewEmailVerification($email, $token, $user)
    {
        try {
            $subject = 'Verify Your New Email Address - ' . config('app.name');
            $verificationUrl = route('email.verify.new', ['token' => $token]);
            $emailBody = $this->createNewEmailVerificationContent($user, $verificationUrl);
            
            // Use the same proven approach as WithdrawController
            $emailSent = false;
            try {
                Mail::html($emailBody, function($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
                $emailSent = true;
                Log::info('New email verification sent successfully to: ' . $email);
            } catch (\Exception $e) {
                Log::error('Mail::html failed for new email verification: ' . $e->getMessage());
                
                // Try a simplified email as fallback
                try {
                    $simpleBody = "
                    <h2>Verify Your New Email Address</h2>
                    <p>Hello " . ($user->name ?? $user->username) . ",</p>
                    <p>Please click the link below to verify your new email address:</p>
                    <p><a href='{$verificationUrl}' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Verify Email Address</a></p>
                    <p>Or copy and paste this URL: {$verificationUrl}</p>
                    <p>This link is valid for 24 hours.</p>
                    <p>Best regards,<br>" . config('app.name') . "</p>
                    ";
                    
                    Mail::html($simpleBody, function($message) use ($email, $subject) {
                        $message->to($email)->subject($subject);
                    });
                    $emailSent = true;
                    Log::info('New email verification sent via fallback method to: ' . $email);
                } catch (\Exception $e2) {
                    Log::error('New email verification fallback also failed: ' . $e2->getMessage());
                    $emailSent = false;
                }
            }
            
            if (!$emailSent) {
                Log::error('Both email methods failed for new email verification to: ' . $email);
                throw new \Exception('Email delivery failed to new email address.');
            }
            
        } catch (\Exception $e) {
            Log::error('New email verification sending failed: ' . $e->getMessage());
            throw new \Exception('Failed to send verification to new email: ' . $e->getMessage());
        }
    }

    /**
     * Create OTP email content for current email verification
     */
    private function createCurrentEmailOtpContent($user, $otp, $newEmail)
    {
        $appName = config('app.name');
        $userName = $user->name ?? $user->username;
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Email Change Security Verification - {$appName}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #dc3545; padding-bottom: 20px; }
                .logo { font-size: 24px; font-weight: bold; color: #dc3545; margin-bottom: 10px; }
                .subtitle { color: #666; font-size: 16px; }
                .otp-code { background: linear-gradient(135deg, #dc3545, #c82333); color: white; font-size: 32px; font-weight: bold; padding: 20px; border-radius: 10px; text-align: center; margin: 25px 0; letter-spacing: 4px; box-shadow: 0 4px 15px rgba(220,53,69,0.3); }
                .security-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .info-box { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666; }
                .highlight { color: #dc3545; font-weight: bold; }
                ul { padding-left: 20px; }
                li { margin: 8px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>{$appName}</div>
                    <div class='subtitle'>🔐 Email Change Security Verification</div>
                </div>
                
                <p>Hello <strong>{$userName}</strong>,</p>
                
                <p>You have requested to change your email address from <strong>" . $user->email . "</strong> to <strong class='highlight'>{$newEmail}</strong></p>
                
                <p>For security reasons, please verify your current email address by entering the code below:</p>
                
                <div class='otp-code'>{$otp}</div>
                
                <div class='security-box'>
                    <strong>🛡️ Security Notice:</strong>
                    <ul>
                        <li>This is <strong>Step 1</strong> of the email change process</li>
                        <li>This code verifies your identity using your current email</li>
                        <li>After verification, we'll send a confirmation link to your new email</li>
                        <li>The fee ($2.00) will be charged only after both verifications are complete</li>
                    </ul>
                </div>
                
                <div class='info-box'>
                    <strong>⏰ Important:</strong>
                    <ul>
                        <li>This code is valid for <strong>10 minutes</strong></li>
                        <li>Enter this code on the security settings page</li>
                        <li>Never share this code with anyone</li>
                        <li>If you didn't request this change, please ignore this email</li>
                    </ul>
                </div>
                
                <div class='footer'>
                    <p>This is a security verification from <strong>{$appName}</strong>.</p>
                    <p>Do not reply to this email.</p>
                    <p>Generated: " . now()->format('M d, Y h:i A T') . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Create verification link email content for new email
     */
    private function createNewEmailVerificationContent($user, $verificationUrl)
    {
        $appName = config('app.name');
        $userName = $user->name ?? $user->username;
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Verify Your New Email Address - {$appName}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #28a745; padding-bottom: 20px; }
                .logo { font-size: 24px; font-weight: bold; color: #28a745; margin-bottom: 10px; }
                .subtitle { color: #666; font-size: 16px; }
                .verify-button { background: linear-gradient(135deg, #28a745, #20c997); color: white; font-size: 18px; font-weight: bold; padding: 15px 30px; border-radius: 10px; text-align: center; margin: 25px 0; text-decoration: none; display: inline-block; box-shadow: 0 4px 15px rgba(40,167,69,0.3); }
                .verify-button:hover { background: linear-gradient(135deg, #218838, #1e7e34); color: white; text-decoration: none; }
                .success-box { background: #d4edda; border-left: 4px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .info-box { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666; }
                .url-backup { word-break: break-all; font-size: 12px; color: #666; margin: 10px 0; }
                ul { padding-left: 20px; }
                li { margin: 8px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>{$appName}</div>
                    <div class='subtitle'>✅ Verify Your New Email Address</div>
                </div>
                
                <p>Hello <strong>{$userName}</strong>,</p>
                
                <p>Great! You have successfully verified your current email address. Now we need to verify your new email address to complete the email change process.</p>
                
                <p style='text-align: center;'>
                    <a href='{$verificationUrl}' class='verify-button'>
                        🔗 Verify New Email Address
                    </a>
                </p>
                
                <div class='success-box'>
                    <strong>✅ Step 2 of Email Change:</strong>
                    <ul>
                        <li>Click the verification button above to confirm this new email address</li>
                        <li>Your email change will be completed automatically</li>
                        <li>The $2.00 fee will be charged upon successful verification</li>
                        <li>You will be redirected to your security settings page</li>
                    </ul>
                </div>
                
                <div class='info-box'>
                    <strong>🔗 Can't click the button?</strong>
                    <p>Copy and paste this link into your browser:</p>
                    <div class='url-backup'>{$verificationUrl}</div>
                </div>
                
                <div class='info-box'>
                    <strong>⏰ Important:</strong>
                    <ul>
                        <li>This verification link is valid for <strong>24 hours</strong></li>
                        <li>Click the link only once to complete verification</li>
                        <li>After verification, this will become your new primary email</li>
                        <li>If you didn't request this change, please ignore this email</li>
                    </ul>
                </div>
                
                <div class='footer'>
                    <p>This is an email verification from <strong>{$appName}</strong>.</p>
                    <p>Do not reply to this email.</p>
                    <p>Generated: " . now()->format('M d, Y h:i A T') . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Send username change verification email
     */
    private function sendUsernameChangeVerification($email, $code, $user, $newUsername)
    {
        try {
            // Simple email approach using basic HTML
            $subject = 'Username Change Verification - ' . config('app.name');
            $emailBody = $this->createChangeEmailContent($user, $code, 'Username Change Verification', $newUsername);
            
            // Try to send email
            $emailSent = false;
            try {
                Mail::html($emailBody, function($message) use ($email, $subject) {
                    $message->to($email)
                            ->subject($subject);
                });
                $emailSent = true;
            } catch (\Exception $e) {
                Log::error('Mail::html failed, trying with send method: ' . $e->getMessage());
                
                // Fallback to view-based email
                try {
                    Mail::send('emails.verification-code', [
                        'user' => $user,
                        'code' => $code,
                        'type' => 'Username Change Verification'
                    ], function($message) use ($email, $subject) {
                        $message->to($email)
                                ->subject($subject);
                    });
                    $emailSent = true;
                } catch (\Exception $e2) {
                    Log::error('Mail::send also failed: ' . $e2->getMessage());
                    $emailSent = false;
                }
            }
            
            if (!$emailSent) {
                throw new \Exception('Email delivery failed - both primary and fallback methods unsuccessful. Please check email configuration.');
            }
            
        } catch (\Exception $e) {
            Log::error('Username change verification sending failed: ' . $e->getMessage());
            
            // Provide more specific error details
            if (str_contains($e->getMessage(), 'Connection') || str_contains($e->getMessage(), 'timeout')) {
                throw new \Exception('Email server connection timeout. Please check your internet connection.');
            } elseif (str_contains($e->getMessage(), 'authentication') || str_contains($e->getMessage(), 'login')) {
                throw new \Exception('Email authentication failed. Server configuration issue.');
            } elseif (str_contains($e->getMessage(), 'invalid') || str_contains($e->getMessage(), 'rejected')) {
                throw new \Exception('Email address rejected by server. Please verify the email address.');
            } else {
                throw new \Exception('Email service temporarily unavailable: ' . $e->getMessage());
            }
        }
    }

    /**
     * Create email content for change verification
     */
    private function createChangeEmailContent($user, $code, $type, $newValue)
    {
        $appName = config('app.name');
        $userName = $user->name ?? $user->username;
        $changeType = str_replace(' Verification', '', $type);
        $fee = ($changeType === 'Email Change') ? '$2.00' : '$5.00';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>{$type} - {$appName}</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #007bff; padding-bottom: 20px; }
                .logo { font-size: 24px; font-weight: bold; color: #007bff; margin-bottom: 10px; }
                .subtitle { color: #666; font-size: 16px; }
                .otp-code { background: linear-gradient(135deg, #007bff, #0056b3); color: white; font-size: 32px; font-weight: bold; padding: 20px; border-radius: 10px; text-align: center; margin: 25px 0; letter-spacing: 4px; box-shadow: 0 4px 15px rgba(0,123,255,0.3); }
                .info-box { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .warning-box { background: #fff3e0; border-left: 4px solid #ff9800; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666; }
                .highlight { color: #007bff; font-weight: bold; }
                ul { padding-left: 20px; }
                li { margin: 8px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>{$appName}</div>
                    <div class='subtitle'>{$type}</div>
                </div>
                
                <p>Hello <strong>{$userName}</strong>,</p>
                
                <p>You have requested to change your " . strtolower(str_replace(' Change', '', $changeType)) . " to: <span class='highlight'>{$newValue}</span></p>
                
                <p>Please use the verification code below to complete this change:</p>
                
                <div class='otp-code'>{$code}</div>
                
                <div class='info-box'>
                    <strong>📋 Change Details:</strong>
                    <ul>
                        <li><strong>Type:</strong> {$changeType}</li>
                        <li><strong>New Value:</strong> {$newValue}</li>
                        <li><strong>Fee:</strong> {$fee}</li>
                        <li><strong>Requested:</strong> " . now()->format('M d, Y h:i A') . "</li>
                    </ul>
                </div>
                
                <div class='warning-box'>
                    <strong>⚠️ Important Security Information:</strong>
                    <ul>
                        <li>This verification code is valid for <strong>30 minutes</strong> only</li>
                        <li>The fee will be automatically deducted upon successful verification</li>
                        <li>Never share this code with anyone</li>
                        <li>If you didn't request this change, please ignore this email and contact support</li>
                        <li>This code can only be used once</li>
                    </ul>
                </div>
                
                <div class='footer'>
                    <p>This is an automated security message from <strong>{$appName}</strong>.</p>
                    <p>Please do not reply to this email.</p>
                    <p>Generated on: " . now()->format('M d, Y \a\t h:i A T') . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

}
