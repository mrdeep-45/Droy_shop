<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShopReviewModel;
use App\Services\ShopNotificationService;

class ShopReviewController extends Controller
{
    // ═════════════════════════════════════════════════════════════════
    // ADMIN ENDPOINTS
    // ═════════════════════════════════════════════════════════════════

    public function index()
    {
        $page_title = 'Product Reviews';
        $page_name  = 'Reviews & Customer Ratings';
        return view('shop.reviews.index', compact('page_title', 'page_name'));
    }

    public function getList(Request $request)
    {
        $query = DB::table('tbl_shop_reviews')
            ->join('tbl_shop_products', 'tbl_shop_products.product_id', '=', 'tbl_shop_reviews.product_id')
            ->join('shop_customers', 'shop_customers.customer_id', '=', 'tbl_shop_reviews.customer_id')
            ->where('tbl_shop_reviews.status', 0)
            ->select(
                'tbl_shop_reviews.*',
                'tbl_shop_products.product_name',
                'tbl_shop_products.product_slug',
                'shop_customers.name as customer_name',
                'shop_customers.email as customer_email'
            );

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('product_info', function ($row) {
                return '<div>
                            <div class="fw-semibold text-primary">' . e($row->product_name) . '</div>
                            <small class="text-muted">ID: #' . $row->product_id . '</small>
                        </div>';
            })
            ->addColumn('customer_info', function ($row) {
                $badge = $row->is_verified_purchase
                    ? '<span class="badge bg-success-subtle text-success ms-1" style="font-size:0.65rem;"><i class="bx bx-check-shield"></i> Verified</span>'
                    : '';
                return '<div>
                            <div class="fw-semibold">' . e($row->customer_name) . $badge . '</div>
                            <small class="text-muted">' . e($row->customer_email) . '</small>
                        </div>';
            })
            ->addColumn('rating_stars', function ($row) {
                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= ($i <= $row->rating)
                        ? '<i class="bx bxs-star text-warning"></i>'
                        : '<i class="bx bx-star text-muted" style="opacity:0.4;"></i>';
                }
                return '<div class="d-flex align-items-center gap-1">' . $stars . ' <span class="fw-bold ms-1 text-dark">' . $row->rating . '.0</span></div>';
            })
            ->addColumn('review_details', function ($row) {
                $title = $row->review_title ? '<div class="fw-bold">' . e($row->review_title) . '</div>' : '';
                $body  = '<div class="text-muted text-wrap" style="max-width:280px;font-size:0.8rem;">' . e(mb_strimwidth($row->review_body, 0, 90, '...')) . '</div>';
                $reply = $row->admin_reply ? '<div class="mt-1 small text-info"><i class="bx bx-reply me-1"></i>Replied: ' . e(mb_strimwidth($row->admin_reply, 0, 45, '...')) . '</div>' : '';
                return $title . $body . $reply;
            })
            ->addColumn('status_badge', function ($row) {
                return $row->is_approved == 1
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bx bx-check me-1"></i>Approved</span>'
                    : '<span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bx bx-time me-1"></i>Pending</span>';
            })
            ->addColumn('actions', function ($row) {
                $approveBtn = $row->is_approved == 1
                    ? '<button class="btn btn-sm btn-outline-warning toggle-approve-btn" data-id="' . $row->review_id . '" data-status="0" title="Unapprove / Hide"><i class="bx bx-hide"></i></button>'
                    : '<button class="btn btn-sm btn-outline-success toggle-approve-btn" data-id="' . $row->review_id . '" data-status="1" title="Approve Review"><i class="bx bx-check"></i></button>';

                return '<div class="d-flex gap-1">
                            ' . $approveBtn . '
                            <button class="btn btn-sm btn-outline-info reply-review-btn" data-id="' . $row->review_id . '" data-reply="' . e($row->admin_reply ?? '') . '" title="Reply to Review"><i class="bx bx-comment-detail"></i></button>
                            <button class="btn btn-sm btn-outline-danger delete-review-btn" data-id="' . $row->review_id . '" title="Delete Review"><i class="bx bx-trash"></i></button>
                        </div>';
            })
            ->rawColumns(['product_info', 'customer_info', 'rating_stars', 'review_details', 'status_badge', 'actions'])
            ->make(true);
    }

    public function approve(Request $request)
    {
        $request->validate([
            'id'     => 'required',
            'status' => 'required|in:0,1',
        ]);

        DB::table('tbl_shop_reviews')
            ->where('review_id', $request->id)
            ->update([
                'is_approved' => (int)$request->status,
                'updated_at'  => now(),
            ]);

        $statusText = $request->status == 1 ? 'approved' : 'hidden';
        return response()->json(['status' => 'success', 'message' => "Review has been {$statusText} successfully!"]);
    }

    public function reply(Request $request)
    {
        $request->validate([
            'review_id'   => 'required',
            'admin_reply' => 'required|string',
        ]);

        $review = DB::table('tbl_shop_reviews')->where('review_id', $request->review_id)->first();
        if (!$review) {
            return response()->json(['status' => 'error', 'message' => 'Review not found.'], 404);
        }

        DB::table('tbl_shop_reviews')
            ->where('review_id', $request->review_id)
            ->update([
                'admin_reply' => trim($request->admin_reply),
                'replied_at'  => now(),
                'updated_at'  => now(),
            ]);

        // Notify customer
        ShopNotificationService::send(
            'customer',
            $review->customer_id,
            'Seller replied to your review',
            "The store response to your review has been posted: \"{$request->admin_reply}\"",
            [
                'type'       => 'review_reply',
                'icon'       => 'bx bx-comment-detail',
                'color'      => 'info',
                'send_email' => true,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Reply saved and customer notified!']);
    }

    public function destroy(Request $request)
    {
        DB::table('tbl_shop_reviews')
            ->where('review_id', $request->id)
            ->update([
                'status'     => 1,
                'updated_at' => now(),
            ]);

        return response()->json(['status' => 'success', 'message' => 'Review deleted successfully!']);
    }

    // ═════════════════════════════════════════════════════════════════
    // CUSTOMER PUBLIC / STOREFRONT SUBMIT ENDPOINT
    // ═════════════════════════════════════════════════════════════════

    public function customerSubmit(Request $request)
    {
        $customerId = session('customer_id');
        if (!$customerId) {
            return response()->json(['status' => 'error', 'message' => 'Please log in to submit a review.'], 401);
        }

        $request->validate([
            'product_id'   => 'required',
            'rating'       => 'required|integer|min:1|max:5',
            'review_title' => 'nullable|string|max:150',
            'review_body'  => 'required|string|min:5',
        ]);

        // Check if verified purchase
        $isVerified = DB::table('tbl_shop_order_items')
            ->join('tbl_shop_orders', 'tbl_shop_orders.order_id', '=', 'tbl_shop_order_items.order_id')
            ->where('tbl_shop_orders.customer_id', $customerId)
            ->where('tbl_shop_order_items.product_id', $request->product_id)
            ->where('tbl_shop_orders.order_status', 'delivered')
            ->exists();

        $reviewId = DB::table('tbl_shop_reviews')->insertGetId([
            'product_id'           => $request->product_id,
            'customer_id'          => $customerId,
            'rating'               => $request->rating,
            'review_title'         => $request->review_title,
            'review_body'          => $request->review_body,
            'is_verified_purchase' => $isVerified ? 1 : 0,
            'is_approved'          => 1, // Auto-approve or moderate
            'status'               => 0,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        // Notify Admins
        $product = DB::table('tbl_shop_products')->where('product_id', $request->product_id)->first();
        $customerName = session('customer_name') ?? 'A customer';

        ShopNotificationService::send(
            'admin',
            0,
            'New Customer Review',
            "{$customerName} gave {$request->rating} stars on {$product->product_name}",
            [
                'type'  => 'product_review',
                'icon'  => 'bx bx-star',
                'color' => 'warning',
                'link'  => route('shop.review.index'),
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Thank you! Your review has been submitted successfully.',
        ]);
    }
}
