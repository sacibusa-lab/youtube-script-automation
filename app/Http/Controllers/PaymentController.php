<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Plan;
use App\Models\User;
use App\Models\AppSetting;
use App\Models\TopupPackage;
use App\Models\Payment;
use App\Services\BillingService;

class PaymentController extends Controller
{
    /**
     * Initializes the Paystack transaction for the selected plan.
     */
    public function initialize(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);

        $secretKey = AppSetting::where('key', 'paystack_secret_key')->value('value');

        if (!$secretKey) {
            return redirect()->route('dashboard')->with('error', 'Payment gateway is not currently configured by the administrator.');
        }
        
        // If plan is free (e.g. Price is 0), grant access immediately
        if ($plan->price <= 0) {
            $this->assignPlanToUser($user, $plan);
            return redirect()->route('dashboard')->with('success', 'Plan activated successfully.');
        }

        $amountInKobo = $plan->price * 100;
        $reference = 'PLN_' . uniqid() . '_' . time();

        $response = Http::withToken($secretKey)->post('https://api.paystack.co/transaction/initialize', [
            'amount'       => $amountInKobo,
            'email'        => $user->email,
            'reference'    => $reference,
            'callback_url' => route('payment.callback'),
            'metadata'     => [
                'user_id' => $user->id,
                'plan_id' => $plan->id
            ]
        ]);

        if ($response->successful() && $response->json('status')) {
            $authUrl = $response->json('data.authorization_url');
            return redirect()->away($authUrl);
        }

