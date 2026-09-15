<?php

namespace App\Http\Controllers;

use App\Models\CountryModel;
use App\Models\StateModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class State extends Controller
{
    //
    public function index()
    {
        $page_title = 'State';
        $page_name = 'state';
        $countrys = CountryModel::where('status', 0)
            ->orderBy('country_name', 'asc')
            ->get();

        // return view('countries.index', compact('page_title', 'page_name'));
        return view('company.master.state', compact('page_title', 'page_name', 'countrys'));
    }

    public function getStates()
    {
        try {
            $states = StateModel::with(['countryGroup' => function ($q) {
                $q->where('status', 0);
            }])
                ->where('status', 0)
                ->orderBy('created_at', 'desc')
                ->get();

            // Filter out those without an active country
            $filtered = $states->filter(fn ($item) => ! is_null($item->countryGroup));

            $grouped = $filtered->groupBy('c_id');
            $formatted = [];

            foreach ($grouped as $c_id => $items) {
                $first = $items->first();
                $stateIds = $items->pluck('state_id')->toArray();

                // Check if ANY state in this group is used in Customer or Supplier
                $isUsedInCustomer = class_exists(\App\Models\CustomerModel::class) && \App\Models\CustomerModel::whereIn('state_id', $stateIds)->where('status', 0)->exists();
                $isUsedInSupplier = class_exists(\App\Models\SupplierModel::class) && \App\Models\SupplierModel::whereIn('state_id', $stateIds)->where('status', 0)->exists();
                $isUsed = $isUsedInCustomer || $isUsedInSupplier;

                $formatted[] = [
                    'c_id' => $c_id,
                    'is_used' => $isUsed,
                    'country_name' => $first->countryGroup->country_name,
                    'state_name' => $items->pluck('state_name')->toArray(),
                    'state_code' => $items->pluck('state_code')->toArray(),
                    'state_id' => $stateIds,
                    'created_by' => get_createdby_name($first->created_by),
                    'created_at' => $first->created_at ? Carbon::parse($first->created_at)->format('d-m-Y H:i:s') : '-',
                ];
            }

            return response()->json(['status' => 'success', 'data' => $formatted]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    public function storeOrUpdateStates(Request $request)
    {
        try {
            $cId = $request->c_id;
            $stateNames = $request->state_name ?? [];
            $stateCodes = $request->state_code ?? [];
            $stateIds = $request->state_id ?? [];

            $errors = [];
            $seenPairs = [];

            foreach ($stateNames as $index => $name) {
                $name = trim($name);
                $code = trim($stateCodes[$index] ?? '');
                $id = $stateIds[$index] ?? null;

                $nameLower = strtolower($name);
                $codeLower = strtolower($code);
                $pairKey = $nameLower.'___'.$codeLower;

                // Validation: Required
                if ($name === '') {
                    $errors["state_name.$index"] = 'State name is required.';
                }

                // In-form duplicate validation
                if (isset($seenPairs[$pairKey])) {
                    $errors["state_name.$index"] = 'Duplicate state name and code in form.';
                } else {
                    $seenPairs[$pairKey] = true;
                }

                // DB-level duplicate check
                if ($name && $code) {
                    $query = StateModel::where('c_id', $cId)
                        ->whereRaw('LOWER(state_name) = ?', [$nameLower])
                        ->whereRaw('LOWER(state_code) = ?', [$codeLower])
                        ->where('status', 0);

                    if (! empty($id)) {
                        $query->where('state_id', '!=', $id);
                    }

                    if ($query->exists()) {
                        $errors["state_name.$index"] = 'This state name and code already exists.';
                    }
                }
            }

            // Return validation errors
            if (! empty($errors)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $errors,
                ], 422);
            }

            // Proceed with insert/update
            $updatedCount = 0;
            $insertedCount = 0;

            foreach ($stateNames as $index => $name) {
                $code = $stateCodes[$index] ?? '';
                $id = $stateIds[$index] ?? null;

                if ($id) {
                    // Update
                    StateModel::where('state_id', $id)->update([
                        'c_id' => $cId,
                        'state_name' => $name,
                        'state_code' => $code,
                        'updated_at' => now(),
                        'updated_by' => getUpdatedBy(),
                    ]);
                    $updatedCount++;
                } else {
                    // Insert
                    StateModel::create([
                        'c_id' => $cId,
                        'state_name' => $name,
                        'state_code' => $code,
                        'status' => 0,
                        'created_at' => now(),
                        'created_by' => getCreatedBy(),
                    ]);
                    $insertedCount++;
                }
            }

            // Response
            $message = match (true) {
                $updatedCount && $insertedCount => 'States saved and updated successfully.',
                $updatedCount => 'States updated successfully.',
                default => 'States added successfully.',
            };

            return response()->json([
                'status' => 'success',
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: '.$e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $states = StateModel::where('c_id', $id)
                ->where('status', 0)
                ->get(['state_id', 'state_name', 'state_code', 'c_id']);

            if ($states->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No states found for this country',
                ], 404);
            }

            foreach ($states as $state) {
                $isUsedInCustomer = class_exists(\App\Models\CustomerModel::class) && \App\Models\CustomerModel::where('state_id', $state->state_id)->where('status', 0)->exists();
                $isUsedInSupplier = class_exists(\App\Models\SupplierModel::class) && \App\Models\SupplierModel::where('state_id', $state->state_id)->where('status', 0)->exists();
                $state->is_used = $isUsedInCustomer || $isUsedInSupplier;
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'c_id' => $id,
                    'states' => $states,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching states: '.$e->getMessage(),
            ], 500);
        }
    }

    public function deleteByCountry($id)
    {
        try {
            $stateIds = StateModel::where('c_id', $id)->pluck('state_id');
            $isUsedInCustomer = class_exists(\App\Models\CustomerModel::class) && \App\Models\CustomerModel::whereIn('state_id', $stateIds)->where('status', 0)->exists();
            $isUsedInSupplier = class_exists(\App\Models\SupplierModel::class) && \App\Models\SupplierModel::whereIn('state_id', $stateIds)->where('status', 0)->exists();
            $isUsed = $isUsedInCustomer || $isUsedInSupplier;

            if ($isUsed) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete! One or more States under this Country are already used in Customer or Supplier.',
                ]);
            }

            $deleted = StateModel::where('c_id', $id)
                ->where('status', 0)
                ->update([
                    'status' => 1,
                    'updated_by' => getUpdatedBy(),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'status' => 'success',
                'message' => $deleted ? 'All states deleted successfully.' : 'No states found to delete.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting states: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $isUsedInCustomer = class_exists(\App\Models\CustomerModel::class) && \App\Models\CustomerModel::where('state_id', $id)->where('status', 0)->exists();
            $isUsedInSupplier = class_exists(\App\Models\SupplierModel::class) && \App\Models\SupplierModel::where('state_id', $id)->where('status', 0)->exists();
            $isUsed = $isUsedInCustomer || $isUsedInSupplier;

            if ($isUsed) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete! This State is already used in Customer or Supplier.',
                ]);
            }

            $updated = StateModel::where('state_id', $id)
                ->where('status', 0)
                ->update([
                    'status' => 1,
                    'updated_at' => now(),
                    'updated_by' => getUpdatedBy(),
                ]);

            if ($updated) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'State removed from the list.',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'State not found or already removed.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update state: '.$e->getMessage(),
            ], 500);
        }
    }
}
