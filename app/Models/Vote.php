<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    // Only track created_at; skip updated_at (votes are immutable)
    const UPDATED_AT = null;

    protected $fillable = ['voter_id', 'position_id', 'candidate_id'];

    protected $casts = ['created_at' => 'datetime'];

    public function voter(): BelongsTo
    {
        return $this->belongsTo(Voter::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
