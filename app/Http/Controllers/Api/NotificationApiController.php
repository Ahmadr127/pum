<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationApiController extends Controller
{
    /**
     * Get list of notifications for the authenticated user.
     * GET /api/notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => $notifications,
            'unread_count' => Notification::where('user_id', $user->id)->unread()->count()
        ]);
    }

    /**
     * Mark a specific notification as read.
     * PATCH /api/notifications/{id}/read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Notifikasi ditandai sebagai sudah dibaca.',
        ]);
    }

    /**
     * Mark all notifications as read for the user.
     * POST /api/notifications/read-all
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Semua notifikasi ditandai sebagai sudah dibaca.',
        ]);
    }
}
