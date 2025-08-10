<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Withdrawal;
use App\Models\WithdrawMethod;

class WithdrawController extends Controller 
{
    public function index()
    {
        return view("frontend.withdraw", ["pageTitle" => "Withdraw Deposit"]);
    }
    
    public function withdraw(Request $request)
    {
        return back()->with("swal_success", [
            "title" => "Test Success!",
            "text" => "WithdrawController is working correctly!",
            "icon" => "success"
        ]);
    }
    
    public function history()
    {
        return view("frontend.withdrawal-history", ["pageTitle" => "Deposit Withdrawal History"]);
    }
    
    public function walletIndex()
    {
        $user = Auth::user();
        
        // Calculate wallet balance
        $depositWallet = $user->deposit_wallet ?? 0;
        $interestWallet = $user->interest_wallet ?? 0;
        $totalWalletBalance = $depositWallet + $interestWallet;
        
        // Get available withdrawal methods
        $withdrawMethods = WithdrawMethod::where('status', 1)->get();
        
        // Get wallet withdrawal statistics
        $withdrawalStats = [
            'total_wallet_withdrawals' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->count(),
            'total_wallet_withdrawn' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 1)->sum('amount'),
            'pending_wallet_withdrawals' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 2)->count(),
            'pending_wallet_amount' => Withdrawal::where('user_id', $user->id)->where('withdraw_type', 'wallet')->where('status', 2)->sum('amount'),
        ];
        
        // Get recent wallet withdrawal history
        $recentWithdrawals = Withdrawal::where('user_id', $user->id)
            ->where('withdraw_type', 'wallet')
            ->with('withdrawMethod')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $data = [
            'pageTitle' => 'Withdraw Wallet Balance',
            'depositWallet' => $depositWallet,
            'interestWallet' => $interestWallet,
            'totalWalletBalance' => $totalWalletBalance,
            'withdrawMethods' => $withdrawMethods,
            'withdrawalStats' => $withdrawalStats,
            'recentWithdrawals' => $recentWithdrawals,
        ];
        
        return view('frontend.withdraw-wallet', $data);
    }

    public function walletWithdraw(Request $request)
    {
        return back()->with("swal_success", [
            "title" => "Test Success!",
            "text" => "WalletWithdraw method is working correctly!",
            "icon" => "success"
        ]);
    }
   
    public function walletHistory()
    {
        return view("frontend.wallet-withdrawal-history", ["pageTitle" => "Wallet Withdrawal History"]);
    }
}
