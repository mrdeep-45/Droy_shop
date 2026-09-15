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
                                    <button type="button" class="btn btn-success btn-sm fw-bold shadow-sm" id="exportExcelBtn">
                                        <i class="fas fa-file-excel me-1"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm" id="exportPdfBtn">
                                        <i class="fas fa-file-pdf me-1"></i> PDF
                                    </button>
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
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold small text-dark">Date Range</label>
                                                    <input type="text" class="form-control bg-white" id="filter_date_range" name="filter_date_range" placeholder="Select Date Range">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold small text-dark">Lead Status</label>
                                                    <select class="form-control select2" id="filter_lead_status" name="filter_lead_status" data-placeholder="All Statuses">
                                                        <option value=""></option>
                                                        @foreach ($lead_status as $status)
                                                            <option value="{{ $status->status_id }}">{{ $status->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold small text-dark">Lead Source</label>
                                                    <select class="form-control select2" id="filter_lead_source" name="filter_lead_source" data-placeholder="All Sources">
                                                        <option value=""></option>
                                                        @foreach ($source as $src)
                                                            <option value="{{ $src->source_id }}">{{ $src->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold small text-dark">Lead Type</label>
                                                    <select class="form-control select2" id="filter_lead_type" name="filter_lead_type" data-placeholder="All Types">
                                                        <option value=""></option>
                                                        <option value="Domestic">Domestic</option>
                                                        <option value="Export">Export</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold small text-dark">Industry Type</label>
                                                    <select class="form-control select2" id="filter_industry_id" name="filter_industry_id" data-placeholder="All Industries">
                                                        <option value=""></option>
                                                        @foreach ($industryTypes as $industry)
                                                            <option value="{{ $industry->industry_type_id }}">{{ $industry->industry_type_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @if(session('login_type') === 'Company')
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold small text-dark">Assigned To</label>
                                                    <select class="form-control select2" id="filter_assigned_to" name="filter_assigned_to" data-placeholder="All Staff">
                                                        <option value=""></option>
                                                        @foreach ($staffList as $staff)
                                                            <option value="{{ $staff->staff_id }}">{{ $staff->staff_name }}  ({{ ucfirst($staff->staff_type) }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold small text-dark">Created By</label>
                                                    <select class="form-control select2" id="filter_created_by" name="filter_created_by" data-placeholder="All Creators">
                                                        <option value=""></option>
                                                        @foreach ($creators as $creator)
                                                            <option value="{{ $creator->id }}">{{ $creator->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endif
                                                <div class="col-md-1 d-flex align-items-end gap-2">
                                                    <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters" title="Reset Filters">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="lead_report_table" class="table table-bordered crud-table w-100">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Lead Details</th>
                                                <th>Company Details</th>
                                                <th>Contact Details</th>
                                                <th>Products</th>
                                                <th>Status &amp; Source</th>
                                                @if(session('login_type') === 'Company')
                                                <th>Owner Details</th>
                                                @endif
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
            // Initialize Select2 dropdowns
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
            let table = $('#lead_report_table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('lead_report.list') }}",
                    data: function (d) {
                        d.lead_status = $('#filter_lead_status').val();
                        d.lead_source = $('#filter_lead_source').val();
                        d.assigned_to = $('#filter_assigned_to').val();
                        d.created_by = $('#filter_created_by').val();
                        d.lead_type = $('#filter_lead_type').val();
                        d.industry_id = $('#filter_industry_id').val();
                        // Parse range dates
                        let dates = $('#filter_date_range').val().split(' to ');
                        if (dates.length === 2) {
                            d.start_date = dates[0];
                            d.end_date = dates[1];
                        }
                    }
                },
                order: [[1, "desc"]],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'lead_details', name: 'enquiry_name', orderable: true },
                    { data: 'company_details', name: 'company_name', orderable: true },
                    { data: 'contact_details', name: 'contact_person_name', orderable: true },
                    { data: 'product_details', name: 'products.prod_name', orderable: false, searchable: false },
                    { data: 'status_source', name: 'leadStatus.title', orderable: false, searchable: false },
                    @if(session('login_type') === 'Company')
                    { data: 'owner_details', name: 'created_by', orderable: true },
                    @endif
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
                drawCallback: function () {
                    // Re-initialize Bootstrap tooltips for dynamic items
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            // Reload table on dropdown change
            $('#filter_lead_status, #filter_lead_source, #filter_assigned_to, #filter_created_by, #filter_lead_type, #filter_industry_id').on('change', function () {
                table.ajax.reload();
            });

            // Reset filters
            $('#resetFilters').click(function () {
                $('#filterForm')[0].reset();
                $('.select2').val('').trigger('change');
                fpRange.clear();
                table.ajax.reload();
            });

            // Export to Excel
            $('#exportExcelBtn').click(function () {
                let params = $.param(getFilterData());
                window.location.href = "{{ route('lead_report.export.excel') }}?" + params;
            });

            // Export to PDF
            $('#exportPdfBtn').click(function () {
                let params = $.param(getFilterData());
                window.open("{{ route('lead_report.export.pdf') }}?" + params, '_blank');
            });

            function getFilterData() {
                let data = {
                    lead_status: $('#filter_lead_status').val(),
                    lead_source: $('#filter_lead_source').val(),
                    assigned_to: $('#filter_assigned_to').val(),
                    created_by: $('#filter_created_by').val(),
                    lead_type: $('#filter_lead_type').val(),
                    industry_id: $('#filter_industry_id').val()
                };
                let dates = $('#filter_date_range').val().split(' to ');
                if (dates.length === 2) {
                    data.start_date = dates[0];
                    data.end_date = dates[1];
                }
                return data;
            }
        });
    </script>
    @include('components.lead-timeline-modal')
    <script src="{{ asset('assets/admin-assets/js/leadTimeline.js') }}"></script>
</body>

</html>
