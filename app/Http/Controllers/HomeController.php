<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('frontend/home');
    }
    
    public function profile($id=null)
    {
        $user = \App\Models\User::find($id);
        return view('frontend/profile', compact('user'));
    }
    public function editProfile()
    {
        $user = Auth::user();
        return view('frontend/editprofile', compact('user'));
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
