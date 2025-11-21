<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idea extends Model
{

    protected $table = 'ideas';

    protected $fillable = [
        'problem_id',
        'structured',
        'solution',
        'opportunities',
        'complexity',
        'review_status',
        'revenue_potential',
        'market_validation',
        'creative_assets'
    ];

    protected $casts = [
        'opportunities' => 'array',
        'revenue_potential' => 'array',
        'market_validation' => 'array',
        'creative_assets' => 'array'
    ];


    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }
}
