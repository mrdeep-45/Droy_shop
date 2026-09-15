@php
    $leadProductIds = isset($lead) && $lead->products ? $lead->products->pluck('product_id')->toArray() : [];
@endphp
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

        .page-header-breadcrumb {
            position: relative;
            min-height: 0px !important;
            top: 0rem !important;
        }

        .flatpickr-wrapper {
            display: block !important;
            width: 100% !important;
        }

        .card-custom-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #ebecf0;
            padding: 12px 16px;
            border-top-left-radius: 7px;
            border-top-right-radius: 7px;
        }

        .lead-detail-card {
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

        .select2-dropdown--above {
            border-radius: 0 0 4px 4px !important;
            border-top: 1px solid #aaa !important;
            border-bottom: 1px solid #aaa !important;
            top: 100% !important;
            bottom: auto !important;
            margin-top: 0 !important;
        }

        .select2-container--open.select2-container--above .select2-selection--single,
        .select2-container--open.select2-container--above .select2-selection--multiple {
            border-top-left-radius: 4px !important;
            border-top-right-radius: 4px !important;
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            background-image: none !important;
        }

        /* Sidebar Color (#00454f) Theme Overrides */
        .text-primary,
        .card-title.text-primary {
            color: #00454f !important;
        }

        .btn-primary {
            background-color: #00454f !important;
            border-color: #00454f !important;
            color: #ffffff !important;
        }
        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
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

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--open .select2-selection--multiple {
            border-color: #00454f !important;
        }

        .table thead th {
            background-color: #00454f !important;
            color: #ffffff !important;
            border-color: #003840 !important;
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
                         <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
    
    <!-- Title Section -->
    <div class="card-title text-primary mb-0 text-truncate">
       Lead Details ({{ $lead->enquiry_no }})
    </div>
    
    <!-- Action Buttons & Tabs Section -->
    <div class="d-flex align-items-center flex-wrap gap-2">
        
        <ul class="nav nav-tabs nav-tabs-footer mb-0 border-0 flex-nowrap" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active text-nowrap px-3 py-1" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true" style="font-size: 13px;">
                    <i class="fas fa-eye me-1"></i>Overview
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-nowrap px-3 py-1 ms-1" id="timeline-tab" data-bs-toggle="tab" href="#timeline" role="tab" aria-controls="timeline" aria-selected="false" style="font-size: 13px;">
                    <i class="fas fa-history me-1"></i>Timeline
                </a>
            </li>
        </ul>
        
        @if($lead->status != 1)
            <button type="button" class="btn btn-primary btn-sm btn-convert-lead text-nowrap px-3 py-1 flex-shrink-0 m-0"
                    data-id="{{ $lead->enquiry_id }}"
                    data-customer="{{ $lead->customer_id }}"
                    data-name="{{ $lead->enquiry_name }}" style="font-size: 13px;">
                <i class="fas fa-handshake me-1"></i>Convert to Deal
            </button>
        @else
            <span class="badge bg-success-transparent text-success fw-bold py-1 px-3 text-nowrap flex-shrink-0 m-0" style="font-size: 13px;">
                <i class="fas fa-check-circle me-1"></i>Converted
            </span>
        @endif
        
    </div>
</div>
                            <div class="card-body">
                                <div class="tab-content" id="leadDetailsTabContent">
                                    <!-- Overview Tab -->
                                    <div class="tab-pane fade show active" id="overview" role="tabpanel"
                                        aria-labelledby="overview-tab">
                                        <!-- First Row: Lead Details in full row -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <form id="leadDetailsForm" novalidate>
                                                    @csrf
                                                    <input type="hidden" name="enquiry_id" id="enquiry_id"
                                                        value="{{ $lead->enquiry_id }}">
                                                    <div class="card lead-detail-card mb-3">
                                                        <div
                                                            class="card-custom-header d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0 text-primary fw-bold"><i
                                                                    class="fas fa-file-alt me-1"></i> Lead Details</h6>
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
                                                                    id="saveLeadDetailsBtn">
                                                                    <i class="fas fa-save me-1"></i> Update Details
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <!-- Row 1 -->
                                                                <!-- ADDED col-6 for mobile to show 2 per row -->
                                                                 <div class="col-6 col-md-3 mb-3">
                                                                     <label for="enquiry_name_display"
                                                                         class="form-label form-label-bold">Lead Name
                                                                         <span class="text-danger">*</span></label>
                                                                     
                                                                     <input type="hidden" name="enquiry_name" id="enquiry_name" value="{{ $lead->enquiry_name }}">
                                                                     
                                                                     <input type="text"
                                                                         class="form-control bg-light"
                                                                         id="enquiry_name_display"
                                                                         value="{{ $lead->enquiry_name }}"
                                                                         readonly>

                                                                     <div id="enquiry_name_select_container" class="d-none">
                                                                         <select class="form-select" 
                                                                             id="enquiry_name_select" 
                                                                             name="product_ids[]" 
                                                                             multiple="multiple" 
                                                                             style="width: 100%;">
                                                                             @foreach($products as $prod)
                                                                                 <option value="{{ $prod->product_id }}" 
                                                                                     data-name="{{ $prod->prod_name }}"
                                                                                     data-price="{{ $prod->prod_amount ?? 0 }}"
                                                                                     {{ in_array($prod->product_id, $leadProductIds) ? 'selected' : '' }}>
                                                                                     {{ $prod->prod_name }}
                                                                                 </option>
                                                                             @endforeach
                                                                         </select>
                                                                     </div>
                                                                 </div>
                                                                <div class="col-6 col-md-3 mb-3">
                                                                    <label for="company_name"
                                                                        class="form-label form-label-bold">Company
                                                                        Name</label>
                                                                    <input type="text"
                                                                        class="form-control lead-edit-field bg-light"
                                                                        name="company_name" id="company_name"
                                                                        value="{{ $lead->company_name }}" readonly>
                                                                </div>
                                                                <div class="col-6 col-md-3 mb-3">
                                                                    <label for="contact_person_name"
                                                                        class="form-label form-label-bold">Contact
                                                                        Person</label>
                                                                    <input type="text"
                                                                        class="form-control lead-edit-field bg-light"
                                                                        name="contact_person_name"
                                                                        id="contact_person_name"
                                                                        value="{{ $lead->contact_person_name }}"
                                                                        readonly>
                                                                </div>
                                                                 <div class="col-6 col-md-3 mb-3">
                                                                     <label for="phone"
                                                                         class="form-label form-label-bold">Mobile No</label>
                                                                     <input type="text"
                                                                         class="form-control lead-edit-field bg-light"
                                                                         name="phone" id="phone"
                                                                         value="{{ $lead->phone }}" maxlength="15"
                                                                         pattern="[0-9()+\- ]*"
                                                                         oninput="this.value = this.value.replace(/[^0-9()+\- ]/g, '')"
                                                                         readonly>
                                                                 </div>

                                                                 <!-- Row 2 -->
                                                                 <div class="col-6 col-md-3 mb-3">
                                                                     <label for="alternate_phone_1"
                                                                         class="form-label form-label-bold">Alt
                                                                         Phone</label>
                                                                     <input type="text"
                                                                         class="form-control lead-edit-field bg-light"
                                                                         name="alternate_phone_1" id="alternate_phone_1"
                                                                         value="{{ $lead->alternate_phone_1 }}"
                                                                         maxlength="15" pattern="[0-9()+\- ]*"
                                                                         oninput="this.value = this.value.replace(/[^0-9()+\- ]/g, '')"
                                                                         readonly>
                                                                 </div>
                                                                 <div class="col-6 col-md-3 mb-3">
                                                                     <label for="email"
                                                                         class="form-label form-label-bold">Email <span class="text-danger">*</span></label>
                                                                     <input type="email"
                                                                         class="form-control lead-edit-field bg-light"
                                                                         name="email" id="email"
                                                                         value="{{ $lead->email }}" required readonly>
                                                                 </div>
                                                                <div class="col-6 col-md-3 mb-3">
                                                                    <label for="source"
                                                                        class="form-label form-label-bold">Lead
                                                                        Source</label>
                                                                    <select class="form-select select2 lead-edit-field"
                                                                        name="source" id="source" disabled>
                                                                        <option value="">Select Source</option>
                                                                        @foreach ($sources as $src)
                                                                            <option value="{{ $src->source_id }}" {{ $lead->source == $src->source_id ? 'selected' : '' }}>
                                                                                {{ $src->title }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-6 col-md-3 mb-3">
                                                                    <label for="status_id"
                                                                        class="form-label form-label-bold">Status <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select select2 lead-edit-field"
                                                                        name="status_id" id="status_id" required
                                                                        disabled>
                                                                        <option value="">Select Status</option>
                                                                        @foreach ($statuses as $stat)
                                                                            <option value="{{ $stat->status_id }}" {{ $lead->status_id == $stat->status_id ? 'selected' : '' }}>
                                                                                {{ $stat->title }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <!-- Row 3 -->
                                                                <div class="col-6 col-md-3 mb-3">
                                                                    <label for="city_id"
                                                                        class="form-label form-label-bold">City</label>
                                                                    <select class="form-select select2 lead-edit-field"
                                                                        name="city_id" id="city_id" disabled>
                                                                        @if($lead->city)
                                                                            <option value="{{ $lead->city_id }}" selected>
                                                                                {{ $lead->city->city_name }}
                                                                            </option>
                                                                        @else
                                                                            <option value="">Search City</option>
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                                <div class="col-6 col-md-3 mb-3">
                                                                    <label for="state_display"
                                                                        class="form-label form-label-bold">State</label>
                                                                    <input type="text" class="form-control bg-light"
                                                                        id="state_display"
                                                                        value="{{ $lead->state->state_name ?? '' }}"
                                                                        readonly>
                                                                    <input type="hidden" name="state_id" id="state_id"
                                                                        value="{{ $lead->state_id ?? '' }}">
                                                                    <input type="hidden" id="company_state_id" value="{{ $companyStateId ?? '' }}">
                                                                </div>
                                                                <div class="col-6 col-md-3 mb-3">
                                                                    <label for="country_display"
                                                                        class="form-label form-label-bold">Country</label>
                                                                    <input type="text" class="form-control bg-light"
                                                                        id="country_display"
                                                                        value="{{ $lead->country->country_name ?? '' }}"
                                                                        readonly>
                                                                    <input type="hidden" name="countries_id"
                                                                        id="countries_id"
                                                                        value="{{ $lead->countries_id ?? '' }}">
                                                                </div>
                                                                 <div class="col-6 col-md-3 mb-3">
                                                                    @if(isStaffLogin())
                                                                        <input type="hidden" name="lead_type" id="lead_type" value="{{ $lead->lead_type ?? '' }}">
                                                                    @else
                                                                        <label for="lead_type"
                                                                            class="form-label form-label-bold">Lead Type</label>
                                                                        <select class="form-select select2 lead-edit-field"
                                                                            name="lead_type" id="lead_type" disabled>
                                                                            <option value="">-- Select --</option>
                                                                            <option value="Domestic" {{ $lead->lead_type == 'Domestic' ? 'selected' : '' }}>Domestic</option>
                                                                            <option value="Export" {{ $lead->lead_type == 'Export' ? 'selected' : '' }}>Export</option>
                                                                        </select>
                                                                    @endif
                                                                 </div>
                                                                 <div class="col-6 col-md-3 mb-3">
                                                                     <label for="assigned_to"
                                                                         class="form-label form-label-bold">Assigned
                                                                         To</label>
                                                                     <select class="form-select select2 lead-edit-field"
                                                                         name="assigned_to" id="assigned_to" disabled>
                                                                         <option value="">Unassigned</option>
                                                                         @foreach ($staffs as $stf)
                                                                             @if(empty($lead->lead_type) || empty($stf->staff_type) || $stf->staff_type === 'Both' || strtolower($stf->staff_type) === strtolower($lead->lead_type))
                                                                                 <option value="{{ $stf->staff_id }}" {{ $lead->assigned_to == $stf->staff_id ? 'selected' : '' }} data-type="{{ $stf->staff_type }}">
                                                                                     {{ $stf->staff_name }}{{ $stf->staff_type ? ' (' . $stf->staff_type . ')' : '' }}
                                                                                 </option>
                                                                             @endif
                                                                         @endforeach
                                                                     </select>
                                                                 </div>

                                                                <!-- Row 4 -->
                                                                <!-- Notes stays full width with col-12 -->
                                                                <div class="col-12 col-md-12 mb-3">
                                                                    @include('components.tooltip-help', ['key' => 'lead_remarks_notes', 'label' => 'Remarks/Notes', 'class' => 'form-label form-label-bold'])
                                                                    <textarea
                                                                        class="form-control lead-edit-field bg-light"
                                                                        name="notes" id="notes" rows="2"
                                                                        readonly>{{ $lead->notes }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Second Row: Products in full row -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card lead-detail-card mb-3">
                                                    <div class="card-custom-header">
                                                        <h6 class="mb-0 text-primary fw-bold"><i
                                                                class="fas fa-cubes me-1"></i> Requested Products</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- Add Product Form -->
                                                        <div class="row g-2 align-items-end">
                                                            <div class="col-12 col-md-2">
                                                                <label
                                                                    class="form-label form-label-bold text-dark">Category</label>
                                                                <select class="form-select select2"
                                                                    id="temp_category_id">
                                                                    <option value="">All Categories</option>
                                                                    @foreach ($categories as $cat)
                                                                        <option value="{{ $cat->category_id }}">
                                                                            {{ $cat->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-12 col-md-3">
                                                                <label
                                                                    class="form-label form-label-bold text-dark">Product</label>
                                                                <select class="form-select select2"
                                                                    id="temp_product_id">
                                                                    <option value="">Select a product...</option>
                                                                    @foreach ($products as $prod)
                                                                        <option value="{{ $prod->product_id }}"
                                                                            data-category="{{ $prod->category_id }}"
                                                                            data-price="{{ $prod->prod_amount }}"
                                                                            data-gst="{{ $prod->gst_no ?? 0 }}"
                                                                            data-description="{{ $prod->prod_desc ?? '-' }}">
                                                                            {{ $prod->prod_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <!-- Qty, Price and GST split on mobile -->
                                                            <div class="col-4 col-md-1">
                                                                <label
                                                                    class="form-label form-label-bold text-dark">Qty</label>
                                                                <input type="number" step="1" class="form-control"
                                                                    id="temp_product_qty" value="1"  min="1">
                                                            </div>
                                                            <div class="col-4 col-md-2">
                                                                <label
                                                                    class="form-label form-label-bold text-dark">Price/Amt</label>
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="temp_product_price" placeholder="0.00">
                                                            </div>
                                                            <div class="col-4 col-md-2">
                                                                <label
                                                                    class="form-label form-label-bold text-dark">GST (%)</label>
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="temp_product_gst" placeholder="0.00">
                                                            </div>
                                                            <div class="col-12 col-md-2">
                                                                <button type="button" class="btn btn-primary w-100"
                                                                    id="btnAddProduct">
                                                                    <i class="fas fa-plus"></i> Add
                                                                </button>
                                                                <div id="editFormButtons" style="display: none;" class="w-100">
                                                                    <div class="d-flex gap-1 w-100">
                                                                        <button type="button" class="btn btn-success w-100 px-1" id="btnUpdateProduct" title="Update">
                                                                            <i class="fas fa-check"></i> Update
                                                                        </button>
                                                                        <button type="button" class="btn btn-light border text-secondary w-100 px-1" id="btnCancelProductEdit" title="Cancel">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row g-2 mt-2">
                                                            <div class="col-12">
                                                                  @include('components.tooltip-help', ['key' => 'lead_product_description', 'label' => 'Description', 'class' => 'form-label form-label-bold'])
                                                                {{-- <label class="form-label form-label-bold text-dark">Description</label> --}}
                                                                <textarea class="form-control" id="temp_product_desc" rows="2" placeholder="Product description..."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card lead-detail-card mb-3">
                                                    <div class="card-custom-header">
                                                        <h6 class="mb-0 text-primary fw-bold"><i
                                                                class="fas fa-shopping-cart me-1"></i> Product Cart</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- Products List Table -->
                                                        <div class="table-responsive mb-3">
                                                            <table
                                                                class="table table-bordered table-striped align-middle"
                                                                id="productsTable" width="100%">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th style="width: 60px; white-space: nowrap;">Sr. No.</th>
                                                                        <th style="white-space: nowrap;">Product Name</th>
                                                                        <th style="white-space: nowrap;">Description</th>
                                                                        <th style="width: 100px; white-space: nowrap;">Quantity</th>
                                                                        <th style="width: 120px; white-space: nowrap;">Price (₹)</th>
                                                                        <th style="width: 100px; white-space: nowrap;">GST (%)</th>
                                                                        <th style="width: 130px; white-space: nowrap;">Total (₹)</th>
                                                                        <th style="width: 80px; white-space: nowrap;" class="text-center">
                                                                            Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="productsTableBody">
                                                                    @forelse ($lead->products as $item)
                                                                        <tr data-product-id="{{ $item->product_id }}"
                                                                            data-category-id="{{ $item->product ? $item->product->category_id : '' }}"
                                                                            data-qty="{{ $item->quantity }}"
                                                                            data-price="{{ $item->price }}"
                                                                            data-gst="{{ $item->gst ?? 0 }}">
                                                                            <td class="sr-no">{{ $loop->iteration }}</td>
                                                                            <td class="col-name">{{ $item->product ? $item->product->prod_name : 'Unknown Product' }}</td>
                                                                            <td class="col-desc">{{ $item->product && $item->product->prod_desc ? $item->product->prod_desc : '-' }}</td>
                                                                            <td class="col-qty">{{ number_format($item->quantity, 2) }}</td>
                                                                            <td class="col-price">₹{{ number_format($item->price, 2) }}</td>
                                                                            <td class="col-gst">{{ number_format($item->gst ?? 0, 2) }}%</td>
                                                                            <td class="row-total">
                                                                                ₹{{ number_format($item->price * $item->quantity, 2) }}
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-light border text-primary btn-edit-row me-1" title="Edit">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </button>
                                                                                <button
                                                                                    class="btn btn-sm btn-light border text-danger btn-delete-row" title="Delete">
                                                                                    <i class="fas fa-trash-alt"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr class="no-products-row">
                                                                            <td colspan="8"
                                                                                class="text-center text-muted py-3">No
                                                                                products added.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr class="fw-bold bg-light">
                                                                        <td colspan="6" class="text-end">Sub Total:</td>
                                                                        <td id="productsSubTotal" colspan="2">₹0.00</td>
                                                                    </tr>
                                                                    <tr class="fw-bold bg-light tax-row cgst-row">
                                                                        <td colspan="6" class="text-end">CGST:</td>
                                                                        <td id="productsCGST" colspan="2">₹0.00</td>
                                                                    </tr>
                                                                    <tr class="fw-bold bg-light tax-row sgst-row">
                                                                        <td colspan="6" class="text-end">SGST:</td>
                                                                        <td id="productsSGST" colspan="2">₹0.00</td>
                                                                    </tr>
                                                                    <tr class="fw-bold bg-light tax-row igst-row" style="display: none;">
                                                                        <td colspan="6" class="text-end">IGST:</td>
                                                                        <td id="productsIGST" colspan="2">₹0.00</td>
                                                                    </tr>
                                                                    <tr class="fw-bold bg-light" style="background-color: #e8f4fd !important;">
                                                                        <td colspan="6" class="text-end text-primary">Grand Total:</td>
                                                                        <td id="productsGrandTotal" colspan="2" class="text-primary">₹0.00</td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>

                                                        <button type="button" class="btn btn-success float-end"
                                                            id="btnSaveProducts">
                                                            <i class="fas fa-save me-1"></i> Save Products
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Third Row: Notes and Attachments in Split view -->
                                        <div class="row">
                                            <!-- Left: Notes Card -->
                                            <div class="col-lg-6 mb-3">
                                                <div class="card lead-detail-card h-100 mb-0">
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
                                                                        <a class="dropdown-item" href="{{ route('crm.activity.notes.export.excel', ['reference_type' => 'lead', 'reference_id' => $lead->enquiry_id]) }}">
                                                                            <i class="far fa-file-excel text-success me-2"></i> Excel
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item" href="{{ route('crm.activity.notes.export.pdf', ['reference_type' => 'lead', 'reference_id' => $lead->enquiry_id]) }}" target="_blank">
                                                                            <i class="far fa-file-pdf text-danger me-2"></i> PDF
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <button class="btn btn-primary btn-sm fw-bold px-3"
                                                                id="btnAddNoteModalBtn" data-bs-toggle="modal"
                                                                data-bs-target="#noteModal">
                                                                <i class="fas fa-plus me-1"></i> Add Note
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive"
                                                            style="max-height: 200px; overflow-y: auto;">
                                                            <table class="table table-bordered table-striped"
                                                                id="notesTable" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 5%;">#</th>
                                                                        <th style="width: 50%;">Note</th>
                                                                        <th style="width: 30%;">Created By & Time</th>
                                                                        <th style="width: 15%;" class="text-center">
                                                                            Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="notesTableBody">
                                                                    <!-- Loaded dynamically -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right: Attachments Card -->
                                            <div class="col-lg-6 mb-3">
                                                <div class="card lead-detail-card h-100 mb-0">
                                                    <div class="card-custom-header">
                                                        <h6 class="mb-0 text-primary fw-bold"><i
                                                                class="fas fa-paperclip me-1"></i> Attachments</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <form id="attachmentForm" enctype="multipart/form-data"
                                                            class="mb-3">
                                                            <div class="mb-2">
                                                                <div class="mb-2">
                                                                    <select class="form-select form-select-sm select2" id="attachmentType" name="attachment_type" data-minimum-results-for-search="Infinity" onchange="toggleLeadAttachmentType(this.value)">
                                                                        <option value="file">File Upload</option>
                                                                        <option value="link">Link Share</option>
                                                                    </select>
                                                                </div>
                                                                <div id="fileUploadWrapper">
                                                                    <input type="file" class="form-control form-control-sm"
                                                                        name="files[]" id="fileInput" multiple required
                                                                        onchange="validateFileSize(this)">
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
                                                            style="max-height: 200px; overflow-y: auto;">
                                                            <table
                                                                class="table table-bordered table-striped align-middle"
                                                                id="attachmentsTable" width="100%">
                                                                <tbody id="attachmentsTableBody">
                                                                    <!-- Loaded dynamically -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fourth Row: calls in full row -->
                                        <div class="row mt-3">
                                            <div class="col-lg-12">
                                                <div class="card lead-detail-card mb-3">
                                                    <div
                                                        class="card-custom-header d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-primary fw-bold"><i
                                                                class="fas fa-phone-alt me-1"></i> Calls</h6>
                                                        <button class="btn btn-primary btn-sm" id="btnScheduleCall"
                                                            data-bs-toggle="modal" data-bs-target="#scheduleCallModal">
                                                            <i class="fas fa-calendar-plus me-1"></i> Schedule Call
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive"
                                                            style="max-height: 200px; overflow-y: auto;">
                                                            <table class="table table-bordered table-striped"
                                                                id="callsTable" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 5%;">#</th>
                                                                        <th style="width: 20%;">Call Info</th>
                                                                        <th style="width: 15%;">Date/Time</th>
                                                                        <th style="width: 20%;">Agenda</th>
                                                                        <th style="width: 25%;">Call Info</th>
                                                                        <th style="width: 15%;" class="text-center">
                                                                            Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="callsTableBody">
                                                                    <!-- Loaded dynamically -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Timeline/History Tab -->
                                    <div class="tab-pane fade" id="timeline" role="tabpanel"
                                        aria-labelledby="timeline-tab">
                                        <div class="card lead-detail-card">
                                            <div class="card-custom-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-primary fw-bold"><i
                                                        class="fas fa-history me-1"></i> Lead Timeline History</h6>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary  d-flex align-items-center gap-1" type="button" id="leadTimelineDownloadBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-download"></i> Download
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="leadTimelineDownloadBtn">
                                                        <li><a class="dropdown-item" href="/lead-timeline/download?format=excel&leadId={{ $lead->enquiry_id }}" target="_blank"><i class="far fa-file-excel text-success me-2"></i> Excel</a></li>
                                                        <li><a class="dropdown-item" href="/lead-timeline/download?format=pdf&leadId={{ $lead->enquiry_id }}" target="_blank"><i class="far fa-file-pdf text-danger me-2"></i> PDF</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="position-relative" id="timelineContainer">
                                                    <div class="timeline-line"></div>
                                                    <!-- Loaded dynamically -->
                                                </div>
                                            </div>
                                        </div> <!-- timeline card end -->
                                    </div> <!-- timeline tab-pane end -->
                                </div> <!-- tab-content end -->
                            </div> <!-- main card-body end -->
                        </div> <!-- main card custom-card end -->
                    </div> <!-- col-xl-12 end -->
                </div> <!-- row end -->
            </div> <!-- container-fluid end -->
        </div> <!-- main-content app-content end -->

        @include('include.footer')
    </div> <!-- page end -->

    <div class="modal fade" id="scheduleCallModal" tabindex="-1" aria-labelledby="scheduleCallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="scheduleCallForm" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="call_edit_id" value="">
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
                            <!-- Show 2 fields per row on mobile for time and date -->
                            <div class="col-6 col-md-6 mb-3">
                                <label for="call_date" class="form-label form-label-bold">Call Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="call_date" id="call_date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-6 col-md-6 mb-3">
                                <label for="call_time" class="form-label form-label-bold">Call Time</label>
                                <input type="time" class="form-control" name="call_time" id="call_time" value="">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="call_subject" class="form-label form-label-bold">Subject <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="subject" id="call_subject"
                                    placeholder="Enter call subject" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="call_agenda" class="form-label form-label-bold">Call Agenda</label>
                                <textarea class="form-control" name="call_agenda" id="call_agenda" rows="3"
                                    placeholder="Describe the agenda..."></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="call_assigned_to" class="form-label form-label-bold">Assign To</label>
                                <select class="form-select select2-modal" name="assigned_to" id="call_assigned_to">
                                    <option value="">Select Staff</option>
                                    @foreach ($staffs as $staff)
                                         @if(empty($lead->lead_type) || empty($staff->staff_type) || $staff->staff_type === 'Both' || strtolower($staff->staff_type) === strtolower($lead->lead_type))
                                             <option value="{{ $staff->staff_id }}" {{ $lead->assigned_to == $staff->staff_id ? 'selected' : '' }} data-type="{{ $staff->staff_type }}">
                                                 {{ $staff->staff_name }}{{ $staff->staff_type ? ' (' . $staff->staff_type . ')' : '' }}
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
                                class="fas fa-check me-1"></i> Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Call Result Dialog Modal -->
    <div class="modal fade" id="callResultModal" tabindex="-1" aria-labelledby="callResultModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="callResultForm">
                    @csrf
                    <input type="hidden" name="id" id="result_call_id">
                    <input type="hidden" name="call_status" id="result_call_status">
                    <div class="modal-header">
                        <h5 class="modal-title" id="callResultModalLabel">Call Outcome</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="callResultInput" class="form-label fw-semibold">Call Result/Notes <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="call_result" id="callResultInput" rows="3" required
                                placeholder="Enter description of what happened during the call..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Result</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="noteModalForm" novalidate>
                    @csrf
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
                                     @if(empty($lead->lead_type) || empty($staff->staff_type) || $staff->staff_type === 'Both' || strtolower($staff->staff_type) === strtolower($lead->lead_type))
                                         <option value="{{ $staff->staff_id }}" data-type="{{ $staff->staff_type }}">
                                             {{ $staff->staff_name }}{{ $staff->staff_type ? ' (' . $staff->staff_type . ')' : '' }}
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
                            <input class="form-control" type="file" id="note_attachment" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png,.jpeg,.zip,.mp4">
                            <small class="text-muted d-block mt-1">Allowed types: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, JPEG, ZIP, MP4. (Max 10MB)</small>
                            <div id="existingNoteAttachmentContainer" class="mt-2 d-none">
                                <a href="#" id="existingNoteAttachmentLink" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-paperclip me-1"></i>View Current Attachment</a>
                            </div>
                        </div>
                        <div class="mb-3 d-none" id="noteLinkAttachmentContainer">
                            <label for="note_link" class="form-label form-label-bold">URL </label>
                            <input class="form-control" type="url" id="note_link" name="link" placeholder="Enter URL (e.g., https://youtube.com/...)">
                            <div id="existingNoteLinkContainer" class="mt-2 d-none">
                                <a href="#" id="existingNoteLinkHref" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-link me-1"></i>View Current Link</a>
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
                                placeholder="Enter Note Title" required>
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

    <!-- Convert to Deal Modal -->
    @include('company.crm.lead.convert_modal', ['products' => $products, 'stages' => $stages])

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
        function toggleLeadAttachmentType(type) {
            if (type === 'link') {
                document.getElementById('fileUploadWrapper').classList.add('d-none');
                document.getElementById('fileInput').required = false;
                document.getElementById('linkUploadWrapper').classList.remove('d-none');
                document.getElementById('linkInput').required = true;
            } else {
                document.getElementById('fileUploadWrapper').classList.remove('d-none');
                document.getElementById('fileInput').required = true;
                document.getElementById('linkUploadWrapper').classList.add('d-none');
                document.getElementById('linkInput').required = false;
            }
        }
    </script>

    <script>
        $(document).ready(function () {
            $(window).on('resize', function () {
                $('.select2-hidden-accessible').select2('close');
            });
            // Helper to parse validation/server error messages
            function getErrorMessage(xhr, defaultMsg = 'Something went wrong.') {
                let msg = defaultMsg;
                if (xhr && xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        let firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        msg = xhr.responseJSON.errors[firstKey][0];
                    } else if (xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                }
                return msg;
            }

            // Setup CSRF header
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize form locked
            lockLeadFields();

            // Sync Lead Name values on Select2 change
            $(document).on('change', '#enquiry_name_select', function () {
                let selectedNames = [];
                $(this).find('option:selected').each(function () {
                    selectedNames.push($(this).data('name'));
                });
                let concatenated = selectedNames.join(', ');
                $('#enquiry_name').val(concatenated);
                $('#enquiry_name_display').val(concatenated);
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

            // Initialize select2
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
                            dropdownParent: $modal.find('.modal-content'),
                            width: '100%'
                        });
                    }
                });
            });

            const leadId = $('#enquiry_id').val();
            const leadEncId = "{{ encrypt($lead->enquiry_id) }}";

            var isRestoring = false;
            var originalLeadValues = {};

            function lockLeadFields() {
                $('#enquiry_name, #company_name, #contact_person_name, #phone, #alternate_phone_1, #email, #notes').prop('readonly', true).addClass('bg-light');
                $('#source, #status_id, #city_id, #assigned_to, #lead_type').prop('disabled', true).trigger('change');
                
                // Toggle Lead Name display / select2
                $('#enquiry_name_select_container').addClass('d-none');
                $('#enquiry_name_display').removeClass('d-none');
 
                $('#btnToggleEdit').removeClass('d-none');
                $('#btnCancelEdit').addClass('d-none');
                $('#saveLeadDetailsBtn').addClass('d-none');
            }
 
            function unlockLeadFields() {
                $('#enquiry_name, #company_name, #contact_person_name, #phone, #alternate_phone_1, #email, #notes').prop('readonly', false).removeClass('bg-light');
                $('#source, #status_id, #city_id, #assigned_to, #lead_type').prop('disabled', false).trigger('change');
                
                // Toggle Lead Name display / select2
                $('#enquiry_name_display').addClass('d-none');
                $('#enquiry_name_select_container').removeClass('d-none');
                $('#enquiry_name_select').select2({
                    placeholder: "Select products...",
                    width: '100%'
                });

                $('#btnToggleEdit').addClass('d-none');
                $('#btnCancelEdit').removeClass('d-none');
                $('#saveLeadDetailsBtn').removeClass('d-none');
            }

            $('#btnToggleEdit').on('click', function () {
                originalLeadValues = {
                    enquiry_name: $('#enquiry_name').val(),
                    product_ids: $('#enquiry_name_select').val(),
                    company_name: $('#company_name').val(),
                    contact_person_name: $('#contact_person_name').val(),
                    phone: $('#phone').val(),
                    alternate_phone_1: $('#alternate_phone_1').val(),
                    email: $('#email').val(),
                    source: $('#source').val(),
                    status_id: $('#status_id').val(),
                    city_id: $('#city_id').val(),
                    city_name: $('#city_id option:selected').text(),
                    state_id: $('#state_id').val(),
                    state_name: $('#state_display').val(),
                    countries_id: $('#countries_id').val(),
                    country_name: $('#country_display').val(),
                    assigned_to: $('#assigned_to').val(),
                    lead_type: $('#lead_type').val(),
                    notes: $('#notes').val()
                };
                unlockLeadFields();
            });

            $('#btnCancelEdit').on('click', function () {
                isRestoring = true;
                $('#enquiry_name').val(originalLeadValues.enquiry_name);
                $('#enquiry_name_display').val(originalLeadValues.enquiry_name);
                $('#enquiry_name_select').val(originalLeadValues.product_ids).trigger('change');

                $('#company_name').val(originalLeadValues.company_name);
                $('#contact_person_name').val(originalLeadValues.contact_person_name);
                $('#phone').val(originalLeadValues.phone);
                $('#alternate_phone_1').val(originalLeadValues.alternate_phone_1);
                $('#email').val(originalLeadValues.email);
                $('#notes').val(originalLeadValues.notes);

                $('#source').val(originalLeadValues.source).trigger('change');
                $('#status_id').val(originalLeadValues.status_id).trigger('change');
                $('#lead_type').val(originalLeadValues.lead_type).trigger('change');
                $('#assigned_to').val(originalLeadValues.assigned_to).trigger('change');

                // Restore City Select2 option
                if (originalLeadValues.city_id) {
                    var cityOption = new Option(originalLeadValues.city_name, originalLeadValues.city_id, true, true);
                    $('#city_id').empty().append(cityOption).trigger('change');
                } else {
                    $('#city_id').empty().append('<option value="">Search City</option>').trigger('change');
                }

                // Restore state/country display + hidden fields
                $('#state_id').val(originalLeadValues.state_id);
                $('#state_display').val(originalLeadValues.state_name);
                $('#countries_id').val(originalLeadValues.countries_id);
                $('#country_display').val(originalLeadValues.country_name);

                isRestoring = false;
                lockLeadFields();
            });

            $('#leadDetailsForm').submit(function (e) {
                e.preventDefault();

                const enquiryName = $('#enquiry_name').val().trim();
                const email = $('#email').val().trim();
                const status = $('#status_id').val();

                if (!enquiryName) {
                    iziToast.warning({ title: 'Warning', message: 'Lead Name is required.', position: 'topRight' });
                    return false;
                }
                if (!email) {
                    iziToast.warning({ title: 'Warning', message: 'Email is required.', position: 'topRight' });
                    return false;
                }
                if (!status) {
                    iziToast.warning({ title: 'Warning', message: 'Status is required.', position: 'topRight' });
                    return false;
                }

                $('#saveLeadDetailsBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');

                // Temporarily enable for serialization
                $('#source, #status_id, #city_id, #assigned_to, #lead_type').prop('disabled', false);
                const formData = $('#leadDetailsForm').serialize();
                $('#source, #status_id, #city_id, #assigned_to, #lead_type').prop('disabled', true);

                $.ajax({
                    url: "{{ route('lead.details.update', ['id' => $lead->enquiry_id]) }}",
                    type: 'POST',
                    data: formData,
                    success: function (res) {
                        $('#saveLeadDetailsBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update Details');
                        if (res.success) {
                            iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                            lockLeadFields();
                            loadLogs();
                        } else {
                            iziToast.error({ title: 'Error', message: res.message || 'Failed to update lead', position: 'topRight' });
                        }
                    },
                    error: function (xhr) {
                        $('#saveLeadDetailsBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update Details');
                        iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Server error', position: 'topRight' });
                    }
                });
            });

            // Calculation function for Requested Products
            function recalculateGrandTotal() {
                let subTotal = 0;
                let totalTax = 0;
                let srNo = 1;

                let customerStateId = $('#state_id').val();
                let companyStateId = $('#company_state_id').val();
                let leadType = $('#lead_type').val() || 'Domestic';
                // If customerStateId is empty/not found, or same as company state
                let isIntraState = !customerStateId || (customerStateId === companyStateId);

                $('#productsTableBody tr').each(function () {
                    if ($(this).hasClass('no-products-row')) return;
                    $(this).find('.sr-no').text(srNo++);
                    
                    let price = parseFloat($(this).attr('data-price')) || 0;
                    let qty = parseFloat($(this).attr('data-qty')) || 0;
                    let gstRate = parseFloat($(this).attr('data-gst')) || 0;
                    
                    let rowSubtotal = price * qty;
                  // If Export, force tax to 0; otherwise compute normal tax
        let rowTax = (leadType === 'Export') ? 0 : (rowSubtotal * (gstRate / 100));
                    
                    $(this).find('.row-total').text('₹' + rowSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    
                    subTotal += rowSubtotal;
                    totalTax += rowTax;
                });

                let cgst = 0;
                let sgst = 0;
                let igst = 0;

               // Check if lead_type is Export
                if (leadType === 'Export') {
                    // Hide all tax/GST rows completely
                    $('.tax-row').hide();
                    
                    // Hide table header/body GST columns if desired
                    $('#productsTable th:nth-child(6), #productsTableBody td:nth-child(6)').hide();
                    $('#temp_product_gst').closest('.col-4').hide(); // Hide GST input field in form
                } else {
                    // Show table header/body GST columns & input field
                    $('#productsTable th:nth-child(6), #productsTableBody td:nth-child(6)').show();
                    $('#temp_product_gst').closest('.col-4').show();

                    if (isIntraState) {
                        cgst = totalTax / 2;
                        sgst = totalTax / 2;
                        $('.cgst-row, .sgst-row').show();
                        $('.igst-row').hide();
                    } else {
                        igst = totalTax;
                        $('.cgst-row, .sgst-row').hide();
                        $('.igst-row').show();
                    }
                }

                let grandTotal = subTotal + totalTax;

                $('#productsSubTotal').text('₹' + subTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#productsCGST').text('₹' + cgst.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#productsSGST').text('₹' + sgst.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#productsIGST').text('₹' + igst.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#productsGrandTotal').text('₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }
            recalculateGrandTotal();

            // Store all products as a master list for filtering
            const masterProducts = [];
            $('#temp_product_id option').each(function () {
                const $opt = $(this);
                if ($opt.val() !== '') {
                    masterProducts.push({
                        id: $opt.val(),
                        text: $opt.text().trim(),
                        category: $opt.attr('data-category'),
                        price: $opt.attr('data-price'),
                        gst: $opt.attr('data-gst') || 0,
                        description: $opt.attr('data-description') || '-'
                    });
                }
            });

            // Handle Category change to filter products dropdown
            $('#temp_category_id').on('change', function () {
                const catId = $(this).val();
                const $prodSelect = $('#temp_product_id');

                // Clear options
                $prodSelect.empty().append('<option value="">Select a product...</option>');

                // Filter and append matching ones
                masterProducts.forEach(prod => {
                    if (!catId || String(prod.category) === String(catId)) {
                        const option = new Option(prod.text, prod.id);
                        $(option).attr('data-price', prod.price);
                        $(option).attr('data-category', prod.category);
                        $(option).attr('data-gst', prod.gst);
                        $(option).attr('data-description', prod.description);
                        $prodSelect.append(option);
                    }
                });

                // Trigger change to update Select2 UI
                $prodSelect.val('').trigger('change.select2');
            });

            // Dropdown change to fetch and populate Price/Amount field and Description
            $('#temp_product_id').change(function () {
                const selectedOption = $(this).find('option:selected');
                const productId = $(this).val();
                if (!productId) {
                    $('#temp_product_price').val('');
                    $('#temp_product_gst').val('');
                    $('#temp_product_desc').val('');
                    return;
                }
                const price = parseFloat(selectedOption.attr('data-price')) || 0;
                $('#temp_product_price').val(price > 0 ? price.toFixed(2) : '');

                const gst = parseFloat(selectedOption.attr('data-gst')) || 0;
                $('#temp_product_gst').val(gst.toFixed(2));

                const desc = selectedOption.attr('data-description') || '';
                $('#temp_product_desc').val(desc === '-' ? '' : desc);
            });

            // Delete product row
            $(document).on('click', '.btn-delete-row', function () {
                if (editingRow && editingRow[0] === $(this).closest('tr')[0]) {
                    resetRequestedProductsForm();
                }
                $(this).closest('tr').remove();
                if ($('#productsTableBody tr').length === 0) {
                    $('#productsTableBody').html('<tr class="no-products-row"><td colspan="8" class="text-center text-muted py-3">No products added.</td></tr>');
                }
                recalculateGrandTotal();
            });

            let editingRow = null;

            // Edit product row (loads details into top form)
            $(document).on('click', '.btn-edit-row', function () {
                const row = $(this).closest('tr');
                editingRow = row;

                const productId = row.attr('data-product-id');
                const categoryId = row.attr('data-category-id') || '';
                const description = row.find('.col-desc').text().trim();
                const qty = parseFloat(row.attr('data-qty')) || 1;
                const price = parseFloat(row.attr('data-price')) || 0;
                const gst = parseFloat(row.attr('data-gst')) || 0;

                // Set Form fields
                $('#temp_category_id').val(categoryId).trigger('change');
                $('#temp_product_id').val(productId).trigger('change');
                $('#temp_product_desc').val(description === '-' ? '' : description);
                $('#temp_product_qty').val(qty);
                $('#temp_product_price').val(price.toFixed(2));
                $('#temp_product_gst').val(gst.toFixed(2));

                // Toggle Form buttons
                $('#btnAddProduct').hide();
                $('#editFormButtons').show();
            });

            // Update product row button handler
            $('#btnUpdateProduct').click(function () {
                if (!editingRow) return;

                const productSelect = $('#temp_product_id');
                const productId = productSelect.val();
                const productName = productSelect.find('option:selected').text().trim();
                const productDesc = $('#temp_product_desc').val().trim() || '-';
                const productPrice = parseFloat($('#temp_product_price').val()) || 0;
                const productQty = parseFloat($('#temp_product_qty').val()) || 0;
                const productGst = parseFloat($('#temp_product_gst').val()) || 0;
                const categoryVal = $('#temp_category_id').val() || productSelect.find('option:selected').attr('data-category') || '';

                if (!productId) {
                    iziToast.error({ title: 'Error', message: 'Please select a product.', position: 'topRight' });
                    return;
                }
                if (productQty <= 0) {
                    iziToast.error({ title: 'Error', message: 'Quantity must be greater than 0.', position: 'topRight' });
                    return;
                }
                if (productPrice < 0) {
                    iziToast.error({ title: 'Error', message: 'Price cannot be negative.', position: 'topRight' });
                    return;
                }

                // Check if another row already has the same product (excluding the row being edited)
                let duplicateRow = null;
                $('#productsTableBody tr').each(function () {
                    if ($(this).attr('data-product-id') == productId && this !== editingRow[0]) {
                        duplicateRow = $(this);
                        return false;
                    }
                });

                if (duplicateRow) {
                    // Merge quantities
                    let existingQty = parseFloat(duplicateRow.attr('data-qty')) || 0;
                    let newQty = existingQty + productQty;
                    duplicateRow.attr('data-qty', newQty);
                    duplicateRow.attr('data-price', productPrice);
                    duplicateRow.attr('data-gst', productGst);
                    duplicateRow.find('.col-qty').text(newQty.toFixed(2));
                    duplicateRow.find('.col-price').text('₹' + productPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    duplicateRow.find('.col-gst').text(productGst.toFixed(2) + '%');
                    
                    const newTotal = newQty * productPrice;
                    duplicateRow.find('.row-total').text('₹' + newTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                    // Remove editing row as it has been merged
                    editingRow.remove();
                } else {
                    // Update row values
                    editingRow.attr('data-product-id', productId);
                    editingRow.attr('data-category-id', categoryVal);
                    editingRow.attr('data-qty', productQty);
                    editingRow.attr('data-price', productPrice);
                    editingRow.attr('data-gst', productGst);

                    editingRow.find('.col-name').text(productName);
                    editingRow.find('.col-desc').text(productDesc);
                    editingRow.find('.col-qty').text(productQty.toFixed(2));
                    editingRow.find('.col-price').text('₹' + productPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    editingRow.find('.col-gst').text(productGst.toFixed(2) + '%');
                    
                    const rowTotal = productQty * productPrice;
                    editingRow.find('.row-total').text('₹' + rowTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                }

                resetRequestedProductsForm();
            });

            // Cancel button click handler
            $('#btnCancelProductEdit').click(function () {
                resetRequestedProductsForm();
            });

            function resetRequestedProductsForm() {
                editingRow = null;
                $('#temp_category_id').val('').trigger('change');
                $('#temp_product_id').val('').trigger('change');
                $('#temp_product_desc').val('');
                $('#temp_product_qty').val(1);
                $('#temp_product_price').val('');
                $('#temp_product_gst').val('');

                // Toggle buttons
                $('#btnAddProduct').show();
                $('#editFormButtons').hide();

                recalculateGrandTotal();
            }

            // Add Product to list
            $('#btnAddProduct').click(function () {
                const productSelect = $('#temp_product_id');
                const productId = productSelect.val();
                const productName = productSelect.find('option:selected').text().trim();
                const productDesc = $('#temp_product_desc').val().trim() || '-';
                const productPrice = parseFloat($('#temp_product_price').val()) || 0;
                const productQty = parseFloat($('#temp_product_qty').val()) || 0;
                const productGst = parseFloat($('#temp_product_gst').val()) || 0;
                const categoryVal = $('#temp_category_id').val() || productSelect.find('option:selected').attr('data-category') || '';

                if (!productId) {
                    iziToast.error({ title: 'Error', message: 'Please select a product.', position: 'topRight' });
                    return;
                }
                if (productQty <= 0) {
                    iziToast.error({ title: 'Error', message: 'Quantity must be greater than 0.', position: 'topRight' });
                    return;
                }

                // Check if already exists in table
                let exists = false;
                $('#productsTableBody tr').each(function () {
                    if ($(this).attr('data-product-id') == productId) {
                        exists = true;
                        let currentQty = parseFloat($(this).attr('data-qty')) || 0;
                        let newQty = currentQty + productQty;
                        $(this).attr('data-qty', newQty);
                        $(this).attr('data-price', productPrice);
                        $(this).attr('data-gst', productGst);
                        $(this).find('.col-qty').text(newQty.toFixed(2));
                        $(this).find('.col-price').text('₹' + productPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $(this).find('.col-gst').text(productGst.toFixed(2) + '%');
                        
                        const newTotal = newQty * productPrice;
                        $(this).find('.row-total').text('₹' + newTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        return false;
                    }
                });

                if (!exists) {
                    $('.no-products-row').remove();
                    const newRow = `
                        <tr data-product-id="${productId}" 
                            data-category-id="${categoryVal}" 
                            data-qty="${productQty}" 
                            data-price="${productPrice}"
                            data-gst="${productGst}">
                            <td class="sr-no"></td>
                            <td class="col-name">${productName}</td>
                            <td class="col-desc">${productDesc}</td>
                            <td class="col-qty">${productQty.toFixed(2)}</td>
                            <td class="col-price">₹${productPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td class="col-gst">${productGst.toFixed(2)}%</td>
                            <td class="row-total">₹${(productPrice * productQty).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-light border text-primary btn-edit-row me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light border text-danger btn-delete-row" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#productsTableBody').append(newRow);
                }

                // Reset inputs
                productSelect.val('').trigger('change');
                $('#temp_product_qty').val(1);
                $('#temp_product_price').val('');
                $('#temp_product_gst').val('');
                $('#temp_product_desc').val('');
                recalculateGrandTotal();
            });

            // Save Products Ajax
            $('#btnSaveProducts').click(function () {
                let productsData = [];
                $('#productsTableBody tr').each(function () {
                    if ($(this).hasClass('no-products-row')) return;
                    let productId = $(this).attr('data-product-id');
                    let qty = $(this).attr('data-qty');
                    let price = $(this).attr('data-price');
                    let gst = $(this).attr('data-gst') || 0;
                    productsData.push({
                        product_id: productId,
                        quantity: qty,
                        price: price,
                        gst: gst
                    });
                });

                if (productsData.length === 0) {
                    iziToast.warning({ title: 'Warning', message: 'Atleast add one product', position: 'topRight' });
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

                $.ajax({
                    url: "{{ route('lead.details.save-products', ['id' => $lead->enquiry_id]) }}",
                    method: 'POST',
                    data: {
                        products: productsData
                    },
                    success: function (res) {
                        if (res.success) {
                            iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                            loadLogs();
                        } else {
                            iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                        }
                    },
                    error: function (xhr) {
                        let msg = getErrorMessage(xhr, 'Something went wrong.');
                        iziToast.error({ title: 'Error', message: msg, position: 'topRight' });
                    },
                    complete: function () {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Products');
                    }
                });
            });



            // Initialize Select2 for City using AJAX (Image/Deal details style)
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

            // Auto-focus the search input when City dropdown opens
            $('#city_id').on('select2:open', function () {
                setTimeout(function () {
                    var searchField = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchField) {
                        searchField.focus();
                    }
                }, 50);
            });

            // Filter staff dropdown by lead type via AJAX
            window.filterDetailsStaffByType = function(type, currentVal) {
                $.get("{{ route('enquiry.staff.by.type') }}", { type: type }, function (res) {
                    if (res.status) {
                        var $select = $('#assigned_to');
                        var prev = currentVal !== undefined ? currentVal : $select.val();
                        
                        var isOpen = $select.next('.select2-container').hasClass('select2-container--open');
                        
                        $select.empty().append('<option value="">Unassigned</option>');
                        $.each(res.staff, function (i, s) {
                            $select.append('<option value="' + s.id + '" data-type="' + (s.type || '') + '">' + s.name + '</option>');
                        });
                        
                        if (prev) {
                            $select.val(prev).trigger('change');
                        } else {
                            $select.trigger('change');
                        }
                        
                        if (isOpen) {
                            $select.select2('close').select2('open');
                        }
                    }
                });
            };

            // Hook up change handler for lead_type
            $('#lead_type').on('change', function () {
                if (isRestoring) return;
                const selectedType = $(this).val();
                filterDetailsStaffByType(selectedType);
                recalculateGrandTotal();
            });

            // When City is selected, automatically fetch and fill State & Country (readonly fields)
            $('#city_id').on('change', function () {
                if (isRestoring) return;
                const cityId = $(this).val();
                if (cityId) {
                    $.get("{{ route('location.by_city', ['id' => ':cityId']) }}".replace(':cityId', cityId), function (res) {
                        if (res.status) {
                            const data = res.data;
                            $('#state_display').val(data.state_name || '');
                            $('#state_id').val(data.state_id || '');
                            $('#country_display').val(data.country_name || '');
                            $('#countries_id').val(data.country_id || '');

                            // Auto-set Lead Type based on country
                            const indiaCountryId = 99;
                            const detectedType = (parseInt(data.country_id) === indiaCountryId) ? 'Domestic' : 'Export';
                            $('#lead_type').val(detectedType).trigger('change');

                            recalculateGrandTotal();
                        }
                    });
                } else {
                    $('#state_display').val('');
                    $('#state_id').val('');
                    $('#country_display').val('');
                    $('#countries_id').val('');
                    recalculateGrandTotal();
                }
            });

            // Load Activities
            loadCalls();
            loadNotes();
            loadAttachments();

            // Calls Section Logic
            var callsTable;
            function loadCalls() {
                $.get("{{ route('crm.activity.calls', ['leadId' => ':leadId']) }}".replace(':leadId', leadId), function (res) {
                    if (res.success) {
                        if ($.fn.DataTable.isDataTable('#callsTable')) {
                            $('#callsTable').DataTable().destroy();
                        }

                        let html = '';
                        res.data.forEach((call, index) => {
                            let statusText = 'Pending';
                            let statusClass = 'bg-warning';
                            if (call.call_status == 1) { statusText = 'Completed'; statusClass = 'bg-success'; }
                            else if (call.call_status == 2) { statusText = 'Cancelled'; statusClass = 'bg-danger'; }

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
                                    <div class="mb-1"><i class="fas fa-user-plus text-primary me-1" title="Created By"></i> <span>created By:</span><strong>${call.changed_by_name || call.call_owner_name}</strong></div>
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

                        if (html === '') {
                            html = '<tr><td colspan="6" class="text-center text-muted">No calls scheduled.</td></tr>';
                        }
                        $('#callsTableBody').html(html);

                        if (res.data.length > 0) {
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
                const form = $(this);
                const btn = form.find('button[type="submit"]');
                btn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ route('crm.activity.update-call-status') }}",
                    method: 'POST',
                    data: form.serialize(),
                    success: function (res) {
                        if (res.success) {
                            iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                            $('#callResultModal').modal('hide');
                            loadCalls();
                            loadLogs();
                        } else {
                            iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                        }
                    },
                    error: function (xhr) {
                        let msg = getErrorMessage(xhr, 'Something went wrong.');
                        iziToast.error({ title: 'Error', message: msg, position: 'topRight' });
                    },
                    complete: function () {
                        btn.prop('disabled', false).text('Save Result');
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

            // Schedule Call Submit
            $('#scheduleCallForm').submit(function (e) {
                e.preventDefault();
                const form = $(this);
                const btn = form.find('button[type="submit"]');
                const isEdit = $('#call_edit_id').val() !== '';
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> ' + (isEdit ? 'Updating...' : 'Scheduling...'));

                let formData = form.serializeArray();
                formData.push({ name: 'reference_type', value: 'lead' });
                formData.push({ name: 'reference_id', value: leadId });

                let url = isEdit ? "{{ route('crm.activity.update-call') }}" : "{{ route('crm.activity.schedule-call') }}";

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $.param(formData),
                    success: function (res) {
                        if (res.success) {
                            iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                            $('#scheduleCallModal').modal('hide');
                            form.trigger('reset');
                            loadCalls();
                            loadLogs();
                        } else {
                            iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                        }
                    },
                    error: function (xhr) {
                        let msg = getErrorMessage(xhr, 'Something went wrong.');
                        iziToast.error({ title: 'Error', message: msg, position: 'topRight' });
                    },
                    complete: function () {
                        btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Schedule');
                    }
                });
            });

            // Delete Call
            $(document).on('click', '.delete-call-btn', function () {
                const id = $(this).data('id');
                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 9999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete this call?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            $.post("{{ route('crm.activity.delete-call') }}", { id: id }, function (res) {
                                if (res.success) {
                                    iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                                    loadCalls();
                                    loadLogs();
                                } else {
                                    iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }],
                    ],
                });
            });


            // Notes Section Logic
            var notesTable;
            function loadNotes() {
                $.get("{{ route('crm.activity.notes', ['leadId' => ':leadId']) }}".replace(':leadId', leadId), function (res) {
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
                            let assignedStaffName = '';
                            if (note.assigned_to) {
                                assignedStaffName = `<br><small class="text-muted"><i class="fas fa-user-tag me-1"></i>Assigned to: ${note.assigned_name}</small>`;
                            }
                            
                           let attachmentHtml = '';
                            if (note.attachment) {
                                let fileUrl = '';
                                // Check if attachment string starts with http:// or https://
                                if (/^https?:\/\//i.test(note.attachment)) {
                                    fileUrl = note.attachment;
                                } else {
                                    fileUrl = "{{ url('assets/uploads/notes/') }}/" + note.attachment;
                                }
                                attachmentHtml = `<div class="mt-2"><a href="${fileUrl}" target="_blank" class="badge bg-info text-decoration-none"><i class="fas fa-paperclip me-1"></i>View Attachment</a></div>`;
                            }

                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <div class="fw-bold text-dark mb-1">${note.title || '-'}</div>
                                        <div>${note.note}</div>
                                        ${followUpBadge}
                                        ${note.is_completed == 1 ? `<div class="mt-2"><span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span> ${note.result ? `<span class="text-muted small ms-1">${note.result}</span>` : ''}</div>` : ''}
                                        ${note.is_completed == 2 ? `<div class="mt-2"><span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Cancelled</span> ${note.result ? `<span class="text-muted small ms-1">${note.result}</span>` : ''}</div>` : ''}
                                        ${assignedStaffName}
                                        ${attachmentHtml}
                                    </td>
                                    <td>
                                        <div>By: <span class="fw-semibold">${note.changed_by_name}</span></div>
                                        <div class="text-muted mt-1" style="font-size: 0.85em; line-height: 1.4;">
                                            <div><span class="fw-semibold text-dark">Created:</span> ${note.created_at_seconds}</div>
                                            ${note.is_edited ? `<div><span class="fw-semibold text-dark">Updated:</span> ${note.updated_at_seconds}</div>` : ''}
                                        </div>
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
                                            <button class="btn btn-sm btn-light border text-primary edit-note-btn px-2 me-1" 
                                                data-id="${note.id}" 
                                                data-title="${note.title || ''}" 
                                                data-note="${encodeURIComponent(note.note)}" 
                                                data-follow-up-date="${note.formatted_follow_up_date || 'N/A'}" 
                                                data-created-at="${note.formatted_created_date || ''}" 
                                                data-assigned-to="${note.assigned_to || ''}"
                                                data-attachment="${note.attachment || ''}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-note-btn px-2" data-id="${note.id}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });

                        if (html === '') {
                            html = '<tr><td colspan="4" class="text-center text-muted">No notes recorded.</td></tr>';
                        }
                        $('#notesTableBody').html(html);

                        if (res.data.length > 0) {
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
                    }
                });
            }

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

                var upperTitle = rawVal;
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

            // Click Add Note modal button
            $(document).on('change', '#note_attachment_type', function() {
                if ($(this).val() === 'link') {
                    $('#noteFileAttachmentContainer').addClass('d-none');
                    $('#noteLinkAttachmentContainer').removeClass('d-none');
                } else {
                    $('#noteFileAttachmentContainer').removeClass('d-none');
                    $('#noteLinkAttachmentContainer').addClass('d-none');
                }
            });

            $('#btnAddNoteModalBtn').click(function () {
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
                $('#noteModal').modal('show');
            });

            // Click Edit Note button
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

            // Note Form Submit
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
                const url = editId ? "{{ route('crm.activity.update-note') }}" : "{{ route('crm.activity.add-note') }}";

                const postData = new FormData();
                postData.append('title', titleText);
                postData.append('note', noteText);
                postData.append('created_at', createdAtVal);
                if (followUpDateVal) postData.append('follow_up_date', followUpDateVal);
                if ($('#note_assigned_to').val()) postData.append('assigned_to', $('#note_assigned_to').val());
                postData.append('reference_type', 'lead');
                postData.append('reference_id', leadId);
                postData.append('_token', "{{ csrf_token() }}");
                if (editId) {
                    postData.append('id', editId);
                }

                // Ensure attachment field is not sent if not chosen
                const attachmentType = $('#note_attachment_type').val();
                if (attachmentType) {
                    postData.append('attachment_type', attachmentType);
                }
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

            // Delete Note
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

            // View All Notes in Modal (Chronological)
            $('#btnViewAllNotes').click(function () {
                $.get("{{ route('crm.activity.get-notes', ['reference_type' => 'lead', 'reference_id' => $lead->enquiry_id]) }}", function(res) {
                    if (res.success) {
                        let sortedNotes = res.data.slice();
                        sortedNotes.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                        let tbody = '';
                        sortedNotes.forEach((note, index) => {
                            tbody += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><strong>${note.title || '-'}</strong></td>
                                    <td>${note.formatted_follow_up_date || '-'}</td>
                                    <td>${note.note.replace(/\n/g, '<br>')}</td>
                                    <td>${note.changed_by_name}</td>
                                    <td>${note.formatted_date}</td>
                                    <td>${note.updated_by_name || '-'}</td>
                                    <td>${note.formatted_updated_at || '-'}</td>
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

            // Note Follow-up Status logic
            $(document).on('click', '.complete-note-btn', function () {
                let noteId = $(this).data('id');
                let status = $(this).data('status');
                $('#completeNoteId').val(noteId);
                $('#completeNoteStatus').val(status);
                $('#completeNoteResult').val('');
                $('#modalCompleteNote').modal('show');
            });

            $('#formCompleteNote').on('submit', function (e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $('#btnSubmitNoteResult').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
                $.post("{{ route('crm.activity.update-note-status') }}", formData, function (res) {
                    $('#btnSubmitNoteResult').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Result');
                    if (res.success) {
                        $('#modalCompleteNote').modal('hide');
                        iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                        loadNotes();
                    }
                $('#btnSubmitNoteResult').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Result');
                    iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Server error', position: 'topRight' });
                });
            });


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

            // attachmentType change is handled by global toggleLeadAttachmentType() and select2 change listener
            $('#attachmentType').on('change select2:select', function () {
                toggleLeadAttachmentType($(this).val());
            });

            // Attachments Section Logic
            function loadAttachments() {
                $.get("{{ route('crm.activity.attachments', ['leadId' => ':leadId']) }}".replace(':leadId', leadId), function (res) {
                    if (res.success) {
                        let html = '';
                        res.data.forEach(att => {
                            const iconClass = getFileIconClass(att.file_name);
                            const isLink = att.file_name.startsWith('http://') || att.file_name.startsWith('https://');
                            const href = isLink ? att.file_name : att.file_path;
                            const displayName = isLink
                                ? (att.file_name.length > 45 ? att.file_name.substring(0, 45) + '...' : att.file_name)
                                : att.file_name;
                            html += `
                                <tr>
                                    <td>
                                        <a href="${href}" target="_blank" class="text-decoration-none" title="${att.file_name}">
                                            <i class="${iconClass} me-1"></i> ${displayName}
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


            // Attachment Form Submit
            $('#attachmentForm').submit(function (e) {
                e.preventDefault();
                
                const type = $('#attachmentType').val();
                let formData = new FormData();
                formData.append('reference_type', 'lead');
                formData.append('reference_id', leadId);
                formData.append('attachment_type', type);
                
                if (type === 'file') {
                    const files = $('#fileInput')[0].files;
                    if (files.length === 0) return;
                    
                    for (let i = 0; i < files.length; i++) {
                        formData.append('files[]', files[i]);
                    }
                } else if (type === 'link') {
                    const linkUrl = $('#linkInput').val();
                    if (!linkUrl) return;
                    formData.append('link', linkUrl);
                }

                const btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Uploading...');

                $.ajax({
                    url: "{{ route('crm.activity.upload-attachment') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        if (res.success) {
                            iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                            if ($('#attachmentType').val() === 'file') {
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
                        if (xhr.status === 413) {
                            iziToast.error({ title: 'Error', message: 'File size is more than 10MB', position: 'topRight' });
                        } else {
                            let msg = getErrorMessage(xhr, 'Something went wrong.');
                            iziToast.error({ title: 'Error', message: msg, position: 'topRight' });
                        }
                    },
                    complete: function () {
                        btn.prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Upload File');
                    }
                });
            });

            // Delete Attachment
            $(document).on('click', '.delete-attachment-btn', function () {
                const id = $(this).data('id');
                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 9999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete this attachment?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            $.post("{{ route('crm.activity.delete-attachment') }}", { id: id }, function (res) {
                                if (res.success) {
                                    iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                                    loadAttachments();
                                    loadLogs();
                                } else {
                                    iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }],
                    ],
                });
            });


            // Timeline tab shown event
            let detailsPollTimer = null;
            $('#timeline-tab').on('shown.bs.tab', function () {
                loadLogs();
                if (detailsPollTimer) {
                    clearInterval(detailsPollTimer);
                }
                detailsPollTimer = setInterval(loadLogs, 3000);
            });
            $('#timeline-tab').on('hidden.bs.tab', function () {
                if (detailsPollTimer) {
                    clearInterval(detailsPollTimer);
                    detailsPollTimer = null;    
                }
            });

            function loadLogs() {
                $.get("{{ route('lead.details.get-logs', ['lead_id' => ':lead_id']) }}".replace(':lead_id', leadEncId), function (res) {
                    if (res.success) {
                        if (!res.data || res.data.length === 0) {
                            $('#timelineContainer').html('<div class="text-center text-muted py-5"><i class="far fa-clock fa-2x mb-3 text-light"></i><br>No logs recorded for this lead.</div>');
                            return;
                        }

                        let groups = {};
                        let dateOrder = [];
                        let totalNotesAdded = res.data.filter(log => log.action && (log.action.toLowerCase().includes('note_added') || log.action.toLowerCase().includes('note added'))).length;
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

                        let html = '<div class="timeline-line"></div>';
                        dateOrder.forEach(dateStr => {
                            html += `
                                <div class="timeline-group">
                                    <div class="timeline-date-header">
                                        <span class="timeline-date-badge">${dateStr}</span>
                                    </div>
                                    <div class="timeline-items-container">
                            `;

                            groups[dateStr].forEach(item => {
                                let log = item.log;
                                let timeStr = item.time;

                                let markerClass = 'bg-primary';
                                let icon = '<i class="fas fa-info"></i>';
                                let boldTitle = 'Activity';
                                let actionLower = log.action.toLowerCase();

                                if (actionLower.includes('create') || actionLower.includes('import')) {
                                    markerClass = 'bg-success';
                                    icon = '<i class="fas fa-plus"></i>';
                                    boldTitle = 'Created';
                                } else if (actionLower.includes('delete') && (actionLower.includes('enquiry') || actionLower.includes('lead'))) {
                                    markerClass = 'bg-danger';
                                    icon = '<i class="fas fa-trash-alt"></i>';
                                    boldTitle = 'Deleted';
                                } else if (actionLower.includes('share') || actionLower.includes('assign')) {
                                    markerClass = 'bg-info';
                                    icon = '<i class="fas fa-share-alt"></i>';
                                    boldTitle = 'Shared';
                                } else if (actionLower.includes('status') && (actionLower.includes('enquiry') || actionLower.includes('lead'))) {
                                    markerClass = 'bg-warning';
                                    icon = '<i class="fas fa-toggle-on"></i>';
                                    boldTitle = 'Status Changed';
                                } else if (actionLower.includes('quantity')) {
                                    markerClass = 'bg-info';
                                    icon = '<i class="fas fa-list-ol"></i>';
                                    boldTitle = 'Quantity Updated';
                                } else if (actionLower.includes('update') || actionLower.includes('edit')) {
                                    markerClass = 'bg-primary';
                                    icon = '<i class="fas fa-pen"></i>';
                                    boldTitle = 'Updated';
                                } else if (actionLower.includes('call_scheduled') || actionLower.includes('call scheduled')) {
                                    markerClass = 'bg-info';
                                    icon = '<i class="fas fa-phone-alt"></i>';
                                    boldTitle = 'Call Scheduled';
                                } else if (actionLower.includes('call_status_updated') || actionLower.includes('call status updated') || actionLower.includes('call_completed') || actionLower.includes('call completed')) {
                                    markerClass = 'bg-success';
                                    icon = '<i class="fas fa-phone-volume"></i>';
                                    boldTitle = 'Call Activity';
                                } else if (actionLower.includes('call_deleted') || actionLower.includes('call deleted') || actionLower.includes('call_cancelled') || actionLower.includes('call cancelled')) {
                                    markerClass = 'bg-danger';
                                    icon = '<i class="fas fa-phone-slash"></i>';
                                    boldTitle = 'Call Cancelled';
                                } else if (actionLower.includes('note_added') || actionLower.includes('note added')) {
                                    markerClass = 'bg-warning';
                                    icon = `<span class="fw-bold" style="font-size:0.75rem;">N${currentNoteNumber}</span>`;
                                    currentNoteNumber--;
                                    boldTitle = 'Note Added';
                                } else if (actionLower.includes('note_updated') || actionLower.includes('note updated')) {
                                    markerClass = 'bg-warning';
                                    let match = log.description.match(/note N(\d+)/i);
                                    if(match) {
                                        icon = `<span class="fw-bold" style="font-size:0.75rem;">N${match[1]}</span>`;
                                    } else {
                                        icon = '<i class="fas fa-edit"></i>';
                                    }
                                    boldTitle = 'Note Updated';
                                } else if (actionLower.includes('note_deleted') || actionLower.includes('note deleted')) {
                                    markerClass = 'bg-danger';
                                    let match = log.description.match(/note N(\d+)/i);
                                    if(match) {
                                        icon = `<span class="fw-bold" style="font-size:0.75rem;">N${match[1]}</span>`;
                                    } else {
                                        icon = '<i class="fas fa-trash-alt"></i>';
                                    }
                                    boldTitle = 'Note Deleted';
                                } else if (actionLower.includes('attachment_uploaded') || actionLower.includes('attachment uploaded')) {
                                    markerClass = 'bg-info';
                                    icon = '<i class="fas fa-paperclip"></i>';
                                    boldTitle = 'Attachment Uploaded';
                                } else if (actionLower.includes('attachment_deleted') || actionLower.includes('attachment deleted')) {
                                    markerClass = 'bg-danger';
                                    icon = '<i class="fas fa-trash"></i>';
                                    boldTitle = 'Attachment Deleted';
                                } else if (actionLower.includes('quotation')) {
                                    markerClass = 'bg-success';
                                    icon = '<i class="fas fa-file-invoice-dollar"></i>';
                                    boldTitle = 'Quotation';
                                }

                                let descHtml = log.description;
                                if (log.file_url) {
                                    descHtml = `<a href="${log.file_url}" target="_blank" class="text-primary fw-bold text-decoration-underline" title="Click to view file"><i class="fas fa-external-link-alt me-1"></i>${log.description}</a>`;
                                }

                                html += `
                                    <div class="timeline-item-row d-flex align-items-center justify-content-between">
                                        <div class="timeline-marker-circle ${markerClass}">
                                            ${icon}
                                        </div>
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <div class="timeline-item-time text-muted me-3">
                                                <i class="far fa-clock me-1"></i> ${timeStr}
                                            </div>
                                            <div class="timeline-item-description">
                                                <strong>${boldTitle}</strong> - ${descHtml}
                                            </div>
                                        </div>
                                        <div class="timeline-item-by ms-3">
                                            <span class="badge rounded-pill">By ${log.changed_by_name}</span>
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

            // Convert to Deal Modal Flatpickr & Select2
            @include('company.crm.lead.convert_modal_scripts')
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