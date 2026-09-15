<?php

namespace App\Http\Controllers;
use App\Models\IndustryTypeModel;
use App\Models\CompanyModel; // Example relational dependency model
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
class IndustryTypeController extends Controller
{
    //
    public function index()
    {
        $data = [
            'page_title' => 'Industry Type',
            'page_name' => 'Industry Type',
        ];

        return view('company.master.industry_type', $data);
    }

   public function getList()
    {
        try {
            $permissions = checkPermissions(get_index_route($this));
            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = IndustryTypeModel::select(['industry_type_id', 'industry_type_name', 'created_by', 'created_at'])
                ->where('status', 0);
               

            return DataTables::of($query)

            ->orderColumn('industry_type_name', function ($query, $direction) {
                $query->orderBy('industry_type_name', $direction);
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
                        $buttons .= '<button class="btn btn-sm btn-primary edit-group" data-id="'.$row->industry_type_id.'"><i class="fas fa-edit"></i> Edit</button>';
                    }

                    if ($canDelete) {
                        // Removed the database column check that caused the error
                        $buttons .= '<button class="btn btn-sm btn-danger delete-group" data-id="'.$row->industry_type_id.'"><i class="fas fa-trash"></i> Delete</button>';
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
        $item = IndustryTypeModel::where('industry_type_id', $id)->first();

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Industry Type not found',
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
            $ids = $request->input('industry_type_id');
            $names = $request->input('industry_type_name');
            $createdItems = [];

            if (empty($names) || !is_array($names)) {
                return response()->json([
                    'errors' => ['industry_type_name.0' => ['At least one Industry Type Name is required.']],
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
                        'errors' => ["industry_type_name.$index" => ["Industry Type Name at position $index is required."]],
                    ], 422);
                }
                $id = $ids[$index] ?? null;

                $exists = IndustryTypeModel::where('industry_type_name', trim($name))
                    ->where('status', 0)
                    ->when($id, fn ($q) => $q->where('industry_type_id', '!=', $id))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'errors' => ["industry_type_name.$index" => ["The Industry Type Name '$name' already exists."]],
                    ], 422);
                }

                if ($id) {
                    $item = IndustryTypeModel::find($id);
                    if ($item) {
                        $item->update([
                            'industry_type_name' => trim($name),
                            'updated_by' => getUpdatedBy(),
                            'updated_at' => now(),
                        ]);
                        $updated = true;
                    }
                } else {
                    $newItem = IndustryTypeModel::create([
                        'industry_type_name' => trim($name),
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
                $message = 'Industry Types updated and added successfully.';
            } elseif ($updated) {
                $message = 'Industry Type has been updated successfully.';
            } elseif ($created) {
                $message = 'Industry Type has been created successfully.';
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
            $id = $request->industry_type_id;
            $item = IndustryTypeModel::find($id);

            if (!$item) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Industry Type not found',
                ]);
            }

            // Directly allow deletion changing status to 1
            $item->update([
                'status' => 1,
                'updated_by' => getUpdatedBy(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Industry Type has been deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
