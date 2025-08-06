<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\AdminTransReceive;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminTransReceiveController extends Controller
{
    public function index()
    {
        return view('admin.transfer.transfer_member');
    }
    public function store(Request $request)
    {
        // Log incoming request for debugging
        Log::info('Transfer request received', [
            'request_data' => $request->all(),
            'admin_id' => Auth::guard('admin')->id(),
            'is_ajax' => $request->ajax()
        ]);

        // Validate the request
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'user_receive' => 'required|string|max:255',
            'note' => 'nullable|string|max:500',
            'password' => 'required|string|min:6',
        ]);

        try {
            // Get the authenticated admin
            $adminId = Auth::guard('admin')->id();
            $admin = Admin::find($adminId);
            
            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin not authenticated'
                ], 401);
            }

            // Verify admin password
            if (!Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction password is incorrect'
                ], 400);
            }

            // Find the user to receive funds (by username)
            $user = User::where('username', $request->user_receive)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Check if user is active
            if ($user->status != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot transfer to inactive user'
                ], 400);
            }

            $amount = floatval($request->amount);

            // Check admin balance (assuming admin has a balance field)
            $adminBalance = $admin->balance ?? 0;
            if ($adminBalance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient admin balance'
                ], 400);
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Deduct from admin balance
                $admin->balance = $admin->balance - $amount;
                if (!$admin->save()) {
                    throw new \Exception('Failed to update admin balance');
                }

                // Add to user's deposit wallet
                $user->deposit_wallet = $user->deposit_wallet + $amount;
                if (!$user->save()) {
                    throw new \Exception('Failed to update user balance');
                }

                // Create transaction record for admin (using admin ID as user_id for admin transactions)
                Transaction::create([
                    'user_id' => $admin->id, // Use admin ID instead of null
                    // 'admin_id' => $admin->id,
                    'amount' => -$amount, // Negative for deduction
                    'charge' => 0,
                    'post_balance' => $admin->balance,
                    'trx_type' => '-',
                    'trx' => 'TRX' . time() . rand(100, 999),
                    'remark' => 'admin_transfer_out',
                    'details' => "Transferred $amount to user {$user->username}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create transaction record for user
                Transaction::create([
                    'user_id' => $user->id,
                    'admin_id' => $admin->id, // Keep track of which admin sent the transfer
                    'amount' => $amount, // Positive for addition
                    'charge' => 0,
                    'post_balance' => $user->deposit_wallet,
                    'trx_type' => '+',
                    'trx' => 'TRX' . time() . rand(100, 999),
                    'remark' => 'admin_transfer_in',
                    'details' => "Received $amount from admin {$admin->username}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create transfer record
                AdminTransReceive::create([
                    'admin_id' => $admin->id,
                    'user_transfer' => $admin->username,
                    'amount' => $amount,
                    'status' => true,
                    'user_receive' => $user->username,
                    'note' => $request->note ?? '',
                ]);

                // Commit the transaction
                DB::commit();

                // Log the successful transfer
                Log::info("Admin transfer successful", [
                    'admin_id' => $admin->id,
                    'admin_username' => $admin->username,
                    'user_id' => $user->id,
                    'user_username' => $user->username,
                    'amount' => $amount,
                    'note' => $request->note
                ]);

                // Return success response
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Successfully transferred $" . number_format($amount, 2) . " to {$user->username}",
                        'data' => [
                            'admin_balance' => $admin->balance,
                            'user_balance' => $user->deposit_wallet,
                            'transfer_amount' => $amount
                        ]
                    ]);
                }

                return redirect()->back()->with('success', "Successfully transferred $" . number_format($amount, 2) . " to {$user->username}");

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Admin transfer error: ' . $e->getMessage(), [
                'admin_id' => $admin->id ?? null,
                'user_receive' => $request->user_receive,
                'amount' => $request->amount,
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transfer failed: ' . $e->getMessage(),
                    'debug' => config('app.debug') ? [
                        'error' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => basename($e->getFile())
                    ] : null
                ], 500);
            }

            return redirect()->back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }

    /**
     * Show transfer history with advanced filtering
     */
    public function history(Request $request)
    {
        $query = AdminTransReceive::with(['admin']);
        
        // Apply filters
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'weekly':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'monthly':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'yearly':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('user_receive', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%");
            });
        }
        
        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }
        
        // Amount range filter
        if ($request->filled('min_amount') && is_numeric($request->min_amount)) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount') && is_numeric($request->max_amount)) {
            $query->where('amount', '<=', $request->max_amount);
        }
        
        $transfers = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Calculate statistics
        $stats = $this->getInternalTransferStats($request);
        
        // Handle export request
        if ($request->has('export')) {
            return $this->exportTransfers($query, $request->export);
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'transfers' => $transfers,
                'stats' => $stats
            ]);
        }
        
        return view('admin.transfer.transfer_history', compact('transfers', 'stats'));
    }

    /**
     * Get transfer details with related transactions
     */
    public function details($id)
    {
        try {
            $transfer = AdminTransReceive::with(['admin'])->findOrFail($id);
            
            // Get related transactions for this transfer
            $transactions = Transaction::where(function($query) use ($transfer) {
                $query->where('details', 'like', "%{$transfer->user_receive}%")
                      ->orWhere('details', 'like', "%{$transfer->amount}%");
            })
            ->where('created_at', '>=', $transfer->created_at->subMinutes(5))
            ->where('created_at', '<=', $transfer->created_at->addMinutes(5))
            ->orderBy('created_at', 'asc')
            ->get();

            return response()->json([
                'success' => true,
                'transfer' => $transfer,
                'transactions' => $transactions
            ]);
            
        } catch (\Exception $e) {
            Log::error('Transfer details error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Transfer not found or error retrieving details.'
            ], 404);
        }
    }
    
    /**
     * Get transfer statistics for dashboard (API endpoint)
     */
    public function getTransferStats(Request $request = null)
    {
        try {
            $stats = [
                'total_transfers' => AdminTransReceive::count(),
                'today_transfers' => AdminTransReceive::whereDate('created_at', today())->count(),
                'weekly_transfers' => AdminTransReceive::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'monthly_transfers' => AdminTransReceive::whereMonth('created_at', now()->month)
                                                       ->whereYear('created_at', now()->year)
                                                       ->count(),
                'total_amount' => AdminTransReceive::sum('amount'),
                'today_amount' => AdminTransReceive::whereDate('created_at', today())->sum('amount'),
                'weekly_amount' => AdminTransReceive::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->sum('amount'),
                'monthly_amount' => AdminTransReceive::whereMonth('created_at', now()->month)
                                                    ->whereYear('created_at', now()->year)
                                                    ->sum('amount'),
            ];
            
            if ($request && $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'stats' => $stats
                ]);
            }
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error('Transfer stats error: ' . $e->getMessage());
            
            if ($request && $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error fetching transfer statistics'
                ], 500);
            }
            
            return [];
        }
    }
    
    /**
     * Get transfer statistics for internal use
     */
    /**
     * Get transfer statistics for internal use
     */
    private function getInternalTransferStats(Request $request = null)
    {
        $query = AdminTransReceive::query();
        
        // Apply same filters as history if request provided
        if ($request && $request->has('filter')) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'weekly':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'monthly':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        }
        
        return [
            'total_transfers' => AdminTransReceive::count(),
            'today_transfers' => AdminTransReceive::whereDate('created_at', today())->count(),
            'weekly_transfers' => AdminTransReceive::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'monthly_transfers' => AdminTransReceive::whereMonth('created_at', now()->month)
                                                   ->whereYear('created_at', now()->year)
                                                   ->count(),
            'total_amount' => AdminTransReceive::sum('amount'),
            'today_amount' => AdminTransReceive::whereDate('created_at', today())->sum('amount'),
            'weekly_amount' => AdminTransReceive::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->sum('amount'),
            'monthly_amount' => AdminTransReceive::whereMonth('created_at', now()->month)
                                                ->whereYear('created_at', now()->year)
                                                ->sum('amount'),
            'filtered_count' => $query->count(),
            'filtered_amount' => $query->sum('amount')
        ];
    }
    
    /**
     * Export transfers to Excel/CSV
     */
    public function exportTransfers($query, $format = 'excel')
    {
        try {
            $transfers = $query->get();
            
            if ($format === 'csv') {
                return $this->exportToCsv($transfers);
            } else {
                return $this->exportToExcel($transfers);
            }
            
        } catch (\Exception $e) {
            Log::error('Transfer export error: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to export transfers. Please try again.');
        }
    }
    
    /**
     * Export to CSV format
     */
    private function exportToCsv($transfers)
    {
        $filename = 'transfers_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($transfers) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Recipient',
                'Amount',
                'Note',
                'Admin',
                'Date',
                'Time'
            ]);
            
            // Add data rows
            foreach ($transfers as $transfer) {
                fputcsv($file, [
                    $transfer->id,
                    $transfer->user_receive,
                    number_format($transfer->amount, 2),
                    $transfer->note,
                    $transfer->admin ? $transfer->admin->name : 'N/A',
                    $transfer->created_at->format('Y-m-d'),
                    $transfer->created_at->format('H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export to Excel format (simple HTML table)
     */
    private function exportToExcel($transfers)
    {
        $filename = 'transfers_export_' . now()->format('Y_m_d_H_i_s') . '.xls';
        
        $headers = [
            'Content-type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $html = '
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Recipient</th>
                    <th>Amount</th>
                    <th>Note</th>
                    <th>Admin</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>';
            
        foreach ($transfers as $transfer) {
            $html .= '
                <tr>
                    <td>' . $transfer->id . '</td>
                    <td>' . htmlspecialchars($transfer->user_receive) . '</td>
                    <td>' . number_format($transfer->amount, 2) . '</td>
                    <td>' . htmlspecialchars($transfer->note) . '</td>
                    <td>' . ($transfer->admin ? htmlspecialchars($transfer->admin->name) : 'N/A') . '</td>
                    <td>' . $transfer->created_at->format('Y-m-d') . '</td>
                    <td>' . $transfer->created_at->format('H:i:s') . '</td>
                </tr>';
        }
        
        $html .= '
            </tbody>
        </table>';
        
        return response($html, 200, $headers);
    }
    
    /**
     * Get transfer reports for analytics
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', 'monthly');
        
        switch ($period) {
            case 'daily':
                $transfers = AdminTransReceive::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get();
                break;
                
            case 'weekly':
                $transfers = AdminTransReceive::selectRaw('YEARWEEK(created_at) as week, COUNT(*) as count, SUM(amount) as total')
                    ->where('created_at', '>=', now()->subWeeks(12))
                    ->groupBy('week')
                    ->orderBy('week', 'desc')
                    ->get();
                break;
                
            case 'monthly':
            default:
                $transfers = AdminTransReceive::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(amount) as total')
                    ->where('created_at', '>=', now()->subMonths(12))
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get();
                break;
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $transfers,
                'period' => $period
            ]);
        }
        
        return view('admin.transfer.reports', compact('transfers', 'period'));
    }
    
    /**
     * Delete transfer record (soft delete)
     */
    public function destroy($id)
    {
        try {
            $transfer = AdminTransReceive::findOrFail($id);
            
            // Check if admin has permission to delete
            if (!Auth::guard('admin')->user()->is_super_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only super admins can delete transfer records.'
                ], 403);
            }
            
            // Log the deletion
            Log::info('Transfer record deleted', [
                'transfer_id' => $transfer->id,
                'recipient' => $transfer->user_receive,
                'amount' => $transfer->amount,
                'deleted_by' => Auth::guard('admin')->id()
            ]);
            
            $transfer->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Transfer record deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Transfer deletion error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transfer record.'
            ], 500);
        }
    }
}
