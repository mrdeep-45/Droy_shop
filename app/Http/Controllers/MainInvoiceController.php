<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GstModel;
use App\Models\MainInvoice;
use App\Models\MainInvoiceItem;
use App\Models\PerformaInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MainInvoiceController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX – load the page
    // ─────────────────────────────────────────────
    public function index()
    {
        $page_name  = 'Main Invoice';
        $invoice_no = MainInvoice::getInvoiceNo();

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

        return view('company.invoice.main.index', compact(
            'page_name',
            'invoice_no',
            'gsts',
            'staffs',
            'terms',
            'categories',
            'products'
        ));
    }

    // ─────────────────────────────────────────────
    // DATATABLE LIST
    // ─────────────────────────────────────────────
    public function list(Request $request)
    {
        $query = MainInvoice::with('customer')
            ->where('status', MainInvoice::ACTIVE)
            ->orderByDesc('main_invoice_id');

        return DataTables::of($query)
         ->addIndexColumn()   // This generates DT_RowIndex
            ->addColumn('invoice_no', function ($inv) {
                return '<span class="badge bg-primary-transparent text-primary fw-semibold">' . e($inv->invoice_no) . '</span>';
            })
            ->addColumn('customer', function ($inv) {
                return e($inv->customer->company_name ?? '-');
            })
            ->addColumn('date', function ($inv) {
                return $inv->invoice_date ? $inv->invoice_date->format('d-m-Y') : '-';
            })
            ->addColumn('total', function ($inv) {
                $prefix = $inv->currency !== 'INR' ? $inv->currency . ' ' : '₹';
                $html   = '<div class="fw-semibold">' . $prefix . number_format($inv->subtotal, 2) . '</div>';
                if ($inv->gst_total > 0) {
                    $html .= '<br>GST : ' . $prefix . number_format($inv->gst_total, 2);
                }
                $html .= '<br><div class="fw-bold text-primary">' . $prefix . number_format($inv->grand_total, 2) . '</div>';
                return $html;
            })
            ->addColumn('advance', function ($inv) {
                $prefix   = $inv->currency !== 'INR' ? $inv->currency . ' ' : '₹';
                $balance  = max(0, $inv->grand_total - $inv->advance_payment);
                return '<small class="text-success fw-bold">Adv: ' . $prefix . number_format($inv->advance_payment, 2) . '</small>'
                    . '<br><small class="text-danger fw-bold">Bal: ' . $prefix . number_format($balance, 2) . '</small>';
            })
            ->addColumn('action', function ($inv) {
                $html  = '<button class="btn btn-sm btn-info text-white m-1 view-invoice" data-id="' . $inv->main_invoice_id . '" title="View Details"><i class="fa-solid fa-eye"></i></button>';
                if (permissions_check('canUpdate')) {
                    $html .= '<button class="btn btn-sm btn-primary m-1 edit-invoice" data-id="' . $inv->main_invoice_id . '" title="Edit Invoice"><i class="fa-solid fa-pen-to-square"></i></button>';
                }
                $html .= '<a href="' . route('main_invoice.pdf', encrypt($inv->main_invoice_id)) . '" target="_blank" class="btn btn-sm btn-danger m-1 pdf-invoice" title="Download PDF"><i class="fa-solid fa-file-pdf"></i></a>';
                $defaultMsg = "Dear " . e($inv->customer->company_name ?? '') . ",\n\nPlease find our tax invoice details attached.\n\nBest regards,\n" . get_company_details(1)->name;
                $html .= '<button class="btn btn-sm btn-success m-1 invoice-mail" data-id="' . $inv->main_invoice_id . '" data-email="' . e($inv->email) . '" data-message="' . e($defaultMsg) . '" title="Share via Email"><i class="fa-solid fa-envelope"></i></button>';
                return $html;
            })
            ->rawColumns(['invoice_no', 'customer', 'total', 'advance', 'action'])
            ->make(true);
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'            => 'required|integer|exists:tbl_customer,customer_id',
            'invoice_date'           => 'required|date_format:d-m-Y',
            'invoice_type'           => 'required|in:domestic,export,Domestic,Export',
            'currency'               => 'required|string|max:10',
            'exchange_rate'          => 'required|numeric|min:0',
            'email'                  => 'required|email',
            'phone'                  => ['required', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
            'country_id'             => 'required|integer',
            'state_id'               => 'required|integer',
            'city_id'                => 'required|integer',
            'advance_payment'        => 'nullable|numeric|min:0',
            'balance_payment'        => 'nullable|numeric|min:0',
            'terms_payment'          => 'nullable|string|max:1000',
            'dispatch_time'          => 'nullable|string|max:255',
            'transportation'         => 'nullable|string|max:255',
            'commercial_terms'       => 'nullable|string|max:1000',
            'assigned_to'            => 'nullable|integer',
            'gst_exemption'          => 'required|in:0,1',
            'include_gst'            => 'nullable|integer|in:0,1',
            'items'                  => 'required|string',
        ]);

        $itemsRaw = json_decode($request->input('items'), true);
        if (!$itemsRaw || !is_array($itemsRaw) || count($itemsRaw) === 0) {
            return response()->json(['status' => false, 'message' => 'Please add at least one product.'], 422);
        }

        DB::beginTransaction();
        try {
            $gstExemption  = (int) $request->input('gst_exemption');
            $includeGst    = (int) $request->input('include_gst', 1); // Main invoice always has GST option
            $exchangeRate  = (float) $request->input('exchange_rate', 1.0);
            $totals        = $this->calculateTotals($itemsRaw, $gstExemption, $includeGst);
            $grandTotal    = $totals['grand_total'];
            $advancePayment = (float) $request->input('advance_payment', 0);
            $balancePayment = max(0, $grandTotal - $advancePayment);

            $invoice = MainInvoice::create([
                'invoice_no'            => MainInvoice::getInvoiceNo(),
                'invoice_type'          => strtolower($request->input('invoice_type', 'domestic')),
                'customer_id'           => $request->input('customer_id'),
                'quotation_id'          => $request->input('quotation_id') ?: null,
                'performa_invoice_id'   => $request->input('performa_invoice_id') ?: null,
                'assigned_to'           => $request->input('assigned_to') ?: null,
                'invoice_date'          => \Carbon\Carbon::createFromFormat('d-m-Y', $request->input('invoice_date'))->toDateString(),
                'advance_payment'       => $advancePayment,
                'balance_payment'       => $balancePayment,
                'terms_payment'         => $request->input('terms_payment'),
                'transportation'        => $request->input('transportation'),
                'dispatch_time'         => $request->input('dispatch_time'),
                'commercial_terms'      => $request->input('commercial_terms'),
                'gst_exemption'         => $gstExemption,
                'include_gst'           => $includeGst,
                'email'                 => $request->input('email'),
                'phone'                 => $request->input('phone'),
                'country_id'            => $request->input('country_id'),
                'state_id'              => $request->input('state_id'),
                'city_id'               => $request->input('city_id'),
                'currency'              => $request->input('currency', 'INR'),
                'exchange_rate'         => $exchangeRate,
                'subtotal'              => $totals['subtotal'],
                'discount_total'        => $totals['discount_total'],
                'gst_total'             => $totals['gst_total'],
                'grand_total'           => $grandTotal,
                'grand_total_converted' => $grandTotal * $exchangeRate,
                'status'                => MainInvoice::ACTIVE,
                'created_by'            => getCreatedBy(),
                'updated_by'            => getCreatedBy(),
            ]);

            if ($request->input('customer_id')) {
                Customer::where('customer_id', $request->input('customer_id'))->update(array_filter([
                    'phone' => $request->input('phone'),
                    'email' => $request->input('email'),
                ]));
            }

            $this->syncItems($invoice->main_invoice_id, $itemsRaw, $gstExemption, $includeGst, $exchangeRate);

            // Mark performa invoice as converted to main if applicable
            if ($invoice->performa_invoice_id) {
                DB::table('tbl_performa_invoice')
                    ->where('invoice_id', $invoice->performa_invoice_id)
                    ->update(['converted_to_main' => 1]);
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Main Invoice created successfully.', 'invoice_no' => $invoice->invoice_no]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────
    // EDIT – load invoice data for form
    // ─────────────────────────────────────────────
    public function edit($id)
    {
        $invoice = MainInvoice::with(['items', 'customer', 'performaInvoice'])->findOrFail($id);
        $items   = $invoice->items->map(fn($item) => [
            'product_id'          => $item->product_id,
            'product_name'        => optional(DB::table('tbl_product')->where('product_id', $item->product_id)->first())->prod_name ?? '-',
            'description'         => $item->description,
            'qty'                 => (float) $item->qty,
            'unit_price'          => (float) $item->unit_price,
            'gst_percent'         => (float) $item->gst_percent,
            'gst_amount'          => (float) $item->gst_amount,
            'discount_percentage' => (float) $item->discount_percentage,
            'discount_price'      => (float) $item->discount_price,
            'total'               => (float) $item->total,
        ])->values();

        $city = DB::table('mst_city')->where('city_id', $invoice->city_id)->first();

        return response()->json([
            'status'  => true,
            'invoice' => [
                'main_invoice_id'       => $invoice->main_invoice_id,
                'invoice_no'            => $invoice->invoice_no,
                'invoice_type'          => $invoice->invoice_type,
                'customer_id'           => $invoice->customer_id,
                'customer_name'         => $invoice->customer->company_name ?? '',
                'quotation_id'          => $invoice->quotation_id,
                'performa_invoice_id'   => $invoice->performa_invoice_id,
                'performa_invoice_no'   => $invoice->performaInvoice->invoice_no ?? '',
                'performa_grand_total'  => $invoice->performaInvoice ? (float) $invoice->performaInvoice->grand_total : 0,
                'performa_advance'      => $invoice->performaInvoice ? (float) $invoice->performaInvoice->advance_payment : 0,
                'performa_balance'      => $invoice->performaInvoice ? (float) $invoice->performaInvoice->balance_payment : 0,
                'created_by'            => $invoice->created_by,
                'created_by_name'       => DB::table('users')->where('id', $invoice->created_by)->value('name') ?? '',
                'assigned_to'           => $invoice->assigned_to,
                'assigned_to_name'      => DB::table('mst_staff')->where('staff_id', $invoice->assigned_to)->value('staff_name') ?? '',
                'invoice_date'          => $invoice->invoice_date?->format('d-m-Y'),
                'advance_payment'       => (float) $invoice->advance_payment,
                'balance_payment'       => (float) $invoice->balance_payment,
                'terms_payment'         => $invoice->terms_payment,
                'transportation'        => $invoice->transportation,
                'dispatch_time'         => $invoice->dispatch_time,
                'commercial_terms'      => $invoice->commercial_terms,
                'gst_exemption'         => $invoice->gst_exemption,
                'include_gst'           => $invoice->include_gst,
                'email'                 => $invoice->email,
                'phone'                 => $invoice->phone,
                'city_id'               => $invoice->city_id,
                'city_name'             => $city->city_name ?? '',
                'state_id'              => $invoice->state_id,
                'state_name'            => DB::table('mst_state')->where('state_id', $invoice->state_id)->value('state_name') ?? '',
                'country_id'            => $invoice->country_id,
                'country_name'          => DB::table('mst_country')->where('c_id', $invoice->country_id)->value('country_name') ?? '',
                'currency'              => $invoice->currency,
                'exchange_rate'         => (float) $invoice->exchange_rate,
                'subtotal'              => (float) $invoice->subtotal,
                'discount_total'        => (float) $invoice->discount_total,
                'gst_total'             => (float) $invoice->gst_total,
                'grand_total'           => (float) $invoice->grand_total,
            ],
            'items'   => $items,
        ]);
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $invoice = MainInvoice::findOrFail($id);
        $request->validate([
            'customer_id'   => 'required|integer|exists:tbl_customer,customer_id',
            'invoice_date'  => 'required|date_format:d-m-Y',
            'invoice_type'  => 'required|in:domestic,export,Domestic,Export',
            'currency'      => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'email'         => 'required|email',
            'phone'         => ['required', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
            'country_id'    => 'required|integer',
            'state_id'      => 'required|integer',
            'city_id'       => 'required|integer',
            'gst_exemption' => 'required|in:0,1',
            'include_gst'   => 'nullable|integer|in:0,1',
            'items'         => 'required|string',
        ]);

        $itemsRaw     = json_decode($request->input('items'), true);
        if (!$itemsRaw || !is_array($itemsRaw) || count($itemsRaw) === 0) {
            return response()->json(['status' => false, 'message' => 'Please add at least one product.'], 422);
        }

        DB::beginTransaction();
        try {
            $gstExemption   = (int) $request->input('gst_exemption');
            $includeGst     = (int) $request->input('include_gst', 1);
            $exchangeRate   = (float) $request->input('exchange_rate', 1.0);
            $totals         = $this->calculateTotals($itemsRaw, $gstExemption, $includeGst);
            $grandTotal     = $totals['grand_total'];
            $advancePayment = (float) $request->input('advance_payment', 0);
            $balancePayment = max(0, $grandTotal - $advancePayment);

            $invoice->update([
                'invoice_type'          => strtolower($request->input('invoice_type', 'domestic')),
                'customer_id'           => $request->input('customer_id'),
                'assigned_to'           => $request->input('assigned_to') ?: null,
                'invoice_date'          => \Carbon\Carbon::createFromFormat('d-m-Y', $request->input('invoice_date'))->toDateString(),
                'advance_payment'       => $advancePayment,
                'balance_payment'       => $balancePayment,
                'terms_payment'         => $request->input('terms_payment'),
                'transportation'        => $request->input('transportation'),
                'dispatch_time'         => $request->input('dispatch_time'),
                'commercial_terms'      => $request->input('commercial_terms'),
                'gst_exemption'         => $gstExemption,
                'include_gst'           => $includeGst,
                'email'                 => $request->input('email'),
                'phone'                 => $request->input('phone'),
                'country_id'            => $request->input('country_id'),
                'state_id'              => $request->input('state_id'),
                'city_id'               => $request->input('city_id'),
                'currency'              => $request->input('currency', 'INR'),
                'exchange_rate'         => $exchangeRate,
                'subtotal'              => $totals['subtotal'],
                'discount_total'        => $totals['discount_total'],
                'gst_total'             => $totals['gst_total'],
                'grand_total'           => $grandTotal,
                'grand_total_converted' => $grandTotal * $exchangeRate,
                'updated_by'            => getCreatedBy(),
            ]);

            if ($request->input('customer_id')) {
                Customer::where('customer_id', $request->input('customer_id'))->update(array_filter([
                    'phone' => $request->input('phone'),
                    'email' => $request->input('email'),
                ]));
            }

            // Delete old items and re-sync
            MainInvoiceItem::where('main_invoice_id', $invoice->main_invoice_id)->delete();
            $this->syncItems($invoice->main_invoice_id, $itemsRaw, $gstExemption, $includeGst, $exchangeRate);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Main Invoice updated successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────
    // GET PERFORMA INVOICES FOR DROPDOWN (by customer)
    // ─────────────────────────────────────────────
    public function getPerformaInvoices(Request $request)
    {
        $customerId = $request->input('customer_id');
        $search     = $request->input('search');

        // Exclude performa invoices already converted to main invoice
        $alreadyConverted = DB::table('tbl_performa_invoice')
            ->where('converted_to_main', 1)
            ->pluck('invoice_id')
            ->toArray();

        $query = PerformaInvoice::where('customer_id', $customerId)
            ->where('status', PerformaInvoice::ACTIVE)
            ->whereNotIn('invoice_id', $alreadyConverted)
            ->orderByDesc('invoice_id');

        if (!empty($search)) {
            $query->where('invoice_no', 'like', "%{$search}%");
        }

        $invoices = $query->limit(50)->get(['invoice_id', 'invoice_no', 'grand_total', 'advance_payment']);

        $results = $invoices->map(function ($inv) {
            $balance = max(0, $inv->grand_total - $inv->advance_payment);
            return [
                'id'   => $inv->invoice_id,
                'text' => $inv->invoice_no
                    . ' — Total: ₹' . number_format($inv->grand_total, 2)
                    . ' | Adv: ₹' . number_format($inv->advance_payment, 2)
                    . ' | Bal: ₹' . number_format($balance, 2),
                'grand_total'     => (float) $inv->grand_total,
                'advance_payment' => (float) $inv->advance_payment,
                'balance_payment' => (float) $balance,
            ];
        });

        return response()->json(['results' => $results]);
    }

    // ─────────────────────────────────────────────
    // GET PERFORMA INVOICE DETAILS (items + header)
    // ─────────────────────────────────────────────
    public function getPerformaInvoiceItems($id)
    {
        $invoice = PerformaInvoice::with(['items.product', 'customer'])->find($id);
        if (!$invoice) {
            return response()->json(['status' => false, 'message' => 'Performa Invoice not found.'], 404);
        }

        $city = DB::table('mst_city')->where('city_id', $invoice->city_id)->first();

        $items = $invoice->items->map(function ($item) {
            $gstPrc = (float) $item->gst_percent;
            if ($gstPrc <= 0) {
                $prodGst = DB::table('tbl_product')
                    ->leftJoin('mst_gst', 'tbl_product.gst', '=', 'mst_gst.gst_id')
                    ->where('tbl_product.product_id', $item->product_id)
                    ->value('mst_gst.gst_no');
                $gstPrc = (float) ($prodGst ?? 0);
            }
            return [
                'product_id'          => $item->product_id,
                'product_name'        => $item->product->prod_name ?? '-',
                'description'         => $item->description,
                'qty'                 => (float) $item->qty,
                'unit_price'          => (float) $item->unit_price,
                'gst_percent'         => $gstPrc,
                'gst_amount'          => (float) $item->gst_amount,
                'discount_percentage' => (float) $item->discount_percentage,
                'discount_price'      => (float) $item->discount_price,
                'total'               => (float) $item->total,
            ];
        })->values();

        return response()->json([
            'status' => true,
            'items'  => $items,
            'performa' => [
                'invoice_no'      => $invoice->invoice_no,
                'invoice_type'    => $invoice->invoice_type,
                'currency'        => $invoice->currency,
                'exchange_rate'   => (float) $invoice->exchange_rate,
                'assigned_to'     => $invoice->assigned_to,
                'grand_total'     => (float) $invoice->grand_total,
                'advance_payment' => (float) $invoice->advance_payment,
                'balance_payment' => max(0, $invoice->grand_total - $invoice->advance_payment),
                'terms_payment'   => $invoice->terms_payment,
                'city_id'         => $invoice->city_id,
                'city_name'       => $city->city_name ?? '',
                'state_id'        => $invoice->state_id,
                'state_name'      => DB::table('mst_state')->where('state_id', $invoice->state_id)->value('state_name') ?? '',
                'country_id'      => $invoice->country_id,
                'country_name'    => DB::table('mst_country')->where('c_id', $invoice->country_id)->value('country_name') ?? '',
                'email'           => $invoice->email,
                'phone'           => $invoice->phone,
                'gst_exemption'   => $invoice->gst_exemption,
                'include_gst'     => $invoice->include_gst,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // PDF
    // ─────────────────────────────────────────────
    public function pdf($id)
    {
        $invoiceId = decrypt($id);
        $invoice   = MainInvoice::with(['items.product', 'customer'])->findOrFail($invoiceId);
        $company   = get_company_details(1);
        $data      = ['invoice' => $invoice, 'company' => $company, 'date' => now()->format('d M Y')];
        $pdf       = \Barryvdh\DomPDF\Facade\Pdf::loadView('company.pdf.main_invoice', $data)->setPaper('A4');
        return $pdf->stream("Invoice-{$invoice->invoice_no}.pdf");
    }

    // ─────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────
    private function calculateTotals(array $items, int $gstExemption, int $includeGst = 1): array
    {
        $subtotal      = 0;
        $gstTotal      = 0;
        $discountTotal = 0;

        foreach ($items as $item) {
            $qty           = (float) ($item['qty'] ?? 0);
            $unitPrice     = (float) ($item['unit_price'] ?? 0);
            $discountPrice = (float) ($item['discount_price'] ?? 0);
            $discountTotal += ($qty * $discountPrice);
            $discountedPrice = max(0, $unitPrice - $discountPrice);
            $lineTotal       = $qty * $discountedPrice;
            $subtotal       += $lineTotal;

            if ($includeGst && $gstExemption == MainInvoice::GST_NOT_EXEMPTED) {
                $gstPercent = (float) ($item['gst_percent'] ?? 0);
                $gstTotal  += $lineTotal * ($gstPercent / 100);
            }
        }

        return [
            'subtotal'       => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'gst_total'      => round($gstTotal, 2),
            'grand_total'    => round($subtotal + $gstTotal, 2),
        ];
    }

    private function syncItems(int $invoiceId, array $itemsRaw, int $gstExemption, int $includeGst = 1, float $exchangeRate = 1.0): void
    {
        foreach ($itemsRaw as $itemData) {
            $qty           = (float) ($itemData['qty'] ?? 0);
            $unitPrice     = (float) ($itemData['unit_price'] ?? 0);
            $discountPrice = (float) ($itemData['discount_price'] ?? 0);
            $discountedPrice = max(0, $unitPrice - $discountPrice);
            $lineTotal       = $qty * $discountedPrice;
            $gstPercent      = 0;
            $gstAmount       = 0;

            if ($includeGst && $gstExemption == MainInvoice::GST_NOT_EXEMPTED) {
                $gstPercent = (float) ($itemData['gst_percent'] ?? 0);
                $gstAmount  = round($lineTotal * ($gstPercent / 100), 2);
            }

            $total = round($lineTotal + $gstAmount, 2);

            MainInvoiceItem::create([
                'main_invoice_id'      => $invoiceId,
                'product_id'           => $itemData['product_id'],
                'description'          => $itemData['description'] ?? null,
                'qty'                  => $qty,
                'unit_price'           => $unitPrice,
                'unit_price_converted' => $unitPrice * $exchangeRate,
                'discount_percentage'  => (float) ($itemData['discount_percentage'] ?? 0),
                'discount_price'       => $discountPrice,
                'gst_percent'          => $gstPercent,
                'gst_amount'           => $gstAmount,
                'total'                => $total,
                'total_converted'      => $total * $exchangeRate,
            ]);
        }
    }

    public function sendEmail(Request $request, $id)
    {
        $request->validate([
            'emails'   => ['required', 'array', 'min:1'],
            'emails.*' => ['required', 'email'],
            'message'  => ['required', 'string'],
        ]);

        $invoice = MainInvoice::findOrFail($id);
        $sent    = sendMainInvoiceOnEmail(
            $request->emails,
            $invoice->main_invoice_id,
            $request->input('message')
        );

        if ($sent) {
            return response()->json(['status' => true, 'message' => 'Email sent successfully.']);
        }
        return response()->json(['status' => false, 'message' => 'Failed to send email.']);
    }
}
