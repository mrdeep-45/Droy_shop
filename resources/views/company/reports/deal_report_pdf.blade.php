<!DOCTYPE html>
<html>
<head>
    <title>Deals Report</title>
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
                            <h2 style="margin:0; text-align:left;">{{ $companyDetails ? $companyDetails->company_name : 'CRM Deals' }}</h2>
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

    <h2>Deals Report</h2>
    
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
                <th>Deal Details</th>
                <th>Company Details</th>
                <th>Contact Details</th>
                <th>Amount</th>
                <th>Closing Date</th>
                <th>Deal Stage</th>
                <th>Owner Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <b>{{ $row->deal_name }}</b><br>
                    ID: #{{ $row->deal_id }}
                </td>
                <td>
                    <b>{{ $row->customer ? $row->customer->company_name : '-' }}</b><br>
                    {{ ($row->customer && $row->customer->cityRelation) ? $row->customer->cityRelation->city_name : 'N/A' }}
                </td>
                <td>
                    @php
                        $person = $row->customer ? $row->customer->contact_person_name : '';
                        $phone = $row->customer ? $row->customer->contact_no : '';
                        $email = $row->customer ? $row->customer->email : '';
                    @endphp
                    @if(!empty($person))
                        <b>{{ $person }}</b><br>
                    @endif
                    @if(!empty($phone))
                        {{ $phone }}<br>
                    @endif
                    @if(!empty($email))
                        {{ $email }}
                    @endif
                </td>
                <td>
                    <b>₹ {{ number_format($row->amount, 2) }}</b>
                </td>
                <td>
                    {{ $row->closing_date ? date('d-M-Y', strtotime($row->closing_date)) : '-' }}
                </td>
                <td>
                    {{ $row->dealStage ? $row->dealStage->stage_name : '-' }}
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
