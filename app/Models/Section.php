<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'year_id', 'is_archived'];

    protected $casts = ['is_archived' => 'boolean'];

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }
}
