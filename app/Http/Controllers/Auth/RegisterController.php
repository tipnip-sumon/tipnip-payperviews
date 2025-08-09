<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
// use Illuminate\Auth\Events\Registered;    
// use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'sponsor' => [
                'nullable', 
                'string', 
                'min:3', 
                'max:64', // Increased to accommodate hash length
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        // Check if sponsor exists by hash or username
                        $sponsor = \App\Models\User::findByReferralHash($value) 
                                 ?: \App\Models\User::where('username', $value)->first();
                        
                        if (!$sponsor) {
                            $fail('The sponsor ID "' . $value . '" does not exist in our system. Please check your referral link or contact the person who referred you.');
                        } elseif ($sponsor->status != 1) {
                            $fail('The sponsor account is not active. Please contact the person who referred you.');
                        }
                    }
                }
            ],
            'username' => [
                'required', 
                'string', 
                'min:3', 
                'max:20', 
                'unique:users',
                'regex:/^[a-zA-Z0-9_]+$/' // Only letters, numbers, and underscores
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agree' => ['accepted'],
        ];

        // Note: Add captcha validation if your project uses it
        if (config('captcha.enabled', false)) {
            $rules['captcha'] = ['required', 'captcha'];
        }

        return Validator::make($data, $rules, [
            'sponsor.min' => 'Sponsor ID must be at least 3 characters long.',
            'sponsor.max' => 'Sponsor ID cannot exceed 64 characters.',
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 3 characters long.',
            'username.max' => 'Username cannot exceed 20 characters.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'username.unique' => 'This username is already taken. Please choose a different one.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered. Please use a different email or try logging in.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match. Please re-enter your password.',
            'agree.accepted' => 'You must accept the terms and conditions to register.',
            // 'captcha.required' => 'Please enter the captcha code.',
            // 'captcha.captcha' => 'The captcha code is incorrect.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Find sponsor by referral hash or username if provided
        $sponsorId = null;
        if (!empty($data['sponsor'])) {
            // First try to find by referral hash
            $sponsor = User::findByReferralHash($data['sponsor']);
            
            // If not found by hash, try by username (for backward compatibility)
            if (!$sponsor) {
                $sponsor = User::where('username', $data['sponsor'])->first();
            }
            
            $sponsorId = $sponsor ? $sponsor->id : null;
        } else {
            // If no sponsor provided, automatically set to user ID 1 (default sponsor)
            $sponsorId = 1;
        }
        
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'ref_by' => $sponsorId,
            'password' => Hash::make($data['password']),
            'email_verified_at' => null, // Will be set when email is verified
        ]);

        // Generate referral hash for the new user
        $user->generateReferralHash(); 

        // Send welcome notification for registration
        try {
            notifyUserRegistration($user->id, $user->username, $user->email);
        } catch (\Exception $e) {
            // Log error but don't fail registration if notification fails
            Log::error('Failed to send registration notification: ' . $e->getMessage());
        }

        // Send welcome email
        try {
            sendWelcomeEmail($user); 
        } catch (\Exception $e) {
            // Log error but don't fail registration if email fails
            Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        // Send referral signup notification to referrer if applicable
        if ($sponsorId) {
            try {
                notifyReferralSignup($sponsorId, $user->username, $user->email);
            } catch (\Exception $e) {
                // Log error but don't fail registration if notification fails
                Log::error('Failed to send referral signup notification: ' . $e->getMessage());
            }
        }

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(\Illuminate\Http\Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        // Don't automatically log the user in if email verification is required
        if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
            return redirect()->route('login')
                ->with('success', 'Registration successful! Please check your email and click the verification link before logging in.');
        }

        // If email verification is not required, log the user in
        $this->guard()->login($user);

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    /**
     * Validate sponsor for real-time checking during registration
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateSponsor(Request $request)
    {
        try {
            $request->validate([
                'sponsor' => 'required|string|min:3|max:64'
            ]);

            $sponsorInput = $request->sponsor;
            
            // Check if sponsor exists by referral hash first
            $sponsor = User::findByReferralHash($sponsorInput);
            
            // If not found by hash, try by username (for backward compatibility)
            if (!$sponsor) {
                $sponsor = User::where('username', $sponsorInput)->first();
            }
            
            if ($sponsor) {
                // Check if sponsor account is active
                if ($sponsor->status != 1) {
                    return response()->json([
                        'valid' => false,
                        'message' => 'This sponsor account is not active.'
                    ]);
                }
                
                return response()->json([
                    'valid' => true,
                    'sponsor_username' => $sponsor->username,
                    'sponsor_name' => trim($sponsor->firstname . ' ' . $sponsor->lastname) ?: $sponsor->username,
                    'message' => 'Valid sponsor found!'
                ]);
            } else {
                return response()->json([
                    'valid' => false,
                    'message' => 'Sponsor ID does not exist in our system. Please check the referral link or contact the person who referred you.'
                ]);
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid sponsor format. Please enter a valid sponsor ID or referral code.'
            ], 422);
        } catch (\Exception $e) {
            Log::error('Sponsor validation error: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'message' => 'Unable to validate sponsor at this time. Please try again.'
            ], 500);
        }
    }

    /**
     * Validate username availability
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateUsername(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|min:3|max:20|regex:/^[a-zA-Z0-9_]+$/'
            ]);

            $username = $request->username;
            
            // Check if username exists
            $existingUser = User::where('username', $username)->first();
            
            if ($existingUser) {
                return response()->json([
                    'valid' => false,
                    'available' => false,
                    'message' => 'Username is already taken. Please choose a different username.'
                ]);
            }
            
            return response()->json([
                'valid' => true,
                'available' => true,
                'message' => 'Username is available!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'valid' => false,
                'available' => false,
                'message' => 'Invalid username format. Must be 3-20 characters with letters, numbers, and underscores only.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Username validation error: ' . $e->getMessage());
            
            return response()->json([
                'valid' => false,
                'available' => false,
                'message' => 'Error validating username. Please try again later.'
            ], 500);
        }
    }

    /**
     * Validate email availability
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|max:255'
            ]);

            $email = $request->email;
            
            // Check if email exists
            $existingUser = User::where('email', $email)->first();
            
            if ($existingUser) {
                return response()->json([
                    'valid' => false,
                    'available' => false,
                    'message' => 'Email address is already registered. Please use a different email or try logging in.'
                ]);
            }
            
            return response()->json([
                'valid' => true,
                'available' => true,
                'message' => 'Email address is available!'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'valid' => false,
                'available' => false,
                'message' => 'Invalid email format. Please enter a valid email address.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Email validation error: ' . $e->getMessage());
            
            return response()->json([
                'valid' => false,
                'available' => false,
                'message' => 'Error validating email. Please try again later.'
            ], 500);
        }
    }

    // /**
    //  * Show the registration form with sponsor reference.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function showRegistrationForm(Request $request)
    // {
    //     $reference = $request->get('ref');
    //     $referralBy = null;
        
    //     if ($reference) {
    //         $referralUser = User::where('username', $reference)->first();
    //         if ($referralUser) {
    //             $referralBy = $reference;
    //         }
    //     }

    //     return view('auth.register', compact('reference', 'referralBy'));
    // }
}
