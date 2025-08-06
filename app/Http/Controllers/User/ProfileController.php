<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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
}
