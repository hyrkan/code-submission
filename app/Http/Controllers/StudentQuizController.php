<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;

class StudentQuizController extends Controller
{
    /**
     * Show the quiz for the student to take.
     */
    public function show(string $quizId)
    {
        $student = auth()->user()->student;

        $quiz = Quiz::with(['items' => function ($query) {
            $query->orderBy('sort_order');
        }, 'year', 'section', 'creator'])
            ->where('is_published', true)
            ->where(function ($query) {
                $query->where('is_archived', false)
                      ->orWhereNull('is_archived');
            })
            ->findOrFail($quizId);

        // Check if the student is allowed to access this quiz
        if ($quiz->year_id && $quiz->year_id !== $student->year_id) {
            abort(403, 'You are not authorized to access this quiz.');
        }
        if ($quiz->section_id && $quiz->section_id !== $student->section_id) {
            abort(403, 'You are not authorized to access this quiz.');
        }

        // Check if the student has already completed all items in this quiz
        $itemsCount = $quiz->items()->count();
        $submissionsCount = QuizSubmission::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->count();

        if ($itemsCount > 0 && $submissionsCount >= $itemsCount) {
            return redirect()->route('student.quizzes.results', $quiz->id);
        }

        return view('student.quizzes.take', compact('quiz'));
    }

    /**
     * Handle code submission for a quiz item.
     */
    public function submit(Request $request, string $quizId)
    {
        $request->validate([
            'quiz_item_id' => 'required|exists:quiz_items,id',
            'code' => 'required|string',
            'language' => 'nullable|string|max:50',
        ]);

        $student = auth()->user()->student;

        $quiz = Quiz::where('is_published', true)
            ->where(function ($query) {
                $query->where('is_archived', false)
                      ->orWhereNull('is_archived');
            })
            ->findOrFail($quizId);

        // Verify the quiz item belongs to this quiz
        $quizItem = $quiz->items()->findOrFail($request->quiz_item_id);

        // Check if the student has already completed all items in this quiz
        $itemsCount = $quiz->items()->count();
        $submissionsCount = QuizSubmission::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->count();

        if ($itemsCount > 0 && $submissionsCount >= $itemsCount) {
            return response()->json([
                'success' => false,
                'message' => 'You have already completed this quiz. Retakes are not allowed.',
            ], 403);
        }

        // Save the submission
        $submission = QuizSubmission::updateOrCreate(
            [
                'quiz_id' => $quiz->id,
                'quiz_item_id' => $quizItem->id,
                'student_id' => $student->id,
            ],
            [
                'code' => $request->code,
                'language' => $request->language ?? 'python',
                'status' => 'submitted',
                'submitted_at' => now(),
            ]
        );

        // Run AI analysis & grading
        $feedback = null;
        $score = null;
        $status = 'submitted';

        $ai = new \App\Services\AiService();
        if ($ai->isConfigured()) {
            $analysis = $ai->analyzeCode(
                $submission->code,
                $submission->language,
                $quizItem->description ?? $quiz->description ?? 'Solve the challenge.',
                $quizItem->expected_output,
                $quizItem->grading_criteria,
                $quizItem->points
            );

            if ($analysis) {
                // Parse score recommendation from [SCORE]number[/SCORE]
                if (preg_match('/\[SCORE\](\d+)\[\/SCORE\]/i', $analysis, $matches)) {
                    $score = (int) $matches[1];
                    // Strip the score tag from clean feedback text
                    $feedback = trim(str_replace($matches[0], '', $analysis));
                } else {
                    $feedback = $analysis;
                }

                // Determine passed/failed status based on 50% score threshold
                $passingScore = ceil($quizItem->points / 2);
                if ($score !== null) {
                    $status = $score >= $passingScore ? 'passed' : 'failed';
                } else {
                    $status = 'graded';
                }

                $submission->update([
                    'score' => $score,
                    'feedback' => $feedback,
                    'status' => $status,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Code submitted successfully!',
            'data' => [
                'quiz_id' => $quiz->id,
                'quiz_item_id' => $quizItem->id,
                'language' => $request->language ?? 'python',
                'submitted_at' => $submission->submitted_at->toISOString(),
                'results_url' => route('student.quizzes.results', $quiz->id),
            ],
        ]);
    }

    /**
     * Show the quiz results with grades, assessments, and code review for the student.
     */
    public function results(string $quizId)
    {
        $student = auth()->user()->student;

        $quiz = Quiz::with(['items' => function ($query) {
            $query->orderBy('sort_order');
        }, 'year', 'section', 'creator'])
            ->findOrFail($quizId);

        // Get all submissions for this student on this quiz
        $submissions = QuizSubmission::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->with('quizItem')
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->keyBy('quiz_item_id');

        // If no submissions found, redirect back
        if ($submissions->isEmpty()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You have not submitted this quiz yet.');
        }

        // Calculate totals
        $totalScore = $submissions->whereNotNull('score')->sum('score');
        $totalPoints = $quiz->total_points;
        $submittedCount = $submissions->count();
        $totalItems = $quiz->items->count();
        $percentage = $totalPoints > 0 ? round(($totalScore / $totalPoints) * 100, 1) : 0;

        return view('student.quizzes.results', compact(
            'quiz',
            'submissions',
            'totalScore',
            'totalPoints',
            'submittedCount',
            'totalItems',
            'percentage'
        ));
    }
}
