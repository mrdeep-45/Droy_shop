<!DOCTYPE html>
<html>
<head>
    <title>Campaign Report</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; margin: 0; padding: 10px; color: #333; }
        h2 { text-align: center; font-size: 18px; margin-bottom: 20px; color: #0d6efd; text-transform: uppercase; }
        .header-container { width: 100%; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; margin-bottom: 20px; }
        .header-table { width: 100%; border: none; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        .logo { max-height: 80px; max-width: 200px; }
        .company-info { text-align: right; line-height: 1.4; }
        .company-name { font-size: 18px; font-weight: bold; color: #0d6efd; margin-bottom: 4px; }
        .filter-badges { margin-bottom: 15px; }
        .badge { display: inline-block; background-color: #f4f4f4; border: 1px solid #ddd; padding: 4px 8px; border-radius: 4px; margin-right: 5px; margin-bottom: 5px; font-size: 10px; }
        .badge strong { color: #0d6efd; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #0d6efd; color: white; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .data-table tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
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
                            <h2 style="margin:0; text-align:left;">{{ $companyDetails ? $companyDetails->company_name : 'CRM Campaigns' }}</h2>
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

    <h2>Campaign Report</h2>
    
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
                <th>Campaign Details</th>
                <th>Campaign Type</th>
                <th>Timeline / Schedule</th>
                <th>Expected Budget</th>
                <th>Actual Cost</th>
                <th>Venue & Location</th>
                <th>Visitors</th>
                <th>Owner / Creator</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><b>{{ $row->campaign_name }}</b></td>
                <td>{{ $row->campaignType ? $row->campaignType->campaign_type_name : 'N/A' }}</td>
                <td>
                    Start: {{ $row->start_date ? date('d-M-Y', strtotime($row->start_date)) : 'N/A' }}<br>
                    End: {{ $row->end_date ? date('d-M-Y', strtotime($row->end_date)) : 'N/A' }}
                </td>
                <td>{{ $row->expected_budget ? number_format($row->expected_budget, 2) : '0.00' }}</td>
                <td>{{ $row->actual_cost ? number_format($row->actual_cost, 2) : '0.00' }}</td>
                <td>{{ $row->venue_location ?: '-' }} (Stall: {{ $row->stall_number ?: '-' }})</td>
                <td>{{ $row->num_visitors ? number_format($row->num_visitors) : '0' }}</td>
                <td>
                    Assigned: {{ $row->assignedStaff ? $row->assignedStaff->staff_name : 'N/A' }}<br>
                    Created By: {{ get_createdby_name($row->created_by) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>