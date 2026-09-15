<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CityModel;
use App\Models\CountryModel;
use App\Models\StateModel;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    //

    public function cities(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = CityModel::select('city_id', 'city_name')
            ->where('status', 0);
            
        if ($search) {
            $query->where('city_name', 'like', "{$search}%");
        }
        
        applyRoleBasedCityFilter($query);
        
        $cities = $query->orderBy('city_name')->paginate($perPage);

        return response()->json($cities);
    }

    public function getLocationByCityId(int $cityId)
    {
        $city = DB::table('mst_city as c')
            ->leftJoin('mst_state as s', 's.state_id', '=', 'c.state_id')
            ->leftJoin('mst_country as co', 'co.c_id', '=', 'c.c_id')
            ->where('c.city_id', $cityId)
            ->select(
                'c.city_id',
                'c.city_name',
                'c.state_id',
                's.state_name',
                'c.c_id as country_id',
                'c.c_id',
                'co.country_name'
            )
            ->first();

        if (!$city) {
            return response()->json([
                'status' => false,
                'message' => 'City not found'
            ], 404);
        }

        $resData = [
            'city_id' => $city->city_id,
            'city_name' => $city->city_name,
            'state_id' => $city->state_id,
            'state_name' => $city->state_name ?? '',
            'country_id' => $city->country_id,
            'c_id' => $city->c_id,
            'country_name' => $city->country_name ?? ''
        ];

        return response()->json(array_merge([
            'status' => true,
            'data' => $resData
        ], $resData));
    }

    public function getLocationByCity(Request $request)
    {
        $cityId = $request->city_id ?? $request->id;

        $city = DB::table('mst_city as c')
            ->leftJoin('mst_state as s', 's.state_id', '=', 'c.state_id')
            ->leftJoin('mst_country as co', 'co.c_id', '=', 'c.c_id')
            ->where('c.city_id', $cityId)
            ->select(
                'c.city_id',
                'c.city_name',
                'c.state_id',
                's.state_name',
                'c.c_id as country_id',
                'c.c_id',
                'co.country_name'
            )
            ->first();

        if (!$city) {
            return response()->json(['error' => 'City not found'], 404);
        }

        $resData = [
            'city_id' => $city->city_id,
            'city_name' => $city->city_name,
            'state_id' => $city->state_id,
            'state_name' => $city->state_name ?? '',
            'country_id' => $city->country_id,
            'c_id' => $city->c_id,
            'country_name' => $city->country_name ?? ''
        ];

        return response()->json(array_merge([
            'status' => true,
            'data' => $resData
        ], $resData));
    }

    public function getCountries()
    {

        $countries = CountryModel::select('c_id AS id', 'country_name')->active()->get();
        return response()->json(['success' => true, 'data' => $countries]);
    }

    public function getAllCities(Request $request)
    {
        // return DB::table('mst_city')
        //     ->select('city_id as id', 'city_name as text')
        //     ->orderBy('city_name')
        //     ->get();
        $search = $request->search;
        $page = $request->page ?? 1;
        $stateId = $request->state_id;
        $fetchOne = $request->fetch_one ?? false;
        $perPage = 50000;

        $query = DB::table('mst_city')
            ->select('city_id as id', 'city_name as text');

        if ($fetchOne && $search) {

            return $query->where('city_id', $search)->first();
        }

        if ($search) {
            $query->where('city_name', 'like', '%' . $search . '%');
        }

        $total = $query->count();
        $results = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'results' => $results,
            'total_count' => $total
        ]);
    }

    public function loadAllCities(Request $request)
    {
        return DB::table('mst_city')
            ->select('city_id as id', 'city_name as text')
            ->orderBy('city_name')
            ->get();
    }

    public function getStates($countryId)
    {

        $states = StateModel::where('c_id', $countryId)->select('state_id AS id', 'state_name')->active()->get();
        return response()->json(['success' => true, 'data' => $states]);
    }

    public function getCities($stateId)
    {

        $cities = CityModel::where('state_id', $stateId)->select('city_id AS id', 'city_name')->active()->get();
        return response()->json(['success' => true, 'data' => $cities]);
    }
}
