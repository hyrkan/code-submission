<?php

namespace App\Http\Controllers;

use App\Models\QuizSubmission;
use App\Models\Quiz;
use App\Models\Year;
use App\Models\Section;
use Illuminate\Http\Request;

class AdminSubmissionController extends Controller
{
    /**
     * Display a listing of all quiz submissions with filters.
     */
    public function index(Request $request)
    {
        $query = QuizSubmission::with(['quiz', 'quizItem', 'student.user', 'student.year', 'student.section'])
            ->orderBy('submitted_at', 'desc');

        if ($request->filled('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }

        if ($request->filled('year_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('year_id', $request->year_id);
            });
        }

        if ($request->filled('section_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('student_number', 'like', "%{$search}%");
                })
                ->orWhereHas('quiz', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $submissions = $query->paginate(20)->withQueryString();
        $quizzes = Quiz::orderBy('name')->get();
        $years = Year::active()->orderBy('name')->get();
        $sections = Section::active()->with('year')->orderBy('name')->get();

        return view('admin.submissions.index', compact('submissions', 'quizzes', 'years', 'sections'));
    }

    /**
     * Show a single submission detail.
     */
    public function show(string $id)
    {
        $submission = QuizSubmission::with(['quiz', 'quizItem', 'student.user', 'student.year', 'student.section'])
            ->findOrFail($id);

        return view('admin.submissions.show', compact('submission'));
    }

    /**
     * Update the submission (grade/feedback).
     */
    public function update(Request $request, string $id)
    {
        $submission = QuizSubmission::findOrFail($id);

        $request->validate([
            'status' => 'required|in:submitted,graded,passed,failed',
            'score' => 'nullable|integer|min:0',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'status' => $request->status,
            'score' => $request->score,
            'feedback' => $request->feedback,
        ]);

        return redirect()->back()->with('success', 'Submission updated successfully.');
    }
}