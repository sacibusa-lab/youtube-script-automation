<?php

namespace App\Http\Controllers;

use App\Models\CreditLog;
use App\Models\Payment;
use App\Models\TopupPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    /**
     * Display the user's billing and usage history.
     */
    public function history()
    {
        $user = Auth::user();
        
        $usageLogs = CreditLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'usage_page');

        $payments = Payment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'payment_page');

        return view('billing.history', compact('usageLogs', 'payments'));
    }

    /**
     * Display the top-up packages selection page.
     */
    public function topup()
    {
        $packages = TopupPackage::where('is_active', true)->get();
        return view('billing.topup', compact('packages'));
    }
}
