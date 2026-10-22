<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShopCmsPageModel;

class ShopCmsController extends Controller
{
    public function index()
    {
        $page_title = 'CMS Pages';
        $page_name  = 'Static Pages & Policy Management';
        return view('shop.cms.pages.index', compact('page_title', 'page_name'));
    }

    public function getList(Request $request)
    {
        $query = ShopCmsPageModel::where('status', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('page_info', function ($row) {
                return '<div>
                            <div class="fw-semibold text-primary">' . e($row->page_title) . '</div>
                            <small class="text-muted"><i class="bx bx-link me-1"></i>/' . e($row->page_slug) . '</small>
                        </div>';
            })
            ->addColumn('footer_badge', function ($row) {
                return $row->show_in_footer == 1
                    ? '<span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bx bx-check me-1"></i>Footer Link</span>'
                    : '<span class="badge bg-light text-muted border">Hidden</span>';
            })
            ->addColumn('status_badge', function ($row) {
                return $row->is_active == 1
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>'
                    : '<span class="badge bg-secondary-subtle text-secondary border">Draft</span>';
            })
            ->addColumn('actions', function ($row) {
                return '<div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-page-btn" data-id="' . $row->page_id . '" title="Edit Page">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-page-btn" data-id="' . $row->page_id . '" title="Delete Page">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['page_info', 'footer_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_title'   => 'required|string|max:200',
            'page_content' => 'required',
        ]);

        try {
            $slug = $request->page_slug ? Str::slug($request->page_slug) : Str::slug($request->page_title);

            $data = [
                'page_title'       => trim($request->page_title),
                'page_slug'        => $slug,
                'page_content'     => $request->page_content,
                'meta_title'       => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords'    => $request->meta_keywords,
                'is_active'        => $request->has('is_active') ? 1 : 0,
                'show_in_footer'   => $request->has('show_in_footer') ? 1 : 0,
                'sort_order'       => (int)($request->sort_order ?? 0),
                'updated_at'       => now(),
            ];

            if ($request->filled('page_id')) {
                DB::table('tbl_shop_cms_pages')->where('page_id', $request->page_id)->update($data);
                $msg = 'CMS Page updated successfully!';
            } else {
                $data['status']     = 0;
                $data['created_at'] = now();
                DB::table('tbl_shop_cms_pages')->insert($data);
                $msg = 'CMS Page published successfully!';
            }

            return response()->json(['status' => 'success', 'message' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $page = ShopCmsPageModel::where('page_id', $id)->where('status', 0)->first();
        if (!$page) {
            return response()->json(['status' => 'error', 'message' => 'Page not found.'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $page]);
    }

    public function destroy(Request $request)
    {
        try {
            DB::table('tbl_shop_cms_pages')
                ->where('page_id', $request->id)
                ->update(['status' => 1, 'updated_at' => now()]);

            return response()->json(['status' => 'success', 'message' => 'Page deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
