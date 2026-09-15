<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        /* Flatpickr today date style fix */
        .flatpickr-day.today:not(.selected) {
            background: transparent !important;
            border-color: #00454f !important;
            color: #2d3748 !important;
            font-weight: bold;
        }

        .flatpickr-day.today:not(.selected):hover {
            background: #edf2f7 !important;
            color: #2d3748 !important;
        }

        /* Select2 dropdown scrollbar */
        .select2-results__options::-webkit-scrollbar {
            width: 8px;
        }

        .select2-results__options::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .select2-results__options::-webkit-scrollbar-thumb {
            background: #00454f;
            border-radius: 10px;
        }

        .select2-results__options::-webkit-scrollbar-thumb:hover {
            background: #003840;
        }

        /* Firefox */
        .select2-results__options {
            scrollbar-width: thin;
            scrollbar-color: #00454f #f1f1f1;
        }

        .table-scroll {
            max-height: 250px;
            overflow-y: auto;
        }

        .table-scroll thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 2;
            border-bottom: 2px solid #f5b225;
        }

        #lead_table td:last-child,
        #lead_table th:last-child {
            min-width: 160px;
            white-space: nowrap;
        }

        .flatpickr-wrapper {
            display: block !important;
            width: 100% !important;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 767.98px) {

            /* Hide only the table, keeping search/entries wrappers visible */
            #lead_table {
                display: none !important;
            }

            /* Adjust card container spacing */
            body,
            .page,
            .main-content,
            .container-fluid {
                overflow-x: hidden !important;
                max-width: 100% !important;
            }

            .card.custom-card {
                margin-left: -10px !important;
                margin-right: -10px !important;
                border-radius: 0 !important;
            }

            /* Header Buttons - Arrange ALL 5 buttons in a single row on mobile */
            .card-header {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 10px !important;
                padding: 12px 10px !important;
            }

            .card-header .card-title {
                margin-bottom: 0 !important;
                text-align: left !important;
                font-size: 16px !important;
            }

            .card-header>div:last-child {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
                width: 100% !important;
                gap: 6px !important;
            }

            #listToolbar {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                gap: 6px !important;
                margin: 0 !important;
            }

            #listToolbar button {
                width: 44px !important;
                min-width: 44px !important;
                height: 44px !important;
                min-height: 44px !important;
                padding: 0 !important;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                border-radius: 6px !important;
            }

            .card-header .nav-tabs-footer {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                border-bottom: none !important;
                gap: 6px !important;
                margin: 0 !important;
                flex-grow: 1 !important;
            }

            .card-header .nav-tabs-footer li {
                display: inline-block !important;
                margin: 0 !important;
            }

            /* List Tab button (icon only) -> make it square! */
            .card-header .nav-tabs-footer li:first-child {
                width: 44px !important;
                min-width: 44px !important;
            }

            .card-header .nav-tabs-footer li:first-child a {
                width: 44px !important;
                min-width: 44px !important;
                height: 44px !important;
                padding: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                border: 1px solid #dee2e6 !important;
                border-radius: 6px !important;
                background-color: #f8f9fa !important;
            }

            .card-header .nav-tabs-footer li a.active {
                background-color: #00454f !important;
                color: #fff !important;
                border-color: #00454f !important;
            }

            /* New Lead Tab button (icon + text) -> make it stretch to fill remaining space! */
            .card-header .nav-tabs-footer li:last-child {
                flex-grow: 1 !important;
            }

            .card-header .nav-tabs-footer li:last-child a {
                width: 100% !important;
                height: 44px !important;
                padding: 0 10px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                text-wrap: nowrap !important;
                border: 1px solid #dee2e6 !important;
                border-radius: 6px !important;
                background-color: #f8f9fa !important;
            }

            .card-header .nav-tabs-footer li:last-child a.active {
                background-color: #00454f !important;
                color: #fff !important;
                border-color: #00454f !important;
            }

            /* Search and Show Entries in single row */
            div.dataTables_wrapper .row:first-child {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
                justify-content: space-between !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            div.dataTables_wrapper div.dataTables_length,
            div.dataTables_wrapper div.dataTables_filter {
                display: inline-flex !important;
                margin: 0 !important;
                padding: 0 !important;
                vertical-align: middle !important;
                width: auto !important;
            }

            div.dataTables_wrapper div.dataTables_length {
                flex-shrink: 0 !important;
                margin-right: 8px !important;
            }

            div.dataTables_wrapper div.dataTables_filter {
                flex-grow: 1 !important;
            }

            div.dataTables_length label {
                font-size: 0 !important;
                display: flex !important;
                align-items: center !important;
                margin-bottom: 0 !important;
            }

            div.dataTables_length label select {
                font-size: 14px !important;
                width: 75px !important;
                height: 44px !important;
                display: inline-block !important;
                border-radius: 6px !important;
                margin: 0 !important;
            }

            div.dataTables_filter label {
                width: 100% !important;
                margin: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
            }

            div.dataTables_filter label input {
                width: 100% !important;
                height: 44px !important;
                margin: 0 !important;
                border-radius: 6px !important;
            }

            /* Advanced Filter fields take 100% width */
            #advancedFilters .row>div {
                width: 100% !important;
                margin-bottom: 12px !important;
            }

            #advancedFilters .row>div:last-child {
                margin-bottom: 0 !important;
                margin-top: 10px !important;
            }

            #advancedFilters button {
                min-height: 44px !important;
            }

            /* Pagination center-align & touch size */
            div.dataTables_wrapper div.dataTables_paginate {
                text-align: center !important;
                width: 100% !important;
                display: flex !important;
                justify-content: center !important;
                margin-top: 15px !important;
            }

            div.dataTables_wrapper div.dataTables_paginate ul.pagination {
                justify-content: center !important;
                width: 100% !important;
            }

            div.dataTables_wrapper div.dataTables_paginate ul.pagination li.paginate_button {
                padding: 0 !important;
                margin: 0 2px !important;
            }

            div.dataTables_wrapper div.dataTables_paginate ul.pagination li.paginate_button a {
                min-height: 44px !important;
                min-width: 44px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 13px !important;
                padding: 6px 12px !important;
                border-radius: 6px !important;
            }
        }

        /* Sidebar Color (#00454f) Theme Overrides */
        .text-primary,
        .card-title.text-primary,
        .lead-mobile-card .text-primary {
            color: #00454f !important;
        }

        .menu-submenu-data thead th,
        #lead_table thead th {
            background-color: #00454f !important;
            color: #ffffff !important;
            border-color: #003840 !important;
            font-weight: 600;
        }

        #btnAdvancedFilters.btn-info {
            background-color: #00454f !important;
            border-color: #00454f !important;
            color: #ffffff !important;
        }
        #btnAdvancedFilters.btn-info:hover,
        #btnAdvancedFilters.btn-info:focus,
        #btnAdvancedFilters.btn-info:active {
            background-color: #00323a !important;
            border-color: #00323a !important;
            color: #ffffff !important;
        }

        .btn-primary,
        .convertDealRecord {
            background-color: #00454f !important;
            border-color: #00454f !important;
            color: #ffffff !important;
        }
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active,
        .convertDealRecord:hover,
        .convertDealRecord:focus,
        .convertDealRecord:active {
            background-color: #00323a !important;
            border-color: #00323a !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 69, 79, 0.2) !important;
        }

        .nav-tabs .nav-item .nav-link.active {
            background-color: #00454f !important;
            color: #ffffff !important;
            border-color: #00454f !important;
        }
        .nav-tabs .nav-item .nav-link {
            color: #00454f !important;
            font-weight: 600;
        }

        .page-item.active .page-link {
            background-color: #00454f !important;
            border-color: #00454f !important;
            color: #ffffff !important;
        }
        .page-link {
            color: #00454f !important;
        }
        .page-link:hover {
            background-color: rgba(0, 69, 79, 0.05) !important;
            color: #003840 !important;
        }

        #lead_table .btn-light.text-primary,
        #lead_table .btn-light.text-info {
            color: #00454f !important;
        }
        #lead_table .btn-light.text-primary:hover,
        #lead_table .btn-light.text-info:hover {
            background-color: #00454f !important;
            color: #ffffff !important;
            border-color: #00454f !important;
        }

        #advancedFilters .p-3 {
            border: 1px solid rgba(0, 69, 79, 0.15) !important;
            background-color: rgba(0, 69, 79, 0.01) !important;
        }

        #applyFilters {
            background-color: #00454f !important;
            border-color: #00454f !important;
            color: #ffffff !important;
        }
        #applyFilters:hover {
            background-color: #003840 !important;
            border-color: #003840 !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--open .select2-selection--multiple {
            border-color: #00454f !important;
        }
    </style>
</head>

