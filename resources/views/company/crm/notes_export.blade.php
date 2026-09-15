<!DOCTYPE html>
<html>
<head>
    <title>Notes Report</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; margin: 0; padding: 10px; color: #333; }
        h2 { text-align: center; font-size: 18px; margin-bottom: 20px; color: #1e3a8a; text-transform: uppercase; }
        .header-container { width: 100%; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; }
        .header-table { width: 100%; border: none; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        .logo { max-height: 80px; max-width: 200px; }
        .company-info { text-align: right; line-height: 1.4; }
        .company-name { font-size: 18px; font-weight: bold; color: #1e3a8a; margin-bottom: 4px; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #1e3a8a; color: white; font-weight: bold; text-transform: uppercase; font-size: 10px; }
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
                            <h2 style="margin:0; text-align:left;">{{ $companyDetails ? $companyDetails->company_name : 'CRM' }}</h2>
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

    <h2>Notes Report - {{ ucfirst($referenceType) }}: {{ $entityName }}</h2>

    @if(isset($entity))
        <table class="data-table" style="margin-bottom: 20px;">
            <tbody>
                @if($referenceType === 'lead')
                <tr>
                    <th>Lead No</th>
                    <td>{{ $entity->enquiry_no ?? '-' }}</td>
                    <th>Lead Name</th>
                    <td>{{ $entity->enquiry_name ?? $entity->lead_name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Customer</th>
                    <td>{{ $entity->customer_name ?? '-' }}</td>
                    <th>Company</th>
                    <td>{{ $entity->company_name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Contact Person</th>
                    <td>{{ $entity->contact_person_name ?? '-' }}</td>
                    <th>Mobile</th>
                    <td>{{ $entity->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $entity->email ?? '-' }}</td>
                    <th>Assigned Staff</th>
                    <td>{{ $entity->assigned_to ? get_createdby_name($entity->assigned_to) : '-' }}</td>
                </tr>
                @elseif($referenceType === 'deal')
                <tr>
                    <th>Deal Name</th>
                    <td>{{ $entity->deal_name ?? '-' }}</td>
                    <th>Company</th>
                    <td>{{ $entity->customer->company_name ?? $entity->lead->company_name ?? '-' }}</td>
                </tr>
              
                <tr>
                    <th>Contact Person</th>
                    <td>{{ $entity->customer->contact_person ?? $entity->lead->contact_person_name ?? '-' }}</td>
                    <th>Mobile</th>
                    <td>{{ $entity->customer->mobile_no ?? $entity->lead->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $entity->customer->email_address ?? $entity->lead->email ?? '-' }}</td>
                    <th>Assigned Staff</th>
                    <td>{{ $entity->assigned_to ? get_createdby_name($entity->assigned_to) : '-' }}</td>
                </tr>
                <tr>
                    <th>Deal Stage</th>
                    <td>{{ $entity->dealStage->stage_name ?? '-' }}</td>
                    <th>Deal Amount</th>
                    <td>{{ $entity->amount ?? '-' }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">Sr. No.</th>
                <th style="width: 15%;">Title</th>
                <th style="width: 10%;">Follow-up Date</th>
                <th style="width: 25%;">Description</th>
                <th style="width: 10%;">Created By</th>
                <th style="width: 10%;">Created Date &amp; Time</th>
                <th style="width: 10%;">Updated By</th>
                <th style="width: 10%;">Updated Date &amp; Time</th>
                <th style="width: 5%;">Attachment</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notes as $index => $note)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $note->title }}</td>
                <td>{{ $note->follow_up_date ? \Carbon\Carbon::parse($note->follow_up_date)->format('d-m-Y') : '-' }}</td>
                <td>{!! nl2br(e($note->note)) !!}</td>
                <td>{{ get_createdby_name($note->created_by) ?: 'System' }}</td>
                <td>{{ \Carbon\Carbon::parse($note->created_at)->format('d-m-Y h:i A') }}</td>
                <td>{{ $note->updated_by ? (get_createdby_name($note->updated_by) ?: 'Unknown') : '-' }}</td>
                <td>{{ $note->updated_by ? \Carbon\Carbon::parse($note->updated_at)->format('d-m-Y h:i A') : '-' }}</td>
                <td>
                    @if($note->attachment)
                        @if(Str::startsWith($note->attachment, 'http'))
                            <a href="{{ $note->attachment }}" target="_blank">View</a>
                        @else
                            <a href="{{ url('assets/uploads/notes/' . $note->attachment) }}" target="_blank">View</a>
                        @endif
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
