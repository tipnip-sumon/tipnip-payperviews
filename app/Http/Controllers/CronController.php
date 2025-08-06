<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Lib\TipNipLab;
use App\Models\Invest;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use PrevailExcel\Nowpayments\Facades\Nowpayments;

class CronController extends Controller
{
     public function cron()
    {
        $now                = Carbon::now();
        $general            = GeneralSetting::first();
        $general->last_cron = $now;
        $general->save();

        // $day    = strtolower(date('D'));
        // $offDay = (array) $general->off_day;
        // if (array_key_exists($day, $offDay)) {
        //     echo "Holiday";
        //     exit;
        // }

        $invests = Invest::where('status', 1)->where('next_time', '<=', $now)->orderBy('last_time')->take(100)->get();
        foreach ($invests as $invest) {
            $now  = $now;
            // $next = TipNipLab::nextWorkingDay($invest->plan->time);
            $user = $invest->user;

            $invest->return_rec_time += 1;
            $invest->paid += $invest->interest;
            $invest->should_pay -= $invest->period > 0 ? $invest->interest : 0;
            $invest->next_time = $now ->addDay(1); // Assuming daily return, you can change this as needed
            $invest->last_time = $now;

            // Add Return Amount to user's Interest Balance
            $user->interest_wallet += $invest->interest;
            $user->save();

            $trx = getTrx();

            // Create The Transaction for Interest Back
            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $invest->interest;
            $transaction->charge       = 0;
            $transaction->post_balance = $user->interest_wallet;
            $transaction->trx_type     = '+';
            $transaction->trx          = $trx;
            $transaction->remark       = 'interest';
            $transaction->wallet_type  = 'interest_wallet';
            $transaction->details      = showAmount($invest->interest) . ' ' . $general->cur_text . ' interest from ' . @$invest->plan->name;
            $transaction->save();

            // Give Referral Commission if Enabled
            if ($general->invest_commission == 1) {
                $commissionType = 'invest_return_commission';
                TipNipLab::levelCommission($user, $invest->interest, $commissionType, $trx, $general);
            }

            // Complete the investment if user get full amount as plan
            if ($invest->return_rec_time >= $invest->period && $invest->period != -1) {
                $invest->status = 0; // Change Status so he do not get any more return

                // Give the capital back if plan says the same
                if ($invest->capital_status == 1) {
                    $capital = $invest->amount;
                    $user->interest_wallet += $capital;
                    $user->save();

                    $transaction               = new Transaction();
                    $transaction->user_id      = $user->id;
                    $transaction->amount       = $capital;
                    $transaction->charge       = 0;
                    $transaction->post_balance = $user->interest_wallet;
                    $transaction->trx_type     = '+';
                    $transaction->trx          = $trx;
                    $transaction->wallet_type  = 'interest_wallet';
                    $transaction->remark       = 'capital_return';
                    $transaction->details      = showAmount($capital) . ' ' . $general->cur_text . ' capital back from ' . @$invest->plan->name;
                    $transaction->save();
                }
            }

            $invest->save();
        }
    }


     public function paymentStatus()
    {
        $deposit = Deposit::Pending()->get();
        foreach ($deposit as $item) {
            $email = env('NOWPAYMENTS_EMAIL');
            $password = env('NOWPAYMENTS_PASSWORD');
            $jwt_token = Nowpayments::getJwt($email, $password);
            $result = Nowpayments::getListOfPayments($item->payment_id,$jwt_token);
            // Check if the result is valid and contains the required fields
            if(!$result || !isset($result['order_id']) || !isset($result['payment_status'])) {
                continue; // Skip if the result is not valid or does not contain required fields
            }
            if($item->trx != $result['order_id']) {
                continue; // Skip if the Order ID does not match
            }
            $depo = Deposit::where('trx', $result['order_id'])->first();
            if($result['payment_status'] == 'waiting') {
                $depo->status = 2; // Set status to pending if payment status is waiting
                $depo->btc_amo = $result['payment_status']; // Update the btc_amo field with the payment status
            } elseif ($result['payment_status'] == 'finished') {
                $depo->status = 1; // Set status to approved if payment status is finished
                $depo->final_amo = $result['outcome_amount']; // Update the btc_amo field with the outcome amount
                $depo->btc_amo = $result['payment_status']; // Update the btc_amo field with the payment status
            } elseif ($result['payment_status'] == 'partially_paid') {
                $depo->status = 1; // Set status to approved if outcome amount is sufficient
                $depo->final_amo = $result['outcome_amount']; // Update the final_amo field with the outcome amount
                $depo->btc_amo = $result['payment_status']; // Update the btc_amo field with the payment status
            }elseif ($result['payment_status'] == 'expired') {
                $depo->delete(); // Delete the deposit if payment status is not recognized
                continue; // Skip further processing for this deposit
            }
            $depo->save();
            // You can also add logic here to update the user's balance or notify them if needed
            if($item->method_currency == 'usdtbsc' || $item->method_currency == 'usdttrc20' || $item->method_currency == 'usdterc20') {
                if ($result['payment_status'] == 'finished') {
                    $user = $depo->user;
                    $user->deposit_wallet += $depo->amount;
                    $user->save();
                }  elseif ($result['payment_status'] == 'partially_paid') {
                    $user = $depo->user;
                    $user->deposit_wallet += $result['outcome_amount'];
                    $user->save();
                }
            } elseif ($item->method_currency == 'btc') {
                if ($result['payment_status'] == 'finished') {
                    $user = $depo->user;
                    $user->btc_wallet += $depo->amount;
                    $user->save();
                }
            }
        }
    }
}