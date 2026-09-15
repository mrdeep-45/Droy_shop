<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        /* Advanced Filter Panel Styling */
        #advancedFilters .p-3 {
            border: 1px solid rgba(0, 69, 79, 0.15) !important;
            background-color: rgba(0, 69, 79, 0.01) !important;
        }

        #btnAdvancedFilters.btn-info {
            background-color: #00454f !important;
            border-color: #00454f !important;
            color: #ffffff !important;
        }

        #btnAdvancedFilters.btn-info:hover,
        #btnAdvancedFilters.btn-info:focus {
            background-color: #00323a !important;
            border-color: #00323a !important;
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
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div class="card-title text-primary mb-0" id="pageTitleLabel">
                                    {{ $page_name }}
                                </div>
                               <div class="d-flex flex-wrap align-items-center gap-2 justify-content-md-end">
                                    <div class="d-flex align-items-center flex-wrap gap-2" id="listToolbar">
                                        <button class="btn btn-info btn-sm fw-bold shadow-sm" type="button" id="btnAdvancedFilters">
                                            <i class="fas fa-filter me-1"></i><span>Advanced Filters</span>
                                        </button>
                                        @if (permissions_check('canCreate'))
                                        <button class="btn btn-success btn-sm fw-bold shadow-sm d-none" type="button" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                                            <i class="fas fa-file-excel me-1"></i><span>Import Excel</span>
                                        </button>
                                        @endif
                                    </div>
                                    <ul class="nav nav-tabs nav-tabs-footer mb-0" role="tablist">
                                        @if (permissions_check('canView'))
                                        <li class="nav-item">
                                            <a class="nav-link active" id="list-tab" data-bs-toggle="tab" role="tab" href="#product-list">
                                                List
                                            </a>
                                        </li>
                                        @endif
                                        @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" role="tab" href="#new-product" id="companyTabLabel">
                                                New Product
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                    <div class="tab-pane fade show active" id="product-list">
                                        <div class="collapse mb-3" id="advancedFilters">
                                            <div class="p-3 rounded shadow-sm">
                                                <form id="filterForm">
                                                    <div class="row g-3 align-items-end">
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-bold small text-dark">Category</label>
                                                            <select class="form-select select2" id="filter_category" name="filter_category" data-placeholder="Select Category">
                                                                <option value=""></option>
                                                                @foreach($category as $cat)
                                                                    <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 d-flex gap-2">
                                                            <button type="button" class="btn btn-dark flex-fill" id="applyFilters">
                                                                <i class="fas fa-search me-1"></i> Search
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary flex-fill" id="resetFilters">
                                                                <i class="fas fa-redo me-1"></i> Reset
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="productData" class="table table-bordered menu-submenu-data" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th style="width:1%;">#</th>
                                                        <th>Product Code</th>
                                                        <th>Product Category</th>
                                                        <th>Product Name</th>
                                                        <th>Unit</th>
                                                        <th>Rate (Rate/Amt)</th>
                                                        <th>GST (%)</th>
                                                        <th>Description</th>
                                                        <th>Created By</th>
                                                        <th>Created On</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                    <div class="tab-pane text-muted px-1" id="new-product" role="tabpanel">
                                        <form id="product_form" novalidate>
                                            @csrf
                                            <input type="hidden" name="product_id" id="product_id">

                                            <div class="modern-card">
                                                <div class="row col-md-12 mb-3">
                                                     <div class="col-md-6 mb-3">
                                                            <label for="category_id" class="form-label">Category<span class="text-danger">*</span></label>
                                                            <select class="form-select select2" name="category_id" id="category_id" >
                                                                <option value="">Select Category</option>
                                                                <option value="add_new_category">&#10133; Add New Category</option>
                                                                @foreach($category as $type)
                                                                    <option value="{{ $type->category_id }}">{{ $type->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback category_idError"></div>
                                                        </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="prod_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="prod_name" id="prod_name" required placeholder="Enter Product Name">
                                                        <div class="invalid-feedback prod_nameError"></div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="prod_amount" class="form-label">Product Rate <span class="text-danger">*</span></label>
                                                        <input type="number" step="0.01" class="form-control" name="prod_amount" id="prod_amount" required placeholder="0.00">
                                                        <div class="invalid-feedback prod_amountError"></div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="prod_desc" class="form-label">Product Description</label>
                                                        <textarea class="form-control" name="prod_desc" id="prod_desc" rows="3" placeholder="Enter Description Details..."></textarea>
                                                        <div class="invalid-feedback prod_descError"></div>
                                                    </div>
                                                     
                                                        <div class="col-md-6 mb-3">
                                                            <label for="unit_id" class="form-label">Unit</label>
                                                            <select class="form-select select2" name="unit_id" id="unit_id">
                                                                <option value="">Select Unit</option>
                                                                <option value="add_new_unit">&#10133; Add New Unit</option>
                                                                @foreach($units as $u)
                                                                    <option value="{{ $u->unit_id }}">{{ $u->unit }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback unit_idError"></div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="gst" class="form-label">GST (%)</label>
                                                            <select class="form-select select2" name="gst" id="gst">
                                                                <option value="">Select GST</option>
                                                                <option value="add_new_gst">&#10133; Add New GST</option>
                                                                @foreach($gst_rates as $rate)
                                                                    <option value="{{ $rate->gst_id }}">{{ $rate->gst_no }}%</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback gstError"></div>
                                                        </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2 mt-2">
                                                <button type="button" class="btn btn-secondary" id="resetFormBtn">Reset</button>
                                                <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
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
        <!--model -->
        <!-- Import Excel Modal -->
        <div class="modal fade" id="importExcelModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="import_excel_form" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-file-excel text-success me-2"></i>Import Products via Excel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info py-2 small">
                                <strong>Excel Format Columns:</strong><br>
                                <code>product_name</code>, <code>rate</code>, <code>category</code>, <code>unit</code>, <code>gst</code>, <code>description</code>
                            </div>
                            <div class="mb-3">
                                <label for="excel_file" class="form-label fw-bold">Select Excel File (.xlsx, .xls) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="excel_file" id="excel_file" accept=".xlsx, .xls, .csv" required>
                                <div class="invalid-feedback">Please choose a valid Excel file.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success" id="importSubmitBtn"><i class="fas fa-upload me-1"></i> Upload & Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="quickCategoryModal" tabindex="-1" aria-labelledby="quickCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="quick_category_form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="quickCategoryModalLabel">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal_category_name" class="form-label">Product category<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name[]" id="modal_category_name" required placeholder="Enter Category Name">
                            <div class="invalid-feedback" id="modal_category_nameError">Please enter a category name.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="quickUnitModal" tabindex="-1" aria-labelledby="quickUnitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="quick_unit_form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="quickUnitModalLabel">Add New Unit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal_unit_name" class="form-label">Unit Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="unit[]" id="modal_unit_name" required placeholder="Enter Unit Name (e.g., Nos, Kgs, PCS)">
                            <div class="invalid-feedback" id="modal_unit_nameError">Please enter a unit name.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="modalUnitSubmitBtn">Add Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="quickGstModal" tabindex="-1" aria-labelledby="quickGstModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="quick_gst_form">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="quickGstModalLabel">Add New GST (%)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="modal_gst_no" class="form-label">GST (%)<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="gst_no[]" id="modal_gst_no" required placeholder="Enter GST Rate Percentage">
                                <div class="invalid-feedback" id="modal_gst_noError">Please enter a GST rate.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="modalGstSubmitBtn">Add GST</button>
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

    <script>
        let editMode = false;

        function setFormToCreateMode() {
            editMode = false;
            $('#product_form').removeAttr('data-mode');
            $('#companyTabLabel').text('New Product');
            $('#submitBtn').text('Submit');
            
            $('#product_id').val('');
            $('#prod_name').val('');
            $('#prod_amount').val('');
            $('#prod_desc').val('');
            $('#category_id, #unit_id, #gst').val('').trigger('change');
            $('.select2').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        function setFormToEditMode(data) {
            editMode = true;
            $('#product_form').attr('data-mode', 'edit');
            $('#companyTabLabel').text('Edit Product');
            $('#submitBtn').text('Update');
            
            $('#product_id').val(data.product_id);
            $('#prod_name').val(data.prod_name);
            $('#prod_amount').val(data.prod_amount);
            $('#prod_desc').val(data.prod_desc);
            $('#category_id').val(data.category_id).trigger('change');
            $('#unit_id').val(data.unit_id).trigger('change');
            $('#gst').val(data.gst).trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        $(document).ready(function () {

             $('.select2').select2({
                width: '100%',
                placeholder: "Select an option",
                allowClear: true
            });
            var canUpdate = {{ permissions_check('canUpdate') ? 'true' : 'false' }};
            var canDelete = {{ permissions_check('canDelete') ? 'true' : 'false' }};
            
            var table = $('#productData').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('products.list') }}",
                    data: function (d) {
                        d.category_id = $('#filter_category').val();
                    }
                },
                autoWidth: false,
                order: [[1, 'desc']],
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, width: "5%" },
                    { data: 'prod_code', name: 'prod_code', orderable: true },
                    { data: 'category_id', name: 'category_id', orderable: true },
                    { data: 'prod_name', name: 'prod_name', orderable: true },
                    { data: 'unit_display', name: 'unit_id', orderable: true },
                    { data: 'prod_amount', name: 'prod_amount', className: 'text-end', orderable: true },
                    { data: 'gst_display', name: 'gst', className: 'text-center', orderable: true },
                    { data: 'prod_desc', name: 'prod_desc', orderable: true },
                    { data: 'created_by', name: 'created_by', orderable: true },
                    { data: 'created_at', name: 'created_at', orderable: true },
                    { data: 'action', orderable: false, searchable: false, visible: canUpdate || canDelete, className: "text-center", width: "15%" }
                ],
                responsive: true
            });
            // Advanced Filter Toggle
            $('#btnAdvancedFilters').on('click', function () {
                if ($('#list-tab').hasClass('active')) {
                    $('#advancedFilters').collapse('toggle');
                }
            });

            // Filter Apply & Reset Handlers
            $('#applyFilters').on('click', function () {
                table.ajax.reload();
            });

            $('#resetFilters').on('click', function () {
                $('#filter_category').val(null).trigger('change');
                table.ajax.reload();
            });

            // Hide/Show List Toolbar on Tab Change
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#new-product') {
                    $('#listToolbar').removeClass('d-flex').addClass('d-none');
                    if (editMode) {
                        $('#companyTabLabel').text('Edit Product');
                        $('#submitBtn').text('Update');
                    } else {
                        setFormToCreateMode();
                    }
                }
                if (target === '#product-list') {
                    $('#listToolbar').removeClass('d-none').addClass('d-flex');
                    setFormToCreateMode();
                }
            });

            // Excel Import Form Handling
            $('#import_excel_form').submit(function (e) {
                e.preventDefault();
                let formData = new FormData(this);

                $('#importSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Importing...');

                $.ajax({
                    url: "{{ route('products.import') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.status === 'success') {
                            iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                            $('#importExcelModal').modal('hide');
                            $('#import_excel_form')[0].reset();
                            table.ajax.reload();
                        } else {
                            iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Import failed', position: 'topRight' });
                    },
                    complete: function () {
                        $('#importSubmitBtn').prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Upload & Import');
                    }
                });
            });
              $('#category_id').on('change', function() {
                if ($(this).val() === 'add_new_category') {
                    $('#modal_category_name').val('');
                    $('#modal_category_name').removeClass('is-invalid');
                    $('#quickCategoryModal').modal('show');
                    $(this).val('').trigger('change.select2');
                }
            });
            $('#unit_id').on('change', function() {
                if ($(this).val() === 'add_new_unit') {
                    $('#modal_unit_name').val('');
                    $('#modal_unit_name').removeClass('is-invalid');
                    $('#quickUnitModal').modal('show');
                    $(this).val('').trigger('change.select2');
                } else if ($(this).val()) {
                    $(this).removeClass('is-invalid');
                }
            });
            $('#gst').on('change', function() {
                if ($(this).val() === 'add_new_gst') {
                    $('#modal_gst_no').val('');
                    $('#modal_gst_no').removeClass('is-invalid');
                    $('#quickGstModal').modal('show');
                    $(this).val('').trigger('change.select2');
                }
            });

             $('#quick_category_form').submit(function(e) {
                e.preventDefault();
                $('#modal_category_name').removeClass('is-invalid');
                let typeName = $('#modal_category_name').val().trim();

                if (!typeName) {
                    $('#modal_category_name').addClass('is-invalid');
                    return;
                }

                $('#modalSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    url: "{{ route('productcategory.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                            
                            let newType = response.data[0]; 
                            let option = new Option(newType.name, newType.category_id, true, true);
                            $('#category_id').append(option).trigger('change');

                            $('#quickCategoryModal').modal('hide');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errMsg = errors['name.0'] ? errors['name.0'][0] : 'Something went wrong';
                            $('#modal_category_name').addClass('is-invalid');
                            $('#modal_category_nameError').text(errMsg).show();
                        } else {
                            iziToast.error({ title: 'Error', message: 'Failed to add category.', position: 'topRight' });
                        }
                    },
                    complete: function() {
                        $('#modalSubmitBtn').prop('disabled', false).html('Add Category');
                    }
                });
            });

            $('#quick_unit_form').submit(function(e) {
                e.preventDefault();
                $('#modal_unit_name').removeClass('is-invalid');
                let unitName = $('#modal_unit_name').val().trim();

                if (!unitName) {
                    $('#modal_unit_name').addClass('is-invalid');
                    return;
                }

                $('#modalUnitSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    url: "{{ route('unit.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                            
                            let newUnit = response.data[0]; 
                            let option = new Option(newUnit.unit, newUnit.unit_id, true, true);
                            
                            $('#unit_id').append(option).trigger('change');
                            $('#quickUnitModal').modal('hide');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errMsg = errors['unit.0'] ? errors['unit.0'][0] : 'Something went wrong';
                            $('#modal_unit_name').addClass('is-invalid');
                            $('#modal_unit_nameError').text(errMsg).show();
                        } else {
                            iziToast.error({ title: 'Error', message: 'Failed to add unit.', position: 'topRight' });
                        }
                    },
                    complete: function() {
                        $('#modalUnitSubmitBtn').prop('disabled', false).html('Add Unit');
                    }
                });
            });

            $('#quick_gst_form').submit(function(e) {
                e.preventDefault();
                $('#modal_gst_no').removeClass('is-invalid');
                let gstNo = $('#modal_gst_no').val().trim();

                if (!gstNo) {
                    $('#modal_gst_no').addClass('is-invalid');
                    return;
                }

                $('#modalGstSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    url: "{{ route('gst.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                            
                            let newGst = response.data[0]; 
                            let option = new Option(newGst.gst_no + '%', newGst.gst_id, true, true);
                            
                            $('#gst').append(option).trigger('change');
                            $('#quickGstModal').modal('hide');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errMsg = errors['gst_no.0'] ? errors['gst_no.0'][0] : 'Something went wrong';
                            $('#modal_gst_no').addClass('is-invalid');
                            $('#modal_gst_noError').text(errMsg).show();
                        } else {
                            iziToast.error({ title: 'Error', message: 'Failed to add GST.', position: 'topRight' });
                        }
                    },
                    complete: function() {
                        $('#modalGstSubmitBtn').prop('disabled', false).html('Add GST');
                    }
                });
            });

            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#new-product') {
                    if (editMode) {
                        $('#companyTabLabel').text('Edit Product');
                        $('#submitBtn').text('Update');
                    } else {
                        setFormToCreateMode();
                    }
                }
                if (target === '#product-list') {
                    setFormToCreateMode();
                }
            });

            $(document).on('click', '.edit-group', function () {
                const id = $(this).data('id');
                $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');
                
                $.ajax({
                    url: "{{ route('products.edit', ':id') }}".replace(':id', id),
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            setFormToEditMode(response.data);
                            $('a[href="#new-product"]').tab('show');
                        } else {
                            iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                            setFormToCreateMode();
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Failed to fetch row data', position: 'topRight' });
                        setFormToCreateMode();
                    },
                    complete: function () {
                        $('#submitBtn').prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.delete-group', function () {
                const id = $(this).data('id');
                iziToast.question({
                    timeout: 20000, close: false, overlay: true, displayMode: 'once', id: 'question', zindex: 999,
                    title: 'Confirm', message: 'Are you sure you want to delete this product?', position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            $.ajax({
                                url: "{{ route('products.destroy') }}",
                                type: "POST",
                                data: { _token: "{{ csrf_token() }}", product_id: id },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                                        table.ajax.reload();
                                    } else {
                                        iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                                    }
                                },
                                error: function (xhr) {
                                    iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Failed to complete execution', position: 'topRight' });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function (instance, toast) { instance.hide({ transitionOut: 'fadeOut' }, toast, 'button'); }]
                    ]
                });
            });

            $('#product_form').submit(function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                
                let name = $('#prod_name').val().trim();
                let amount = $('#prod_amount').val().trim();
                
                let isValid = true;

                if (!$('#category_id').val()) {
                    $('#category_id').addClass('is-invalid');
                    isValid = false;
                }
                
                if (!name) {
                    $('#prod_name').addClass('is-invalid').next('.invalid-feedback').text('Product Name is required').show();
                    isValid = false;
                }
                if (!amount) {
                    $('#prod_amount').addClass('is-invalid').next('.invalid-feedback').text('Rate calculation amount is required').show();
                    isValid = false;
                }
                  
                // if (!$('#unit_id').val()) {
                //     $('#unit_id').addClass('is-invalid');
                //     isValid = false;
                // }

                // if (!$('#gst').val()) {
                //     $('#gst').addClass('is-invalid');
                //     isValid = false;
                // }

                if (!isValid) {
                    iziToast.warning({ title: 'Warning', message: 'Please complete required fields.', position: 'topRight' });
                    return;
                }

                $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    url: "{{ route('products.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        iziToast.success({
                            title: 'Success', message: response.message, position: 'topRight', timeout: 700,
                            onClosed: function () {
                                table.ajax.reload();
                                $('a[href="#product-list"]').tab('show');
                                setFormToCreateMode();
                            }
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const field in errors) {
                                $(`#${field}`).addClass('is-invalid').next('.invalid-feedback').text(errors[field][0]).show();
                            }
                        } else {
                            iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'An operational fault occurred', position: 'topRight' });
                        }
                    },
                    complete: function () {
                        $('#submitBtn').prop('disabled', false).html(editMode ? 'Update' : 'Submit');
                    }
                });
            });

            $(document).on('keypress', function (e) {
                if (e.which === 13) {
                    if ($(e.target).is('input[type="search"], textarea, #modal_gst_no, #modal_category_name, #modal_unit_name')) return;
                    let $btn = $('#submitBtn');
                    if ($btn.is(':visible')) {
                        e.preventDefault();
                        $btn.click();
                    }
                }
            });

            $('#resetFormBtn').on('click', function() {
                setFormToCreateMode();
            });

            $('#prod_name, #prod_amount').on('input', function () {
                if ($(this).val().trim()) {
                    $(this).removeClass('is-invalid').next('.invalid-feedback').hide();
                }
            });
            $('#gst').on('change', function () {
                if ($(this).val()) {
                    $(this).removeClass('is-invalid');
                }
            });
        });
    </script>
</body>
</html>