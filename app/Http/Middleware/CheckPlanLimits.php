<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;

class CheckPlanLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $limitType = null): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $plan = $user->plan;

        // General API access check
        if ($limitType === 'api' && (!$plan || !$plan->api_access)) {
            return response()->json(['error' => 'Your current plan does not allow API access. Please upgrade to the Agency plan.'], 403);
        }

        // Bulk Upload Check
        if ($limitType === 'bulk_upload' && (!$plan || !$plan->bulk_upload)) {
            return response()->json(['error' => 'Bulk upload is not supported on your current plan.'], 403);
        }

        // Check if they have credits before even starting the request
        if ($user->total_credits <= $user->used_credits) {
            return response()->json([
                'error' => 'Insufficient credits. Please upgrade your plan or wait for the next billing cycle.',
                'code' => 'INSUFFICIENT_CREDITS'
            ], 402); // 402 Payment Required
        }

        return $next($request);
    }
}
