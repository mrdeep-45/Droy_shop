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
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold small text-dark">Closing Date Range</label>
                                                    <input type="text" class="form-control bg-white" id="filter_date_range" name="filter_date_range" placeholder="Select Closing Date Range">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold small text-dark">Deal Stage</label>
                                                    <select class="form-control select2" id="filter_deal_stage" name="filter_deal_stage" data-placeholder="All Stages">
                                                        <option value=""></option>
                                                        @foreach ($stages as $stage)
                                                            <option value="{{ $stage->id }}">{{ $stage->stage_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @if(session('login_type') === 'Company')
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold small text-dark">Assigned To</label>
                                                    <select class="form-control select2" id="filter_assigned_to" name="filter_assigned_to" data-placeholder="All Staff">
                                                        <option value=""></option>
                                                        @foreach ($staffList as $staff)
                                                            <option value="{{ $staff->staff_id }}">{{ $staff->staff_name }}  ({{ ucfirst($staff->staff_type) }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endif
                                                <div class="col-md-2 d-flex align-items-end gap-2">
                                                    <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters" title="Reset Filters">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="deal_report_table" class="table table-bordered crud-table w-100">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Deal Details</th>
                                                <th>Company Details</th>
                                                <th>Contact Details</th>
                                                <th>Amount</th>
                                                <th>Closing Date</th>
                                                <th>Deal Stage</th>
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
            let table = $('#deal_report_table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('deal_report.list') }}",
                    data: function (d) {
                        d.deal_stage = $('#filter_deal_stage').val();
                        d.assigned_to = $('#filter_assigned_to').val();
                        
                        // Parse range dates
                        let dates = $('#filter_date_range').val().split(' to ');
                        if (dates.length === 2) {
                            d.start_date = dates[0];
                            d.end_date = dates[1];
                        }
                    }
                },
                order: [[5, "desc"]],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'deal_details', name: 'deal_name', orderable: true },
                    { data: 'company_details', name: 'customer_id', orderable: true },
                    { data: 'contact_details', name: 'customer_id', orderable: false, searchable: false },
                    { data: 'amount', name: 'amount', orderable: true },
                    { data: 'closing_date', name: 'closing_date', orderable: true },
                    { data: 'deal_stage', name: 'deal_stage', orderable: true },
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
            $('#filter_deal_stage, #filter_assigned_to').on('change', function () {
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
                window.location.href = "{{ route('deal_report.export.excel') }}?" + params;
            });

            // Export to PDF
            $('#exportPdfBtn').click(function () {
                let params = $.param(getFilterData());
                window.open("{{ route('deal_report.export.pdf') }}?" + params, '_blank');
            });

            function getFilterData() {
                let data = {
                    deal_stage: $('#filter_deal_stage').val(),
                    assigned_to: $('#filter_assigned_to').val()
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
</body>

</html>
