@php
    $logoPath = '';
    $base64Logo = '';
    if ($company && $company->logo) {
        $logoPath = public_path('assets/uploads/company/' . $company->logo);
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    } else {
        $logoPath = public_path('assets/uploads/pdf_logo/logo.jpg');
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }

    $isINR = $invoice->currency === 'INR';
    $prefix = $isINR ? '₹' : $invoice->currency . ' ';
    $includeGst = (int) ($invoice->include_gst ?? 1) === 1;
    $gstExempt  = (int) ($invoice->gst_exemption ?? 1) === 0;
    $isDomestic = strtolower($invoice->invoice_type ?? 'domestic') === 'domestic';

    $hasDiscount = false;
    foreach ($invoice->items as $item) {
        if (($item->discount_price ?? 0) > 0) { $hasDiscount = true; break; }
    }

    $subtotal    = (float) $invoice->subtotal;
    $gstTotal    = (float) $invoice->gst_total;
    $grandTotal  = (float) $invoice->grand_total;
    $advance     = (float) $invoice->advance_payment;
    $balance     = max(0, $grandTotal - $advance);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_no }}</title>
    <style>
        @page { margin: 0px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; line-height: 1.4; margin: 0; padding: 0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }
        .text-primary { color: #0078B6; }
        .text-success { color: #2e7d32; }
        .text-danger  { color: #c62828; }

        .page-wrapper { padding: 20px 30px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .company-name { font-size: 18px; font-weight: bold; color: #0078B6; }
        .invoice-title { font-size: 22px; font-weight: bold; color: #0078B6; text-align: right; }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .info-table td { padding: 3px 6px; vertical-align: top; }
        .box { border: 1px solid #ddd; border-radius: 4px; padding: 8px 12px; margin-bottom: 12px; }
        .section-title { font-size: 10px; font-weight: bold; color: #0078B6; text-transform: uppercase; border-bottom: 1px solid #eee; padding-bottom: 4px; margin-bottom: 6px; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .items-table th { background: #0078B6; color: #fff; padding: 6px 8px; font-size: 10px; text-transform: uppercase; }
        .items-table td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 10px; vertical-align: top; }
        .items-table tr:nth-child(even) td { background: #f9f9f9; }

        .totals-table { width: 50%; margin-left: auto; border-collapse: collapse; }
        .totals-table td { padding: 5px 10px; font-size: 11px; }
        .totals-table .grand-row td { font-size: 13px; font-weight: bold; color: #0078B6; border-top: 2px solid #0078B6; }

        .footer-box { margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; font-size: 10px; color: #777; text-align: center; }
        .gst-badge { display: inline; background: #e8f5e9; color: #2e7d32; padding: 1px 5px; border-radius: 3px; font-size: 10px; }
        .payment-summary { margin-top: 12px; background: #e3f2fd; border-left: 4px solid #1565c0; padding: 8px 12px; font-size: 11px; }
        .watermark { color: #28a745; font-weight: bold; text-transform: uppercase; font-size: 10px; }
    </style>
</head>
<body>
<div class="page-wrapper">
    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                @if($base64Logo)
                    <img src="{{ $base64Logo }}" style="max-height:70px; max-width:200px;" alt="Logo">
                @endif
                <div class="company-name">{{ $company->name ?? '' }}</div>
                <div style="font-size:10px; color:#555;">
                    {{ $company->address ?? '' }}<br>
                    @if($company->phone) Tel: {{ $company->phone }}&nbsp;&nbsp; @endif
                    @if($company->email) Email: {{ $company->email }} @endif
                    @if($company->gstin) &nbsp;| GSTIN: {{ $company->gstin }} @endif
                </div>
            </td>
            <td style="text-align:right; vertical-align:top;">
                <div class="invoice-title">TAX INVOICE</div>
                <div style="font-size:12px; color:#555; margin-top:6px;">
                    <strong>Invoice No:</strong> {{ $invoice->invoice_no }}<br>
                    <strong>Date:</strong> {{ $invoice->invoice_date?->format('d M Y') }}<br>
                    <strong>Type:</strong> {{ ucfirst($invoice->invoice_type) }}
                </div>
            </td>
        </tr>
    </table>

    {{-- BILL TO --}}
    <div class="box">
        <div class="section-title">Bill To</div>
        <strong>{{ $invoice->customer->company_name ?? '' }}</strong><br>
        {{ $invoice->customer->address ?? '' }}<br>
        @if($invoice->email) Email: {{ $invoice->email }}&nbsp; @endif
        @if($invoice->phone) | Phone: {{ $invoice->phone }} @endif<br>
        @if($invoice->customer->gst_no) GSTIN: {{ $invoice->customer->gst_no }} @endif
    </div>

    {{-- ITEMS TABLE --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:4%; text-align:center;">#</th>
                <th style="width:34%;">Product / Description</th>
                <th style="width:8%; text-align:center;">Qty</th>
                <th style="width:12%; text-align:right;">Unit Price</th>
                @if($hasDiscount)
                    <th style="width:10%; text-align:right;">Discount</th>
                @endif
                @if($includeGst && !$gstExempt)
                    <th style="width:10%; text-align:right;">GST%</th>
                    <th style="width:11%; text-align:right;">GST Amt</th>
                @endif
                <th style="width:13%; text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
                @php
                    $qty    = (float) $item->qty;
                    $up     = (float) $item->unit_price;
                    $dp     = (float) $item->discount_price;
                    $gp     = ($includeGst && !$gstExempt) ? (float) $item->gst_percent : 0;
                    $dis    = $qty * $dp;
                    $disc   = max(0, $up - $dp);
                    $lt     = $qty * $disc;
                    $gstAmt = $lt * ($gp / 100);
                    $total  = $lt + $gstAmt;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ optional($item->product)->prod_name ?? '-' }}</strong>
                        @if($item->description)
                            <br><span style="color:#666; font-size:9.5px;">{{ $item->description }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ (int)$qty }}</td>
                    <td class="text-right">{{ $prefix }}{{ number_format($up, 2) }}</td>
                    @if($hasDiscount)
                        <td class="text-right">{{ $prefix }}{{ number_format($dis, 2) }}</td>
                    @endif
                    @if($includeGst && !$gstExempt)
                        <td class="text-center"><span class="gst-badge">{{ $gp }}%</span></td>
                        <td class="text-right">{{ $prefix }}{{ number_format($gstAmt, 2) }}</td>
                    @endif
                    <td class="text-right text-bold">{{ $prefix }}{{ number_format($total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALS --}}
    <table class="totals-table">
        <tr>
            <td style="color:#777;">Sub Total:</td>
            <td class="text-right text-bold">{{ $prefix }}{{ number_format($subtotal, 2) }}</td>
        </tr>
        @if($includeGst && !$gstExempt && $gstTotal > 0)
            @if($isDomestic)
                <tr>
                    <td style="color:#777;">CGST:</td>
                    <td class="text-right">{{ $prefix }}{{ number_format($gstTotal / 2, 2) }}</td>
                </tr>
                <tr>
                    <td style="color:#777;">SGST:</td>
                    <td class="text-right">{{ $prefix }}{{ number_format($gstTotal / 2, 2) }}</td>
                </tr>
            @else
                <tr>
                    <td style="color:#777;">IGST:</td>
                    <td class="text-right">{{ $prefix }}{{ number_format($gstTotal, 2) }}</td>
                </tr>
            @endif
        @endif
        <tr class="grand-row">
            <td>Grand Total:</td>
            <td class="text-right">{{ $prefix }}{{ number_format($grandTotal, 2) }}</td>
        </tr>
    </table>

    {{-- PAYMENT SUMMARY --}}
    @if($advance > 0 || $balance > 0)
        <div class="payment-summary">
            <strong>Payment Summary:</strong>&nbsp;&nbsp;
            Grand Total: <strong>{{ $prefix }}{{ number_format($grandTotal, 2) }}</strong>
            &nbsp;|&nbsp; Advance Paid: <strong class="text-success">{{ $prefix }}{{ number_format($advance, 2) }}</strong>
            &nbsp;|&nbsp; Balance Due: <strong class="text-danger">{{ $prefix }}{{ number_format($balance, 2) }}</strong>
        </div>
    @endif

    {{-- TERMS --}}
    @if($invoice->terms_payment)
        <div class="box" style="margin-top:14px;">
            <div class="section-title">Terms of Payment</div>
            {{ $invoice->terms_payment }}
        </div>
    @endif

    @if($invoice->commercial_terms)
        <div class="box">
            <div class="section-title">Commercial Terms</div>
            {{ $invoice->commercial_terms }}
        </div>
    @endif

    <div class="footer-box">
        This is a computer-generated document and does not require a signature.<br>
        Generated on {{ $date }} by {{ $company->name ?? '' }}
    </div>
</div>
</body>
</html>
