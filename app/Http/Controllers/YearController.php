<?php

namespace App\Http\Controllers;

use App\Models\Year;
use Illuminate\Http\Request;

class YearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $years = Year::withCount('sections')->orderBy('name')->get();
        return view('settings.years.index', compact('years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings.years.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:years,name',
        ]);

        Year::create([
            'name' => $request->name,
        ]);

        return redirect()->route('years.index')->with('success', 'Year created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('years.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $year = Year::findOrFail($id);
        return view('settings.years.edit', compact('year'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $year = Year::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:years,name,' . $year->id,
        ]);

        $year->update([
            'name' => $request->name,
        ]);

        return redirect()->route('years.index')->with('success', 'Year updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $year = Year::findOrFail($id);

        // Unlink sections from this year before deleting
        $year->sections()->update(['year_id' => null]);
        $year->delete();

        return redirect()->route('years.index')->with('success', 'Year deleted successfully.');
    }

    /**
     * Toggle archive status.
     */
    public function toggleArchive(string $id)
    {
        $year = Year::findOrFail($id);
        $year->update(['is_archived' => !$year->is_archived]);

        $status = $year->is_archived ? 'archived' : 'unarchived';
        return redirect()->route('years.index')->with('success', "Year {$status} successfully.");
    }
}