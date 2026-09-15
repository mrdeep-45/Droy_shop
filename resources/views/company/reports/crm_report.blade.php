<!DOCTYPE html>
<html {!! get_html_data() !!}>
<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        .flatpickr-wrapper {
            display: block !important;
            width: 100% !important;
        }
        /* Custom Premium Table Styling */
        #crm_report_table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        #crm_report_table thead th {
            background-color: #064e5a !important; /* Premium Dark Slate */
            color: #f8fafc !important;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 8px 12px;
            border-bottom: 2px solid #1e293b;
        }
        #crm_report_table tbody tr {
            transition: background-color 0.2s ease;
        }
        #crm_report_table tbody tr:hover {
            background-color: #f8fafc;
        }
        #crm_report_table tbody td {
            padding: 6px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        
        /* Soft Badges */
        .badge-soft-warning {
            background-color: #fef3c7;
            color: #d97706;
            font-size: 10px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .badge-soft-success {
            background-color: #d1fae5;
            color: #059669;
            font-size: 10px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }
        .badge-soft-info {
            background-color: #e0f2fe;
            color: #0284c7;
            font-size: 10px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        /* Pipeline Track & Steps */
        .pipeline-track {
            min-height: 48px;
        }
        .pipeline-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 3;
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .pipeline-circle:hover {
            transform: scale(1.2) translateY(-1px);
        }
        
        /* Premium Glow Rings for Active Steps */
        .circle-lead.active {
            background-color: #e28509 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(226, 133, 9, 0.18);
        }
        .circle-deal.active {
            background-color: #2563eb !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
        }
        .circle-quot.active {
            background-color: #5850ec !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(88, 80, 236, 0.18);
        }
        .circle-perf.active {
            background-color: #10b981 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18);
        }
        .circle-cust.active {
            background-color: #8b5cf6 !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.18);
        }
        .pipeline-circle.active i {
            color: #ffffff !important;
        }
        
        /* Pulse Animation Keyframes */
        @keyframes pulse-lead {
            0% { box-shadow: 0 0 0 0px rgba(226, 133, 9, 0.6); }
            100% { box-shadow: 0 0 0 10px rgba(226, 133, 9, 0); }
        }
        @keyframes pulse-deal {
            0% { box-shadow: 0 0 0 0px rgba(37, 99, 235, 0.6); }
            100% { box-shadow: 0 0 0 10px rgba(37, 99, 235, 0); }
        }
        @keyframes pulse-quot {
            0% { box-shadow: 0 0 0 0px rgba(88, 80, 236, 0.6); }
            100% { box-shadow: 0 0 0 10px rgba(88, 80, 236, 0); }
        }
        @keyframes pulse-perf {
            0% { box-shadow: 0 0 0 0px rgba(16, 185, 129, 0.6); }
            100% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        }
        @keyframes pulse-cust {
            0% { box-shadow: 0 0 0 0px rgba(139, 92, 246, 0.6); }
            100% { box-shadow: 0 0 0 10px rgba(139, 92, 246, 0); }
        }

        /* Pulse Classes */
        .latest-lead {
            animation: pulse-lead 1.6s infinite;
        }
        .latest-deal {
            animation: pulse-deal 1.6s infinite;
        }
        .latest-quot {
            animation: pulse-quot 1.6s infinite;
        }
        .latest-perf {
            animation: pulse-perf 1.6s infinite;
        }
        .latest-cust {
            animation: pulse-cust 1.6s infinite;
        }
        
        /* Muted Steps styling */
        .pipeline-circle.bg-light {
            background-color: #f8fafc !important;
            color: #94a3b8 !important;
            border: 1px solid #cbd5e1 !important;
            cursor: not-allowed;
            pointer-events: none;
        }
        .pipeline-circle.bg-light:hover {
            transform: none;
            box-shadow: none !important;
        }
        
        /* Label styles */
        .pipeline-label {
            font-size: 10px;
            font-weight: 500;
            margin-top: 5px;
            display: block;
            transition: color 0.3s ease;
            color: #64748b;
        }
        .pipeline-label.active-lead { color: #e28509; font-weight: 600; }
        .pipeline-label.active-deal { color: #2563eb; font-weight: 600; }
        .pipeline-label.active-quot { color: #5850ec; font-weight: 600; }
        .pipeline-label.active-perf { color: #10b981; font-weight: 600; }
        .pipeline-label.active-cust { color: #8b5cf6; font-weight: 600; }
    </style>
</head>
<body>
    @include('include.switcher')
    @include('include.loader')
    <div class="page">
        @include('include.header')
        <div class="page-header-breadcrumb d-md-flex d-block align-items-center justify-content-between d-none"></div>
        <div class="main-content app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="card-title text-primary mb-0">
                                    {{ $page_name }}
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button class="btn btn-info btn-sm fw-bold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                                        <i class="fas fa-filter me-1"></i> Filters
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="collapse mb-4" id="advancedFilters">
                                    <div class="p-3 bg-light rounded shadow-sm border">
                                        <form id="filterForm">
                                            <div class="row g-3">
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold small text-dark">Enquiry Date Range</label>
                                                    <input type="text" class="form-control bg-white" id="filter_date_range" name="filter_date_range" placeholder="Select Date Range">
                                                </div>
                                                @if(session('login_type') === 'Company')
                                                <div class="col-md-5">
                                                    <label class="form-label fw-bold small text-dark">Assigned To</label>
                                                    <select class="form-control select2" id="filter_assigned_to" name="filter_assigned_to" data-placeholder="All Staff">
                                                        <option value=""></option>
                                                        @foreach ($staffList as $staff)
                                                            <option value="{{ $staff->staff_id }}">{{ $staff->staff_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endif
                                                <div class="col-md-2 d-flex align-items-end gap-2">
                                                    <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters" title="Reset Filters">
                                                        <i class="fas fa-undo"></i> Reset
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="crm_report_table" class="table table-bordered crud-table w-100">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Enquiry / Customer</th>
                                                <th class="text-center" style="min-width: 410px;">Pipeline Status</th>
                                                <th>Value</th>
                                                <th>Lead Date</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('include.footer')
    </div>
    @include('include.scroll-top')
    @include('include.footer_link')
    @include('include.datatable_js_link')

    <script>
        $(document).ready(function () {
            // Initialize Select2 dropdown
            $('.select2').select2({
                width: '100%',
                allowClear: true,
                placeholder: function() {
                    return $(this).data('placeholder');
                }
            });

            // Initialize Date Picker Range
            let fpRange = flatpickr("#filter_date_range", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d-M-Y",
                onClose: function(selectedDates, dateStr, instance) {
                    table.ajax.reload();
                }
            });

            // Initialize DataTable
            let table = $('#crm_report_table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('crm_report.list') }}",
                    data: function (d) {
                        d.assigned_to = $('#filter_assigned_to').val();
                        
                        // Parse range dates
                        let dates = $('#filter_date_range').val().split(' to ');
                        if (dates.length === 2) {
                            d.start_date = dates[0];
                            d.end_date = dates[1];
                        }
                    }
                },
                order: [[4, "desc"]], // Sort by Date column (created date) descending
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'enquiry_customer', name: 'company_name' },
                    { data: 'pipeline_status', name: 'enquiry_id', searchable: false, className: 'text-center' },
                    { data: 'value', name: 'enquiry_id',  searchable: false },
                    { data: 'date', name: 'enquiry_date' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
                drawCallback: function () {
                    // Re-initialize Bootstrap tooltips for dynamic items
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            // Reload table on dropdown change
            $('#filter_assigned_to').on('change', function () {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').click(function () {
                $('#filterForm')[0].reset();
                $('.select2').val('').trigger('change');
                fpRange.clear();
                table.ajax.reload();
            });
        });
    </script>
</body>
</html>
