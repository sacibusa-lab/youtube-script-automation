<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OtpVerificationMail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $query = Plan::where('is_active', true);
        
        if ($request->has('plan_id')) {
            $query->where('id', $request->plan_id);
        } else {
            $query->orderBy('sort_order');
        }

        $plans = $query->get();

        // Fallback if the requested plan_id doesn't exist or is inactive
        if ($plans->isEmpty()) {
            $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        }

        return view('auth.register', compact('plans'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $otpCode = strtoupper(Str::random(6));

        $user = User::create([
            'name'                   => $request->name,
            'email'                  => $request->email,
            'password'               => Hash::make($request->password),
            'plan_id'                => $request->plan_id,  // stage the chosen plan
            'otp_code'               => $otpCode,
            'otp_expires_at'         => now()->addMinutes(15),
            'total_credits'          => 0,                  // no credits until payment completes
            'used_credits'           => 0,
            'total_image_tokens'     => 0,
            'used_image_tokens'      => 0,
            'credits_used_this_month'=> 0,
        ]);

        event(new Registered($user));

        // Send OTP Email
        Mail::to($user->email)->send(new OtpVerificationMail($otpCode, $user->name));

        Auth::login($user);

        // Redirect to OTP verification prompt
        return redirect()->route('verification.notice');
    }
}
