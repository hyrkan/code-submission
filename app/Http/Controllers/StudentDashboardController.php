<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $quizzes = Quiz::with(['year', 'section', 'creator', 'items'])
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

        return view('student.dashboard.index', compact('quizzes'));
    }
}
