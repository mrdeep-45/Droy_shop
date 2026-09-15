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

    $isINR = $document->currency === 'INR';
    $isDomestic = strtolower($document->quotation_type ?? '') === 'domestic';
    $isExport = strtolower($document->quotation_type ?? '') === 'export';
    $isIntrastate = (int) ($document->state_id ?? 0) === (int) ($company->state ?? 0);
    $gstExempt = (int) ($document->gst_exemption ?? 1) === 0;

    $hasDiscount = false;
    foreach ($document->items as $item) {
        if (($item->discount_price ?? 0) > 0) {
            $hasDiscount = true;
            break;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quotation - {{ $document->quotation_no }}</title>
    <style>
        @page {
            margin: 120px 30px 65px 30px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            color: #1e293b;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        header {
            position: fixed;
            top: -105px;
            left: 0px;
            right: 0px;
            height: 90px;
            border-bottom: 2px solid #0d9488;
        }

        footer {
            position: fixed;
            bottom: -55px;
            left: 0px;
            right: 0px;
            height: 52px;
            text-align: center;
            border-top: 1.5px solid #0f172a;
            padding-top: 5px;
            padding-left: 60px;
            padding-right: 60px;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }
        .text-teal { color: #0d9488; }
        .text-dark { color: #0f172a; }
        .text-gray { color: #64748b; }

        .header-top-strip {
            height: 3px;
            background-color: #0d9488;
            width: 100%;
            margin-bottom: 6px;
        }

        .logo-wrapper {
            float: left;
            width: 45%;
        }

        .logo-img {
            max-width: 160px;
            max-height: 50px;
        }

        .header-details {
            float: right;
            width: 50%;
            text-align: right;
        }

        .quote-title {
            font-size: 18px;
            font-weight: bold;
            color: #0d9488;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .meta-table {
            float: right;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 1px 0 1px 6px;
            text-align: right;
            vertical-align: middle;
        }

        .meta-label {
            color: #64748b;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .meta-value {
            font-weight: bold;
            color: #0f172a;
            font-size: 10px;
        }

        .address-section {
            width: 100%;
            margin-bottom: 18px;
            margin-top: 5px;
        }

        .address-col {
            float: left;
            width: 48%;
        }

        .address-col.right {
            float: right;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #0d9488;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1.5px solid #0d9488;
            padding-bottom: 3px;
            margin-bottom: 6px;
            width: 100%;
            display: block;
        }

        .client-name {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .address-details {
            font-size: 10px;
            color: #475569;
            line-height: 1.4;
        }

        .subject-block {
            background-color: #f0fdfa;
            border-left: 3.5px solid #0d9488;
            padding: 7px 12px;
            margin-bottom: 16px;
            border-radius: 0 4px 4px 0;
        }

        .subject-label {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0d9488;
            margin-bottom: 2px;
            display: block;
        }

        .subject-text {
            font-size: 10.5px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.35;
        }

        .category-header {
            margin-top: 14px;
            margin-bottom: 5px;
            page-break-after: avoid;
        }

        .category-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #0d9488;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #f0fdfa;
            padding: 4px 8px;
            border-left: 3px solid #0d9488;
            display: inline-block;
            border-radius: 0 3px 3px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .items-table th {
            background-color: #0d9488;
            color: #ffffff;
            padding: 6px 6px;
            text-align: left;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 7px 6px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
            font-size: 10px;
            vertical-align: top;
        }

        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-right-col { text-align: right; }
        .text-center-col { text-align: center; }

        .footer-section {
            width: 100%;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .terms-section {
            width: 100%;
            margin-top: 10px;
        }

        .terms-head {
            font-weight: bold;
            color: #0d9488;
            font-size: 10.5px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
        }

        .terms-body {
            font-size: 9.5px;
            color: #475569;
            line-height: 1.4;
            text-align: justify;
        }

        .bank-details-block {
            margin-top: 14px;
            border: 1px solid #cbd5e1;
            border-left: 3.5px solid #0d9488;
            padding: 8px 12px;
            margin-bottom: 12px;
            background-color: #f8fafc;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        .bank-details-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0d9488;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #cbd5e1;
        }

        .bank-details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bank-details-table td {
            padding: 4px 4px;
            vertical-align: top;
            font-size: 9.5px;
        }

        .bank-details-table tr+tr td {
            border-top: 1px solid #f1f5f9;
        }

        .bank-details-label {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.3px;
        }

        .bank-details-value {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
        }

        .bank-details-value.mono {
            font-family: 'Consolas', 'Courier New', monospace;
            letter-spacing: 0.5px;
        }

        .bank-upi-value {
            display: inline-block;
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            border-radius: 3px;
            padding: 1px 6px;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 9px;
            font-weight: bold;
            color: #0d9488;
        }

        .signature-block {
            margin-top: 20px;
            text-align: right;
            page-break-inside: avoid;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>

    <header>
        <div class="clearfix" style="padding: 2px 0 0 0;">
            <div class="logo-wrapper">
                @if($base64Logo)
                    <img src="{{ $base64Logo }}" class="logo-img" alt="Company Logo">
                @else
                    <h2 class="text-teal" style="margin:0;">{{ $company->name }}</h2>
                @endif
            </div>

            <div class="header-details">
                <div class="quote-title">QUOTATION</div>
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">QUOTE NO:</td>
                        <td class="meta-value text-teal">
                            {{ $document->quotation_no }}
                            @if($document->revision_no > 0)
                                #{{ $document->revision_no }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">DATE:</td>
                        <td class="meta-value">{{ $document->quotation_date }}</td>
                    </tr>
                    @if(!$isINR)
                        <tr>
                            <td class="meta-label">CURRENCY:</td>
                            <td class="meta-value">{{ $document->currency }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">EX. RATE:</td>
                            <td class="meta-value" style="font-weight: normal; font-size: 9px;">
                                1 {{ $document->currency }} = {{ number_format($document->exchange_rate, 4) }} INR
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </header>

    <footer>
        <div style="font-size: 11px; font-weight: bold; color: #0d9488; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">
            {{ $company->name }}
        </div>
        <div style="font-size: 8.5px; color: #1e293b; margin-bottom: 2px; line-height: 1.3;">
            {{ str_replace(["\t", "\r", "\n"], ' ', $company->address) }}
        </div>
        <div style="font-size: 8.5px; color: #1e293b;">
            @if(!empty($company->contact_phone) || !empty($company->contact_no))
                Ph.: {{ $company->contact_phone ?? $company->contact_no }},
            @endif
            @if(!empty($company->contact_email) || !empty($company->email))
                E-mail: {{ $company->contact_email ?? $company->email }},
            @endif
            @if(!empty($company->website))
                Website: <span style="color: #0000ff; text-decoration: underline;">{{ str_replace(['http://', 'https://'], '', $company->website) }}</span>
            @endif
            @if(!empty($company->gst_no))
                <strong style="margin-left: 6px; font-weight: bold; color: #0f172a;">GST No : {{ $company->gst_no }}</strong>
            @endif
        </div>
    </footer>

    <main>
        <div class="address-section clearfix">
            <div class="address-col">
                <div class="section-title">Bill To</div>
                <div class="client-name">{{ $document->customer_name }}</div>
                <div class="address-details">
                    @if($document->company_name && $document->company_name !== $document->customer_name)
                        <strong>{{ $document->company_name }}</strong><br>
                    @endif
                    @if($document->contact_person_name)
                        Contact: {{ $document->contact_person_name }}<br>
                    @endif
                    @if($document->email)
                        {{ $document->email }}<br>
                    @endif
                    @if($document->phone)
                        {{ $document->phone }}<br>
                    @endif
                    @if($document->city || $document->state)
                        {{ implode(', ', array_filter([$document->city, $document->state, $document->country])) }}<br>
                    @endif
                    @if($document->gst_no)
                        GST: {{ $document->gst_no }}
                    @endif
                </div>
            </div>

            <div class="address-col right">
                <div class="section-title">From</div>
                <div class="client-name text-teal">{{ $company->name }}</div>
                <div class="address-details">
                    {{ $company->address }}<br>
                    GST: {{ $company->gst_no ?? 'N/A' }}<br>
                    {{ $company->contact_email ?? $company->email ?? '' }}<br>
                    {{ $company->contact_phone ?? $company->contact_no ?? '' }}
                </div>
            </div>
        </div>

        @if($document->subject)
            <div class="subject-block">
                <span class="subject-label">Subject</span>
                <div class="subject-text">{{ $document->subject }}</div>
            </div>
        @endif

        @php
            $groupedItems = collect($document->items)->groupBy(function($item) {
                return !empty($item->category_name) ? $item->category_name : 'General';
            });
            $sr_no = 1;
        @endphp

        @foreach($groupedItems as $categoryName => $catItems)
            <div class="category-header">
                <span class="category-title">
                    {{ $categoryName }}
                </span>
            </div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: {{ $hasDiscount ? '48%' : '58%' }}">Description</th>
                        <th class="text-center-col" style="width: 12%">Qty/Unit</th>
                        <th class="text-right-col" style="width: 12%">Price</th>
                        @if($hasDiscount)
                            <th class="text-right-col" style="width: 10%">Discount</th>
                        @endif
                        <th class="text-right-col" style="width: 13%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($catItems as $item)
                        @php
                            $unitPrice = $isINR ? $item->price : ($item->unit_price_converted ?? $item->price);
                            $lineTotal = $isINR ? $item->line_total : ($item->total_converted ?? $item->line_total);
                        @endphp
                        <tr>
                            <td class="text-gray">{{ $sr_no }}</td>
                            <td>
                                <strong style="font-size: 10.5px;">{{ $item->item_name }}</strong>
                                @if($item->item_description)
                                    <br><span style="font-size: 8.5px; color: #475569;">{{ \Illuminate\Support\Str::limit($item->item_description, 250, '...') }}</span>
                                @endif
                            </td>
                            <td class="text-center-col">{{ (int)$item->qty }}@if(!empty($item->unit_name)) {{ $item->unit_name }}@endif</td>
                            <td class="text-right-col">{{ number_format($unitPrice, 2) }}</td>
                            @if($hasDiscount)
                                <td class="text-right-col">
                                    @if(($item->discount_price ?? 0) > 0)
                                        {{ $item->discount_percentage }}% ({{ number_format($item->discount_price, 2) }})
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                            <td class="text-right-col text-bold">{{ number_format($lineTotal, 2) }}</td>
                        </tr>
                        @php $sr_no++; @endphp
                    @endforeach
                </tbody>
            </table>
        @endforeach

        <table style="width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: 12px; page-break-inside: avoid;">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #cbd5e1; font-size: 10px;">
                        <tr style="background-color: #f8fafc;">
                            <td style="padding: 5px 8px; font-weight: bold; color: #475569; border-bottom: 1px solid #e2e8f0;">Sub Total:</td>
                            <td style="padding: 5px 8px; text-align: right; font-weight: bold; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ number_format($document->subtotal ?? 0, 2) }}</td>
                        </tr>
                        @if(($document->gst_total ?? 0) > 0)
                            <tr style="background-color: #ffffff;">
                                <td style="padding: 5px 8px; font-weight: bold; color: #475569; border-bottom: 1px solid #e2e8f0;">GST Total:</td>
                                <td style="padding: 5px 8px; text-align: right; font-weight: bold; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ number_format($document->gst_total, 2) }}</td>
                            </tr>
                        @endif
                        <tr style="background-color: #f0fdfa;">
                            <td style="padding: 6px 8px; font-size: 11px; font-weight: bold; color: #0d9488;">Total Amount ({{ $document->currency }}):</td>
                            <td style="padding: 6px 8px; text-align: right; font-size: 11px; font-weight: bold; color: #0d9488;">{{ number_format($document->grand_total ?? 0, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="footer-section clearfix">
            <div class="terms-section">
                @if($document->terms_conditions)
                    @php
                        $parsedTerms = [];
                        $rawTerms = $document->terms_conditions;
                        
                        // Insert newline before embedded numbered points like " 2. ", " 3. ", " 2) " (only 1-3 digits!)
                        $rawTerms = preg_replace('/(?<=\S)\s+(\d{1,3}[\.\)]\s+)/', "\n$1", $rawTerms);

                        // Split strictly line by line
                        $lines = explode("\n", str_replace("\r", "", $rawTerms));
                        $currentTerm = null;

                        foreach ($lines as $line) {
                            $trimmed = trim($line);
                            if ($trimmed === '') continue;

                            // Check A: Numbered item line (e.g. "1. Customer..." or "1) ...", max 3 digits + space)
                            if (preg_match('/^(\d{1,3}[\.\)])\s+(.*)$/', $trimmed, $matches)) {
                                if ($currentTerm) {
                                    $parsedTerms[] = $currentTerm;
                                }
                                $currentTerm = [
                                    'type' => 'number',
                                    'label' => $matches[1],
                                    'detail' => trim($matches[2])
                                ];
                            }
                            // Check B: Colon item line (e.g. "Prices : Ex-works..." or "Delivery: ...")
                            elseif (preg_match('/^([^:\n]{1,35})\s*:\s*(.*)$/', $trimmed, $matches)) {
                                if ($currentTerm) {
                                    $parsedTerms[] = $currentTerm;
                                }
                                $currentTerm = [
                                    'type' => 'colon',
                                    'label' => trim($matches[1]),
                                    'detail' => trim($matches[2])
                                ];
                            }
                            // Check C: Plain or Continuation line
                            else {
                                if ($currentTerm) {
                                    $currentTerm['detail'] .= ' ' . $trimmed;
                                } else {
                                    $currentTerm = [
                                        'type' => 'plain',
                                        'label' => '',
                                        'detail' => $trimmed
                                    ];
                                }
                            }
                        }
                        if ($currentTerm) {
                            $parsedTerms[] = $currentTerm;
                        }
                    @endphp
                    <div class="terms-head">Terms & Conditions:</div>
                    <table class="terms-table" style="width: 100%; border-collapse: collapse; margin-top: 4px; margin-bottom: 8px;">
                        @foreach($parsedTerms as $t)
                            <tr>
                                @if($t['type'] === 'number')
                                    <td style="width: 4%; vertical-align: top; font-weight: bold; color: #0f172a; font-size: 9.5px; padding: 3px 2px 3px 0;">
                                        {{ $t['label'] }}
                                    </td>
                                    <td colspan="2" style="width: 96%; vertical-align: top; color: #334155; font-size: 9.5px; padding: 3px 0; line-height: 1.35;">
                                        {{ $t['detail'] }}
                                    </td>
                                @elseif($t['type'] === 'colon')
                                    <td style="width: 20%; vertical-align: top; font-weight: bold; color: #0f172a; font-size: 9.5px; padding: 3px 4px 3px 0;">
                                        {{ $t['label'] }}
                                    </td>
                                    <td style="width: 2%; vertical-align: top; font-weight: bold; color: #0f172a; font-size: 9.5px; padding: 3px 4px 3px 0;">
                                        :
                                    </td>
                                    <td style="width: 78%; vertical-align: top; color: #334155; font-size: 9.5px; padding: 3px 0; line-height: 1.35;">
                                        {{ $t['detail'] }}
                                    </td>
                                @else
                                    <td colspan="3" style="vertical-align: top; color: #334155; font-size: 9.5px; padding: 3px 0; line-height: 1.35;">
                                        {{ $t['detail'] }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @endif

                @if(!$isINR)
                    <div style="margin-top: 8px; font-size: 8.5px; color: #64748b; font-style: italic;">
                        * Exchange Rate: 1 {{ $document->currency }} =
                        {{ number_format($document->exchange_rate, 4) }} INR
                    </div>
                @endif

                @if($document->remarks)
                    <div style="margin-top: 10px;">
                        <div style="font-weight: bold; color: #0d9488; font-size: 9.5px; text-transform: uppercase; margin-bottom: 3px;">Notes</div>
                        <div style="font-size: 9.5px; color: #475569; line-height: 1.4; white-space: pre-line;">{{ $document->remarks }}</div>
                    </div>
                @endif
            </div>
        </div>

        @php
            $sigPath = public_path('assets/uploads/company/' . ($company->signature ?? ''));
            $base64Sig = '';
            if (!empty($company->signature) && file_exists($sigPath)) {
                $sigType = pathinfo($sigPath, PATHINFO_EXTENSION);
                $sigData = file_get_contents($sigPath);
                $base64Sig = 'data:image/' . $sigType . ';base64,' . base64_encode($sigData);
            }
        @endphp

        <!-- <table style="width: 100%; border-collapse: collapse; margin-top: 14px; page-break-inside: avoid;">
            <tr>
                <td style="width: 72%; vertical-align: top; padding-right: 12px;">
                    <div style="border: 1px solid #cbd5e1; border-left: 3.5px solid #0d9488; padding: 8px 10px; background-color: #f8fafc; border-radius: 4px;">
                        <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; color: #0d9488; margin-bottom: 6px; padding-bottom: 3px; border-bottom: 1px solid #cbd5e1;">
                            BANK DETAILS
                        </div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 9.5px;">
                            <tr>
                                <td style="width: 40%; font-weight: bold; color: #475569; padding: 2.5px 0;">BANK NAME:</td>
                                <td style="font-weight: bold; color: #0f172a; padding: 2.5px 0;">{{ $company->bank_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; color: #475569; padding: 2.5px 0;">ACCOUNT HOLDER NAME:</td>
                                <td style="font-weight: bold; color: #0f172a; padding: 2.5px 0;">{{ $company->account_holder_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; color: #475569; padding: 2.5px 0;">ACCOUNT NO.:</td>
                                <td style="font-weight: bold; color: #0f172a; font-family: 'Consolas', monospace; padding: 2.5px 0;">{{ $company->account_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; color: #475569; padding: 2.5px 0;">IFSC CODE:</td>
                                <td style="font-weight: bold; color: #0f172a; font-family: 'Consolas', monospace; padding: 2.5px 0;">{{ $company->ifsc_code ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; color: #475569; padding: 2.5px 0;">BRANCH NAME:</td>
                                <td style="font-weight: bold; color: #0f172a; padding: 2.5px 0;">{{ $company->branch_name ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td style="width: 28%; vertical-align: bottom; text-align: center; padding-bottom: 4px;">
                    @if($base64Sig)
                        <img src="{{ $base64Sig }}" style="max-height: 48px; max-width: 140px; margin-bottom: 4px;" alt="Signature">
                    @else
                        <div style="height: 45px;"></div>
                    @endif
                    <div style="font-size: 9.5px; font-weight: bold; color: #0d9488; text-transform: uppercase;">
                        Authorised Signatory
                    </div>
                    <div style="font-size: 9px; font-weight: bold; color: #0f172a; margin-top: 2px;">
                        {{ $company->name ?? 'HEXAGON PRODUCT DEVELOPMENT PVT LTD' }}
                    </div>
                </td>
            </tr>
        </table> -->

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 8;
            $color = array(0.3, 0.3, 0.3);
            $pdf->page_text(520, 818, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, $size, $color);
        }
    </script>

</body>

</html>
