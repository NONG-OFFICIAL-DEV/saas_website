<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreOnboardingProvisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public onboarding — no platform account exists yet
    }

    public function rules(): array
    {
        $isSmartStore = $this->input('product_slug') === 'nexstack-pos';

        return [
            'product_slug' => ['required', 'string', Rule::in(['nexstack-pos', 'studio-management'])],
            // Only meaningful for Studio today — Smart Store's self-service
            // endpoint always assigns its free plan regardless of this value.
            'plan_code' => ['nullable', 'string', 'max:100'],
            'business_name' => ['required', 'string', 'max:150'],
            // Smart Store's own tenant table requires a real business_type_id;
            // Studio has no equivalent concept.
            'business_type_id' => [$isSmartStore ? 'required' : 'nullable', 'uuid'],
            'owner_first_name' => ['required', 'string', 'max:80'],
            'owner_last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }
}
