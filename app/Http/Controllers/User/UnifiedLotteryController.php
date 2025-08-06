<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LotteryDraw;
use App\Models\LotterySetting;
use App\Models\SpecialLotteryTicket;
use App\Models\User;
use App\Services\SpecialTicketService;
use Carbon\Carbon;
use Exception;

class UnifiedLotteryController extends Controller
{
    protected $specialTicketService;

    public function __construct(SpecialTicketService $specialTicketService)
    {
        $this->middleware('auth');
        $this->specialTicketService = $specialTicketService;
    }

    /**
     * Display the unified lottery dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get current lottery draw
        $currentDraw = LotteryDraw::where('status', 'pending')->first();
        $lotterySettings = LotterySetting::getSettings();
        
        // Unified ticket statistics using the lottery_tickets table
        $lotteryTicketsCount = \App\Models\LotteryTicket::where('user_id', $user->id)
            ->where('token_type', 'lottery')
            ->where('status', 'active')
            ->count();
            
        $specialTokensCount = \App\Models\LotteryTicket::where('current_owner_id', $user->id)
            ->where('token_type', 'special')
            ->where('is_valid_token', true)
            ->where('status', 'active')
            ->whereNull('used_as_token_at')
            ->count();
            
        $sponsorTicketsCount = \App\Models\LotteryTicket::where('current_owner_id', $user->id)
            ->where('token_type', 'sponsor')
            ->where('is_valid_token', true)
            ->where('status', 'active')
            ->whereNull('used_as_token_at')
            ->count();
        
        // Total winnings from lottery_tickets table - only from completed/drawn draws, not pending
        $totalWinnings = \App\Models\LotteryTicket::where('user_id', $user->id)
            ->where('status', 'winner')
            ->whereNotNull('prize_amount')
            ->whereHas('lotteryDraw', function($query) {
                $query->whereIn('status', ['drawn', 'completed']);
            })
            ->sum('prize_amount') ?? 0;
        
        // Available tokens for use
        $availableTokensCount = $specialTokensCount + $sponsorTicketsCount;
        
        // Get all tokens (special + sponsor) for unified view
        $allTokens = \App\Models\LotteryTicket::where('current_owner_id', $user->id)
            ->whereIn('token_type', ['special', 'sponsor'])
            ->where('is_valid_token', true)
            ->where('status', 'active')
            ->with(['sponsor', 'referralUser', 'currentOwner', 'originalOwner'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get available tokens with details (active and unused)
        $availableTokens = \App\Models\LotteryTicket::where('current_owner_id', $user->id)
            ->whereIn('token_type', ['special', 'sponsor'])
            ->where('is_valid_token', true)
            ->where('status', 'active')
            ->whereNull('used_as_token_at')
            ->whereNull('used_for_plan_id')
            ->with(['sponsor', 'referralUser', 'currentOwner'])
            ->get();
        
        // Transfer stats (simplified)
        $transferStats = [
            'sent' => \App\Models\LotteryTicket::where('original_owner_id', $user->id)
                ->where('current_owner_id', '!=', $user->id)
                ->count(),
            'received' => \App\Models\LotteryTicket::where('current_owner_id', $user->id)
                ->where('original_owner_id', '!=', $user->id)
                ->count(),
            'total' => 0
        ];
        $transferStats['total'] = $transferStats['sent'] + $transferStats['received'];
        
        // Financial data
        $totalBalance = ($user->deposit_wallet ?? 0) + ($user->interest_wallet ?? 0);
        
        // Recent activity
        $recentActivity = $this->getRecentActivity($user);
        
        // Sharing stats
        $sharingStats = $this->getSharingStats($user);
        
        // Next lottery draw info
        $nextDraw = $this->getNextDrawInfo($currentDraw);
        
        // Get detailed winning statistics
        $winningStats = $this->getWinningStats($user);
        
        return view('user.lottery.unified.index', compact(
            'user',
            'lotteryTicketsCount',
            'specialTokensCount',
            'sponsorTicketsCount',
            'totalWinnings',
            'totalBalance',
            'recentActivity',
            'sharingStats',
            'nextDraw',
            'lotterySettings',
            'currentDraw',
            'availableTokens',
            'allTokens',
            'availableTokensCount',
            'transferStats',
            'winningStats'
        ));
    }

    /**
     * Get detailed winning statistics for the user
     */
    private function getWinningStats($user)
    {
        // Get winning tickets from lottery_tickets table - only from completed draws
        $winningTickets = \App\Models\LotteryTicket::where('user_id', $user->id)
            ->where('status', 'winner')
            ->whereNotNull('prize_amount')
            ->whereHas('lotteryDraw', function($query) {
                $query->whereIn('status', ['drawn', 'completed']);
            })
            ->with(['lotteryDraw'])
            ->get();
        
        // Get winner records from lottery_winners table - only from completed draws
        $winnerRecords = \App\Models\LotteryWinner::where('user_id', $user->id)
            ->whereNotNull('prize_amount')
            ->whereHas('lotteryDraw', function($query) {
                $query->whereIn('status', ['drawn', 'completed']);
            })
            ->with(['lotteryDraw', 'lotteryTicket'])
            ->get();
        
        // Filter out any records with pending draws (extra safety check)
        $winningTickets = $winningTickets->filter(function($ticket) {
            return $ticket->lotteryDraw && in_array($ticket->lotteryDraw->status, ['drawn', 'completed']);
        });
        
        $winnerRecords = $winnerRecords->filter(function($winner) {
            return $winner->lotteryDraw && in_array($winner->lotteryDraw->status, ['drawn', 'completed']);
        });
        
        // Calculate combined statistics
        $totalWinAmount = $winningTickets->sum('prize_amount') + $winnerRecords->sum('prize_amount');
        $totalWins = $winningTickets->count() + $winnerRecords->count();
        
        // Get biggest win
        $biggestTicketWin = $winningTickets->max('prize_amount') ?? 0;
        $biggestWinnerWin = $winnerRecords->max('prize_amount') ?? 0;
        $biggestWin = max($biggestTicketWin, $biggestWinnerWin);
        
        // Get recent wins (last 30 days)
        $recentWinningTickets = $winningTickets->where('updated_at', '>=', now()->subDays(30));
        $recentWinnerRecords = $winnerRecords->where('created_at', '>=', now()->subDays(30));
        $recentWinAmount = $recentWinningTickets->sum('prize_amount') + $recentWinnerRecords->sum('prize_amount');
        
        // Get claim status summary
        $claimedAmount = $winnerRecords->where('claim_status', 'claimed')->sum('prize_amount');
        $pendingAmount = $winnerRecords->where('claim_status', 'pending')->sum('prize_amount');
        
        return [
            'total_wins' => $totalWins,
            'total_win_amount' => $totalWinAmount,
            'biggest_win' => $biggestWin,
            'recent_win_amount' => $recentWinAmount,
            'claimed_amount' => $claimedAmount,
            'pending_amount' => $pendingAmount,
            'winning_tickets' => $winningTickets,
            'winner_records' => $winnerRecords,
            'win_rate' => $this->calculateWinRate($user)
        ];
    }

