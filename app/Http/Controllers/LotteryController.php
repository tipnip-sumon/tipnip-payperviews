<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Models\LotteryWinner;
use App\Models\LotterySetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LotteryController extends Controller
{
    /**
     * Display lottery homepage
     */
    public function index()
    {
        try {
            $settings = LotterySetting::getSettings();
            
            if (!$settings->is_active) {
                return view('lottery.index')->with([
                    'pageTitle' => 'Lottery - Currently Inactive',
                    'settings' => $settings,
                    'currentDraw' => null,
                    'userTickets' => collect([]),
                    'userStats' => [
                        'total_tickets' => 0,
                        'total_wins' => 0,
                        'total_winnings' => 0,
                        'total_spent' => 0,
                        'kyc_verified' => false
                    ],
                    'recentWinners' => collect([]),
                    'currentDrawWinners' => collect([]),
                    'message' => 'Lottery system is currently inactive. Please check back later.',
                    'lottery_inactive' => true
                ]);
            }

            $currentDraw = LotteryDraw::where('status', 'pending')->first();
            $userTickets = collect([]);
            $userStats = [
                'total_tickets' => 0,
                'total_wins' => 0,
                'total_winnings' => 0,
                'total_spent' => 0,
                'kyc_verified' => false
            ];
            
            if (Auth::check()) {
                $user = Auth::user();
                
                // Check KYC status for user experience
                $kycVerified = $user->kv == 1;
                
                if ($currentDraw && $kycVerified) {
                    $userTickets = LotteryTicket::where('user_id', $user->id)
                                              ->where('status', 'active')
                                              ->where('lottery_draw_id', $currentDraw->id)
                                              ->with('lotteryDraw')
                                              ->latest()
                                              ->get();
                }
                
                // Calculate user statistics
                $userStats = [
                    'total_tickets' => LotteryTicket::where('user_id', $user->id)->where('status','active')->count(),
                    'total_wins' => LotteryWinner::where('user_id', $user->id)->count(),
                    'total_winnings' => LotteryWinner::where('user_id', $user->id)
                                                   ->where('claim_status', 'claimed')
                                                   ->sum('prize_amount'),
                    'total_spent' => LotteryTicket::where('user_id', $user->id)->sum('ticket_price'),
                    'kyc_verified' => $kycVerified
                ];
            }

            // Recent winners (public) - exclude current draw if it's pending
            $recentWinners = LotteryWinner::with(['user', 'lotteryDraw', 'lotteryTicket'])
                                        ->whereHas('lotteryDraw', function($query) {
                                            $query->where('status', '!=', 'pending');
                                        })
                                        ->latest()
                                        ->take(5)
                                        ->get();

            // Current draw winners (if manual winners are selected)
            $currentDrawWinners = collect([]);
            if ($currentDraw && $currentDraw->has_manual_winners) {
                $currentDrawWinners = LotteryWinner::with(['user', 'lotteryTicket'])
                                                 ->where('lottery_draw_id', $currentDraw->id)
                                                 ->orderBy('prize_position')
                                                 ->get();
            }

            return view('lottery.index', compact( 
                'settings',
                'currentDraw', 
                'userTickets',
                'userStats',
                'recentWinners',
                'currentDrawWinners'
            ))->with('pageTitle', 'Lottery - Win Big Every Draw!');

        } catch (Exception $e) {
            Log::error('Lottery index error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load lottery page.');
        }
    }

    /**
     * Purchase a lottery ticket
     */
    public function buyTicket(Request $request)
    {
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please login to purchase tickets.'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to purchase tickets.');
        }

        try {
            $settings = LotterySetting::getSettings();
            
            if (!$settings->isActive()) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Lottery is currently inactive.'], 400);
                }
                return back()->with('error', 'Lottery is currently inactive.');
            }

            $user = Auth::user();
            
            // Check KYC verification requirement
            if ($user->kv != 1) {
                $message = 'KYC verification is required to purchase lottery tickets. Please complete your KYC verification to continue.';
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message, 'redirect' => route('user.kyc.index')], 400);
                }
                return redirect()->route('user.kyc.index')->with('error', $message);
            }
            
            $ticketCount = $request->input('ticket_quantity', 1);
            
            // Validate ticket count
            if ($ticketCount < 1 || $ticketCount > 50) {
                $message = 'Invalid ticket count. You can buy 1-50 tickets at once.';
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }
                return back()->with('error', $message);
            }

            $totalCost = $settings->ticket_price * $ticketCount;
            
            // Check user balance
            if ($user->deposit_wallet < $totalCost) {
                $message = 'Insufficient balance. You need $' . number_format($totalCost, 2) . ' to purchase ' . $ticketCount . ' ticket(s).';
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }
                return back()->with('error', $message);
            }

            // Check max tickets per user for current draw
            $currentDraw = LotteryDraw::getCurrentDraw();
            $userCurrentTickets = LotteryTicket::forUser($user->id)
                                             ->where('lottery_draw_id', $currentDraw->id)
                                             ->count();
            
            if (($userCurrentTickets + $ticketCount) > $settings->max_tickets_per_user) {
                $remaining = $settings->max_tickets_per_user - $userCurrentTickets;
                $message = "You can only buy {$remaining} more ticket(s) for this draw. Maximum {$settings->max_tickets_per_user} tickets per user per draw.";
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }
                return back()->with('error', $message);
            }

            DB::beginTransaction();

            $allPurchasedTickets = [];
            $primaryTickets = [];
            
            // Create tickets with virtual multiplier
            for ($i = 0; $i < $ticketCount; $i++) {
                $ticketsCreated = LotteryTicket::createTicketsWithMultiplier($user->id, 'deposit_wallet');
                $allPurchasedTickets = array_merge($allPurchasedTickets, $ticketsCreated);
                $primaryTickets[] = $ticketsCreated[0]; // First ticket is always the primary/real ticket
            }

            DB::commit();

            // Calculate total tickets created (including bonus)
            $totalTicketsCreated = count($allPurchasedTickets);
            $bonusTicketsCreated = $totalTicketsCreated - $ticketCount;
            
            $message = "Successfully purchased {$ticketCount} lottery ticket(s) for \$" . number_format($totalCost, 2) . ".";
            if ($bonusTicketsCreated > 0) {
                $message .= " Bonus: {$bonusTicketsCreated} additional ticket(s) created!";
            }
            
            // For AJAX requests, return JSON with updated data
            if ($request->ajax()) {
                // Get updated data
                $currentDraw = LotteryDraw::getCurrentDraw();
                $userStats = $this->getUserStats($user->id);
                
                // Format new tickets for display (show all tickets including virtual)
                $newTicketsFormatted = array_map(function($ticket) {
                    return [
                        'ticket_number' => $ticket->ticket_number,
                        'created_at' => $ticket->created_at->format('M d, h:i A'),
                        'is_virtual' => $ticket->is_virtual
                    ];
                }, $allPurchasedTickets);
                
                // Refresh the draw to get updated totals
                $currentDraw->refresh();
                
                $responseData = [
                    'success' => true,
                    'message' => $message,
                    'new_tickets' => $newTicketsFormatted,
                    'tickets_sold' => $currentDraw->total_tickets_sold,
                    'total_prize_pool' => $currentDraw->total_prize_pool,
                    'user_stats' => [
                        'total_tickets' => $userStats['total_tickets'],
                        'total_spent' => $userStats['total_spent'],
                        'total_wins' => $userStats['total_wins'],
                        'total_winnings' => $userStats['total_winnings']
                    ]
                ];
                
                // Log the response data for debugging
                Log::info('AJAX Lottery Purchase Response', $responseData);
                
                return response()->json($responseData);
            }
            
            return back()->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Lottery ticket purchase error: ' . $e->getMessage());
            
            $errorMessage = 'Failed to purchase ticket: ' . $e->getMessage();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }
            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Display user's lottery tickets
     */
    public function myTickets(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $user = Auth::user();
            
            // Build base query
            $query = LotteryTicket::where('user_id', $user->id)
                                 ->with(['lotteryDraw', 'winner', 'lotteryDraw.winners']);

            // Apply filters with validation
            if ($request->filled('status') && in_array($request->status, ['active', 'claimed', 'winner', 'refunded', 'expired'])) {
                switch ($request->status) {
                    case 'active':
                        $query->whereHas('lotteryDraw', function($q) {
                            $q->where('status', 'pending');
                        });
                        break;
                    case 'claimed':
                        $query->whereHas('lotteryDraw', function($q) {
                            $q->where('status', 'completed');
                        });
                        break;
                    case 'winner':
                        $query->whereHas('winner');
                        break;
                    case 'refunded':
                        $query->where('status', 'refunded');
                        break;
                    case 'expired':
                        $query->whereHas('lotteryDraw', function($q) {
                            $q->where('status', 'completed');
                        })->whereDoesntHave('winner');
                        break;
                }
            }

            if ($request->filled('draw_id') && is_numeric($request->draw_id)) {
                $query->where('lottery_draw_id', $request->draw_id);
            }

            if ($request->filled('date_from') && strtotime($request->date_from)) {
                $query->whereDate('purchased_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to') && strtotime($request->date_to)) {
                $query->whereDate('purchased_at', '<=', $request->date_to);
            }

            // Get paginated tickets with error handling
            $tickets = $query->latest('purchased_at')->paginate(20);
            
            // Preserve query parameters for pagination
            $tickets->appends($request->except('page'));

            // Calculate statistics with error handling
            $stats = [
                'total_tickets' => LotteryTicket::where('user_id', $user->id)->count(),
                'active_tickets' => LotteryTicket::where('user_id', $user->id)
                                                ->whereHas('lotteryDraw', function($q) {
                                                    $q->where('status', 'pending');
                                                })->count(),
                'winning_tickets' => LotteryTicket::where('user_id', $user->id)
                                                 ->whereHas('winner')
                                                 ->whereHas('lotteryDraw', function($q) {
                                                     $q->where('status', 'completed');
                                                 })->count(),
                'total_winnings' => LotteryWinner::where('user_id', $user->id)
                                                ->where('claim_status', 'claimed')
                                                ->whereHas('lotteryDraw', function($q) {
                                                    $q->where('status', 'completed');
                                                })
                                                ->sum('prize_amount') ?? 0,
            ];

            // Get available draws for filter
            $draws = LotteryDraw::whereHas('tickets', function($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })
                        ->orderBy('draw_date', 'desc')
                        ->get();

            // Get winning history - Only from completed draws
            $winningHistory = LotteryWinner::where('user_id', $user->id)
                                         ->with(['lotteryDraw', 'lotteryTicket'])
                                         ->whereHas('lotteryDraw', function($q) {
                                             $q->where('status', 'completed');
                                         })
                                         ->latest()
                                         ->take(6)
                                         ->get();

            return view('lottery.my-tickets', compact(
                'tickets',
                'stats',
                'draws',
                'winningHistory'
            ))->with('pageTitle', 'My Lottery Tickets');

        } catch (Exception $e) {
            Log::error('My tickets error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to load your tickets. Please try again.');
        }
    }

    /**
     * Display lottery results
     */
    public function results(Request $request)
    {
        try {
            // Build base query
            $query = LotteryDraw::with(['winners.user', 'winners.lotteryTicket']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('draw_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('draw_date', '<=', $request->date_to);
            }

            // Get draws (limit to recent 4 unless show_all is requested)
            if ($request->has('show_all') || $request->hasAny(['status', 'date_from', 'date_to'])) {
                $draws = $query->latest('draw_date')->paginate(12);
            } else {
                $draws = $query->latest('draw_date')->take(4)->get();
            }

            // Get latest completed draw
            $latestDraw = LotteryDraw::where('status', 'completed')
                                   ->with(['winners.user', 'winners.lotteryTicket'])
                                   ->latest('draw_date')
                                   ->first(); 

            // Calculate statistics
            $statistics = [
                'total_draws' => LotteryDraw::count(),
                'total_prizes' => LotteryWinner::sum('prize_amount'),
                'total_winners' => LotteryWinner::count(),
                'total_tickets' => LotteryTicket::count()
            ];

            return view('lottery.results', compact('draws', 'latestDraw', 'statistics'))
                  ->with('pageTitle', 'Lottery Results');

        } catch (Exception $e) {
            Log::error('Lottery results error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load lottery results.');
        }
    }

    /**
     * Display specific draw details
     */
    public function drawDetails($id)
    {
        try {
            $draw = LotteryDraw::with([
                'winners.user', 
                'winners.lotteryTicket', 
                'tickets.user',
                'firstPrizeWinner.user',
                'firstPrizeWinner.lotteryTicket',
                'secondPrizeWinner.user', 
                'secondPrizeWinner.lotteryTicket',
                'thirdPrizeWinner.user',
                'thirdPrizeWinner.lotteryTicket'
            ])->findOrFail($id);

            $settings = LotterySetting::getSettings();

            return view('lottery.draw-details', compact('draw', 'settings')) 
                  ->with('pageTitle', 'Draw #' . $draw->id . ' Details');

        } catch (Exception $e) {
            Log::error('Draw details error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load draw details.');
        }
    }

    /**
     * Claim prize (manual claim)
     */
    public function claimPrize($winnerId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $winner = LotteryWinner::where('user_id', Auth::id())
                                 ->where('id', $winnerId)
                                 ->firstOrFail();

            if ($winner->isClaimed()) {
                return back()->with('error', 'Prize already claimed.');
            }

            if ($winner->isExpired()) {
                return back()->with('error', 'Prize claim has expired.');
            }

            DB::beginTransaction();

            $winner->claimPrize('manual');

            DB::commit();

            return back()->with('success', 'Prize of $' . number_format($winner->prize_amount, 2) . ' claimed successfully! Your balance has been updated.');

        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Prize not found or not accessible.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Prize claim error: ' . $e->getMessage());
            return back()->with('error', 'Failed to claim prize. Please try again.');
        }
    }

    /**
     * Display lottery statistics
     */
    public function statistics()
    {
        try {
            $settings = LotterySetting::getSettings();

            // Global statistics
            $globalStats = [
                'total_draws' => LotteryDraw::count(),
                'completed_draws' => LotteryDraw::where('status', 'completed')->count(),
                'total_tickets' => LotteryTicket::count(),
                'unique_players' => LotteryTicket::distinct('user_id')->count('user_id'),
                'total_revenue' => LotteryTicket::sum('ticket_price'),
                'avg_revenue_per_draw' => LotteryDraw::where('status', 'completed')->count() > 0 
                    ? LotteryTicket::sum('ticket_price') / LotteryDraw::where('status', 'completed')->count() 
                    : 0,
                'total_prizes' => LotteryWinner::sum('prize_amount'),
                'total_winners' => LotteryWinner::count()
            ];

            // Prize distribution settings
            $prizeDistribution = [
                'first_prize_percentage' => $settings->first_prize_percentage ?? 50,
                'second_prize_percentage' => $settings->second_prize_percentage ?? 30,
                'third_prize_percentage' => $settings->third_prize_percentage ?? 20
            ];

            // Top performing draws
            $topDraws = LotteryDraw::where('status', 'completed')
                                 ->orderBy('total_tickets_sold', 'desc')
                                 ->take(5)
                                 ->get();

            // Recent winners
            $recentWinners = LotteryWinner::with(['user', 'lotteryDraw', 'lotteryTicket'])
                                        ->latest()
                                        ->take(10)
                                        ->get();

            // Detailed statistics
            $detailedStats = [
                'avg_tickets_per_draw' => $globalStats['completed_draws'] > 0 
                    ? $globalStats['total_tickets'] / $globalStats['completed_draws'] 
                    : 0,
                'avg_players_per_draw' => $globalStats['completed_draws'] > 0 
                    ? $globalStats['unique_players'] / $globalStats['completed_draws'] 
                    : 0,
                'avg_tickets_per_player' => $globalStats['unique_players'] > 0 
                    ? $globalStats['total_tickets'] / $globalStats['unique_players'] 
                    : 0,
                'return_rate' => $globalStats['total_revenue'] > 0 
                    ? ($globalStats['total_prizes'] / $globalStats['total_revenue']) * 100 
                    : 0,
                'avg_prize_pool' => $globalStats['completed_draws'] > 0 
                    ? $globalStats['total_prizes'] / $globalStats['completed_draws'] 
                    : 0,
                'total_commission' => $globalStats['total_revenue'] * (($settings->admin_commission_percentage ?? 10) / 100),
                'payout_ratio' => $globalStats['total_revenue'] > 0 
                    ? ($globalStats['total_prizes'] / $globalStats['total_revenue']) * 100 
                    : 0,
                'popular_draw_time' => 'Weekend',
                'fastest_sold_out' => '2 hours',
                'completion_rate' => LotteryDraw::count() > 0 
                    ? ($globalStats['completed_draws'] / LotteryDraw::count()) * 100 
                    : 0,
                'avg_draw_duration' => '24 hours'
            ];

            // Chart data
            $chartData = [
                'revenue' => [
                    'labels' => [],
                    'data' => []
                ]
            ];

            // Generate last 30 days revenue data
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $chartData['revenue']['labels'][] = $date->format('M d');
                $chartData['revenue']['data'][] = LotteryTicket::whereDate('purchased_at', $date)->sum('ticket_price');
            }

            // Personal statistics (if logged in)
            $personalStats = [
                'total_tickets' => 0,
                'total_spent' => 0,
                'total_wins' => 0,
                'total_winnings' => 0,
                'win_rate' => 0
            ];

            if (Auth::check()) {
                $user = Auth::user();
                $personalStats = [
                    'total_tickets' => LotteryTicket::where('user_id', $user->id)->count(),
                    'total_spent' => LotteryTicket::where('user_id', $user->id)->sum('ticket_price'),
                    'total_wins' => LotteryWinner::where('user_id', $user->id)->count(),
                    'total_winnings' => LotteryWinner::where('user_id', $user->id)
                                                   ->where('claim_status', 'claimed')
                                                   ->sum('prize_amount'),
                ];
                
                $personalStats['win_rate'] = $personalStats['total_tickets'] > 0 
                    ? ($personalStats['total_wins'] / $personalStats['total_tickets']) * 100 
                    : 0;
            }

            return view('lottery.statistics', compact(
                'globalStats',
                'prizeDistribution',
                'topDraws',
                'recentWinners',
                'detailedStats',
                'chartData',
                'personalStats',
                'settings'
            ))->with('pageTitle', 'Lottery Statistics');

        } catch (Exception $e) {
            Log::error('Lottery statistics error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load statistics.');
        }
    }

    /**
     * Check draw status for real-time updates
     */
    public function statusCheck(Request $request)
    {
        try {
            $drawId = $request->input('draw_id');
            $draw = LotteryDraw::find($drawId);
            
            if (!$draw) {
                return response()->json(['error' => 'Draw not found'], 404);
            }
            
            return response()->json([
                'status' => $draw->status,
                'has_manual_winners' => $draw->has_manual_winners,
                'draw_date_passed' => $draw->draw_date->isPast(),
                'total_tickets_sold' => $draw->total_tickets_sold,
                'total_prize_pool' => $draw->total_prize_pool
            ]);
            
        } catch (Exception $e) {
            Log::error('Lottery status check error: ' . $e->getMessage());
            return response()->json(['error' => 'Status check failed'], 500);
        }
    }

    /**
     * Get user lottery statistics
     */
    private function getUserStats($userId)
    {
        $userTickets = LotteryTicket::where('user_id', $userId)->count();
        $userWins = LotteryWinner::where('user_id', $userId)->count();
        $userWinnings = LotteryWinner::where('user_id', $userId)->sum('prize_amount');
        $userSpent = LotteryTicket::where('user_id', $userId)
                                 ->join('lottery_settings', function($join) {
                                     $join->on('lottery_tickets.created_at', '>=', DB::raw('lottery_settings.created_at'))
                                          ->orWhereNull('lottery_settings.created_at');
                                 })
                                 ->sum('lottery_settings.ticket_price');

        // If we can't calculate spent amount from settings, use a fallback calculation
        if (!$userSpent) {
            $settings = LotterySetting::getSettings();
            $userSpent = $userTickets * ($settings->ticket_price ?? 2);
        }

        return [
            'total_tickets' => $userTickets,
            'total_wins' => $userWins,
            'total_winnings' => $userWinnings,
            'total_spent' => $userSpent,
            'kyc_verified' => Auth::user()->kv == 1
        ];
    }
}
