<?php

namespace App\Services\Onboarding;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Maps the platform's generic onboarding payload onto Studio's own,
 * already-live, unauthenticated tenant-registration endpoint. Studio owns
 * its tenant/auth/billing entirely — this only ever calls its public API,
 * never touches its database directly.
 */
class StudioProvisioningAdapter
{
    public function provision(array $data): array
    {
        $baseUrl = config('services.studio.base_url');

        try {
            $response = Http::timeout(15)->post("{$baseUrl}/api/v1/auth/register", [
                'studio_name' => $data['business_name'],
                'owner_name' => trim("{$data['owner_first_name']} {$data['owner_last_name']}"),
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'] ?? $data['password'],
                'plan_code' => $data['plan_code'] ?? null,
            ]);
        } catch (ConnectionException) {
            return [
                'success' => false,
                'status' => 503,
                'message' => 'Studio is temporarily unreachable. Please try again shortly.',
            ];
        }

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Your Studio workspace is ready.',
                'login_url' => config('services.studio.login_url'),
            ];
        }

        return [
            'success' => false,
            'status' => $response->status(),
            'message' => $response->json('message') ?? 'Studio registration failed.',
            'errors' => $response->json('meta.errors') ?? $response->json('errors'),
        ];
    }
}
