<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\Year;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudentRegistrationController extends Controller
{
    /**
     * Display the student registration view.
     */
    public function create(): View
    {
        $years = Year::active()->orderBy('name')->get();
        $sections = Section::active()->with('year')->orderBy('name')->get();
        return view('student.auth.register', compact('years', 'sections'));
    }

    /**
     * Handle an incoming student registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'student_number' => ['required', 'string', 'max:255'],
            'course' => ['required', 'string', 'max:255'],
            'year_id' => ['required', 'exists:years,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Student::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'student_number' => $request->student_number,
            'course' => $request->course,
            'year_id' => $request->year_id,
            'section_id' => $request->section_id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('student.dashboard', absolute: false));
    }
}