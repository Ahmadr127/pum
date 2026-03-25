<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebasePingController extends Controller
{
    /**
     * Test Firebase connection and credential validity.
     * GET /api/firebase/ping  (PUBLIC — no auth required)
     */
    public function ping()
    {
        $credentialsPath = storage_path('app/firebase-auth.json');

        // 1. Check if the credentials file exists
        if (!file_exists($credentialsPath)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Firebase credentials file not found at: ' . $credentialsPath,
            ], 500);
        }

        // 2. Parse the credentials file to get project info
        $credentials = json_decode(file_get_contents($credentialsPath), true);
        $projectId   = $credentials['project_id'] ?? 'unknown';
        $clientEmail = $credentials['client_email'] ?? 'unknown';

        // 3. Try to instantiate the Firebase messaging service
        try {
            $messaging = Firebase::messaging();

            return response()->json([
                'status'       => 'ok',
                'project_id'   => $projectId,
                'client_email' => $clientEmail,
                'message'      => 'Firebase connection successful. Messaging service is ready.',
            ]);
        } catch (\Throwable $e) {
            Log::error('[Firebase Ping] Failed: ' . $e->getMessage());

            return response()->json([
                'status'       => 'error',
                'project_id'   => $projectId,
                'client_email' => $clientEmail,
                'message'      => 'Firebase messaging init failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
