<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SpecialLotteryTicket;
use App\Models\SpecialTicketTransfer;
use App\Models\FirstPurchaseCommission;
use App\Models\LotteryTicket;
use App\Models\User;
use App\Services\SpecialTicketService;
use Carbon\Carbon;

class SpecialTicketController extends Controller
{
    protected $specialTicketService;

    public function __construct(SpecialTicketService $specialTicketService)
    {
        $this->specialTicketService = $specialTicketService;
    }

    /**
     * Display user's special lottery tickets
     */
    public function index()
    {
        $user = Auth::user();
        $pageTitle = 'My Special Tickets & Commission Lottery Dashboard';

        // Get user's special discount tokens
        $tickets = SpecialLotteryTicket::forSponsor($user->id)
                                     ->with(['lotteryDraw', 'referral', 'usedForPlan'])
                                     ->orderBy('purchased_at', 'desc')
                                     ->paginate(20);

        // Get available discount tokens (special tickets that can be used as discount)
        $availableTokens = SpecialLotteryTicket::forSponsor($user->id)
                                              ->where('status', 'active')
                                              ->where('is_valid_token', true)
                                              ->where('token_expires_at', '>', now())
                                              ->with(['lotteryDraw', 'referral'])
                                              ->orderBy('purchased_at', 'desc')
                                              ->get();

        // Get user's commission lottery tickets (worth $25 each, participates in weekly lottery)
        $commissionTickets = LotteryTicket::where('user_id', $user->id)
                                         ->where('payment_method', 'commission_reward')
                                         ->with(['lotteryDraw'])
                                         ->orderBy('purchased_at', 'desc')
                                         ->get();

        // Get transfer statistics
        $transferStats = $this->specialTicketService->getUserTransferStats($user->id);

        // Get statistics
        $stats = $this->specialTicketService->getUserTicketStats($user->id);
        $referralStats = $this->specialTicketService->getReferralTicketStats($user->id);

        // Get commission statistics
        $commissionStats = FirstPurchaseCommission::getSponsorStats($user->id);

        return view('user.special_tickets.index', compact(
            'tickets',
            'availableTokens',
            'commissionTickets',
            'transferStats',
            'stats',
            'referralStats',
            'commissionStats',
            'pageTitle'
        ));
    }

    /**
     * Display available tokens for plan purchases
     */
    public function tokens()
    {
        $user = Auth::user();
        $pageTitle = 'Special Discount Tokens';

        // Get available tokens
        $availableTokens = $this->specialTicketService->getAvailableTokens($user->id);

        // Get token usage history
        $usedTokens = SpecialLotteryTicket::forSponsor($user->id)
                                        ->where('status', 'used_as_token')
                                        ->with(['usedForPlan'])
                                        ->orderBy('used_as_token_at', 'desc')
                                        ->take(10)
                                        ->get();

        return view('user.special_tickets.tokens', compact(
            'availableTokens',
            'usedTokens',
            'pageTitle'
        ));
    }

    /**
     * Calculate discount for specific plan
     */
    public function calculateDiscount(Request $request)
    {
        $request->validate([
            'plan_amount' => 'required|numeric|min:0'
        ]);

        $user = Auth::user();
        $planAmount = $request->plan_amount;

        $discountInfo = $this->specialTicketService->calculatePotentialDiscount($user->id, $planAmount);

        return response()->json([
            'success' => true,
            'total_discount' => $discountInfo['total_discount'],
            'final_amount' => $discountInfo['final_amount'],
            'tokens_used' => $discountInfo['tokens_used'],
            'savings_percentage' => $planAmount > 0 ? round(($discountInfo['total_discount'] / $planAmount) * 100, 2) : 0
        ]);
    }

    /**
     * Display special ticket history
     */
    public function history()
    {
        $user = Auth::user();
        $pageTitle = 'Special Tickets History';

        // Get all tickets with filtering
        $query = SpecialLotteryTicket::forSponsor($user->id)
                                   ->with(['lotteryDraw', 'referral', 'usedForPlan']);

        // Apply filters if provided
        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('date_from')) {
            $query->whereDate('purchased_at', '>=', request('date_from'));
        }

        if (request('date_to')) {
            $query->whereDate('purchased_at', '<=', request('date_to'));
        }

        $tickets = $query->orderBy('purchased_at', 'desc')->paginate(20);

        // Summary statistics for filtered results
        $filteredStats = [
            'total_tickets' => $query->count(),
            'total_discount_used' => $query->where('status', 'used_as_token')->sum('token_discount_amount'),
            'total_refunds' => $query->where('status', 'refunded')->sum('refund_amount'),
            'active_tokens' => $query->where('status', 'active')->where('is_valid_token', true)->count(),
        ];

