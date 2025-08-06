<?php

namespace App\Services;

use App\Models\LotteryDraw;
use App\Models\LotteryTicket;
use App\Models\LotteryWinner;
use App\Models\LotteryDailySummary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LotteryOptimizationService
{
    /**
     * Clean up virtual lottery data after draw completion
     * Keeps only essential data for historical records
     */
    public function cleanupVirtualLotteryData(LotteryDraw $draw): array
    {
        try {
            DB::beginTransaction();
            
            $stats = [
                'virtual_tickets_deleted' => 0,
                'real_tickets_kept' => 0,
                'winners_preserved' => 0,
                'draw_optimized' => false
            ];
            
            // Only clean completed or drawn draws
            if (!in_array($draw->status, ['completed', 'drawn'])) {
                return [
                    'success' => false,
                    'message' => 'Can only clean completed or drawn draws',
                    'stats' => $stats
                ];
            }
            
            // Count existing tickets
            $virtualTicketsCount = LotteryTicket::where('lottery_draw_id', $draw->id)
                ->where('is_virtual', true)
                ->count();
                
            $realTicketsCount = LotteryTicket::where('lottery_draw_id', $draw->id)
                ->where('is_virtual', false)
                ->count();
                
            $winnersCount = LotteryWinner::where('lottery_draw_id', $draw->id)->count();
            
            // Delete virtual tickets (keeping only real user tickets and winners)
            $deletedVirtualTickets = LotteryTicket::where('lottery_draw_id', $draw->id)
                ->where('is_virtual', true)
                ->whereNotIn('id', function($query) use ($draw) {
                    $query->select('lottery_ticket_id')
                          ->from('lottery_winners')
                          ->where('lottery_draw_id', $draw->id)
                          ->whereNotNull('lottery_ticket_id');
                })
                ->delete();
            
            // Update draw record to keep only essential data
            $draw->update([
                'virtual_tickets_sold' => 0, // Clear virtual count
                'total_tickets_sold' => $realTicketsCount, // Keep only real tickets
                'display_tickets_sold' => $realTicketsCount, // Update display count
                'optimized_at' => now(),
                'cleanup_performed' => true
            ]);
            
            $stats = [
                'virtual_tickets_deleted' => $deletedVirtualTickets,
                'real_tickets_kept' => $realTicketsCount,
                'winners_preserved' => $winnersCount,
                'draw_optimized' => true
            ];
            
            DB::commit();
            
            Log::info('Lottery draw optimized', [
                'draw_id' => $draw->id,
                'draw_number' => $draw->draw_number,
                'stats' => $stats
            ]);
            
            return [
                'success' => true,
                'message' => 'Virtual lottery data cleaned successfully',
                'stats' => $stats
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cleaning lottery data', [
                'draw_id' => $draw->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error cleaning lottery data: ' . $e->getMessage(),
                'stats' => $stats
            ];
        }
    }
    
    /**
     * Create daily lottery summary for user (single row per day/user)
     */
    public function createDailyLotterySummary(User $user, Carbon $date = null): array
    {
        $date = $date ?? today();
        
        try {
            // Get user's lottery activities for the day
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $userTickets = LotteryTicket::where('user_id', $user->id)
                ->where('is_virtual', false) // Only real tickets
                ->whereBetween('purchased_at', [$dayStart, $dayEnd])
                ->with(['lotteryDraw', 'winner'])
                ->get();
                
            if ($userTickets->isEmpty()) {
                return [
                    'success' => true,
                    'message' => 'No lottery activity for this date',
                    'summary' => null
                ];
            }
            
            // Create summary data
            $summary = [
                'user_id' => $user->id,
                'summary_date' => $date->format('Y-m-d'),
                'total_tickets_purchased' => $userTickets->count(),
                'total_amount_spent' => $userTickets->sum('ticket_price'),
                'total_winnings' => $userTickets->sum('prize_amount'),
                'draws_participated' => $userTickets->pluck('lottery_draw_id')->unique()->count(),
                'winning_tickets' => $userTickets->where('prize_amount', '>', 0)->count(),
                'ticket_details' => $userTickets->map(function($ticket) {
                    return [
                        'ticket_number' => $ticket->ticket_number,
                        'draw_number' => $ticket->lotteryDraw->draw_number ?? null,
                        'ticket_price' => $ticket->ticket_price,
                        'prize_amount' => $ticket->prize_amount,
                        'status' => $ticket->status,
                        'purchased_at' => $ticket->purchased_at->format('H:i:s')
                    ];
                })->toArray(),
                'net_result' => $userTickets->sum('prize_amount') - $userTickets->sum('ticket_price'),
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Store in a new lottery_daily_summaries table (we'll create this)
            DB::table('lottery_daily_summaries')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'summary_date' => $date->format('Y-m-d')
                ],
                $summary
            );
            
            return [
                'success' => true,
                'message' => 'Daily lottery summary created',
                'summary' => $summary
            ];
            
        } catch (\Exception $e) {
            Log::error('Error creating daily lottery summary', [
                'user_id' => $user->id,
                'date' => $date->format('Y-m-d'),
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error creating summary: ' . $e->getMessage(),
                'summary' => null
            ];
        }
    }
    
    /**
     * Clean up old completed draws and optimize storage
     */
    public function optimizeOldDraws(int $daysToKeep = 30): array
    {
        try {
            $cutoffDate = now()->subDays($daysToKeep);
            
            $oldDraws = LotteryDraw::whereIn('status', ['completed', 'drawn'])
                ->where('drawn_at', '<', $cutoffDate)
                ->whereNull('optimized_at') // Not already optimized
                ->get();
                
            $stats = [
                'draws_processed' => 0,
                'virtual_tickets_deleted' => 0,
                'storage_saved' => 0
            ];
            
            foreach ($oldDraws as $draw) {
                $result = $this->cleanupVirtualLotteryData($draw);
                if ($result['success']) {
                    $stats['draws_processed']++;
                    $stats['virtual_tickets_deleted'] += $result['stats']['virtual_tickets_deleted'];
                }
            }
            
            // Estimate storage saved (approximate)
            $stats['storage_saved'] = $stats['virtual_tickets_deleted'] * 0.5; // KB per record
            
            return [
                'success' => true,
                'message' => "Optimized {$stats['draws_processed']} lottery draws",
                'stats' => $stats
            ];
            
        } catch (\Exception $e) {
            Log::error('Error optimizing old draws', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error optimizing draws: ' . $e->getMessage(),
                'stats' => $stats
            ];
        }
    }
    
    /**
     * Get user's lottery statistics from optimized data
     */
    public function getUserLotteryStats(User $user, int $days = 30): array
    {
        try {
            $startDate = now()->subDays($days)->format('Y-m-d');
            
            $summaries = DB::table('lottery_daily_summaries')
                ->where('user_id', $user->id)
                ->where('summary_date', '>=', $startDate)
                ->orderBy('summary_date', 'desc')
                ->get();
                
            if ($summaries->isEmpty()) {
                return [
                    'total_tickets' => 0,
                    'total_spent' => 0,
                    'total_winnings' => 0,
                    'net_result' => 0,
                    'days_active' => 0,
                    'average_per_day' => 0,
                    'win_rate' => 0,
                    'daily_breakdown' => []
                ];
            }
            
            $stats = [
                'total_tickets' => $summaries->sum('total_tickets_purchased'),
                'total_spent' => $summaries->sum('total_amount_spent'),
                'total_winnings' => $summaries->sum('total_winnings'),
                'net_result' => $summaries->sum('net_result'),
                'days_active' => $summaries->count(),
                'average_per_day' => $summaries->avg('total_amount_spent'),
                'win_rate' => $summaries->sum('winning_tickets') / max(1, $summaries->sum('total_tickets_purchased')) * 100,
                'daily_breakdown' => $summaries->toArray()
            ];
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error('Error getting user lottery stats', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Delete lottery daily summaries older than specified days
     */
    public function deleteOldSummaries(int $daysToKeep = 90): array
    {
        try {
            $cutoffDate = now()->subDays($daysToKeep);
            
            $oldSummaries = LotteryDailySummary::where('summary_date', '<', $cutoffDate)->get();
            
            $stats = [
                'summaries_deleted' => $oldSummaries->count(),
                'users_affected' => $oldSummaries->pluck('user_id')->unique()->count(),
                'date_range_deleted' => [
                    'from' => $oldSummaries->min('summary_date'),
                    'to' => $oldSummaries->max('summary_date')
                ],
                'storage_freed' => 0
            ];
            
            // Calculate approximate storage freed
            $stats['storage_freed'] = $stats['summaries_deleted'] * 0.5; // ~0.5KB per summary
            
            // Delete old summaries
            LotteryDailySummary::where('summary_date', '<', $cutoffDate)->delete();
            
            Log::info('Lottery daily summaries cleanup completed', $stats);
            
            return [
                'success' => true,
                'message' => "Deleted {$stats['summaries_deleted']} old lottery summaries",
                'stats' => $stats
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to delete old lottery summaries', [
                'error' => $e->getMessage(),
                'days_to_keep' => $daysToKeep
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to delete old summaries: ' . $e->getMessage(),
                'stats' => []
            ];
        }
    }

    /**
     * Delete lottery summaries for a specific user
     */
    public function deleteUserSummaries(User $user, Carbon $startDate = null, Carbon $endDate = null): array
    {
        try {
            $query = LotteryDailySummary::where('user_id', $user->id);
            
            if ($startDate) {
                $query->where('summary_date', '>=', $startDate);
            }
            
            if ($endDate) {
                $query->where('summary_date', '<=', $endDate);
            }
            
            $summaries = $query->get();
            $deletedCount = $summaries->count();
            
            $stats = [
                'summaries_deleted' => $deletedCount,
                'user_id' => $user->id,
                'user_name' => $user->username,
                'date_range' => [
                    'from' => $startDate ? $startDate->format('Y-m-d') : 'all',
                    'to' => $endDate ? $endDate->format('Y-m-d') : 'all'
                ],
                'storage_freed' => $deletedCount * 0.5 // ~0.5KB per summary
            ];
            
            // Delete the summaries
            $query->delete();
            
            Log::info('User lottery summaries deleted', $stats);
            
            return [
                'success' => true,
                'message' => "Deleted {$deletedCount} lottery summaries for user {$user->username}",
                'stats' => $stats
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to delete user lottery summaries', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to delete user summaries: ' . $e->getMessage(),
                'stats' => []
            ];
        }
    }

    /**
     * Delete specific lottery summary by ID
     */
    public function deleteSummaryById(int $summaryId): array
    {
        try {
            $summary = LotteryDailySummary::find($summaryId);
            
            if (!$summary) {
                return [
                    'success' => false,
                    'message' => 'Lottery summary not found',
                    'stats' => []
                ];
            }
            
            $stats = [
                'summary_id' => $summaryId,
                'user_id' => $summary->user_id,
                'summary_date' => $summary->summary_date->format('Y-m-d'),
                'tickets_purchased' => $summary->total_tickets_purchased,
                'amount_spent' => $summary->total_amount_spent,
                'winnings' => $summary->total_winnings
            ];
            
            $summary->delete();
            
            Log::info('Lottery summary deleted by ID', $stats);
            
            return [
                'success' => true,
                'message' => "Deleted lottery summary for {$stats['summary_date']}",
                'stats' => $stats
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to delete lottery summary by ID', [
                'error' => $e->getMessage(),
                'summary_id' => $summaryId
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to delete summary: ' . $e->getMessage(),
                'stats' => []
            ];
        }
    }

    /**
     * Delete duplicate lottery summaries (same user, same date)
     */
    public function deleteDuplicateSummaries(): array
    {
        try {
            // Find duplicate summaries
            $duplicates = DB::table('lottery_daily_summaries')
                ->select('user_id', 'summary_date', DB::raw('COUNT(*) as count'))
                ->groupBy('user_id', 'summary_date')
                ->having('count', '>', 1)
                ->get();
            
            $deletedCount = 0;
            $stats = [
                'duplicate_groups' => $duplicates->count(),
                'summaries_deleted' => 0,
                'storage_freed' => 0
            ];
            
            foreach ($duplicates as $duplicate) {
                // Keep the most recent summary and delete the rest
                $summariesToDelete = LotteryDailySummary::where('user_id', $duplicate->user_id)
                    ->where('summary_date', $duplicate->summary_date)
                    ->orderBy('updated_at', 'desc')
                    ->skip(1) // Keep the first (most recent)
                    ->take($duplicate->count - 1)
                    ->get();
                
                foreach ($summariesToDelete as $summary) {
                    $summary->delete();
                    $deletedCount++;
                }
            }
            
            $stats['summaries_deleted'] = $deletedCount;
            $stats['storage_freed'] = $deletedCount * 0.5; // ~0.5KB per summary
            
            Log::info('Duplicate lottery summaries cleanup completed', $stats);
            
            return [
                'success' => true,
                'message' => "Deleted {$deletedCount} duplicate lottery summaries",
                'stats' => $stats
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to delete duplicate lottery summaries', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to delete duplicates: ' . $e->getMessage(),
                'stats' => []
            ];
        }
    }

    /**
     * Bulk delete all lottery summaries (use with extreme caution)
     */
    public function deleteAllSummaries(bool $confirm = false): array
    {
        if (!$confirm) {
            return [
                'success' => false,
                'message' => 'Confirmation required to delete all summaries',
                'stats' => []
            ];
        }
        
        try {
            $totalSummaries = LotteryDailySummary::count();
            $totalUsers = LotteryDailySummary::distinct('user_id')->count();
            
            $stats = [
                'summaries_deleted' => $totalSummaries,
                'users_affected' => $totalUsers,
                'storage_freed' => $totalSummaries * 0.5 // ~0.5KB per summary
            ];
            
            // Delete all summaries
            LotteryDailySummary::truncate();
            
            Log::warning('ALL lottery daily summaries deleted', $stats);
            
            return [
                'success' => true,
                'message' => "Deleted ALL {$totalSummaries} lottery summaries",
                'stats' => $stats
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to delete all lottery summaries', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to delete all summaries: ' . $e->getMessage(),
                'stats' => []
            ];
        }
    }
}
