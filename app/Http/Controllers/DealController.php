<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\ProductModel;
use App\Models\DealStage;
use App\Models\DealLog;
use App\Models\EnquiryManageModel;
use App\Models\Quotation;
use App\Models\QuotationLog;
use App\Services\DealLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DealController extends Controller
{
    //
      private DealLogService $logService;
    private CrmCallController $crmCall;
    private CrmNoteController $crmNote;
    private CrmAttachmentController $crmAttachment;
     public function __construct(
        DealLogService $logService,
        CrmCallController $crmCall,
        CrmNoteController $crmNote,
        CrmAttachmentController $crmAttachment
    ) {
        $this->logService = $logService;
        $this->crmCall = $crmCall;
        $this->crmNote = $crmNote;
        $this->crmAttachment = $crmAttachment;
    }
    public function index()
    {
        $page_name = 'Deal Management';

        $deals = Deal::selectRaw('MIN(deal_id) as deal_id, deal_name')
            ->whereNotNull('deal_name')
            ->groupBy('deal_name')
            ->get();

        $stages = DealStage::where('status', 0)->where('is_won', 0)->where('is_lost', 0)->orderBy('order_no', 'asc')->get();

        $staffs = DB::table('mst_staff')
            ->select('staff_id', 'staff_name', 'status', 'staff_email', 'staff_type')
            ->where('status', 0)
            ->orderBy('staff_name', 'asc')
            ->get();

        $creatorIds = Deal::where('status', 0)
            ->whereNotNull('created_by')
            ->distinct()
            ->pluck('created_by');

        $basicMachineCatId = DB::table('tbl_product_category')
            ->where('name', 'LIKE', '%Basic Machine%')
            ->value('category_id') ?? 1;

        $products = ProductModel::active()
            ->where('category_id', $basicMachineCatId)
            ->orderBy('prod_name', 'asc')
            ->get();

        $creators = $creatorIds
            ->map(function ($id) {
                $name = get_createdby_name($id);
                return $name ? (object) ['id' => $id, 'name' => $name] : null;
            })
            ->filter()
            ->unique('name')
            ->sortBy('name')
            ->values();

        return view('company.crm.deal.kanban', compact('page_name', 'deals', 'stages', 'staffs', 'creators','products'));
    }

    public function createDirect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:tbl_product,product_id',
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'deal_stage' => 'required|exists:mst_deal_stage,id',
            'amount' => 'required|numeric|min:0',
            'closing_date' => 'required',
            'phone' => 'nullable|string|max:20',
            'iwo_no' => 'nullable|string|max:255',
            'dispatch_date' => 'nullable|date_format:d-m-Y',
            'gst_no' => [
                'nullable',
                'string',
                'size:15',
                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/i'
            ],
            'assigned_to' => 'nullable|integer|exists:mst_staff,staff_id',
            'customer_id' => 'nullable|integer|exists:tbl_customer,customer_id',
            'deal_type' => 'nullable|string|in:Domestic,Export',
            'city_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
        ], [
            'gst_no.regex' => 'Please enter a valid 15-character GST number.',
            'gst_no.size' => 'GST number must be exactly 15 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $closingDate = $this->formatDMY($request->closing_date);
            $createdBy = getCreatedBy();
            $dealType = $request->input('deal_type') ?: 'Domestic';

            $cityId = $request->input('city_id');
            $stateId = $request->input('state_id');
            $countryId = $request->input('country_id');

            $customerId = $request->input('customer_id');
            if (!empty($customerId) && (empty($cityId) || empty($stateId) || empty($countryId))) {
                $cust = DB::table('tbl_customer')->where('customer_id', $customerId)->first();
                if ($cust) {
                    $cityId = $cityId ?: $cust->city_id;
                    $stateId = $stateId ?: $cust->state_id;
                    $countryId = $countryId ?: $cust->country_id;
                }
            }

            // Construct deal name from product IDs
            $productNames = '';
            if ($request->has('product_ids') && is_array($request->product_ids)) {
                $productNames = DB::table('tbl_product')
                    ->whereIn('product_id', $request->product_ids)
                    ->pluck('prod_name')
                    ->implode(', ');
            }

            // Step 1: Create the lead in enquiry_manage with status = 1 (Converted to Deal)
            $lead = EnquiryManageModel::create([
                'enquiry_no' => EnquiryManageModel::get_enquiry_no(),
                'enquiry_name' => $productNames ?: 'Direct Deal',
                'company_name' => $request->company_name,
                'contact_person_name' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->input('phone'),
                'customer_id' => $request->input('customer_id'),
                'assigned_to' => $request->input('assigned_to'),
                'city_id' => $cityId,
                'state_id' => $stateId,
                'countries_id' => $countryId,
                'lead_type' => $dealType,
                'enquiry_date' => now()->format('Y-m-d'),
                'status' => 1, // 1 = Converted to Deal directly
                'lead_status' => 0,
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ]);

            $dispatchDate = $request->filled('dispatch_date') ? Carbon::createFromFormat('d-m-Y', $request->dispatch_date)->format('Y-m-d') : null;

            // Step 2: Create the deal in tbl_deal linked to the lead
            $deal = Deal::create([
                'lead_id' => $lead->enquiry_id,
                'customer_id' => $request->input('customer_id'),
                'deal_name' => $productNames ?: 'Direct Deal',
                'iwo_no' => $request->input('iwo_no'),
                'dispatch_date' => $dispatchDate,
                'deal_type' => $dealType,
                'gst_no' => $request->input('gst_no'),
                'amount' => $request->amount,
                'closing_date' => $closingDate,
                'deal_stage' => $request->deal_stage,
                'assigned_to' => $request->input('assigned_to'),
                'description' => $request->input('description'),
                'is_direct' => 1,
                'deal_status' => 0,
                'status' => 0,
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ]);

            // Link the selected products to the lead in tbl_enquiry_items using their database price and GST
            if ($request->has('product_ids') && is_array($request->product_ids)) {
                foreach ($request->product_ids as $productId) {
                    $productInfo = DB::table('tbl_product')->where('product_id', $productId)->first();
                    $price = $productInfo ? $productInfo->prod_amount : 0;

                    // Fetch GST value
                    $gstVal = 0;
                    if ($productInfo && $productInfo->gst) {
                        $gstVal = DB::table('mst_gst')->where('gst_id', $productInfo->gst)->value('gst_no') ?? 0;
                    }

                    DB::table('tbl_enquiry_items')->insert([
                        'enquiry_id' => $lead->enquiry_id,
                        'product_id' => $productId,
                        'quantity' => 1,
                        'price' => $price,
                        'gst' => $gstVal,
                    ]);
                }
            }

            // Log the Deal timeline and Lead history
            $this->logService->log($deal, 'deal_created', 'Direct deal created from Kanban board');

            DB::table('enquiry_history')->insert([
                'enquiry_id' => $lead->enquiry_id,
                'change_type' => 'Enquiry Created',
                'reason' => 'Direct deal created from Kanban board: ' . $deal->deal_name,
                'table_name' => 'enquiry_manage',
                'changed_by' => $createdBy,
                'changed_at' => now(),
            ]);

            DB::commit();
            return $this->success('Deal created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exception($e);
        }
    }

    public function getCustomersList(Request $request)
    {
        $search = $request->input('q') ?: $request->input('search', '');
        $customers = DB::table('tbl_customer')
            ->select('customer_id as id', 'company_name as text', 'contact_person', 'phone', 'email', 'gst_no')
            ->where('status', 0)
            ->when($search, fn($q) => $q->where('company_name', 'like', "%{$search}%"))
            ->orderBy('company_name')
            ->limit(30)
            ->get();

        return response()->json(['results' => $customers]);
    }

     public function getKanbanData(Request $request)
    {
        $dealId = $request->company_id;
        $stageId = $request->stage_id;
        $createdBy = $request->created_by;
        $dateRange = $request->date_range;
        $quotationNo = $request->quotation_no;
        $sortBy = $request->sort_by;
        $isDirect = $request->is_direct;

        $role = Session::get('login_type');
        $user_id = getCreatedBy();

        $stagesQuery = DealStage::with([
            'deals' => function ($q) use ($dealId, $stageId, $createdBy, $dateRange, $quotationNo, $role, $user_id, $isDirect) {

                $q->where(function ($query) {
                    $query->where('status', 0);
                });

                if ($role == 'Staff') {
                    $q->where(function ($q2) use ($user_id) {
                        $q2->where('created_by', $user_id)
                           ->orWhere('assigned_to', $user_id);
                    });
                }

                if ($dealId) {
                    $q->where('deal_id', $dealId);
                }
                if ($createdBy) {
                    $q->where('created_by', $createdBy);
                }
                if ($dateRange) {
                    $dates = explode(' to ', $dateRange);
                    if (count($dates) == 2) {
                        try {
                            $start = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->format('Y-m-d');
                            $end = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->format('Y-m-d');
                            $q->whereBetween('closing_date', [$start, $end]);
                        } catch (\Exception $e) {
                        }
                    } elseif (count($dates) == 1 && !empty($dates[0])) {
                        try {
                            $start = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->format('Y-m-d');
                            $q->whereDate('closing_date', $start);
                        } catch (\Exception $e) {
                        }
                    }
                }

                if ($isDirect) {
                    $q->where('is_direct', 1);
                }

                $q->orderBy('closing_date', 'asc');

            }
        ])->where('status', 0);

        // If a specific stage is filtered
        if ($stageId) {
            $stagesQuery->where('id', $stageId);
        }

        $stages = $stagesQuery->orderBy("order_no", "asc")->get();

        $stages->each(function ($stage) use ($sortBy) {
            $stage->deals->each(function ($deal) {
                $deal->sort_time = $deal->closing_date ? Carbon::parse($deal->closing_date)->timestamp : 0;
                $deal->closing_date = $deal->closing_date ? Carbon::parse($deal->closing_date)->format('d-m-Y') : null;

                $lead_info = get_lead_name($deal->lead_id);
                $deal->company_name = $lead_info['company_name'] ?? '—';
                $deal->contact_person = $lead_info['contact_person_name'] ?? '—';
                if ($deal->is_direct) {
                    $deal->lead_no = '—';
                } else {
                    $deal->lead_no = $lead_info['enquiry_no'] ?? '—';
                }

                $deal->created_by_name = get_createdby_name($deal->created_by);
                $deal->assigned_to_name = get_createdby_name($deal->assigned_to);

                $deal->encrypted_id = encrypt($deal->deal_id);
                $deal->quot_count = Quotation::where('deal_id', $deal->deal_id)->count();
                $deal->latest_amount = Quotation::where('deal_id', $deal->deal_id)
                    ->latest('quotation_id')
                    ->value('grand_total') ?? $deal->amount;
                $latestQuot = Quotation::where('deal_id', $deal->deal_id)
                    ->latest('quotation_id')
                    ->first();

                $deal->quot_date = $latestQuot ? Carbon::parse($latestQuot->created_at)->format('d M Y') : null;
                $deal->latest_quotation_id = $latestQuot ? encrypt($latestQuot->quotation_id) : null;
            });

            if ($sortBy) {
                $dealsCollection = $stage->deals;
                if ($sortBy == 'amount_asc') {
                    $stage->setRelation('deals', $dealsCollection->sortBy('latest_amount')->values());
                } elseif ($sortBy == 'amount_desc') {
                    $stage->setRelation('deals', $dealsCollection->sortByDesc('latest_amount')->values());
                } elseif ($sortBy == 'date_asc') {
                    $stage->setRelation('deals', $dealsCollection->sortBy('sort_time')->values());
                } elseif ($sortBy == 'date_desc') {
                    $stage->setRelation('deals', $dealsCollection->sortByDesc('sort_time')->values());
                }
            }
        });

        return $this->success('Data fetched', $stages);
    }

    public function updateStage(Request $request, $dealId)
    {
        $request->validate([
            'stage_id' => 'required|exists:mst_deal_stage,id',
        ]);

        $deal = Deal::with('dealStage')->findOrFail($dealId);
        $oldStage = $deal->dealStage;
        $oldStageName = $oldStage->stage_name ?? 'N/A';
        $newStage = DealStage::findOrFail($request->stage_id);

        if ($deal->deal_stage == $request->stage_id) {
            return $this->error('Deal is already in the selected stage');
        }

        // Check won/complain movement constraints
        $isCurrentWon = $oldStage && $oldStage->is_won == 1;
        $isCurrentComplain = $oldStage && strtolower(trim($oldStage->stage_name)) === 'complain';

        $isNewWon = $newStage->is_won == 1;
        $isNewComplain = strtolower(trim($newStage->stage_name)) === 'complain';

        if ($isCurrentWon && !$isNewComplain) {
            return $this->error('Deals in Won stage can only be moved to Complain.');
        }

        if ($isCurrentComplain && !$isNewWon) {
            return $this->error('Deals in Complain stage can only be moved to a Won stage.');
        }

        if ($isNewComplain && !$isCurrentWon) {
            return $this->error('Only deals in Won stage can be moved to Complain.');
        }

        $deal->update([
            'deal_stage' => $request->stage_id,
            'deal_status' => $newStage->is_won ? 1 : ($newStage->is_lost ? 2 : 0),
            'updated_by' => getCreatedBy(),
        ]);

        $logMessage = "Stage changed from \"$oldStageName\" to \"$newStage->stage_name\"";
        if ($request->filled('reason')) {
            $logMessage .= ". Complaint Details: " . $request->reason;
            $deal->update([
                'complain_reason' => $request->reason
            ]);
        }

        $this->logService->log($deal, 'stage_updated', $logMessage);

        return $this->success('Deal stage updated successfully');
    }

     public function editDeal($id)
    {
        try {
            $deal = Deal::findOrFail($id);
            if ($deal->closing_date) {
                $deal->closing_date_formatted = Carbon::parse($deal->closing_date)->format('d/m/Y');
            }
            if ($deal->lead_id) {
                $leadDetails = get_lead_name($deal->lead_id);
                $deal->company_name = $leadDetails['company_name'] ?? null;
                $deal->gst_no = strtoupper($deal->gst_no);
            }
            return $this->success('Deal fetched successfully', $deal);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch deal details.');
        }
    }
    
     public function updateDeal(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'deal_name' => 'required',
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'closing_date' => 'required|date_format:d/m/Y',
            'iwo_no' => 'nullable|string|max:255',
            'dispatch_date' => 'nullable|date_format:d/m/Y',
            'gst_no' => ['nullable', 'string', 'size:15', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/i'],
            'sales_person' => 'nullable|integer|exists:mst_staff,staff_id',
        ], [
            'gst_no.regex' => 'Please enter a valid 15-character GST number',
            'gst_no.size' => 'GST number must be exactly 15 characters long.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dealNameStr = is_array($request->deal_name) ? implode(', ', $request->deal_name) : $request->deal_name;
            $deal = Deal::findOrFail($id);
            $dispatchDate = $request->filled('dispatch_date') ? Carbon::createFromFormat('d/m/Y', $request->dispatch_date)->format('Y-m-d') : null;

            $deal->update([
                'deal_name' => $dealNameStr,
                'company_name' => $request->company_name,
                'amount' => $request->amount,
                'closing_date' => Carbon::createFromFormat('d/m/Y', $request->closing_date)->format('Y-m-d'),
                'description' => $request->description,
                'iwo_no' => $request->iwo_no,
                'dispatch_date' => $dispatchDate,
                'gst_no' => $request->gst_no,

                'updated_by' => getCreatedBy(),
            ]);
            if ($deal->lead_id) {
                $lead = EnquiryManageModel::find($deal->lead_id);
                if ($lead) {
                    $lead->company_name = $request->company_name;
                    $lead->enquiry_name = $dealNameStr;
                    $lead->save();

                    // Sync the products in tbl_enquiry_items
                    $productNames = is_array($request->deal_name) ? $request->deal_name : array_map('trim', explode(',', $request->deal_name));
                    $productInfos = DB::table('tbl_product')->whereIn('prod_name', $productNames)->get();
                    if ($productInfos->isNotEmpty()) {
                        DB::table('tbl_enquiry_items')->where('enquiry_id', $lead->enquiry_id)->delete();
                        foreach ($productInfos as $productInfo) {
                            DB::table('tbl_enquiry_items')->insert([
                                'enquiry_id' => $lead->enquiry_id,
                                'product_id' => $productInfo->product_id,
                                'quantity' => 1,
                                'price' => $request->amount,
                            ]);
                        }
                    }
                }
            }

            $this->logService->log($deal, 'deal_updated', "Deal details updated manually");

            return $this->success('Deal updated successfully.');
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }

    public function closedWon(Request $request)
    {
        $request->validate($this->closedWonRules());

        DB::beginTransaction();
        try {
            $deal = Deal::with('dealStage')->findOrFail($request->deal_id);
            $oldStage = $deal->dealStage->stage_name ?? 'N/A';

            $newStage = DealStage::findOrFail($request->stage_id);

            $this->updateDealForClose($deal, $request, 1);

            $this->logService->log($deal, 'stage_updated', "Stage changed from \"$oldStage\" to \"$newStage->stage_name\" for Closed Won");

            $lead = EnquiryManageModel::find($request->lead_id);

            if ($lead?->customer_id) {
                $customer = Customer::find($lead->customer_id);
                if ($customer) {
                    $customer->update([
                        'company_name' => $request->company_name,
                        'contact_person' => $request->contact_person_name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'billing_attention' => $request->contact_person_name,
                        'country_id' => $request->country,
                        'state_id' => $request->state,
                        'city_id' => $request->city,
                        'address' => $request->address,
                        'gst_no' => $request->gst_no,
                        'updated_by' => getCreatedBy(),
                    ]);
                }
                $deal->customer_id = $lead->customer_id;
                $deal->save();

                DB::commit();

                return $this->success('Deal moved to Closed Won and customer details updated successfully');
            }
            $request->merge([
                'sales_person' => $deal->sales_person ?: null,
            ]);
            $customer = $this->createCustomerFromLead($request);
            $lead->customer_id = $customer->customer_id;
            $lead->save();

            $deal->customer_id = $customer->customer_id;
            $deal->save();

            $quotation = Quotation::where('deal_id', $deal->deal_id)->get();
            $quotation->each(function ($quot) use ($customer) {
                $quot->customer_id = $customer->customer_id;
                $quot->save();
            });
            $this->logService->log($deal, 'customer_created', 'Customer created from lead and associated with deal');

            DB::commit();

            return $this->success('Deal moved to Closed Won and converted to customer successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->exception($e);
        }
    }
    private function closedWonRules()
    {
        return [
            'deal_id' => 'required',
            'lead_id' => 'required',
            'stage_id' => 'required|exists:mst_deal_stage,id',
            'company_name' => 'required|string',
            'contact_person_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'address' => 'required',
            'amount' => 'required|numeric|min:0',
            'iwo_no' => 'required|string|max:255',
            'dispatch_date' => 'required|date_format:d-m-Y'
        ];
    }

    private function createCustomerFromLead($request)
    {
        return Customer::create([
            'company_name' => $request->company_name,
            'contact_person' => $request->contact_person_name,
            'sales_person' => $request->sales_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'billing_attention' => $request->contact_person_name,
            'country_id' => $request->country,
            'state_id' => $request->state,
            'city_id' => $request->city,
            'address' => $request->address,
            'gst_no' => $request->gst_no,
            'created_by' => getCreatedBy(),
            'updated_by' => getCreatedBy(),
        ]);
    }

     public function closedLost(Request $request)
    {
        $request->validate([
            'deal_id' => 'required',
            'lead_id' => 'required',
            'stage_id' => 'required|exists:mst_deal_stage,id',
        ]);

        DB::beginTransaction();
        try {
            $deal = Deal::with('dealStage')->findOrFail($request->deal_id);
            $oldStage = $deal->dealStage;
            $oldStageName = $oldStage->stage_name ?? 'N/A';

            // Prevent transition to Lost if current is Won or Complain
            if ($oldStage && $oldStage->is_won == 1) {
                return $this->error('Deals in Won stage can only be moved to Complain.');
            }
            if ($oldStage && strtolower(trim($oldStage->stage_name)) === 'complain') {
                return $this->error('Deals in Complain stage can only be moved to a Won stage.');
            }

            $closingDate = $this->formatDMY($request->closing_date);
            $newStage = DealStage::findOrFail($request->stage_id);

            $this->updateDealForClose($deal, $request, 2);

            $this->logService->log($deal, 'stage_updated', "Stage changed from \"$oldStageName\" to \"$newStage->stage_name\" and deal marked as Closed Lost");

            DB::commit();

            return $this->success('Deal moved to Closed Lost successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->exception($e);
        }
    }

     private function updateDealForClose($deal, $request, $status = 2)
    {
        $data = [
            'deal_stage' => $request->stage_id,
            'amount' => $request->amount,
            'deal_status' => $status,
            'updated_by' => getCreatedBy(),
        ];
        if ($status == 1) {
            $data['iwo_no'] = $request->iwo_no;
            if ($request->filled('dispatch_date')) {
                $data['dispatch_date'] = Carbon::createFromFormat('d-m-Y', $request->dispatch_date)->format('Y-m-d');
            }
        }
        $deal->update($data);
    }
     // Deal details logic has been moved to DealDetailsController.

    /**
     * Wrapper: delegates to CrmCallController with reference_type = 'deal'.
     */
    public function scheduleCall(Request $request)
    {
        $request->merge([
            'reference_type' => 'deal',
            'reference_id' => $request->deal_id,
        ]);
        return $this->crmCall->scheduleCall($request);
    }

    /**
     * Wrapper: delegates to CrmCallController for updating a call.
     */
    public function updateCall(Request $request)
    {
        return $this->crmCall->updateCall($request);
    }
     /**
     * Wrapper: delegates to CrmNoteController with reference_type = 'deal'.
     */
    public function addNote(Request $request)
    {
        $request->merge([
            'reference_type' => 'deal',
            'reference_id' => $request->deal_id,
        ]);
        return $this->crmNote->addNote($request);
    }

     /**
     * Wrapper: delegates to CrmNoteController.
     */
    public function updateNote(Request $request)
    {
        return $this->crmNote->updateNote($request);
    }

     /**
     * Wrapper: delegates to CrmAttachmentController with reference_type = 'deal'.
     */
    public function uploadAttachment(Request $request)
    {
        $request->merge([
            'reference_type' => 'deal',
            'reference_id' => $request->deal_id,
        ]);
        return $this->crmAttachment->uploadAttachment($request);
    }
    /**
     * Wrapper: delegates to CrmCallController with reference_type = 'deal'.
     */
    public function getCalls($deal_id)
    {
        return $this->crmCall->getCalls('deal', (int) $deal_id);
    }

    /**
     * Wrapper: delegates to CrmCallController.
     */
    public function getDashboardCalls()
    {
        return $this->crmCall->getDashboardCalls();
    }

    /**
     * Wrapper: delegates to CrmNoteController with reference_type = 'deal'.
     */
    public function getNotes($deal_id)
    {
        return $this->crmNote->getNotes('deal', (int) $deal_id);
    }

    /**
     * Wrapper: delegates to CrmAttachmentController with reference_type = 'deal'.
     */
    public function getAttachments($deal_id)
    {
        return $this->crmAttachment->getAttachments('deal', (int) $deal_id);
    }

    /**
     * Wrapper: delegates to CrmCallController.
     */
    public function updateCallStatus(Request $request)
    {
        return $this->crmCall->updateCallStatus($request);
    }

    /**
     * Wrapper: delegates to CrmCallController.
     */
    public function deleteCall(Request $request)
    {
        return $this->crmCall->deleteCall($request);
    }

    /**
     * Wrapper: delegates to CrmNoteController.
     */
    public function deleteNote(Request $request)
    {
        return $this->crmNote->deleteNote($request);
    }

    /**
     * Wrapper: delegates to CrmAttachmentController.
     */
    public function deleteAttachment(Request $request)
    {
        return $this->crmAttachment->deleteAttachment($request);
    }

    public function getLogs($deal_id)
    {
        try {
            $deal = Deal::find($deal_id);
            $dealLogs = DealLog::where('deal_id', $deal_id)->get();
            $quotationIds = Quotation::where('deal_id', $deal_id)->pluck('quotation_id');
            $quotationLogs = QuotationLog::whereIn('quotation_id', $quotationIds)->get();

            // Fetch parent Lead history logs if lead_id exists
            $leadLogs = [];
            if ($deal && $deal->lead_id) {
                $leadLogs = DB::table('enquiry_history')
                    ->where('enquiry_id', $deal->lead_id)
                    ->get();
            }

            // Map Lead logs to resemble Deal logs
            $mappedLeadLogs = collect($leadLogs)->map(function ($log) {
                return (object)[
                    'log_id' => $log->history_id,
                    'action' => $log->change_type,
                    'description' => $log->reason,
                    'performed_by' => $log->changed_by,
                    'performed_at' => $log->changed_at,
                    'is_lead_log' => true
                ];
            });

            // Merge and sort them
            $allLogs = $dealLogs->concat($quotationLogs)->concat($mappedLeadLogs)
                ->sortByDesc(function ($log) {
                    return Carbon::parse($log->performed_at ?? $log->changed_at)->timestamp;
                })
                ->values();

            $logs = $allLogs->map(function ($log) use ($deal) {
                $log->performed_by_name = get_createdby_name($log->performed_by ?? $log->changed_by) ?: 'System';
                $log->formatted_date = Carbon::parse($log->performed_at ?? $log->changed_at)->format('d M Y h:i:s A');

                if (isset($log->quotation_id)) {
                    $log->is_quotation_log = true;
                    $log->enc_quotation_id = encrypt($log->quotation_id);

                    $payload = $log->payload;
                    if (is_string($payload)) {
                        $payload = json_decode($payload, true);
                    }
                    if (is_string($payload)) {
                        $payload = json_decode($payload, true);
                    }
                    $log->parsed_payload = $payload;
                }

                $leadId = $deal ? $deal->lead_id : null;
                $dealId = $deal ? $deal->deal_id : null;
                $log->file_url = $this->resolveAttachmentUrl($log->description, $leadId, $dealId);

                return $log;
            });

            return response()->json([
                'success' => true,
                'data' => $logs
            ]);
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }

    private function resolveAttachmentUrl($description, $leadId, $dealId)
    {
        // 1. Check if description has a web link (e.g., https://youtube.com...)
        if (preg_match('/(https?:\/\/[^\s\']+)/', $description, $urlMatches)) {
            return $urlMatches[1];
        }

        // 2. Check if description matches uploaded file formats
        $fileName = null;
        if (preg_match("/Uploaded file: '(.*)'/", $description, $matches)) {
            $fileName = $matches[1];
        } elseif (preg_match("/Uploaded attachment: (.*)/", $description, $matches)) {
            $fileName = $matches[1];
        }

        if ($fileName) {
            // Query the tbl_crm_attachments table for this file
            $query = \Illuminate\Support\Facades\DB::table('tbl_crm_attachments')
                ->where('file_name', $fileName)
                ->where('status', 0);

            if ($dealId) {
                $query->where(function($q) use ($dealId, $leadId) {
                    $q->where(function($sq) use ($dealId) {
                        $sq->where('reference_type', 'deal')->where('reference_id', $dealId);
                    });
                    if ($leadId) {
                        $q->orWhere(function($sq) use ($leadId) {
                            $sq->where('reference_type', 'lead')->where('reference_id', $leadId);
                        });
                    }
                });
            } elseif ($leadId) {
                $query->where('reference_type', 'lead')->where('reference_id', $leadId);
            }

            $attachment = $query->orderBy('id', 'desc')->first();
          if ($attachment) {

                $filePath = trim($attachment->file_path, '/');

                // Already contains assets/uploads
                if (preg_match('#^assets/uploads/#', $filePath)) {
                    return url($filePath);
                }

                // Already starts with deals/ or leads/ etc.
                if (preg_match('#^(deals|lead|leads|quotation|company|documents)/#', $filePath)) {
                    return config('app.img_path') . '/' . $filePath;
                }

                // Only filename stored
                return config('app.img_path') . '/' . $filePath;
            }
        }

        return null;
    }

    private function formatDate($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }

    private function formatDMY($date)
    {
        if (!$date)
            return null;

        // Try multiple formats: with dashes or slashes, zero-padded or not
        $formats = ['d-m-Y', 'd/m/Y', 'j-n-Y', 'j/n/Y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $date)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        // Fallback: let Carbon try to parse it naturally
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function success(string $msg, $data = [])
    {
        return response()->json(['success' => true, 'message' => $msg, 'data' => $data]);
    }

    private function error(string $msg)
    {
        return response()->json(['success' => false, 'message' => $msg]);
    }

    private function exception($e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Internal server error',
            'error' => $e->getMessage(),
        ], 500);
    }

    public function getDetailsForClosedWon($lead_id)
    {
        $lead = EnquiryManageModel::find($lead_id);

        if (!$lead) {
            return $this->error('Lead not found', 404);
        }

        $data = $lead->toArray();
        $deal = Deal::where('lead_id', $lead_id)->first();
        $quotation = $deal ? Quotation::where('deal_id', $deal->deal_id)->latest('quotation_id')->first() : null;

        $data['gst_no'] = $deal->gst_no ?? null;
        $data['amount'] = $deal->amount ?? null;
        $data['address'] = '';

        $customerId = $lead->customer_id ?: ($deal ? $deal->customer_id : null);
        if ($customerId) {
            $customer = Customer::find($customerId);
            if ($customer) {
                if (!empty($customer->company_name)) $data['company_name'] = $customer->company_name;
                if (!empty($customer->contact_person)) $data['contact_person'] = $customer->contact_person;
                if (!empty($customer->email)) $data['email'] = $customer->email;
                if (!empty($customer->gst_no)) $data['gst_no'] = $customer->gst_no;
                if (!empty($customer->phone)) $data['phone'] = $customer->phone;
                $data['address'] = !empty($customer->address) ? $customer->address : (!empty($customer->billing_address) ? $customer->billing_address : '');
                if (!empty($customer->city_id)) $data['city_id'] = $customer->city_id;
            }
        }

        if ($quotation) {
            if (!empty($quotation->customer_name)) $data['company_name'] = $quotation->customer_name;
            if (!empty($quotation->contact_person_name)) $data['contact_person'] = $quotation->contact_person_name;
            if (!empty($quotation->grand_total)) $data['amount'] = $quotation->grand_total;
            if (!empty($quotation->email)) $data['email'] = $quotation->email;
            if (!empty($quotation->phone)) $data['phone'] = $quotation->phone;
            if (!empty($quotation->city_id)) $data['city_id'] = $quotation->city_id;
            if (!empty($quotation->gst_no)) $data['gst_no'] = $quotation->gst_no;
            if ($deal && $deal->closing_date) {
                $data['closing_date'] = Carbon::parse($deal->closing_date)->format('d/m/Y');
            }
        }

        return $this->success('Data loaded', $data);
    }
}
