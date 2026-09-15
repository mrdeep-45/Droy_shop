<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DealStage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DealStageController extends Controller
{
    //
    public function index()
    {
        $page_title = 'Deal Stage';
        $page_name = 'Deal Stage';

        //return view('countries.index', compact('page_title', 'page_name'));
        return view('company.master.dealstage', compact('page_title', 'page_name'));
    }

    public function getList()
    {
        try {
            $permissions = checkPermissions(get_index_route($this));
            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = DealStage::select(['id', 'stage_name', 'color','is_won', 'created_by', 'created_at'])
                ->where('status', 0)
                ->orderBy('order_no', 'asc');

            return DataTables::of($query)
            ->addColumn('won_toggle', function ($row) use ($canUpdate) {
                    $checked = $row->is_won ? 'checked' : '';
                    $label   = $row->is_won ? 'Won Stage' : 'Set as Won';
                    $labelClass = $row->is_won ? 'text-success fw-bold' : 'text-muted';
                    if ($canUpdate) {
                        return '
                        <div class="d-flex align-items-center gap-2">
                            <label class="won-toggle-label" style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;">
                                <span class="won-toggle-switch">
                                    <input type="checkbox" class="won-toggle-input toggle-won-stage" data-id="' . $row->id . '" data-name="' . htmlspecialchars($row->stage_name) . '" ' . $checked . ' style="display:none;">
                                    <span class="won-toggle-track ' . ($row->is_won ? 'active' : '') . '">
                                        <span class="won-toggle-thumb"></span>
                                    </span>
                                </span>
                                <span class="won-toggle-text ' . $labelClass . '" style="font-size:0.82rem;">' . $label . '</span>
                            </label>
                        </div>';
                    }
                    return $row->is_won ? '<span class="badge bg-success">Won</span>' : '<span class="text-muted">—</span>';
                })
                // Created By
                ->addColumn('created_by', function ($row) {
                    return get_createdby_name($row->created_by);
                })
                // Created At
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? Carbon::parse($row->created_at)->format('d-m-Y H:i:s') : '—';
                })
                ->addColumn('action', function ($row) use ($canUpdate, $canDelete) {
                    $buttons = '<div class="d-flex gap-1">';

                    if ($canUpdate) {
                        $buttons .= '
            <button class="btn btn-sm btn-primary edit-group" data-id="' . $row->id . '">
                <i class="fas fa-edit"></i> Edit
            </button>
        ';
                    }

                    if ($canDelete) {
                        $buttons .= '
            <button class="btn btn-sm btn-danger delete-group" data-id="' . $row->id . '">
                <i class="fas fa-trash"></i> Delete
            </button>
        ';
                    }

                    $buttons .= '</div>';

                    return $buttons;
                })

                ->addIndexColumn()
                ->rawColumns(['action', 'won_toggle'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function edit($id)
    {
        try {
            $group = DealStage::where('id', $id)->first();

            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dealstage not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $group  // $group->is_service will be included automatically
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching Dealstage: ' . $e->getMessage()
            ], 500);
        }
    }
    public function storeOrUpdate(Request $request)
    {



        $company_id = get_company_id();
        $created_type = Session::get('login_type');

        try {
            $ids = $request->input('id'); // could be array or single value or null
            $stagenames = $request->input('stage_name'); // array of stagenames
            $colors = $request->input('color', []); // array of is_service, default empty

            if (empty($stagenames) || !is_array($stagenames)) {
                // return response()->json(['status' => 'error', 'message' => 'At least one Category is required.'], 422);
                return response()->json([
                    'errors' => [
                        'stage_name.0' => ['At least one Deal-stage Name is required.']
                    ]
                ], 422);
            }

            if (!is_array($ids)) {
                $ids = $ids ? [$ids] : [];
            }

            $updated = false;
            $created = false;

            foreach ($stagenames as $index => $name) {
                if (empty(trim($name))) {
                    //return response()->json(['status' => 'error', 'message' => "Dealstage at position $index is required."], 422);
                    return response()->json([
                        'errors' => [
                            "stage_name.$index" => ["Dealstage at position $index is required."]
                        ]
                    ], 422);
                }

                $id = $ids[$index] ?? null;
                $color = $colors[$index] ?? null;

                $exists = DealStage::where('stage_name', $name)->where('status', 0)
                    ->when($id, fn($q) => $q->where('id', '!=', $id))
                    ->exists();

                if ($exists) {
                    //return response()->json(['status' => 'error', 'message' => "This Stage Name '$name' already exists."], 422);
                    return response()->json([
                        'errors' => [
                            "stage_name.$index" => ["This Dealstage '$name' already exists."]
                        ]
                    ], 422);
                }

                if ($id) {
                    $item = DealStage::find($id);
                    if ($item) {
                        $item->update([
                            'stage_name' => $name,
                            'company_id' => $company_id,
                            'created_type' => $created_type,
                            'color' => $color

                        ]);
                        $updated = true;
                    }
                } else {
                    $orderNo = DealStage::max('order_no') + 1;

                    DealStage::create([
                        'stage_name' => $name,
                        'company_id' => $company_id,
                        'created_type' => $created_type,
                        'color' => $color,
                        'status' => 0,
                        'order_no' => $orderNo

                    ]);
                    $created = true;
                }
            }

            if ($updated && $created) {
                $message = 'Deal Stage updated and added successfully.';
            } elseif ($updated) {
                $message = 'Deal Stage updated successfully.';
            } elseif ($created) {
                $message = 'New Deal Stage added successfully.';
            } else {
                $message = 'No changes were made.';
            }

            return response()->json(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function destroy(Request $request)
    {
        $id = $request->id;

        try {
            DB::beginTransaction();

            $dealStage = DealStage::find($id);
            if (!$dealStage) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dealstage not found.'
                ]);
            }

            if ($dealStage->is_won == 1 || $dealStage->is_lost == 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => "You cannot delete the Won/Lost stage '{$dealStage->stage_name}'."
                ]);
            }

            $dealStage->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Dealstage deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete Dealstage: ' . $e->getMessage()
            ]);
        }
    }

    public function updateOrder(Request $request)
    {
        try {
            foreach ($request->order as $item) {
                DealStage::where('id', $item['id'])->update([
                    'order_no' => $item['order_no']
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'         => 'required|integer|exists:mst_deal_stage,id',
            'stage_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        try {
            $stage = DealStage::find($request->id);

            $exists = DealStage::where('stage_name', $request->stage_name)
                ->where('status', 0)
                ->where('id', '!=', $request->id)
                ->exists();

            if ($exists) {
                return response()->json(['status' => 'error', 'message' => "A stage named '{$request->stage_name}' already exists."], 422);
            }

            $stage->update(['stage_name' => $request->stage_name]);

            return response()->json(['status' => 'success', 'message' => 'Stage name updated successfully.', 'stage_name' => $stage->stage_name]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function setWonStage(Request $request)
    {
        $id = $request->input('id');

        try {
            $stage = DealStage::find($id);

            if (!$stage) {
                return response()->json(['status' => false, 'message' => 'Stage not found.'], 404);
            }

            if ($stage->is_won == 1) {
                return response()->json(['status' => false, 'message' => 'This stage is already Won and cannot be changed.'], 400);
            }

            $stage->is_won = 1;
            $stage->is_lost = 0;
            $stage->save();

            return response()->json([
                'status'  => true,
                'is_won'  => 1,
                'message' => "'{$stage->stage_name}' is now a Won Stage.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function setLostStage(Request $request)
    {
        $id = $request->input('id');

        try {
            $stage = DealStage::find($id);

            if (!$stage) {
                return response()->json(['status' => false, 'message' => 'Stage not found.'], 404);
            }

            if ($stage->is_lost == 1) {
                return response()->json(['status' => false, 'message' => 'This stage is already Lost and cannot be changed.'], 400);
            }

            $stage->is_lost = 1;
            $stage->is_won = 0;
            $stage->save();

            return response()->json([
                'status'  => true,
                'is_lost' => 1,
                'message' => "'{$stage->stage_name}' is now a Lost Stage.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
