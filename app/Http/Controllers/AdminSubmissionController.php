<?php

namespace App\Http\Controllers;

use App\Models\QuizSubmission;
use App\Models\Quiz;
use App\Models\Student;
use App\Models\Year;
use App\Models\Section;
use Illuminate\Http\Request;

class AdminSubmissionController extends Controller
{
    /**
     * Level 1: Display a paginated list of quizzes that have submissions.
     */
    public function index(Request $request)
    {
        // Get quizzes that have at least one submission
        $query = Quiz::whereHas('submissions')
            ->withCount(['submissions', 'items'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        $quizzes = $query->paginate(12)->withQueryString();

        // Attach submission stats to each quiz
        $quizzes->getCollection()->transform(function ($quiz) {
            $stats = QuizSubmission::where('quiz_id', $quiz->id)
                ->selectRaw("COUNT(*) as total")
                ->selectRaw("COUNT(DISTINCT student_id) as student_count")
                ->selectRaw("SUM(status = 'submitted') as pending_count")
                ->selectRaw("SUM(status = 'graded') as graded_count")
                ->selectRaw("SUM(status = 'passed') as passed_count")
                ->selectRaw("SUM(status = 'failed') as failed_count")
                ->first();
            $quiz->sub_stats = $stats;
            return $quiz;
        });

        return view('admin.submissions.index', compact('quizzes'));
    }

    /**
     * Level 2: Display students who submitted for a specific quiz (paginated).
     */
    public function quizStudents(Request $request, Quiz $quiz)
    {
        // Get distinct students who have submissions for this quiz, with aggregated stats
        $query = Student::whereHas('submissions', function ($q) use ($quiz) {
                $q->where('quiz_id', $quiz->id);
            })
            ->with(['user', 'year', 'section'])
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('year_id')) {
            $query->where('year_id', $request->year_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->paginate(15)->withQueryString();

        // Attach per-student submission stats for this quiz
        $students->getCollection()->transform(function ($student) use ($quiz) {
            $stats = QuizSubmission::where('quiz_id', $quiz->id)
                ->where('student_id', $student->id)
                ->selectRaw("COUNT(*) as total")
                ->selectRaw("SUM(score) as total_score")
                ->selectRaw("SUM(status = 'submitted') as pending_count")
                ->selectRaw("SUM(status = 'graded') as graded_count")
                ->selectRaw("SUM(status = 'passed') as passed_count")
                ->selectRaw("SUM(status = 'failed') as failed_count")
                ->first();
            $student->sub_stats = $stats;

            // Determine overall status
            if ($stats->failed_count > 0) {
                $student->overall_status = 'failed';
            } elseif ($stats->pending_count > 0) {
                $student->overall_status = 'submitted';
            } elseif ($stats->graded_count > 0) {
                $student->overall_status = 'graded';
            } else {
                $student->overall_status = 'passed';
            }

            return $student;
        });

        $years = Year::active()->orderBy('name')->get();
        $sections = Section::active()->with('year')->orderBy('name')->get();

        return view('admin.submissions.quiz-students', compact('quiz', 'students', 'years', 'sections'));
    }

    /**
     * Level 3: Display all challenge submissions by a specific student for a specific quiz.
     */
    public function studentDetail(Quiz $quiz, Student $student)
    {
        $submissions = QuizSubmission::with(['quizItem'])
            ->where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->orderBy('submitted_at', 'asc')
            ->get();

        $student->load(['user', 'year', 'section']);
        $quiz->load('items');

        return view('admin.submissions.student-detail', compact('quiz', 'student', 'submissions'));
    }

    /**
     * Show a single submission detail (for grading).
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