<?php

namespace App\Http\Controllers;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class EnquiryStatus extends Controller
{
    //
    public function index()
    {
        $data = [
            'page_title' => 'Lead Status',
            'page_name'  => 'Lead Status',
           ];
        return view('company.master.lead_status', $data);
    }

       public function getList()
    {
        try {
            $data = LeadStatus::select(['status_id', 'title', 'created_by', 'created_at'])
                ->where('status', 0)
                ->orderBy('sort_order', 'asc')
                ->get()
                ->map(function ($row) {

                    // Production Safe checking
                    $isUsed = false;
                    try {
                        $isUsed = \App\Models\EnquiryManageModel::where('status_id', $row->status_id)->where('status', 0)->exists();
                    } catch (\Exception $e) {
                        $isUsed = false;
                    }

                    return [
                        'status_id'  => $row->status_id,
                        'title'      => $row->title,
                        'created_by' => get_createdby_name($row->created_by),
                        'created_at' => \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i'),
                        'is_used'    => $isUsed // Flag passed to blade
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data'   => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeOrUpdate(Request $request)
    {

        $company_id   = get_company_id();
        $created_type = Session::get('login_type');

        DB::beginTransaction();
        try {
            $ids   = $request->input('status_id');   // could be array or single value or null
            $names = $request->input('status_name'); // array of names

            if (is_string($names)) {
                $names = [$names];
            }

            if (empty($names) || ! is_array($names)) {
                // return response()->json(['status' => 'error', 'message' => 'At least one status name is required.'], 422);
                return response()->json([
                    'errors' => [
                        'status_name.0' => ['At least one status name is required.'],
                    ],
                ], 422);
            }

            if (! is_array($ids)) {
                $ids = $ids ? [$ids] : [];
            }

            $updated = false;
            $created = false;

            foreach ($names as $index => $name) {
                if (empty(trim($name))) {
                    // return response()->json(['status' => 'error', 'message' => "Status name at position $index is required."], 422);
                    return response()->json([
                        'errors' => [
                            "status_name.$index" => ["Status name at position $index is required."],
                        ],
                    ], 422);
                }

                $id = $ids[$index] ?? null;

                $exists = LeadStatus::where('title', $name)->where('status', 0)
                    ->when($id, fn($q) => $q->where('status_id', '!=', $id))
                    ->exists();

                if ($exists) {
                    //return response()->json(['status' => 'error', 'message' => "This status '$name' already exists."], 422);
                    return response()->json([
                        'errors' => [
                            "status_name.$index" => ["This Status '$name' already exists."],
                        ],
                    ], 422);
                }

                if ($id) {
                    $item = LeadStatus::find($id);
                    if ($item) {
                        $item->update([
                            'title'        => $name,
                            'company_id'   => $company_id,
                            'created_type' => $created_type,
                            'updated_by'   => getUpdatedBy(),
                            'updated_at'   => now(),
                        ]);
                        $updated = true;
                    }
                } else {
                    $maxSortOrder = LeadStatus::where('status', 0)->max('sort_order') ?? 0;
                    $newStatus = LeadStatus::create([
                        'title'        => $name,
                        'sort_order'   => $maxSortOrder + 1 + $index,
                        'company_id'   => $company_id,
                        'created_type' => $created_type,
                        'status'       => 0,
                        'created_by'   => getCreatedBy(),
                        'created_at'   => now(),
                    ]);
                    $created = true;
                }
            }
            DB::commit();
            if ($updated && $created) {
                $message = 'Statuses updated and added successfully.';
            } elseif ($updated) {
                $message = 'Statuses updated successfully.';
            } elseif ($created) {
                $message = 'New statuses added successfully.';
            } else {
                $message = 'No changes were made.';
            }

            return response()->json([
                'status' => 'success', 
                'message' => $message, 
                'id' => isset($newStatus) ? $newStatus->status_id : null,
                'status_name' => isset($newStatus) ? $newStatus->title : null
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        try {
            $order = $request->input('order');
            if (is_array($order)) {
                foreach ($order as $item) {
                    LeadStatus::where('status_id', $item['id'])->update([
                        'sort_order' => $item['order_no']
                    ]);
                }
            }
            return response()->json([
                'status'  => 'success',
                'message' => 'Order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {

            $group = LeadStatus::where('status_id', $id)
                ->first();

            if (! $group) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Status not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data'   => $group,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error fetching stock group: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        $status_id = $request->status_id;

        try {
            DB::beginTransaction();

            $stockGroup = LeadStatus::find($status_id);
            if (! $stockGroup) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Status not found.',
                ]);
            }

            // Production Safe checking
            $isUsed = false;
            try {
                $isUsed = \App\Models\EnquiryManageModel::where('status_id', $status_id)->where('status', 0)->exists();
            } catch (\Exception $e) {
                $isUsed = false;
            }

            if ($isUsed) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Cannot delete! This Lead Status is already used in Lead.',
                ]);
            }

            // Just update status to 1
            $stockGroup->update([
                'status'     => 1,
                'updated_at' => now(),
                'updated_by' => getUpdatedBy(),
            ]);

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Status marked as inactive.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ]);
        }
    }
}
