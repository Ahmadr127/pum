<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FcmTokenController extends Controller
{
    /**
     * Store a new FCM token for the user.
     * POST /api/fcm-token
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string|min:50',
            'device_type'  => 'nullable|string|in:android,ios,web',
        ]);

        $user = Auth::user();

        // Use updateOrCreate to avoid duplicates for the same token
        $incomingToken = trim((string) $request->device_token);
        $isPlaceholder = $incomingToken === 'YOUR_FCM_TOKEN_HERE';
        $tokenShaPrefix = substr(hash('sha256', $incomingToken), 0, 12);

        // Guard rails: never store obvious placeholders / bogus tokens.
        if ($isPlaceholder) {
            return response()->json([
                'status'  => 'error',
                'message' => 'device_token masih placeholder. Ambil token asli dari Firebase Messaging di aplikasi.',
            ], 422);
        }

        $deviceToken = UserDeviceToken::updateOrCreate(
            ['device_token' => $incomingToken],
            [
                'user_id'     => $user->id,
                'device_type' => $request->device_type,
            ]
        );

        // Runtime evidence: detect placeholder token being stored.
        Log::info('FCM token registered (pum)', [
            'user_id' => $user->id,
            'device_type' => $request->device_type,
            'token_id' => $deviceToken->id,
            'incoming_is_placeholder_exact' => $isPlaceholder,
            'incoming_token_sha256_prefix' => $tokenShaPrefix,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'FCM Token berhasil didaftarkan.',
        ]);
    }

    /**
     * Remove an FCM token (e.g., on logout).
     * DELETE /api/fcm-token
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
        ]);

        $token = trim((string) $request->device_token);
        $deleted = UserDeviceToken::where('user_id', Auth::id())
            ->where('device_token', $token)
            ->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'FCM Token berhasil dihapus.',
            'deleted' => $deleted,
        ]);
    }
}