        return view('user.special_tickets.history', compact(
            'tickets',
            'filteredStats',
            'pageTitle'
        ));
    }

    /**
     * Display special ticket statistics
     */
    public function statistics()
    {
        try {
            $user = Auth::user();
            $pageTitle = 'Special Tickets Statistics';

            // Get comprehensive statistics
            $stats = $this->specialTicketService->getUserTicketStats($user->id);
            $referralStats = $this->specialTicketService->getReferralTicketStats($user->id);
            $commissionStats = FirstPurchaseCommission::getSponsorStats($user->id);

            // Monthly breakdown - get last 12 months of commission activity
            $monthlyStats = FirstPurchaseCommission::where('sponsor_user_id', $user->id)
                ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as count, SUM(commission_amount) as total')
                ->groupBy('year', 'month')
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->take(12)
                ->get();
            
            // Token usage by plan - get plan usage statistics
            $planUsageStats = FirstPurchaseCommission::where('sponsor_user_id', $user->id)
                ->join('plans', 'first_purchase_commissions.plan_id', '=', 'plans.id')
                ->select('plans.name as plan_name')
                ->selectRaw('COUNT(*) as usage_count, SUM(commission_amount) as total_amount')
                ->groupBy('plans.id', 'plans.name')
                ->orderByDesc('usage_count')
                ->get();

            return view('user.special_tickets.statistics', compact(
                'stats',
                'referralStats', 
                'commissionStats',
                'monthlyStats',
                'planUsageStats',
                'pageTitle'
            ));
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Statistics page error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error page with details
            return response('<div class="container mt-5"><div class="alert alert-danger"><h4>Statistics Error</h4><p>' . $e->getMessage() . '</p></div></div>');
        }
    }

    /**
     * Show transfer interface
     */
    public function transfer()
    {
        $user = Auth::user();
        $availableTickets = $this->specialTicketService->getAvailableTokens($user->id);
        $transferStats = $this->specialTicketService->getUserTransferStats($user->id);
        
        return view('user.special_tickets.transfer', compact('availableTickets', 'transferStats'));
    }

    /**
     * Send transfer request
     */
    public function sendTransfer(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:special_lottery_tickets,id',
            'recipient_username' => 'required|exists:users,username',
            'transfer_type' => 'required|in:gift,sale,share',
            'amount' => 'nullable|numeric|min:0',
            'message' => 'nullable|string|max:255',
        ]);

        try {
            $recipient = User::where('username', $request->recipient_username)->firstOrFail();
            
            $transfer = $this->specialTicketService->transferToken(
                $request->ticket_id,
                Auth::id(),
                $recipient->id,
                $request->transfer_type,
                $request->amount ?? 0,
                $request->message
            );

            $notify[] = ['success', 'Transfer request sent successfully! Transfer code: ' . $transfer->transfer_code];
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /**
     * Show incoming transfers
     */
    public function incoming()
    {
        $user = Auth::user();
        $incomingTransfers = SpecialTicketTransfer::incoming($user->id)
                                                 ->with(['specialTicket', 'fromUser'])
                                                 ->orderBy('created_at', 'desc')
                                                 ->paginate(15);
        
        return view('user.special_tickets.incoming', compact('incomingTransfers'));
    }

    /**
     * Show outgoing transfers
     */
    public function outgoing()
    {
        $user = Auth::user();
        $outgoingTransfers = SpecialTicketTransfer::outgoing($user->id)
                                                 ->with(['specialTicket', 'toUser'])
                                                 ->orderBy('created_at', 'desc')
                                                 ->paginate(15);
        
        return view('user.special_tickets.outgoing', compact('outgoingTransfers'));
    }

    /**
     * Accept transfer
     */
    public function acceptTransfer($transferId)
    {
        try {
            $transfer = $this->specialTicketService->acceptTransfer($transferId, Auth::id());
            
            $notify[] = ['success', 'Transfer accepted successfully!'];
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /**
     * Reject transfer
     */
    public function rejectTransfer($transferId)
    {
        try {
            $transfer = SpecialTicketTransfer::findOrFail($transferId);
            
            if ($transfer->to_user_id != Auth::id()) {
                throw new \Exception('Unauthorized action.');
            }

            $transfer->rejectTransfer();
            
            $notify[] = ['success', 'Transfer rejected successfully!'];
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /**
     * Cancel transfer
     */
    public function cancelTransfer($transferId)
    {
        try {
            $transfer = SpecialTicketTransfer::findOrFail($transferId);
            
            if ($transfer->from_user_id != Auth::id()) {
                throw new \Exception('Unauthorized action.');
            }

            $transfer->cancelTransfer();
            
            $notify[] = ['success', 'Transfer cancelled successfully!'];
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }
}
