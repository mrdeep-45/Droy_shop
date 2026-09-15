<?php
namespace App\Http\Controllers;
use App\Models\Deal;
use App\Models\GstModel;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\QuotationItem;
use Illuminate\Support\Facades\DB;
use App\Services\QuotationLogService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class QuotationController extends Controller
{
    //
     public function __construct(protected QuotationLogService $logService)
    {
    }
     public function index()
    {
        $page_name = 'Quotation Management';

        $quotation_nos = Quotation::select('quotation_no')->distinct()->where('is_revised', Quotation::ORIGINAL)->orderBy('quotation_no', 'asc')->get();
        $customers = Customer::select('customer_id', 'company_name')->where('status', 0)->orderBy('company_name', 'asc')->get();
        
        $staffs = [];
        if (Session::get('login_type') == 'Company') {
            $staffs = DB::table('mst_staff')->select('staff_id', 'staff_name')->where('status', 0)->orderBy('staff_name', 'asc')->get();
        }

        return view('company.crm.quotation.index', compact('page_name', 'quotation_nos', 'customers', 'staffs'));
    }
    public function create($id)
    {
        $deal_id = decrypt($id);

        $deal = Deal::with([
            'lead.products.product',
            'customer',
            'lead.city',
            'lead.state',
            'lead.country',
        ])->find($deal_id);

        if (!$deal) {
            return redirect()->back()->with('error', 'Deal not found.');
        }

        $lead = $deal->lead;
        $gsts = GstModel::where('status', 0)->orderBy('gst_no', 'asc')->get();

        $staffs = DB::table('mst_staff')
            ->select('staff_id', 'staff_name', 'status', 'staff_email', 'staff_type')
            ->where('status', 0)
            ->orderBy('staff_name', 'asc')
            ->get();

        $existingQuotation = null;
        $isRevise = false;
        $previousUrl = url()->previous();

        $existingQuotation = Quotation::where('deal_id', $deal->deal_id)
            ->latest('quotation_id')
            ->first();

        if ($existingQuotation) {
            $isRevise = true;
        }

        $page_title = $isRevise ? 'Revise Quotation' : 'Create Quotation';
        $page_name = $page_title;
        $categories = DB::table('tbl_product_category')
            ->where('status', 0)
            ->orderBy('name', 'asc')
            ->get();
        $products = DB::table('tbl_product')
            ->leftJoin('mst_gst', 'tbl_product.gst', '=', 'mst_gst.gst_id')
            ->leftJoin('tbl_unit', 'tbl_product.unit_id', '=', 'tbl_unit.unit_id')
            ->leftJoin('tbl_product_category', 'tbl_product.category_id', '=', 'tbl_product_category.category_id')
            ->select('tbl_product.*', 'mst_gst.gst_no', 'tbl_unit.unit as unit_name', 'tbl_product_category.name as category_name')
            ->where('tbl_product.status', 0)
            ->orderBy('tbl_product.prod_name', 'asc')
            ->get();

        $terms_templates = \App\Models\TermsMaster::where('status', 0)->orderBy('module', 'asc')->get();

        return view('company.crm.quotation.create', compact(
            'page_title',
            'page_name',
            'deal',
            'lead',
            'previousUrl',
            'isRevise',
            'existingQuotation',
            'gsts',
            'staffs',
            'categories',
            'products',
            'terms_templates'
        ));
    }
     public function direct($id = null)
    {
        $existingQuotation = null;
        $isRevise = false;
        if ($id) {
            $existingQuotation = Quotation::find(decrypt($id));
            if (!$existingQuotation) {
                return redirect()->back()->with('error', 'Quotation not found.');
            }
            $isRevise = true;
        }
        $page_title = $isRevise ? 'Revise Direct Quotation' : 'Create Direct Quotation';
        $page_name = $page_title;
        $previousUrl = url()->previous();
        $gsts = GstModel::where('status', 0)->orderBy('gst_no', 'asc')->get();
        $staffs = DB::table('mst_staff')
            ->select('staff_id', 'staff_name', 'status', 'staff_email', 'staff_type')
            ->where('status', 0)
            ->orderBy('staff_name', 'asc')
            ->get();
        $categories = DB::table('tbl_product_category')
            ->where('status', 0)
            ->orderBy('name', 'asc')
            ->get();
        $products = DB::table('tbl_product')
            ->leftJoin('mst_gst', 'tbl_product.gst', '=', 'mst_gst.gst_id')
            ->leftJoin('tbl_unit', 'tbl_product.unit_id', '=', 'tbl_unit.unit_id')
            ->leftJoin('tbl_product_category', 'tbl_product.category_id', '=', 'tbl_product_category.category_id')
            ->select('tbl_product.*', 'mst_gst.gst_no', 'tbl_unit.unit as unit_name', 'tbl_product_category.name as category_name')
            ->where('tbl_product.status', 0)
            ->orderBy('tbl_product.prod_name', 'asc')
            ->get();

        $terms_templates = \App\Models\TermsMaster::where('status', 0)->orderBy('module', 'asc')->get();

        return view('company.crm.quotation.direct', compact(
            'page_title',
            'page_name',
            'previousUrl',
            'isRevise',
            'existingQuotation',
            'gsts',
            'staffs',
            'categories',
            'products',
            'terms_templates'
        ));
    }
     public function getRevisionData($id)
    {
        $quotation = Quotation::with(['items.product.category', 'customer'])->find($id);
        if (!$quotation) {
            return response()->json([
                'status' => false,
                'message' => 'Quotation not found'
            ], 404);
        }
        $cityName = DB::table('mst_city')->where('city_id', $quotation->city_id)->value('city_name') ?? '';
        $items = $quotation->items->map(function ($item) {
            $categoryName = ($item->product && $item->product->category) ? $item->product->category->name : '';
            $prodName = $item->product->prod_name ?? '-';
            if ($categoryName) {
                $prodName .= ' (' . $categoryName . ')';
            }
            return [
                'item_id' => $item->product_id,
                'product_name' => $prodName,
                'description' => $item->description,
                'qty' => (int) $item->qty,
                'unit_price' => (float) $item->unit_price,
                'gst_name' => $item->gst_percent > 0 ? $item->gst_percent . '%' : '',
                'gst_percent' => (float) $item->gst_percent,
                'gst_amount' => (float) $item->gst_amount,
                'discount_percentage' => (float) $item->discount_percentage,
                'discount_price' => (float) $item->discount_price,
                'total_price' => (float) $item->total,
            ];
        });
        return response()->json([
            'status' => true,
            'data' => [
                'quotation_person' => $quotation->contact_person_name ?? $quotation->customer_name,
                'customer_name' => $quotation->customer_name,
                'customer_id' => $quotation->customer_id,
                'customer_company_name' => $quotation->customer->company_name ?? '',
                'gst_no' => $quotation->gst_no,
                'city_id' => $quotation->city_id,
                'city_name' => $cityName,
                'subject' => $quotation->subject,
                'email' => $quotation->email,
                'phone' => $quotation->phone,
                'message' => $quotation->message,
                'assigned_to' => $quotation->assigned_to,
                'terms_conditions' => $quotation->terms,
                'gst_exemption' => $quotation->gst_exemption,
                'quotation_type' => ucfirst($quotation->quotation_type),
                'currency' => $quotation->currency,
                'exchange_rate' => (float) $quotation->exchange_rate,
                'remarks' => '',
                'items' => $items,
                'attachments' => !empty($quotation->attachments) ? array_map(function($f) {
                    $file = trim($f);
                    return [
                        'file_name' => $file,
                        'file_url' => asset('assets/uploads/quotations/attachments/' . $file)
                    ];
                }, array_filter(explode(',', $quotation->attachments))) : []
            ]
        ]);
    }
     public function store(Request $request)
    {
        $request->validate([
            'phone' => ['nullable', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
            'customer_name' => 'required|string|max:255',
            'contact_person_name' => 'required|string|max:255',
            'gst_no' => 'nullable|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'terms' => 'required|string',
            'city_id' => 'required|integer',
            'state_id' => 'required|integer',
            'country_id' => 'required|integer',
            'items' => 'required|string',
            'subtotal' => 'required|numeric',
            'gst_total' => 'required|numeric',
            'discount_total' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'gst_exemption' => 'required|integer',
            'assigned_to' => 'nullable|integer',
            'remarks' => 'nullable|string',
            'quotation_type' => 'nullable|string',
            'currency' => 'nullable|string',
            'exchange_rate' => 'nullable|numeric',
            'customer_id' => 'nullable|integer',
            'send_email' => 'nullable',
            'email' => [
                $request->boolean('send_email') ? 'required' : 'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $emails = array_map('trim', explode(',', $value));
                        foreach ($emails as $email) {
                            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $fail("The email '$email' is not a valid email address.");
                            }
                        }
                    }
                },
                'max:500'
            ],
        ], [
            'email.required' => 'Quotation Email is required when Send Email to Customer is enabled.',
        ]);
        $leadId = $request->input('lead_id') ?: null;
        $dealId = $request->input('deal_id') ?: null;
        $customerId = $request->input('customer_id') ?: null;
        $quotationNo = null;
        $revisedCount = 0;
        $parent_quotation_id = null;
        $isRevised = Quotation::ORIGINAL;
        if ($leadId || $dealId) {
            $query = Quotation::query();
            if ($leadId) {
                $query->where('lead_id', $leadId);
                $customerId = \App\Models\EnquiryManageModel::find($leadId)->customer_id ?? null;
            }
            if ($dealId) {
                $deal = Deal::find($dealId);
                if ($deal) {
                    $query->where('deal_id', $dealId);
                    $customerId = $deal->customer_id;
                    if ($request->filled('assigned_to')) {
                        $deal->update([
                            'assigned_to' => $request->input('assigned_to'),
                        ]);
                    }
                }
            }
            $existing = $query->orderBy('quotation_id', 'desc')->first();
            if ($existing) {
                $quotationNo = $existing->quotation_no;
                $parent_quotation_id = $existing->quotation_id;
                $revisedCount = ($existing->revised_count ?? 0) + 1;
                $isRevised = Quotation::REVISED;
            }
        } else {
            $parentQuotationId = $request->input('parent_quotation_id') ?: null;
            if ($parentQuotationId) {
                $parentQuotation = Quotation::find($parentQuotationId);
                if ($parentQuotation) {
                    $quotationNo = $parentQuotation->quotation_no;
                    $parent_quotation_id = $parentQuotation->quotation_id;
                    $revisedCount = ($parentQuotation->revised_count ?? 0) + 1;
                    $isRevised = Quotation::REVISED;
                }
            }
        }
        if (!$quotationNo) {
            $quotationNo = Quotation::getQuotationNo();
        }

        $attachmentPaths = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->extension();

                $file->move(
                    public_path('assets/uploads/quotations/attachments'),
                    $fileName
                );

                $attachmentPaths[] = $fileName;
            }
        }

        $itemsRaw = json_decode($request->input('items'), true);
        if (!$itemsRaw || !is_array($itemsRaw)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid items data.',
            ], 422);
        }

        foreach ($itemsRaw as $itemData) {
            validator($itemData, [
                'description' => 'nullable|string',
            ])->validate();
        }

        DB::beginTransaction();
        try {
            $quotation = Quotation::create([
                'quotation_no' => $quotationNo,
                'quotation_type' => strtolower($request->input('quotation_type', 'domestic')),
                'deal_id' => $dealId,
                'lead_id' => $leadId,
                'customer_id' => $customerId ?: null,
                'assigned_to' => $request->input('assigned_to') ?: null,
                'customer_name' => $request->input('customer_name'),
                'contact_person_name' => $request->input('contact_person_name'),
                'gst_no' => $request->input('gst_no'),
                'gst_exemption' => (int) $request->input('gst_exemption', Quotation::GST_NOT_EXEMPTED),
                'email' => $request->input('email'),
                'country_id' => $request->input('country_id'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                'quotation_date' => now()->toDateString(),
                'is_revised' => $isRevised,
                'revised_count' => $revisedCount,
                'terms' => $request->input('terms'),
                'subject' => $request->input('subject'),
                'message' => $request->input('message'),
                'phone' => $request->input('phone'),
                'parent_quotation_id' => $parent_quotation_id,
                'status' => Quotation::ACTIVE,
                'currency' => $request->input('currency', 'INR'),
                'exchange_rate' => $request->input('exchange_rate', 1.0),
                'grand_total' => $request->input('grand_total'),
                'discount_total' => $request->input('discount_total', 0.00),
                'gst_total' => $request->input('gst_total', 0.00),
                'grand_total_converted' => (float) $request->input('grand_total') * (float) $request->input('exchange_rate', 1.0),
                'is_direct' => $leadId || $dealId ? 1 : 0,
                'created_by' => getCreatedBy(),
                'updated_by' => getCreatedBy(),
                'attachments' => (function() use ($request, $attachmentPaths) {
                    $existingAtts = [];
                    if ($request->has('existing_attachments')) {
                        $input = $request->input('existing_attachments');
                        if (is_array($input)) {
                            $existingAtts = $input;
                        } else {
                            $existingAtts = array_filter(explode(',', $input));
                        }
                    }
                    $all = array_unique(array_merge($existingAtts, $attachmentPaths));
                    return !empty($all) ? implode(',', $all) : null;
                })()
            ]);

            $createdItems = [];
            $itemExcRate = (float) $request->input('exchange_rate', 1.0);
            foreach ($itemsRaw as $item) {
                $row = QuotationItem::create([
                    'quotation_id' => $quotation->quotation_id,
                    'product_id' => $item['item_id'],
                    'description' => $item['description'] ?? null,
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'] ?? 0,
                    'gst_percent' => $item['gst_percent'] ?? 0,
                    'gst_amount' => $item['gst_amount'] ?? 0,
                    'discount_percentage' => $item['discount_percentage'] ?? 0,
                    'discount_price' => $item['discount_price'] ?? 0,
                    'total' => $item['total_price'] ?? 0,
                    'unit_price_converted' => ($item['unit_price'] ?? 0) * $itemExcRate,
                    'total_converted' => ($item['total_price'] ?? 0) * $itemExcRate,
                ]);

                $createdItems[] = $row->toArray();
            }

            if ($isRevised === Quotation::REVISED) {
                $this->logService->logRevision($quotation);
            } else {
                $this->logService->logCreate($quotation);
            }

            if (!empty($createdItems)) {
                $this->logService->logItemsSync($quotation->quotation_id, $createdItems, [], []);
            }

            if ($request->boolean('send_email') && $quotation->email) {
                sendQuotOnEmail($quotation->email, $quotation->subject, $quotation->quotation_id);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => $isRevised === Quotation::REVISED
                    ? 'Quotation revised successfully.'
                    : 'Quotation created successfully.',
                'data' => [
                    'quotation_id' => $quotation->quotation_id,
                    'quotation_no' => $quotation->quotation_no,
                    'revision_no' => $quotation->revised_count,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
     public function quotationPdf($id, Request $request)
    {
        $quotationId = decrypt($id);

        $document = prepareDocumentData($quotationId);

        $data = [
            'document' => $document,
            'date' => now()->format('d M Y'),
            'company' => get_company_details(1)
        ];

        $pdf = Pdf::setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('company.pdf.quotation', $data)
            ->setPaper('A4');

        $revSuffix = ((int) ($document->revision_no ?? 0) > 0) ? "-R{$document->revision_no}" : '';
        $filename = "Quotation-{$document->quotation_no}-{$document->customer_name}{$revSuffix}.pdf";

        return $pdf->stream($filename);
    }
     public function list(Request $request)
    {
        $role = Session::get('login_type');
        $user_id = getCreatedBy();

        $query = Quotation::with(['lead', 'deal', 'items.product'])
            ->whereIn('quotation_id', function ($sub) {
                $sub->selectRaw('MAX(quotation_id)')
                    ->from('tbl_quotation')
                    ->groupBy('quotation_no');
            });
           

        if ($role == 'Staff') {
            $query->where(function ($q) use ($user_id) {
                $q->where('created_by', $user_id)
                    ->orWhere('assigned_to', $user_id);
            });
        }

        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $end = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('quotation_date', [$start, $end]);
                } catch (\Exception $e) {}
            } elseif (count($dates) == 1 && !empty($dates[0])) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->format('Y-m-d');
                    $query->whereDate('quotation_date', $start);
                } catch (\Exception $e) {}
            }
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('quotation_no')) {
            $query->where('quotation_no', $request->quotation_no);
        }

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($role == 'Company') {
            if ($request->filled('created_by')) {
                $query->where('created_by', $request->created_by);
            }
            if ($request->filled('assigned_to')) {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        if (!$request->has('order')) {
            $query->orderByDesc('quotation_id');
        }

        return DataTables::of($query)

            ->addIndexColumn()

            ->filter(function ($query) use ($request) {
                if ($request->filled('search.value')) {
                    $search = $request->input('search.value');

                    $query->where(function ($q) use ($search) {
                        $q->where('quotation_no', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%")
                            ->orWhere('subject', 'like', "%{$search}%");
                    });
                }
            })

            ->editColumn('quotation_no', function ($q) {
                $createdBy = get_createdby_name($q->created_by);
                $assignedTo = get_createdby_name($q->assigned_to);

                $html = '<div class="fw-semibold">' . e($q->quotation_no);

                if ($q->revised_count > 0) {
                    $html .= ' <span class="badge bg-warning text-dark">R' . $q->revised_count . '</span>';
                }

                $html .= '</div>';

                if (!empty($createdBy)) {
                    $html .= '<small class="text-muted d-block mt-1">
                    <i class="fas fa-user text-primary me-1"></i>' . e($createdBy) . '
                  </small>';
                }

                if (!empty($assignedTo)) {
                    $html .= '<small class="text-muted d-block">
                    <i class="fas fa-user-tie text-success me-1"></i>' . e($assignedTo) . '
                  </small>';
                }

                return $html;
            })

            ->editColumn('customer_name', function ($q) {
                $name = e($q->customer_name);
                if ($q->is_direct == 0) {
                    $name .= '<br><span class="badge bg-info text-white" style="font-size: 10px;">Direct Quotation</span>';
                }
                return $name;
            })

            ->editColumn('quotation_date', function ($q) {
                return \Carbon\Carbon::parse($q->quotation_date)->format('d M Y');
            })

            ->addColumn('items', function ($q) {
                $isINR = strtoupper($q->currency ?? 'INR') === 'INR';
                $curr = $q->currency ?? 'INR';
                $excRate = (float) ($q->exchange_rate ?? 1.0);
                $prefix = $isINR ? '₹' : $curr . ' ';
                $html = '<div style="max-height:100px;overflow-y:auto;overflow-x:hidden;padding-right:2px;">';
                foreach ($q->items as $item) {
                    $lineTotal = $isINR ? $item->total : ($item->total_converted > 0 ? $item->total_converted : $item->total * $excRate);
                    $unitPrice = $isINR ? $item->unit_price : ($item->unit_price_converted > 0 ? $item->unit_price_converted : $item->unit_price * $excRate);
                    $discPrice = $isINR ? $item->discount_price : $item->discount_price * $excRate;
                    $gstAmt = $isINR ? $item->gst_amount : $item->gst_amount * $excRate;
                    $priceRow = '<span class="me-2 text-muted" style="font-size:11px;">Price: <b>' . $prefix . number_format($unitPrice, 2) . '</b></span>';
                    if ($discPrice > 0) {
                        $priceRow .= '<span class="me-2 text-danger" style="font-size:11px;">Disc: <b>' . $prefix . number_format($discPrice, 2) . '</b></span>';
                    }
                    if ($item->gst_percent > 0) {
                        $priceRow .= '<span class="text-muted" style="font-size:11px;">GST ' . $item->gst_percent . '%: <b>' . $prefix . number_format($gstAmt, 2) . '</b></span>';
                    }
                    $html .= '
                    <div style="border-left:3px solid #4e73df;background:#f8f9fc;border-radius:4px;padding:5px 8px;margin-bottom:5px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold text-dark text-truncate" style="font-size:12px;max-width:160px;" title="' . e($item->product->prod_name ?? '-') . '">
                                ' . e($item->product->prod_name ?? '-') . '
                            </span>
                            <span class="badge ms-1 text-white" style="font-size:9px;background:#4e73df;white-space:nowrap;">
                                Qty: ' . $item->qty . '
                            </span>
                        </div>
                        <div class="mt-1 d-flex flex-wrap align-items-center justify-content-between">
                            <div>' . $priceRow . '</div>
                            <span class="fw-bold text-success" style="font-size:12px;white-space:nowrap;">' . $prefix . number_format($lineTotal, 2) . '</span>
                        </div>
                    </div>';
                }
                return $html . '</div>';
            })

            ->addColumn('total', function ($q) {
                $isINR = strtoupper($q->currency ?? 'INR') === 'INR';
                $curr = $q->currency ?? 'INR';
                $excRate = (float) ($q->exchange_rate ?? 1.0);
                $subtotalINR = $q->grand_total - $q->gst_total;
                $subtotal = $isINR ? $subtotalINR : $subtotalINR * $excRate;
                $gst = $isINR ? $q->gst_total : $q->gst_total * $excRate;
                $grand = $isINR ? $q->grand_total : ($q->grand_total_converted > 0 ? $q->grand_total_converted : $q->grand_total * $excRate);
                $prefix = $isINR ? '₹' : $curr . ' ';
                return '
                <div class="small text-muted">
                    Subtotal : ' . $prefix . number_format($subtotal, 2) . '<br>
                    GST : ' . $prefix . number_format($gst, 2) . '
                </div>
                <div class="fw-bold text-primary">
                    ' . $prefix . number_format($grand, 2) . '
                </div>';
            })

            ->addColumn('action', function ($q) {
                $revisionCount = Quotation::where('quotation_no', $q->quotation_no)
                    ->where('is_revised', Quotation::REVISED)
                    ->count();
                $historyBtn = '';
                if ($revisionCount > 0) {
                    $historyBtn = '
                    <button class="btn btn-sm btn-info m-1 quot-hist position-relative"
                        data-id="' . $q->deal_id . '"
                        data-customer-name="' . $q->customer_name . '"
                        data-quot-no="' . $q->quotation_no . '"
                        data-quot-id="' . $q->quotation_id . '">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            ' . $revisionCount . '
                        </span>
                    </button>';
                }
                $reviseBtn = '';
                if (!($q->deal && $q->deal->deal_status == \App\Models\Deal::DEAL_WON)) {
                    $reviseUrl = $q->is_direct == 0
                        ? route('direct_quotation.index', encrypt($q->quotation_id))
                        : route('quotation.create', encrypt($q->deal_id));
                    $reviseBtn = '
                    <a href="' . $reviseUrl . '" target="_blank" class="btn btn-sm btn-warning m-1">
                        <i class="fa-solid fa-repeat"></i>
                    </a>';
                }

                return $historyBtn . '
                <a href="' . route('quotation.pdf', encrypt($q->quotation_id)) . '" target="_blank" class="btn btn-sm btn-danger m-1">
                    <i class="fa-solid fa-file-pdf"></i>
                </a>
                ' . $reviseBtn . '
                <button class="btn btn-sm btn-success m-1 quot-mail"
                    data-id="' . $q->quotation_id . '"
                    data-message="' . e($q->message) . '"
                    data-email="' . e($q->email) . '">
                    <i class="fa-solid fa-envelope"></i>
                </button>';
            })
             ->orderColumn('quotation_no', function ($query, $order) {
                $query->orderBy('quotation_no', $order);
            })

            ->orderColumn('customer_name', function ($query, $order) {
                $query->orderBy('customer_name', $order);
            })

            ->orderColumn('quotation_date', function ($query, $order) {
                $query->orderBy('quotation_date', $order);
            })

            ->orderColumn('grand_total', function ($query, $order) {
                $query->orderBy('grand_total', $order);
            })
            ->rawColumns([
                'quotation_no',
                'customer_name',
                'items',
                'total',
                'action'
            ])

            ->make(true);
    }
    public function getQuotHistory(Request $request)
    {
        $dealId = $request->input('deal_id');
        $quotId = $request->input('quot_id');
        $query = Quotation::with('items.product')
            ->where('quotation_id', '!=', $quotId);
        $quot = Quotation::find($quotId);
        if ($quot && $quot->is_direct == 0) {
            $query->where('quotation_no', $quot->quotation_no);
        } else {
            $query->where('deal_id', $dealId);
        }
        $quotations = $query->orderBy('quotation_id', 'desc')->get();

        $data = $quotations->map(function ($q) {
            $isINR = strtoupper($q->currency ?? 'INR') === 'INR';
            $curr = $q->currency ?? 'INR';
            $excRate = (float) ($q->exchange_rate ?? 1.0);
            $prefix = $isINR ? '₹' : $curr . ' ';
            $subtotalINR = $q->grand_total - $q->gst_total;
            $subtotal = $isINR ? $subtotalINR : $subtotalINR * $excRate;
            $gst = $isINR ? $q->gst_total : $q->gst_total * $excRate;
            $grand = $isINR ? $q->grand_total : ($q->grand_total_converted > 0 ? $q->grand_total_converted : $q->grand_total * $excRate);

            return [
                'enc_id' => encrypt($q->quotation_id),
                'quotation_no' => $q->quotation_no,
                'revision_no' => $q->revised_count,
                'revision_label' => $q->revised_count > 0 ? 'Rev. ' . $q->revised_count : 'Original',
                'date' => \Carbon\Carbon::parse($q->created_at)->format('d M Y, h:i A'),
                'customer_name' => $q->customer_name,
                'subtotal' => $prefix . number_format($subtotal, 2),
                'gst_total' => $prefix . number_format($gst, 2),
                'grand_total' => $prefix . number_format($grand, 2),
                'items' => $q->items->map(function ($item) use ($isINR, $prefix, $excRate) {
                    $price = $isINR ? $item->unit_price : ($item->unit_price_converted > 0 ? $item->unit_price_converted : $item->unit_price * $excRate);
                    $gstAmt = $isINR ? $item->gst_amount : $item->gst_amount * $excRate;
                    $lineTotal = $isINR ? $item->total : ($item->total_converted > 0 ? $item->total_converted : $item->total * $excRate);
                    return [
                        'item_name' => $item->product->prod_name ?? '-',
                        'qty' => $item->qty,
                        'price' => $prefix . number_format($price, 2),
                        'gst_percent' => $item->gst_percent,
                        'gst_amount' => $prefix . number_format($gstAmt, 2),
                        'line_total' => $prefix . number_format($lineTotal, 2),
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
    public function sendQuotationEmail(Request $request)
    {
        $request->validate([
            'quotation_id' => ['required', 'integer'],
            'emails' => ['required', 'array', 'min:1'],
            'emails.*' => ['required', 'email'],
            'message' => ['required', 'string'],
        ]);

        try {
            sendQuotOnEmail(
                $request->emails,
                'Quotation Details',
                $request->quotation_id,
                $request->input('message', null)
            );

            return response()->json([
                'status' => true,
                'message' => 'Quotation email sent successfully.'
            ]);
        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status' => false,
                'message' => 'Unable to send quotation email. Please try again later.'
            ], 500);
        }
    }
}
