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
    public function sendTest(Request $request, \App\Services\NotificationService $notificationService)
    {
        $request->validate([
            'title' => 'nullable|string',
            'body'  => 'nullable|string',
        ]);

        $user = Auth::user();
        
        $title = $request->title ?? 'Test Notifikasi PUM';
        $body = $request->body ?? 'Ini adalah pesan percobaan dari sistem PUM Azra.';

        $notificationService->notifyUsers(collect([$user]), $title, $body, [
            'type' => 'test_notification',
            'sent_at' => now()->toDateTimeString(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Test notifikasi telah dikirim ke perangkat Anda.',
        ]);
    }
}
