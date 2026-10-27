<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ShopActivityLogController extends Controller
{
    /**
     * Show activity log page.
     */
    public function index()
    {
        return view('shop.activity_log.index');
    }

    /**
     * DataTables list for activity log.
     */
    public function getList(Request $request)
    {
        $logs = DB::table('tbl_shop_activity_logs as al')
            ->select([
                'al.log_id as id',
                'al.user_type',
                'al.user_id',
                'al.action',
                'al.description',
                'al.ip_address',
                'al.created_at',
            ])
            ->orderByDesc('al.created_at');

        return DataTables::of($logs)
            ->addColumn('user_badge', function ($row) {
                $colors = [
                    'admin'    => 'primary',
                    'vendor'   => 'info',
                    'customer' => 'success',
                    'delivery' => 'warning',
                ];
                $color = $colors[$row->user_type] ?? 'secondary';
                return "<span class='badge bg-{$color}'>" . ucfirst($row->user_type) . " #{$row->user_id}</span>";
            })
            ->rawColumns(['user_badge'])
            ->make(true);
    }

    /**
     * Static helper to log an activity (call from anywhere).
     */
    public static function log(string $userType, int $userId, string $action, string $description = '')
    {
        try {
            DB::table('tbl_shop_activity_logs')->insert([
                'user_type'   => $userType,
                'user_id'     => $userId,
                'action'      => $action,
                'description' => $description,
                'ip_address'  => request()->ip(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist yet
        }
    }
}
