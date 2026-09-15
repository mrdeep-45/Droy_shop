@php
    $categories_global = \App\Models\ProductCategoryModel::active()->get();
    $units_global = \App\Models\UnitModel::active()->get();
    $gsts_global = \Illuminate\Support\Facades\DB::table('mst_gst')->where('status', 0)->orderByRaw('CAST(gst_no AS UNSIGNED) ASC')->get();
@endphp

<div class="modal fade" id="product_modal_global" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary"><i class="fas fa-box-open me-2"></i>Create Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="product_modal_body_global">
                <form id="productForm_global" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="product_id" id="product_id_global">
                    <input type="hidden" name="raw_materials" id="raw_materials_json_global" value="[]">

                    <div class="d-flex justify-content-end mb-2">
                        <small class="text-danger fw-semibold">* Fields are mandatory</small>
                    </div>

                    <!-- Card 1: General Details -->
                    <div class="modern-card">
                        <div class="modern-card-title">General Details</div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="category_id_global" class="form-label">Product Category <span class="text-danger">*</span></label>
                                <select class="form-select select2_global" id="category_id_global" name="category_id" required>
                                    <option value="" selected disabled>Select Product Category</option>
                                    <option value="add_new_category_global">&#10133; Add New Category</option>
                                    @foreach($categories_global as $val)
                                    <option value="{{ $val->category_id }}">{{ $val->name }}</option>
                                    @endforeach
                                </select>
                            </div>



                            <div class="col-md-6">
                                <label for="prod_name_global" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prod_name_global" name="prod_name" required placeholder="Enter Product Name">
                            </div>

                            <div class="col-md-6">
                                <label for="prod_desc_global" class="form-label">Product Description</label>
                                <textarea class="form-control" id="prod_desc_global" name="prod_desc" rows="1" placeholder="Enter Product Description"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Statutory & Pricing Details -->
                    <div class="modern-card">
                        <div class="modern-card-title">Statutory & Pricing Details</div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="gst_global" class="form-label">GST <span class="text-danger">*</span></label>
                                <select class="form-select select2_global" id="gst_global" name="gst" required>
                                    <option value="" selected disabled>Select GST</option>
                                    <option value="add_new_gst_global">&#10133; Add New GST</option>
                                    @foreach($gsts_global as $row)
                                    <option value="{{ $row->gst_id }}">{{ $row->gst_no }}%</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="prod_amount_global" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="prod_amount_global" name="prod_amount" placeholder="0.00">
                            </div>
                        </div>
                    </div>



                    {{-- Form Actions --}}
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                        <button type="button" class="btn btn-light" id="resetBtn_global">Reset</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn_global">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Category Modal -->
<div class="modal fade" id="addCategoryModal_global" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">New Product Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form id="quickAddCategoryForm_global" novalidate>
                    @csrf
                    <div id="groupNameContainerCategory_global">
                        <div class="row group-name-row col-md-12 mb-3">
                            <div class="col-md-10">
                                <label class="form-label">Product Category <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name[]" required placeholder="Enter Product Category">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-primary add-group-btn-category_global">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-end pt-2 border-top">
                        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add HSN Modal -->
<div class="modal fade" id="addHsnModal_global" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">New HSN Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form id="quickAddHsnForm_global" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">HSN Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="hsn_no[]" required placeholder="Enter HSN Code">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GST(%)</label>
                            <select name="gst_id[]" id="quick_add_hsn_gst_global" class="form-select select2_global" style="width:100%">
                                <option value="">Select GST</option>
                                @foreach($gsts_global as $g)
                                <option value="{{ $g->gst_id }}">{{ $g->gst_no }}%</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 text-end pt-2 border-top">
                        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add GST Modal -->
