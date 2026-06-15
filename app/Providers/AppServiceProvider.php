<?php

namespace App\Providers;

use App\Models\QuizSubmission;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Share notification data with the master layout for admin users
        View::composer('main_layout.master', function ($view) {
            $notifCount = 0;
            $recentSubmissions = collect();

            if (auth()->check() && !auth()->user()->student) {
                $lastSeen = session('notifications_last_seen', now()->startOfDay());

                $notifCount = QuizSubmission::where('submitted_at', '>', $lastSeen)->count();

                if ($notifCount > 0) {
                    $recentSubmissions = QuizSubmission::with(['student', 'quiz'])
                        ->where('submitted_at', '>', $lastSeen)
                        ->orderBy('submitted_at', 'desc')
                        ->take(10)
                        ->get();
                }
            }

            $view->with('notifCount', $notifCount);
            $view->with('notifRecentSubmissions', $recentSubmissions);
        });
    }
}
