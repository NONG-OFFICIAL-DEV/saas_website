<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOnboardingProvisionRequest;
use App\Models\OnboardingSubmission;
use App\Services\Onboarding\SmartStoreProvisioningAdapter;
use App\Services\Onboarding\StudioProvisioningAdapter;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OnboardingController extends Controller
{
    public function __construct(
        private readonly StudioProvisioningAdapter $studio,
        private readonly SmartStoreProvisioningAdapter $smartStore,
    ) {
    }

    /**
     * Smart Store's setup step needs a real business_type_id — proxied
     * server-side so the browser never has to call another product's API
     * directly (avoids a CORS dependency on that product).
     */
    public function businessTypes(Request $request)
    {
        try {
            $response = Http::timeout(10)->get(config('services.smart_store.base_url') . '/api/v1/public/business-types');
        } catch (ConnectionException) {
            return response()->json(['success' => false, 'message' => 'Could not load business types.'], 502);
        }

        if (!$response->successful()) {
            return response()->json(['success' => false, 'message' => 'Could not load business types.'], 502);
        }

        return response()->json(['success' => true, 'data' => $response->json('data')]);
    }

    public function provision(StoreOnboardingProvisionRequest $request)
    {
        $data = $request->validated();
        $data['password_confirmation'] = $request->input('password_confirmation');

        $adapter = $data['product_slug'] === 'studio-management' ? $this->studio : $this->smartStore;

        $result = $adapter->provision($data);

        // Visibility log only — never store the password. The real tenant
        // (and its own auth) lives entirely inside whichever product's own
        // backend actually provisioned it.
        OnboardingSubmission::create([
            'product_slug' => $data['product_slug'],
            'business_name' => $data['business_name'],
            'owner_first_name' => $data['owner_first_name'],
            'owner_last_name' => $data['owner_last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'plan_code' => $data['plan_code'] ?? null,
            'status' => $result['success'] ? 'success' : 'failed',
            'error_message' => $result['success'] ? null : ($result['message'] ?? null),
        ]);

        return response()->json($result, $result['success'] ? 201 : ($result['status'] ?? 422));
    }
}
