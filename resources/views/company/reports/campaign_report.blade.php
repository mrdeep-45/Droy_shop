<!DOCTYPE html>
<html {!! get_html_data() !!}>
<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        .flatpickr-wrapper { display: block !important; width: 100% !important; }
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
                                <div class="card-title text-primary mb-0">{{ $page_name }}</div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-success btn-sm fw-bold shadow-sm" id="exportExcelBtn">
                                        <i class="fas fa-file-excel me-1"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm" id="exportPdfBtn">
                                        <i class="fas fa-file-pdf me-1"></i> PDF
                                    </button>
                                    <button class="btn btn-info btn-sm fw-bold shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
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
                                                    <label class="form-label fw-bold small text-dark">Campaign Duration Range</label>
                                                    <input type="text" class="form-control bg-white" id="filter_date_range" placeholder="Select Date Range">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold small text-dark">Campaign Type</label>
                                                    <select class="form-control select2" id="filter_campaign_type" data-placeholder="All Types">
                                                        <option value=""></option>
                                                        @foreach ($campaignTypes as $type)
                                                            <option value="{{ $type->campaign_type_id }}">{{ $type->campaign_type_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold small text-dark">Venue / Location Search</label>
                                                    <input type="text" class="form-control" id="filter_venue" placeholder="e.g. Pragati Maidan">
                                                </div>
                                                @if(session('login_type') === 'Company')
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold small text-dark">Owner (Assigned To)</label>
                                                    <select class="form-control select2" id="filter_assigned_to" data-placeholder="All Staff">
                                                        <option value=""></option>
                                                        @foreach ($staffList as $staff)
                                                            <option value="{{ $staff->staff_id }}">{{ $staff->staff_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold small text-dark">Created By</label>
                                                    <select class="form-control select2" id="filter_created_by" data-placeholder="All Creators">
                                                        <option value=""></option>
                                                        @foreach ($creators as $creator)
                                                            <option value="{{ $creator->id }}">{{ $creator->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endif
                                                <div class="col-md-1 d-flex align-items-end justify-content-end">
                                                    <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters" title="Reset Filters">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="campaign_report_table" class="table table-bordered w-100">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Campaign Details</th>
                                                <th>Schedule & Timeline</th>
                                                <th>Financial Details</th>
                                                <th>Venue & Visitors</th>
                                                @if(session('login_type') === 'Company')
                                                <th>Ownership Details</th>
                                                @endif
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
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

    <!-- Timeline History Modal Shared Component Container Integration -->
    <div class="modal fade" id="timelineHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">Campaign Action Log History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light-subtle" style="max-height: 450px; overflow-y: auto;">
                    <div id="timelineContentContainer"></div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include('include.scroll-top')
    @include('include.footer_link')
    @include('include.datatable_js_link')

    <script>
        $(document).ready(function () {
            $('.select2').select2({ width: '100%', allowClear: true, placeholder: function() { return $(this).data('placeholder'); } });

            let fpRange = flatpickr("#filter_date_range", {
                mode: "range", dateFormat: "Y-m-d", altInput: true, altFormat: "d-M-Y",
                onClose: function() { table.ajax.reload(); }
            });

            let table = $('#campaign_report_table').DataTable({
                processing: true, serverSide: true, responsive: true,
                ajax: {
                    url: "{{ route('campaign_report.list') }}",
                    data: function (d) {
                        d.campaign_type_id = $('#filter_campaign_type').val();
                        d.assigned_to = $('#filter_assigned_to').val();
                        d.created_by = $('#filter_created_by').val();
                        d.venue_location = $('#filter_venue').val();
                        let dates = $('#filter_date_range').val().split(' to ');
                        if (dates.length === 2) { d.start_date = dates[0]; d.end_date = dates[1]; }
                    }
                },
                order: [[1, "desc"]],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'campaign_details', name: 'campaign_name' , orderable: true },
                    { data: 'schedule_details', name: 'start_date', orderable: true },
                    { data: 'financial_details', name: 'expected_budget', orderable: true },
                    { data: 'exhibition_details', name: 'venue_location', orderable: true },
                    @if(session('login_type') === 'Company')
                    { data: 'owner_details', name: 'created_by', orderable: true },
                    @endif
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });

            $('#filter_campaign_type, #filter_assigned_to, #filter_created_by').on('change', function () { table.ajax.reload(); });
            $('#filter_venue').on('keyup', function () { table.ajax.reload(); });

            $('#resetFilters').click(function () {
                $('#filterForm')[0].reset();
                $('.select2').val('').trigger('change');
                fpRange.clear();
                table.ajax.reload();
            });

            $('#exportExcelBtn').click(function () { window.location.href = "{{ route('campaign_report.export.excel') }}?" + $.param(getFilterData()); });
            $('#exportPdfBtn').click(function () { window.open("{{ route('campaign_report.export.pdf') }}?" + $.param(getFilterData()), '_blank'); });

            function getFilterData() {
                let data = {
                    campaign_type_id: $('#filter_campaign_type').val(),
                    assigned_to: $('#filter_assigned_to').val(),
                    created_by: $('#filter_created_by').val(),
                    venue_location: $('#filter_venue').val()
                };
                let dates = $('#filter_date_range').val().split(' to ');
                if (dates.length === 2) { data.start_date = dates[0]; data.end_date = dates[1]; }
                return data;
            }

            // History Log Ajax Action Handler
            $(document).on('click', '.view-history', function () {
                const id = $(this).data('id');
                $('#timelineContentContainer').html('<div class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span> Loading logs...</div>');
                $('#timelineHistoryModal').modal('show');
                $.ajax({
                    url: "{{ route('campaign.history', ':id') }}".replace(':id', id),
                    type: "GET",
                    success: function (res) {
                        if (res.status === 'success' && res.data.length > 0) {
                            let html = '';
                            res.data.forEach(log => {
                                html += `
                                    <div class="card mb-2 border-0 shadow-sm">
                                        <div class="card-body p-3">
                                            <span class="badge bg-secondary mb-1">${log.date} ${log.time}</span>
                                            <p class="mb-1 text-dark"><strong>${log.action}</strong> - ${log.description}</p>
                                            <small class="text-muted">By: ${log.user}</small>
                                        </div>
                                    </div>`;
                            });
                            $('#timelineContentContainer').html(html);
                        } else {
                            $('#timelineContentContainer').html('<div class="text-center py-4 text-muted">No modifications tracked.</div>');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>