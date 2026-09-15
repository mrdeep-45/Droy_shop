<?php

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\CityModel;
use App\Models\StateModel;
use App\Models\CountryModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

function prepareDocumentData(int $quotationId): object
{
    $quotation = Quotation::with([
        'items.product.category',
        'items.product.unit',
        'lead.city',
        'lead.state',
        'lead.country',
        'deal',
        'customer',
        'revisions',
    ])->findOrFail($quotationId);

    $items = $quotation->items->map(function ($item) {
        $discountedPrice = max(0, $item->unit_price - $item->discount_price);
        $baseAmount = $discountedPrice * $item->qty;

        return (object) [
            'quotation_item_id' => $item->quotation_item_id,
            'product_id' => $item->product_id,
            'category_id' => $item->product->category_id ?? null,
            'category_name' => $item->product->category->name ?? 'General',
            'unit_name' => $item->product->unit->unit ?? '',
            'item_name' => $item->product->prod_name ?? '-',
            'item_code' => $item->product->prod_code ?? '-',
            'hsn_code' => $item->product->hsn_code ?? '',
            'item_description' => $item->description ?? null,
            'qty' => $item->qty,
            'price' => $item->unit_price,
            'unit_price_converted' => $item->unit_price_converted,
            'gst_percent' => $item->gst_percent,
            'gst_amount' => $item->gst_amount,
            'discount_percentage' => $item->discount_percentage,
            'discount_price' => $item->discount_price,
            'base_amount' => $baseAmount,
            'line_total' => $item->total,
            'total_converted' => $item->total_converted,
        ];
    });

    $attachments = [];
    if (!empty($quotation->attachments)) {
        $attachments = array_map(function ($file) {
            return (object) [
                'filename' => $file,
                'url' => asset('assets/uploads/quotations/attachments/' . $file),
                'is_image' => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif']),
            ];
        }, explode(',', $quotation->attachments));
    }

    $revisionHistory = $quotation->revisions->map(function ($rev) {
        return (object) [
            'quotation_id' => $rev->quotation_id,
            'quotation_no' => $rev->quotation_no,
            'revision_no' => $rev->revision_no,
            'quotation_date' => $rev->quotation_date,
            'grand_total' => $rev->grand_total,
        ];
    });

    $lead = $quotation->lead;
    $deal = $quotation->deal;
    $customer = $quotation->customer;

    $city = CityModel::where('city_id', $quotation->city_id)->value('city_name');
    $state = StateModel::where('state_id', $quotation->state_id)->value('state_name');
    $country = CountryModel::where('c_id', $quotation->country_id)->value('country_name');

    $subtotal = $items->sum('base_amount');

    return (object) [
        'quotation_id' => $quotation->quotation_id,
        'state_id' => $quotation->state_id,
        'quotation_no' => $quotation->quotation_no,
        'revision_no' => $quotation->revised_count,
        'is_revised' => $quotation->is_revised,
        'is_original' => $quotation->is_revised == Quotation::ORIGINAL,
        'quotation_type' => $quotation->quotation_type,
        'quotation_date' => \Carbon\Carbon::parse($quotation->quotation_date)->format('d M Y'),
        'quotation_date_raw' => $quotation->quotation_date,

        'customer_name' => $quotation->customer_name,
        'company_name' => $quotation->company_name,
        'contact_person_name' => $quotation->contact_person_name,
        'email' => $quotation->email,
        'phone' => $quotation->phone,
        'gst_no' => $quotation->gst_no,

        'city' => $city,
        'state' => $state,
        'country' => $country,

        'subject' => $quotation->subject,
        'message' => $quotation->message,
        'remarks' => $quotation->remarks,
        'terms_conditions' => $quotation->terms,

        'subtotal' => $subtotal,
        'installation_total' => 0,
        'gst_total' => $quotation->gst_total,
        'discount_total' => $quotation->discount_total,
        'grand_total' => $quotation->grand_total,
        'currency' => $quotation->currency,
        'exchange_rate' => $quotation->exchange_rate,
        'grand_total_converted' => $quotation->grand_total_converted,

        'items' => $items,
        'items_count' => $items->count(),
        'attachments' => $attachments,

        'lead_id' => $quotation->lead_id,
        'lead_no' => $lead->lead_no ?? null,
        'lead_name' => $lead->lead_name ?? null,

        'deal_id' => $quotation->deal_id,
        'deal_name' => $deal->deal_name ?? null,

        'customer_id' => $quotation->customer_id,
        'customer_no' => $customer->customer_no ?? null,

        'parent_quotation_no' => $quotation->quotation_no ?? null,
        'revision_history' => $revisionHistory,

        'created_at' => \Carbon\Carbon::parse($quotation->created_at)->format('d M Y h:i A'),
        'created_by' => $quotation->created_by,
        'gst_exemption' => $quotation->gst_exemption,
    ];
}



