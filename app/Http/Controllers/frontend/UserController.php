<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Invest;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\SupportTicket;
use App\Lib\HyipLab;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        $data['pageTitle']         = 'Dashboard';
        $user                      = auth()->user();
        $data['user']              = $user;
        $data['totalInvest']       = Invest::where('user_id', auth()->id())->sum('amount');
        $data['totalWithdraw']     = Withdrawal::where('user_id', $user->id)->whereIn('status', [1])->sum('amount');
        $data['lastWithdraw']      = Withdrawal::where('user_id', $user->id)->whereIn('status', [1])->latest()->first('amount');
        $data['totalDeposit']      = Deposit::where('user_id', $user->id)->where('status', 1)->sum('amount');
        $data['lastDeposit']       = Deposit::where('user_id', $user->id)->where('status', 1)->latest()->first('amount');
        $data['transactions']      = $data['user']->transactions->sortByDesc('id')->take(8);
        $data['referral_earnings'] = Transaction::where('remark', 'referral_commission')->where('user_id', auth()->id())->sum('amount');

        $data['submittedDeposits']  = Deposit::where('status', '!=', 0)->where('user_id', $user->id)->sum('amount');
        $data['successfulDeposits'] = Deposit::successful()->where('user_id', $user->id)->sum('amount');
        $data['requestedDeposits']  = Deposit::where('user_id', $user->id)->sum('amount');
        $data['initiatedDeposits']  = Deposit::initiated()->where('user_id', $user->id)->sum('amount');
        $data['pendingDeposits']    = Deposit::pending()->where('user_id', $user->id)->sum('amount');
        $data['rejectedDeposits']   = Deposit::rejected()->where('user_id', $user->id)->sum('amount');

        $data['submittedWithdrawals']  = Withdrawal::where('status', '!=', 0)->where('user_id', $user->id)->sum('amount');
        $data['successfulWithdrawals'] = Withdrawal::approved()->where('user_id', $user->id)->sum('amount');
        $data['rejectedWithdrawals']   = Withdrawal::rejected()->where('user_id', $user->id)->sum('amount');
        $data['initiatedWithdrawals']  = Withdrawal::initiated()->where('user_id', $user->id)->sum('amount');
        $data['requestedWithdrawals']  = Withdrawal::where('user_id', $user->id)->sum('amount');
        $data['pendingWithdrawals']    = Withdrawal::pending()->where('user_id', $user->id)->sum('amount');

        $data['invests']               = Invest::where('user_id', $user->id)->sum('amount');
        $data['completedInvests']      = Invest::where('user_id', $user->id)->where('status', 0)->sum('amount');
        $data['runningInvests']        = Invest::where('user_id', $user->id)->where('status', 1)->sum('amount');
        $data['interests']             = Transaction::where('remark', 'interest')->where('user_id', $user->id)->sum('amount');
        $data['depositWalletInvests']  = Invest::where('user_id', $user->id)->where('wallet_type', 'deposit_wallet')->where('status', 1)->sum('amount');
        $data['interestWalletInvests'] = Invest::where('user_id', $user->id)->where('wallet_type', 'interest_wallet')->where('status', 1)->sum('amount');

       
        $data['chartData'] = Transaction::where('remark', 'interest')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('user_id', $user->id)
            ->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date")
            ->orderBy('created_at', 'asc')
            ->groupBy('date')
            ->get();

        return view('frontend/dashboard', $data);
    }
    /**
     * Display a listing of the resource.
     */
    public function transfer_funds()
    {
        $pageTitle = "Transfer Funds";
        return view('frontend.transfer_fund', compact('pageTitle'));
    }
    public function transferBalanceSubmit(Request $request)
    {
        $notify = [];
        $user = auth()->user();
        if (!$user) {
            $notify[] = ['error', 'You need to login first'];
            return back()->withNotify($notify);
        }
        $request->validate([
            'username' => 'required',
            'amount'   => 'required|numeric|gt:0',
            'wallet'   => 'required|in:deposit_wallet,interest_wallet',
        ]);

        $user = auth()->user();
        if ($user->username == $request->username) {
            $notify[] = ['error', 'You cannot transfer balance to your own account'];
            return back()->withNotify($notify);
        }

        $receiver = User::where('username', $request->username)->first();
        if (!$receiver) {
            $notify[] = ['error', 'Oops! Receiver not found'];
            return back()->withNotify($notify);
        }

        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }
        }

        $general     = gs();
        $charge      = $general->f_charge + ($request->amount * $general->p_charge) / 100;
        $afterCharge = $request->amount + $charge;
        $wallet      = $request->wallet;

        if ($user->$wallet < $afterCharge) {
            $notify[] = ['error', 'You have no sufficient balance to this wallet'];
            return back()->withNotify($notify);
        }

        $user->$wallet -= $afterCharge;
        $user->save();

        $trx1                      = getTrx();
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = getAmount($afterCharge);
        $transaction->charge       = $charge;
        $transaction->trx_type     = '-';
        $transaction->trx          = $trx1;
        $transaction->wallet_type  = $wallet;
        $transaction->remark       = 'balance_transfer';
        $transaction->details      = 'Balance transfer to ' . $receiver->username;
        $transaction->post_balance = getAmount($user->$wallet);
        $transaction->save();

        $receiver->deposit_wallet += $request->amount;
        $receiver->save();

        $trx2                      = getTrx();
        $transaction               = new Transaction();
        $transaction->user_id      = $receiver->id;
        $transaction->amount       = getAmount($request->amount);
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->trx          = $trx2;
        $transaction->wallet_type  = 'deposit_wallet';
        $transaction->remark       = 'balance_received';
        $transaction->details      = 'Balance received from ' . $user->username;
        $transaction->post_balance = getAmount($user->deposit_wallet);
        $transaction->save();

        notify($user, 'BALANCE_TRANSFER', [
            'amount'        => showAmount($request->amount),
            'charge'        => showAmount($charge),
            'wallet_type'   => keyToTitle($wallet),
            'post_balance'  => showAmount($user->$wallet),
            'user_fullname' => $receiver->fullname,
            'username'      => $receiver->username,
            'trx'           => $trx1,
        ]);

        notify($receiver, 'BALANCE_RECEIVE', [
            'wallet_type'  => 'Deposit wallet',
            'amount'       => showAmount($request->amount),
            'post_balance' => showAmount($receiver->deposit_wallet),
            'sender'       => $user->username,
            'trx'          => $trx2,
        ]);

        $notify[] = ['success', 'Balance transferred successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