        return redirect()->route('dashboard')->with('error', 'Failed to initialize payment gateway.');
    }

    /**
     * Initializes the Paystack transaction for a top-up package.
     */
    public function initializeTopup(Request $request)
    {
        $request->validate([
            'topup_package_id' => 'required|exists:topup_packages,id'
        ]);

        $user = Auth::user();
        $package = TopupPackage::findOrFail($request->topup_package_id);

        $secretKey = AppSetting::where('key', 'paystack_secret_key')->value('value');

        if (!$secretKey) {
            return redirect()->back()->with('error', 'Payment gateway is not currently configured.');
        }
        
        $amountInKobo = $package->price * 100;
        $reference = 'TUP_' . uniqid() . '_' . time();

        $response = Http::withToken($secretKey)->post('https://api.paystack.co/transaction/initialize', [
            'amount' => $amountInKobo,
            'email' => $user->email,
            'reference' => $reference,
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'user_id' => $user->id,
                'topup_package_id' => $package->id,
                'type' => 'topup'
            ]
        ]);

        if ($response->successful() && $response->json('status')) {
            $authUrl = $response->json('data.authorization_url');
            return redirect()->away($authUrl);
        }

        return redirect()->back()->with('error', 'Failed to initialize payment gateway.');
    }

    /**
     * Handles the callback from Paystack after payment (browser redirect).
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('dashboard')->with('error', 'No reference supplied.');
        }

        $secretKey = AppSetting::where('key', 'paystack_secret_key')->value('value');

        $response = Http::withToken($secretKey)->get("https://api.paystack.co/transaction/verify/" . rawurlencode($reference));

        if ($response->successful() && $response->json('status') && $response->json('data.status') === 'success') {
            $data     = $response->json('data');
            $metadata = $data['metadata'] ?? [];
            $userId   = $metadata['user_id'] ?? null;

            if ($userId) {
                $user = User::find($userId);
                $type = $metadata['type'] ?? 'subscription';

                if ($user) {
                    if ($type === 'topup' && isset($metadata['topup_package_id'])) {
                        $package = TopupPackage::find($metadata['topup_package_id']);
                        if ($package) {
                            $this->applyTopupToUser($user, $package, $reference, $data['amount'] / 100);
                            if (Auth::check() && Auth::id() === $user->id) {
                                return redirect()->route('billing.history')->with('success', "Top-up successful! {$package->name} have been added to your balance.");
                            }
                        }
                    } else {
                        $planId = $metadata['plan_id'] ?? null;
                        $plan = Plan::find($planId);
                        if ($plan) {
                            $this->recordPayment($user, $plan->price, 'subscription', $reference, $plan->id);
                            $this->assignPlanToUser($user, $plan);

                            if (Auth::check() && Auth::id() === $user->id) {
                                return redirect()->route('dashboard')->with('success', "Payment successful! You are now subscribed to the {$plan->name} plan.");
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('dashboard')->with('error', 'Payment verification failed or was cancelled.');
    }

    /**
     * Paystack Webhook — server-to-server event listener.
     * Handles: charge.success, subscription.disable, invoice.payment_failed
     * This route is public (no auth) but HMAC-verified.
     */
    public function webhook(Request $request)
    {
        $webhookSecret = AppSetting::where('key', 'paystack_webhook_secret')->value('value');

        // Verify HMAC signature
        if ($webhookSecret) {
            $signature = $request->header('x-paystack-signature');
            $expected  = hash_hmac('sha512', $request->getContent(), $webhookSecret);

            if (!hash_equals($expected, $signature ?? '')) {
                Log::warning('[PAYSTACK WEBHOOK] Invalid signature — rejected.');
                return response()->json(['status' => 'invalid_signature'], 400);
            }
        }

        $event = $request->json('event');
        $data  = $request->json('data');

        Log::info("[PAYSTACK WEBHOOK] Event received: {$event}");

        match ($event) {
            'charge.success'            => $this->handleChargeSuccess($data),
            'subscription.disable'      => $this->handleSubscriptionDisable($data),
            'invoice.payment_failed'    => $this->handlePaymentFailed($data),
            default                     => Log::info("[PAYSTACK WEBHOOK] Unhandled event: {$event}")
        };

        return response()->json(['status' => 'ok']);
    }

    // ──────────────────────────────────────────────────────────────
    // Webhook Event Handlers
    // ──────────────────────────────────────────────────────────────

    /**
     * Successful charge — find the user via email and assign/renew their plan.
     */
    protected function handleChargeSuccess(array $data): void
    {
        $email    = $data['customer']['email'] ?? null;
        $metadata = $data['metadata'] ?? [];
        $type     = $metadata['type'] ?? 'subscription';

        Log::info("[PAYSTACK WEBHOOK] charge.success for {$email}. Metadata:", $metadata);

        if (!$email) {
            Log::warning('[PAYSTACK WEBHOOK] charge.success missing customer email.');
            return;
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            Log::warning("[PAYSTACK WEBHOOK] charge.success: Could not find user ({$email}).");
            return;
        }

        if ($type === 'topup') {
            $packageId = $metadata['topup_package_id'] ?? null;
            $package = TopupPackage::find($packageId);
            if ($package) {
                Log::info("[PAYSTACK WEBHOOK] Applying topup {$package->name} to user {$user->id}");
                $this->handleTopupSuccess($user, $package, $data);
            }
        } else {
            $planId   = $metadata['plan_id'] ?? null;
            $plan = $planId ? Plan::find($planId) : ($user?->plan ?? null);

            if ($plan) {
                Log::info("[PAYSTACK WEBHOOK] Re-assigning plan {$plan->name} to user {$user->id}. (Metadata ID was: " . ($planId ?? 'NULL') . ")");
                $this->recordPayment($user, $data['amount'] / 100, 'subscription', $data['reference'], $plan->id);
                $this->assignPlanToUser($user, $plan);
            } else {
                Log::warning("[PAYSTACK WEBHOOK] charge.success: No plan found for user {$user->id} in metadata or current profile.");
            }
        }
    }

    /**
     * Successful topup via webhook.
     */
    protected function handleTopupSuccess(User $user, TopupPackage $package, array $data): void
    {
        $reference = $data['reference'];
        $amount = $data['amount'] / 100;
        
        $this->applyTopupToUser($user, $package, $reference, $amount);
        Log::info("[PAYSTACK WEBHOOK] Top-up {$package->name} applied to User {$user->id}.");
    }

    /**
     * Subscription disabled — suspend user's credit access.
     */
    protected function handleSubscriptionDisable(array $data): void
    {
        $email = $data['customer']['email'] ?? null;
        if (!$email) return;

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->total_credits = 0;
            $user->save();
            Log::info("[PAYSTACK WEBHOOK] Subscription disabled for User {$user->id}. Credits zeroed.");
        }
    }

    /**
     * Payment failed — log and optionally notify the user.
     */
    protected function handlePaymentFailed(array $data): void
    {
        $email = $data['customer']['email'] ?? null;
        Log::warning("[PAYSTACK WEBHOOK] Payment failed for: {$email}");
        // Future: send payment failure email to user
    }

    // ──────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Allocates the plan to the user and resets token balances.
     */
    protected function assignPlanToUser(User $user, Plan $plan): void
    {
        Log::info("[BILLING] Assigning Plan ID: {$plan->id} ({$plan->name}) to User ID: {$user->id}. Old Plan ID: " . ($user->plan_id ?? 'None'));

        $user->plan_id                 = $plan->id;
        $user->total_credits           = $plan->monthly_credits;
        $user->used_credits            = 0;
        $user->credits_used_this_month = 0;
        $user->daily_credits_used      = 0;
        $user->daily_credits_reset_at  = now();
        $user->total_image_tokens      = $plan->monthly_image_tokens;
        $user->used_image_tokens       = 0;
        $user->last_rollover_at        = now();
        $user->save();
        
        Log::info("[BILLING] Successfully updated User {$user->id} to {$plan->name}. New balance: {$user->total_credits} credits.");
    }

    /**
     * Applies a top-up package to a user.
     */
    protected function applyTopupToUser(User $user, TopupPackage $package, string $reference, float $amount): void
    {
        // Avoid double processing
        if (Payment::where('reference', $reference)->where('status', 'success')->exists()) {
            return;
        }

        $this->recordPayment($user, $amount, 'topup', $reference, null, $package->id);

        $user->total_credits += $package->credits;
        $user->save();
    }

    /**
     * Records a payment in the database.
     */
    protected function recordPayment(User $user, float $amount, string $type, string $reference, ?int $planId = null, ?int $packageId = null): void
    {
        Payment::updateOrCreate(
            ['reference' => $reference],
            [
                'user_id' => $user->id,
                'amount' => $amount,
                'status' => 'success',
                'type' => $type,
                'plan_id' => $planId,
                'topup_package_id' => $packageId,
            ]
        );
    }
}
