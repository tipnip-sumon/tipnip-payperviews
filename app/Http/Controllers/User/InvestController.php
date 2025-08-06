<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GatewayCurrency;
use App\Models\Invest;
use App\Models\Plan;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Api\TicketValidationController;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
// use App\Lib\HyipLab;
use App\Lib\TipNipLab;
// use Carbon\Carbon;

class InvestController extends Controller
{
    public function index()
    {
        $pageTitle = 'Deposit for Ads Viewing';
        $user = Auth::user();
        
        // For first-time users, show all plans. For existing users, show eligible upgrades
        if ($user && $user->invests()->where('status', 1)->exists()) {
            // Existing depositor - show upgrade options
            $currentDeposit = $user->invests()->where('status', 1)->first();
            $currentPlanId = $currentDeposit ? $currentDeposit->plan_id : 0;
            $plans = Plan::where('status', 1)->where('id', '>', $currentPlanId)->get();
        } else {
            // First-time user - show all plans
            $plans = Plan::where('status', 1)->get();
        }
        
        // Get user's deposit statistics
        $userStats = null;
        $specialTicketStats = null;
        if ($user) {
            $currentDeposit = $user->invests()->where('status', 1)->first();
            $userStats = [
                'total_deposits' => $user->invests()->count(),
                'current_deposit' => $currentDeposit,
                'current_plan' => $currentDeposit && $currentDeposit->plan ? $currentDeposit->plan->name : null,
                'current_amount' => $currentDeposit ? $currentDeposit->amount : 0,
                'highest_plan' => $currentDeposit ? $currentDeposit->plan_id : 0,
                'has_plan_one' => $user->invests()->where('plan_id', 1)->exists(),
                'is_first_purchase' => \App\Models\FirstPurchaseCommission::isFirstPurchase($user->id),
            ];

            // Get special ticket statistics
            $specialTicketService = new \App\Services\SpecialTicketService();
            $specialTicketStats = $specialTicketService->getUserTicketStats($user->id);
            $availableTokens = $specialTicketService->getAvailableTokens($user->id);
            $specialTicketStats['available_tokens'] = $availableTokens;
            $specialTicketStats['usable_tokens_count'] = $availableTokens->count();
        }
        
        // Handle pre-selected token from URL parameter
        $preSelectedToken = null;
        if (request()->has('use_token')) {
            $tokenId = request()->get('use_token');
            $specialTicketService = new \App\Services\SpecialTicketService();
            $availableTokens = $specialTicketService->getAvailableTokens($user->id);
            $preSelectedToken = $availableTokens->where('id', $tokenId)->first();
        }
        
        return view('frontend.invest', compact('pageTitle', 'plans', 'userStats', 'specialTicketStats', 'preSelectedToken'));
    }
    public function getPlanAmount(Request $request)
    {
        $plan = Plan::where('status',1)->findOrFail($request->plan_id);
        $user = Auth::user();
        
        // Get special token discount calculation
        $specialTicketService = new \App\Services\SpecialTicketService();
        
        if ($plan->fixed_amount > 0) {
            $discountInfo = $specialTicketService->calculatePotentialDiscount($user->id, $plan->fixed_amount);
            
            return response()->json([
                'amount' => $plan->fixed_amount, 
                'interest' => 0, // No interest for deposits
                'time_name' => 'No fixed term', 
                'time' => 'Unlimited',
                'daily_video_limit' => $plan->daily_video_limit,
                'video_earning_rate' => $plan->video_earning_rate,
                'special_token_discount' => $discountInfo['total_discount'],
                'final_amount_after_discount' => $discountInfo['final_amount'],
                'available_tokens' => count($discountInfo['tokens_used']),
                'tokens_details' => $discountInfo['tokens_used']
            ]);
        }
        return response()->json([
            'minimum' => $plan->minimum, 
            'maximum' => $plan->maximum,
            'interest' => 0, // No interest for deposits
            'time_name' => 'No fixed term', 
            'time' => 'Unlimited',
            'daily_video_limit' => $plan->daily_video_limit,
            'video_earning_rate' => $plan->video_earning_rate
        ]);
    }
    public function invest(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return back()->with(['error' => 'You need to login first']);
        }
        if ($user->status != 1) {
            return back()->with(['error' => 'Your account is not active']);
        }
        
