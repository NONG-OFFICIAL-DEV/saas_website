<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    use HasUuids;

    protected $fillable = ['slug', 'icon', 'sort_order', 'is_published'];

    protected $casts = [
        'sort_order' => 'integer',
        'is_published' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(SolutionTranslation::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'solution_product')
            ->using(SolutionProduct::class)
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function translation(string $locale = 'en')
    {
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', 'en');
    }
}
