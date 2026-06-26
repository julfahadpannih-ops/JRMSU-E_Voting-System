<?php

namespace App\Http\Traits;

use App\Models\AuditLog;
use Illuminate\Http\Request;

trait LogsActivity
{
    /**
     * Write one audit log entry.
     *
     * @param  Request  $request
     * @param  string   $action     e.g. 'voter_approved'
     * @param  string   $actorType  'admin' | 'voter'
     * @param  int      $actorId
     * @param  string   $actorName
     * @param  array    $details    any extra context
     */
    protected function auditLog(
        Request $request,
        string  $action,
        string  $actorType,
        ?int    $actorId,
        string  $actorName,
        array   $details = []
    ): void {
        AuditLog::create([
            'action'     => $action,
            'actor_type' => $actorType,
            'actor_id'   => $actorId,
            'actor_name' => $actorName,
            'details'    => $details ? json_encode($details) : null,
            'ip_address' => $request->ip(),
        ]);
    }
}
