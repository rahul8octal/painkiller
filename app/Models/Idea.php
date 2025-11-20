<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idea extends Model
{

    protected $table = 'ideas';

    protected $fillable = ['problem_id', 'structured', 'solution', 'opportunities', 'complexity', 'review_status'];

    protected $casts = ['opportunities' => 'array'];


    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }
}
