<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OnboardingSubmission extends Model
{
    use HasUuids;

    protected $fillable = [
        'product_slug',
        'business_name',
        'owner_first_name',
        'owner_last_name',
        'email',
        'phone',
        'plan_code',
        'status',
        'error_message',
    ];
}
