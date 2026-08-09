<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductScreenshotTranslation extends Model
{
    use HasUuids;

    protected $fillable = ['screenshot_id', 'locale', 'alt_text', 'caption'];

    public function screenshot()
    {
        return $this->belongsTo(ProductScreenshot::class, 'screenshot_id');
    }
}
