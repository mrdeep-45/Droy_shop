<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        .select2-results__options::-webkit-scrollbar { width: 8px; }
        .select2-results__options::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .select2-results__options::-webkit-scrollbar-thumb { background: #7E9A6B; border-radius: 10px; }
        .select2-results__options::-webkit-scrollbar-thumb:hover { background: #6b8759; }
        .select2-results__options { scrollbar-width: thin; scrollbar-color: #7E9A6B #f1f1f1; }
        .flatpickr-wrapper { display: block !important; width: 100% !important; }
        .performa-info-box { background: linear-gradient(to right, #e3f2fd, #f0f7ff); border-left: 4px solid #2196F3; border-radius: 6px; padding: 12px 16px; }
        @media (max-width: 767.98px) {
            #invoiceTable {
                display: none !important;
            }
            .invoice-mobile-card {
                border-radius: 10px;
                border: 1px solid #e9ecef;
                border-top: 4px solid #00454f;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                background-color: #fff;
                padding: 12px 14px;
                margin-bottom: 16px;
                position: relative;
                overflow: hidden;
            }
            .invoice-mobile-card .card-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 4px;
                font-size: 12px;
                color: #333;
            }
            .invoice-mobile-card .card-row:last-child {
                margin-bottom: 0;
            }
            .invoice-mobile-card .label-muted {
                color: #6c757d;
                font-weight: 500;
                font-size: 11px;
            }
            .invoice-mobile-card .value-bold {
                font-weight: 600;
                color: #000;
            }
            .invoice-mobile-card .total-highlight {
                font-weight: 700;
                color: #198754;
                font-size: 13px;
            }
            .invoice-mobile-card .btn-custom-action {
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
            .invoice-mobile-card .btn-custom-action i {
                font-size: 13px;
                width: 14px;
                text-align: center;
            }
            .invoice-mobile-card table {
                width: 100% !important;
                margin-bottom: 0 !important;
                font-size: 11px !important;
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
                                <div class="card-title text-primary mb-0">{{ $page_name }}</div>
                                <ul class="nav nav-tabs justify-content-end nav-tabs-footer mb-0" role="tablist">
                                    @if (permissions_check('canView'))
                                        <li class="nav-item">
                                            <a class="nav-link active" id="list-tab" data-bs-toggle="tab" role="tab"
                                                href="#invoice-list" aria-selected="true">
                                                <i class="fas fa-list me-1"></i> List
                                            </a>
                                        </li>
                                    @endif
                                    @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link" id="new-invoice-tab" data-bs-toggle="tab" role="tab"
                                                href="#new-invoice" aria-selected="false">
                                                <i class="fas fa-plus me-1"></i>
                                                <span id="new-invoice-tab-text">New Invoice</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <div class="card-body p-0">
                                <div class="tab-content">

                                    {{-- ══════════════ LIST TAB ══════════════ --}}
                                    @if (permissions_check('canView'))
                                        <div class="tab-pane fade show active" id="invoice-list">
                                            <div class="table-responsive">
                                                <table id="invoiceTable" class="table table-bordered" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">#</th>
                                                            <th width="12%">Invoice No</th>
                                                            <th width="15%">Customer</th>
                                                            <th width="10%">Date</th>
                                                            <th width="20%">Total (incl. GST)</th>
                                                            <th width="15%">Advance / Balance</th>
                                                            @if(permissions_check('canUpdate'))
                                                                <th width="15%" class="text-center">Actions</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <div id="invoice_mobile_cards" class="d-md-none mt-2">
                                                 <!-- Dynamic Mobile Cards will be rendered here -->
                                             </div>
                                        </div>
                                    @endif

                                    {{-- ══════════════ FORM TAB ══════════════ --}}
                                    @if(permissions_check('canCreate'))
                                        <div class="tab-pane text-muted p-4" id="new-invoice" role="tabpanel">
                                            <form id="invoiceForm" novalidate>
                                                @csrf
                                                <input type="hidden" name="main_invoice_id" id="main_invoice_id">
                                                <input type="hidden" name="performa_invoice_id" id="performa_invoice_id">

                                                {{-- ── Section: Invoice Details ── --}}
                                                <div class="card custom-card border mb-4">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="card-title text-dark mb-0 fw-bold fs-13">
                                                            <i class="fa-solid fa-file-invoice me-2 text-primary"></i>Invoice Details
                                                        </h6>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            {{-- Customer --}}
                                                            <div class="col-12 col-md-3 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Customer <span class="text-danger">*</span></label>
                                                                <select name="customer_id" id="customer_id" class="form-select form-select-sm">
                                                                    <option value="">Search Customer...</option>
                                                                </select>
                                                            </div>
                                                            {{-- Performa Invoice --}}
                                                            <div class="col-12 col-md-3 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">
                                                                    Performa Invoice
                                                                    <span class="badge bg-secondary-transparent text-secondary ms-1 fs-10">Optional</span>
                                                                </label>
                                                                <select name="performa_invoice_select" id="performa_invoice_select" class="form-select form-select-sm select2" disabled>
                                                                    <option value="">Select Customer first...</option>
                                                                </select>
                                                            </div>
                                                            {{-- Invoice No --}}
                                                            <div class="col-6 col-md-2 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Invoice No</label>
                                                                <input type="text" class="form-control form-control-sm bg-light"
                                                                    name="invoice_no" id="invoice_no"
                                                                    value="{{ $invoice_no }}" readonly>
                                                            </div>
                                                            {{-- Invoice Date --}}
                                                            <div class="col-6 col-md-2 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Invoice Date <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="invoice_date" id="invoice_date"
                                                                    value="{{ date('d-m-Y') }}">
                                                            </div>
                                                            {{-- Invoice Type --}}
                                                            <div class="col-6 col-md-2 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Invoice Type <span class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm select2" id="invoice_type" name="invoice_type" required>
                                                                    <option value="domestic" selected>Domestic</option>
                                                                    <option value="export">Export</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ── Performa Info Box ── --}}
                                                <div id="performa_info_box" class="performa-info-box mb-4 d-none">
                                                    <div class="d-flex flex-wrap gap-4 align-items-center">
                                                        <div>
                                                            <div class="fs-11 fw-bold text-muted text-uppercase">Performa No</div>
                                                            <div class="fw-bold text-dark fs-14" id="pinfo_invoice_no">—</div>
                                                        </div>
                                                        <div>
                                                            <div class="fs-11 fw-bold text-muted text-uppercase">Grand Total</div>
                                                            <div class="fw-bold text-success fs-14" id="pinfo_grand_total">₹0.00</div>
                                                        </div>
                                                        <div>
                                                            <div class="fs-11 fw-bold text-muted text-uppercase">Advance Paid</div>
                                                            <div class="fw-bold text-primary fs-14" id="pinfo_advance">₹0.00</div>
                                                        </div>
                                                        <div>
                                                            <div class="fs-11 fw-bold text-muted text-uppercase">Balance Due</div>
                                                            <div class="fw-bold text-danger fs-14" id="pinfo_balance">₹0.00</div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" id="clearPerformaBtn">
                                                            <i class="fa-solid fa-xmark me-1"></i> Clear
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- ── Section: Customer Details ── --}}
                                                <div class="card custom-card border mb-4">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="card-title text-dark mb-0 fw-bold fs-13">
                                                            <i class="fa-solid fa-address-card me-2 text-primary"></i>Customer Details
                                                        </h6>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row">
                                                            <div class="col-6 col-md-3 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Email <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control form-control-sm" name="email" id="email">
                                                            </div>
                                                            <div class="col-6 col-md-3 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Phone <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm" name="phone" id="phone"
                                                                    maxlength="15" oninput="this.value = this.value.replace(/[^0-9()+\- ]/g, '')">
                                                            </div>
                                                            <div class="col-6 col-md-2 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">City <span class="text-danger">*</span></label>
                                                                <select name="city_id" id="city_id" class="form-select form-select-sm select2">
                                                                    <option value="">Search City...</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6 col-md-2 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">State <span class="text-danger">*</span></label>
                                                                <input type="text" id="state_name" class="form-control form-control-sm bg-light" readonly>
                                                                <input type="hidden" name="state_id" id="state_id">
                                                            </div>
                                                            <div class="col-6 col-md-2 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Country <span class="text-danger">*</span></label>
                                                                <input type="text" id="country_name" class="form-control form-control-sm bg-light" readonly>
                                                                <input type="hidden" name="country_id" id="country_id">
                                                            </div>
                                                            <div class="col-6 col-md-3 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Assigned Staff</label>
                                                                <select name="assigned_to" id="assigned_to" class="form-select form-select-sm select2">
                                                                    <option value=""></option>
                                                                    @foreach($staffs as $staff)
                                                                        <option value="{{ $staff->staff_id }}" data-type="{{ $staff->staff_type }}">{{ $staff->staff_name }} {{ $staff->staff_type ? '(' . ucfirst($staff->staff_type) . ')' : '' }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-6 col-md-3 mb-2">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Currency <span class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm select2" id="currency" name="currency" required>
                                                                    <option value="INR" selected>Indian Rupee (INR)</option>
                                                                    <option value="USD">US Dollar (USD)</option>
                                                                    <option value="EUR">Euro (EUR)</option>
                                                                    <option value="GBP">British Pound (GBP)</option>
                                                                    <option value="AED">UAE Dirham (AED)</option>
                                                                    <option value="SAR">Saudi Riyal (SAR)</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6 col-md-3 mb-2" id="exchange_rate_container" style="display:none;">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Exchange Rate <span class="text-danger">*</span></label>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">1 INR =</span>
                                                                    <input type="number" class="form-control form-control-sm"
                                                                        name="exchange_rate" id="exchange_rate" step="0.000001" value="1">
                                                                    <span class="input-group-text" id="targetCurrencyDisplay">INR</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ── Section: Product Configuration ── --}}
                                                <div class="card custom-card border mb-4">
                                                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                        <h6 class="card-title text-dark mb-0 fw-bold fs-13">
                                                            <i class="fa-solid fa-sliders me-2 text-primary"></i>Product Configuration
                                                        </h6>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="border rounded p-3 bg-white mb-3 shadow-sm">
                                                            <div class="row g-2 align-items-end">
                                                                 <div class="col-6 col-md-2 mb-2">
                                                                     <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Category</label>
                                                                     <select class="form-select form-select-sm select2" id="cart_category_select" style="width: 100%;">
                                                                         <option value=""></option>
                                                                         @foreach ($categories as $cat)
                                                                             <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                                                                         @endforeach
                                                                     </select>
                                                                 </div>
                                                                 <div class="col-6 col-md-3 mb-2">
                                                                     <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Product <span class="text-danger">*</span></label>
                                                                     <select class="form-select form-select-sm select2" id="cart_product_select" style="width: 100%;">
                                                                         <option value=""></option>
                                                                         @foreach ($products as $prod)
                                                                             <option value="{{ $prod->product_id }}"
                                                                                 data-category="{{ $prod->category_id }}"
                                                                                 data-price="{{ $prod->prod_amount }}"
                                                                                 data-gst="{{ $prod->gst_no ?? 0 }}"
                                                                                 data-description="{{ $prod->prod_desc ?? '' }}">
                                                                                 {{ $prod->prod_name }}
                                                                             </option>
                                                                         @endforeach
                                                                     </select>
                                                                 </div>
                                                                 <div class="col-6 col-md-1 mb-2">
                                                                     <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Qty <span class="text-danger">*</span></label>
                                                                     <input type="number" step="1" class="form-control form-control-sm" id="item_qty" value="1" min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                                 </div>
                                                                 <div class="col-6 col-md-2 mb-2" id="unit_price_container">
                                                                     <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Price/Amt <span class="text-danger">*</span></label>
                                                                     <input type="number" step="0.01" class="form-control form-control-sm" id="unit_price" placeholder="0.00" value="" min="0" onfocus="if(this.value=='0')this.value=''">
                                                                 </div>
                                                                 <div class="col-6 col-md-2 mb-2" id="gst_select_container">
                                                                     <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">GST (%)</label>
                                                                     <input type="number" step="0.01" class="form-control form-control-sm" id="item_gst_rate" placeholder="0.00" value="" min="0" onfocus="if(this.value=='0')this.value=''">
                                                                 </div>
                                                                 <input type="hidden" id="discount_percentage" value="0">
                                                                 <input type="hidden" id="discount_price" value="0">
                                                                 <div class="col-6 col-md-2 mb-2">
                                                                     <button type="button" class="btn btn-primary btn-sm w-100 fw-bold" id="addItemBtn" style="margin-top: 21px;">
                                                                         <i class="fa-solid fa-plus me-1"></i> Add Product
                                                                     </button>
                                                                     <div id="editFormButtons" style="display:none; margin-top: 21px;" class="w-100">
                                                                         <div class="d-flex gap-1 w-100">
                                                                             <button type="button" class="btn btn-success btn-sm w-100 px-1 fw-bold" id="btnUpdateProduct">
                                                                                 <i class="fas fa-check"></i> Update
                                                                             </button>
                                                                             <button type="button" class="btn btn-light btn-sm border text-secondary w-100 px-1 fw-bold" id="cancelEditBtn">
                                                                                 <i class="fas fa-times"></i> Cancel
                                                                             </button>
                                                                         </div>
                                                                     </div>
                                                                 </div>
                                                            </div>
                                                            {{-- Description --}}
                                                            <div class="row g-2 mt-2" id="item_description_container">
                                                                <div class="col-12">
                                                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Description</label>
                                                                    <textarea id="item_description" class="form-control form-control-sm" rows="2" placeholder="Product description..."></textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Live Preview Strip --}}
                                                        <div class="p-3 rounded border border-primary border-opacity-25"
                                                            style="background:linear-gradient(to right, #f8f9fa, #eef2ff);">
                                                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                                                <div class="text-center px-2">
                                                                    <span class="text-muted d-block fs-10 fw-bold text-uppercase mb-1">Unit Total</span>
                                                                    <span class="fw-bold text-dark fs-15" id="live_unit_total">₹0.00</span>
                                                                </div>
                                                                <div class="text-muted fs-18 fw-bold">+</div>
                                                                <div class="text-center px-2">
                                                                    <span class="text-muted d-block fs-10 fw-bold text-uppercase mb-1">GST</span>
                                                                    <span class="fw-bold text-dark fs-15" id="live_gst">₹0.00</span>
                                                                </div>
                                                                <div class="text-primary fs-18 fw-bold">=</div>
                                                                <div class="text-center px-3 py-1 bg-white rounded shadow-sm border">
                                                                    <span class="text-primary d-block fs-10 fw-bold text-uppercase mb-1">Line Total</span>
                                                                    <span class="fw-bold fs-16 text-primary" id="live_total">₹0.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ── Section: Item List ── --}}
                                                <div class="card custom-card border mb-4">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="card-title text-dark mb-0 fw-bold fs-13">
                                                            <i class="fa-solid fa-table-list me-2 text-primary"></i>Item List
                                                        </h6>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-hover mb-0" style="font-size: 13px;">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th style="width:4%;" class="text-center">#</th>
                                                                        <th style="width:30%;">Product Description</th>
                                                                        <th style="width:8%;" class="text-center">Qty</th>
                                                                        <th style="width:13%;" class="text-end">Price (₹)</th>
                                                                        <th style="width:12%;" class="text-end">Discount (₹)</th>
                                                                        <th style="width:13%;" class="text-end">GST (₹)</th>
                                                                        <th style="width:12%;" class="text-end">Total (₹)</th>
                                                                        <th style="width:10%;" class="text-center text-nowrap">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="itemListBody">
                                                                    <tr id="emptyRow">
                                                                        <td colspan="8" class="text-center text-muted py-5 bg-light">
                                                                            <i class="fa-solid fa-cart-arrow-down fs-3 mb-2 opacity-50"></i><br>
                                                                            No items added yet
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                                <input type="hidden" name="subtotal" id="hidden_subtotal" value="0">
                                                                <input type="hidden" name="hidden_gst_total" id="hidden_gst_total" value="0">
                                                                <input type="hidden" name="grand_total" id="hidden_grand_total" value="0">
                                                                <input type="hidden" name="discount_total" id="hidden_discount_total" value="0">
                                                                <tfoot id="itemListFooter"></tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ── Section: Other Information ── --}}
                                                <div class="card custom-card border mb-4">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="card-title text-dark mb-0 fw-bold fs-13">
                                                            <i class="fa-solid fa-circle-info me-2 text-primary"></i>Other Information
                                                        </h6>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Terms of Payment</label>
                                                                <select name="terms_payment" id="terms_payment" class="form-select form-select-sm">
                                                                    <option value="">-- Select Terms --</option>
                                                                    @foreach($terms as $term)
                                                                        <option value="{{ $term->terms }}">{{ $term->module }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ── Section: Payment Information ── --}}
                                                <div class="card custom-card border mb-4">
                                                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                        <h6 class="card-title text-dark mb-0 fw-bold fs-13">
                                                            <i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Payment Information
                                                        </h6>
                                                        {{-- Include GST toggle moved here --}}
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="fs-12 fw-bold text-muted">Include GST</span>
                                                            <div class="form-check form-switch mb-0">
                                                                <input type="checkbox" name="include_gst" id="includeGst"
                                                                    class="form-check-input" value="1" checked
                                                                    style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                                                <label class="form-check-label" for="includeGst"></label>
                                                            </div>
                                                            {{-- Hidden GST exemption (always not-exempt, export handled by invoice type) --}}
                                                            <input type="hidden" name="gst_exemption_toggle" id="gstExemption" value="0">
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Grand Total (incl. GST)</label>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">₹</span>
                                                                    <input type="number" class="form-control form-control-sm bg-light" id="pay_grand_total" value="0" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">
                                                                    Advance Amount <span class="text-danger">*</span>
                                                                </label>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">₹</span>
                                                                    <input type="number" class="form-control form-control-sm"
                                                                        name="advance_payment" id="advance_amount" min="0" step="0.01" value="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Amount Payable
                                                                    <small class="text-muted fw-normal text-lowercase">(Grand Total − Advance)</small>
                                                                </label>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">₹</span>
                                                                    <input type="number" class="form-control form-control-sm bg-light"
                                                                        name="balance_payment" id="balance_amount" value="0" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Submit --}}
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-light border" id="mi_reset_btn">
                                                        <i class="fas fa-times me-1"></i> Cancel
                                                    </button>
                                                    <button type="submit" class="btn btn-primary" id="mi_submit_btn">
                                                        <i class="fa-solid fa-check-circle me-1"></i>
                                                        <span id="submitBtnText">Save Invoice</span>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                </div>{{-- tab-content --}}
                            </div>{{-- card-body --}}
                        </div>{{-- card --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- View Main Invoice Modal -->
        <div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-labelledby="viewInvoiceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold text-white fs-15" id="viewInvoiceModalLabel">
                            <i class="fa-solid fa-file-invoice me-2"></i>Main Invoice Details — <span id="view_invoice_no"></span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-light">
                        <div class="card custom-card border-0 shadow-sm mb-4">
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="fs-11 fw-bold text-uppercase text-muted mb-1">Customer</div>
                                        <div class="fw-bold text-dark fs-14" id="view_customer_name"></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="fs-11 fw-bold text-uppercase text-muted mb-1">Invoice Date</div>
                                        <div class="fw-semibold text-dark" id="view_invoice_date"></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="fs-11 fw-bold text-uppercase text-muted mb-1">Invoice Type</div>
                                        <div class="fw-semibold text-dark" id="view_invoice_type"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fs-11 fw-bold text-uppercase text-muted mb-1">Email / Phone</div>
                                        <div class="text-dark" id="view_contact_info"></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="fs-11 fw-bold text-uppercase text-muted mb-1">Currency / Rate</div>
                                        <div class="text-dark" id="view_currency_info"></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="fs-11 fw-bold text-uppercase text-muted mb-1">GST Inclusion</div>
                                        <div class="text-dark" id="view_gst_inclusion"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fs-11 fw-bold text-uppercase text-muted mb-1">Created By</div>
                                        <div class="text-dark fw-semibold" id="view_created_by"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="fs-11 fw-bold text-uppercase text-muted mb-1">Assigned To</div>
                                        <div class="text-dark fw-semibold" id="view_assigned_to"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card custom-card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-2 border-bottom">
                                <h6 class="card-title text-dark mb-0 fw-bold fs-12"><i class="fa-solid fa-list me-2 text-primary"></i>Items List</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0" style="font-size: 12px;">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%" class="text-center">#</th>
                                                <th>Product Name</th>
                                                <th width="10%" class="text-center">Qty</th>
                                                <th width="15%" class="text-end">Price</th>
                                                <th width="15%" class="text-end">Discount</th>
                                                <th width="15%" class="text-end">GST</th>
                                                <th width="15%" class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="viewItemsBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card custom-card border-0 shadow-sm h-100">
                                    <div class="card-header bg-white py-2 border-bottom">
                                        <h6 class="card-title text-dark mb-0 fw-bold fs-12"><i class="fa-solid fa-circle-info me-2 text-primary"></i>Other Info</h6>
                                    </div>
                                    <div class="card-body p-3 fs-12" id="view_other_info"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card custom-card border-0 shadow-sm h-100">
                                    <div class="card-header bg-white py-2 border-bottom">
                                        <h6 class="card-title text-dark mb-0 fw-bold fs-12"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Summary</h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted fs-12">Sub Total:</span>
                                            <span class="fw-semibold fs-12" id="summary_subtotal"></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted fs-12">GST Total:</span>
                                            <span class="fw-semibold fs-12" id="summary_gst"></span>
                                        </div>
                                        <div class="d-flex justify-content-between border-top pt-2 mb-2">
                                            <span class="fw-bold fs-13 text-dark">Grand Total:</span>
                                            <span class="fw-bold fs-13 text-primary" id="summary_grand"></span>
                                        </div>
                                        <div class="d-flex justify-content-between text-success mb-2 border-top pt-2">
                                            <span class="fs-12 fw-semibold">Advance Paid:</span>
                                            <span class="fw-bold fs-12" id="summary_advance"></span>
                                        </div>
                                        <div class="d-flex justify-content-between text-danger">
                                            <span class="fs-12 fw-bold">Balance Remaining:</span>
                                            <span class="fw-bold fs-13 text-danger" id="summary_balance"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        @include('company.invoice.main.partials.email')
        @include('include.footer')
    </div>

    @include('include.footer_link')
    @include('include.datatable_js_link')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    $(function () {
        'use strict';

        // ─── CONFIG ────────────────────────────────────────────────────────
        const STATE_ID = @json((int) get_company_details(1)->state);
        const URLS = {
            store:                "{{ route('main_invoice.store') }}",
            list:                 "{{ route('main_invoice.list') }}",
            edit:                 "{{ route('main_invoice.edit', ':id') }}",
            update:               "{{ route('main_invoice.update', ':id') }}",
            performaInvoices:     "{{ route('main_invoice.performa_invoices') }}",
            performaItems:        "{{ route('main_invoice.performa_invoice_items', ':id') }}",
            customerSearch:       "{{ route('customer.search') }}",
            customerDetails:      "{{ route('customer.details', ':id') }}",
            cities:               "{{ route('location.cities') }}",
            cityDetails:          "{{ route('location.by_city', ['id' => ':id']) }}",
        };

        // ─── STATE ─────────────────────────────────────────────────────────
        const STATE = {
            items: [],
            editingIndex: null,
            isEditMode: false,
            loadingRevision: false,
            loadingInvoice: false,
            currentPerforma: null,
        };

         // ─── HANDLERS OBJECT ──────────────────────────────────────────────
        const Handlers = {
            updateLivePreview() {
                const qty        = parseFloat($('#item_qty').val()) || 0;
                const up         = parseFloat($('#unit_price').val()) || 0;
                const dp         = parseFloat($('#discount_price').val()) || 0;
                const gp         = parseFloat($('#item_gst_rate').val()) || 0;
                const includeGst = $('#includeGst').is(':checked');
                const isExport   = $('#invoice_type').val() === 'export';
                const discounted = Math.max(0, up - dp);
                const lineTotal  = qty * discounted;
                let gstAmt = 0;
                if (includeGst && !isExport) gstAmt = lineTotal * (gp / 100);
                const total = lineTotal + gstAmt;
                $('#live_unit_total').text(UI.currency(lineTotal));
                $('#live_gst').text(UI.currency(gstAmt));
                $('#live_total').text(UI.currency(total));
            },

            renderTable() {
                const $body = $('#itemListBody').empty();
                if (!STATE.items.length) {
                    $body.html('<tr id="emptyRow"><td colspan="8" class="text-center text-muted py-5 bg-light"><i class="fa-solid fa-cart-arrow-down fs-3 mb-2 opacity-50"></i><br>No items added yet</td></tr>');
                    return Handlers.calculateTotals();
                }
                const includeGst = $('#includeGst').is(':checked');
                const isExport   = $('#invoice_type').val() === 'export';
                const excRate    = parseFloat($('#exchange_rate').val()) || 1;
                const cur        = $('#currency').val() || 'INR';
                const showConv   = cur !== 'INR';

                const formatCol  = val => showConv
                    ? `${UI.currency(val)}<br><small class="text-muted">(${cur} ${(val * excRate).toFixed(2)})</small>`
                    : UI.currency(val);

                STATE.items.forEach((it, i) => {
                    let gstPrc = 0;
                    if (includeGst && !isExport) {
                        if (!it.gst_percent || parseFloat(it.gst_percent) === 0) {
                            const mp = masterProducts.find(p => String(p.id) === String(it.product_id));
                            if (mp && mp.gst) {
                                it.gst_percent = parseFloat(mp.gst) || 0;
                            }
                        }
                        gstPrc = parseFloat(it.gst_percent) || 0;
                    }
                    const disc     = Math.max(0, it.unit_price - it.discount_price);
                    const lineSub  = disc * it.qty;
                    const gstAmt   = lineSub * (gstPrc / 100);
                    const total    = lineSub + gstAmt;
                    it.gst_amount  = gstAmt;
                    it.total       = total;
                    const gstDisplay = gstPrc > 0
                        ? `${gstPrc}%<br><small class="text-muted">(${formatCol(gstAmt)})</small>`
                        : '-';
                    const discDisplay = it.discount_price > 0
                        ? `${it.discount_percentage}%<br><small class="text-muted">(${formatCol(it.discount_price)})</small>`
                        : '-';
                    $body.append(`
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td>${UI.escape(it.product_name)}${it.description ? `<div class="text-muted" style="font-size:10px;">${UI.escape(it.description)}</div>` : ''}</td>
                            <td class="text-center">${it.qty}</td>
                            <td class="text-end">${formatCol(it.unit_price)}</td>
                            <td class="text-end">${discDisplay}</td>
                            <td class="text-end">${gstDisplay}</td>
                            <td class="text-end fw-bold">${formatCol(total)}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary update-item me-1" data-index="${i}"><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-sm btn-outline-danger remove-item" data-index="${i}"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>`);
                });
                Handlers.calculateTotals();
            },

            calculateTotals() {
                const includeGst = $('#includeGst').is(':checked');
                const gstExempt  = $('#gstExemption').is(':checked');
                const isExport   = $('#invoice_type').val() === 'export';
                const sId        = parseInt($('#state_id').val()) || 0;
                const excRate    = parseFloat($('#exchange_rate').val()) || 1;
                const cur        = $('#currency').val() || 'INR';
                const showConv   = cur !== 'INR';
                const formatVal  = val => showConv
                    ? `${UI.currency(val)}<br><small class="text-muted">(${cur} ${(val * excRate).toFixed(2)})</small>`
                    : UI.currency(val);

                let sub = 0, gstTot = 0, discTot = 0;
                STATE.items.forEach(it => {
                    const disc    = Math.max(0, it.unit_price - it.discount_price);
                    const lineSub = disc * it.qty;
                    sub    += lineSub;
                    discTot += (it.discount_price || 0) * it.qty;
                    let gstPrc = 0;
                    if (includeGst && !isExport) {
                        if (!it.gst_percent || parseFloat(it.gst_percent) === 0) {
                            const mp = masterProducts.find(p => String(p.id) === String(it.product_id));
                            if (mp && mp.gst) {
                                it.gst_percent = parseFloat(mp.gst) || 0;
                            }
                        }
                        gstPrc = parseFloat(it.gst_percent) || 0;
                    }
                    it.gst_amount = lineSub * (gstPrc / 100);
                    it.total = lineSub + it.gst_amount;
                    gstTot += it.gst_amount;
                });

                const grand = sub + gstTot;
                $('#hidden_subtotal').val(sub.toFixed(2));
                $('#hidden_gst_total').val(gstTot.toFixed(2));
                $('#hidden_grand_total').val(grand.toFixed(2));
                $('#hidden_discount_total').val(discTot.toFixed(2));
                $('#pay_grand_total').val(grand.toFixed(2));

                const adv = parseFloat($('#advance_amount').val()) || 0;
                $('#balance_amount').val(Math.max(0, grand - adv).toFixed(2));

                let footer = `<tr class="table-active"><td colspan="7" class="text-end fw-bold text-muted">Sub Total:</td><td class="text-end fw-bold">${formatVal(sub)}</td></tr>`;
                if (discTot > 0) {
                    footer += `<tr class="table-active"><td colspan="7" class="text-end fw-bold text-danger">Discount:</td><td class="text-end fw-bold text-danger">-${formatVal(discTot)}</td></tr>`;
                }
                if (!isExport && gstTot > 0) {
                    if (sId === STATE_ID) {
                        footer += `<tr class="table-active"><td colspan="7" class="text-end fw-bold text-muted">CGST:</td><td class="text-end fw-bold">${formatVal(gstTot / 2)}</td></tr>
                                   <tr class="table-active"><td colspan="7" class="text-end fw-bold text-muted">SGST:</td><td class="text-end fw-bold">${formatVal(gstTot / 2)}</td></tr>`;
                    } else {
                        footer += `<tr class="table-active"><td colspan="7" class="text-end fw-bold text-muted">IGST:</td><td class="text-end fw-bold">${formatVal(gstTot)}</td></tr>`;
                    }
                }
                footer += `<tr class="table-active"><td colspan="7" class="text-end fw-bold text-dark fs-14">Grand Total:</td><td class="text-end fw-bold fs-15 text-primary">${formatVal(grand)}</td></tr>`;
                $('#itemListFooter').html(footer);
            },
        };

        // ─── UI HELPERS ────────────────────────────────────────────────────
        const UI = {
            toast: (type, title, msg) => iziToast[type]({ title, message: msg, position: 'topRight' }),
            warn: (msg, sel) => {
                UI.toast('warning', 'Warning', msg);
                if (sel) {
                    const $el = $(sel);
                    $el.hasClass('select2-hidden-accessible') ? $el.select2('open') : $el.focus();
                }
            },
            currency: (val) => formatIndianCurrency(val),
            escape: (str) => String(str).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[s]),
            setSelect2: (sel, id, text) => {
                const $el = $(sel);
                if (!$el.find(`option[value="${id}"]`).length) $el.append(new Option(text, id, true, true));
                else $el.val(id);
                $el.trigger('change');
            },
        };

        // ─── DATATABLE ─────────────────────────────────────────────────────
        const invoiceTable = $('#invoiceTable').DataTable({
            processing: true, serverSide: true, responsive: true,
            ajax: URLS.list,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'invoice_no',  name: 'invoice_no' },
                { data: 'customer',    name: 'customer', orderable: false },
                { data: 'date',        name: 'invoice_date' },
                { data: 'total',       name: 'grand_total', orderable: false },
                { data: 'advance',     name: 'advance', orderable: false },
                @if(permissions_check('canUpdate'))
                { data: 'action', name: 'action', searchable: false, orderable: false },
                @endif
            ],
            order: [[0, 'desc']],
            drawCallback: function () {
                renderMobileCards(this.api());
            }
        });

        // Reposition mobile container right after the table
        $('#invoiceTable').after($('#invoice_mobile_cards'));

        // ─── MASTER PRODUCT LIST (for category filter) ─────────────────────
        const masterProducts = [];
        $('#cart_product_select option').each(function () {
            const $o = $(this);
            if ($o.val()) {
                masterProducts.push({
                    id: $o.val(),
                    text: $o.text().trim(),
                    category: $o.attr('data-category'),
                    price: $o.attr('data-price'),
                    gst: $o.attr('data-gst') || 0,
                    description: $o.attr('data-description') || '',
                });
            }
        });

        // ─── SELECT2 INITIALIZATIONS ───────────────────────────────────────

        // Customer (AJAX)
        $('#customer_id').select2({
            width: '100%',
            placeholder: 'Search Customer...',
            allowClear: true,
            ajax: {
                url: URLS.customerSearch,
                dataType: 'json',
                delay: 300,
                cache: true,
                data: params => ({ search: params.term || '', page: params.page || 1 }),
                processResults: (data, params) => ({
                    results: data.results,
                    pagination: { more: data.pagination ? data.pagination.more : false }
                }),
            }
        });

        // City (AJAX)
        $('#city_id').select2({
            width: '100%',
            placeholder: 'Search City...',
            allowClear: true,
            ajax: {
                url: URLS.cities,
                dataType: 'json',
                delay: 250,
                cache: true,
                data: params => ({ search: params.term, per_page: 20 }),
                processResults: data => ({
                    results: $.map(data.data, obj => ({ id: obj.city_id, text: obj.city_name }))
                }),
            }
        });

        // Performa Invoice (re-initialized per customer)
        $('#performa_invoice_select').select2({ width: '100%', placeholder: 'Select Performa Invoice...', allowClear: true });

        // Static dropdowns
        $('#invoice_type').select2({ placeholder: 'Select Invoice Type', width: '100%' });
        $('#currency').select2({ placeholder: 'Select Currency', width: '100%' });
        $('#assigned_to').select2({ placeholder: 'Select Staff', allowClear: true, width: '100%' });
        filterStaffByType($('#invoice_type').val());
        $('#terms_payment').select2({ placeholder: '-- Select Terms of Payment --', allowClear: true, width: '100%' });
        $('#cart_category_select').select2({ placeholder: 'All Categories', allowClear: true, width: '100%' });
        $('#cart_product_select').select2({ placeholder: 'Select a product...', allowClear: true, width: '100%' });

        // Date picker
        $('#invoice_date').flatpickr({ dateFormat: 'd-m-Y' });

        // ─── LOAD PERFORMA INVOICES FOR CUSTOMER ──────────────────────────
        function loadPerformaInvoices(customerId) {
            const $sel = $('#performa_invoice_select');
            $sel.val(null);
            try { $sel.select2('destroy'); } catch(e) {}
            $sel.html('<option value="">Select Customer first...</option>');
            $('#performa_info_box').addClass('d-none');
            $('#performa_invoice_id').val('');

            if (!customerId) {
                $sel.prop('disabled', true).select2({ width: '100%', placeholder: 'Select Customer first...' });
                return;
            }

            $sel.prop('disabled', false).select2({
                width: '100%',
                placeholder: 'Select Performa Invoice...',
                allowClear: true,
                ajax: {
                    url: URLS.performaInvoices,
                    dataType: 'json',
                    delay: 300,
                    data: params => ({ customer_id: customerId, search: params.term || '' }),
                    processResults: data => ({ results: data.results }),
                }
            });
        }

        // ─── CUSTOMER CHANGE ──────────────────────────────────────────────
        $('#customer_id').on('change', function () {
            const cid = $(this).val();
            loadPerformaInvoices(cid);
            if (!cid) { clearCustomerFields(); return; }

            $.get(URLS.customerDetails.replace(':id', cid), function (res) {
                if (res && res.customer) {
                    const c = res.customer;
                    $('#email').val(c.email || '');
                    $('#phone').val(c.phone || '');
                    if (res.city && res.city.city_id) {
                        UI.setSelect2('#city_id', res.city.city_id, res.city.city_name);
                        $('#state_id').val(res.state ? res.state.state_id : '');
                        $('#state_name').val(res.state ? res.state.state_name : '');
                        $('#country_id').val(res.country ? res.country.c_id : '');
                        $('#country_name').val(res.country ? res.country.country_name : '');
                    } else {
                        $('#city_id').val(null).trigger('change');
                        $('#state_id, #state_name, #country_id, #country_name').val('');
                    }
                }
            });
        });

        function clearCustomerFields() {
            $('#email, #phone').val('');
            $('#city_id').val(null).trigger('change');
            $('#state_name, #state_id, #country_name, #country_id').val('');
        }

        // ─── PERFORMA INVOICE CHANGE ──────────────────────────────────────
        $('#performa_invoice_select').on('change', function () {
            const pid = $(this).val();
            $('#performa_invoice_id').val(pid || '');
            if (!pid) {
                $('#performa_info_box').addClass('d-none');
                $('#advance_amount').prop('readonly', false);
                return;
            }
            if (STATE.loadingInvoice && STATE.currentPerforma) {
                const p = STATE.currentPerforma;
                $('#pinfo_invoice_no').text(p.invoice_no);
                $('#pinfo_grand_total').text('₹' + parseFloat(p.grand_total || 0).toLocaleString('en-IN', {minimumFractionDigits:2}));
                $('#pinfo_advance').text('₹' + parseFloat(p.advance || 0).toLocaleString('en-IN', {minimumFractionDigits:2}));
                $('#pinfo_balance').text('₹' + parseFloat(p.balance || 0).toLocaleString('en-IN', {minimumFractionDigits:2}));
                $('#performa_info_box').removeClass('d-none');
                return;
            }
            STATE.loadingRevision = true;
            $.get(URLS.performaItems.replace(':id', pid), function (res) {
                try {
                    if (!res.status) { STATE.loadingRevision = false; return; }
                    const p = res.performa;

                    // Show info box
                    $('#pinfo_invoice_no').text(p.invoice_no);
                    $('#pinfo_grand_total').text('₹' + parseFloat(p.grand_total).toLocaleString('en-IN', {minimumFractionDigits:2}));
                    $('#pinfo_advance').text('₹' + parseFloat(p.advance_payment).toLocaleString('en-IN', {minimumFractionDigits:2}));
                    $('#pinfo_balance').text('₹' + parseFloat(p.balance_payment).toLocaleString('en-IN', {minimumFractionDigits:2}));
                    $('#performa_info_box').removeClass('d-none');

                    // Load items first
                    STATE.items = res.items.map(item => {
                        let gstRate = parseFloat(item.gst_percent) || 0;
                        if (gstRate === 0) {
                            const mp = masterProducts.find(p => String(p.id) === String(item.product_id));
                            if (mp && mp.gst) {
                                gstRate = parseFloat(mp.gst) || 0;
                            }
                        }
                        return {
                            product_id:          item.product_id,
                            product_name:        item.product_name,
                            description:         item.description || '',
                            qty:                 parseFloat(item.qty),
                            unit_price:          parseFloat(item.unit_price),
                            gst_percent:         gstRate,
                            gst_amount:          parseFloat(item.gst_amount),
                            discount_percentage: parseFloat(item.discount_percentage),
                            discount_price:      parseFloat(item.discount_price),
                            total:               parseFloat(item.total),
                        };
                    });
                    Handlers.renderTable();

                    // Pre-fill form details from performa
                    $('#invoice_type').val(p.invoice_type).trigger('change');
                    $('#currency').val(p.currency).trigger('change');
                    $('#exchange_rate').val(p.exchange_rate);
                    if (p.terms_payment) $('#terms_payment').val(p.terms_payment).trigger('change');
                    if (p.assigned_to) $('#assigned_to').val(p.assigned_to).trigger('change');
                    if (p.email) $('#email').val(p.email);
                    if (p.phone) $('#phone').val(p.phone);
                    if (p.city_id && p.city_name) {
                        $('#city_id').html('<option value="' + p.city_id + '" selected>' + p.city_name + '</option>').trigger('change.select2');
                        $('#state_id').val(p.state_id || '');
                        $('#state_name').val(p.state_name || '');
                        $('#country_id').val(p.country_id || '');
                        $('#country_name').val(p.country_name || '');
                    }

                    // GST: always keep ON for Main Invoice (unless invoice_type is export)
                    const isExp = $('#invoice_type').val() === 'export';
                    $('#includeGst').prop('disabled', isExp).prop('checked', !isExp).trigger('change');

                    // Advance: pre-fill with performa's advance amount and set readonly so it cannot be modified
                    $('#advance_amount').val(parseFloat(p.advance_payment).toFixed(2)).prop('readonly', true).trigger('input');

                    STATE.loadingRevision = false;
                } catch (e) {
                    console.error("Error in Performa Invoice selection AJAX callback:", e);
                    alert("Error loading Performa Invoice details: " + e.message);
                    STATE.loadingRevision = false;
                }
            }).fail((xhr) => {
                console.error("AJAX request failed:", xhr);
                alert("Failed to fetch Performa Invoice details from server.");
                STATE.loadingRevision = false;
            });
        });

        // ─── CLEAR PERFORMA ───────────────────────────────────────────────
        $('#clearPerformaBtn').on('click', function () {
            $('#performa_invoice_select').val(null).trigger('change');
        });

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

            $select.empty().append('<option value="">Select Staff</option>');
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

        // ─── CITY CHANGE ──────────────────────────────────────────────────
        $('#city_id').on('change', function () {
            const id = $(this).val();
            if (!id) {
                $('#state_id, #state_name, #country_id, #country_name').val('');
                Handlers.calculateTotals();
                return;
            }
            $.get("{{ route('get.location.by.city') }}", { city_id: id }, function (res) {
                let data = res;
                if (typeof res === 'string') {
                    try {
                        data = JSON.parse(res);
                    } catch (e) {}
                }
                if (data && data.data) {
                    data = data.data;
                }
                if (data && (data.state_name || data.country_name)) {
                    $('#state_id').val(data.state_id || '');
                    $('#state_name').val(data.state_name || '');
                    const countryId = data.country_id || data.c_id;
                    $('#country_id').val(countryId || '');
                    $('#country_name').val(data.country_name || '');

                    // Auto detect invoice type based on country ID 99 (India)
                    const invType = (parseInt(countryId) === 99 || (data.country_name && data.country_name.toLowerCase() === 'india')) ? 'domestic' : 'export';
                    $('#invoice_type').val(invType).trigger('change');
                    Handlers.calculateTotals();
                } else {
                    // Fallback: fetch via location.by_city
                    $.get("{{ route('location.by_city', ['id' => '__ID__']) }}".replace('__ID__', id), function (res2) {
                        let data2 = res2;
                        if (typeof res2 === 'string') {
                            try {
                                data2 = JSON.parse(res2);
                            } catch (e) {}
                        }
                        if (data2 && data2.data) {
                            data2 = data2.data;
                        }
                        if (data2 && (data2.state_name || data2.country_name)) {
                            $('#state_id').val(data2.state_id || '');
                            $('#state_name').val(data2.state_name || '');
                            const countryId2 = data2.country_id || data2.c_id;
                            $('#country_id').val(countryId2 || '');
                            $('#country_name').val(data2.country_name || '');

                            const invType = (parseInt(countryId2) === 99 || (data2.country_name && data2.country_name.toLowerCase() === 'india')) ? 'domestic' : 'export';
                            $('#invoice_type').val(invType).trigger('change');
                        } else {
                            $('#state_id, #state_name, #country_id, #country_name').val('');
                        }
                        Handlers.calculateTotals();
                    });
                }
            }).fail(function () {
                $('#state_id, #state_name, #country_id, #country_name').val('');
                Handlers.calculateTotals();
            });
        });

        // ─── CURRENCY CHANGE ──────────────────────────────────────────────
        $('#currency').on('change', function () {
            const cur = $(this).val() || 'INR';
            $('#targetCurrencyDisplay').text(cur);
            if (STATE.loadingRevision) return;
            if (cur === 'INR') {
                $('#exchange_rate_container').hide();
                $('#invoice_type').val('domestic').trigger('change');
                $('#exchange_rate').val(1);
                Handlers.renderTable();
                return;
            }
            $('#exchange_rate_container').show();
            $('#invoice_type').val('export').trigger('change');
            $.ajax({
                url: `https://api.frankfurter.dev/v2/rate/INR/${cur}`,
                type: 'GET',
                success: res => {
                    if (res && res.rate) $('#exchange_rate').val(parseFloat(res.rate));
                    else $('#exchange_rate').val(1);
                    Handlers.renderTable();
                },
                error: () => { $('#exchange_rate').val(1); Handlers.renderTable(); }
            });
        });

        // ─── GST TOGGLE (Include GST only, exemption removed) ────────────
        $('#includeGst').on('change', function () {
            Handlers.updateLivePreview();
            Handlers.renderTable();
        });

        // ─── INVOICE TYPE CHANGE ──────────────────────────────────────────
        $('#invoice_type').on('change', function () {
            const type = $(this).val();
            filterStaffByType(type);
            const isExport = type === 'export';
            if (isExport) {
                $('#includeGst').prop('checked', false).prop('disabled', true).trigger('change');
            } else {
                $('#includeGst').prop('disabled', false).prop('checked', true).trigger('change');
            }
            Handlers.updateLivePreview();
            Handlers.renderTable();
        });

        // ─── CATEGORY FILTER ─────────────────────────────────────────────
        $('#cart_category_select').on('change', function () {
            const catId = $(this).val();
            const $prod = $('#cart_product_select');
            $prod.empty().append('<option value=""></option>');
            masterProducts.forEach(p => {
                if (!catId || String(p.category) === String(catId)) {
                    const opt = new Option(p.text, p.id);
                    $(opt).attr({ 'data-price': p.price, 'data-category': p.category, 'data-gst': p.gst, 'data-description': p.description });
                    $prod.append(opt);
                }
            });
            $prod.val('').trigger('change.select2');
        });

        // ─── PRODUCT CHANGE ───────────────────────────────────────────────
        $('#cart_product_select').on('change', function () {
            const opt = $(this).find('option:selected');
            const pid = $(this).val();
            if (!pid) {
                $('#unit_price').val(0); $('#item_gst_rate').val(0); $('#item_description').val('');
                Handlers.updateLivePreview(); return;
            }
            $('#unit_price').val(opt.attr('data-price') || 0);
            $('#item_gst_rate').val(opt.attr('data-gst') || 0);
            const desc = opt.attr('data-description') || '';
            $('#item_description').val(desc === '-' ? '' : desc);
            Handlers.updateLivePreview();
        });

        // ─── LIVE PREVIEW ─────────────────────────────────────────────────
        $('#item_qty, #unit_price, #item_gst_rate, #discount_price').on('input change', Handlers.updateLivePreview);

        // ─── ADD ITEM ─────────────────────────────────────────────────────
        $('#addItemBtn').on('click', function () {
            const pid   = $('#cart_product_select').val();
            const pname = $('#cart_product_select option:selected').text().trim();
            if (!pid || !pname) { UI.warn('Please select a product.', '#cart_product_select'); return; }
            const qty  = parseFloat($('#item_qty').val()) || 0;
            const up   = parseFloat($('#unit_price').val()) || 0;
            if (qty <= 0)  { UI.warn('Quantity must be greater than 0.', '#item_qty'); return; }
            if (up <= 0)   { UI.warn('Price must be greater than 0.', '#unit_price'); return; }

            if (STATE.editingIndex !== null) {
                // Update existing
                const it = STATE.items[STATE.editingIndex];
                it.qty              = qty;
                it.unit_price       = up;
                it.gst_percent      = parseFloat($('#item_gst_rate').val()) || 0;
                it.discount_price   = parseFloat($('#discount_price').val()) || 0;
                it.discount_percentage = parseFloat($('#discount_percentage').val()) || 0;
                it.description      = $('#item_description').val().trim();
                STATE.editingIndex = null;
                $('#addItemBtn').html('<i class="fa-solid fa-plus me-1"></i> Add Product');
                $('#editFormButtons').hide();
                $('#addItemBtn').show();
            } else {
                STATE.items.push({
                    product_id:           pid,
                    product_name:         pname,
                    description:          $('#item_description').val().trim(),
                    qty:                  qty,
                    unit_price:           up,
                    gst_percent:          parseFloat($('#item_gst_rate').val()) || 0,
                    gst_amount:           0,
                    discount_percentage:  parseFloat($('#discount_percentage').val()) || 0,
                    discount_price:       parseFloat($('#discount_price').val()) || 0,
                    total:                0,
                });
            }
            resetProductForm();
            Handlers.renderTable();
        });

        // ─── EDIT / REMOVE ITEM ───────────────────────────────────────────
        $(document).on('click', '.update-item', function () {
            const idx  = parseInt($(this).data('index'));
            const item = STATE.items[idx];
            if (!item) return;
            STATE.editingIndex = idx;
            UI.setSelect2('#cart_product_select', item.product_id, item.product_name);
            $('#item_qty').val(item.qty);
            $('#unit_price').val(item.unit_price);
            $('#item_gst_rate').val(item.gst_percent);
            $('#discount_price').val(item.discount_price);
            $('#discount_percentage').val(item.discount_percentage);
            $('#item_description').val(item.description || '');
            $('#addItemBtn').html('<i class="fas fa-check me-1"></i> Update').show();
            $('#editFormButtons').hide();
            Handlers.updateLivePreview();
            Handlers.renderTable();
        });

        $(document).on('click', '.remove-item', function () {
            STATE.items.splice(parseInt($(this).data('index')), 1);
            Handlers.renderTable();
        });

        $('#cancelEditBtn').on('click', function () {
            STATE.editingIndex = null;
            $('#addItemBtn').html('<i class="fa-solid fa-plus me-1"></i> Add Product').show();
            $('#editFormButtons').hide();
            resetProductForm();
            Handlers.renderTable();
        });

        function resetProductForm() {
            $('#cart_category_select').val(null).trigger('change');
            $('#cart_product_select').val(null).trigger('change');
            $('#item_qty').val(1);
            $('#unit_price').val('');
            $('#item_gst_rate').val('');
            $('#discount_percentage').val(0);
            $('#discount_price').val(0);
            $('#item_description').val('');
            Handlers.updateLivePreview();
        }

        // ─── ADVANCE AMOUNT → AUTO BALANCE ───────────────────────────────
        $('#advance_amount').on('input', function () {
            const gt  = parseFloat($('#hidden_grand_total').val()) || 0;
            const adv = parseFloat($(this).val()) || 0;
            $('#balance_amount').val(Math.max(0, gt - adv).toFixed(2));
        });

       
        // ─── FORM SUBMIT ─────────────────────────────────────────────────
        function validateForm() {
            if (!$('#customer_id').val())   { UI.warn('Please select a customer.', '#customer_id'); return false; }
            if (!$('#invoice_date').val())  { UI.warn('Please enter invoice date.', '#invoice_date'); return false; }
            if (!$('#email').val())         { UI.warn('Please enter email address.', '#email'); return false; }
            if (!$('#phone').val())         { UI.warn('Please enter phone number.', '#phone'); return false; }
            if (!$('#city_id').val())       { UI.warn('Please select a city.', '#city_id'); return false; }
            if (!$('#state_id').val())      { UI.warn('State is required.', '#city_id'); return false; }
            if (!$('#country_id').val())    { UI.warn('Country is required.', '#city_id'); return false; }
            if (!STATE.items.length)        { UI.warn('Please add at least one product.', '#cart_product_select'); return false; }
            return true;
        }

        $('#invoiceForm').on('submit', function (e) {
            e.preventDefault();
            if (!validateForm()) return;
            const invoiceId = $('#main_invoice_id').val();
            const url       = invoiceId ? URLS.update.replace(':id', invoiceId) : URLS.store;
            const fd        = new FormData(this);
            fd.set('items',         JSON.stringify(STATE.items));
            fd.set('gst_exemption', $('#gstExemption').is(':checked') ? 0 : 1);
            fd.set('include_gst',   $('#includeGst').is(':checked') ? 1 : 0);
            fd.set('advance_payment',  $('#advance_amount').val() || 0);
            fd.set('balance_payment',  $('#balance_amount').val() || 0);
            if ($('#performa_invoice_id').val()) {
                fd.set('performa_invoice_id', $('#performa_invoice_id').val());
            }

            const $btn      = $('#mi_submit_btn');
            const origHtml  = $btn.html();
            $.ajax({
                url: url, type: 'POST', data: fd, processData: false, contentType: false,
                beforeSend: () => $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...'),
                success: res => {
                    if (res.status) {
                        UI.toast('success', 'Success', res.message || 'Invoice saved!');
                        resetForm();
                        invoiceTable.ajax.reload();
                        $('a[href="#invoice-list"]')[0].click();
                    } else {
                        UI.toast('error', 'Error', res.message || 'Something went wrong.');
                    }
                },
                error: xhr => {
                    if (xhr.status === 422) {
                        const errors  = xhr.responseJSON?.errors || {};
                        const firstKey = Object.keys(errors)[0];
                        UI.toast('error', 'Validation Error', errors[firstKey][0]);
                    } else {
                        UI.toast('error', 'Error', 'Server error. Please try again.');
                    }
                },
                complete: () => $btn.prop('disabled', false).html(origHtml),
            });
        });

        // ─── VIEW INVOICE MODAL HANDLER ───────────────────────────────────
        $(document).on('click', '.view-invoice', function () {
            const id = $(this).data('id');
            $.get(URLS.edit.replace(':id', id), function (res) {
                if (res.status && res.invoice) {
                    const inv = res.invoice;
                    const items = res.items;
                    $('#view_invoice_no').text(inv.invoice_no);
                    $('#view_customer_name').text(inv.customer_name);
                    $('#view_invoice_date').text(inv.invoice_date);
                    $('#view_invoice_type').text((inv.invoice_type || 'domestic').toUpperCase());
                    $('#view_contact_info').html(`Email: <strong>${inv.email || '-'}</strong><br>Phone: <strong>${inv.phone || '-'}</strong>`);
                    $('#view_currency_info').html(`Currency: <strong>${inv.currency}</strong><br>Exchange Rate: <strong>1 INR = ${inv.exchange_rate} ${inv.currency}</strong>`);
                    $('#view_gst_inclusion').html(parseInt(inv.include_gst) === 1 ? '<span class="badge bg-success">GST Included</span>' : '<span class="badge bg-secondary">Without GST</span>');
                    $('#view_created_by').text(inv.created_by_name || '-');
                    $('#view_assigned_to').text(inv.assigned_to_name || '-');

                    let otherInfo = '';
                    if (inv.terms_payment) otherInfo += `<strong>Terms of Payment:</strong><br>${inv.terms_payment}<br><br>`;
                    if (inv.dispatch_time) otherInfo += `<strong>Dispatch Time:</strong> ${inv.dispatch_time}<br>`;
                    if (inv.transportation) otherInfo += `<strong>Transportation:</strong> ${inv.transportation}<br>`;
                    if (inv.commercial_terms) otherInfo += `<strong>Commercial Terms:</strong><br>${inv.commercial_terms}`;
                    if (!otherInfo) otherInfo = '<span class="text-muted">No additional details available.</span>';
                    $('#view_other_info').html(otherInfo);

                    const convert = (val) => {
                        if (inv.currency === 'INR') return UI.currency(val);
                        return `${UI.currency(val)} (${inv.currency} ${(val * inv.exchange_rate).toFixed(2)})`;
                    };
                    $('#summary_subtotal').text(convert(inv.subtotal));
                    $('#summary_gst').text(convert(inv.gst_total));
                    $('#summary_grand').text(convert(inv.grand_total));
                    $('#summary_advance').text(convert(inv.advance_payment));
                    $('#summary_balance').text(convert(inv.balance_payment));

                    const $body = $('#viewItemsBody').empty();
                    if (items && items.length > 0) {
                        items.forEach((it, i) => {
                            const discountPrice = parseFloat(it.discount_price) || 0;
                            const discountPercentage = parseFloat(it.discount_percentage) || 0;
                            const discountText = discountPrice > 0 ? `${discountPercentage}% (${convert(discountPrice)})` : '-';
                            $body.append(`
                                <tr>
                                    <td class="text-center">${i + 1}</td>
                                    <td>
                                        <strong>${UI.escape(it.product_name)}</strong>
                                        ${it.description ? `<div class="text-muted" style="font-size:10px;">${UI.escape(it.description)}</div>` : ''}
                                    </td>
                                    <td class="text-center">${it.qty}</td>
                                    <td class="text-end">${convert(it.unit_price)}</td>
                                    <td class="text-end text-danger">${discountText}</td>
                                    <td class="text-end">${it.gst_percent > 0 ? `${it.gst_percent}% (${convert(it.gst_amount)})` : '-'}</td>
                                    <td class="text-end fw-bold text-success">${convert(it.total)}</td>
                                </tr>
                            `);
                        });
                    } else {
                        $body.append('<tr><td colspan="7" class="text-center text-muted">No items found.</td></tr>');
                    }

                    $('#viewInvoiceModal').modal('show');
                } else {
                    UI.toast('error', 'Error', 'Failed to fetch invoice details.');
                }
            }).fail(function () {
                UI.toast('error', 'Error', 'Failed to fetch invoice details.');
            });
        });

        // ─── EDIT INVOICE ─────────────────────────────────────────────────
        $(document).on('click', '.edit-invoice', function () {
            const id = $(this).data('id');
            $.get(URLS.edit.replace(':id', id), function (res) {
                if (!res.status) return;
                const inv = res.invoice;

                STATE.loadingInvoice = true;
                STATE.currentPerforma = {
                    invoice_no: inv.performa_invoice_no,
                    grand_total: inv.performa_grand_total,
                    advance: inv.performa_advance,
                    balance: inv.performa_balance
                };

                resetForm();
                STATE.isEditMode = true;
                $('#new-invoice-tab-text').text('Edit Invoice');
                $('#mi_submit_btn').html('<i class="fa-solid fa-check-circle me-1"></i> Update Invoice');
                $('#main_invoice_id').val(inv.main_invoice_id);
                $('#performa_invoice_id').val(inv.performa_invoice_id || '');
                $('#invoice_no').val(inv.invoice_no);
                $('#invoice_date').val(inv.invoice_date);
                
                // Customer
                const cOpt = new Option(inv.customer_name, inv.customer_id, true, true);
                $('#customer_id').empty().append(cOpt).trigger('change.select2');
                loadPerformaInvoices(inv.customer_id);

                // Performa Invoice
                if (inv.performa_invoice_id) {
                    const pOpt = new Option(inv.performa_invoice_no, inv.performa_invoice_id, true, true);
                    $('#performa_invoice_select').empty().append(pOpt).trigger('change.select2');
                } else {
                    $('#performa_invoice_select').val(null).trigger('change.select2');
                }

                // Fields
                $('#email').val(inv.email);
                $('#phone').val(inv.phone);
                if (inv.city_id && inv.city_name) {
                    $('#city_id').html('<option value="' + inv.city_id + '" selected>' + inv.city_name + '</option>').trigger('change.select2');
                }
                $('#state_id').val(inv.state_id || '');
                $('#state_name').val(inv.state_name || '');
                $('#country_id').val(inv.country_id || '');
                $('#country_name').val(inv.country_name || '');
                
                // Set Invoice Type first, then Assigned Staff to ensure filtering matches options list
                $('#invoice_type').val(inv.invoice_type || 'domestic').trigger('change');
                $('#assigned_to').val(inv.assigned_to || '').trigger('change');
                
                $('#currency').val(inv.currency || 'INR').trigger('change');
                $('#exchange_rate').val(inv.exchange_rate || 1);
                $('#gstExemption').val('0');
                $('#includeGst').prop('checked', parseInt(inv.include_gst) === 1).trigger('change');
                $('#terms_payment').val(inv.terms_payment || '').trigger('change');
                $('#advance_amount').val(parseFloat(inv.advance_payment || 0).toFixed(2));
                if (inv.performa_invoice_id) {
                    $('#advance_amount').prop('readonly', true);
                } else {
                    $('#advance_amount').prop('readonly', false);
                }
                $('#balance_amount').val(parseFloat(inv.balance_payment || 0).toFixed(2));
                STATE.items = res.items;
                Handlers.renderTable();

                STATE.loadingInvoice = false;
                STATE.currentPerforma = null;

                const tabEl = document.querySelector('#new-invoice-tab');
                if (tabEl) {
                    const tab = bootstrap.Tab.getOrCreateInstance(tabEl);
                    tab.show();
                }
            });
        });

        // ─── RESET FORM ──────────────────────────────────────────────────
        function resetForm() {
            STATE.isEditMode = false;
            STATE.editingIndex = null;
            STATE.items = [];
            $('#invoiceForm')[0].reset();
            $('#main_invoice_id, #performa_invoice_id').val('');
            $('#customer_id').val(null).trigger('change');
            $('#city_id').val(null).trigger('change');
            $('#assigned_to').val(null).trigger('change');
            $('#invoice_type').val('domestic').trigger('change');
            $('#currency').val('INR').trigger('change');
            $('#exchange_rate').val(1);
            $('#includeGst').prop('disabled', false).prop('checked', true).trigger('change');
            $('#gstExemption').val('0');
            $('#advance_amount, #balance_amount, #pay_grand_total').val(0);
            $('#advance_amount').prop('readonly', false);
            $('#state_name, #state_id, #country_name, #country_id').val('');
            $('#performa_info_box').addClass('d-none');
            $('#new-invoice-tab-text').text('New Invoice');
            $('#mi_submit_btn').html('<i class="fa-solid fa-check-circle me-1"></i> Save Invoice');
            $('#addItemBtn').html('<i class="fa-solid fa-plus me-1"></i> Add Product').show();
            $('#editFormButtons').hide();
            resetProductForm();
            Handlers.renderTable();
        }

        $('#mi_reset_btn').on('click', function () { resetForm(); $('a[href="#invoice-list"]')[0].click(); });
        $('a[href="#new-invoice"]').on('click', function () { if (!STATE.isEditMode) resetForm(); });

        // Initialize on page load
        Handlers.calculateTotals();
        Handlers.updateLivePreview();

        function renderMobileCards(api) {
            const data = api.rows({ page: 'current' }).data();
            const $container = $('#invoice_mobile_cards');
            $container.empty();

            if (data.length === 0) {
                $container.append('<div class="text-center text-muted p-4 bg-white rounded shadow-sm fs-12">No records found.</div>');
                return;
            }

            data.each(function (row) {
                const $quotNoDiv = $('<div>' + row.invoice_no + '</div>');
                const invoiceNo = $quotNoDiv.text().trim();

                const customerName = $('<div>' + row.customer + '</div>').text().trim();
                const invoiceDate = $('<div>' + row.date + '</div>').text().trim();

                const $totalDiv = $('<div>' + row.total + '</div>');
                const totalAmount = $totalDiv.find('.fw-bold').text().trim();

                const $advanceDiv = $('<div>' + row.advance + '</div>');
                const advanceHtml = $advanceDiv.html();

                // Extract actions from row.action
                const $actionDiv = $('<div>' + row.action + '</div>');
                const $viewBtn = $actionDiv.find('.view-invoice');
                const $editBtn = $actionDiv.find('.edit-invoice');
                const $pdfBtn = $actionDiv.find('.pdf-invoice, a[href*="/pdf/"], a:has(.fa-file-pdf)');
                const $mailBtn = $actionDiv.find('.invoice-mail, button:has(.fa-envelope)');

                const invoiceId = row.main_invoice_id || $viewBtn.attr('data-id') || $editBtn.attr('data-id') || $mailBtn.attr('data-id');

                // Let the eye button toggle the collapse drawer and fetch data dynamically
                const $mobileViewBtn = $(`
                    <button type="button" class="btn btn-sm btn-custom-action text-white btn-toggle-invoice-details" style="background-color: #00454f; border-color: #00454f;" data-id="${invoiceId}" data-bs-toggle="collapse" data-bs-target="#collapseInvoice_${invoiceId}" aria-expanded="false" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                `);

                if ($pdfBtn.length) {
                    $pdfBtn.removeClass()
                        .addClass('btn btn-sm btn-light border text-danger btn-custom-action pdf-invoice')
                        .attr('target', '_blank')
                        .html('<i class="fas fa-file-pdf"></i>');
                }

                if ($editBtn.length) {
                    $editBtn.removeClass()
                        .addClass('btn btn-sm btn-light border text-warning btn-custom-action edit-invoice')
                        .html('<i class="fas fa-pen-to-square"></i>');
                }

                if ($mailBtn.length) {
                    $mailBtn.removeClass()
                        .addClass('btn btn-sm btn-light border text-success btn-custom-action invoice-mail')
                        .html('<i class="fas fa-envelope"></i>');
                }

                // Build actions container
                const $actionsContainer = $('<div class="d-flex justify-content-end align-items-center border-top pt-2 mt-2 gap-2 w-100"></div>');
                $actionsContainer.append($mobileViewBtn);
                if ($pdfBtn.length) $actionsContainer.append($pdfBtn);
                if ($mailBtn.length) $actionsContainer.append($mailBtn);
                if ($editBtn.length) $actionsContainer.append($editBtn);

                const cardHtml = `
                    <div class="invoice-mobile-card">
                        <div class="card-row">
                            <div>
                                <span class="label-muted">Invoice No:</span>
                                <span class="value-bold">${invoiceNo}</span>
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
                                    <span class="value-bold">${invoiceDate}</span>
                                </div>
                                <div>
                                    <span class="label-muted">Total:</span>
                                    <span class="total-highlight">${totalAmount}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-row mt-2 pt-2 border-top">
                            <div class="w-100 d-flex justify-content-between" style="font-size: 11px;">
                                ${advanceHtml}
                            </div>
                        </div>

                        <!-- Collapsible Details -->
                        <div class="collapse mt-2 pt-2 border-top" id="collapseInvoice_${invoiceId}">
                            <div class="text-center py-3 text-muted" style="font-size: 11px;">
                                <i class="fas fa-spinner fa-spin me-2"></i> Loading invoice details...
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

        // Delegate click for loading detail drawer contents dynamically via AJAX
        $(document).on('click', '.btn-toggle-invoice-details', function () {
            const id = $(this).data('id');
            const $collapse = $(`#collapseInvoice_${id}`);
            if ($collapse.hasClass('data-loaded')) {
                return;
            }

            $.get(URLS.edit.replace(':id', id), function (res) {
                if (res.status && res.invoice) {
                    const inv = res.invoice;
                    const items = res.items;

                    const createdBy = inv.created_by_name || '';
                    const assignedTo = inv.assigned_to_name || '';

                    // Format currency helper
                    const convert = (val) => {
                        if (inv.currency === 'INR') return formatIndianCurrency(val);
                        return `${formatIndianCurrency(val)} (${inv.currency} ${(val * inv.exchange_rate).toFixed(2)})`;
                    };

                    // Build products table
                    let itemsHtml = `
                        <table class="table table-bordered mb-0" style="font-size: 11px;">
                            <thead>
                                <tr class="bg-light">
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    if (items && items.length > 0) {
                        items.forEach(it => {
                            itemsHtml += `
                                <tr>
                                    <td>
                                        <strong>${String(it.product_name).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[s])}</strong>
                                        ${it.description ? `<div class="text-muted" style="font-size: 9px;">${String(it.description).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[s])}</div>` : ''}
                                    </td>
                                    <td class="text-center">${it.qty}</td>
                                    <td class="text-end">${convert(it.unit_price)}</td>
                                    <td class="text-end fw-bold text-success">${convert(it.total)}</td>
                                </tr>
                            `;
                        });
                    } else {
                        itemsHtml += `<tr><td colspan="4" class="text-center text-muted">No items found.</td></tr>`;
                    }
                    itemsHtml += '</tbody></table>';

                    const subtotal = parseFloat(inv.subtotal) || 0;
                    const discount = parseFloat(inv.discount_total) || 0;
                    const gst = parseFloat(inv.gst_total) || 0;
                    const grand = parseFloat(inv.grand_total) || 0;
                    const advance = parseFloat(inv.advance_payment) || 0;
                    const balance = parseFloat(inv.balance_payment) || 0;

                    let discountHtml = '';
                    if (discount > 0) {
                        discountHtml = `
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Discount:</span>
                                <span class="fw-semibold text-danger">-${convert(discount)}</span>
                            </div>
                        `;
                    }

                    const innerHtml = `
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
                        <div class="table-responsive border rounded mb-3">
                            ${itemsHtml}
                        </div>

                        <div class="text-muted small mb-1 fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">BILLING SUMMARY</div>
                        <div class="p-2 rounded bg-light border" style="font-size: 11px;">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-semibold text-dark">${convert(subtotal)}</span>
                            </div>
                            ${discountHtml}
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">GST:</span>
                                <span class="fw-semibold text-dark">${convert(gst)}</span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-1 mt-1 font-weight-bold">
                                <span class="fw-bold text-dark">Grand Total:</span>
                                <span class="fw-bold text-success">${convert(grand)}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-1 pt-1 border-top" style="font-size: 10.5px;">
                                <span class="text-success">Advance Paid:</span>
                                <span class="fw-semibold text-success">${convert(advance)}</span>
                            </div>
                            <div class="d-flex justify-content-between" style="font-size: 10.5px;">
                                <span class="text-danger">Balance Remaining:</span>
                                <span class="fw-semibold text-danger">${convert(balance)}</span>
                            </div>
                        </div>
                    `;

                    $collapse.html(innerHtml).addClass('data-loaded');
                } else {
                    $collapse.html('<div class="text-center py-2 text-danger fs-11">Failed to load details.</div>');
                }
            }).fail(function () {
                $collapse.html('<div class="text-center py-2 text-danger fs-11">Failed to load details.</div>');
            });
        });
    });
    </script>
</body>
</html>
