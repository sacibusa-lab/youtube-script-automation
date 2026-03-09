<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\OtpVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpVerificationController extends Controller
{
    /**
     * Show the OTP verification screen.
     */
    public function show(Request $request)
    {
        // If the user's email is already verified, send them to initialize payment or dashboard
        if ($request->user()->hasVerifiedEmail()) {
            // Check if they already have an active paid plan or fallback to dashboard
            if ($request->user()->plan_id) {
                return redirect()->route('payment.initialize', ['plan_id' => $request->user()->plan_id]);
            }
            return redirect()->intended(route('dashboard', absolute: false));
        }

        return view('auth.verify-otp');
    }

    /**
     * Handle the OTP code submission.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        // 1. Check if OTP matches
        if ($user->otp_code !== strtoupper($request->otp_code)) {
            return back()->withErrors(['otp_code' => 'The verification code is incorrect.']);
        }

        // 2. Check if expired
        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'This verification code has expired. Please request a new one.']);
        }

        // 3. Mark as verified
        $user->email_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // 4. Send to payment initiation passing their staged plan_id
        if ($user->plan_id) {
            return redirect()->route('payment.initialize', ['plan_id' => $user->plan_id]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Generate a new OTP and resend the email.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Generate a new 6-digit random code
        $code = Str::random(6);
        $code = strtoupper($code);

        // Save to user
        $request->user()->forceFill([
            'otp_code' => $code,
            'otp_expires_at' => now()->addMinutes(15) // 15 mins validity
        ])->save();

        // Dispatch Email
        Mail::to($request->user()->email)->send(new OtpVerificationMail($code, $request->user()->name));

        return back()->with('status', 'verification-link-sent');
    }
}
