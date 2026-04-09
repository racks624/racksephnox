<?php

namespace App\Http\Controllers;

use App\Events\NotificationRead;
use App\Events\NotificationSent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display notifications with advanced filtering
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Cache the unread count for quick access
        $unreadCount = Cache::remember("user_{$user->id}_unread_count", 60, function () use ($user) {
            return $user->unreadNotifications->count();
        });
        
        // Build query with filters
        $query = $user->notifications();
        
        // Filter by type (read/unread)
        if ($request->type === 'unread') {
            $query->whereNull('read_at');
        } elseif ($request->type === 'read') {
            $query->whereNotNull('read_at');
        }
        
        // Filter by category
        if ($request->category && in_array($request->category, ['deposit', 'investment', 'withdrawal', 'trading', 'system'])) {
            $query->where('data->category', $request->category);
        }
        
        // Sort options
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $notifications = $query->paginate(20);
        
        // Group notifications by date for better UI
        $groupedNotifications = $notifications->getCollection()->groupBy(function ($notification) {
            return $notification->created_at->format('Y-m-d');
        });
        
        $notifications->setCollection($groupedNotifications->flatten());
        
        // Get notification statistics
        $stats = [
            'total' => $user->notifications()->count(),
            'unread' => $unreadCount,
            'read' => $user->notifications()->whereNotNull('read_at')->count(),
            'by_category' => [
                'deposit' => $user->notifications()->where('data->category', 'deposit')->count(),
                'investment' => $user->notifications()->where('data->category', 'investment')->count(),
                'withdrawal' => $user->notifications()->where('data->category', 'withdrawal')->count(),
                'trading' => $user->notifications()->where('data->category', 'trading')->count(),
                'system' => $user->notifications()->where('data->category', 'system')->count(),
            ],
        ];
        
        return view('notifications.index', compact('notifications', 'stats', 'unreadCount'));
    }
    
    /**
     * Get notifications via API (for real-time updates)
     */
    public function apiIndex(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->data['message'] ?? 'New notification',
                    'icon' => $notification->data['icon'] ?? '🔔',
                    'category' => $notification->data['category'] ?? 'system',
                    'read' => !is_null($notification->read_at),
                    'time_ago' => $notification->created_at->diffForHumans(),
                    'created_at' => $notification->created_at->toIso8601String(),
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $user->unreadNotifications->count(),
        ]);
    }
    
    /**
     * Mark a single notification as read with event broadcast
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
            
            // Broadcast the read event for real-time updates
            broadcast(new NotificationRead($notification, Auth::user()))->toOthers();
            
            // Clear the unread count cache
            Cache::forget("user_" . Auth::id() . "_unread_count");
        }
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notification marked as read.']);
        }
        
        return back()->with('success', 'Notification marked as read.');
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications->count();
        
        if ($count > 0) {
            $user->unreadNotifications->markAsRead();
            
            // Clear the unread count cache
            Cache::forget("user_" . Auth::id() . "_unread_count");
            
            // Log the action
            Log::info("User {$user->id} marked {$count} notifications as read");
        }
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => "{$count} notifications marked as read."]);
        }
        
        return back()->with('success', "{$count} notifications marked as read.");
    }
    
    /**
     * Delete a single notification
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $wasUnread = is_null($notification->read_at);
        $notification->delete();
        
        if ($wasUnread) {
            Cache::forget("user_" . Auth::id() . "_unread_count");
        }
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notification deleted.']);
        }
        
        return back()->with('success', 'Notification deleted.');
    }
    
    /**
     * Delete all notifications
     */
    public function destroyAll()
    {
        $user = Auth::user();
        $count = $user->notifications()->count();
        $user->notifications()->delete();
        
        Cache::forget("user_" . Auth::id() . "_unread_count");
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => "{$count} notifications cleared."]);
        }
        
        return back()->with('success', "{$count} notifications cleared.");
    }
    
    /**
     * Delete notifications by category
     */
    public function destroyByCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|in:deposit,investment,withdrawal,trading,system',
        ]);
        
        $user = Auth::user();
        $count = $user->notifications()
            ->where('data->category', $request->category)
            ->delete();
        
        Cache::forget("user_" . Auth::id() . "_unread_count");
        
        return back()->with('success', "{$count} {$request->category} notifications cleared.");
    }
    
    /**
     * Show notification preferences
     */
    public function preferences()
    {
        $user = Auth::user();
        $preferences = $user->notification_preferences ?? [
            'email_deposit' => true,
            'email_investment' => true,
            'email_withdrawal' => true,
            'email_trading' => true,
            'database_deposit' => true,
            'database_investment' => true,
            'database_withdrawal' => true,
            'database_trading' => true,
            'broadcast_deposit' => true,
            'broadcast_investment' => true,
            'broadcast_withdrawal' => true,
            'broadcast_trading' => true,
            'daily_digest' => false,
            'weekly_report' => true,
        ];
        
        return view('notifications.preferences', compact('preferences'));
    }
    
    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'email_deposit' => 'sometimes|boolean',
            'email_investment' => 'sometimes|boolean',
            'email_withdrawal' => 'sometimes|boolean',
            'email_trading' => 'sometimes|boolean',
            'database_deposit' => 'sometimes|boolean',
            'database_investment' => 'sometimes|boolean',
            'database_withdrawal' => 'sometimes|boolean',
            'database_trading' => 'sometimes|boolean',
            'broadcast_deposit' => 'sometimes|boolean',
            'broadcast_investment' => 'sometimes|boolean',
            'broadcast_withdrawal' => 'sometimes|boolean',
            'broadcast_trading' => 'sometimes|boolean',
            'daily_digest' => 'sometimes|boolean',
            'weekly_report' => 'sometimes|boolean',
        ]);
        
        $user = Auth::user();
        $preferences = $request->only([
            'email_deposit', 'email_investment', 'email_withdrawal', 'email_trading',
            'database_deposit', 'database_investment', 'database_withdrawal', 'database_trading',
            'broadcast_deposit', 'broadcast_investment', 'broadcast_withdrawal', 'broadcast_trading',
            'daily_digest', 'weekly_report',
        ]);
        
        // Ensure boolean values
        foreach ($preferences as $key => $value) {
            $preferences[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        
        $user->notification_preferences = $preferences;
        $user->save();
        
        return back()->with('success', 'Notification preferences updated successfully.');
    }
    
    /**
     * Test notification (for admin debugging)
     */
    public function test(Request $request)
    {
        if (!Auth::user()->is_admin) {
            abort(403);
        }
        
        $request->validate([
            'message' => 'required|string|max:255',
            'category' => 'required|in:deposit,investment,withdrawal,trading,system',
        ]);
        
        $user = Auth::user();
        $user->notify(new \App\Notifications\CustomNotification($request->message, $request->category));
        
        return back()->with('success', 'Test notification sent.');
    }
}
