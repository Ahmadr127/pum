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
        $credentialsJson = env('FIREBASE_CREDENTIALS_JSON');
        $credentialsPath = env('FIREBASE_CREDENTIALS', storage_path('app/firebase-auth.json'));

        // Determine which mode is being used
        $mode       = $credentialsJson ? 'json_string' : 'file_path';
        $projectId  = 'unknown';
        $clientEmail = 'unknown';

        if ($mode === 'json_string') {
            // Parse the JSON string from env
            $decoded = json_decode($credentialsJson, true);
            if (!$decoded) {
                return response()->json([
                    'status'  => 'error',
                    'mode'    => $mode,
                    'message' => 'FIREBASE_CREDENTIALS_JSON is set but contains invalid JSON.',
                ], 500);
            }
            $projectId   = $decoded['project_id']   ?? 'unknown';
            $clientEmail = $decoded['client_email']  ?? 'unknown';
        } else {
            // File path mode
            if (!file_exists($credentialsPath)) {
                return response()->json([
                    'status'  => 'error',
                    'mode'    => $mode,
                    'path'    => $credentialsPath,
                    'message' => 'Credentials file not found. Upload firebase-auth.json to storage/app/ OR set FIREBASE_CREDENTIALS_JSON in .env instead.',
                ], 500);
            }
            $decoded     = json_decode(file_get_contents($credentialsPath), true);
            $projectId   = $decoded['project_id']   ?? 'unknown';
            $clientEmail = $decoded['client_email']  ?? 'unknown';
        }

        // Try to init messaging service
        try {
            Firebase::messaging();

            return response()->json([
                'status'       => 'ok',
                'mode'         => $mode,
                'project_id'   => $projectId,
                'client_email' => $clientEmail,
                'message'      => 'Firebase connection successful. Messaging service is ready.',
            ]);
        } catch (\Throwable $e) {
            Log::error('[Firebase Ping] Failed: ' . $e->getMessage());

            return response()->json([
                'status'       => 'error',
                'mode'         => $mode,
                'project_id'   => $projectId,
                'client_email' => $clientEmail,
                'message'      => 'Firebase messaging init failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
