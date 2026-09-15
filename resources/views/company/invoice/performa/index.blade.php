<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        .select2-results__options::-webkit-scrollbar {
            width: 8px;
        }

        .select2-results__options::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .select2-results__options::-webkit-scrollbar-thumb {
            background: #7E9A6B;
            border-radius: 10px;
        }

        .select2-results__options::-webkit-scrollbar-thumb:hover {
            background: #6b8759;
        }

        .select2-results__options {
            scrollbar-width: thin;
            scrollbar-color: #7E9A6B #f1f1f1;
        }

        .flatpickr-wrapper {
            display: block !important;
            width: 100% !important;
        }

        /* Lead-style card classes for Product Configuration */
        .lead-detail-card {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e1e4e8;
            overflow: hidden;
        }
        .card-custom-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #ebecf0;
            padding: 12px 16px;
            border-top-left-radius: 7px;
            border-top-right-radius: 7px;
        }
        .form-label-bold {
            font-weight: 600;
            color: #4a5568;
            font-size: 0.85rem;
        }

        /* Fix Select2 allowClear (×) icon overlap on quotation & other dropdowns */
        .select2-container--default .select2-selection--single .select2-selection__clear {
            position: absolute;
            right: 24px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            line-height: 1;
            color: #aaa;
            cursor: pointer;
            z-index: 1;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-right: 40px !important;
        }

        /* Advance Amount toggle buttons */
        .advance-type-btn {
            border: 1px solid #ced4da;
            background: #f8f9fa;
            color: #495057;
            font-size: 12px;
            font-weight: 700;
            padding: 0 10px;
            height: 31px;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            line-height: 29px;
        }
        .advance-type-btn:first-child {
            border-radius: 4px 0 0 4px;
            border-right: none;
        }
        .advance-type-btn:last-child {
            border-radius: 0;
            border-right: 1px solid #ced4da;
        }
        .advance-type-btn.active {
            background: #0d6efd;
            color: #fff;
            border-color: #0d6efd;
        }
        .advance-type-btn.active + .advance-type-btn {
            border-left-color: #0d6efd;
        }
        #advance_input {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            border-left: 0 !important;
            height: 31px !important;
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
                                <ul class="nav nav-tabs justify-content-end nav-tabs-footer mb-0" role="tablist">
                                    @if (permissions_check('canView'))
                                        <li class="nav-item">
                                            <a class="nav-link active" id="list-tab" data-bs-toggle="tab" role="tab"
                                                href="#invoice-list" aria-selected="true"><i class="fas fa-list me-1"></i>
                                                List</a>
                                        </li>
                                    @endif
                                    @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link" id="new-invoice-tab" data-bs-toggle="tab" role="tab"
                                                href="#new-invoice" aria-selected="false"><i class="fas fa-plus me-1"></i>
                                                <span id="new-invoice-tab-text">New PI</span></a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="card-body p-0">
                                <div class="tab-content">
                                    @if (permissions_check('canView'))
                                        <div class="tab-pane fade show active" id="invoice-list">
                                            <div class="table-responsive">
                                                <table id="invoiceTable" class="table table-bordered" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">#</th>
                                                            <th width="12%">Invoice No</th>
                                                            <th width="13%">Customer</th>
                                                            <th width="10%">Date</th>
                                                            <th width="25%">Products</th>
                                                            <th width="15%">Total</th>
                                                            @if(permissions_check('canUpdate'))
                                                                <th width="20%" class="text-center">Actions</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    @if(permissions_check('canCreate'))
                                        <div class="tab-pane text-muted p-4" id="new-invoice" role="tabpanel">
                                            <form id="invoiceForm" novalidate>
                                                @csrf
                                                <input type="hidden" name="invoice_id" id="invoice_id">
                                                <div class="card custom-card border mb-4">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="card-title text-dark mb-0 fw-bold fs-13"><i
                                                                class="fa-solid fa-file-invoice me-2 text-primary"></i>Invoice
                                                            Details</h6>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row g-3">
                                                            <div class="col-12 col-md-3">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Customer
                                                                    <span class="text-danger">*</span></label>
                                                                <select name="customer_id" id="customer_id"
                                                                    class="form-select form-select-sm">
                                                                    <option value="">Search Customer...</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-12 col-md-3">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Quotation</label>
                                                                <select name="quotation_id" id="quotation_id"
                                                                    class="form-select form-select-sm select2">
                                                                    <option value="">Select Quotation</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6 col-md-2">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Invoice
                                                                    No</label>
                                                                <input type="text"
                                                                    class="form-control form-control-sm bg-light"
                                                                    name="invoice_no" id="invoice_no"
                                                                    value="{{ $invoice_no }}" readonly>
                                                            </div>
                                                            <div class="col-6 col-md-2">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Invoice
                                                                    Date <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="invoice_date" id="invoice_date"
                                                                    value="{{ date('d-m-Y') }}">
                                                            </div>
                                                            <div class="col-6 col-md-2">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Invoice
                                                                    Type <span class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm select2"
                                                                    id="invoice_type" name="invoice_type" required>
                                                                    <option value=""></option>
                                                                    <option value="domestic">Domestic</option>
                                                                    <option value="export">Export</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card custom-card border mb-4">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="card-title text-dark mb-0 fw-bold fs-13"><i
                                                                class="fa-solid fa-address-card me-2 text-primary"></i>Customer
                                                            Details</h6>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row g-3">
                                                            <div class="col-6 col-md-3">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Email
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control form-control-sm"
                                                                    name="email" id="email">
                                                            </div>
                                                            <div class="col-6 col-md-3">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Phone
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                     name="phone" id="phone" maxlength="15" pattern="[0-9()+\- ]*" oninput="this.value = this.value.replace(/[^0-9()+\- ]/g, '')">
                                                            </div>
                                                            <div class="col-6 col-md-2">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">City
                                                                    <span class="text-danger">*</span></label>
                                                                <select name="city_id" id="city_id"
                                                                    class="form-select form-select-sm">
                                                                    <option value="">Search City...</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6 col-md-2">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">State
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" id="state_name"
                                                                    class="form-control form-control-sm bg-light" readonly>
                                                                <input type="hidden" name="state_id" id="state_id">
                                                            </div>
                                                            <div class="col-6 col-md-2">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Country
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" id="country_name"
                                                                    class="form-control form-control-sm bg-light" readonly>
                                                                <input type="hidden" name="country_id" id="country_id">
                                                            </div>
                                                            <div class="col-6 col-md-3">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Assigned
                                                                    Staff</label>
                                                                <select name="assigned_to" id="assigned_to"
                                                                    class="form-select form-select-sm select2">
                                                                    <option></option>
                                                                    @foreach($staffs as $staff)
                                                                        <option value="{{ $staff->staff_id }}" data-type="{{ $staff->staff_type }}">
                                                                            {{ $staff->staff_name }} {{ $staff->staff_type ? '(' . ucfirst($staff->staff_type) . ')' : '' }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-6 col-md-3">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Currency
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-select form-select-sm select2"
                                                                    id="currency" name="currency" required>
                                                                    <option value="INR" selected>Indian Rupee (INR)</option>
                                                                    <option value="USD">US Dollar (USD)</option>
                                                                    <option value="EUR">Euro (EUR)</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6 col-md-3" id="exchange_rate_container">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Exchange
                                                                    Rate <span class="text-danger">*</span></label>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">1 INR =</span>
                                                                    <input type="number"
                                                                        class="form-control form-control-sm"
                                                                        name="exchange_rate" id="exchange_rate"
                                                                        step="0.000001" value="1">
                                                                    <span class="input-group-text"
                                                                        id="targetCurrencyDisplay">INR</span>
                                                                </div>
                                                            </div>
                                                             <input type="hidden" name="gst_exemption" value="1">
                                                            </div>
                                                        </div>
                                                    </div>

                                                <!-- Product Configuration -->
                                                <div class="card lead-detail-card mb-3">
                                                    <div class="card-custom-header">
                                                        <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-cubes me-1"></i> Requested Products</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- Add Product Form -->
                                                        <div class="row g-2 align-items-end">
                                                            <div class="col-12 col-md-2">
                                                                <label class="form-label form-label-bold text-dark">Category</label>
                                                                <select class="form-select select2" id="cart_category_select" style="width:100%;">
                                                                    <option value="">All Categories</option>
                                                                    @foreach ($categories as $cat)
                                                                        <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-12 col-md-3">
                                                                <label class="form-label form-label-bold text-dark">Product <span class="text-danger">*</span></label>
                                                                <select class="form-select select2" id="cart_product_select" style="width:100%;">
                                                                    <option value="">Select a product...</option>
                                                                    @foreach ($products as $prod)
                                                                        <option value="{{ $prod->product_id }}"
                                                                            data-category="{{ $prod->category_id }}"
                                                                            data-price="{{ $prod->prod_amount }}"
                                                                            data-name="{{ $prod->prod_name }}"
                                                                            data-gst="{{ $prod->gst_no ?? 0 }}"
                                                                            data-description="{{ $prod->prod_desc ?? '' }}">
                                                                            {{ $prod->prod_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-4 col-md-1">
                                                                <label class="form-label form-label-bold text-dark">Qty <span class="text-danger">*</span></label>
                                                                <input type="number" step="1" class="form-control"
                                                                    id="item_qty" value="1" min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                            </div>
                                                            <div class="col-4 col-md-2" id="unit_price_container">
                                                                <label class="form-label form-label-bold text-dark">Price/Amt <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="unit_price" placeholder="0.00" value="" min="0" onfocus="if(this.value=='0')this.value=''">
                                                            </div>
                                                            <div class="col-4 col-md-2" id="gst_select_container">
                                                                <label class="form-label form-label-bold text-dark">GST (%)</label>
                                                                <input type="number" step="0.01" class="form-control"
                                                                    id="item_gst_rate" placeholder="0.00" value="" min="0" onfocus="if(this.value=='0')this.value=''">
                                                            </div>
                                                            <div class="col-12 col-md-2">
                                                                <div id="addItemBtn">
                                                                    <button type="button" class="btn btn-primary w-100" id="addItemBtnEl">
                                                                        <i class="fas fa-plus"></i> Add
                                                                    </button>
                                                                </div>
                                                                <div id="editFormButtons" style="display:none;" class="w-100">
                                                                    <div class="d-flex gap-1 w-100">
                                                                        <button type="button" class="btn btn-success w-100 px-1" id="btnUpdateProduct" title="Update">
                                                                            <i class="fas fa-check"></i> Update
                                                                        </button>
                                                                        <button type="button" class="btn btn-light border text-secondary w-100 px-1" id="cancelEditBtn" title="Cancel">
                                                                            <i class="fas fa-times"></i> Cancel
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Hidden discount fields -->
                                                            <input type="hidden" id="discount_percentage" value="0">
                                                            <input type="hidden" id="discount_price" value="0">
                                                        </div>
                                                        <!-- Description -->
                                                        <div class="row g-2 mt-2" id="item_description_container">
                                                            <div class="col-12">
                                                                 @include('components.tooltip-help', ['key' => 'pinvoice_product_description', 'label' => 'Description', 'class' => 'form-label fs-12 fw-bold text-muted text-uppercase mb-1'])
                                                                {{-- <label class="form-label form-label-bold text-dark">Description</label> --}}
                                                                <textarea class="form-control" id="item_description" rows="2" placeholder="Product description..."></textarea>
                                                            </div>
                                                        </div>
                                                        {{-- Hidden live preview IDs (used by JS calculations) --}}
                                                        <span id="live_unit_total" class="d-none"></span>
                                                        <span id="live_gst" class="d-none"></span>
                                                        <span id="live_total" class="d-none"></span>
                                                    </div>
                                                </div>

                                                <div class="card custom-card border mb-4">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="card-title text-dark mb-0 fw-bold fs-13"><i
                                                                class="fa-solid fa-table-list me-2 text-primary"></i>Item
                                                            List</h6>
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
                                                                        <th style="width: 13%;" class="text-end">Price (₹)
                                                                        </th>
                                                                        <th style="width: 12%;" class="text-end">Discount
                                                                            (₹)</th>
                                                                        <th style="width: 13%;" class="text-end">GST (₹)
                                                                        </th>
                                                                        <th style="width: 12%;" class="text-end">Total (₹)
                                                                        </th>
                                                                        <th style="width: 10%;"
                                                                            class="text-center text-nowrap">Action</th>
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
                                                                <input type="hidden" name="hidden_gst_total"
                                                                    id="hidden_gst_total" value="0">
                                                                <input type="hidden" name="grand_total"
                                                                    id="hidden_grand_total" value="0">
                                                                <input type="hidden" name="discount_total"
                                                                    id="hidden_discount_total" value="0">
                                                                <tfoot id="itemListFooter">
                                                                    <tr class="table-active">
                                                                        <td colspan="7" class="text-end fw-bold text-muted">
                                                                            Sub Total:</td>
                                                                        <td class="text-end fw-bold">₹0.00</td>
                                                                    </tr>
                                                                    <tr class="table-active">
                                                                        <td colspan="7" class="text-end fw-bold text-muted">
                                                                            IGST:</td>
                                                                        <td class="text-end fw-bold">₹0.00</td>
                                                                    </tr>
                                                                    <tr class="table-active">
                                                                        <td colspan="7"
                                                                            class="text-end fw-bold text-dark fs-14">Grand
                                                                            Total:</td>
                                                                        <td class="text-end fw-bold fs-15 text-primary">
                                                                            ₹0.00</td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card custom-card border mb-4">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="card-title text-dark mb-0 fw-bold fs-13"><i
                                                                class="fa-solid fa-truck me-2 text-primary"></i>Dispatch & Order Details</h6>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row g-3">
                                                            <div class="col-12 col-md-4">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Buyer Order No (Quotation No)</label>
                                                                <input type="text" class="form-control form-control-sm" name="buyer_order_no" id="buyer_order_no" placeholder="Enter Buyer Order No">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Buyer Order Date</label>
                                                                <input type="text" class="form-control form-control-sm" name="buyer_order_date" id="buyer_order_date" placeholder="DD-MM-YYYY">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Delivery Note</label>
                                                                <input type="text" class="form-control form-control-sm" name="delivery_note" id="delivery_note" placeholder="Enter Delivery Note">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">P.O. Reference</label>
                                                                <input type="text" class="form-control form-control-sm" name="po_reference" id="po_reference" placeholder="Enter P.O. Reference">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Destination</label>
                                                                <input type="text" class="form-control form-control-sm" name="destination" id="destination" placeholder="Enter Destination">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Transportation</label>
                                                                <input type="text" class="form-control form-control-sm" name="transportation" id="transportation" placeholder="e.g. By Road / By Air">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-4">
                                                    <!-- Left Box: Attachments -->
                                                    <div class="col-12 col-md-6">
                                                        <div class="card custom-card border h-100 mb-0">
                                                            <div class="card-header bg-light py-2">
                                                                <h6 class="card-title text-dark mb-0 fw-bold fs-13"><i
                                                                        class="fa-solid fa-paperclip me-2 text-primary"></i>Attachments</h6>
                                                            </div>
                                                            <div class="card-body p-3">
                                                                <div class="border rounded p-4 text-center bg-light mb-3" style="border-style: dashed !important; border-color: #ccc !important; cursor: pointer;" onclick="$('#invoice_files').click();">
                                                                    <i class="fa-solid fa-cloud-arrow-up fs-3 text-muted mb-2"></i>
                                                                    <h6 class="fs-13 fw-semibold mb-1">Click to select files</h6>
                                                                    <span class="text-muted fs-11">Supports Images & PDFs</span>
                                                                </div>
                                                                <input type="file" class="form-control form-control-sm d-none" name="files[]" id="invoice_files" multiple>
                                                                <div id="existing_attachments_container" class="mt-2"></div>
                                                                <div id="new_attachments_container" class="mt-2"></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Right Box: Terms & Conditions -->
                                                    <div class="col-12 col-md-6">
                                                        <div class="card custom-card border h-100 mb-0">
                                                            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                                <h6 class="card-title text-dark mb-0 fw-bold fs-13"><i
                                                                        class="fa-solid fa-scale-balanced me-2 text-primary"></i>Terms & Conditions</h6>
                                                                @if(Route::has('terms.store'))
                                                                    <button type="button" class="btn btn-primary btn-xs text-white" data-bs-toggle="modal" data-bs-target="#createTermsModal">
                                                                        <i class="fas fa-plus me-1"></i> Create Terms
                                                                    </button>
                                                                @endif
                                                            </div>
                                                            <div class="card-body p-3">
                                                                <div class="mb-3">
                                                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Select Terms Template</label>
                                                                    <select id="terms_select" name="terms_payment[]" class="form-select form-select-sm select2" multiple="multiple" data-placeholder="Search Terms Template..." style="width: 100%;">
                                                                        @foreach($terms as $term)
                                                                            <option value="{{ $term->term_id }}" data-terms="{{ $term->terms }}" data-module="{{ $term->module ?: 'General Terms' }}">
                                                                                {{ $term->module ?: 'General Terms' }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div>
                                                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Terms Text <span class="text-danger">*</span></label>
                                                                    <textarea class="form-control form-control-sm" id="terms_payment_text" rows="6" placeholder="Terms details..."></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card custom-card border mb-4">
                                                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                        <h6 class="card-title text-dark mb-0 fw-bold fs-13"><i
                                                                class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Payment
                                                            Information</h6>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="fs-12 fw-bold text-muted">Include GST</span>
                                                            <div class="form-check form-switch mb-0">
                                                                <input type="checkbox" name="include_gst" id="includeGst"
                                                                    class="form-check-input" value="1"
                                                                    style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                                                <label class="form-check-label" for="includeGst"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Grand
                                                                    Total (incl. GST)</label>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">₹</span>
                                                                    <input type="number"
                                                                        class="form-control form-control-sm bg-light"
                                                                        id="pay_grand_total" value="0" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Advance
                                                                    Amount <span class="text-danger">*</span></label>
                                                                <div class="input-group input-group-sm">
                                                                    <button type="button" class="advance-type-btn active" data-type="percentage">%</button>
                                                                    <button type="button" class="advance-type-btn" data-type="amount">₹</button>
                                                                    <input type="hidden" id="advance_type_val" value="percentage">
                                                                    <input type="number"
                                                                        class="form-control form-control-sm"
                                                                        id="advance_input"
                                                                        min="0" step="0.01" value="0" placeholder="0"
                                                                        onfocus="if(this.value == '0' || this.value === '0.00') this.value = ''"
                                                                        onblur="if(this.value === '' || this.value < 0) { this.value = '0'; }"
                                                                        oninput="if(this.value < 0) this.value = 0;">
                                                                </div>
                                                                <small class="text-muted fs-11 float-end" id="advance_hint"></small>
                                                                <input type="hidden" name="advance_payment" id="advance_amount" value="0">
                                                                <input type="hidden" name="advance_payment_percentage" id="advance_pct" value="0">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label
                                                                    class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Balance
                                                                    Amount</label>
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">₹</span>
                                                                    <input type="number"
                                                                        class="form-control form-control-sm bg-light"
                                                                        name="balance_payment" id="balance_amount"
                                                                        value="0" readonly>
                                                                </div>
                                                                <small class="text-muted fs-11">Grand Total − Advance Amount</small>
                                                                <small class="text-muted fs-11 float-end text-success fw-bold" id="balance_hint"></small>
                                                                <input type="hidden" name="balance_payment_percentage" id="balance_pct" value="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

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
                                                            <button type="button" class="btn btn-light border px-4 fw-bold"
                                                                id="pi_reset_btn">Reset</button>
                                                            <button type="submit" class="btn btn-primary px-4 fw-bold"
                                                                id="pi_submit_btn"><i
                                                                    class="fa-solid fa-check-circle me-1"></i>Save
                                                                Invoice</button>
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

        <!-- View Invoice Modal -->
        <div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-labelledby="viewInvoiceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold text-white fs-15" id="viewInvoiceModalLabel">
                            <i class="fa-solid fa-file-invoice me-2"></i>Invoice Details — <span id="view_invoice_no"></span>
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
                                    <div class="col-md-3" id="view_gst_inclusion_wrapper">
                                        <div class="fs-11 fw-bold text-uppercase text-muted mb-1">GST Inclusion</div>
                                        <div class="text-dark" id="view_gst_inclusion"></div>
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
                                                <th width="15%" class="text-end view_gst_col">GST</th>
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
                                        <!-- <div class="d-flex justify-content-between mb-2 text-danger">
                                            <span class="fs-12">Discount:</span>
                                            <span class="fw-semibold fs-12" id="summary_discount"></span>
                                        </div> -->
                                        <div class="d-flex justify-content-between mb-2" id="view_summary_gst_row">
                                            <span class="text-muted fs-12">GST Total:</span>
                                            <span class="fw-semibold fs-12" id="summary_gst"></span>
                                        </div>
                                        <div class="d-flex justify-content-between border-top pt-2 mb-2" id="view_summary_grand_row">
                                            <span class="fw-bold fs-13 text-dark">Grand Total:</span>
                                            <span class="fw-bold fs-13 text-primary" id="summary_grand"></span>
                                        </div>
                                        <div class="d-flex justify-content-between text-danger mb-2 border-top pt-2">
                                            <span class="fs-12 fw-semibold">Advance Paid:</span>
                                            <span class="fw-bold fs-12" id="summary_advance"></span>
                                        </div>
                                        <div class="d-flex justify-content-between text-success">
                                            <span class="fs-12 fw-bold">Balance Remaining:</span>
                                            <span class="fw-bold fs-13 text-success" id="summary_balance"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-light btn-sm fw-bold border px-3" data-bs-dismiss="modal">Close</button>
                        <a href="" id="view_pdf_btn" target="_blank" class="btn btn-danger btn-sm fw-bold px-3"><i class="fa-solid fa-file-pdf me-1"></i>Open PDF</a>
                    </div>
                </div>
            </div>
        </div>

        @include('company.crm.customer.modal')

        <!-- Create Terms Modal -->
        <div class="modal fade" id="createTermsModal" tabindex="-1" aria-labelledby="createTermsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold text-white fs-15" id="createTermsModalLabel">Create New Terms</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createTermsForm">
                        @csrf
                        <div class="modal-body p-4 bg-light">
                            <div class="mb-3">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Module Name / Title <span class="text-danger">*</span></label>
                                <input type="text" name="module[]" class="form-control form-control-sm" placeholder="e.g. Payment Terms" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Terms &amp; Conditions <span class="text-danger">*</span></label>
                                <textarea name="terms[]" class="form-control form-control-sm" rows="6" placeholder="Enter terms and conditions text..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2">
                            <button type="button" class="btn btn-light btn-sm fw-bold border px-3" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="btnSaveTerms" class="btn btn-primary btn-sm fw-bold px-3">Save Terms</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @include('include.footer')
    </div>
    @include('include.scroll-top')
    @include('include.footer_link')
    @include('include.datatable_js_link')
    @include('company.invoice.performa.partials.email')
    <script>
        $(document).ready(function () {
            let isEditMode = false;
            let isFormPopulating = false;

            // Automatically switch to New Invoice tab if tab=new is in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'new') {
                const newInvoiceTab = $('#new-invoice-tab');
                if (newInvoiceTab.length) {
                    newInvoiceTab[0].click();
                }
            }

            // Auto-fill quotation & customer if passed via URL
            if (urlParams.has('customer_id') && urlParams.has('quotation_id')) {
                const cid = urlParams.get('customer_id');
                const cname = urlParams.get('customer_name') || 'Customer';
                const qid = urlParams.get('quotation_id');
                const qno = urlParams.get('quotation_no') || 'Quotation';
                
                setTimeout(() => {
                    UI.setSelect2('#customer_id', cid, cname);
                    $('#customer_id').prop('disabled', true);
                    if ($('input[type="hidden"][name="customer_id"]').length === 0) {
                        $('<input>').attr({type: 'hidden', name: 'customer_id', value: cid}).insertAfter('#customer_id');
                    }
                    
                    setTimeout(() => {
                        UI.setSelect2('#quotation_id', qid, qno);
                        $('#quotation_id').prop('disabled', true);
                        if ($('input[type="hidden"][name="quotation_id"]').length === 0) {
                            $('<input>').attr({type: 'hidden', name: 'quotation_id', value: qid}).insertAfter('#quotation_id');
                        }
                    }, 500);
                }, 500);
            }
            const CONFIG = {
                STATE_ID: @json((int) get_company_details(1)->state),
                urls: {
                    cities: "{{ route('location.cities') }}",
                    cityDetails: "{{ route('location.by_city', ['id' => ':id']) }}",
                    items: "{{ route('getdata.quotation.products') }}",
                    store: "{{ route('performa_invoice.store') }}",
                    quotations: "{{ route('performa_invoice.quotations') }}",
                    quotationItems: "{{ route('performa_invoice.quotation.items', ':id') }}",
                    edit: "{{ route('performa_invoice.edit', ':id') }}",
                    update: "{{ route('performa_invoice.update', ':id') }}"
                },
                csrf: $('meta[name="csrf-token"]').attr('content')
            };

            const STATE = {
                items: [],
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

            let invoiceTable = $('#invoiceTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('performa_invoice.list') }}",
                order: [[1, 'desc']], 
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'invoice_no', name: 'invoice_no', orderable: true },
                    { data: 'customer', name: 'customer', orderable: true },
                    { data: 'invoice_date', name: 'invoice_date', orderable: true },
                    { data: 'items', name: 'items', searchable: false, orderable: false },
                    { data: 'total', name: 'grand_total', orderable: true },
                    @if(permissions_check('canUpdate'))
                        { data: 'action', name: 'action', searchable: false, orderable: false }
                    @endif
            ]
            });

            function formatCustomerOption(state) {
                if (!state.id) return state.text;
                if (state.id === 'NEW') {
                    return $('<div class="d-flex align-items-center text-primary fw-bold p-1" style="cursor:pointer;border-bottom:1px dashed #cfe2ff;margin-bottom:4px;">' +
                        '<i class="fas fa-plus-circle me-2" style="font-size:1.1rem;"></i>' +
                        '<span>' + state.text + '</span></div>');
                }
                return state.text;
            }

            if ($('#customer_id').hasClass('select2-hidden-accessible')) {
                $('#customer_id').select2('destroy');
            }
            $('#customer_id').select2({
                width: '100%',
                placeholder: 'Search Customer...',
                allowClear: true,
                ajax: {
                    url: "{{ route('customer.search') }}",
                    dataType: 'json',
                    delay: 300,
                    cache: true,
                    data: function (params) { return { search: params.term || '', page: params.page || 1 }; },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        let results = data.results;
                        if (params.page === 1 && !isEditMode) {
                            results.unshift({ id: 'NEW', text: 'Create New Customer' });
                        }
                        return { results: results, pagination: { more: data.pagination ? data.pagination.more : false } };
                    }
                },
                templateResult: formatCustomerOption
            });

            $('#customer_id').on('change', function () {
                if (isFormPopulating) return;
                const customerId = $(this).val();
                if (!customerId) {
                    $('#email, #phone').val('');
                    $('#city_id').val(null).trigger('change');
                    $('#state_id, #state_name, #country_id, #country_name').val('');
                    setTimeout(function () {
                        loadQuotationsForCustomer(null);
                    }, 50);
                    return;
                }
                if (customerId === 'NEW') {
                    $('#customer_id').val(null).trigger('change');
                    $('#createCustomerForm')[0].reset();
                    $('#modal_city_id').val(null).trigger('change');
                    $('#modal_state_id, #modal_country_id').html('');
                    $('#createCustomerModal').modal('show');
                    return;
                }

                $.get("{{ route('customer.details', ':id') }}".replace(':id', customerId), function (res) {
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

                setTimeout(function () {
                    loadQuotationsForCustomer(customerId);
                }, 50);
            });

            function loadQuotationsForCustomer(customerId) {
                console.log("loadQuotationsForCustomer called with customerId:", customerId);
                $('#quotation_id').val(null).trigger('change', { programmatic: true });
                $('#quotation_id').select2('destroy').html('<option value="">Select Quotation</option>');
                $('#quotation_id').select2({
                    width: '100%',
                    placeholder: 'Select Quotation',
                    allowClear: true,
                    ajax: {
                        url: CONFIG.urls.quotations,
                        dataType: 'json',
                        delay: 300,
                        data: function (params) {
                            console.log("Fetching quotations for customer ID:", customerId, "Search query:", params.term);
                            return { customer_id: customerId, search: params.term || '' };
                        },
                        processResults: function (data) {
                            console.log("Fetched quotations results:", data);
                            return { results: data.results };
                        }
                    }
                });
            }

            $('#quotation_id').on('change', function (e, data) {
                if (isFormPopulating) return;
                const id = $(this).val();
                if (!id) {
                    STATE.items = [];
                    Handlers.renderTable();

                    if (data && data.programmatic) {
                        return;
                    }
                    
                    $('#invoice_type').val('domestic').trigger('change');
                    $('#currency').val('INR').trigger('change');
                    $('#exchange_rate').val(1).trigger('input');
                    $('#includeGst').prop('checked', false).trigger('change');
                    
                    $('#buyer_order_no').val('');
                    $('#buyer_order_date').val('');
                    $('#delivery_note').val('');
                    $('#po_reference').val('');
                    
                    $('#terms_payment').val('');
                    $('#terms_select').val(null).trigger('change.select2');
                    
                    $('#assigned_to').val(null).trigger('change');
                    
                    $('#advance_type_val').val('percentage'); $('.advance-type-btn').removeClass('active'); $('.advance-type-btn[data-type="percentage"]').addClass('active');
                    $('#advance_input').val(0);
                    $('#advance_amount').val(0);
                    $('#advance_pct').val(0);
                    $('#balance_amount').val(0);
                    $('#balance_pct').val(0);
                    $('#advance_hint').text('');
                    
                    const customerId = $('#customer_id').val();
                    if (customerId && customerId !== 'NEW') {
                        $.get("{{ route('customer.details', ':id') }}".replace(':id', customerId), function (res) {
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
                    } else {
                        $('#email, #phone').val('');
                        $('#city_id').val(null).trigger('change');
                        $('#state_id, #state_name, #country_id, #country_name').val('');
                    }
                    return;
                }
                STATE.loadingRevision = true;
                $.get(CONFIG.urls.quotationItems.replace(':id', id), function (res) {
                    if (res.status && res.items) {
                        STATE.items = res.items;
                        if (res.quotation) {
                            const q = res.quotation;
                            $('#invoice_type').val(q.quotation_type).trigger('change');
                            $('#currency').val(q.currency).trigger('change');
                            $('#exchange_rate').val(q.exchange_rate).trigger('input');
                            $('#includeGst').prop('checked', (parseInt(q.include_gst) === 1 || q.gst_type === 'with_gst')).trigger('change');
                            
                            // Auto fill buyer order no and date
                            $('#buyer_order_no').val(q.quotation_no || '');
                            $('#buyer_order_date').val(q.quotation_date || '');
                            
                            // Keep Terms & Conditions empty when quotation is selected
                            $('#terms_payment').val('');
                            $('#terms_payment_text').val('');
                            $('#terms_select').val(null).trigger('change.select2');

                            if (q.city_id && q.city_name) {
                                UI.setSelect2('#city_id', q.city_id, q.city_name);
                                $('#state_id').val(q.state_id || '');
                                $('#state_name').val(q.state_name || '');
                                $('#country_id').val(q.country_id || '');
                                $('#country_name').val(q.country_name || '');
                            }
                            if (q.assigned_to) {
                                $('#assigned_to').val(q.assigned_to).trigger('change');
                            }
                            if (q.advance_payment_percentage > 0) {
                                 $('#advance_type_val').val('percentage'); $('.advance-type-btn').removeClass('active'); $('.advance-type-btn[data-type="percentage"]').addClass('active');
                                 $('#advance_input').val(parseFloat(parseFloat(q.advance_payment_percentage).toFixed(2)));
                             } else if (q.advance_payment > 0) {
                                 $('#advance_type_val').val('amount'); $('.advance-type-btn').removeClass('active'); $('.advance-type-btn[data-type="amount"]').addClass('active');
                                 $('#advance_input').val(parseFloat(parseFloat(q.advance_payment).toFixed(2)));
                             }
                        }
                        Handlers.renderTable();
                    }
                    STATE.loadingRevision = false;
                });
            });

            $('#includeGst').on('change', function () {
                Handlers.updateLivePreview();
                Handlers.renderTable();
            });

            UI.initAjaxSelect2('#city_id', CONFIG.urls.cities, "Search City...", data => $.map(data.data, obj => ({ id: obj.city_id, text: obj.city_name })));
            $('#quotation_id').select2({ width: '100%', placeholder: 'Select Quotation', allowClear: true });
            $('#invoice_type').select2({ placeholder: "Select Invoice Type" }).on('change', function () {
                const type = $(this).val();
                filterStaffByType(type);
                const isExport = type === 'export';
                if (isExport) {
                    $('#includeGst').prop('checked', false).prop('disabled', true).trigger('change');
                } else {
                    $('#includeGst').prop('disabled', false);
                }
                Handlers.updateLivePreview();
                Handlers.renderTable();
            });
            $('#currency').select2({ placeholder: "Select Currency" });
            $('#assigned_to').select2({ placeholder: "Select Staff", allowClear: true });
            // Advance amount toggle buttons
            $(document).on('click', '.advance-type-btn', function () {
                $('.advance-type-btn').removeClass('active');
                $(this).addClass('active');
                const mode = $(this).data('type');
                $('#advance_type_val').val(mode);
                // Reset validation state when switching mode
                $('#advance_input').removeClass('is-invalid is-valid');
                $('#advance_hint').html('');
                const grand = parseFloat($('#hidden_grand_total').val()) || 0;
                const currentAdvAmt = parseFloat($('#advance_amount').val()) || 0;
                const currentAdvPct = parseFloat($('#advance_pct').val()) || 0;
                if (mode === 'percentage') {
                    $('#advance_input').val(currentAdvPct > 0 ? parseFloat(currentAdvPct.toFixed(2)) : 0);
                } else {
                    $('#advance_input').val(currentAdvAmt > 0 ? parseFloat(currentAdvAmt.toFixed(2)) : 0);
                }
                Handlers.updateAdvanceAndBalance(grand);
            });
            filterStaffByType($('#invoice_type').val());
            $('#terms_select').select2({ placeholder: "-- Apply Terms Template --", allowClear: true });
            $('#cart_category_select').select2({ placeholder: "All Categories", allowClear: true });
            $('#cart_product_select').select2({ placeholder: "Select a product...", allowClear: true });
            $('#invoice_date').flatpickr({ dateFormat: 'd-m-Y' });
            $('#buyer_order_date').flatpickr({ dateFormat: 'd-m-Y' });

            // Store all products as a master list for filtering
            const masterProducts = [];
            $('#cart_product_select option').each(function () {
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

            // Filter products based on Category select
            $('#cart_category_select').on('change', function () {
                const catId = $(this).val();
                const $prodSelect = $('#cart_product_select');
                $prodSelect.empty().append('<option></option>');

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

                $prodSelect.val('').trigger('change.select2');
            });

            // Update inputs when product select changes
            $('#cart_product_select').on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const productId = $(this).val();
                if (!productId) {
                    STATE.current = null;
                    $('#unit_price').val(0);
                    $('#item_gst_rate').val(0);
                    $('#item_description').val('');
                    $('#discount_percentage_container').addClass('d-none');
                    $('#discount_price_container').addClass('d-none');
                    Handlers.updateLivePreview();
                    return;
                }

                const price = parseFloat(selectedOption.attr('data-price')) || 0;
                $('#unit_price').val(price);

                const gst = parseFloat(selectedOption.attr('data-gst')) || 0;
                $('#item_gst_rate').val(gst);

                const desc = selectedOption.attr('data-description') || '';
                const name = selectedOption.attr('data-name') || selectedOption.text().trim();
                $('#item_description').val(desc === '-' ? '' : desc);

                STATE.current = {
                    id: productId,
                    text: selectedOption.text().trim(),
                    name: name,
                    gst: gst,
                    price: price,
                    description: desc
                };

                $('#discount_percentage_container').removeClass('d-none');
                $('#discount_price_container').removeClass('d-none');
                $('#discount_percentage').val(0);
                $('#discount_price').val(0);

                Handlers.updateLivePreview();
            });

            $('#terms_select').on('change', function () {
                if (isFormPopulating) return;
                const selectedOptions = $(this).find('option:selected');
                let text = '';
                const groups = {};
                
                selectedOptions.each(function () {
                    const moduleName = $(this).data('module') || 'General Terms';
                    const termText = $(this).data('terms') || '';
                    if (!groups[moduleName]) {
                        groups[moduleName] = [];
                    }
                    groups[moduleName].push(termText);
                });
                
                Object.keys(groups).forEach(moduleName => {
                    text += `${moduleName}:\n`;
                    groups[moduleName].forEach(term => {
                        text += `- ${term}\n`;
                    });
                    text += `\n`;
                });
                
                $('#terms_payment_text').val(text.trim());
            });

            $('#invoice_files').on('change', function () {
                const container = $('#new_attachments_container').empty();
                if (this.files && this.files.length > 0) {
                    container.append('<label class="form-label fs-11 fw-semibold text-muted text-uppercase mb-1">New Attachments</label>');
                    const ul = $('<ul class="list-group list-group-flush fs-12 mb-0"></ul>');
                    Array.from(this.files).forEach(file => {
                        ul.append(`<li class="list-group-item py-1 px-2 border rounded mb-1 bg-white"><i class="fas fa-file-alt me-1 text-muted"></i>${file.name}</li>`);
                    });
                    container.append(ul);
                }
            });

            const Handlers = {
                clearLocation() {
                    $('#state_id, #state_name, #country_id, #country_name').val('');
                    Handlers.calculateTotals();
                },
                resetProductConfig() {
                    if (STATE.resetting) return;
                    STATE.resetting = true;
                    STATE.current = null;
                    $('#cart_product_select').val(null).trigger('change');
                    $('#item_qty').val(1);
                    $('#discount_percentage_container, #discount_price_container').addClass('d-none');
                    $('#item_gst_select').val('').trigger('change');
                    $('#unit_price, #discount_percentage, #discount_price, #item_gst_rate').val('');
                    $('#item_description').val('');
                    STATE.resetting = false;
                    Handlers.updateLivePreview();
                },
                updateLivePreview() {
                    const qty = parseFloat($('#item_qty').val()) || 0;
                    const price = parseFloat($('#unit_price').val()) || 0;
                    let discountPrice = parseFloat($('#discount_price').val()) || 0;
                    const isExport = $('#invoice_type').val() === 'export';
                    const includeGst = $('#includeGst').is(':checked');
                    let gstPercent = 0;
                    if (!isExport && includeGst) {
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
                        $('#live_gst').text(UI.currency(gstAmount));
                        $('#live_total').text(UI.currency(total));
                    } else {
                        const convUnitTotal = unitTotal * excRate;
                        const convGst = gstAmount * excRate;
                        const convTot = total * excRate;
                        $('#live_unit_total').html(`${UI.currency(unitTotal)}<br><small class="text-muted">(${curr} ${convUnitTotal.toFixed(2)})</small>`);
                        $('#live_gst').html(`${UI.currency(gstAmount)}<br><small class="text-muted">(${curr} ${convGst.toFixed(2)})</small>`);
                        $('#live_total').html(`${UI.currency(total)}<br><small class="text-muted">(${curr} ${convTot.toFixed(2)})</small>`);
                    }
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
                    Handlers.clampDiscount();
                    const qty = parseInt($('#item_qty').val(), 10);
                    if (!qty || qty <= 0) return UI.warn('Enter valid positive integer quantity.', '#item_qty');
                    const p = parseFloat($('#unit_price').val()) || 0;
                    if (p <= 0) return UI.warn('Unit Price must be > 0.', '#unit_price');
                    const description = $('#item_description').val().trim();
                    const isExport = $('#invoice_type').val() === 'export';
                    const includeGst = $('#includeGst').is(':checked');
                    let gstPrc = 0, gstNm = '';
                    if (!isExport) {
                        gstPrc = parseFloat($('#item_gst_rate').val()) || 0;
                        if (includeGst) {
                            gstNm = gstPrc > 0 ? gstPrc + '%' : '';
                        }
                    }
                    let discPrc = parseFloat($('#discount_percentage').val()) || 0;
                    let discPrice = parseFloat($('#discount_price').val()) || 0;
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
                        it.product_gst = gstPrc;
                        it.gst_amount = gstAmt;
                        it.total = sub + gstAmt;
                        it.discount_percentage = discPrc;
                        it.discount_price = discPrice;
                        STATE.editingIndex = null;
                        $('#editFormButtons').hide();
                        $('#addItemBtn').show();
                    } else {
                        const existing = STATE.items.findIndex(x => x.product_id === STATE.current.id);
                        if (existing > -1) {
                            const it = STATE.items[existing];
                            it.qty += qty;
                            it.unit_price = p;
                            it.description = description;
                            it.gst_name = gstNm;
                            it.gst_percent = gstPrc;
                            it.product_gst = gstPrc;
                            it.discount_percentage = discPrc;
                            it.discount_price = discPrice;
                            const itDiscountedPrice = Math.max(0, p - discPrice);
                            it.gst_amount = (itDiscountedPrice * it.qty) * (gstPrc / 100);
                            it.total = (itDiscountedPrice * it.qty) + it.gst_amount;
                        } else {
                            STATE.items.push({
                                product_id: STATE.current.id,
                                product_name: STATE.current.text,
                                description: description,
                                qty,
                                unit_price: p,
                                gst_name: gstNm,
                                gst_percent: gstPrc,
                                product_gst: gstPrc,
                                gst_amount: gstAmt,
                                total: sub + gstAmt,
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
                    const isExport = $('#invoice_type').val() === 'export';
                    const includeGst = $('#includeGst').is(':checked');
                    const curr = $('#currency').val();
                    const excRate = parseFloat($('#exchange_rate').val()) || 1;
                    const showConv = curr !== 'INR';
                    const truncateWords = (text, limit = 50) => {
                        if (!text) return '';
                        const words = String(text).trim().split(/\s+/);
                        return words.length > limit ? words.slice(0, limit).join(' ') + '...' : String(text);
                    };

                    STATE.items.forEach((it, i) => {
                        const masterGst = parseFloat(it.product_gst) || parseFloat(it.gst_percent) || 0;
                        const itGstPrc = (!isExport && includeGst && masterGst > 0) ? masterGst : 0;
                        const discountedUnitPrice = Math.max(0, it.unit_price - it.discount_price);
                        const itemSub = discountedUnitPrice * it.qty;
                        const itGstAmt = itemSub * (itGstPrc / 100);
                        const itTotal = itemSub + itGstAmt;
                        it.gst_percent = itGstPrc;
                        it.gst_amount = itGstAmt;
                        it.total = itTotal;

                        const formatCol = (val) => {
                            if (!showConv) return UI.currency(val);
                            return `${UI.currency(val)}<br><small class="text-muted">(${curr} ${(val * excRate).toFixed(2)})</small>`;
                        };

                        const gst = itGstPrc > 0 ? `${itGstPrc}%<br><small class="text-muted">(${formatCol(itGstAmt)})</small>` : '0';
                        const discountDisplay = (it.discount_price > 0) ? `${parseFloat(it.discount_percentage).toFixed(2)}%<br><small class="text-muted">(${formatCol(it.discount_price)})</small>` : '0';
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
                            <td class="text-end">${discountDisplay}</td>
                            <td class="text-end">${gst}</td>
                            <td class="text-end">${formatCol(it.total)}</td>
                            <td class="text-center text-nowrap">
                                <button class="btn btn-sm btn-outline-secondary update-item me-2" data-index="${i}" ${disabledAttr}><i class="fa-solid fa-pen"></i></button>
                                <button class="btn btn-sm btn-outline-danger remove-item" data-index="${i}" ${disabledAttr}><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    `);
                    });
                    Handlers.calculateTotals();
                },
                updateAdvanceAndBalance(grandTotal) {
                    const grand = (grandTotal !== undefined) ? grandTotal : (parseFloat($('#hidden_grand_total').val()) || 0);
                    const mode = $('#advance_type_val').val() || 'percentage';
                    const rawVal = parseFloat($('#advance_input').val()) || 0;
                    const $input = $('#advance_input');
                    const $hint = $('#advance_hint');

                    let advAmt = 0;
                    let advPct = 0;
                    let isValid = true;

                    if (mode === 'percentage') {
                        if (rawVal > 100) {
                            $input.addClass('is-invalid').removeClass('is-valid');
                            $hint.html('<span class="text-danger">⚠ Percentage cannot exceed 100%</span>');
                            isValid = false;
                        } else if (rawVal < 0) {
                            $input.addClass('is-invalid').removeClass('is-valid');
                            $hint.html('<span class="text-danger">⚠ Value cannot be negative</span>');
                            isValid = false;
                        } else {
                            $input.removeClass('is-invalid').addClass('is-valid');
                            advPct = rawVal;
                            advAmt = (grand * advPct) / 100;
                            $hint.html(grand > 0 ? `<span class="text-success">= ${UI.currency(advAmt)}</span>` : '');
                        }
                    } else {
                        if (rawVal > grand && grand > 0) {
                            $input.addClass('is-invalid').removeClass('is-valid');
                            $hint.html(`<span class="text-danger">⚠ Cannot exceed Grand Total (${UI.currency(grand)})</span>`);
                            isValid = false;
                        } else if (rawVal < 0) {
                            $input.addClass('is-invalid').removeClass('is-valid');
                            $hint.html('<span class="text-danger">⚠ Value cannot be negative</span>');
                            isValid = false;
                        } else {
                            $input.removeClass('is-invalid').addClass('is-valid');
                            advAmt = rawVal;
                            advPct = grand > 0 ? (advAmt / grand) * 100 : 0;
                            $hint.html(grand > 0 ? `<span class="text-success">= ${advPct.toFixed(2)}%</span>` : '');
                        }
                    }

                    const curr = $('#currency').val();
                    const excRate = parseFloat($('#exchange_rate').val()) || 1;
                    const showConv = curr !== 'INR';
                    const $balHint = $('#balance_hint');

                    if (isValid) {
                        const balAmt = Math.max(0, grand - advAmt);
                        const balPct = grand > 0 ? Math.max(0, 100 - advPct) : 0;
                        $('#advance_amount').val(advAmt.toFixed(2));
                        $('#advance_pct').val(advPct.toFixed(2));
                        $('#balance_amount').val(balAmt.toFixed(2));
                        $('#balance_pct').val(balPct.toFixed(2));

                        if (showConv && balAmt > 0) {
                            const convBal = balAmt * excRate;
                            $balHint.html(`= ${curr} ${convBal.toFixed(2)}`);
                        } else {
                            $balHint.html('');
                        }
                    } else {
                        $balHint.html('');
                    }
                },
                calculateTotals() {
                    const isExport = $('#invoice_type').val() === 'export';
                    const includeGst = $('#includeGst').is(':checked');
                    let sub = 0, gstTot = 0, discountTot = 0;
                    STATE.items.forEach(it => {
                        const discountedUnitPrice = Math.max(0, it.unit_price - it.discount_price);
                        const itemSub = discountedUnitPrice * it.qty;
                        sub += itemSub;
                        discountTot += (parseFloat(it.discount_price) || 0) * it.qty;
                        const masterGst = parseFloat(it.product_gst) || parseFloat(it.gst_percent) || 0;
                        const itGstPrc = (!isExport && includeGst && masterGst > 0) ? masterGst : 0;
                        it.gst_percent = itGstPrc;
                        it.gst_amount = itemSub * (itGstPrc / 100);
                        it.total = itemSub + it.gst_amount;
                        gstTot += it.gst_amount;
                    });
                    const sId = parseInt($('#state_id').val()) || 0;
                    const grand = sub + gstTot;
                    $('#hidden_subtotal').val(sub.toFixed(2));
                    $('#hidden_gst_total').val(gstTot.toFixed(2));
                    $('#hidden_grand_total').val(grand.toFixed(2));
                    $('#hidden_discount_total').val(discountTot.toFixed(2));
                    $('#pay_grand_total').val(grand.toFixed(2));

                    Handlers.updateAdvanceAndBalance(grand);

                    const advAmt = parseFloat($('#advance_amount').val()) || 0;
                    const balAmt = parseFloat($('#balance_amount').val()) || 0;

                    const curr = $('#currency').val();
                    const excRate = parseFloat($('#exchange_rate').val()) || 1;
                    const formatVal = (val) => {
                        if (curr === 'INR') return UI.currency(val);
                        return `${UI.currency(val)}<br><small class="text-muted">(${curr} ${(val * excRate).toFixed(2)})</small>`;
                    };

                    let footer = `<tr class="table-active"><td colspan="7" class="text-end fw-bold">Sub Total:</td><td class="text-end fw-bold">${formatVal(sub)}</td></tr>`;
                    if (discountTot > 0) {
                        footer += `<tr class="table-active"><td colspan="7" class="text-end fw-bold text-danger">Discount:</td><td class="text-end fw-bold text-danger">-${formatVal(discountTot)}</td></tr>`;
                    }
                    if (!isExport && gstTot > 0) {
                        if (sId === CONFIG.STATE_ID) {
                            footer += `<tr class="table-active"><td colspan="7" class="text-end fw-bold">CGST:</td><td class="text-end fw-bold">${formatVal(gstTot / 2)}</td></tr>
                                   <tr class="table-active"><td colspan="7" class="text-end fw-bold">SGST:</td><td class="text-end fw-bold">${formatVal(gstTot / 2)}</td></tr>`;
                        } else {
                            footer += `<tr class="table-active"><td colspan="7" class="text-end fw-bold">IGST:</td><td class="text-end fw-bold">${formatVal(gstTot)}</td></tr>`;
                        }
                    }
                    footer += `<tr class="table-active"><td colspan="7" class="text-end fw-bold">Grand Total:</td><td class="text-end fw-bold fs-5 text-primary">${formatVal(grand)}</td></tr>`;
                    if (advAmt > 0) {
                        footer += `<tr class="table-active"><td colspan="7" class="text-end fw-semibold text-danger">Advance Payment:</td><td class="text-end fw-bold text-danger">-${formatVal(advAmt)}</td></tr>`;
                        footer += `<tr class="table-active"><td colspan="7" class="text-end fw-bold text-success fs-14">Balance Payment:</td><td class="text-end fw-bold fs-15 text-success">${formatVal(balAmt)}</td></tr>`;
                    }
                    $('#itemListFooter').html(footer);
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

            $('#city_id').on('change', function () {
                const id = $(this).val();
                if (!id) {
                    Handlers.clearLocation();
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
                                Handlers.clearLocation();
                            }
                            Handlers.calculateTotals();
                        });
                    }
                }).fail(function () {
                    Handlers.clearLocation();
                });
            });



            $('#preset_descriptions').on('change', function () {
                const val = $(this).val();
                if (val) {
                    const currentDesc = $('#item_description').val().trim();
                    if (currentDesc) {
                        $('#item_description').val(currentDesc + '\n' + val);
                    } else {
                        $('#item_description').val(val);
                    }
                    $(this).val('').trigger('change.select2');
                }
            });

            $('#addItemBtnEl').on('click', Handlers.processItemAddition);
            $('#btnUpdateProduct').on('click', Handlers.processItemAddition);

            $(document).on('click', '.remove-item', function () {
                STATE.items.splice($(this).data('index'), 1);
                Handlers.renderTable();
            });

            $(document).on('click', '.update-item', function () {
                const idx = $(this).data('index');
                const item = STATE.items[idx];
                if (!item) return;
                STATE.editingIndex = idx;
                $('#addItemBtn').hide();
                $('#editFormButtons').show();
                $('.update-item, .remove-item').prop('disabled', true);
                const optionData = STATE.cache[item.product_id] || {
                    id: item.product_id,
                    text: item.product_name,
                    gst: item.gst_percent,
                    price: item.unit_price
                };
                STATE.cache[item.product_id] = optionData;
                STATE.current = optionData;
                UI.setSelect2('#cart_product_select', item.product_id, item.product_name);
                $('#item_qty').val(item.qty);
                $('#item_description').val(item.description || '');
                $('#unit_price').val(item.unit_price);
                $('#item_gst_rate').val(item.gst_percent || 0);
                $('#discount_percentage_container').removeClass('d-none');
                $('#discount_price_container').removeClass('d-none');
                $('#discount_percentage').val(item.discount_percentage || 0);
                $('#discount_price').val(item.discount_price || 0);
                Handlers.syncDiscount('price');
                Handlers.updateLivePreview();
                Handlers.renderTable();
            });

            $('#cancelEditBtn').on('click', function () {
                STATE.editingIndex = null;
                $('#editFormButtons').hide();
                $('#addItemBtn').show();
                Handlers.resetProductConfig();
                Handlers.renderTable();
            });

            $(document).on('input', '#discount_percentage', function () { Handlers.syncDiscount('percentage'); });
            $(document).on('input', '#discount_price', function () { Handlers.syncDiscount('price'); });
            $(document).on('input', '#unit_price', function () { Handlers.syncDiscount('percentage'); });
            $(document).on('input change', '#item_qty, #unit_price, #item_gst_rate, #item_gst_select, #cart_product_select, #discount_percentage, #discount_price', Handlers.updateLivePreview);

            $(document).on('input change', '#advance_input', function () {
                // Strip negative values instantly
                if (parseFloat(this.value) < 0) this.value = 0;
                Handlers.updateAdvanceAndBalance();
            });

            // Block minus (-), plus (+), e/E keys in advance input (not allowed in number fields semantically)
            $(document).on('keydown', '#advance_input', function (e) {
                if (e.key === '-' || e.key === '+' || e.key === 'e' || e.key === 'E') {
                    e.preventDefault();
                }
            });

            $('#invoice_type').on('change', function () {
                Handlers.updateLivePreview();
                Handlers.renderTable();
            });

            $('#currency').on('change', function () {
                const curr = $(this).val();
                $('#targetCurrencyDisplay').text(curr);
                if (STATE.loadingRevision) return;
                if (curr === 'INR') {
                    $('#invoice_type').val('domestic').trigger('change');
                    $('#exchange_rate').val(1).prop('readonly', true).trigger('input');
                    return;
                }
                $('#invoice_type').val('export').trigger('change');
                $('#exchange_rate').prop('readonly', false);
            });

            // Advance Amount input → auto-recalculate balance
            $('#advance_amount').on('input', function () {
                const grand = parseFloat($('#hidden_grand_total').val()) || 0;
                const adv = Math.max(0, parseFloat($(this).val()) || 0);
                const bal = Math.max(0, grand - adv);
                $('#balance_amount').val(bal.toFixed(2));
                // Update footer
                Handlers.calculateTotals();
            });

            function validateForm() {
                if (!$('#customer_id').val()) { UI.warn('Please select a customer.', '#customer_id'); return false; }
                if (!$('#invoice_date').val()) { UI.warn('Please enter invoice date.', '#invoice_date'); return false; }
                if (!$('#invoice_type').val()) { UI.warn('Please select invoice type.', '#invoice_type'); return false; }
                if (!$('#currency').val()) { UI.warn('Please select currency.', '#currency'); return false; }
                if (!$('#exchange_rate').val() || parseFloat($('#exchange_rate').val()) <= 0) { UI.warn('Please enter exchange rate.', '#exchange_rate'); return false; }
                if (!$('#email').val()) { UI.warn('Please enter email address.', '#email'); return false; }
                if (!$('#phone').val()) { UI.warn('Please enter phone number.', '#phone'); return false; }
                if (!$('#city_id').val()) { UI.warn('Please select a city.', '#city_id'); return false; }
                if (!$('#state_id').val()) { UI.warn('State is required.', '#city_id'); return false; }
                if (!$('#country_id').val()) { UI.warn('Country is required.', '#city_id'); return false; }
                if (!STATE.items.length) { UI.warn('At least one product is required.', '#cart_product_select'); return false; }
                const advAmt = parseFloat($('#advance_amount').val()) || 0;
                const advRawInput = parseFloat($('#advance_input').val()) || 0;
                const advMode = $('#advance_type_val').val() || 'percentage';
                const grand = parseFloat($('#hidden_grand_total').val()) || 0;
                if (advMode === 'percentage' && advRawInput > 100) {
                    UI.warn('Advance percentage cannot exceed 100%. Please correct the Advance Amount.', '#advance_input');
                    $('#advance_input').addClass('is-invalid').focus();
                    return false;
                }
                if (advMode === 'amount' && advAmt > grand) {
                    UI.warn(`Advance amount (${UI.currency(advAmt)}) cannot exceed Grand Total (${UI.currency(grand)}).`, '#advance_input');
                    $('#advance_input').addClass('is-invalid').focus();
                    return false;
                }
                if (advAmt < 0) { UI.warn('Advance amount cannot be negative.', '#advance_input'); return false; }
                return true;
            }

            $('#invoiceForm').on('submit', function (e) {
                e.preventDefault();
                if (!validateForm()) return;
                const invoiceId = $('#invoice_id').val();
                const url = invoiceId ? CONFIG.urls.update.replace(':id', invoiceId) : CONFIG.urls.store;
                const fd = new FormData(this);
                fd.set('items', JSON.stringify(STATE.items));
                // GST always included at product rate
                fd.set('gst_exemption', '1');  // 1 = not exempted = use product GST
                fd.set('include_gst', $('#includeGst').is(':checked') ? '1' : '0');
                fd.set('advance_payment', $('#advance_amount').val() || '0');
                fd.set('balance_payment', $('#balance_amount').val() || '0');
                fd.set('advance_payment_percentage', $('#advance_pct').val() || '0');
                fd.set('balance_payment_percentage', $('#balance_pct').val() || '0');
                fd.set('send_email', $('#send_email').is(':checked') ? 1 : 0);

                const $btn = $('#pi_submit_btn');
                const originalHtml = $btn.html();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
                    },
                    success: function (res) {
                        if (res.status) {
                            UI.toast('success', 'Success', res.message);
                            resetForm();
                            if (res.data && res.data.new_invoice_no) {
                                $('#invoice_no').val(res.data.new_invoice_no);
                            }
                            invoiceTable.ajax.reload();
                            $('a[href="#invoice-list"]')[0].click();
                        } else {
                            UI.toast('error', 'Error', res.message || 'Something went wrong.');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors) {
                                let firstKey = Object.keys(errors)[0];
                                UI.toast('error', 'Validation Error', errors[firstKey][0]);
                            }
                        } else {
                            UI.toast('error', 'Error', 'Server error. Please try again.');
                        }
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });

           // Function to clear form and reset to default "New PI" mode
function resetForm() {
            isEditMode = false; 
            $('#invoice_id').val('');
            
            // Reset Tab UI
            $('#new-invoice-tab-text').text('New PI');
            $('#new-invoice-tab i').removeClass('fa-pen').addClass('fa-plus');
            
            // Reset Button UI
            $('#pi_submit_btn').html('<i class="fa-solid fa-check-circle me-1"></i>Save Invoice');
            
            // Reset Form Fields
            $('#invoiceForm')[0].reset();
            $('#customer_id').val(null).trigger('change');
            $('#quotation_id').val(null).trigger('change', { programmatic: true });
            $('#city_id').val(null).trigger('change');
            $('#assigned_to').val(null).trigger('change');
            $('#invoice_type').val('domestic').trigger('change');
            $('#includeGst').prop('disabled', false).prop('checked', false).trigger('change');
            $('#currency').val('INR').trigger('change');
            $('#exchange_rate').val(1).trigger('input');
            $('#advance_type_val').val('percentage'); $('.advance-type-btn').removeClass('active'); $('.advance-type-btn[data-type="percentage"]').addClass('active');
            $('#advance_input').val(0);
            $('#advance_amount').val(0);
            $('#advance_pct').val(0);
            $('#balance_amount').val(0);
            $('#balance_pct').val(0);
            $('#advance_hint').text('');
            $('#pay_grand_total').val(0);
            $('#state_name, #country_name, #state_id, #country_id').val('');
            
            // Reset new fields
            $('#buyer_order_no, #buyer_order_date, #destination, #delivery_note, #po_reference').val('');
            $('#terms_select').val(null).trigger('change.select2');
            $('#terms_payment_text').val('');
            $('#existing_attachments_container').empty();
            $('#new_attachments_container').empty();
            $('#invoiceForm').find('input[name="delete_attachments[]"]').remove();
            
            // Clear items and UI table
            STATE.items = [];
            Handlers.renderTable();
            Handlers.resetProductConfig();
        }

        // Reset when clicking "List" tab
        $('#list-tab').on('click', function () {
            resetForm();
        });

        // Reset when clicking "New PI" tab (if not in edit mode)
        $('a[href="#new-invoice"]').on('click', function () {
            if (isEditMode) return;
            resetForm();
        });

        // Edit button click logic
        $(document).on('click', '.edit-invoice', function () {
            const id = $(this).data('id');
            
            $.get(CONFIG.urls.edit.replace(':id', id), function (res) {
                if (res.status) {
                    const d = res.data;
                    isEditMode = true; // Activate Edit Mode
                    isFormPopulating = true; // Block event handlers during initial population
                    
                    // Update UI to "Edit" state
                    $('#new-invoice-tab-text').text('Edit PI');
                    $('#new-invoice-tab i').removeClass('fa-plus').addClass('fa-pen');
                    $('#pi_submit_btn').html('<i class="fa-solid fa-check-circle me-1"></i>Update Invoice');
                    $('#invoice_id').val(d.invoice_id);
                    
                    // Switch to form tab
                    $('a[href="#new-invoice"]').tab('show');

                    // Populate Data...
                    const cOpt = new Option(d.customer_name, d.customer_id, true, true);
                    $('#customer_id').empty().append(cOpt).trigger('change.select2');
                    loadQuotationsForCustomer(d.customer_id);

                    if (d.quotation_id && d.quotation_no) {
                        const qOpt = new Option(d.quotation_no, d.quotation_id, true, true);
                        $('#quotation_id').empty().append(qOpt).trigger('change.select2');
                    }

                    $('#invoice_no').val(d.invoice_no);
                    $('#invoice_date').val(d.invoice_date);
                    $('#email').val(d.email);
                    $('#phone').val(d.phone);

                    if (d.city_id && d.city_name) {
                        $('#city_id').html('<option value="' + d.city_id + '" selected>' + d.city_name + '</option>').trigger('change.select2');
                    }
                    $('#state_name').val(d.state_name || '');
                    $('#state_id').val(d.state_id || '');
                    $('#country_name').val(d.country_name || '');
                    $('#country_id').val(d.country_id || '');

                    $('#invoice_type').val(d.invoice_type ? d.invoice_type.toLowerCase() : 'domestic').trigger('change');
                    $('#assigned_to').val(d.assigned_to || '').trigger('change');
                    $('#currency').val(d.currency || 'INR').trigger('change');
                    $('#exchange_rate').val(d.exchange_rate || 1).trigger('input');
                    $('#includeGst').prop('checked', (d.include_gst ?? 1) == 1).trigger('change');

                    $('#terms_payment_text').val(d.terms_payment_text || '');
                    $('#delivery_note').val(d.delivery_note || d.dispatch_time || '');
                    $('#po_reference').val(d.po_reference || '');
                    $('#transportation').val(d.transportation || '');
                    $('#destination').val(d.destination || '');
                    $('#buyer_order_no').val(d.buyer_order_no || '');
                    $('#buyer_order_date').val(d.buyer_order_date || '');
                    const advPct = parseFloat(d.advance_payment_percentage || 0);
                    const advAmt = parseFloat(d.advance_payment || 0);

                    if (advPct > 0) {
                        $('#advance_type_val').val('percentage'); $('.advance-type-btn').removeClass('active'); $('.advance-type-btn[data-type="percentage"]').addClass('active');
                        $('#advance_input').val(parseFloat(advPct.toFixed(2)));
                    } else if (advAmt > 0) {
                        $('#advance_type_val').val('amount'); $('.advance-type-btn').removeClass('active'); $('.advance-type-btn[data-type="amount"]').addClass('active');
                        $('#advance_input').val(parseFloat(advAmt.toFixed(2)));
                    } else {
                        $('#advance_type_val').val('percentage'); $('.advance-type-btn').removeClass('active'); $('.advance-type-btn[data-type="percentage"]').addClass('active');
                        $('#advance_input').val(0);
                    }
                    Handlers.updateAdvanceAndBalance();

                    // Pre-select matching options in multiple select terms dropdown
                    if (d.terms_payment) {
                        const selectedTerms = d.terms_payment.split(',');
                        $('#terms_select').val(selectedTerms).trigger('change.select2');
                    } else {
                        $('#terms_select').val(null).trigger('change.select2');
                    }

                    // Render existing attachments list
                    const attachContainer = $('#existing_attachments_container').empty();
                    if (d.attachments && d.attachments.length > 0) {
                        attachContainer.append('<label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Existing Attachments</label>');
                        const ul = $('<ul class="list-group list-group-flush fs-12 mb-0"></ul>');
                        d.attachments.forEach(file => {
                            const li = $(`<li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2 border rounded mb-1 bg-white">
                                <a href="/assets/uploads/performa/attachments/${file}" target="_blank" class="text-primary"><i class="fas fa-file-alt me-1"></i>${file}</a>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-existing-file" data-file="${file}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </li>`);
                            ul.append(li);
                        });
                        attachContainer.append(ul);
                    }

                    STATE.items = d.items;
                    Handlers.renderTable();
                    isFormPopulating = false;
                }
            });
        });

        // Delete existing attachment handler
        $(document).on('click', '.delete-existing-file', function (e) {
            e.preventDefault();
            const filename = $(this).data('file');
            $(this).closest('li').remove();
            $('#invoiceForm').append(`<input type="hidden" name="delete_attachments[]" value="${filename}">`);
        });



            $('#modal_city_id').select2({
                dropdownParent: $('#createCustomerModal'),
                width: '100%',
                placeholder: '-- Select or Type City --',
                allowClear: true,
                ajax: {
                    url: "{{ route('location.cities') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ search: params.term, per_page: 20 }),
                    processResults: data => ({
                        results: $.map(data.data, item => ({ text: item.city_name, id: item.city_id }))
                    }),
                    cache: true
                }
            });

            $('#modal_city_id').on('change', function () {
                const city_id = $(this).val();
                if (city_id) {
                    $.get("{{ route('get.location.by.city') }}", { city_id: city_id }, function (res) {
                        if (res.state_id) {
                            $('#modal_state_id').html('<option value="' + res.state_id + '" selected>' + res.state_name + '</option>');
                        } else { $('#modal_state_id').html(''); }
                        if (res.country_id) {
                            $('#modal_country_id').html('<option value="' + res.country_id + '" selected>' + res.country_name + '</option>');
                        } else { $('#modal_country_id').html(''); }
                    }).fail(function () {
                        $('#modal_state_id, #modal_country_id').html('');
                    });
                } else {
                    $('#modal_state_id, #modal_country_id').html('');
                }
            });

            $(document).on('click', '.view-invoice', function () {
                const id = $(this).data('id');
                const url = CONFIG.urls.edit.replace(':id', id);
                $.get(url, function (res) {
                    if (res.status && res.data) {
                        const d = res.data;
                        $('#view_invoice_no').text(d.invoice_no);
                        $('#view_customer_name').text(d.customer_name);
                        $('#view_invoice_date').text(d.invoice_date);
                        $('#view_invoice_type').text(d.invoice_type.toUpperCase());
                        $('#view_contact_info').html(`Email: <strong>${d.email}</strong><br>Phone: <strong>${d.phone}</strong>`);
                        $('#view_currency_info').html(`Currency: <strong>${d.currency}</strong><br>Exchange Rate: <strong>1 INR = ${d.exchange_rate} ${d.currency}</strong>`);
                        $('#view_gst_inclusion').html(d.include_gst == 1 ? '<span class="badge bg-success">GST Included</span>' : '<span class="badge bg-secondary">Without GST</span>');
                        if (d.include_gst == 1) {
                            $('#view_gst_inclusion_wrapper').show();
                            $('#view_summary_gst_row').addClass('d-flex').removeClass('d-none');
                            $('#view_summary_grand_row').addClass('d-flex').removeClass('d-none');
                        } else {
                            $('#view_gst_inclusion_wrapper').hide();
                            $('#view_summary_gst_row').addClass('d-none').removeClass('d-flex');
                            $('#view_summary_grand_row').addClass('d-none').removeClass('d-flex');
                        }

                        let otherInfo = '';
                        if (d.buyer_order_no) otherInfo += `<strong>Buyer Order No:</strong> ${d.buyer_order_no}<br>`;
                        if (d.buyer_order_date) otherInfo += `<strong>Buyer Order Date:</strong> ${d.buyer_order_date}<br>`;
                        if (d.destination) otherInfo += `<strong>Destination:</strong> ${d.destination}<br>`;
                        if (d.transportation) otherInfo += `<strong>Transportation:</strong> ${d.transportation}<br>`;
                        if (d.delivery_note || d.dispatch_time) otherInfo += `<strong>Delivery Note:</strong> ${d.delivery_note || d.dispatch_time}<br>`;
                        if (d.po_reference) otherInfo += `<strong>P.O. Reference:</strong> ${d.po_reference}<br>`;
                        if (d.terms_payment_text) otherInfo += `<br><strong>Terms & Conditions:</strong><br>${d.terms_payment_text.replace(/\n/g, '<br>')}<br>`;
                        
                        if (d.attachments && d.attachments.length > 0) {
                            otherInfo += `<br><br><strong>Attachments:</strong><br>`;
                            d.attachments.forEach(file => {
                                otherInfo += `<a href="/assets/uploads/performa/attachments/${file}" target="_blank" class="badge bg-light text-primary border me-1 my-1"><i class="fas fa-file-alt me-1"></i>${file}</a>`;
                            });
                        }
                        if (!otherInfo) otherInfo = '<span class="text-muted">No additional details available.</span>';
                        $('#view_other_info').html(otherInfo);

                        const symbol = d.currency === 'INR' ? '₹' : d.currency + ' ';
                        const convert = (val) => {
                            if (d.currency === 'INR') return UI.currency(val);
                            return `${UI.currency(val)} (${d.currency} ${(val * d.exchange_rate).toFixed(2)})`;
                        };
                        $('#summary_subtotal').text(convert(d.subtotal));
                        $('#summary_discount').text('-' + convert(d.discount_total));
                        $('#summary_gst').text(convert(d.gst_total));
                        $('#summary_grand').text(convert(d.grand_total));
                        $('#summary_advance').text(convert(d.advance_payment) );
                        $('#summary_balance').text(convert(d.balance_payment));

                        const $body = $('#viewItemsBody').empty();
                        if (d.items && d.items.length > 0) {
                            d.items.forEach((it, i) => {
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
                                        <td class="text-end view_gst_col">${it.gst_percent > 0 ? `${it.gst_percent}% (${convert(it.gst_amount)})` : '-'}</td>
                                        <td class="text-end fw-bold text-success">${convert(it.total)}</td>
                                    </tr>
                                `);
                            });
                            if (d.include_gst == 1) {
                                $('.view_gst_col').show();
                            } else {
                                $('.view_gst_col').hide();
                            }
                        } else {
                            $body.append(`<tr><td colspan="${d.include_gst == 1 ? 7 : 6}" class="text-center text-muted">No items found.</td></tr>`);
                        }

                        $('#view_pdf_btn').attr('href', d.pdf_url || '');
                    }
                });
            });

            $('#btnSaveCustomer').click(function () {
                let $btn = $(this);
                let form = $('#createCustomerForm')[0];
                if (!form.checkValidity()) {
                    let firstInvalid = $(form).find(':invalid').first();
                    let fieldName = firstInvalid.closest('.mb-3').find('label').text().replace('*', '').trim() || 'required';
                    UI.toast('warning', 'Validation', 'Please fill the ' + fieldName + ' field.');
                    firstInvalid.focus();
                    return;
                }
                $btn.prop('disabled', true).text('Saving...');
                let formData = new FormData(form);
                $.ajax({
                    url: "{{ route('ajax.customer.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status) {
                            UI.toast('success', 'Success', response.message);
                            $('#createCustomerModal').modal('hide');
                            let newOption = new Option(response.customer.text, response.customer.id, true, true);
                            $('#customer_id').append(newOption).trigger('change');
                        } else {
                            UI.toast('error', 'Error', response.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let firstKey = Object.keys(errors)[0];
                            UI.toast('error', 'Validation Error', errors[firstKey][0]);
                            $('#createCustomerForm [name="' + firstKey + '"]').focus();
                        } else {
                            UI.toast('error', 'Error', 'An error occurred.');
                        }
                    },
                    complete: function () {
                        $btn.prop('disabled', false).text('Save Customer');
                    }
                });
            });

            // Save new terms from modal via AJAX
            $('#btnSaveTerms').click(function () {
                let $btn = $(this);
                let form = $('#createTermsForm')[0];
                if (!form.checkValidity()) {
                    let firstInvalid = $(form).find(':invalid').first();
                    let fieldName = firstInvalid.closest('.mb-3').find('label').text().replace('*', '').trim() || 'required';
                    UI.toast('warning', 'Validation', 'Please fill the ' + fieldName + ' field.');
                    firstInvalid.focus();
                    return;
                }
                $btn.prop('disabled', true).text('Saving...');
                let formData = new FormData(form);
                $.ajax({
                    url: "{{ route('terms.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 'success') {
                            UI.toast('success', 'Success', response.message);
                            $('#createTermsModal').modal('hide');
                            form.reset();
                            
                            // Dynamically append new terms to terms_select dropdown
                            if (response.created_terms && response.created_terms.length > 0) {
                                response.created_terms.forEach(term => {
                                    let newOption = new Option(term.module || 'General Terms', term.term_id, false, false);
                                    $(newOption).attr('data-terms', term.terms);
                                    $(newOption).attr('data-module', term.module || 'General Terms');
                                    $('#terms_select').append(newOption);
                                });
                                $('#terms_select').trigger('change.select2');
                            }
                        } else {
                            UI.toast('error', 'Error', response.message || 'Failed to save terms.');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let firstKey = Object.keys(errors)[0];
                            UI.toast('error', 'Validation Error', errors[firstKey][0]);
                        } else {
                            UI.toast('error', 'Error', 'An error occurred.');
                        }
                    },
                    complete: function () {
                        $btn.prop('disabled', false).text('Save Terms');
                    }
                });
            });

            resetForm();
        });
    </script>
    @include('company.invoice.performa.partials.history')
</body>

</html>
