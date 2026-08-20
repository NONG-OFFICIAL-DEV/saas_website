<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OnboardingSubmissionResource;
use App\Models\OnboardingSubmission;
use Illuminate\Http\Request;

class OnboardingSubmissionController extends Controller
{
    /** Signup activity log across every product, newest first. */
    public function index(Request $request)
    {
        $submissions = OnboardingSubmission::orderByDesc('created_at')->get();

        return response()->json([
            'success' => true,
            'data' => OnboardingSubmissionResource::collection($submissions),
        ]);
    }

    public function destroy(OnboardingSubmission $onboarding_submission)
    {
        $onboarding_submission->delete();

        return response()->json(['success' => true, 'message' => 'Submission removed.']);
    }
}