<body>
    @include('include.switcher')
    @include('include.loader')
    <div class="page">
        @include('include.header')
        <div class="page-header-breadcrumb d-md-flex d-block align-items-center justify-content-between ">
        </div>
        <div class="main-content app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div class="card-title text-primary mb-0 d-flex align-items-center gap-2">
                                    <span>{{ $page_title }}</span>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2 justify-content-md-end">
                                    <div class="d-flex align-items-center flex-wrap gap-2" id="listToolbar">
                                        <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" id="reloadLeadListBtn" title="Reload Lead List">
                                            <i class="fas fa-sync-alt"></i><span class="d-none d-md-inline ms-1">Reload</span>
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm fw-bold shadow-sm"
                                            id="exportExcelBtn">
                                            <i class="fas fa-file-excel"></i><span
                                                class="d-none d-md-inline ms-1">Excel</span>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm"
                                            id="exportPdfBtn">
                                            <i class="fas fa-file-pdf"></i><span
                                                class="d-none d-md-inline ms-1">PDF</span>
                                        </button>
                                        <button class="btn btn-info btn-sm fw-bold shadow-sm" type="button"
                                            id="btnAdvancedFilters">
                                            <i class="fas fa-filter"></i><span class="d-none d-md-inline ms-1">Advanced
                                                Filters</span>
                                        </button>
                                    </div>
                                    <ul class="nav nav-tabs justify-content-end nav-tabs-footer mb-0 flex-wrap"
                                        role="tablist">
                                        @if (permissions_check('canView'))
                                            <li class="nav-item">
                                                <a class="nav-link active" id="list-tab" data-bs-toggle="tab"
                                                    href="#deal-stage-list" role="tab"><i class="fas fa-list"></i><span
                                                        class="d-none d-md-inline ms-1">List</span></a>
                                            </li>
                                        @endif
                                        @if (permissions_check('canCreate'))
                                            <li class="nav-item">
                                                <a class="nav-link" id="new-lead-tab" data-bs-toggle="tab"
                                                    href="#new-deal-stage" role="tab"><i class="fas fa-plus"></i><span
                                                        class="ms-1" id="new-lead-tab-text">New Lead</span></a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    @if (permissions_check('canView'))
                                        <div class="tab-pane border-0 show active text-muted px-1 " id="deal-stage-list"
                                            role="tabpanel">

                                            <div class="collapse mb-3" id="advancedFilters">
                                                <div class="p-3 rounded shadow-sm">
                                                    <form id="filterForm">
                                                        <div class="row g-3">
                                                            <div class="col-md-3">
                                                                <label class="form-label fw-bold small text-dark">Lead
                                                                    Date</label>
                                                                <input type="text" class="form-control bg-white"
                                                                    id="filter_lead_date" name="filter_lead_date"
                                                                    placeholder="Select Date Range">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label
                                                                    class="form-label fw-bold small text-dark">Company</label>
                                                                <select class="form-control" id="filter_company"
                                                                    name="filter_company">
                                                                    <option value=""></option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label fw-bold small text-dark">Contact
                                                                    Person</label>
                                                                <input type="text" class="form-control"
                                                                    id="filter_contact_person" name="filter_contact_person"
                                                                    placeholder="e.g. John Doe">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label
                                                                    class="form-label fw-bold small text-dark">City</label>
                                                                <select class="form-control" id="filter_city"
                                                                    name="filter_city">
                                                                    <option value=""></option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label
                                                                    class="form-label fw-bold small text-dark">Product</label>
                                                                <select class="form-control select2" id="filter_product"
                                                                    name="filter_product" data-placeholder="Select Product">
                                                                    <option value=""></option>
                                                                    @foreach ($service as $item)
                                                                        <option value="{{ $item->product_id }}">
                                                                            {{ $item->prod_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            @if(session('login_type') === 'Company')
                                                                <div class="col-md-3">
                                                                    <label class="form-label fw-bold small text-dark">Created
                                                                        By</label>
                                                                    <select class="form-control select2" id="filter_created_by"
                                                                        name="filter_created_by"
                                                                        data-placeholder="Select Creator">
                                                                        <option value=""></option>
                                                                        @foreach ($creators as $creator)
                                                                            <option value="{{ $creator->id }}">{{ $creator->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label fw-bold small text-dark">Assigned
                                                                        To</label>
                                                                    <select class="form-control select2" id="filter_assigned_to"
                                                                        name="filter_assigned_to"
                                                                        data-placeholder="Select Staff">
                                                                        <option value=""></option>
                                                                        @foreach ($staffList as $staff)
                                                                            <option value="{{ $staff->staff_id }}">
                                                                                {{ $staff->staff_name }}
                                                                                ({{ ucfirst($staff->staff_type) }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @endif
                                                            <div class="col-md-3 d-flex align-items-end gap-2">
                                                                <button type="button" class="btn btn-dark flex-fill"
                                                                    id="applyFilters"><i class="fas fa-search me-1"></i>
                                                                    Search</button>
                                                                <button type="button"
                                                                    class="btn btn-outline-secondary flex-fill"
                                                                    id="resetFilters"><i class="fas fa-redo me-1"></i>
                                                                    Reset</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="table-responsive">
                                                    <table id="lead_table"
                                                        class="table table-bordered table-sm table-hover menu-submenu-data"
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Lead Details</th>
                                                                <th>Company Details</th>
                                                                <th>Contact Details</th>
                                                                {{-- <th>Products</th> --}}
                                                                <th>Status & Source</th>
                                                                @if(session('login_type') === 'Company')
                                                                    <th>Lead Owner</th>
                                                                @endif
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div id="lead_mobile_cards" class="d-md-none mt-2">
                                                    <!-- Dynamic Mobile Cards will be rendered here -->
                                                </div>
                                            </div>
                                        </div>

                                    @endif
                                    @if (permissions_check('canCreate'))
                                        <div class="tab-pane text-muted border-0 px-1 " id="new-deal-stage" role="tabpanel">
                                            <form id="deal_stage_form" enctype="multipart/form-data" novalidate>
                                                @csrf
                                                <input type="hidden" name="lead_id" id="lead_id">
                                                <div class="row mt-3">
                                                    <div class="col-xl-12">
                                                        <div class="row gy-3">
                                                            <div class="col-12 col-md-3 mb-2">
                                                                <label class="form-label">Lead No</label>
                                                                <input type="text" class="form-control bg-light"
                                                                    name="enquiry_no" value="{{ $enquiry_no }}" readonly>
                                                            </div>
                                                            <div class="col-12 col-md-3 mb-2">
                                                                <label class="form-label">Lead Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="hidden" name="enquiry_name"
                                                                    id="enquiry_name_hidden">
                                                                 <select class="form-select select2" name="product_id[]"
                                                                    id="enquiry_name_multi" multiple required
                                                                    data-placeholder="-- Select Product(s) --"
                                                                    style="width:100%;">
                                                                    <option value="NEW" class="text-primary fw-bold">+ Create New Product</option>
                                                                    @foreach ($service as $item)
                                                                         <option value="{{ $item->product_id }}"
                                                                             data-name="{{ $item->prod_name }}"
                                                                             data-price="{{ $item->prod_amount ?? 0 }}">
                                                                             {{ $item->prod_name }}
                                                                         </option>
                                                                     @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-6 col-md-3 mb-2">
                                                                <label class="form-label">Lead Date <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" name="enquiry_date"
                                                                    id="enquiry_date" value="{{ date('d-m-Y') }}" required>
                                                            </div>
                                                            <div class="col-6 col-md-3 mb-2">
                                                                <label
                                                                    class="form-label d-inline-flex align-items-center">Lead
                                                                    Status <span class="text-danger ms-1">*</span>
                                                                    <a href="javascript:void(0);"
                                                                        class="text-secondary ms-2" data-bs-toggle="modal"
                                                                        data-bs-target="#statusGuideModal"
                                                                        title="View status descriptions">
                                                                        <i class="fas fa-info-circle text-primary"
                                                                            style="font-size: 14px;"></i>
                                                                    </a>
                                                                </label>
                                                                <select class="form-select" name="enquiry_status"
                                                                    id="enquiry_status" required>
                                                                    <option value="">Select</option>
                                                                    <option value="NEW" class="text-primary fw-bold">Create
                                                                        New Status</option>
                                                                    @foreach ($lead_status as $ls)
                                                                        <option value="{{ $ls->status_id }}">
                                                                            {{ $ls->title }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-6 col-md-3 mb-2">
                                                                <label class="form-label">Source <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select" name="source" id="source_id"
                                                                    required>
                                                                    <option value="">-- Select --</option>
                                                                    <option value="NEW" class="text-primary fw-bold">Create
                                                                        New Source</option>
                                                                    @foreach ($source->groupBy('source_type') as $srcCat => $items)
                                                                        <optgroup
                                                                            label="{{ !empty($srcCat) ? $srcCat : 'Other' }}">
                                                                            @foreach ($items as $s)
                                                                                <option value="{{ $s->source_id }}">
                                                                                    {{ $s->title }}
                                                                                </option>
                                                                            @endforeach
                                                                        </optgroup>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-6 col-md-3 mb-2">
                                                                <label class="form-label">Industry<span class="text-danger">*</span></label>
                                                                <select class="form-select select2" name="industry_id"
                                                                    id="industry_id" required>
                                                                    <option value="">-- Select Industry --</option>
                                                                    <option value="NEW" class="text-primary fw-bold">+ Create New Industry</option>
                                                                    @foreach ($industries as $industry)
                                                                        <option value="{{ $industry->industry_type_id }}">
                                                                            {{ $industry->industry_type_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback">Please select an industry.</div>
                                                            </div>
                                                            <div class="col-6 col-md-3 mb-2">
                                                                <label class="form-label">Existing Customer</label>
                                                                <select class="form-select" name="customer_id"
                                                                    id="customer_id"></select>
                                                            </div>
                                                            <div class="col-6 col-md-3 mb-2">
                                                                <label class="form-label">Company Name</label>
                                                                <input type="text" class="form-control" name="company_name"
                                                                    id="company_name">
                                                            </div>
                                                            <div class="col-6 col-md-3 mb-2">
                                                                <label class="form-label">Contact Person</label>
                                                                <input type="text" class="form-control"
                                                                    name="contact_person" id="contact_person">
                                                            </div>
                                                             <div class="col-6 col-md-3 field-group mb-2">
                                                                 <label class="form-label fw-medium">
                                                                     Mobile No
                                                                 </label>
                                                                 <input type="text" class="form-control" name="mobile_no"
                                                                     id="mobile_no" placeholder="Mobile Number"
                                                                     maxlength="15" pattern="[0-9()+\- ]*"
                                                                     oninput="this.value = this.value.replace(/[^0-9()+\- ]/g, '')">
                                                                 <div class="invalid-feedback">
                                                                     Please enter a valid mobile number (max 15 digits).
                                                                 </div>
                                                             </div>
                                                             <div class="col-6 col-md-3 field-group mb-2">
                                                                 <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                                                                 <div class="position-relative">
                                                                     <input type="email" class="form-control"
                                                                         name="email_address" id="email_address" required>
                                                                     <i class="fas fa-envelope position-absolute top-50 end-0 translate-middle-y me-3"
                                                                         style="color: #20c997;"></i>
                                                                     <div class="invalid-feedback">
                                                                         Please enter a valid email address.
                                                                     </div>
                                                                 </div>
                                                             </div>
                                                            <div class="col-6 col-md-3 field-group mb-2">
                                                                <label class="form-label fw-medium">
                                                                    Alt. Phone
                                                                </label>
                                                                <input type="text" class="form-control" name="alt_phone"
                                                                    id="alt_phone" placeholder="Alternate Phone"
                                                                    maxlength="15" pattern="[0-9()+\- ]*"
                                                                    oninput="this.value = this.value.replace(/[^0-9()+\- ]/g, '')">
                                                                <div class="invalid-feedback">
                                                                    Please enter a valid alternate phone number (max 15
                                                                    digits).
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-md-4 field-group mb-2">
                                                                <label class="form-label fw-medium">City <span
                                                                        class="text-danger">*</span></label>
                                                                <select class="form-select" id="city_id" name="city_id"
                                                                    required></select>
                                                                <div class="invalid-feedback">Please select a city.</div>
                                                            </div>
                                                            <div class="col-6 col-md-4 field-group mb-2">
                                                                <label class="form-label fw-medium">State</label>
                                                                <select class="form-select" id="state_id_display">
                                                                    <option value="">-- Select State --</option>
                                                                </select>
                                                                <input type="hidden" id="state_id" name="state_id">
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                            <div class="col-6 col-md-4 field-group mb-2">
                                                                <label class="form-label fw-medium">Country</label>
                                                                <select class="form-select" id="country_id_display">
                                                                    <option value="">-- Select Country --</option>
                                                                </select>
                                                                <input type="hidden" id="country_id" name="country_id">
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                            @if(isStaffLogin())
                                                                <input type="hidden" name="lead_type" id="lead_type" value="{{ (isset($loggedInStaff) && $loggedInStaff) ? $loggedInStaff->staff_type : (session('staff_type') ?? 'Domestic') }}">
                                                            @else
                                                                <div class="col-6 col-md-3 mb-2">
                                                                    <label class="form-label">Lead Type</label>
                                                                    <select class="form-select" name="lead_type" id="lead_type">
                                                                        <option value="">-- Select --</option>
                                                                        <option value="Domestic">Domestic</option>
                                                                        <option value="Export">Export</option>
                                                                    </select>
                                                                </div>
                                                            @endif
                                                            <div class="col-12 col-md-3 mb-2">
                                                                <label class="form-label">Assign To Staff</label>
                                                                <select class="form-select" name="assign_to" id="assign_to">
                                                                    <option value="">-- Select Staff --</option>
                                                                    @foreach ($staffList as $staff)
                                                                        <option value="{{ $staff->staff_id }}"
                                                                            data-type="{{ $staff->staff_type }}">
                                                                            {{ $staff->staff_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-12 mb-2">
                                                                @include('components.tooltip-help', ['key' => 'lead_remarks_notes', 'label' => 'Remarks / Notes'])
                                                                <textarea class="form-control" name="remarks" rows="3"
                                                                    placeholder="Any specific requirements..."></textarea>
                                                            </div>
                                                            <div class="col-md-12 mt-3">
                                                                <div id="convert_deal_section">
                                                                    <div
                                                                        class="form-check form-switch d-flex align-items-center">
                                                                        <input class="form-check-input mt-0 me-2"
                                                                            type="checkbox" id="convert_deal"
                                                                            name="convert_to_deal" style="cursor: pointer;">
                                                                        <label
                                                                            class="form-check-label fw-bold text-dark mb-0"
                                                                            for="convert_deal">
                                                                            Convert directly to Deal?
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div id="dealDetails" class="card border shadow-sm mb-3"
                                                                    style="display:none;">
                                                                    <div class="card-body">
                                                                        <h6 class="fw-bold mb-3">Deal Details</h6>
                                                                        <div class="row g-3">
                                                                            <div class="col-md-3">
                                                                                <label class="form-label">Deal Amount <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="number" class="form-control"
                                                                                    name="deal_amount">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label class="form-label">Closing Date <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="text" class="form-control"
                                                                                    id="closing_date" name="closing_date">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label class="form-label">Deal Type</label>
                                                                                <select class="form-select" name="deal_type"
                                                                                    id="deal_type">
                                                                                    <option value="">Select Type</option>
                                                                                    <option value="new">New Business
                                                                                    </option>
                                                                                    <option value="existing">Existing
                                                                                        Business</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label class="form-label">Deal Stage <span
                                                                                        class="text-danger">*</span></label>
                                                                                <select class="form-select"
                                                                                    name="deal_stage" id="deal_stage">
                                                                                    <option value="">Select Stage</option>
                                                                                    @foreach ($deal_stage as $stage)
                                                                                        <option value="{{ $stage->id }}">
                                                                                            {{ $stage->stage_name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label class="form-label">Assign To Staff</label>
                                                                                <select class="form-select"
                                                                                    name="deal_assigned_to"
                                                                                    id="deal_assigned_to">
                                                                                    <option value="">-- Select Staff --
                                                                                    </option>
                                                                                    @foreach ($staffList as $staff)
                                                                                        <option value="{{ $staff->staff_id }}" {{ (session('staff_id') == $staff->staff_id) ? 'selected' : '' }}
                                                                                            data-type="{{ $staff->staff_type }}">
                                                                                            {{ $staff->staff_name }}{{ $staff->staff_type ? ' (' . $staff->staff_type . ')' : '' }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label class="form-label">GST No</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="gst_no">
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label">Description</label>
                                                                                <textarea class="form-control" rows="3"
                                                                                    name="description"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 d-flex gap-2 mt-3">
                                                                <button type="submit" id="submit_lead_btn"
                                                                    class="btn text-white fw-bold px-4 py-2"
                                                                    style="background-color: #20c997; border-color: #20c997;">SAVE
                                                                    RECORD</button>
                                                                <button type="button" id="btn_clear_lead_form"
                                                                    class="btn btn-light border px-4 py-2 fw-bold text-dark">CLEAR</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Source Modal -->
        <div class="modal fade" id="createSourceModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-dark">Create New Source</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="createSourceForm" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Source Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="source_type" id="modal_source_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Online">Online</option>
                                    <option value="Offline">Offline</option>
                                </select>
                                <div class="invalid-feedback">Please select Source Type.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Source Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="source_name" required
                                    placeholder="Enter source name">
                                <div class="invalid-feedback">Please enter Source Name.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Save Source</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Modal -->
        <div class="modal fade" id="createStatusModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Lead Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="createStatusForm" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Status Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="status_name" required>
                                <div class="invalid-feedback">Please enter Status Name.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Save Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Modal -->
        <div class="modal fade" id="createProductModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-dark">Create New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="createProductForm" novalidate>
                            @csrf
                            <div class="row">
                                <!-- 1. Category -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-dark">Category <span class="text-danger">*</span></label>
                                    <select class="form-select select2" name="category_id" id="cp_category_id" required style="width: 100%;">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $type)
                                            <option value="{{ $type->category_id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select Category.</div>
                                </div>

                                <!-- 2. Product Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-dark">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="prod_name" required placeholder="Enter Product Name">
                                    <div class="invalid-feedback">Please enter Product Name.</div>
                                </div>

                                <!-- 3. Unit -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-dark">Unit</label>
                                    <select class="form-select select2" name="unit_id" id="cp_unit_id" style="width: 100%;">
                                        <option value="">Select Unit</option>
                                        @foreach($unit as $u)
                                            <option value="{{ $u->unit_id }}">{{ $u->unit }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 4. Product Rate -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-dark">Product Rate <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="prod_amount" required placeholder="0.00">
                                    <div class="invalid-feedback">Please enter Product Rate.</div>
                                </div>

                                <!-- 5. Product Description -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold text-dark">Product Description</label>
                                    <textarea class="form-control" name="prod_desc" rows="3" placeholder="Enter Description Details..."></textarea>
                                </div>

                                <!-- 6. GST (%) - Optional -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-dark">GST (%)</label>
                                    <select class="form-select select2" name="gst" id="cp_gst" style="width: 100%;">
                                        <option value="">Select GST</option>
                                        @foreach($gst_rates as $rate)
                                            <option value="{{ $rate->gst_id }}">{{ $rate->gst_no }}%</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mt-2">Save Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Industry Modal -->
        <div class="modal fade" id="createIndustryModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-dark">Create New Industry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="createIndustryForm" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Industry Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="industry_type_name" required placeholder="Enter industry name">
                                <div class="invalid-feedback">Please enter Industry Name.</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Save Industry</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Convert to Deal Modal -->
        @include('company.crm.lead.convert_modal', ['products' => $service, 'stages' => $deal_stage])

        @include('company.crm.customer.modal')
        @include('include.footer')
    </div>
    @include('include.scroll-top')
    @include('include.footer_link')
    @include('include.datatable_js_link')
    <script src="{{ asset('assets/admin-assets/js/leadTimeline.js') }}"></script>


    <script>
        window.onerror = function (message, source, lineno, colno, error) {
            alert("JS Error: " + message + " at line " + lineno + " in " + (source ? source.split('/').pop() : 'unknown'));
            return false;
        };

        $(document).ready(function () {
            const loggedInStaffType = "{{ $loggedInStaff ? $loggedInStaff->staff_type : '' }}";
            const loggedInStaffId = "{{ $loggedInStaff ? $loggedInStaff->staff_id : '' }}";
            let _isCitySettingLeadType = false;

            window.autoSelectLoggedInStaff = function () {
                if (window.isEditMode) return;
                if (loggedInStaffType) {
                    _isCitySettingLeadType = true;
                    $('#lead_type').val(loggedInStaffType).trigger('change');
                    if (loggedInStaffId) {
                        filterStaffByType(loggedInStaffType, loggedInStaffId);
                    }
                    _isCitySettingLeadType = false;
                }
            };

            $(window).on('resize', function () {
                $('.select2-hidden-accessible').select2('close');
            });
            // Handle multi-select name generation and deal amount calculation
            $('#enquiry_name_multi').on('change', function () {
                let selectedValues = $(this).val() || [];
                if (selectedValues.includes('NEW')) {
                    let updatedValues = selectedValues.filter(val => val !== 'NEW');
                    $(this).val(updatedValues).trigger('change.select2');
                    $('#createProductForm')[0].reset();
                    $('#cp_category_id, #cp_unit_id, #cp_gst').val(null).trigger('change');
                    $('#createProductModal').modal('show');
                    return;
                }

                let selectedNames = [];
                let totalAmount = 0;
                $(this).find('option:selected').each(function () {
                    if ($(this).val() !== 'NEW') {
                        selectedNames.push($(this).data('name'));
                        let price = parseFloat($(this).data('price') || $(this).attr('data-price')) || 0;
                        totalAmount += price;
                    }
                });
                $('#enquiry_name_hidden').val(selectedNames.join(', '));
                if (totalAmount > 0) {
                    $('[name="deal_amount"]').val(totalAmount.toFixed(2));
                } else {
                    $('[name="deal_amount"]').val('');
                }
            });


            // Global edit mode flag
            window.isEditMode = false;
            window.isLoadingEditData = false;
            window.isLoadingConvertData = false;

            // Automatically switch to New Lead tab if tab=new is in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'new') {
                $('#listToolbar').removeClass('d-flex').addClass('d-none');
                $('a[href="#new-deal-stage"]').tab('show');
            }

            function formatCustomOption(state) {
                if (!state.id) {
                    return state.text;
                }

                if (state.id === 'NEW') {
                    var labelText = state.text || '';
                    if (labelText.indexOf('+') === 0) {
                        labelText = labelText.substring(1).trim();
                    }
                    return $(
                        '<div class="d-flex align-items-center text-primary fw-bold p-1" style="cursor: pointer; border-bottom: 1px dashed #cfe2ff; margin-bottom: 4px;">' +
                        '<i class="fas fa-plus-circle me-2" style="font-size: 1.1rem;"></i>' +
                        '<span>' + labelText + '</span>' +
                        '</div>'
                    );
                }

                return state.text;
            }

            $('#source_id, #enquiry_status, #enquiry_name_multi').select2({
                width: '100%',
                allowClear: true,
                placeholder: '-- Select --',
                templateResult: formatCustomOption
            });

            $('#state_id_display, #country_id_display, #assign_to, #item_select, #deal_stage, #deal_type, #deal_assigned_to')
                .select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: '-- Select --'
                });

            if ($('#lead_type').is('select')) {
                $('#lead_type').select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: '-- Select --'
                });
            }

            $('#industry_id').select2({
                width: '100%',
                allowClear: true,
                placeholder: '-- Select Industry --',
                templateResult: formatCustomOption
            });
            $('#closing_date').flatpickr({
                dateFormat: "d-m-Y",
                minDate: "today"
            });

            $('#modal_source_type').select2({
                dropdownParent: $('#createSourceModal .modal-content'),
                width: '100%',
                placeholder: 'Select Type'
            });

            $('#enquiry_date').flatpickr({
                dateFormat: "d-m-Y",
            });

            let table = $('#lead_table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('enquiry.list') }}",
                    data: function (d) {
                        d.company = $('#filter_company').val();
                        d.contact_person = $('#filter_contact_person').val();
                        d.city = $('#filter_city').val();
                        d.product = $('#filter_product').val();
                        d.created_by = $('#filter_created_by').val();
                        d.assigned_to = $('#filter_assigned_to').val();
                        d.lead_date = $('#filter_lead_date').val();
                    }
                },

                order: [
                    [1, "desc"]
                ],

                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'lead_details',
                    name: 'enquiry_name',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'company_details',
                    name: 'company_name',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'contact_details',
                    name: 'contact_person_name',
                    orderable: true,
                    searchable: true
                },
                // {
                //     data: 'product_details',
                //     name: 'products.prod_name',
                //     orderable: false,
                //     searchable: false
                // },
                {
                    data: 'status_source',
                    name: 'leadStatus.title',
                    orderable: false,
                    searchable: false
                },
                    @if(session('login_type') === 'Company')
                                {
                            data: 'owner_details',
                            name: 'created_by',
                            orderable: false,
                            searchable: false
                        },
                    @endif
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                }
                ],

                language: {
                    search: "",
                    searchPlaceholder: "Search..."
                },

                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],

                drawCallback: function () {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    renderMobileCards(this.api());
                }
            });

            // Move mobile cards container inside DataTable wrapper right after the table
            // so that pagination and entries count render below the mobile cards
            $('#lead_table').after($('#lead_mobile_cards'));

            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                let target = $(e.target).attr('href');

                if (target === '#new-deal-stage') {
                    $('#listToolbar').removeClass('d-flex').addClass('d-none');
                } else {
                    $('#listToolbar').removeClass('d-none').addClass('d-flex');
                }
            });

            // --- FILTER UI SELECT2 INITIALIZATION ---
            $('#filter_lead_date').flatpickr({
                mode: "range",
                dateFormat: "d-m-Y",
            });

            $('#filter_product, #filter_created_by, #filter_assigned_to').select2({
                width: '100%',
                allowClear: true
            });

            $('#filter_company').select2({
                width: '100%',
                placeholder: 'Select Company',
                allowClear: true,
                ajax: {
                    url: "{{ route('customer.search') }}",
                    dataType: 'json',
                    delay: 300,
                    cache: true,
                    data: function (params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination ? data.pagination.more : false
                            }
                        };
                    }
                },
                templateResult: formatCustomOption
            });

            $('#filter_city').select2({
                width: '100%',
                placeholder: 'Select City',
                allowClear: true,
                ajax: {
                    url: "{{ route('customer.city.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    },
                    cache: true
                }
            });

            $('#applyFilters').on('click', function () {
                $('#lead_table').DataTable().ajax.reload();
            });

            $('#resetFilters').on('click', function () {
                $('#filterForm')[0].reset();
                $('#filter_company, #filter_city, #filter_product, #filter_created_by, #filter_assigned_to').val(null).trigger('change');
                if ($('#filter_lead_date')[0]._flatpickr) {
                    $('#filter_lead_date')[0]._flatpickr.clear();
                }
                $('#lead_table').DataTable().ajax.reload();
            });

            $('#btnAdvancedFilters').on('click', function () {
                if ($('#list-tab').hasClass('active')) {
                    $('#advancedFilters').collapse('toggle');
                }
            });



            $('#exportExcelBtn').on('click', function () {
                var query = $.param({
                    company: $('#filter_company').val(),
                    contact_person: $('#filter_contact_person').val(),
                    city: $('#filter_city').val(),
                    product: $('#filter_product').val(),
                    created_by: $('#filter_created_by').val(),
                    assigned_to: $('#filter_assigned_to').val(),
                    lead_date: $('#filter_lead_date').val()
                });
                window.open("{{ route('enquiry.export.excel') }}?" + query, '_blank');
            });

            $('#exportPdfBtn').on('click', function () {
                var query = $.param({
                    company: $('#filter_company').val(),
                    contact_person: $('#filter_contact_person').val(),
                    city: $('#filter_city').val(),
                    product: $('#filter_product').val(),
                    created_by: $('#filter_created_by').val(),
                    assigned_to: $('#filter_assigned_to').val(),
                    lead_date: $('#filter_lead_date').val()
                });
                window.open("{{ route('enquiry.export.pdf') }}?" + query, '_blank');
            });


            $('#city_id').select2({
                width: '100%',
                placeholder: '-- Select or Type City --',
                createTag: function (params) {
                    var term = $.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                },
                ajax: {
                    url: "{{ route('customer.city.search') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1,
                            lead_type: $('#lead_type').val()
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    }
                }
            });

            /* -----------------------------
             * City -> State / Country auto-fill + Lead Type auto-set
             * ----------------------------- */
            function resetLocationFields() {
                $('#state_id_display').html('<option value="">-- Select State --</option>').trigger('change');
                $('#country_id_display').html('<option value="">-- Select Country --</option>').trigger('change');
                $('#state_id, #country_id').val('');
            }

            // Filter staff dropdown by lead type via AJAX
             window.filterStaffByType = function (type, currentVal) {
                $.get("{{ route('enquiry.staff.by.type') }}", { type: type }, function (res) {
                    if (res.status) {
                        var $select = $('#assign_to');
                        var prev = currentVal !== undefined ? currentVal : $select.val();
                        
                        // Check if select2 dropdown is open
                        var isOpen = $select.next('.select2-container').hasClass('select2-container--open');
                        
                        $select.empty().append('<option value="">-- Select Staff --</option>');
                        $.each(res.staff, function (i, s) {
                            $select.append('<option value="' + s.id + '" data-type="' + (s.type || '') + '">' + s.name + '</option>');
                        });
                        
                        if (prev) {
                            $select.val(prev).trigger('change');
                        } else {
                            $select.trigger('change');
                        }
                        
                        // Refresh dropdown immediately if already open
                        if (isOpen) {
                            $select.select2('close').select2('open');
                        }
                    } else {
                        alert('filterStaffByType status false: ' + JSON.stringify(res));
                    }
                    if (window.isLoadingEditData) {
                        window.isLoadingEditData = false;
                    }
                }).fail(function (xhr) {
                    alert('filterStaffByType AJAX failed: status=' + xhr.status + ', text=' + xhr.statusText);
                    if (window.isLoadingEditData) {
                        window.isLoadingEditData = false;
                    }
                });
            }

            $('#city_id').on('change', function () {
                if (window.isLoadingEditData) return;
                const cityId = $(this).val();

                if (!cityId) {
                    resetLocationFields();
                    return;
                }

                $.get("{{ route('customer.city.details', ':id') }}".replace(':id', cityId), function (res) {

                    $('#state_id_display')
                        .html(`<option value="${res.state_id}" selected>${res.state_name}</option>`)
                        .trigger('change');

                    $('#country_id_display')
                        .html(`<option value="${res.c_id}" selected>${res.country_name}</option>`)
                        .trigger('change');

                    $('#state_id').val(res.state_id);
                    $('#country_id').val(res.c_id);

                    // Auto-set Lead Type based on country
                    const indiaCountryId = 99;
                    const detectedType = (parseInt(res.c_id) === indiaCountryId) ? 'Domestic' : 'Export';
                    _isCitySettingLeadType = true;
                    $('#lead_type').val(detectedType).trigger('change');
                    _isCitySettingLeadType = false;

                }).fail(function () {
                    resetLocationFields();
                });
            });

            // When lead type is changed, filter staff dropdown accordingly & reset city if manually changed
            $('#lead_type').on('change', function () {
                if (window.isLoadingEditData) return;
                const selectedType = $(this).val();
                filterStaffByType(selectedType);

                if (!_isCitySettingLeadType) {
                    $('#city_id').val(null).trigger('change.select2');
                    resetLocationFields();
                }
            });

            // Reload Lead List button click handler
            $('#reloadLeadListBtn').on('click', function () {
                var $btn = $(this);
                var $icon = $btn.find('i');
                $icon.addClass('fa-spin');
                if (typeof table !== 'undefined' && table.ajax) {
                    table.ajax.reload(function () {
                        $icon.removeClass('fa-spin');
                    }, false);
                } else {
                    $icon.removeClass('fa-spin');
                }
            });

            $('#customer_id').select2({
                width: '100%',
                placeholder: 'Select Customer',
                allowClear: true,
                ajax: {
                    url: "{{ route('customer.search') }}",
                    dataType: 'json',
                    delay: 300,
                    cache: true,

                    data: function (params) {
                        return {
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },

                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        var results = data.results;
                        if (params.page === 1 && !isEditMode) {
                            results.unshift({ id: 'NEW', text: 'Create New Customer' });
                        }

                        return {
                            results: results,
                            pagination: {
                                more: data.pagination ? data.pagination.more : false
                            }
                        };
                    }
                },
                templateResult: formatCustomOption
            });


            $('#customer_id').on('select2:select', function (e) {

                let customerId = e.params.data.id;

                if (customerId === 'NEW') {
                    $('#customer_id').val(null).trigger('change');
                    $('#createCustomerForm')[0].reset();
                    $('#createCustomerModal').modal('show');
                    return;
                }

                $.get("{{ route('customer.details', ':id') }}".replace(':id', customerId), function (res) {

                    // Basic Details
                    $('#company_name').val(res.customer.company_name);
                    $('#contact_person').val(res.customer.contact_person);
                    $('#mobile_no').val(res.customer.phone);
                    $('#alt_phone').val(res.customer.alt_phone);
                    $('#email_address').val(res.customer.email);

                    _isCitySettingLeadType = true;

                    // City
                    if (res.city) {
                        var cityOption = new Option(
                            res.city.city_name,
                            res.city.city_id,
                            true,
                            true
                        );
                        $('#city_id').empty().append(cityOption).val(res.city.city_id).trigger('change.select2');
                    } else {
                        $('#city_id').empty().val(null).trigger('change.select2');
                    }

                    // State
                    if (res.state) {
                        $('#state_id_display')
                            .html(`<option value="${res.state.state_id}" selected>${res.state.state_name}</option>`)
                            .trigger('change.select2');
                        $('#state_id').val(res.state.state_id);
                    } else {
                        $('#state_id_display').html('<option value="">-- Select State --</option>').trigger('change.select2');
                        $('#state_id').val('');
                    }

                    // Country
                    if (res.country) {
                        $('#country_id_display')
                            .html(`<option value="${res.country.c_id}" selected>${res.country.country_name}</option>`)
                            .trigger('change.select2');
                        $('#country_id').val(res.country.c_id);

                        // Auto-set Lead Type based on country
                        const indiaCountryId = 99;
                        const detectedType = (parseInt(res.country.c_id) === indiaCountryId) ? 'Domestic' : 'Export';
                        $('#lead_type').val(detectedType).trigger('change');
                    } else {
                        $('#country_id_display').html('<option value="">-- Select Country --</option>').trigger('change.select2');
                        $('#country_id').val('');
                    }

                    _isCitySettingLeadType = false;

                });

            });

            $('#customer_id').on('select2:clear', function () {

                $('#company_name').val('');
                $('#contact_person').val('');
                $('#mobile_no').val('');
                $('#alt_phone').val('');
                $('#email_address').val('');

                $('#city_id').val(null).trigger('change');

                $('#state_id_display')
                    .html('<option value="">-- Select State --</option>')
                    .trigger('change');

                $('#country_id_display')
                    .html('<option value="">-- Select Country --</option>')
                    .trigger('change');

                $('#state_id').val('');
                $('#country_id').val('');

                // Reset Lead Type and reload all staff
                _leadTypeAutoSet = true;
                $('#lead_type').val('').trigger('change');
                filterStaffByType('');
            });






            function autoSelectDealType() {
                let customerId = $('#customer_id').val();
                if (customerId) {
                    $('#deal_type').val('existing').trigger('change');
                } else {
                    $('#deal_type').val('new').trigger('change');
                }
            }

            // Sync customer select to deal type
            $('#customer_id').on('change', function () {
                autoSelectDealType();
            });

            // Sync assign staff select to deal assign staff
            $('#assign_to').on('change', function () {
                $('#deal_assigned_to').val($(this).val()).trigger('change');
            });

            $('#convert_deal').on('change', function () {
                if ($(this).is(':checked')) {
                    autoSelectDealType();
                    // Pre-fill deal assigned staff with currently selected lead staff
                    $('#deal_assigned_to').val($('#assign_to').val()).trigger('change');
                    $('#dealDetails').stop(true, true).slideDown(300);
                } else {
                    $('#dealDetails').stop(true, true).slideUp(300);
                }
            });

        window.resetLeadForm = function (newEnquiryNo) {
            window.isEditMode = false;
            $('#lead_id').val('');
            $('#new-lead-tab-text').text('New Lead');
            $('#submit_lead_btn').text('SAVE RECORD');
            $('#convert_deal_section').show();

            // Clear form inputs
            $('#deal_stage_form')[0].reset();
            $('textarea[name="remarks"]').val('');
            $('#dealDetails').slideUp(300);
            $('#convert_deal').prop('checked', false);

            // Reset Select2s
            $('#enquiry_name_multi, #customer_id, #city_id, #source_id, #enquiry_status, #assign_to, #lead_type, #industry_id, #deal_stage, #deal_type')
                .val(null).trigger('change');

            // Reset Hidden & Displays
            $('#enquiry_name_hidden').val('');
            $('#state_id_display').html('<option value="">-- Select State --</option>').trigger('change');
            $('#country_id_display').html('<option value="">-- Select Country --</option>').trigger('change');
            $('#state_id').val('');
            $('#country_id').val('');

            // Restore Lead No default or new number
            if (newEnquiryNo) {
                $('input[name="enquiry_no"]').val(newEnquiryNo);
            } else {
                $('input[name="enquiry_no"]').val('{{ $enquiry_no }}');
            }

            // Auto-select logged-in staff details if available
            autoSelectLoggedInStaff();
        }

        $('#deal_stage_form').on('submit', function (e) {
            e.preventDefault();

            $(this).find('.is-invalid').removeClass('is-invalid');
            $(this).find('.select2-selection.is-invalid').removeClass('is-invalid');
            $(this).find('.invalid-feedback').remove();

            let form = this;
            let formData = new FormData(form);

            formData.set('convert_to_deal', $('#convert_deal').is(':checked') ? 1 : 0);

            let $submitBtn = $(form).find('button[type="submit"]');
            let originalText = $submitBtn.text();


            $.ajax({
                url: "{{ route('enquiry.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $submitBtn.prop('disabled', true).text('SAVING...');
                },
                success: function (res) {
                    if (res.status === true) {
                        iziToast.success({
                            title: 'Success',
                            message: res.message || 'Saved successfully',
                            position: 'topRight'
                        });

                        resetLeadForm(res.new_enquiry_no);

                        if ($('#lead_table').length && $.fn.DataTable.isDataTable('#lead_table')) {
                            $('#lead_table').DataTable().ajax.reload();
                        }

                        $('a[href="#deal-stage-list"]')[0].click();
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: res.message || 'Something went wrong',
                                position: 'topRight'
                            });
                        }
                    },
                    error: function (xhr) {

                        $submitBtn.prop('disabled', false).text(originalText);

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let firstField = null;

                            $.each(errors, function (field, messages) {
                                let inputName = field;
                                if (field.includes('.')) {
                                    inputName = field.split('.')[0] + '[]';
                                }

                                let $input = $(`[name="${inputName}"]`);

                                if (!$input.length) {
                                    $input = $(`[name="${field}[]"]`);
                                }

                                if (!$input.length) return; // skip if still not found

                                if (!firstField) {
                                    firstField = $input;
                                }

                                let isSelect2 = $input.is('select') &&
                                    $input.hasClass('select2-hidden-accessible');

                                let $visual = isSelect2 ?
                                    $input.next('.select2-container').find(
                                        '.select2-selection') :
                                    $input;

                                $input.addClass('is-invalid');
                                $visual.addClass('is-invalid');

                                let $container = $input.closest(
                                    '.field-group, .col-md-3, .col-md-4, .col-md-6, .col-md-12, .col-12, td'
                                );

                                let $feedback = $container.find('.invalid-feedback');

                                if ($feedback.length) {
                                    $feedback.text(messages[0]).addClass('d-block');
                                } else {
                                    let $feedbackEl = $(
                                        `<div class="invalid-feedback d-block">${messages[0]}</div>`
                                    );
                                    $container.append($feedbackEl);
                                }
                            });

                            if (firstField) {
                                let isSelect2 = firstField.is('select') && firstField.hasClass('select2-hidden-accessible');
                                if (isSelect2) {
                                    firstField.select2('open');
                                } else {
                                    firstField.focus();
                                }
                            }

                            iziToast.warning({
                                title: 'Warning',
                                message: xhr.responseJSON.message || 'Please fix the highlighted fields',
                                position: 'topRight'
                            });
                            return;
                        }

                        iziToast.error({
                            title: 'Error',
                            message: 'Server error, please try again',
                            position: 'topRight'
                        });
                    },
                    complete: function () {
                        $submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });

        });

        // AJAX Customer Submission
        $('#btnSaveCustomer').click(function () {
            let $btn = $(this);
            let $form = $('#createCustomerForm');
            $form.find('.is-invalid').removeClass('is-invalid');

            let $companyName = $form.find('[name="company_name"]');
            let $contactPerson = $form.find('[name="contact_person"]');
            let $email = $form.find('[name="email"]');

            let missing = [];
            if (!$companyName.val() || !$companyName.val().trim()) {
                $companyName.addClass('is-invalid');
                missing.push('Company Name');
            }
            if (!$contactPerson.val() || !$contactPerson.val().trim()) {
                $contactPerson.addClass('is-invalid');
                missing.push('Contact Person');
            }
            if (!$email.val() || !$email.val().trim()) {
                $email.addClass('is-invalid');
                missing.push('Email');
            }

            if (missing.length > 0) {
                if (typeof iziToast !== 'undefined') {
                    iziToast.warning({
                        title: 'Validation Error',
                        message: 'Please fill required field(s): ' + missing.join(', '),
                        position: 'topRight'
                    });
                }
                return;
            }

            $btn.prop('disabled', true).text('Saving...');

            let formData = new FormData($form[0]);

            $.ajax({
                url: "{{ route('ajax.customer.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.status) {
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topRight'
                        });

                        $('#createCustomerModal').modal('hide');

                        // Add to select2
                        var newOption = new Option(response.customer.text, response.customer.id, true, true);
                        $('#customer_id').append(newOption).trigger('change');

                        // Trigger the select2:select event to auto-fetch all details (including City, State, Country)
                        $('#customer_id').trigger({
                            type: 'select2:select',
                            params: {
                                data: { id: response.customer.id }
                            }
                        });

                    } else {
                        iziToast.error({
                            title: 'Error',
                            message: response.message,
                            position: 'topRight'
                        });
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errList = [];
                        $.each(errors, function (field, messages) {
                            let $input = $form.find('[name="' + field + '"]');
                            if ($input.length) {
                                $input.addClass('is-invalid');
                                let $fb = $input.siblings('.invalid-feedback');
                                if ($fb.length) $fb.text(messages[0]);
                            }
                            errList.push(messages[0]);
                        });

                        iziToast.warning({
                            title: 'Validation Error',
                            message: errList.join('<br>'),
                            position: 'topRight'
                        });
                        return;
                    }

                    let msg = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    iziToast.error({
                        title: 'Error',
                        message: msg,
                        position: 'topRight'
                    });
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Save Customer');
                }
            });
        });

        // Initialize Select2 for City inside Modal
        $('#modal_city_id').select2({
            dropdownParent: $('#createCustomerModal .modal-content'),
            width: '100%',
            placeholder: '-- Select or Type City --',
            allowClear: true,
            ajax: {
                url: "{{ route('location.cities') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        per_page: 20
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: item.city_name,
                                id: item.city_id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        // Auto Populate State and Country on City Select
        $('#modal_city_id').on('change', function () {
            var city_id = $(this).val();
            if (city_id) {
                $.ajax({
                    url: "{{ route('get.location.by.city') }}",
                    type: "GET",
                    data: { city_id: city_id },
                    success: function (response) {
                        if (response.city_id) {
                            var data = response;

                            // Set State
                            if (data.state_id) {
                                $('#modal_state_id').html(`<option value="${data.state_id}" selected>${data.state_name}</option>`);
                            } else {
                                $('#modal_state_id').html('');
                            }

                            // Set Country
                            if (data.country_id) {
                                $('#modal_country_id').html(`<option value="${data.country_id}" selected>${data.country_name}</option>`);
                            } else {
                                $('#modal_country_id').html('');
                            }
                        }
                    },
                    error: function () {
                        $('#modal_state_id').html('');
                        $('#modal_country_id').html('');
                    }
                });
            } else {
                $('#modal_state_id').html('');
                $('#modal_country_id').html('');
            }
        });

        // Source Modal Logic
        $('#source_id').on('change', function () {
            if ($(this).val() === 'NEW') {
                $(this).val(null).trigger('change.select2');
                $('#createSourceForm')[0].reset();
                $('#modal_source_type').val(null).trigger('change');
                $('#createSourceModal').modal('show');
            }
        });

        $('#createSourceForm').on('submit', function (e) {
            e.preventDefault();
            let $form = $(this);
            $form.find('.is-invalid').removeClass('is-invalid');

            let $sourceType = $form.find('[name="source_type"]');
            let $sourceName = $form.find('[name="source_name"]');

            let missing = [];
            if (!$sourceType.val()) {
                $sourceType.addClass('is-invalid');
                missing.push('Source Type');
            }
            if (!$sourceName.val() || !$sourceName.val().trim()) {
                $sourceName.addClass('is-invalid');
                missing.push('Source Name');
            }

            if (missing.length > 0) {
                if (typeof iziToast !== 'undefined') {
                    iziToast.warning({
                        title: 'Validation Error',
                        message: 'Please fill required field(s): ' + missing.join(', '),
                        position: 'topRight'
                    });
                }
                return;
            }

            let $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('leadsource.store') }}",
                type: "POST",
                data: $form.serialize(),
                success: function (response) {
                    if (response.status === 'success' || response.status === true) {
                        iziToast.success({ title: 'Success', message: 'Source created successfully!', position: 'topRight' });
                        $('#createSourceModal').modal('hide');

                        // Add new option and select it under the correct optgroup
                        if (response.id && response.source_name) {
                            let selectedType = $('#createSourceForm select[name="source_type"]').val() || 'Other';
                            let optgroup = $('#source_id').find('optgroup[label="' + selectedType + '"]');
                            if (!optgroup.length) {
                                optgroup = $('<optgroup>').attr('label', selectedType);
                                $('#source_id').append(optgroup);
                            }
                            var newOption = new Option(response.source_name, response.id, true, true);
                            optgroup.append(newOption);
                            $('#source_id').trigger('change');
                        }
                    } else {
                        iziToast.error({ title: 'Error', message: response.message || 'Something went wrong.', position: 'topRight' });
                    }
                },
                error: function (xhr) {
                    let msg = 'Failed to create Source.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errList = [];
                        $.each(xhr.responseJSON.errors, function (field, messages) {
                            let $input = $form.find('[name="' + field + '"]');
                            if ($input.length) {
                                $input.addClass('is-invalid');
                                let $fb = $input.siblings('.invalid-feedback');
                                if ($fb.length) $fb.text(messages[0]);
                            }
                            errList.push(messages[0]);
                        });
                        msg = errList.join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    iziToast.warning({ title: 'Validation Error', message: msg, position: 'topRight' });
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Save Source');
                }
            });
        });

        // Status Modal Logic
        $('#enquiry_status').on('change', function () {
            if ($(this).val() === 'NEW') {
                $(this).val(null).trigger('change.select2');
                $('#createStatusForm')[0].reset();
                $('#createStatusModal').modal('show');
            }
        });

        $('#createStatusForm').on('submit', function (e) {
            e.preventDefault();
            let $form = $(this);
            $form.find('.is-invalid').removeClass('is-invalid');

            let $statusName = $form.find('[name="status_name"]');
            if (!$statusName.val() || !$statusName.val().trim()) {
                $statusName.addClass('is-invalid');
                if (typeof iziToast !== 'undefined') {
                    iziToast.warning({
                        title: 'Validation Error',
                        message: 'Please enter Status Name.',
                        position: 'topRight'
                    });
                }
                return;
            }

            let $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('leadstatus.store') }}",
                type: "POST",
                data: $form.serialize(),
                success: function (response) {
                    if (response.status === 'success' || response.status === true) {
                        iziToast.success({ title: 'Success', message: 'Status created successfully!', position: 'topRight' });
                        $('#createStatusModal').modal('hide');

                        // Add new option and select it
                        if (response.id && response.status_name) {
                            var newOption = new Option(response.status_name, response.id, true, true);
                            $('#enquiry_status').append(newOption).trigger('change');
                        }
                    } else {
                        iziToast.error({ title: 'Error', message: response.message || 'Something went wrong.', position: 'topRight' });
                    }
                },
                error: function (xhr) {
                    let msg = 'Failed to create Status.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errList = [];
                        $.each(xhr.responseJSON.errors, function (field, messages) {
                            let $input = $form.find('[name="' + field + '"]');
                            if ($input.length) {
                                $input.addClass('is-invalid');
                                let $fb = $input.siblings('.invalid-feedback');
                                if ($fb.length) $fb.text(messages[0]);
                            }
                            errList.push(messages[0]);
                        });
                        msg = errList.join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    iziToast.warning({ title: 'Validation Error', message: msg, position: 'topRight' });
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Save Status');
                }
            });
        });

        // Industry Modal Logic
        $('#industry_id').on('change', function () {
            if ($(this).val() === 'NEW') {
                $(this).val(null).trigger('change.select2');
                $('#createIndustryForm')[0].reset();
                $('#createIndustryModal').modal('show');
            }
        });

        // Remove is-invalid on field change
        $('#createProductForm input, #createProductForm select, #createProductForm textarea, #createIndustryForm input').on('input change', function () {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
            }
        });

        // Industry Modal Submit Logic
        $('#createIndustryForm').on('submit', function (e) {
            e.preventDefault();
            let $form = $(this);
            $form.find('.is-invalid').removeClass('is-invalid');

            let $industryInput = $form.find('[name="industry_type_name"]');
            let industryName = $industryInput.val();
            if (!industryName || !industryName.trim()) {
                $industryInput.addClass('is-invalid');
                if (typeof iziToast !== 'undefined') {
                    iziToast.warning({
                        title: 'Validation Error',
                        message: 'Please enter Industry Name.',
                        position: 'topRight'
                    });
                }
                return;
            }

            let $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('lead.industry.ajax-store') }}",
                type: "POST",
                data: $form.serialize(),
                success: function (response) {
                    if (response.status === true) {
                        if (typeof iziToast !== 'undefined') {
                            iziToast.success({ title: 'Success', message: 'Industry created successfully!', position: 'topRight' });
                        }
                        $('#createIndustryModal').modal('hide');

                        if (response.id && response.industry_type_name) {
                            var newOption = new Option(response.industry_type_name, response.id, true, true);
                            $('#industry_id').append(newOption).trigger('change');
                        }
                    } else {
                        if (typeof iziToast !== 'undefined') {
                            iziToast.error({ title: 'Error', message: response.message || 'Something went wrong.', position: 'topRight' });
                        }
                    }
                },
                error: function (xhr) {
                    let msg = 'Failed to create Industry.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errList = [];
                        $.each(xhr.responseJSON.errors, function (field, messages) {
                            let $input = $form.find('[name="' + field + '"]');
                            if ($input.length) {
                                $input.addClass('is-invalid');
                                let $fb = $input.siblings('.invalid-feedback');
                                if ($fb.length) $fb.text(messages[0]);
                            }
                            errList.push(messages[0]);
                        });
                        msg = errList.join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (typeof iziToast !== 'undefined') {
                        iziToast.warning({ title: 'Validation Error', message: msg, position: 'topRight' });
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Save Industry');
                }
            });
        });

        $('#createProductModal').on('shown.bs.modal', function () {
            $('#cp_category_id, #cp_unit_id, #cp_gst').select2({
                dropdownParent: $('#createProductModal'),
                width: '100%'
            });
        });

        // Product Modal Form Submit Logic
        $('#createProductForm').on('submit', function (e) {
            e.preventDefault();
            let $form = $(this);
            $form.find('.is-invalid').removeClass('is-invalid');

            let $categoryId = $form.find('[name="category_id"]');
            let $prodName = $form.find('[name="prod_name"]');
            let $prodAmount = $form.find('[name="prod_amount"]');

            let missing = [];
            if (!$categoryId.val()) {
                $categoryId.addClass('is-invalid');
                missing.push('Category');
            }
            if (!$prodName.val() || !$prodName.val().trim()) {
                $prodName.addClass('is-invalid');
                missing.push('Product Name');
            }
            if ($prodAmount.val() === '' || $prodAmount.val() === null || isNaN($prodAmount.val())) {
                $prodAmount.addClass('is-invalid');
                missing.push('Product Rate');
            }

            if (missing.length > 0) {
                if (typeof iziToast !== 'undefined') {
                    iziToast.warning({
                        title: 'Validation Error',
                        message: 'Please fill required field(s): ' + missing.join(', '),
                        position: 'topRight'
                    });
                }
                return;
            }

            let $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('lead.product.ajax-store') }}",
                type: "POST",
                data: $form.serialize(),
                success: function (response) {
                    if (response.status === true) {
                        if (typeof iziToast !== 'undefined') {
                            iziToast.success({ title: 'Success', message: 'Product created successfully!', position: 'topRight' });
                        }
                        $('#createProductModal').modal('hide');

                        if (response.id && response.prod_name) {
                            var newOption = new Option(response.prod_name, response.id, true, true);
                            $(newOption).attr('data-name', response.prod_name).attr('data-price', response.prod_amount || 0);

                            let currentVals = $('#enquiry_name_multi').val() || [];
                            if (!currentVals.includes(response.id.toString())) {
                                currentVals.push(response.id.toString());
                            }

                            $('#enquiry_name_multi').append(newOption).val(currentVals).trigger('change');
                        }
                    } else {
                        if (typeof iziToast !== 'undefined') {
                            iziToast.error({ title: 'Error', message: response.message || 'Something went wrong.', position: 'topRight' });
                        }
                    }
                },
                error: function (xhr) {
                    let msg = 'Failed to create Product.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errList = [];
                        $.each(xhr.responseJSON.errors, function (field, messages) {
                            let $input = $form.find('[name="' + field + '"]');
                            if ($input.length) {
                                $input.addClass('is-invalid');
                                let $fb = $input.siblings('.invalid-feedback');
                                if ($fb.length) $fb.text(messages[0]);
                            }
                            errList.push(messages[0]);
                        });
                        msg = errList.join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (typeof iziToast !== 'undefined') {
                        iziToast.warning({ title: 'Validation Error', message: msg, position: 'topRight' });
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Save Product');
                }
            });
        });
        $('#new-lead-tab, #btn_clear_lead_form, a[href="#deal-stage-list"]').on('click', function (e) {
            if ($(this).attr('id') === 'new-lead-tab') {
                if (window.isProgrammaticClick) {
                    window.isProgrammaticClick = false;
                    return;
                }
            }

            resetLeadForm();
        });

        // Edit Lead Logic
        $(document).on('click', '.editRecord', function (e) {
            e.preventDefault();
            $('.tooltip').remove();
            let leadId = $(this).data('id');

            $.ajax({
                url: "{{ route('enquiry.edit', ':id') }}".replace(':id', leadId),
                type: "GET",
                success: function (res) {
                    if (res.status) {
                        let data = res.data;

                        // Enable loading edit data flag
                        window.isLoadingEditData = true;

                        // Switch Tab
                        window.isProgrammaticClick = true;
                        $('#new-lead-tab')[0].click();
                        window.isEditMode = true;
                        $('#new-lead-tab-text').text('Edit Lead');
                        $('#submit_lead_btn').text('UPDATE RECORD');
                        $('#lead_id').val(data.enquiry_id);

                        // Hide convert section
                        $('#convert_deal_section').hide();

                        // Populate standard fields
                        $('input[name="enquiry_no"]').val(data.enquiry_no);
                        if (res.products && res.products.length > 0) {
                            let productIds = res.products.map(p => p.product_id);
                            $('#enquiry_name_multi').val(productIds).trigger('change');
                        } else {
                            let matchedOption = $('#enquiry_name_multi option').filter(function () {
                                return $(this).data('name') == data.enquiry_name;
                            }).first();
                            if (matchedOption.length) {
                                $('#enquiry_name_multi').val([matchedOption.val()]).trigger('change');
                            }
                        }
                        if (data.enquiry_date) {
                            let parts = data.enquiry_date.split('-');
                            if (parts.length === 3) {
                                let formattedDate = parts[2] + "-" + parts[1] + "-" + parts[0];
                                $('input[name="enquiry_date"]').val(formattedDate);
                            }
                        }

                        // Populate Customer
                        if (res.data.customer) {
                            let cOpt = new Option(res.data.customer.company_name, res.data.customer.customer_id, true, true);
                            $('#customer_id').empty().append(cOpt).trigger('change');
                        } else {
                            $('#customer_id').val(null).trigger('change');
                        }
                        // Set company, contact, mobile, email, alt phone
                        $('input[name="company_name"]').val(data.company_name);
                        $('input[name="contact_person"]').val(data.contact_person_name);
                        $('input[name="mobile_no"]').val(data.phone);
                        $('input[name="email_address"]').val(data.email);
                        $('input[name="alt_phone"]').val(data.alternate_phone_1);
                        $('textarea[name="remarks"]').val(data.notes);

                        // Populate other Select2s
                        if (data.source) $('#source_id').val(data.source).trigger('change');
                        if (data.status_id) $('#enquiry_status').val(data.status_id).trigger('change');

                        // Lead Type & Industry
                        if (data.lead_type) {
                            $('#lead_type').val(data.lead_type).trigger('change');
                        } else {
                            $('#lead_type').val('').trigger('change');
                        }
                        if (data.industry_id) {
                            $('#industry_id').val(data.industry_id).trigger('change');
                        } else {
                            $('#industry_id').val('').trigger('change');
                        }

                        // Populate City
                        if (res.data.city) {
                            var cityOpt = new Option(res.data.city.city_name, res.data.city.city_id, true, true);
                            $('#city_id').empty().append(cityOpt).trigger('change');
                        } else {
                            $('#city_id').val(null).trigger('change');
                        }

                        // Populate State
                        if (res.data.state) {
                            $('#state_id_display').html(`<option value="${res.data.state.state_id}" selected>${res.data.state.state_name}</option>`).trigger('change');
                            $('#state_id').val(res.data.state.state_id);
                        } else {
                            $('#state_id_display').html('<option value="">-- Select State --</option>').trigger('change');
                            $('#state_id').val('');
                        }

                        // Populate Country
                        if (res.data.country) {
                            $('#country_id_display').html(`<option value="${res.data.country.c_id}" selected>${res.data.country.country_name}</option>`).trigger('change');
                            $('#country_id').val(res.data.country.c_id);
                        } else {
                            $('#country_id_display').html('<option value="">-- Select Country --</option>').trigger('change');
                            $('#country_id').val('');
                        }

                        // Assign To Staff: filter by lead_type first, then set value
                        filterStaffByType(data.lead_type || '', data.assigned_to);

                    } else {
                        iziToast.error({ title: 'Error', message: res.message });
                    }
                }
            });
        });

        // Delete Lead Logic
        $(document).on('click', '.deleteRecord', function (e) {
            e.preventDefault();
            $('.tooltip').remove();
            let leadId = $(this).data('id');

            iziToast.question({
                timeout: 20000,
                close: false,
                overlay: true,
                displayMode: 'once',
                id: 'question',
                zindex: 9999,
                title: 'Confirm',
                message: 'Are you sure you want to delete this Lead?',
                position: 'center',
                buttons: [
                    ['<button><b>YES</b></button>', function (instance, toast) {
                        instance.hide({}, toast, 'button');

                        $.ajax({
                            url: "{{ route('lead.delete', ':id') }}".replace(':id', leadId),
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                if (response.status === 'success' || response.status === true) {
                                    iziToast.success({
                                        title: 'Success',
                                        message: response.message || 'Lead deleted successfully',
                                        position: 'topRight'
                                    });
                                    $('#lead_table').DataTable().ajax.reload(null, false);
                                } else {
                                    iziToast.error({
                                        title: 'Error',
                                        message: response.message || 'Something went wrong',
                                        position: 'topRight'
                                    });
                                }
                            },
                            error: function (xhr) {
                                let msg = 'Failed to delete Lead.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                iziToast.error({
                                    title: 'Error',
                                    message: msg,
                                    position: 'topRight'
                                });
                            }
                        });
                    }],
                    ['<button>NO</button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }]
                ]
            });
        });



        @include('company.crm.lead.convert_modal_scripts')

        // Mobile cards rendering helper
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
            const $container = $('#lead_mobile_cards');
            $container.empty();

            if (data.length === 0) {
                $container.append('<div class="text-center text-muted p-4 bg-white rounded shadow-sm">No records found.</div>');
                return;
            }

            data.each(function (row) {
                // Extract parsed fields from formatted HTML columns
                const leadName = parseHtmlCol(row.lead_details, '[title="Lead Name"]') || row.enquiry_name;
                const detailsUrl = parseHtmlCol(row.lead_details, 'a', 'href') || 'javascript:void(0);';
                const leadNo = parseHtmlCol(row.lead_details, '[title="Lead No"]');
                const leadDate = parseHtmlCol(row.lead_details, '[title="Lead Date"]');

                const companyName = parseHtmlCol(row.company_details, '[title="Company Name"]');
                const cityState = parseHtmlCol(row.company_details, '[title="City"]');

                const contactPerson = parseHtmlCol(row.contact_details, '[title="Contact Person"]') || '-';
                const phone = parseHtmlCol(row.contact_details, '[title="Phone Number"]');

                const statusTitle = parseHtmlCol(row.status_source, '[title="Lead Status"]') || 'Open';
                const sourceTitle = parseHtmlCol(row.status_source, '[title="Lead Source"]') || 'N/A';

                const assignedTo = parseHtmlCol(row.owner_details, '[title="Assigned To"]') || '-';
                const createdBy = parseHtmlCol(row.owner_details, '[title="Created By"]').replace('By:', '').trim() || '-';

                // Map status to border color and badge design
                const statusLower = statusTitle.toLowerCase().trim();
                let borderColor = '#6c757d'; // Default Junk/Gray
                let badgeClass = 'bg-secondary';

                if (statusLower.includes('open')) {
                    borderColor = '#0d6efd'; // Blue
                    badgeClass = 'bg-primary-transparent text-primary';
                } else if (statusLower.includes('connected')) {
                    borderColor = '#198754'; // Green
                    badgeClass = 'bg-success-transparent text-success';
                } else if (statusLower.includes('qualified') && !statusLower.includes('not')) {
                    borderColor = '#6f42c1'; // Purple
                    badgeClass = 'bg-purple-transparent text-purple';
                } else if (statusLower.includes('future') || statusLower.includes('contact in future')) {
                    borderColor = '#fd7e14'; // Orange
                    badgeClass = 'bg-warning-transparent text-warning';
                } else if (statusLower.includes('not qualified')) {
                    borderColor = '#dc3545'; // Red
                    badgeClass = 'bg-danger-transparent text-danger';
                } else if (statusLower.includes('junk')) {
                    borderColor = '#6c757d'; // Gray
                    badgeClass = 'bg-secondary-transparent text-secondary';
                }

                // Extract action buttons and restyle
                const $actionDiv = $('<div>' + row.action + '</div>');
                const $convertBtn = $actionDiv.find('.convertDealRecord');
                const $editBtn = $actionDiv.find('.editRecord');
                const $deleteBtn = $actionDiv.find('.deleteRecord');
                const $timelineBtn = $actionDiv.find('button[onclick*="LeadTimeline.open"]');

                // View details button
                const $viewBtn = $(`
                    <a href="${detailsUrl}" class="btn btn-sm btn-light border text-primary flex-fill px-2 py-2 d-flex align-items-center justify-content-center" title="View Details" style="min-height: 44px; font-size: 13px; border-radius: 6px;">
                        <i class="fas fa-eye fs-14"></i>
                    </a>
                `);

                // Re-style existing buttons
                if ($timelineBtn.length) {
                    $timelineBtn.addClass('btn btn-sm btn-light border text-info flex-fill px-2 py-2')
                        .css({ 'min-height': '44px', 'font-size': '13px', 'border-radius': '6px' })
                        .html('<i class="fas fa-clock fs-14"></i>');
                }
                if ($editBtn.length) {
                    $editBtn.addClass('btn btn-sm btn-light border text-warning flex-fill px-2 py-2')
                        .css({ 'min-height': '44px', 'font-size': '13px', 'border-radius': '6px' })
                        .html('<i class="fas fa-edit fs-14"></i>');
                }
                if ($deleteBtn.length) {
                    $deleteBtn.addClass('btn btn-sm btn-light border text-danger flex-fill px-2 py-2')
                        .css({ 'min-height': '44px', 'font-size': '13px', 'border-radius': '6px' })
                        .html('<i class="fas fa-trash-alt fs-14"></i>');
                }
                if ($convertBtn.length) {
                    $convertBtn.addClass('btn btn-sm btn-light border text-success flex-fill px-2 py-2')
                        .css({ 'min-height': '44px', 'font-size': '13px', 'border-radius': '6px' })
                        .html('<i class="fas fa-handshake fs-14"></i>');
                }

                // Build actions container
                const $actionsContainer = $('<div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2 gap-1 flex-wrap w-100"></div>');
                if ($timelineBtn.length) $actionsContainer.append($timelineBtn);
                $actionsContainer.append($viewBtn);
                if ($editBtn.length) $actionsContainer.append($editBtn);
                if ($deleteBtn.length) $actionsContainer.append($deleteBtn);
                if ($convertBtn.length) $actionsContainer.append($convertBtn);

                const cardHtml = `
                    <div class="card lead-mobile-card mb-3" style="border-left: 4px solid ${borderColor} !important; border-radius: 8px; border: 1px solid #ebecf0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div class="card-body p-3">
                            <!-- Lead Name & Status -->
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold mb-0 text-primary" style="font-size: 15px;">
                                    <a href="${detailsUrl}" class="text-decoration-none text-primary">${leadName}</a>
                                </h6>
                                <span class="badge ${badgeClass}" style="font-size: 11px; padding: 4px 8px;">${statusTitle}</span>
                            </div>
                            
                            <!-- Lead ID • Created Date -->
                            <div class="text-muted mb-2" style="font-size: 12px;">
                                ${leadNo} &bull; ${leadDate}
                            </div>
                            
                            <!-- Created By | Assigned To -->
                            <div class="row g-2 mb-2 text-dark" style="font-size: 13px;">
                                <div class="col-6">
                                    <span class="text-muted small d-block" style="font-size: 11px;">Created By</span>
                                    <span class="text-truncate d-block fw-bold"><i class="fas fa-user-edit text-info me-1" style="font-size: 11px;"></i>${createdBy}</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small d-block" style="font-size: 11px;">Assigned To</span>
                                    <span class="text-truncate d-block fw-bold"><i class="fas fa-user-check text-success me-1" style="font-size: 11px;"></i>${assignedTo}</span>
                                </div>
                            </div>

                            <!-- Contact Person | Mobile Number -->
                            <div class="row g-2 mb-2 text-dark" style="font-size: 13px;">
                                <div class="col-6">
                                    <span class="text-muted small d-block" style="font-size: 11px;">Contact Person</span>
                                    <span class="d-block text-truncate"><i class="fas fa-user text-primary me-1" style="font-size: 11px;"></i>${contactPerson}</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small d-block" style="font-size: 11px;">Mobile Number</span>
                                    <span class="d-block text-truncate">
                                        ${phone ? `<a href="tel:${phone}" class="text-decoration-none text-dark"><i class="fas fa-phone-alt text-success me-1" style="font-size: 11px;"></i>${phone}</a>` : '-'}
                                    </span>
                                </div>
                            </div>

                            <!-- Company Name | City, State -->
                            <div class="row g-2 mb-2 text-dark" style="font-size: 13px;">
                                <div class="col-6">
                                    <span class="text-muted small d-block" style="font-size: 11px;">Company Name</span>
                                    <strong class="text-truncate d-block" title="${companyName}">${companyName}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small d-block" style="font-size: 11px;">City, State</span>
                                    <span class="d-block text-truncate"><i class="fas fa-map-marker-alt text-danger me-1" style="font-size: 11px;"></i>${cityState}</span>
                                </div>
                            </div>

                            <!-- Lead Source -->
                            <div class="mb-3 text-dark" style="font-size: 13px;">
                                <span class="text-muted small d-block" style="font-size: 11px;">Lead Source</span>
                                <span class="d-block text-truncate"><i class="fas fa-share-alt text-secondary me-1" style="font-size: 11px;"></i>${sourceTitle}</span>
                            </div>

                            <!-- Dynamic Action Buttons -->
                            <div class="card-actions-wrapper"></div>
                        </div>
                    </div>
                `;

                const $card = $(cardHtml);
                $card.find('.card-actions-wrapper').replaceWith($actionsContainer);
                $container.append($card);
            });
        }
    </script>

    <!-- Status Guide Modal -->
    <div class="modal fade" id="statusGuideModal" tabindex="-1" aria-labelledby="statusGuideModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="statusGuideModalLabel">
                        <i class="fas fa-info-circle text-primary me-2" style="font-size: 1.2rem;"></i>
                        <span>Lead Status Descriptions</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 border-start border-primary border-4 bg-light rounded shadow-xs">
                            <strong class="text-dark d-block mb-1" style="font-size: 14px;">Open</strong>
                            <span class="text-muted" style="font-size: 13px; line-height: 1.45;">The lead has been
                                created and is waiting for the first interaction by the sales person.</span>
                        </div>
                        <div class="p-3 border-start border-primary border-4 bg-light rounded shadow-xs">
                            <strong class="text-dark d-block mb-1" style="font-size: 14px;">Connected</strong>
                            <span class="text-muted" style="font-size: 13px; line-height: 1.45;">Initial contact has
                                been successfully established with the prospect.</span>
                        </div>
                        <div class="p-3 border-start border-primary border-4 bg-light rounded shadow-xs">
                            <strong class="text-dark d-block mb-1" style="font-size: 14px;">Contact in Future</strong>
                            <span class="text-muted" style="font-size: 13px; line-height: 1.45;">The prospect has
                                requested to be contacted at a later date.</span>
                        </div>
                        <div class="p-3 border-start border-primary border-4 bg-light rounded shadow-xs">
                            <strong class="text-dark d-block mb-1" style="font-size: 14px;">Qualified</strong>
                            <span class="text-muted" style="font-size: 13px; line-height: 1.45;">The lead meets the
                                qualification criteria and is ready to be converted into an opportunity.</span>
                        </div>
                        <div class="p-3 border-start border-primary border-4 bg-light rounded shadow-xs">
                            <strong class="text-dark d-block mb-1" style="font-size: 14px;">Not Qualified</strong>
                            <span class="text-muted" style="font-size: 13px; line-height: 1.45;">The lead does not meet
                                the qualification criteria.</span>
                        </div>
                        <div class="p-3 border-start border-primary border-4 bg-light rounded shadow-xs">
                            <strong class="text-dark d-block mb-1" style="font-size: 14px;">Junk</strong>
                            <span class="text-muted" style="font-size: 13px; line-height: 1.45;">Invalid, duplicate,
                                spam, or irrelevant lead.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-secondary fw-bold px-4" data-bs-dismiss="modal">Close
                        Guide</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>