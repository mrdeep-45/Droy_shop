<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\GstModel;
use App\Models\HsnGstMapModel;
use App\Models\RawmaterialModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
class GstController extends Controller
{
    //
    public function index()
    {
        $data = [
            'page_title' => 'Gst',
            'page_name' => 'Gst',
        ];

        return view('company.master.gst', $data);
    }

    public function getList()
    {
        try {
            $permissions = checkPermissions(get_index_route($this));
            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = GstModel::select(['gst_id', 'gst_no', 'created_by', 'created_at'])
                ->where('status', 0)
                ->orderBy('gst_id', 'desc');

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
                        $buttons .= '<button class="btn btn-sm btn-primary edit-group" data-id="'.$row->gst_id.'"><i class="fas fa-edit"></i> Edit</button>';
                    }

                    if ($canDelete) {
                        // Check if this GST is already used in Mapping or Raw Material
                        $isUsedInMap = HsnGstMapModel::where('gst_id', $row->gst_id)
                            ->where('status', 0)
                            ->exists();

                        $isUsedInRM = RawmaterialModel::where('gst_id', $row->gst_id)
                            ->where('status', 0)
                            ->exists();

                        if ($isUsedInMap || $isUsedInRM) {
                            $buttons .= '<button class="btn btn-sm btn-danger" disabled style="opacity: 0.5; cursor: not-allowed;" title="Cannot delete: GST is in use"> <i class="fas fa-trash"></i> Delete</button>';
                        } else {
                            $buttons .= '<button class="btn btn-sm btn-danger delete-group" data-id="'.$row->gst_id.'"><i class="fas fa-trash"></i> Delete</button>';
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
        $item = GstModel::where('gst_id', $id)->first();

        if (! $item) {
            return response()->json([
                'status' => 'error',
                'message' => 'GST No not found',
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
            $ids = $request->input('gst_id');
            $names = $request->input('gst_no');
            $createdItems = [];

            if (empty($names) || ! is_array($names)) {
                return response()->json([
                    'errors' => ['gst_no.0' => ['At least one GST No is required.']],
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
                        'errors' => ["gst_no.$index" => ["GST No at position $index is required."]],
                    ], 422);
                }
                $id = $ids[$index] ?? null;

                $exists = GstModel::where('gst_no', $name)
                    ->where('status', 0)
                    ->when($id, fn ($q) => $q->where('gst_id', '!=', $id))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'errors' => ["gst_no.$index" => ["This GST No '$name' already exists."]],
                    ], 422);
                }

                if ($id) {
                    $item = GstModel::find($id);
                    if ($item) {
                        $item->update([
                            'gst_no' => $name,
                            'updated_by' => getUpdatedBy(),
                            'updated_at' => now(),
                        ]);
                        $updated = true;
                    }
                } else {
                    $newItem = GstModel::create([
                        'gst_no' => $name,
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
                $message = 'GST numbers updated and added successfully.';
            } elseif ($updated) {
                $message = 'GST has been updated successfully.';
            } elseif ($created) {
                $message = 'GST has been created successfully.';
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
            $id = $request->gst_id;
            $item = GstModel::find($id);

            if (! $item) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'GST No not found',
                ]);
            }

            // Prevent deletion from backend if it is already used
            $isUsedInMap = HsnGstMapModel::where('gst_id', $id)
                ->where('status', 0)
                ->exists();

            $isUsedInRM = RawmaterialModel::where('gst_id', $id)
                ->where('status', 0)
                ->exists();

            if ($isUsedInMap || $isUsedInRM) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete! This GST is already used in HSN Mapping or Raw Material.',
                ]);
            }

            $item->update([
                'status' => 1,
                'updated_by' => getUpdatedBy(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'GST has been deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
