<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GstModel;
use App\Models\PerformaInvoice;
use App\Models\PerformaInvoiceItem;
use App\Models\Quotation;
use App\Services\PerformaInvoiceLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class PerformaInvoiceController extends Controller
{
    public function __construct(protected PerformaInvoiceLogService $logService)
    {
    }

        public function index()
        {
            $page_name = 'Performa Invoice';
            $invoice_no = PerformaInvoice::getInvoiceNo();
            $gsts = GstModel::active()->get();
            $staffs = DB::table('mst_staff')
                ->select('staff_id', 'staff_name', 'staff_type')
                ->where('status', 0)
                ->orderBy('staff_name')
                ->get();
            $terms = DB::table('tbl_terms')
                ->select('term_id', 'module', 'terms')
                ->where('status', 0)
                ->orderBy('module')
                ->orderBy('terms')
                ->get();
            $categories = DB::table('tbl_product_category')
                ->where('status', 0)
                ->orderBy('name', 'asc')
                ->get();
            $products = DB::table('tbl_product')
                ->leftJoin('mst_gst', 'tbl_product.gst', '=', 'mst_gst.gst_id')
                ->select('tbl_product.*', 'mst_gst.gst_no')
                ->where('tbl_product.status', 0)
                ->orderBy('tbl_product.prod_name', 'asc')
                ->get();

            return view('company.invoice.performa.index', compact(
                'page_name',
                'invoice_no',
                'gsts',
                'staffs',
                'terms',
                'categories',
                'products'
            ));
        }

        public function list(Request $request)
        {
            $role = Session::get('login_type');
            $user_id = getCreatedBy();
            $query = PerformaInvoice::with(['customer', 'items.product']);
                

            if ($role == 'Staff') {
                $query->where(function ($q) use ($user_id) {
                    $q->where('created_by', $user_id)
                        ->orWhere('assigned_to', $user_id);
                });
            }

            if (!$request->has('order')) {
                $query->orderByDesc('invoice_id');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($request->filled('search.value')) {
                        $search = $request->input('search.value');
                        $query->where(function ($q) use ($search) {
                            $q->where('invoice_no', 'like', "%{$search}%")
                                ->orWhereHas('customer', function ($c) use ($search) {
                                    $c->where('company_name', 'like', "%{$search}%");
                                })
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                    }
                })
                ->editColumn('invoice_no', function ($invoice) {
                    $createdBy = get_createdby_name($invoice->created_by);
                    $assignedTo = get_createdby_name($invoice->assigned_to);
                    $html = '<div class="fw-semibold">' . e($invoice->invoice_no) . '</div>';
                    if ($createdBy) {
                        $html .= '<small class="text-muted d-block mt-1"><i class="fas fa-user text-primary me-1"></i>' . e($createdBy) . '</small>';
                    }
                    if ($assignedTo) {
                        $html .= '<small class="text-muted d-block"><i class="fas fa-user-tie text-success me-1"></i>' . e($assignedTo) . '</small>';
                    }
                    return $html;
                })
                ->addColumn('customer', function ($invoice) {
                    $html = e($invoice->customer->company_name ?? '-');
                    if ($invoice->quotation_id) {
                        $html .= '<br><span class="badge bg-info">From Quotation</span>';
                    } else {
                        $html .= '<br><span class="badge bg-secondary">Direct</span>';
                    }
                    return $html;
                })
                ->editColumn('invoice_date', function ($invoice) {
                    return $invoice->invoice_date->format('d M Y');
                })
                ->addColumn('items', function ($invoice) {
                    $isINR = strtoupper($invoice->currency ?? 'INR') === 'INR';
                    $curr = $invoice->currency ?? 'INR';
                    $excRate = (float) ($invoice->exchange_rate ?? 1.0);
                    $html = '<div style="max-height:100px;overflow-y:auto;">';
                    foreach ($invoice->items as $item) {
                        $price = $isINR ? $item->unit_price : ($item->unit_price_converted > 0 ? $item->unit_price_converted : $item->unit_price * $excRate);
                        $gstAmt = $isINR ? $item->gst_amount : $item->gst_amount * $excRate;
                        $total = $isINR ? $item->total : ($item->total_converted > 0 ? $item->total_converted : $item->total * $excRate);
                        $prefix = $isINR ? '₹' : $curr . ' ';
                        $html .= '<div style="border-left:3px solid #4e73df;background:#f8f9fc;border-radius:4px;padding:5px 8px;margin-bottom:5px;">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">' . e($item->product->prod_name ?? '-') . '</span>
                                <span class="badge bg-primary">Qty : ' . $item->qty . '</span>
                            </div>
                            <div class="small text-muted">Price : ' . $prefix . number_format($price, 2) . '</div>';
                        if ($item->discount_price > 0) {
                            $disc = $isINR ? $item->discount_price : $item->discount_price * $excRate;
                            $html .= '<div class="small text-danger">Discount : ' . $item->discount_percentage . '% (' . $prefix . number_format($disc, 2) . ')</div>';
                        }
                        $html .= '<div class="small text-muted">GST : ' . $item->gst_percent . '% (' . $prefix . number_format($gstAmt, 2) . ')</div>
                            <div class="fw-bold text-success">' . $prefix . number_format($total, 2) . '</div>
                        </div>';
                    }
                    return $html . '</div>';
                })
                ->addColumn('total', function ($invoice) {
                    $isINR = strtoupper($invoice->currency ?? 'INR') === 'INR';
                    $curr = $invoice->currency ?? 'INR';
                    $excRate = (float) ($invoice->exchange_rate ?? 1.0);
                    $subtotal = $isINR ? $invoice->subtotal : $invoice->subtotal * $excRate;
                    $gst = $isINR ? $invoice->gst_total : $invoice->gst_total * $excRate;
                    $discount = $isINR ? $invoice->discount_total : $invoice->discount_total * $excRate;
                    $grand = $isINR ? $invoice->grand_total : ($invoice->grand_total_converted > 0 ? $invoice->grand_total_converted : $invoice->grand_total * $excRate);
                    $prefix = $isINR ? '₹' : $curr . ' ';
                    $html = '<div class="small text-muted">Subtotal : ' . $prefix . number_format($subtotal, 2);
                    if ($discount > 0) {
                        $html .= '<br>Discount : <span class="text-danger">-' . $prefix . number_format($discount, 2) . '</span>';
                    }
                    $html .= '<br>GST : ' . $prefix . number_format($gst, 2) . '</div><div class="fw-bold text-primary">' . $prefix . number_format($grand, 2) . '</div>';
                    return $html;
                })
                ->addColumn('action', function ($invoice) {
                    $html = '';
                    $html .= '<button class="btn btn-sm btn-info text-white m-1 view-invoice" data-id="' . $invoice->invoice_id . '" data-bs-toggle="modal" data-bs-target="#viewInvoiceModal"><i class="fa-solid fa-eye"></i></button>';
                    if (permissions_check('canUpdate')) {
                        $html .= '<button class="btn btn-sm btn-primary m-1 edit-invoice" data-id="' . $invoice->invoice_id . '"><i class="fa-solid fa-pen-to-square"></i></button>';
                    }


                    $message = 'Dear ' . ($invoice->customer->company_name ?? '') . ",\n\nPlease find our performa invoice details below:\n\nBest regards,\n" . get_company_details(1)->name;
                    $html .= '<button class="btn btn-sm btn-success m-1 invoice-mail" data-id="' . $invoice->invoice_id . '" data-email="' . e($invoice->email) . '" data-message="' . e($message) . '"><i class="fa-solid fa-envelope"></i></button>';
                    $html .= '<a href="' . route('performa_invoice.pdf', encrypt($invoice->invoice_id)) . '" target="_blank" class="btn btn-sm btn-danger m-1"><i class="fa-solid fa-file-pdf"></i></a>';
                    return $html;
                })
                ->orderColumn('invoice_no', function ($query, $order) {
                    $query->orderBy('invoice_no', $order);
                })

                ->orderColumn('customer_name', function ($query, $order) {
                    $query->orderBy('customer_name', $order);
                })

                ->orderColumn('invoice_date', function ($query, $order) {
                    $query->orderBy('invoice_date', $order);
                })

                ->orderColumn('grand_total', function ($query, $order) {
                    $query->orderBy('grand_total', $order);
                })
                ->rawColumns(['invoice_no', 'customer', 'items', 'total', 'action'])
                ->make(true);
        }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:tbl_customer,customer_id',
            'invoice_date' => 'required|date_format:d-m-Y',
            'invoice_type' => 'required|in:domestic,export,Domestic,Export',
            'currency' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'email' => 'required|email',
            'phone' => ['required', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            'terms_payment' => 'nullable|array',
            'dispatch_time' => 'nullable|string|max:255',
            'delivery_note' => 'nullable|string|max:255',
            'po_reference' => 'nullable|string|max:255',
            'transportation' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'buyer_order_no' => 'nullable|string|max:100',
            'buyer_order_date' => 'nullable|date_format:d-m-Y',
            'advance_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'advance_payment' => 'nullable|numeric|min:0',
            'balance_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'balance_payment' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|integer',
            'gst_exemption' => 'required|in:0,1',
            'include_gst' => 'nullable|integer|in:0,1',
            'items' => 'required|string',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx,xls,xlsx,zip|max:5120',
        ]);

        $itemsRaw = json_decode($request->input('items'), true);
        if (!$itemsRaw || !is_array($itemsRaw) || count($itemsRaw) === 0) {
            return response()->json([
                'status' => false,
                'message' => 'Please add at least one product.',
            ], 422);
        }

        $attachmentPaths = [];
        if ($request->hasFile('files')) {
            $dir = public_path('assets/uploads/performa/attachments');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            foreach ($request->file('files') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->extension();
                $file->move($dir, $fileName);
                $attachmentPaths[] = $fileName;
            }
        }

        DB::beginTransaction();
        try {
            $gstExemption = (int) $request->input('gst_exemption');
            $includeGst = (int) $request->input('include_gst', 0);
            $exchangeRate = (float) $request->input('exchange_rate', 1.0);
            $totals = $this->calculateTotals($itemsRaw, $gstExemption, $includeGst);
            $invoice = PerformaInvoice::create([
                'invoice_no' => PerformaInvoice::getInvoiceNo(),
                'invoice_type' => strtolower($request->input('invoice_type', 'domestic')),
                'customer_id' => $request->input('customer_id'),
                'quotation_id' => $request->input('quotation_id') ?: null,
                'buyer_order_no' => $request->input('buyer_order_no'),
                'buyer_order_date' => $request->input('buyer_order_date') 
                    ? \Carbon\Carbon::createFromFormat('d-m-Y', $request->input('buyer_order_date'))->toDateString() 
                    : null,
                'assigned_to' => $request->input('assigned_to') ?: null,
                'invoice_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->input('invoice_date'))->toDateString(),
                'advance_payment_percentage' => $request->input('advance_payment_percentage') === '' || $request->input('advance_payment_percentage') === null ? 0 : $request->input('advance_payment_percentage'),
                'advance_payment' => $request->input('advance_payment') === '' || $request->input('advance_payment') === null ? 0 : $request->input('advance_payment'),
                'balance_payment_percentage' => $request->input('balance_payment_percentage') === '' || $request->input('balance_payment_percentage') === null ? 0 : $request->input('balance_payment_percentage'),
                'balance_payment' => $request->input('balance_payment') === '' || $request->input('balance_payment') === null ? 0 : $request->input('balance_payment'),
                'terms_payment' => $request->has('terms_payment') ? implode(',', (array)$request->input('terms_payment')) : null,
                'transportation' => $request->input('transportation'),
                'destination' => $request->input('destination'),
                'dispatch_time' => $request->input('delivery_note'), // mapping delivery_note to dispatch_time too just in case
                'delivery_note' => $request->input('delivery_note'),
                'po_reference' => $request->input('po_reference'),
                'gst_exemption' => $gstExemption,
                'include_gst' => $includeGst,
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'country_id' => $request->input('country_id'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                'currency' => $request->input('currency', 'INR'),
                'exchange_rate' => $exchangeRate,
                'subtotal' => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'gst_total' => $totals['gst_total'],
                'grand_total' => $totals['grand_total'],
                'grand_total_converted' => $totals['grand_total'] * $exchangeRate,
                'status' => PerformaInvoice::ACTIVE,
                'created_by' => getCreatedBy(),
                'updated_by' => getCreatedBy(),
                'attachments' => !empty($attachmentPaths) ? implode(',', $attachmentPaths) : null,
            ]);

            if ($request->input('customer_id')) {
                Customer::where('customer_id', $request->input('customer_id'))->update(array_filter([
                    'phone' => $request->input('phone'),
                    'email' => $request->input('email'),
                ]));
            }

            $createdItems = $this->syncItems($invoice->invoice_id, $itemsRaw, $gstExemption, $includeGst, $exchangeRate);
            $this->logService->logCreate($invoice);
            if (!empty($createdItems)) {
                $this->logService->logItemsSync($invoice->invoice_id, $createdItems, [], []);
            }

            DB::commit();

            if ($request->boolean('send_email') && $invoice->email) {
                sendPerformaInvoiceOnEmail($invoice->email, $invoice->invoice_id);
            }

            return response()->json([
                'status' => true,
                'message' => 'Invoice created successfully.',
                'data' => [
                    'invoice_id' => $invoice->invoice_id,
                    'invoice_no' => $invoice->invoice_no,
                    'new_invoice_no' => PerformaInvoice::getInvoiceNo(),
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $invoice = PerformaInvoice::with(['items.product', 'customer'])->find($id);
        if (!$invoice) {
            return response()->json(['status' => false, 'message' => 'Invoice not found.'], 404);
        }

        $cityName = DB::table('mst_city')->where('city_id', $invoice->city_id)->value('city_name');
        $stateName = DB::table('mst_state')->where('state_id', $invoice->state_id)->value('state_name');
        $countryName = DB::table('mst_country')->where('c_id', $invoice->country_id)->value('country_name');
        $quotationNo = '';
        if ($invoice->quotation_id) {
            $quotationNo = DB::table('tbl_quotation')->where('quotation_id', $invoice->quotation_id)->value('quotation_no') ?? '';
        }

        $exchangeRate = (float) ($invoice->exchange_rate ?? 1.0);
        $items = $invoice->items->map(function ($item) use ($exchangeRate) {
            $rawPrice = floatval($item->unit_price);
            $discountPrice = (float) ($item->discount_price ?? 0);
            $discountPercentage = (float) ($item->discount_percentage ?? 0);
            
            $prodGst = 0.0;
            if ($item->product) {
                $prodGstVal = \DB::table('mst_gst')
                    ->where('gst_id', $item->product->gst)
                    ->value('gst_no');
                $prodGst = (float) $prodGstVal;
            }
            if ($prodGst == 0 && $item->gst_percent > 0) {
                $prodGst = (float) $item->gst_percent;
            }

            return [
                'pii_id' => $item->pii_id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->prod_name ?? '-',
                'description' => $item->description,
                'qty' => (float) $item->qty,
                'unit_price' => (float) $item->unit_price,
                'gst_percent' => (float) $item->gst_percent,
                'gst_amount' => (float) $item->gst_amount,
                'product_gst' => $prodGst,
                'total' => (float) $item->total,
                'discount_percentage' => $discountPercentage,
                'discount_price' => $discountPrice,
            ];
        });

        $termsText = '';
        $storedTermsPayment = $invoice->terms_payment;
        if ($invoice->terms_payment) {
            $termIds = array_filter(explode(',', $invoice->terms_payment));
            $allNumeric = true;
            foreach ($termIds as $id) {
                if (!is_numeric($id)) {
                    $allNumeric = false;
                    break;
                }
            }

            if ($allNumeric && !empty($termIds)) {
                $dbTerms = DB::table('tbl_terms')
                    ->whereIn('term_id', $termIds)
                    ->orderByRaw('FIELD(term_id, ' . implode(',', $termIds) . ')')
                    ->get();
                    
                $groups = [];
                foreach ($dbTerms as $t) {
                    $groups[$t->module ?: 'General Terms'][] = $t->terms;
                }
                
                $formatted = [];
                foreach ($groups as $moduleName => $moduleTerms) {
                    $formatted[] = $moduleName . ":";
                    foreach ($moduleTerms as $term) {
                        $formatted[] = "- " . $term;
                    }
                    $formatted[] = "";
                }
                $termsText = trim(implode("\n", $formatted));
            } else {
                $termsText = $invoice->terms_payment;
                $storedTermsPayment = ''; // Clear raw text for input selection
            }
        }

        return response()->json([
            'status' => true,
            'data' => [
                'invoice_id' => $invoice->invoice_id,
                'invoice_no' => $invoice->invoice_no,
                'pdf_url' => route('performa_invoice.pdf', encrypt($invoice->invoice_id)),
                'invoice_type' => $invoice->invoice_type,
                'customer_id' => $invoice->customer_id,
                'customer_name' => $invoice->customer->company_name ?? '',
                'quotation_id' => $invoice->quotation_id,
                'quotation_no' => $quotationNo,
                'assigned_to' => $invoice->assigned_to,
                'invoice_date' => $invoice->invoice_date->format('d-m-Y'),
                'buyer_order_no' => $invoice->buyer_order_no,
                'buyer_order_date' => $invoice->buyer_order_date ? $invoice->buyer_order_date->format('d-m-Y') : '',
                'destination' => $invoice->destination,
                'email' => $invoice->email,
                'phone' => $invoice->phone,
                'city_id' => $invoice->city_id,
                'city_name' => $cityName,
                'state_id' => $invoice->state_id,
                'state_name' => $stateName,
                'country_id' => $invoice->country_id,
                'country_name' => $countryName,
                'gst_exemption' => $invoice->gst_exemption,
                'include_gst' => $invoice->include_gst,
                'terms_payment' => $storedTermsPayment,
                'terms_payment_text' => $termsText,
                'dispatch_time' => $invoice->dispatch_time,
                'delivery_note' => $invoice->delivery_note,
                'po_reference' => $invoice->po_reference,
                'transportation' => $invoice->transportation,
                'advance_payment_percentage' => (float) $invoice->advance_payment_percentage,
                'advance_payment' => (float) $invoice->advance_payment,
                'balance_payment_percentage' => (float) $invoice->balance_payment_percentage,
                'balance_payment' => (float) $invoice->balance_payment,
                'currency' => $invoice->currency,
                'exchange_rate' => $exchangeRate,
                'subtotal' => (float) $invoice->subtotal,
                'gst_total' => (float) $invoice->gst_total,
                'grand_total' => (float) $invoice->grand_total,
                'items' => $items,
                'attachments' => $invoice->attachments ? explode(',', $invoice->attachments) : [],
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $invoice = PerformaInvoice::find($id);
        if (!$invoice) {
            return response()->json(['status' => false, 'message' => 'Invoice not found.'], 404);
        }

        $request->validate([
            'customer_id' => 'required|integer|exists:tbl_customer,customer_id',
            'invoice_date' => 'required|date_format:d-m-Y',
            'invoice_type' => 'required|in:domestic,export,Domestic,Export',
            'currency' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'email' => 'required|email',
            'phone' => ['required', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            'terms_payment' => 'nullable|array',
            'dispatch_time' => 'nullable|string|max:255',
            'delivery_note' => 'nullable|string|max:255',
            'po_reference' => 'nullable|string|max:255',
            'transportation' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'buyer_order_no' => 'nullable|string|max:100',
            'buyer_order_date' => 'nullable|date_format:d-m-Y',
            'advance_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'advance_payment' => 'nullable|numeric|min:0',
            'balance_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'balance_payment' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|integer',
            'gst_exemption' => 'required|in:0,1',
            'include_gst' => 'nullable|integer|in:0,1',
            'items' => 'required|string',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx,xls,xlsx,zip|max:5120',
        ]);

        $itemsRaw = json_decode($request->input('items'), true);
        if (!$itemsRaw || !is_array($itemsRaw) || count($itemsRaw) === 0) {
            return response()->json([
                'status' => false,
                'message' => 'Please add at least one product.',
            ], 422);
        }

        // Handle attachment file changes
        $attachmentPaths = $invoice->attachments ? explode(',', $invoice->attachments) : [];
        if ($request->has('delete_attachments')) {
            $toDelete = $request->input('delete_attachments');
            if (is_array($toDelete)) {
                foreach ($toDelete as $filename) {
                    $filePath = public_path('assets/uploads/performa/attachments/' . $filename);
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                    $attachmentPaths = array_diff($attachmentPaths, [$filename]);
                }
            }
        }

        if ($request->hasFile('files')) {
            $dir = public_path('assets/uploads/performa/attachments');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            foreach ($request->file('files') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->extension();
                $file->move($dir, $fileName);
                $attachmentPaths[] = $fileName;
            }
        }

        DB::beginTransaction();
        try {
            $before = $invoice->toArray();
            $gstExemption = (int) $request->input('gst_exemption');
            $includeGst = (int) $request->input('include_gst', 0);
            $exchangeRate = (float) $request->input('exchange_rate', 1.0);
            $totals = $this->calculateTotals($itemsRaw, $gstExemption, $includeGst);

            $invoice->update([
                'customer_id' => $request->input('customer_id'),
                'quotation_id' => $request->input('quotation_id') ?: null,
                'buyer_order_no' => $request->input('buyer_order_no'),
                'buyer_order_date' => $request->input('buyer_order_date') 
                    ? \Carbon\Carbon::createFromFormat('d-m-Y', $request->input('buyer_order_date'))->toDateString() 
                    : null,
                'assigned_to' => $request->input('assigned_to') ?: null,
                'invoice_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->input('invoice_date'))->toDateString(),
                'advance_payment_percentage' => $request->input('advance_payment_percentage') === '' || $request->input('advance_payment_percentage') === null ? 0 : $request->input('advance_payment_percentage'),
                'advance_payment' => $request->input('advance_payment') === '' || $request->input('advance_payment') === null ? 0 : $request->input('advance_payment'),
                'balance_payment_percentage' => $request->input('balance_payment_percentage') === '' || $request->input('balance_payment_percentage') === null ? 0 : $request->input('balance_payment_percentage'),
                'balance_payment' => $request->input('balance_payment') === '' || $request->input('balance_payment') === null ? 0 : $request->input('balance_payment'),
                'terms_payment' => $request->has('terms_payment') ? implode(',', (array)$request->input('terms_payment')) : null,
                'transportation' => $request->input('transportation'),
                'destination' => $request->input('destination'),
                'dispatch_time' => $request->input('delivery_note'), // mapping delivery_note to dispatch_time too just in case
                'delivery_note' => $request->input('delivery_note'),
                'po_reference' => $request->input('po_reference'),
                'gst_exemption' => $gstExemption,
                'include_gst' => $includeGst,
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'country_id' => $request->input('country_id'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                'invoice_type' => strtolower($request->input('invoice_type', 'domestic')),
                'currency' => $request->input('currency', 'INR'),
                'exchange_rate' => $exchangeRate,
                'subtotal' => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'gst_total' => $totals['gst_total'],
                'grand_total' => $totals['grand_total'],
                'grand_total_converted' => $totals['grand_total'] * $exchangeRate,
                'updated_by' => getCreatedBy(),
                'attachments' => !empty($attachmentPaths) ? implode(',', array_values($attachmentPaths)) : null,
            ]);

            if ($request->input('customer_id')) {
                Customer::where('customer_id', $request->input('customer_id'))->update(array_filter([
                    'phone' => $request->input('phone'),
                    'email' => $request->input('email'),
                ]));
            }

            $existingIds = $invoice->items()->pluck('pii_id')->toArray();
            $incomingIds = array_filter(array_column($itemsRaw, 'pii_id'));
            $toDelete = array_diff($existingIds, $incomingIds);
            $created = [];
            $updated = [];
            $deleted = [];

            if (!empty($toDelete)) {
                $deletedItems = PerformaInvoiceItem::whereIn('pii_id', $toDelete)->get()->toArray();
                PerformaInvoiceItem::whereIn('pii_id', $toDelete)->delete();
                $deleted = $deletedItems;
            }

            foreach ($itemsRaw as $itemData) {
                $itemRow = $this->buildItemRow($itemData, $invoice->invoice_id, $gstExemption, $includeGst, $exchangeRate);
                if (!empty($itemData['pii_id']) && in_array($itemData['pii_id'], $existingIds)) {
                    $existingItem = PerformaInvoiceItem::find($itemData['pii_id']);
                    if ($existingItem) {
                        $isDiff = (floatval($existingItem->qty) !== floatval($itemRow['qty'])) ||
                                  (floatval($existingItem->unit_price) !== floatval($itemRow['unit_price'])) ||
                                  (floatval($existingItem->total) !== floatval($itemRow['total'])) ||
                                  (floatval($existingItem->gst_percent) !== floatval($itemRow['gst_percent']));
                        $existingItem->update($itemRow);
                        if ($isDiff) {
                            $updated[] = $existingItem->toArray();
                        }
                    }
                } else {
                    $newItem = PerformaInvoiceItem::create($itemRow);
                    $created[] = $newItem->toArray();
                }
            }

            $invoice->refresh();
            $this->logService->logUpdate($invoice, $before, $invoice->toArray(), $created, $updated, $deleted);
            DB::commit();

            if ($request->boolean('send_email') && $invoice->email) {
                sendPerformaInvoiceOnEmail($invoice->email, $invoice->invoice_id);
            }

            return response()->json([
                'status' => true,
                'message' => 'Invoice updated successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getQuotations(Request $request)
    {
        $customerId = $request->input('customer_id');
        $search = $request->input('search');
        \Illuminate\Support\Facades\Log::info('getQuotations called', [
            'customer_id' => $customerId,
            'search' => $search
        ]);

        // Get quotation IDs that already have an active Performa Invoice
        $alreadyConverted = DB::table('tbl_performa_invoice')
            ->where('status', PerformaInvoice::ACTIVE)
            ->whereNotNull('quotation_id')
            ->pluck('quotation_id')
            ->toArray();

        $latestIds = Quotation::where('customer_id', $customerId)
            ->where('status', Quotation::ACTIVE)
            ->selectRaw('MAX(quotation_id) as quotation_id')
            ->groupBy('quotation_no');

        $query = Quotation::whereIn('quotation_id', $latestIds)
            ->whereNotIn('quotation_id', $alreadyConverted)
            ->orderByDesc('quotation_id');

        if (!empty($search)) {
            $query->where('quotation_no', 'like', "%{$search}%");
        }

        $quotations = $query->limit(50)->get([
            'quotation_id',
            'quotation_no',
            'quotation_date',
            'grand_total'
        ]);
        \Illuminate\Support\Facades\Log::info('getQuotations count: ' . $quotations->count(), [
            'ids' => $quotations->pluck('quotation_id')->toArray()
        ]);
        $results = $quotations->map(function ($q) {
            return [
                'id' => $q->quotation_id,
                'text' => $q->quotation_no . ' — Total: ₹' . number_format($q->grand_total, 2),
            ];
        });
        return response()->json(['results' => $results]);
    }

    public function getQuotationItems($id)
    {
        $quotation = Quotation::with('items.product')->find($id);
        if (!$quotation) {
            return response()->json(['status' => false, 'message' => 'Quotation not found.'], 404);
        }

        $rawItems = $quotation->items;
        if ($rawItems->isEmpty() && $quotation->parent_quotation_id) {
            $parent = Quotation::with('items.product')->find($quotation->parent_quotation_id);
            if ($parent && !$parent->items->isEmpty()) {
                $rawItems = $parent->items;
            }
        }

        $items = $rawItems->map(function ($item) {
            $prodGst = 0.0;
            if ($item->product) {
                $prodGstVal = \DB::table('mst_gst')
                    ->where('gst_id', $item->product->gst)
                    ->value('gst_no');
                $prodGst = (float) $prodGstVal;
            }
            if ($prodGst == 0 && $item->gst_percent > 0) {
                $prodGst = (float) $item->gst_percent;
            }

            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product->prod_name ?? '-',
                'description' => $item->description,
                'qty' => (float) $item->qty,
                'unit_price' => (float) $item->unit_price,
                'gst_percent' => (float) $item->gst_percent,
                'gst_amount' => (float) $item->gst_amount,
                'product_gst' => $prodGst,
                'total' => (float) $item->total,
                'discount_percentage' => (float) $item->discount_percentage,
                'discount_price' => (float) $item->discount_price,
            ];
        });
        $hasGst = ($quotation->gst_total > 0) ? 1 : 0;
        if (!$hasGst) {
            foreach ($rawItems as $item) {
                if ($item->gst_percent > 0) {
                    $hasGst = 1;
                    break;
                }
            }
        }

        $cityName = DB::table('mst_city')->where('city_id', $quotation->city_id)->value('city_name') ?? '';
        $stateName = DB::table('mst_state')->where('state_id', $quotation->state_id)->value('state_name') ?? '';
        $countryName = DB::table('mst_country')->where('c_id', $quotation->country_id)->value('country_name') ?? '';
        return response()->json([
            'status' => true,
            'items' => $items,
            'quotation' => [
                'quotation_no' => $quotation->quotation_no,
                'quotation_date' => $quotation->quotation_date ? \Carbon\Carbon::parse($quotation->quotation_date)->format('d-m-Y') : '',
                'quotation_type' => strtolower($quotation->quotation_type),
                'currency' => $quotation->currency,
                'exchange_rate' => $quotation->exchange_rate,
                'gst_exemption' => $quotation->gst_exemption,
                'include_gst' => $hasGst,
                'terms' => $quotation->terms,
                'city_id' => $quotation->city_id,
                'city_name' => $cityName,
                'state_id' => $quotation->state_id,
                'state_name' => $stateName,
                'country_id' => $quotation->country_id,
                'country_name' => $countryName,
                'assigned_to' => $quotation->assigned_to,
            ]
        ]);
    }

    public function sendEmail(Request $request, $id)
    {
        $request->validate([
            'emails' => ['required', 'array', 'min:1'],
            'emails.*' => ['required', 'email'],
            'message' => ['required', 'string'],
        ]);

        $invoice = PerformaInvoice::findOrFail($id);
        $sent = sendPerformaInvoiceOnEmail(
            $request->emails,
            $invoice->invoice_id,
            $request->input('message')
        );

        if ($sent) {
            return response()->json(['status' => true, 'message' => 'Email sent successfully.']);
        }
        return response()->json(['status' => false, 'message' => 'Failed to send email.']);
    }

    public function performaInvoicePdf($id)
    {
        $invoiceId = decrypt($id);
        $document = preparePerformaInvoiceDocumentData($invoiceId);
        $data = [
            'document' => $document,
            'date' => now()->format('d M Y'),
            'company' => get_company_details(1)
        ];
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('company.pdf.performa', $data)->setPaper('A4');
        return $pdf->stream("PerformaInvoice-{$document->invoice_no}-{$document->customer_name}.pdf");
    }

    private function calculateTotals(array $items, int $gstExemption, int $includeGst = 0): array
    {
        $subtotal = 0;
        $gstTotal = 0;
        $discountTotal = 0;
        foreach ($items as $item) {
            $qty = (float) ($item['qty'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $discountPrice = (float) ($item['discount_price'] ?? 0);
            $discountTotal += ($qty * $discountPrice);
            $discountedPrice = max(0, $unitPrice - $discountPrice);
            $lineTotal = $qty * $discountedPrice;
            $subtotal += $lineTotal;
            if ($includeGst && $gstExemption == PerformaInvoice::GST_NOT_EXEMPTED) {
                $gstPercent = (float) ($item['gst_percent'] ?? 0);
                $gstTotal += $lineTotal * ($gstPercent / 100);
            }
        }
        return [
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'gst_total' => round($gstTotal, 2),
            'grand_total' => round($subtotal + $gstTotal, 2),
        ];
    }

    private function buildItemRow(array $itemData, int $invoiceId, int $gstExemption, int $includeGst = 0, float $exchangeRate = 1.0): array
    {
        $qty = (float) ($itemData['qty'] ?? 0);
        $unitPrice = (float) ($itemData['unit_price'] ?? 0);
        $discountPrice = (float) ($itemData['discount_price'] ?? 0);
        $discountedPrice = max(0, $unitPrice - $discountPrice);
        $lineTotal = $qty * $discountedPrice;
        $gstPercent = 0;
        $gstAmount = 0;
        if ($includeGst && $gstExemption == PerformaInvoice::GST_NOT_EXEMPTED) {
            $gstPercent = (float) ($itemData['gst_percent'] ?? 0);
            $gstAmount = round($lineTotal * ($gstPercent / 100), 2);
        }
        $total = round($lineTotal + $gstAmount, 2);
        return [
            'invoice_id' => $invoiceId,
            'product_id' => $itemData['product_id'],
            'description' => $itemData['description'] ?? null,
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'unit_price_converted' => $unitPrice * $exchangeRate,
            'discount_percentage' => (float) ($itemData['discount_percentage'] ?? 0),
            'discount_price' => $discountPrice,
            'gst_percent' => $gstPercent,
            'gst_amount' => $gstAmount,
            'total' => $total,
            'total_converted' => $total * $exchangeRate,
        ];
    }

    private function syncItems(int $invoiceId, array $itemsRaw, int $gstExemption, int $includeGst = 0, float $exchangeRate = 1.0): array
    {
        $created = [];
        foreach ($itemsRaw as $itemData) {
            $row = $this->buildItemRow($itemData, $invoiceId, $gstExemption, $includeGst, $exchangeRate);
            $newItem = PerformaInvoiceItem::create($row);
            $created[] = $newItem->toArray();
        }
        return $created;
    }

    public function history($id)
    {
        $invoice = PerformaInvoice::with(['items.product'])->find($id);
        if (!$invoice) {
            return response()->json(['status' => false, 'message' => 'Invoice not found.'], 404);
        }

        $logs = DB::table('tbl_performa_invoice_log')
            ->where('invoice_id', $id)
            ->orderBy('performed_at', 'asc')
            ->get();

        $formatCurrency = function ($amount, $curr = 'INR') {
            $prefix = (strtoupper($curr ?? 'INR') === 'INR' || !$curr) ? '₹' : $curr . ' ';
            return $prefix . number_format(floatval($amount), 2);
        };

        // Fetch all terms to build a quick lookup map of term_id => module name
        $termsLookup = DB::table('tbl_terms')
            ->select('term_id', 'module')
            ->get()
            ->pluck('module', 'term_id')
            ->toArray();

        $formatTerms = function ($value) use ($termsLookup) {
            if (empty($value)) return '-';
            $ids = array_filter(explode(',', $value));
            $allNumeric = true;
            foreach ($ids as $termId) {
                if (!is_numeric($termId)) {
                    $allNumeric = false;
                    break;
                }
            }
            if ($allNumeric && !empty($ids)) {
                $names = [];
                foreach ($ids as $termId) {
                    if (isset($termsLookup[$termId])) {
                        $names[] = $termsLookup[$termId];
                    } else {
                        $names[] = 'Term #' . $termId;
                    }
                }
                return implode(', ', $names);
            }
            return $value;
        };

        // Group logs within 3-second windows to merge update logs from same transaction
        $groupedLogs = [];
        foreach ($logs as $log) {
            $p = json_decode($log->payload, true);
            if (is_string($p)) $p = json_decode($p, true);

            if ($log->action === 'create') {
                $groupedLogs[] = [
                    'action' => 'create',
                    'performed_at' => $log->performed_at,
                    'performed_by' => $log->performed_by,
                    'payload' => $p,
                ];
                continue;
            }

            if ($log->action === 'update') {
                $hasFieldChanges = isset($p['before']) && !empty($p['before']);
                $hasItemChanges = isset($p['items_created']) || isset($p['items_updated']) || isset($p['items_deleted']);
                if (!$hasFieldChanges && !$hasItemChanges) {
                    continue;
                }

                $logTime = \Carbon\Carbon::parse($log->performed_at);
                $matchedIdx = null;
                foreach ($groupedLogs as $idx => $g) {
                    if ($g['action'] === 'update' && abs(\Carbon\Carbon::parse($g['performed_at'])->timestamp - $logTime->timestamp) <= 3) {
                        $matchedIdx = $idx;
                        break;
                    }
                }

                if ($matchedIdx !== null) {
                    if ($hasFieldChanges) {
                        $groupedLogs[$matchedIdx]['payload']['before'] = array_merge($groupedLogs[$matchedIdx]['payload']['before'] ?? [], $p['before']);
                        $groupedLogs[$matchedIdx]['payload']['after'] = array_merge($groupedLogs[$matchedIdx]['payload']['after'] ?? [], $p['after'] ?? []);
                    }
                    if (isset($p['items_created'])) $groupedLogs[$matchedIdx]['payload']['items_created'] = $p['items_created'];
                    if (isset($p['items_updated'])) $groupedLogs[$matchedIdx]['payload']['items_updated'] = $p['items_updated'];
                    if (isset($p['items_deleted'])) $groupedLogs[$matchedIdx]['payload']['items_deleted'] = $p['items_deleted'];
                } else {
                    $groupedLogs[] = [
                        'action' => 'update',
                        'performed_at' => $log->performed_at,
                        'performed_by' => $log->performed_by,
                        'payload' => $p,
                    ];
                }
            }
        }

        // Reconstruct state of each revision forward starting from Rev 0
        $revisions = [];
        $currentState = [
            'subtotal' => floatval($invoice->subtotal),
            'gst_total' => floatval($invoice->gst_total),
            'grand_total' => floatval($invoice->grand_total),
            'currency' => $invoice->currency ?? 'INR',
            'items' => [],
        ];

        // Seed initial items from live invoice as baseline if needed
        foreach ($invoice->items as $it) {
            $key = $it->pii_id;
            $currentState['items'][$key] = [
                'pii_id' => $it->pii_id,
                'product_id' => $it->product_id,
                'item_name' => $it->product->prod_name ?? '-',
                'qty' => floatval($it->qty),
                'price' => floatval($it->unit_price),
                'gst_percent' => floatval($it->gst_percent),
                'gst_amount' => floatval($it->gst_amount),
                'line_total' => floatval($it->total),
            ];
        }

        $revCounter = 0;

        foreach ($groupedLogs as $g) {
            $p = $g['payload'];
            $curr = $p['currency'] ?? $p['after']['currency'] ?? $currentState['currency'] ?? 'INR';

            if ($g['action'] === 'create') {
                if (isset($p['subtotal'])) $currentState['subtotal'] = floatval($p['subtotal']);
                if (isset($p['gst_total'])) $currentState['gst_total'] = floatval($p['gst_total']);
                if (isset($p['grand_total'])) $currentState['grand_total'] = floatval($p['grand_total']);
                if (isset($p['currency'])) $currentState['currency'] = $curr;

                if (isset($p['items_created']) && !empty($p['items_created'])) {
                    $currentState['items'] = [];
                    foreach ($p['items_created'] as $it) {
                        $pName = DB::table('tbl_product')->where('product_id', $it['product_id'] ?? 0)->value('prod_name') ?? ('Item #' . ($it['product_id'] ?? ''));
                        $key = $it['pii_id'] ?? ('prod_' . ($it['product_id'] ?? rand()));
                        $currentState['items'][$key] = [
                            'pii_id' => $it['pii_id'] ?? null,
                            'product_id' => $it['product_id'] ?? null,
                            'item_name' => $pName,
                            'qty' => floatval($it['qty'] ?? 0),
                            'price' => floatval($it['unit_price'] ?? 0),
                            'gst_percent' => floatval($it['gst_percent'] ?? 0),
                            'gst_amount' => floatval($it['gst_amount'] ?? 0),
                            'line_total' => floatval($it['total'] ?? 0),
                        ];
                    }
                }

                $itemsList = array_values(array_map(function ($it) use ($formatCurrency, $curr) {
                    return [
                        'item_name' => $it['item_name'],
                        'qty' => number_format($it['qty'], 2),
                        'price' => $formatCurrency($it['price'], $curr),
                        'gst_percent' => number_format($it['gst_percent'], 2),
                        'gst_amount' => $formatCurrency($it['gst_amount'], $curr),
                        'line_total' => $formatCurrency($it['line_total'], $curr),
                    ];
                }, $currentState['items']));

                $revisions[] = [
                    'revision_no' => 0,
                    'date' => \Carbon\Carbon::parse($g['performed_at'])->format('d M Y, h:i A'),
                    'grand_total' => $formatCurrency($currentState['grand_total'], $curr),
                    'subtotal' => $formatCurrency($currentState['subtotal'], $curr),
                    'gst_total' => $formatCurrency($currentState['gst_total'], $curr),
                    'enc_id' => encrypt($invoice->invoice_id),
                    'items' => $itemsList,
                    'changes' => [],
                ];
                $revCounter++;

            } else if ($g['action'] === 'update') {
                $changes = [];

                if (isset($p['after']) && is_array($p['after'])) {
                    foreach ($p['after'] as $k => $nVal) {
                        $oVal = $p['before'][$k] ?? null;
                        if ($oVal == $nVal) continue;

                        if ($k === 'subtotal') $currentState['subtotal'] = floatval($nVal);
                        if ($k === 'gst_total') $currentState['gst_total'] = floatval($nVal);
                        if ($k === 'grand_total') $currentState['grand_total'] = floatval($nVal);
                        if ($k === 'currency') $currentState['currency'] = $nVal;

                        $label = ucwords(str_replace('_', ' ', $k));
                        if (in_array($k, ['grand_total', 'subtotal', 'gst_total', 'discount_total', 'advance_payment', 'balance_payment'])) {
                            $oldF = $formatCurrency($oVal, $curr);
                            $newF = $formatCurrency($nVal, $curr);
                        } elseif ($k === 'terms_payment') {
                            $oldF = $formatTerms($oVal);
                            $newF = $formatTerms($nVal);
                        } else {
                            $oldF = is_array($oVal) ? json_encode($oVal) : (string)($oVal ?? '-');
                            $newF = is_array($nVal) ? json_encode($nVal) : (string)($nVal ?? '-');
                        }
                        $changes[] = [
                            'field' => $label,
                            'old' => $oldF,
                            'new' => $newF,
                        ];
                    }
                }

                $hasItemDiffs = false;
                if (isset($p['items_created']) && !empty($p['items_created'])) {
                    $hasItemDiffs = true;
                    foreach ($p['items_created'] as $it) {
                        $pName = DB::table('tbl_product')->where('product_id', $it['product_id'] ?? 0)->value('prod_name') ?? ('Item #' . ($it['product_id'] ?? ''));
                        $key = $it['pii_id'] ?? ('prod_' . ($it['product_id'] ?? rand()));
                        $currentState['items'][$key] = [
                            'pii_id' => $it['pii_id'] ?? null,
                            'product_id' => $it['product_id'] ?? null,
                            'item_name' => $pName,
                            'qty' => floatval($it['qty'] ?? 0),
                            'price' => floatval($it['unit_price'] ?? 0),
                            'gst_percent' => floatval($it['gst_percent'] ?? 0),
                            'gst_amount' => floatval($it['gst_amount'] ?? 0),
                            'line_total' => floatval($it['total'] ?? 0),
                        ];
                    }
                }

                if (isset($p['items_updated']) && !empty($p['items_updated'])) {
                    foreach ($p['items_updated'] as $it) {
                        $key = $it['pii_id'] ?? null;
                        if ($key && isset($currentState['items'][$key])) {
                            $oldQty = $currentState['items'][$key]['qty'];
                            $oldPrice = $currentState['items'][$key]['price'];
                            $newQty = floatval($it['qty'] ?? 0);
                            $newPrice = floatval($it['unit_price'] ?? 0);
                            if ($oldQty !== $newQty || $oldPrice !== $newPrice) {
                                $hasItemDiffs = true;
                            }

                            $currentState['items'][$key]['qty'] = $newQty;
                            $currentState['items'][$key]['price'] = $newPrice;
                            $currentState['items'][$key]['gst_percent'] = floatval($it['gst_percent'] ?? 0);
                            $currentState['items'][$key]['gst_amount'] = floatval($it['gst_amount'] ?? 0);
                            $currentState['items'][$key]['line_total'] = floatval($it['total'] ?? 0);
                        }
                    }
                }

                if (isset($p['items_deleted']) && !empty($p['items_deleted'])) {
                    $hasItemDiffs = true;
                    foreach ($p['items_deleted'] as $it) {
                        $key = is_array($it) ? ($it['pii_id'] ?? null) : $it;
                        if ($key && isset($currentState['items'][$key])) {
                            unset($currentState['items'][$key]);
                        }
                    }
                }

                if ($hasItemDiffs) {
                    $itemMsgs = [];
                    if (!empty($p['items_created'])) $itemMsgs[] = count($p['items_created']) . ' Item(s) Added';
                    if (!empty($p['items_updated'])) $itemMsgs[] = count($p['items_updated']) . ' Item(s) Modified';
                    if (!empty($p['items_deleted'])) $itemMsgs[] = count($p['items_deleted']) . ' Item(s) Removed';
                    if (!empty($itemMsgs)) {
                        $changes[] = [
                            'field' => 'Items Updated',
                            'old' => '-',
                            'new' => implode(', ', $itemMsgs),
                        ];
                    }
                }

                if (empty($changes)) {
                    continue;
                }

                $itemsList = array_values(array_map(function ($it) use ($formatCurrency, $curr) {
                    return [
                        'item_name' => $it['item_name'],
                        'qty' => number_format($it['qty'], 2),
                        'price' => $formatCurrency($it['price'], $curr),
                        'gst_percent' => number_format($it['gst_percent'], 2),
                        'gst_amount' => $formatCurrency($it['gst_amount'], $curr),
                        'line_total' => $formatCurrency($it['line_total'], $curr),
                    ];
                }, $currentState['items']));

                $revisions[] = [
                    'revision_no' => $revCounter++,
                    'date' => \Carbon\Carbon::parse($g['performed_at'])->format('d M Y, h:i A'),
                    'grand_total' => $formatCurrency($currentState['grand_total'], $curr),
                    'subtotal' => $formatCurrency($currentState['subtotal'], $curr),
                    'gst_total' => $formatCurrency($currentState['gst_total'], $curr),
                    'enc_id' => encrypt($invoice->invoice_id),
                    'items' => $itemsList,
                    'changes' => $changes,
                ];
            }
        }

        // Sort descending so latest revision is at the top
        usort($revisions, function ($a, $b) {
            return $b['revision_no'] - $a['revision_no'];
        });

        return response()->json([
            'status' => true,
            'invoice_no' => $invoice->invoice_no,
            'data' => $revisions
        ]);
    }
}
