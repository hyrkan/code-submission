<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'is_archived'];

    protected $casts = ['is_archived' => 'boolean'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }
}
