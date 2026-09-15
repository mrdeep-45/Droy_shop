<?php

namespace App\Http\Controllers;

use App\Models\CityModel;
use App\Models\CountryModel;
use App\Models\CustomerModel;
use App\Models\StateModel;
use App\Models\SupplierModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class City extends Controller
{
    //
    public function index()
    {
        $page_title = 'City';
        $page_name = 'city';
        $countrys = CountryModel::where('status', 0)
            ->orderBy('country_name', 'asc')
            ->get();
        $states = StateModel::where('status', 0)
            ->orderBy('state_name', 'asc')
            ->get();

        // return view('countries.index', compact('page_title', 'page_name'));
        return view('company.master.city', compact('page_title', 'page_name', 'countrys', 'states'));
    }

    public function getCities()
    {
        try {
            // Raw DB query to avoid loading thousands of Eloquent objects
            $cities = DB::table('mst_city')
                ->leftJoin('mst_country', 'mst_country.c_id', '=', 'mst_city.c_id')
                ->leftJoin('mst_state', 'mst_state.state_id', '=', 'mst_city.state_id')
                ->where('mst_city.status', 0)
                ->select([
                    'mst_city.city_id',
                    'mst_city.c_id',
                    'mst_city.state_id',
                    'mst_city.city_name',
                    'mst_city.created_by',
                    'mst_city.created_at',
                    'mst_country.country_name',
                    'mst_state.state_name'
                ])
                ->get();

            // Fetch all unique city IDs used by customers/suppliers to avoid N+1 queries
            $usedCityIds = [];
            if (class_exists(CustomerModel::class)) {
                $usedCityIds = array_merge($usedCityIds, DB::table('tbl_customer')->where('status', 0)->pluck('city_id')->toArray());
            }
            if (class_exists(SupplierModel::class)) {
                $usedCityIds = array_merge($usedCityIds, DB::table('tbl_supplier')->where('status', 0)->pluck('city_id')->toArray());
            }
            $usedCityIdsMap = array_flip(array_unique(array_filter($usedCityIds))); // O(1) lookup map

            // Memoized helper lookup to avoid redundant queries for creators
            $staffCache = [];
            $getCreatedByNameCached = function($created_by) use (&$staffCache) {
                if (empty($created_by)) {
                    return 'System';
                }
                if (array_key_exists($created_by, $staffCache)) {
                    return $staffCache[$created_by];
                }
                $name = get_createdby_name($created_by);
                $staffCache[$created_by] = $name ?: 'System';
                return $staffCache[$created_by];
            };

            // Group in memory using raw array structure
            $grouped = [];
            foreach ($cities as $city) {
                $key = $city->c_id . '-' . $city->state_id;
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'c_id' => $city->c_id,
                        'state_id' => $city->state_id,
                        'country_name' => $city->country_name ?? 'N/A',
                        'state_name' => $city->state_name ?? 'N/A',
                        'city_name' => [],
                        'city_id' => [],
                        'created_by' => $getCreatedByNameCached($city->created_by),
                        'created_at' => $city->created_at ? Carbon::parse($city->created_at)->format('d-m-Y H:i:s') : '-',
                    ];
                }
                $grouped[$key]['city_name'][] = $city->city_name;
                $grouped[$key]['city_id'][] = $city->city_id;
            }

            // Add the 'is_used' flag to each grouped item
            $formatted = [];
            foreach ($grouped as $group) {
                $isUsed = false;
                foreach ($group['city_id'] as $cid) {
                    if (isset($usedCityIdsMap[$cid])) {
                        $isUsed = true;
                        break;
                    }
                }
                $group['is_used'] = $isUsed;
                $formatted[] = $group;
            }

            return response()->json(['status' => 'success', 'data' => $formatted]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeOrUpdateCities(Request $request)
    {
        try {
            $cId = $request->c_id;
            $stateId = $request->state_id;
            $cityNames = $request->city_name ?? [];
            $cityIds = $request->city_id ?? []; // Add this

            $errors = [];

            foreach ($cityNames as $index => $name) {
                $name = trim($name);
                $cityId = $cityIds[$index] ?? null;

                if ($name === '') {
                    $errors["city_name.$index"] = 'City name is required.';
                }

                if ($name) {
                    $query = CityModel::where('c_id', $cId)
                        ->where('state_id', $stateId)
                        ->where('city_name', $name)
                        ->where('status', 0);

                    // Exclude current city when editing
                    if (! empty($cityId)) {
                        $query->where('city_id', '!=', $cityId);
                    }

                    if ($query->exists()) {
                        $errors["city_name.$index"] = 'City already exists.';
                    }
                }
            }

            if (! empty($errors)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $errors,
                ], 422);
            }

            foreach ($cityNames as $index => $name) {
                $cityId = $cityIds[$index] ?? null;

                if ($cityId) {
                    // Update existing city
                    CityModel::where('city_id', $cityId)->update([
                        'c_id' => $cId,
                        'state_id' => $stateId,
                        'city_name' => $name,
                        'updated_by' => getUpdatedBy(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Insert new city
                    CityModel::create([
                        'c_id' => $cId,
                        'state_id' => $stateId,
                        'city_name' => $name,
                        'status' => 0,
                        'created_by' => getCreatedBy(),
                        'created_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Cities saved successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: '.$e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request)
    {
        try {
            $cId = $request->c_id;
            $stateId = $request->state_id;

            $cities = CityModel::where('c_id', $cId)
                ->where('state_id', $stateId)
                ->where('status', 0)
                ->get(['city_id', 'city_name', 'c_id', 'state_id']);

            if ($cities->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No cities found for the selected country and state.',
                ], 404);
            }

            foreach ($cities as $city) {
                $isUsedInCustomer = class_exists(CustomerModel::class) && CustomerModel::where('city_id', $city->city_id)->where('status', 0)->exists();
                $isUsedInSupplier = class_exists(SupplierModel::class) && SupplierModel::where('city_id', $city->city_id)->where('status', 0)->exists();
                $city->is_used = ($isUsedInCustomer || $isUsedInSupplier);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'c_id' => $cId,
                    'state_id' => $stateId,
                    'cities' => $cities,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching cities: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {

            $isUsedInCustomer = class_exists(CustomerModel::class) && CustomerModel::where('city_id', $id)->where('status', 0)->exists();
            $isUsedInSupplier = class_exists(SupplierModel::class) && SupplierModel::where('city_id', $id)->where('status', 0)->exists();

            if ($isUsedInCustomer || $isUsedInSupplier) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete! This City is already used in Customer or Supplier.',
                ]);
            }

            $updated = CityModel::where('city_id', $id)
                ->where('status', 0)
                ->update([
                    'status' => 1,
                    'updated_at' => now(),
                    'updated_by' => getUpdatedBy(),
                ]);

            if ($updated) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'City removed from the list.',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'City not found or already removed.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update city: '.$e->getMessage(),
            ], 500);
        }
    }

    public function deleteByState($id)
    {
        try {

            $cityIds = CityModel::where('state_id', $id)->pluck('city_id');
            $isUsedInCustomer = class_exists(CustomerModel::class) && CustomerModel::whereIn('city_id', $cityIds)->where('status', 0)->exists();
            $isUsedInSupplier = class_exists(SupplierModel::class) && SupplierModel::whereIn('city_id', $cityIds)->where('status', 0)->exists();

            if ($isUsedInCustomer || $isUsedInSupplier) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete! One or more Cities are used in Customer or Supplier.',
                ]);
            }

            $deleted = CityModel::where('state_id', $id)
                ->where('status', 0)
                ->update([
                    'status' => 1,
                    'updated_by' => getUpdatedBy(),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'status' => 'success',
                'message' => $deleted ? 'All cities deleted successfully.' : 'No cities found to delete.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting cities: '.$e->getMessage(),
            ], 500);
        }
    }

    public function deleteByCountryAndState(Request $request)
    {
        $cId = $request->c_id;
        $stateId = $request->state_id;

        try {

            $cityIds = CityModel::where('c_id', $cId)->where('state_id', $stateId)->pluck('city_id');
            $isUsedInCustomer = class_exists(CustomerModel::class) && CustomerModel::whereIn('city_id', $cityIds)->where('status', 0)->exists();
            $isUsedInSupplier = class_exists(SupplierModel::class) && SupplierModel::whereIn('city_id', $cityIds)->where('status', 0)->exists();

            if ($isUsedInCustomer || $isUsedInSupplier) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete! One or more Cities are used in Customer or Supplier.',
                ]);
            }

            $deleted = CityModel::where('c_id', $cId)
                ->where('state_id', $stateId)
                ->update([
                    'status' => 1,
                    'updated_by' => getUpdatedBy(),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'status' => 'success',
                'message' => $deleted ? 'Cities deleted successfully.' : 'No matching cities found.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting cities: '.$e->getMessage(),
            ], 500);
        }
    }

    public function ajaxCities(Request $request)
    {
        $search = $request->get('q', '');

        $cities = CityModel::where('city_name', 'like', "%{$search}%")
            ->where('status', 0) // only active
            ->limit(10)
            ->get(['city_id', 'city_name']);

        $formatted = $cities->map(function ($c) {
            return ['id' => $c->city_id, 'text' => $c->city_name];
        });

        return response()->json(['results' => $formatted]);
    }

    public function getCityDetails($city_id)
    {
        $city = CityModel::with(['state.countryGroup'])->find($city_id);

        if (! $city) {
            return response()->json(['status' => false, 'message' => 'City not found']);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'city_id' => $city->city_id,
                'city_name' => $city->city_name,
                'state_id' => $city->state_id,
                'state_name' => $city->state->state_name ?? '',
                'state_code' => $city->state->state_code ?? '',
                'country_id' => $city->state->countryGroup->c_id ?? '',
                'country_name' => $city->state->countryGroup->country_name ?? '',
            ],
        ]);
    }
}
