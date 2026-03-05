<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TopupPackage;
use Illuminate\Http\Request;

class TopupPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = TopupPackage::latest()->get();
        return view('admin.topups.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.topups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        TopupPackage::create($validated);

        return redirect()->route('admin.topup-packages.index')->with('success', 'Top-up package created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TopupPackage $topupPackage)
    {
        return view('admin.topups.edit', ['package' => $topupPackage]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TopupPackage $topupPackage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $topupPackage->update($validated);

        return redirect()->route('admin.topup-packages.index')->with('success', 'Top-up package updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TopupPackage $topupPackage)
    {
        $topupPackage->delete();

        return redirect()->route('admin.topup-packages.index')->with('success', 'Top-up package removed.');
    }
}
