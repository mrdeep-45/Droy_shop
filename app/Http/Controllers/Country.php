<?php

namespace App\Http\Controllers;

use App\Models\CountryModel;
use App\Models\StateModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class Country extends Controller
{
    //
    public function index()
    {
        $page_title = 'Country ';
        $page_name = 'country';

        // return view('countries.index', compact('page_title', 'page_name'));
        return view('company.master.country', compact('page_title', 'page_name'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'country_name' => 'required|string|max:1000',

            ];
            $customAttributes = [
                'name' => 'Country Name',

            ];
            $validator = Validator::make($request->all(), $rules, [], $customAttributes);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                $firstField = array_key_first($errors);
                $firstMessage = $errors[$firstField][0];

                return response()->json([
                    'success' => false,
                    'field' => $firstField,
                    'message' => $firstMessage,
                ], 422);
            }
            $exists = CountryModel::where('country_name', $request->country_name)
                ->where('status', 0)
                ->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'field' => 'country_name',
                    'message' => 'The country name already exists.',
                ], 422);
            }
            $validated = $validator->validated();
            $validated['created_by'] = getCreatedBy();
            $validated['created_at'] = now();
            $validated['status'] = 0;
            CountryModel::create($validated);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Country created successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create Country.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function list(Request $request)
    {
        $query = CountryModel::select([
            'c_id',
            'country_name',
            'status',
            'created_at',
            'created_by',
        ])->where('status', 0);

        $permissions = checkPermissions(get_index_route($this));
        $canUpdate = $permissions['canUpdate'] ?? false;
        $canDelete = $permissions['canDelete'] ?? false;

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                return $row->status == 1 ? 'Inactive' : 'Active';
            })
            ->addColumn('created_by', function ($row) {
                return get_createdby_name($row->created_by);
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('d-m-Y h:i:s') : '-';
            })
            ->addColumn('action', function ($row) use ($canUpdate, $canDelete) {
                $btn = '<div class="d-flex gap-1 justify-content-center">';

                if ($canUpdate) {
                    $btn .= '<button class="btn btn-sm btn-primary edit-group" data-id="'.$row->c_id.'"><i class="fas fa-edit"></i> Edit</button>';
                }

                if ($canDelete) {
                    // Check if Country is used in Customer or Supplier
                    $isUsedInCustomer = class_exists(\App\Models\CustomerModel::class) && \App\Models\CustomerModel::where('country_id', $row->c_id)->where('status', 0)->exists();
                    $isUsedInSupplier = class_exists(\App\Models\SupplierModel::class) && \App\Models\SupplierModel::where('c_id', $row->c_id)->where('status', 0)->exists();

                    if ($isUsedInCustomer || $isUsedInSupplier) {
                        $btn .= '<button class="btn btn-sm btn-danger" disabled style="opacity: 0.5; cursor: not-allowed;" title="Cannot delete: Country is in use"> <i class="fas fa-trash"></i> Delete</button>';
                    } else {
                        $btn .= '<button class="btn btn-sm btn-danger delete-group" data-id="'.$row->c_id.'"><i class="fas fa-trash"></i> Delete</button>';
                    }
                }

                $btn .= '</div>';

                return $btn;
            })

            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit($id)
    {
        try {
            $country = CountryModel::where('c_id', $id)->first();

            if (! $country) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Country not found.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $country,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching country data: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'c_id' => 'required|exists:mst_country,c_id',
                'country_name' => 'required|string|max:1000',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                $firstField = array_key_first($errors);
                $firstMessage = $errors[$firstField][0];

                return response()->json([
                    'success' => false,
                    'field' => $firstField,
                    'message' => $firstMessage,
                ], 422);
            }

            $country = CountryModel::find($request->c_id);

            $country->country_name = $request->country_name;
            $country->updated_at = now();
            $country->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Country updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Failed to update Country.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
{
    $item = CountryModel::find($request->c_id);

    if (!$item) {
        return response()->json(['status'=>'error','message'=>'Not found']);
    }

    $isUsedInCustomer = class_exists(\App\Models\CustomerModel::class) && \App\Models\CustomerModel::where('country_id', $request->c_id)->where('status', 0)->exists();
    $isUsedInSupplier = class_exists(\App\Models\SupplierModel::class) && \App\Models\SupplierModel::where('c_id', $request->c_id)->where('status', 0)->exists();

    if ($isUsedInCustomer || $isUsedInSupplier) {
        return response()->json(['status' => 'error', 'message' => 'Cannot delete! This Country is already used in Customer or Supplier.']);
    }

    $item->update([
        'status' => 1,
        'updated_by' => getUpdatedBy(),
        'updated_at' => now()
    ]);

    return response()->json(['status'=>'success','message'=>'Deleted successfully']);
}

}
