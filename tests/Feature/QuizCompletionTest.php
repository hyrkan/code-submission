<?php

use App\Models\User;
use App\Models\Student;
use App\Models\Quiz;
use App\Models\QuizItem;
use App\Models\QuizSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('blocks submissions once all items are completed', function () {
    // 1. Create a student user
    $user = User::factory()->create();
    $student = Student::create([
        'user_id' => $user->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    // 2. Create a published quiz and quiz items
    $quiz = Quiz::create([
        'name' => 'Coding Challenge',
        'language' => 'python',
        'total_points' => 20,
        'is_published' => true,
        'created_by' => $user->id,
    ]);

    $quizItem1 = QuizItem::create([
        'quiz_id' => $quiz->id,
        'title' => 'Challenge #1',
        'points' => 10,
        'sort_order' => 1,
    ]);

    $quizItem2 = QuizItem::create([
        'quiz_id' => $quiz->id,
        'title' => 'Challenge #2',
        'points' => 10,
        'sort_order' => 2,
    ]);

    // 3. Create submissions for both items (completing the quiz)
    QuizSubmission::create([
        'quiz_id' => $quiz->id,
        'quiz_item_id' => $quizItem1->id,
        'student_id' => $student->id,
        'code' => 'def add(): pass',
        'language' => 'python',
        'status' => 'submitted',
        'submitted_at' => now(),
    ]);

    QuizSubmission::create([
        'quiz_id' => $quiz->id,
        'quiz_item_id' => $quizItem2->id,
        'student_id' => $student->id,
        'code' => 'def subtract(): pass',
        'language' => 'python',
        'status' => 'submitted',
        'submitted_at' => now(),
    ]);

    // 4. Try to access the quiz page
    $response = $this->actingAs($user)
        ->get(route('student.quizzes.take', $quiz->id));

    // Assert redirect to dashboard with error flash message
    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error', 'You have already completed this quiz. Please wait for your results.');

    // 5. Try to submit again
    $submitResponse = $this->actingAs($user)
        ->postJson(route('student.quizzes.submit', $quiz->id), [
            'quiz_item_id' => $quizItem1->id,
            'code' => 'def modified(): pass',
            'language' => 'python',
        ]);

    // Assert blocked with 403 Forbidden
    $submitResponse->assertStatus(403)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'You have already completed this quiz. Retakes are not allowed.');
});