if (!function_exists('sendQuotOnEmail')) {

    function sendQuotOnEmail($to, $subject, $quotId, $message = null, $cc = [])
    {
        if (is_string($to) && str_contains($to, ',')) {
            $to = array_map('trim', explode(',', $to));
        }
        $view = "company.email.quotation";
        $document = prepareDocumentData($quotId);
        $pdfUrl = route('quotation.pdf', encrypt($quotId));
        $data = [
            'document' => $document,
            'date' => $document->quotation_date,
            'company' => get_company_details(1),
            'pdfUrl' => $pdfUrl
        ];
        $company = get_company_details(1)->name;

        $subject = $document->subject . " | Quotation No: {$document->quotation_no} | {$company}";


        $pdfData = [
            'document' => $document,
            'date' => now()->format('d M Y'),
            'company' => get_company_details(1)
        ];

        $pdfData['document']->message = $message ?? $document->message;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isPhpEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('company.pdf.quotation', $pdfData)
            ->setPaper('A4');
        $revSuffix = ((int) ($document->revision_no ?? 0) > 0) ? "-R{$document->revision_no}" : '';
        $pdfAttachment = [
            'data' => $pdf->output(),
            'name' => "Quotation-{$document->quotation_no}-{$document->customer_name}{$revSuffix}.pdf"
        ];


        $attachments = [];
        if (!empty($document->attachments)) {
            foreach ($document->attachments as $attachment) {
                $filePath = public_path('assets/uploads/quotations/attachments/' . $attachment->filename);
                if (file_exists($filePath)) {
                    $attachments[] = $filePath;
                }
            }
        }

        return sendEmail($to, $subject, $view, $data, $cc, $attachments, $pdfAttachment);
    }
}

if (!function_exists('sendEmail')) {

    function sendEmail($to, $subject, $view, $data = [], $cc = [], $attachments = [], $pdfAttachment = null, $bcc = [])
    {
        try {
             $to = is_array($to) ? $to : (empty($to) ? [] : array_map('trim', explode(',', $to)));
            $cc = is_array($cc) ? $cc : (empty($cc) ? [] : array_map('trim', explode(',', $cc)));
            $bcc = is_array($bcc) ? $bcc : (empty($bcc) ? [] : array_map('trim', explode(',', $bcc)));
            $attachments = is_array($attachments) ? $attachments : (empty($attachments) ? [] : [$attachments]);

            $to = array_values(array_filter($to, function ($email) {
                return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
            }));

            $cc = array_values(array_filter($cc, function ($email) {
                return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
            }));

            $bcc = array_values(array_filter($bcc, function ($email) {
                return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
            }));

            Mail::send($view, $data, function ($message) use ($to, $subject, $cc, $bcc, $attachments, $pdfAttachment) {

                $message->to($to);
                $message->subject($subject);

                if (!empty($cc)) {
                    $message->cc($cc);
                }

                 if (!empty($bcc)) {
                    $message->bcc($bcc);
                }

                // Attach files from paths

                if (!empty($attachments)) {
                    foreach ($attachments as $item) {
                        if (is_array($item) && isset($item['path']) && file_exists($item['path'])) {
                            $options = [];
                            if (!empty($item['name'])) $options['as'] = $item['name'];
                            if (!empty($item['mime'])) $options['mime'] = $item['mime'];
                            $message->attach($item['path'], $options);
                        } else if (is_string($item) && file_exists($item)) {
                            $message->attach($item);
                        }
                    }
                }

                // Attach PDF file from raw data if present
                if (!empty($pdfAttachment) && isset($pdfAttachment['data']) && isset($pdfAttachment['name'])) {
                    $message->attachData($pdfAttachment['data'], $pdfAttachment['name'], [
                        'mime' => 'application/pdf',
                    ]);
                }
            });

            return true;
        } catch (\Throwable $th) {
            Log::error('Email send failed: ' . $th->getMessage());
            return false;
        }
    }
}

