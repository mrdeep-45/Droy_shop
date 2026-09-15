<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use App\Models\TermsMaster;
use App\Models\Customer;
use App\Models\ProductCategoryModel;
use App\Models\UnitModel;
use App\Models\HsnGstMapModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetDataController extends Controller
{
    public function getUnits()
    {
        $units = UnitModel::active()->get();
        return response()->json([
            'status' => 'success',
            'data' => $units
        ]);
    }

    public function getGst()
    {
        $gsts = DB::table('mst_gst')->where('status', 0)->orderByRaw('CAST(gst_no AS UNSIGNED) ASC')->get();
        return response()->json([
            'status' => 'success',
            'data' => $gsts
        ]);
    }

    public function getCategories()
    {
        $categories = ProductCategoryModel::active()->get();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function getHsn()
    {
        $hsn = HsnGstMapModel::with('gst')->where('status', 0)->get();
        return response()->json([
            'status' => 'success',
            'data' => $hsn
        ]);
    }

    public function getRawMaterials()
    {
        $raw_materials = DB::table('tbl_raw_material')
            ->leftJoin('tbl_unit', 'tbl_unit.unit_id', '=', 'tbl_raw_material.unit_id')
            ->leftJoin('mst_gst', 'mst_gst.gst_id', '=', 'tbl_raw_material.gst_id')
            ->where('tbl_raw_material.status', 0)
            ->select(
                'tbl_raw_material.rm_id',
                'tbl_raw_material.name',
                'tbl_raw_material.unit_id',
                'tbl_unit.unit',
                'mst_gst.gst_no'
            )
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $raw_materials
        ]);
    }

    public function getProductsForQuotation(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = ProductModel::with('category')->where('status', 0);
        $query->orderBy('prod_name', 'asc');


        if (!empty($search)) {
            $query->where('prod_name', 'like', "%{$search}%");
        }

        $items = $query->paginate($perPage, ['*'], 'page', $page);

        $results = [];

        foreach ($items as $item) {
            $categoryName = $item->category ? $item->category->name : '';
            $prodText = $item->prod_name;
            if ($categoryName) {
                $prodText .= ' (' . $categoryName . ')';
            }
            $results[] = [
                'id' => $item->product_id,
                'text' => $prodText,
                'gst' => getGstById($item->gst),
                'unit_price' => $item->prod_amount
            ];
        }

        return response()->json([
            'results' => $results,
            'total_count' => $items->total()
        ]);
    }

    public function getTermsCondition(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = TermsMaster::query()->where('status', 0);

        if (!empty($search)) {
            $query->where('module', 'like', "%{$search}%");
        }

        $items = $query->paginate($perPage, ['*'], 'page', $page);

        $results = [];
        foreach ($items as $item) {
            $results[] = [
                'id' => $item->term_id,
                'text' => $item->module,
                'terms' => $item->terms
            ];
        }

        return response()->json([
            'results' => $results,
            'total_count' => $items->total()
        ]);
    }
    public function getCustomersData(Request $request)
    {
        $search = $request->get('search');
        $page = $request->get('page', 1);

        $customers = Customer::where('status', 0)
            ->when($search, function ($query) use ($search) {
                $query->where('company_name', 'like', "%{$search}%");
            })
            ->orderBy('company_name', 'asc')
            ->paginate(20, ['customer_id', 'company_name'], 'page', $page);

        return response()->json([
            'results' => $customers->map(function ($customer) {
                return [
                    'id' => $customer->customer_id,
                    'text' => $customer->company_name,
                ];
            }),
            'pagination' => [
                'more' => $customers->hasMorePages(),
            ],
        ]);
    }

    public function getCustomerDetails($id)
    {
        $customer = Customer::select(
            'customer_id',
            'company_name',
            'contact_person',
            'phone',
            'email',
            'city_id',
            'state_id',
            'country_id',
            'gst_no'
        )
            ->where('customer_id', $id)
            ->firstOrFail();

        $city = $customer->city_id ? DB::table('mst_city')->where('city_id', $customer->city_id)->first() : null;
        $state = $customer->state_id ? DB::table('mst_state')->where('state_id', $customer->state_id)->first() : null;
        $country = $customer->country_id ? DB::table('mst_country')->where('c_id', $customer->country_id)->first() : null;

        return response()->json([
            'customer' => $customer,
            'city' => $city,
            'state' => $state,
            'country' => $country,
        ]);
    }
}
