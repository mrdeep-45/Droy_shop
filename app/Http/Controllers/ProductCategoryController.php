<?php

namespace App\Http\Controllers;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
class ProductCategoryController extends Controller
{
    //
     public function index()
    {
        $data = [
            'page_title' => 'Product Category',
            'page_name' => 'Product Category',
        ];

        return view('company.master.productcategory', $data);
    }
     public function getList()
    {
        try {
            $permissions = checkPermissions(get_index_route($this));
            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = ProductCategoryModel::select(['category_id', 'name', 'created_by', 'created_at'])
                ->where('status', 0);
                

            return DataTables::of($query)

            // ✅ Created By
                ->addColumn('created_by', function ($row) {
                    return get_createdby_name($row->created_by);
                })
                ->orderColumn('created_by', function ($query, $direction) {
                    $query->orderBy('created_by', $direction);
                })

            // ✅ Created At
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
                })
                ->orderColumn('created_at', function ($query, $direction) {
                    $query->orderBy('created_at', $direction);
                })

                ->addColumn('action', function ($row) {
                    // RBAC (Role-Based Access Control) Logic:
                    $canUpdate = permissions_check('canUpdate', 'productcategory.index');
                    $canDelete = permissions_check('canDelete', 'productcategory.index');

                    $buttons = '<div class="d-flex justify-content-center gap-1 text-nowrap">';

                    if ($canUpdate) {
                        $buttons .= '<button class="btn btn-sm btn-primary edit-group" data-id="'.$row->category_id.'"><i class="fas fa-edit"></i> Edit</button>';
                    }

                    if ($canDelete) {
                        // Production Safe checking
                        $isUsed = false;
                        try {
                            $isUsed = ProductModel::where('category_id', $row->category_id)
                                ->where('status', 0)
                                ->exists();
                        } catch (\Exception $e) {
                            $isUsed = false;
                        }

                        if ($isUsed) {
                            $buttons .= '<button type="button" class="btn btn-sm btn-danger" disabled style="opacity: 0.5; cursor: not-allowed;" title="Cannot delete: Product Category is in use"><i class="fas fa-trash"></i> Delete</button>';
                        } else {
                            $buttons .= '<button class="btn btn-sm btn-danger delete-group" data-id="'.$row->category_id.'"><i class="fas fa-trash"></i> Delete</button>';
                        }
                    }

                    $buttons .= '</div>';

                    return $buttons;
                })

                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function edit($id)
    {
        $item = ProductCategoryModel::where('category_id', $id)->first();

        if (! $item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product category not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $item,
        ]);
    }
     public function storeOrUpdate(Request $request)
    {
        try {
            $ids = $request->input('category_id');
            $names = $request->input('name');
            if (empty($names) || ! is_array($names)) {
                return response()->json([
                    'errors' => ['name.0' => ['At least one Product Category is required.']],
                ], 422);
            }
            if (! is_array($ids)) {
                $ids = $ids ? [$ids] : [];
            }
            $updated = false;
            $created = false;
            $createdItems = [];
            foreach ($names as $index => $name) {
                if (empty(trim($name))) {
                    return response()->json([
                        'errors' => ["name.$index" => ["Product Category at position $index is required."]],
                    ], 422);
                }
                $id = $ids[$index] ?? null;
                $exists = ProductCategoryModel::where('name', $name)
                    ->where('status', 0)
                    ->when($id, fn ($q) => $q->where('category_id', '!=', $id))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'errors' => ["name.$index" => ["This Product Category '$name' already exists."]],
                    ], 422);
                }

                if ($id) {
                    $item = ProductCategoryModel::find($id);
                    if ($item) {
                        // Backend Security Check (RBAC):
                        if (!permissions_check('canUpdate', 'productcategory.index')) {
                            return response()->json([
                                'errors' => ["name.$index" => ["You do not have permission to update this."]],
                            ], 422);
                        }

                        $item->update([
                            'name' => $name,
                            'updated_by' => getUpdatedBy(),
                            'updated_at' => now(),
                        ]);
                        $updated = true;
                    }
                } else {
                    $newItem = ProductCategoryModel::create([
                        'name' => $name,
                        'status' => 0,
                        'created_by' => getCreatedBy(),
                        'created_at' => now(),
                    ]);
                    $createdItems[] = $newItem;
                    $created = true;
                }
            }
            DB::commit();
            if ($updated && $created) {
                $message = 'Product Categories updated and added successfully.';
            } elseif ($updated) {
                $message = 'Product Category updated successfully.';
            } elseif ($created) {
                $message = 'New Product Categories added successfully.';
            } else {
                $message = 'No changes were made.';
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $createdItems
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
     public function destroy(Request $request)
    {
        try {
            $id = $request->category_id;
            $item = ProductCategoryModel::find($id);

            if (! $item) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product Category not found',
                ]);
            }

            // Backend Security Check (RBAC):
            if (!permissions_check('canDelete', 'productcategory.index')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have permission to delete this.',
                ]);
            }

            // Production Safe checking
            $isUsed = false;
            try {
                $isUsed = ProductModel::where('category_id', $id)
                    ->where('status', 0)
                    ->exists();
            } catch (\Exception $e) {
                $isUsed = false;
            }

            if ($isUsed) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete! This Product Category is already used in Products.',
                ]);
            }

            $item->update([
                'status' => 1,
                'updated_by' => getUpdatedBy(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product Category marked as inactive.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