if (!function_exists('sendComposeEmail')) {
    function sendComposeEmail($to, $subject, $bodyMessage, $cc = [], $bcc = [], $attachments = [])
    {
        $company = get_company_details(1);
        $view = "company.email.compose";
        $data = [
            'bodyMessage' => $bodyMessage,
            'subject' => $subject,
            'company' => $company,
            'date' => now()->format('d M Y h:i A')
        ];
        return sendEmail($to, $subject, $view, $data, $cc, $attachments, null, $bcc);
    }
}

function preparePerformaInvoiceDocumentData(int $invoiceId): object
{
    $invoice = \App\Models\PerformaInvoice::with(['items.product', 'customer'])->findOrFail($invoiceId);
    $items = $invoice->items->map(function ($item) {
        $baseAmount = floatval($item->unit_price) * floatval($item->qty);
        return (object) [
            'product_id' => $item->product_id,
            'item_name' => $item->product->prod_name ?? '-',
            'item_code' => $item->product->prod_code ?? '-',
            'hsn_code' => $item->product->hsn_code ?? '',
            'item_description' => $item->description ?? null,
            'qty' => floatval($item->qty),
            'price' => floatval($item->unit_price),
            'unit_price_converted' => floatval($item->unit_price_converted),
            'gst_percent' => floatval($item->gst_percent),
            'gst_amount' => floatval($item->gst_amount),
            'discount_percentage' => floatval($item->discount_percentage ?? 0.0),
            'discount_price' => floatval($item->discount_price ?? 0.0),
            'base_amount' => $baseAmount,
            'line_total' => floatval($item->total),
            'total_converted' => floatval($item->total_converted),
        ];
    });
    $city = DB::table('mst_city')->where('city_id', $invoice->city_id)->value('city_name');
    $state = DB::table('mst_state')->where('state_id', $invoice->state_id)->value('state_name');
    $country = DB::table('mst_country')->where('c_id', $invoice->country_id)->value('country_name');

    $termsText = '';
    $termsList = [];
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

            foreach ($dbTerms as $t) {
                $termsList[] = (object) [
                    'title' => $t->module ?: 'Terms & Conditions',
                    'terms' => $t->terms
                ];
            }

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
            $lines = explode("\n", str_replace("\r", "", $invoice->terms_payment));
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed === '') continue;
                if (preg_match('/^([^:\n]{1,40})\s*:\s*(.*)$/', $trimmed, $matches)) {
                    $termsList[] = (object) [
                        'title' => trim($matches[1]),
                        'terms' => trim($matches[2])
                    ];
                } else {
                    $termsList[] = (object) [
                        'title' => 'Terms & Conditions',
                        'terms' => ltrim($trimmed, '- ')
                    ];
                }
            }
        }
    }

    return (object) [
        'invoice_id' => $invoice->invoice_id,
        'state_id' => $invoice->state_id,
        'invoice_no' => $invoice->invoice_no,
        'invoice_date' => \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y'),
        'invoice_date_raw' => $invoice->invoice_date,
        'buyer_order_no' => $invoice->buyer_order_no,
        'buyer_order_date' => $invoice->buyer_order_date ? \Carbon\Carbon::parse($invoice->buyer_order_date)->format('d M Y') : '',
        'quotation_type' => $invoice->invoice_type,
        'customer_name' => $invoice->customer->company_name ?? '',
        'company_name' => $invoice->customer->company_name ?? '',
        'contact_person_name' => $invoice->customer->contact_person ?? '',
        'email' => $invoice->email,
        'phone' => $invoice->phone,
        'gst_no' => $invoice->customer->gst_no ?? '',
        'city' => $city,
        'state' => $state,
        'country' => $country,
        'subject' => 'Performa Invoice For ' . ($invoice->customer->company_name ?? ''),
        'message' => 'Dear ' . ($invoice->customer->company_name ?? '') . ",\n\nPlease find our performa invoice details below:\n\nBest regards,\n" . get_company_details(1)->name,
        'remarks' => '',
        'terms_conditions' => $termsText ?: '',
        'terms_list' => $termsList,
        'dispatch_time' => $invoice->dispatch_time,
        'delivery_note' => $invoice->delivery_note ?: $invoice->dispatch_time,
        'po_reference' => $invoice->po_reference,
        'transportation' => $invoice->transportation,
        'destination' => $invoice->destination,
        'advance_payment_percentage' => floatval($invoice->advance_payment_percentage),
        'advance_payment' => floatval($invoice->advance_payment),
        'balance_payment_percentage' => floatval($invoice->balance_payment_percentage),
        'balance_payment' => floatval($invoice->balance_payment),
        'subtotal' => floatval($invoice->subtotal),
        'gst_total' => floatval($invoice->gst_total),
        'discount_total' => floatval($invoice->discount_total ?? 0.0),
        'grand_total' => floatval($invoice->grand_total),
        'currency' => $invoice->currency ?? 'INR',
        'exchange_rate' => floatval($invoice->exchange_rate ?? 1.0),
        'grand_total_converted' => floatval($invoice->grand_total_converted ?? $invoice->grand_total),
        'items' => $items,
        'items_count' => $items->count(),
        'attachments' => $invoice->attachments ? explode(',', $invoice->attachments) : [],
        'created_at' => \Carbon\Carbon::parse($invoice->created_at)->format('d M Y h:i A'),
        'created_by' => $invoice->created_by,
        'gst_exemption' => $invoice->gst_exemption,
        'include_gst' => $invoice->include_gst,
    ];
}

