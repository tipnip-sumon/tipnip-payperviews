<?php

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Transaction Helper Functions
 * Specialized functions for handling transactions and financial operations
 */

if (!function_exists('createTransaction')) {
    /**
     * Create a new transaction record
     */
    function createTransaction($userId, $amount, $type, $details = [], $charge = 0)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('User not found');
            }

            $transaction = Transaction::create([
                'user_id' => $userId,
                'amount' => $amount,
                'charge' => $charge,
                'post_balance' => $user->balance,
                'trx_type' => $type === 'credit' ? '+' : '-',
                'trx' => getTrx(),
                'remark' => $details['remark'] ?? $type,
                'details' => json_encode($details),
            ]);

            return $transaction;
        } catch (\Exception $e) {
            Log::error('Failed to create transaction: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('creditUser')) {
    /**
     * Credit amount to user balance
     */
    function creditUser($userId, $amount, $remark = 'Credit', $details = [])
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('User not found');
            }

            $user->balance += $amount;
            $user->save();

            $transaction = createTransaction($userId, $amount, 'credit', array_merge([
                'remark' => $remark,
                'previous_balance' => $user->balance - $amount,
                'new_balance' => $user->balance,
            ], $details));

            return $transaction;
        } catch (\Exception $e) {
            Log::error('Failed to credit user: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('debitUser')) {
    /**
     * Debit amount from user balance
     */
    function debitUser($userId, $amount, $remark = 'Debit', $details = [])
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('User not found');
            }

            if ($user->balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            $user->balance -= $amount;
            $user->save();

            $transaction = createTransaction($userId, $amount, 'debit', array_merge([
                'remark' => $remark,
                'previous_balance' => $user->balance + $amount,
                'new_balance' => $user->balance,
            ], $details));

            return $transaction;
        } catch (\Exception $e) {
            Log::error('Failed to debit user: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('transferBalance')) {
    /**
     * Transfer balance between users
     */
    function transferBalance($fromUserId, $toUserId, $amount, $remark = 'Transfer', $charge = 0)
    {
        try {
            $fromUser = User::find($fromUserId);
            $toUser = User::find($toUserId);

            if (!$fromUser || !$toUser) {
                throw new \Exception('User(s) not found');
            }

            $totalAmount = $amount + $charge;
            if ($fromUser->balance < $totalAmount) {
                throw new \Exception('Insufficient balance');
            }

            // Debit from sender
            $debitTransaction = debitUser($fromUserId, $totalAmount, $remark . ' (Sent)', [
                'transfer_type' => 'sent',
                'recipient_id' => $toUserId,
                'transfer_amount' => $amount,
                'transfer_charge' => $charge,
            ]);

            // Credit to receiver
            $creditTransaction = creditUser($toUserId, $amount, $remark . ' (Received)', [
                'transfer_type' => 'received',
                'sender_id' => $fromUserId,
                'transfer_amount' => $amount,
            ]);

            return [
                'debit' => $debitTransaction,
                'credit' => $creditTransaction,
                'success' => true
            ];
        } catch (\Exception $e) {
            Log::error('Failed to transfer balance: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

if (!function_exists('getUserTransactions')) {
    /**
     * Get user transactions with filters
     */
    function getUserTransactions($userId, $filters = [])
    {
        try {
            $query = Transaction::where('user_id', $userId);

            if (isset($filters['type'])) {
                $query->where('trx_type', $filters['type']);
            }

            if (isset($filters['remark'])) {
                $query->where('remark', 'like', '%' . $filters['remark'] . '%');
            }

            if (isset($filters['from_date'])) {
                $query->whereDate('created_at', '>=', $filters['from_date']);
            }

            if (isset($filters['to_date'])) {
                $query->whereDate('created_at', '<=', $filters['to_date']);
            }

            return $query->orderBy('created_at', 'desc')
                        ->paginate($filters['per_page'] ?? 20);
        } catch (\Exception $e) {
            Log::error('Failed to get user transactions: ' . $e->getMessage());
            return collect();
        }
    }
}

if (!function_exists('calculateTransactionSummary')) {
    /**
     * Calculate transaction summary for user
     */
    function calculateTransactionSummary($userId, $days = 30)
    {
        try {
            $fromDate = now()->subDays($days);
            
            $summary = Transaction::where('user_id', $userId)
                ->where('created_at', '>=', $fromDate)
                ->selectRaw('
                    SUM(CASE WHEN trx_type = "+" THEN amount ELSE 0 END) as total_credit,
                    SUM(CASE WHEN trx_type = "-" THEN amount ELSE 0 END) as total_debit,
                    COUNT(CASE WHEN trx_type = "+" THEN 1 END) as credit_count,
                    COUNT(CASE WHEN trx_type = "-" THEN 1 END) as debit_count
                ')
                ->first();

            return [
                'total_credit' => $summary->total_credit ?? 0,
                'total_debit' => $summary->total_debit ?? 0,
                'credit_count' => $summary->credit_count ?? 0,
                'debit_count' => $summary->debit_count ?? 0,
                'net_amount' => ($summary->total_credit ?? 0) - ($summary->total_debit ?? 0),
                'period_days' => $days
            ];
        } catch (\Exception $e) {
            Log::error('Failed to calculate transaction summary: ' . $e->getMessage());
            return [
                'total_credit' => 0,
                'total_debit' => 0,
                'credit_count' => 0,
                'debit_count' => 0,
                'net_amount' => 0,
                'period_days' => $days
            ];
        }
    }
}

if (!function_exists('getRecentTransactions')) {
    /**
     * Get recent transactions for user
     */
    function getRecentTransactions($userId, $limit = 10)
    {
        try {
            return Transaction::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to get recent transactions: ' . $e->getMessage());
            return collect();
        }
    }
}

if (!function_exists('validateTransaction')) {
    /**
     * Validate transaction before processing
     */
    function validateTransaction($userId, $amount, $type = 'debit')
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return ['valid' => false, 'error' => 'User not found'];
            }

            if ($amount <= 0) {
                return ['valid' => false, 'error' => 'Amount must be greater than zero'];
            }

            if ($type === 'debit' && $user->balance < $amount) {
                return ['valid' => false, 'error' => 'Insufficient balance'];
            }

            // Check daily limits if configured
            $dailyLimit = getSetting('daily_transaction_limit', 0);
            if ($dailyLimit > 0) {
                $todayTransactions = Transaction::where('user_id', $userId)
                    ->whereDate('created_at', today())
                    ->where('trx_type', $type === 'credit' ? '+' : '-')
                    ->sum('amount');

                if (($todayTransactions + $amount) > $dailyLimit) {
                    return ['valid' => false, 'error' => 'Daily transaction limit exceeded'];
                }
            }

            return ['valid' => true];
        } catch (\Exception $e) {
            Log::error('Failed to validate transaction: ' . $e->getMessage());
            return ['valid' => false, 'error' => 'Validation failed'];
        }
    }
}
