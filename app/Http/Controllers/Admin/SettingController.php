<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AppSetting;

class SettingController extends Controller
{
    /**
     * Display the settings dashboard.
     */
    public function index()
    {
        $settings = AppSetting::pluck('value', 'key')->toArray();
        return view('admin.settings', compact('settings'));
    }

    /**
     * Store or update settings.
     */
    public function store(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            AppSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
