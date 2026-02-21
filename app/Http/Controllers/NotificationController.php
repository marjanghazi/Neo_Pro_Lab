<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->with('request')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($user->isAdmin()) {
            return view('admin.notifications.index', compact('notifications'));
        } elseif ($user->isCourier()) {
            return view('courier.notifications.index', compact('notifications'));
        } else {
            return view('client.notifications.index', compact('notifications'));
        }
    }

    /**
     * Display the specified notification
     */
    public function show(Notification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Mark as read when viewed
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        $user = Auth::user();

        if ($user->isAdmin()) {
            return view('admin.notifications.show', compact('notification'));
        } elseif ($user->isCourier()) {
            return view('courier.notifications.show', compact('notification'));
        } else {
            return view('client.notifications.show', compact('notification'));
        }
    }

    /**
     * Get recent notifications for dropdown (AJAX)
     */
    public function getRecent()
    {
        $user = Auth::user();

        $notifications = Notification::where('user_id', $user->id)
            ->with('request')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        // Format notifications for response
        $formattedNotifications = $notifications->map(function ($notification) {
            // Decode data if it's a string
            $data = $notification->data;
            if (is_string($data)) {
                $data = json_decode($data, true) ?: [];
            }

            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'read_at' => $notification->read_at,
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at->toDateTimeString(),
                'created_at_human' => $notification->created_at->diffForHumans(),
                'data' => $data,
                'request' => $notification->request ? [
                    'id' => $notification->request->id,
                    'request_number' => $notification->request->request_number,
                ] : null,
                'icon' => $this->getNotificationIcon($notification->type),
                'color' => $this->getNotificationColor($notification->type),
            ];
        });

        return response()->json([
            'notifications' => $formattedNotifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        Notification::where('user_id', Auth::id())->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get unread count for the authenticated user (AJAX)
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get notification icon based on type
     */
    private function getNotificationIcon($type)
    {
        $icons = [
            'new_request' => 'fas fa-file-circle-plus',
            'payment_required' => 'fas fa-credit-card',
            'payment_received' => 'fas fa-circle-check',
            'payment_completed' => 'fas fa-circle-check',
            'payment_failed' => 'fas fa-circle-exclamation',
            'request_assigned' => 'fas fa-truck-fast',
            'request_cancelled' => 'fas fa-ban',
            'request_completed' => 'fas fa-circle-check',
            'pickup_started' => 'fas fa-cube',
            'pickup_completed' => 'fas fa-check-circle',
            'in_transit' => 'fas fa-truck',
            'arrived_at_destination' => 'fas fa-location-dot',
            'delivery_completed' => 'fas fa-check-double',
            'quote_accepted' => 'fas fa-file-signature',
            'quote_declined' => 'fas fa-file-excel',
            'proof_uploaded' => 'fas fa-camera',
            'signature_captured' => 'fas fa-pen',
            'admin_note' => 'fas fa-note-sticky',
            'system' => 'fas fa-gear',
        ];

        return $icons[$type] ?? 'fas fa-bell';
    }

    /**
     * Get notification color based on type
     */
    private function getNotificationColor($type)
    {
        $colors = [
            'new_request' => 'blue',
            'payment_required' => 'orange',
            'payment_received' => 'green',
            'payment_completed' => 'green',
            'payment_failed' => 'red',
            'request_assigned' => 'purple',
            'request_cancelled' => 'red',
            'request_completed' => 'green',
            'pickup_started' => 'yellow',
            'pickup_completed' => 'green',
            'in_transit' => 'blue',
            'arrived_at_destination' => 'green',
            'delivery_completed' => 'green',
            'quote_accepted' => 'green',
            'quote_declined' => 'red',
            'proof_uploaded' => 'blue',
            'signature_captured' => 'green',
            'admin_note' => 'gray',
            'system' => 'gray',
        ];

        return $colors[$type] ?? 'blue';
    }

    /**
     * Get notification URL based on type and user role
     */
    private function getNotificationUrl($notification)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            if ($notification->request) {
                return route('admin.requests.show', $notification->request->id);
            }
            return route('admin.notifications.index');
        } elseif ($user->isCourier()) {
            if ($notification->request) {
                return route('courier.requests.show', $notification->request->id);
            }
            return route('courier.notifications');
        } else {
            if ($notification->request) {
                return route('client.requests.show', $notification->request->id);
            }
            return route('client.notifications');
        }
    }
}
