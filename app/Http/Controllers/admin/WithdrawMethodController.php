<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WithdrawMethod;
use Illuminate\Support\Str;

class WithdrawMethodController extends Controller
{
    /**
     * Display a listing of withdrawal methods
     */
    public function index()
    {
        $pageTitle = 'Withdrawal Methods';
        $withdrawMethods = WithdrawMethod::ordered()->paginate(15);
        
        return view('admin.withdraw-methods.index', compact('pageTitle', 'withdrawMethods'));
    }

    /**
     * Show the form for creating a new withdrawal method
     */
    public function create()
    {
        $pageTitle = 'Add Withdrawal Method';
        return view('admin.withdraw-methods.create', compact('pageTitle'));
    }

    /**
     * Store a newly created withdrawal method
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'method_key' => 'required|string|max:191|unique:withdraw_methods,method_key',
            'status' => 'boolean',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'daily_limit' => 'required|numeric|gt:0',
            'fixed_charge' => 'nullable|numeric|min:0',
            'percent_charge' => 'nullable|numeric|min:0|max:100',
            'charge_type' => 'required|in:fixed,percent',
            'charge' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:191',
            'processing_time' => 'required|string|max:191',
            'currency' => 'nullable|string|max:40',
            'instructions' => 'nullable|string',
            'sort_order' => 'integer|min:0',
            'form_id' => 'nullable|integer',
            'min_limit' => 'nullable|numeric|min:0',
            'max_limit' => 'nullable|numeric',
            'rate' => 'nullable|numeric|min:0'
        ], [
            'method_key.unique' => 'This method key is already taken.',
            'max_amount.gt' => 'Maximum amount must be greater than minimum amount.',
            'daily_limit.gt' => 'Daily limit must be greater than 0.' 
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status');
        $data['sort_order'] = $request->sort_order ?? 0;
        $data['form_id'] = $request->form_id ?? 0;
        $data['fixed_charge'] = $request->fixed_charge ?? 0;
        $data['percent_charge'] = $request->percent_charge ?? 0;
        $data['min_limit'] = $request->min_amount ?? 0; // Sync legacy field
        $data['max_limit'] = $request->max_amount ?? 0; // Sync legacy field
        $data['rate'] = $request->rate ?? 0;

        WithdrawMethod::create($data);

        return redirect()->route('admin.withdraw-methods.index')
            ->with('success', 'Withdrawal method created successfully.');
    }

    /**
     * Display the specified withdrawal method
     */
    public function show(WithdrawMethod $withdrawMethod)
    {
        $pageTitle = 'Withdrawal Method Details';
        return view('admin.withdraw-methods.show', compact('pageTitle', 'withdrawMethod'));
    }

    /**
     * Show the form for editing the specified withdrawal method
     */
    public function edit(WithdrawMethod $withdrawMethod)
    {
        $pageTitle = 'Edit Withdrawal Method';
        return view('admin.withdraw-methods.edit', compact('pageTitle', 'withdrawMethod'));
    }

    /**
     * Update the specified withdrawal method
     */
    public function update(Request $request, WithdrawMethod $withdrawMethod)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'method_key' => 'required|string|max:191|unique:withdraw_methods,method_key,' . $withdrawMethod->id,
            'status' => 'boolean',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'daily_limit' => 'required|numeric|gt:0',
            'fixed_charge' => 'nullable|numeric|min:0',
            'percent_charge' => 'nullable|numeric|min:0|max:100',
            'charge_type' => 'required|in:fixed,percent',
            'charge' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:191',
            'processing_time' => 'required|string|max:191',
            'currency' => 'nullable|string|max:40',
            'instructions' => 'nullable|string',
            'sort_order' => 'integer|min:0',
            'form_id' => 'nullable|integer',
            'min_limit' => 'nullable|numeric|min:0',
            'max_limit' => 'nullable|numeric',
            'rate' => 'nullable|numeric|min:0'
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status');
        $data['sort_order'] = $request->sort_order ?? 0;
        $data['form_id'] = $request->form_id ?? 0;
        $data['fixed_charge'] = $request->fixed_charge ?? 0;
        $data['percent_charge'] = $request->percent_charge ?? 0;
        $data['min_limit'] = $request->min_amount ?? 0; // Sync legacy field
        $data['max_limit'] = $request->max_amount ?? 0; // Sync legacy field
        $data['rate'] = $request->rate ?? 0;

        $withdrawMethod->update($data);

