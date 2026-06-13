<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'department',
        'position',
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
