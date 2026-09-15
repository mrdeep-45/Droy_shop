<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use Yajra\DataTables\Facades\DataTables;
class CustomerController extends Controller
{
    //
    public function index()
    {
        $page_name = "Company List";

        return view('company.master.customer', compact('page_name'));
    }

    public function searchCity(Request $request)
    {
        $search = $request->search;
        $page   = $request->page ?? 1;
        $limit  = 20;

        $query = DB::table('mst_city')
            ->where('status', 0);

        if ($search) {
            $query->where('city_name', 'like', "%{$search}%");
        }

        applyRoleBasedCityFilter($query);

        $total = $query->count();

        $cities = $query
            ->orderBy('city_name')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();

        $results = [];

        foreach ($cities as $city) {
            $results[] = [
                'id' => $city->city_id,
                'text' => $city->city_name,
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $limit) < $total
            ]
        ]);
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
     public function ajaxStore(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
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
            $customer->created_by = getCreatedBy();
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name'   => 'required',
            'contact_person' => 'required',
            'email'          => 'required',
            'phone'          => ['required', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
            'gst_no' => [
                'nullable',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'
            ],
            'pan_no' => [
                'nullable',
                'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            ],
        ]);

        if ($request->filled('customer_id')) {

            // Update
            $customer = Customer::findOrFail($request->customer_id);

            $customer->update([
                'company_name'   => $request->company_name,
                'contact_person' => $request->contact_person,
                'email'          => $request->email,
                'phone'          => $request->phone,
                'gst_no'         => $request->gst_no,
                'pan_no'         => $request->pan_no,
                'country_id'     => $request->country_id,
                'state_id'       => $request->state_id,
                'city_id'        => $request->city_id,
                'address'        => $request->address,
                'updated_by'     => getUpdatedBy(),
            ]);

            $message = 'Customer updated successfully.';
        } else {

            // Create
            $customer = Customer::create([
                'company_name'   => $request->company_name,
                'contact_person' => $request->contact_person,
                'email'          => $request->email,
                'phone'          => $request->phone,
                'gst_no'         => $request->gst_no,
                'pan_no'         => $request->pan_no,
                'status'         => 0,
                'country_id'     => $request->country_id,
                'state_id'       => $request->state_id,
                'city_id'        => $request->city_id,
                'address'        => $request->address,
                'created_by'     => getCreatedBy(),
                'updated_by'     => getUpdatedBy(),
            ]);

            $message = 'Customer created successfully.';
        }

        return response()->json([
            'success'  => true,
            'message'  => $message,
            'customer' => $customer,
        ]);
    }
    public function list()
    {
        $customers = Customer::from('tbl_customer as c')
            ->with(['leads' => function($q) {
                $q->with('products.product');
            }, 'deals' => function($q) {
                $q->with('lead.products.product');
            }])
            ->leftJoin('mst_city as city', 'city.city_id', '=', 'c.city_id')
            ->leftJoin('mst_state as state', 'state.state_id', '=', 'c.state_id')
            ->leftJoin('mst_country as country', 'country.c_id', '=', 'c.country_id')
            ->select(
                'c.customer_id',
                'c.company_name',
                'c.contact_person',
                'c.email',
                'c.phone',
                'c.gst_no',
                'c.pan_no',
                'city.city_name',
                'state.state_name',
                'country.country_name',
                'c.created_by',
                'c.created_at'
            )->where('c.status', 0);

        return DataTables::of($customers)

            ->addIndexColumn()

            ->editColumn('company_name', function ($row) {
                return '
                <div>
                    <strong>' . e($row->company_name) . '</strong>
                </div>';
            })

            ->addColumn('contact_detail', function ($row) {

                return '
<div>
    <div>
        <i class="fa-solid fa-envelope fs-13 me-1"></i> ' . e($row->email) . '
    </div>
    <div>
        <i class="fa-solid fa-phone fs-13 me-1"></i> ' . e($row->phone) . '
    </div>
</div>';
            })

            ->addColumn('location', function ($row) {

                return ($row->city_name ?: '--') .
                    ' - ' .
                    ($row->state_name ?: '--') .
                    ' - ' .
                    ($row->country_name ?: '--');
            })

            ->addColumn('products', function ($row) {
                $html = '';
                
                if ($row->leads) {
                    foreach ($row->leads as $lead) {
                        if ($lead->products) {
                            foreach ($lead->products as $item) {
                                if ($item->product) {
                                    $html .= '<div class="mb-1"><span class="badge bg-info">Lead #' . e($lead->enquiry_no) . '</span> <i class="fa fa-arrow-right fs-12 mx-1 text-muted"></i> ' . e($item->product->prod_name) . '</div>';
                                }
                            }
                        }
                    }
                }
                
                if ($row->deals) {
                    foreach ($row->deals as $deal) {
                        if ($deal->lead && $deal->lead->products) {
                            foreach ($deal->lead->products as $item) {
                                if ($item->product) {
                                    $html .= '<div class="mb-1"><span class="badge bg-success">Deal #D-' . str_pad($deal->deal_id, 3, '0', STR_PAD_LEFT) . '</span> <i class="fa fa-arrow-right fs-12 mx-1 text-muted"></i> ' . e($item->product->prod_name) . '</div>';
                                }
                            }
                        }
                    }
                }
                
                return $html ?: '--';
            })

            ->addColumn('created', function ($row) {
                 $createdBy = get_createdby_name($row->created_by);
                return '
                <div>
                    <strong>' . $createdBy  . '</strong><br>
                    <small>' . date('d-m-Y', strtotime($row->created_at)) . '</small>
                </div>';
            })

            ->addColumn('action', function ($row) {

                $html = '<div class="d-flex align-items-center gap-2 flex-nowrap">';
                if (permissions_check('canUpdate')) {
                    $html .= '<button type="button" class="btn btn-dark btn-sm edit-btn" data-id="' . $row->customer_id . '"><i class="fa-solid fa-pen-to-square fs-13 me-1"></i>Edit</button>';
                }
                if (permissions_check('canDelete')) {
                    $html .= '<button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->customer_id . '"><i class="fa-solid fa-trash fs-13 me-1"></i>Delete</button>';
                }
                $html .= '</div>';
                return $html;
            })
            ->rawColumns([
                'company_name',
                'contact_detail',
                'created',
                'products',
                'action'
            ])
            ->filter(function ($query) {

                if (request()->has('search') && request()->search['value'] != '') {

                    $search = request()->search['value'];

                    $query->where(function ($q) use ($search) {

                        $q->where('c.company_name', 'like', "%{$search}%")
                            ->orWhere('c.contact_person', 'like', "%{$search}%")
                            ->orWhere('c.email', 'like', "%{$search}%")
                            ->orWhere('c.phone', 'like', "%{$search}%")
                            ->orWhere('city.city_name', 'like', "%{$search}%")
                            ->orWhere('state.state_name', 'like', "%{$search}%")
                            ->orWhere('country.country_name', 'like', "%{$search}%");
                    });
                }
            }, true)
            ->make(true);
    }

    public function edit($id)
    {
        $customer = DB::table('tbl_customer as c')
            ->leftJoin('mst_city as city', 'city.city_id', '=', 'c.city_id')
            ->leftJoin('mst_state as state', 'state.state_id', '=', 'c.state_id')
            ->leftJoin('mst_country as country', 'country.c_id', '=', 'c.country_id')
            ->where('c.customer_id', $id)
            ->select(
                'c.customer_id',
                'c.company_name',
                'c.contact_person',
                'c.email',
                'c.phone',
                'c.gst_no',
                'c.pan_no',
                'c.address',
                'c.city_id',
                'c.state_id',
                'c.country_id',
                'city.city_name',
                'state.state_name',
                'country.country_name'
            )
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        return response()->json($customer);
    }

    public function delete(Request $request)
    {
        DB::table('tbl_customer')
            ->where('customer_id', $request->customer_id)
            ->update([
                'status' => 1,
                'updated_by' => getUpdatedBy(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.'
        ]);
    }
}
