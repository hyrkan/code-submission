<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Year;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Section::with('year')->withCount('students');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('year_id')) {
            $query->where('year_id', $request->year_id);
        }

        $sections = $query->orderBy('name')->get();
        $years = Year::active()->orderBy('name')->get();
        return view('settings.sections.index', compact('sections', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $years = Year::active()->orderBy('name')->get();
        return view('settings.sections.create', compact('years'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'year_id' => 'nullable|exists:years,id',
        ]);

        Section::create([
            'name' => $request->name,
            'year_id' => $request->year_id,
        ]);

        return redirect()->route('sections.index')->with('success', 'Section created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('sections.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $section = Section::findOrFail($id);
        $years = Year::active()->orderBy('name')->get();
        return view('settings.sections.edit', compact('section', 'years'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $section = Section::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'year_id' => 'nullable|exists:years,id',
        ]);

        $section->update([
            'name' => $request->name,
            'year_id' => $request->year_id,
        ]);

        return redirect()->route('sections.index')->with('success', 'Section updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $section = Section::findOrFail($id);

        if ($section->students()->count() > 0) {
            return redirect()->route('sections.index')->with('error', 'Cannot delete section with enrolled students.');
        }

        $section->delete();

        return redirect()->route('sections.index')->with('success', 'Section deleted successfully.');
    }

    /**
     * Toggle archive status.
     */
    public function toggleArchive(string $id)
    {
        $section = Section::findOrFail($id);
        $section->update(['is_archived' => !$section->is_archived]);

        $status = $section->is_archived ? 'archived' : 'unarchived';
        return redirect()->route('sections.index')->with('success', "Section {$status} successfully.");
    }
}