<?php

namespace App\Http\Controllers;
use App\Models\ProductModel;
use App\Models\ProductCategoryModel;
use App\Models\GstModel;
use App\Models\UnitModel;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    //
    public function index()
    {
         $page_title = 'Product';
        $page_name = 'Product Management';
        $category = ProductCategoryModel::where('status', 0)->orderBy('name', 'asc')->get();
        $gst_rates = GstModel::where('status', 0)->orderByRaw('CAST(gst_no AS UNSIGNED) ASC')->get();
        $units = UnitModel::where('status', 0)->orderBy('unit', 'asc')->get();
         return view('company.master.product', compact('page_title', 'page_name', 'category', 'gst_rates', 'units'));
    }
    public function getList(Request $request)
    {
        try {
            $permissions = checkPermissions(get_index_route($this));
            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = ProductModel::with(['category', 'gstDetails', 'unitDetails'])->select([
                'tbl_product.product_id', 
                'tbl_product.prod_code', 
                'tbl_product.prod_name', 
                'tbl_product.prod_amount', 
                'tbl_product.prod_desc', 
                'tbl_product.category_id', 
                'tbl_product.gst', 
                'tbl_product.unit_id', 
                'tbl_product.created_by', 
                'tbl_product.created_at'
            ])
                ->where('tbl_product.status', 0);
               // Apply Category Filter if selected
       if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('tbl_product.category_id', $request->category_id);
        }

       // $query->orderBy('product_id', 'desc');
                

            return DataTables::of($query)
             ->editColumn('category_id', function ($row) {
                    return $row->category ? $row->category->name : 'N/A';
                })
                ->orderColumn('category_id', function ($query, $direction) {
                    $query->leftJoin('tbl_product_category', 'tbl_product_category.category_id', '=', 'tbl_product.category_id')
                        ->orderBy('tbl_product_category.name', $direction);
                })
                ->addColumn('gst_display', function ($row) {
                    return $row->gstDetails ? $row->gstDetails->gst_no . '%' : '0%';
                })
                ->orderColumn('gst_display', function ($query, $direction) {
                    $query->leftJoin('mst_gst', 'mst_gst.gst_id', '=', 'tbl_product.gst')
                        ->orderByRaw('CAST(mst_gst.gst_no AS UNSIGNED) ' . $direction);
                })
                ->addColumn('unit_display', function ($row) {
                    return $row->unitDetails ? $row->unitDetails->unit : 'N/A';
                })
                ->orderColumn('unit_display', function ($query, $direction) {
                    $query->leftJoin('tbl_unit', 'tbl_unit.unit_id', '=', 'tbl_product.unit_id')
                        ->orderBy('tbl_unit.unit', $direction);
                })
                ->editColumn('created_by', function ($row) {
                    return get_createdby_name($row->created_by);
                })
               ->orderColumn('created_by', function ($query, $direction) {
                    $query->orderBy('tbl_product.created_by', $direction);
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
                })
                ->orderColumn('created_at', function ($query, $direction) {
                    $query->orderBy('tbl_product.created_at', $direction);
                })
                ->editColumn('prod_amount', function ($row) {
                    return number_format($row->prod_amount, 2);
                })
                ->addColumn('action', function ($row) use ($canUpdate, $canDelete) {
                    $buttons = '<div class="d-flex gap-1 justify-content-center">';

                    if ($canUpdate) {
                        $buttons .= '<button class="btn btn-sm btn-primary edit-group" data-id="'.$row->product_id.'"><i class="fas fa-edit"></i> Edit</button>';
                    }

                    if ($canDelete) {
                        $buttons .= '<button class="btn btn-sm btn-danger delete-group" data-id="'.$row->product_id.'"><i class="fas fa-trash"></i> Delete</button>';
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
        $item = ProductModel::where('product_id', $id)->first();

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product record not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $item,
        ]);
    }

    public function store(Request $request)
    {
        return $this->storeOrUpdate($request);
    }
    public function storeOrUpdate(Request $request)
    {
        $id = $request->input('product_id');

        $request->validate([
            'category_id' => 'required|exists:tbl_product_category,category_id',
            'prod_name'   => 'required|string|max:255',
            'prod_amount' => 'required|numeric|min:0',
            'prod_desc'   => 'nullable|string',
            'gst'         => 'nullable|exists:mst_gst,gst_id',
            'unit_id'     => 'nullable|exists:tbl_unit,unit_id',
        ]);

        DB::beginTransaction();
        try {
            $name = trim($request->input('prod_name'));
            $amount = $request->input('prod_amount');
            $desc = $request->input('prod_desc');
            $category_id = $request->input('category_id');
            $gst = $request->input('gst');
            $unit_id = $request->input('unit_id');
            // Check Duplicate Name Exclusions
            $exists = ProductModel::where('prod_name', $name)
                ->where('status', 0)
                ->when($id, fn ($q) => $q->where('product_id', '!=', $id))
                ->exists();

            if ($exists) {
                return response()->json([
                    'errors' => ['prod_name' => ["The Product Name '$name' already exists."]],
                ], 422);
            }

            if ($id) {
                // Update Path
                $item = ProductModel::find($id);
                if ($item) {
                    $item->update([
                        'prod_name'   => $name,
                        'prod_amount' => $amount,
                        'prod_desc'   => $desc,
                        'category_id' => $category_id,
                        'gst'         => $gst,
                        'unit_id'     => $unit_id,
                        'updated_by'  => getUpdatedBy(),
                        'updated_at'  => now(),
                    ]);
                    $message = 'Product updated successfully.';
                }
            } else {
                // Auto-Increment code logic generation (e.g., PROD-0001)
                $latestProduct = ProductModel::orderBy('product_id', 'desc')->first();
                $nextId = $latestProduct ? ($latestProduct->product_id + 1) : 1;
                $generatedCode = 'PROD-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                ProductModel::create([
                    'prod_code'   => $generatedCode,
                    'prod_name'   => $name,
                    'prod_amount' => $amount,
                    'prod_desc'   => $desc,
                    'category_id' => $category_id,
                    'gst'         => $gst,
                    'unit_id'     => $unit_id,
                    'status'      => 0,
                    'created_by'  => getCreatedBy(),
                    'created_at'  => now(),
                ]);
                $message = 'Product created successfully.';
            }

            DB::commit();
            return response()->json(['status' => 'success', 'success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $id = $request->product_id;
            $item = ProductModel::find($id);

            if (!$item) {
                return response()->json(['status' => 'error', 'message' => 'Product not found']);
            }

            $item->update([
                'status'     => 1,
                'updated_by' => getUpdatedBy(),
                'updated_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Product has been deleted successfully.']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            Excel::import(new ProductImport, $request->file('excel_file'));
            return response()->json(['status' => 'success', 'message' => 'Products imported successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }
}
