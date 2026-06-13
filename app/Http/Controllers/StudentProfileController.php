<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class StudentProfileController extends Controller
{
    /**
     * Display the student's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $student = $user->student;

        return view('student.profile.edit', compact('user', 'student'));
    }

    /**
     * Update the student's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $student = $user->student;

        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'student_number' => 'nullable|string|max:255',
            'course'         => 'nullable|string|max:255',
        ]);

        $user->fill([
            'name'  => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $student->update([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'student_number' => $request->student_number,
            'course'         => $request->course,
        ]);

        return Redirect::route('student.profile.edit')->with('status', 'profile-updated');
    }
}
