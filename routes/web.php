<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\StudentProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->is_student) {
            return redirect()->route('student.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('student.login');
});

// Employee/Admin routes - protected by auth + is_employee middleware
Route::middleware(['auth', 'is_employee'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('employees', EmployeeController::class);
    Route::resource('years', YearController::class);
    Route::patch('years/{year}/toggle-archive', [YearController::class, 'toggleArchive'])->name('years.toggle-archive');
    Route::resource('sections', SectionController::class);
    Route::patch('sections/{section}/toggle-archive', [SectionController::class, 'toggleArchive'])->name('sections.toggle-archive');
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/quizzes', [QuizController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::get('/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::patch('/quizzes/{quiz}/toggle-archive', [QuizController::class, 'toggleArchive'])->name('quizzes.toggle-archive');
    Route::patch('/quizzes/{quiz}/toggle-publish', [QuizController::class, 'togglePublish'])->name('quizzes.toggle-publish');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin: Students management
    Route::get('/students', [\App\Http\Controllers\AdminStudentController::class, 'index'])->name('admin.students.index');
    Route::get('/students/{student}/analytics', [\App\Http\Controllers\AdminStudentController::class, 'analytics'])->name('admin.students.analytics');
    Route::get('/api/sections', [\App\Http\Controllers\AdminStudentController::class, 'getSections'])->name('admin.students.sections');

    // Admin: Quiz Submissions (3-level hierarchy)
    Route::get('/submissions', [\App\Http\Controllers\AdminSubmissionController::class, 'index'])->name('admin.submissions.index');
    Route::get('/submissions/quiz/{quiz}', [\App\Http\Controllers\AdminSubmissionController::class, 'quizStudents'])->name('admin.submissions.quiz-students');
    Route::get('/submissions/quiz/{quiz}/student/{student}', [\App\Http\Controllers\AdminSubmissionController::class, 'studentDetail'])->name('admin.submissions.student-detail');
    Route::get('/submissions/detail/{submission}', [\App\Http\Controllers\AdminSubmissionController::class, 'show'])->name('admin.submissions.show');
    Route::patch('/submissions/detail/{submission}', [\App\Http\Controllers\AdminSubmissionController::class, 'update'])->name('admin.submissions.update');

    // Admin: Notifications
    Route::post('/notifications/dismiss', function () {
        session(['notifications_last_seen' => now()]);
        return response()->json(['success' => true]);
    })->name('admin.notifications.dismiss');

    // Admin: Settings
    Route::get('/settings', [\App\Http\Controllers\AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings/ai', [\App\Http\Controllers\AdminSettingController::class, 'saveAi'])->name('admin.settings.save-ai');
    Route::match(['get', 'post'], '/settings/ai/test', [\App\Http\Controllers\AdminSettingController::class, 'testAi'])->name('admin.settings.test-ai');
    Route::post('/settings/ai/balance', [\App\Http\Controllers\AdminSettingController::class, 'checkBalance'])->name('admin.settings.balance');
});

// Student routes - protected by auth + is_student middleware
Route::middleware('guest')->group(function () {
    Route::get('/student/login', [\App\Http\Controllers\StudentAuthController::class, 'create'])->name('student.login');
    Route::post('/student/login', [\App\Http\Controllers\StudentAuthController::class, 'store']);
    Route::get('/student/register', [\App\Http\Controllers\StudentRegistrationController::class, 'create'])->name('student.register');
    Route::post('/student/register', [\App\Http\Controllers\StudentRegistrationController::class, 'store']);
});

Route::middleware(['auth', 'is_student'])->group(function () {
    Route::get('/student/dashboard', [\App\Http\Controllers\StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/student/profile', [StudentProfileController::class, 'edit'])->name('student.profile.edit');
    Route::patch('/student/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');
    Route::get('/student/quizzes/{quiz}', [\App\Http\Controllers\StudentQuizController::class, 'show'])->name('student.quizzes.take');
    Route::post('/student/quizzes/{quiz}/submit', [\App\Http\Controllers\StudentQuizController::class, 'submit'])->name('student.quizzes.submit');
    Route::get('/student/quizzes/{quiz}/results', [\App\Http\Controllers\StudentQuizController::class, 'results'])->name('student.quizzes.results');
});

require __DIR__.'/auth.php';
