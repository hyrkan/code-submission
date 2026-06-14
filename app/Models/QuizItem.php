<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizItem extends Model
{
    protected $fillable = [
        'quiz_id',
        'title',
        'description',
        'difficulty',
        'sample_input',
        'sample_output',
        'expected_output',
        'coding_standards',
        'grading_criteria',
        'points',
        'sort_order',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}