<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShopBlogModel;

class ShopBlogController extends Controller
{
    public function index()
    {
        $page_title = 'Blog Management';
        $page_name  = 'Blog Articles & Stories';
        return view('shop.cms.blog.index', compact('page_title', 'page_name'));
    }

    public function getList(Request $request)
    {
        $query = ShopBlogModel::where('status', 0);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('article_info', function ($row) {
                $img = $row->blog_image ? asset('storage/' . $row->blog_image) : 'https://placehold.co/80x60?text=Blog';
                return '<div class="d-flex align-items-center gap-2">
                            <img src="' . $img . '" class="rounded shadow-sm" style="width:60px;height:45px;object-fit:cover;">
                            <div>
                                <div class="fw-semibold text-primary">' . e($row->blog_title) . '</div>
                                <small class="text-muted"><i class="bx bx-folder me-1"></i>' . e($row->blog_category ?? 'General') . '</small>
                            </div>
                        </div>';
            })
            ->addColumn('views', function ($row) {
                return '<span class="badge bg-light text-dark border"><i class="bx bx-show me-1"></i>' . number_format($row->view_count) . ' views</span>';
            })
            ->addColumn('status_badge', function ($row) {
                return $row->is_published == 1
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>'
                    : '<span class="badge bg-secondary-subtle text-secondary border">Draft</span>';
            })
            ->addColumn('date', function ($row) {
                return '<small class="text-muted">' . ($row->published_at ? \Carbon\Carbon::parse($row->published_at)->format('d M Y') : 'Not Published') . '</small>';
            })
            ->addColumn('actions', function ($row) {
                return '<div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-blog-btn" data-id="' . $row->blog_id . '" title="Edit Blog">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-blog-btn" data-id="' . $row->blog_id . '" title="Delete Blog">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>';
            })
            ->rawColumns(['article_info', 'views', 'status_badge', 'date', 'actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'blog_title'   => 'required|string|max:250',
            'blog_content' => 'required',
            'blog_image'   => $request->blog_id ? 'nullable|image|max:3072' : 'nullable|image|max:3072',
        ]);

        try {
            $slug = $request->blog_slug ? Str::slug($request->blog_slug) : Str::slug($request->blog_title);

            $data = [
                'blog_title'       => trim($request->blog_title),
                'blog_slug'        => $slug,
                'blog_content'     => $request->blog_content,
                'blog_excerpt'     => $request->blog_excerpt,
                'blog_category'    => $request->blog_category ?? 'Fashion & Trends',
                'tags'             => $request->tags,
                'is_published'     => $request->has('is_published') ? 1 : 0,
                'published_at'     => $request->has('is_published') ? now() : null,
                'meta_title'       => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords'    => $request->meta_keywords,
                'updated_at'       => now(),
            ];

            if ($request->hasFile('blog_image')) {
                $path = $request->file('blog_image')->store('blog', 'public');
                $data['blog_image'] = $path;
            }

            if ($request->filled('blog_id')) {
                DB::table('tbl_shop_blogs')->where('blog_id', $request->blog_id)->update($data);
                $msg = 'Blog article updated successfully!';
            } else {
                $data['status']     = 0;
                $data['view_count'] = 0;
                $data['created_at'] = now();
                DB::table('tbl_shop_blogs')->insert($data);
                $msg = 'Blog article published successfully!';
            }

            return response()->json(['status' => 'success', 'message' => $msg]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $blog = ShopBlogModel::where('blog_id', $id)->where('status', 0)->first();
        if (!$blog) {
            return response()->json(['status' => 'error', 'message' => 'Article not found.'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $blog]);
    }

    public function destroy(Request $request)
    {
        try {
            DB::table('tbl_shop_blogs')
                ->where('blog_id', $request->id)
                ->update(['status' => 1, 'updated_at' => now()]);

            return response()->json(['status' => 'success', 'message' => 'Blog article deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