        return redirect()->route('admin.withdraw-methods.index')
            ->with('success', 'Withdrawal method updated successfully.');
    }

    /**
     * Remove the specified withdrawal method
     */
    public function destroy(WithdrawMethod $withdrawMethod)
    {
        $withdrawMethod->delete();
        
        return redirect()->route('admin.withdraw-methods.index')
            ->with('success', 'Withdrawal method deleted successfully.');
    }

    /**
     * Toggle status of withdrawal method
     */
    public function toggleStatus(WithdrawMethod $withdrawMethod)
    {
        $withdrawMethod->update(['status' => !$withdrawMethod->status]);
        
        $status = $withdrawMethod->status ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Withdrawal method {$status} successfully.",
            'status' => $withdrawMethod->status
        ]);
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:withdraw_methods,id',
            'items.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->items as $item) {
            WithdrawMethod::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sort order updated successfully.'
        ]);
    }

    /**
     * Get withdrawal method statistics
     */
    public function statistics()
    {
        $stats = [
            'total_methods' => WithdrawMethod::count(),
            'active_methods' => WithdrawMethod::where('status', true)->count(),
            'inactive_methods' => WithdrawMethod::where('status', false)->count(),
            'fixed_charge_methods' => WithdrawMethod::where('charge_type', 'fixed')->count(),
            'percent_charge_methods' => WithdrawMethod::where('charge_type', 'percent')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Seed default withdrawal methods
     */
    public function seedDefaults()
    {
        $defaultMethods = [
            [
                'name' => 'Bank Transfer',
                'method_key' => 'bank_transfer',
                'status' => true,
                'min_amount' => 10.00,
                'max_amount' => 10000.00,
                'daily_limit' => 5000.00,
                'charge_type' => 'fixed',
                'charge' => 2.00,
                'description' => 'Direct bank transfer to your account',
                'icon' => 'fe fe-credit-card',
                'processing_time' => '1-3 business days',
                'currency' => 'USD',
                'instructions' => 'Please provide your bank account details including account number, routing number, and bank name.',
                'sort_order' => 1
            ],
            [
                'name' => 'PayPal',
                'method_key' => 'paypal',
                'status' => true,
                'min_amount' => 5.00,
                'max_amount' => 5000.00,
                'daily_limit' => 2500.00,
                'charge_type' => 'percent',
                'charge' => 2.5,
                'description' => 'PayPal instant transfer',
                'icon' => 'fab fa-paypal',
                'processing_time' => '1-24 hours',
                'currency' => 'USD',
                'instructions' => 'Please provide your PayPal email address.',
                'sort_order' => 2
            ],
            [
                'name' => 'Bitcoin',
                'method_key' => 'bitcoin',
                'status' => true,
                'min_amount' => 20.00,
                'max_amount' => 50000.00,
                'daily_limit' => 10000.00,
                'charge_type' => 'fixed',
                'charge' => 5.00,
                'description' => 'Bitcoin cryptocurrency withdrawal',
                'icon' => 'fab fa-bitcoin',
                'processing_time' => '1-6 hours',
                'currency' => 'BTC',
                'instructions' => 'Please provide your Bitcoin wallet address.',
                'sort_order' => 3
            ],
            [
                'name' => 'Skrill',
                'method_key' => 'skrill',
                'status' => false,
                'min_amount' => 10.00,
                'max_amount' => 5000.00,
                'daily_limit' => 2500.00,
                'charge_type' => 'percent',
                'charge' => 3.0,
                'description' => 'Skrill e-wallet transfer',
                'icon' => 'fas fa-wallet',
                'processing_time' => '1-24 hours',
                'currency' => 'USD',
                'instructions' => 'Please provide your Skrill email address.',
                'sort_order' => 4
            ],
            [
                'name' => 'Perfect Money',
                'method_key' => 'perfect_money',
                'status' => false,
                'min_amount' => 5.00,
                'max_amount' => 10000.00,
                'daily_limit' => 5000.00,
                'charge_type' => 'fixed',
                'charge' => 1.00,
                'description' => 'Perfect Money e-currency transfer',
                'icon' => 'fas fa-money-check-alt',
                'processing_time' => '1-6 hours',
                'currency' => 'USD',
                'instructions' => 'Please provide your Perfect Money account number.',
                'sort_order' => 5
            ]
        ];

        foreach ($defaultMethods as $method) {
            WithdrawMethod::updateOrCreate(
                ['method_key' => $method['method_key']],
                $method
            );
        }

        return redirect()->route('admin.withdraw-methods.index')
            ->with('success', 'Default withdrawal methods created successfully.');
    }
}
