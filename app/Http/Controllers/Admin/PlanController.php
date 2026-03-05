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
     * Show form to create a new plan.
     */
    public function create()
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name',
            'price' => 'required|numeric|min:0',
            'monthly_credits' => 'required|numeric|min:0',
            'monthly_image_tokens' => 'required|integer|min:0',
            'max_tokens_per_request' => 'required|integer|min:0',
            'concurrent_jobs' => 'required|integer|min:1',
            'batch_generation_limit' => 'required|integer|min:1',
            'rollover_percent' => 'required|numeric|min:0|max:100',
            'bulk_upload' => 'boolean',
            'series_memory' => 'boolean',
            'api_access' => 'boolean',
            'team_members' => 'boolean',
            'priority_queue' => 'boolean',
            'direct_support' => 'boolean',
            'image_credit_cost' => 'required|integer|min:0',
            'max_images_per_script' => 'required|integer|min:0',
            'max_regeneration_attempts' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $booleanFields = ['bulk_upload', 'series_memory', 'api_access', 'team_members', 'priority_queue', 'direct_support', 'is_active'];
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field);
        }

        Plan::create($validated);

        return redirect()->route('admin.plans.index')->with('success', 'New subscription plan created successfully!');
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
        \Log::info("Admin updating plan ID: {$plan->id}. Request:", $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'monthly_credits' => 'required|numeric|min:0',
            'monthly_image_tokens' => 'required|integer|min:0',
            'max_tokens_per_request' => 'required|integer|min:0',
            'concurrent_jobs' => 'required|integer|min:1',
            'batch_generation_limit' => 'required|integer|min:1',
            'rollover_percent' => 'required|numeric|min:0|max:100',
            'bulk_upload' => 'boolean',
            'series_memory' => 'boolean',
            'api_access' => 'boolean',
            'team_members' => 'boolean',
            'priority_queue' => 'boolean',
            'direct_support' => 'boolean',
            'image_credit_cost' => 'required|integer|min:0',
            'max_images_per_script' => 'required|integer|min:0',
            'max_regeneration_attempts' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Convert checkboxes to boolean since unchecked checkboxes aren't sent
        $booleanFields = ['bulk_upload', 'series_memory', 'api_access', 'team_members', 'priority_queue', 'direct_support', 'is_active'];
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field);
        }

        $plan->update($validated);

        \Log::info("Plan updated ID: {$plan->id}. New state: " . json_encode($plan->fresh()));

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully!');
    }
}
