<?php

namespace App\Http\Controllers;
use App\Models\ProductModel;
use App\Models\UnitModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
class UnitController extends Controller
{
    //
    public function index()
    {
        $data = [
            'page_title' => 'Unit',
            'page_name' => 'Unit',
        ];

        return view('company.master.unit', $data);
    }
    public function getList()
    {
        try {
            $permissions = checkPermissions(get_index_route($this));
            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = UnitModel::select(['unit_id', 'unit', 'created_by', 'created_at'])
                ->where('status', 0)
                ->orderBy('unit_id', 'desc');

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
                        $buttons .= '<button class="btn btn-sm btn-primary edit-group" data-id="'.$row->unit_id.'"><i class="fas fa-edit"></i> Edit</button>';
                    }

                    if ($canDelete) {
                        // Check if this Unit is already used in Product or Raw Material
                       
                            $buttons .= '<button class="btn btn-sm btn-danger delete-group" data-id="'.$row->unit_id.'"><i class="fas fa-trash"></i> Delete</button>';
                        
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
        $item = UnitModel::where('unit_id', $id)->first();

        if (! $item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unit not found',
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
            $ids = $request->input('unit_id');
            $names = $request->input('unit');
            $createdItems = [];
            if (empty($names) || ! is_array($names)) {
                return response()->json([
                    'errors' => ['unit.0' => ['At least one Unit is required.']],
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
                        'errors' => ["unit.$index" => ["Unit at position $index is required."]],
                    ], 422);
                }
                $id = $ids[$index] ?? null;
                $exists = UnitModel::where('unit', $name)
                    ->where('status', 0)
                    ->when($id, fn ($q) => $q->where('unit_id', '!=', $id))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'errors' => ["unit.$index" => ["This Unit '$name' already exists."]],
                    ], 422);
                }

                if ($id) {
                    $item = UnitModel::find($id);
                    if ($item) {
                        $item->update([
                            'unit' => $name,
                            'updated_by' => getUpdatedBy(),
                            'updated_at' => now(),
                        ]);
                        $updated = true;
                    }
                } else {
                    $newItem = UnitModel::create([
                        'unit' => $name,
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
                $message = 'Units updated and added successfully.';
            } elseif ($updated) {
                $message = 'Unit has been updated successfully.';
            } elseif ($created) {
                $message = 'Unit has been created successfully.';
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
            $id = $request->unit_id;
            $item = UnitModel::find($id);

            if (! $item) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unit not found',
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
                'message' => 'Unit has been deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

}
