<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        // Get all published quizzes matching student's year/section
        $allQuizzes = Quiz::with(['year', 'section', 'creator', 'items'])
            ->where('is_published', true)
            ->where(function ($query) {
                $query->where('is_archived', false)
                      ->orWhereNull('is_archived');
            })
            ->where(function ($query) use ($student) {
                $query->whereNull('year_id')
                      ->orWhere('year_id', $student->year_id);
            })
            ->where(function ($query) use ($student) {
                $query->whereNull('section_id')
                      ->orWhere('section_id', $student->section_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get quiz IDs the student has already submitted to
        $submittedQuizIds = QuizSubmission::where('student_id', $student->id)
            ->distinct()
            ->pluck('quiz_id')
            ->toArray();

        $now = Carbon::now();

        // Categorize quizzes
        $completedQuizzes = $allQuizzes->filter(function ($quiz) use ($submittedQuizIds) {
            return in_array($quiz->id, $submittedQuizIds);
        });

        $overdueQuizzes = $allQuizzes->filter(function ($quiz) use ($submittedQuizIds, $now) {
            return !in_array($quiz->id, $submittedQuizIds)
                && $quiz->scheduled_at
                && $quiz->scheduled_at->lt($now);
        });

        $activeQuizzes = $allQuizzes->filter(function ($quiz) use ($submittedQuizIds, $now) {
            return !in_array($quiz->id, $submittedQuizIds)
                && (!$quiz->scheduled_at || $quiz->scheduled_at->gte($now));
        });

        return view('student.dashboard.index', compact(
            'completedQuizzes',
            'overdueQuizzes',
            'activeQuizzes'
        ));
    }
}