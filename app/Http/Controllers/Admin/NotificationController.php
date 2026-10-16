<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $orderNotifications = Notification::whereIn('type', ['new_order', 'courier_update'])
            ->latest()
            ->limit(20)
            ->get();

        $chatNotifications = Notification::where('type', 'chat')
            ->latest()
            ->limit(20)
            ->get();

        $unreadCount = Notification::unread()->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        return response()->json([
            'orders' => $orderNotifications,
            'messages' => $chatNotifications,
            'unread_count' => $unreadCount,
            'pending_orders' => $pendingOrders,
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'unread_count' => Notification::unread()->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
        ]);
    }

    public function markRead(Request $request): JsonResponse
    {
        if ($request->input('id')) {
            Notification::where('id', $request->input('id'))->update(['read_at' => now()]);
        } else {
            Notification::unread()->update(['read_at' => now()]);
        }

        return response()->json([
            'unread_count' => Notification::unread()->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
        ]);
    }
}