function sendPerformaInvoiceOnEmail($to, $invoiceId, $message = null)
{
    $view = "company.email.performa";
    $document = preparePerformaInvoiceDocumentData($invoiceId);
    if ($message !== null) {
        $document->message = $message;
    }
    $pdfUrl = route('performa_invoice.pdf', encrypt($invoiceId));
    $data = [
        'document' => $document,
        'date' => $document->invoice_date,
        'company' => get_company_details(1),
        'pdfUrl' => $pdfUrl
    ];
    $companyName = get_company_details(1)->name;
    $subject = "Performa Invoice | Invoice No: {$document->invoice_no} | {$companyName}";
    $pdfData = [
        'document' => $document,
        'date' => now()->format('d M Y'),
        'company' => get_company_details(1)
    ];
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('company.pdf.performa', $pdfData)->setPaper('A4');
    $pdfAttachment = [
        'data' => $pdf->output(),
        'name' => "PerformaInvoice-{$document->invoice_no}.pdf"
    ];
    return sendEmail($to, $subject, $view, $data, [], [], $pdfAttachment);
}

function prepareMainInvoiceDocumentData($invoiceId)
{
    $invoice = \App\Models\MainInvoice::with(['items.product', 'customer'])->findOrFail($invoiceId);
    $items = $invoice->items->map(function ($item) {
        $discountPrice = floatval($item->discount_price ?? 0.0);
        $discountedUnitPrice = max(0, floatval($item->unit_price) - $discountPrice);
        $baseAmount = floatval($item->qty) * $discountedUnitPrice;
        return (object) [
            'item_id' => $item->mii_id,
            'product_name' => $item->product->prod_name ?? '-',
            'item_code' => $item->product->prod_code ?? '-',
            'hsn_code' => $item->product->hsn_code ?? '',
            'item_description' => $item->description ?? null,
            'qty' => floatval($item->qty),
            'price' => floatval($item->unit_price),
            'unit_price_converted' => floatval($item->unit_price_converted ?? $item->unit_price),
            'gst_percent' => floatval($item->gst_percent),
            'gst_amount' => floatval($item->gst_amount),
            'discount_percentage' => floatval($item->discount_percentage ?? 0.0),
            'discount_price' => floatval($item->discount_price ?? 0.0),
            'base_amount' => $baseAmount,
            'line_total' => floatval($item->total),
            'total_converted' => floatval($item->total_converted ?? $item->total),
        ];
    });
    $city = DB::table('mst_city')->where('city_id', $invoice->city_id)->value('city_name');
    $state = DB::table('mst_state')->where('state_id', $invoice->state_id)->value('state_name');
    $country = DB::table('mst_country')->where('c_id', $invoice->country_id)->value('country_name');
    return (object) [
        'main_invoice_id' => $invoice->main_invoice_id,
        'state_id' => $invoice->state_id,
        'invoice_no' => $invoice->invoice_no,
        'invoice_date' => \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y'),
        'invoice_date_raw' => $invoice->invoice_date,
        'quotation_type' => $invoice->invoice_type,
        'customer_name' => $invoice->customer->company_name ?? '',
        'company_name' => $invoice->customer->company_name ?? '',
        'contact_person_name' => $invoice->customer->contact_person ?? '',
        'email' => $invoice->email,
        'phone' => $invoice->phone,
        'gst_no' => $invoice->customer->gst_no ?? '',
        'city' => $city,
        'state' => $state,
        'country' => $country,
        'subject' => 'Tax Invoice For ' . ($invoice->customer->company_name ?? ''),
        'message' => 'Dear ' . ($invoice->customer->company_name ?? '') . ",\n\nPlease find our tax invoice details below:\n\nBest regards,\n" . get_company_details(1)->name,
        'remarks' => '',
        'terms_conditions' => $invoice->terms_payment,
        'dispatch_time' => $invoice->dispatch_time,
        'transportation' => $invoice->transportation,
        'commercial_terms' => $invoice->commercial_terms,
        'advance_payment' => floatval($invoice->advance_payment),
        'balance_payment' => floatval($invoice->balance_payment),
        'subtotal' => floatval($invoice->subtotal),
        'gst_total' => floatval($invoice->gst_total),
        'discount_total' => floatval($invoice->discount_total ?? 0.0),
        'grand_total' => floatval($invoice->grand_total),
        'currency' => $invoice->currency ?? 'INR',
        'exchange_rate' => floatval($invoice->exchange_rate ?? 1.0),
        'grand_total_converted' => floatval($invoice->grand_total_converted ?? $invoice->grand_total),
        'items' => $items,
        'items_count' => $items->count(),
        'attachments' => [],
        'created_at' => \Carbon\Carbon::parse($invoice->created_at)->format('d M Y h:i A'),
        'created_by' => $invoice->created_by,
        'gst_exemption' => $invoice->gst_exemption,
        'include_gst' => $invoice->include_gst,
    ];
}

function sendMainInvoiceOnEmail($to, $invoiceId, $message = null)
{
    $view = "company.email.main";
    $document = prepareMainInvoiceDocumentData($invoiceId);
    if ($message !== null) {
        $document->message = $message;
    }
    $pdfUrl = route('main_invoice.pdf', encrypt($invoiceId));
    $data = [
        'document' => $document,
        'date' => $document->invoice_date,
        'company' => get_company_details(1),
        'pdfUrl' => $pdfUrl
    ];
    $companyName = get_company_details(1)->name;
    $subject = "Tax Invoice | Invoice No: {$document->invoice_no} | {$companyName}";
    $pdfData = [
        'document' => $document,
        'date' => now()->format('d M Y'),
        'company' => get_company_details(1)
    ];
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('company.pdf.main_invoice', $pdfData)->setPaper('A4');
    $pdfAttachment = [
        'data' => $pdf->output(),
        'name' => "TaxInvoice-{$document->invoice_no}.pdf"
    ];
    return sendEmail($to, $subject, $view, $data, [], [], $pdfAttachment);
}
