<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductFaqTranslation extends Model
{
    use HasUuids;

    protected $fillable = ['product_faq_id', 'locale', 'question', 'answer'];

    public function faq()
    {
        return $this->belongsTo(ProductFaq::class, 'product_faq_id');
    }
}