<div class="modal fade" id="addGstModal_global" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">New GST</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form id="quickAddGstForm_global" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">GST Rate (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="gst_no[]" required placeholder="Enter GST Rate">
                    </div>
                    <div class="mt-3 text-end pt-2 border-top">
                        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Unit Modal -->
<div class="modal fade" id="addUnitModal_global" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">New Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form id="quickAddUnitForm_global" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="unit[]" required placeholder="Enter Unit">
                    </div>
                    <div class="mt-3 text-end pt-2 border-top">
                        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Stock Group Modal -->
<div class="modal fade" id="addStockGroupModal_global" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">New Stock Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form id="quickAddStockGroupForm_global" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Stock Group Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name[]" required placeholder="Enter Stock Group Name">
                    </div>
                    <div class="mt-3 text-end pt-2 border-top">
                        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Stock Category Modal -->
<div class="modal fade" id="addStockCategoryModal_global" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">New Stock Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form id="quickAddStockCategoryForm_global" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock Group <span class="text-danger">*</span></label>
                            <select class="form-select select2_global" id="modal_stock_group_id_parent_global" name="stock_group_id" required style="width: 100%">
                                <option value="" selected disabled>Select Stock Group</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name[]" required placeholder="Enter Category Name">
                        </div>
                    </div>
                    <div class="mt-3 text-end pt-2 border-top">
                        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Alternate Unit Modal -->
<div class="modal fade" id="addAlternateUnitModal_global" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">New Alternate Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form id="quickAddAlternateUnitForm_global" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Alternate Unit Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name[]" required placeholder="Enter Alternate Unit Name">
                    </div>
                    <div class="mt-3 text-end pt-2 border-top">
                        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Raw Material Modal -->
<div class="modal fade" id="addRawMaterialModal_global" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">New Raw Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form id="quickAddRawMaterialForm_global" novalidate>
                    @csrf
                    <!-- Section 1: Basic Details -->
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Name and Alias <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="modal_rm_name_global" required placeholder="Enter Name and Alias">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Enter Description"></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Enter Notes"></textarea>
                        </div>
                    </div>
                    
                    <!-- Section 2: Inventory Details -->
                    <div class="card mb-3 shadow-none border">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0 text-white">Inventory Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Stock Group <span class="text-danger">*</span></label>
                                    <select class="form-select select2_global" id="modal_stock_group_id_global" name="stock_group_id" required>
                                        <option value="" selected disabled>Select Stock Group</option>
                                        <option value="add_new_stock_group_global">&#10133; Add New Stock Group</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Stock Category <span class="text-danger">*</span></label>
                                    <select class="form-select select2_global" id="modal_stock_category_id_global" name="stock_category_id" required>
                                        <option value="" selected disabled>Select Stock Group First</option>
                                        <option value="add_new_stock_category_global">&#10133; Add New Stock Category</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Unit of Measurement <span class="text-danger">*</span></label>
                                    <select class="form-select select2_global" id="modal_unit_id_global" name="unit_id" required>
                                        <option value="" selected disabled>Select Unit</option>
                                        <option value="add_new_unit_global">&#10133; Add New Unit</option>
                                        @foreach($units_global as $val)
                                            <option value="{{ $val->unit_id }}">{{ $val->unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Alternate Unit <span class="text-danger">*</span></label>
                                    <select class="form-select select2_global" id="modal_alt_unit_id_global" name="alt_unit_id" required>
                                        <option value="" selected disabled>Select Alternate Unit</option>
                                        <option value="add_new_alt_unit_global">&#10133; Add New Alternate Unit</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 3: Statutory Details -->
                    <div class="card mb-3 shadow-none border">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0 text-white">Statutory Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Reorder Level <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="reorder_level" required placeholder="0.00">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">HSN/SAC Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="hsn_code" required placeholder="Enter HSN Code">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Type of Supply <span class="text-danger">*</span></label>
                                    <select class="form-select select2_global" name="type_of_supply" required>
                                        <option value="" selected disabled>--Select--</option>
                                        <option value="Goods">Goods</option>
                                        <option value="Services">Services</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">GST Rate (%) <span class="text-danger">*</span></label>
                                    <select class="form-select select2_global" name="gst_id" id="modal_gst_id_global" required>
                                        <option value="" selected disabled>Select GST</option>
                                        <option value="add_new_gst_global">&#10133; Add New GST</option>
                                        @foreach($gsts_global as $g)
                                            <option value="{{ $g->gst_id }}">{{ $g->gst_no }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Incl. of Duties & Taxes <span class="text-danger">*</span></label>
                                    <select class="form-select select2_global" name="incl_of_taxes" required>
                                        <option value="" selected disabled>--Select--</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-end pt-2 border-top">
                        <button type="submit" class="btn btn-primary btn-sm" id="modalRawMaterialSubmitBtn_global">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let materials_global = [];

        // Open Modal handler
        $(document).on("click", "#add_product_global", function () {
            $('#productForm_global')[0].reset();
            $('#productForm_global').find('.select2_global').val(null).trigger('change');
            materials_global = [];
            renderMaterialsTable_global();

            $("#product_modal_global").modal("show");

            // Fetch HSN and Raw Materials dynamically
            loadHsnAndRawMaterials_global();
        });

        // Initialize Select2 dropdowns relative to their modal containers
        $('.select2_global').each(function() {
            let parentModal = $(this).closest('.modal');
            if (parentModal.length > 0) {
                $(this).select2({
                    dropdownParent: parentModal,
                    width: '100%'
                });
            } else {
                $(this).select2({
                    width: '100%'
                });
            }
        });

        // Setup Dropdown triggers for Modals
        function setupQuickAddDropdown_global(dropdownId, triggerValue, modalId) {
            $(document).on('change', '#' + dropdownId, function() {
                if ($(this).val() === triggerValue) {
                    $('#' + modalId).data('triggering-select-id', dropdownId);

                    let openModal = $('.modal.show');
                    if (openModal.length > 0) {
                        let openModalId = openModal.attr('id');
                        if (openModalId !== modalId) {
                            $('#' + modalId).data('parent-modal-id', openModalId);
                            let inst = bootstrap.Modal.getInstance(openModal[0]);
                            if (inst) inst.hide();
                        }
                    }

                    var modal = bootstrap.Modal.getInstance(document.getElementById(modalId)) || new bootstrap.Modal(document.getElementById(modalId));
                    modal.show();
                    $(this).val('');
                }
            });
        }

        setupQuickAddDropdown_global('category_id_global', 'add_new_category_global', 'addCategoryModal_global');
        setupQuickAddDropdown_global('hsn_code_global', 'add_new_hsn_global', 'addHsnModal_global');
        setupQuickAddDropdown_global('unit_id_global', 'add_new_unit_global', 'addUnitModal_global');
        setupQuickAddDropdown_global('gst_global', 'add_new_gst_global', 'addGstModal_global');
        setupQuickAddDropdown_global('rm_id_global', 'add_new_rm_global', 'addRawMaterialModal_global');

        setupQuickAddDropdown_global('modal_stock_group_id_global', 'add_new_stock_group_global', 'addStockGroupModal_global');
        setupQuickAddDropdown_global('modal_stock_category_id_global', 'add_new_stock_category_global', 'addStockCategoryModal_global');
        setupQuickAddDropdown_global('modal_unit_id_global', 'add_new_unit_global', 'addUnitModal_global');
        setupQuickAddDropdown_global('modal_alt_unit_id_global', 'add_new_alt_unit_global', 'addAlternateUnitModal_global');
        setupQuickAddDropdown_global('modal_gst_id_global', 'add_new_gst_global', 'addGstModal_global');

        // Setup AJAX Submit for Modals
        function setupQuickAddAjax_global(formId, modalId, submitUrl, fallbackSelectId, inputName, valueKey, refreshApiUrl, customFormatOption = null) {
            $('#' + formId).on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let formData = form.serialize();

                $.ajax({
                    url: submitUrl,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success' && response.data && response.data.length > 0) {
                            let newItem = response.data[0];
                            
                            let modalEl = $('#' + modalId);
                            let selectId = modalEl.data('triggering-select-id') || fallbackSelectId;

                            $.ajax({
                                url: refreshApiUrl,
                                type: 'GET',
                                dataType: 'json',
                                success: function(refreshResponse) {
                                    if (refreshResponse.status === 'success') {
                                        if (customFormatOption) {
                                            let select = $('#' + selectId);
                                            select.empty();
                                            select.append(`<option value="" selected disabled>Select</option>`);
                                            refreshResponse.data.forEach(item => {
                                                let selectIt = (item[valueKey] == newItem[valueKey]);
                                                let opt = customFormatOption(item, selectIt);
                                                select.append(opt);
                                            });
                                            select.trigger('change');
                                        } else {
                                            let addOptText = null;
                                            let addOptVal = null;
                                            let placeholder = "Select";
                                            
                                            if (selectId === 'category_id_global') {
                                                addOptText = '&#10133; Add New Category';
                                                addOptVal = 'add_new_category_global';
                                                placeholder = 'Select Product Category';
                                            } else if (selectId === 'unit_id_global' || selectId === 'modal_unit_id_global') {
                                                addOptText = '&#10133; Add New Unit';
                                                addOptVal = 'add_new_unit_global';
                                                placeholder = 'Select Unit';
                                            } else if (selectId === 'gst_global' || selectId === 'modal_gst_id_global') {
                                                addOptText = '&#10133; Add New GST';
                                                addOptVal = 'add_new_gst_global';
                                                placeholder = 'Select GST';
                                                
                                                updateSelectOptions_global('#' + selectId, refreshResponse.data, valueKey, inputName, placeholder, addOptText, addOptVal, '%');
                                                $('#' + selectId).val(newItem[valueKey]).trigger('change');
                                                return;
                                            }

                                            updateSelectOptions_global('#' + selectId, refreshResponse.data, valueKey, inputName, placeholder, addOptText, addOptVal);
                                            $('#' + selectId).val(newItem[valueKey]).trigger('change');
                                        }
                                    }
                                }
                            });

                            bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                            form[0].reset();

                            iziToast.success({
                                title: 'Success',
                                message: 'Added successfully',
                                position: 'topRight'
                            });
                        } else {
                            iziToast.error({ title: 'Error', message: 'Something went wrong', position: 'topRight' });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Something went wrong';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMsg = Object.values(xhr.responseJSON.errors)[0][0];
                        }
                        iziToast.error({ title: 'Error', message: errorMsg, position: 'topRight' });
                    }
                });
            });
        }

        setupQuickAddAjax_global('quickAddCategoryForm_global', 'addCategoryModal_global', '{{ route("productcategory.store") }}', 'category_id_global', 'name', 'category_id', '{{ route("get.categories.global") }}');
        setupQuickAddAjax_global('quickAddHsnForm_global', 'addHsnModal_global', '{{ route("hsn_gst.store") }}', 'hsn_code_global', 'hsn_no', 'hsn_no', '{{ route("get.hsn.global") }}', function(newItem, selectIt) {
            let opt = $('<option>', {
                value: newItem.hsn_no,
                text: newItem.hsn_no,
                'data-gst-id': newItem.gst_id,
                'data-gst-name': newItem.gst ? newItem.gst.gst_no : ''
            });
            if (selectIt) opt.prop('selected', true);
            return opt;
        });
        setupQuickAddAjax_global('quickAddUnitForm_global', 'addUnitModal_global', '{{ route("unit.store") }}', 'unit_id_global', 'unit', 'unit_id', '{{ route("get.units.global") }}');
        setupQuickAddAjax_global('quickAddGstForm_global', 'addGstModal_global', '{{ route("gst.store") }}', 'gst_global', 'gst_no', 'gst_id', '{{ route("get.gst.global") }}', function(newItem, selectIt) {
            let opt = $('<option>', {
                value: newItem.gst_id,
                text: newItem.gst_no + '%'
            });
            if (selectIt) opt.prop('selected', true);
            return opt;
        });

        setupQuickAddAjax_global('quickAddStockGroupForm_global', 'addStockGroupModal_global', '{{ route("stockgroup.store") }}', 'modal_stock_group_id_global', 'name', 'stock_group_id', '{{ route("product.latest_options") }}');
        setupQuickAddAjax_global('quickAddAlternateUnitForm_global', 'addAlternateUnitModal_global', '{{ route("altunit.store") }}', 'modal_alt_unit_id_global', 'name', 'alt_unit_id', '{{ route("product.latest_options") }}');

        // Stock Category Nested Save
        let selectCategoryAfterLoad_global = null;
        $('#quickAddStockCategoryForm_global').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            $.ajax({
                url: '{{ route("stockcategory.store") }}',
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.status === 'success' && response.data && response.data.length > 0) {
                        let newItem = response.data[0];
                        selectCategoryAfterLoad_global = newItem.stock_category_id;
                        $('#modal_stock_group_id_global').trigger('change');
                        bootstrap.Modal.getInstance(document.getElementById('addStockCategoryModal_global')).hide();
                        form[0].reset();
                        iziToast.success({ title: 'Success', message: 'Category added successfully', position: 'topRight' });
                    }
                }
            });
        });

        // Stock Group change listener in RM Modal
        $('#modal_stock_group_id_global').on('change', function () {
            const groupId = $(this).val();
            const categorySelect = $('#modal_stock_category_id_global');
            
            categorySelect.empty();
            categorySelect.append('<option value="" selected disabled>Select Category</option>');
            categorySelect.append('<option value="add_new_stock_category_global">&#10133; Add New Stock Category</option>');
            categorySelect.trigger('change');
            
            if (groupId && groupId !== 'add_new_stock_group_global') {
                $.ajax({
                    url: "{{ route('rawmaterial.categories') }}",
                    type: "GET",
                    data: { stock_group_id: groupId },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            response.data.forEach(function(cat) {
                                categorySelect.append(`<option value="${cat.stock_category_id}">${cat.name}</option>`);
                            });
                            if (selectCategoryAfterLoad_global) {
                                categorySelect.val(selectCategoryAfterLoad_global);
                                selectCategoryAfterLoad_global = null;
                            }
                            categorySelect.trigger('change');
                        }
                    }
                });
            }
        });

        // Setup AJAX quick add submit for raw material
        setupQuickAddAjax_global('quickAddRawMaterialForm_global', 'addRawMaterialModal_global', '{{ route("rawmaterial.store") }}', 'rm_id_global', 'name', 'rm_id', '{{ route("get.rawmaterials.global") }}', function(newItem, selectIt) {
            let opt = $('<option>', {
                value: newItem.rm_id,
                text: newItem.name,
                'data-gst': newItem.gst_no,
                'data-unit-id': newItem.unit_id,
                'data-unit-name': newItem.unit
            });
            if (selectIt) opt.prop('selected', true);
            return opt;
        });

        // Nested Modal Backdrops
        $(document).on('hidden.bs.modal', '.modal', function () {
            let currentModal = $(this);
            let parentModalId = currentModal.data('parent-modal-id');
            if (parentModalId) {
                currentModal.removeData('parent-modal-id');
                let parentEl = document.getElementById(parentModalId);
                if (parentEl) {
                    setTimeout(() => {
                        let inst = bootstrap.Modal.getInstance(parentEl) || new bootstrap.Modal(parentEl);
                        inst.show();
                    }, 150);
                }
            }
        });

        // Auto Map GST when HSN is selected
        $('#hsn_code_global').on('change', function() {
            let selectedOption = $(this).find('option:selected');
            let hsnValue = selectedOption.val();
            if (hsnValue === 'add_new_hsn_global') return;
            if (!hsnValue) {
                $('#gst_global').val('').trigger('change');
                return;
            }
            let gstId = selectedOption.data('gst-id');
            if (gstId) {
                $('#gst_global').val(gstId).trigger('change');
            } else {
                $('#gst_global').val('').trigger('change');
            }
        });

        // Raw Material Select Change (unit display)
        $('#rm_id_global').on('change', function () {
            const selectedOption = $(this).find('option:selected');
            const unitName = selectedOption.data('unit-name') || '';
            $('#rm_unit_display_global').val(unitName);
        });

        // Add Raw Material mapping row
        $(document).on('click', '#addMaterialBtn_global', function () {
            const rmSelect = $('#rm_id_global');
            const selectedOption = rmSelect.find('option:selected');
            const rmId = rmSelect.val();
            const qtyVal = $('#rm_qty_global').val();

            if (!rmId || rmId === 'add_new_rm_global') {
                iziToast.error({ title: 'Validation Error', message: 'Please select a Raw Material.', position: 'topRight' });
                return;
            }

            const qty = parseFloat(qtyVal);
            if (isNaN(qty) || qty <= 0) {
                iziToast.error({ title: 'Validation Error', message: 'Please enter a valid Quantity.', position: 'topRight' });
                return;
            }

            if (materials_global.some(item => item.rm_id == rmId)) {
                iziToast.error({ title: 'Duplicate Error', message: 'This Raw Material is already mapped.', position: 'topRight' });
                return;
            }

            const rmName = selectedOption.text().trim();
            const unitId = selectedOption.data('unit-id');
            const unitName = selectedOption.data('unit-name') || '';
            const gstNo = selectedOption.data('gst') || 0;

            materials_global.push({
                rm_id: rmId,
                name: rmName,
                qty: qty,
                unit_id: unitId,
                unit_name: unitName,
                gst_no: gstNo
            });

            renderMaterialsTable_global();

            rmSelect.val('').trigger('change');
            $('#rm_qty_global').val('');
            $('#rm_unit_display_global').val('');
        });

        // Remove Material mapping row
        $(document).on('click', '.removeMaterialBtn_global', function () {
            const index = $(this).data('index');
            materials_global.splice(index, 1);
            renderMaterialsTable_global();
        });

        function renderMaterialsTable_global() {
            const tbody = $('#material-tbody_global');
            tbody.empty();

            if (materials_global.length === 0) {
                $('#material-table-section_global').hide();
                $('#raw_materials_json_global').val('[]');
                return;
            }

            $('#material-table-section_global').show();

            materials_global.forEach((item, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.name}</td>
                        <td class="text-center">${item.qty}</td>
                        <td class="text-center">${item.unit_name}</td>
                        <td class="text-center">${item.gst_no}%</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm removeMaterialBtn_global" data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });

            $('#raw_materials_json_global').val(JSON.stringify(materials_global));
        }

        // Sub-modal Multi-Add Categories
        $(document).on('click', '.add-group-btn-category_global', function() {
            let html = `
                <div class="row group-name-row col-md-12 mb-3">
                    <div class="col-md-10">
                        <label class="form-label">Product Category <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name[]" required placeholder="Enter Product Category">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-group-btn_global">−</button>
                    </div>
                </div>
            `;
            $('#groupNameContainerCategory_global').append(html);
        });

        $(document).on('click', '.remove-group-btn_global', function() {
            $(this).closest('.group-name-row').remove();
        });

        // Initialize Select2 in HSN quick-add modal
        $('select[name="gst_id[]"]', '#addHsnModal_global').select2({
            dropdownParent: $('#addHsnModal_global'),
            width: '100%'
        });

        // Load dynamic data on demand (HSN, RM, Stock Groups, Alternate Units)
        function loadHsnAndRawMaterials_global() {
            $.ajax({
                url: "{{ route('get.hsn.global') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.status === 'success') {
                        updateSelectOptionsHsn_global('#hsn_code_global', response.data);
                    }
                }
            });

            $.ajax({
                url: "{{ route('get.rawmaterials.global') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.status === 'success') {
                        updateSelectOptionsRawMaterial_global('#rm_id_global', response.data);
                    }
                }
            });

            $.ajax({
                url: "{{ route('product.latest_options') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.status === 'success') {
                        updateSelectOptions_global('#modal_stock_group_id_global', response.stock_groups, 'stock_group_id', 'name', 'Select Stock Group', '&#10133; Add New Stock Group', 'add_new_stock_group_global');
                        updateSelectOptions_global('#modal_stock_group_id_parent_global', response.stock_groups, 'stock_group_id', 'name', 'Select Stock Group', null, null);
                        updateSelectOptions_global('#modal_alt_unit_id_global', response.alternate_units, 'alt_unit_id', 'name', 'Select Alternate Unit', '&#10133; Add New Alternate Unit', 'add_new_alt_unit_global');
                    }
                }
            });
        }

        function updateSelectOptions_global(selectSelector, dataList, idKey, nameKey, placeholder, addOptionText = null, addOptionValue = null, suffix = '') {
            $(selectSelector).each(function() {
                let select = $(this);
                let currentValue = select.val();
                select.empty();
                
                select.append(`<option value="" selected disabled>${placeholder}</option>`);
                if (addOptionText && addOptionValue) {
                    select.append(`<option value="${addOptionValue}">${addOptionText}</option>`);
                }
                if (dataList && dataList.length > 0) {
                    dataList.forEach(function(item) {
                        let label = item[nameKey] + suffix;
                        let option = $('<option>', {
                            value: item[idKey],
                            text: label
                        });
                        select.append(option);
                    });
                }
                if (currentValue) {
                    select.val(currentValue).trigger('change');
                }
            });
        }

        function updateSelectOptionsHsn_global(selectSelector, dataList) {
            $(selectSelector).each(function() {
                let select = $(this);
                let currentValue = select.val();
                select.empty();
                select.append('<option value="" selected disabled>Select HSN Code</option>');
                select.append('<option value="add_new_hsn_global">&#10133; Add New HSN Code</option>');
                if (dataList && dataList.length > 0) {
                    dataList.forEach(function(item) {
                        let option = $('<option>', {
                            value: item.hsn_no,
                            text: item.hsn_no,
                            'data-gst-id': item.gst_id,
                            'data-gst-name': item.gst ? item.gst.gst_no : ''
                        });
                        select.append(option);
                    });
                }
                if (currentValue) select.val(currentValue).trigger('change');
            });
        }

        function updateSelectOptionsRawMaterial_global(selectSelector, dataList) {
            $(selectSelector).each(function() {
                let select = $(this);
                let currentValue = select.val();
                select.empty();
                select.append('<option value="" selected disabled>Select Raw Material</option>');
                select.append('<option value="add_new_rm_global">&#10133; Add New Raw Material</option>');
                if (dataList && dataList.length > 0) {
                    dataList.forEach(function(item) {
                        let option = $('<option>', {
                            value: item.rm_id,
                            text: item.name,
                            'data-gst': item.gst_no,
                            'data-unit-id': item.unit_id,
                            'data-unit-name': item.unit
                        });
                        select.append(option);
                    });
                }
                if (currentValue) select.val(currentValue).trigger('change');
            });
        }

        // Reset Form Handler
        $('#resetBtn_global').on('click', function() {
            $('#productForm_global')[0].reset();
            $('#productForm_global').find('.select2_global').val(null).trigger('change');
            materials_global = [];
            renderMaterialsTable_global();
        });

        // Submit Product Form via AJAX
        $('#productForm_global').on('submit', function (e) {
            e.preventDefault();

            let isValid = true;
            const categoryId = $('#category_id_global').val();
            const prodName = $('#prod_name_global').val() ? $('#prod_name_global').val().trim() : '';
            const hsnCode = $('#hsn_code_global').val();
            const unitId = $('#unit_id_global').val();
            const gst = $('#gst_global').val();

            if (!categoryId) {
                iziToast.error({ title: 'Validation Error', message: 'Product Category is required.', position: 'topRight' });
                isValid = false;
            }
            if (!prodName) {
                iziToast.error({ title: 'Validation Error', message: 'Product Name is required.', position: 'topRight' });
                isValid = false;
            }
            if (!gst) {
                iziToast.error({ title: 'Validation Error', message: 'GST is required.', position: 'topRight' });
                isValid = false;
            }

            if (!isValid) return false;

            const btn = $('#submitBtn_global');
            btn.prop('disabled', true).text('Submitting...');

            $.ajax({
                url: "{{ route('ProductStore') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function (response) {
                    btn.prop('disabled', false).text('Submit');
                    if (response.success || response.status === 'success') {
                        iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                        
                        // Fire custom document event with the payload
                        $(document).trigger('global_product_created', [response]);
                        
                        $('#product_modal_global').modal('hide');
                    } else {
                        iziToast.error({ title: 'Error', message: response.message || 'Failed to save product.', position: 'topRight' });
                    }
                },
                error: function (xhr) {
                    btn.prop('disabled', false).text('Submit');
                    let errorMsg = 'Failed to save product.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    iziToast.error({ title: 'Error', message: errorMsg, position: 'topRight' });
                }
            });
        });
    });
</script>
