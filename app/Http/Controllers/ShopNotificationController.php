<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ShopNotificationModel;

class ShopNotificationController extends Controller
{
    /**
     * Get list of notifications for the current authenticated user session
     */
    public function getList(Request $request)
    {
        $loginType = session('shop_login_type');
        $userId    = session('shop_admin_id') ?? session('vendor_id') ?? session('delivery_staff_id');

        $userType = match ($loginType) {
            'ShopSuperAdmin', 'ShopAdmin' => 'admin',
            'Vendor'                      => 'vendor',
            'DeliveryStaff'               => 'delivery',
            'Customer'                    => 'customer',
            default                       => 'admin',
        };

        // Admins can see all admin notifications or notifications targeted to admin (user_id = 0 or specific admin)
        $query = DB::table('tbl_shop_notifications')
            ->where('user_type', $userType);

        if ($userType !== 'admin') {
            $query->where('user_id', $userId);
        }

        $notifications = $query->orderByDesc('notification_id')
            ->limit(15)
            ->get()
            ->map(function ($item) {
                return [
                    'notification_id' => $item->notification_id,
                    'title'           => $item->title,
                    'message'         => $item->message,
                    'icon'            => $item->icon ?: 'bx bx-bell',
                    'color'           => $item->color ?: 'primary',
                    'link'            => $item->link ?: '#',
                    'is_read'         => (bool)$item->is_read,
                    'time_ago'        => \Carbon\Carbon::parse($item->created_at)->diffForHumans(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $notifications,
        ]);
    }

    /**
     * Mark single notification as read
     */
    public function markRead(Request $request)
    {
        $request->validate(['id' => 'required']);

        DB::table('tbl_shop_notifications')
            ->where('notification_id', $request->id)
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Mark all notifications as read for current user
     */
    public function markAllRead(Request $request)
    {
        $loginType = session('shop_login_type');
        $userId    = session('shop_admin_id') ?? session('vendor_id') ?? session('delivery_staff_id');

        $userType = match ($loginType) {
            'ShopSuperAdmin', 'ShopAdmin' => 'admin',
            'Vendor'                      => 'vendor',
            'DeliveryStaff'               => 'delivery',
            'Customer'                    => 'customer',
            default                       => 'admin',
        };

        $query = DB::table('tbl_shop_notifications')->where('user_type', $userType);
        if ($userType !== 'admin') {
            $query->where('user_id', $userId);
        }

        $query->where('is_read', 0)->update([
            'is_read' => 1,
            'read_at' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'All notifications marked as read.']);
    }
}
