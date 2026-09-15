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
        .select2-container--default .select2-selection--single {
            position: relative !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-right: 42px !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear {
            position: absolute !important;
            right: 24px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            float: none !important;
            margin: 0 !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            line-height: 1 !important;
            z-index: 4 !important;
            cursor: pointer !important;
            transition: color 150ms ease !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear:hover {
            color: #ef4444 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            right: 8px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
        }
    </style>
</head>
@php
    $cityId = $lead->city_id ?? ($deal->customer->city_id ?? '');
    $cityName = $lead->city->city_name ?? '';
    if (empty($cityName) && !empty($cityId)) {
        $cityName = DB::table('mst_city')->where('city_id', $cityId)->value('city_name') ?: '';
    }

    $stateId = $lead->state_id ?? ($deal->customer->state_id ?? '');
    $stateName = $lead->state->state_name ?? '';
    if (empty($stateName) && !empty($stateId)) {
        $stateName = DB::table('mst_state')->where('state_id', $stateId)->value('state_name') ?: '';
    }

    $countryId = $lead->countries_id ?? ($deal->customer->country_id ?? '');
    $countryName = $lead->country->country_name ?? '';
    if (empty($countryName) && !empty($countryId)) {
        $countryName = DB::table('mst_country')->where('c_id', $countryId)->value('country_name') ?: '';
    }
@endphp
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
                                    @if (permissions_check('canView'))
                                        <a href="{{ route('quotation.index') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-list me-1"></i> List
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="tab-content">
                                    @if (true)
                                        <div class="tab-pane fade show active p-4" id="quot-create">
                                            {{-- Quotation Details Section --}}
                                            <div class="card custom-card border mb-4">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="card-title text-dark mb-0 fw-bold fs-13"><i
                                                            class="fa-solid fa-file-invoice me-2 text-primary"></i>Quotation
                                                        Details</h6>
                                                </div>
                                                <div class="card-body p-3">
                                                    <div class="row g-3">
                                                        <div class="col-12 col-md-4 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Quotation
                                                                Person <span class="text-danger">*</span></label>
                                                            <input type="text" name="quotation_person" id="quotation_person"
                                                                class="form-control form-control-sm"
                                                                value="{{ $lead->contact_person_name ?? '' }}">
                                                        </div>
                                                        <div class="col-12 col-md-4 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Customer
                                                                Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="customer_name" id="customer_name"
                                                                class="form-control form-control-sm"
                                                                value="{{ $lead->company_name ?? '' }}">
                                                        </div>
                                                        <div class="col-6 col-md-4 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">GST
                                                                No</label>
                                                            <input type="text" name="gst_no" id="gst_no"
                                                                class="form-control form-control-sm text-uppercase"
                                                                value="{{ $deal->gst_no ?? '' }}">
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">City
                                                                <span class="text-danger">*</span></label>
                                                            <select name="city_id" id="city_id"
                                                                class="form-select form-select-sm select2" data-placeholder="Search City...">
                                                                <option value="">Search City...</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">State
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text" id="state_name"
                                                                class="form-control form-control-sm bg-light"
                                                                value="{{ $stateName }}" readonly>
                                                            <input type="hidden" name="state_id" id="state_id"
                                                                value="{{ $stateId }}">
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Country
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text" id="country_name"
                                                                class="form-control form-control-sm bg-light"
                                                                value="{{ $countryName }}" readonly>
                                                            <input type="hidden" name="country_id" id="country_id"
                                                                value="{{ $countryId }}">
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Quotation
                                                                Type <span class="text-danger">*</span></label>
                                                             <select class="form-select form-select-sm select2" id="quotation_type"
                                                                 name="quotation_type" required>
                                                                 <option value=""></option>
                                                                 <option value="Domestic" {{ (!isset($existingQuotation) || $existingQuotation->quotation_type == 'Domestic') ? 'selected' : '' }}>Domestic</option>
                                                                 <option value="Export" {{ (isset($existingQuotation) && $existingQuotation->quotation_type == 'Export') ? 'selected' : '' }}>Export</option>
                                                             </select>
                                                        </div>
                                                        <div class="col-12 col-md-3 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Subject
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text" name="subject" id="subject"
                                                                class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Quotation
                                                                Email <span id="email_required_asterisk"
                                                                    class="text-danger d-none">*</span></label>
                                                            <input type="text" name="email" id="quotation_email"
                                                                class="form-control form-control-sm"
                                                                value="{{ $lead->email ?? '' }}"
                                                                placeholder="e.g. email1@example.com, email2@example.com">
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Phone
                                                                <span id="phone_required_asterisk"
                                                                    class="text-danger d-none">*</span></label>
                                                            <input type="text" name="phone" id="quotation_phone"
                                                                class="form-control form-control-sm"
                                                                 value="{{ $lead->phone ?? '' }}" maxlength="15" pattern="[0-9()+\- ]*" oninput="this.value = this.value.replace(/[^0-9()+\- ]/g, '')">
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Assigned
                                                                Staff</label>
                                                            <select name="assigned_to" id="assigned_to"
                                                                class="form-select form-select-sm select2">
                                                                <option value="">Select Assigned Staff</option>
                                                                @foreach($staffs as $staff)
                                                                    <option value="{{ $staff->staff_id }}" data-type="{{ $staff->staff_type }}" {{ (isset($deal) && $deal->assigned_to == $staff->staff_id) ? 'selected' : '' }}>
                                                                        {{ $staff->staff_name }} {{ $staff->staff_type ? '(' . ucfirst($staff->staff_type) . ')' : '' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Currency
                                                                <span class="text-danger">*</span></label>
                                                            <select class="form-select form-select-sm select2"
                                                                id="currency" name="currency" required>
                                                                <option value=""></option>
                                                                <option value="INR" {{ (!isset($existingQuotation) || $existingQuotation->currency == 'INR') ? 'selected' : '' }}>Indian Rupee (INR)</option>
                                                                <option value="USD" {{ (isset($existingQuotation) && $existingQuotation->currency == 'USD') ? 'selected' : '' }}>US Dollar (USD)</option>
                                                                <option value="EUR" {{ (isset($existingQuotation) && $existingQuotation->currency == 'EUR') ? 'selected' : '' }}>Euro (EUR)</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6 col-md-3 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Exchange
                                                                Rate <span class="text-danger">*</span></label>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text">1 INR =</span>
                                                                <input type="number" class="form-control form-control-sm"
                                                                    name="exchange_rate" id="exchange_rate" step="0.000001"
                                                                    value="{{ isset($existingQuotation) ? $existingQuotation->exchange_rate : 1 }}"
                                                                    {{ (!isset($existingQuotation) || $existingQuotation->currency == 'INR') ? 'readonly' : '' }}>
                                                                <span class="input-group-text"
                                                                    id="targetCurrencyDisplay">INR</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-4 mb-2">
                                                            <label
                                                                class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Message
                                                                <span class="text-danger">*</span></label>
                                                            <textarea name="message" id="message"
                                                                class="form-control form-control-sm" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Product Configuration --}}
                                            <div class="card custom-card border mb-4">
                                               <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                    <h6 class="card-title text-dark mb-0 fw-bold fs-13">
                                                        <i class="fa-solid fa-sliders me-2 text-primary"></i>
                                                        Product Configuration
                                                    </h6>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="fs-12 fw-bold text-muted">Include GST</span>
                                                        <div class="form-check form-switch mb-0 me-2">
                                                            <input type="checkbox" name="include_gst" id="includeGst"
                                                                class="form-check-input" value="1"
                                                                style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                                            <label class="form-check-label" for="includeGst"></label>
                                                        </div>
                                                        <button type="button"
                                                            class="btn btn-primary btn-sm"
                                                            id="add_product_global">
                                                            <i class="fa-solid fa-plus me-1"></i>
                                                            Create Product
                                                        </button>
                                                    </div>
                                                </div>
                                                 <div class="card-body p-3">
                                                     <!-- Add Product Form -->
                                                     <div class="border rounded p-3 bg-white mb-3 shadow-sm">
                                                         <div class="row g-2 align-items-end">
                                                             <div class="col-6 col-md-2 mb-2">
                                                                 <label
                                                                     class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Category</label>
                                                                 <select class="form-select form-select-sm select2"
                                                                     id="cart_category_select" style="width: 100%;">
                                                                     <option value="">All Categories</option>
                                                                     @foreach ($categories as $cat)
                                                                         <option value="{{ $cat->category_id }}">
                                                                             {{ $cat->name }}
                                                                         </option>
                                                                     @endforeach
                                                                 </select>
                                                             </div>
                                                             <div class="col-6 col-md-3 mb-2">
                                                                 <label
                                                                     class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Product <span class="text-danger">*</span></label>
                                                                 <select class="form-select form-select-sm select2"
                                                                     id="cart_product_select" style="width: 100%;">
                                                                     <option value="">Select a product...</option>
                                                                     @foreach ($products as $prod)
                                                                         <option value="{{ $prod->product_id }}"
                                                                             data-category="{{ $prod->category_id }}"
                                                                             data-price="{{ $prod->prod_amount }}"
                                                                             data-gst="{{ $prod->gst_no ?? 0 }}"
                                                                             data-unit="{{ $prod->unit_name ?? '' }}"
                                                                             data-description="{{ $prod->prod_desc ?? '-' }}">
                                                                             {{ $prod->prod_name }} @if(!empty($prod->category_name)) ({{ $prod->category_name }}) @endif
                                                                         </option>
                                                                     @endforeach
                                                                 </select>
                                                             </div>
                                                             <div class="col-6 col-md-1 mb-2">
                                                                 <label
                                                                     class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Unit</label>
                                                                 <input type="text" class="form-control form-control-sm bg-light"
                                                                     id="item_unit" placeholder="Unit" readonly>
                                                             </div>
                                                             <div class="col-6 col-md-1 mb-2">
                                                                 <label
                                                                     class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Qty <span class="text-danger">*</span></label>
                                                                 <input type="number" step="1" class="form-control form-control-sm"
                                                                     id="item_qty" value="1" min="1">
                                                             </div>
                                                             <div class="col-6 col-md-2 mb-2" id="unit_price_container">
                                                                 <label
                                                                     class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Price/Amt <span class="text-danger">*</span></label>
                                                                 <input type="number" step="0.01" class="form-control form-control-sm"
                                                                     id="unit_price" placeholder="0.00" value="0" min="0">
                                                             </div>
                                                             <div class="col-6 col-md-1 mb-2" id="gst_select_container">
                                                                 <label
                                                                     class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">GST (%)</label>
                                                                 <input type="number" step="0.01" class="form-control form-control-sm"
                                                                     id="item_gst_rate" placeholder="0.00" value="0" min="0">
                                                             </div>
                                                             <!-- Keep Discount percentage and price hidden to avoid DB breaks -->
                                                             <input type="hidden" id="discount_percentage" value="0">
                                                             <input type="hidden" id="discount_price" value="0">
                                                             
                                                             <div class="col-6 col-md-2 mb-2">
                                                                 <button type="button" class="btn btn-primary btn-sm w-100 fw-bold animate-btn"
                                                                     id="addItemBtn" style="margin-top: 21px;">
                                                                     <i class="fa-solid fa-plus me-1"></i> Add
                                                                 </button>
                                                                 <div id="editFormButtons" style="display: none; margin-top: 21px;" class="w-100">
                                                                     <div class="d-flex gap-1 w-100">
                                                                         <button type="button" class="btn btn-success btn-sm w-100 px-1 fw-bold animate-btn" id="btnUpdateProduct" title="Update">
                                                                             <i class="fas fa-check"></i> Update
                                                                         </button>
                                                                         <button type="button" class="btn btn-light btn-sm border text-secondary w-100 px-1 fw-bold animate-btn" id="cancelEditBtn" title="Cancel">
                                                                             <i class="fas fa-times"></i> Cancel
                                                                         </button>
                                                                     </div>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                         <div class="row g-2 mt-2" id="item_description_container">
                                                             <div class="col-12">
                                                                 @include('components.tooltip-help', ['key' => 'quotation_description', 'label' => 'Description', 'class' => 'form-label fs-12 fw-bold text-muted text-uppercase mb-1'])
                                                                 {{-- <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Description</label> --}}
                                                                 <textarea id="item_description" class="form-control form-control-sm" rows="2" placeholder="Product description..."></textarea>
                                                             </div>
                                                         </div>
                                                     </div>
                                                     {{-- Live Preview Strip --}}
                                                     <div class="p-3 rounded border border-primary border-opacity-25"
                                                         style="background:linear-gradient(to right, #f8f9fa, #eef2ff);">
                                                         <div
                                                             class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                                             <div class="text-center px-2">
                                                                 <span
                                                                     class="text-muted d-block fs-10 fw-bold text-uppercase mb-1">Unit
                                                                     Total</span>
                                                                 <span class="fw-bold text-dark fs-15"
                                                                     id="live_unit_total">₹0.00</span>
                                                             </div>
                                                             
                                                             <div class="text-muted fs-18 fw-bold">+</div>
                                                             <div class="text-center px-2">
                                                                 <span
                                                                     class="text-muted d-block fs-10 fw-bold text-uppercase mb-1">GST</span>
                                                                 <span class="fw-bold text-dark fs-15"
                                                                     id="live_gst">₹0.00</span>
                                                             </div>
                                                             <div class="text-primary fs-18 fw-bold">=</div>
                                                             <div
                                                                 class="text-center px-3 py-1 bg-white rounded shadow-sm border">
                                                                 <span
                                                                     class="text-primary d-block fs-10 fw-bold text-uppercase mb-1">Line
                                                                     Total</span>
                                                                 <span class="fw-bold fs-16 text-primary"
                                                                     id="live_total">₹0.00</span>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </div>
                                            </div>
                                            {{-- Item List Table --}}
                                            <div class="card custom-card border mb-4">
                                                <div class="card-header bg-light py-2">
                                                    <h6 class="card-title text-dark mb-0 fw-bold fs-13"><i
                                                            class="fa-solid fa-table-list me-2 text-primary"></i>Item List
                                                    </h6>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-hover mb-0"
                                                            style="font-size: 13px;">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width: 4%;" class="text-center">#</th>
                                                                    <th style="width: 30%;">Product Description</th>
                                                                    <th style="width: 8%;" class="text-center">Qty</th>
                                                                    <th style="width: 13%;" class="text-end">Price (₹)</th>
                                                                    <th style="width: 13%;" class="text-end">GST (₹)</th>
                                                                    <th style="width: 12%;" class="text-end">Total (₹)</th>
                                                                    <th style="width: 10%;" class="text-center text-nowrap">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="itemListBody">
                                                                <tr id="emptyRow">
                                                                    <td colspan="8"
                                                                        class="text-center text-muted py-5 bg-light">
                                                                        <i
                                                                            class="fa-solid fa-cart-arrow-down fs-3 mb-2 opacity-50"></i><br>
                                                                        No items added yet
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <input type="hidden" name="subtotal" id="hidden_subtotal"
                                                                value="0">
                                                            <input type="hidden" name="installation_total"
                                                                id="hidden_installation_total" value="0">
                                                            <input type="hidden" name="hidden_gst_total"
                                                                id="hidden_gst_total" value="0">
                                                            <input type="hidden" name="grand_total" id="hidden_grand_total"
                                                                value="0">
                                                            <input type="hidden" name="discount_total" id="hidden_discount_total"
                                                                value="0">
                                                            <tfoot id="itemListFooter">
                                                                <tr class="table-active">
                                                                    <td colspan="5" class="text-end fw-bold text-muted">Sub Total:</td>
                                                                    <td class="text-end fw-bold">₹0.00</td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr class="table-active">
                                                                    <td colspan="5" class="text-end fw-bold text-muted">IGST:</td>
                                                                    <td class="text-end fw-bold">₹0.00</td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr class="table-active">
                                                                    <td colspan="5" class="text-end fw-bold text-dark fs-14">Grand Total:</td>
                                                                    <td class="text-end fw-bold fs-15 text-primary">₹0.00</td>
                                                                    <td></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Attachments & Terms (Side-by-Side Layout) --}}
                                            <div class="row g-4 mb-4">
                                                <!-- Attachments Card -->
                                                <div class="col-xl-6">
                                                    <div class="card custom-card border mb-0 h-100">
                                                        <div class="card-header bg-light py-2">
                                                            <h6 class="card-title text-dark mb-0 fw-bold fs-13"><i
                                                                    class="fa-solid fa-paperclip me-2 text-primary"></i>Attachments
                                                            </h6>
                                                        </div>
                                                        <div class="card-body p-3">
                                                            <div class="mb-3 p-2 bg-light border rounded d-none" id="existingAttachmentsContainer">
                                                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-2"><i class="fas fa-file-archive me-1 text-primary"></i> Existing Attachments</label>
                                                                <div class="d-flex flex-wrap gap-2" id="existingAttachmentsList"></div>
                                                            </div>

                                                            <div class="border border-dashed rounded p-4 text-center mb-3 bg-primary bg-opacity-10"
                                                                style="cursor: pointer; border-color: #cbd5e1 !important;"
                                                                onclick="$('#quotation_files').click()">
                                                                <i
                                                                    class="fa-solid fa-cloud-arrow-up fs-2 text-primary mb-2"></i>
                                                                <p class="mb-0 text-dark fw-semibold fs-13">Click to select
                                                                    files</p>
                                                                <span class="text-muted fs-11">Supports Images & PDFs</span>
                                                            </div>
                                                            <input type="file" id="quotation_files" class="d-none" multiple
                                                                accept="image/*,application/pdf">
                                                            <div id="filePreviewContainer" class="row g-2"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Terms & Conditions Card -->
                                                <div class="col-xl-6">
                                                    <div class="card custom-card border mb-0 h-100">
                                                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                            <h6 class="card-title text-dark mb-0 fw-bold fs-13">
                                                                <i class="fa-solid fa-scale-balanced me-2 text-primary"></i>
                                                                Terms & Conditions
                                                            </h6>
                                                            <button type="button"
                                                                class="btn btn-primary btn-sm"
                                                                id="add_terms_conditions_global">
                                                                <i class="fa-solid fa-plus me-1"></i>
                                                                Create Terms
                                                            </button>
                                                        </div>
                                                        <div class="card-body p-3">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Select
                                                                    Terms Template</label>
                                                                <select id="terms_select"
                                                                    class="form-select form-select-sm select2">
                                                                    <option value="">Search Terms Template...</option>
                                                                    @foreach($terms_templates as $term)
                                                                        <option value="{{ $term->term_id }}" data-terms="{{ $term->terms }}">{{ $term->module }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="mb-0">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Terms
                                                                    Text <span class="text-danger">*</span></label>
                                                                <textarea name="terms" id="terms"
                                                                    class="form-control form-control-sm"
                                                                    rows="4"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Final Action Footer --}}
                                            <div
                                                class="card custom-card border border-primary border-opacity-50 shadow-sm mb-2">
                                                <div
                                                    class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3 bg-primary bg-opacity-10 rounded">
                                                    <div class="d-flex align-items-center gap-4 flex-wrap">
                                                        <div class="form-check form-switch ms-2">
                                                            <input type="checkbox" name="send_email" id="send_email"
                                                                class="form-check-input shadow-sm" value="1"
                                                                style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                                            <label class="form-check-label fw-bold text-dark ms-2 mt-1"
                                                                for="send_email" style="cursor: pointer;">Send Email to
                                                                Customer</label>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ $previousUrl }}"
                                                            class="btn btn-light border px-4 fw-bold">Cancel</a>
                                                        <button type="button" id="createQuotBtn"
                                                            class="btn btn-primary px-4 fw-bold"><i
                                                                class="fa-solid fa-check-circle me-1"></i> Create
                                                            Quotation</button>
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
        </div>
        @include('include.footer')
    </div>
    @include('include.scroll-top')
    @include('include.footer_link')
    @include('company.includes.add_product')
    @include('company.includes.add_terms')
    <script>
        $(document).ready(function () {
            const CONFIG = {
                STATE_ID: @json((int) get_company_details(1)->state),
                COMPANY_NAME: @json(get_company_details(1)->name),
                isRevise: {{ $isRevise ? 'true' : 'false' }},
                existingQuotId: '{{ $existingQuotation->quotation_id ?? "" }}',
                urls: {
                    cities: "{{ route('location.cities') }}",
                    cityDetails: "{{ route('location.by_city', ['id' => '__ID__']) }}",
                    items: "{{ route('getdata.quotation.products') }}",
                    terms: "{{ route('getdata.terms-conditions') }}",
                    store: "{{ route('quotation.store') }}",
                    revisionData: "{{ route('quotation.revision.data', ['id' => '__ID__']) }}"
                },
                data: {
                    leadId: "{{ $lead->lead_id ?? '' }}",
                    dealId: "{{ $deal->deal_id ?? '' }}",
                    companyName: "{{ $lead->company_name ?? $deal->company_name ?? '' }}",
                    previousUrl: "{{ $previousUrl }}",
                    csrf: $('meta[name="csrf-token"]').attr('content')
                }
            };
            const STATE = {
                items: [],
                files: [],
                cache: {},
                current: null,
                resetting: false,
                editingIndex: null,
                loadingRevision: false
            };
            const UI = {
                toast: (type, title, message) => iziToast[type]({ title, message, position: 'topRight' }),
                warn: (msg, selector) => {
                    UI.toast('warning', 'Warning', msg);
                    if (selector) {
                        const $el = $(selector);
                        $el.hasClass('select2-hidden-accessible') ? $el.select2('open') : $el.focus();
                    }
                },
                currency: val => formatIndianCurrency(val),
                escape: str => String(str).replace(/[&<>"']/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[s]),
                setSelect2: (selector, id, text) => {
                    const $el = $(selector);
                    if (!$el.find(`option[value="${id}"]`).length) $el.append(new Option(text, id, true, true));
                    else $el.val(id);
                    $el.trigger('change');
                },
                initAjaxSelect2: (selector, url, placeholder, mapFn) => {
                    $(selector).select2({
                        dropdownParent: $(selector).parent(),
                        placeholder: placeholder,
                        allowClear: true,
                        ajax: {
                            url: url,
                            dataType: 'json',
                            delay: 250,
                            data: params => ({ search: params.term, page: params.page || 1 }),
                            processResults: (data, params) => {
                                params.page = params.page || 1;
                                const total = data.total_count ?? data.total ?? 0;
                                return {
                                    results: mapFn ? mapFn(data) : data.results,
                                    pagination: { more: (params.page * 10) < total }
                                };
                            },
                            cache: true
                        }
                    });
                }
            };

            function filterStaffByType(type, selectSelector = '#assigned_to') {
                const $select = $(selectSelector);
                if (!$select.length) return;

                if (!$select.data('master-options')) {
                    const master = [];
                    $select.find('option').each(function () {
                        const val = $(this).val();
                        if (val) {
                            master.push({
                                id: val,
                                text: $(this).text().trim(),
                                type: $(this).attr('data-type') || ''
                            });
                        }
                    });
                    $select.data('master-options', master);
                }

                const master = $select.data('master-options') || [];
                const currentVal = $select.val();
                const targetType = (type || '').toLowerCase();

                $select.empty().append('<option value="">Select Assigned Staff</option>');
                master.forEach(item => {
                    const itemType = (item.type || '').toLowerCase();
                    if (!targetType || itemType === targetType) {
                        const isSelected = (currentVal && String(currentVal) === String(item.id));
                        $select.append(new Option(item.text, item.id, false, isSelected));
                    }
                });

                if (currentVal && $select.find(`option[value="${currentVal}"]`).length) {
                    $select.val(currentVal);
                } else {
                    $select.val('');
                }
                $select.trigger('change.select2');
            }

            const API = {
                fetchCityDetails: (id, cb) => $.get(CONFIG.urls.cityDetails.replace('__ID__', id), cb),
                fetchRevisionData: (id, cb) => $.get(CONFIG.urls.revisionData.replace('__ID__', id), cb),
                submitQuotation: (formData, cb, errCb) => {
                    $.ajax({
                        url: CONFIG.urls.store,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: cb,
                        error: errCb
                    });
                },
                fetchExchangeRate: (target, cb) => {
                    if (target === 'INR') {
                        cb(1.0);
                        return;
                    }
                    $.ajax({
                        url: `https://api.frankfurter.dev/v2/rate/INR/${target}`,
                        type: 'GET',
                        success: res => {
                            if (res && res.rate) {
                                cb(parseFloat(res.rate));
                            } else {
                                cb(1.0);
                            }
                        },
                        error: () => {
                            UI.toast('warning', 'Warning', 'Exchange rate API error. Defaulted to 1.');
                            cb(1.0);
                        }
                    });
                }
            };
            const gstTypeIsWithGst = () => $('#includeGst').is(':checked');
            const Handlers = {
                initSelect2() {
                    UI.initAjaxSelect2('#city_id', CONFIG.urls.cities, "Search City...", data => $.map(data.data, obj => ({ id: obj.city_id, text: obj.city_name })));
                    $('#assigned_to').select2({ placeholder: "Select Assigned Staff", allowClear: true });
                    $('#quotation_type').select2({ placeholder: "Select Quotation Type", allowClear: true });
                    filterStaffByType($('#quotation_type').val());
                    $('#currency').select2({ placeholder: "Select Currency", allowClear: true });
                    $('#terms_select').select2({ placeholder: "Search Terms Template...", allowClear: true });
                    $('#cart_category_select').select2({ placeholder: "All Categories", allowClear: true });
                    $('#cart_product_select').select2({ placeholder: "Select a product...", allowClear: true });
                    @if(!empty($cityId))
                        if (!CONFIG.isRevise) UI.setSelect2('#city_id', "{{ $cityId }}", "{{ $cityName }}");
                    @endif
                    
                    window.masterProducts = [];
                    $('#cart_product_select option').each(function () {
                        const $opt = $(this);
                        if ($opt.val() !== '') {
                            window.masterProducts.push({
                                id: $opt.val(),
                                text: $opt.text().trim(),
                                category: $opt.attr('data-category'),
                                price: $opt.attr('data-price'),
                                gst: $opt.attr('data-gst') || 0,
                                unit: $opt.attr('data-unit') || '',
                                description: $opt.attr('data-description') || '-'
                            });
                        }
                    });
                    Handlers.updateProductDropdown();
                },

                updateProductDropdown() {
                    const catId = $('#cart_category_select').val();
                    const $prodSelect = $('#cart_product_select');
                    const currentVal = $prodSelect.val();

                    const addedProductIds = (STATE.items || []).map(item => String(item.item_id));
                    let editingProdId = null;
                    if (STATE.editingIndex !== null && STATE.editingIndex !== undefined && STATE.items[STATE.editingIndex]) {
                        editingProdId = String(STATE.items[STATE.editingIndex].item_id);
                    }

                    try { $prodSelect.select2('close'); } catch(e) {}

                    $prodSelect.empty().append('<option value="">Select a product...</option>');

                    if (window.masterProducts && Array.isArray(window.masterProducts)) {
                        window.masterProducts.forEach(prod => {
                            const prodIdStr = String(prod.id);
                            const isMatchCategory = !catId || catId === "" || String(prod.category) === String(catId);
                            const isAdded = addedProductIds.includes(prodIdStr);
                            const isEditingThis = editingProdId !== null && prodIdStr === editingProdId;

                            if (isMatchCategory && (!isAdded || isEditingThis)) {
                                const option = new Option(prod.text, prod.id);
                                $(option).attr('data-price', prod.price);
                                $(option).attr('data-category', prod.category);
                                $(option).attr('data-gst', prod.gst);
                                $(option).attr('data-unit', prod.unit || '');
                                $(option).attr('data-description', prod.description);
                                $prodSelect.append(option);
                            }
                        });
                    }

                    if (currentVal && $prodSelect.find(`option[value="${currentVal}"]`).length > 0) {
                        $prodSelect.val(currentVal).trigger('change.select2');
                    } else {
                        $prodSelect.val('').trigger('change.select2');
                    }
                },

                loadRevision() {
                    if (!CONFIG.isRevise || !CONFIG.existingQuotId) return;
                    STATE.loadingRevision = true;
                    API.fetchRevisionData(CONFIG.existingQuotId, res => {
                        if (!res.status) {
                            STATE.loadingRevision = false;
                            return UI.toast('error', 'Error', 'Failed to load revision data.');
                        }
                        const d = res.data;
                        $('#quotation_person').val(d.quotation_person);
                        $('#customer_name').val(d.customer_name);
                        $('#gst_no').val(d.gst_no);
                        $('#subject').val(d.subject);
                        $('#quotation_email').val(d.email);
                        $('#quotation_phone').val(d.phone);
                        $('#message').val(d.message);
                        $('#terms').val(d.terms_conditions);
                        $('#assigned_to').val(d.assigned_to).trigger('change');
                        
                        $('#quotation_type').val(d.quotation_type).trigger('change');
                        $('#currency').val(d.currency).trigger('change');
                        $('#exchange_rate').val(d.exchange_rate).trigger('input');
                        if (d.city_id && d.city_name) UI.setSelect2('#city_id', d.city_id, d.city_name);
                        
                        const hasGstInRev = (parseInt(d.include_gst) === 1 || d.gst_type === 'with_gst' || (d.items && d.items.some(it => parseFloat(it.gst_percent) > 0)));
                        $('#includeGst').prop('checked', hasGstInRev).trigger('change');

                        STATE.items = d.items;
                        Handlers.renderTable();
                        Handlers.updateProductDropdown();
                        if (d.attachments && d.attachments.length > 0) {
                            let html = '';
                            d.attachments.forEach(att => {
                                html += `
                                    <div class="badge bg-white text-dark border p-2 d-inline-flex align-items-center gap-2 shadow-sm">
                                        <i class="fas fa-paperclip text-primary"></i>
                                        <a href="${att.file_url}" target="_blank" class="text-primary fw-semibold text-decoration-underline" title="Click to view/download">
                                            ${att.file_name.length > 28 ? att.file_name.substring(0, 28) + '...' : att.file_name}
                                        </a>
                                        <input type="hidden" name="existing_attachments[]" value="${att.file_name}">
                                    </div>
                                `;
                            });
                            $('#existingAttachmentsList').html(html);
                            $('#existingAttachmentsContainer').removeClass('d-none');
                        }
                        STATE.loadingRevision = false;
                    });
                },
                bindEvents() {
                    $('#city_id').on('change', function () {
                        const id = $(this).val();
                        if (!id) return Handlers.clearLocation();
                        API.fetchCityDetails(id, res => {
                            if (res.status) {
                                $('#state_id').val(res.data.state_id);
                                $('#state_name').val(res.data.state_name);
                                $('#country_id').val(res.data.country_id);
                                $('#country_name').val(res.data.country_name);

                                // Auto-select quotation type and currency based on country ID 99 (India)
                                const isIndia = (parseInt(res.data.country_id) === 99 || (res.data.country_name && res.data.country_name.toLowerCase() === 'india'));
                                const qType = isIndia ? 'Domestic' : 'Export';
                                $('#quotation_type').val(qType).trigger('change');
                                if (isIndia) {
                                    $('#currency').val('INR').trigger('change');
                                }
                            } else Handlers.clearLocation();
                            Handlers.calculateTotals();
                        });
                    });
                    $('#terms_select').on('change', function () {
                        const selected = $(this).find('option:selected');
                        if (selected.length && selected.val()) {
                            $('#terms').val(selected.data('terms') || '');
                        } else {
                            $('#terms').val('');
                        }
                    });
                    // Filter products based on Category select
                    $('#cart_category_select').on('change', function () {
                        Handlers.updateProductDropdown();
                        $('#item_unit').val('');
                        $('#unit_price').val(0);
                        $('#item_gst_rate').val(0);
                        $('#item_description').val('');
                    });

                    // Update inputs when product select changes
                    $('#cart_product_select').change(function () {
                        const selectedOption = $(this).find('option:selected');
                        const productId = $(this).val();
                        if (!productId) {
                            STATE.current = null;
                            $('#item_unit').val('');
                            $('#unit_price').val(0);
                            $('#item_gst_rate').val(0);
                            $('#item_description').val('');
                            Handlers.updateLivePreview();
                            return;
                        }

                        const price = parseFloat(selectedOption.attr('data-price')) || 0;
                        $('#unit_price').val(price);

                        const gst = parseFloat(selectedOption.attr('data-gst')) || 0;
                        $('#item_gst_rate').val(gst);

                        const unit = selectedOption.attr('data-unit') || '';
                        $('#item_unit').val(unit);

                        const desc = selectedOption.attr('data-description') || '';
                        $('#item_description').val(desc === '-' ? '' : desc);

                        STATE.current = {
                            id: productId,
                            text: selectedOption.text().trim(),
                            gst: gst,
                            price: price,
                            unit: unit,
                            description: desc
                        };
                        Handlers.updateLivePreview();
                    });

                    $('#addItemBtn').on('click', Handlers.processItemAddition);
                    $('#btnUpdateProduct').on('click', Handlers.processItemAddition);

                    $(document).on('click', '.remove-item', function () {
                        STATE.items.splice($(this).data('index'), 1);
                        Handlers.renderTable();
                        Handlers.updateProductDropdown();
                    });

                    $(document).on('click', '.update-item', function () {
                        const idx = $(this).data('index');
                        const item = STATE.items[idx];
                        if (!item) return;
                        STATE.editingIndex = idx;
                        $('#addItemBtn').hide();
                        $('#editFormButtons').show();
                        $('.update-item, .remove-item').prop('disabled', true);

                        STATE.current = {
                            id: item.item_id,
                            text: item.product_name,
                            gst: item.gst_percent,
                            price: item.unit_price,
                            description: item.description
                        };

                        const masterProd = window.masterProducts.find(x => String(x.id) === String(item.item_id));
                        if (masterProd && masterProd.category) {
                            $('#cart_category_select').val(masterProd.category);
                        }

                        Handlers.updateProductDropdown();

                        $('#cart_product_select').val(item.item_id).trigger('change');
                        $('#item_qty').val(item.qty);
                        $('#item_description').val(item.description || '');
                        $('#unit_price').val(item.unit_price);
                        $('#item_gst_rate').val(item.gst_percent);

                        Handlers.updateLivePreview();
                        Handlers.renderTable();
                    });

                    $('#cancelEditBtn').on('click', function () {
                        STATE.editingIndex = null;
                        $('#addItemBtn').show();
                        $('#editFormButtons').hide();
                        Handlers.resetProductConfig();
                        Handlers.renderTable();
                    });
                    $('#quotation_files').on('change', function (e) {
                        STATE.files.push(...Array.from(e.target.files));
                        Handlers.renderFiles();
                        $(this).val('');
                    });
                    $('#send_email').on('change', function () {
                        if ($(this).is(':checked')) {
                            $('#email_required_asterisk').removeClass('d-none');
                            $('#quotation_email').prop('required', true);
                        } else {
                            $('#email_required_asterisk').addClass('d-none');
                            $('#quotation_email').prop('required', false);
                        }
                    }).trigger('change');
                    $('#createQuotBtn').on('click', Handlers.submitForm);
                    $(document).on('input', '#discount_percentage', function () {
                        Handlers.syncDiscount('percentage');
                    });
                    $(document).on('input', '#discount_price', function () {
                        
                    });
                    $(document).on('input', '#unit_price', function () {
                        Handlers.syncDiscount('percentage');
                    });
                    $('#currency').on('change', function () {
                        const curr = $(this).val();
                        $('#targetCurrencyDisplay').text(curr);
                        if (STATE.loadingRevision) return;
                        if (curr === 'INR') {
                            $('#exchange_rate').val(1).prop('readonly', true).trigger('input');
                            $('#quotation_type').val('Domestic').trigger('change');
                        } else {
                            $('#exchange_rate').prop('readonly', false);
                            $('#quotation_type').val('Export').trigger('change');
                        }
                    });
                    $('#quotation_type').on('change', function () {
                        const type = $(this).val();
                        filterStaffByType(type);
                        const isExport = type === 'Export';
                        const withGst = gstTypeIsWithGst();
                        if (isExport) {
                            $('#gst_select_container').addClass('d-none');
                            $('#includeGst').prop('checked', false).prop('disabled', true).trigger('change');
                        } else {
                            $('#includeGst').prop('disabled', false);
                            if (STATE.current) {
                                if (!withGst) {
                                    $('#gst_select_container').addClass('d-none');
                                } else if (STATE.current.gst) {
                                    $('#gst_select_container').removeClass('d-none');
                                } else {
                                    $('#gst_select_container').addClass('d-none');
                                }
                            }
                        }
                        Handlers.updateLivePreview();
                        Handlers.calculateTotals();
                    });
                    $('#gstExemption').on('change', function () {
                        const checked = $(this).is(':checked');
                        if (checked || !gstTypeIsWithGst()) {
                            $('#gst_select_container').addClass('d-none');
                        } else {
                            if (STATE.current?.gst) {
                                $('#gst_select_container').removeClass('d-none');
                            }
                        }
                        Handlers.updateLivePreview();
                        Handlers.calculateTotals();
                    });
                    $('#includeGst').on('change', function () {
                        const withGst = gstTypeIsWithGst();
                        if (!withGst) {
                            $('#gst_select_container').addClass('d-none');
                        } else {
                            const qType = $('#quotation_type').val();
                            const isExport = qType === 'Export';
                            if (!isExport && STATE.current?.gst) {
                                $('#gst_select_container').removeClass('d-none');
                            }
                        }
                        Handlers.renderTable();
                        Handlers.calculateTotals();
                        Handlers.updateLivePreview();
                    });
                },
                clearLocation() {
                    $('#state_id, #state_name, #country_id, #country_name').val('');
                    Handlers.calculateTotals();
                },
                resetProductConfig() {
                     if (STATE.resetting) return;
                     STATE.resetting = true;
                     STATE.current = null;
                     Handlers.updateProductDropdown();
                     $('#item_qty').val(1);
                     $('#item_gst_rate').val(0);
                     $('#unit_price, #discount_percentage, #discount_price').val(0);
                     $('#item_description').val('');
                     STATE.resetting = false;
                     Handlers.updateLivePreview();
                 },
                 updateLivePreview() {
                     const qty = parseInt($('#item_qty').val(), 10) || 0;
                     const price = parseFloat($('#unit_price').val()) || 0;
                     let discountPrice = 0;
                     const qType = $('#quotation_type').val();
                     const isExport = qType === 'Export';
                     let gstPercent = 0;
                     if (gstTypeIsWithGst() && !isExport) {
                         gstPercent = parseFloat($('#item_gst_rate').val()) || 0;
                     }
                     const discountedUnitPrice = Math.max(0, price - discountPrice);
                     const saleTotal = discountedUnitPrice * qty;
                     const gstAmount = saleTotal * (gstPercent / 100);
                     const total = saleTotal + gstAmount;
                     const excRate = parseFloat($('#exchange_rate').val()) || 1;
                     const curr = $('#currency').val();
                     const unitTotal = price * qty;
                     const totalDiscount = discountPrice * qty;
                     if (curr === 'INR') {
                         $('#live_unit_total').text(UI.currency(unitTotal));
                         $('#live_discount').text(UI.currency(totalDiscount));
                         $('#live_gst').text(UI.currency(gstAmount));
                         $('#live_total').text(UI.currency(total));
                     } else {
                         const convUnitTotal = unitTotal * excRate;
                         const convDiscount = totalDiscount * excRate;
                         const convGst = gstAmount * excRate;
                         const convTot = total * excRate;
                         $('#live_unit_total').html(`${UI.currency(unitTotal)}<br><small class="text-muted">(${curr} ${convUnitTotal.toFixed(2)})</small>`);
                         $('#live_discount').html(`${UI.currency(totalDiscount)}<br><small class="text-muted">(${curr} ${convDiscount.toFixed(2)})</small>`);
                         $('#live_gst').html(`${UI.currency(gstAmount)}<br><small class="text-muted">(${curr} ${convGst.toFixed(2)})</small>`);
                         $('#live_total').html(`${UI.currency(total)}<br><small class="text-muted">(${curr} ${convTot.toFixed(2)})</small>`);
                     }
                 },
                 bindLivePreviewEvents() {
                     $(document).on('input change', '#item_qty, #unit_price, #item_gst_rate, #cart_product_select, #discount_percentage, #discount_price, #exchange_rate', Handlers.updateLivePreview);
                 },
                 syncDiscount(source) {
                     if (!STATE.current) return;
                     const unitPrice = parseFloat($('#unit_price').val()) || 0;
                     if (unitPrice <= 0) return;
                     const maxDiscount = unitPrice;
                     if (source === 'price') {
                         let price = parseFloat($('#discount_price').val()) || 0;
                         if (price > maxDiscount) {
                             price = maxDiscount;
                             $('#discount_price').val(price);
                         }
                         const pct = (price / unitPrice) * 100;
                         $('#discount_percentage').val(pct.toFixed(2));
                     } else if (source === 'percentage') {
                         let pct = parseFloat($('#discount_percentage').val()) || 0;
                         let price = (unitPrice * pct) / 100;
                         if (price > maxDiscount) {
                             price = maxDiscount;
                             pct = (price / unitPrice) * 100;
                             $('#discount_percentage').val(pct.toFixed(2));
                         }
                         $('#discount_price').val(price.toFixed(2));
                     }
                 },
                 clampDiscount() {
                     if (!STATE.current) return;
                     const unitPrice = parseFloat($('#unit_price').val()) || 0;
                     const maxDiscount = unitPrice;
                     let price = parseFloat($('#discount_price').val()) || 0;
                     price = Math.max(0, Math.min(price, maxDiscount, unitPrice));
                     const pct = unitPrice > 0 ? (price / unitPrice) * 100 : 0;
                     $('#discount_price').val(price.toFixed(2));
                     $('#discount_percentage').val(Math.max(0, Math.min(pct, 100)).toFixed(2));
                 },
                 processItemAddition() {
                     if (!STATE.current) return UI.warn('Please select a product first.', '#cart_product_select');
                     
                     const qty = parseInt($('#item_qty').val(), 10);
                     if (!qty || qty <= 0) return UI.warn('Enter valid quantity.', '#item_qty');
                     const p = parseFloat($('#unit_price').val()) || 0;
                     if (p <= 0) return UI.warn('Unit Price must be > 0.', '#unit_price');
                     const description = $('#item_description').val().trim();
                     const qType = $('#quotation_type').val();
                     const isExport = qType === 'Export';
                     let gstPrc = 0, gstNm = '';
                     if (gstTypeIsWithGst() && !isExport) {
                         gstPrc = parseFloat($('#item_gst_rate').val()) || 0;
                         gstNm = gstPrc > 0 ? gstPrc + '%' : '';
                     }
                     let discPrc = 0;
                     let discPrice = 0;
                     const discountedUnitPrice = Math.max(0, p - discPrice);
                     const sub = discountedUnitPrice * qty;
                     const gstAmt = sub * (gstPrc / 100);
                     if (STATE.editingIndex !== null && STATE.editingIndex !== undefined) {
                         const it = STATE.items[STATE.editingIndex];
                         it.qty = qty;
                         it.unit_price = p;
                         it.description = description;
                         it.gst_name = gstNm;
                         it.gst_percent = gstPrc;
                         it.gst_amount = gstAmt;
                         it.total_price = sub + gstAmt;
                         it.discount_percentage = discPrc;
                         it.discount_price = discPrice;
                         STATE.editingIndex = null;
                         $('#addItemBtn').show();
                         $('#editFormButtons').hide();
                     } else {
                         const existing = STATE.items.findIndex(x => x.item_id === STATE.current.id);
                         if (existing > -1) {
                             const it = STATE.items[existing];
                             it.qty += qty;
                             it.unit_price = p;
                             it.description = description;
                             it.gst_name = gstNm;
                             it.gst_percent = gstPrc;
                             it.discount_percentage = discPrc;
                             it.discount_price = discPrice;
                             const itDiscountedPrice = Math.max(0, p - discPrice);
                             it.gst_amount = (itDiscountedPrice * it.qty) * (gstPrc / 100);
                             it.total_price = (itDiscountedPrice * it.qty) + it.gst_amount;
                         } else {
                             STATE.items.push({
                                 item_id: STATE.current.id,
                                 product_name: STATE.current.text,
                                 description: description,
                                 qty,
                                 unit_price: p,
                                 gst_name: gstNm,
                                 gst_percent: gstPrc,
                                 gst_amount: gstAmt,
                                 total_price: sub + gstAmt,
                                 discount_percentage: discPrc,
                                 discount_price: discPrice
                             });
                         }
                     }
                     Handlers.renderTable();
                     Handlers.resetProductConfig();
                 },
                renderTable() {
                    const $body = $('#itemListBody').empty();
                    if (!STATE.items.length) {
                        $body.append('<tr id="emptyRow"><td colspan="8" class="text-center text-muted py-4">No items added yet</td></tr>');
                        return Handlers.calculateTotals();
                    }
                    const curr = $('#currency').val();
                    const excRate = parseFloat($('#exchange_rate').val()) || 1;
                    const showConv = curr !== 'INR';
                    const qType = $('#quotation_type').val();
                    const isExport = qType === 'Export';
                    const truncateWords = (text, limit = 50) => {
                        if (!text) return '';
                        const words = String(text).trim().split(/\s+/);
                        return words.length > limit ? words.slice(0, limit).join(' ') + '...' : String(text);
                    };
                    STATE.items.forEach((it, i) => {
                        let itGstPrc = 0;
                        if (gstTypeIsWithGst() && !isExport && it.gst_percent > 0) {
                            itGstPrc = it.gst_percent;
                        }
                        const discountedUnitPrice = Math.max(0, it.unit_price - it.discount_price);
                        const itemSub = discountedUnitPrice * it.qty;
                        const itGstAmt = itemSub * (itGstPrc / 100);
                        const itTotal = itemSub + itGstAmt;
                        it.gst_amount = itGstAmt;
                        it.total_price = itTotal;
                        const formatCol = (val) => {
                            if (!showConv) return UI.currency(val);
                            return `${UI.currency(val)}<br><small class="text-muted">(${curr} ${(val * excRate).toFixed(2)})</small>`;
                        };
                        const gst = itGstPrc > 0 ? `${itGstPrc}%<br><small class="text-muted">(${formatCol(itGstAmt)})</small>` : '0';
                        const discountDisplay = '0';
                        const isEditingThis = STATE.editingIndex === i;
                        const disabledAttr = STATE.editingIndex !== null ? 'disabled' : '';
                        $body.append(`
                            <tr class="${isEditingThis ? 'table-warning' : ''}">
                                <td class="text-center">${i + 1}</td>
                                <td>
                                    ${UI.escape(it.product_name)}
                                    ${it.description ? `<div class="text-muted" style="font-size:10px;">${UI.escape(truncateWords(it.description))}</div>` : ''}
                                </td>
                                <td class="text-center">${it.qty}</td>
                                <td class="text-end">${formatCol(it.unit_price)}</td>
                                <td class="text-end">${gst}</td>
                                <td class="text-end">${formatCol(it.total_price)}</td>
                                <td class="text-center text-nowrap">
                                    <button class="btn btn-sm btn-outline-secondary update-item me-2" data-index="${i}" ${disabledAttr}><i class="fa-solid fa-pen"></i></button>
                                    <button class="btn btn-sm btn-outline-danger remove-item" data-index="${i}" ${disabledAttr}><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        `);
                    });
                    Handlers.calculateTotals();
                },
                calculateTotals() {
                    const qType = $('#quotation_type').val();
                    const isExport = qType === 'Export';
                    let sub = 0, gstTot = 0, discountTot = 0;
                    STATE.items.forEach(it => {
                        const discountedUnitPrice = Math.max(0, it.unit_price - it.discount_price);
                        const itemSub = discountedUnitPrice * it.qty;
                        sub += itemSub;
                        discountTot += (parseFloat(it.discount_price) || 0) * it.qty;
                        let itGstPrc = 0;
                        if (gstTypeIsWithGst() && !isExport && it.gst_percent > 0) {
                            itGstPrc = it.gst_percent;
                        }
                        it.gst_amount = itemSub * (itGstPrc / 100);
                        it.total_price = itemSub + it.gst_amount;
                        gstTot += it.gst_amount;
                    });
                    const sId = parseInt($('#state_id').val()) || 0;
                    const grand = sub + gstTot;
                    $('#hidden_subtotal').val(sub);
                    $('#hidden_gst_total').val(gstTot);
                    $('#hidden_installation_total').val(0);
                    $('#hidden_grand_total').val(grand);
                    $('#hidden_discount_total').val(discountTot);
                    const excRate = parseFloat($('#exchange_rate').val()) || 1;
                    const curr = $('#currency').val();
                    const formatVal = (val) => {
                        if (curr === 'INR') return UI.currency(val);
                        return `${UI.currency(val)}<br><small class="text-muted">(${curr} ${(val * excRate).toFixed(2)})</small>`;
                    };
                    let footer = `<tr class="table-active"><td colspan="5" class="text-end fw-bold">Sub Total:</td><td class="text-end fw-bold">${formatVal(sub)}</td><td></td></tr>`;
                    if (!isExport && gstTypeIsWithGst()) {
                        if (sId === CONFIG.STATE_ID) {
                            footer += `<tr class="table-active"><td colspan="5" class="text-end fw-bold">CGST:</td><td class="text-end fw-bold">${formatVal(gstTot / 2)}</td><td></td></tr>
                                       <tr class="table-active"><td colspan="5" class="text-end fw-bold">SGST:</td><td class="text-end fw-bold">${formatVal(gstTot / 2)}</td><td></td></tr>`;
                        } else {
                            footer += `<tr class="table-active"><td colspan="5" class="text-end fw-bold">IGST:</td><td class="text-end fw-bold">${formatVal(gstTot)}</td><td></td></tr>`;
                        }
                    }
                    $('#itemListFooter').html(footer + `<tr class="table-active"><td colspan="5" class="text-end fw-bold">Grand Total:</td><td class="text-end fw-bold fs-5 text-primary">${formatVal(grand)}</td><td></td></tr>`);
                },
                renderFiles() {
                    const $cnt = $('#filePreviewContainer').empty();
                    STATE.files.forEach((f, i) => {
                        const col = $('<div class="col-md-3 file-preview-item"></div>');
                        const card = $('<div class="card p-2 border position-relative"></div>');
                        $('<button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle" style="width: 24px; height: 24px; padding: 0; z-index: 10;"><i class="fa-solid fa-xmark"></i></button>').on('click', (e) => {
                            e.preventDefault();
                            STATE.files.splice(i, 1);
                            Handlers.renderFiles();
                        }).appendTo(card);
                        if (f.type.startsWith('image/')) {
                            const reader = new FileReader();
                            const img = $('<img class="img-fluid rounded" style="height: 100px; object-fit: cover; width: 100%;">').appendTo(card);
                            reader.onload = e => img.attr('src', e.target.result);
                            reader.readAsDataURL(f);
                        } else {
                            card.append(`<div class="text-center py-3"><i class="fa-solid fa-file-pdf fa-3x text-danger mb-2"></i><div class="text-truncate small" style="max-width: 100%;">${UI.escape(f.name)}</div></div>`);
                        }
                        $cnt.append(col.append(card));
                    });
                },
                submitForm(e) {
                    e.preventDefault();
                    const req = [
                        { id: '#quotation_person', msg: 'Quotation Person is required.' },
                        { id: '#customer_name', msg: 'Customer Name is required.' },
                        { id: '#city_id', msg: 'City is required.' },
                        { id: '#state_id', selector: '#state_name', msg: 'State is required.' },
                        { id: '#country_id', selector: '#country_name', msg: 'Country is required.' },
                        { id: '#subject', msg: 'Subject is required.' },
                        { id: '#message', msg: 'Message is required.' },
                        { id: '#terms', msg: 'Terms & Conditions are required.' }
                    ];
                    if ($('#send_email').is(':checked') || $('#quotation_email').val()) {
                        const emailVal = String($('#quotation_email').val()).trim();
                        if ($('#send_email').is(':checked') && !emailVal) {
                            return UI.warn('Quotation Email is required when Send Email to Customer is enabled.', '#quotation_email');
                        }
                        if (emailVal) {
                            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            const emails = emailVal.split(',').map(e => e.trim()).filter(e => e.length > 0);
                            for (let e of emails) {
                                if (!emailPattern.test(e)) {
                                    return UI.warn(`Please enter a valid Quotation Email address ('${e}' is invalid).`, '#quotation_email');
                                }
                            }
                        }
                    }
                    if (!STATE.items.length) return UI.warn('At least one product is required.', '#cart_product_select');
                    const fd = new FormData();
                    fd.append('lead_id', CONFIG.data.leadId);
                    fd.append('deal_id', CONFIG.data.dealId);
                    fd.append('company_name', CONFIG.data.companyName);
                    fd.append('customer_name', $('#customer_name').val());
                    fd.append('contact_person_name', $('#quotation_person').val());
                    fd.append('gst_no', $('#gst_no').val());
                    fd.append('city_id', $('#city_id').val());
                    fd.append('state_id', $('#state_id').val());
                    fd.append('country_id', $('#country_id').val());
                    fd.append('subject', $('#subject').val());
                    fd.append('message', $('#message').val());
                    fd.append('terms', $('#terms').val());
                    fd.append('email', $('#quotation_email').val());
                    fd.append('phone', $('#quotation_phone').val());
                    fd.append('remarks', '');
                    fd.append('send_email', $('#send_email').is(':checked') ? 1 : 0);
                    fd.append('subtotal', $('#hidden_subtotal').val());
                    fd.append('installation_total', $('#hidden_installation_total').val());
                    fd.append('gst_total', $('#hidden_gst_total').val());
                    fd.append('grand_total', $('#hidden_grand_total').val());
                    fd.append('discount_total', $('#hidden_discount_total').val() || 0);
                    fd.append('gst_exemption', 1);
                    fd.append('items', JSON.stringify(STATE.items));
                    fd.append('_token', CONFIG.data.csrf);
                    fd.append('assigned_to', $('#assigned_to').val() || '');
                    fd.append('quotation_type', $('#quotation_type').val());
                    fd.append('currency', $('#currency').val());
                    fd.append('exchange_rate', $('#exchange_rate').val());
                    $('[name="existing_attachments[]"]').each(function() {
                        fd.append('existing_attachments[]', $(this).val());
                    });
                    STATE.files.forEach(f => fd.append('files[]', f));
                    const $btn = $('#createQuotBtn').prop('disabled', true).text('Saving...');
                    API.submitQuotation(fd, res => {
                        if (res.status) {
                            UI.toast('success', 'Success', res.message);
                            setTimeout(() => window.location.href = CONFIG.data.previousUrl, 1500);
                        } else {
                            UI.toast('error', 'Error', res.message || 'Something went wrong.');
                            $btn.prop('disabled', false).text('Create Quotation');
                        }
                    }, xhr => {
                        let errMsg = 'Server error. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errs = xhr.responseJSON.errors;
                            const firstKey = Object.keys(errs)[0];
                            if (firstKey && errs[firstKey].length) {
                                errMsg = errs[firstKey][0];
                            }
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        UI.toast('error', 'Error', errMsg);
                        $btn.prop('disabled', false).text('Create Quotation');
                    });
                }
            };
            Handlers.initSelect2();
            Handlers.bindEvents();
            Handlers.bindLivePreviewEvents();
            Handlers.loadRevision();
            
            @if(!$isRevise && $lead && $lead->products && $lead->products->count() > 0)
                @foreach($lead->products as $leadItem)
                    @if($leadItem->product)
                        @php
                            $p = $leadItem->product;
                            $gstNo = $p->gstDetails ? $p->gstDetails->gst_no : 0;
                        @endphp
                        STATE.items.push({
                            item_id: "{{ $leadItem->product_id }}",
                            product_name: "{{ $p->prod_name }}",
                            description: "",
                            qty: {{ (int) $leadItem->quantity }},
                            unit_price: parseFloat("{{ $leadItem->price ?? 0 }}") || 0,
                            gst_name: "{{ $gstNo > 0 ? $gstNo . '%' : '' }}",
                            gst_percent: {{ $gstNo ?: 0 }},
                            gst_amount: 0,
                            total_price: 0,
                            discount_percentage: 0,
                            discount_price: 0
                        });
                    @endif
                @endforeach
                Handlers.renderTable();
            @endif
            if (!CONFIG.isRevise) {
                const quotationPerson = $('#quotation_person').val() || '';
                const customerName = $('#customer_name').val() || '';
                const companyName = CONFIG.COMPANY_NAME || '';
                $('#subject').val('Quotation For ' + quotationPerson);
                const defaultMsg = `Dear ${customerName || quotationPerson},\n\nPlease find our quotation details below:\n\nBest regards,\n${companyName}`;
                $('#message').val(defaultMsg);
            }
        });
    </script>
</body>
</html>