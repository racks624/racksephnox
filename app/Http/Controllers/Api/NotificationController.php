<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);
        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $request->user()->unreadNotifications->count(),
        ]);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'success' => true,
            'count' => $request->user()->unreadNotifications->count(),
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    public function destroy(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();
        return response()->json(['success' => true, 'message' => 'Notification deleted']);
    }

    public function destroyAll(Request $request)
    {
        $request->user()->notifications()->delete();
        return response()->json(['success' => true, 'message' => 'All notifications deleted']);
    }

    public function preferences(Request $request)
    {
        $preferences = $request->user()->notification_preferences ?? [
            'email_deposit' => true,
            'email_investment' => true,
            'email_withdrawal' => true,
            'database_deposit' => true,
            'database_investment' => true,
            'database_withdrawal' => true,
            'broadcast_deposit' => true,
            'broadcast_investment' => true,
            'broadcast_withdrawal' => true,
        ];
        return response()->json(['success' => true, 'data' => $preferences]);
    }

    public function updatePreferences(Request $request)
    {
        $preferences = $request->only([
            'email_deposit', 'email_investment', 'email_withdrawal',
            'database_deposit', 'database_investment', 'database_withdrawal',
            'broadcast_deposit', 'broadcast_investment', 'broadcast_withdrawal',
        ]);
        $request->user()->notification_preferences = $preferences;
        $request->user()->save();
        return response()->json(['success' => true, 'message' => 'Preferences updated']);
    }
}
