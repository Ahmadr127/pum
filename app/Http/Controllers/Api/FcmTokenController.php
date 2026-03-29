<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDeviceToken;
use App\Support\FcmTokenFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FcmTokenController extends Controller
{
    /**
     * Store a new FCM token for the user.
     *
     * Multi-device: satu akun boleh banyak token (setiap perangkat punya FCM token unik).
     * Baris diidentifikasi oleh `device_token` unik; notifikasi mengirim ke semua token user.
     *
     * POST /api/fcm-token
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string|min:50|max:500',
            'device_type' => 'nullable|string|in:android,ios,web',
        ]);

        $user = Auth::user();

        $incomingToken = trim((string) $request->device_token);
        $isPlaceholder = $incomingToken === 'YOUR_FCM_TOKEN_HERE';
        $shaPrefix = FcmTokenFormatter::sha256Prefix($incomingToken);

        if ($isPlaceholder) {
            return response()->json([
                'status' => 'error',
                'message' => 'device_token masih placeholder. Ambil token asli dari Firebase Messaging di aplikasi.',
            ], 422);
        }

        $deviceToken = UserDeviceToken::updateOrCreate(
            ['device_token' => $incomingToken],
            [
                'user_id' => $user->id,
                'device_type' => $request->device_type,
            ]
        );

        $tokensRegistered = UserDeviceToken::where('user_id', $user->id)->count();

        Log::info('FCM token registered (pum)', [
            'user_id' => $user->id,
            'device_type' => $request->device_type,
            'token_id' => $deviceToken->id,
            'tokens_registered_for_user' => $tokensRegistered,
            'incoming_is_placeholder_exact' => $isPlaceholder,
            'incoming_token_sha256_prefix' => $shaPrefix,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'FCM Token berhasil didaftarkan.',
            'data' => [
                'token_id' => $deviceToken->id,
                'token' => $incomingToken,
                'token_preview' => FcmTokenFormatter::preview($incomingToken),
                'sha256_prefix' => $shaPrefix,
                'tokens_registered_for_user' => $tokensRegistered,
            ],
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
            'status' => 'success',
            'message' => 'FCM Token berhasil dihapus.',
            'deleted' => $deleted,
        ]);
    }
}
