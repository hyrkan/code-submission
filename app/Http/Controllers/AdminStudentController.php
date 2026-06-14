<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Year;
use App\Models\Section;
use Illuminate\Http\Request;

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