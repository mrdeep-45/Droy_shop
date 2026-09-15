<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        @media (max-width: 767.98px) {
            #quotationTable {
                display: none !important;
            }
            .quotation-mobile-card {
                border-radius: 10px;
                border: 1px solid #e9ecef;
                border-top: 4px solid #00454f;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                background-color: #fff;
                padding: 12px 14px;
                margin-bottom: 16px;
            }
            .quotation-mobile-card .card-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 4px;
                font-size: 12px;
                color: #333;
            }
            .quotation-mobile-card .card-row:last-child {
                margin-bottom: 0;
            }
            .quotation-mobile-card .label-muted {
                color: #6c757d;
                font-weight: 500;
                font-size: 11px;
            }
            .quotation-mobile-card .value-bold {
                font-weight: 600;
                color: #000;
            }
            .quotation-mobile-card .total-highlight {
                font-weight: 700;
                color: #198754;
                font-size: 13px;
            }
            .quotation-mobile-card .badge-direct {
                background-color: #e0f2fe;
                color: #0369a1;
                font-size: 10px;
                font-weight: 600;
                padding: 3px 8px;
                border-radius: 4px;
                display: inline-block;
            }
            .quotation-mobile-card .btn-custom-action {
                width: 32px;
                height: 32px;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                border-radius: 4px;
                box-sizing: border-box;
                margin: 0 !important;
            }
            .quotation-mobile-card .btn-custom-action i {
                font-size: 13px;
                width: 14px;
                text-align: center;
            }
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
                                <div class="d-flex justify-content-end mb-0">
                                    <button class="btn btn-info btn-sm fw-bold shadow-sm me-2 d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                                        <i class="fas fa-filter"></i><span class="ms-1 d-none d-sm-inline">Advanced Filters</span>
                                    </button>
                                    @if (permissions_check('canCreate'))
                                        <a href="{{ route('direct_quotation.index') }}" class="btn btn-sm btn-primary d-flex align-items-center">
                                            <i class="fas fa-plus"></i><span class="ms-1 d-none d-sm-inline">Create </span><span class="ms-1">Quotation</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                        <div class="tab-pane fade show active" id="lead-list">
                                            
                                            <!-- Inline Filters -->
                                            <div class="collapse mb-3" id="advancedFilters">
                                                <div class="p-3 rounded shadow-sm bg-light border">
                                                    <div class="row g-3">
                                                         <div class="col-lg-3 col-md-4 col-12">
                                                             <label class="form-label fs-13 fw-semibold">Date Range</label>
                                                             <div class="input-group input-group-sm">
                                                                 <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                                 <input type="text" id="filterDateRange" class="form-control bg-white" placeholder="Select Date Range" readonly>
                                                             </div>
                                                         </div>

                                                         <div class="col-lg-3 col-md-4 col-6">
                                                             <label class="form-label fs-13 fw-semibold">Customer</label>
                                                             <select id="filterCustomer" class="form-select form-select-sm select2">
                                                                 <option value="">All Customers</option>
                                                                 @foreach($customers as $customer)
                                                                     <option value="{{ $customer->customer_id }}">{{ $customer->company_name }}</option>
                                                                 @endforeach
                                                             </select>
                                                         </div>

                                                         <div class="col-lg-3 col-md-4 col-6">
                                                             <label class="form-label fs-13 fw-semibold">Quotation No</label>
                                                             <select id="filterQuotationNo" class="form-select form-select-sm select2">
                                                                 <option value="">All Quotations</option>
                                                                 @foreach($quotation_nos as $quotation)
                                                                     <option value="{{ $quotation->quotation_no }}">{{ $quotation->quotation_no }}</option>
                                                                 @endforeach
                                                             </select>
                                                         </div>

                                                         <div class="col-lg-3 col-md-4 col-6">
                                                             <label class="form-label fs-13 fw-semibold">City</label>
                                                             <select id="filterCity" class="form-select form-select-sm select2">
                                                                 <option value="">All Cities</option>
                                                             </select>
                                                         </div>

                                                         <div class="col-lg-3 col-md-4 col-6">
                                                             <label class="form-label fs-13 fw-semibold">State</label>
                                                             <select id="filterState" class="form-select form-select-sm select2">
                                                                 <option value="">All States</option>
                                                             </select>
                                                         </div>

                                                         @if(Session::get('login_type') == 'Company')
                                                         <div class="col-lg-3 col-md-4 col-6">
                                                             <label class="form-label fs-13 fw-semibold">Created By</label>
                                                             <select id="filterCreatedBy" class="form-select form-select-sm select2">
                                                                 <option value="">All Staff</option>
                                                                 @foreach($staffs as $staff)
                                                                     <option value="{{ $staff->staff_id }}">{{ $staff->staff_name }}</option>
                                                                 @endforeach
                                                             </select>
                                                         </div>

                                                         <div class="col-lg-3 col-md-4 col-6">
                                                             <label class="form-label fs-13 fw-semibold">Assigned To</label>
                                                             <select id="filterAssignedTo" class="form-select form-select-sm select2">
                                                                 <option value="">All Staff</option>
                                                                 @foreach($staffs as $staff)
                                                                     <option value="{{ $staff->staff_id }}">{{ $staff->staff_name }}</option>
                                                                 @endforeach
                                                             </select>
                                                         </div>
                                                         @endif

                                                         <div class="col-lg-3 col-md-4 col-12 d-flex align-items-end gap-2">
                                                             <button type="button" id="btnApplyFilter" class="btn btn-dark flex-fill">
                                                                 <i class="fas fa-search me-1"></i> Search
                                                             </button>
                                                             <button type="button" id="btnResetFilter" class="btn btn-outline-secondary flex-fill">
                                                                 <i class="fas fa-redo me-1"></i> Reset
                                                             </button>
                                                         </div>
                                                     </div>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table id="quotationTable" class="table table-bordered" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">#</th>
                                                            <th width="12%">Quotation No</th>
                                                            <th width="13%">Customer</th>
                                                            <th width="10%">Date</th>
                                                            <th width="25%">Products</th>
                                                            <th width="15%">Total</th>
                                                            @if(permissions_check('canUpdate') || permissions_check('canDelete'))
                                                                <th width="20%" class="text-center">Actions</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <div id="quotation_mobile_cards" class="d-md-none mt-2">
                                                 <!-- Dynamic Mobile Cards will be rendered here -->
                                            </div>
                                        </div>
                                    @endif

                                    @if(permissions_check('canCreate'))
                                        <div class="tab-pane text-muted" id="new-lead" role="tabpanel">

                                        </div>
                                    @endif
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
    @include('company.crm.quotation.partials.history')
    @include('company.crm.quotation.partials.mail')
    <script>
        @if(permissions_check('canView'))
        var columns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
            { data: 'quotation_no', name: 'quotation_no', orderable: true },
            { data: 'customer_name', name: 'customer_name', orderable: true },
            { data: 'quotation_date', name: 'quotation_date', orderable: true },
            { data: 'items', name: 'items', orderable: false, searchable: false },
            { data: 'total', name: 'grand_total', orderable: true },
            @if(permissions_check('canUpdate') || permissions_check('canDelete'))
            { data: 'action', name: 'action', orderable: false, searchable: false }
            @endif
        ];

        if ($('#quotationTable').length) {
            var table = $('#quotationTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                 order: [[1, 'desc']],
                ajax: {
                    url: "{{ route('quotation.list') }}",
                    data: function(d) {
                        d.date_range = $('#filterDateRange').val();
                        d.customer_id = $('#filterCustomer').val();
                        d.quotation_no = $('#filterQuotationNo').val();
                        d.state_id = $('#filterState').val();
                        d.city_id = $('#filterCity').val();
                        @if(Session::get('login_type') == 'Company')
                        d.created_by = $('#filterCreatedBy').val();
                        d.assigned_to = $('#filterAssignedTo').val();
                        @endif
                    }
                },
                columns: columns,
                drawCallback: function () {
                    renderMobileCards(this.api());
                }
            });

            // Move mobile cards container inside DataTable wrapper right after the table
            $('#quotationTable').after($('#quotation_mobile_cards'));

            // Initialize static Select2 inline
            $('#filterCustomer, #filterQuotationNo, #filterCreatedBy, #filterAssignedTo').select2({
                width: '100%'
            });

            // Initialize State Select2 with AJAX
           

            // Initialize City Select2 with AJAX
            $('#filterCity').select2({
                width: '100%',
                ajax: {
                    url: "{{ route('customer.city.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Search City...',
                allowClear: true
            });

            // Auto fetch state when city is selected
            $('#filterCity').on('select2:select', function (e) {
                var cityId = e.params.data.id;
                if (cityId) {
                    $.ajax({
                        url: "{{ url('/customer/city-details') }}/" + cityId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response && response.state_id) {
                                // Create the option and append to Select2
                                var option = new Option(response.state_name, response.state_id, true, true);
                                $('#filterState').append(option).trigger('change');
                            }
                        }
                    });
                }
            });

            // Initialize Flatpickr Date Range
            const fpDate = flatpickr("#filterDateRange", {
                mode: "range",
                dateFormat: "d/m/Y",
                disableMobile: true
            });

            // Apply Filters
            $('#btnApplyFilter').on('click', function() {
                table.ajax.reload();
                // Optional: close offcanvas on apply
                // var offcanvasElement = document.getElementById('filterOffcanvas');
                // var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                // offcanvas.hide();
            });

            // Reset Filters
            $('#btnResetFilter').on('click', function() {
                $('#filterCustomer').val('').trigger('change');
                $('#filterQuotationNo').val('').trigger('change');
                $('#filterState').val('').trigger('change');
                $('#filterCity').val('').trigger('change');
                @if(Session::get('login_type') == 'Company')
                $('#filterCreatedBy').val('').trigger('change');
                $('#filterAssignedTo').val('').trigger('change');
                @endif
                fpDate.clear();
                table.ajax.reload();
            });
        }

        function parseHtmlCol(htmlStr, selector, attr = null) {
            if (!htmlStr) return '';
            const $temp = $('<div>' + htmlStr + '</div>');
            const $el = $temp.find(selector);
            if (attr) {
                return $el.attr(attr) || '';
            }
            return $el.text().trim();
        }

        function renderMobileCards(api) {
            const data = api.rows({ page: 'current' }).data();
            const $container = $('#quotation_mobile_cards');
            $container.empty();

            if (data.length === 0) {
                $container.append('<div class="text-center text-muted p-4 bg-white rounded shadow-sm fs-12">No records found.</div>');
                return;
            }

            data.each(function (row) {
                const $quotNoDiv = $('<div>' + row.quotation_no + '</div>');
                const quotationNo = $quotNoDiv.find('.fw-semibold').text().trim() || $quotNoDiv.text().trim();
                const createdBy = $quotNoDiv.find('.fa-user').parent().text().trim();
                const assignedTo = $quotNoDiv.find('.fa-user-tie').parent().text().trim();

                const $custDiv = $('<div>' + row.customer_name + '</div>');
                const isDirect = $custDiv.find('.badge').length > 0;
                const customerName = $custDiv.text().replace('Direct Quotation', '').trim();

                const $totalDiv = $('<div>' + row.total + '</div>');
                const totalAmount = $totalDiv.find('.fw-bold').text().trim();

                let subtotalVal = '';
                let gstVal = '';
                let grandTotalVal = '';

                try {
                    const textMutedHtml = $totalDiv.find('.text-muted').html();
                    if (textMutedHtml) {
                        const parts = textMutedHtml.split('<br>');
                        subtotalVal = parts[0] ? parts[0].replace(/Subtotal\s*:/i, '').trim() : '';
                        gstVal = parts[1] ? parts[1].replace(/GST\s*:/i, '').trim() : '';
                    }
                    grandTotalVal = $totalDiv.find('.fw-bold').text().trim();
                } catch (e) {
                    console.error("Error parsing billing summary:", e);
                }

                // Extract actions from row.action
                const $actionDiv = $('<div>' + row.action + '</div>');
                const $pdfBtn = $actionDiv.find('a[href*="/quotation/pdf/"]');
                const $reviseBtn = $actionDiv.find('a[href*="/direct-quotation/"], a[href*="/quotation/create/"]');
                const $mailBtn = $actionDiv.find('.quot-mail');
                const $historyBtn = $actionDiv.find('.quot-hist');

                const $viewBtn = $(`
                    <button type="button" class="btn btn-sm btn-custom-action text-white" style="background-color: #00454f; border-color: #00454f;" data-bs-toggle="collapse" data-bs-target="#collapseQuotation_${row.quotation_id}" aria-expanded="false" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                `);

                if ($pdfBtn.length) {
                    $pdfBtn.removeClass()
                        .addClass('btn btn-sm btn-light border text-danger btn-custom-action')
                        .attr('target', '_blank')
                        .html('<i class="fas fa-file-pdf"></i>');
                }

                if ($historyBtn.length) {
                    const badgeText = $historyBtn.find('.badge').text().trim();
                    $historyBtn.removeClass()
                        .addClass('btn btn-sm btn-light border text-info btn-custom-action position-relative quot-hist')
                        .html('<i class="fas fa-history"></i>' + (badgeText ? ` <span class="badge bg-danger text-white rounded-pill position-absolute" style="font-size: 8px; padding: 2px 4px; top: -5px; right: -5px;">${badgeText}</span>` : ''));
                }

                if ($reviseBtn.length) {
                    $reviseBtn.removeClass()
                        .addClass('btn btn-sm btn-light border text-warning btn-custom-action')
                        .html('<i class="fas fa-redo"></i>');
                }

                if ($mailBtn.length) {
                    $mailBtn.removeClass()
                        .addClass('btn btn-sm btn-light border text-success btn-custom-action quot-mail')
                        .html('<i class="fas fa-envelope"></i>');
                }

                // Build actions container
                const $actionsContainer = $('<div class="d-flex justify-content-end align-items-center border-top pt-2 mt-2 gap-2 w-100"></div>');
                $actionsContainer.append($viewBtn);
                if ($pdfBtn.length) $actionsContainer.append($pdfBtn);
                if ($historyBtn.length) $actionsContainer.append($historyBtn);
                if ($reviseBtn.length) $actionsContainer.append($reviseBtn);
                if ($mailBtn.length) $actionsContainer.append($mailBtn);

                const cardHtml = `
                    <div class="quotation-mobile-card">
                        <div class="card-row">
                            <div>
                                <span class="label-muted">Quotation No:</span>
                                <span class="value-bold">${quotationNo}</span>
                            </div>
                        </div>
                        <div class="card-row">
                            <div>
                                <span class="label-muted">Customer:</span>
                                <span class="value-bold">${customerName}</span>
                            </div>
                        </div>
                        <div class="card-row">
                            <div class="w-100 d-flex justify-content-between">
                                <div>
                                    <span class="label-muted">Date:</span>
                                    <span class="value-bold">${row.quotation_date}</span>
                                </div>
                                <div>
                                    <span class="label-muted">Total:</span>
                                    <span class="total-highlight">${totalAmount}</span>
                                </div>
                            </div>
                        </div>
                        ${isDirect ? `
                        <div class="card-row mt-1">
                            <span class="badge-direct">Direct Quotation</span>
                        </div>
                        ` : ''}

                        <!-- Collapsible Details -->
                        <div class="collapse mt-2 pt-2 border-top" id="collapseQuotation_${row.quotation_id}">
                            <!-- Small Details side-by-side -->
                            <div class="row g-2 mb-3">
                                ${createdBy ? `
                                <div class="col-6">
                                    <div class="p-2 rounded bg-light border" style="font-size: 11px;">
                                        <div class="text-muted mb-1" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Created By</div>
                                        <div class="fw-bold text-dark text-truncate" title="${createdBy}"><i class="fas fa-user text-primary me-1" style="font-size: 10px;"></i>${createdBy}</div>
                                    </div>
                                </div>
                                ` : ''}
                                ${assignedTo ? `
                                <div class="col-6">
                                    <div class="p-2 rounded bg-light border" style="font-size: 11px;">
                                        <div class="text-muted mb-1" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Assigned To</div>
                                        <div class="fw-bold text-dark text-truncate" title="${assignedTo}"><i class="fas fa-user-tie text-success me-1" style="font-size: 10px;"></i>${assignedTo}</div>
                                    </div>
                                </div>
                                ` : ''}
                            </div>

                            <div class="text-muted small mb-2 fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">PRODUCTS & ITEMS</div>
                            ${row.items}
                            
                            <div class="text-muted small mt-3 mb-1 fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">BILLING SUMMARY</div>
                            <div class="p-2 rounded bg-light border" style="font-size: 11px;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Subtotal:</span>
                                    <span class="fw-semibold text-dark">${subtotalVal}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">GST:</span>
                                    <span class="fw-semibold text-dark">${gstVal}</span>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-1 mt-1 font-weight-bold">
                                    <span class="fw-bold text-dark">Grand Total:</span>
                                    <span class="fw-bold text-success">${grandTotalVal}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-actions-wrapper"></div>
                    </div>
                `;

                const $card = $(cardHtml);
                $card.find('.card-actions-wrapper').replaceWith($actionsContainer);
                $container.append($card);
            });
        }
        @endif
    </script>
</body>

</html>
