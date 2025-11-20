<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{

    protected $table = 'problems';

    protected $fillable = ['source_id', 'external_id', 'title', 'body', 'url', 'author', 'votes', 'tags', 'signals', 'scores', 'total_score', 'status'];


    protected $casts = ['tags' => 'array', 'signals' => 'array', 'scores' => 'array'];



    public function source()
    {
        return $this->belongsTo(Source::class);
    }
    public function ideas()
    {
        return $this->hasMany(Idea::class);
    }
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
