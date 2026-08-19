<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SolutionTranslation extends Model
{
    use HasUuids;

    protected $fillable = ['solution_id', 'locale', 'name', 'tagline', 'description'];

    public function solution()
    {
        return $this->belongsTo(Solution::class);
    }
}
