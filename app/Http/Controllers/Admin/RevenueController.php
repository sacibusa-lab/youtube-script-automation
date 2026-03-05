<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    /**
     * Display a revenue dashboard with MRR and transaction logs.
     */
    public function index()
    {
        // 1. Monthly Recurring Revenue (MRR) - Estimated from active users and their plans
        $mrr = User::join('plans', 'users.plan_id', '=', 'plans.id')
            ->whereNotNull('users.plan_id')
            ->sum('plans.price');

        // 2. Total Lifetime Revenue
        $totalRevenue = Payment::where('status', 'success')->sum('amount');

        // 3. Transactions this month
        $monthlyRevenue = Payment::where('status', 'success')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // 4. Revenue Breakdown (Subscription vs Topup)
        $revenueByType = Payment::where('status', 'success')
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->get();

        // 5. Recent Payments List
        $recentPayments = Payment::with(['user', 'plan', 'topupPackage'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // 6. Growth Chart Data (Last 6 Months)
        $chartData = Payment::where('status', 'success')
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return view('admin.revenue.index', compact(
            'mrr', 
            'totalRevenue', 
            'monthlyRevenue', 
            'revenueByType', 
            'recentPayments',
            'chartData'
        ));
    }
}
