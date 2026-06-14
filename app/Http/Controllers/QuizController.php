<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Year;
use App\Models\Section;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Quiz::with(['year', 'section', 'creator']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $quizzes = $query->orderBy('created_at', 'desc')->get();

        return view('quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $years = Year::active()->orderBy('name')->get();
        $sections = Section::active()->with('year')->orderBy('name')->get();
        return view('quizzes.create', compact('years', 'sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'language' => 'required|string|in:python,java,javascript,c,cpp,php',
            'year_id' => 'nullable|exists:years,id',
            'section_id' => 'nullable|exists:sections,id',
            'time_limit' => 'nullable|integer|min:1',
            'is_published' => 'nullable|boolean',
            'scheduled_at' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.difficulty' => 'required|in:easy,medium,hard',
            'items.*.sample_input' => 'nullable|string',
            'items.*.sample_output' => 'nullable|string',
            'items.*.expected_output' => 'nullable|string',
            'items.*.coding_standards' => 'nullable|string',
            'items.*.grading_criteria' => 'nullable|string',
            'items.*.points' => 'required|integer|min:0',
        ]);

        $quiz = Quiz::create([
            'name' => $request->name,
            'description' => $request->description,
            'language' => $request->language,
            'year_id' => $request->year_id,
            'section_id' => $request->section_id,
            'created_by' => auth()->id(),
            'time_limit' => $request->time_limit,
            'total_points' => collect($request->items)->sum('points'),
            'is_published' => $request->boolean('is_published'),
            'scheduled_at' => $request->scheduled_at,
        ]);

        foreach ($request->items as $index => $item) {
            $quiz->items()->create([
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'difficulty' => $item['difficulty'],
                'sample_input' => $item['sample_input'] ?? null,
                'sample_output' => $item['sample_output'] ?? null,
                'expected_output' => $item['expected_output'] ?? null,
                'coding_standards' => $item['coding_standards'] ?? null,
                'grading_criteria' => $item['grading_criteria'] ?? null,
                'points' => $item['points'],
                'sort_order' => $index + 1,
            ]);
        }

        return redirect()->route('quizzes.index')->with('success', 'Quiz created successfully with ' . count($request->items) . ' items.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $quiz = Quiz::with('items')->findOrFail($id);
        $years = Year::active()->orderBy('name')->get();
        $sections = Section::active()->with('year')->orderBy('name')->get();
        return view('quizzes.edit', compact('quiz', 'years', 'sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $quiz = Quiz::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'language' => 'required|string|in:python,java,javascript,c,cpp,php',
            'year_id' => 'nullable|exists:years,id',
            'section_id' => 'nullable|exists:sections,id',
            'time_limit' => 'nullable|integer|min:1',
            'is_published' => 'nullable|boolean',
            'scheduled_at' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.difficulty' => 'required|in:easy,medium,hard',
            'items.*.sample_input' => 'nullable|string',
            'items.*.sample_output' => 'nullable|string',
            'items.*.expected_output' => 'nullable|string',
            'items.*.coding_standards' => 'nullable|string',
            'items.*.grading_criteria' => 'nullable|string',
            'items.*.points' => 'required|integer|min:0',
        ]);

        $quiz->update([
            'name' => $request->name,
            'description' => $request->description,
            'language' => $request->language,
            'year_id' => $request->year_id,
            'section_id' => $request->section_id,
            'time_limit' => $request->time_limit,
            'total_points' => collect($request->items)->sum('points'),
            'is_published' => $request->boolean('is_published'),
            'scheduled_at' => $request->scheduled_at,
        ]);

        // Delete existing items and recreate
        $quiz->items()->delete();

        foreach ($request->items as $index => $item) {
            $quiz->items()->create([
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'difficulty' => $item['difficulty'],
                'sample_input' => $item['sample_input'] ?? null,
                'sample_output' => $item['sample_output'] ?? null,
                'expected_output' => $item['expected_output'] ?? null,
                'coding_standards' => $item['coding_standards'] ?? null,
                'grading_criteria' => $item['grading_criteria'] ?? null,
                'points' => $item['points'],
                'sort_order' => $index + 1,
            ]);
        }

        return redirect()->route('quizzes.show', $quiz->id)->with('success', 'Quiz updated successfully.');
    }

    /**
     * Toggle publish status.
     */
    public function togglePublish(string $id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->update(['is_published' => !$quiz->is_published]);

        $status = $quiz->is_published ? 'published' : 'unpublished';
        return redirect()->back()->with('success', "Quiz {$status} successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quiz = Quiz::with(['year', 'section', 'creator', 'items'])->findOrFail($id);
        return view('quizzes.show', compact('quiz'));
    }

    /**
     * Toggle archive status.
     */
    public function toggleArchive(string $id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->update(['is_archived' => !$quiz->is_archived]);

        $status = $quiz->is_archived ? 'archived' : 'unarchived';
        return redirect()->route('quizzes.index')->with('success', "Quiz {$status} successfully.");
    }
}
