<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    <style>
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #e2e8f0 !important;
            color: #1e293b !important;
            font-weight: 500;
        }

        /* Match Select2 height with standard Bootstrap form-control height and force full width */
        #createDirectDealModal .select2-container {
            width: 100% !important;
        }

        #createDirectDealModal .select2-container .select2-selection--single {
            height: 38px !important;
            width: 100% !important;
            border: 1px solid #e1e3ea;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
        }

        #createDirectDealModal .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: normal;
            padding-left: 0.75rem;
            width: 100% !important;
        }

        #createDirectDealModal .select2-container .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 0.5rem;
        }

        .kanban-wrapper {
            --kbn-bg-color: #f8fafc;
            --kbn-column-bg: #f8fafc;
            --kbn-card-hover: #ffffff;
            --kbn-primary: #3b82f6;
            --kbn-text-dark: #0f172a;
            --kbn-text-muted: #64748b;
            --kbn-border-radius: 10px;
        }

        .kanban-wrapper .kanban-columns-container {
            display: flex;
            overflow-x: auto;
            padding-bottom: 12px;
            gap: 20px;
            height: calc(100vh - 260px);
            align-items: stretch;
        }

        /* Custom styled horizontal scrollbar */
        .kanban-wrapper .kanban-columns-container::-webkit-scrollbar {
            height: 10px;
        }

        .kanban-wrapper .kanban-columns-container::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 6px;
        }

        .kanban-wrapper .kanban-columns-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 6px;
        }

        .kanban-wrapper .kanban-columns-container::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }

        .kanban-wrapper .kanban-column {
            background-color: var(--kbn-column-bg);
            border-radius: var(--kbn-border-radius);
            min-width: 300px;
            max-width: 300px;
            display: flex;
            flex-direction: column;
            height: 100%;
            flex-shrink: 0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .kanban-wrapper .kanban-header {
            padding: 12px 16px;
            font-weight: 400;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .kanban-wrapper .kanban-header .stage-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kanban-wrapper .kanban-header .badge {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }

        .kanban-wrapper .deals-container {
            flex-grow: 1;
            padding: 14px;
            overflow-y: auto;
            min-height: 100px;
        }

        .kanban-wrapper .deals-container::-webkit-scrollbar {
            width: 5px;
        }

        .kanban-wrapper .deals-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .kanban-wrapper .deals-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }

        .kanban-wrapper .deal-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 14px;
            margin-bottom: 14px;
            cursor: grab;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            border: 1px solid #e2e8f0;
            border-left-width: 4px;
            user-select: none;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .kanban-wrapper .deal-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .kanban-wrapper .deal-card.dragging {
            opacity: 0.5;
            border: 2px dashed var(--kbn-primary);
            background-color: #f8f9fa;
            transform: rotate(1deg);
        }

        .kanban-wrapper .deal-card .card-title a {
            color: #1e293b;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            display: block;
            line-height: 1.3;
        }

        .kanban-wrapper .deal-card .card-title a:hover {
            color: var(--kbn-primary);
        }

        .kanban-wrapper .deal-card .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            color: var(--kbn-text-muted);
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #eee;
        }

        .kanban-wrapper .deal-amount-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .kanban-wrapper .kanban-column.drop-zone {
            background-color: #f1f5f9;
            border: 2px dashed #3b82f6;
        }

        .kanban-wrapper .deal-card.is-client {
            background: #ffffff;
            border-left: 4px solid #10b981;
        }

        .kanban-wrapper .deal-card.is-client .card-title a {
            color: #10b981;
        }

        .kanban-wrapper .deal-card.is-lost {
            background: #ffffff;
            border-left: 4px solid #ef4444;
        }

        .kanban-wrapper .deal-card.is-lost .card-title a {
            color: #ef4444;
        }

        .kanban-wrapper .deal-card[draggable="false"] {
            cursor: default;
        }

        .filter-chevron {
            transition: transform .2s;
            font-size: 10px;
        }

        [data-bs-toggle="collapse"][aria-expanded="true"] .filter-chevron {
            transform: rotate(180deg);
        }

        .select2-sort+.select2-container--default .select2-selection--single .select2-selection__rendered {
            border: none !important;
        }

        .kanban-wrapper .send-quotation-btn {
            background-color: #ffffff !important;
            color: #334155 !important;
            border: 1px solid #cbd5e1 !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            padding: 6px 12px !important;
            border-radius: 6px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            transition: all 0.2s !important;
            width: 100% !important;
            margin-top: 8px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .kanban-wrapper .send-quotation-btn:hover {
            background-color: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
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
                                    <button class="btn btn-sm fw-bold d-flex align-items-center gap-2"
                                        id="btnToggleNewDealFilter"
                                        style="background: #fff; color: #eab308; border: 1px solid #e2e8f0; border-radius: 8px; padding: 7px 16px; font-size: 13px; transition: all 0.2s;">
                                        <i class="far fa-star"></i> New Deal
                                    </button>

                                    <button class="btn btn-sm btn-light border fw-bold d-flex align-items-center gap-2"
                                        style="border-radius: 8px; padding: 7px 16px; font-size: 13px;" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#dealFilterPanel"
                                        aria-expanded="false" aria-controls="dealFilterPanel">
                                        <i class="fas fa-filter"></i>
                                        <span>Filters</span>
                                        <i class="fas fa-chevron-down filter-chevron"></i>
                                    </button>

                                    @if (permissions_check('canCreate'))
                                        <button class="btn btn-sm fw-bold d-flex align-items-center gap-2" id="newDealBtn"
                                            style="background:#1e293b; color:#fff; border-radius:8px; padding:7px 16px; font-size:13px;">
                                            <i class="fas fa-plus"></i> New Deal
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body p-0">

                                <div class="tab-content">
                                    @if (permissions_check('canView'))
                                        <div class="tab-pane border-0 show active text-muted p-3" id="lead-list"
                                            role="tabpanel">
                                            <div class="text-end mb-2 mx-1">
                                                <small class="text-muted" id="activeFiltersHint"></small>
                                            </div>

                                            <div class="collapse" id="dealFilterPanel">
                                                <div
                                                    class="row mb-3 px-2 g-2 bg-light border rounded pt-2 pb-3 mx-1 shadow-sm">
                                                    <div class="col-xl-2 col-md-4">
                                                        <label
                                                            class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Deal
                                                            Name</label>
                                                        <select id="filterCompany"
                                                            class="form-select form-select-sm select2">
                                                            <option value="">All Deals</option>
                                                            @foreach ($deals as $deal)
                                                                <option value="{{ $deal->deal_id }}">{{ $deal->deal_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-xl-2 col-md-4">
                                                        <label
                                                            class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Date
                                                            Range</label>
                                                        <input type="text" id="filterDateRange"
                                                            class="form-control form-control-sm bg-white"
                                                            placeholder="Select Date Range">
                                                    </div>
                                                    <div class="col-xl-2 col-md-4">
                                                        <label
                                                            class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Stage</label>
                                                        <select id="filterStage" class="form-select form-select-sm select2">
                                                            <option value="">All Stages</option>
                                                            @foreach ($stages as $stage)
                                                                <option value="{{ $stage->id }}">{{ $stage->stage_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    {{-- <div class="col-xl-2 col-md-4">
                                                        <label
                                                            class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Created
                                                            By</label>
                                                        <select id="filterCreatedBy"
                                                            class="form-select form-select-sm select2">
                                                            <option value="">All Staff</option>
                                                            @foreach ($creators as $creator)
                                                            <option value="{{ $creator->id }}">{{ $creator->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> --}}
                                                    {{-- <div class="col-xl-2 col-md-4">
                                                        <label
                                                            class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Quotation
                                                            No</label>
                                                        <select id="filterQuotationNo"
                                                            class="form-select form-select-sm select2">
                                                            <option value="">All Quotations</option>
                                                            @foreach ($quotations as $quot)
                                                            <option value="{{ $quot->quotation_no }}">{{ $quot->quotation_no
                                                                }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> --}}
                                                    <div class="col-xl-2 col-md-4">
                                                        <label
                                                            class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Sort
                                                            By</label>
                                                        <select id="filterSortBy" class="form-select form-select-sm select2"
                                                            data-minimum-results-for-search="Infinity">
                                                            <option value="">Default View</option>
                                                            <option value="amount_asc">Amount (Low to High)</option>
                                                            <option value="amount_desc">Amount (High to Low)</option>
                                                            <option value="date_asc">Closing Date (Old to New)</option>
                                                            <option value="date_desc">Closing Date (New to Old)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                                        <button class="btn btn-sm btn-primary fw-bold px-4"
                                                            id="applyFilters">
                                                            <i class="fas fa-filter me-1"></i> Apply Filters
                                                        </button>
                                                        <button class="btn btn-sm btn-secondary fw-bold px-4"
                                                            id="clearFilters">
                                                            <i class="fas fa-times me-1"></i> Clear Filters
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="kanban-wrapper">
                                                <div class="kanban-container">
                                                    <div class="kanban-columns-container">
                                                        <div class="text-center py-5 w-100 loading-spinner">
                                                            <div class="spinner-border text-primary" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                            <p class="mt-2 text-muted">Loading deals...</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>


                                    @endif
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>


            <!-- ===================== COMPLAIN REASON MODAL ===================== -->
            <div class="modal fade" id="complainReasonModal" tabindex="-1" aria-labelledby="complainReasonModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-light pb-2">
                            <h6 class="modal-title fw-bold text-danger" id="complainReasonModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Specify Complaint Details</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="complainReasonForm" novalidate>
                                <div class="mb-0">
                                    <label for="complainReasonInput" class="form-label fw-bold small text-dark">What is the complaint/query? <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="complainReasonInput" rows="4" placeholder="Enter complaint details here..." required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer py-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger btn-sm" id="submitComplainReasonBtn"><i class="fas fa-check me-1"></i>Submit Complaint</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== EDIT STAGE NAME MODAL ===================== -->
            <div class="modal fade" id="editStageNameModal" tabindex="-1" aria-labelledby="editStageNameModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header bg-light pb-2">
                            <h6 class="modal-title fw-bold text-primary" id="editStageNameModalLabel"><i class="fas fa-pen me-2"></i>Edit Stage Name</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="editStageId">
                            <div class="mb-0">
                                <label class="form-label fw-bold small text-dark">Stage Name</label>
                                <input type="text" class="form-control" id="editStageName" placeholder="Enter stage name" maxlength="100">
                                <div class="invalid-feedback" id="editStageNameError"></div>
                            </div>
                        </div>
                        <div class="modal-footer py-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary btn-sm" id="saveStageNameBtn"><i class="fas fa-save me-1"></i>Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== CREATE DIRECT DEAL MODAL ===================== -->
            <div class="modal fade" id="createDirectDealModal" aria-labelledby="createDirectDealModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-light pb-2">
                            <h5 class="modal-title text-primary fw-bold" id="createDirectDealModalLabel">
                                <i class="fas fa-handshake me-2"></i>Create New Deal
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createDirectDealForm" novalidate>
                            @csrf
                            <div class="modal-body">
                                <div class="row g-3">

                                <div class="col-12 col-md-6 mb-2">
                                    <label class="form-label">Deal Name <span class="text-danger">*</span></label>
                                    <select class="form-select select2" name="product_ids[]" id="nd_deal_name" multiple="multiple" style="width: 100%;" required>
                                        @foreach($products as $product)
                                            @if($product->category_id == 1)
                                                <option value="{{ $product->product_id }}" data-price="{{ $product->prod_amount }}">
                                                    {{ $product->prod_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-6 mb-2">
                                    <div class="d-flex justify-content-between align-items-center"
                                         style="min-height:24px;">
                                         <label class="form-label mb-0">Company Name <span
                                                 class="text-danger">*</span></label>
                                         <div class="btn-group btn-group-sm" role="group">
                                             <input type="radio" class="btn-check" name="nd_customer_type"
                                                 id="nd_tab_existing" autocomplete="off" checked>
                                             <label class="btn btn-outline-primary fw-semibold py-0 px-2"
                                                 for="nd_tab_existing"
                                                 style="font-size:11px;line-height:22px;">Existing</label>
                                             <input type="radio" class="btn-check" name="nd_customer_type"
                                                 id="nd_tab_new" autocomplete="off">
                                             <label class="btn btn-outline-primary fw-semibold py-0 px-2"
                                                 for="nd_tab_new"
                                                 style="font-size:11px;line-height:22px;">New</label>
                                         </div>
                                     </div>
                                      <div id="nd_existing_wrap" class="mt-1">
                                          <select id="nd_customer_select" class="form-select" style="width:100%;">
                                              <option value=""></option>
                                          </select>
                                      </div>
                                      <div id="nd_new_wrap" class="mt-1 d-none">
                                          <input type="text" class="form-control" name="company_name_new"
                                              id="nd_company_name_new" placeholder="Type new company name">
                                      </div>
                                      <input type="hidden" name="company_name" id="nd_company_name_hidden">
                                  </div>

                                  {{-- Location Row (City | State | Country) - Visible in New Company Mode --}}
                                  <div id="nd_location_row" class="col-12 mb-2 d-none">
                                      <div class="row">
                                          <div class="col-12 col-md-4 mb-2 mb-md-0">
                                              <label class="form-label">City <span class="text-danger">*</span></label>
                                              <select id="nd_city_id" name="city_id" class="form-select select2" style="width:100%;">
                                                  <option value=""></option>
                                              </select>
                                          </div>
                                          <div class="col-6 col-md-4 mb-2 mb-md-0">
                                              <label class="form-label">State</label>
                                              <input type="text" id="nd_state_name" class="form-control bg-light" placeholder="State" readonly>
                                              <input type="hidden" id="nd_state_id" name="state_id">
                                          </div>
                                          <div class="col-6 col-md-4 mb-2 mb-md-0">
                                              <label class="form-label">Country</label>
                                              <input type="text" id="nd_country_name" class="form-control bg-light" placeholder="Country" readonly>
                                              <input type="hidden" id="nd_country_id" name="country_id">
                                          </div>
                                      </div>
                                   </div>

                                 {{-- Row 2: Contact Person | Phone Number | Email --}}
                                 <div class="col-6 col-md-4 mb-2">
                                     <label class="form-label">Contact Person</label>
                                     <input type="text" class="form-control" name="contact_person"
                                         id="nd_contact_person" placeholder="e.g. John Doe">
                                 </div>

                                 <div class="col-6 col-md-4 mb-2">
                                     <label class="form-label">Phone Number</label>
                                     <input type="text" class="form-control" name="phone" id="nd_phone"
                                          placeholder="e.g. 9876543210" maxlength="15"
                                          pattern="[0-9()+\- ]*"
                                          oninput="this.value = this.value.replace(/[^0-9()+\- ]/g, '')">
                                 </div>

                                 <div class="col-6 col-md-4 mb-2">
                                     <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                     <input type="email" class="form-control" name="email" id="nd_email"
                                          placeholder="e.g. john@example.com" required>
                                 </div>

                                      {{-- Row 3: Deal Type | Deal Stage | Assigned Staff --}}
                                      @if(isStaffLogin())
                                          <input type="hidden" name="deal_type" id="nd_deal_type" value="{{ session('staff_type') ?? 'Domestic' }}">
                                      @else
                                          <div class="col-12 col-md-4 mb-2">
                                              <label class="form-label">Deal Type <span class="text-danger">*</span></label>
                                              <select class="form-select select2" name="deal_type" id="nd_deal_type" style="width:100%;" required>
                                                  <option value=""></option>
                                                  <option value="Domestic">Domestic</option>
                                                  <option value="Export">Export</option>
                                              </select>
                                          </div>
                                      @endif

                                      <div class="col-6 col-md-4 mb-2">
                                          <label class="form-label">Deal Stage <span class="text-danger">*</span></label>
                                          <select class="form-select" name="deal_stage" id="nd_deal_stage" style="width:100%;" required>
                                              <option value="">Select Stage</option>
                                              @foreach($stages as $stage)
                                                  <option value="{{ $stage->id }}">{{ $stage->stage_name }}</option>
                                              @endforeach
                                          </select>
                                      </div>

                                      <div class="col-6 col-md-4 mb-2">
                                          <label class="form-label">Assigned Staff</label>
                                          <select class="form-select" name="assigned_to" id="nd_assigned_to" style="width:100%;">
                                              <option value="">Select Staff</option>
                                              @foreach($staffs as $staff)
                                                  <option value="{{ $staff->staff_id }}" data-type="{{ $staff->staff_type }}" {{ (session('staff_id') == $staff->staff_id) ? 'selected' : '' }}>
                                                      {{ $staff->staff_name }}{{ $staff->staff_type ? ' (' . ucfirst($staff->staff_type) . ')' : '' }}
                                                  </option>
                                              @endforeach
                                          </select>
                                      </div>

                                     {{-- Row 4: Deal Amount | Closing Date | GST Number --}}
                                     <div class="col-6 col-md-4 mb-2">
                                         <label class="form-label">Deal Amount (₹) <span
                                                 class="text-danger">*</span></label>
                                         <input type="number" class="form-control" name="amount" id="nd_amount"
                                             step="0.01" min="0" placeholder="0.00" required>
                                     </div>

                                     <div class="col-6 col-md-4 mb-2">
                                         <label class="form-label">Closing Date <span
                                                 class="text-danger">*</span></label>
                                         <input type="text" class="form-control bg-white" name="closing_date"
                                             id="nd_closing_date" placeholder="DD/MM/YYYY" readonly required>
                                     </div>

                                     <div class="col-6 col-md-4 mb-2">
                                         <label class="form-label">GST Number</label>
                                         <input type="text" class="form-control text-uppercase" name="gst_no"
                                             id="nd_gst_no" placeholder="e.g. 27AAPFU0939F1ZV" maxlength="15">
                                     </div>

                                     {{-- Row: IWO No | Dispatch Date --}}
                                    <div class="col-6 mb-2">
                                        <label class="form-label">IWO No</label>
                                        <input type="text" class="form-control" name="iwo_no" id="nd_iwo_no" placeholder="e.g. IWO-1024">
                                    </div>

                                    <div class="col-6 mb-2">
                                        <label class="form-label">Dispatch Date</label>
                                        <input type="text" class="form-control bg-white" name="dispatch_date" id="nd_dispatch_date" placeholder="DD-MM-YYYY" readonly>
                                    </div>

                                     {{-- Row 5: Description (Full Width) --}}
                                     <div class="col-12 mb-2">
                                         @include('components.tooltip-help', ['key' => 'deal_module_description', 'label' => 'Description', 'class' => 'form-label form-label-bold'])
                                         {{-- <label class="form-label">Description</label> --}}
                                         <textarea class="form-control" name="description" id="nd_description" rows="3"
                                             maxlength="1000" placeholder="Enter description..."></textarea>
                                     </div>
                                 </div>
                             </div>
                             <div class="modal-footer">
                                <button type="button"
                                    class="btn btn-secondary btn-wave waves-effect waves-light fw-bold"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary btn-wave waves-effect waves-light fw-bold"
                                    id="saveDealBtn">Save Deal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- ===================== END CREATE DIRECT DEAL MODAL ===================== -->

            <div class="modal fade" id="ConvertDealModal" tabindex="-1" aria-labelledby="ConvertDealModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ConvertDealModalLabel">Verify Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="dealClosedWon" method="POST" novalidate>
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="deal_id" id="deal_id_input">
                                <input type="hidden" name="lead_id" id="lead_id_input">
                                <input type="hidden" name="stage_id" id="stage_id_input">

                                <div class="row">
                                    <div class="col-md-4 col-6 mb-1">
                                        <label for="amount" class="form-label">Amount</label>
                                        <input type="number" class="form-control" step="0.01" name="amount" id="amount"
                                            placeholder="Enter amount" required>
                                        <div class="invalid-feedback">Please enter the amount.</div>
                                    </div>



                                    <div class="col-md-4 col-6 mb-1">
                                        <label for="stage" class="form-label">Stage</label>
                                        <input type="text" class="form-control" name="stage" id="stage"
                                            value="Closed Won" readonly required>
                                        <div class="invalid-feedback">Stage is required.</div>
                                    </div>

                                    <div class="col-md-4 col-6 mb-1">
                                        <label for="company_name" class="form-label">Company Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="company_name" name="company_name"
                                            required>
                                        <div class="invalid-feedback">Please enter company name.</div>
                                    </div>



                                    <div class="col-md-4 col-6 mb-1">
                                        <label for="contact_person_name" class="form-label">Contact Person Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="contact_person_name"
                                            name="contact_person_name" placeholder="Contact Person Name" required>
                                        <div class="invalid-feedback">Please enter contact person name.</div>
                                    </div>

                                    <div class="col-md-4 col-6 mb-1">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <div class="invalid-feedback">Please enter a valid email address.</div>
                                    </div>

                                    <div class="col-md-4 col-6 mb-1">
                                        <label for="phone" class="form-label">Phone <span
                                                class="text-danger">*</span></label>
                                         <input type="text" class="form-control" id="phone" name="phone" maxlength="15" pattern="[0-9()+\- ]*" oninput="this.value = this.value.replace(/[^0-9()+\- ]/g, '')" required>
                                        <div class="invalid-feedback">Please enter phone.</div>
                                    </div>
                                    <div class="col-md-4 col-6 mb-1">
                                        <label for="gst_no" class="form-label">GST No</label>
                                        <input type="text" class="form-control" id="gst_no" name="gst_no">
                                    </div>
                                    <div class="col-md-4 col-6 mb-1">
                                        <label for="closed_won_iwo_no" class="form-label">IWO No <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="closed_won_iwo_no" name="iwo_no" required>
                                        <div class="invalid-feedback">Please enter IWO No.</div>
                                    </div>
                                    <div class="col-md-4 col-6 mb-1">
                                        <label for="closed_won_dispatch_date" class="form-label">Dispatch Date <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="closed_won_dispatch_date" name="dispatch_date" required placeholder="DD-MM-YYYY">
                                        <div class="invalid-feedback">Please select Dispatch Date.</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-6 mb-1">
                                            <label for="cust_city" class="form-label">City <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control select2" id="cust_city" name="city"
                                                required></select>
                                            <div class="invalid-feedback">Please select city.</div>
                                        </div>

                                        <div class="col-md-4 col-6 mb-1">
                                            <label for="state_name" class="form-label">State <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="state_name" class="form-control bg-light border-0"
                                                readonly required>
                                            <input type="hidden" name="state" id="cust_state">
                                            <div class="invalid-feedback">State is required.</div>
                                        </div>

                                        <div class="col-md-4 col-6 mb-1">
                                            <label for="country_name" class="form-label">Country <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="country_name" class="form-control bg-light border-0"
                                                readonly required>
                                            <input type="hidden" name="country" id="cust_country">
                                            <div class="invalid-feedback">Country is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-1">
                                        <label for="deal_address" class="form-label">Address <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" id="deal_address" name="address" rows="2"
                                            placeholder="Enter full address" required></textarea>
                                        <div class="invalid-feedback">Please enter address.</div>
                                    </div>

                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" id="cancelConvertDeal"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="ConvertDealLostModal" tabindex="-1" aria-labelledby="ConvertDealLostModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ConvertDealLostModalLabel">Verify Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="dealClosedLost" method="POST" novalidate>
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="deal_id" id="deal_lost_id_input">
                                <input type="hidden" name="lead_id" id="lead_lost_id_input">
                                <input type="hidden" name="stage_id" id="stage_lost_id_input">
                                <div class="mb-3">
                                    <label for="amount_lost" class="form-label">Amount</label>
                                    <input type="number" class="form-control" name="amount" id="amount_lost"
                                        placeholder="Enter amount" required>
                                    <div class="invalid-feedback">Please enter the amount.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="closing_date_lost_input" class="form-label">Closing Date</label>
                                    <input type="text" class="form-control" name="closing_date"
                                        id="closing_date_lost_input" required>
                                    <div class="invalid-feedback">Please select a closing date.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="stage_lost" class="form-label">Stage</label>
                                    <input type="text" class="form-control" name="stage" id="stage_lost"
                                        value="Closed Lost" readonly required>
                                    <div class="invalid-feedback">Stage is required.</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" id="cancelConvertDealLost"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="EditDealModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-light pb-2">
                            <h5 class="modal-title text-primary fw-bold"><i class="fas fa-edit me-2"></i>Edit Deal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editDealForm" novalidate>
                            @csrf
                            <input type="hidden" name="deal_id" id="edit_deal_id">
                            <div class="modal-body bg-white">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                         <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Deal Name
                                             <span class="text-danger">*</span></label>
                                         <select class="form-select form-select-sm select2" name="deal_name[]"
                                             id="edit_deal_name" multiple required style="width: 100%;">
                                             @foreach($products as $product)
                                                 <option value="{{ $product->prod_name }}" data-price="{{ $product->prod_amount ?? 0 }}">
                                                     {{ $product->prod_name }}
                                                 </option>
                                             @endforeach
                                         </select>
                                     </div>
                                    <div class="col-md-6">
                                        <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Company
                                            Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" name="company_name"
                                            id="edit_company_name" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Amount
                                            <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control form-control-sm" step="0.01"
                                            name="amount" id="edit_amount" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Closing
                                            Date <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm bg-white"
                                            name="closing_date" id="edit_closing_date" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">GST
                                            No</label>
                                        <input type="text" class="form-control form-control-sm text-uppercase"
                                            name="gst_no" id="edit_gst_no" maxlength="15"
                                            pattern="^[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}[1-9A-Za-z]{1}[Zz][0-9A-Za-z]{1}$">
                                        <div class="invalid-feedback">Please enter a valid 15-character GST format.
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">
                                            Description
                                        </label>

                                        <textarea class="form-control form-control-sm" name="description"
                                            id="edit_description" rows="3" maxlength="1000"
                                            placeholder="Enter description..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light pt-2 pb-2">
                                <button type="button" class="btn btn-sm btn-secondary fw-bold"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-primary fw-bold" id="saveEditDealBtn"><i
                                        class="fas fa-save me-1"></i> Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
        @include('include.footer')
    </div>
    @include('include.scroll-top')
    @include('include.footer_link')

    <script>
        $(document).ready(function () {

            const img_path = "{{ $img_path }}";
            const canUpdateJS = {{ permissions_check('canUpdate') ? 'true' : 'false' }};

            function hexToRgb(hex) {
                var c;
                if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)) {
                    c = hex.substring(1).split('');
                    if (c.length == 3) {
                        c = [c[0], c[0], c[1], c[1], c[2], c[2]];
                    }
                    c = '0x' + c.join('');
                    return [(c >> 16) & 255, (c >> 8) & 255, c & 255].join(',');
                }
                return '13, 110, 253';
            }

            const state = {
                csrfToken: $('meta[name="csrf-token"]').attr('content'),
                originalStage: null,
                draggedDealId: null,
                draggedLeadId: null,
                draggedElement: null,
                isFormSubmitted: false,
                isProgrammaticSelect: false,
                filterNewDealOnly: false,
            };

            const routes = {
                kanban: "{{ route('deal.kanban.data') }}",
                closedWon: "{{ route('deal.closed-won') }}",
                closedLost: "{{ route('deal.closed-lost') }}",
                updateStage: "{{ route('deal.update-stage', ['deal' => ':dealId']) }}",
                closedWonDetails: "{{ route('deal.details.for.closed-won', ['lead' => ':leadId']) }}",
                getLocationByCity: "{{ route('location.by_city', ['id' => ':id']) }}",
                QuotationUrl: "{{ route('quotation.create', ['dealId' => ':dealId']) }}",
                editDeal: "{{ route('deal.edit', ['id' => ':id']) }}",
                updateDeal: "{{ route('deal.update', ['id' => ':id']) }}",
                createDirect: "{{ route('deal.create-direct') }}",
                customersList: "{{ route('deal.customers-list') }}",
                updateStageName: "{{ route('dealstage.updateName') }}"
            };


            const api = {
                request: (url, method, data, btn = null, loadingText = '') => {
                    if (btn) ui.toggleButton(btn, true, loadingText);

                    return $.ajax({
                        url,
                        type: method,
                        data,
                        headers: {
                            'X-CSRF-TOKEN': state.csrfToken
                        }
                    }).always(() => {
                        if (btn) ui.toggleButton(btn, false);
                    });
                }
            };


            const ui = {
                toast: (type, message, title = '') => {
                    iziToast[type]({
                        title,
                        message,
                        position: 'topRight',
                        timeout: 700,
                    });
                },

                toggleButton: (btn, loading, text = 'Loading...') => {
                    if (!btn) return;
                    if (loading) {
                        $(btn).data("orig", $(btn).html());
                        $(btn).html(`<span class="spinner-border spinner-border-sm me-2"></span>${text}`)
                            .prop("disabled", true);
                    } else {
                        $(btn).html($(btn).data("orig")).prop("disabled", false);
                    }
                },

                showSpinner: () => {
                    $("body").append(`
            <div class="global-spinner-overlay"
                style="position:fixed; inset:0; display:flex; justify-content:center; align-items:center;
                       background:rgba(0,0,0,0.35); z-index:9999;">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        `);
                },

                hideSpinner: () => {
                    $(".global-spinner-overlay").remove();
                }
            };


            function initSelect2Ajax(selector, url, placeholder, mapFn) {
                $(selector).select2({
                    dropdownParent: $(selector).parent(),
                    placeholder: placeholder,
                    allowClear: true,
                    width: "100%",
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { search: params.term, page: params.page || 1 };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            if (mapFn) {
                                return {
                                    results: mapFn(data),
                                    pagination: { more: data.next_page_url ? true : false }
                                };
                            }
                            return {
                                results: data.results,
                                pagination: { more: (params.page * 10) < data.total_count }
                            };
                        },
                        cache: true
                    }
                });
            }

            function setSelect2Value(selector, id, text) {
                var $el = $(selector);
                if ($el.find("option[value='" + id + "']").length === 0) {
                    $el.append(new Option(text, id, true, true));
                } else {
                    $el.val(id);
                }
                $el.trigger('change');
            }

            // Initialize flatpickr for the edit modal
            flatpickr("#edit_closing_date", {
                dateFormat: "d/m/Y",
                allowInput: true
            });

            // Open Edit Modal & Fetch Data
            $(document).on('click', '.edit-deal-btn', function (e) {
                e.preventDefault();
                const dealId = $(this).data('id');
                ui.showSpinner();

                api.request(routes.editDeal.replace(':id', dealId), 'GET', {})
                    .done(res => {
                        if (res.success) {
                            const d = res.data;
                            $('#edit_deal_id').val(d.deal_id);
                            $('#edit_description').val(d.description);
                            if ($('#edit_deal_name').data('select2')) {
                                $('#edit_deal_name').select2('destroy');
                            }
                            $('#edit_deal_name').select2({
                                dropdownParent: $('#EditDealModal'),
                                placeholder: 'Select Product',
                                width: '100%'
                            });

                            if (d.deal_name) {
                                var dealNames = typeof d.deal_name === 'string' ? d.deal_name.split(',').map(function (s) { return s.trim(); }) : d.deal_name;
                                dealNames.forEach(function (name) {
                                    if ($('#edit_deal_name').find("option[value='" + name + "']").length === 0) {
                                        $('#edit_deal_name').append(new Option(name, name, true, true));
                                    }
                                });
                                $('#edit_deal_name').val(dealNames).trigger('change', [true]);
                            } else {
                                $('#edit_deal_name').val([]).trigger('change', [true]);
                            }
                            $('#edit_company_name').val(d.company_name);
                            $('#edit_amount').val(d.amount);
                            $('#edit_closing_date').val(d.closing_date_formatted);
                            $('#edit_gst_no').val(d.gst_no);

                            if ($('#edit_sales_person').data('select2')) {
                                $('#edit_sales_person').select2('destroy');
                            }
                            $('#edit_sales_person').select2({
                                dropdownParent: $('#EditDealModal'),
                                placeholder: 'Select Sales Person',
                                allowClear: true,
                                width: '100%'
                            });

                            if (d.sales_person) {
                                $('#edit_sales_person').val(String(d.sales_person)).trigger('change');
                            } else {
                                $('#edit_sales_person').val('').trigger('change');
                            }

                            $('#EditDealModal').modal('show');

                            $('#EditDealModal').modal('show');
                        } else {
                            ui.toast('error', res.message || 'Failed to load deal data');
                        }
                    })
                    .fail(() => ui.toast('error', 'Server error'))
                    .always(() => ui.hideSpinner());
            });

            // Handle Update Submission
            $('#editDealForm').on('submit', function (e) {
                e.preventDefault();
                const form = $(this);

                if (!form[0].checkValidity()) {
                    form.addClass('was-validated');
                    return;
                }

                const dealId = $('#edit_deal_id').val();
                const submitBtn = $('#saveEditDealBtn');

                ui.toggleButton(submitBtn, true, "Saving...");

                api.request(routes.updateDeal.replace(':id', dealId), 'POST', form.serialize())
                    .done(res => {
                        if (res.success) {
                            ui.toast('success', res.message);
                            $('#EditDealModal').modal('hide');
                            refreshCurrentView();
                        } else {
                            ui.toast('error', res.message || 'Failed to update deal');
                        }
                    })
                    .fail(xhr => {
                        let msg = "An error occurred.";
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join("<br>");
                        }
                        ui.toast("error", msg);
                    })
                    .always(() => ui.toggleButton(submitBtn, false));
            });

            // Auto-calculate total amount when products are selected/deselected in Edit Deal modal
            $(document).on('change', '#edit_deal_name', function (e, isInitialLoad) {
                if (isInitialLoad) return;
                var total = 0;
                $(this).find('option:selected').each(function () {
                    total += parseFloat($(this).data('price')) || 0;
                });
                $('#edit_amount').val(total.toFixed(2));
            });

            // Reset modal on close
            $('#EditDealModal').on('hidden.bs.modal', function () {
                $('#editDealForm')[0].reset();
                $('#editDealForm').removeClass('was-validated');
                $('#edit_sales_person').val('').trigger('change');
                $('#edit_description').val('');
                if ($('#edit_deal_name').data('select2')) {
                    $('#edit_deal_name').select2('destroy');
                }
            });


            function initializeComponents() {
                $('.select2').not('#edit_sales_person, #EditDealModal .select2').select2({
                    width: "100%"
                });

                initSelect2Ajax('#cust_city', "{{ route('location.cities') }}", "Search City...", function (data) {
                    return $.map(data.data, function (obj) { return { id: obj.city_id, text: obj.city_name }; });
                });

                $('#cust_city').on('change', function () {
                    if (state.isProgrammaticSelect) return;
                    var cityId = $(this).val();
                    if (!cityId) {
                        $('#cust_state, #cust_country').val('');
                        $('#state_name, #country_name').val('');
                        return;
                    }
                    var url = "{{ route('location.by_city', ':id') }}".replace(':id', cityId);
                    api.request(url, 'GET', {}).done(function (response) {
                        if (response.status) {
                            $('#cust_state').val(response.data.state_id);
                            $('#state_name').val(response.data.state_name);
                            $('#cust_country').val(response.data.country_id);
                            $('#country_name').val(response.data.country_name);
                        } else {
                            $('#cust_state, #cust_country').val('');
                            $('#state_name, #country_name').val('');
                        }
                    });
                });

                $('#filterDateRange').flatpickr({
                    mode: "range",
                    dateFormat: "d/m/Y",
                    allowInput: true
                });

                $('#applyFilters').on('click', function () {
                    loadKanbanData();
                });

                // Toggle New Deal Filter button
                $('#btnToggleNewDealFilter').on('click', function () {
                    state.filterNewDealOnly = !state.filterNewDealOnly;

                    if (state.filterNewDealOnly) {
                        $(this).css({
                            'background': '#eab308',
                            'color': '#fff',
                            'border-color': '#eab308'
                        }).find('i').removeClass('far').addClass('fas');
                    } else {
                        $(this).css({
                            'background': '#fff',
                            'color': '#eab308',
                            'border-color': '#e2e8f0'
                        }).find('i').removeClass('fas').addClass('far');
                    }

                    loadKanbanData();
                });

                $('#clearFilters').on('click', function () {
                    $('#filterCompany').val('').trigger('change');
                    $('#filterStage').val('').trigger('change');
                    $('#filterCreatedBy').val('').trigger('change');
                    $('#filterQuotationNo').val('').trigger('change');
                    $('#filterSortBy').val('').trigger('change');
                    if ($('#filterDateRange')[0] && $('#filterDateRange')[0]._flatpickr) {
                        $('#filterDateRange')[0]._flatpickr.clear();
                    }
                    loadKanbanData();
                });


                flatpickr("#closing_date_lost_input", {
                    dateFormat: "d/m/Y",
                    allowInput: true,
                    defaultDate: "today"
                });

                $("#ClosingDate").flatpickr({
                    dateFormat: "d-m-Y",
                    minDate: "today",
                    disableMobile: true,
                    onChange: (d, s, inst) => inst.close()
                });

                $(document).on('click', '.send-quotation-btn', function () {
                    let dealId = $(this).data('dealid');

                    let url = routes.QuotationUrl.replace(':dealId', dealId);

                    window.location.href = url;
                });
            }


            function loadKanbanData() {

                const filters = {
                    company_id: $('#filterCompany').val(),
                    date_range: $('#filterDateRange').val(),
                    stage_id: $('#filterStage').val(),
                    created_by: $('#filterCreatedBy').val(),
                    quotation_no: $('#filterQuotationNo').val(),
                    sort_by: $('#filterSortBy').val(),
                    is_direct: state.filterNewDealOnly ? 1 : ''
                };

                api.request(routes.kanban, 'GET', filters).done(res => {
                    if (res.success) {
                        renderKanbanColumns(res.data);
                        initDragAndDrop();
                    }
                })
                    .fail(() => ui.toast('error', 'Error loading Kanban data', 'Error'));
            }


            function renderKanbanColumns(stages) {
                let html = '';

                stages.forEach((stage, index) => {
                    const totalAmount = stage.deals.reduce((sum, d) => {
                        const amt = d.latest_amount ?? d.amount;
                        const val = parseFloat(amt);
                        return sum + (isNaN(val) ? 0 : val);
                    }, 0);

                    const stageNameRaw = stage.stage_name || 'Untitled';
                    const stageName = String(stageNameRaw).trim();
                    const stageNameLower = stageName.toLowerCase();

                    const colors = [
                        '#7c3aed', // Approach (Purple)
                        '#2563eb', // Inquiry (Blue)
                        '#0d9488', // Quotation (Teal)
                        '#ea580c', // Focused (Orange)
                        '#16a34a', // Green
                        '#0284c7', // Light Blue
                        '#be185d', // Pink
                    ];
                    const isWon = stage.is_won == 1;
                    const isLost = stage.is_lost == 1;

                    let stageColor = colors[index % colors.length];
                    if (isLost) {
                        stageColor = '#dc3545';
                    } else if (isWon) {
                        stageColor = '#2fb380';
                    }

                    const wonIndicator = isWon
                        ? `<span title="Won Stage"
                            style="font-size:0.68rem;
                                background:#ffffff;
                                color:#16a34a;
                                padding:2px 6px;
                                border-radius:12px;
                                font-weight:700;
                                white-space: nowrap;
                                flex-shrink: 0;
                                box-shadow:0 1px 3px rgba(0,0,0,.1);">
                            &#127942; Won
                        </span>`
                        : '';
                        const lostIndicator = isLost
                            ? `<span title="Lost Stage"
                                style="font-size:0.68rem;
                                    background:#ffffff;
                                    color:#dc2626;
                                    padding:2px 6px;
                                    border-radius:12px;
                                    font-weight:700;
                                    white-space: nowrap;
                                    flex-shrink: 0;
                                    box-shadow:0 1px 3px rgba(0,0,0,.1);">
                                &#10060; Lost
                            </span>`
                            : '';

                    html += `
                <div class="kanban-column" data-stage-id="${stage.id}" data-stage-name="${stageName}" data-is-won="${isWon ? 1 : 0}" data-is-lost="${isLost ? 1 : 0}">
                    <div class="kanban-header" style="background-color: ${stageColor}; padding: 10px 12px; border-top-left-radius: var(--kbn-border-radius); border-top-right-radius: var(--kbn-border-radius); display: flex; flex-direction: row; align-items: center; justify-content: space-between; height: 50px; flex-shrink: 0;">
                        <div class="d-flex align-items-center gap-1 " style="max-width: 80%;">
                             <span class="stage-title fw-bold text-white fs-10" style="font-size: 0.85rem; text-transform: uppercase;">${stageName}</span>
                             <span class="badge bg-white rounded-circle d-inline-flex align-items-center justify-content-center" style="color: ${stageColor}; width: 22px; height: 22px; font-size: 11px; font-weight: 700; padding: 0; flex-shrink: 0;">${stage.deals.length}</span>
                              ${wonIndicator}
                            ${lostIndicator}
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button class="btn btn-sm text-white toggle-column-filter p-0 d-flex align-items-center justify-content-center rounded-circle" style="background: rgba(255,255,255,0.15); width: 24px; height: 24px; border: none;" title="Filter/Sort"><i class="fas fa-filter fs-10"></i></button>
                            @if(permissions_check('canUpdate'))
                            <div class="dropdown">
                                <button class="btn btn-sm text-white p-0 d-flex align-items-center justify-content-center rounded-circle column-options-btn" style="background: rgba(255,255,255,0.15); width: 24px; height: 24px; border: none;" title="Options" data-stage-id="${stage.id}" data-stage-name="${stageName}" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h fs-10"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 fs-12 py-1" style="min-width:160px;">
                                    <li><a class="dropdown-item edit-stage-name-btn py-2" href="#" data-stage-id="${stage.id}" data-stage-name="${stageName}"><i class="fas fa-pen text-primary me-2 fs-10"></i> Edit Stage Name</a></li>
                                </ul>
                            </div>
                            @else
                            <button class="btn btn-sm text-white p-0 d-flex align-items-center justify-content-center rounded-circle" style="background: rgba(255,255,255,0.15); width: 24px; height: 24px; border: none;" title="Options" disabled><i class="fas fa-ellipsis-h fs-10"></i></button>
                            @endif
                        </div>
                    </div>
                    <div class="column-filter-panel d-none" style="background-color: ${stageColor}; padding: 0 12px 12px 12px;">
                        <input type="text" class="form-control form-control-sm mb-2 col-search-text shadow-sm border-0" placeholder="Search Deal/Company...">
                        <input type="text" class="form-control form-control-sm mb-2 col-date-range shadow-sm border-0" placeholder="Date Range" style="background-color:#fff;">
                        <div class="d-flex gap-2 mb-2">
                            <div class="w-50">
                                <select class="form-select select2-sort col-sort-amount shadow-sm border-0" data-placeholder="Sort by Amount..">
                                    <option value=""></option>
                                    <option value="asc">Amount (Low to High)</option>
                                    <option value="desc">Amount (High to Low)</option>
                                </select>
                            </div>
                            <div class="w-50">
                                <select class="form-select select2-sort col-sort-date shadow-sm border-0" data-placeholder="Sort by Closing..">
                                    <option value=""></option>
                                    <option value="asc">Closing (Old to New)</option>
                                    <option value="desc">Closing (New to Old)</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <button class="btn btn-sm btn-light text-dark col-apply-filter fw-bold shadow-sm px-3">Apply</button>
                            <button class="btn btn-sm btn-light text-dark col-clear-filter fw-bold shadow-sm px-3">Clear</button>
                        </div>
                    </div>
                    <div class="deals-container custom-scrollbar">
                        ${renderDealCards(stage.deals, stageColor, stageName)}
                    </div>
                    <div class="kanban-column-footer d-flex justify-content-between align-items-center px-3 py-2 border-top" style="background-color: #f8fafc; border-bottom-left-radius: var(--kbn-border-radius); border-bottom-right-radius: var(--kbn-border-radius); height: 40px; flex-shrink: 0;">
                        <span class="fw-bold fs-11" style="color: ${stageColor}; text-transform: uppercase; letter-spacing: 0.5px;">Total</span>
                        <span class="fw-bold fs-12 total-amount" data-target="${totalAmount}" style="color: ${stageColor};">₹ 0</span>
                    </div>
                </div>`;
                });

                $(".kanban-columns-container").html(html);
                animateTotalAmounts();
                setupColumnFilters();
            }

            function renderDealCards(deals, stageColor, stageName) {
                if (!deals.length) return `
                    <div class="no-deals text-center py-5 text-muted opacity-50">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                        <p class="m-0 small">No deals</p>
                    </div>
                `;

                return deals.map(deal => {
                    const amountToUse = deal.latest_amount ?? deal.amount;
                    let parsedAmount = parseFloat(amountToUse);
                    if (isNaN(parsedAmount)) parsedAmount = 0;

                    const formattedAmount = parsedAmount.toLocaleString('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    const isClient = deal.deal_status == 1;
                    const isLost = deal.deal_status == 2;
                    const clientClass = isClient ? 'is-client' : '';
                    const lostClass = isLost ? 'is-lost' : '';
                    const borderColor = (isClient || isLost) ? '' : stageColor;

                    const isDraggable = !isLost;

                    // Format closing date to d-m-Y in client-side
                    let formattedClosingDate = '—';
                    let timestamp = 0;
                    if (deal.closing_date) {
                        try {
                            const dateObj = new Date(deal.closing_date);
                            if (!isNaN(dateObj.getTime())) {
                                timestamp = dateObj.getTime();
                                const dd = String(dateObj.getDate()).padStart(2, '0');
                                const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
                                const yyyy = dateObj.getFullYear();
                                formattedClosingDate = `${dd}-${mm}-${yyyy}`;
                            }
                        } catch (e) {
                            formattedClosingDate = deal.closing_date;
                        }
                    }
                    const cleanDealName = (deal.deal_name || '').toString().replace(/"/g, '&quot;').toLowerCase();
                    const cleanCompany = (deal.company_name || '').toString().replace(/"/g, '&quot;').toLowerCase();

                    return `
                    <div class="deal-card ${clientClass} ${lostClass}"
                         style="position: relative; padding: 14px; ${borderColor ? 'border-left-color: ' + borderColor + ';' : ''}"
                         data-lead-id="${deal.lead_id}"
                         data-deal-id="${deal.deal_id}"
                         data-stage-id="${deal.stage_id}"
                         data-amount="${parsedAmount}"
                         data-closing-date="${deal.closing_date || ''}"
                         data-deal-name="${cleanDealName}"
                         data-company-name="${cleanCompany}"
                         data-timestamp="${timestamp}"
                         data-is-direct="${deal.is_direct || 0}"
                         data-iwo-no="${deal.iwo_no || ''}"
                         data-dispatch-date="${deal.dispatch_date || ''}"
                         draggable="${isDraggable ? 'true' : 'false'}">

                         <!-- Dropdown Menu (Absolute Positioned) -->
                         <div class="dropdown" style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                             <button class="btn btn-link text-muted p-2 border-0" style="box-shadow: none; line-height: 1;background: rgba(47, 179, 128, 0.15);" data-bs-toggle="dropdown" aria-expanded="false">
                                 <i class="fas fa-ellipsis-v fs-12 text-dark"></i>
                             </button>
                             <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 fs-12 py-1">
                                 ${(() => {
                                     const stageNameLowerCheck = (stageName || '').toLowerCase().trim();
                                     const isStageWonOrLost = isClient || isLost || stageNameLowerCheck.includes('won') || stageNameLowerCheck.includes('lost') || stageNameLowerCheck.includes('lose') || stageNameLowerCheck.includes('loss') || stageNameLowerCheck === 'close client' || stageNameLowerCheck === 'client';
                                     const showEdit = canUpdateJS && !isStageWonOrLost;

                                     return `
                                         ${showEdit ? `<li><a class="dropdown-item edit-deal-btn py-1" href="#" data-id="${deal.deal_id}"><i class="fas fa-pen text-primary me-2 fs-10"></i> Edit Deal</a></li>` : ''}
                                         <li><a class="dropdown-item py-1" href="/deal/details/${deal.encrypted_id}"><i class="fas fa-eye text-success me-2 fs-10"></i> View Details</a></li>
                                         <li><a class="dropdown-item py-1" href="#" onclick="LeadTimeline.open({ dealId: ${deal.deal_id} })"><i class="fas fa-history text-info me-2 fs-10"></i> View History</a></li>
                                     `;
                                 })()}
                             </ul>
                         </div>

                         <!-- New Deal Tag -->
                         ${deal.is_direct ? `
                         <div class="mb-2">
                             <span class="badge bg-warning-transparent text-warning rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 10px; font-weight: 600; line-height: 1;">
                                 <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background-color: currentColor;"></span>
                                 New Deal
                             </span>
                         </div>
                         ` : ''}

                         <!-- Deal Name -->
                         <div class="fw-bold text-dark mb-2 pe-3" style="font-size: 15px; line-height: 1.3;" title="${deal.deal_name}">
                             <a href="/deal/details/${deal.encrypted_id}" class="text-dark text-decoration-none d-block text-truncate">
                                 ${deal.deal_name}
                             </a>
                         </div>

                         <!-- Customer & Company -->
                         <div class="mb-2" style="font-size: 13px; color: #64748b;">
                             <div class="text-truncate mb-1">
                                 <i class="far fa-user me-2" style="width: 14px;"></i>
                                 <span>${deal.contact_person || '—'}</span>
                             </div>
                             <div class="text-truncate">
                                 <i class="fas fa-building me-2" style="width: 14px;"></i>
                                 <span>${deal.company_name || '—'}</span>
                             </div>
                         </div>

                         <!-- Lead No & Date Badges -->
                         <div class="d-flex gap-2 flex-wrap mb-2">
                             ${!deal.is_direct ? `
                             <span class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded" style="background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 500;">
                                 <i class="fas fa-hashtag" style="font-size: 9px;"></i> ${deal.lead_no || '—'}
                             </span>
                             ` : ''}
                             <span class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded" style="background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 500;">
                                 <i class="far fa-calendar-alt" style="font-size: 9px;"></i> ${formattedClosingDate}
                             </span>
                         </div>

                         <!-- Created By & Assigned To -->
                         <div class="mb-2" style="font-size: 12.5px; color: #64748b;">
                             <div class="text-truncate mb-1">
                                 <i class="far fa-user me-2" style="width: 14px;"></i>
                                 Created By: <span class="fw-semibold text-dark">${deal.created_by_name || 'System'}</span>
                             </div>
                             <div class="text-truncate">
                                 <i class="fas fa-user-tie me-2" style="width: 14px;"></i>
                                 Assigned To: <span class="fw-semibold text-dark">${deal.assigned_to_name || '—'}</span>
                             </div>
                         </div>

                         <!-- Amount -->
                         <div class="mb-2">
                             <span class="fw-bold px-2 py-1 rounded" style="background-color: rgba(${hexToRgb(borderColor || stageColor)}, 0.10); color: ${borderColor || stageColor}; font-size: 13px;">
                                 Rs ${formattedAmount}
                             </span>
                         </div>

                         <!-- Send Quotation Button & PDF Icon Button -->
                         <div class="w-100 pt-1">
                             ${(() => {
                            const stageNameLower2 = (stageName || '').toLowerCase().trim();
                            const isCloseClient = stageNameLower2 === 'close client' || stageNameLower2 === 'closed won' || stageNameLower2 === 'client';
                            const isLoseStage = stageNameLower2 === 'lose' || stageNameLower2 === 'loss' || stageNameLower2 === 'closed lost';
                            const hideBtn = isClient || isLost || isCloseClient || isLoseStage;
                            if (hideBtn) return '';

                            const pdfIconBtn = deal.latest_quotation_id ? `
                                <a href="/quotation/pdf/${deal.latest_quotation_id}" target="_blank"
                                   class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center px-2"
                                   style="height: 33px; border-radius: 6px; border-color: #fecaca; color: #dc2626; background: #fff5f5;"
                                   title="View Latest Quotation PDF">
                                    <i class="fas fa-file-pdf fs-13"></i>
                                </a>
                            ` : '';

                            return `
                            <div class="d-flex align-items-center gap-1 w-100">
                                <button class="btn btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-2 send-quotation-btn"
                                        style="border: 1px solid #e2e8f0; color: #334155; background: #fff; font-size: 12px; padding: 7px; border-radius: 6px; font-weight: 500;"
                                        data-dealid="${deal.encrypted_id}"
                                        data-leadid="${deal.lead_id}">
                                    <i class="far fa-file-alt"></i> Send Quotation
                                    ${deal.quot_count > 0 ? `
                                        <span class="badge rounded-pill bg-danger" style="font-size: 9px; padding: 3px 6px; margin-left: 2px;">
                                            ${deal.quot_count}
                                        </span>
                                    ` : ''}
                                </button>
                                ${pdfIconBtn}
                            </div>`;
                       })()}
                         </div>
                    </div>
                    `;
                }).join('');
            }


            function animateTotalAmounts() {
                $(".total-amount").each(function () {
                    const $this = $(this);
                    const target = parseFloat($this.data("target"));
                    const duration = 1000;
                    const start = performance.now();

                    function animate(time) {
                        const progress = Math.min((time - start) / duration, 1);
                        $this.text("₹" + Math.floor(target * progress).toLocaleString());

                        if (progress < 1) requestAnimationFrame(animate);
                    }

                    requestAnimationFrame(animate);
                });
            }


            function fetchLeadDetails(leadId) {

                ui.showSpinner();

                const url = routes.closedWonDetails.replace(':leadId', leadId);

                api.request(url, "GET", {})
                    .done(res => {
                        if (!res.success) {
                            ui.toast("warning", "Lead not found");
                            return;
                        }

                        const data = res.data;

                        $("#company_name").val(data.company_name);
                        $("#contact_person_name").val(data.contact_person_name || data.contact_person);
                        $("#email").val(data.email);
                        $("#phone").val(data.phone || data.mobile_no);
                        $("#gst_no").val(data.gst_no);
                        $("#deal_address").val(data.address);
                        $('#amount').val(data.amount ?? 0);

                        if (data.city_id) {
                            fetchLocationFromCity(data.city_id);
                        } else {
                            resetLocationFields();
                        }
                    })
                    .fail(() => {
                        ui.toast("error", "Error fetching lead details");
                    })
                    .always(() => {
                        ui.hideSpinner();
                    });
            }


            function fetchLocationFromCity(cityId) {
                api.request(routes.getLocationByCity.replace(':id', cityId), "GET", {})
                    .done(loc => {
                        loc = loc.data;
                        if (loc.city_name) {
                            state.isProgrammaticSelect = true;
                            setSelect2Value('#cust_city', cityId, loc.city_name);

                            if (loc.state_id && loc.state_name) {
                                $('#cust_state').val(loc.state_id);
                                $('#state_name').val(loc.state_name);
                            }

                            if (loc.country_id && loc.country_name) {
                                $('#cust_country').val(loc.country_id);
                                $('#country_name').val(loc.country_name);
                            }
                            state.isProgrammaticSelect = false;
                        }
                    })
                    .fail(() => {
                        ui.toast("error", "Error fetching location details");
                    });
            }

            function resetLocationFields() {
                state.isProgrammaticSelect = true;
                $('#cust_city').val(null).trigger('change');
                $('#cust_state, #cust_country').val('');
                $('#state_name, #country_name').val('');
                state.isProgrammaticSelect = false;
            }


            let pendingStageUpdate = null;

            $('#complainReasonModal').on('hidden.bs.modal', function () {
                if (pendingStageUpdate && !pendingStageUpdate.submitted) {
                    revertDeal(pendingStageUpdate.dealId);
                }
                pendingStageUpdate = null;
                $('#complainReasonInput').val('');
            });

            $('#submitComplainReasonBtn').on('click', function () {
                const reason = $('#complainReasonInput').val().trim();
                if (!reason) {
                    ui.toast('warning', 'Please specify the complaint details.', 'Validation Error');
                    return;
                }
                if (pendingStageUpdate) {
                    pendingStageUpdate.submitted = true;
                    $('#complainReasonModal').modal('hide');
                    updateDealStage(pendingStageUpdate.dealId, pendingStageUpdate.stageId, reason);
                }
            });

            let scrollAnimFrame = null;
            let targetScrollSpeed = 0;

            function autoScrollLoop() {
                if (targetScrollSpeed !== 0) {
                    const container = document.querySelector('.kanban-columns-container');
                    if (container) {
                        container.scrollLeft += targetScrollSpeed;
                    }
                    scrollAnimFrame = requestAnimationFrame(autoScrollLoop);
                } else {
                    scrollAnimFrame = null;
                }
            }

            function updateAutoScrollSpeed(speed) {
                targetScrollSpeed = speed;
                if (speed !== 0 && !scrollAnimFrame) {
                    scrollAnimFrame = requestAnimationFrame(autoScrollLoop);
                }
            }

            function stopAutoScroll() {
                targetScrollSpeed = 0;
                if (scrollAnimFrame) {
                    cancelAnimationFrame(scrollAnimFrame);
                    scrollAnimFrame = null;
                }
            }

            function handleAutoScrollOnDrag(e) {
                if (!state.draggedElement) {
                    stopAutoScroll();
                    return;
                }

                const container = document.querySelector('.kanban-columns-container');
                if (!container) return;

                const rect = container.getBoundingClientRect();
                const x = e.originalEvent ? e.originalEvent.clientX : e.clientX;
                const threshold = 150;

                if (!x) return;

                if (x > rect.right - threshold && x < rect.right + 60) {
                    const ratio = Math.min(1, Math.max(0, (x - (rect.right - threshold)) / threshold));
                    const speed = Math.max(2, Math.min(22, Math.round(ratio * ratio * 22)));
                    updateAutoScrollSpeed(speed);
                } else if (x < rect.left + threshold && x > rect.left - 60) {
                    const ratio = Math.min(1, Math.max(0, ((rect.left + threshold) - x) / threshold));
                    const speed = Math.max(2, Math.min(22, Math.round(ratio * ratio * 22)));
                    updateAutoScrollSpeed(-speed);
                } else {
                    updateAutoScrollSpeed(0);
                }
            }

            $(document).on('dragover', function (e) {
                handleAutoScrollOnDrag(e);
            });

            $(document).on('dragend drop', function () {
                stopAutoScroll();
            });

            function initDragAndDrop() {

                $(".deal-card").on("dragstart", function (e) {
                    if ($(this).attr('draggable') === 'false') {
                        e.preventDefault();
                        return false;
                    }

                    state.draggedDealId = $(this).data("deal-id");
                    state.draggedLeadId = $(this).data("lead-id");
                    state.originalStage = $(this).closest(".kanban-column").data("stage-id");
                    state.draggedElement = $(this);

                    e.originalEvent.dataTransfer.setData("deal", state.draggedDealId);
                    $(this).addClass("dragging");
                });

                $(".deal-card").on("dragend", function () {
                    $(this).removeClass("dragging");
                    stopAutoScroll();
                });

                $(".kanban-column").on("dragover", function (e) {
                    e.preventDefault();
                    $(this).addClass("drop-zone");
                });

                $(".kanban-column").on("dragleave", function () {
                    $(this).removeClass("drop-zone");
                });

                $(".kanban-column").on("drop", function (e) {
                    e.preventDefault();
                    $(this).removeClass("drop-zone");

                    const dealId = state.draggedDealId;
                    const leadId = state.draggedLeadId;
                    const newStageId = $(this).data("stage-id");
                    const stageName = $(this).data("stage-name");

                    const origCol = $(`.kanban-column[data-stage-id="${state.originalStage}"]`);
                    const isOriginalWon = origCol.data('is-won') == 1;
                    const isOriginalComplain = (origCol.data('stage-name') || '').toLowerCase().trim() === 'complain';

                    const isNewWon = $(this).data('is-won') == 1;
                    const isNewLost = $(this).data('is-lost') == 1;
                    const isNewComplain = (stageName || '').toLowerCase().trim() === 'complain';

                    if (isOriginalWon && !isNewComplain) {
                        ui.toast('warning', 'Deals in Won stage can only be moved to Complain.', 'Restriction');
                        revertDeal(dealId);
                        return;
                    }

                    if (isOriginalComplain && !isNewWon) {
                        ui.toast('warning', 'Deals in Complain stage can only be moved to a Won stage.', 'Restriction');
                        revertDeal(dealId);
                        return;
                    }

                    if (isNewComplain && !isOriginalWon) {
                        ui.toast('warning', 'Only deals in Won stage can be moved to Complain.', 'Restriction');
                        revertDeal(dealId);
                        return;
                    }

                    // Won -> Complain: Ask for Complain Reason
                    if (isOriginalWon && isNewComplain) {
                        pendingStageUpdate = {
                            dealId: dealId,
                            stageId: newStageId,
                            submitted: false
                        };
                        $('#complainReasonModal').modal('show');
                        return;
                    }

                    // Complain -> Won: Confirm via iziToast.question
                    if (isOriginalComplain && isNewWon) {
                        iziToast.question({
                            timeout: 20000,
                            close: false,
                            overlay: true,
                            displayMode: 'once',
                            id: 'question',
                            zindex: 9999,
                            title: 'Confirm stage change',
                            message: 'Has the query/complaint been resolved and do you want to change from Complain to Won?',
                            position: 'center',
                            buttons: [
                                ['<button><b>YES</b></button>', function (instance, toast) {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                    // Move to Won stage directly without opening Verify Details modal
                                    updateDealStage(dealId, newStageId);
                                }, true],
                                ['<button>NO</button>', function (instance, toast) {
                                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                    revertDeal(dealId);
                                }]
                            ]
                        });
                        return;
                    }

                    $(this).find(".deals-container").prepend(state.draggedElement);

                    if (isNewWon) openClosedWonModal(dealId, leadId, newStageId);
                    else if (isNewLost) openClosedLostModal(dealId, leadId, newStageId);
                    else updateDealStage(dealId, newStageId);
                });
            }


            function updateDealStage(dealId, stageId, reason = '') {

                ui.showSpinner();

                const url = routes.updateStage.replace(':dealId', dealId);
                const data = {
                    stage_id: stageId,
                    reason: reason
                };

                api.request(url, "POST", data)
                    .done(res => {
                        if (res.success) {
                            ui.toast("success", res.message, "Success");
                            refreshCurrentView();
                        } else {
                            ui.toast("error", res.message || "Failed to update stage");
                            revertDeal(dealId);
                        }
                    })
                    .fail(xhr => {
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            Object.values(xhr.responseJSON.errors).forEach(msgArr => {
                                ui.toast("error", msgArr[0], "Validation Error");
                            });
                        } else {
                            ui.toast("error", "Error updating stage", "Error");
                        }

                        revertDeal(dealId);
                    })
                    .always(() => {
                        ui.hideSpinner();
                    });
            }

            // ---- Edit Stage Name ----
            $(document).on('click', '.edit-stage-name-btn', function (e) {
                e.preventDefault();
                const stageId   = $(this).data('stage-id');
                const stageName = $(this).data('stage-name');
                $('#editStageId').val(stageId);
                $('#editStageName').val(stageName).removeClass('is-invalid');
                $('#editStageNameError').text('');
                $('#editStageNameModal').modal('show');
            });

            $('#saveStageNameBtn').on('click', function () {
                const $btn      = $(this);
                const stageId   = $('#editStageId').val();
                const stageName = $('#editStageName').val().trim();

                $('#editStageName').removeClass('is-invalid');
                $('#editStageNameError').text('');

                if (!stageName) {
                    $('#editStageName').addClass('is-invalid');
                    $('#editStageNameError').text('Stage name is required.');
                    return;
                }

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');

                $.ajax({
                    url: routes.updateStageName,
                    type: 'POST',
                    data: { id: stageId, stage_name: stageName },
                    headers: { 'X-CSRF-TOKEN': state.csrfToken }
                }).done(function (res) {
                    if (res.status === 'success') {
                        ui.toast('success', res.message, 'Success');
                        // Update the column header live
                        const $col = $(`.kanban-column[data-stage-id="${stageId}"]`);
                        $col.attr('data-stage-name', res.stage_name);
                        $col.find('.stage-title').text(res.stage_name);
                        $col.find('.edit-stage-name-btn').data('stage-name', res.stage_name);
                        $col.find('.column-options-btn').data('stage-name', res.stage_name);
                        $('#editStageNameModal').modal('hide');
                    } else {
                        $('#editStageName').addClass('is-invalid');
                        $('#editStageNameError').text(res.message || 'Failed to update stage name.');
                    }
                }).fail(function (xhr) {
                    const msg = xhr.responseJSON?.message || 'An error occurred.';
                    $('#editStageName').addClass('is-invalid');
                    $('#editStageNameError').text(msg);
                }).always(function () {
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save');
                });
            });

            // Reset modal state on hide
            $('#editStageNameModal').on('hidden.bs.modal', function () {
                $('#editStageName').removeClass('is-invalid');
                $('#editStageNameError').text('');
            });

            function openClosedWonModal(dealId, leadId, newStageId) {
                const $dealCard = $(`.deal-card[data-deal-id="${state.draggedDealId}"]`);
                $dealCard.attr('draggable', 'false');
                $('#ConvertDealModal').modal('show');
                $('#deal_id_input').val(dealId);
                $('#lead_id_input').val(leadId);
                $('#stage_id_input').val(newStageId);
                $('#amount').val($dealCard.data('amount'));

                // Pre-fill IWO No
                const iwoNo = $dealCard.data('iwo-no') || '';
                $('#closed_won_iwo_no').val(iwoNo);

                // Pre-fill Dispatch Date
                const dispatchDate = $dealCard.data('dispatch-date') || '';
                if (dispatchDate) {
                    let formattedDispatchDate = '';
                    try {
                        const dateObj = new Date(dispatchDate);
                        if (!isNaN(dateObj.getTime())) {
                            const day = String(dateObj.getDate()).padStart(2, '0');
                            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                            const year = dateObj.getFullYear();
                            formattedDispatchDate = `${day}-${month}-${year}`;
                        }
                    } catch (e) {
                        formattedDispatchDate = dispatchDate;
                    }
                    $('#closed_won_dispatch_date').val(formattedDispatchDate);
                } else {
                    $('#closed_won_dispatch_date').val('');
                }

                // Init flatpickr for closed_won_dispatch_date
                flatpickr('#closed_won_dispatch_date', {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    disableMobile: true,
                    defaultDate: $('#closed_won_dispatch_date').val() ? $('#closed_won_dispatch_date').val() : ''
                });

                const closingDate = $dealCard.data('closing-date');
                if (closingDate) {
                    // Parse any date format (ISO, d-m-Y, d/m/Y) → D-M-Y for display
                    let formattedDate = '';
                    try {
                        const dateObj = new Date(closingDate);
                        if (!isNaN(dateObj.getTime())) {
                            const day = dateObj.getDate();
                            const month = dateObj.getMonth() + 1;
                            const year = dateObj.getFullYear();
                            formattedDate = `${day}-${month}-${year}`;
                        }
                    } catch (e) {
                        formattedDate = '';
                    }
                    $('#closing_date_input').val(formattedDate);
                    fetchLeadDetails(leadId);
                } else {
                    $('#closing_date_input').val('');
                    fetchLeadDetails(leadId);
                }
                $('#stage').val('Closed Won');
            }

            function openClosedLostModal(dealId, leadId, newStageId) {
                const $dealCard = $(`.deal-card[data-deal-id="${state.draggedDealId}"]`);

                $('#ConvertDealLostModal').modal('show');
                $('#deal_lost_id_input').val(dealId);
                $('#lead_lost_id_input').val(leadId);
                $('#stage_lost_id_input').val(newStageId);
                $('#amount_lost').val($dealCard.data('amount'));
                const closingDate = $dealCard.data('closing-date');
                if (closingDate) {
                    // Parse any date format (ISO, d-m-Y, d/m/Y) → D-M-Y for display
                    let formattedDate = '';
                    try {
                        const dateObj = new Date(closingDate);
                        if (!isNaN(dateObj.getTime())) {
                            const day = dateObj.getDate();
                            const month = dateObj.getMonth() + 1;
                            const year = dateObj.getFullYear();
                            formattedDate = `${day}-${month}-${year}`;
                        }
                    } catch (e) {
                        formattedDate = '';
                    }
                    $('#closing_date_lost_input').val(formattedDate);
                } else {
                    $('#closing_date_lost_input').val('');
                }
                $('#stage_lost').val('Closed Lost');
            }


            function refreshCurrentView() {
                loadKanbanData();
            }

            function submitClosedWonForm() {
                const form = $('#dealClosedWon');

                if (!form[0].checkValidity()) {
                    form.addClass('was-validated');
                    return;
                }

                const submitBtn = form.find('button[type="submit"]');

                ui.toggleButton(submitBtn, true, "Processing...");
                ui.showSpinner();

                api.request(routes.closedWon, "POST", form.serialize())
                    .done(res => {
                        if (res.success) {
                            ui.toast("success", res.message || "Deal marked as Closed Won", "Success");
                            $('#ConvertDealModal').modal('hide');
                            refreshCurrentView();
                        } else {
                            ui.toast("error", res.message || "Something went wrong");
                            revertDeal();
                        }
                    })
                    .fail(xhr => {
                        let msg = "An error occurred while updating.";

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join("<br>");
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }

                        ui.toast("error", msg, "Error");
                        revertDeal();
                    })
                    .always(() => {
                        ui.toggleButton(submitBtn, false);
                        ui.hideSpinner();
                    });
            }

            $('#cancelConvertDeal').on('click', function (e) {
                e.preventDefault();
                $('#ConvertDealModal').modal('hide');
                revertDeal();
            });

            $('#cancelConvertDealLost').on('click', function (e) {
                e.preventDefault();
                $('#ConvertDealLostModal').modal('hide');

                revertDeal();
            });

            $('#ConvertDealModal').on('hidden.bs.modal', function () {
                $('#dealClosedWon').removeClass('was-validated');
                if (!state.isFormSubmitted) {
                    revertDeal();
                }
                state.isFormSubmitted = false;
            });

            $('#ConvertDealLostModal').on('hidden.bs.modal', function () {
                $('#dealClosedLost').removeClass('was-validated');
                if (!state.isFormSubmitted) {
                    revertDeal();
                }
                state.isFormSubmitted = false;
            });

            $('#dealClosedWon').on('submit', function (e) {
                e.preventDefault();
                state.isFormSubmitted = true;
                submitClosedWonForm();
            });

            $('#dealClosedLost').on('submit', function (e) {
                e.preventDefault();
                state.isFormSubmitted = true;
                submitClosedLostForm();
            });

            function submitClosedLostForm() {
                const form = $('#dealClosedLost');

                if (!form[0].checkValidity()) {
                    form.addClass('was-validated');
                    return;
                }

                const submitBtn = form.find('button[type="submit"]');

                ui.toggleButton(submitBtn, true, "Processing...");
                ui.showSpinner();

                api.request(routes.closedLost, "POST", form.serialize())
                    .done(res => {
                        if (res.success) {
                            ui.toast("success", res.message || "Deal marked as Closed Lost", "Success");
                            $('#ConvertDealLostModal').modal('hide');
                            refreshCurrentView();
                        } else {
                            ui.toast("error", res.message || "Something went wrong");
                            revertDeal();
                        }
                    })
                    .fail(xhr => {
                        let msg = "An error occurred while updating.";

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join("<br>");
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }

                        ui.toast("error", msg, "Error");
                        revertDeal();
                    })
                    .always(() => {
                        ui.toggleButton(submitBtn, false);
                        ui.hideSpinner();
                    });
            }

            function revertDeal(dealId) {
                const $dealCard = $(`.deal-card[data-deal-id="${state.draggedDealId}"]`) || dealId;
                const $originalColumn = $(
                    `.kanban-column[data-stage-id="${state.originalStage}"] .deals-container`);
                $originalColumn.prepend($dealCard);
            }

            initializeComponents();
            loadKanbanData();

            function setupColumnFilters() {
                $('.select2-sort').select2({
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });
                $('.col-date-range').flatpickr({
                    mode: "range",
                    dateFormat: "d/M/Y",
                    allowInput: true
                });
            }

            $(document).on('click', '.toggle-column-filter', function () {
                $(this).closest('.kanban-column').find('.column-filter-panel').toggleClass('d-none');
            });

            $(document).on('click', '.col-apply-filter', function () {
                const $col = $(this).closest('.kanban-column');
                const searchText = $col.find('.col-search-text').val().toLowerCase().trim();
                const dateRange = $col.find('.col-date-range').val();
                const sortAmount = $col.find('.col-sort-amount').val();
                const sortDate = $col.find('.col-sort-date').val();

                let minTime = 0, maxTime = Infinity;
                if (dateRange && dateRange.includes(' to ')) {
                    const dates = dateRange.split(' to ');
                    minTime = parseKanbanDate(dates[0]);
                    maxTime = parseKanbanDate(dates[1]);
                } else if (dateRange) {
                    minTime = maxTime = parseKanbanDate(dateRange);
                }

                const $container = $col.find('.deals-container');
                const $cards = $container.find('.deal-card');

                // 1. Apply Search and Date Filter
                $cards.each(function () {
                    const $card = $(this);
                    const dName = $card.data('deal-name') || '';
                    const cName = $card.data('company-name') || '';
                    const time = parseInt($card.data('timestamp')) || 0;

                    let matchSearch = !searchText || dName.includes(searchText) || cName.includes(searchText);
                    let matchDate = !dateRange || (time >= minTime && time <= maxTime);

                    if (matchSearch && matchDate) {
                        $card.removeClass('d-none');
                    } else {
                        $card.addClass('d-none');
                    }
                });

                // 2. Apply Sorting
                if (sortAmount || sortDate) {
                    const cardsArray = $cards.toArray();
                    cardsArray.sort(function (a, b) {
                        let res = 0;
                        if (sortAmount) {
                            const amtA = parseFloat($(a).data('amount')) || 0;
                            const amtB = parseFloat($(b).data('amount')) || 0;
                            res = sortAmount === 'asc' ? amtA - amtB : amtB - amtA;
                        }
                        if (res === 0 && sortDate) {
                            const timeA = parseInt($(a).data('timestamp')) || 0;
                            const timeB = parseInt($(b).data('timestamp')) || 0;
                            res = sortDate === 'asc' ? timeA - timeB : timeB - timeA;
                        }
                        return res;
                    });

                    // Re-append sorted cards
                    $.each(cardsArray, function (index, card) {
                        $container.append(card);
                    });
                }
            });

            $(document).on('click', '.col-clear-filter', function () {
                const $col = $(this).closest('.kanban-column');
                $col.find('.col-search-text').val('');
                if ($col.find('.col-date-range')[0]._flatpickr) {
                    $col.find('.col-date-range')[0]._flatpickr.clear();
                }
                $col.find('.col-sort-amount').val('').trigger('change');
                $col.find('.col-sort-date').val('').trigger('change');

                const $container = $col.find('.deals-container');
                const $cards = $container.find('.deal-card');

                // Show all and revert to default ID sort (newest top)
                $cards.removeClass('d-none');
                const cardsArray = $cards.toArray();
                cardsArray.sort(function (a, b) {
                    const idA = parseInt($(a).data('deal-id')) || 0;
                    const idB = parseInt($(b).data('deal-id')) || 0;
                    return idB - idA;
                });
                $.each(cardsArray, function (index, card) {
                    $container.append(card);
                });
            });

            function parseKanbanDate(dateStr) {
                const parts = dateStr.trim().split('/');
                if (parts.length === 3) {
                    const m = { Jan: 0, Feb: 1, Mar: 2, Apr: 3, May: 4, Jun: 5, Jul: 6, Aug: 7, Sep: 8, Oct: 9, Nov: 10, Dec: 11 }[parts[1]] || 0;
                    return new Date(parts[2], m, parts[0]).getTime();
                }
                return 0;
            }

            // ==========================================
            // NEW DEAL MODAL & FILTER LOGIC
            // ==========================================

            // 1. Add missing hidden customer_id input to the modal form
            $('#createDirectDealForm').append('<input type="hidden" name="customer_id" id="nd_customer_id_hidden">');

            // 2. Initialize Flatpickr for Direct Deal closing date
            flatpickr("#nd_closing_date", {
                dateFormat: "d-m-Y",
                allowInput: false,
                minDate: "today"
            });

            // 3. Tab Switching Logic for Company selection
            let ndCompanyMode = 'existing'; // 'existing' or 'new'

            // Listen to Bootstrap btn-check radio button changes for tabs toggling
            $('input[name="nd_customer_type"]').on('change', function () {
                $('#nd_deal_type').val(null).trigger('change.select2');
                if ($('#nd_tab_existing').is(':checked')) {
                    ndCompanyMode = 'existing';
                    $('#nd_existing_wrap').removeClass('d-none');
                    $('#nd_new_wrap, #nd_location_row').addClass('d-none');
                    $('#nd_company_name_new').val('');
                    $('#nd_city_id').val(null).trigger('change');
                    $('#nd_state_id, #nd_state_name, #nd_country_id, #nd_country_name').val('');
                } else {
                    ndCompanyMode = 'new';
                    $('#nd_new_wrap, #nd_location_row').removeClass('d-none');
                    $('#nd_existing_wrap').addClass('d-none');
                    $('#nd_customer_select').val(null).trigger('change');
                    $('#nd_customer_id_hidden').val('');
                }
            });

            // 4. Initialize AJAX Select2 dropdowns when the modal is fully shown
            $('#createDirectDealModal').on('shown.bs.modal', function () {
                // Destroy previous instances if any to ensure clean width calculation
                ['#nd_customer_select', '#nd_city_id', '#nd_deal_type', '#nd_deal_stage', '#nd_deal_name', '#nd_assigned_to'].forEach(function(selector) {
                    if ($(selector).data('select2')) {
                        $(selector).select2('destroy');
                    }
                });

                // Initialize AJAX Select2 for existing customers
                $('#nd_customer_select').select2({
                    dropdownParent: $('#createDirectDealModal'),
                    placeholder: 'Select Existing Customer',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: routes.customersList,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { search: params.term, page: params.page || 1 };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: $.map(data.results, function (obj) {
                                    return {
                                        id: obj.id,
                                        text: obj.text,
                                        contact_person: obj.contact_person,
                                        phone: obj.phone,
                                        email: obj.email,
                                        gst_no: obj.gst_no
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });

                // Initialize Select2 for City dropdown in new mode
                $('#nd_city_id').select2({
                    dropdownParent: $('#createDirectDealModal'),
                    placeholder: 'Search City...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: "{{ url('location/cities') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { search: params.term, page: params.page || 1 };
                        },
                        processResults: function (data) {
                            return {
                                results: $.map(data.data || [], function (obj) {
                                    return { id: obj.city_id, text: obj.city_name };
                                })
                            };
                        },
                        cache: true
                    }
                });

                // Initialize Select2 for Deal Type dropdown
                if ($('#nd_deal_type').is('select')) {
                    $('#nd_deal_type').select2({
                        dropdownParent: $('#createDirectDealModal'),
                        placeholder: 'Select Deal Type',
                        allowClear: true,
                        width: '100%'
                    });
                }

                // Initialize Select2 for Stage dropdown
                $('#nd_deal_stage').select2({
                    dropdownParent: $('#createDirectDealModal'),
                    placeholder: 'Select Stage',
                    width: '100%'
                });

                // Initialize Select2 for Deal Name (Product) dropdown
                $('#nd_deal_name').select2({
                    dropdownParent: $('#createDirectDealModal'),
                    placeholder: 'Select Product',
                    allowClear: true,
                    width: '100%'
                });

                flatpickr("#nd_dispatch_date", {
                    dateFormat: "d-m-Y",
                    allowInput: true,
                    disableMobile: true,
                });

                // Initialize Select2 for Assigned Staff dropdown
                $('#nd_assigned_to').select2({
                    dropdownParent: $('#createDirectDealModal'),
                    placeholder: 'Select Staff',
                    allowClear: true,
                    width: '100%'
                });
            });

            $(document).on('change', '#nd_deal_name', function () {
                let totalAmount = 0;
                const selectedVals = $(this).val() || [];
                $('#nd_deal_name option').each(function () {
                    if (selectedVals.indexOf($(this).val()) !== -1) {
                        const price = parseFloat($(this).attr('data-price')) || 0;
                        totalAmount += price;
                    }
                });
                $('#nd_amount').val(totalAmount > 0 ? totalAmount.toFixed(2) : '0.00');
            });

            // Destroy Select2 dropdowns when modal is hidden to avoid duplicate container bindings
            $('#createDirectDealModal').on('hidden.bs.modal', function () {
                if ($('#nd_city_id').data('select2')) {
                    $('#nd_city_id').select2('destroy');
                }
                if ($('#nd_deal_type').data('select2')) {
                    $('#nd_deal_type').select2('destroy');
                }
                if ($('#nd_customer_select').data('select2')) {
                    $('#nd_customer_select').select2('destroy');
                }
                if ($('#nd_deal_stage').data('select2')) {
                    $('#nd_deal_stage').select2('destroy');
                }
                if ($('#nd_deal_name').data('select2')) {
                    $('#nd_deal_name').select2('destroy');
                }
                if ($('#nd_assigned_to').data('select2')) {
                    $('#nd_assigned_to').select2('destroy');
                }
                $('#nd_iwo_no').val('');
    $('#nd_dispatch_date').val('');
            });

            // City change handler for new customer mode
            $('#nd_city_id').on('change', function () {
                const cityId = $(this).val();
                if (!cityId) {
                    $('#nd_state_id, #nd_state_name, #nd_country_id, #nd_country_name').val('');
                    $('#nd_deal_type').val(null).trigger('change.select2');
                    return;
                }
                $.get("{{ url('location/city') }}/" + cityId, function (res) {
                    if (res && res.data) {
                        const d = res.data;
                        $('#nd_state_id').val(d.state_id || '');
                        $('#nd_state_name').val(d.state_name || '');
                        $('#nd_country_id').val(d.country_id || d.c_id || '');
                        $('#nd_country_name').val(d.country_name || '');

                        const countryId = parseInt(d.country_id || d.c_id);
                        const isIndia = (countryId === 99 || (d.country_name && d.country_name.toLowerCase() === 'india'));
                        const dealType = isIndia ? 'Domestic' : 'Export';
                        $('#nd_deal_type').val(dealType).trigger('change');
                    }
                });
            });

            // Autofill contact person, phone, email, gst number & auto-select deal type on selecting existing customer
            $('#nd_customer_select').on('change', function () {
                if (!$(this).data('select2')) return;
                const data = $(this).select2('data')[0];
                if (data && data.id) {
                    $('#nd_company_name_hidden').val(data.text);
                    $('#nd_customer_id_hidden').val(data.id);
                    $('#nd_contact_person').val(data.contact_person || '');
                    $('#nd_phone').val(data.phone || '');
                    $('#nd_email').val(data.email || '');
                    $('#nd_gst_no').val(data.gst_no || '');

                    $.get("{{ url('get-data/customer') }}/" + data.id + "/details", function (res) {
                        if (res && res.customer) {
                            let countryId = res.country ? (res.country.c_id || res.country.country_id) : res.customer.country_id;
                            let countryName = res.country ? res.country.country_name : '';
                            if (countryId || countryName) {
                                const isIndia = (parseInt(countryId) === 99 || (countryName && countryName.toLowerCase() === 'india'));
                                const dealType = isIndia ? 'Domestic' : 'Export';
                                $('#nd_deal_type').val(dealType).trigger('change');
                            } else {
                                $('#nd_deal_type').val(null).trigger('change.select2');
                            }
                        }
                    });
                } else {
                    $('#nd_company_name_hidden, #nd_customer_id_hidden, #nd_contact_person, #nd_phone, #nd_email, #nd_gst_no').val('');
                    $('#nd_deal_type').val(null).trigger('change.select2');
                }
            });

            let allStaffList = [];
            function captureStaffList() {
                if (allStaffList.length === 0) {
                    $('#nd_assigned_to option').each(function () {
                        if ($(this).val() !== '') {
                            allStaffList.push({
                                id: $(this).val(),
                                name: $(this).text().trim(),
                                type: $(this).data('type') || ''
                            });
                        }
                    });
                }
            }

            function filterDirectDealStaff(typeVal) {
                captureStaffList();
                const currentVal = $('#nd_assigned_to').val();
                let html = '<option value="">Select Staff</option>';

                allStaffList.forEach(function (staff) {
                    const staffType = staff.type ? staff.type.toLowerCase() : '';
                    const selectedType = typeVal ? typeVal.toLowerCase() : '';

                    if (!selectedType || !staffType || staffType === 'both' || staffType === selectedType) {
                        const isSel = (staff.id == currentVal) ? 'selected' : '';
                        html += `<option value="${staff.id}" data-type="${staff.type}" ${isSel}>${staff.name}</option>`;
                    }
                });

                $('#nd_assigned_to').html(html);
                if ($('#nd_assigned_to').data('select2')) {
                    $('#nd_assigned_to').trigger('change.select2');
                }
            }

            $('#nd_deal_type').on('change', function () {
                filterDirectDealStaff($(this).val());
            });

            // 5. Open Modal Event
            $('#newDealBtn').on('click', function () {
                $('#createDirectDealForm')[0].reset();
                $('#nd_deal_type').val(null).trigger('change.select2');
                $('#nd_city_id').val(null).trigger('change');
                $('#nd_state_id, #nd_state_name, #nd_country_id, #nd_country_name').val('');
                $('#nd_deal_name').val(null).trigger('change');
                $('#nd_customer_select').val(null).trigger('change');
                $('#nd_customer_id_hidden').val('');
                $('#nd_company_name_hidden').val('');
                $('#nd_iwo_no').val('');
                 $('#nd_dispatch_date').val('');
                $('#createDirectDealForm').removeClass('was-validated');

                // Reset radio button to default "Existing Customer"
                $('#nd_tab_existing').prop('checked', true).trigger('change');

                filterDirectDealStaff('');

                const loggedInStaffId = "{{ session('staff_id') }}";
                if (loggedInStaffId) {
                    $('#nd_assigned_to').val(loggedInStaffId).trigger('change');
                } else {
                    $('#nd_assigned_to').val(null).trigger('change');
                }

                $('#createDirectDealModal').modal('show');
            });

            // 6. Submit Direct Deal Form with explicit field-by-field iziToast validation messages
            $('#createDirectDealForm').on('submit', function (e) {
                e.preventDefault();
                const form = $(this);

                const productIds = $('#nd_deal_name').val();
                if (!productIds || productIds.length === 0) {
                    ui.toast('warning', 'Please select at least one product.', 'Validation Error');
                    return;
                }

                // Company Name Validation
                let companyName = '';
                if (ndCompanyMode === 'existing') {
                    const customerId = $('#nd_customer_select').val();
                    if (!customerId) {
                        ui.toast('warning', 'Please select an existing customer.', 'Validation Error');
                        return;
                    }
                    companyName = $('#nd_company_name_hidden').val();
                } else {
                    companyName = $('#nd_company_name_new').val().trim();
                    if (!companyName) {
                        ui.toast('warning', 'Company Name is required.', 'Validation Error');
                        return;
                    }
                    $('#nd_company_name_hidden').val(companyName);
                    const cityId = $('#nd_city_id').val();
                    if (!cityId) {
                        ui.toast('warning', 'City is required.', 'Validation Error');
                        return;
                    }
                }

                const dealType = $('#nd_deal_type').val();
                if (!dealType) {
                    ui.toast('warning', 'Deal Type is required.', 'Validation Error');
                    return;
                }

                const email = $('#nd_email').val().trim();
                if (!email) {
                    ui.toast('warning', 'Email Address is required.', 'Validation Error');
                    return;
                }
                const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailReg.test(email)) {
                    ui.toast('warning', 'Please enter a valid email address.', 'Validation Error');
                    return;
                }

                const dealStage = $('#nd_deal_stage').val();
                if (!dealStage) {
                    ui.toast('warning', 'Deal Stage is required.', 'Validation Error');
                    return;
                }

                const amount = $('#nd_amount').val().trim();
                if (!amount) {
                    ui.toast('warning', 'Deal Amount is required.', 'Validation Error');
                    return;
                }
                if (parseFloat(amount) < 0 || isNaN(parseFloat(amount))) {
                    ui.toast('warning', 'Please enter a valid deal amount.', 'Validation Error');
                    return;
                }

                const closingDate = $('#nd_closing_date').val().trim();
                if (!closingDate) {
                    ui.toast('warning', 'Closing Date is required.', 'Validation Error');
                    return;
                }

                const gstNo = $('#nd_gst_no').val().trim();
                if (gstNo) {
                    const gstReg = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/i;
                    if (!gstReg.test(gstNo)) {
                        ui.toast('warning', 'Please enter a valid 15-character GST number.', 'Validation Error');
                        return;
                    }
                }

                const submitBtn = $('#saveDealBtn');
                ui.toggleButton(submitBtn, true, 'Saving...');
                ui.showSpinner();

                const formData = form.serialize();

                api.request(routes.createDirect, 'POST', formData)
                    .done(res => {
                        if (res.success) {
                            ui.toast('success', res.message || 'Deal created successfully', 'Success');
                            $('#createDirectDealModal').modal('hide');
                            loadKanbanData();
                        } else {
                            ui.toast('error', res.message || 'Failed to create deal');
                        }
                    })
                    .fail(xhr => {
                        let msg = 'An error occurred while creating the deal.';
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }
                        ui.toast('error', msg, 'Error');
                    })
                    .always(() => {
                        ui.toggleButton(submitBtn, false);
                        ui.hideSpinner();
                    });
            });

            // 7. Toggle Filter "New Deal" (Direct Deals only)
            $('#filterNewDealBtn').on('click', function () {
                state.filterNewDeal = !state.filterNewDeal;

                if (state.filterNewDeal) {
                    $(this).css({
                        'background': '#ffedd5',
                        'color': '#ea580c',
                        'border-color': '#f97316'
                    });
                } else {
                    $(this).css({
                        'background': '#fff7ed',
                        'color': '#c2410c',
                        'border-color': '#fdba74'
                    });
                }

                loadKanbanData();
            });

            // Automatically open Create Deal Modal if action=new or tab=new is in URL query parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'new' || urlParams.get('tab') === 'new') {
                const newDealBtn = $('#newDealBtn');
                if (newDealBtn.length) {
                    newDealBtn.click();
                }
            }

        });
    </script>

</body>

</html>