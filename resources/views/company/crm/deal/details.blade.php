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
            border-color: #00b4b4 !important;
            color: #2d3748 !important;
            font-weight: bold;
        }
        .flatpickr-day.today:not(.selected):hover {
            background: #edf2f7 !important;
            color: #2d3748 !important;
        }

        .page-header-breadcrumb {
            position: relative;
            min-height: 0px !important;
            top: 0rem !important;
        }

        .card-custom-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #ebecf0;
            padding: 12px 16px;
            border-top-left-radius: 7px;
            border-top-right-radius: 7px;
        }

        .deal-detail-card {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
            border: 1px solid #e1e4e8;
            overflow: hidden;
        }

        .form-label-bold {
            font-weight: 600;
            color: #4a5568;
            font-size: 0.85rem;
        }

        /* Timeline style */
        .timeline-group {
            margin-bottom: 25px;
            position: relative;
        }

        .timeline-date-header {
            margin-bottom: 15px;
        }

        .timeline-date-badge {
            background-color: #f6ad55;
            color: #fff;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 50px;
            display: inline-block;
        }

        .timeline-items-container {
            position: relative;
            padding-left: 30px;
        }

        .timeline-line {
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item-row {
            position: relative;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .timeline-marker-circle {
            position: absolute;
            left: -27px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 3px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            z-index: 2;
        }

        .timeline-marker-circle.bg-success {
            background-color: #38a169 !important;
            color: #fff !important;
            box-shadow: 0 0 0 2px #38a169;
        }

        .timeline-marker-circle.bg-primary {
            background-color: #3182ce !important;
            color: #fff !important;
            box-shadow: 0 0 0 2px #3182ce;
        }

        .timeline-marker-circle.bg-info {
            background-color: #00b5ad !important;
            color: #fff !important;
            box-shadow: 0 0 0 2px #00b5ad;
        }

        .timeline-marker-circle.bg-warning {
            background-color: #dd6b20 !important;
            color: #fff !important;
            box-shadow: 0 0 0 2px #dd6b20;
        }

        .timeline-marker-circle.bg-danger {
            background-color: #e53e3e !important;
            color: #fff !important;
            box-shadow: 0 0 0 2px #e53e3e;
        }

        .timeline-marker-circle.bg-secondary {
            background-color: #718096 !important;
            color: #fff !important;
            box-shadow: 0 0 0 2px #718096;
        }

        .timeline-item-time {
            font-size: 0.85rem;
            font-weight: 500;
            color: #4a5568;
            min-width: 85px;
        }

        .timeline-item-description {
            font-size: 0.9rem;
            color: #2d3748;
        }

        .timeline-item-by .badge {
            font-size: 0.75rem;
            padding: 5px 12px;
            background-color: #f7fafc;
            border: 1px solid #edf2f7;
            color: #4a5568;
        }

        /* Force select2 dropdown ALWAYS below the input */
        .select2-container--open .select2-selection--single {
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        /* Override Select2 upward opening - force dropdown below */
        .select2-dropdown--above {
            border-radius: 0 0 4px 4px !important;
            border-top: 1px solid #aaa !important;
            border-bottom: 1px solid #aaa !important;
            top: 100% !important;
            bottom: auto !important;
            margin-top: 0 !important;
        }

        /* Prevent the selection from showing above-open styles */
        .select2-container--open.select2-container--above .select2-selection--single,
        .select2-container--open.select2-container--above .select2-selection--multiple {
            border-radius: 4px !important;
            border-bottom-color: #aaa !important;
        }

        /* Flatpickr calendar inside modal - ensure it stays visible and correctly positioned */
        #noteModal .flatpickr-calendar {
            z-index: 9999 !important;
        }

        .flatpickr-wrapper {
            display: block !important;
            width: 100% !important;
        }

        .flatpickr-calendar {
            z-index: 1060 !important;
        }

        /* Lock body scroll to prevent background movement when modal is open */
        body.modal-open {
            overflow: hidden !important;
        }
        /* Responsive Timeline styling for Mobile Screens */
        @media (max-width: 767.98px) {
            /* 1. Reduce overall padding to fit smaller width */
            .timeline-wrapper {
                padding: 10px 5px !important;
            }

            .timeline-items-container {
                padding-left: 20px; /* Reduce padding on mobile */
            }

            .timeline-line {
                left: 10px; /* Move vertical line closer to edge */
            }

            /* 2. Fix the row elements from breaking vertically */
            .timeline-item-row {
                flex-direction: column !important; /* Stack columns instead of jamming horizontally */
                align-items: flex-start !important;
                padding: 12px 15px;
            }

            /* Adjust relative position of the indicator icon */
            .timeline-marker-circle {
                left: -22px; /* Pull back into alignment with the line */
                top: 22px;   /* Lock near top instead of vertical middle */
                transform: none;
                width: 20px;
                height: 20px;
                font-size: 0.6rem;
            }

            /* Allow container inside the row to take up full available width */
            .timeline-item-row > div.flex-grow-1 {
                width: 100% !important;
            }

            /* 3. Stack the Time, Description, and Performed By badges cleanly */
            .timeline-item-row .d-flex.align-items-center {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 5px;
            }

            .timeline-item-time {
                font-size: 0.75rem;
                min-width: unset;
                margin-bottom: 2px;
                color: #718096 !important;
            }

            .timeline-item-description {
                font-size: 0.85rem;
                line-height: 1.4;
                word-break: break-word;
            }

            /* Move 'By user' badge below the message instead of squeezing next to it */
            .timeline-item-by {
                margin-left: 0 !important;
                margin-top: 8px !important;
                align-self: flex-start;
            }

            .timeline-item-by .badge {
                font-size: 0.7rem;
                padding: 4px 8px;
            }

            /* Adjust quotation metadata container on mobile */
            .timeline-item-row button, 
            .timeline-item-row a.btn {
                padding: 4px 8px !important;
                font-size: 10px !important;
            }
        }
    </style>
</head>

<body>
    @include('include.switcher')
    @include('include.loader')
    <div class="page">
        @include('include.header')

        <div class="page-header-breadcrumb d-md-flex d-block align-items-center justify-content-between "></div>

        <div class="main-content app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12 mt-1">
                        <div class="card custom-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="card-title text-primary mb-0">
                                    {{ $page_name }} ({{ $deal->deal_name }})
                                </div>
                                <ul class="nav nav-tabs justify-content-end nav-tabs-footer mb-0" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="overview-tab" data-bs-toggle="tab"
                                            href="#overview" role="tab" aria-controls="overview" aria-selected="true">
                                            <i class="fas fa-eye me-1"></i> Overview
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="timeline-tab" data-bs-toggle="tab" href="#timeline"
                                            role="tab" aria-controls="timeline" aria-selected="false">
                                            <i class="fas fa-history me-1"></i> Timeline
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link"
                                            href="{{ route('quotation.create', encrypt($deal->deal_id)) }}">
                                            <i class="fa-solid fa-quote-left"></i> Send Quotation
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">

                                    <!-- Overview Tab Pane -->
                                    <div class="tab-pane fade show active text-muted px-1" id="overview" role="tabpanel"
                                        aria-labelledby="overview-tab">
                                        <div class="row">
                                            <!-- Left Column: Forms -->
                                            <div class="col-lg-8 mb-3">

                                                <!-- Edit Deal Form -->
                                                <form id="dealDetailsForm" class="h-100" novalidate>
                                                    @csrf
                                                    <input type="hidden" name="deal_id" id="deal_id"
                                                        value="{{ $deal->deal_id }}">

                                                    <div class="card deal-detail-card h-100 mb-0">
                                                        <div
                                                            class="card-custom-header d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0 text-primary fw-bold"><i
                                                                    class="fas fa-edit me-1"></i> Edit Deal Details</h6>
                                                            <div class="d-flex gap-2">
                                                                <button type="button"
                                                                    class="btn btn-primary btn-sm px-3"
                                                                    id="btnToggleEdit">
                                                                    <i class="fas fa-pen me-1"></i> Edit
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-light border btn-sm px-3 d-none"
                                                                    id="btnCancelEdit">
                                                                    <i class="fas fa-times me-1"></i> Cancel
                                                                </button>
                                                                <button type="submit"
                                                                    class="btn btn-primary btn-sm px-3 d-none"
                                                                    id="saveDealDetailsBtn">
                                                                    <i class="fas fa-save me-1"></i> Update Details
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                 <div class="col-md-6 mb-3">
                                                                     <label for="deal_name"
                                                                         class="form-label form-label-bold">Deal Name / Products
                                                                         <span class="text-danger">*</span></label>
                                                                     <select class="form-select select2 deal-edit-field"
                                                                         name="product_ids[]" id="deal_name" multiple="multiple" required
                                                                         disabled>
                                                                         @foreach ($all_products as $prod)
                                                                             <option value="{{ $prod->product_id }}" {{ in_array($prod->product_id, $selected_product_ids) ? 'selected' : '' }}>
                                                                                 {{ $prod->prod_name }}
                                                                             </option>
                                                                         @endforeach
                                                                     </select>
                                                                 </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="closing_date"
                                                                        class="form-label form-label-bold">Closing Date
                                                                        <span class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        class="form-control deal-edit-field bg-light"
                                                                        name="closing_date" id="closing_date"
                                                                        value="{{ $deal->closing_date ? $deal->closing_date->format('d-m-Y') : '' }}"
                                                                        placeholder="Select date" required readonly
                                                                        disabled>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="deal_stage"
                                                                        class="form-label form-label-bold">Stage <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select select2 deal-edit-field"
                                                                        name="deal_stage" id="deal_stage" required
                                                                        disabled>
                                                                        @foreach ($stages as $stage)
                                                                            <option value="{{ $stage->id }}" {{ $deal->deal_stage == $stage->id ? 'selected' : '' }}>
                                                                                {{ $stage->stage_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label for="assigned_to_edit" class="form-label form-label-bold">Assigned Staff</label>
                                                                    <select class="form-select select2 deal-edit-field" name="assigned_to" id="assigned_to_edit" disabled>
                                                                        <option value="">Select Staff</option>
                                                                        @foreach ($staffs as $staff)
                                                                            <option value="{{ $staff->staff_id }}" {{ $deal->assigned_to == $staff->staff_id ? 'selected' : '' }}>
                                                                                {{ $staff->staff_name }} {{ $staff->staff_type ? '(' . ucfirst($staff->staff_type) . ')' : '' }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="deal_type" class="form-label form-label-bold">Deal Type</label>
                                                                    <select class="form-select select2 deal-edit-field" name="deal_type" id="deal_type" disabled>
                                                                        <option value="">Select Type</option>
                                                                        <option value="new" {{ $deal->deal_type == 'new' ? 'selected' : '' }}>New Business</option>
                                                                        <option value="existing" {{ $deal->deal_type == 'existing' ? 'selected' : '' }}>Existing Business</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label for="iwo_no" class="form-label form-label-bold">IWO No</label>
                                                                    <input type="text" class="form-control deal-edit-field bg-light" name="iwo_no" id="iwo_no" value="{{ $deal->iwo_no }}" placeholder="Enter IWO No" readonly disabled>
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label for="dispatch_date" class="form-label form-label-bold">Dispatch Date</label>
                                                                    <input type="text" class="form-control deal-edit-field bg-light" name="dispatch_date" id="dispatch_date" value="{{ $deal->dispatch_date ? \Carbon\Carbon::parse($deal->dispatch_date)->format('d-m-Y') : '' }}" placeholder="Enter Dispatch Date" readonly disabled>
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label for="industry_id" class="form-label form-label-bold">Industry</label>
                                                                    <select class="form-select select2 deal-edit-field" name="industry_id" id="industry_id" disabled>
                                                                        <option value="">-- Select Industry --</option>
                                                                        @foreach ($industries as $ind)
                                                                            <option value="{{ $ind->industry_type_id }}" {{ ($deal->lead && $deal->lead->industry_id == $ind->industry_type_id) ? 'selected' : '' }}>
                                                                                {{ $ind->industry_type_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                
                                                                 <div class="col-md-6 mb-3">
                                                                    <label for="city_id" class="form-label form-label-bold">City</label>
                                                                    <select class="form-select deal-edit-field" name="city_id" id="city_id" disabled>
                                                                        @if($deal->lead && $deal->lead->city_id)
                                                                            @php
                                                                                $selectedCity = collect($cities)->firstWhere('city_id', $deal->lead->city_id);
                                                                            @endphp
                                                                            @if($selectedCity)
                                                                                <option value="{{ $selectedCity->city_id }}" selected>{{ $selectedCity->city_name }}</option>
                                                                            @endif
                                                                        @endif
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label form-label-bold">State</label>
                                                                    @php
                                                                        $selectedState = $deal->lead && $deal->lead->state_id ? collect($states)->firstWhere('state_id', $deal->lead->state_id) : null;
                                                                    @endphp
                                                                    <input type="text" class="form-control bg-light" id="state_display" value="{{ $selectedState ? $selectedState->state_name : '' }}" readonly disabled>
                                                                    <input type="hidden" name="state_id" id="state_id" value="{{ $selectedState ? $selectedState->state_id : '' }}" disabled>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label form-label-bold">Country</label>
                                                                    @php
                                                                        $selectedCountry = $deal->lead && $deal->lead->countries_id ? collect($countries)->firstWhere('c_id', $deal->lead->countries_id) : null;
                                                                    @endphp
                                                                    <input type="text" class="form-control bg-light" id="country_display" value="{{ $selectedCountry ? $selectedCountry->country_name : '' }}" readonly disabled>
                                                                    <input type="hidden" name="countries_id" id="countries_id" value="{{ $selectedCountry ? $selectedCountry->c_id : '' }}" disabled>
                                                                </div>
                                                               
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="amount"
                                                                        class="form-label form-label-bold">Estimated
                                                                        Deal Amount</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">₹</span>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control bg-light" name="amount"
                                                                            id="amount" value="{{ $deal->amount }}"
                                                                            readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 mb-3">
                                                                     @include('components.tooltip-help', ['key' => 'deal_module_description', 'label' => 'Description', 'class' => 'form-label form-label-bold'])
                                                                    {{-- <label for="description"
                                                                        class="form-label form-label-bold">Description</label> --}}
                                                                    <textarea class="form-control deal-edit-field bg-light"
                                                                        name="description" id="description" rows="3"
                                                                        readonly>{{ $deal->description }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                            </div>

                                            <!-- Right Column: Notes & Attachments -->
                                            <div class="col-lg-4 mb-3">


                                                <!-- Attachments Section -->
                                                <div class="card deal-detail-card h-100 mb-0">
                                                    <div class="card-custom-header">
                                                        <h6 class="mb-0 text-primary fw-bold"><i
                                                                class="fas fa-paperclip me-1"></i> Attachments</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <form id="attachmentForm" enctype="multipart/form-data"
                                                            class="mb-3">
                                                            <div class="mb-2">
                                                                <div class="mb-2">
                                                                    <select class="form-select form-select-sm select2" id="attachmentType" data-minimum-results-for-search="Infinity">
                                                                        <option value="file">File Upload</option>
                                                                        <option value="link">Link Share</option>
                                                                    </select>
                                                                </div>
                                                                <div id="fileUploadWrapper">
                                                                    <input type="file" class="form-control form-control-sm"
                                                                        name="files[]" id="fileInput" multiple required onchange="validateFileSize(this)">
                                                                </div>
                                                                <div id="linkUploadWrapper" class="d-none">
                                                                    <input type="url" class="form-control form-control-sm"
                                                                        name="link" id="linkInput" placeholder="Enter URL (e.g., https://youtube.com/...)">
                                                                </div>
                                                            </div>
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm w-100"><i
                                                                    class="fas fa-upload me-1"></i> Upload File</button>
                                                        </form>

                                                        <div class="table-responsive"
                                                            style="max-height: 120px; overflow-y: auto;">
                                                            <table class="table table-bordered table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>File Name</th>
                                                                        <th class="text-center">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="attachmentsTableBody">
                                                                    <!-- Dynamic Content -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- Full-width Notes Section -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card deal-detail-card mb-3">
                                                    <div
                                                        class="card-custom-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-primary fw-bold"><i
                                                                class="fas fa-sticky-note me-1"></i> Notes</h6>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <button class="btn btn-info btn-sm fw-bold px-3"
                                                                id="btnViewAllNotes" title="View Notes">
                                                                <i class="fas fa-eye me-1"></i> View
                                                            </button>
                                                            <div class="dropdown">
                                                                <button class="btn btn-secondary btn-sm fw-bold  px-3"
                                                                    type="button" id="btnDownloadNotesDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Download Notes">
                                                                    <i class="fas fa-download me-1"></i> Download
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnDownloadNotesDropdown">
                                                                    <li>
                                                                        <a class="dropdown-item" href="{{ route('crm.activity.notes.export.excel', ['reference_type' => 'deal', 'reference_id' => $deal->deal_id]) }}">
                                                                            <i class="far fa-file-excel text-success me-2"></i> Excel
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item" href="{{ route('crm.activity.notes.export.pdf', ['reference_type' => 'deal', 'reference_id' => $deal->deal_id]) }}" target="_blank">
                                                                            <i class="far fa-file-pdf text-danger me-2"></i> PDF
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <button class="btn btn-primary btn-sm fw-bold px-3"
                                                                id="btnNewNote" data-bs-toggle="modal"
                                                                data-bs-target="#noteModal">
                                                                <i class="fas fa-plus me-1"></i> Add Note
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive"
                                                            style="max-height: 400px; overflow-y: auto;">
                                                            <table class="table table-bordered table-striped"
                                                                id="notesTable" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="5%">#</th>
                                                                        <th width="50%">NOTE</th>
                                                                        <th width="30%">CREATED BY & TIME</th>
                                                                        <th width="15%" class="text-center">ACTIONS</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="notesTableBody">
                                                                    <!-- Dynamic Content -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Full-width Calls List Section -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card deal-detail-card mb-3">
                                                    <div
                                                        class="card-custom-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-primary fw-bold"><i
                                                                class="fas fa-phone-alt me-1"></i> Schedule Call</h6>
                                                        <button class="btn btn-primary btn-sm" id="btnScheduleCall"
                                                            data-bs-toggle="modal" data-bs-target="#scheduleCallModal">
                                                            <i class="fas fa-calendar-plus me-1"></i> Schedule Call
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive"
                                                            style="max-height: 400px; overflow-y: auto;">
                                                            <table class="table table-bordered table-striped"
                                                                id="callsTable" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="5%">#</th>
                                                                        <th width="20%">SUBJECT</th>
                                                                        <th width="15%">CALL DATE</th>
                                                                        <th width="20%">AGENDA</th>
                                                                        <th width="25%">CALL INFO</th>
                                                                        <th width="15%" class="text-center">ACTIONS</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="callsTableBody">
                                                                    <!-- Dynamic Content -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Timeline Tab Pane -->
                                    <div class="tab-pane fade text-muted px-1" id="timeline" role="tabpanel"
                                        aria-labelledby="timeline-tab">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card deal-detail-card">
                                                    <div class="card-custom-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-primary fw-bold"><i
                                                                class="fas fa-history me-1"></i> Deal Timeline & Logs
                                                        </h6>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-outline-primary  d-flex align-items-center gap-1" type="button" id="dealTimelineDownloadBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-download"></i> Download
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dealTimelineDownloadBtn">
                                                                <li><a class="dropdown-item" href="/lead-timeline/download?format=excel&dealId={{ $deal->deal_id }}" target="_blank"><i class="far fa-file-excel text-success me-2"></i> Excel</a></li>
                                                                <li><a class="dropdown-item" href="/lead-timeline/download?format=pdf&dealId={{ $deal->deal_id }}" target="_blank"><i class="far fa-file-pdf text-danger me-2"></i> PDF</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="timeline-wrapper p-3" id="timelineContainer"
                                                            style="max-height: 600px; overflow-y: auto;">
                                                            <!-- Dynamic Logs -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('include.footer')
    </div>

    <!-- Schedule Call Modal -->
    <div class="modal fade" id="scheduleCallModal" tabindex="-1" aria-labelledby="scheduleCallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="scheduleCallForm" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="call_edit_id" value="">
                    <input type="hidden" name="deal_id" value="{{ $deal->deal_id }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="scheduleCallModalLabel"><i
                                class="fas fa-phone-alt text-primary me-2"></i><span id="callModalTitleAction">Schedule a</span> Call</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="card-title text-muted mb-4 border-bottom pb-2">
                            <i class="fas fa-info-circle me-1"></i> Call Information
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="call_date" class="form-label form-label-bold">Call Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="call_date" id="call_date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="call_time" class="form-label form-label-bold">Call Time</label>
                                <input type="time" class="form-control" name="call_time" id="call_time" value="">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="call_subject" class="form-label form-label-bold">Subject <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="subject" id="call_subject"
                                    placeholder="Enter call subject" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="call_agenda" class="form-label form-label-bold">Call Agenda</label>
                                <textarea class="form-control" name="call_agenda" id="call_agenda" rows="3"
                                    placeholder="Describe the agenda..."></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="call_assigned_to" class="form-label form-label-bold">Assign To</label>
                                <select class="form-select select2-modal" name="assigned_to" id="call_assigned_to">
                                    <option value="">Select Staff</option>
                                    @php
                                        $currentDealType = $deal->deal_type ?: ($deal->lead ? $deal->lead->lead_type : '');
                                    @endphp
                                    @foreach ($staffs as $staff)
                                        @if(empty($currentDealType) || empty($staff->staff_type) || $staff->staff_type === 'Both' || strtolower($staff->staff_type) === strtolower($currentDealType))
                                            <option value="{{ $staff->staff_id }}" {{ $deal->assigned_to == $staff->staff_id ? 'selected' : '' }} data-type="{{ $staff->staff_type }}">
                                                {{ $staff->staff_name }}{{ $staff->staff_type ? ' (' . ucfirst($staff->staff_type) . ')' : '' }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitCall"><i
                                class="fas fa-calendar-check me-1"></i> Save Call Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Note Modal -->
    <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="noteModalForm" novalidate>
                    @csrf
                    <input type="hidden" name="deal_id" value="{{ $deal->deal_id }}">
                    <input type="hidden" name="id" id="note_edit_id" value="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="noteModalLabel"><i
                                class="fas fa-sticky-note text-primary me-2"></i><span
                                id="noteModalTitleAction">Add</span> Note</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="note_title" class="form-label form-label-bold">Title <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="title" id="note_title" required>
                                <option value="">Select Note Title</option>
                                 <option value="__create_new_title__">+ Create New Title</option>
                                @foreach ($noteTitles as $nt)
                                    <option value="{{ $nt->note_title }}">{{ $nt->note_title }}</option>
                                @endforeach
                               
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="noteModalInput" class="form-label form-label-bold">Note <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="note" id="noteModalInput" rows="5"
                                placeholder="Type your note here..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="note_assigned_to" class="form-label form-label-bold">Assign To</label>
                            <select class="form-select select2-modal" name="assigned_to" id="note_assigned_to">
                                <option value="">Select Staff</option>
                                @foreach ($staffs as $staff)
                                    @if(empty($currentDealType) || empty($staff->staff_type) || $staff->staff_type === 'Both' || strtolower($staff->staff_type) === strtolower($currentDealType))
                                        <option value="{{ $staff->staff_id }}" data-type="{{ $staff->staff_type }}">
                                            {{ $staff->staff_name }}{{ $staff->staff_type ? ' (' . ucfirst($staff->staff_type) . ')' : '' }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="noteCreatedDateContainer">
                            <label for="note_created_at" class="form-label form-label-bold">Created Date <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="created_at" id="note_created_at"
                                placeholder="Select Created Date" required>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggleFollowUpDate">
                                <label class="form-check-label form-label-bold" for="toggleFollowUpDate">Add Follow Up Date?</label>
                            </div>
                        </div>
                        <div class="mb-3 d-none" id="followUpDateContainer">
                            <label for="follow_up_date" class="form-label form-label-bold">Follow Up Date <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="follow_up_date" id="follow_up_date"
                                placeholder="Select Follow Up Date">
                        </div>
                        <div class="mb-3">
                            <label class="form-label form-label-bold">Attachment Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="attachment_type" id="note_attachment_type">
                                <option value="file" selected>File Upload</option>
                                <option value="link">Link Share</option>
                            </select>
                        </div>
                        <div class="mb-3" id="noteFileAttachmentContainer">
                            <label for="note_attachment" class="form-label form-label-bold">Attachment </label>
                            <input type="file" class="form-control form-control-sm" name="attachment" id="note_attachment" onchange="validateFileSize(this)" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png,.jpeg,.zip,.mp4">
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Allowed types: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, JPEG, ZIP, MP4. (Max 10MB)</small>
                            <div id="existingNoteAttachmentContainer" class="mt-2 d-none">
                                <a href="#" id="existingNoteAttachmentLink" target="_blank" class="badge bg-info text-decoration-none"><i class="fas fa-paperclip me-1"></i>View Current Attachment</a>
                            </div>
                        </div>
                        <div class="mb-3 d-none" id="noteLinkAttachmentContainer">
                            <label for="note_link" class="form-label form-label-bold">URL </label>
                            <input class="form-control" type="url" id="note_link" name="link" placeholder="Enter URL (e.g., https://youtube.com/...)">
                            <div id="existingNoteLinkContainer" class="mt-2 d-none">
                                <a href="#" id="existingNoteLinkHref" target="_blank" class="badge bg-info text-decoration-none"><i class="fas fa-link me-1"></i>View Current Link</a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitNote"><i
                                class="fas fa-check me-1"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Add Note Title Modal -->
    <div class="modal fade" id="quickAddNoteTitleModal" tabindex="-1" aria-labelledby="quickAddNoteTitleModalLabel"
        aria-hidden="true" data-bs-backdrop="false" style="z-index: 1070;">
        <div class="modal-dialog  modal-sm">
            <div class="modal-content shadow-lg border">
                <form id="quickAddNoteTitleForm" novalidate>
                    @csrf
                    <div class="modal-header py-2">
                        <h6 class="modal-title fw-bold" id="quickAddNoteTitleModalLabel"><i
                                class="fas fa-plus-circle text-primary me-2"></i>Add Note Title</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3">
                        <div class="mb-2">
                            <label for="quick_note_title_input" class="form-label form-label-bold">Note Title <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="note_title" id="quick_note_title_input"
                                placeholder="ENTER NOTE TITLE" required style="text-transform: uppercase;">
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-light border btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btnSaveQuickNoteTitle"><i
                                class="fas fa-check me-1"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Call Result Modal -->
    <div class="modal fade" id="callResultModal" tabindex="-1" aria-labelledby="callResultModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="callResultForm" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="result_call_id" value="">
                    <input type="hidden" name="call_status" id="result_call_status" value="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="callResultModalLabel"><i
                                class="fas fa-comment-dots text-primary me-2"></i>Call Result</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="callResultInput" class="form-label form-label-bold">Call Result <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="call_result" id="callResultInput" rows="5"
                                placeholder="Type call result details here..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitCallResult"><i
                                class="fas fa-check me-1"></i> Save Result</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

            <!-- Modal Complete Note Follow-up -->
            <div class="modal fade" id="modalCompleteNote" tabindex="-1" aria-labelledby="modalCompleteNoteLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-light border-bottom-0">
                            <h5 class="modal-title" id="modalCompleteNoteLabel">
                                <i class="fas fa-check-double text-primary me-2"></i>Follow-up Result
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form id="formCompleteNote">
                                @csrf
                                <input type="hidden" name="id" id="completeNoteId">
                                <input type="hidden" name="is_completed" id="completeNoteStatus">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Result/Outcome Notes </label>
                                    <textarea class="form-control bg-light" name="result" id="completeNoteResult" rows="3" placeholder="Enter the result of this follow-up..."></textarea>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" id="btnSubmitNoteResult">
                                        <i class="fas fa-check me-1"></i> Save Result
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

    @include('include.scroll-top')
    @include('include.footer_link')
    @include('include.datatable_js_link')


    <script>
        // Global file size validation – defined outside document.ready so inline onchange can always access it
        // Uses decimal MB (10,000,000 bytes) to match what macOS/Windows show users as "10 MB"
        function validateFileSize(input) {
            var maxBytes = 10 * 1000 * 1000; // 10MB in decimal (matches OS file size display)
            var files = input.files;
            if (files && files.length > 0) {
                for (var i = 0; i < files.length; i++) {
                    if (files[i].size > maxBytes) {
                        if (typeof iziToast !== 'undefined') {
                            iziToast.error({
                                title: 'File Too Large',
                                message: 'File "' + files[i].name + '" exceeds the maximum allowed size of 10MB. Please select a smaller file.',
                                position: 'topRight',
                                timeout: 5000
                            });
                        } else {
                            alert('File "' + files[i].name + '" exceeds the maximum allowed size of 10MB.');
                        }
                        input.value = '';
                        return false;
                    }
                }
            }
            return true;
        }
    </script>

    <script>
        $(document).ready(function () {
            // Flatpickr for Closing Date
            flatpickr('#closing_date', {
                dateFormat: 'd-m-Y',
                allowInput: false,
                disableMobile: true,
                defaultDate: $('#closing_date').val() ? $('#closing_date').val() : new Date()
            });

            // Flatpickr for Note Follow Up Date
            var followUpFlatpickr = flatpickr('#follow_up_date', {
                dateFormat: 'd-m-Y',
                allowInput: false,
                disableMobile: true
            });

            // Flatpickr for Note Created Date
            var createdDateFlatpickr = flatpickr('#note_created_at', {
                dateFormat: 'd-m-Y',
                allowInput: false,
                disableMobile: true
            });

            // CSRF Token Setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize Select2 - always open downward
            $('.select2').each(function () {
                var $this = $(this);
                var minSearch = $this.attr('data-minimum-results-for-search');
                var options = { width: '100%' };
                if (minSearch !== undefined) {
                    options.minimumResultsForSearch = minSearch === 'Infinity' ? Infinity : parseInt(minSearch);
                }
                $this.select2(options);
            });

            // Modal Select2
            $('#scheduleCallModal, #noteModal').on('shown.bs.modal', function () {
                var $modal = $(this);
                $modal.find('.select2-modal').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            dropdownParent: $modal,
                            width: '100%'
                        });
                    }
                });
            });

            // --- DEFINITIVE FIX: Force ALL Select2 dropdowns to always open DOWNWARD ---
            // Select2 appends to body with absolute positioning. When it detects insufficient
            // space below, it adds .select2-dropdown--above and flips the top value to open upward.
            // We intercept this and recalculate top using getBoundingClientRect + scrollY.
            $(document).on('select2:open', function () {
                setTimeout(function () {
                    var $dropdown = $('.select2-dropdown--above');
                    if ($dropdown.length) {
                        var $openContainer = $('.select2-container--open');
                        var $selection = $openContainer.find('.select2-selection');
                        if ($selection.length) {
                            var rect = $selection[0].getBoundingClientRect();
                            var scrollY = window.pageYOffset || document.documentElement.scrollTop;
                            // Force dropdown exactly below the selection input
                            $dropdown.css({
                                'top': (rect.bottom + scrollY) + 'px',
                                'bottom': 'auto'
                            });
                        }
                    }
                }, 10);
            });

            const dealId = "{{ $deal->deal_id }}";

            // Load Sub-features
            loadCalls();
            loadNotes();
            loadAttachments();

            // Initialize Select2 for City using AJAX
            $('#city_id').select2({
                ajax: {
                    url: "{{ route('location.cities') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: $.map(data.data, function (item) {
                                return {
                                    id: item.city_id,
                                    text: item.city_name
                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Search City',
                minimumInputLength: 2,
                width: '100%'
            });

            // Auto-focus the search input when City dropdown opens (so user can type immediately)
            $('#city_id').on('select2:open', function () {
                setTimeout(function () {
                    var searchField = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchField) {
                        searchField.focus();
                    }
                }, 50);
            });

            // When City is selected, automatically fetch and fill State & Country (readonly fields)
            $('#city_id').on('change', function () {
                const cityId = $(this).val();
                if (cityId) {
                    $.get('/location/city/' + cityId, function (res) {
                        if (res.status) {
                            const data = res.data;

                            // Fill State readonly display + hidden id
                            if (data.state_id) {
                                $('#state_display').val(data.state_name || '');
                                $('#state_id').val(data.state_id);
                            } else {
                                $('#state_display').val('');
                                $('#state_id').val('');
                            }

                            // Fill Country readonly display + hidden id
                            if (data.country_id) {
                                $('#country_display').val(data.country_name || '');
                                $('#countries_id').val(data.country_id);
                            } else {
                                $('#country_display').val('');
                                $('#countries_id').val('');
                            }
                        }
                    });
                } else {
                    // Clear state and country if city is cleared
                    $('#state_display').val('');
                    $('#state_id').val('');
                    $('#country_display').val('');
                    $('#countries_id').val('');
                }
            });

            // Store original values for cancel
            var originalDealValues = {};

            // Helper to lock all editable fields
            function lockDealFields() {
                $('#deal_name').prop('disabled', true).trigger('change');
                $('#closing_date').prop('disabled', true).addClass('bg-light');
                $('#deal_stage').prop('disabled', true).trigger('change');
                $('#description').prop('readonly', true).addClass('bg-light');
                
                $('#assigned_to_edit, #deal_type, #industry_id, #countries_id, #state_id, #city_id').prop('disabled', true).trigger('change');
                $('#iwo_no').prop('disabled', true).prop('readonly', true).addClass('bg-light');
                $('#dispatch_date').prop('disabled', true).prop('readonly', true).addClass('bg-light');

                $('#btnToggleEdit').removeClass('d-none');
                $('#btnCancelEdit').addClass('d-none');
                $('#saveDealDetailsBtn').addClass('d-none');
            }

            // Helper to unlock all editable fields
            function unlockDealFields() {
                $('#deal_name').prop('disabled', false).trigger('change');
                $('#closing_date').prop('disabled', false).removeClass('bg-light');
                $('#deal_stage').prop('disabled', false).trigger('change');
                $('#description').prop('readonly', false).removeClass('bg-light');

                $('#assigned_to_edit, #deal_type, #industry_id, #countries_id, #state_id, #city_id').prop('disabled', false).trigger('change');
                $('#iwo_no').prop('disabled', false).prop('readonly', false).removeClass('bg-light');
                $('#dispatch_date').prop('disabled', false).prop('readonly', false).removeClass('bg-light');

                // Reinitialise flatpickr for the date field
                flatpickr('#closing_date', {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    disableMobile: true,
                    defaultDate: $('#closing_date').val() ? $('#closing_date').val() : new Date()
                });

                flatpickr('#dispatch_date', {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    disableMobile: true,
                    defaultDate: $('#dispatch_date').val() ? $('#dispatch_date').val() : ''
                });

                $('#btnToggleEdit').addClass('d-none');
                $('#btnCancelEdit').removeClass('d-none');
                $('#saveDealDetailsBtn').removeClass('d-none');
            }

            // Toggle Edit button
            $('#btnToggleEdit').on('click', function () {
                // Save current values for cancel
                originalDealValues = {
                    product_ids: $('#deal_name').val(),
                    closing_date: $('#closing_date').val(),
                    deal_stage: $('#deal_stage').val(),
                    description: $('#description').val(),
                    assigned_to: $('#assigned_to_edit').val(),
                    deal_type: $('#deal_type').val(),
                    iwo_no: $('#iwo_no').val(),
                    dispatch_date: $('#dispatch_date').val(),
                    industry_id: $('#industry_id').val(),
                    countries_id: $('#countries_id').val(),
                    state_id: $('#state_id').val(),
                    city_id: $('#city_id').val(),
                    country_display: $('#country_display').val(),
                    state_display: $('#state_display').val()
                };
                unlockDealFields();
            });

            // Cancel Edit button
            $('#btnCancelEdit').on('click', function () {
                // Restore original values
                $('#deal_name').val(originalDealValues.product_ids).trigger('change');
                $('#closing_date').val(originalDealValues.closing_date);
                $('#deal_stage').val(originalDealValues.deal_stage).trigger('change');
                $('#description').val(originalDealValues.description);
                $('#assigned_to_edit').val(originalDealValues.assigned_to).trigger('change');
                $('#deal_type').val(originalDealValues.deal_type).trigger('change');
                $('#iwo_no').val(originalDealValues.iwo_no);
                $('#dispatch_date').val(originalDealValues.dispatch_date);
                $('#industry_id').val(originalDealValues.industry_id).trigger('change');
                $('#countries_id').val(originalDealValues.countries_id);
                $('#country_display').val(originalDealValues.country_display);
                $('#state_id').val(originalDealValues.state_id);
                $('#state_display').val(originalDealValues.state_display);
                
                // For city_id, if we change it back to original without firing the select2 AJAX change,
                // we should prevent the change event from firing if possible, but triggering change is fine.
                // However, wait, if we trigger change, the on('change') will fetch state/country!
                // To avoid fetching during cancel, we just restore everything.
                $('#city_id').val(originalDealValues.city_id).trigger('change.select2');
                lockDealFields();
            });

            // Update Deal details form submission
            $('#dealDetailsForm').submit(function (e) {
                e.preventDefault();

                const productsSelected = $('#deal_name').val();
                const closingDate = $('#closing_date').val();
                const stage = $('#deal_stage').val();

                if (!productsSelected || productsSelected.length === 0) {
                    iziToast.error({ title: 'Validation Error', message: 'At least one product must be selected.', position: 'topRight' });
                    return false;
                }
                if (!closingDate) {
                    iziToast.error({ title: 'Validation Error', message: 'Closing Date is required.', position: 'topRight' });
                    return false;
                }
                if (!stage) {
                    iziToast.error({ title: 'Validation Error', message: 'Stage is required.', position: 'topRight' });
                    return false;
                }

                $('#saveDealDetailsBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');

                // Temporarily enable disabled fields for serialization
                $('#deal_stage, #deal_name, #assigned_to_edit, #deal_type, #industry_id, #countries_id, #state_id, #city_id, #iwo_no, #dispatch_date').prop('disabled', false);
                const formData = $('#dealDetailsForm').serialize();
                $('#deal_stage, #deal_name, #assigned_to_edit, #deal_type, #industry_id, #countries_id, #state_id, #city_id, #iwo_no, #dispatch_date').prop('disabled', true);

                $.ajax({
                    url: "{{ route('deal.details.update', ['id' => $deal->deal_id]) }}",
                    type: 'POST',
                    data: formData,
                    success: function (res) {
                        $('#saveDealDetailsBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update Details');
                        if (res.success) {
                            iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                            if (res.amount !== undefined) {
                                $('#amount').val(res.amount);
                            }
                            lockDealFields();
                            loadLogs();
                        } else {
                            iziToast.error({ title: 'Error', message: res.message || 'Failed to update deal', position: 'topRight' });
                        }
                    },
                    error: function (xhr) {
                        $('#saveDealDetailsBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update Details');
                       if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            
                            // Loop through each field error and display an iziToast message
                            $.each(errors, function (field, messages) {
                                messages.forEach(function (msg) {
                                    iziToast.error({
                                        title: 'Validation Error',
                                        message: msg,
                                        position: 'topRight'
                                    });
                                });
                            });
                        } else {
                            // Fallback for general 500 or network errors
                            const errorMessage = xhr.responseJSON?.message || 'Server error occurred.';
                            iziToast.error({
                                title: 'Error',
                                message: errorMessage,
                                position: 'topRight'
                            });
                        }
                    }
                });
            });

            // Edit Call button click
            $(document).on('click', '.btn-edit-call', function () {
                const call = $(this).data('call');
                $('#call_edit_id').val(call.id);
                
                // Format date to YYYY-MM-DD for input[type="date"]
                let d = call.call_date;
                if (d) {
                    let parts = d.split('-');
                    if (parts.length === 3) {
                        if (parts[0].length === 4) {
                            $('#call_date').val(d);
                        } else {
                            $('#call_date').val(parts[2] + '-' + parts[1] + '-' + parts[0]);
                        }
                    } else {
                        $('#call_date').val(d);
                    }
                } else {
                    $('#call_date').val('');
                }
                
                $('#call_time').val(call.call_time || '');
                $('#call_subject').val(call.subject);
                $('#call_agenda').val(call.call_agenda || '');
                $('#call_assigned_to').val(call.assigned_to || '').trigger('change');
                
                $('#callModalTitleAction').text('Edit');
                $('#btnSubmitCall').html('<i class="fas fa-check me-1"></i> Update');
                $('#scheduleCallModal').modal('show');
            });

            // Add new Call button click
            $('#btnScheduleCall').click(function () {
                $('#scheduleCallForm').trigger('reset');
                $('#call_edit_id').val('');
                $('#call_assigned_to').val('').trigger('change');
                $('#callModalTitleAction').text('Schedule a');
                $('#btnSubmitCall').html('<i class="fas fa-check me-1"></i> Schedule');
            });

            // Schedule Call
            $('#scheduleCallForm').submit(function (e) {
                e.preventDefault();

                const date = $('#call_date').val();
                const time = $('#call_time').val();
                const subject = $('#call_subject').val().trim();

                if (!date) {
                    iziToast.error({ title: 'Validation Error', message: 'Call Date is required.', position: 'topRight' });
                    return false;
                }
                if (!subject) {
                    iziToast.error({ title: 'Validation Error', message: 'Subject is required.', position: 'topRight' });
                    return false;
                }

                const isEdit = $('#call_edit_id').val() !== '';
                $('#btnSubmitCall').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> ' + (isEdit ? 'Updating...' : 'Scheduling...'));
                
                let url = isEdit ? "{{ route('deal.details.update-call') }}" : "{{ route('deal.details.schedule-call') }}";

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (res) {
                        $('#btnSubmitCall').prop('disabled', false).html('<i class="fas fa-check me-1"></i> ' + (isEdit ? 'Update' : 'Schedule'));
                        if (res.success) {
                            iziToast.success({
                                title: 'Success',
                                message: res.message,
                                position: 'topRight'
                            });
                            $('#scheduleCallModal').modal('hide');
                            $('#scheduleCallForm')[0].reset();
                            $('#call_assigned_to').val('').trigger('change');
                            loadCalls();
                            loadLogs();
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: res.message,
                                position: 'topRight'
                            });
                        }
                    },
                    error: function (xhr) {
                        $('#btnSubmitCall').prop('disabled', false).html('<i class="fas fa-check me-1"></i> ' + (isEdit ? 'Update' : 'Schedule'));
                        iziToast.error({
                            title: 'Error',
                            message: xhr.responseJSON?.message || 'Server error',
                            position: 'topRight'
                        });
                    }
                });
            });

            $('#toggleFollowUpDate').change(function () {
                if ($(this).is(':checked')) {
                    $('#followUpDateContainer').removeClass('d-none');
                } else {
                    $('#followUpDateContainer').addClass('d-none');
                    $('#follow_up_date').val('');
                    if (followUpFlatpickr) {
                        followUpFlatpickr.clear();
                    }
                }
            });

            // Quick Add Note Title modal & option selection handling
            var previousNoteTitleVal = '';

            $('#note_title').on('select2:selecting', function (e) {
                var currentVal = $(this).val();
                if (currentVal !== '__create_new_title__') {
                    previousNoteTitleVal = currentVal;
                }
            });

            $('#note_title').on('change', function () {
                var selectedVal = $(this).val();
                if (selectedVal === '__create_new_title__') {
                    $('#quick_note_title_input').val('');
                    $('#quickAddNoteTitleModal').modal('show');
                }
            });

            $('#quickAddNoteTitleModal').on('shown.bs.modal', function () {
                $('#quick_note_title_input').focus();
            });

            $('#quickAddNoteTitleModal').on('hidden.bs.modal', function () {
                // If __create_new_title__ is still selected, revert to previous selection
                if ($('#note_title').val() === '__create_new_title__') {
                    $('#note_title').val(previousNoteTitleVal || '').trigger('change');
                }
                // Ensure parent note modal remains active and scrollable
                if ($('#noteModal').hasClass('show')) {
                    $('body').addClass('modal-open');
                }
            });

            $('#quickAddNoteTitleForm').submit(function (e) {
                e.preventDefault();
                var rawVal = $('#quick_note_title_input').val().trim();
                if (!rawVal) {
                    iziToast.error({
                        title: 'Validation Error',
                        message: 'Note Title is required.',
                        position: 'topRight'
                    });
                    return;
                }

                var upperTitle = rawVal.toUpperCase();
                var $btn = $('#btnSaveQuickNoteTitle');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    url: "{{ route('note-titles.store') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        note_title: upperTitle
                    },
                    success: function (res) {
                        $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save');
                        if (res.status === 'success') {
                            iziToast.success({
                                title: 'Success',
                                message: res.message || 'Note Title created successfully.',
                                position: 'topRight'
                            });

                            var createdTitle = upperTitle;
                            if (res.data && res.data.length > 0 && res.data[0].note_title) {
                                createdTitle = res.data[0].note_title;
                            }

                            var $select = $('#note_title');
                            if ($select.find('option[value="' + createdTitle + '"]').length === 0) {
                                var $createOpt = $select.find('option[value="__create_new_title__"]');
                                var newOption = new Option(createdTitle, createdTitle, false, false);
                                if ($createOpt.length > 0) {
                                    $(newOption).insertBefore($createOpt);
                                } else {
                                    $select.append(newOption);
                                }
                            }

                            previousNoteTitleVal = createdTitle;
                            $select.val(createdTitle).trigger('change');
                            $('#quickAddNoteTitleModal').modal('hide');
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: res.message || 'Failed to save Note Title.',
                                position: 'topRight'
                            });
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save');
                        var msg = 'Failed to save Note Title.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                var firstKey = Object.keys(xhr.responseJSON.errors)[0];
                                msg = xhr.responseJSON.errors[firstKey][0];
                            }
                        }
                        iziToast.error({
                            title: 'Error',
                            message: msg,
                            position: 'topRight'
                        });
                    }
                });
            });

            $(document).on('change', '#note_attachment_type', function() {
                if ($(this).val() === 'link') {
                    $('#noteFileAttachmentContainer').addClass('d-none');
                    $('#noteLinkAttachmentContainer').removeClass('d-none');
                } else {
                    $('#noteFileAttachmentContainer').removeClass('d-none');
                    $('#noteLinkAttachmentContainer').addClass('d-none');
                }
            });

            // Add/Edit Note Modal triggers
            $('#btnNewNote').click(function () {
                $('#noteModalForm')[0].reset();
                $('#note_edit_id').val('');
                $('#existingNoteAttachmentContainer').addClass('d-none');
                $('#existingNoteLinkContainer').addClass('d-none');
                $('#note_attachment_type').val('file').trigger('change');
                $('#existingNoteAttachmentLink').attr('href', '#');
                previousNoteTitleVal = '';
                $('#note_title').val('').trigger('change');

                // Set Created Date to today by default
                const today = new Date();
                const dd = String(today.getDate()).padStart(2, '0');
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const yyyy = today.getFullYear();
                const todayStr = `${dd}-${mm}-${yyyy}`;
                $('#note_created_at').val(todayStr);
                if (createdDateFlatpickr) {
                    createdDateFlatpickr.setDate(todayStr, false);
                }

                $('#toggleFollowUpDate').prop('checked', false).trigger('change');
                $('#note_assigned_to').val('').trigger('change');
                $('#noteModalTitleAction').text('Add');
            });

            $(document).on('click', '.edit-note-btn', function () {
                const id = $(this).data('id');
                const title = $(this).data('title') || '';
                const note = decodeURIComponent($(this).data('note'));
                const followUpDate = $(this).data('follow-up-date');
                const createdAtDate = $(this).data('created-at');
                const assignedTo = $(this).data('assigned-to');
                const attachment = $(this).data('attachment');

                $('#note_edit_id').val(id);
                $('#note_attachment_type').val('file').trigger('change');
                if (attachment) {
                    if (attachment.startsWith('http://') || attachment.startsWith('https://')) {
                        $('#note_attachment_type').val('link').trigger('change');
                        $('#existingNoteLinkContainer').removeClass('d-none');
                        $('#existingNoteLinkHref').attr('href', attachment);
                        $('#note_link').val(attachment);
                    } else {
                        $('#existingNoteAttachmentContainer').removeClass('d-none');
                        $('#existingNoteAttachmentLink').attr('href', "{{ url('assets/uploads/notes/') }}/" + attachment);
                    }
                } else {
                    $('#existingNoteAttachmentContainer').addClass('d-none');
                    $('#existingNoteLinkContainer').addClass('d-none');
                    $('#existingNoteAttachmentLink').attr('href', '#');
                }
                
                if (title && $('#note_title option[value="' + title + '"]').length === 0) {
                    var $createOpt = $('#note_title option[value="__create_new_title__"]');
                    var existingOption = new Option(title, title, false, false);
                    if ($createOpt.length > 0) {
                        $(existingOption).insertBefore($createOpt);
                    } else {
                        $('#note_title').append(existingOption);
                    }
                }
                previousNoteTitleVal = title;
                $('#note_title').val(title).trigger('change');
                $('#noteModalInput').val(note);
                $('#note_assigned_to').val(assignedTo || '').trigger('change');

                if (createdAtDate) {
                    $('#note_created_at').val(createdAtDate);
                    if (createdDateFlatpickr) {
                        createdDateFlatpickr.setDate(createdAtDate, false);
                    }
                } else {
                    $('#note_created_at').val('');
                    if (createdDateFlatpickr) {
                        createdDateFlatpickr.clear();
                    }
                }

                if (followUpDate && followUpDate !== 'N/A') {
                    $('#toggleFollowUpDate').prop('checked', true).trigger('change');
                    $('#follow_up_date').val(followUpDate);
                    if (followUpFlatpickr) {
                        followUpFlatpickr.setDate(followUpDate, false);
                    }
                } else {
                    $('#toggleFollowUpDate').prop('checked', false).trigger('change');
                    $('#follow_up_date').val('');
                    if (followUpFlatpickr) {
                        followUpFlatpickr.clear();
                    }
                }

                $('#noteModalTitleAction').text('Edit');
                $('#noteModal').modal('show');
            });

            // Note Form submission (both Add and Edit)
            $('#noteModalForm').submit(function (e) {
                e.preventDefault();
                const titleText = $('#note_title').val().trim();
                if (!titleText) {
                    iziToast.error({
                        title: 'Validation Error',
                        message: 'Title is required.',
                        position: 'topRight'
                    });
                    return;
                }
                const noteText = $('#noteModalInput').val().trim();
                if (!noteText) {
                    iziToast.error({
                        title: 'Validation Error',
                        message: 'Note content is required.',
                        position: 'topRight'
                    });
                    return;
                }

                const createdAtVal = $('#note_created_at').val().trim();
                if (!createdAtVal) {
                    iziToast.error({
                        title: 'Validation Error',
                        message: 'Created Date is required.',
                        position: 'topRight'
                    });
                    return;
                }

                let followUpDateVal = '';
                if ($('#toggleFollowUpDate').is(':checked')) {
                    followUpDateVal = $('#follow_up_date').val().trim();
                    if (!followUpDateVal) {
                        iziToast.error({
                            title: 'Validation Error',
                            message: 'Follow Up Date is required.',
                            position: 'topRight'
                        });
                        return;
                    }
                }

                const editId = $('#note_edit_id').val();
                const url = editId ? "{{ route('deal.details.update-note') }}" : "{{ route('deal.details.add-note') }}";

                const postData = new FormData();
                postData.append('title', titleText);
                postData.append('note', noteText);
                postData.append('created_at', createdAtVal);
                if (followUpDateVal) postData.append('follow_up_date', followUpDateVal);
                if ($('#note_assigned_to').val()) postData.append('assigned_to', $('#note_assigned_to').val());
                postData.append('_token', "{{ csrf_token() }}");
                if (editId) {
                    postData.append('id', editId);
                } else {
                    postData.append('deal_id', dealId);
                }

                // Ensure attachment field is not sent if not chosen
                const attachmentType = $('#note_attachment_type').val();
                postData.append('attachment_type', attachmentType);
                if (attachmentType === 'file') {
                    var attachmentFile = $('#note_attachment')[0].files[0];
                    if (attachmentFile) {
                        postData.append('attachment', attachmentFile);
                    }
                } else if (attachmentType === 'link') {
                    var linkVal = $('#note_link').val().trim();
                    if (linkVal) {
                        postData.append('link', linkVal);
                    }
                }

                $('#btnSubmitNote').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: postData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        $('#btnSubmitNote').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save');
                        if (res.success) {
                            iziToast.success({
                                title: 'Success',
                                message: res.message,
                                position: 'topRight'
                            });
                            $('#noteModal').modal('hide');
                            loadNotes();
                            loadLogs();
                        } else {
                            iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                        }
                    },
                    error: function (xhr) {
                        $('#btnSubmitNote').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save');
                        let msg = xhr.responseJSON?.message || 'Failed to save note.';
                        if (xhr.responseJSON?.errors) {
                            let firstErr = Object.values(xhr.responseJSON.errors)[0];
                            if (Array.isArray(firstErr)) {
                                msg = firstErr[0];
                            }
                        }
                        iziToast.error({ title: 'Error', message: msg, position: 'topRight' });
                    }
                });
            });

            // View All Notes in Modal (Chronological)
            $('#btnViewAllNotes').click(function () {
                $.get(`/deal/details/notes/${dealId}`, function (res) {
                    if (res.success) {
                        let sortedNotes = res.data.slice();
                        sortedNotes.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                        let tbody = '';
                        sortedNotes.forEach((note, index) => {
                            tbody += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><strong>${note.title || '-'}</strong></td>
                                    <td>${note.formatted_follow_up_date || 'N/A'}</td>
                                    <td>${note.note.replace(/\n/g, '<br>')}</td>
                                    <td>${note.changed_by_name}</td>
                                    <td>${note.formatted_date}</td>
                                    <td>${note.updated_by_name || 'N/A'}</td>
                                    <td>${note.formatted_updated_at || 'N/A'}</td>
                                </tr>
                            `;
                        });
                        if (tbody === '') {
                            tbody = '<tr><td colspan="8" class="text-center text-muted">No notes found.</td></tr>';
                        }
                        $('#allNotesViewTableBody').html(tbody);
                        $('#viewNotesModal').modal('show');
                    }
                });
            });


            // Upload Attachment
            $('#attachmentForm').submit(function (e) {
                e.preventDefault();
                
                const type = $('#attachmentType').val();
                const formData = new FormData();
                formData.append('deal_id', dealId);
                formData.append('attachment_type', type);

                if (type === 'file') {
                    const files = $('#fileInput')[0].files;
                    if (files.length === 0) return;

                    // Validate file size (10MB limit per file)
                    const maxSizeBytes = 10 * 1024 * 1024; // 10MB
                    for (let i = 0; i < files.length; i++) {
                        if (files[i].size > maxSizeBytes) {
                            iziToast.error({
                                title: 'Validation Error',
                                message: `File "${files[i].name}" exceeds the maximum limit of 10MB.`,
                                position: 'topRight'
                            });
                            return;
                        }
                    }

                    for (let i = 0; i < files.length; i++) {
                        formData.append('files[]', files[i]);
                    }
                } else if (type === 'link') {
                    const linkUrl = $('#linkInput').val();
                    if (!linkUrl) return;
                    formData.append('link', linkUrl);
                }

                const $btn = $(this).find('button[type="submit"]');
                const originalHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Uploading...');

                $.ajax({
                    url: "{{ route('deal.details.upload-attachment') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        $btn.prop('disabled', false).html(originalHtml);
                        if (res.success) {
                            iziToast.success({
                                title: 'Success',
                                message: res.message,
                                position: 'topRight'
                            });
                            if (type === 'file') {
                                $('#fileInput').val('');
                            } else {
                                $('#linkInput').val('');
                            }
                            loadAttachments();
                            loadLogs();
                        } else {
                            iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                        }
                    },
                    error: function (xhr) {
                        $btn.prop('disabled', false).html(originalHtml);
                        let errMsg = 'Failed to upload attachments.';
                        if (xhr.status === 413) {
                            errMsg = 'The total upload size is too large. Each file must not exceed 10MB.';
                        } else if (xhr.status === 419) {
                            errMsg = 'Your session has expired. Please refresh the page and try again.';
                        } else if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                let errList = [];
                                Object.keys(xhr.responseJSON.errors).forEach(function (key) {
                                    errList.push(xhr.responseJSON.errors[key].join(', '));
                                });
                                if (errList.length > 0) {
                                    errMsg = errList.join('<br>');
                                } else if (xhr.responseJSON.message) {
                                    errMsg = xhr.responseJSON.message;
                                }
                            } else if (xhr.responseJSON.message) {
                                errMsg = xhr.responseJSON.message;
                            }
                        }
                        iziToast.error({ title: 'Error', message: errMsg, position: 'topRight' });
                    }
                });
            });

            // Delete Handlers
            $(document).on('click', '.delete-call-btn', function () {
                const id = $(this).data('id');
                iziToast.question({
                    timeout: false,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    title: 'Confirm Delete',
                    message: 'Are you sure you want to delete this call?',
                    position: 'center',
                    buttons: [
                        ['<button><b>Yes, Delete</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            $.post("{{ route('deal.details.delete-call') }}", { id: id }, function (res) {
                                if (res.success) {
                                    iziToast.success({ title: 'Deleted', message: res.message, position: 'topRight' });
                                    loadCalls();
                                    loadLogs();
                                }
                            });
                        }, true],
                        ['<button>Cancel</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }],
                    ],
                });
            });

            $(document).on('click', '.delete-note-btn', function () {
                let noteId = $(this).data('id');
                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete this note?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            $.ajax({
                                url: "{{ route('crm.activity.delete-note') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    id: noteId
                                },
                                success: function (res) {
                                    if (res.success) {
                                        iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                                        loadNotes();
                                    } else {
                                        iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                                    }
                                },
                                error: function (xhr) {
                                    iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Server Error', position: 'topRight' });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }]
                    ]
                });
            });

            // Note Follow-up Status logic
            $(document).on('click', '.complete-note-btn', function() {
                let noteId = $(this).data('id');
                let status = $(this).data('status');
                $('#completeNoteId').val(noteId);
                $('#completeNoteStatus').val(status);
                $('#completeNoteResult').val('');
                $('#modalCompleteNote').modal('show');
            });

            $('#formCompleteNote').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $('#btnSubmitNoteResult').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
                $.post("{{ route('crm.activity.update-note-status') }}", formData, function(res) {
                    $('#btnSubmitNoteResult').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Result');
                    if(res.success) {
                        $('#modalCompleteNote').modal('hide');
                        iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                        loadNotes();
                    } else {
                        iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                    }
                }).fail(function(xhr) {
                    $('#btnSubmitNoteResult').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Result');
                    iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Server error', position: 'topRight' });
                });
            });

            $(document).on('click', '.delete-attachment-btn', function () {
                const id = $(this).data('id');
                iziToast.question({
                    timeout: false,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    title: 'Confirm Delete',
                    message: 'Are you sure you want to delete this attachment?',
                    position: 'center',
                    buttons: [
                        ['<button><b>Yes, Delete</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            $.post("{{ route('deal.details.delete-attachment') }}", { id: id }, function (res) {
                                if (res.success) {
                                    iziToast.success({ title: 'Deleted', message: res.message, position: 'topRight' });
                                    loadAttachments();
                                    loadLogs();
                                }
                            });
                        }, true],
                        ['<button>Cancel</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }],
                    ],
                });
            });

            // Load data functions
            var callsTable;
            function loadCalls() {
                $.get(`/deal/details/calls/${dealId}`, function (res) {
                    if (res.success) {
                        if ($.fn.DataTable.isDataTable('#callsTable')) {
                            $('#callsTable').DataTable().destroy();
                        }

                        let html = '';
                        res.data.forEach((call, index) => {
                            let statusText = 'Pending';
                            let statusClass = 'bg-warning';
                            if (call.call_status == 1) { statusText = 'Completed'; statusClass = 'bg-success'; }
                            else if (call.call_status == 2) { statusText = 'Rejected'; statusClass = 'bg-danger'; }

                            let actionButtons = '';
                            let editBtn = '';
                            if (call.call_status == 0) {
                                actionButtons = `
                                    <button class="btn btn-sm btn-success btn-complete-call px-2 me-1" data-id="${call.id}" title="Complete Call">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-cancel-call px-2 me-1" data-id="${call.id}" title="Cancel Call">
                                        <i class="fas fa-times"></i>
                                    </button>
                                `;
                                editBtn = `
                                    <button class="btn btn-sm btn-primary btn-edit-call px-2 me-1" data-id="${call.id}" data-call='${JSON.stringify(call)}' title="Edit Call">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                `;
                            }

                            let agendaHtml = `<div>${call.call_agenda || '-'}</div>`;
                            if (call.call_result) {
                                agendaHtml += `<div class="text-muted mt-1" style="font-size: 0.85em; color: #888; font-style: italic;">Result: ${call.call_result}</div>`;
                            }

                            let auditHtml = `
                                <div style="font-size: 0.85em;">
                                    <div class="mb-1"><i class="fas fa-user-plus text-primary me-1" title="Created By"></i><span>created by:</span> <strong>${call.call_owner_name || call.changed_by_name}</strong></div>
                                    <div class="text-muted mb-2"><i class="far fa-clock me-1"></i> ${call.created_at_seconds}</div>
                                    <div><i class="fas fa-user-edit text-warning me-1" title="Updated By"></i> <span>updated by:</span><strong>${call.is_edited ? call.updated_by_name : '-'}</strong></div>
                                    <div class="text-muted"><i class="far fa-clock me-1"></i> ${call.is_edited ? call.updated_at_seconds : '-'}</div>
                                </div>
                            `;

                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <div><strong>${call.subject}</strong></div>
                                        <span class="badge ${statusClass} mt-1">${statusText}</span>
                                    </td>
                                    <td>${call.formatted_time}</td>
                                    <td>${agendaHtml}</td>
                                    <td>${auditHtml}</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            ${actionButtons}
                                            ${editBtn}
                                            <button class="btn btn-sm btn-danger delete-call-btn px-2" data-id="${call.id}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });

                        $('#callsTableBody').html(html);

                        callsTable = $('#callsTable').DataTable({
                            autoWidth: false,
                            responsive: true,
                            order: [[0, 'asc']],
                            columnDefs: [
                                { orderable: false, targets: [1, 3, 4, 5] },
                                { className: 'align-middle', targets: '_all' }
                            ]
                        });
                    }
                });
            }

            // Click Complete Call button
            $(document).on('click', '.btn-complete-call', function () {
                const id = $(this).data('id');
                $('#result_call_id').val(id);
                $('#result_call_status').val(1); // 1 = Completed
                $('#callResultInput').val('');
                $('#callResultModalLabel').html('<i class="fas fa-check text-success me-2"></i>Complete Call - Result');
                $('#callResultModal').modal('show');
            });

            // Click Cancel Call button
            $(document).on('click', '.btn-cancel-call', function () {
                const id = $(this).data('id');
                $('#result_call_id').val(id);
                $('#result_call_status').val(2); // 2 = Rejected/Cancelled
                $('#callResultInput').val('');
                $('#callResultModalLabel').html('<i class="fas fa-times text-danger me-2"></i>Cancel Call - Result');
                $('#callResultModal').modal('show');
            });

            // Call Result Form Submit
            $('#callResultForm').submit(function (e) {
                e.preventDefault();

                const id = $('#result_call_id').val();
                const callStatus = $('#result_call_status').val();
                const callResult = $('#callResultInput').val().trim();

                if (!callResult) {
                    iziToast.error({ title: 'Validation Error', message: 'Call Result message is required.', position: 'topRight' });
                    return false;
                }

                $('#btnSubmitCallResult').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.post("{{ route('deal.details.update-call-status') }}", {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    call_status: callStatus,
                    call_result: callResult
                }, function (res) {
                    $('#btnSubmitCallResult').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Result');
                    if (res.success) {
                        $('#callResultModal').modal('hide');
                        iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                        loadCalls();
                        loadLogs();
                    } else {
                        iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                    }
                }).fail(function (xhr) {
                    $('#btnSubmitCallResult').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Result');
                    iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Server error', position: 'topRight' });
                });
            });

            var notesTable;
            function loadNotes() {
                $.get(`/deal/details/notes/${dealId}`, function (res) {
                    if (res.success) {
                        if ($.fn.DataTable.isDataTable('#notesTable')) {
                            $('#notesTable').DataTable().destroy();
                        }

                        let html = '';
                        res.data.forEach((note, index) => {
                            let followUpBadge = '';
                            if (note.follow_up_date && note.formatted_follow_up_date !== 'N/A') {
                                followUpBadge = `<br><span class="badge bg-light text-primary border mt-2"><i class="far fa-calendar-alt me-1"></i>Follow Up: ${note.formatted_follow_up_date}</span>`;
                            }
                            let attachmentHtml = '';
                            if (note.attachment) {
                                if (note.attachment.startsWith('http://') || note.attachment.startsWith('https://')) {
                                    attachmentHtml = `<div class="mt-2"><a href="${note.attachment}" target="_blank" class="badge bg-info text-decoration-none"><i class="fas fa-link me-1"></i>View Link</a></div>`;
                                } else {
                                    let fileUrl = "{{ url('assets/uploads/notes/') }}/" + note.attachment;
                                    attachmentHtml = `<div class="mt-2"><a href="${fileUrl}" target="_blank" class="badge bg-info text-decoration-none"><i class="fas fa-paperclip me-1"></i>View Attachment</a></div>`;
                                }
                            }
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <div class="fw-bold text-dark mb-1">${note.title || '-'}</div>
                                        <div class="text-break">${note.note}</div>
                                        ${followUpBadge}
                                        ${note.is_completed == 1 ? `<div class="mt-2"><span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span> ${note.result ? `<span class="text-muted small ms-1">${note.result}</span>` : ''}</div>` : ''}
                                        ${note.is_completed == 2 ? `<div class="mt-2"><span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Cancelled</span> ${note.result ? `<span class="text-muted small ms-1">${note.result}</span>` : ''}</div>` : ''}
                                        ${attachmentHtml}
                                    </td>
                                    <td>
                                        <strong>${note.owner_name}</strong><br>
                                        <small class="text-muted">Assigned: <strong class="text-secondary">${note.assigned_name || 'None'}</strong></small><br>
                                        <small class="text-muted">Created: ${note.formatted_date}</small>
                                        ${note.is_edited ? `<br><small class="text-muted">Updated: ${note.formatted_updated_at}</small>` : ''}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            ${note.follow_up_date && note.is_completed == 0 ? `
                                            <button class="btn btn-sm btn-success complete-note-btn px-2 me-1" data-id="${note.id}" data-status="1" title="Mark as Completed">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger complete-note-btn px-2 me-1" data-id="${note.id}" data-status="2" title="Mark as Cancelled">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            ` : ''}
                                            <button class="btn btn-sm btn-info edit-note-btn me-1" data-id="${note.id}" data-title="${note.title || ''}" data-note="${encodeURIComponent(note.note)}" data-follow-up-date="${note.formatted_follow_up_date || 'N/A'}" data-created-at="${note.formatted_created_date || ''}" data-assigned-to="${note.assigned_to || ''}" data-attachment="${note.attachment || ''}">
                                                <i class="fas fa-pencil-alt text-white"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-note-btn" data-id="${note.id}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });

                        $('#notesTableBody').html(html);
                        notesTable = $('#notesTable').DataTable({
                            autoWidth: false,
                            responsive: true,
                            order: [[0, 'asc']],
                            columnDefs: [
                                { orderable: false, targets: [1, 2, 3] },
                                { className: 'align-middle', targets: '_all' }
                            ]
                        });
                    }
                });
            }

            function getFileIconClass(filename) {
                if (filename.startsWith('http://') || filename.startsWith('https://')) return 'fas fa-link text-primary';
                const ext = filename.split('.').pop().toLowerCase();
                switch (ext) {
                    case 'pdf': return 'far fa-file-pdf text-danger';
                    case 'doc':
                    case 'docx': return 'far fa-file-word text-primary';
                    case 'xls':
                    case 'xlsx':
                    case 'csv': return 'far fa-file-excel text-success';
                    case 'ppt':
                    case 'pptx': return 'far fa-file-powerpoint text-warning';
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'gif': return 'far fa-file-image text-info';
                    case 'zip':
                    case 'rar': return 'far fa-file-archive text-secondary';
                    case 'txt':
                    case 'log': return 'far fa-file-alt text-secondary';
                    default: return 'far fa-file text-muted';
                }
            }

            $('#attachmentType').on('change', function() {
                const type = $(this).val();
                if (type === 'file') {
                    $('#fileUploadWrapper').removeClass('d-none');
                    $('#fileInput').prop('required', true);
                    $('#linkUploadWrapper').addClass('d-none');
                    $('#linkInput').prop('required', false);
                } else {
                    $('#fileUploadWrapper').addClass('d-none');
                    $('#fileInput').prop('required', false);
                    $('#linkUploadWrapper').removeClass('d-none');
                    $('#linkInput').prop('required', true);
                }
            });

            function loadAttachments() {
                $.get(`/deal/details/attachments/${dealId}`, function (res) {
                    if (res.success) {
                        let html = '';
                        res.data.forEach(att => {
                            const iconClass = getFileIconClass(att.file_name);
                            html += `
                                <tr>
                                    <td>
                                        <a href="${att.file_path}" target="_blank" class="text-decoration-none">
                                            <i class="${iconClass} me-1"></i> ${att.file_name}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger delete-attachment-btn" data-id="${att.id}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        if (html === '') {
                            html = '<tr><td colspan="2" class="text-center text-muted">No attachments.</td></tr>';
                        }
                        $('#attachmentsTableBody').html(html);
                    }
                });
            }

            // Listen for Timeline tab click
            $('#timeline-tab').on('click shown.bs.tab', function () {
                loadLogs();
            });

            function loadLogs() {
                $('#timelineContainer').html('<div class="text-center text-muted py-5"><span class="spinner-border text-primary" role="status"></span><br>Loading logs...</div>');
                let logsUrl = "{{ route('deal.details.get-logs', ':id') }}".replace(':id', dealId);
                $.get(logsUrl, function (res) {
                    if (res.success) {
                        if (!res.data || res.data.length === 0) {
                            $('#timelineContainer').html('<div class="text-center text-muted py-5"><i class="far fa-clock fa-2x mb-3 text-light"></i><br>No logs recorded for this deal.</div>');
                            return;
                        }

                        let groups = {};
                        let dateOrder = [];
                        let totalNotesAdded = res.data.filter(log => log.action === 'note_added').length;
                        let currentNoteNumber = totalNotesAdded;
                        
                        res.data.forEach(log => {
                            let dateStr = 'Unknown Date';
                            let timeStr = '';
                            if (log.formatted_date) {
                                let parts = log.formatted_date.split(' ');
                                if (parts.length >= 3) {
                                    dateStr = parts[0] + ' ' + parts[1] + ' ' + parts[2];
                                    timeStr = parts.slice(3).join(' ');
                                } else {
                                    dateStr = log.formatted_date;
                                }
                            }

                            if (!groups[dateStr]) {
                                groups[dateStr] = [];
                                dateOrder.push(dateStr);
                            }
                            groups[dateStr].push({
                                log: log,
                                time: timeStr
                            });
                        });

                        let html = '';
                        dateOrder.forEach(dateStr => {
                            html += `
                                <div class="timeline-group">
                                    <div class="timeline-date-header">
                                        <span class="timeline-date-badge">${dateStr}</span>
                                    </div>
                                    <div class="timeline-items-container">
                                        <div class="timeline-line"></div>
                            `;

                            groups[dateStr].forEach(item => {
                                let log = item.log;
                                let timeStr = item.time;

                                let markerClass = 'bg-primary';
                                let icon = '<i class="fas fa-info"></i>';
                                let boldTitle = 'Activity';

                                if (log.action === 'create' || log.action === 'convert') {
                                    markerClass = 'bg-success';
                                    icon = '<i class="fas fa-plus"></i>';
                                    boldTitle = 'Created';
                                } else if (log.action === 'deal_updated' || log.action === 'update') {
                                    markerClass = 'bg-primary';
                                    icon = '<i class="fas fa-pen"></i>';
                                    boldTitle = 'Updated';
                                } else if (log.action === 'call_scheduled') {
                                    markerClass = 'bg-info';
                                    icon = '<i class="fas fa-phone-alt"></i>';
                                    boldTitle = 'Call activity';
                                } else if (log.action === 'call_status_updated') {
                                    markerClass = 'bg-info';
                                    icon = '<i class="fas fa-phone-volume"></i>';
                                    boldTitle = 'Call activity';
                                } else if (log.action === 'call_deleted') {
                                    markerClass = 'bg-danger';
                                    icon = '<i class="fas fa-phone-slash"></i>';
                                    boldTitle = 'Call activity';
                                } else if (log.action === 'note_added') {
                                    markerClass = 'bg-warning';
                                    icon = `<span class="fw-bold" style="font-size:0.75rem;">N${currentNoteNumber}</span>`;
                                    currentNoteNumber--;
                                    boldTitle = 'Note Added';
                                } else if (log.action === 'note_updated' || log.action === 'Note Updated') {
                                    markerClass = 'bg-warning';
                                    let match = log.description.match(/note N(\d+)/i);
                                    if(match) {
                                        icon = `<span class="fw-bold" style="font-size:0.75rem;">N${match[1]}</span>`;
                                    } else {
                                        icon = '<i class="fas fa-edit"></i>';
                                    }
                                    boldTitle = 'Note Updated';
                                } else if (log.action === 'note_deleted' || log.action === 'Note Deleted') {
                                    markerClass = 'bg-danger';
                                    let match = log.description.match(/note N(\d+)/i);
                                    if(match) {
                                        icon = `<span class="fw-bold" style="font-size:0.75rem;">N${match[1]}</span>`;
                                    } else {
                                        icon = '<i class="fas fa-trash-alt"></i>';
                                    }
                                    boldTitle = 'Note Deleted';
                                } else if (log.action === 'attachment_uploaded') {
                                    markerClass = 'bg-info';
                                    icon = '<i class="fas fa-paperclip"></i>';
                                    boldTitle = 'Attachment';
                                } else if (log.action === 'attachment_deleted') {
                                    markerClass = 'bg-danger';
                                    icon = '<i class="fas fa-trash"></i>';
                                    boldTitle = 'Attachment';
                                } else if (log.action === 'stage_updated') {
                                    markerClass = 'bg-success';
                                    icon = '<i class="fas fa-layer-group"></i>';
                                    boldTitle = 'Stage Updated';
                                } else if (log.action === 'delete') {
                                    markerClass = 'bg-danger';
                                    icon = '<i class="fas fa-trash-alt"></i>';
                                    boldTitle = 'Deleted';
                                } else if (log.action === 'revision') {
                                    markerClass = 'bg-warning';
                                    icon = '<i class="fas fa-code-branch"></i>';
                                    boldTitle = 'Revision';
                                }

                                let extraHtml = '';
                                if (log.is_quotation_log) {
                                    let collapseId = 'collapseLog_q_' + log.log_id;
                                    let basePdfUrl = "{{ route('quotation.pdf', ':id') }}";
                                    let pdfUrl = basePdfUrl.replace(':id', log.enc_quotation_id);
                                    
                                    let payloadHtml = '';
                                    if (log.parsed_payload) {
                                        let p = log.parsed_payload;
                                        
                                        if (p.quotation_no) {
                                            payloadHtml = `
                                            <div class="mt-2 border border-light-subtle rounded-3 p-3 bg-white shadow-sm" style="font-size: 12px;">
                                                <div class="row gy-3 gx-2">
                                                    <div class="col-sm-6">
                                                        <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Quotation No</span>
                                                        <span class="fw-bold text-dark" style="font-size: 13px;">${p.quotation_no}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Grand Total</span>
                                                        <span class="fw-bold text-primary" style="font-size: 13px;">${p.currency || '₹'} ${parseFloat(p.grand_total || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Customer Name</span>
                                                        <span class="fw-medium text-dark">${p.customer_name || 'N/A'}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Email</span>
                                                        <span class="text-dark">${p.email || 'N/A'}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Type</span>
                                                        <span class="badge bg-light text-dark border text-capitalize">${p.quotation_type || 'N/A'}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Date</span>
                                                        <span class="text-dark">${p.quotation_date ? new Date(p.quotation_date).toLocaleDateString('en-GB') : 'N/A'}</span>
                                                    </div>
                                                </div>
                                            </div>`;
                                        } 
                                        else if (p.items_created || p.items_updated || p.items_deleted) {
                                            let itemsHtml = '';
                                            
                                            if (p.items_created && p.items_created.length > 0) {
                                                itemsHtml += `<div class="mb-3"><span class="badge bg-success mb-2"><i class="fas fa-plus me-1"></i> Added Items (${p.items_created.length})</span>`;
                                                p.items_created.forEach(i => {
                                                    itemsHtml += `<div class="d-flex justify-content-between align-items-center small text-muted border-bottom pb-2 mb-2">
                                                        <div class="d-flex flex-column">
                                                            <span class="fw-semibold text-dark">Product ID: ${i.product_id}</span>
                                                            <span style="font-size: 11px;">Qty: ${i.qty} @ ${i.unit_price}</span>
                                                        </div>
                                                        <span class="fw-bold text-success">₹${parseFloat(i.total || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
                                                    </div>`;
                                                });
                                                itemsHtml += `</div>`;
                                            }
                                            if (p.items_updated && p.items_updated.length > 0) {
                                                itemsHtml += `<div class="mb-3"><span class="badge bg-primary mb-2"><i class="fas fa-pen me-1"></i> Updated Items (${p.items_updated.length})</span>`;
                                                p.items_updated.forEach(i => {
                                                    itemsHtml += `<div class="d-flex justify-content-between align-items-center small text-muted border-bottom pb-2 mb-2">
                                                        <div class="d-flex flex-column">
                                                            <span class="fw-semibold text-dark">Product ID: ${i.product_id}</span>
                                                            <span style="font-size: 11px;">Qty: ${i.qty} @ ${i.unit_price}</span>
                                                        </div>
                                                        <span class="fw-bold text-primary">₹${parseFloat(i.total || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
                                                    </div>`;
                                                });
                                                itemsHtml += `</div>`;
                                            }
                                            if (p.items_deleted && p.items_deleted.length > 0) {
                                                itemsHtml += `<div class="mb-3"><span class="badge bg-danger mb-2"><i class="fas fa-trash me-1"></i> Removed Items (${p.items_deleted.length})</span>`;
                                                p.items_deleted.forEach(i => {
                                                    itemsHtml += `<div class="small text-muted border-bottom pb-2 mb-2"><i class="fas fa-minus-circle text-danger me-1"></i> Item ID: ${i.item_id} removed</div>`;
                                                });
                                                itemsHtml += `</div>`;
                                            }
                                            
                                            payloadHtml = `
                                            <div class="mt-2 border border-light-subtle rounded-3 p-3 bg-white shadow-sm" style="font-size: 12px; max-height: 250px; overflow-y: auto;">
                                                ${itemsHtml || '<span class="text-muted"><i class="fas fa-info-circle me-1"></i> No items changed</span>'}
                                            </div>`;
                                        }
                                        else {
                                            payloadHtml = `<div class="mt-2 bg-light p-2 rounded-3 border text-start" style="max-height: 200px; overflow-y: auto;">
                                                <pre class="mb-0" style="white-space: pre-wrap; font-size: 11px;"><code>${JSON.stringify(p, null, 2)}</code></pre>
                                            </div>`;
                                        }
                                    }

                                    extraHtml = `
                                    <div class="mt-2 w-100">
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px;" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                                                <i class="fas fa-code me-1"></i> Payload
                                            </button>
                                            <a href="${pdfUrl}" target="_blank" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size: 11px;">
                                                <i class="fas fa-file-pdf me-1"></i> PDF
                                            </a>
                                        </div>
                                        <div class="collapse mt-2" id="${collapseId}">
                                            ${payloadHtml}
                                        </div>
                                    </div>
                                    `;
                                }

                                html += `
                                    <div class="timeline-item-row d-flex align-items-start justify-content-between">
                                        <div class="timeline-marker-circle ${markerClass} flex-shrink-0 mt-1">
                                            ${icon}
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1 w-100" style="min-width: 0;">
                                            <div class="d-flex align-items-center">
                                                <div class="timeline-item-time text-muted me-3">
                                                    <i class="far fa-clock me-1"></i> ${timeStr}
                                                </div>
                                                <div class="timeline-item-description text-wrap text-break">
                                                    <strong>${boldTitle}</strong> - ${log.file_url ? 
                                                        (log.description.includes('http://') || log.description.includes('https://') ?
                                                            `${log.description.split(log.file_url)[0] || 'Added link attachment: '}<a href="${log.file_url}" target="_blank" class="text-primary fw-bold text-decoration-underline" title="Click to open link"><i class="fas fa-external-link-alt me-1"></i>${log.file_url}</a>` :
                                                            `<a href="${log.file_url}" target="_blank" class="text-primary fw-bold text-decoration-underline" title="Click to view/download file"><i class="fas fa-external-link-alt me-1"></i>${log.description}</a>`
                                                        ) : log.description
                                                    }
                                                </div>
                                            </div>
                                            ${extraHtml}
                                        </div>
                                        <div class="timeline-item-by ms-3 flex-shrink-0 mt-1">
                                            <span class="badge rounded-pill">By ${log.performed_by_name}</span>
                                        </div>

                                    </div>
                                `;
                            });

                            html += `
                                    </div>
                                </div>
                            `;
                        });

                        // Capture currently open collapse IDs to prevent them from closing on reload
                        const openCollapseIds = [];
                        $('#timelineContainer').find('.collapse.show').each(function() {
                            const id = $(this).attr('id');
                            if (id) openCollapseIds.push(id);
                        });

                        $('#timelineContainer').html(html);

                        // Restore open collapse details
                        openCollapseIds.forEach(function(id) {
                            $('#timelineContainer').find('#' + id).addClass('show');
                        });
                    }
                });
            }
        });
    </script>
    <!-- View Notes Modal -->
    <div class="modal fade" id="viewNotesModal" tabindex="-1" aria-labelledby="viewNotesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewNotesModalLabel"><i class="fas fa-sticky-note text-primary me-2"></i> All Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="allNotesViewTable" width="100%">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">Sr. No.</th>
                                    <th style="width: 15%;">Title</th>
                                    <th style="width: 10%;">Follow-up Date</th>
                                    <th style="width: 25%;">Description</th>
                                    <th style="width: 10%;">Created By</th>
                                    <th style="width: 12%;">Created Date &amp; Time</th>
                                    <th style="width: 10%;">Updated By</th>
                                    <th style="width: 13%;">Updated Date &amp; Time</th>
                                </tr>
                            </thead>
                            <tbody id="allNotesViewTableBody">
                                <!-- Populated dynamically in chronological order -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