    /**
     * Calculate user's lottery win rate
     */
    private function calculateWinRate($user)
    {
        // Count total lottery tickets from completed draws only
        $totalTickets = \App\Models\LotteryTicket::where('user_id', $user->id)
            ->where('token_type', 'lottery')
            ->whereHas('lotteryDraw', function($query) {
                $query->whereIn('status', ['drawn', 'completed']);
            })
            ->count();
        
        // Count winning tickets from completed draws only
        $winningTickets = \App\Models\LotteryTicket::where('user_id', $user->id)
            ->where('status', 'winner')
            ->whereHas('lotteryDraw', function($query) {
                $query->whereIn('status', ['drawn', 'completed']);
            })
            ->count();
        
        if ($totalTickets == 0) {
            return 0;
        }
        
        return round(($winningTickets / $totalTickets) * 100, 2);
    }

    /**
     * Get recent activity for the user (unified version)
     */
    private function getRecentActivity($user)
    {
        $activities = collect();
        
        // Recent lottery ticket purchases
        $lotteryTickets = \App\Models\LotteryTicket::where('user_id', $user->id)
            ->where('token_type', 'lottery')
            ->latest()
            ->limit(5)
            ->get();
            
        foreach ($lotteryTickets as $ticket) {
            $activities->push([
                'type' => 'lottery_purchase',
                'type_class' => 'bg-success',
                'icon' => 'fe fe-ticket',
                'title' => 'Lottery Ticket Purchased',
                'description' => "Purchased lottery ticket #{$ticket->ticket_number}",
                'time_ago' => $ticket->created_at->diffForHumans(),
                'created_at' => $ticket->created_at
            ]);
        }
        
        // Recent special token activities
        $specialTokens = \App\Models\LotteryTicket::where('current_owner_id', $user->id)
            ->whereIn('token_type', ['special', 'sponsor'])
            ->latest()
            ->limit(3)
            ->get();
            
        foreach ($specialTokens as $token) {
            $activities->push([
                'type' => 'token_received',
                'type_class' => 'bg-warning',
                'icon' => 'fe fe-star',
                'title' => 'Special Token Received',
                'description' => "Received {$token->token_type} token #{$token->ticket_number}",
                'time_ago' => $token->created_at->diffForHumans(),
                'created_at' => $token->created_at
            ]);
        }
        
        // Recent lottery wins - only from completed/drawn draws, not pending ones
        $lotteryWins = \App\Models\LotteryTicket::where('user_id', $user->id)
            ->where('status', 'winner')
            ->whereNotNull('prize_amount')
            ->whereHas('lotteryDraw', function($query) {
                $query->whereIn('status', ['drawn', 'completed']);
            })
            ->with(['lotteryDraw']) // Include draw information
            ->latest()
            ->limit(5) // Show more wins
            ->get();
            
        foreach ($lotteryWins as $win) {
            // Only show if draw is actually completed
            if ($win->lotteryDraw && in_array($win->lotteryDraw->status, ['drawn', 'completed'])) {
                $drawInfo = " (Draw #{$win->lotteryDraw->id} - {$win->lotteryDraw->status})";
                $activities->push([
                    'type' => 'lottery_win',
                    'type_class' => 'bg-danger',
                    'icon' => 'fe fe-trophy',
                    'title' => 'Lottery Win! 🏆',
                    'description' => "Won $" . number_format($win->prize_amount, 2) . " with ticket #{$win->ticket_number}" . $drawInfo,
                    'time_ago' => $win->updated_at ? $win->updated_at->diffForHumans() : $win->created_at->diffForHumans(),
                    'created_at' => $win->updated_at ?? $win->created_at,
                    'amount' => $win->prize_amount
                ]);
            }
        }
        
        // Also check lottery_winners table for additional win records - only from completed draws
        $additionalWins = \App\Models\LotteryWinner::where('user_id', $user->id)
            ->whereNotNull('prize_amount')
            ->whereHas('lotteryDraw', function($query) {
                $query->whereIn('status', ['drawn', 'completed']);
            })
            ->with(['lotteryDraw', 'lotteryTicket'])
            ->latest()
            ->limit(3)
            ->get();
            
        foreach ($additionalWins as $winner) {
            // Only show if draw is actually completed
            if ($winner->lotteryDraw && in_array($winner->lotteryDraw->status, ['drawn', 'completed'])) {
                $ticketInfo = $winner->lotteryTicket ? "#{$winner->lotteryTicket->ticket_number}" : "#N/A";
                $drawInfo = " (Draw #{$winner->lotteryDraw->id} - {$winner->lotteryDraw->status})";
                $positionInfo = $winner->prize_position ? " - Position {$winner->prize_position}" : "";
                
                $activities->push([
                    'type' => 'lottery_winner',
                    'type_class' => 'bg-warning',
                    'icon' => 'fe fe-award',
                    'title' => 'Prize Winner! 🥇',
                    'description' => "Won $" . number_format($winner->prize_amount, 2) . " with ticket {$ticketInfo}" . $drawInfo . $positionInfo,
                    'time_ago' => $winner->created_at->diffForHumans(),
                    'created_at' => $winner->created_at,
                    'amount' => $winner->prize_amount,
                    'claim_status' => $winner->claim_status ?? 'pending'
                ]);
            }
        }
        
        // Sort by date and take latest 10
        return $activities->sortByDesc('created_at')->take(10);
    }

