<?php

namespace App\Http\Controllers\Gateway;

use App\Models\Plan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    public function index() 
    {
        $pageTitle = 'Deposit Plan';
        $user = Auth::user();
        $plans = Plan::where('status', 1)->get();
        return view('frontend.deposit', compact('pageTitle', 'plans', 'user'));
    }
}
