@php
    $termsMap = \Illuminate\Support\Facades\DB::table('tbl_terms')->pluck('module', 'term_id')->toArray();
    $resolveTerms = function($value) use ($termsMap) {
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
            foreach ($ids as $id) {
                if (isset($termsMap[$id])) {
                    $names[] = $termsMap[$id];
                } else {
                    $names[] = 'Term #' . $id;
                }
            }
            return implode(', ', $names);
        }
        return $value;
    };
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Timeline History Report</title>
    <style>
        @page {
            margin: 15px 15px 30px 15px;
        }
        body { font-family: sans-serif; font-size: 11px; margin: 0; padding: 0; color: #333; }
        h2 { text-align: left; font-size: 13px; margin-top: 0; margin-bottom: 0; color: #1e3a8a; text-transform: uppercase; }
        .header-container { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 3px; margin-bottom: 8px; }
        .header-table { width: 100%; border: none; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        .logo { max-height: 45px; max-width: 140px; }
        .company-info { text-align: right; line-height: 1.25; font-size: 9px; }
        .company-name { font-size: 14px; font-weight: bold; color: #1e3a8a; margin-bottom: 2px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 6px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #1e3a8a; color: white; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .data-table tr:nth-child(even) { background-color: #f9f9f9; }
        .footer {
            position: fixed;
            bottom: -15px;
            left: 0px;
            right: 0px;
            height: 15px;
            text-align: right;
            font-size: 8px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 2px;
        }
    </style>
</head>
<body>
    @if(isset($is_pdf) && $is_pdf)
        <div class="footer">
            Printed on: {{ date('d-m-Y h:i A') }}
        </div>
    @endif

    @if(isset($is_pdf) && $is_pdf)
        @php
            $companyDetails = get_company_details(1);
            $logoPath = null;
            if($companyDetails && $companyDetails->logo) {
                $logoPath = public_path('assets/uploads/company/' . $companyDetails->logo);
            }
        @endphp
        <div class="header-container">
            <table class="header-table">
                <tr>
                    <td style="width: 50%;">
                        @if($logoPath && file_exists($logoPath))
                            <img src="{{ $logoPath }}" alt="Company Logo" class="logo">
                        @else
                            <h2 style="margin:0; text-align:left;">{{ $companyDetails ? $companyDetails->company_name : 'CRM' }}</h2>
                        @endif
                    </td>
                    <td class="company-info" style="width: 50%;">
                        <div class="company-name">{{ $companyDetails ? $companyDetails->company_name : 'Company Name' }}</div>
                        @if($companyDetails)
                            @if($companyDetails->contact_email) <div><strong>Email:</strong> {{ $companyDetails->contact_email }}</div> @endif
                            @if($companyDetails->phone) <div><strong>Phone:</strong> {{ $companyDetails->phone }}</div> @endif
                            @if($companyDetails->address) <div><strong>Address:</strong> {{ $companyDetails->address }}</div> @endif
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif

    @if(isset($is_pdf) && $is_pdf)
        <!-- Dynamic Header & Customer Summary for PDF (Takes minimal space) -->
        <table style="width: 100%; border: none; margin-bottom: 6px; margin-top: 2px;">
            <tr>
                <td style="border: none; padding: 0; vertical-align: bottom;">
                    <h2 style="margin: 0; font-size: 13px; color: #1e3a8a; text-transform: uppercase;">
                        Timeline History - {{ $title }}
                    </h2>
                </td>
                @if(isset($lead) && $lead)
                    @php
                        $companyName = $lead->customer ? $lead->customer->company_name : ($lead->company_name ?: '-');
                        $contactPerson = $lead->contact_person_name ?? ($lead->customer ? $lead->customer->contact_person_name : '-');
                        $phone = $lead->phone ?? ($lead->customer ? $lead->customer->phone : '-');
                        $email = $lead->email ?? ($lead->customer ? $lead->customer->email : '-');
                        $prodNames = $lead->products->map(function($p) {
                            return $p->product ? $p->product->prod_name : ('Product #' . $p->product_id);
                        })->filter()->implode(', ');
                    @endphp
                    <td style="border: none; padding: 0; text-align: right; vertical-align: top;">
                        <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px 8px; display: inline-block; text-align: left; font-size: 9px; line-height: 1.3;">
                            <span style="color: #1e3a8a; font-weight: bold;">Company:</span> {{ $companyName }} | 
                            <span style="color: #1e3a8a; font-weight: bold;">Contact:</span> {{ $contactPerson }} ({{ $phone }})<br>
                            <span style="color: #1e3a8a; font-weight: bold;">Email:</span> {{ $email }}
                            @if(!empty($prodNames))
                                | <span style="color: #1e3a8a; font-weight: bold;">Product(s):</span> {{ $prodNames }}
                            @endif
                        </div>
                    </td>
                @endif
            </tr>
        </table>
    @else
        <!-- Original Table Format Kept Intact for Excel Export -->
        @if(isset($lead) && $lead)
            @php
                $companyName = $lead->customer ? $lead->customer->company_name : ($lead->company_name ?: '-');
                $contactPerson = $lead->contact_person_name ?? ($lead->customer ? $lead->customer->contact_person_name : '-');
                $phone = $lead->phone ?? ($lead->customer ? $lead->customer->phone : '-');
                $email = $lead->email ?? ($lead->customer ? $lead->customer->email : '-');
                $prodNames = $lead->products->map(function($p) {
                    return $p->product ? $p->product->prod_name : ('Product #' . $p->product_id);
                })->filter()->implode(', ');
            @endphp
            <table style="border: none;">
                <tr>
                    <td style="font-weight: bold; border: none; color: #1e3a8a;">CUSTOMER DETAILS</td>
                    <td style="border: none;"></td>
                    <td style="font-weight: bold; border: none; color: #1e3a8a;">CONTACT &amp; PRODUCT INFO</td>
                    <td style="border: none;"></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; border: none;">Company:</td>
                    <td style="border: none;">{{ $companyName }}</td>
                    <td style="font-weight: bold; border: none;">Phone:</td>
                    <td style="border: none;">{{ $phone }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; border: none;">Contact Person:</td>
                    <td style="border: none;">{{ $contactPerson }}</td>
                    <td style="font-weight: bold; border: none;">Email:</td>
                    <td style="border: none;">{{ $email }}</td>
                </tr>
                @if(!empty($prodNames))
                <tr>
                    <td style="font-weight: bold; border: none;">Product(s):</td>
                    <td colspan="3" style="border: none;">{{ $prodNames }}</td>
                </tr>
                @endif
            </table>
            <br>
        @endif
    @endif

    <table class="data-table">
        <thead>
            @if(!isset($is_pdf) || !$is_pdf)
                <tr>
                    <th colspan="6" style="font-weight: bold; font-size: 14px; text-align: center; height: 35px; background-color: #ffffff; color: #1e3a8a; border: 1px solid #ddd;">Timeline History - {{ $title }}</th>
                </tr>
            @endif
            <tr>
                <th style="background-color: #1e3a8a; color: white; font-weight: bold; border: 1px solid #ddd; padding: 8px; text-align: left;">Sr. No.</th>
                <th style="background-color: #1e3a8a; color: white; font-weight: bold; border: 1px solid #ddd; padding: 8px; text-align: left;">Date</th>
                <th style="background-color: #1e3a8a; color: white; font-weight: bold; border: 1px solid #ddd; padding: 8px; text-align: left;">Time</th>
                <th style="background-color: #1e3a8a; color: white; font-weight: bold; border: 1px solid #ddd; padding: 8px; text-align: left;">Performed By</th>
                <th style="background-color: #1e3a8a; color: white; font-weight: bold; border: 1px solid #ddd; padding: 8px; text-align: left;">Change details</th>
                <th style="background-color: #1e3a8a; color: white; font-weight: bold; border: 1px solid #ddd; padding: 8px; text-align: left;">Attachment / PDF Link</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $item)
            @php
                $pdfUrl = null;
                $docTitle = '';
                if (!empty($item['is_quotation_log'])) {
                    $pdfUrl = route('quotation.pdf', $item['enc_quotation_id']);
                    $docTitle = 'Quotation ' . ($item['parsed_payload']['quotation_no'] ?? '');
                } elseif (!empty($item['is_performa_log'])) {
                    $pdfUrl = route('performa_invoice.pdf', $item['enc_performa_id']);
                    $docTitle = 'Performa Invoice ' . ($item['parsed_payload']['invoice_no'] ?? '');
                } elseif (!empty($item['is_main_invoice_log'])) {
                    $pdfUrl = route('main_invoice.pdf', $item['enc_main_id']);
                    $docTitle = 'Main Invoice ' . ($item['parsed_payload']['invoice_no'] ?? '');
                }
                $p = $item['parsed_payload'];
            @endphp
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; white-space: nowrap;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; white-space: nowrap;">{{ date('d-m-Y', strtotime($item['date_str'])) }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; white-space: nowrap;">{{ $item['time'] }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top;">{{ $item['user'] }}</td>
                
                <!-- Column 5: Change details -->
                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top;">
                    <strong>{{ $item['title'] }}</strong> - 
                    @if(!empty($item['file_url']))
                        @if(preg_match('/(https?:\/\/[^\s\']+)/', $item['description']))
                            @php
                                $parts = explode($item['file_url'], $item['description'], 2);
                                $prefix = $parts[0] ?? 'Added link attachment: ';
                            @endphp
                            {{ $prefix }}
                        @else
                            {{ $item['description'] }}
                        @endif
                    @else
                        {{ $item['description'] }}
                    @endif

                    @if($p)
                        @if(isset($p['quotation_no']) || isset($p['invoice_no']))
                            <br><strong>Document Details:</strong>
                            <br>&nbsp;&nbsp;• Document No: {{ $p['quotation_no'] ?? $p['invoice_no'] ?? 'N/A' }}
                            <br>&nbsp;&nbsp;• Grand Total: {{ $p['currency'] ?? '₹' }} {{ number_format(floatval($p['grand_total'] ?? 0), 2) }}
                            @if(!empty($p['customer_name'])) <br>&nbsp;&nbsp;• Customer: {{ $p['customer_name'] }} @endif
                            @if(!empty($p['email'])) <br>&nbsp;&nbsp;• Email: {{ $p['email'] }} @endif
                            @if(!empty($p['quotation_type']) || !empty($p['invoice_type'])) <br>&nbsp;&nbsp;• Type: {{ $p['quotation_type'] ?? $p['invoice_type'] ?? 'N/A' }} @endif
                            @if(!empty($p['quotation_date']) || !empty($p['invoice_date'])) <br>&nbsp;&nbsp;• Date: {{ $p['quotation_date'] ?? $p['invoice_date'] }} @endif
                        @elseif(isset($p['items_created']) || isset($p['items_updated']) || isset($p['items_deleted']))
                            <br><strong>Item Changes:</strong>
                            @if(isset($p['items_created']) && count($p['items_created']) > 0)
                                <br>&nbsp;&nbsp;<em>Added Items ({{ count($p['items_created']) }}):</em>
                                @foreach($p['items_created'] as $i)
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- Product ID: {{ $i['product_id'] }} (Qty: {{ $i['qty'] }} @ {{ $i['unit_price'] }} = {{ $p['currency'] ?? '₹' }}{{ number_format(floatval($i['total'] ?? 0), 2) }})
                                @endforeach
                            @endif
                            @if(isset($p['items_updated']) && count($p['items_updated']) > 0)
                                <br>&nbsp;&nbsp;<em>Updated Items ({{ count($p['items_updated']) }}):</em>
                                @foreach($p['items_updated'] as $i)
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- Product ID: {{ $i['product_id'] }} (Qty: {{ $i['qty'] }} @ {{ $i['unit_price'] }} = {{ $p['currency'] ?? '₹' }}{{ number_format(floatval($i['total'] ?? 0), 2) }})
                                @endforeach
                            @endif
                            @if(isset($p['items_deleted']) && count($p['items_deleted']) > 0)
                                <br>&nbsp;&nbsp;<em>Removed Items ({{ count($p['items_deleted']) }}):</em>
                                @foreach($p['items_deleted'] as $i)
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;- Item ID: {{ $i['item_id'] }} removed
                                @endforeach
                            @endif
                        @elseif(isset($p['before']) || isset($p['after']))
                            <br><strong>Value Changes:</strong>
                            @if(isset($p['before']) && is_array($p['before']) && count($p['before']) > 0)
                                <br>&nbsp;&nbsp;<em>Before:</em>
                                @foreach($p['before'] as $bk => $bv)
                                     <br>&nbsp;&nbsp;&nbsp;&nbsp;- {{ ucwords(str_replace('_', ' ', $bk)) }}: {{ is_array($bv) ? json_encode($bv) : ($bk === 'terms_payment' ? $resolveTerms($bv) : (string)$bv) }}
                                @endforeach
                            @endif
                            @if(isset($p['after']) && is_array($p['after']) && count($p['after']) > 0)
                                <br>&nbsp;&nbsp;<em>After:</em>
                                @foreach($p['after'] as $ak => $av)
                                     <br>&nbsp;&nbsp;&nbsp;&nbsp;- {{ ucwords(str_replace('_', ' ', $ak)) }}: {{ is_array($av) ? json_encode($av) : ($ak === 'terms_payment' ? $resolveTerms($av) : (string)$av) }}
                                @endforeach
                            @endif
                        @elseif(is_array($p))
                            <br><strong>Details:</strong>
                            @foreach($p as $k => $v)
                                 <br>&nbsp;&nbsp;• {{ ucwords(str_replace('_', ' ', $k)) }}: {{ is_array($v) ? json_encode($v) : ($k === 'terms_payment' ? $resolveTerms($v) : (string)$v) }}
                            @endforeach
                        @endif
                    @endif
                </td>
                
                <!-- Column 6: Attachment / PDF Link -->
                <td style="border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top;">
                    @php $hasLink = false; @endphp

                    @if(!empty($item['file_url']))
                        @php $hasLink = true; @endphp
                        <a href="{{ $item['file_url'] }}" target="_blank" style="color: #1e3a8a; font-weight: bold; text-decoration: underline;">
                            Open Attachment
                        </a>
                    @endif

                    @if($pdfUrl)
                        @if($hasLink) <br> @endif
                        @php $hasLink = true; @endphp
                        <a href="{{ $pdfUrl }}" target="_blank" style="color: #d9534f; font-weight: bold; text-decoration: underline;">
                            View {{ $docTitle }} (PDF)
                        </a>
                    @endif

                    @if($p && !empty($p['attachments']))
                        @php
                            $attList = is_array($p['attachments']) ? $p['attachments'] : array_filter(explode(',', $p['attachments']));
                        @endphp
                        @foreach($attList as $attFile)
                            @php
                                $attTrim = is_array($attFile) ? ($attFile['file_name'] ?? '') : trim($attFile);
                                $attUrl = is_array($attFile) ? ($attFile['file_url'] ?? '#') : asset('assets/uploads/quotations/attachments/' . $attTrim);
                            @endphp
                            @if(!empty($attTrim))
                                @if($hasLink) <br> @endif
                                @php $hasLink = true; @endphp
                                <a href="{{ $attUrl }}" target="_blank" style="color: #337ab7; text-decoration: underline;">
                                    Open Attachment: {{ strlen($attTrim) > 20 ? substr($attTrim, 0, 20) . '...' : $attTrim }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>