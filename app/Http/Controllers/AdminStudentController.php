<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\QuizSubmission;
use App\Models\Year;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStudentController extends Controller
{
    /**
     * Display a listing of all students with year/section filters.
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'year', 'section']);

        if ($request->filled('year_id')) {
            $query->where('year_id', $request->year_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->orderBy('last_name')->orderBy('first_name')->paginate(15)->withQueryString();
        $years = Year::active()->orderBy('name')->get();
        $sections = Section::active()->with('year')->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'years', 'sections'));
    }

    /**
     * Display analytics for an individual student.
     */
    public function analytics(Student $student)
    {
        $student->load(['user', 'year', 'section']);

        // Get all submissions grouped by quiz, ordered by quiz creation date
        $submissions = QuizSubmission::with(['quiz', 'quizItem'])
            ->where('student_id', $student->id)
            ->orderBy('submitted_at', 'asc')
            ->get();

        // Group by quiz
        $byQuiz = $submissions->groupBy('quiz_id');

        // Build quiz performance data (ordered by quiz creation date)
        $quizPerformance = [];
        foreach ($byQuiz as $quizId => $quizSubs) {
            $quiz = $quizSubs->first()->quiz;
            if (!$quiz) continue;

            $totalScore = $quizSubs->sum('score');
            $maxScore = $quiz->items->sum('points');
            $pctScore = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0;
            $passed = $quizSubs->where('status', 'passed')->count();
            $failed = $quizSubs->where('status', 'failed')->count();
            $total = $quizSubs->count();

            // Determine overall status
            if ($failed > 0) {
                $status = 'failed';
            } elseif ($quizSubs->where('status', 'submitted')->count() > 0) {
                $status = 'pending';
            } else {
                $status = 'passed';
            }

            $quizPerformance[] = [
                'quiz_id' => $quiz->id,
                'quiz_name' => $quiz->name,
                'quiz_date' => $quiz->created_at,
                'submitted_at' => $quizSubs->max('submitted_at'),
                'total_score' => $totalScore,
                'max_score' => $maxScore,
                'pct_score' => $pctScore,
                'passed_count' => $passed,
                'failed_count' => $failed,
                'total_items' => $total,
                'status' => $status,
                'language' => $quiz->language,
            ];
        }

        // Sort by quiz creation date
        usort($quizPerformance, fn($a, $b) => $a['quiz_date']->timestamp - $b['quiz_date']->timestamp);

        // Calculate overall stats
        $totalQuizzes = count($quizPerformance);
        $totalSubmissions = $submissions->count();
        $overallAvgScore = $totalQuizzes > 0 ? round(array_sum(array_column($quizPerformance, 'pct_score')) / $totalQuizzes, 1) : 0;
        $totalPassed = $submissions->where('status', 'passed')->count();
        $totalFailed = $submissions->where('status', 'failed')->count();
        $passRate = $totalSubmissions > 0 ? round(($totalPassed / $totalSubmissions) * 100, 1) : 0;

        // Calculate trend (compare last 50% of quizzes to first 50%)
        $trend = 'stable';
        if ($totalQuizzes >= 2) {
            $midpoint = (int) ceil($totalQuizzes / 2);
            $firstHalf = array_slice($quizPerformance, 0, $midpoint);
            $secondHalf = array_slice($quizPerformance, $midpoint);

            $firstAvg = count($firstHalf) > 0 ? array_sum(array_column($firstHalf, 'pct_score')) / count($firstHalf) : 0;
            $secondAvg = count($secondHalf) > 0 ? array_sum(array_column($secondHalf, 'pct_score')) / count($secondHalf) : 0;

            $diff = $secondAvg - $firstAvg;
            if ($diff > 10) {
                $trend = 'improving';
            } elseif ($diff < -10) {
                $trend = 'declining';
            }
        }

        // Difficulty breakdown
        $easySubs = $submissions->filter(fn($s) => $s->quizItem && $s->quizItem->difficulty === 'easy');
        $mediumSubs = $submissions->filter(fn($s) => $s->quizItem && $s->quizItem->difficulty === 'medium');
        $hardSubs = $submissions->filter(fn($s) => $s->quizItem && $s->quizItem->difficulty === 'hard');

        $difficultyStats = [
            'easy' => [
                'total' => $easySubs->count(),
                'passed' => $easySubs->where('status', 'passed')->count(),
                'avg_score' => $easySubs->count() > 0 && $easySubs->avg('score') !== null ? round($easySubs->avg('score'), 1) : 0,
            ],
            'medium' => [
                'total' => $mediumSubs->count(),
                'passed' => $mediumSubs->where('status', 'passed')->count(),
                'avg_score' => $mediumSubs->count() > 0 && $mediumSubs->avg('score') !== null ? round($mediumSubs->avg('score'), 1) : 0,
            ],
            'hard' => [
                'total' => $hardSubs->count(),
                'passed' => $hardSubs->where('status', 'passed')->count(),
                'avg_score' => $hardSubs->count() > 0 && $hardSubs->avg('score') !== null ? round($hardSubs->avg('score'), 1) : 0,
            ],
        ];

        return view('admin.students.analytics', compact(
            'student', 'quizPerformance', 'totalQuizzes', 'totalSubmissions',
            'overallAvgScore', 'totalPassed', 'totalFailed', 'passRate',
            'trend', 'difficultyStats'
        ));
    }

    /**
     * Get sections filtered by year (for AJAX).
     */
    public function getSections(Request $request)
    {
        $query = Section::active()->orderBy('name');

        if ($request->filled('year_id')) {
            $query->where('year_id', $request->year_id);
        }

        return response()->json($query->get(['id', 'name', 'year_id']));
    }
}