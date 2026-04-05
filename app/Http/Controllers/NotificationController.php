<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->notifications();

        if ($request->type === 'unread') {
            $query->whereNull('read_at');
        } elseif ($request->type === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }

    public function destroyAll()
    {
        Auth::user()->notifications()->delete();
        return back()->with('success', 'All notifications cleared.');
    }

    public function preferences()
    {
        $user = Auth::user();
        $preferences = $user->notification_preferences ?? [
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
        return view('notifications.preferences', compact('preferences'));
    }

    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        $preferences = $request->only([
            'email_deposit', 'email_investment', 'email_withdrawal',
            'database_deposit', 'database_investment', 'database_withdrawal',
            'broadcast_deposit', 'broadcast_investment', 'broadcast_withdrawal',
        ]);
        $user->notification_preferences = $preferences;
        $user->save();
        return back()->with('success', 'Preferences updated.');
    }
}
