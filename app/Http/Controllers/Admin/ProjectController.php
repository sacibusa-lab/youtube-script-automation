<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Global Story Logs.
     */
    public function index()
    {
        $projects = Video::with('user')
            ->latest()
            ->paginate(30);

        return view('admin.projects.index', compact('projects'));
    }
}
