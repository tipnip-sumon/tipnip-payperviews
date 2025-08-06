<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = Plan::with('invests')->orderBy('id')->get();
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:plans',
                'fixed_amount' => 'required|numeric|min:0',
                'time' => 'nullable|integer|min:1|max:365',
                'daily_video_limit' => 'required|integer|min:1|max:1000',
                'video_earning_rate' => 'required|numeric|min:0.01|max:100',
                'description' => 'nullable|string',
                'video_access_enabled' => 'nullable|boolean',
                'interest' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,8})?$/',
                'interest_type' => 'required|string|in:currency,percentage',
                'capital_back' => 'nullable|boolean',
                'lifetime' => 'nullable|boolean',
                'minimum' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,8})?$/',
                'maximum' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,8})?$/',
            ]);

            // Additional validations
            if (isset($data['minimum']) && isset($data['maximum']) && $data['minimum'] > $data['maximum']) {
                return back()->withErrors(['maximum' => 'Maximum amount must be greater than minimum amount.'])->withInput();
            }

            // Convert interest_type string to integer for database storage
            if (isset($data['interest_type'])) {
                $interestTypeMap = [
                    'currency' => 0,    // 0 = 'currency'
                    'percentage' => 1   // 1 = '%'
                ];
                $data['interest_type'] = $interestTypeMap[$data['interest_type']] ?? 0;
            } else {
                $data['interest_type'] = 0;
            }

            // Set default values
            $data['time_name'] = 'days';
            $data['status'] = 1; // Active by default
            
            // Ensure minimum and maximum have default values (database doesn't allow NULL)
            $data['interest'] = $data['interest'] ?? 0.00000000;
            $data['minimum'] = $data['minimum'] ?? 0.00000000;
            $data['maximum'] = $data['maximum'] ?? 0.00000000;
            
            // Handle checkboxes
            $data['featured'] = $request->has('featured') ? 1 : 0;
            $data['capital_back'] = $request->has('capital_back') ? 1 : 0;
            $data['lifetime'] = $request->has('lifetime') ? 1 : 0;
            
            Plan::create($data);
            
            return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create plan. Please try again.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $plan = Plan::with(['invests.user'])->findOrFail($id);
        return view('admin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $plan = Plan::with('invests')->findOrFail($id);
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $id,
            'fixed_amount' => 'required|numeric|min:0',
            'time' => 'nullable|integer|min:1|max:365',
            'daily_video_limit' => 'required|integer|min:1|max:1000',
            'video_earning_rate' => 'required|numeric|min:0|max:1',
            'description' => 'nullable|string|max:1000',
            'video_access_enabled' => 'required|boolean',
            'interest' => 'nullable|numeric|min:0|max:100',
            'interest_type' => 'nullable|string|in:daily,monthly,total',
            'featured' => 'boolean',
            'capital_back' => 'boolean',
            'lifetime' => 'boolean',
            'minimum' => 'nullable|numeric|min:0',
            'maximum' => 'nullable|numeric|min:0',
        ]);

        // Additional validations
        if (isset($data['minimum']) && isset($data['maximum']) && $data['minimum'] > $data['maximum']) {
            return back()->withErrors(['maximum' => 'Maximum amount must be greater than minimum amount.'])->withInput();
        }

        // Convert interest_type string to integer for database storage
        if (isset($data['interest_type'])) {
            $interestTypeMap = [
                'daily' => 1,
                'monthly' => 2,
                'total' => 3
            ];
            $data['interest_type'] = $interestTypeMap[$data['interest_type']] ?? 0;
        } else {
            $data['interest_type'] = 0;
        }

        // Handle checkboxes
        $data['featured'] = $request->has('featured') ? 1 : 0;
        $data['capital_back'] = $request->has('capital_back') ? 1 : 0;
        $data['lifetime'] = $request->has('lifetime') ? 1 : 0;

        try {
            $plan->update($data);
            
            $message = 'Plan updated successfully.';
            if ($plan->invests()->where('status', 1)->count() > 0) {
                $message .= ' Note: Changes may affect active investments.';
            }
            
            return redirect()->route('admin.plans.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update plan. Please try again.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        
        // Check if plan has active investments
        $activeInvestments = $plan->invests()->where('status', 1)->count();
        if ($activeInvestments > 0) {
            return redirect()->route('admin.plans.index')
                           ->with('error', 'Cannot delete plan with active investments. Please complete or transfer investments first.');
        }

        try {
            $plan->delete();
            return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.plans.index')
                           ->with('error', 'Failed to delete plan. Please try again.');
        }
    }

    /**
     * Get plan statistics
     */
    public function statistics($id)
    {
        try {
            $plan = Plan::with('invests')->findOrFail($id);
            
            $stats = [
                'total_investors' => $plan->invests->count(),
                'active_investments' => $plan->invests->where('status', 1)->count(),
                'total_investment_amount' => $plan->invests->sum('amount'),
                'average_investment' => $plan->invests->avg('amount') ?? 0,
                'total_earned' => $plan->invests->sum('return_amount') ?? 0,
                'plan_age_days' => $plan->created_at->diffInDays(now()),
                'daily_earning_potential' => $plan->daily_video_limit * $plan->video_earning_rate,
                'monthly_earning_potential' => $plan->daily_video_limit * $plan->video_earning_rate * 30,
            ];
            
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load plan statistics'], 500);
        }
    }

    /**
     * Toggle plan status
     */
    public function toggleStatus($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            $plan->video_access_enabled = !$plan->video_access_enabled;
            $plan->save();
            
            $status = $plan->video_access_enabled ? 'activated' : 'deactivated';
            
            return response()->json([
                'success' => true,
                'message' => "Plan {$status} successfully.",
                'new_status' => $plan->video_access_enabled
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update plan status.'
            ], 500);
        }
    }
}
