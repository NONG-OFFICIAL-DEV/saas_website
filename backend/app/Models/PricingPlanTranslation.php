<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PricingPlanTranslation extends Model
{
    use HasUuids;

    protected $fillable = ['pricing_plan_id', 'locale', 'name', 'tagline', 'cta_label', 'features'];

    protected $casts = ['features' => 'array'];

    public function plan()
    {
        return $this->belongsTo(PricingPlan::class, 'pricing_plan_id');
    }
}
