<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Payment;
use App\Models\CreditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('plan');

        $payments = Payment::where('user_id', $user->id)
            ->with(['plan', 'topupPackage'])
            ->latest()
            ->take(10)
            ->get();

        $recentUsage = CreditLog::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $scriptBalance   = $user->total_credits - $user->used_credits;
        $imageBalance    = $user->total_image_tokens - $user->used_image_tokens;
        $scriptUsedPct   = $user->total_credits > 0
            ? round(($user->used_credits / $user->total_credits) * 100)
            : 0;
        $imageUsedPct    = $user->total_image_tokens > 0
            ? round(($user->used_image_tokens / $user->total_image_tokens) * 100)
            : 0;

        return view('profile.edit', compact(
            'user',
            'payments',
            'recentUsage',
            'scriptBalance',
            'imageBalance',
            'scriptUsedPct',
            'imageUsedPct',
        ));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', Rules\Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