    /**
     * Get sharing statistics for the user
     */
    private function getSharingStats($user)
    {
        // Get referral statistics
        $totalReferrals = User::where('ref_by', $user->id)->count();
        $activeReferrals = User::where('ref_by', $user->id)
            ->whereHas('invests', function($query) {
                $query->where('status', 1);
            })->count();
        
        // Calculate potential earnings from sharing
        $potentialEarnings = $totalReferrals * 5; // Example: $5 per referral
        
        // Get user's referral link
        $referralLink = route('register') . '?ref=' . $user->username;
        
        return [
            'total_referrals' => $totalReferrals,
            'active_referrals' => $activeReferrals,
            'potential_earnings' => $potentialEarnings,
            'referral_link' => $referralLink,
            'share_count' => 0, // Track share events if implemented
        ];
    }

    /**
     * Get next lottery draw information
     */
    private function getNextDrawInfo($currentDraw)
    {
        if (!$currentDraw) {
            return [
                'exists' => false,
                'time_remaining' => 'No active draw',
                'total_prize' => 0,
                'participants' => 0
            ];
        }
        
        $timeRemaining = $currentDraw->draw_date->diffForHumans();
        $totalPrize = $currentDraw->total_prize_pool ?? 0;
        $participants = $currentDraw->tickets()->count(); // Show total ticket count instead of unique participants
        
        return [
            'exists' => true,
            'time_remaining' => $timeRemaining,
            'total_prize' => $totalPrize,
            'participants' => $participants,
            'draw_date' => $currentDraw->draw_date,
            'tickets_sold' => $currentDraw->tickets()->count()
        ];
    }

