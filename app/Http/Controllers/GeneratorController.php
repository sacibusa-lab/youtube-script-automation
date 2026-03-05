<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneratorController extends Controller
{
    /**
     * Display the AI Generator application.
     */
    public function index(): View
    {
        return view('generator');
    }
}