        $request->validate([
            'plan_amount'        => 'required|min:50|max:200|numeric',
            'plan_id'       => 'required',
            'wallet_type'   => 'required',
        ],
        [
            'plan_amount.required' => 'Deposit amount is required',
            'plan_amount.min'      => 'Minimum deposit amount is 50',
            'plan_amount.max'      => 'Maximum deposit amount is 200',
            'plan_id.required'     => 'Please select a plan',
        ]);
        
        // Check if user can deposit for the selected plan (only for existing investors)
        $existingDeposit = $user->getCurrentDeposit(); 
        if ($existingDeposit && !$user->canInvestInPlan($request->plan_id)) {
            return back()->with(['error' => 'You can only upgrade to plans higher than your current plan']);
        }
        
        $plan = Plan::where('status',1)->findOrFail($request->plan_id);
        
        // Validate that the plan has proper time configuration
        if (empty($plan->time) || !is_numeric($plan->time)) { 
            return back()->with(['error' => 'Invalid plan configuration. Please contact support.']);
        }
        
        $amount = $request->plan_amount;

        $wallet = $request->wallet_type;

        //Direct checkout
        if ($wallet != 'deposit_wallet' && $wallet != 'interest_wallet') {

            $gate = GatewayCurrency::whereHas('method', function ($gate) {
                $gate->where('status', 1);
            })->find($request->wallet_type);

            if (!$gate) {
                return back()->with(['error' => 'Invalid gateway']);
            }

            if ($gate->min_amount > $request->plan_amount || $gate->max_amount < $request->plan_amount) {
                return back()->with(['error' => 'Please follow deposit limit']);
            }

            $data = PaymentController::insertDeposit($gate,$request->plan_amount,$plan);
            session()->put('Track', $data->trx);
            return to_route('user.deposit.confirm');
        }

        if ($request->plan_amount > $user->$wallet) {
            return back()->with(['error' => 'Your balance is not sufficient']);
        }
        $password = $request->password;
        if (!Auth::attempt(['username' => $user->username, 'password' => $password])) {
            return back()->with(['error' => 'Invalid password']);
        }

        // Handle ticket discounts and special tokens
        $originalAmount = $amount; // This is the full plan amount for sponsor calculations
        $walletDeductionAmount = $amount; // This will be reduced by discounts
        $tokenDiscount = 0;
        $ticketDiscount = 0;
        $tokenDetails = [];
        $ticketDetails = null;
        $appliedTicketNumber = null;
        
        // Handle special token discount
        if ($request->has('use_special_tokens') && $request->use_special_tokens == 'yes') {
            $specialTicketService = new \App\Services\SpecialTicketService();
            $tokenResult = $specialTicketService->applyTokensToPlantPurchase($user->id, $plan->id, $amount);
            
            if ($tokenResult['success']) {
                $tokenDiscount = $tokenResult['total_discount'];
                $walletDeductionAmount = $tokenResult['final_amount']; // User pays discounted amount
                $tokenDetails = $tokenResult['tokens_used'];
                // Note: $originalAmount stays the same for sponsor commission calculation
            }
        }
        // Handle regular ticket discount
        elseif ($request->has('apply_ticket') && $request->apply_ticket == 'yes') {
            $ticketNumber = $request->input('ticket_number');
            
            if ($ticketNumber) {
                // Validate ticket using the validation controller logic
                $ticketController = new TicketValidationController();
                $validationRequest = new \Illuminate\Http\Request();
                $validationRequest->merge([
                    'ticket_number' => $ticketNumber,
                    'usage_type' => 'investment'
                ]);
                
                try {
                    $validationResponse = $ticketController->validateTicket($validationRequest);
                    $validationData = $validationResponse->getData(true);
                    
                    if ($validationData['success'] && $validationData['discount_percentage'] > 0) {
                        // Check if user has existing deposit for upgrade amount calculation
                        $existingDeposit = $user->getCurrentDeposit();
                        $upgradeAmount = $existingDeposit && $existingDeposit->amount < $amount 
                            ? $amount - $existingDeposit->amount 
                            : $amount;
                        
                        // Apply discount to upgrade amount only
                        $discountPercentage = $validationData['discount_percentage'] / 100;
                        $ticketDiscount = $upgradeAmount * $discountPercentage;
                        $walletDeductionAmount = $amount - $ticketDiscount; // Total amount minus discount
                        
                        $appliedTicketNumber = $ticketNumber;
                        $ticketDetails = [
                            'ticket_number' => $ticketNumber,
                            'discount_percentage' => $validationData['discount_percentage'],
                            'discount_amount' => $ticketDiscount,
                            'upgrade_amount' => $upgradeAmount,
                            'applied_to' => 'investment'
                        ];
                    }
                } catch (\Exception $e) {
                    // If ticket validation fails, proceed without discount
                    Log::warning('Ticket validation failed during investment: ' . $e->getMessage());
                }
            }
        }

