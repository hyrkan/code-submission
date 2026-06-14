<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    protected $fillable = ['name', 'is_archived'];

    protected $casts = ['is_archived' => 'boolean'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }
}