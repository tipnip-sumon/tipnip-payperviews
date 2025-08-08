<?php

namespace App\Http\Controllers\admin;

use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display all deposits
     */
    public function index(Request $request)
    {
        $pageTitle = 'All Deposits';
        
        if ($request->ajax()) {
            $deposits = Deposit::with(['user', 'gateway'])
                ->when($request->status, function($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->latest();

            return DataTables::of($deposits)
                ->addColumn('user', function ($row) {
                    return $row->user ? $row->user->username : 'N/A';
                })
                ->addColumn('gateway', function ($row) {
                    return $row->gateway ? $row->gateway->name : 'NOWPayments';
                })
                ->addColumn('amount', function ($row) {
                    return '$' . number_format($row->amount, 2);
                })
                ->addColumn('charge', function ($row) {
                    return '$' . number_format($row->charge, 2);
                })
                ->addColumn('total_amount', function ($row) {
                    return '$' . number_format($row->amount + $row->charge, 2);
                })
                ->addColumn('status', function ($row) {
                    $badgeClass = $row->status == 1 ? 'success' : ($row->status == 2 ? 'warning' : 'danger');
                    $statusText = $row->status == 1 ? 'Approved' : ($row->status == 2 ? 'Pending' : 'Rejected');
                    return '<span class="badge bg-' . $badgeClass . '">' . $statusText . '</span>';
                })
                ->addColumn('date', function ($row) {
                    return $row->created_at->format('M d, Y h:i A');
                })
                ->addColumn('actions', function ($row) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('admin.deposits.show', $row->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    
                    if ($row->status == 2) {
                        $actions .= '<button class="btn btn-sm btn-success ms-1" onclick="approveDeposit(' . $row->id . ')"><i class="fas fa-check"></i></button>';
                        $actions .= '<button class="btn btn-sm btn-danger ms-1" onclick="rejectDeposit(' . $row->id . ')"><i class="fas fa-times"></i></button>';
                    }
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        $stats = [
            'total' => Deposit::count(),
            'pending' => Deposit::where('status', 2)->count(),
            'approved' => Deposit::where('status', 1)->count(),
            'rejected' => Deposit::where('status', 3)->count(),
            'total_amount' => Deposit::where('status', 1)->sum('amount'),
            'pending_amount' => Deposit::where('status', 2)->sum('amount'),
        ];

        return view('admin.deposits.index', compact('pageTitle', 'stats'));
    }

    /**
     * Display pending deposits
     */
    public function pending(Request $request)
    {
        $pageTitle = 'Pending Deposits';
        
        $stats = [
            'pending_count' => Deposit::where('status', 2)->count(),
            'pending_amount' => Deposit::where('status', 2)->sum('amount'),
            'today_pending' => Deposit::where('status', 2)->whereDate('created_at', today())->count(),
            'today_amount' => Deposit::where('status', 2)->whereDate('created_at', today())->sum('amount'),
        ];
        
        // Return JSON for AJAX requests (for menu counts)
        // if ($request->expectsJson()) {
        //     return response()->json(['stats' => $stats]);
        // }
        
        if ($request->ajax()) {
            $deposits = Deposit::with(['user', 'gateway'])
                ->where('status', 2) // Only pending deposits
                ->latest();
            return DataTables::of($deposits)
                ->addColumn('user', function ($row) {
                    $user = $row->user;
                    if (!$user) return 'N/A';
                    
                    return '<div class="d-flex align-items-center">' .
                           '<img src="' . $user->avatar_url . '" class="rounded-circle me-2" style="width: 32px; height: 32px;" onerror="this.src=\'' . asset('assets/images/users/16.jpg') . '\'">' .
                           '<div>' .
                           '<div class="fw-medium">' . e($user->fullname) . '</div>' .
                           '<small class="text-muted">@' . e($user->username) . '</small>' .
                           '</div></div>';
                })
                ->addColumn('gateway', function ($row) {
                    return $row->gateway ? $row->gateway->name : 'N/A';
                })
                ->addColumn('amount', function ($row) {
                    return '<div class="align-items-center">' .
                           '<div class="fw-bold">$' . number_format($row->amount, 2) . '</div>' .
                           '<small class="text-muted">+ $' . number_format($row->charge, 2) . ' fee</small>' .
                           '</div>';
                })
                ->addColumn('total_amount', function ($row) {
                    return '<div class="fw-bold text-primary">$' . number_format($row->amount + $row->charge, 2) . '</div>';
                })
                ->addColumn('date', function ($row) {
                    return '<div>' .
                           '<div>' . $row->created_at->format('M d, Y') . '</div>' .
                           '<small class="text-muted">' . $row->created_at->format('h:i A') . '</small>' .
                           '</div>';
                })
                ->addColumn('actions', function ($row) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('admin.deposits.show', $row->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    
                    if ($row->status == 2) {
                        $actions .= '<button class="btn btn-sm btn-success ms-1" onclick="approveDeposit(' . $row->id . ')"><i class="fas fa-check"></i></button>';
                        $actions .= '<button class="btn btn-sm btn-danger ms-1" onclick="rejectDeposit(' . $row->id . ')"><i class="fas fa-times"></i></button>';
                    }
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['user','gateway', 'amount', 'total_amount', 'date', 'actions'])
                ->make(true);
        }

        return view('admin.deposits.pending', compact('pageTitle', 'stats'));
    }

    /**
     * Display approved deposits
     */
    public function approved(Request $request)
    {
        $pageTitle = 'Approved Deposits';
        
        if ($request->ajax()) {
            $deposits = Deposit::with(['user', 'gateway'])
                ->where('status', 1)
                ->when($request->search, function($query) use ($request) {
                    return $query->whereHas('user', function($q) use ($request) {
                        $q->where('username', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%');
                    });
                })
                ->latest();

            return DataTables::of($deposits)
                ->addColumn('user', function ($row) {
                    $user = $row->user;
                    if (!$user) return 'N/A';
                    
                    return '<div class="d-flex align-items-center">' .
                           '<img src="' . $user->avatar_url . '" class="rounded-circle me-2" style="width: 32px; height: 32px;">' .
                           '<div>' .
                           '<div class="fw-medium">' . $user->username . '</div>' .
                           '<small class="text-muted">' . $user->email . '</small>' .
                           '</div></div>';
                })
                ->addColumn('gateway', function ($row) {
                    return $row->gateway ? $row->gateway->name : 'N/A';
                })
                ->addColumn('amount', function ($row) {
                    return '<div class="text-end">' .
                           '<div class="fw-bold text-success">$' . number_format($row->amount, 2) . '</div>' .
                           '<small class="text-muted">+ $' . number_format($row->charge, 2) . ' fee</small>' .
                           '</div>';
                })
                ->addColumn('approved_at', function ($row) {
                    return '<div>' .
                           '<div>' . $row->updated_at->format('M d, Y') . '</div>' .
                           '<small class="text-muted">' . $row->updated_at->format('h:i A') . '</small>' .
                           '</div>';
                })
                ->addColumn('actions', function ($row) {
                    return '<a href="' . route('admin.deposits.show', $row->id) . '" class="btn btn-sm btn-info" title="View Details"><i class="fas fa-eye"></i></a>';
                })
                ->rawColumns(['user', 'amount', 'approved_at', 'actions'])
                ->make(true);
        }

        $stats = [
            'approved_count' => Deposit::where('status', 1)->count(),
            'approved_amount' => Deposit::where('status', 1)->sum('amount'),
            'today_approved' => Deposit::where('status', 1)->whereDate('updated_at', today())->count(),
            'today_amount' => Deposit::where('status', 1)->whereDate('updated_at', today())->sum('amount'),
        ];

        return view('admin.deposits.approved', compact('pageTitle', 'stats'));
    }

    /**
     * Display rejected deposits
     */
    public function rejected(Request $request)
    {
        $pageTitle = 'Rejected Deposits';
        
        if ($request->ajax()) {
            $deposits = Deposit::with(['user', 'gateway'])
                ->where('status', 3)
                ->latest();

            return DataTables::of($deposits)
                ->addColumn('user', function ($row) {
                    $user = $row->user;
                    if (!$user) return 'N/A';
                    
                    return '<div class="d-flex align-items-center">' .
                           '<img src="' . $user->avatar_url . '" class="rounded-circle me-2" style="width: 32px; height: 32px;">' .
                           '<div>' .
                           '<div class="fw-medium">' . $user->username . '</div>' .
                           '<small class="text-muted">' . $user->email . '</small>' .
                           '</div></div>';
                })
                ->addColumn('gateway', function ($row) {
                    return $row->gateway ? $row->gateway->name : 'N/A';
                })
                ->addColumn('amount', function ($row) {
                    return '<div class="text-end">' .
                           '<div class="fw-bold text-danger">$' . number_format($row->amount, 2) . '</div>' .
                           '<small class="text-muted">+ $' . number_format($row->charge, 2) . ' fee</small>' .
                           '</div>';
                })
                ->addColumn('rejected_at', function ($row) {
                    return '<div>' .
                           '<div>' . $row->updated_at->format('M d, Y') . '</div>' .
                           '<small class="text-muted">' . $row->updated_at->format('h:i A') . '</small>' .
                           '</div>';
                })
                ->addColumn('actions', function ($row) {
                    return '<a href="' . route('admin.deposits.show', $row->id) . '" class="btn btn-sm btn-info" title="View Details"><i class="fas fa-eye"></i></a>';
                })
                ->rawColumns(['user', 'amount', 'rejected_at', 'actions'])
                ->make(true);
            }

        $stats = [
            'rejected_count' => Deposit::where('status', 3)->count(),
            'rejected_amount' => Deposit::where('status', 3)->sum('amount'),
            'today_rejected' => Deposit::where('status', 3)->whereDate('updated_at', today())->count(),
            'today_amount' => Deposit::where('status', 3)->whereDate('updated_at', today())->sum('amount'),
        ];

        return view('admin.deposits.rejected', compact('pageTitle', 'stats'));
    }

    /**
     * Show deposit details
     */
    public function show($id)
    {
        $deposit = Deposit::with(['user', 'gateway'])->findOrFail($id);
        
        // Convert JSON to array with proper error handling
        $depositDetails = [];
        if (!empty($deposit->detail)) {
            $depositDetails = json_decode($deposit->detail, true);
            
            // Check if JSON decoding was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Failed to decode deposit details JSON', [
                    'deposit_id' => $deposit->id,
                    'json_error' => json_last_error_msg(),
                    'raw_detail' => $deposit->detail
                ]);
                $depositDetails = [];
            }
        }
        
        $pageTitle = 'Deposit Details';
        
        return view('admin.deposits.show', compact('deposit', 'depositDetails', 'pageTitle'));
    }

    /**
     * Approve deposit
     */
    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $deposit = Deposit::findOrFail($id);
            if ($deposit->status != 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending deposits can be approved.'
                ]);
            }
            $user = $deposit->user;
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ]);
            }
            $deposit->status = 1; // Approved
            $deposit->save();
            // Credit user balance
            $user->deposit_wallet += $deposit->amount;
            $user->save();
            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $deposit->amount,
                'post_balance' => $user->balance,
                'charge' => $deposit->charge,
                'trx_type' => '+',
                'details' => 'Deposit via ' . ($deposit->gateway->name ?? 'Gateway'),
                'trx' => $deposit->trx,
                'remark' => 'deposit',
            ]);
            
            DB::commit();
            // Log the approval
            Log::info('Deposit approved', [
                'deposit_id' => $deposit->id,
                'user_id' => $user->id,
                'amount' => $deposit->amount,
                'admin_id' => auth()->guard('admin')->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Deposit approved successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Deposit approval failed', [
                'deposit_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve deposit. Please try again.'
            ]);
        }
    }

    /**
     * Reject deposit
     */
    public function reject(Request $request, $id)
    {
        try {
            $deposit = Deposit::findOrFail($id);
            
            if ($deposit->status != 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending deposits can be rejected.'
                ]);
            }
            $deposit->status = 3; // Rejected
            $deposit->admin_feedback = $request->reason ?? 'Rejected by admin';
            $deposit->save();

            Log::info('Deposit rejected', [
                'deposit_id' => $deposit->id,
                'reason' => $request->reason,
                'admin_id' => auth()->guard('admin')->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Deposit rejected successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Deposit rejection failed', [
                'deposit_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject deposit. Please try again.'
            ]);
        }
    }

    /**
     * Bulk action for deposits
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'deposits' => 'required|array',
            'deposits.*' => 'exists:deposits,id'
        ]);

        try {
            DB::beginTransaction();
            
            $deposits = Deposit::whereIn('id', $request->deposits)
                             ->where('status', 2)
                             ->get();

            $processed = 0;
            foreach ($deposits as $deposit) {
                if ($request->action === 'approve') {
                    $deposit->update(['status' => 1]);
                    
                    // Credit user balance
                    $user = $deposit->user;
                    if ($user) {
                        $user->balance += $deposit->amount;
                        $user->save();

                        // Create transaction record
                        Transaction::create([
                            'user_id' => $user->id,
                            'amount' => $deposit->amount,
                            'post_balance' => $user->balance,
                            'charge' => $deposit->charge,
                            'trx_type' => '+',
                            'details' => 'Deposit via ' . ($deposit->gateway->name ?? 'Gateway'),
                            'trx' => $deposit->trx,
                            'remark' => 'deposit',
                        ]);
                    }
                } else {
                    $deposit->update([
                        'status' => 3,
                        'admin_feedback' => 'Bulk rejection by admin'
                    ]);
                }
                $processed++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully {$request->action}d {$processed} deposits."
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed. Please try again.'
            ]);
        }
    }

    // Withdrawal Methods (similar structure)
    /**
     * Show all withdrawals with filtering
     */
    public function withdrawals(Request $request)
    {
        $pageTitle = 'All Withdrawals';
        
        $withdrawals = Withdrawal::with(['user'])
            ->when($request->search, function($query) use ($request) {
                return $query->where('trx', 'like', '%' . $request->search . '%')
                           ->orWhereHas('user', function($q) use ($request) {
                               $q->where('username', 'like', '%' . $request->search . '%')
                                 ->orWhere('email', 'like', '%' . $request->search . '%');
                           });
            })
            ->when($request->status !== null, function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->withdraw_type, function($query) use ($request) {
                return $query->where('withdraw_type', $request->withdraw_type);
            })
            ->when($request->from_date, function($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->from_date);
            })
            ->when($request->to_date, function($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->to_date);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Withdrawal::count(),
            'pending' => Withdrawal::where('status', 2)->count(), // Pending status
            'approved' => Withdrawal::where('status', 1)->count(), // Approved status
            'rejected' => Withdrawal::where('status', 3)->count(), // Rejected status
            'deposit_withdrawals' => Withdrawal::where('withdraw_type', 'deposit')->count(),
            'wallet_withdrawals' => Withdrawal::where('withdraw_type', 'wallet')->count(),
        ];

        return view('admin.withdrawals.index', compact('pageTitle', 'withdrawals', 'stats'));
    }

    /**
     * Show pending withdrawals
     */
    public function pendingWithdrawals(Request $request)
    {
        $pageTitle = 'Pending Withdrawals';
        
        $withdrawals = Withdrawal::with(['user'])
            ->where('status', 2) // Pending status
            ->when($request->search, function($query) use ($request) {
                return $query->where('trx', 'like', '%' . $request->search . '%')
                           ->orWhereHas('user', function($q) use ($request) {
                               $q->where('username', 'like', '%' . $request->search . '%')
                                 ->orWhere('email', 'like', '%' . $request->search . '%');
                           });
            })
            ->when($request->withdraw_type, function($query) use ($request) {
                return $query->where('withdraw_type', $request->withdraw_type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.withdrawals.pending', compact('pageTitle', 'withdrawals'));
    }

    /**
     * Show approved withdrawals
     */
    public function approvedWithdrawals(Request $request)
    {
        $pageTitle = 'Approved Withdrawals';
        
        $withdrawals = Withdrawal::with(['user'])
            ->where('status', 1)
            ->when($request->search, function($query) use ($request) {
                return $query->where('trx', 'like', '%' . $request->search . '%')
                           ->orWhereHas('user', function($q) use ($request) {
                               $q->where('username', 'like', '%' . $request->search . '%')
                                 ->orWhere('email', 'like', '%' . $request->search . '%');
                           });
            })
            ->when($request->withdraw_type, function($query) use ($request) {
                return $query->where('withdraw_type', $request->withdraw_type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.withdrawals.approved', compact('pageTitle', 'withdrawals'));
    }

    /**
     * Show rejected withdrawals
     */
    public function rejectedWithdrawals(Request $request)
    {
        $pageTitle = 'Rejected Withdrawals';
        
        $withdrawals = Withdrawal::with(['user'])
            ->where('status', 3) // Rejected status
            ->when($request->search, function($query) use ($request) {
                return $query->where('trx', 'like', '%' . $request->search . '%')
                           ->orWhereHas('user', function($q) use ($request) {
                               $q->where('username', 'like', '%' . $request->search . '%')
                                 ->orWhere('email', 'like', '%' . $request->search . '%');
                           });
            })
            ->when($request->withdraw_type, function($query) use ($request) {
                return $query->where('withdraw_type', $request->withdraw_type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.withdrawals.rejected', compact('pageTitle', 'withdrawals'));
    }

    /**
     * Show single withdrawal details
     */
    public function showWithdrawal($id)
    {
        $withdrawal = Withdrawal::with(['user'])->findOrFail($id);
        $pageTitle = 'Withdrawal Details';
        
        return view('admin.withdrawals.show', compact('pageTitle', 'withdrawal'));
    }

    /**
     * Approve withdrawal
     */
    public function approveWithdrawal(Request $request, $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        
        if ($withdrawal->status != 2) { // Check for pending status
            return back()->with('error', 'This withdrawal has already been processed.');
        }

        try {
            DB::beginTransaction();

            // Update withdrawal status
            $withdrawal->update([
                'status' => 1,
                'admin_feedback' => $request->admin_feedback ?? 'Withdrawal approved',
                'processed_at' => now()
            ]);

            // For deposit withdrawals, the deposit is already set to withdrawn status
            // For wallet withdrawals, the amount is already deducted, so no need to do anything

            // Update user's transaction record if exists
            $transaction = Transaction::where('trx', $withdrawal->trx)->first();
            if ($transaction) {
                $transaction->update([
                    'post_balance' => $withdrawal->user->deposit_wallet + $withdrawal->user->interest_wallet
                ]);
            }

            DB::commit();

            // Send notification to user (you can implement this)
            // notify($withdrawal->user, 'WITHDRAWAL_APPROVED', [
            //     'amount' => $withdrawal->final_amount,
            //     'trx' => $withdrawal->trx
            // ]);

            return back()->with('success', 'Withdrawal approved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal approval error: ' . $e->getMessage());
            
            return back()->with('error', 'An error occurred while approving the withdrawal.');
        }
    }

    /**
     * Reject withdrawal
     */
    public function rejectWithdrawal(Request $request, $id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        
        if ($withdrawal->status != 2) { // Check for pending status
            return back()->with('error', 'This withdrawal has already been processed.');
        }

        $request->validate([
            'admin_feedback' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Update withdrawal status
            $withdrawal->update([
                'status' => 3, // Rejected status
                'admin_feedback' => $request->admin_feedback,
                'processed_at' => now()
            ]);

            // If it's a deposit withdrawal, restore the deposit status
            if ($withdrawal->withdraw_type == 'deposit') {
                // Find the user's deposit that was withdrawn and restore it
                $user = $withdrawal->user;
                $deposit = $user->invests()->where('status', 2)->first(); // status 2 = withdrawn
                if ($deposit) {
                    $deposit->update(['status' => 1]); // Restore to active
                }
            }

            // If it's a wallet withdrawal, restore the user's wallet balance
            if ($withdrawal->withdraw_type == 'wallet') {
                $user = $withdrawal->user;
                $info = json_decode($withdrawal->withdraw_information);
                
                if (isset($info->wallet_breakdown)) {
                    // Get the original wallet balances at the time of withdrawal
                    $originalDepositWallet = $info->wallet_breakdown->deposit_wallet ?? 0;
                    $originalInterestWallet = $info->wallet_breakdown->interest_wallet ?? 0;
                    $withdrawalAmount = $withdrawal->amount;
                    
                    // Calculate how much was deducted from each wallet using the same logic as withdrawal
                    $remainingAmount = $withdrawalAmount;
                    $depositDeduction = 0;
                    $interestDeduction = 0;
                    
                    // First calculate deduction from deposit wallet
                    if ($remainingAmount > 0 && $originalDepositWallet > 0) {
                        $depositDeduction = min($remainingAmount, $originalDepositWallet);
                        $remainingAmount -= $depositDeduction;
                    }
                    
                    // Then calculate deduction from interest wallet
                    if ($remainingAmount > 0 && $originalInterestWallet > 0) {
                        $interestDeduction = min($remainingAmount, $originalInterestWallet);
                    }
                    
                    // Restore the exact amounts that were deducted
                    $user->deposit_wallet += $depositDeduction;
                    $user->interest_wallet += $interestDeduction;
                    $user->save();
                    
                    // Log the restoration for debugging
                    Log::info("Withdrawal restoration - TRX: {$withdrawal->trx}, Deposit: +{$depositDeduction}, Interest: +{$interestDeduction}");
                } else {
                    // Fallback: restore full amount to deposit wallet if wallet_breakdown is missing
                    $user->deposit_wallet += $withdrawal->amount;
                    $user->save();
                }
            }

            // Update transaction record
            $transaction = Transaction::where('trx', $withdrawal->trx)->first();
            if ($transaction) {
                $transaction->delete(); // Remove the transaction since withdrawal was rejected
            }

            DB::commit();

            // Send notification to user
            // notify($withdrawal->user, 'WITHDRAWAL_REJECTED', [
            //     'amount' => $withdrawal->final_amount,
            //     'trx' => $withdrawal->trx,
            //     'reason' => $request->admin_feedback
            // ]);

            return back()->with('success', 'Withdrawal rejected and funds restored.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal rejection error: ' . $e->getMessage());
            
            return back()->with('error', 'An error occurred while rejecting the withdrawal.');
        }
    }

    /**
     * Bulk action for withdrawals
     */
    public function withdrawalBulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'withdrawals' => 'required|array',
            'withdrawals.*' => 'exists:withdrawals,id',
            'admin_feedback' => 'required_if:action,reject|string|max:500'
        ]);

        $withdrawals = Withdrawal::whereIn('id', $request->withdrawals)
                                ->where('status', 2) // Pending status
                                ->get();

        if ($withdrawals->isEmpty()) {
            return back()->with('error', 'No valid pending withdrawals selected.');
        }

        try {
            DB::beginTransaction();

            $processed = 0;
            foreach ($withdrawals as $withdrawal) {
                if ($request->action == 'approve') {
                    $withdrawal->update([
                        'status' => 1,
                        'admin_feedback' => $request->admin_feedback ?? 'Bulk approved',
                        'processed_at' => now()
                    ]);
                } else {
                    $withdrawal->update([
                        'status' => 3, // Rejected status
                        'admin_feedback' => $request->admin_feedback,
                        'processed_at' => now()
                    ]);

                    // Restore funds for rejected withdrawals
                    if ($withdrawal->withdraw_type == 'deposit') {
                        $deposit = $withdrawal->user->invests()->where('status', 2)->first();
                        if ($deposit) {
                            $deposit->update(['status' => 1]);
                        }
                    } elseif ($withdrawal->withdraw_type == 'wallet') {
                        $user = $withdrawal->user;
                        $info = json_decode($withdrawal->withdraw_information);
                        
                        if (isset($info->wallet_breakdown)) {
                            // Get the original wallet balances at the time of withdrawal
                            $originalDepositWallet = $info->wallet_breakdown->deposit_wallet ?? 0;
                            $originalInterestWallet = $info->wallet_breakdown->interest_wallet ?? 0;
                            $withdrawalAmount = $withdrawal->amount;
                            
                            // Calculate how much was deducted from each wallet using the same logic as withdrawal
                            $remainingAmount = $withdrawalAmount;
                            $depositDeduction = 0;
                            $interestDeduction = 0;
                            
                            // First calculate deduction from deposit wallet
                            if ($remainingAmount > 0 && $originalDepositWallet > 0) {
                                $depositDeduction = min($remainingAmount, $originalDepositWallet);
                                $remainingAmount -= $depositDeduction;
                            }
                            
                            // Then calculate deduction from interest wallet
                            if ($remainingAmount > 0 && $originalInterestWallet > 0) {
                                $interestDeduction = min($remainingAmount, $originalInterestWallet);
                            }
                            
                            // Restore the exact amounts that were deducted
                            $user->deposit_wallet += $depositDeduction;
                            $user->interest_wallet += $interestDeduction;
                            $user->save();
                        } else {
                            // Fallback: restore full amount to deposit wallet if wallet_breakdown is missing
                            $user->deposit_wallet += $withdrawal->amount;
                            $user->save();
                        }
                    }
                }
                $processed++;
            }

            DB::commit();

            return back()->with('success', "Successfully {$request->action}d {$processed} withdrawals.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal bulk action error: ' . $e->getMessage());
            
            return back()->with('error', 'An error occurred during bulk action.');
        }
    }

    /**
     * Export deposits to CSV
     */
    public function export(Request $request)
    {
        $deposits = Deposit::with(['user', 'gateway'])
            ->when($request->status, function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->from_date, function($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->from_date);
            })
            ->when($request->to_date, function($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->to_date);
            })
            ->get();

        $filename = 'deposits_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($deposits) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'User',
                'Email',
                'Gateway',
                'Amount',
                'Charge',
                'Total Amount',
                'Status',
                'Transaction ID',
                'Method Code',
                'Created Date',
                'Updated Date'
            ]);

            // Add data rows
            foreach ($deposits as $deposit) {
                fputcsv($file, [
                    $deposit->id,
                    $deposit->user ? $deposit->user->username : 'N/A',
                    $deposit->user ? $deposit->user->email : 'N/A',
                    $deposit->gateway ? $deposit->gateway->name : 'N/A',
                    $deposit->amount,
                    $deposit->charge,
                    $deposit->amount + $deposit->charge,
                    $deposit->status == 1 ? 'Approved' : ($deposit->status == 2 ? 'Rejected' : 'Pending'),
                    $deposit->trx ?? 'N/A',
                    $deposit->method_code ?? 'N/A',
                    $deposit->created_at->format('Y-m-d H:i:s'),
                    $deposit->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export withdrawals to CSV
     */
    public function exportWithdrawals(Request $request)
    {
        try {
            Log::info('Export withdrawals started', ['request' => $request->all()]);
            
            $withdrawals = Withdrawal::with(['user', 'withdrawMethod'])
                ->when($request->status !== null && $request->status !== '', function($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->when($request->withdraw_type, function($query) use ($request) {
                    return $query->where('withdraw_type', $request->withdraw_type);
                })
                ->when($request->from_date, function($query) use ($request) {
                    return $query->whereDate('created_at', '>=', $request->from_date);
                })
                ->when($request->to_date, function($query) use ($request) {
                    return $query->whereDate('created_at', '<=', $request->to_date);
                })
                ->get();

            Log::info('Found withdrawals', ['count' => $withdrawals->count()]);

            $filename = 'withdrawals_' . now()->format('Y_m_d_H_i_s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ];

            $callback = function() use ($withdrawals) {
                $file = fopen('php://output', 'w');
                
                Log::info('Starting CSV generation');
                
                // Write BOM for UTF-8
                fwrite($file, "\xEF\xBB\xBF");
                
                // Enhanced CSV headers with payment details
                fputcsv($file, [
                    'ID', 'User', 'Email', 'Transaction ID', 'Type', 'Amount', 'Charge', 
                    'Final Amount', 'Method', 'Currency', 'Wallet Address', 'Account Details', 
                    'Payment Information', 'Status', 'Admin Note', 'Processing Time', 'Date', 'Processed Date'
                ]);

                $processedCount = 0;
                foreach ($withdrawals as $withdrawal) {
                    try {
                        // Simple extraction for wallet address and payment info
                        $walletAddress = '';
                        $accountDetails = '';
                        $paymentInfo = '';
                        
                        if ($withdrawal->withdraw_information) {
                            $withdrawInfo = $withdrawal->withdraw_information;
                            
                            // If it's a string, decode it
                            if (is_string($withdrawInfo)) {
                                $withdrawInfo = json_decode($withdrawInfo, true) ?? [];
                            }
                            
                            // If it's an object, convert to array
                            if (is_object($withdrawInfo)) {
                                $withdrawInfo = (array) $withdrawInfo;
                            }
                            
                            if (is_array($withdrawInfo)) {
                                // Look for wallet address in common fields
                                $walletAddress = $withdrawInfo['wallet_address'] ?? 
                                               $withdrawInfo['address'] ?? 
                                               $withdrawInfo['details'] ?? '';
                                
                                // Get method from the info
                                $methodFromInfo = $withdrawInfo['method'] ?? '';
                                
                                // Create payment info string
                                $paymentInfo = implode('; ', array_filter([
                                    $methodFromInfo ? "Method: {$methodFromInfo}" : '',
                                    $walletAddress ? "Address: {$walletAddress}" : ''
                                ]));
                            }
                        }

                        $status = $withdrawal->status == 1 ? 'Approved' : ($withdrawal->status == 3 ? 'Rejected' : 'Pending');
                        
                        fputcsv($file, [
                            $withdrawal->id,
                            $withdrawal->user ? $withdrawal->user->username : '',
                            $withdrawal->user ? $withdrawal->user->email : '',
                            $withdrawal->trx,
                            ucfirst($withdrawal->withdraw_type ?? 'deposit'),
                            number_format($withdrawal->amount, 2),
                            number_format($withdrawal->charge, 2),
                            number_format($withdrawal->final_amount, 2),
                            $withdrawal->withdrawMethod ? $withdrawal->withdrawMethod->name : 'N/A',
                            $withdrawal->withdrawMethod ? $withdrawal->withdrawMethod->currency : 'USD',
                            $walletAddress,
                            $accountDetails,
                            $paymentInfo,
                            $status,
                            $withdrawal->admin_feedback ?? '',
                            $withdrawal->withdrawMethod ? $withdrawal->withdrawMethod->processing_time : 'N/A',
                            $withdrawal->created_at->format('Y-m-d H:i:s'),
                            $withdrawal->processed_at ? $withdrawal->processed_at->format('Y-m-d H:i:s') : ''
                        ]);
                        
                        $processedCount++;
                    } catch (\Exception $e) {
                        Log::error('Error processing withdrawal row', [
                            'withdrawal_id' => $withdrawal->id ?? 'unknown',
                            'error' => $e->getMessage()
                        ]);
                        continue;
                    }
                }

                Log::info('CSV generation completed', ['processed' => $processedCount]);
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Withdrawal export error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Show export withdrawals page
     */
    public function showWithdrawalsExport()
    {
        $pageTitle = 'Export Withdrawals';
        return view('admin.withdrawals.export', compact('pageTitle'));
    }
}
