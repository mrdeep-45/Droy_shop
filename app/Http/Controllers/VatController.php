<?php

namespace App\Http\Controllers;
use App\Models\VatModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
class VatController extends Controller
{
    //
     public function index()
    {
        $data = [
            'page_title' => 'Vat',
            'page_name' => 'Vat',
        ];

        return view('company.master.vat', $data);
    }

    public function getList()
    {
        try {
            $permissions = checkPermissions(get_index_route($this));
            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = VatModel::select(['vat_id', 'vat_no', 'created_by', 'created_at'])
                ->where('status', 0)
                ->orderBy('vat_id', 'desc');

            return DataTables::of($query)
                // Created By
                ->editColumn('created_by', function ($row) {
                    return get_createdby_name($row->created_by);
                })
                // Created At
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
                })
                ->addColumn('action', function ($row) use ($canUpdate, $canDelete) {
                    $buttons = '<div class="d-flex gap-1 justify-content-center">';

                    if ($canUpdate) {
                        $buttons .= '<button class="btn btn-sm btn-primary edit-group" data-id="'.$row->vat_id.'"><i class="fas fa-edit"></i> Edit</button>';
                    }

                    if ($canDelete) {
                        // Check if this Vat is already used in Mapping or Raw Material
                      
                            $buttons .= '<button class="btn btn-sm btn-danger delete-group" data-id="'.$row->vat_id.'"><i class="fas fa-trash"></i> Delete</button>';
                        
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
        $item = VatModel::where('vat_id', $id)->first();

        if (! $item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vat No not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $item,
        ]);
    }

    public function storeOrUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids = $request->input('vat_id');
            $names = $request->input('vat_no');
            $createdItems = [];

            if (empty($names) || ! is_array($names)) {
                return response()->json([
                    'errors' => ['vat_no.0' => ['At least one VAT No is required.']],
                ], 422);
            }

            if (! is_array($ids)) {
                $ids = $ids ? [$ids] : [];
            }

            $updated = false;
            $created = false;

            foreach ($names as $index => $name) {
                if (empty(trim($name))) {
                    return response()->json([
                        'errors' => ["vat_no.$index" => ["Vat No at position $index is required."]],
                    ], 422);
                }
                $id = $ids[$index] ?? null;

                $exists = VatModel::where('vat_no', $name)
                    ->where('status', 0)
                    ->when($id, fn ($q) => $q->where('vat_id', '!=', $id))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'errors' => ["vat_no.$index" => ["This VAT No '$name' already exists."]],
                    ], 422);
                }

                if ($id) {
                    $item = VatModel::find($id);
                    if ($item) {
                        $item->update([
                            'vat_no' => $name,
                            'updated_by' => getUpdatedBy(),
                            'updated_at' => now(),
                        ]);
                        $updated = true;
                    }
                } else {
                    $newItem = VatModel::create([
                        'vat_no' => $name,
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
                $message = 'VAT numbers updated and added successfully.';
            } elseif ($updated) {
                $message = 'VAT has been updated successfully.';
            } elseif ($created) {
                $message = 'VAT has been created successfully.';
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
            $id = $request->vat_id;
            $item = VatModel::find($id);

            if (! $item) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'VAT not found',
                ]);
            }

            // Prevent deletion from backend if it is already used
           

            $item->update([
                'status' => 1,
                'updated_by' => getUpdatedBy(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'VAT has been deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
