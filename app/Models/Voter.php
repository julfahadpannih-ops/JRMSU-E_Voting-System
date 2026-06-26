<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voter extends Authenticatable
{
    protected $fillable = [
        'student_id', 'name', 'course', 'password', 'has_voted', 'is_approved',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'has_voted'   => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
