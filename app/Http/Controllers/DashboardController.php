<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\Student;
use App\Models\Year;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $years = Year::active()->orderBy('name')->get();
        $sections = Section::active()->with('year')->orderBy('name')->get();

        // Determine filter scope
        $yearId = $request->filled('year_id') ? $request->year_id : null;
        $sectionId = $request->filled('section_id') ? $request->section_id : null;

        // Get filtered sections to show
        $filteredSections = Section::active()->with('year')->orderBy('name');
        if ($yearId) {
            $filteredSections->where('year_id', $yearId);
        }
        if ($sectionId) {
            $filteredSections->where('id', $sectionId);
        }
        $filteredSections = $filteredSections->get();

        // Build section performance data
        $sectionPerformance = [];
        foreach ($filteredSections as $section) {
            $studentsInSection = Student::where('section_id', $section->id)->pluck('id');

            if ($studentsInSection->isEmpty()) continue;

            $studentCount = $studentsInSection->count();

            // Get all submissions for students in this section
            $submissions = QuizSubmission::whereIn('student_id', $studentsInSection)
                ->with(['quiz', 'quizItem'])
                ->get();

            if ($submissions->isEmpty()) {
                $sectionPerformance[] = [
                    'section' => $section,
                    'student_count' => $studentCount,
                    'total_submissions' => 0,
                    'total_quizzes' => 0,
                    'avg_score_pct' => 0,
                    'pass_rate' => 0,
                    'passed_count' => 0,
                    'failed_count' => 0,
                    'pending_count' => 0,
                    'avg_score' => 0,
                    'max_score' => 0,
                    'quiz_breakdown' => [],
                ];
                continue;
            }

            // Group by quiz to get per-quiz stats
            $byQuiz = $submissions->groupBy('quiz_id');
            $totalQuizzesTaken = $byQuiz->count();

            $totalScorePct = 0;
            $quizCount = 0;
            $totalPassed = $submissions->where('status', 'passed')->count();
            $totalFailed = $submissions->where('status', 'failed')->count();
            $totalPending = $submissions->where('status', 'submitted')->count();
            $totalGraded = $submissions->where('status', 'graded')->count();

            $quizBreakdown = [];
            foreach ($byQuiz as $quizId => $quizSubs) {
                $quiz = $quizSubs->first()->quiz;
                if (!$quiz) continue;

                $uniqueStudents = $quizSubs->unique('student_id')->count();
                $maxScore = $quiz->items->sum('points');
                $avgScore = $quizSubs->avg('score');
                $pctScore = $maxScore > 0 ? round(($avgScore / $maxScore) * 100, 1) : 0;
                $quizPassed = $quizSubs->where('status', 'passed')->count();
                $quizTotal = $quizSubs->count();

                $totalScorePct += $pctScore;
                $quizCount++;

                $quizBreakdown[] = [
                    'quiz_name' => $quiz->name,
                    'quiz_id' => $quiz->id,
                    'language' => $quiz->language,
                    'students_attempted' => $uniqueStudents,
                    'avg_score' => round($avgScore, 1),
                    'max_score' => $maxScore,
                    'pct_score' => $pctScore,
                    'pass_rate' => $quizTotal > 0 ? round(($quizPassed / $quizTotal) * 100, 1) : 0,
                ];
            }

            $avgScorePct = $quizCount > 0 ? round($totalScorePct / $quizCount, 1) : 0;
            $totalSubs = $submissions->count();
            $passRate = $totalSubs > 0 ? round(($totalPassed / $totalSubs) * 100, 1) : 0;

            $sectionPerformance[] = [
                'section' => $section,
                'student_count' => $studentCount,
                'total_submissions' => $totalSubs,
                'total_quizzes' => $totalQuizzesTaken,
                'avg_score_pct' => $avgScorePct,
                'pass_rate' => $passRate,
                'passed_count' => $totalPassed,
                'failed_count' => $totalFailed,
                'pending_count' => $totalPending,
                'graded_count' => $totalGraded,
                'quiz_breakdown' => $quizBreakdown,
            ];
        }

        // Sort by avg score descending
        usort($sectionPerformance, fn($a, $b) => $b['avg_score_pct'] <=> $a['avg_score_pct']);

        // Overall stats
        $totalStudents = Student::when($yearId, fn($q) => $q->where('year_id', $yearId))
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->count();

        $activeQuizzes = Quiz::where('is_published', true)
            ->where('is_archived', false)
            ->count();

        $totalSubmissionsQuery = QuizSubmission::query();
        if ($yearId || $sectionId) {
            $studentIds = Student::when($yearId, fn($q) => $q->where('year_id', $yearId))
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->pluck('id');
            $totalSubmissionsQuery->whereIn('student_id', $studentIds);
        }
        $totalSubmissions = $totalSubmissionsQuery->count();

        return view('dashboard.index', compact(
            'years', 'sections', 'sectionPerformance',
            'totalStudents', 'activeQuizzes', 'totalSubmissions',
            'yearId', 'sectionId'
        ));
    }
}