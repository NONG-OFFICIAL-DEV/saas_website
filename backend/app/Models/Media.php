<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasUuids;

    protected $fillable = ['disk', 'path', 'url', 'filename', 'mime_type', 'size', 'uploaded_by'];

    protected $casts = ['size' => 'integer'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
