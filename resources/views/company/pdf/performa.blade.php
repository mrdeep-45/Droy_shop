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
    $includeGst = (int) ($document->include_gst ?? 0) === 1;

    $hasDiscount = false;
    foreach ($document->items as $item) {
        if (($item->discount_price ?? 0) > 0) {
            $hasDiscount = true;
            break;
        }
    }

    $companyPan = '';
    if (!empty($company->gst_no) && strlen($company->gst_no) >= 12) {
        $companyPan = substr($company->gst_no, 2, 10);
    }
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Proforma Invoice - {{ $document->invoice_no }}</title>
    <style>
        @page {
            margin: 15px 20px 80px 20px;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0px;
            right: 0px;
            height: 65px;
            text-align: center;
            border-top: 2px solid #0078B6;
            padding-top: 5px;
            padding-bottom: 12px;
            font-size: 8.5px;
            line-height: 1.35;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            color: #000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }

        .container {
            width: 100%;
        }

        /* Table structures */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
        }

        .main-table td {
            border: 1px solid #000;
            padding: 0;
            vertical-align: top;
        }

        .inner-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .inner-table td {
            border: none;
            border-bottom: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }

        .inner-table tr:last-child td {
            border-bottom: none;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-top: -1.5px;
        }

        .items-table th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 5px;
            font-weight: bold;
            text-align: center;
        }

        .items-table td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }

        .items-table tr.total-row td {
            border: 1px solid #000;
            font-weight: bold;
        }

        /* Bottom info block */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-top: -1.5px;
        }

        .bottom-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        .meta-label {
            font-size: 8px;
            color: #555;
            display: block;
            text-transform: uppercase;
            font-weight: normal;
        }

        .meta-val {
            font-weight: bold;
            font-size: 9.5px;
        }
    </style>
</head>

