<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = ['action', 'details', 'auditable_type', 'auditable_id', 'user_id'];

    protected $casts = ['details' => 'array'];

    
    public function auditable()
    {
        return $this->morphTo();
    }
}
