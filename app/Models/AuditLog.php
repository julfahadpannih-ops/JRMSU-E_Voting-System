<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'action',
        'actor_type',
        'actor_id',
        'actor_name',
        'details',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Decode JSON details automatically.
     */
    public function getDetailsDecodedAttribute(): array
    {
        return $this->details ? json_decode($this->details, true) : [];
    }
}
