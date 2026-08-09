<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SiteContentBlock extends Model
{
    use HasUuids;

    protected $fillable = ['key', 'data'];

    protected $casts = ['data' => 'array'];

    public function translations()
    {
        return $this->hasMany(SiteContentBlockTranslation::class, 'block_id');
    }

    public function translation(string $locale = 'en')
    {
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', 'en');
    }
}
