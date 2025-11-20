<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $table = 'sources';

    protected $fillable = ['name', 'type', 'config', 'active'];

    protected $casts = ['config' => 'array'];


    public function problems()
    {
        return $this->hasMany(Problem::class);
    }
}