        // Validate wallet balance against the amount user actually needs to pay
        if ($walletDeductionAmount > $user->$wallet) {
            return back()->with(['error' => 'Your balance is not sufficient for the discounted amount']);
        }

        // Check if user has existing deposit
        $existingDeposit = $user->getCurrentDeposit();
        
        // Always create new investment record - each investment should be tracked separately
        $hyip = new TipNipLab($user, $plan);
        // Pass original amount for sponsor calculations, but deduct discounted amount from wallet
        $hyip->investWithDiscount($originalAmount, $walletDeductionAmount, $wallet, $tokenDiscount, $tokenDetails, $ticketDiscount, $ticketDetails, $appliedTicketNumber);
        
        $message = 'Investment successful! You can now start watching ads and earning.';
        if ($tokenDiscount > 0) {
            $message .= " Special token discount of \${$tokenDiscount} applied!";
        }
        if ($ticketDiscount > 0) {
            $message .= " Ticket discount of \${$ticketDiscount} applied!";
        }
        
        return back()->with(['success' => $message]);
    }
    public function plans()
    {
        $pageTitle = 'Investment Plans';
        $plans = Plan::where('status',0)->get();
        return view('user.invest_plans',compact('pageTitle','plans'));
    }

    public function statistics()
    {
        $pageTitle = 'Invest Statistics';        $invests    = Invest::where('user_id',Auth::id())->orderBy('id','desc')->with('plan')->where('status',1)->paginate(10);
        $activePlan = Invest::where('user_id', Auth::id())->where('status', 1)->count();
        
        $investChart = Invest::where('user_id',Auth::id())->with('plan')->groupBy('plan_id')->select('plan_id')->selectRaw("SUM(amount) as investAmount")->orderBy('investAmount', 'desc')->get();
        return view('user.invest_statistics',compact('pageTitle','invests','investChart', 'activePlan'));
    }

    public function log(Request $request)
    {
        if($request->ajax()){
            $user = Auth::user()->id;
            $data = Invest::where('user_id',$user)->get();
            // $data = Invest::where('user_id',$user)->with('plan')->select('invests.*')
            //     ->where('status',1)
            //     ->orderBy('id','desc')
            //     ->get();
            return Datatables::of($data)
                ->addColumn('created_at', function($row){
                    return showDateTime($row->created_at);
                    // return Carbon::parse($row->created_at)->diffForHumans(); // For human readable format
                    // return Carbon::parse($row->created_at)->format('Y-m-d H:i:s'); // For specific format
                })
                ->addColumn('plan_name', function($row){
                    return $row->plan->name;
                })
                ->addColumn('amount', function($row){
                    return getAmount($row->amount*2);
                })
                ->addColumn('amounts', function($row){
                    return "$". showAmount($row->amount);
                })
                ->addColumn('work_bonus', function($row){
                    return "$0.25/Task";
                })
                ->addColumn('expire_in', function($row){
                    $date=date_create($row->created_at);
                    date_add($date,date_interval_create_from_date_string("30 days"));
                    return showDateTime($date);
                })
                ->addIndexColumn()
                ->make(true);
        }
        $pageTitle = 'Ads Plan Report';
        return view('frontend.task-list',compact('pageTitle'));
    }
}
