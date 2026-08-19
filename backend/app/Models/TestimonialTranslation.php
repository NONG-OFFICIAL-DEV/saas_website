<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TestimonialTranslation extends Model
{
    use HasUuids;

    protected $fillable = ['testimonial_id', 'locale', 'quote'];

    public function testimonial()
    {
        return $this->belongsTo(Testimonial::class);
    }
}
