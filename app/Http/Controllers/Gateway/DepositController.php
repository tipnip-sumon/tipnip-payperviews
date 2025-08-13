<?php

namespace App\Http\Controllers\Gateway;

use App\Models\Plan;
use App\Models\Deposit;
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
    
    public function status()
    {
        $user = Auth::user();
        $pendingDeposit = Deposit::where('user_id', $user->id)
            ->where('status', 2) // Pending status
            ->first();
            
        // Also check for recently completed deposits (last 10 minutes)
        $recentlyCompleted = Deposit::where('user_id', $user->id)
            ->where('status', 1) // Approved status
            ->where('updated_at', '>=', now()->subMinutes(10))
            ->exists();
            
        return response()->json([
            'success' => true,
            'has_pending' => $pendingDeposit ? true : false,
            'recently_completed' => $recentlyCompleted,
            'deposit' => $pendingDeposit ? [
                'id' => $pendingDeposit->id,
                'trx' => $pendingDeposit->trx,
                'amount' => $pendingDeposit->amount,
                'final_amo' => $pendingDeposit->final_amo,
                'method_currency' => $pendingDeposit->method_currency,
                'btc_wallet' => $pendingDeposit->btc_wallet,
                'admin_feedback' => $pendingDeposit->admin_feedback,
                'btc_amo' => $pendingDeposit->btc_amo,
                'status' => $pendingDeposit->status,
                'created_at' => $pendingDeposit->created_at,
                'updated_at' => $pendingDeposit->updated_at,
            ] : null
        ]);
    }
}
