<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StudentProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Employee/Admin routes - protected by auth + is_employee middleware
Route::middleware(['auth', 'is_employee'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('employees', EmployeeController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Student routes - protected by auth + is_student middleware
Route::middleware('guest')->group(function () {
    Route::get('/student/login', [\App\Http\Controllers\StudentAuthController::class, 'create'])->name('student.login');
    Route::post('/student/login', [\App\Http\Controllers\StudentAuthController::class, 'store']);
});

Route::middleware(['auth', 'is_student'])->group(function () {
    Route::get('/student/dashboard', [\App\Http\Controllers\StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/student/profile', [StudentProfileController::class, 'edit'])->name('student.profile.edit');
    Route::patch('/student/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');
});

require __DIR__.'/auth.php';