<body>

    <div class="container">
        
        <!-- Header Logo (Right) -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; border: none;">
            <tr>
                <td style="border: none; text-align: right; vertical-align: top;">
                    @if($base64Logo)
                        <img src="{{ $base64Logo }}" style="max-height: 55px; max-width: 180px;" alt="Logo">
                    @endif
                </td>
            </tr>
        </table>

        <!-- PROFORMA INVOICE Title -->
        <div style="text-align: center; margin-bottom: 12px;">
            <span style="font-size: 18px; font-weight: bold; text-decoration: underline; text-transform: uppercase; letter-spacing: 0.5px;">Proforma Invoice</span>
        </div>

        <!-- Main Info Box -->
        <table class="main-table">
            <tr>
                <!-- Seller details (Left) -->
                <td style="width: 50%; padding: 6px;">
                    <strong style="font-size: 11px;">{{ $company->name }}</strong><br>
                    <span style="font-size: 9px; color: #333;">
                        {!! nl2br(e($company->address)) !!}<br>
                        @if($company->contact_phone || $company->email)
                            Ph. {{ $company->contact_phone ?? $company->contact_no }}, Email : {{ $company->contact_email ?? $company->email }}<br>
                        @endif
                        Contact Person : {{ $company->contact_person ?? 'Mr Ahamed Ibrahim' }}
                    </span>
                    <br><br>
                    <strong>CIN :</strong> {{ $company->registration_number ?? 'U29220GJ2008PTC053638' }}
                </td>
                
                <!-- Invoice Metadata (Right) -->
                <td style="width: 50%;">
                    <table class="inner-table">
                        <tr>
                            <td style="width: 60%; border-right: 1px solid #000;">
                                <span class="meta-label">P. Invoice No.:</span>
                                <span class="meta-val">{{ $document->invoice_no }}</span>
                            </td>
                            <td style="width: 40%;">
                                <span class="meta-label">Dated</span>
                                <span class="meta-val">{{ $document->invoice_date }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="height: 25px;">
                                <span class="meta-label">Delivery Note:</span>
                                <span class="meta-val">{{ $document->delivery_note ?? '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 60%; border-right: 1px solid #000;">
                                <span class="meta-label">Buyer's order no.</span>
                                <span class="meta-val">{{ $document->buyer_order_no ?: '-' }}</span>
                            </td>
                            <td style="width: 40%;">
                                <span class="meta-label">Date:</span>
                                <span class="meta-val">{{ $document->buyer_order_date ?: '-' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 60%; border-right: 1px solid #000;">
                                <span class="meta-label">Transport</span>
                                <span class="meta-val" style="font-weight: normal;">{{ $document->transportation ?: '-' }}</span>
                            </td>
                            <td style="width: 40%;">
                                <span class="meta-label">Destination</span>
                                <span class="meta-val" style="font-weight: normal;">{{ $document->destination ?: '-' }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <!-- Buyer Details (Left) -->
                <td style="width: 50%; padding: 6px;">
                    <span class="meta-label" style="font-size: 9px; font-weight: bold; margin-bottom: 2px;">Buyer :</span>
                    <strong style="font-size: 11px;">{{ $document->customer_name }}</strong><br>
                    <span style="font-size: 9.5px;">
                        @if($document->company_name && $document->company_name !== $document->customer_name)
                            <strong>{{ $document->company_name }}</strong><br>
                        @endif
                        @if($document->city || $document->state)
                            {{ implode(', ', array_filter([$document->city, $document->state, $document->country])) }}<br>
                        @endif
                        @if($document->contact_person_name)
                            Kind Attn : {{ $document->contact_person_name }}<br>
                        @endif
                        @if($document->email)
                            Email - {{ $document->email }}<br>
                        @endif
                        @if($document->phone)
                            Ph - {{ $document->phone }}
                        @endif
                    </span>
                </td>
                
                <!-- Terms / Reference Details (Right) -->
                <td style="width: 50%;">
                    <table class="inner-table">
                        <tr>
                            <td style="height: 22px;">
                                <span class="meta-label">P. O. Reference</span>
                                <span class="meta-val" style="font-weight: normal;">{{ $document->po_reference ?? '' }}</span>
                            </td>
                        </tr>
                        @if(isset($document->terms_list) && count($document->terms_list) > 0)
                            @foreach($document->terms_list as $termItem)
                                <tr>
                                    <td style="padding: 4px 6px;">
                                        <span class="meta-label" style="text-transform: uppercase;">{{ $termItem->title }}:</span>
                                        <span class="meta-val" style="font-weight: normal; font-size: 9px; display: block; margin-top: 2px;">
                                            {{ $termItem->terms }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @elseif(!empty($document->terms_conditions))
                            <tr>
                                <td style="padding: 4px 6px;">
                                    <span class="meta-label">TERMS & CONDITIONS:</span>
                                    <span class="meta-val" style="font-weight: normal; font-size: 9px; display: block; margin-top: 2px;">
                                        {!! nl2br(e($document->terms_conditions)) !!}
                                    </span>
                                </td>
                            </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        <!-- Items List Table -->
        @php
            $showGst = $isDomestic && !$gstExempt && $includeGst;
        @endphp
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: {{ $showGst ? '42%' : '58%' }}; text-align: left;">Description of Goods</th>
                    @if($showGst)
                        <th style="width: 10%">GST %</th>
                    @endif
                    <th style="width: 10%">Quantity</th>
                    <th style="width: 13%; text-align: right;">Rate</th>
                    <th style="width: 8%">Per</th>
                    <th style="width: 12%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $sr = 1; @endphp
                @foreach($document->items as $item)
                    @php
                        $unitPrice = $isINR ? $item->price : ($item->unit_price_converted ?? $item->price);
                        $lineTotal = $isINR ? $item->line_total : ($item->total_converted ?? $item->line_total);
                        $hsnCode = DB::table('tbl_product')->where('product_id', $item->product_id)->value('prod_code') ?? '';
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $sr }}</td>
                        <td>
                            @if($hsnCode)
                                <strong>HSN CODE : {{ $hsnCode }}</strong><br>
                            @endif
                            <strong style="font-size: 10px;">{{ $item->item_name }}</strong>
                            @if($item->item_description)
                                <div style="font-size: 8px; color: #555; margin-top: 1px;">{{ $item->item_description }}</div>
                            @endif
                        </td>
                        @if($showGst)
                            <td style="text-align: center;">
                                {{ $item->gst_percent > 0 ? number_format($item->gst_percent, 0) . '%' : '-' }}
                            </td>
                        @endif
                        <td style="text-align: center;">{{ (int)$item->qty }}</td>
                        <td style="text-align: right;">{{ number_format($unitPrice, 2) }}</td>
                        <td style="text-align: center;">Nos</td>
                        <td style="text-align: right;">{{ number_format($lineTotal, 2) }}</td>
                    </tr>
                    @php $sr++; @endphp
                @endforeach
                
                <!-- Spacers to maintain height -->
                @for($i = $sr; $i <= 6; $i++)
                    <tr>
                        <td style="height: 18px;"></td>
                        <td style="height: 18px;"></td>
                        @if($showGst)
                            <td style="height: 18px;"></td>
                        @endif
                        <td style="height: 18px;"></td>
                        <td style="height: 18px;"></td>
                        <td style="height: 18px;"></td>
                        <td style="height: 18px;"></td>
                    </tr>
                @endfor

                <!-- Totals section inside table footer -->
                @php
                    $colspanVal = $showGst ? 5 : 4;
                    $subtotalVal = $isINR ? $document->subtotal : $document->subtotal * $document->exchange_rate;
                    $discountVal = $isINR ? $document->discount_total : $document->discount_total * $document->exchange_rate;
                    $preGstTotal = $subtotalVal - $discountVal;
                    $grandTotalVal = $isINR ? $document->grand_total : $document->grand_total_converted;
                @endphp
                <tr class="total-row">
                    <td colspan="{{ $colspanVal }}" style="text-align: right;">Total</td>
                    <td style="text-align: center; border-left: none;"></td>
                    <td style="text-align: right;">{{ number_format($preGstTotal, 2) }}</td>
                </tr>

                @if($showGst && $document->gst_total > 0)
                    @php 
                        $gstDisplay = $isINR ? $document->gst_total : $document->gst_total * $document->exchange_rate; 
                    @endphp
                    @if($isIntrastate)
                        <tr class="total-row">
                            <td colspan="{{ $colspanVal }}" style="text-align: right;">CGST</td>
                            <td style="text-align: center; border-left: none;"></td>
                            <td style="text-align: right;">{{ number_format($gstDisplay / 2, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="{{ $colspanVal }}" style="text-align: right;">SGST</td>
                            <td style="text-align: center; border-left: none;"></td>
                            <td style="text-align: right;">{{ number_format($gstDisplay / 2, 2) }}</td>
                        </tr>
                    @else
                        <tr class="total-row">
                            <td colspan="{{ $colspanVal }}" style="text-align: right;">IGST</td>
                            <td style="text-align: center; border-left: none;"></td>
                            <td style="text-align: right;">{{ number_format($gstDisplay, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="{{ $colspanVal }}" style="text-align: right;">Total</td>
                        <td style="text-align: center; border-left: none;"></td>
                        <td style="text-align: right;">{{ number_format($grandTotalVal, 2) }}</td>
                    </tr>
                @endif

                @if(isset($document->advance_payment_percentage) && floatval($document->advance_payment_percentage) > 0)
                    @php
                        $toPayNow = $isINR ? $document->advance_payment : $document->advance_payment * $document->exchange_rate;
                        $advPct = floatval($document->advance_payment_percentage);
                        $balPct = floatval($document->balance_payment_percentage);
                        if ($balPct <= 0 && $advPct > 0 && $advPct < 100) {
                            $balPct = 100 - $advPct;
                        }
                        $balVal = floatval($document->balance_payment) > 0
                            ? ($isINR ? $document->balance_payment : $document->balance_payment * $document->exchange_rate)
                            : max(0, $grandTotalVal - $toPayNow);
                    @endphp
                    <tr class="total-row">
                        <td colspan="{{ $colspanVal }}" style="text-align: right;">To pay now @ {{ number_format($advPct, 0) }}%</td>
                        <td style="text-align: center; border-left: none;"></td>
                        <td style="text-align: right; color: #d9534f;">{{ number_format($toPayNow, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="{{ $colspanVal }}" style="text-align: right;">Balance Amount @if($balPct > 0) @ {{ number_format($balPct, 0) }}% @endif</td>
                        <td style="text-align: center; border-left: none;"></td>
                        <td style="text-align: right;">{{ number_format($balVal, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Bottom Grid Table -->
        <table class="bottom-table">
            <tr>
                <!-- Tax, words, bank details (Left) -->
                <td style="width: 60%;">
                    <strong>Amount Chargeable (in words):</strong><br>
                    <span style="font-style: italic; font-weight: bold;">
                        @php
                            $chargeableVal = $document->grand_total;
                            if (isset($document->advance_payment_percentage) && floatval($document->advance_payment_percentage) > 0) {
                                $toPayNow = $document->advance_payment;
                                $chargeableVal = floatval($document->balance_payment) > 0
                                    ? $document->balance_payment
                                    : max(0, $document->grand_total - $toPayNow);
                            }
                            if (!$isINR) {
                                $chargeableVal = $chargeableVal * $document->exchange_rate;
                            }
                        @endphp
                        {{ getAmountInWords($chargeableVal) }}
                    </span>
                    <br><br>
                    <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 5px;">
                        <tr>
                            <td style="width: 38%; padding: 2px 0 2px 5px;">Company's GST No :</td>
                            <td style="width: 62%; padding: 2px 0 2px 5px;"><strong>{{ $company->gst_no ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0 2px 5px;">Company's PAN :</td>
                            <td style="padding: 2px 0 2px 5px;"><strong>{{ $companyPan ?: '-' }}</strong></td>
                        </tr>
                    </table>
                    
                    <div style="padding-left: 5px; margin-top: 4px; margin-bottom: 2px;">
                        <strong>Company's bank details</strong>
                    </div>
                    <table style="width: 100%; border-collapse: collapse; border: none; margin-top: 2px;">
                        <tr>
                            <td style="width: 38%; padding: 2px 0 2px 5px; color: #555;">Bank Name :</td>
                            <td style="width: 62%; padding: 2px 0 2px 5px;"><strong>{{ $company->bank_name ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0 2px 5px; color: #555;">A/c. No :</td>
                            <td style="padding: 2px 0 2px 5px;"><strong>{{ $company->account_number ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 0 2px 5px; color: #555;">Branch & IFSC :</td>
                            <td style="padding: 2px 0 2px 5px;"><strong>{{ $company->branch_name ?? '-' }} & {{ $company->ifsc_code ?? '-' }}</strong></td>
                        </tr>
                    </table>
                </td>
                
                <!-- Signature Area (Right) -->
                <td style="width: 40%; text-align: center; height: 120px; position: relative;">
                    <div style="text-align: right; font-size: 8px; font-weight: bold; margin-bottom: 2px;">E. & O.E</div>
                    <div style="font-weight: bold; font-size: 9.5px; margin-top: 5px;">For {{ strtoupper($company->name) }}</div>
                    
                    @if($company->signature)
                        @php
                            $sigPath = public_path('assets/uploads/company/' . $company->signature);
                            $base64Sig = '';
                            if (file_exists($sigPath)) {
                                $sigType = pathinfo($sigPath, PATHINFO_EXTENSION);
                                $sigData = file_get_contents($sigPath);
                                $base64Sig = 'data:image/' . $sigType . ';base64,' . base64_encode($sigData);
                            }
                        @endphp
                        @if($base64Sig)
                            <div style="margin-top: 15px; margin-bottom: 5px;">
                                <img src="{{ $base64Sig }}" style="max-height: 45px;" alt="Signature">
                            </div>
                        @endif
                    @else
                        <div style="height: 50px;"></div>
                    @endif
                    
                    <div style="position: absolute; bottom: 8px; left: 0; right: 0; text-align: center; font-weight: bold; font-size: 9.5px;">
                        Authorised Signatory
                    </div>
                </td>
            </tr>
        </table>

        <!-- Jurisdiction text -->
        <div style="text-align: center; font-style: italic; font-size: 8.5px; margin-top: 10px; margin-bottom: 15px; font-weight: bold; letter-spacing: 0.5px;">
            SUBJECT TO VADODARA JURISDICTION
        </div>

        <!-- Fixed Footer at bottom of page -->
        <footer>
            <strong style="color: #0078B6; font-size: 11px; font-weight: bold;">{{ strtoupper($company->name) }}</strong><br>
            <strong style="font-size: 8.5px; text-transform: uppercase;">: REGISTERED OFFICE & FACTORY :</strong><br>
            <span style="color: #444;">
                {{ $company->address }}<br>
                <strong>Contact Us :</strong> Ph. : {{ $company->contact_phone ?? $company->contact_no }} &nbsp;|&nbsp; <strong>E-mail :</strong> {{ $company->contact_email ?? $company->email }} &nbsp;|&nbsp; <strong>URL :</strong> {{ $company->website ?? 'http://www.alphiemixer.com' }}<br>
                <strong>CIN :</strong> {{ $company->registration_number ?? '' }} &nbsp;|&nbsp; <strong>GSTIN No. :</strong> {{ $company->gst_no ?? '' }}
            </span>
        </footer>

    </div>

</body>

</html>
