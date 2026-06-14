<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $activeQuizzes = Quiz::where('is_published', true)->where('is_archived', false)->count();
        $totalStudents = Student::count();
        $totalSubmissions = QuizSubmission::count();
        $recentSubmissions = QuizSubmission::with(['student', 'quiz'])
            ->latest('submitted_at')
            ->take(10)
            ->get();

        return view('dashboard.index', compact(
            'activeQuizzes',
            'totalStudents',
            'totalSubmissions',
            'recentSubmissions'
        ));
    }
}