<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'name',
        'description',
        'language',
        'year_id',
        'section_id',
        'created_by',
        'time_limit',
        'total_points',
        'is_published',
        'is_archived',
        'scheduled_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_archived' => 'boolean',
        'scheduled_at' => 'datetime',
    ];

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(QuizItem::class)->orderBy('sort_order');
    }

    public function submissions()
    {
        return $this->hasMany(QuizSubmission::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}