<?php

namespace App\Http\Controllers;

use App\Models\CityModel;
use App\Models\EnquiryManageModel;
use App\Models\HsnGstMapModel;
use App\Models\Manager;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;
use App\Models\RoleModel;
use App\Models\Staffmodel;
use App\Models\UnitModel;
use App\Models\Customer;
use App\Models\Deal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

use App\Services\DealLogService;

class LeadController extends Controller
{
    protected $logService;

    public function __construct(DealLogService $logService)
    {
        $this->logService = $logService;
    }

    private function createPassword($name)
    {
        $firstName = explode(' ', trim($name))[0];
        return strtolower($firstName) . '@123';
    }

    public function index()
    {
        $page_title = 'Lead Management';
        $page_name = 'Lead Management';
        $source = DB::table('mst_lead_source')->select('source_id', 'title', 'source_type')->where('status', 0)->orderBy('title', 'asc')->get();
        $service = ProductModel::where('status', 0)->where('category_id', 1)->orderBy('prod_name', 'ASC')->get();
        $lead_status = DB::table('lead_status')->select('status_id', 'title')->where('status', 0)->orderBy('sort_order', 'asc')->get();
        $enquiry_no = (new EnquiryManageModel())->get_enquiry_no();
        $categories = ProductCategoryModel::active()->get();
        $category = $categories;
        $hsn_data = HsnGstMapModel::with('gst')
            ->where('status', 0)
            ->get();
        $unit = UnitModel::active()->get();
        $stage = DB::table('mst_deal_stage')->select('stage_name', 'id')->whereNotIn('id', [6, 12, 13, 16])
            ->where('status', 0)->orderBy('order_no', 'asc')->get();
        $deal_stage = $stage;
        $industries = DB::table('mst_industry_type')->select('industry_type_id', 'industry_type_name')->where('status', 0)->orderBy('industry_type_name', 'asc')->get();

        $staffList = Staffmodel::where('status', 0)->orderBy('staff_name', 'asc')->get();

        $loggedInStaff = null;
        if (session('staff_id')) {
            $loggedInStaff = Staffmodel::find(session('staff_id'));
        }

        $gst_rates = DB::table('mst_gst')->where('status', 0)->orderBy('gst_no', 'asc')->get();

        $creators = DB::table('enquiry_manage')
            ->where('status', 0)
            ->select('created_by')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->created_by,
                    'name' => get_createdby_name($item->created_by)
                ];
            })
            ->filter(function ($item) {
                return !empty($item->name) && $item->name !== '-';
            })
            ->values();


        return view('company.crm.lead.index', compact('page_title', 'page_name', 'source', 'service', 'lead_status', 'enquiry_no', 'creators', 'category', 'categories', 'hsn_data', 'unit', 'staffList', 'stage', 'deal_stage', 'industries', 'loggedInStaff', 'gst_rates'));
    }


    public function search(Request $request)
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
            'alt_phone',
            'email',
            'city_id',
            'state_id',
            'country_id'
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

    public function searchCity(Request $request)
    {
        $search = $request->get('search');
        $page = $request->get('page', 1);
        $leadType = $request->get('lead_type');
        $limit = 20;

        $query = DB::table('mst_city')
            ->where('status', 0);

        if ($search) {
            $query->where('city_name', 'like', "%{$search}%");
        }

        if (!empty($leadType)) {
            $indiaId = DB::table('mst_country')->where('country_name', 'India')->value('c_id') ?? 99;
            if ($leadType === 'Domestic') {
                $query->where('c_id', $indiaId);
            } elseif ($leadType === 'Export' || $leadType === 'International') {
                $query->where('c_id', '!=', $indiaId);
            }
        }
        
        applyRoleBasedCityFilter($query);

        $total = $query->count();

        $cities = $query
            ->orderBy('city_name')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();

        return response()->json([
            'results' => $cities->map(function ($city) {
                return [
                    'id' => $city->city_id,
                    'text' => $city->city_name
                ];
            }),
            'pagination' => [
                'more' => ($page * $limit) < $total
            ]
        ]);
    }

    public function ajaxStoreProduct(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'prod_name' => 'required|string|max:255',
            'unit_id' => 'nullable|integer',
            'prod_amount' => 'required|numeric|min:0',
            'prod_desc' => 'nullable|string',
            'gst' => 'nullable|integer',
        ], [
            'category_id.required' => 'Please select Category.',
            'prod_name.required' => 'Please enter Product Name.',
            'prod_amount.required' => 'Please enter Product Rate.',
        ]);

        try {
            $categoryId = $request->category_id;
            $name = trim($request->prod_name);
            $unitId = $request->unit_id;
            $amount = $request->prod_amount;
            $desc = $request->prod_desc;
            $gstId = $request->gst;

            $exists = ProductModel::where('prod_name', $name)->where('status', 0)->exists();
            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => "The Product Name '$name' already exists."
                ], 422);
            }

            $latestProduct = ProductModel::orderBy('product_id', 'desc')->first();
            $nextId = $latestProduct ? ($latestProduct->product_id + 1) : 1;
            $generatedCode = 'PROD-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $product = ProductModel::create([
                'prod_code' => $generatedCode,
                'category_id' => $categoryId,
                'prod_name' => $name,
                'unit_id' => $unitId,
                'prod_amount' => $amount,
                'prod_desc' => $desc,
                'gst' => $gstId,
                'status' => 0,
                'created_by' => getCreatedBy(),
                'created_at' => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'id' => $product->product_id,
                'prod_name' => $product->prod_name,
                'prod_amount' => $product->prod_amount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ajaxStoreIndustry(Request $request)
    {
        $request->validate([
            'industry_type_name' => 'required|string|max:255',
        ], [
            'industry_type_name.required' => 'Please enter Industry Name.',
        ]);

        try {
            $name = trim($request->industry_type_name);

            $exists = DB::table('mst_industry_type')->where('industry_type_name', $name)->where('status', 0)->exists();
            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => "The Industry '$name' already exists."
                ], 422);
            }

            $id = DB::table('mst_industry_type')->insertGetId([
                'industry_type_name' => $name,
                'status' => 0,
                'created_by' => getCreatedBy(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Industry created successfully',
                'id' => $id,
                'industry_type_name' => $name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create industry: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCityDetails($id)
    {
        $city = DB::table('mst_city as c')
            ->join('mst_state as s', 's.state_id', '=', 'c.state_id')
            ->join('mst_country as co', 'co.c_id', '=', 'c.c_id')
            ->where('c.city_id', $id)
            ->select(
                'c.city_id',
                'c.city_name',
                's.state_id',
                's.state_name',
                'co.c_id',
                'co.country_name'
            )
            ->first();

        if (!$city) {
            return response()->json(null);
        }

        return response()->json($city);
    }

    public function getStaffByType(Request $request)
    {
        $type = $request->get('type'); // 'Domestic' or 'Export'

        $query = Staffmodel::where('status', 0)->orderBy('staff_name', 'asc');

        if ($type) {
            $query->where(function ($q) use ($type) {
                $q->where('staff_type', $type)
                  ->orWhere('staff_type', 'Both')
                  ->orWhereNull('staff_type')
                  ->orWhere('staff_type', '');
            });
        }

        $staffList = $query->get(['staff_id', 'staff_name', 'staff_type']);

        return response()->json([
            'status' => true,
            'staff' => $staffList->map(function ($s) {
                $typeLabel = $s->staff_type ? " ({$s->staff_type})" : '';
                return [
                    'id' => $s->staff_id,
                    'name' => $s->staff_name . $typeLabel,
                    'type' => $s->staff_type
                ];
            })
        ]);
    }

    public function ajaxStore(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['nullable', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
            'gst_no' => 'nullable|string|max:50',
            'pan_no' => 'nullable|string|max:50',
            'city_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
            'address' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $customer = new Customer();
            $customer->company_name = $request->company_name;
            $customer->contact_person = $request->contact_person;
            $customer->email = $request->email;
            $customer->phone = $request->phone;
            $customer->gst_no = $request->gst_no;
            $customer->pan_no = $request->pan_no;
            $customer->city_id = $request->city_id;
            $customer->state_id = $request->state_id;
            $customer->country_id = $request->country_id;
            $customer->address = $request->address;
            $customer->status = 0; // Active
            $customer->created_by = getCreatedBy() ?? 1;
            $customer->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Customer created successfully',
                'customer' => [
                    'id' => $customer->customer_id,
                    'text' => $customer->company_name . ' (' . $customer->contact_person . ')',
                    'company_name' => $customer->company_name,
                    'contact_person' => $customer->contact_person,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create customer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function convertToDeal(Request $request)
    {
        if (!$request->deal_name && $request->has('product_ids')) {
            $productNames = DB::table('tbl_product')
                ->whereIn('product_id', $request->product_ids)
                ->pluck('prod_name')
                ->implode(', ');
            $request->merge(['deal_name' => $productNames]);
        }

        $request->validate([
            'lead_id' => 'required',
            'deal_name' => 'required',
            'deal_amount' => 'required|numeric',
            'closing_date' => 'required',
            'deal_stage' => 'required',
            'assigned_to' => 'required',
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'mobile_no' => ['nullable', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
            'email_address' => 'required|email|max:255',
            'industry_id' => 'nullable|integer',
            'customer_id' => 'nullable|integer|exists:tbl_customer,customer_id',
            'lead_type' => 'required|string|in:Domestic,Export',
            'iwo_no' => 'nullable|string|max:255',
            'dispatch_date' => 'nullable|date_format:d-m-Y',
            'gst_no' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city_id' => 'nullable',
            'state_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $enquiry = EnquiryManageModel::findOrFail($request->lead_id);

            // Handle city ID creation or lookup if text tag is passed
            $cityId = $request->city_id;
            if ($cityId && !is_numeric($cityId)) {
                $existingCity = DB::table('mst_city')
                    ->where('city_name', 'like', $cityId)
                    ->first();
                if ($existingCity) {
                    $cityId = $existingCity->city_id;
                } else {
                    $cityId = DB::table('mst_city')->insertGetId([
                        'city_name' => $cityId,
                        'state_id' => $request->state_id ?: 0,
                        'c_id' => $request->country_id ?: 0,
                        'status' => 0,
                        'created_by' => getCreatedBy(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Create or update Customer details
            $customerId = $request->customer_id;
            if (!$customerId) {
                $customer = Customer::create([
                    'company_name' => $request->company_name,
                    'contact_person' => $request->contact_person,
                    'email' => $request->email_address,
                    'phone' => $request->mobile_no,
                    'gst_no' => $request->gst_no,
                    'city_id' => $cityId ?: null,
                    'state_id' => $request->state_id ?: null,
                    'country_id' => $request->country_id ?: null,
                    'address' => $request->address,
                    'status' => 0,
                    'created_by' => getCreatedBy(),
                ]);
                $customerId = $customer->customer_id;
            } else {
                $customer = Customer::find($customerId);
                if ($customer) {
                    $customer->update([
                        'company_name' => $request->company_name,
                        'contact_person' => $request->contact_person,
                        'email' => $request->email_address,
                        'phone' => $request->mobile_no,
                        'gst_no' => $request->gst_no,
                        'city_id' => $cityId ?: null,
                        'state_id' => $request->state_id ?: null,
                        'country_id' => $request->country_id ?: null,
                        'address' => $request->address,
                    ]);
                }
            }

            // Update Lead details and mark as converted
            $enquiry->update([
                'enquiry_name' => $request->deal_name,
                'customer_id' => $customerId ?: null,
                'company_name' => $request->company_name,
                'contact_person_name' => $request->contact_person,
                'phone' => $request->mobile_no,
                'email' => $request->email_address,
                'industry_id' => $request->industry_id ?: null,
                'lead_type' => $request->lead_type,
                'city_id' => $cityId ?: null,
                'state_id' => $request->state_id ?: null,
                'countries_id' => $request->country_id ?: null,
                'status' => 1
            ]);

            // Get old products list
            $oldProductIds = DB::table('tbl_enquiry_items')
                ->where('enquiry_id', $enquiry->enquiry_id)
                ->pluck('product_id')
                ->toArray();
            
            $oldProductNames = DB::table('tbl_product')
                ->whereIn('product_id', $oldProductIds)
                ->pluck('prod_name')
                ->toArray();
            $oldProductsStr = !empty($oldProductNames) ? implode(', ', $oldProductNames) : 'None';

            // Sync products if passed
            if ($request->has('product_ids')) {
                $existingItems = DB::table('tbl_enquiry_items')
                    ->where('enquiry_id', $enquiry->enquiry_id)
                    ->get()
                    ->keyBy('product_id')
                    ->toArray();

                $newProductIds = $request->product_ids;

                // Delete products not in the new list
                $productIdsToDelete = array_diff(array_keys($existingItems), $newProductIds);
                if (!empty($productIdsToDelete)) {
                    DB::table('tbl_enquiry_items')
                        ->where('enquiry_id', $enquiry->enquiry_id)
                        ->whereIn('product_id', $productIdsToDelete)
                        ->delete();
                }

                // Insert/Update new products
                foreach ($newProductIds as $productId) {
                    $productInfo = DB::table('tbl_product')->where('product_id', $productId)->first();
                    $price = $productInfo ? $productInfo->prod_amount : 0;
                    
                    // Fetch GST value
                    $gstVal = 0;
                    if ($productInfo && $productInfo->gst) {
                        $gstVal = DB::table('mst_gst')->where('gst_id', $productInfo->gst)->value('gst_no') ?? 0;
                    }

                    if (isset($existingItems[$productId])) {
                        // Update existing (preserve ID!)
                        DB::table('tbl_enquiry_items')
                            ->where('id', $existingItems[$productId]->id)
                            ->update([
                                'quantity' => 1,
                                'price' => $price,
                                'gst' => $gstVal,
                            ]);
                    } else {
                        // Insert new
                        DB::table('tbl_enquiry_items')->insert([
                            'enquiry_id' => $enquiry->enquiry_id,
                            'product_id' => $productId,
                            'quantity' => 1,
                            'price' => $price,
                            'gst' => $gstVal,
                        ]);
                    }
                }
            }

            // Get new products list and calculate diffs
            $newProductIds = $request->input('product_ids', []);
            $newProductNames = DB::table('tbl_product')
                ->whereIn('product_id', $newProductIds)
                ->pluck('prod_name')
                ->toArray();
            $newProductsStr = !empty($newProductNames) ? implode(', ', $newProductNames) : 'None';

            $addedIds = array_diff($newProductIds, $oldProductIds);
            $removedIds = array_diff($oldProductIds, $newProductIds);

            // Log individual added products
            foreach ($addedIds as $pId) {
                $pName = DB::table('tbl_product')->where('product_id', $pId)->value('prod_name') ?: 'Unknown';
                DB::table('enquiry_history')->insert([
                    'enquiry_id'  => $enquiry->enquiry_id,
                    'change_type' => 'Product Added',
                    'reason'      => 'Product "' . $pName . '" added while converting Lead to Deal.',
                    'table_name'  => 'enquiry_manage',
                    'changed_by'  => getCreatedBy(),
                    'changed_at'  => now(),
                ]);
            }

            // Log individual removed products
            foreach ($removedIds as $pId) {
                $pName = DB::table('tbl_product')->where('product_id', $pId)->value('prod_name') ?: 'Unknown';
                DB::table('enquiry_history')->insert([
                    'enquiry_id'  => $enquiry->enquiry_id,
                    'change_type' => 'Product Removed',
                    'reason'      => 'Product "' . $pName . '" removed from the Deal.',
                    'table_name'  => 'enquiry_manage',
                    'changed_by'  => getCreatedBy(),
                    'changed_at'  => now(),
                ]);
            }

            // Log product list update summary
            if (!empty($addedIds) || !empty($removedIds)) {
                DB::table('enquiry_history')->insert([
                    'enquiry_id'  => $enquiry->enquiry_id,
                    'change_type' => 'Product List Updated',
                    'reason'      => 'Product list updated from ' . $oldProductsStr . ' to ' . $newProductsStr . '.',
                    'table_name'  => 'enquiry_manage',
                    'changed_by'  => getCreatedBy(),
                    'changed_at'  => now(),
                ]);
            }

            // Log Lead converted to Deal
            DB::table('enquiry_history')->insert([
                'enquiry_id'  => $enquiry->enquiry_id,
                'change_type' => 'Lead Converted',
                'reason'      => 'Lead converted to Deal.',
                'table_name'  => 'enquiry_manage',
                'changed_by'  => getCreatedBy(),
                'changed_at'  => now(),
            ]);

            $deal = Deal::create([
                'lead_id' => $enquiry->enquiry_id,
                'customer_id' => $customerId,
                'deal_name' => $request->deal_name,
                'iwo_no' => $request->iwo_no,
                'dispatch_date' => $request->dispatch_date ? Carbon::createFromFormat('d-m-Y', $request->dispatch_date)->format('Y-m-d') : null,
                'type' => $request->deal_type,
                'deal_type' => $request->lead_type ?: ($enquiry->lead_type ?? 'Domestic'),
                'amount' => $request->deal_amount,
                'gst_no' => $request->gst_no,
                'closing_date' => Carbon::createFromFormat('d-m-Y', $request->closing_date)->format('Y-m-d'),
                'deal_stage' => $request->deal_stage,
                'assigned_to' => $request->assigned_to,
                'status' => 0,
                'deal_status' => 0,
                'description' => $request->description,
                'created_by' => getCreatedBy(),
                'created_at' => now(),
            ]);

            $this->logService->logConvert($deal);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Lead successfully converted to deal!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }



    public function edit($id)
    {
        $enquiry = EnquiryManageModel::with(['customer', 'city', 'state', 'country', 'sourceInfo', 'leadStatus', 'products.product', 'assignedStaff'])->find($id);

        if (!$enquiry) {
            return response()->json(['status' => false, 'message' => 'Lead not found']);
        }

        $products = $enquiry->products->map(function ($item) {
            return [
                'id' => $item->id,
                'enquiry_id' => $item->enquiry_id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'prod_name' => $item->product ? $item->product->prod_name : 'Unknown',
            ];
        });

        $industryName = null;
        if ($enquiry->industry_id) {
            $industryName = DB::table('mst_industry_type')->where('industry_type_id', $enquiry->industry_id)->value('industry_type_name');
        }

        return response()->json([
            'status' => true,
            'data' => array_merge($enquiry->toArray(), [
                'lead_type' => $enquiry->lead_type,
                'industry_id' => $enquiry->industry_id,
                'industry_name' => $industryName,
            ]),
            'products' => $products
        ]);
    }

    public function store(Request $request)
    {

        $validated = $request->validate(
            [
                'enquiry_name' => 'required',
                'enquiry_date' => 'required',
                'source' => 'required',
                 'industry_id' => 'required|integer',
                'mobile_no' => ['nullable', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
                'email_address' => 'required|email|max:255',
                'alt_phone' => ['nullable', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
                'city_id' => 'required',
                'enquiry_status' => 'required',
                'lead_type' => 'nullable|string|in:Domestic,Export',
               

                'product_id' => 'required|array',
                'product_id.*' => 'required',
                'qty' => 'nullable|array',
                'qty.*' => 'nullable|numeric',
                'amount' => 'nullable|array',
                'amount.*' => 'nullable|numeric',

                'deal_amount' => 'required_if:convert_to_deal,1|nullable|numeric',
                'closing_date' => 'required_if:convert_to_deal,1',
                'deal_stage' => 'required_if:convert_to_deal,1',
            ],
            [
                'city_id.required' => 'Please select a city.',
                'industry_id.required' => 'Please select an industry.',
                'industry_id.integer' => 'Please select a valid industry.',
                'deal_amount.required_if' => 'Please enter the Deal Amount.',
                'deal_amount.numeric' => 'Deal Amount must be a valid number.',

                'closing_date.required_if' => 'Please select the Closing Date.',

                'deal_stage.required_if' => 'Please select the Deal Stage.',
            ]
        );
        DB::beginTransaction();

        try {
            $detailedChanges = [];
            $leadId = $request->lead_id;

            if ($leadId) {
                // Update existing
                $enquiry = EnquiryManageModel::findOrFail($leadId);
                $oldAssignedTo = $enquiry->assigned_to;
                $oldStatusId = $enquiry->status_id;

                // Capture old values for diff calculation
                $oldEnquiryName = $enquiry->enquiry_name;
                $oldContactPerson = $enquiry->contact_person_name;
                $oldPhone = $enquiry->phone;
                $oldAltPhone = $enquiry->alternate_phone_1;
                $oldEmail = $enquiry->email;
                $oldCompanyName = $enquiry->company_name;
                $oldSourceId = $enquiry->source;
                $oldNotes = $enquiry->notes;
                $oldCountryId = $enquiry->countries_id;
                $oldStateId = $enquiry->state_id;
                $oldCityId = $enquiry->city_id;

                $enquiry->update([
                    'enquiry_name' => $request->enquiry_name,
                    'enquiry_date' => Carbon::createFromFormat('d-m-Y', $request->enquiry_date)->format('Y-m-d'),
                    'customer_id' => $request->customer_id,
                    'company_name' => $request->company_name,
                    'contact_person_name' => $request->contact_person,
                    'source' => $request->source,
                    'assigned_to' => $request->assign_to,
                    'phone' => $request->mobile_no,
                    'email' => $request->email_address,
                    'alternate_phone_1' => $request->alt_phone,
                    'status_id' => $request->enquiry_status,
                    'city_id' => $request->city_id,
                    'state_id' => $request->state_id,
                    'countries_id' => $request->country_id,
                    'notes' => $request->remarks,
                    'lead_type' => $request->lead_type,
                    'industry_id' => $request->industry_id ?: null,
                    'updated_by' => getUpdatedBy(),
                ]);

                // Calculate detailed changes
                if ((string)$oldEnquiryName !== (string)$request->enquiry_name) {
                    $detailedChanges[] = "Lead Name from \"{$oldEnquiryName}\" to \"{$request->enquiry_name}\"";
                }
                if ((string)$oldContactPerson !== (string)$request->contact_person) {
                    $detailedChanges[] = "Contact Person from \"{$oldContactPerson}\" to \"{$request->contact_person}\"";
                }
                if ((string)$oldPhone !== (string)$request->mobile_no) {
                    $detailedChanges[] = "Phone from \"{$oldPhone}\" to \"{$request->mobile_no}\"";
                }
                if ((string)$oldAltPhone !== (string)$request->alt_phone) {
                    $detailedChanges[] = "Alt Phone from \"{$oldAltPhone}\" to \"{$request->alt_phone}\"";
                }
                if ((string)$oldEmail !== (string)$request->email_address) {
                    $detailedChanges[] = "Email from \"{$oldEmail}\" to \"{$request->email_address}\"";
                }
                if ((string)$oldCompanyName !== (string)$request->company_name) {
                    $detailedChanges[] = "Company Name from \"{$oldCompanyName}\" to \"{$request->company_name}\"";
                }
                if ((int)$oldSourceId !== (int)$request->source) {
                    $oldSourceName = DB::table('mst_lead_source')->where('source_id', $oldSourceId)->value('title') ?: 'None';
                    $newSourceName = DB::table('mst_lead_source')->where('source_id', $request->source)->value('title') ?: 'None';
                    $detailedChanges[] = "Source from \"{$oldSourceName}\" to \"{$newSourceName}\"";
                }
                if ((int)$oldStatusId !== (int)$request->enquiry_status) {
                    $oldStatusName = DB::table('lead_status')->where('status_id', $oldStatusId)->value('title') ?: 'None';
                    $newStatusName = DB::table('lead_status')->where('status_id', $request->enquiry_status)->value('title') ?: 'None';
                    $detailedChanges[] = "Status from \"{$oldStatusName}\" to \"{$newStatusName}\"";
                }
                if ((string)$oldNotes !== (string)$request->remarks) {
                    $detailedChanges[] = "Remarks from \"{$oldNotes}\" to \"{$request->remarks}\"";
                }
                if ((int)$oldCountryId !== (int)$request->country_id) {
                    $oldCountry = DB::table('mst_country')->where('c_id', $oldCountryId)->value('country_name') ?: 'None';
                    $newCountry = DB::table('mst_country')->where('c_id', $request->country_id)->value('country_name') ?: 'None';
                    $detailedChanges[] = "Country from \"{$oldCountry}\" to \"{$newCountry}\"";
                }
                if ((int)$oldStateId !== (int)$request->state_id) {
                    $oldState = DB::table('mst_state')->where('state_id', $oldStateId)->value('state_name') ?: 'None';
                    $newState = DB::table('mst_state')->where('state_id', $request->state_id)->value('state_name') ?: 'None';
                    $detailedChanges[] = "State from \"{$oldState}\" to \"{$newState}\"";
                }
                if ((int)$oldCityId !== (int)$request->city_id) {
                    $oldCity = DB::table('mst_city')->where('city_id', $oldCityId)->value('city_name') ?: 'None';
                    $newCity = DB::table('mst_city')->where('city_id', $request->city_id)->value('city_name') ?: 'None';
                    $detailedChanges[] = "City from \"{$oldCity}\" to \"{$newCity}\"";
                }
                if ((int)$oldAssignedTo !== (int)$request->assign_to) {
                    $oldStaff = DB::table('mst_staff')->where('staff_id', $oldAssignedTo)->value('staff_name') ?: 'None';
                    $newStaff = DB::table('mst_staff')->where('staff_id', $request->assign_to)->value('staff_name') ?: 'None';
                    $detailedChanges[] = "Assigned To from \"{$oldStaff}\" to \"{$newStaff}\"";
                }

                // Check for status change
                if ((int)$oldStatusId !== (int)$request->enquiry_status) {
                    $oldStatusName = DB::table('lead_status')->where('status_id', $oldStatusId)->value('title') ?: 'None';
                    $newStatusName = DB::table('lead_status')->where('status_id', $request->enquiry_status)->value('title') ?: 'None';
                    
                    DB::table('enquiry_history')->insert([
                        'enquiry_id' => $enquiry->enquiry_id,
                        'change_type' => 'Lead Status Changed',
                        'reason' => 'Lead status changed from "' . $oldStatusName . '" to "' . $newStatusName . '".',
                        'table_name' => 'enquiry_manage',
                        'changed_by' => getCreatedBy(),
                        'changed_at' => now(),
                    ]);
                }

                // Check for assignment change
                if ((int)$oldAssignedTo !== (int)$request->assign_to) {
                    $oldStaff = DB::table('mst_staff')->where('staff_id', $oldAssignedTo)->value('staff_name') ?: 'None';
                    $newStaff = DB::table('mst_staff')->where('staff_id', $request->assign_to)->value('staff_name') ?: 'None';
                    
                    DB::table('enquiry_history')->insert([
                        'enquiry_id' => $enquiry->enquiry_id,
                        'change_type' => 'Lead Shared',
                        'reason' => 'Lead assignment changed from "' . $oldStaff . '" to "' . $newStaff . '".',
                        'table_name' => 'enquiry_manage',
                        'changed_by' => getCreatedBy(),
                        'changed_at' => now(),
                    ]);
                }

            } else {
                // Create new
                $enquiry = EnquiryManageModel::create([
                    'enquiry_no' => EnquiryManageModel::get_enquiry_no(),
                    'enquiry_name' => $request->enquiry_name,
                    'enquiry_date' => Carbon::createFromFormat('d-m-Y', $request->enquiry_date)->format('Y-m-d'),
                    'customer_id' => $request->customer_id,
                    'company_name' => $request->company_name,
                    'contact_person_name' => $request->contact_person,
                    'source' => $request->source,
                    'assigned_to' => $request->assign_to,
                    'phone' => $request->mobile_no,
                    'email' => $request->email_address,
                    'alternate_phone_1' => $request->alt_phone,
                    'status_id' => $request->enquiry_status,
                    'city_id' => $request->city_id,
                    'state_id' => $request->state_id,
                    'countries_id' => $request->country_id,
                    'notes' => $request->remarks,
                    'lead_type' => $request->lead_type,
                    'industry_id' => $request->industry_id ?: null,
                    'status' => $request->boolean('convert_to_deal') ? 1 : 0,
                    'created_by' => getCreatedBy(),
                ]);

                if ($request->assign_to) {
                    $newStaff = DB::table('mst_staff')->where('staff_id', $request->assign_to)->value('staff_name') ?: 'None';
                    DB::table('enquiry_history')->insert([
                        'enquiry_id' => $enquiry->enquiry_id,
                        'change_type' => 'Lead Shared',
                        'reason' => 'Lead assigned to "' . $newStaff . '".',
                        'table_name' => 'enquiry_manage',
                        'changed_by' => getCreatedBy(),
                        'changed_at' => now(),
                    ]);
                }
            }

            if ($request->has('product_id')) {
                $existingItems = DB::table('tbl_enquiry_items')->where('enquiry_id', $enquiry->enquiry_id)->get()->keyBy('id')->toArray();
                $processedIds = [];

                foreach ($request->product_id as $i => $productId) {
                    $quantity = isset($request->qty[$i]) ? $request->qty[$i] : 1;
                    $price = isset($request->amount[$i]) ? $request->amount[$i] : null;

                    if ($price === null) {
                        $productInfo = DB::table('tbl_product')->where('product_id', $productId)->first();
                        $price = $productInfo ? $productInfo->prod_amount : 0;
                    } else {
                        $productInfo = DB::table('tbl_product')->where('product_id', $productId)->first();
                    }

                    // Fetch GST value
                    $gstVal = 0;
                    if ($productInfo && $productInfo->gst) {
                        $gstVal = DB::table('mst_gst')->where('gst_id', $productInfo->gst)->value('gst_no') ?? 0;
                    }

                    $matchId = null;
                    foreach ($existingItems as $id => $item) {
                        if ($item->product_id == $productId && !in_array($id, $processedIds)) {
                            $matchId = $id;
                            break;
                        }
                    }

                    if ($matchId) {
                        DB::table('tbl_enquiry_items')->where('id', $matchId)->update([
                            'quantity' => $quantity,
                            'price' => $price,
                            'gst' => $gstVal,
                        ]);
                        $processedIds[] = $matchId;
                    } else {
                        DB::table('tbl_enquiry_items')->insert([
                            'enquiry_id' => $enquiry->enquiry_id,
                            'product_id' => $productId,
                            'quantity' => $quantity,
                            'price' => $price,
                            'gst' => $gstVal,
                        ]);
                    }
                }

                $idsToDelete = array_diff(array_keys($existingItems), $processedIds);
                if (!empty($idsToDelete)) {
                    DB::table('tbl_enquiry_items')->whereIn('id', $idsToDelete)->delete();
                }
            } else {
                DB::table('tbl_enquiry_items')->where('enquiry_id', $enquiry->enquiry_id)->delete();
            }

            if (!$leadId && $request->boolean('convert_to_deal')) {
                $deal = Deal::create([
                    'lead_id' => $enquiry->enquiry_id,
                    'customer_id' => $request->customer_id,
                    'deal_name' => $request->enquiry_name,
                    'type' => $request->deal_type,
                    'deal_type' => $request->lead_type ?: ($enquiry->lead_type ?? 'Domestic'),
                    'amount' => $request->deal_amount,
                    'gst_no' => $request->gst_no,
                    'closing_date' => Carbon::createFromFormat('d-m-Y', $request->closing_date)->format('Y-m-d'),
                    'deal_stage' => $request->deal_stage,
                    'assigned_to' => $request->deal_assigned_to ?: $request->assign_to,
                    'status' => 0,
                    'deal_status_id' => 0,
                    'description' => $request->description,
                    'created_by' => getCreatedBy(),
                    'created_at' => now(),
                ]);

                $this->logService->logConvert($deal);
            }

            $companyName = DB::table('tbl_customer')
                ->where('customer_id', $request->customer_id)
                ->value('company_name');

            $reason = 'Enquiry ' . ($leadId ? 'updated' : 'created') . ' for customer "' . ($companyName ?? 'N/A') . '".';
            if ($leadId && !empty($detailedChanges)) {
                $reason = "Lead details updated: " . implode(', ', $detailedChanges);
            }

            DB::table('enquiry_history')->insert([
                'enquiry_id' => $enquiry->enquiry_id,
                'change_type' => $leadId ? 'Enquiry Updated' : 'Enquiry Created',
                'reason' => $reason,
                'table_name' => 'enquiry_manage',
                'changed_by' => getCreatedBy(),
                'changed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Lead saved successfully',
                'data' => $enquiry,
                'new_enquiry_no' => (new EnquiryManageModel())->get_enquiry_no(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to save: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getFilteredLeadsQuery(Request $request)
    {
        $data = EnquiryManageModel::with([
            'customer',
            'sourceInfo',
            'leadStatus',
            'assignedStaff',
            'products.product',
            'city.state'
        ])->where('status', 0);

        // --- Staff Scope: Only show leads relevant to the logged-in staff ---
        if (session('login_type') === 'Staff') {
            $staffId = session('staff_id');

            $data->where(function ($q) use ($staffId) {
                // 1. Lead directly assigned to or created by the staff
                $q->where('assigned_to', $staffId)
                    ->orWhere('created_by', $staffId)

                    // 2. Lead has a note assigned to or created by this staff
                    ->orWhereIn('enquiry_id', function ($sub) use ($staffId) {
                        $sub->select('reference_id')
                            ->from('tbl_crm_notes')
                            ->where('reference_type', 'lead')
                            ->where('status', 0)
                            ->where(function ($w) use ($staffId) {
                                $w->where('assigned_to', $staffId)
                                  ->orWhere('created_by', $staffId);
                            });
                    })

                    // 3. Lead has a scheduled call assigned to or created by this staff
                    ->orWhereIn('enquiry_id', function ($sub) use ($staffId) {
                        $sub->select('reference_id')
                            ->from('tbl_crm_calls')
                            ->where('reference_type', 'lead')
                            ->where('status', 0)
                            ->where(function ($w) use ($staffId) {
                                $w->where('assigned_to', $staffId)
                                  ->orWhere('created_by', $staffId);
                            });
                    });
            });
        }
        // --- End Staff Scope ---

        if ($request->filled('company')) {
            $data->where('customer_id', $request->company);
        }

        if ($request->filled('contact_person')) {
            $data->where(function ($q) use ($request) {
                $q->where('contact_person_name', 'like', '%' . $request->contact_person . '%')
                    ->orWhereHas('customer', function ($q2) use ($request) {
                        $q2->where('contact_person', 'like', '%' . $request->contact_person . '%');
                    });
            });
        }

        if ($request->filled('city')) {
            $data->where('city_id', $request->city);
        }

        if ($request->filled('product')) {
            $data->whereHas('products', function ($q) use ($request) {
                $q->where('product_id', $request->product);
            });
        }

        if ($request->filled('created_by')) {
            $data->where('created_by', $request->created_by);
        }

        if ($request->filled('assigned_to')) {
            $data->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('lead_date')) {
            $dates = explode(' to ', $request->lead_date);
            if (count($dates) == 2) {
                $start = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
                $end = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d');
                $data->whereBetween('enquiry_date', [$start, $end]);
            } else {
                $start = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
                $data->whereDate('enquiry_date', $start);
            }
        }

        if (!$request->has('order')) {
            $data->orderByDesc('enquiry_id');
        }

        return $data;
    }

    public function enquiryList(Request $request)
    {
        if ($request->ajax()) {

            $data = $this->getFilteredLeadsQuery($request);

            return DataTables::of($data)

                ->addIndexColumn()

                ->addColumn('lead_details', function ($row) {
                    $perms = checkPermissions('lead.index');
                    $canAccessDetails = $perms['hasAnyPermission'] ?? false;

                    $u = route('lead.details', encrypt($row->enquiry_id));
                    $url = $canAccessDetails ? $u : 'javascript:void(0);';

                    $notesText = !empty($row->notes) ? e($row->notes) : (!empty($row->remarks) ? e($row->remarks) : '');
                    $hasNotes = !empty($notesText);

                    $tooltipTitle = 'Lead Name';
                    if ($hasNotes) {
                        $tooltipTitle .= '<br><span style="color:#d1d5db;">' . $notesText . '</span>';
                    }

                    $safeTitle = htmlspecialchars($tooltipTitle, ENT_QUOTES, 'UTF-8');

                    $html = '<a href="' . $url . '"><strong class="text-primary" data-bs-toggle="tooltip" data-bs-html="true" title="' . $safeTitle . '">' . e($row->enquiry_name) . '</strong></a><br>';
                    $html .= '<small class="text-muted" data-bs-toggle="tooltip" title="Lead No"><i class="fas fa-hashtag text-secondary"></i> ' . e($row->enquiry_no) . '</small><br>';
                    $html .= '<small class="text-muted" data-bs-toggle="tooltip" title="Lead Date"><i class="fas fa-calendar-alt text-warning"></i> ' . date('d-M-Y', strtotime($row->enquiry_date)) . '</small>';

                    return $html;
                })
                ->addColumn('company_details', function ($row) {
                    $company = $row->customer ? $row->customer->company_name : ($row->company_name ?: '-');
                    $city = $row->city ? $row->city->city_name : '';
                    return '
                    <strong class="text-dark" data-bs-toggle="tooltip" title="Company Name">' . $company . '</strong><br>
                    <small class="text-muted" data-bs-toggle="tooltip" title="City"><i class="fas fa-map-marker-alt text-danger"></i> ' . ($city ?: 'N/A') . '</small>
                ';
                })
                ->addColumn('contact_details', function ($row) {
                    $html = '';
                    if (!empty($row->contact_person_name)) {
                        $html .= '<strong class="text-dark" data-bs-toggle="tooltip" title="Contact Person"><i class="fas fa-user text-primary"></i> ' . $row->contact_person_name . '</strong><br>';
                    }
                    if (!empty($row->phone)) {
                        $html .= '<small data-bs-toggle="tooltip" title="Phone Number"><i class="fas fa-phone-alt text-success"></i> <a href="tel:' . $row->phone . '" class="text-decoration-none text-muted">' . $row->phone . '</a></small><br>';
                    }
                    if (!empty($row->email)) {
                        $html .= '<small data-bs-toggle="tooltip" title="Email Address"><i class="fas fa-envelope text-info"></i> <a href="mailto:' . $row->email . '" class="text-decoration-none text-muted">' . $row->email . '</a></small>';
                    }
                    return $html;
                })
                ->addColumn('product_details', function ($row) {
                    $products = $row->products->map(function ($item) {
                        return $item->product ? '<small class="text-dark" data-bs-toggle="tooltip" title="Product Name"><i class="fas fa-check-circle text-success me-1"></i>' . $item->product->prod_name . '</small>' : null;
                    })->filter()->unique()->implode('<br>');

                    return $products ?: '-';
                })
                ->addColumn('status_source', function ($row) {
                    $status = $row->leadStatus ? $row->leadStatus->title : '-';
                    $source = $row->sourceInfo ? $row->sourceInfo->title : '-';
                    return '
                    <span class="badge bg-info mb-1" data-bs-toggle="tooltip" title="Lead Status">' . $status . '</span><br>
                    <small class="text-muted" data-bs-toggle="tooltip" title="Lead Source"><i class="fas fa-share-alt text-secondary"></i> ' . $source . '</small>
                ';
                })
                ->addColumn('owner_details', function ($row) {
                    $assignedHtml = '';
                    if ($row->assignedStaff) {
                        $assignedHtml = '<small data-bs-toggle="tooltip" title="Assigned To"><i class="fas fa-user-check text-success"></i> <span class="text-success fw-bold">' . $row->assignedStaff->staff_name . '</span></small><br>';
                    }

                    $createdBy = get_createdby_name($row->created_by);
                    $date = date('d-m-Y h:i A', strtotime($row->created_at));

                    return $assignedHtml . '
                    <small class="text-muted" data-bs-toggle="tooltip" title="Created By"><i class="fas fa-user-edit text-info"></i> By: ' . $createdBy . '</small><br>
                    <small class="text-muted" data-bs-toggle="tooltip" title="Created On"><i class="fas fa-clock text-warning"></i> ' . $date . '</small>
                ';
                })

                ->addColumn('action', function ($row) {

                    $perms = checkPermissions('lead.index');
                    $canCreate = $perms['canCreate'] ?? false;
                    $canUpdate = $perms['canUpdate'] ?? false;
                    $canDelete = $perms['canDelete'] ?? false;

                    $btn = '<div class="d-flex flex-column gap-2 align-items-center" style="min-width: 130px;">';

                    if ($row->status == 0 && $canCreate) {
                        $productIds = $row->products->pluck('product_id')->implode(',');
                        $btn .= '
            <button type="button" class="btn btn-primary btn-sm w-100 shadow-sm convertDealRecord" data-id="' . $row->enquiry_id . '" data-customer="' . $row->customer_id . '" data-name="' . htmlspecialchars($row->enquiry_name) . '" data-products="' . $productIds . '" data-assigned-to="' . ($row->assigned_to ?? '') . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Convert Lead to Deal" style="border-radius: 6px; font-weight: 600; font-size: 12px; padding: 6px 10px;">
                <i class="fas fa-handshake me-1"></i> Convert to Deal
            </button>';
                    }

                    $btn .= '<div class="d-flex gap-2 w-100">';

                    if ($canUpdate) {
                        $btn .= '
            <button type="button" class="btn btn-light btn-sm flex-fill shadow-sm border text-primary editRecord" data-id="' . $row->enquiry_id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Lead" style="border-radius: 6px;">
                <i class="fas fa-edit"></i>
            </button>';
                    }

                    if ($canDelete) {
                        $btn .= '
            <button type="button"
                class="btn btn-light btn-sm flex-fill shadow-sm border text-danger deleteRecord"
                data-id="' . $row->enquiry_id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Lead" style="border-radius: 6px;">
                <i class="fas fa-trash-alt"></i>
            </button>';
                    }

                    $btn .= '
            <button type="button"
                class="btn btn-light btn-sm flex-fill shadow-sm border text-info"
                onclick="LeadTimeline.open({ leadId: ' . $row->enquiry_id . ' })" data-bs-toggle="tooltip" data-bs-placement="top" title="View Timeline" style="border-radius: 6px;">
                <i class="fas fa-clock"></i>
            </button>
        </div>
    </div>';

                    return $btn;
                })
                ->rawColumns([
                    'lead_details',
                    'company_details',
                    'contact_details',
                    'product_details',
                    'status_source',
                    'owner_details',
                    'action'
                ])
                ->make(true);
        }
    }

    public function getExportFilename(Request $request)
    {
        $filename = 'Leads';

        if ($request->filled('company')) {
            $company = Customer::find($request->company);
            if ($company) {
                $filename .= '_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $company->company_name);
            }
        } elseif ($request->filled('city')) {
            $city = CityModel::find($request->city);
            if ($city) {
                $filename .= '_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $city->city_name);
            }
        } elseif ($request->filled('lead_date')) {
            $filename .= '_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $request->lead_date);
        }

        return $filename;
    }

    public function getAppliedFilters(Request $request)
    {
        $filters = [];

        if ($request->filled('company')) {
            $company = Customer::find($request->company);
            if ($company)
                $filters['Company'] = $company->company_name;
        }

        if ($request->filled('city')) {
            $city = CityModel::find($request->city);
            if ($city)
                $filters['City'] = $city->city_name;
        }

        if ($request->filled('contact_person')) {
            $filters['Contact Person'] = $request->contact_person;
        }

        if ($request->filled('product')) {
            $product = ProductModel::find($request->product);
            if ($product)
                $filters['Product'] = $product->prod_name;
        }

        if ($request->filled('created_by')) {
            $filters['Created By'] = get_createdby_name($request->created_by);
        }

        if ($request->filled('assigned_to')) {
            $staff = Staffmodel::find($request->assigned_to);
            if ($staff)
                $filters['Assigned To'] = $staff->staff_name;
        }

        if ($request->filled('lead_date')) {
            $filters['Lead Date'] = $request->lead_date;
        }

        return $filters;
    }

    public function exportExcel(Request $request)
    {
        $filename = $this->getExportFilename($request) . '.xlsx';
        return Excel::download(new \App\Exports\LeadExport($request), $filename);
    }

    public function exportPdf(Request $request)
    {
        $filename = $this->getExportFilename($request) . '.pdf';
        $data = $this->getFilteredLeadsQuery($request)->get();
        $filters = $this->getAppliedFilters($request);
        $is_pdf = true;
        $pdf = Pdf::loadView('company.crm.lead.export_pdf', compact('data', 'filters', 'is_pdf'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

    public function delete($id)
    {
        try {
            $enquiry = EnquiryManageModel::findOrFail($id);
            $enquiry->update([
                'status' => 2,
                'updated_by' => getUpdatedBy()
            ]);

            DB::table('enquiry_history')->insert([
                'enquiry_id' => $enquiry->enquiry_id,
                'change_type' => 'Enquiry Deleted',
                'reason' => 'Enquiry marked as deleted (status 2).',
                'table_name' => 'enquiry_manage',
                'changed_by' => getCreatedBy(),
                'changed_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Lead deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete lead: ' . $e->getMessage()
            ], 500);
        }
    }
}