    /**
     * Handle share tracking
     */
    public function trackShare(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'platform' => 'required|string',
            'content_type' => 'required|string'
        ]);
        
        // Log share event (implement your tracking logic)
        Log::info("User {$user->id} shared on {$request->platform} - {$request->content_type}");
        
        // You could implement a sharing rewards system here
        // Example: Give points or tokens for sharing
        
        return response()->json(['success' => true]);
    }

    /**
     * Get lottery countdown for AJAX or redirect to main lottery page
     */
    public function getCountdown(Request $request)
    {
        $currentDraw = LotteryDraw::where('status', 'pending')->first();
        
        // If this is an AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            if (!$currentDraw) {
                return response()->json(['countdown' => 'No active draw']);
            }
            
            $timeRemaining = $currentDraw->draw_date->diffForHumans();
            return response()->json(['countdown' => $timeRemaining]);
        }
        
        // If someone accesses this URL directly, redirect them to the main lottery page
        return redirect()->route('lottery.unified.index')->with('info', 'Redirected to main lottery dashboard.');
    }

    /**
     * Display all activity for the user
     */
    public function allActivity()
    {
        $user = Auth::user();
        
        // Get comprehensive activity data
        $activities = $this->getRecentActivity($user);
        
        return view('user.lottery.unified.activity', compact('activities', 'user'));
    }

    /**
     * Display user's lottery wins and prizes
     */
    public function myWins()
    {
        $user = Auth::user();
        
        // Get all winning tickets - only from completed draws
        $winningTickets = \App\Models\LotteryTicket::where('user_id', $user->id)
            ->where('status', 'winner')
            ->whereNotNull('prize_amount')
            ->whereHas('lotteryDraw', function($query) {
                $query->whereIn('status', ['drawn', 'completed']);
            })
            ->with(['lotteryDraw'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        
        // Get all winner records - only from completed draws
        $winnerRecords = \App\Models\LotteryWinner::where('user_id', $user->id)
            ->whereNotNull('prize_amount')
            ->whereHas('lotteryDraw', function($query) {
                $query->whereIn('status', ['drawn', 'completed']);
            })
            ->with(['lotteryDraw', 'lotteryTicket'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get winning statistics
        $winningStats = $this->getWinningStats($user);
        
        return view('user.lottery.unified.my-wins', compact(
            'user',
            'winningTickets',
            'winnerRecords',
            'winningStats'
        ));
    }

    /**
     * Show available plans that can use tokens
     */
    public function availablePlans()
    {
        $user = Auth::user();
        
        // Get all available active plans
        $plans = \App\Models\Plan::where('status', 1)
            ->orderBy('minimum', 'asc')
            ->get();
        
        // Initialize token variables with safe defaults
        $availableTokens = collect(); // Empty collection
        $availableTokensCount = 0;
        $totalTokenValue = 0.00;
        
        try {
            // Get user's available tokens from unified lottery_tickets table
            $availableTokens = \App\Models\LotteryTicket::where('current_owner_id', $user->id)
                ->whereIn('token_type', ['special', 'sponsor'])
                ->where('is_valid_token', true)
                ->where('is_transferable', true)
                ->where('status', 'active')
                ->whereNull('used_as_token_at')
                ->whereNull('used_for_plan_id')
                ->where(function($query) {
                    $query->whereNull('token_expires_at')
                          ->orWhere('token_expires_at', '>', now());
                })
                ->with(['sponsor', 'referralUser', 'currentOwner', 'originalOwner'])
                ->orderBy('token_discount_amount', 'desc')
                ->get();
            
            $availableTokensCount = $availableTokens->count();
            $totalTokenValue = $availableTokens->sum('token_discount_amount') ?: ($availableTokensCount * 2.00);
            
        } catch (Exception $e) {
            // If any error occurs, use defaults (already set above)
            Log::warning('Error fetching available tokens: ' . $e->getMessage());
        }
        
        return view('user.lottery.unified.available-plans', compact(
            'user',
            'plans', 
            'availableTokens',
            'availableTokensCount',
            'totalTokenValue'
        ));
    }

    /**
     * Transfer special tokens
     */
    public function transferTokens(Request $request)
    {
        $request->validate([
            'recipient_username' => 'required|string|exists:users,username',
            'token_ids' => 'required|array',
            'token_ids.*' => 'exists:lottery_tickets,id',
            'message' => 'nullable|string|max:500'
        ]);
        
        $user = Auth::user();
        $recipient = User::where('username', $request->recipient_username)->first();
        
        if ($recipient->id === $user->id) {
            return back()->with('error', 'You cannot transfer tokens to yourself.');
        }
        
        try {
            $transferResults = [];
            $tokenIds = is_array($request->token_ids) ? $request->token_ids : [$request->token_ids];
            
            DB::beginTransaction();
            
            foreach ($tokenIds as $tokenId) {
                // Get the token from unified lottery_tickets table
                $token = \App\Models\LotteryTicket::where('id', $tokenId)
                    ->where('current_owner_id', $user->id)
                    ->whereIn('token_type', ['special', 'sponsor'])
                    ->where('is_transferable', true)
                    ->where('is_valid_token', true)
                    ->whereNull('used_as_token_at')
                    ->first();
                
                if (!$token) {
                    throw new \Exception("Token #{$tokenId} not found, not owned by you, or not transferable.");
                }
                
                // Transfer the token to the recipient
                $token->update([
                    'current_owner_id' => $recipient->id,
                    'transfer_count' => $token->transfer_count + 1,
                    'last_transferred_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Log the transfer
                Log::info('Token transferred', [
                    'token_id' => $token->id,
                    'token_number' => $token->ticket_number,
                    'from_user_id' => $user->id,
                    'to_user_id' => $recipient->id,
                    'transfer_count' => $token->transfer_count,
                    'message' => $request->message
                ]);
                
                $transferResults[] = [
                    'token_id' => $token->id,
                    'token_number' => $token->ticket_number,
                    'success' => true
                ];
            }
            
            DB::commit();
            
            $count = count($transferResults);
            return back()->with('success', "Successfully transferred {$count} token(s) to {$recipient->username}!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Token transfer failed', [
                'user_id' => $user->id,
                'recipient_username' => $request->recipient_username,
                'token_ids' => $tokenIds ?? [],
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }

    /**
     * Use special tokens as lottery tickets
     */
    public function useToken(Request $request)
    {
        $request->validate([
            'token_ids' => 'required|array',
            'token_ids.*' => 'integer',
            'lottery_draw_id' => 'required|exists:lottery_draws,id'
        ]);
        
        $user = Auth::user();
        
        // Check if lottery draw is still accepting entries
        $lotteryDraw = LotteryDraw::find($request->lottery_draw_id);
        if (!$lotteryDraw || $lotteryDraw->status !== 'pending') {
            return back()->with('error', 'Lottery draw is not accepting entries.');
        }
        
        try {
            // Use VirtualTicketService to handle token usage
            $virtualTicketService = app(\App\Services\VirtualTicketService::class);
            
            $usedCount = 0;
            $errors = [];
            
            foreach ($request->token_ids as $tokenId) {
                // Use the unified lottery_tickets table
                $token = \App\Models\LotteryTicket::where('id', $tokenId)
                    ->where('user_id', $user->id)
                    ->where('token_type', 'special')
                    ->first();
                
                if (!$token) {
                    $errors[] = "Token #{$tokenId} not found or not owned by you.";
                    continue;
                }
                
                if ($token->used_as_token_at) {
                    $errors[] = "Token #{$token->ticket_number} has already been used.";
                    continue;
                }
                
                if (!$token->is_valid_token || $token->status !== 'active') {
                    $errors[] = "Token #{$token->ticket_number} is not valid for use.";
                    continue;
                }
                
                // Mark token as used for lottery draw
                $result = $virtualTicketService->markTicketAsUsed($token->id, $lotteryDraw->id);
                
                if ($result) {
                    // Update additional fields for lottery usage
                    $token->update([
                        'lottery_draw_id' => $lotteryDraw->id,
                        'used_for_plan_id' => null, // Clear plan usage if any
                        'updated_at' => now()
                    ]);
                    
                    $usedCount++;
                    
                    Log::info("Token used for lottery", [
                        'token_id' => $token->id,
                        'token_number' => $token->ticket_number,
                        'user_id' => $user->id,
                        'lottery_draw_id' => $lotteryDraw->id
                    ]);
                } else {
                    $errors[] = "Failed to use token #{$token->ticket_number}.";
                }
            }
            
            if ($usedCount > 0) {
                $message = "Successfully used {$usedCount} token(s) for lottery draw #{$lotteryDraw->id}.";
                if (!empty($errors)) {
                    $message .= " Some tokens could not be used: " . implode(', ', $errors);
                }
                return back()->with('success', $message);
            } else {
                return back()->with('error', 'No tokens could be used. ' . implode(', ', $errors));
            }
            
        } catch (\Exception $e) {
            Log::error('Token usage failed', [
                'user_id' => $user->id,
                'lottery_draw_id' => $request->lottery_draw_id,
                'token_ids' => $request->token_ids,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to use tokens: ' . $e->getMessage());
        }
    }

    /**
     * Share system page
     */
    public function shareSystem()
    {
        $user = Auth::user();
        $sharingStats = $this->getSharingStats($user);
        
        return view('user.lottery.share', compact('user', 'sharingStats'));
    }

    /**
     * Check if user is eligible for token discount on a specific plan
     */
    private function checkTokenDiscountEligibility($user, $plan)
    {
        try {
            // Check if user has any active special tokens
            $hasActiveTokens = \App\Models\LotteryTicket::where('current_owner_id', $user->id)
                ->whereIn('token_type', ['special', 'sponsor'])
                ->where('is_valid_token', true)
                ->where('status', 'active')
                ->whereNull('used_as_token_at')
                ->exists();
            
            // Basic eligibility: has tokens and plan allows investments
            return $hasActiveTokens && $plan->status == 1;
        } catch (Exception $e) {
            // If any error occurs, default to false
            return false;
        }
    }

    /**
     * Create a new token in the unified lottery_tickets table
     */
    public function createToken(Request $request)
    {
        $request->validate([
            'token_type' => 'required|in:special,sponsor',
            'recipient_user_id' => 'required|exists:users,id',
            'token_discount_amount' => 'required|numeric|min:0.01|max:1000',
            'is_transferable' => 'boolean',
            'token_expires_at' => 'nullable|date|after:now',
            'message' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        
        try {
            DB::beginTransaction();
            
            // Generate unique ticket number
            $ticketNumber = $this->generateUniqueTicketNumber();
            
            // Create the token in lottery_tickets table
            $token = \App\Models\LotteryTicket::create([
                'ticket_number' => $ticketNumber,
                'user_id' => $request->recipient_user_id,
                'lottery_draw_id' => 0, // 0 for tokens, will be set when used for lottery
                'ticket_price' => $request->token_discount_amount,
                'purchased_at' => now(),
                'status' => 'active',
                'token_type' => $request->token_type,
                'sponsor_user_id' => $user->id, // Creator as sponsor
                'referral_user_id' => null,
                'current_owner_id' => $request->recipient_user_id,
                'original_owner_id' => $request->recipient_user_id,
                'is_valid_token' => true,
                'is_transferable' => $request->is_transferable ?? false,
                'transfer_count' => 0,
                'token_discount_amount' => $request->token_discount_amount,
                'early_usage_bonus' => 0.00,
                'token_expires_at' => $request->token_expires_at,
                'payment_method' => 'admin_grant',
                'is_virtual' => false
            ]);
            
            DB::commit();
            
            Log::info('Token created successfully', [
                'token_id' => $token->id,
                'token_number' => $token->ticket_number,
                'token_type' => $request->token_type,
                'creator_id' => $user->id,
                'recipient_id' => $request->recipient_user_id,
                'discount_amount' => $request->token_discount_amount
            ]);
            
            return back()->with('success', "Token #{$token->ticket_number} created successfully!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Token creation failed', [
                'creator_id' => $user->id,
                'recipient_id' => $request->recipient_user_id,
                'token_type' => $request->token_type,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Token creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique ticket number for tokens
     */
    private function generateUniqueTicketNumber()
    {
        do {
            // Generate format: TKN-YYYYMMDD-HHMMSS-XXXX (TKN prefix for tokens)
            $ticketNumber = 'TKN-' . date('Ymd-His') . '-' . strtoupper(substr(uniqid(), -4));
        } while (\App\Models\LotteryTicket::where('ticket_number', $ticketNumber)->exists());
        
        return $ticketNumber;
    }
}
