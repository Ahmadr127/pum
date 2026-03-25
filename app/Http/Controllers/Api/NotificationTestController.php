<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendFcmNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationTestController extends Controller
{
    /**
     * Send a test notification to the current user.
     * POST /api/test-notification
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string',
            'body'  => 'nullable|string',
        ]);

        $user = Auth::user();
        $tokens = $user->deviceTokens()->pluck('device_token')->toArray();

        if (empty($tokens)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User ini tidak memiliki device token yang terdaftar.',
            ], 422);
        }

        $title = $request->title ?? 'Test Notifikasi PUM';
        $body = $request->body ?? 'Ini adalah pesan percobaan dari sistem PUM Azra.';

        SendFcmNotification::dispatch($tokens, $title, $body, [
            'type' => 'test_notification',
            'sent_at' => now()->toDateTimeString(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Test notifikasi telah dikirim ke ' . count($tokens) . ' perangkat.',
        ]);
    }
}
