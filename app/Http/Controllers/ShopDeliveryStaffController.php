<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShopDeliveryStaffModel;
use App\Models\ShopDeliveryAssignmentModel;
use App\Models\ShopOrderModel;

class ShopDeliveryStaffController extends Controller
{
    // ═════════════════════════════════════════════════════════════════
    // ADMIN ENDPOINTS (Delivery Staff CRUD)
    // ═════════════════════════════════════════════════════════════════

    public function index()
    {
        $page_title = 'Delivery Staff';
        $page_name  = 'Delivery Staff Management';
        return view('shop.delivery.staff.index', compact('page_title', 'page_name'));
    }

    public function getList(Request $request)
    {
        $query = ShopDeliveryStaffModel::where('status', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('profile_display', function ($row) {
                $img = $row->profile_image 
                    ? asset('storage/' . $row->profile_image) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($row->name) . '&background=0d6efd&color=fff';
                return '<div class="d-flex align-items-center gap-2">
                            <img src="' . $img . '" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                            <div>
                                <div class="fw-semibold">' . e($row->name) . '</div>
                                <small class="text-muted">' . e($row->staff_code ?? 'DS-' . str_pad($row->delivery_staff_id, 4, '0', STR_PAD_LEFT)) . '</small>
                            </div>
                        </div>';
            })
            ->addColumn('contact_info', function ($row) {
                return '<div><i class="bx bx-envelope text-muted me-1"></i>' . e($row->email) . '</div>' .
                       '<div><i class="bx bx-phone text-muted me-1"></i>' . e($row->phone) . '</div>';
            })
            ->addColumn('vehicle_info', function ($row) {
                $type = ucfirst($row->vehicle_type ?? 'Bike');
                $num  = $row->vehicle_number ? ' (' . e($row->vehicle_number) . ')' : '';
                return '<span class="badge bg-light text-dark border"><i class="bx bx-cycling me-1"></i>' . $type . $num . '</span>';
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->is_available == 1) {
                    return '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bx bx-check me-1"></i>Available</span>';
                }
                return '<span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bx bx-time me-1"></i>Busy</span>';
            })
            ->addColumn('active_orders', function ($row) {
                $count = DB::table('tbl_shop_delivery_assignments')
                    ->where('delivery_staff_id', $row->delivery_staff_id)
                    ->whereIn('delivery_status', ['assigned', 'picked', 'out_for_delivery'])
                    ->count();
                return '<span class="badge bg-primary rounded-pill px-2">' . $count . ' active</span>';
            })
            ->addColumn('actions', function ($row) {
                return '<div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-staff-btn" data-id="' . $row->delivery_staff_id . '" title="Edit Staff">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-staff-btn" data-id="' . $row->delivery_staff_id . '" title="Delete Staff">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['profile_display', 'contact_info', 'vehicle_info', 'status_badge', 'active_orders', 'actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150',
            'phone'    => 'required|string|max:20',
            'password' => $request->staff_id ? 'nullable|min:4' : 'required|min:4',
        ]);

        try {
            $data = [
                'name'           => trim($request->name),
                'email'          => strtolower(trim($request->email)),
                'phone'          => trim($request->phone),
                'vehicle_type'   => $request->vehicle_type ?? 'bike',
                'vehicle_number' => $request->vehicle_number,
                'is_available'   => $request->has('is_available') ? 1 : 0,
                'updated_at'     => now(),
            ];

            if ($request->filled('password')) {
                $data['password'] = md5($request->password);
            }

            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('delivery_staff', 'public');
                $data['profile_image'] = $path;
            }

            if ($request->filled('staff_id')) {
                // Update
                DB::table('shop_delivery_staff')
                    ->where('delivery_staff_id', $request->staff_id)
                    ->update($data);
                $msg = 'Delivery staff updated successfully!';
            } else {
                // Check email duplication
                $exists = DB::table('shop_delivery_staff')
                    ->where('email', $data['email'])
                    ->where('status', 0)
                    ->exists();
                if ($exists) {
                    return response()->json(['status' => 'error', 'message' => 'Email is already registered.'], 422);
                }

                $data['staff_code'] = 'DS-' . strtoupper(substr(uniqid(), -6));
                $data['status']     = 0;
                $data['created_at'] = now();

                DB::table('shop_delivery_staff')->insert($data);
                $msg = 'Delivery staff added successfully!';
            }

            return response()->json(['status' => 'success', 'message' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $staff = ShopDeliveryStaffModel::where('delivery_staff_id', $id)->where('status', 0)->first();
        if (!$staff) {
            return response()->json(['status' => 'error', 'message' => 'Staff member not found.'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $staff]);
    }

    public function destroy(Request $request)
    {
        try {
            DB::table('shop_delivery_staff')
                ->where('delivery_staff_id', $request->id)
                ->update(['status' => 1, 'updated_at' => now()]);

            return response()->json(['status' => 'success', 'message' => 'Staff deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function ajaxList(Request $request)
    {
        $staff = DB::table('shop_delivery_staff')
            ->where('status', 0)
            ->where('is_available', 1)
            ->select('delivery_staff_id as id', 'name', 'phone')
            ->get();
        return response()->json(['status' => 'success', 'data' => $staff]);
    }

    // ═════════════════════════════════════════════════════════════════
    // DELIVERY STAFF PORTAL ENDPOINTS
    // ═════════════════════════════════════════════════════════════════

    public function myOrders(Request $request)
    {
        $staffId    = session('delivery_staff_id');
        $page_title = 'Assigned Orders';
        $page_name  = 'My Assigned Deliveries';

        $assignments = DB::table('tbl_shop_delivery_assignments')
            ->join('tbl_shop_orders', 'tbl_shop_orders.order_id', '=', 'tbl_shop_delivery_assignments.order_id')
            ->join('shop_customers', 'shop_customers.customer_id', '=', 'tbl_shop_orders.customer_id')
            ->where('tbl_shop_delivery_assignments.delivery_staff_id', $staffId)
            ->whereIn('tbl_shop_delivery_assignments.delivery_status', ['assigned', 'picked', 'out_for_delivery'])
            ->select(
                'tbl_shop_delivery_assignments.*',
                'tbl_shop_orders.order_number',
                'tbl_shop_orders.total_amount',
                'tbl_shop_orders.payment_method',
                'tbl_shop_orders.payment_status',
                'tbl_shop_orders.shipping_address',
                'shop_customers.name as customer_name',
                'shop_customers.phone as customer_phone'
            )
            ->orderByDesc('tbl_shop_delivery_assignments.assignment_id')
            ->get();

        return view('shop.delivery.orders.index', compact('page_title', 'page_name', 'assignments'));
    }

    public function showOrder($id)
    {
        $staffId = session('delivery_staff_id');

        $assignment = DB::table('tbl_shop_delivery_assignments')
            ->join('tbl_shop_orders', 'tbl_shop_orders.order_id', '=', 'tbl_shop_delivery_assignments.order_id')
            ->join('shop_customers', 'shop_customers.customer_id', '=', 'tbl_shop_orders.customer_id')
            ->where('tbl_shop_delivery_assignments.assignment_id', $id)
            ->where('tbl_shop_delivery_assignments.delivery_staff_id', $staffId)
            ->select(
                'tbl_shop_delivery_assignments.*',
                'tbl_shop_orders.order_number',
                'tbl_shop_orders.total_amount',
                'tbl_shop_orders.payment_method',
                'tbl_shop_orders.payment_status',
                'tbl_shop_orders.shipping_address',
                'tbl_shop_orders.customer_notes',
                'shop_customers.name as customer_name',
                'shop_customers.phone as customer_phone',
                'shop_customers.email as customer_email'
            )
            ->first();

        if (!$assignment) {
            return redirect()->route('delivery.order.index')->with('error', 'Assignment not found or unauthorized.');
        }

        $items = DB::table('tbl_shop_order_items')
            ->where('order_id', $assignment->order_id)
            ->get();

        $page_title = 'Delivery Details #' . $assignment->order_number;
        $page_name  = 'Order Delivery Details';

        return view('shop.delivery.orders.show', compact('page_title', 'page_name', 'assignment', 'items'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'assignment_id'   => 'required',
            'delivery_status' => 'required|in:picked,out_for_delivery,failed',
        ]);

        $staffId = session('delivery_staff_id');

        $assignment = DB::table('tbl_shop_delivery_assignments')
            ->where('assignment_id', $request->assignment_id)
            ->where('delivery_staff_id', $staffId)
            ->first();

        if (!$assignment) {
            return response()->json(['status' => 'error', 'message' => 'Assignment not found.'], 404);
        }

        DB::beginTransaction();
        try {
            $updateData = [
                'delivery_status' => $request->delivery_status,
                'updated_at'      => now(),
            ];

            if ($request->delivery_status === 'failed') {
                $updateData['failure_reason'] = $request->failure_reason ?? 'Customer unreachable / Delivery failed';
            }

            DB::table('tbl_shop_delivery_assignments')
                ->where('assignment_id', $request->assignment_id)
                ->update($updateData);

            // Update parent order status and tracking
            $orderStatus = match ($request->delivery_status) {
                'picked'           => 'processing',
                'out_for_delivery' => 'out_for_delivery',
                'failed'           => 'processing',
                default            => 'processing',
            };

            DB::table('tbl_shop_orders')
                ->where('order_id', $assignment->order_id)
                ->update(['order_status' => $orderStatus, 'updated_at' => now()]);

            DB::table('tbl_shop_order_tracking')->insert([
                'order_id'        => $assignment->order_id,
                'status'          => $request->delivery_status,
                'description'     => 'Delivery status updated to ' . ucfirst(str_replace('_', ' ', $request->delivery_status)) . ($request->failure_reason ? ' (' . $request->failure_reason . ')' : ''),
                'created_by'      => $staffId,
                'created_by_type' => 'delivery',
                'created_at'      => now(),
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Delivery status updated successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required',
            'delivery_otp'  => 'required',
        ]);

        $staffId = session('delivery_staff_id');

        $assignment = DB::table('tbl_shop_delivery_assignments')
            ->where('assignment_id', $request->assignment_id)
            ->where('delivery_staff_id', $staffId)
            ->first();

        if (!$assignment) {
            return response()->json(['status' => 'error', 'message' => 'Assignment not found.'], 404);
        }

        // Compare OTP
        if (trim($assignment->delivery_otp) !== trim($request->delivery_otp)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid OTP! Please check with customer.'], 422);
        }

        DB::beginTransaction();
        try {
            // Update assignment
            DB::table('tbl_shop_delivery_assignments')
                ->where('assignment_id', $request->assignment_id)
                ->update([
                    'delivery_status' => 'delivered',
                    'otp_verified_at' => now(),
                    'delivered_at'    => now(),
                    'updated_at'      => now(),
                ]);

            // Update order to delivered and paid if COD
            $order = DB::table('tbl_shop_orders')->where('order_id', $assignment->order_id)->first();
            $orderUpdates = [
                'order_status' => 'delivered',
                'delivered_at' => now(),
                'updated_at'   => now(),
            ];

            if ($order && $order->payment_method === 'cod') {
                $orderUpdates['payment_status'] = 'paid';
            }

            DB::table('tbl_shop_orders')->where('order_id', $assignment->order_id)->update($orderUpdates);

            // Update item statuses
            DB::table('tbl_shop_order_items')->where('order_id', $assignment->order_id)->update(['item_status' => 'delivered']);

            // Insert tracking
            DB::table('tbl_shop_order_tracking')->insert([
                'order_id'        => $assignment->order_id,
                'status'          => 'delivered',
                'description'     => 'Order delivered successfully with OTP verification',
                'created_by'      => $staffId,
                'created_by_type' => 'delivery',
                'created_at'      => now(),
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Order verified & marked as DELIVERED!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function history(Request $request)
    {
        $staffId    = session('delivery_staff_id');
        $page_title = 'Delivery History';
        $page_name  = 'My Delivery History';

        $deliveries = DB::table('tbl_shop_delivery_assignments')
            ->join('tbl_shop_orders', 'tbl_shop_orders.order_id', '=', 'tbl_shop_delivery_assignments.order_id')
            ->join('shop_customers', 'shop_customers.customer_id', '=', 'tbl_shop_orders.customer_id')
            ->where('tbl_shop_delivery_assignments.delivery_staff_id', $staffId)
            ->whereIn('tbl_shop_delivery_assignments.delivery_status', ['delivered', 'failed'])
            ->select(
                'tbl_shop_delivery_assignments.*',
                'tbl_shop_orders.order_number',
                'tbl_shop_orders.total_amount',
                'tbl_shop_orders.payment_method',
                'shop_customers.name as customer_name',
                'shop_customers.phone as customer_phone'
            )
            ->orderByDesc('tbl_shop_delivery_assignments.delivered_at')
            ->get();

        return view('shop.delivery.history.index', compact('page_title', 'page_name', 'deliveries'));
    }
}
