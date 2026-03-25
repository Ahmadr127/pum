<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FcmTokenController extends Controller
{
    /**
     * Store a new FCM token for the user.
     * POST /api/fcm-token
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'device_type'  => 'nullable|string|in:android,ios,web',
        ]);

        $user = Auth::user();

        // Use updateOrCreate to avoid duplicates for the same token
        UserDeviceToken::updateOrCreate(
            ['device_token' => $request->device_token],
            [
                'user_id'     => $user->id,
                'device_type' => $request->device_type,
            ]
        );

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

        UserDeviceToken::where('device_token', $request->device_token)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'FCM Token berhasil dihapus.',
        ]);
    }
}
