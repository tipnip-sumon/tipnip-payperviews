<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\Deposit;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PrevailExcel\Nowpayments\Facades\Nowpayments;
use Yajra\DataTables\DataTables;
use GuzzleHttp\Client;

class PaymentController extends Controller
{

    /**
     * Collect Order data and create Payment
     * @return Url
     */
    public function createCryptoPayment(Request $request)
    {
        
        try{
            // Validate that price_amount is an integer
            $validator = Validator::make($request->all(), [
                'pay_currency' => 'required|string',
                'price_amount' => 'required|integer|min:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'remark' => 'validation_error',
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ])->setStatusCode(422, 'Validation Failed');
            }
            
            $req_amount = request()->price_amount;
            $data = [
                "price_amount" => $req_amount,
                "price_currency" => request()->price_currency ?? 'USD',
                "pay_currency" => request()->pay_currency ?? 'USDTBSC',
                'payout_currency' => request()->payout_currency ?? 'USDTBSC',
                "order_id" => request()->order_id ?? uniqid(),
                "customer_email" => request()->customer_email ?? Auth::user()->email
            ];

            //$cancelUrl = request()->currentUrl;
            //$cancelUrl = $cancelUrl . '/pay/delete/' . Auth::id();
            
            $user = Auth::user();
            $deposit = Deposit::Pending()->where('user_id',$user->id)->first();
            // $deposit = Deposit::where('user_id',$user->id)->where('status','!=',1)->first();
            if ($deposit) {
                return response()->json([
                    'remark' => 'error',
                    'status' => 'error',
                    'message' => $deposit->admin_feedback ?? 'Payment already exists. Please wait for it to be processed.',
                ])->setStatusCode(400, 'Payment Already Exists');
            }
            $paymentDetails = Nowpayments::createPayment($data);

            $deposit = new Deposit();
            $deposit->user_id = Auth::id();
            $deposit->plan_id = 1; // Assuming 1 is for crypto, you can change this as needed
            $deposit->method_code = 1; // Assuming 1 is for crypto, you can change this as needed
            $deposit->payment_id = $paymentDetails['payment_id'] ?? null; // Assuming you get an ID from the payment details
            $deposit->amount = $req_amount; // Assuming this is the amount to be deposited
            $deposit->method_currency = $paymentDetails['pay_currency'];
            $deposit->rate = 1; // Assuming rate is 1, you can change this as needed
            $deposit->final_amo = $paymentDetails['pay_amount'];
            $deposit->detail = json_encode($paymentDetails);
            $deposit->customer_email = $paymentDetails['customer_email'] ?? Auth::user()->email;
            $deposit->trx = $paymentDetails['order_id'] ?? getTrx();
            $deposit->status = 2; // Assuming 2 is for pending, you can change this as needed
            $deposit->btc_wallet = $paymentDetails['pay_address'] ?? null;
            $deposit->created_at = $paymentDetails['created_at'] ?? now();
            $deposit->save();
            $data = [
                'price_amount' => $req_amount,
                'price_currency' => request()->price_currency ?? 'usd',
                'pay_currency' => request()->pay_currency ?? 'usdtbsc',
                "success_url" => request()->success_url,
                "cancel_url" => request()->cancel_url ?? url('/pay/delete/'.Auth::id()),
                'order_id' => $paymentDetails['order_id'] ?? uniqid(),
                'order_description' => 'Payment for deposit',
                "is_fee_paid_by_user" => false
            ];
            $paymentDetails = Nowpayments::createInvoice($data);

            $deposit = Deposit::where('trx', $paymentDetails['order_id'])->first();
            $deposit->admin_feedback = $paymentDetails['invoice_url'];
            $deposit->updated_at = $paymentDetails['updated_at'] ?? now();
            $deposit->save();

            return response()->json([
                'remark' => 'payment_data',
                'status' => 'success',
                'data' => $paymentDetails,
                'message' => 'Payment created successfully.',
                'cancel_url' => $paymentDetails['cancel_url'] ?? route('home'),
            ])->setStatusCode(200, 'Payment Created Successfully' );
            
        }catch(\Exception $e) {
            return response()->json([
                'remark' => 'error',
                'status' => 'error',
                'message' => $e->getMessage(),
            ])->setStatusCode(500, 'Error Creating Payment');
        }
    }
    public function cancelCryptoPayment($user_id)
    {
        if (Auth::user()->id != $user_id) {
            return response()->json([
                'remark' => 'error',
                'status' => 'error',
                'message' => 'Unauthorized action.'.$user_id,
            ]);
        }
        // Check if the user has any pending deposits
        $pendingDeposit = Deposit::Pending()->where('user_id', $user_id)->first();
        if (!$pendingDeposit) {
            return response()->json([
                'remark' => 'error',
                'status' => 'error',
                'message' => 'No pending payment found to cancel.',
            ])->setStatusCode(404, 'No Pending Payment Found');
        }else {
            $minutes  = Carbon::now()->subMinutes(5); // Set the time limit to 20 minutes
            $paymentCancel = Deposit::Pending()->where('user_id', $user_id)->where('created_at', '<=',$minutes)->delete();
            if ($paymentCancel) {
            return response()->json([
                    'remark' => 'cancel',
                    'status' => 'success',
                    'message' => 'Payment cancelled successfully.',
                ])->setStatusCode(200, 'Payment Cancelled Successfully');
            }else {
                $deleteTime = $pendingDeposit->created_at->diffInMinutes(Carbon::now());
                $res = $pendingDeposit->created_at->diffForHumans(Carbon::now(), true);
                if ($deleteTime < 5) {
                    return response()->json([
                        'remark' => 'error',
                        'status' => 'error',
                        'message' => 'Payment can only be cancelled after 5 minutes.'.$res.' running time.',
                    ])->setStatusCode(400, 'Payment Cancellation Not Allowed Yet');
                }
            }
        }
        
       
    }
    public function paymentHistory(Request $request)
    {
        $pageTitle = "Payment History";
        $user = Auth::user();
        
        // Handle export requests
        if ($request->has('export')) {
            return $this->exportPaymentHistory($request);
        }
        
        // Handle payment details request
        if ($request->has('get_details') && $request->payment_id) {
            return $this->getPaymentDetails($request->payment_id);
        }
        
        // Handle statistics request
        if ($request->has('get_stats')) {
            $statistics = $this->getPaymentStatistics($request);
            return response()->json(['statistics' => $statistics]);
        }
        
        if ($request->ajax()) {
            $query = Deposit::with('gateway')->where('user_id', $user->id);
            
            // Apply filters
            if ($request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            if ($request->status !== null && $request->status !== '') {
                $query->where('status', $request->status);
            }
            
            if ($request->currency) {
                $query->where('method_currency', $request->currency);
            }
            
            if ($request->min_amount) {
                $query->where('amount', '>=', $request->min_amount);
            }
            
            if ($request->max_amount) {
                $query->where('amount', '<=', $request->max_amount);
            }
            
            if ($request->search_query) {
                $searchTerm = $request->search_query;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('payment_id', 'like', "%{$searchTerm}%")
                      ->orWhere('trx', 'like', "%{$searchTerm}%")
                      ->orWhere('method_currency', 'like', "%{$searchTerm}%");
                });
            }
            
            return DataTables::of($query)
                ->addColumn('created_at', function ($row) {
                    return '<span data-bs-toggle="tooltip" title="' . $row->created_at . '">' . 
                           showDateTime($row->created_at, 'd M Y') . '</span>';
                })
                ->addColumn('payment_id', function ($row) {
                    $paymentId = $row->payment_id ?: 'N/A';
                    return '<span class="fw-bold text-primary">' . $paymentId . '</span>';
                })
                ->addColumn('gateway', function ($row) {
                    return '<span class="badge bg-info">' . ($row->gateway->name ?? 'Crypto') . '</span>';
                })
                ->addColumn('method_currency', function ($row) {
                    return '<span class="badge bg-secondary">' . strtoupper($row->method_currency) . '</span>';
                })
                ->addColumn('amount', function ($row) {
                    return '<span class="fw-bold text-success">$' . showAmount($row->amount) . '</span>';
                })
                ->addColumn('charge', function ($row) {
                    $charge = $row->charge ?? 0;
                    return $charge > 0 ? '<span class="text-warning">$' . showAmount($charge) . '</span>' : 'N/A';
                })
                ->addColumn('total_amount', function ($row) {
                    $charge = $row->charge ?? 0;
                    $total = $row->amount + $charge;
                    return '<span class="fw-bold text-primary">$' . showAmount($total) . '</span>';
                })
                ->addColumn('status', function ($row) {
                    $statusMap = [
                        0 => '<span class="badge bg-warning text-dark">Pending</span>',
                        1 => '<span class="badge bg-success">Successful</span>',
                        2 => '<span class="badge bg-info">Processing</span>',
                        3 => '<span class="badge bg-danger">Cancelled</span>'
                    ];
                    return $statusMap[$row->status] ?? '<span class="badge bg-secondary">Unknown</span>';
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="btn-group" role="group">';
                    
                    // Sanitize payment_id for JavaScript
                    $paymentId = htmlspecialchars($row->payment_id ?: '', ENT_QUOTES, 'UTF-8');
                    
                    // View Details Button
                    $actions .= '<button class="btn btn-sm btn-outline-primary" onclick="viewPaymentDetails(\'' . $paymentId . '\')" data-bs-toggle="tooltip" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>';
                    
                    // Edit Button (only for pending/processing payments)
                    if (in_array($row->status, [0, 2])) {
                        $actions .= '<button class="btn btn-sm btn-outline-warning ms-1" onclick="editPayment(' . (int)$row->id . ')" data-bs-toggle="tooltip" title="Edit Payment">
                                        <i class="fas fa-edit"></i>
                                    </button>';
                    }
                    
                    // Delete Button (only for pending payments)
                    if ($row->status == 0) {
                        $actions .= '<button class="btn btn-sm btn-outline-danger ms-1" onclick="deletePayment(' . (int)$row->id . ')" data-bs-toggle="tooltip" title="Delete Payment">
                                        <i class="fas fa-trash"></i>
                                    </button>';
                    }
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->addIndexColumn()
                ->rawColumns(['created_at', 'payment_id', 'gateway', 'method_currency', 'amount', 'charge', 'total_amount', 'status', 'action'])
                ->make(true);
        }
        
        // Get initial statistics and currencies for filters
        $statistics = $this->getPaymentStatistics($request);
        $currencies = Deposit::where('user_id', $user->id)
                            ->distinct()
                            ->pluck('method_currency')
                            ->filter()
                            ->sort()
                            ->values();
        
        return view('frontend.payment-history', compact('pageTitle', 'statistics', 'currencies'));
    }
    
    private function getPaymentStatistics(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Deposit::where('user_id', $user->id);
            
            // Apply same filters as main query
            if ($request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            if ($request->status !== null && $request->status !== '') {
                $query->where('status', $request->status);
            }
            
            if ($request->currency) {
                $query->where('method_currency', $request->currency);
            }
            
            if ($request->min_amount) {
                $query->where('amount', '>=', $request->min_amount);
            }
            
            if ($request->max_amount) {
                $query->where('amount', '<=', $request->max_amount);
            }
            
            if ($request->search_query) {
                $searchTerm = $request->search_query;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('payment_id', 'like', "%{$searchTerm}%")
                      ->orWhere('trx', 'like', "%{$searchTerm}%")
                      ->orWhere('method_currency', 'like', "%{$searchTerm}%");
                });
            }
            
            $baseQuery = clone $query;
            
            return [
                'total_payments' => $baseQuery->count(),
                'total_amount' => (float) $baseQuery->sum('amount'),
                'successful_payments' => $baseQuery->where('status', 1)->count(),
                'pending_payments' => $baseQuery->where('status', 0)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting payment statistics: ' . $e->getMessage());
            return [
                'total_payments' => 0,
                'total_amount' => 0,
                'successful_payments' => 0,
                'pending_payments' => 0,
            ];
        }
    }
    
    private function getPaymentDetails($paymentId)
    {
        $payment = Deposit::where('user_id', Auth::id())
                          ->where('payment_id', $paymentId)
                          ->first();
        
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
        
        $html = view('frontend.partials.payment-details', compact('payment'))->render();
        
        return response()->json(['html' => $html]);
    }
    
    private function exportPaymentHistory(Request $request)
    {
        $user = Auth::user();
        $query = Deposit::where('user_id', $user->id);
        
        // Apply filters (same as main query)
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        if ($request->currency) {
            $query->where('method_currency', $request->currency);
        }
        
        if ($request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }
        
        if ($request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }
        
        if ($request->search_query) {
            $searchTerm = $request->search_query;
            $query->where(function($q) use ($searchTerm) {
                $q->where('payment_id', 'like', "%{$searchTerm}%")
                  ->orWhere('trx', 'like', "%{$searchTerm}%")
                  ->orWhere('method_currency', 'like', "%{$searchTerm}%");
            });
        }
        
        $payments = $query->orderBy('created_at', 'desc')->get();
        
        if ($request->export === 'excel') {
            return $this->exportToExcel($payments);
        } elseif ($request->export === 'pdf') {
            return $this->exportToPdf($payments);
        }
        
        return response()->json(['error' => 'Invalid export format'], 400);
    }
    
    private function exportToExcel($payments)
    {
        $filename = 'payment-history-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Payment ID', 'Gateway', 'Currency', 'Amount', 'Charge', 'Total', 'Status']);
            
            foreach ($payments as $payment) {
                $statusMap = [
                    0 => 'Pending',
                    1 => 'Successful', 
                    2 => 'Processing',
                    3 => 'Cancelled'
                ];
                
                fputcsv($file, [
                    showDateTime($payment->created_at),
                    $payment->payment_id ?: 'N/A',
                    $payment->gateway->name ?? 'Crypto',
                    strtoupper($payment->method_currency),
                    '$' . showAmount($payment->amount),
                    ($payment->charge && $payment->charge > 0) ? '$' . showAmount($payment->charge) : 'N/A',
                    '$' . showAmount($payment->amount + ($payment->charge ?? 0)),
                    $statusMap[$payment->status] ?? 'Unknown'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function exportToPdf($payments)
    {
        // This would require a PDF library like dompdf
        // For now, return a simple text export
        $content = "Payment History Report\n";
        $content .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($payments as $payment) {
            $content .= "Date: " . showDateTime($payment->created_at) . "\n";
            $content .= "Payment ID: " . ($payment->payment_id ?: 'N/A') . "\n";
            $content .= "Currency: " . strtoupper($payment->method_currency) . "\n";
            $content .= "Amount: $" . showAmount($payment->amount) . "\n";
            $content .= "Status: " . ($payment->status == 1 ? 'Successful' : 'Pending') . "\n";
            $content .= "---\n";
        }
        
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="payment-history-' . date('Y-m-d') . '.txt"');
    }
    
    /**
     * Create a new payment record
     */
    public function createPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'method_currency' => 'required|string|max:10',
            'gateway_id' => 'nullable|integer',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            $deposit = new Deposit();
            $deposit->user_id = $user->id;
            $deposit->plan_id = $request->plan_id ?? 1;
            $deposit->method_code = $request->gateway_id ?? 1;
            $deposit->payment_id = 'PAY_' . strtoupper(uniqid());
            $deposit->amount = $request->amount;
            $deposit->method_currency = strtoupper($request->method_currency);
            $deposit->rate = 1;
            $deposit->final_amo = $request->amount;
            $deposit->charge = $request->charge ?? 0;
            $deposit->detail = json_encode([
                'description' => $request->description,
                'created_by' => 'user',
                'manual_entry' => true
            ]);
            $deposit->trx = getTrx();
            $deposit->status = 0; // Pending
            $deposit->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment record created successfully',
                'data' => $deposit
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment record
     */
    public function updatePayment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'method_currency' => 'required|string|max:10',
            'status' => 'required|in:0,1,2,3',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $deposit = Deposit::where('user_id', $user->id)->findOrFail($id);
            
            // Only allow editing of pending or processing payments
            if ($deposit->status == 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot edit completed payments'
                ], 403);
            }

            $deposit->amount = $request->amount;
            $deposit->method_currency = strtoupper($request->method_currency);
            $deposit->final_amo = $request->amount;
            $deposit->charge = $request->charge ?? $deposit->charge;
            $deposit->status = $request->status;
            
            $detail = json_decode($deposit->detail, true) ?: [];
            $detail['description'] = $request->description;
            $detail['updated_by'] = 'user';
            $detail['last_updated'] = now()->toDateTimeString();
            $deposit->detail = json_encode($detail);
            
            $deposit->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment updated successfully',
                'data' => $deposit
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete payment record
     */
    public function deletePayment($id)
    {
        try {
            $user = Auth::user();
            $deposit = Deposit::where('user_id', $user->id)->findOrFail($id);
            
            // Only allow deletion of pending payments
            if ($deposit->status == 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete completed payments'
                ], 403);
            }

            $deposit->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment for editing
     */
    public function getPayment($id)
    {
        try {
            $user = Auth::user();
            $deposit = Deposit::where('user_id', $user->id)->findOrFail($id);
            
            $detail = json_decode($deposit->detail, true) ?: [];
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $deposit->id,
                    'payment_id' => $deposit->payment_id,
                    'amount' => $deposit->amount,
                    'method_currency' => $deposit->method_currency,
                    'charge' => $deposit->charge,
                    'status' => $deposit->status,
                    'description' => $detail['description'] ?? '',
                    'created_at' => $deposit->created_at->format('Y-m-d H:i:s'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found'
            ], 404);
        }
    }
    // public function paymentDetails()
    // {
    //     $deposit = Deposit::Pending()->get();
    //     foreach ($deposit as $item) {
    //         $email = env('NOWPAYMENTS_EMAIL');
    //         $password = env('NOWPAYMENTS_PASSWORD');
    //         $jwt_token = Nowpayments::getJwt($email, $password);
    //         $result = Nowpayments::getListOfPayments($item->payment_id,$jwt_token);
    //         // Check if the result is valid and contains the required fields
    //         if(!$result || !isset($result['order_id']) || !isset($result['payment_status'])) {
    //             continue; // Skip if the result is not valid or does not contain required fields
    //         }
    //         if($item->trx != $result['order_id']) {
    //             continue; // Skip if the Order ID does not match
    //         }
    //         $depo = Deposit::where('trx', $result['order_id'])->first();
    //         if($result['payment_status'] == 'waiting') {
    //             $depo->status = 2; // Set status to pending if payment status is waiting
    //             $depo->btc_amo = $result['payment_status']; // Update the btc_amo field with the payment status
    //         } elseif ($result['payment_status'] == 'finished') {
    //             $depo->status = 1; // Set status to approved if payment status is finished
    //             $depo->final_amo = $result['outcome_amount']; // Update the btc_amo field with the outcome amount
    //             $depo->btc_amo = $result['payment_status']; // Update the btc_amo field with the payment status
    //         } elseif ($result['payment_status'] == 'partially_paid') {
    //             $depo->status = 1; // Set status to approved if outcome amount is sufficient
    //             $depo->final_amo = $result['outcome_amount']; // Update the final_amo field with the outcome amount
    //             $depo->btc_amo = $result['payment_status']; // Update the btc_amo field with the payment status
    //         }elseif ($result['payment_status'] == 'expired') {
    //             $depo->delete(); // Delete the deposit if payment status is not recognized
    //             continue; // Skip further processing for this deposit
    //         }
    //         $depo->save();
    //         // You can also add logic here to update the user's balance or notify them if needed
    //         if ($result['payment_status'] == 'finished') {
    //             $user = $depo->user;
    //             $user->deposit_wallet += $depo->amount;
    //             $user->save();
    //         }  elseif ($result['payment_status'] == 'partially_paid') {
    //             $user = $depo->user;
    //             $user->deposit_wallet += $result['outcome_amount'];
    //             $user->save();
    //         }
    //     }
    // }
}