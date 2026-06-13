<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentAuthController extends Controller
{
    /**
     * Display the student login view.
     */
    public function create(): View
    {
        return view('student.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Check if user is a student
        if (!$user->student) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('student.login')->withErrors([
                'email' => 'These credentials do not match our student records.',
            ]);
        }

        return redirect()->intended(route('student.dashboard', absolute: false));
    }
}
