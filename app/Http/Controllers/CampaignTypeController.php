<?php

namespace App\Http\Controllers;
use App\Models\CampaignTypeModel;
use App\Models\CampaignModel; // Swap with your actual structural Campaign Model if you wish to block deletion when used
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
class CampaignTypeController extends Controller
{
    //
    public function index()
    {
        $data = [
            'page_title' => 'Campaign Type',
            'page_name' => 'Campaign Type',
        ];

        return view('company.master.campaign_type', $data);
    }

    public function getList()
    {
        try {
            $permissions = checkPermissions(get_index_route($this));
            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = CampaignTypeModel::select(['campaign_type_id', 'campaign_type_name', 'created_by', 'created_at'])
                ->where('status', 0);
                

            return DataTables::of($query)
                ->orderColumn('campaign_type_name', function ($query, $direction) {
                    $query->orderBy('campaign_type_name', $direction);
                })
                ->editColumn('created_by', function ($row) {
                    return get_createdby_name($row->created_by);
                })
                ->orderColumn('created_by', function ($query, $direction) {
                    $query->orderBy('created_by', $direction);
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
                })
                ->orderColumn('created_at', function ($query, $direction) {
                    $query->orderBy('created_at', $direction);
                })
                ->addColumn('action', function ($row) use ($canUpdate, $canDelete) {
                    $buttons = '<div class="d-flex gap-1 justify-content-center">';

                    if ($canUpdate) {
                        $buttons .= '<button class="btn btn-sm btn-primary edit-group" data-id="'.$row->campaign_type_id.'"><i class="fas fa-edit"></i> Edit</button>';
                    }

                    if ($canDelete) {
                        $buttons .= '<button class="btn btn-sm btn-danger delete-group" data-id="'.$row->campaign_type_id.'"><i class="fas fa-trash"></i> Delete</button>';
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
        $item = CampaignTypeModel::where('campaign_type_id', $id)->first();

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Campaign Type record not found',
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
            $ids = $request->input('campaign_type_id');
            $names = $request->input('campaign_type_name');
            $createdItems = [];

            if (empty($names) || !is_array($names)) {
                return response()->json([
                    'errors' => ['campaign_type_name.0' => ['At least one Campaign Type Name is required.']],
                ], 422);
            }

            if (!is_array($ids)) {
                $ids = $ids ? [$ids] : [];
            }

            $updated = false;
            $created = false;

            foreach ($names as $index => $name) {
                if (empty(trim($name))) {
                    return response()->json([
                        'errors' => ["campaign_type_name.$index" => ["Campaign Type Name at position $index is required."]],
                    ], 422);
                }
                $id = $ids[$index] ?? null;

                $exists = CampaignTypeModel::where('campaign_type_name', trim($name))
                    ->where('status', 0)
                    ->when($id, fn ($q) => $q->where('campaign_type_id', '!=', $id))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'errors' => ["campaign_type_name.$index" => ["The Campaign Type Name '$name' already exists."]],
                    ], 422);
                }

                if ($id) {
                    $item = CampaignTypeModel::find($id);
                    if ($item) {
                        $item->update([
                            'campaign_type_name' => trim($name),
                            'updated_by' => getUpdatedBy(),
                            'updated_at' => now(),
                        ]);
                        $updated = true;
                    }
                } else {
                    $newItem = CampaignTypeModel::create([
                        'campaign_type_name' => trim($name),
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
                $message = 'Campaign Types updated and added successfully.';
            } elseif ($updated) {
                $message = 'Campaign Type has been updated successfully.';
            } elseif ($created) {
                $message = 'Campaign Type has been created successfully.';
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
            $id = $request->campaign_type_id;
            $item = CampaignTypeModel::find($id);

            if (!$item) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Campaign Type data not found',
                ]);
            }

            $item->update([
                'status' => 1,
                'updated_by' => getUpdatedBy(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Campaign Type has been deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
