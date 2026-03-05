<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * List all users.
     */
    public function index()
    {
        $users = User::withCount('videos') // Video model is what we call stories
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Toggle the admin status of a user.
     */
    public function toggleAdmin(User $user)
    {
        // Don't allow toggling yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own admin status.');
        }

        $user->update(['is_admin' => !$user->is_admin]);

        return back()->with('success', "Role updated for {$user->name}.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $plans = Plan::all();
        return view('admin.users.edit', compact('user', 'plans'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        Log::info("Admin attempting manual update for User ID: {$user->id}. Request Data: " . json_encode($request->all()));

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'total_credits' => 'required|numeric|min:0',
            'used_credits' => 'required|numeric|min:0',
            'total_image_tokens' => 'required|numeric|min:0',
            'used_image_tokens' => 'required|numeric|min:0',
        ]);

        $oldTiers = $user->only(['plan_id', 'total_credits']);
        
        $user->plan_id = $validated['plan_id'];
        $user->total_credits = $validated['total_credits'];
        $user->used_credits = $validated['used_credits'];
        $user->total_image_tokens = $validated['total_image_tokens'];
        $user->used_image_tokens = $validated['used_image_tokens'];
        
        $user->save();

        Log::info("Admin successfully updated User ID: {$user->id}.", [
            'old' => $oldTiers,
            'new' => $user->only(['plan_id', 'total_credits'])
        ]);

        return redirect()->route('admin.users.index')->with('success', "User account for {$user->name} has been updated.");
    }

    /**
     * Remove a user from the platform.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return back()->with('success', 'User removed successfully.');
    }
}
