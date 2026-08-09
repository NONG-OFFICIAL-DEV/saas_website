<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SiteContentBlockTranslation extends Model
{
    use HasUuids;

    protected $fillable = ['block_id', 'locale', 'content'];

    protected $casts = ['content' => 'array'];

    public function block()
    {
        return $this->belongsTo(SiteContentBlock::class, 'block_id');
    }
}
