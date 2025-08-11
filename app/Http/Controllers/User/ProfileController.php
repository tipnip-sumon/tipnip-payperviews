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
     * Request paid email change with verification
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
        if ($user->pending_email_change) {
            return back()->with('error', 'You already have a pending email change request. Please complete or cancel it first.');
        }

        try {
            // Generate verification code
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store pending change data
            $user->pending_email_change = $request->new_email;
            $user->email_change_token = $verificationCode;
            $user->email_change_requested_at = now();
            $user->save();

            // Send verification email to NEW email address
            $this->sendEmailChangeVerification($request->new_email, $verificationCode, $user);

            return back()->with('success', 'Verification code sent to your new email address. Please check ' . $request->new_email . ' and enter the code to complete the change. Fee of $' . number_format($emailChangeFee, 2) . ' will be deducted upon verification.');
            
        } catch (\Exception $e) {
            Log::error('Email change request error: ' . $e->getMessage());
            return back()->with('error', 'Failed to send verification email. Please try again.');
        }
    }

    /**
     * Verify and complete email change
     */
    public function verifyEmailChange(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'verification_code' => 'required|digits:6'
        ]);

        if (!$user->pending_email_change || !$user->email_change_token) {
            return back()->with('error', 'No pending email change found.');
        }

        // Check if verification code is correct
        if ($user->email_change_token !== $request->verification_code) {
            return back()->with('error', 'Invalid verification code.');
        }

        // Check if token is not expired (valid for 30 minutes)
        if ($user->email_change_requested_at->addMinutes(30)->isPast()) {
            // Clear expired token
            $user->pending_email_change = null;
            $user->email_change_token = null;
            $user->email_change_requested_at = null;
            $user->save();
            
            return back()->with('error', 'Verification code has expired. Please request a new one.');
        }

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

            // Update email and clear pending change
            $freshUser->email = $newEmail;
            $freshUser->email_verified_at = null; // Reset email verification
            $freshUser->pending_email_change = null;
            $freshUser->email_change_token = null;
            $freshUser->email_change_requested_at = null;
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

            return back()->with('success', 'Email address changed successfully! Please verify your new email address. Fee of $' . number_format($emailChangeFee, 2) . ' has been deducted from your account.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Email change verification error: ' . $e->getMessage());
            return back()->with('error', 'Failed to change email address. Please try again.');
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
            Log::error('Username change request error: ' . $e->getMessage());
            return back()->with('error', 'Failed to send verification email. Please try again.');
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
        if ($user->username_change_requested_at->addMinutes(30)->isPast()) {
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
        
        $user->pending_email_change = null;
        $user->email_change_token = null;
        $user->email_change_requested_at = null;
        $user->save();

        return back()->with('success', 'Email change request cancelled.');
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
     * Send email change verification email
     */
    private function sendEmailChangeVerification($email, $code, $user)
    {
        try {
            // Simple email approach using basic HTML
            $subject = 'Email Change Verification - ' . config('app.name');
            $emailBody = $this->createChangeEmailContent($user, $code, 'Email Change Verification', $email);
            
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
                        'type' => 'Email Change Verification'
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
                throw new \Exception('All email sending methods failed');
            }
            
        } catch (\Exception $e) {
            Log::error('Email change verification sending failed: ' . $e->getMessage());
            throw new \Exception('Failed to send verification email: ' . $e->getMessage());
        }
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
                throw new \Exception('All email sending methods failed');
            }
            
        } catch (\Exception $e) {
            Log::error('Username change verification sending failed: ' . $e->getMessage());
            throw new \Exception('Failed to send verification email: ' . $e->getMessage());
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
