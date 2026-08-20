<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OnboardingSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_slug' => $this->product_slug,
            'business_name' => $this->business_name,
            'owner_first_name' => $this->owner_first_name,
            'owner_last_name' => $this->owner_last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'plan_code' => $this->plan_code,
            'status' => $this->status,
            'error_message' => $this->error_message,
            'created_at' => $this->created_at,
        ];
    }
}
