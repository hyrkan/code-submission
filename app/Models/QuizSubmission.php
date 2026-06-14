<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    protected $fillable = [
        'quiz_id',
        'quiz_item_id',
        'student_id',
        'code',
        'language',
        'status',
        'score',
        'feedback',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function quizItem()
    {
        return $this->belongsTo(QuizItem::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}