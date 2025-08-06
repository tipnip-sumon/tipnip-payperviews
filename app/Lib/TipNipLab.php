<?php

namespace App\Lib;

use App\Models\AdminNotification;
use App\Models\Holiday;
use App\Models\Invest;
use App\Models\Referral;
use App\Models\TimeSetting;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class TipNipLab
{
    /**
    * Instance of investor user
    *
    * @var object
    */
    private $user;

    /**
    * Plan which is purchasing
    *
    * @var object
    */
    private $plan;

    /**
    * General setting
    *
    * @var object
    */
    private $setting;

    /**
    * Set some properties
    *
    * @param object $user
    * @param object $plan
    * @return void
    */
    public function __construct($user, $plan)
    {
        $this->user = $user;
        $this->plan = $plan;
    }

    /**
    * Invest process with special token discount and ticket discount
    *
    * @param float $originalAmount - Full plan amount for sponsor calculations
    * @param float $walletDeductionAmount - Discounted amount to deduct from wallet
    * @param string $wallet
    * @param float $tokenDiscount
    * @param array $tokenDetails
    * @param float $ticketDiscount
    * @param array $ticketDetails
    * @param string $appliedTicketNumber
    * @return void
    */
    public function investWithDiscount($originalAmount, $walletDeductionAmount, $wallet, $tokenDiscount = 0, $tokenDetails = [], $ticketDiscount = 0, $ticketDetails = null, $appliedTicketNumber = null){
        $plan = $this->plan;
        $user = $this->user;

        // Validate plan has proper time configuration
        if (empty($plan->time) || !is_numeric($plan->time)) {
            throw new \Exception('Invalid plan configuration: time field is missing or invalid');
        }

        // Deduct only the discounted amount from user's wallet
        $user->$wallet -= $walletDeductionAmount;
        $user->save();

        $trx                        = getTrx();
        $transaction                = new Transaction();
        $transaction->user_id       = $user->id;
        $transaction->amount        = $walletDeductionAmount; // Record actual amount deducted
        $transaction->post_balance  = $user->$wallet;
        $transaction->charge        = 0;
        $transaction->trx_type      = '-';
        
        $discountNote = '';
        if ($tokenDiscount > 0) {
            $discountNote .= " (Special token discount: \${$tokenDiscount})";
        }
        if ($ticketDiscount > 0) {
            $discountNote .= " (Ticket discount: \${$ticketDiscount})";
        }
        $transaction->details       = 'Invested on ' . $plan->name . $discountNote;
        $transaction->trx           = $trx;
        $transaction->wallet_type   = $wallet;
        $transaction->remark        = 'invest';
        $transaction->save();

        $timeName = "Month";

        //start
        if ($plan->interest_type == 1) {
            $interestAmount = ($originalAmount * $plan->interest) / 100; // Use original amount for interest calculation
        } else {
            $interestAmount = $plan->interest;
        }

        $period = ($plan->lifetime == 1) ? -1 : $plan->repeat_time;

        $shouldPay = -1;
        if ($period > 0) {
            $shouldPay = $interestAmount * $period;
        }

        $invest                 = new Invest();
        $invest->user_id        = $user->id;
        $invest->plan_id        = $plan->id;
        $invest->amount         = $originalAmount; // Store original amount for sponsor commission calculations
        $invest->actual_paid    = $walletDeductionAmount; // Store what user actually paid
        $invest->token_discount = $tokenDiscount; // Store discount amount
        $invest->ticket_discount = $ticketDiscount; // Store ticket discount amount
        $invest->applied_ticket = $appliedTicketNumber; // Store applied ticket number
        $invest->interest       = $interestAmount;
        $invest->period         = $period;
        $invest->time_name      = $timeName;
        $invest->hours          = (string)($plan->time ?? 24);
        $invest->next_time      = now()->addHours($plan->time ?? 24);
        $invest->should_pay     = $shouldPay;
        $invest->status         = 1;
        $invest->wallet_type    = $wallet;
        $invest->capital_status = $plan->capital_back;
        $invest->trx            = $trx;
        $invest->save();

        // Check if this is user's first investment and trigger congratulation email
        $this->checkAndSendFirstInvestmentEmail($user, $invest);

        // Mark ticket as used if a ticket was applied
        if ($appliedTicketNumber) {
            try {
                // Prepare comprehensive metadata
                $metadata = [
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'original_amount' => $originalAmount,
                    'actual_paid' => $walletDeductionAmount,
                    'ticket_discount' => $ticketDiscount,
                    'token_discount' => $tokenDiscount,
                    'wallet_type' => $wallet,
                    'transaction_trx' => $trx,
                    'investment_date' => now()->toDateTimeString(),
                    'user_username' => $user->username,
                    'user_email' => $user->email,
                    'applied_at' => now()->toDateTimeString(),
                    'ip_address' => request()->ip() ?? 'unknown',
                    'user_agent' => request()->userAgent() ?? 'unknown'
                ];

                // Create used_ticket record with comprehensive metadata
                $usedTicket = new \App\Models\UsedTicket();
                $usedTicket->user_id = $user->id;
                $usedTicket->ticket_number = $appliedTicketNumber;
                $usedTicket->usage_type = 'investment';
                $usedTicket->discount_amount = $ticketDiscount;
                $usedTicket->investment_id = $invest->id;
                $usedTicket->metadata = $metadata;
                $usedTicket->status = 'used';
                $usedTicket->used_at = now();
                $usedTicket->save();
                
                // Mark lottery ticket as expired so it won't participate in lottery draws
                DB::update(
                    'UPDATE lottery_tickets SET status = ?, updated_at = NOW() WHERE ticket_number = ?', 
                    ['expired', $appliedTicketNumber]
                );
                
                Log::info("Ticket {$appliedTicketNumber} marked as used for investment and expired in lottery system");
                
            } catch (\Exception $e) {
                Log::warning('Failed to mark ticket as used: ' . $e->getMessage());
            }
        }

        // Process first purchase commission and special lottery ticket using ORIGINAL amount
        if ($user->ref_by) {
            // Check if sponsor qualifies for special tokens
            $sponsorQualified = $this->checkSponsorQualification($user->ref_by);
            
            $firstPurchase = \App\Models\FirstPurchaseCommission::isFirstPurchase($user->id);
            if ($firstPurchase) {
                if ($sponsorQualified) {
                    \App\Models\FirstPurchaseCommission::processFirstPurchase(
                        $user->id, 
                        $user->ref_by, 
                        $plan->id, 
                        $originalAmount // Use original amount for sponsor rewards
                    );
                    
                    Log::info("Special tokens/tickets created for qualified sponsor {$user->ref_by} from first purchase by user {$user->id}");
                } else {
                    Log::info("Sponsor {$user->ref_by} does not meet qualification requirements for special tokens from user {$user->id} first purchase");
                }
            } else {
                // For subsequent investments, still create sponsor tokens if original amount >= $25 AND sponsor is qualified
                if ($originalAmount >= 25 && $sponsorQualified) {
                    \App\Models\SpecialLotteryTicket::createForSponsor($user->ref_by, $user->id, $originalAmount);
                    Log::info("Special tokens created for qualified sponsor {$user->ref_by} from subsequent investment by user {$user->id}");
                } else if ($originalAmount >= 25 && !$sponsorQualified) {
                    Log::info("Sponsor {$user->ref_by} does not meet qualification requirements for special tokens from user {$user->id} investment");
                }
            }
        }

        // Regular referral commission system using ORIGINAL amount
        $commissionType = 'invest_commission';
        self::levelCommission($user, $originalAmount, $commissionType, $trx, $this->setting);

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = showAmount($originalAmount).' invested to '.$plan->name . ($tokenDiscount > 0 ? " (with \${$tokenDiscount} discount)" : '');
        $adminNotification->message = "User {$user->username} has invested " . showAmount($originalAmount) . " in {$plan->name}" . ($tokenDiscount > 0 ? " with a special discount of \${$tokenDiscount}" : '') . ".";
        $adminNotification->click_url = '#';
        $adminNotification->save();
    }

    /**
    * Invest process
    *
    * @param float $amount
    * @param string $wallet
    * @return void
    */
    public function invest($amount, $wallet){
        $plan = $this->plan;
        $user = $this->user;

        // Validate plan has proper time configuration
        if (empty($plan->time) || !is_numeric($plan->time)) {
            throw new \Exception('Invalid plan configuration: time field is missing or invalid');
        }

        $user->$wallet -= $amount;
        $user->save();

        $trx                        = getTrx();
        $transaction                = new Transaction();
        $transaction->user_id       = $user->id;
        $transaction->amount        = $amount;
        $transaction->post_balance  = $user->$wallet;
        $transaction->charge        = 0;
        $transaction->trx_type      = '-';
        $transaction->details       = 'Invested on ' . $plan->name;
        $transaction->trx           = $trx;
        $transaction->wallet_type   = $wallet;
        $transaction->remark        = 'invest';
        $transaction->save();

        $timeName = "Month";
        // $timeName = TimeSetting::where('time', $plan->time)->first();

        //start
        if ($plan->interest_type == 1) {
            $interestAmount = ($amount * $plan->interest) / 100;
        } else {
            $interestAmount = $plan->interest;
        }

        $period = ($plan->lifetime == 1) ? -1 : $plan->repeat_time;

        //$next = self::nextWorkingDay($plan->time);

        $shouldPay = -1;
        if ($period > 0) {
            $shouldPay = $interestAmount * $period;
        }

        $invest                 = new Invest();
        $invest->user_id        = $user->id;
        $invest->plan_id        = $plan->id;
        $invest->amount         = $amount;
        $invest->interest       = $interestAmount;
        $invest->period         = $period;
        $invest->time_name      = $timeName;
        $invest->hours          = (string)($plan->time ?? 24); // Convert to string and default to 24 hours if time is null
        $invest->next_time      = now()->addHours($plan->time ?? 24);
        $invest->should_pay     = $shouldPay;
        $invest->status         = 1;
        $invest->wallet_type    = $wallet;
        $invest->capital_status = $plan->capital_back;
        $invest->trx            = $trx;
        $invest->save();

        // Check if this is user's first investment and trigger congratulation email
        $this->checkAndSendFirstInvestmentEmail($user, $invest);

        // Process first purchase commission and special lottery ticket
        if ($user->ref_by) {
            // Check if sponsor qualifies for special tokens
            $sponsorQualified = $this->checkSponsorQualification($user->ref_by);
            
            $firstPurchase = \App\Models\FirstPurchaseCommission::isFirstPurchase($user->id);
            if ($firstPurchase) {
                if ($sponsorQualified) {
                    \App\Models\FirstPurchaseCommission::processFirstPurchase(
                        $user->id, 
                        $user->ref_by, 
                        $plan->id, 
                        $amount
                    );
                    
                    Log::info("Special tokens/tickets created for qualified sponsor {$user->ref_by} from first purchase by user {$user->id}");
                } else {
                    Log::info("Sponsor {$user->ref_by} does not meet qualification requirements for special tokens from user {$user->id} first purchase");
                }
            } else {
                // For subsequent investments, still create sponsor tokens if amount >= $25 AND sponsor is qualified
                if ($amount >= 25 && $sponsorQualified) {
                    \App\Models\SpecialLotteryTicket::createForSponsor($user->ref_by, $user->id, $amount);
                    Log::info("Special tokens created for qualified sponsor {$user->ref_by} from subsequent investment by user {$user->id}");
                } else if ($amount >= 25 && !$sponsorQualified) {
                    Log::info("Sponsor {$user->ref_by} does not meet qualification requirements for special tokens from user {$user->id} investment");
                }
            }
        }

        // Regular referral commission system
        // if ($this->setting->invest_commission == 1) {
            $commissionType = 'invest_commission';
            self::levelCommission($user, $amount, $commissionType, $trx, $this->setting);
        // }

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = showAmount($amount).' invested to '.$plan->name;
        $adminNotification->message = "User {$user->username} has invested " . showAmount($amount) . " in {$plan->name}.";
        $adminNotification->click_url = '#';
        $adminNotification->save();
    }

    /**
    * Get the next working day of the system
    *
    * @param integer $hours
    * @return string
    */
    // public static function nextWorkingDay($hours)
    // {
    //     $now = now();
    //     $setting = "Friday";
    //     while(0==0){
    //         $nextPossible = Carbon::parse($now)->addHours($hours)->toDateTimeString();

    //         if(!self::isHoliDay($nextPossible,$setting)){
    //             $next = $nextPossible;
    //             break;
    //         }
    //         $now = $now->addDay();
    //     }
    //     return $next;
    // }


    // /**
    // * Check the date is holiday or not
    // *
    // * @param string $date
    // * @param object $setting
    // * @return string
    // */
    // public static function isHoliDay($date,$setting){
    //     $isHoliday = true;
    //     $dayName = strtolower(date('D',strtotime($date)));
    //     $holiday = Holiday::where('date',date('Y-m-d',strtotime($date)))->count();
    //     $offDay = (array)$setting->off_day;

    //     if(!array_key_exists($dayName, $offDay)){
    //         if($holiday == 0){
    //             $isHoliday = false;
    //         }
    //     }

    //     return $isHoliday;

    // }

    /**
    * Give referral commission
    *
    * @param object $user
    * @param float $amount
    * @param string $commissionType
    * @param string $trx
    * @param object $setting
    * @return void
    */
    public static function levelCommission($user, $amount, $commissionType, $trx){
        $meUser = $user;
        $i = 1;
        // $level = 10;
        $level = Referral::where('commission_type',$commissionType)->count();
        $transactions = [];
        while ($i <= $level) {
            $me = $meUser;
            $refer = $me->referrer;
            if ($refer == "") {
                break;
            }

            $commission = Referral::where('commission_type',$commissionType)->where('level', $i)->first();
            if (!$commission) {
                break;
            }

            // $com = ($amount * 10) / 100;
            $com = ($amount * $commission->percent) / 100;
            $refer->interest_wallet += $com;
            $refer->save();

            $transactions[] = [
                'user_id' => $refer->id,
                'amount' => $com,
                'post_balance' => $refer->interest_wallet,
                'charge' => 0,
                'trx_type' => '+',
                'details' => 'level '.$i.' Referral Commission From ' . $user->username,
                'trx' => $trx,
                'wallet_type' =>  'interest_wallet',
                'remark'=>'referral_commission',
                'created_at'=>now()
            ];
            $meUser = $refer;
            $i++;
        }

        if (!empty($transactions)) {
            Transaction::insert($transactions);
        }
    }

    /**
     * Check if sponsor qualifies for special tokens/tickets
     * 
     * @param int $sponsorUserId
     * @return bool
     */
    private function checkSponsorQualification($sponsorUserId)
    {
        $sponsor = \App\Models\User::find($sponsorUserId);
        
        if (!$sponsor) {
            Log::warning("Sponsor user not found: {$sponsorUserId}");
            return false;
        }

        // Check if sponsor account is active
        if ($sponsor->status != 1) {
            Log::info("Sponsor {$sponsorUserId} is not active (status: {$sponsor->status})");
            return false;
        }

        // Check if sponsor has minimum investment amount
        $minimumSponsorInvestment = 25.00; // Configurable minimum investment
        $totalSponsorInvestment = \App\Models\Invest::where('user_id', $sponsorUserId)
                                                   ->where('status', 1) // Active investments only
                                                   ->sum('amount');

        if ($totalSponsorInvestment < $minimumSponsorInvestment) {
            Log::info("Sponsor {$sponsorUserId} does not meet minimum investment requirement. Has: ${totalSponsorInvestment}, Required: ${minimumSponsorInvestment}");
            return false;
        }

        // Check if sponsor has any active investment (not just historical)
        $hasActiveInvestment = \App\Models\Invest::where('user_id', $sponsorUserId)
                                                 ->where('status', 1) // Active status
                                                 ->where('next_time', '>', now()) // Still earning
                                                 ->exists();

        if (!$hasActiveInvestment) {
            Log::info("Sponsor {$sponsorUserId} does not have any active investments");
            return false;
        }

        Log::info("Sponsor {$sponsorUserId} qualified! Total investment: ${totalSponsorInvestment}, Has active investments: YES");
        return true;
    }

    /**
     * Check if this is user's first investment and send congratulation email
     *
     * @param object $user
     * @param object $invest
     * @return void
     */
    private function checkAndSendFirstInvestmentEmail($user, $invest)
    {
        try {
            // Check if this is the user's first investment
            $previousInvestments = Invest::where('user_id', $user->id)
                ->where('id', '!=', $invest->id)
                ->count();

            if ($previousInvestments === 0) {
                // This is their first investment, queue the congratulation email
                \App\Jobs\SendFirstInvestmentCongratulationJob::dispatch($user, $invest)
                    ->onQueue('emails');
                
                Log::info("First investment congratulation email queued for user: {$user->id}, investment: {$invest->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to queue first investment congratulation email: " . $e->getMessage());
        }
    }
}
