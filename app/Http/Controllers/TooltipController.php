<?php

namespace App\Http\Controllers;

use App\Models\TooltipModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TooltipController extends Controller
{
    public function index()
    {
        $data = [
            'page_title' => 'Tooltip Master',
            'page_name'  => 'Tooltip Master',
        ];
        return view('company.master.tooltip', $data);
    }

    public function getList()
    {
        try {

            $permissions = checkPermissions(get_index_route($this));

            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = TooltipModel::select([
                    'tooltip_id',
                    'module_name',
                    'field_key',
                    'field_label',
                    'tooltip_text',
                    'created_by',
                    'created_at'
                ])
                ->where('status', 0)
                ->orderByDesc('tooltip_id');

            return DataTables::of($query)

                ->editColumn('created_by', function ($row) {
                    return get_createdby_name($row->created_by);
                })

                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)
                        ->format('d-m-Y H:i:s');
                })

                ->editColumn('tooltip_text', function ($row) {
                    $text = $row->tooltip_text;
                    // if (strlen($text) > 80) {
                    //     $text = Str::limit($text, 80, '...');
                    // }
                    return $text;
                })

                ->addColumn('action', function ($row) use ($canUpdate, $canDelete) {

                    $buttons = '<div class="d-flex gap-1 justify-content-center">';

                    if ($canUpdate) {
                        $buttons .= '
                        <button
                            class="btn btn-sm btn-primary edit-tooltip"
                            data-id="' . $row->tooltip_id . '">
                            <i class="fas fa-edit"></i> Edit
                        </button>';
                    }

                    if ($canDelete) {
                        $buttons .= '
                        <button
                            class="btn btn-sm btn-danger delete-tooltip"
                            data-id="' . $row->tooltip_id . '">
                            <i class="fas fa-trash"></i> Delete
                        </button>';
                    }

                    $buttons .= '</div>';

                    return $buttons;

                })

                ->addIndexColumn()

                ->rawColumns(['action'])

                ->make(true);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function edit($id)
    {
        $item = TooltipModel::find($id);

        if (!$item) {

            return response()->json([
                'status' => 'error',
                'message' => 'Tooltip not found'
            ], 404);

        }

        return response()->json([
            'status' => 'success',
            'data' => $item
        ]);
    }

    public function storeOrUpdate(Request $request)
    {
        DB::beginTransaction();

        try {

            $id = $request->input('tooltip_id');
            $moduleName = trim($request->input('module_name', ''));
            $fieldKey = trim($request->input('field_key', ''));
            $fieldLabel = trim($request->input('field_label', ''));
            $tooltipText = trim($request->input('tooltip_text', ''));

            // Validation
            $errors = [];

            if (empty($moduleName)) {
                $errors['module_name'] = ['Module Name is required.'];
            }

            if (empty($fieldLabel)) {
                $errors['field_label'] = ['Field Label is required.'];
            }

            if (empty($fieldKey)) {
                $errors['field_key'] = ['Field Key is required.'];
            }

            if (empty($tooltipText)) {
                $errors['tooltip_text'] = ['Tooltip Text is required.'];
            }

            if (!empty($errors)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Please fix the validation errors.',
                    'errors'  => $errors
                ], 422);
            }

            // Check unique field_key
            $exists = TooltipModel::where('field_key', $fieldKey)
                ->where('status', 0)
                ->when($id, function ($q) use ($id) {
                    $q->where('tooltip_id', '!=', $id);
                })
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This Field Key already exists.',
                    'errors'  => [
                        'field_key' => ['This Field Key already exists.']
                    ]
                ], 422);
            }

            if ($id) {

                $item = TooltipModel::find($id);

                if ($item) {

                    $item->update([
                        'module_name'  => $moduleName,
                        'field_key'    => $fieldKey,
                        'field_label'  => $fieldLabel,
                        'tooltip_text' => $tooltipText,
                        'updated_by'   => getUpdatedBy(),
                        'updated_at'   => now()
                    ]);

                    $message = 'Tooltip updated successfully.';

                } else {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Tooltip not found.'
                    ], 404);
                }

            } else {

                TooltipModel::create([
                    'module_name'  => $moduleName,
                    'field_key'    => $fieldKey,
                    'field_label'  => $fieldLabel,
                    'tooltip_text' => $tooltipText,
                    'status'       => 0,
                    'created_by'   => getCreatedBy(),
                    'created_at'   => now()
                ]);

                $message = 'Tooltip created successfully.';

            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => $message
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function destroy(Request $request)
    {
        try {

            $item = TooltipModel::find($request->tooltip_id);

            if (!$item) {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Tooltip not found'
                ]);

            }

            $item->update([
                'status'     => 1,
                'updated_by' => getUpdatedBy(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Tooltip deleted successfully.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);

        }
    }

    public function getTooltipByKey($key)
    {
        $tooltip = TooltipModel::where('field_key', $key)
            ->where('status', 0)
            ->first();

        if (!$tooltip) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tooltip not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $tooltip
        ]);
    }
}
