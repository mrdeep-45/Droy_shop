<!DOCTYPE html>
<html>
<head>
    <title>Leads Export</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; margin: 0; padding: 10px; color: #333; }
        h2 { text-align: center; font-size: 18px; margin-bottom: 20px; color: #A92323; text-transform: uppercase; }
        .header-container { width: 100%; border-bottom: 2px solid #A92323; padding-bottom: 10px; margin-bottom: 20px; }
        .header-table { width: 100%; border: none; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        .logo { max-height: 80px; max-width: 200px; }
        .company-info { text-align: right; line-height: 1.4; }
        .company-name { font-size: 18px; font-weight: bold; color: #A92323; margin-bottom: 4px; }
        .filter-badges { margin-bottom: 15px; }
        .badge { display: inline-block; background-color: #f4f4f4; border: 1px solid #ddd; padding: 4px 8px; border-radius: 4px; margin-right: 5px; margin-bottom: 5px; font-size: 10px; }
        .badge strong { color: #A92323; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #A92323; color: white; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .data-table tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    @if(isset($is_pdf) && $is_pdf)
        @php
            $companyDetails = get_company_details(1);
            $logoPath = null;
            if($companyDetails && $companyDetails->logo) {
                // Determine if local or needs absolute URL
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
                            <h2 style="margin:0; text-align:left;">{{ $companyDetails ? $companyDetails->company_name : 'CRM Leads' }}</h2>
                        @endif
                    </td>
                    <td class="company-info" style="width: 50%;">
                        <div class="company-name">{{ $companyDetails ? $companyDetails->company_name : 'Company Name' }}</div>
                        @if($companyDetails)
                            @if($companyDetails->email) <div>{{ $companyDetails->email }}</div> @endif
                            @if($companyDetails->phone) <div>{{ $companyDetails->phone }}</div> @endif
                            @if($companyDetails->address) <div>{{ $companyDetails->address }}</div> @endif
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <h2>Leads Report</h2>
    
    @if(isset($filters) && count($filters) > 0)
        <div class="filter-badges">
            @foreach($filters as $key => $val)
                <span class="badge"><strong>{{ $key }}:</strong> {{ $val }}</span>
            @endforeach
        </div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Lead Details</th>
                <th>Company Name</th>
                <th>Contact Details</th>
                <th>Products</th>
                <th>Status &amp; Source</th>
                <th>Owner Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <b>{{ $row->enquiry_name }}</b><br>
                    #{{ $row->enquiry_no }}<br>
                    {{ date('d-M-Y', strtotime($row->enquiry_date)) }}
                </td>
                <td>
                    <b>{{ $row->customer ? $row->customer->company_name : ($row->company_name ?: '-') }}</b><br>
                    {{ $row->city ? $row->city->city_name : 'N/A' }}
                </td>
                <td>
                    @if(!empty($row->contact_person_name))
                        <b>{{ $row->contact_person_name }}</b><br>
                    @endif
                    @if(!empty($row->phone))
                        {{ $row->phone }}<br>
                    @endif
                    @if(!empty($row->email))
                        {{ $row->email }}
                    @endif
                </td>
                <td>
                    @php
                        $products = $row->products->map(function ($item) {
                            return $item->product ? $item->product->prod_name : null;
                        })->filter()->unique()->implode(', ');
                    @endphp
                    {{ $products ?: '-' }}
                </td>
                <td>
                    {{ $row->leadStatus ? $row->leadStatus->title : '-' }}<br>
                    {{ $row->sourceInfo ? $row->sourceInfo->title : '-' }}
                </td>
                <td>
                    @if($row->assignedStaff)
                        <b>Assigned:</b> {{ $row->assignedStaff->staff_name }}<br>
                    @endif
                    <b>Created:</b> {{ get_createdby_name($row->created_by) }}<br>
                    {{ date('d-m-Y h:i A', strtotime($row->created_at)) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
