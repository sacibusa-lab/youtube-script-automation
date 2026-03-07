<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        return view('welcome', compact('plans'));
    }

    /**
     * Display the pricing page.
     */
    public function pricing()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        return view('pricing', compact('plans'));
    }
}
