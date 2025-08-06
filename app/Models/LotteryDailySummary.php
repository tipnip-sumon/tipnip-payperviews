<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LotteryDailySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'summary_date',
        'total_tickets_purchased',
        'total_amount_spent',
        'total_winnings',
        'net_result',
        'draws_participated',
        'winning_tickets',
        'ticket_details'
    ];

    protected $casts = [
        'summary_date' => 'date',
        'total_amount_spent' => 'decimal:8',
        'total_winnings' => 'decimal:8',
        'net_result' => 'decimal:8',
        'ticket_details' => 'array'
    ];

    /**
     * Get the user who owns this summary
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get summaries for a specific date range
     */
    public function scopeForDateRange($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('summary_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get summaries for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get win rate percentage
     */
    public function getWinRateAttribute()
    {
        if ($this->total_tickets_purchased == 0) {
            return 0;
        }
        
        return ($this->winning_tickets / $this->total_tickets_purchased) * 100;
    }

    /**
     * Check if this was a profitable day
     */
    public function getIsProfitableAttribute()
    {
        return $this->net_result > 0;
    }

    /**
     * Get formatted net result with profit/loss indicator
     */
    public function getFormattedNetResultAttribute()
    {
        $prefix = $this->net_result >= 0 ? '+$' : '-$';
        return $prefix . number_format(abs($this->net_result), 8);
    }

    /**
     * Scope to get old summaries
     */
    public function scopeOlderThan($query, Carbon $date)
    {
        return $query->where('summary_date', '<', $date);
    }

    /**
     * Scope to get summaries in date range
     */
    public function scopeBetweenDates($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('summary_date', [$startDate, $endDate]);
    }

    /**
     * Get duplicate summaries (same user, same date)
     */
    public static function getDuplicates()
    {
        return static::select('user_id', 'summary_date')
            ->groupBy('user_id', 'summary_date')
            ->havingRaw('COUNT(*) > 1')
            ->get();
    }

    /**
     * Delete summaries older than specified days
     */
    public static function deleteOlder(int $days)
    {
        $cutoffDate = now()->subDays($days);
        return static::where('summary_date', '<', $cutoffDate)->delete();
    }

    /**
     * Delete duplicates, keeping the most recent one
     */
    public static function deleteDuplicates()
    {
        $duplicates = static::select('user_id', 'summary_date', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id', 'summary_date')
            ->having('count', '>', 1)
            ->get();

        $deletedCount = 0;
        foreach ($duplicates as $duplicate) {
            // Keep the most recent and delete the rest
            $toDelete = static::where('user_id', $duplicate->user_id)
                ->where('summary_date', $duplicate->summary_date)
                ->orderBy('updated_at', 'desc')
                ->skip(1)
                ->get();
                
            foreach ($toDelete as $summary) {
                $summary->delete();
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}
