<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShopBannerModel;

class ShopBannerController extends Controller
{
    public function index()
    {
        $page_title = 'Banners & Sliders';
        $page_name  = 'Banner & Slider Management';
        return view('shop.cms.banner.index', compact('page_title', 'page_name'));
    }

    public function getList(Request $request)
    {
        $query = ShopBannerModel::where('status', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('banner_preview', function ($row) {
                $img = $row->banner_image ? asset('storage/' . $row->banner_image) : 'https://placehold.co/120x50?text=Banner';
                return '<div class="d-flex align-items-center gap-2">
                            <img src="' . $img . '" class="rounded shadow-sm" style="width:110px;height:45px;object-fit:cover;">
                            <div>
                                <div class="fw-semibold">' . e($row->banner_title) . '</div>
                                <small class="text-muted">' . e($row->banner_subtitle ?? '') . '</small>
                            </div>
                        </div>';
            })
            ->addColumn('position_badge', function ($row) {
                return '<span class="badge bg-secondary-subtle text-secondary border">' . ucfirst(str_replace('_', ' ', $row->banner_position)) . '</span>';
            })
            ->addColumn('link_display', function ($row) {
                if ($row->banner_link) {
                    return '<a href="' . e($row->banner_link) . '" target="_blank" class="text-decoration-none small text-truncate d-inline-block" style="max-width:180px;"><i class="bx bx-link-external me-1"></i>' . e($row->banner_link) . '</a>';
                }
                return '<span class="text-muted small">None</span>';
            })
            ->addColumn('status_badge', function ($row) {
                return $row->is_active == 1
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>'
                    : '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactive</span>';
            })
            ->addColumn('actions', function ($row) {
                return '<div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-banner-btn" data-id="' . $row->banner_id . '" title="Edit Banner">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-banner-btn" data-id="' . $row->banner_id . '" title="Delete Banner">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['banner_preview', 'position_badge', 'link_display', 'status_badge', 'actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'banner_title'    => 'required|string|max:200',
            'banner_position' => 'required',
            'banner_image'    => $request->banner_id ? 'nullable|image|max:3072' : 'required|image|max:3072',
        ]);

        try {
            $data = [
                'banner_title'        => trim($request->banner_title),
                'banner_subtitle'     => $request->banner_subtitle,
                'banner_link'         => $request->banner_link,
                'banner_cta_text'     => $request->banner_cta_text,
                'banner_position'     => $request->banner_position,
                'target_type'         => $request->target_type,
                'target_id'           => $request->target_id,
                'sort_order'          => (int)($request->sort_order ?? 0),
                'is_active'           => $request->has('is_active') ? 1 : 0,
                'updated_at'          => now(),
            ];

            if ($request->hasFile('banner_image')) {
                $path = $request->file('banner_image')->store('banners', 'public');
                $data['banner_image'] = $path;
            }

            if ($request->hasFile('banner_mobile_image')) {
                $path = $request->file('banner_mobile_image')->store('banners', 'public');
                $data['banner_mobile_image'] = $path;
            }

            if ($request->filled('banner_id')) {
                DB::table('tbl_shop_banners')->where('banner_id', $request->banner_id)->update($data);
                $msg = 'Banner updated successfully!';
            } else {
                $data['status']     = 0;
                $data['created_at'] = now();
                DB::table('tbl_shop_banners')->insert($data);
                $msg = 'Banner created successfully!';
            }

            return response()->json(['status' => 'success', 'message' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $banner = ShopBannerModel::where('banner_id', $id)->where('status', 0)->first();
        if (!$banner) {
            return response()->json(['status' => 'error', 'message' => 'Banner not found.'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $banner]);
    }

    public function destroy(Request $request)
    {
        try {
            DB::table('tbl_shop_banners')
                ->where('banner_id', $request->id)
                ->update(['status' => 1, 'updated_at' => now()]);

            return response()->json(['status' => 'success', 'message' => 'Banner deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
