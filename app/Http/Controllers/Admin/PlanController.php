<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Plan;

class PlanController extends Controller
{
    /**
     * List all plans.
     */
    public function index()
    {
        $plans = Plan::all();
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show form to edit a plan.
     */
    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update plan details.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'monthly_credits' => 'required|integer|min:0',
            'monthly_image_tokens' => 'required|integer|min:0',
            'max_tokens_per_request' => 'required|integer|min:0',
            'concurrent_jobs' => 'required|integer|min:1',
            'batch_generation_limit' => 'required|integer|min:1',
            'rollover_percent' => 'required|integer|min:0|max:100',
            'bulk_upload' => 'boolean',
            'series_memory' => 'boolean',
            'api_access' => 'boolean',
            'team_members' => 'boolean',
            'priority_queue' => 'boolean',
            'direct_support' => 'boolean',
        ]);

        // Convert checkboxes to boolean since unchecked checkboxes aren't sent
        $booleanFields = ['bulk_upload', 'series_memory', 'api_access', 'team_members', 'priority_queue', 'direct_support'];
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field);
        }

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully!');
    }
}
