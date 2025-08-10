<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
        return view("frontend.withdraw-wallet", ["pageTitle" => "Withdraw Wallet Balance"]);
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
