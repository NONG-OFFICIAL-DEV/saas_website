<?php

namespace App\Services\Onboarding;

use Illuminate\Support\Facades\Http;

/**
 * Maps the platform's generic onboarding payload onto Smart Store's own,
 * already-live, unauthenticated business-registration endpoint. Smart Store
 * owns its tenant/auth/billing entirely — this only ever calls its public
 * API, never touches its database directly.
 */
class SmartStoreProvisioningAdapter
{
    public function provision(array $data): array
    {
        $baseUrl = config('services.smart_store.base_url');

        $response = Http::timeout(15)->post("{$baseUrl}/api/v1/public/business-register", [
            'owner_first_name' => $data['owner_first_name'],
            'owner_last_name' => $data['owner_last_name'],
            'owner_email' => $data['email'],
            'owner_password' => $data['password'],
            'owner_phone' => $data['phone'] ?? null,
            'name' => $data['business_name'],
            'business_type_id' => $data['business_type_id'],
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Your Smart Store workspace is ready.',
                'login_url' => config('services.smart_store.login_url'),
            ];
        }

        return [
            'success' => false,
            'status' => $response->status(),
            'message' => $response->json('message') ?? 'Smart Store registration failed.',
            'errors' => $response->json('errors'),
        ];
    }
}
