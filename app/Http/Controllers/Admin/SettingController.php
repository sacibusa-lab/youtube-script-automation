<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

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
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,ico|max:1024',
            'mail_port' => 'nullable|numeric',
            'mail_from_address' => 'nullable|email',
        ]);

        $data = $request->except(['_token', '_method', 'logo', 'favicon']);

        // Handle File Uploads
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('settings', 'public');
            AppSetting::updateOrCreate(['key' => 'logo'], ['value' => $logoPath]);
        }

        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('settings', 'public');
            AppSetting::updateOrCreate(['key' => 'favicon'], ['value' => $faviconPath]);
        }

        foreach ($data as $key => $value) {
            AppSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clear the cache so changes reflect globally immediately
        Cache::forget('site_settings');

        return back()->with('success', 'Settings updated successfully.');
    }
}
