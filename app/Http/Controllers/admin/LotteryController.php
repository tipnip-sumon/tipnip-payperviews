<?php

namespace App\Http\Controllers\admin;

use Exception;
use Illuminate\Http\Request;
use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Models\LotteryWinner;
use App\Models\LotterySetting;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class LotteryController extends Controller
{
    /**
     * Display lottery dashboard
     */
    public function index(Request $request)
    {
        try {
            $settings = LotterySetting::getSettings();
            $currentDraw = LotteryDraw::getCurrentDraw();
            
            $stats = [
                'total_draws' => LotteryDraw::count(),
                'total_revenue' => LotteryTicket::sum('ticket_price'),
                'total_tickets' => LotteryTicket::count(),
                'total_prizes' => LotteryWinner::where('claim_status', 'claimed')->sum('prize_amount'),
            ];

            $recentDraws = LotteryDraw::with('winners')
                                   ->latest()
                                   ->take(5)
                                   ->get();

            $pendingClaims = LotteryWinner::with(['user', 'lotteryDraw'])
                                        ->where('claim_status', 'pending')
                                        ->latest()
                                        ->take(10)
                                        ->get();

            // Get draws for the data table with filtering and sorting
            $drawsQuery = LotteryDraw::with(['winners.user']);

            // Apply filters
            if ($request->filled('status')) {
                $drawsQuery->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $drawsQuery->where('draw_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $drawsQuery->where('draw_date', '<=', $request->date_to);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $drawsQuery->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('total_tickets', 'like', "%{$search}%")
                      ->orWhere('total_prize', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortField = $request->get('sort', 'draw_date');
            $sortDirection = $request->get('direction', 'desc');
            
            if (in_array($sortField, ['id', 'draw_date', 'total_tickets', 'total_prize'])) {
                $drawsQuery->orderBy($sortField, $sortDirection);
            } else {
                $drawsQuery->latest('draw_date');
            }

            // Paginate results
            $draws = $drawsQuery->paginate(15)->withQueryString();

            return view('admin.lottery.index', compact(
                'settings',
                'currentDraw',
                'stats',
                'recentDraws',
                'pendingClaims',
                'draws'
            ))->with('pageTitle', 'Lottery Management');

        } catch (Exception $e) {
            Log::error('Admin lottery index error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load lottery dashboard.');
        }
    }

    /**
     * Display lottery settings
     */
    public function settings()
    {
        try {
            $settings = LotterySetting::getSettings();
            
            return view('admin.lottery.settings', compact('settings'))
                  ->with('pageTitle', 'Lottery Settings');

        } catch (Exception $e) {
            Log::error('Lottery settings error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load lottery settings.');
        }
    }

    /**
     * Update lottery settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_price' => 'required|numeric|min:0.01|max:1000',
            'draw_day' => 'required|integer|min:0|max:6',
            'draw_time' => 'required|date_format:H:i',
            'max_tickets_per_user' => 'required|integer|min:1|max:1000',
            'min_tickets_for_draw' => 'required|integer|min:1|max:1000',
            'admin_commission_percentage' => 'required|numeric|min:0|max:50',
            'ticket_expiry_hours' => 'required|integer|min:1|max:8760',
            'prize_structure' => 'required|array',
            'prize_structure.*.name' => 'required|string|max:100',
            'prize_structure.*.percentage' => 'required|numeric|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Validate prize structure percentages total 100%
            $totalPercentage = array_sum(array_column($request->prize_structure, 'percentage'));
            if ($totalPercentage != 100) {
                return back()->with('error', 'Prize structure percentages must total exactly 100%. Current total: ' . $totalPercentage . '%');
            }

            $data = [
                'ticket_price' => $request->ticket_price,
                'draw_day' => $request->draw_day,
                'draw_time' => $request->draw_time . ':00',
                'is_active' => $request->has('is_active'),
                'max_tickets_per_user' => $request->max_tickets_per_user,
                'min_tickets_for_draw' => $request->min_tickets_for_draw,
                'admin_commission_percentage' => $request->admin_commission_percentage,
                'auto_draw' => $request->has('auto_draw'),
                'auto_prize_distribution' => $request->has('auto_prize_distribution'),
                'ticket_expiry_hours' => $request->ticket_expiry_hours,
                'prize_structure' => $request->prize_structure,
            ];

            LotterySetting::updateSettings($data);

            return back()->with('success', 'Lottery settings updated successfully!');

        } catch (Exception $e) {
            Log::error('Lottery settings update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update lottery settings: ' . $e->getMessage());
        }
    }

    /**
     * Display all draws
     */
    public function draws()
    {
        try {
            $draws = LotteryDraw::with(['winners.user'])
                             ->latest()
                             ->paginate(20);

            return view('admin.lottery.draws', compact('draws'))
                  ->with('pageTitle', 'Lottery Draws');

        } catch (Exception $e) {
            Log::error('Admin lottery draws error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load lottery draws.');
        }
    }

    /**
     * Display draw details
     */
    public function drawDetails($id)
    {
        try {
            $draw = LotteryDraw::with(['winners.user', 'tickets.user'])
                             ->findOrFail($id);

            // Calculate statistics for this draw
            $ticketsSold = $draw->tickets()->count();
            $totalRevenue = $draw->tickets()->sum('ticket_price');
            $winnersCount = $draw->winners()->count();
            $prizesPaid = $draw->winners()->where('claim_status', 'claimed')->sum('prize_amount');
            
            // Calculate prize pool (total revenue minus admin commission)
            $settings = LotterySetting::getSettings();
            $adminCommission = ($settings['admin_commission_percentage'] / 100) * $totalRevenue;
            $prizePool = $totalRevenue - $adminCommission;
            
            // Get winners for display
            $winners = $draw->winners()->with('user')->orderBy('prize_position')->get();
            
            // Get recent tickets for this draw (last 10)
            $recentTickets = $draw->tickets()
                                ->with('user')
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();

            return view('admin.lottery.draw-details', compact(
                'draw', 
                'ticketsSold', 
                'totalRevenue', 
                'winnersCount', 
                'prizesPaid',
                'prizePool',
                'winners',
                'recentTickets'
            ))->with('pageTitle', 'Draw Details - ' . ($draw->draw_number ?? 'Draw #' . $draw->id));

        } catch (Exception $e) {
            Log::error('Admin draw details error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load draw details.');
        }
    }

    /**
     * Manually perform draw
     */
    public function performDraw($id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);

            if ($draw->status !== 'pending') {
                $message = 'This draw has already been performed or is not in pending status.';
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message]);
                }
                return back()->with('error', $message);
            }

            if (!$draw->isReadyForDraw()) {
                $settings = LotterySetting::getSettings();
                $message = "Not enough tickets sold. Minimum {$settings->min_tickets_for_draw} tickets required.";
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message]);
                }
                return back()->with('error', $message);
            }

            DB::beginTransaction();

            $winners = $draw->performDraw();

            DB::commit();

            $winnerCount = count($winners);
            $message = "Draw performed successfully! {$winnerCount} winner(s) selected.";
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return back()->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Manual draw error: ' . $e->getMessage());
            $message = 'Failed to perform draw: ' . $e->getMessage();
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message]);
            }
            return back()->with('error', $message);
        }
    }

    /**
     * Distribute prizes to winners
     */
    public function distributePrizes($id)
    {
        try {
            $draw = LotteryDraw::with('winners.user')->findOrFail($id);
            
            if ($draw->status !== 'completed') {
                $message = 'Draw must be completed before distributing prizes.';
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message]);
                }
                return back()->with('error', $message);
            }

            if ($draw->prizes_distributed) {
                $message = 'Prizes have already been distributed for this draw.';
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message]);
                }
                return back()->with('error', $message);
            }

            DB::beginTransaction();
            
            $distributedCount = 0;
            
            foreach ($draw->winners as $winner) {
                if ($winner->claim_status === 'pending' && $winner->user) {
                    // Add prize to user balance
                    $winner->user->increment('balance', $winner->prize_amount);
                    
                    // Update winner record
                    $winner->update([
                        'claim_status' => 'claimed',
                        'claimed_at' => now(),
                        'claim_method' => 'auto_distribution'
                    ]);
                    
                    $distributedCount++;
                }
            }

            // Mark draw as prizes distributed
            $draw->update(['prizes_distributed' => true]);
            
            DB::commit();

            $message = "Prizes distributed to {$distributedCount} winner(s) successfully!";
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return back()->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Distribute prizes error: ' . $e->getMessage());
            $message = 'Failed to distribute prizes: ' . $e->getMessage();
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message]);
            }
            return back()->with('error', $message);
        }
    }

    /**
     * Display all tickets
     */
    public function tickets(Request $request)
    {
        try {
            $query = LotteryTicket::with(['user', 'lotteryDraw']);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by user
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by draw
            if ($request->filled('draw_id')) {
                $query->where('lottery_draw_id', $request->draw_id);
            }

            // Search by ticket number or user email
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('ticket_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('email', 'like', "%{$search}%")
                                   ->orWhere('name', 'like', "%{$search}%");
                      });
                });
            }

            $tickets = $query->latest()->paginate(50);

            // Calculate statistics for the filtered query (without pagination)
            $baseQuery = LotteryTicket::query();
            
            // Apply same filters for statistics
            if ($request->filled('status')) {
                $baseQuery->where('status', $request->status);
            }
            if ($request->filled('user_id')) {
                $baseQuery->where('user_id', $request->user_id);
            }
            if ($request->filled('draw_id')) {
                $baseQuery->where('lottery_draw_id', $request->draw_id);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $baseQuery->where(function($q) use ($search) {
                    $q->where('ticket_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('email', 'like', "%{$search}%")
                                   ->orWhere('name', 'like', "%{$search}%");
                      });
                });
            }

            // Calculate statistics
            $totalTickets = $baseQuery->count();
            $soldTickets = $baseQuery->whereIn('status', ['active', 'winner', 'lost'])->count();
            $winningTickets = $baseQuery->where('status', 'winner')->count();
            $totalRevenue = $baseQuery->sum('ticket_price');

            // Get filter options
            $draws = LotteryDraw::orderBy('draw_date', 'desc')->get();
            $statuses = ['active', 'expired', 'winner', 'lost', 'refunded'];

            return view('admin.lottery.tickets', compact(
                'tickets', 
                'draws', 
                'statuses',
                'totalTickets',
                'soldTickets', 
                'winningTickets',
                'totalRevenue'
            ))->with('pageTitle', 'Lottery Tickets');

        } catch (Exception $e) {
            Log::error('Admin lottery tickets error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load lottery tickets.');
        }
    }

    /**
     * Display all winners
     */
    public function winners(Request $request)
    {
        try {
            $query = LotteryWinner::with(['user', 'lotteryDraw', 'lotteryTicket']);

            // Filter by claim status (renamed for consistency with view)
            if ($request->filled('claim_status')) {
                $query->where('claim_status', $request->claim_status);
            }

            // Filter by prize status (from the view)
            if ($request->filled('prize_status')) {
                if ($request->prize_status === 'distributed') {
                    $query->where('prize_distributed', true);
                } elseif ($request->prize_status === 'pending') {
                    $query->where('prize_distributed', false);
                }
            }

            // Filter by draw
            if ($request->filled('draw_id')) {
                $query->where('lottery_draw_id', $request->draw_id);
            }

            // Filter by position
            if ($request->filled('position')) {
                if ($request->position === 'other') {
                    $query->where('prize_position', '>', 3);
                } else {
                    $query->where('prize_position', $request->position);
                }
            }

            // Search by winner name or email
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $winners = $query->latest()->paginate(50);

            // Calculate statistics for the filtered query (without pagination)
            $baseQuery = LotteryWinner::query();
            
            // Apply same filters for statistics
            if ($request->filled('claim_status')) {
                $baseQuery->where('claim_status', $request->claim_status);
            }
            if ($request->filled('prize_status')) {
                if ($request->prize_status === 'distributed') {
                    $baseQuery->where('prize_distributed', true);
                } elseif ($request->prize_status === 'pending') {
                    $baseQuery->where('prize_distributed', false);
                }
            }
            if ($request->filled('draw_id')) {
                $baseQuery->where('lottery_draw_id', $request->draw_id);
            }
            if ($request->filled('position')) {
                if ($request->position === 'other') {
                    $baseQuery->where('prize_position', '>', 3);
                } else {
                    $baseQuery->where('prize_position', $request->position);
                }
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $baseQuery->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Calculate statistics
            $totalWinners = $baseQuery->count();
            $totalPrizes = $baseQuery->sum('prize_amount');
            $distributedPrizes = $baseQuery->where('prize_distributed', true)->sum('prize_amount');
            $pendingPrizes = $baseQuery->where('prize_distributed', false)->sum('prize_amount');

            // Get filter options
            $draws = LotteryDraw::orderBy('draw_date', 'desc')->get();
            $claimStatuses = ['pending', 'claimed', 'expired'];

            return view('admin.lottery.winners', compact( 
                'winners', 
                'draws', 
                'claimStatuses',
                'totalWinners',
                'totalPrizes',
                'distributedPrizes',
                'pendingPrizes'
            ))->with('pageTitle', 'Lottery Winners');

        } catch (Exception $e) {
            Log::error('Admin lottery winners error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load lottery winners.');
        }
    }

    /**
     * Force claim prize for user
     */
    public function forceClaim($winnerId)
    {
        try {
            $winner = LotteryWinner::findOrFail($winnerId);

            if ($winner->isClaimed()) {
                return back()->with('error', 'Prize already claimed.');
            }

            DB::beginTransaction();

            $winner->claimPrize('admin_force');

            DB::commit();

            return back()->with('success', 'Prize claimed successfully for user: ' . $winner->user->username);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Force claim error: ' . $e->getMessage());
            return back()->with('error', 'Failed to claim prize: ' . $e->getMessage());
        }
    }

    /**
     * Cancel/Expire a draw
     */
    public function cancelDraw($id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);

            if ($draw->status !== 'pending') {
                return back()->with('error', 'Only pending draws can be cancelled.');
            }

            DB::beginTransaction();

            // Refund all tickets
            $tickets = $draw->tickets;
            foreach ($tickets as $ticket) {
                if ($ticket->payment_method === 'balance') {
                    $user = $ticket->user;
                    $user->balance += $ticket->ticket_price;
                    $user->save();
                }
                $ticket->update(['status' => 'expired']);
            }

            // Update draw status to completed since we don't have 'cancelled' status
            // We'll add a note that it was cancelled via other means
            $draw->update([
                'status' => 'completed',
                'notes' => 'Draw cancelled and tickets refunded on ' . now()->format('Y-m-d H:i:s')
            ]);

            DB::commit();

            return back()->with('success', 'Draw cancelled and all tickets refunded.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Cancel draw error: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel draw: ' . $e->getMessage());
        }
    }

    /**
     * Generate lottery report
     */
    public function report(Request $request)
    {
        try {
            // Handle date range filters
            $dateRange = $request->input('date_range', 'this_month');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Set dates based on range
            switch ($dateRange) {
                case 'today':
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
                    break;
                case 'yesterday':
                    $startDate = now()->subDay()->startOfDay();
                    $endDate = now()->subDay()->endOfDay();
                    break;
                case 'this_week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'last_week':
                    $startDate = now()->subWeek()->startOfWeek();
                    $endDate = now()->subWeek()->endOfWeek();
                    break;
                case 'this_month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'last_month':
                    $startDate = now()->subMonth()->startOfMonth();
                    $endDate = now()->subMonth()->endOfMonth();
                    break;
                case 'this_year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                case 'custom':
                    $startDate = $startDate ? \Carbon\Carbon::parse($startDate) : now()->subMonth();
                    $endDate = $endDate ? \Carbon\Carbon::parse($endDate) : now();
                    break;
                default:
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
            }

            // Quick stats
            $stats = [
                'total_draws' => LotteryDraw::whereBetween('draw_date', [$startDate, $endDate])->count(),
                'total_tickets' => LotteryTicket::whereBetween('purchased_at', [$startDate, $endDate])->count(),
                'total_winners' => LotteryWinner::whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_revenue' => LotteryTicket::whereBetween('purchased_at', [$startDate, $endDate])->sum('ticket_price'),
                'total_prizes' => LotteryWinner::whereBetween('created_at', [$startDate, $endDate])->sum('prize_amount'),
                'admin_commission' => 0,
            ];

            // Calculate admin commission
            $settings = LotterySetting::getSettings();
            $stats['admin_commission'] = $stats['total_revenue'] * ($settings->admin_commission_percentage / 100);

            // Financial summary
            $financial = [
                'total_sales' => $stats['total_revenue'],
                'prizes_distributed' => LotteryWinner::whereBetween('created_at', [$startDate, $endDate])
                                                   ->where('prize_distributed', true)
                                                   ->sum('prize_amount'),
                'prizes_pending' => LotteryWinner::whereBetween('created_at', [$startDate, $endDate])
                                                ->where('prize_distributed', false)
                                                ->sum('prize_amount'),
                'admin_commission' => $stats['admin_commission'],
            ];

            $financial['net_profit'] = $financial['total_sales'] - $financial['prizes_distributed'] - $financial['admin_commission'];
            $financial['profit_margin'] = $financial['total_sales'] > 0 ? 
                ($financial['net_profit'] / $financial['total_sales']) * 100 : 0;

            // Top performing draws
            $topDraws = LotteryDraw::whereBetween('draw_date', [$startDate, $endDate])
                                 ->selectRaw('
                                     *, 
                                     total_tickets_sold as tickets_sold,
                                     (total_tickets_sold * ?) as revenue,
                                     ((total_tickets_sold * ?) - total_prize_pool) as profit
                                 ', [$settings->ticket_price, $settings->ticket_price])
                                 ->orderBy('revenue', 'desc')
                                 ->take(5)
                                 ->get();

            // Recent winners
            $recentWinners = LotteryWinner::with(['user', 'lotteryDraw', 'lotteryTicket'])
                                        ->whereBetween('created_at', [$startDate, $endDate])
                                        ->latest()
                                        ->take(10)
                                        ->get();

            // User activity stats
            $userStats = [
                'total_users' => LotteryTicket::whereBetween('purchased_at', [$startDate, $endDate])
                                            ->distinct('user_id')
                                            ->count('user_id'),
                'active_users' => LotteryTicket::whereBetween('purchased_at', [$startDate, $endDate])
                                              ->where('created_at', '>=', now()->subDays(30))
                                              ->distinct('user_id')
                                              ->count('user_id'),
                'winners_count' => LotteryWinner::whereBetween('created_at', [$startDate, $endDate])
                                               ->distinct('user_id')
                                               ->count('user_id'),
                'avg_spending' => 0,
            ];

            if ($userStats['total_users'] > 0) {
                $userStats['avg_spending'] = $stats['total_revenue'] / $userStats['total_users'];
            }

            // Chart data for revenue trend
            $chartData = [
                'revenue' => [
                    'labels' => [],
                    'data' => []
                ],
                'status' => [
                    'labels' => ['Completed', 'Pending', 'Drawn'],
                    'data' => [
                        LotteryDraw::whereBetween('draw_date', [$startDate, $endDate])->where('status', 'completed')->count(),
                        LotteryDraw::whereBetween('draw_date', [$startDate, $endDate])->where('status', 'pending')->count(),
                        LotteryDraw::whereBetween('draw_date', [$startDate, $endDate])->where('status', 'drawn')->count(),
                    ]
                ]
            ];

            // Generate revenue chart data (last 30 days)
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $chartData['revenue']['labels'][] = $date->format('M d');
                $chartData['revenue']['data'][] = LotteryTicket::whereDate('purchased_at', $date)->sum('ticket_price');
            }

            return view('admin.lottery.report', compact(
                'stats',
                'financial', 
                'topDraws',
                'recentWinners',
                'userStats',
                'chartData'
            ))->with('pageTitle', 'Lottery Report');

        } catch (Exception $e) {
            Log::error('Lottery report error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate lottery report.');
        }
    }

    /**
     * Export lottery data
     */
    public function export(Request $request)
    {
        try {
            $type = $request->get('type', 'draws');
            $format = $request->get('format', 'csv');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            $fileName = 'lottery_' . $type . '_' . date('Y-m-d_H-i-s');

            switch ($type) {
                case 'draws':
                    $query = LotteryDraw::with(['winners.user']);
                    if ($dateFrom) $query->where('draw_date', '>=', $dateFrom);
                    if ($dateTo) $query->where('draw_date', '<=', $dateTo);
                    $data = $query->get();
                    
                    $headers = ['Draw ID', 'Draw Date', 'Status', 'Total Tickets', 'Prize Pool', 'Winner', 'Prize Amount'];
                    $rows = $data->map(function ($draw) {
                        return [
                            $draw->id,
                            $draw->draw_date->format('Y-m-d H:i:s'),
                            $draw->status,
                            $draw->total_tickets,
                            $draw->total_prize,
                            $draw->winners->first() ? $draw->winners->first()->user->username : 'No Winner',
                            $draw->winners->first() ? $draw->winners->first()->prize_amount : 0,
                        ];
                    });
                    break;

                case 'tickets':
                    $query = LotteryTicket::with(['user', 'lotteryDraw']);
                    if ($dateFrom) $query->where('purchased_at', '>=', $dateFrom);
                    if ($dateTo) $query->where('purchased_at', '<=', $dateTo);
                    $data = $query->get();
                    
                    $headers = ['Ticket ID', 'User', 'Draw ID', 'Ticket Number', 'Price', 'Purchase Date'];
                    $rows = $data->map(function ($ticket) {
                        return [
                            $ticket->id,
                            $ticket->user->username,
                            $ticket->lottery_draw_id,
                            $ticket->ticket_number,
                            $ticket->ticket_price,
                            $ticket->purchased_at->format('Y-m-d H:i:s'),
                        ];
                    });
                    break;

                case 'winners':
                    $query = LotteryWinner::with(['user', 'lotteryDraw']);
                    if ($dateFrom) $query->where('created_at', '>=', $dateFrom);
                    if ($dateTo) $query->where('created_at', '<=', $dateTo);
                    $data = $query->get();
                    
                    $headers = ['Winner ID', 'User', 'Draw ID', 'Prize Amount', 'Claim Status', 'Won Date'];
                    $rows = $data->map(function ($winner) {
                        return [
                            $winner->id,
                            $winner->user->username,
                            $winner->lottery_draw_id,
                            $winner->prize_amount,
                            $winner->claim_status,
                            $winner->created_at->format('Y-m-d H:i:s'),
                        ];
                    });
                    break;

                case 'summary':
                    $data = collect([
                        ['Metric', 'Value'],
                        ['Total Draws', LotteryDraw::count()],
                        ['Total Tickets Sold', LotteryTicket::count()],
                        ['Total Revenue', '$' . number_format(LotteryTicket::sum('ticket_price'), 2)],
                        ['Total Prizes Awarded', '$' . number_format(LotteryWinner::where('claim_status', 'claimed')->sum('prize_amount'), 2)],
                        ['Pending Claims', LotteryWinner::where('claim_status', 'pending')->count()],
                        ['Active Users', LotteryTicket::distinct('user_id')->count()],
                    ]);
                    
                    $headers = $data->first();
                    $rows = $data->slice(1);
                    break;

                default:
                    return back()->with('error', 'Invalid export type.');
            }

            if ($format === 'csv') {
                $csvContent = $this->generateCsv($headers, $rows);
                return response($csvContent)
                    ->header('Content-Type', 'text/csv')
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '.csv"');
            }

            return back()->with('error', 'Export format not supported yet.');

        } catch (Exception $e) {
            Log::error('Lottery export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export lottery data.');
        }
    }

    /**
     * Import lottery data
     */
    public function import(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120', // 5MB
                'import_type' => 'required|in:tickets,users',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->with('error', 'Invalid file or import type.');
            }

            $file = $request->file('file');
            $type = $request->input('import_type');
            
            // For now, return a success message as import functionality needs more detailed implementation
            return back()->with('success', 'Import functionality is being implemented. Please use manual entry for now.');

        } catch (Exception $e) {
            Log::error('Lottery import error: ' . $e->getMessage());
            return back()->with('error', 'Failed to import lottery data.');
        }
    }

    /**
     * Download sample CSV
     */
    public function sampleCsv(Request $request)
    {
        try {
            $type = $request->get('type', 'tickets');
            
            if ($type === 'tickets') {
                $headers = ['user_id', 'draw_id', 'ticket_number', 'purchase_date'];
                $sample = [
                    ['1', '1', 'TKT001', '2024-01-15 10:30:00'],
                    ['2', '1', 'TKT002', '2024-01-15 11:15:00'],
                    ['3', '2', 'TKT003', '2024-01-16 09:45:00'],
                ];
            } else {
                $headers = ['username', 'email', 'balance', 'status'];
                $sample = [
                    ['user1', 'user1@example.com', '100.00', 'active'],
                    ['user2', 'user2@example.com', '250.50', 'active'],
                    ['user3', 'user3@example.com', '75.25', 'active'],
                ];
            }

            $csvContent = $this->generateCsv($headers, $sample);
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="lottery_' . $type . '_sample.csv"');

        } catch (Exception $e) {
            Log::error('Sample CSV error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate sample CSV.');
        }
    }

    /**
     * Bulk action for draws
     */
    public function drawsBulkAction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:cancel,delete,export',
                'draw_ids' => 'required|array|min:1',
                'draw_ids.*' => 'exists:lottery_draws,id',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->with('error', 'Invalid bulk action request.');
            }

            $action = $request->input('action');
            $drawIds = $request->input('draw_ids');
            $draws = LotteryDraw::whereIn('id', $drawIds);

            switch ($action) {
                case 'cancel':
                    // Since 'cancelled' is not a valid status, we'll mark as 'completed' with a note
                    $pendingDraws = $draws->where('status', 'pending')->get();
                    $count = 0;
                    foreach ($pendingDraws as $draw) {
                        $draw->update([
                            'status' => 'completed',
                            'notes' => 'Bulk cancelled on ' . now()->format('Y-m-d H:i:s')
                        ]);
                        $count++;
                    }
                    return back()->with('success', "Successfully cancelled {$count} draw(s).");

                case 'delete':
                    // Only allow deletion of completed draws (including cancelled ones marked as completed)
                    $count = $draws->where('status', 'completed')->delete();
                    return back()->with('success', "Successfully deleted {$count} draw(s).");

                case 'export':
                    $data = $draws->with(['winners.user'])->get();
                    $headers = ['Draw ID', 'Draw Date', 'Status', 'Total Tickets', 'Prize Pool'];
                    $rows = $data->map(function ($draw) {
                        return [
                            $draw->id,
                            $draw->draw_date->format('Y-m-d H:i:s'),
                            $draw->status,
                            $draw->total_tickets,
                            $draw->total_prize,
                        ];
                    });

                    $csvContent = $this->generateCsv($headers, $rows);
                    return response($csvContent)
                        ->header('Content-Type', 'text/csv')
                        ->header('Content-Disposition', 'attachment; filename="selected_draws_' . date('Y-m-d_H-i-s') . '.csv"');

                default:
                    return back()->with('error', 'Invalid action.');
            }

        } catch (Exception $e) {
            Log::error('Draws bulk action error: ' . $e->getMessage());
            return back()->with('error', 'Failed to perform bulk action.');
        }
    }

    /**
     * Create a draw
     */
    public function createDraw()
    {
        try {
            // Get lottery settings for reference
            $settings = LotterySetting::getSettings();
            if (!$settings) {
                Log::warning('No lottery settings found, creating default settings');
                $settings = new \stdClass();
                $settings->ticket_price = 2.00;
                $settings->admin_commission_percentage = 10;
                $settings->prize_structure = [];
            }
            
            // Get all active draws with their tickets for manual winner selection
            // Note: LotteryDraw doesn't have lotterySetting relationship, we'll get settings separately
            $draws = LotteryDraw::with([
                'tickets' => function($query) {
                    $query->where(function($q) {
                        $q->where('is_virtual', false)->orWhereNull('is_virtual');
                    });
                },
                'tickets.user', 
                'winners'
            ])
                ->where('status', 'pending')
                ->orderBy('draw_date', 'desc')
                ->paginate(10);

            Log::info('Manual winner selection page: Found ' . $draws->count() . ' draws');
            
            // Get ticket statistics
            $ticketStats = [];
            foreach ($draws as $draw) {
                try {
                    // Filter real tickets only (not virtual)
                    $realTickets = $draw->tickets ? $draw->tickets->filter(function($ticket) {
                        return $ticket->is_virtual === false || $ticket->is_virtual === null;
                    }) : collect();
                    
                    $ticketStats[$draw->id] = [
                        'total_tickets' => $realTickets->count(),
                        'unique_users' => $realTickets->unique('user_id')->count(),
                        'has_manual_winners' => $draw->manual_winner_selection_enabled ?? false,
                        'manual_winners_count' => $draw->winners ? $draw->winners->where('is_manual_selection', true)->count() : 0
                    ];
                } catch (Exception $e) {
                    Log::error('Error processing draw ' . $draw->id . ': ' . $e->getMessage());
                    $ticketStats[$draw->id] = [
                        'total_tickets' => 0,
                        'unique_users' => 0,
                        'has_manual_winners' => false,
                        'manual_winners_count' => 0
                    ];
                }
            }
            
            Log::info('Manual winner selection page: Prepared ticket stats for ' . count($ticketStats) . ' draws');
            
            return view('admin.lottery.manual-winners.index', [
                'pageTitle' => 'Manual Winner Token/Ticket Selection',
                'draws' => $draws,
                'ticketStats' => $ticketStats, 
                'settings' => $settings
            ]);
        } catch (Exception $e) {
            Log::error('Manual winner selection page error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to load manual winner selection page: ' . $e->getMessage());
        }
    }

    /**
     * Store a new draw
     */
    public function storeDraw(Request $request)
    {
        try {
            // Convert checkbox values to proper booleans
            $request->merge([
                'auto_draw' => $request->input('auto_draw') === 'true' ? true : false,
                'auto_prize_distribution' => $request->input('auto_prize_distribution') === 'true' ? true : false,
                'manual_winner_selection' => $request->input('manual_winner_selection') === 'true' ? true : false,
            ]);

            $validator = Validator::make($request->all(), [
                'draw_date' => 'required|date|after_or_equal:today',
                'draw_time' => 'required|date_format:H:i',
                'ticket_price' => 'required|numeric|min:0.01|max:1000',
                'max_tickets' => 'required|integer|min:1|max:10000',
                'number_of_winners' => 'required|integer|min:1|max:10',
                'admin_commission' => 'required|numeric|min:0|max:50',
                'prize_distribution' => 'required|array|min:1',
                'prize_distribution.*.position' => 'required|integer|min:1',
                'prize_distribution.*.winner_index' => 'required|integer|min:1',
                'prize_distribution.*.amount' => 'required|numeric|min:0',
                'prize_distribution.*.name' => 'required|string|max:255',
                'prize_distribution.*.type' => 'required|string|in:fixed_amount,percentage',
                'auto_draw' => 'boolean',
                'auto_prize_distribution' => 'boolean',
                'manual_winner_selection' => 'nullable|boolean',
            ], [
                'draw_date.required' => 'Draw date is required.',
                'draw_date.after_or_equal' => 'Draw date must be today or in the future.',
                'draw_time.required' => 'Draw time is required.',
                'draw_time.date_format' => 'Draw time must be in HH:MM format.',
                'ticket_price.required' => 'Ticket price is required.',
                'ticket_price.min' => 'Ticket price must be at least $0.01.',
                'ticket_price.max' => 'Ticket price cannot exceed $1000.',
                'max_tickets.required' => 'Maximum tickets is required.',
                'max_tickets.min' => 'Maximum tickets must be at least 1.',
                'max_tickets.max' => 'Maximum tickets cannot exceed 10,000.',
                'number_of_winners.required' => 'Number of winners is required.',
                'admin_commission.required' => 'Admin commission is required.',
                'admin_commission.max' => 'Admin commission cannot exceed 50%.',
                'prize_distribution.required' => 'Prize distribution is required.',
                'prize_distribution.*.position.required' => 'Winner position is required.',
                'prize_distribution.*.winner_index.required' => 'Winner index is required.',
                'prize_distribution.*.amount.required' => 'Prize amount is required.',
                'prize_distribution.*.name.required' => 'Prize name is required.',
                'prize_distribution.*.type.required' => 'Prize type is required.',
                'auto_draw.boolean' => 'Auto draw must be enabled or disabled.',
                'auto_prize_distribution.boolean' => 'Auto prize distribution must be enabled or disabled.',
                'manual_winner_selection.boolean' => 'Manual winner selection must be enabled or disabled.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Combine date and time
            $drawDateTime = Carbon::parse($request->draw_date . ' ' . $request->draw_time);

            // Calculate estimated prize pool
            $estimatedRevenue = $request->max_tickets * $request->ticket_price;
            $adminCommissionAmount = $estimatedRevenue * ($request->admin_commission / 100);
            $totalPrizePool = $estimatedRevenue - $adminCommissionAmount;

            // Process prize distribution structure
            $prizeDistribution = $request->prize_distribution;
            $prizeStructure = [];
            
            foreach ($prizeDistribution as $winner) {
                $prizeStructure[] = [
                    'position' => (int)$winner['position'],
                    'winner_index' => (int)$winner['winner_index'],
                    'amount' => (float)$winner['amount'],
                    'name' => $winner['name'],
                    'type' => $winner['type']
                ];
            }

            DB::beginTransaction();

            // Generate unique draw number
            $drawNumber = 'DRAW_' . date('Y_m_d') . '_' . str_pad(LotteryDraw::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

            $draw = LotteryDraw::create([
                'draw_number' => $drawNumber,
                'draw_date' => $request->draw_date,
                'draw_time' => $drawDateTime,
                'status' => 'pending',
                'total_prize_pool' => $totalPrizePool,
                'total_tickets_sold' => 0,
                'max_tickets' => $request->max_tickets,
                'ticket_price' => $request->ticket_price,
                'prize_distribution' => json_encode($prizeStructure),
                'prize_distribution_type' => 'fixed_amount',
                'winning_numbers' => null,
                'auto_draw' => $request->auto_draw ?? false,
                'auto_prize_distribution' => $request->auto_prize_distribution ?? false,
                'manual_winner_selection' => $request->manual_winner_selection ?? false,
            ]);

            // Update lottery settings with new defaults
            $settings = LotterySetting::first();
            if ($settings) {
                $settings->update([
                    'ticket_price' => $request->ticket_price,
                    'admin_commission_percentage' => $request->admin_commission,
                    'auto_draw' => $request->auto_draw,
                    'auto_prize_distribution' => $request->auto_prize_distribution,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.lottery.draws')->with('success', 'Draw created successfully! Draw Number: ' . $drawNumber);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Store draw error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to create draw: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete a draw
     */
    public function deleteDraw($id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);
            
            if ($draw->status === 'pending' && $draw->total_tickets === 0) {
                $draw->delete();
                return back()->with('success', 'Draw deleted successfully.');
            }

            return back()->with('error', 'Cannot delete draw with tickets sold or in progress.');

        } catch (Exception $e) {
            Log::error('Delete draw error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete draw.');
        }
    }

    /**
     * Show the form for editing a lottery draw
     */
    public function editDraw($id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);

            // Check if draw can be edited
            if ($draw->status === 'completed') {
                return back()->with('error', 'Cannot edit a completed draw.');
            }

            if ($draw->status === 'drawn' && $draw->winners()->exists()) {
                return back()->with('error', 'Cannot edit a draw that already has winners.');
            }

            $settings = LotterySetting::getSettings();

            return view('admin.lottery.edit-draw', compact('draw', 'settings'))
                  ->with('pageTitle', 'Edit Draw - ' . ($draw->draw_number ?? 'Draw #' . $draw->id));

        } catch (Exception $e) {
            Log::error('Admin edit draw error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load draw for editing.');
        }
    }

    /**
     * Update the specified lottery draw
     */
    public function updateDraw(Request $request, $id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);

            // Check if draw can be updated
            if ($draw->status === 'completed') {
                return back()->with('error', 'Cannot update a completed draw.');
            }

            if ($draw->status === 'drawn' && $draw->winners()->exists()) {
                return back()->with('error', 'Cannot update a draw that already has winners.');
            }

            $validator = Validator::make($request->all(), [
                'draw_number' => 'nullable|string|max:50|unique:lottery_draws,draw_number,' . $id,
                'draw_date' => 'required|date|after_or_equal:today',
                'draw_time' => 'required|date_format:H:i',
                'total_prize_pool' => 'required|numeric|min:0',
                'first_prize' => 'required|numeric|min:0',
                'second_prize' => 'nullable|numeric|min:0',
                'third_prize' => 'nullable|numeric|min:0',
                'consolation_prizes' => 'nullable|integer|min:0',
                'consolation_prize_amount' => 'nullable|numeric|min:0',
                'max_tickets' => 'nullable|integer|min:1',
                'ticket_price' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:1000',
                'terms_and_conditions' => 'nullable|string|max:2000',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Please correct the validation errors.');
            }

            // Check if tickets are already sold and price is being changed
            if ($draw->total_tickets_sold > 0 && $request->ticket_price != $draw->ticket_price) {
                return back()->with('error', 'Cannot change ticket price when tickets have already been sold.');
            }

            // Combine date and time
            $drawDateTime = Carbon::parse($request->draw_date . ' ' . $request->draw_time);

            DB::beginTransaction();

            // Update draw details
            $draw->update([
                'draw_number' => $request->draw_number ?: 'DRAW_' . time(),
                'draw_date' => $drawDateTime,
                'total_prize_pool' => $request->total_prize_pool,
                'first_prize' => $request->first_prize,
                'second_prize' => $request->second_prize,
                'third_prize' => $request->third_prize,
                'consolation_prizes' => $request->consolation_prizes ?? 0,
                'consolation_prize_amount' => $request->consolation_prize_amount ?? 0,
                'max_tickets' => $request->max_tickets,
                'ticket_price' => $request->ticket_price,
                'description' => $request->description,
                'terms_and_conditions' => $request->terms_and_conditions,
                'is_active' => $request->has('is_active')
            ]);

            // If ticket price changed and no tickets sold, update ticket price in settings
            if ($draw->total_tickets_sold == 0 && $request->ticket_price != $draw->ticket_price) {
                $settings = LotterySetting::getSettings();
                $settings->ticket_price = $request->ticket_price;
                $settings->save();
            }

            DB::commit();

            Log::info("Draw {$draw->draw_number} updated by admin", [
                'draw_id' => $draw->id,
                'changes' => $request->all()
            ]);

            return redirect()
                ->route('admin.lottery.draws.details', $draw->id)
                ->with('success', 'Draw updated successfully.');

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Admin update draw error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Failed to update draw: ' . $e->getMessage());
        }
    }

    /**
     * Handle bulk actions for lottery tickets
     */
    public function ticketsBulkAction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:refund,mark_winner,delete,distribute_prize',
                'ticket_ids' => 'required|array|min:1',
                'ticket_ids.*' => 'exists:lottery_tickets,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request data.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $action = $request->action;
            $ticketIds = $request->ticket_ids;
            $tickets = LotteryTicket::whereIn('id', $ticketIds)->with(['user', 'lotteryDraw'])->get();

            if ($tickets->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid tickets found.'
                ], 404);
            }

            $successCount = 0;
            $errorMessages = [];

            DB::beginTransaction();

            foreach ($tickets as $ticket) {
                try {
                    switch ($action) {
                        case 'refund':
                            $this->refundTicket($ticket);
                            $successCount++;
                            break;

                        case 'mark_winner':
                            $this->markTicketAsWinner($ticket);
                            $successCount++;
                            break;

                        case 'delete':
                            $this->deleteTicket($ticket);
                            $successCount++;
                            break;

                        case 'distribute_prize':
                            $this->distributePrizeForTicket($ticket);
                            $successCount++;
                            break;

                        default:
                            $errorMessages[] = "Unknown action for ticket {$ticket->ticket_number}";
                    }
                } catch (Exception $e) {
                    $errorMessages[] = "Failed to {$action} ticket {$ticket->ticket_number}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Successfully processed {$successCount} ticket(s)";
            if (!empty($errorMessages)) {
                $message .= ". Errors: " . implode(', ', $errorMessages);
            }

            return response()->json([
                'success' => $successCount > 0,
                'message' => $message,
                'processed_count' => $successCount,
                'total_count' => count($ticketIds),
                'errors' => $errorMessages
            ]);

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Lottery tickets bulk action error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process bulk action: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refund a single ticket
     */
    private function refundTicket(LotteryTicket $ticket)
    {
        if ($ticket->status === 'refunded') {
            throw new Exception('Ticket already refunded');
        }

        if ($ticket->lotteryDraw && $ticket->lotteryDraw->status === 'completed') {
            throw new Exception('Cannot refund ticket from completed draw');
        }

        // Refund to user's wallet
        $user = $ticket->user;
        if ($user) {
            $user->deposit_wallet += $ticket->ticket_price;
            $user->save();
        }

        // Update ticket status
        $ticket->status = 'refunded';
        $ticket->save();

        // Log the refund action
        Log::info("Ticket {$ticket->ticket_number} refunded for user {$user->email}");
    }

    /**
     * Mark a ticket as winner
     */
    private function markTicketAsWinner(LotteryTicket $ticket)
    {
        if ($ticket->status === 'winner') {
            throw new Exception('Ticket already marked as winner');
        }

        if (!$ticket->lotteryDraw) {
            throw new Exception('Ticket must be associated with a draw');
        }

        // Update ticket status
        $ticket->status = 'winner';
        $ticket->save();

        // Create winner record if not exists
        $existingWinner = LotteryWinner::where('lottery_ticket_id', $ticket->id)->first();
        if (!$existingWinner) {
            // Get default prize amount from settings or use ticket price * 10 as default
            $settings = LotterySetting::getSettings();
            $defaultPrizeAmount = $ticket->ticket_price * 10;

            LotteryWinner::create([
                'lottery_draw_id' => $ticket->lottery_draw_id,
                'lottery_ticket_id' => $ticket->id,
                'user_id' => $ticket->user_id,
                'prize_position' => 1, // Default position
                'prize_name' => 'Manual Winner', // Added required field
                'prize_amount' => $defaultPrizeAmount,
                'claim_status' => 'pending'
            ]);

            // Update ticket with prize amount
            $ticket->prize_amount = $defaultPrizeAmount;
            $ticket->save();
        }

        Log::info("Ticket {$ticket->ticket_number} marked as winner");
    }

    /**
     * Delete a single ticket
     */
    private function deleteTicket(LotteryTicket $ticket)
    {
        if ($ticket->status === 'winner' && $ticket->prize_amount > 0) {
            throw new Exception('Cannot delete winning ticket with prize amount');
        }

        if ($ticket->lotteryDraw && $ticket->lotteryDraw->status === 'completed') {
            throw new Exception('Cannot delete ticket from completed draw');
        }

        // Delete associated winner record if exists
        LotteryWinner::where('lottery_ticket_id', $ticket->id)->delete();

        // Delete the ticket
        $ticketNumber = $ticket->ticket_number;
        $ticket->delete();

        Log::info("Ticket {$ticketNumber} deleted");
    }

    /**
     * Distribute prize for a ticket
     */
    private function distributePrizeForTicket(LotteryTicket $ticket)
    {
        if ($ticket->status !== 'winner') {
            throw new Exception('Only winning tickets can have prizes distributed');
        }

        if (!$ticket->prize_amount || $ticket->prize_amount <= 0) {
            throw new Exception('Ticket has no prize amount to distribute');
        }

        if ($ticket->claimed_at) {
            throw new Exception('Prize already distributed for this ticket');
        }

        // Find winner record
        $winner = LotteryWinner::where('lottery_ticket_id', $ticket->id)->first();
        if (!$winner) {
            throw new Exception('Winner record not found for this ticket');
        }

        if ($winner->prize_distributed) {
            throw new Exception('Prize already distributed');
        }

        // Add prize to user's wallet
        $user = $ticket->user;
        if ($user) {
            $user->deposit_wallet += $ticket->prize_amount;
            $user->save();
        }

        // Update winner record
        $winner->claim_status = 'claimed';
        $winner->prize_distributed = true;
        $winner->distributed_at = now();
        $winner->save();

        // Update ticket
        $ticket->claimed_at = now();
        $ticket->save();

        Log::info("Prize $" . $ticket->prize_amount . " distributed for ticket {$ticket->ticket_number} to user {$user->email}");
    }

    /**
     * Generate CSV content
     */
    private function generateCsv($headers, $rows)
    {
        $csvContent = '';
        
        // Add headers
        $csvContent .= implode(',', array_map(function($header) {
            return '"' . str_replace('"', '""', $header) . '"';
        }, $headers)) . "\n";
        
        // Add rows
        foreach ($rows as $row) {
            $csvContent .= implode(',', array_map(function($cell) {
                return '"' . str_replace('"', '""', $cell) . '"';
            }, $row)) . "\n";
        }
        
        return $csvContent;
    }

    /**
     * Show details of a specific lottery ticket
     */
    public function ticketDetails($id)
    {
        try {
            $ticket = LotteryTicket::with(['user', 'draw', 'winners'])
                                 ->findOrFail($id);

            // Get ticket statistics
            $ticketStats = [
                'purchase_date' => $ticket->created_at,
                'draw_date' => $ticket->draw->draw_date ?? null,
                'is_winner' => $ticket->winners->count() > 0,
                'prize_amount' => $ticket->winners->sum('prize_amount'),
                'claim_status' => $ticket->winners->first()->claim_status ?? null,
            ];

            return view('admin.lottery.ticket-details', compact(
                'ticket',
                'ticketStats'
            ))->with('pageTitle', 'Ticket Details - ' . $ticket->ticket_number);

        } catch (Exception $e) {
            Log::error('Admin ticket details error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load ticket details.');
        }
    }

    /**
     * Show details of a specific lottery winner
     */
    public function winnerDetails($id)
    {
        try {
            $winner = LotteryWinner::with(['user', 'draw', 'ticket'])
                                 ->findOrFail($id);

            // Get winner statistics
            $winnerStats = [
                'draw_date' => $winner->draw->draw_date ?? null,
                'total_participants' => $winner->draw->tickets()->count(),
                'winning_position' => $winner->prize_position,
                'claim_deadline' => null, // Not implemented in current schema
                'days_to_claim' => null,
            ];

            return view('admin.lottery.winner-details', compact(
                'winner',
                'winnerStats'
            ))->with('pageTitle', 'Winner Details - ' . ($winner->user->name ?? 'Winner #' . $winner->id));

        } catch (Exception $e) {
            Log::error('Admin winner details error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load winner details.');
        }
    }

    /**
     * Export specific draw data
     */
    public function exportDraw(Request $request, $id)
    {
        try {
            $draw = LotteryDraw::with(['tickets.user', 'winners.user'])
                             ->findOrFail($id);

            $type = $request->get('type', 'tickets');
            $format = $request->get('format', 'csv');

            switch ($type) {
                case 'tickets':
                    return $this->exportDrawTickets($draw, $format);
                case 'winners':
                    return $this->exportDrawWinners($draw, $format);
                case 'summary':
                    return $this->exportDrawSummary($draw, $format);
                default:
                    return $this->exportDrawTickets($draw, $format);
            }

        } catch (Exception $e) {
            Log::error('Draw export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export draw data.');
        }
    }

    /**
     * Export draw tickets
     */
    private function exportDrawTickets($draw, $format = 'csv')
    {
        $tickets = $draw->tickets()->with('user')->orderBy('created_at', 'desc')->get();
        
        $data = [];
        $data[] = ['Ticket Number', 'Buyer Name', 'Buyer Email', 'Purchase Date', 'Price', 'Status', 'Winner Status'];
        
        foreach ($tickets as $ticket) {
            $isWinner = $draw->winners()->where('lottery_ticket_id', $ticket->id)->exists();
            $data[] = [
                $ticket->ticket_number,
                $ticket->user->name ?? 'Unknown',
                $ticket->user->email ?? 'No email',
                $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i:s') : 'Unknown',
                number_format($ticket->ticket_price, 2),
                $ticket->status ?? 'active',
                $isWinner ? 'Winner' : 'Regular'
            ];
        }

        $filename = 'draw_' . $draw->id . '_tickets_' . date('Y-m-d_H-i-s');
        
        if ($format === 'csv') {
            $csvContent = $this->arrayToCsv($data);
            return response($csvContent, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"'
            ]);
        }

        // Default to CSV if format not supported
        return $this->exportDrawTickets($draw, 'csv');
    }

    /**
     * Export draw winners
     */
    private function exportDrawWinners($draw, $format = 'csv')
    {
        $winners = $draw->winners()->with(['user', 'ticket'])->orderBy('prize_position')->get();
        
        $data = [];
        $data[] = ['Position', 'Winner Name', 'Winner Email', 'Ticket Number', 'Prize Amount', 'Claim Status', 'Claimed Date'];
        
        foreach ($winners as $winner) {
            $data[] = [
                $winner->prize_position,
                $winner->user->name ?? 'Unknown',
                $winner->user->email ?? 'No email',
                $winner->ticket->ticket_number ?? 'Unknown',
                number_format($winner->prize_amount, 2),
                $winner->claim_status,
                $winner->claimed_at ? $winner->claimed_at->format('Y-m-d H:i:s') : 'Not claimed'
            ];
        }

        $filename = 'draw_' . $draw->id . '_winners_' . date('Y-m-d_H-i-s');
        
        if ($format === 'csv') {
            $csvContent = $this->arrayToCsv($data);
            return response($csvContent, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"'
            ]);
        }

        return $this->exportDrawWinners($draw, 'csv');
    }

    /**
     * Export draw summary
     */
    private function exportDrawSummary($draw, $format = 'csv')
    {
        $ticketsSold = $draw->tickets()->count();
        $totalRevenue = $draw->tickets()->sum('ticket_price');
        $winnersCount = $draw->winners()->count();
        $prizesPaid = $draw->winners()->where('claim_status', 'claimed')->sum('prize_amount');
        
        $data = [];
        $data[] = ['Draw Summary for Draw #' . $draw->id];
        $data[] = [''];
        $data[] = ['Draw Information', ''];
        $data[] = ['Draw ID', $draw->id];
        $data[] = ['Draw Date', $draw->draw_date ? $draw->draw_date->format('Y-m-d') : 'Not scheduled'];
        $data[] = ['Draw Time', $draw->draw_time ? $draw->draw_time->format('H:i') : 'Not scheduled'];
        $data[] = ['Status', $draw->status];
        $data[] = ['Max Tickets', $draw->max_tickets];
        $data[] = ['Ticket Price', '$' . number_format($draw->ticket_price, 2)];
        $data[] = ['Admin Commission', $draw->admin_commission . '%'];
        $data[] = [''];
        $data[] = ['Statistics', ''];
        $data[] = ['Tickets Sold', $ticketsSold];
        $data[] = ['Total Revenue', '$' . number_format($totalRevenue, 2)];
        $data[] = ['Winners Count', $winnersCount];
        $data[] = ['Prizes Paid', '$' . number_format($prizesPaid, 2)];
        $data[] = [''];
        $data[] = ['Winners', ''];
        
        $winners = $draw->winners()->with(['user', 'ticket'])->orderBy('prize_position')->get();
        foreach ($winners as $winner) {
            $data[] = [
                'Position ' . $winner->prize_position,
                $winner->user->name ?? 'Unknown' . ' - Ticket #' . ($winner->ticket->ticket_number ?? 'Unknown') . ' - $' . number_format($winner->prize_amount, 2)
            ];
        }

        $filename = 'draw_' . $draw->id . '_summary_' . date('Y-m-d_H-i-s');
        
        if ($format === 'csv') {
            $csvContent = $this->arrayToCsv($data);
            return response($csvContent, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"'
            ]);
        }

        return $this->exportDrawSummary($draw, 'csv');
    }

    /**
     * Show manual winner selection page
     */
    public function manualWinners(Request $request, $id)
    {
        try {
            $draw = LotteryDraw::with(['lotterySetting', 'winners.user'])->findOrFail($id);
            
            // Check if manual winner selection is allowed
            if (!$draw->lotterySetting->manual_winner_selection) {
                return redirect()->route('admin.lottery.draws.details', $id)
                    ->with('error', 'Manual winner selection is not enabled for this lottery.');
            }

            // Check if draw is in pending state
            if ($draw->status !== 'pending') {
                return redirect()->route('admin.lottery.draws.details', $id)
                    ->with('error', 'Manual winner selection is only available for pending draws.');
            }

            // Get all tickets for this draw (real tickets only)
            $tickets = LotteryTicket::with('user')
                        ->where('lottery_draw_id', $id)
                        ->where(function($query) {
                            $query->where('is_virtual', false)
                                  ->orWhereNull('is_virtual');
                        })
                        ->get();

            // Get prize structure
            $prizeStructure = $draw->getPrizeStructureWithAmounts();

            return view('admin.lottery.manual-winners', compact('draw', 'tickets', 'prizeStructure'));

        } catch (Exception $e) {
            Log::error('Error loading manual winners page: ' . $e->getMessage());
            return redirect()->route('admin.lottery.draws')
                ->with('error', 'Failed to load manual winner selection page.');
        }
    }

    /**
     * Store manually selected winners
     */
    public function storeManualWinners(Request $request, $id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);
            
            // Check if draw is in pending state
            if ($draw->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Manual winner selection is only available for pending draws.'
                ], 400);
            }

            // Handle both single ticket and batch operations
            if ($request->has('ticket_ids')) {
                // Batch operation with ticket IDs
                $validator = Validator::make($request->all(), [
                    'ticket_ids' => 'required|array',
                    'ticket_ids.*' => 'integer|exists:lottery_tickets,id',
                    'position' => 'integer|min:1'
                ]);
                
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                
                // Validate each ticket is available for selection
                $ticketIds = $request->ticket_ids;
                $unavailableTickets = [];
                
                foreach ($ticketIds as $ticketId) {
                    $ticket = LotteryTicket::with('user')->find($ticketId);
                    
                    if (!$ticket) {
                        $unavailableTickets[] = "Ticket ID {$ticketId} not found";
                        continue;
                    }
                    
                    // Check if ticket belongs to this draw
                    if ($ticket->lottery_draw_id != $id) {
                        $unavailableTickets[] = "Ticket #{$ticket->ticket_number} does not belong to this draw";
                        continue;
                    }
                    
                    // Check if ticket is already a winner
                    $existingWinner = LotteryWinner::where('lottery_draw_id', $id)
                                                 ->where('lottery_ticket_id', $ticketId)
                                                 ->first();
                    if ($existingWinner) {
                        $unavailableTickets[] = "Ticket #{$ticket->ticket_number} is already selected as a winner";
                        continue;
                    }
                    
                    // Check if ticket is still valid (not used/expired)
                    $isValidTicket = LotteryTicket::where('id', $ticketId)
                                                 ->where('status', 'active')
                                                 ->where(function($query) {
                                                     $query->where('is_virtual', false)
                                                           ->orWhereNull('is_virtual');
                                                 })
                                                 ->whereNull('used_as_token_at')
                                                 ->whereNotExists(function($query) use ($ticket) {
                                                     $query->select(DB::raw(1))
                                                           ->from('used_tickets')
                                                           ->where('ticket_number', $ticket->ticket_number)
                                                           ->whereIn('status', ['used', 'invalid']);
                                                 })
                                                 ->exists();
                    
                    if (!$isValidTicket) {
                        $unavailableTickets[] = "Ticket #{$ticket->ticket_number} is no longer available (may have been used or expired)";
                    }
                }
                
                if (!empty($unavailableTickets)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Some tickets are not available for selection:',
                        'unavailable_tickets' => $unavailableTickets
                    ], 422);
                }
                
            } else {
                // Legacy format with winners array
                $validator = Validator::make($request->all(), [
                    'winners' => 'required|array',
                    'winners.*.position' => 'required|integer|min:1',
                    'winners.*.ticket_id' => 'required|integer|exists:lottery_tickets,id',
                    'winners.*.prize_amount' => 'required|numeric|min:0'
                ]);
                
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed.',
                        'errors' => $validator->errors()
                    ], 422);
                }
                
                // Validate each ticket in legacy format
                $unavailableTickets = [];
                foreach ($request->winners as $index => $winner) {
                    $ticketId = $winner['ticket_id'];
                    $ticket = LotteryTicket::with('user')->find($ticketId);
                    
                    if (!$ticket) {
                        $unavailableTickets[] = "Ticket ID {$ticketId} not found";
                        continue;
                    }
                    
                    // Check if ticket belongs to this draw
                    if ($ticket->lottery_draw_id != $id) {
                        $unavailableTickets[] = "Ticket #{$ticket->ticket_number} does not belong to this draw";
                        continue;
                    }
                    
                    // Check if ticket is already a winner
                    $existingWinner = LotteryWinner::where('lottery_draw_id', $id)
                                                 ->where('lottery_ticket_id', $ticketId)
                                                 ->first();
                    if ($existingWinner) {
                        $unavailableTickets[] = "Ticket #{$ticket->ticket_number} is already selected as a winner";
                        continue;
                    }
                    
                    // Check if ticket is still valid (not used/expired)
                    $isValidTicket = LotteryTicket::where('id', $ticketId)
                                                 ->where('status', 'active')
                                                 ->where(function($query) {
                                                     $query->where('is_virtual', false)
                                                           ->orWhereNull('is_virtual');
                                                 })
                                                 ->whereNull('used_as_token_at')
                                                 ->whereNotExists(function($query) use ($ticket) {
                                                     $query->select(DB::raw(1))
                                                           ->from('used_tickets')
                                                           ->where('ticket_number', $ticket->ticket_number)
                                                           ->whereIn('status', ['used', 'invalid']);
                                                 })
                                                 ->exists();
                    
                    if (!$isValidTicket) {
                        $unavailableTickets[] = "Ticket #{$ticket->ticket_number} is no longer available (may have been used or expired)";
                    }
                }
                
                if (!empty($unavailableTickets)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Some tickets are not available for selection:',
                        'unavailable_tickets' => $unavailableTickets
                    ], 422);
                }
            }

            if (!$request->has('ticket_ids') && $validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            if ($request->has('ticket_ids')) {
                // Handle ticket_ids format (from new interface)
                $ticketIds = $request->ticket_ids;
                $settings = LotterySetting::getSettings();
                
                // Get prize structure from lottery settings
                $prizeStructure = $settings->prize_structure ?? [];
                if (is_string($prizeStructure)) {
                    $prizeStructure = json_decode($prizeStructure, true) ?? [];
                }
                
                // Determine position assignment method
                if ($request->has('same_position')) {
                    // All tickets get the same position
                    $position = $request->same_position;
                    foreach ($ticketIds as $ticketId) {
                        $this->createManualWinner($id, $ticketId, $position, $prizeStructure);
                    }
                } elseif ($request->has('custom_positions')) {
                    // Each ticket gets a custom position
                    $customPositions = $request->custom_positions;
                    foreach ($ticketIds as $index => $ticketId) {
                        $position = $customPositions[$index] ?? ($index + 1);
                        $this->createManualWinner($id, $ticketId, $position, $prizeStructure);
                    }
                } elseif ($request->has('position')) {
                    // Single ticket with specific position
                    $position = $request->position;
                    foreach ($ticketIds as $ticketId) {
                        $this->createManualWinner($id, $ticketId, $position, $prizeStructure);
                    }
                } else {
                    // Sequential positions (default behavior)
                    $existingWinnersCount = LotteryWinner::where('lottery_draw_id', $id)->count();
                    foreach ($ticketIds as $index => $ticketId) {
                        $position = $existingWinnersCount + $index + 1;
                        $this->createManualWinner($id, $ticketId, $position, $prizeStructure);
                    }
                }
            } else {
                // Handle legacy winners format
                foreach ($request->winners as $winnerData) {
                    $ticket = LotteryTicket::find($winnerData['ticket_id']);
                    
                    LotteryWinner::create([
                        'lottery_draw_id' => $id,
                        'user_id' => $ticket->user_id,
                        'lottery_ticket_id' => $ticket->id,
                        'prize_position' => $winnerData['position'],
                        'prize_name' => "Position {$winnerData['position']}",
                        'prize_amount' => $winnerData['prize_amount'],
                        'claim_status' => 'pending',
                        'selected_at' => Carbon::now(),
                        'is_manual_selection' => true
                    ]);
                }
            }

            // Update draw to indicate manual winners have been set
            $draw->update([
                'has_manual_winners' => true,
                'manual_winners_count' => LotteryWinner::where('lottery_draw_id', $id)->count()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Winners have been successfully selected manually.'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error storing manual winners: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save manual winners. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to create a manual winner
     */
    private function createManualWinner($drawId, $ticketId, $position, $prizeStructure)
    {
        $ticket = LotteryTicket::findOrFail($ticketId);
        
        // Calculate base prize amount for this position from lottery settings
        $basePrizeAmount = 0;
        if (is_array($prizeStructure) && isset($prizeStructure[$position])) {
            $basePrizeAmount = $prizeStructure[$position]['amount'] ?? 0;
        } elseif (is_string($prizeStructure)) {
            $prizeData = json_decode($prizeStructure, true);
            if ($prizeData && isset($prizeData[$position])) {
                $basePrizeAmount = $prizeData[$position]['amount'] ?? 0;
            }
        }
        
        // Check if this ticket is already a winner
        $existingWinner = LotteryWinner::where('lottery_draw_id', $drawId)
                                      ->where('lottery_ticket_id', $ticketId)
                                      ->first();
        
        if (!$existingWinner) {
            // Create the winner first with base amount
            LotteryWinner::create([
                'lottery_draw_id' => $drawId,
                'user_id' => $ticket->user_id,
                'lottery_ticket_id' => $ticketId,
                'prize_position' => $position,
                'prize_name' => "Position {$position}",
                'prize_amount' => $basePrizeAmount, // Will be updated after split calculation
                'claim_status' => 'pending',
                'selected_at' => Carbon::now(),
                'is_manual_selection' => true
            ]);
            
            // After creating winner, recalculate and update prize splits for this position
            $this->updatePrizeSplitsForPosition($drawId, $position, $basePrizeAmount);
        }
    }
    
    /**
     * Update prize amounts for all winners in a specific position (split equally)
     */
    private function updatePrizeSplitsForPosition($drawId, $position, $totalPrizeAmount)
    {
        // Get all winners for this position in this draw
        $winnersForPosition = LotteryWinner::where('lottery_draw_id', $drawId)
                                          ->where('prize_position', $position)
                                          ->get();
        
        $winnerCount = $winnersForPosition->count();
        
        if ($winnerCount > 0) {
            // Calculate split amount (round to 2 decimal places)
            $splitAmount = round($totalPrizeAmount / $winnerCount, 2);
            
            // Update all winners for this position with the split amount
            LotteryWinner::where('lottery_draw_id', $drawId)
                        ->where('prize_position', $position)
                        ->update([
                            'prize_amount' => $splitAmount,
                            'prize_name' => $winnerCount > 1 ? 
                                "Position {$position} (Split {$winnerCount} ways)" : 
                                "Position {$position}"
                        ]);
        }
    }

    /**
     * Change the position of an existing manual winner
     */
    public function changeWinnerPosition($id, $winnerId, Request $request)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);
            
            if ($draw->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot change winner positions for completed draws.'
                ], 400);
            }
            
            $validator = Validator::make($request->all(), [
                'position' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid position provided.',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $winner = LotteryWinner::where('id', $winnerId)
                                  ->where('lottery_draw_id', $id)
                                  ->first();
            
            if (!$winner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Winner not found.'
                ], 404);
            }

            $oldPosition = $winner->prize_position;
            $newPosition = $request->position;
            $settings = LotterySetting::getSettings();
            
            // Get prize structure from lottery settings
            $prizeStructure = $settings->prize_structure ?? [];
            if (is_string($prizeStructure)) {
                $prizeStructure = json_decode($prizeStructure, true) ?? [];
            }
            
            // Get base prize amounts for both positions
            $oldBasePrizeAmount = 0;
            if (isset($prizeStructure[$oldPosition])) {
                $oldBasePrizeAmount = $prizeStructure[$oldPosition]['amount'] ?? 0;
            }
            
            $newBasePrizeAmount = 0;
            if (isset($prizeStructure[$newPosition])) {
                $newBasePrizeAmount = $prizeStructure[$newPosition]['amount'] ?? 0;
            }
            
            // Update the winner's position first
            $winner->update([
                'prize_position' => $newPosition,
                'prize_name' => "Position {$newPosition}",
                'prize_amount' => $newBasePrizeAmount // Will be updated by split calculation
            ]);
            
            // Recalculate prize splits for both old and new positions
            if ($oldPosition != $newPosition) {
                // Update splits for the old position (fewer winners now)
                $this->updatePrizeSplitsForPosition($id, $oldPosition, $oldBasePrizeAmount);
                
                // Update splits for the new position (more winners now)
                $this->updatePrizeSplitsForPosition($id, $newPosition, $newBasePrizeAmount);
            }
            
            // Clear any potential caching
            $winner->fresh();
            
            return response()->json([
                'success' => true,
                'message' => "Winner position changed to Position {$newPosition} successfully."
            ]);
            
        } catch (Exception $e) {
            Log::error('Change winner position error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to change winner position: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a specific manual winner
     */
    public function removeManualWinner($id, $winnerId)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);
            
            if ($draw->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove winners from completed draws.'
                ], 400);
            }
            
            $winner = LotteryWinner::where('id', $winnerId)
                                  ->where('lottery_draw_id', $id)
                                  ->first();
            
            if (!$winner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Winner not found.'
                ], 404);
            }
            
            $position = $winner->prize_position;
            $settings = LotterySetting::getSettings();
            
            // Get prize structure from lottery settings
            $prizeStructure = $settings->prize_structure ?? [];
            if (is_string($prizeStructure)) {
                $prizeStructure = json_decode($prizeStructure, true) ?? [];
            }
            
            // Get base prize amount for this position
            $basePrizeAmount = 0;
            if (isset($prizeStructure[$position])) {
                $basePrizeAmount = $prizeStructure[$position]['amount'] ?? 0;
            }
            
            // Delete the winner
            $winner->delete();
            
            // Recalculate prize splits for the remaining winners in this position
            $this->updatePrizeSplitsForPosition($id, $position, $basePrizeAmount);
            
            // Update draw manual winner flag if no more manual winners exist
            $remainingManualWinners = LotteryWinner::where('lottery_draw_id', $id)
                                                  ->where('is_manual_selection', true)
                                                  ->count();
            
            if ($remainingManualWinners === 0) {
                $draw->update(['has_manual_winners' => false]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Winner removed successfully.'
            ]);
            
        } catch (Exception $e) {
            Log::error('Remove manual winner error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove winner: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-generate lottery draw
     */
    public function autoGenerateDraw(Request $request)
    {
        try {
            $settings = LotterySetting::getSettings();
            
            if (!$settings->auto_generate_draws) {
                return back()->with('error', 'Auto-generation is disabled in settings.');
            }

            // Calculate next draw time
            $nextDrawTime = $this->calculateNextDrawTime($settings);
            
            // Check for existing draw
            $existingDraw = LotteryDraw::where('draw_time', $nextDrawTime)
                                       ->where('status', 'pending')
                                       ->first();
            
            if ($existingDraw) {
                return back()->with('error', 'A draw already exists for this time slot.');
            }
            
            // Create new auto draw
            $draw = $this->createAutoDraw($settings, $nextDrawTime);
            
            return redirect()->route('admin.lottery.draws.details', $draw->id)
                           ->with('success', 'Auto draw generated successfully! Draw Number: ' . $draw->draw_number);
                           
        } catch (Exception $e) {
            Log::error('Auto generate draw error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate auto draw: ' . $e->getMessage());
        }
    }

    /**
     * Execute auto draw immediately
     */
    public function executeAutoDraw($id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);
            
            if ($draw->status !== 'pending') {
                return back()->with('error', 'Only pending draws can be executed.');
            }
            
            $settings = LotterySetting::getSettings();
            $this->executeDrawNow($draw, $settings);
            
            return back()->with('success', 'Draw executed successfully!');
            
        } catch (Exception $e) {
            Log::error('Execute auto draw error: ' . $e->getMessage());
            return back()->with('error', 'Failed to execute draw: ' . $e->getMessage());
        }
    }

    /**
     * Manual winner selection interface
     */
    public function manualWinnerSelection($id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);
            
            if ($draw->status !== 'pending') {
                return back()->with('error', 'Winners can only be selected for pending draws.');
            }
            
            // Get all tickets for this draw (real tickets only)
            $tickets = LotteryTicket::with('user')
                                   ->where('lottery_draw_id', $id)
                                   ->where('status', 'active')
                                   ->where(function($query) {
                                       $query->where('is_virtual', false)
                                             ->orWhereNull('is_virtual');
                                   })
                                   ->get();
            
            // Get eligible users (users with tickets)
            $users = User::whereIn('id', $tickets->pluck('user_id'))
                        ->orderBy('username')
                        ->get();
            
            $prizeStructure = $draw->prize_distribution;
            
            return view('admin.lottery.manual-winners', compact('draw', 'tickets', 'users', 'prizeStructure'))
                  ->with('pageTitle', 'Manual Winner Selection - ' . $draw->draw_number);
                  
        } catch (Exception $e) {
            Log::error('Manual winner selection error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load manual winner selection.');
        }
    }

    /**
     * Show manual winner manipulation page
     */
    public function manualWinnerManipulation($id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);
            
            if ($draw->status === 'completed') {
                return back()->with('error', 'Cannot manipulate winners for completed draws.');
            }
            
            // Get only valid, active tickets for this draw (exclude virtual, used, and expired tickets)
            $tickets = LotteryTicket::with('user')
                                   ->where('lottery_draw_id', $id)
                                   ->where('status', 'active') // Only active tickets
                                   ->where(function($query) {
                                       $query->where('is_virtual', false)
                                             ->orWhereNull('is_virtual'); // Exclude virtual tickets
                                   })
                                   ->whereNull('used_as_token_at') // Exclude tickets used as tokens
                                   ->whereNotExists(function($query) {
                                       // Exclude tickets marked as used in used_tickets table
                                       $query->select(DB::raw(1))
                                             ->from('used_tickets')
                                             ->whereColumn('used_tickets.ticket_number', 'lottery_tickets.ticket_number')
                                             ->whereIn('used_tickets.status', ['used', 'invalid']);
                                   })
                                   ->get();
            
            // Get current prize distribution structure
            $prizeDistribution = json_decode($draw->prize_distribution, true) ?? [];
            
            // Get existing manual winners if any (ordered by position)
            $existingWinners = LotteryWinner::with(['user', 'lotteryTicket'])
                                          ->where('lottery_draw_id', $id)
                                          ->orderBy('prize_position', 'asc')
                                          ->get();
            
            return view('admin.lottery.manual-winner-manipulation', compact( 
                'draw', 
                'tickets', 
                'prizeDistribution', 
                'existingWinners'
            ))->with('pageTitle', 'Manual Winner Manipulation - ' . $draw->draw_number);
                  
        } catch (Exception $e) {
            Log::error('Manual winner manipulation error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load manual winner manipulation.');
        }
    }

    /**
     * Validate ticket availability for winner selection
     */
    public function validateTicketAvailability(Request $request, $drawId, $ticketId)
    {
        try {
            $draw = LotteryDraw::findOrFail($drawId);
            $ticket = LotteryTicket::findOrFail($ticketId);
            
            // Check if ticket belongs to this draw
            if ($ticket->lottery_draw_id != $drawId) {
                return response()->json([
                    'valid' => false,
                    'reason' => 'ticket_not_in_draw',
                    'message' => 'This ticket does not belong to the selected draw.'
                ]);
            }
            
            // Check if ticket is already a winner
            $existingWinner = LotteryWinner::where('lottery_draw_id', $drawId)
                                         ->where('lottery_ticket_id', $ticketId)
                                         ->first();
            
            if ($existingWinner) {
                return response()->json([
                    'valid' => false,
                    'reason' => 'already_winner',
                    'message' => 'This ticket is already selected as a winner.',
                    'position' => $existingWinner->prize_position
                ]);
            }
            
            // Check if ticket is still valid (not used/expired)
            $isValidTicket = LotteryTicket::where('id', $ticketId)
                                         ->where('status', 'active')
                                         ->where(function($query) {
                                             $query->where('is_virtual', false)
                                                   ->orWhereNull('is_virtual');
                                         })
                                         ->whereNull('used_as_token_at')
                                         ->whereNotExists(function($query) use ($ticket) {
                                             $query->select(DB::raw(1))
                                                   ->from('used_tickets')
                                                   ->where('ticket_number', $ticket->ticket_number)
                                                   ->whereIn('status', ['used', 'invalid']);
                                         })
                                         ->exists();
            
            if (!$isValidTicket) {
                return response()->json([
                    'valid' => false,
                    'reason' => 'ticket_unavailable',
                    'message' => 'This ticket is no longer available for selection (may have been used or expired).'
                ]);
            }
            
            return response()->json([
                'valid' => true,
                'message' => 'Ticket is available for winner selection.',
                'ticket_number' => $ticket->ticket_number,
                'user' => $ticket->user ? $ticket->user->username : 'N/A'
            ]);
            
        } catch (Exception $e) {
            Log::error('Ticket validation error: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'reason' => 'validation_error',
                'message' => 'Error validating ticket availability.'
            ], 500);
        }
    }

    /**
     * Save manually selected winning tickets
     */
    public function saveManualWinningTickets(Request $request, $id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);
            
            if ($draw->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot manipulate winners for completed draws.'
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'manual_winners' => 'required|array',
                'manual_winners.*.ticket_id' => 'required|integer|exists:lottery_tickets,id',
                'manual_winners.*.position' => 'required|integer|min:1|max:3',
                'manual_winners.*.winner_index' => 'required|integer|min:1|max:2',
                'manual_winners.*.prize_amount' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Clear existing winners for this draw
            LotteryWinner::where('lottery_draw_id', $id)->delete();

            // Create new manual winners
            foreach ($request->manual_winners as $manualWinner) {
                $ticket = LotteryTicket::findOrFail($manualWinner['ticket_id']);
                
                // Verify ticket belongs to this draw
                if ($ticket->lottery_draw_id != $id) {
                    throw new Exception("Ticket {$ticket->ticket_number} does not belong to this draw");
                }
                
                LotteryWinner::create([
                    'lottery_draw_id' => $id,
                    'lottery_ticket_id' => $ticket->id,
                    'user_id' => $ticket->user_id,
                    'prize_position' => $manualWinner['position'],
                    'winner_index' => $manualWinner['winner_index'],
                    'prize_name' => $this->getPrizeName($manualWinner['position'], $manualWinner['winner_index']),
                    'prize_amount' => $manualWinner['prize_amount'],
                    'claim_status' => 'pending',
                    'is_manual_selection' => true,
                    'selected_at' => now(),
                    'selected_by' => Auth::id()
                ]);

                // Update ticket status to winner
                $ticket->update(['status' => 'winner']);
            }

            // Mark draw as having manual winner selection
            $draw->update([
                'manual_winner_selection_enabled' => true,
                'has_manual_winners' => true
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Manual winners saved successfully! Only selected tickets will win.',
                'winner_count' => count($request->manual_winners)
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Save manual winning tickets error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to save manual winners: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear manual winner selection (revert to automatic)
     */
    public function clearManualWinners($id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);
            
            if ($draw->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot clear winners for completed draws.'
                ], 400);
            }

            DB::beginTransaction();

            // Clear all winners
            LotteryWinner::where('lottery_draw_id', $id)->delete();

            // Reset ticket statuses
            LotteryTicket::where('lottery_draw_id', $id)
                         ->where('status', 'winner')
                         ->update(['status' => 'active']);

            // Mark draw as not having manual winners
            $draw->update([
                'has_manual_winners' => false,
                'manual_winner_selection_enabled' => false
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Manual winner selection cleared. Draw will use automatic winner selection when executed.'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Clear manual winners error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear manual winners: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tickets for a specific draw (AJAX)
     */
    public function getDrawTickets($id)
    {
        try {
            $draw = LotteryDraw::findOrFail($id);
            
            // Only show truly active tickets (not used, not expired, not virtual)
            $tickets = LotteryTicket::with('user:id,username,email')
                ->where('lottery_draw_id', $draw->id)
                ->where('status', 'active')
                ->where(function($query) {
                    $query->where('is_virtual', false)
                          ->orWhereNull('is_virtual');
                })
                ->whereNull('used_as_token_at')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('used_tickets')
                          ->whereColumn('used_tickets.ticket_number', 'lottery_tickets.ticket_number')
                          ->whereIn('used_tickets.status', ['used', 'invalid']);
                })
                ->orderBy('ticket_number')
                ->get();
            
            return response()->json([ 
                'success' => true,
                'tickets' => $tickets->map(function($ticket) {
                    return [
                        'id' => $ticket->id,
                        'ticket_number' => $ticket->ticket_number,
                        'status' => $ticket->status,
                        'created_at' => $ticket->created_at,
                        'user' => $ticket->user ? [
                            'id' => $ticket->user->id,
                            'username' => $ticket->user->username,
                            'email' => $ticket->user->email
                        ] : null
                    ];
                })
            ]);
        } catch (Exception $e) {
            Log::error('Error getting draw tickets: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load tickets.'
            ], 500);
        }
    }

    /**
     * Get prize name based on position and winner index
     */
    private function getPrizeName($position, $winnerIndex)
    {
        $positions = [
            1 => '1st Prize',
            2 => '2nd Prize', 
            3 => '3rd Prize'
        ];
        
        return ($positions[$position] ?? "Position {$position}") . " (Winner {$winnerIndex})";
    }

    /**
     * Calculate next draw time
     */
    private function calculateNextDrawTime(LotterySetting $settings)
    {
        $now = Carbon::now();
        
        switch ($settings->auto_generation_frequency) {
            case 'daily':
                return $now->addDay()->setTime(
                    $settings->draw_time->hour,
                    $settings->draw_time->minute
                );
                
            case 'weekly':
                return $now->next($settings->draw_day)->setTime(
                    $settings->draw_time->hour,
                    $settings->draw_time->minute
                );
                
            case 'monthly':
                return $now->addMonth()->day(1)->setTime(
                    $settings->draw_time->hour,
                    $settings->draw_time->minute
                );
                
            default:
                return $now->addDay()->setTime(20, 0);
        }
    }

    /**
     * Create auto draw
     */
    private function createAutoDraw(LotterySetting $settings, Carbon $drawTime)
    {
        $maxTickets = $settings->max_virtual_tickets ?? 1000;
        $ticketPrice = $settings->ticket_price;
        $totalRevenue = $maxTickets * $ticketPrice;
        $adminCommission = $totalRevenue * ($settings->admin_commission_percentage / 100);
        $prizePool = $totalRevenue - $adminCommission;
        
        // Get prize structure from lottery settings
        $prizeStructure = $settings->prize_structure ?? null;
        $prizeDistribution = [];
        
        if ($prizeStructure && is_string($prizeStructure)) {
            $prizeStructure = json_decode($prizeStructure, true);
        }
        
        if ($prizeStructure && is_array($prizeStructure)) {
            // Use actual prize structure from settings
            foreach ($prizeStructure as $position => $prizeInfo) {
                $winnersCount = $settings->number_of_winners_per_position ?? 2; // Default to 2 winners per position
                
                for ($winnerIndex = 1; $winnerIndex <= $winnersCount; $winnerIndex++) {
                    $prizeDistribution[] = [
                        'position' => (int) $position,
                        'winner_index' => $winnerIndex,
                        'amount' => (float) ($prizeInfo['amount'] ?? 0),
                        'name' => ($prizeInfo['name'] ?? "Position {$position}") . ($winnersCount > 1 ? " (Winner {$winnerIndex})" : ''),
                        'type' => $prizeInfo['type'] ?? 'fixed_amount'
                    ];
                }
            }
        } else {
            // Fallback to default prizes if no prize structure is set
            $defaultPrizes = [
                1 => ['name' => '1st Prize', 'amount' => 50, 'winners' => 2],
                2 => ['name' => '2nd Prize', 'amount' => 50, 'winners' => 2], 
                3 => ['name' => '3rd Prize', 'amount' => 50, 'winners' => 2],
            ];
            
            foreach ($defaultPrizes as $position => $prizeInfo) {
                for ($winnerIndex = 1; $winnerIndex <= $prizeInfo['winners']; $winnerIndex++) {
                    $prizeDistribution[] = [
                        'position' => $position,
                        'winner_index' => $winnerIndex,
                        'amount' => (float) $prizeInfo['amount'],
                        'name' => $prizeInfo['name'] . ' (Winner ' . $winnerIndex . ')',
                        'type' => 'fixed_amount'
                    ];
                }
            }
        }
        
        $drawNumber = 'AUTO_' . $drawTime->format('Y_m_d_H_i');
        
        return LotteryDraw::create([
            'draw_number' => $drawNumber,
            'draw_date' => $drawTime->toDateString(),
            'draw_time' => $drawTime,
            'status' => 'pending',
            'total_prize_pool' => $prizePool,
            'total_tickets_sold' => 0,
            'max_tickets' => $maxTickets,
            'ticket_price' => $ticketPrice,
            'admin_commission_percentage' => $settings->admin_commission_percentage,
            'number_of_winners' => count($prizeDistribution),
            'prize_distribution_type' => 'fixed_amount',
            'auto_draw' => true,
            'auto_prize_distribution' => true,
            'prize_distribution' => json_encode($prizeDistribution),
            'enable_virtual_tickets' => $settings->enable_virtual_tickets ?? true,
            'manual_winner_selection_enabled' => $settings->enable_manual_winner_selection ?? false,
        ]);
    }

    /**
     * Execute draw immediately
     */
    private function executeDrawNow(LotteryDraw $draw, LotterySetting $settings)
    {
        // Generate virtual tickets if needed
        if ($settings->enable_virtual_tickets && $draw->virtual_tickets_sold == 0) {
            $this->generateVirtualTicketsForDraw($draw, $settings);
        }
        
        // Check if draw has manual winners set
        if ($draw->has_manual_winners) {
            // Manual winners already set, just update draw status
            $draw->update([
                'status' => 'drawn',
                'drawn_at' => now()
            ]);
            
            Log::info("Draw {$draw->draw_number} executed with manual winners");
        } else {
            // Execute the draw using the automatic command logic
            Artisan::call('lottery:auto-process', ['--force' => true]);
            
            Log::info("Draw {$draw->draw_number} executed with automatic winner selection");
        }
    }

    /**
     * Generate virtual tickets for a specific draw
     */
    private function generateVirtualTicketsForDraw(LotteryDraw $draw, LotterySetting $settings)
    {
        $minVirtual = $settings->min_virtual_tickets ?? 100;
        $maxVirtual = $settings->max_virtual_tickets ?? 1000;
        $virtualCount = rand($minVirtual, $maxVirtual);
        
        $users = User::where('is_active', true)->inRandomOrder()->limit($virtualCount)->get();
        
        $tickets = [];
        $ticketCount = 0;
        
        foreach ($users as $user) {
            $userTickets = rand(1, 3);
            
            for ($i = 0; $i < $userTickets && $ticketCount < $virtualCount; $i++) {
                $tickets[] = [
                    'ticket_number' => 'VT_' . $draw->id . '_' . str_pad($ticketCount + 1, 6, '0', STR_PAD_LEFT),
                    'user_id' => $user->id,
                    'lottery_draw_id' => $draw->id,
                    'ticket_price' => $draw->ticket_price,
                    'purchased_at' => now(),
                    'status' => 'active',
                    'payment_method' => 'virtual',
                    'is_virtual' => true,
                    'virtual_user_type' => 'real_user',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $ticketCount++;
            }
            
            if ($ticketCount >= $virtualCount) break;
        }
        
        if (!empty($tickets)) {
            LotteryTicket::insert($tickets);
            
            $draw->update([
                'virtual_tickets_sold' => count($tickets),
                'display_tickets_sold' => count($tickets),
                'total_tickets_sold' => $draw->total_tickets_sold + count($tickets)
            ]);
        }
    }
}
