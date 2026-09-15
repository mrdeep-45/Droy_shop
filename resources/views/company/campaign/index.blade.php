<!DOCTYPE html>
<html {!! get_html_data() !!}>
<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        .custom-file-dropzone {
        border-color: #dee2e6 !important;
        transition: border-color 0.2s ease-in-out, background-color 0.2s ease-in-out;
    }
    .custom-file-dropzone:hover {
        border-color: #0d6efd !important;
        background-color: #f8f9fa !important;
    }
    .preview-thumb-wrapper {
        position: relative;
        width: 70px;
        height: 70px;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #dee2e6;
        background-color: #fff;
    }
    .preview-thumb-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-thumb-wrapper .remove-preview-img {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: rgba(220, 53, 69, 0.9);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    line-height: 1;
    cursor: pointer;
    z-index: 2;
}
.preview-thumb-wrapper .remove-preview-img:hover {
    background: #dc3545;
}
    .design-row .card {
        transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .design-row .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05) !important;
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
                                <div class="card-title text-primary mb-0" id="pageTitleLabel">
                                    {{ $page_name }}
                                </div>
                                <ul class="nav nav-tabs nav-tabs-footer mb-0" role="tablist">
                                    @if (permissions_check('canView'))
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#campaign-list">List</a>
                                    </li>
                                    @endif
                                    @if (permissions_check('canCreate'))
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" role="tab" href="#new-campaign" id="campaignTabLabel">New Campaign</a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    @if (permissions_check('canView'))
                                    <div class="tab-pane fade show active" id="campaign-list">
                                        <div class="table-responsive">
                                            <table id="campaignData" class="table table-bordered text-nowrap" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th style="width:1%;">#</th>
                                                        <th>Campaign Name</th>
                                                        <th>Campaign Type</th>
                                                        <th>Owner</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Timeline Status</th>
                                                        <th>Expected Budget</th>
                                                        <th>Actual Cost</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                    <div class="tab-pane text-muted px-1" id="new-campaign" role="tabpanel">
                                        <form id="campaign_form" enctype="multipart/form-data" novalidate>
                                            @csrf
                                            <input type="hidden" name="campaign_id" id="campaign_id">

                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <h6 class="fw-bold text-primary">Basic Details</h6>
                                                    <hr class="mt-1 mb-3">
                                                </div>
                                                <div class="col-12 col-md-4 mb-2">
                                                    <label for="campaign_name" class="form-label">Campaign Name<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="campaign_name" id="campaign_name"  placeholder="Enter Campaign Name">
                                                    <div class="invalid-feedback">Please enter a campaign name.</div>
                                                </div>
                                                
                                                <div class="col-6 col-md-4 mb-2">
                                                    <label for="campaign_type_id" class="form-label">Campaign Type<span class="text-danger">*</span></label>
                                                    <select class="form-select select2" name="campaign_type_id" id="campaign_type_id" >
                                                        <option value="">Select Campaign Type</option>
                                                        <option value="add_new_campaign_type">&#10133; Add New Campaign Type</option>
                                                        @foreach($campaignTypes as $type)
                                                            <option value="{{ $type->campaign_type_id }}">{{ $type->campaign_type_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback">Please select a campaign type.</div>
                                                </div>

                                                <div class="col-6 col-md-4 mb-2">
                                                    <label for="assigned_to" class="form-label">Campaign Owner / Assigned To</label>
                                                    <select class="form-select select2" name="assigned_to" id="assigned_to">
                                                        <option value="">Select Assigned To</option>
                                                        @foreach($staffMembers as $staff)
                                                            <option value="{{ $staff->staff_id }}">{{ $staff->staff_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-6 col-md-3 mb-2">
                                                    <label for="start_date" class="form-label">Start Date</label>
                                                   <input type="text" class="form-control flatpickr-date" name="start_date" id="start_date" placeholder="dd-mm-yyyy">
                                                </div>
                                                <div class="col-6 col-md-3 mb-2">
                                                    <label for="end_date" class="form-label">End Date</label>
                                                    <input type="text" class="form-control flatpickr-date" name="end_date" id="end_date" placeholder="dd-mm-yyyy">
                                                </div>
                                                <div class="col-6 col-md-3 mb-2">
                                                    <label for="expected_budget" class="form-label">Expected Budget</label>
                                                    <input type="number" step="0.01" class="form-control" name="expected_budget" id="expected_budget" placeholder="0.00">
                                                </div>
                                                <div class="col-6 col-md-3 mb-2">
                                                    <label for="actual_cost" class="form-label">Actual Cost</label>
                                                    <input type="number" step="0.01" class="form-control" name="actual_cost" id="actual_cost" placeholder="0.00">
                                                </div>
                                                <div class="col-12 mb-2">
                                                    {{-- <label for="description" class="form-label">Description / Remarks</label> --}}
                                                     @include('components.tooltip-help', ['key' => 'campaign_remark', 'label' => 'Description / Remark', 'class' => 'form-label '])
                                                    <textarea class="form-control" name="description" id="description" rows="3" placeholder="Enter remarks..."></textarea>
                                                </div>

                                                <div class="col-12 mb-2 mt-3">
                                                    <h6 class="fw-bold text-primary">Exhibition Details</h6>
                                                    <hr class="mt-1 mb-2">
                                                </div>
                                                <div class="col-6 col-md-4 mb-2">
                                                    <label for="stall_number" class="form-label">Stall Number</label>
                                                    <input type="text" class="form-control" name="stall_number" id="stall_number" placeholder="Enter Stall Number">
                                                </div>
                                                <div class="col-6 col-md-4 mb-2">
                                                    <label for="num_visitors" class="form-label">Number of Visitors</label>
                                                    <input type="number" class="form-control" name="num_visitors" id="num_visitors" placeholder="Expected number of visitors">
                                                </div>
                                                <div class="col-12 col-md-4 mb-2">
                                                    <label for="venue_location" class="form-label">Exhibition Venue & Location</label>
                                                    <input type="text" class="form-control" name="venue_location" id="venue_location" placeholder="Venue & Location details">
                                                </div>
                                                <div class="col-12 mb-2">
                                                    {{-- <label for="machines_displayed" class="form-label">Machines to be Displayed / Used</label> --}}
                                                     @include('components.tooltip-help', ['key' => 'campaign_machine_detail', 'label' => 'Machines to be Displayed / Used', 'class' => 'form-label'])
                                                    <textarea class="form-control" name="machines_displayed" id="machines_displayed" rows="2" placeholder="List the machines to be displayed..."></textarea>
                                                </div>
                                               <div class="col-md-12 mb-3">
                                                   <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                                        <div>
                                                            <h5 class="text-dark fw-bold mb-1">Posters, Banners, and Design Details</h5>
                                                            <p class="text-muted small mb-0">Add descriptive labels and upload creative files for this campaign.</p>
                                                        </div>
                                                        <button type="button" class="btn btn-primary btn-sm px-3 add-design-btn d-flex align-items-center gap-2">
                                                            <i class="bi bi-plus-circle"></i> Add New Group
                                                        </button>
                                                    </div>
                                                    <div id="designContainer" class="row g-3">
                                                        <!-- First Row (Default Base Row) -->
                                                        <div class="col-md-6 design-row" data-index="0">
                                                            <div class="card h-100 shadow-sm border-0 bg-light">
                                                                <div class="card-body position-relative d-flex flex-column justify-content-between p-3">
                                                                    <div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label text-secondary small fw-semibold">Design Label</label>
                                                                            <input type="text" class="form-control form-control-sm" name="design_names[0]" placeholder="e.g., Main Social Banner">
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label class="form-label text-secondary small fw-semibold">Upload Images</label>
                                                                            <div class="custom-file-dropzone border border-dashed rounded text-center p-3 bg-white position-relative" style="cursor: pointer;">
                                                                                <i class="bi bi-cloud-arrow-up fs-4 text-primary d-block mb-1"></i>
                                                                                <span class="small text-muted d-block">Drag files here or <strong>browse</strong></span>
                                                                                <input type="file" class="form-control design-file-input opacity-0 position-absolute top-0 start-0 w-100 h-100" name="design_files[0][]" accept="image/*" multiple style="cursor: pointer;">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-2 live-preview-container d-flex flex-wrap gap-2" style="display: none;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                              
                                            </div>

                                            <div class="d-flex justify-content-end gap-2 mt-4">
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
        <div class="modal fade" id="quickCampaignTypeModal" tabindex="-1" aria-labelledby="quickCampaignTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="quick_campaign_type_form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="quickCampaignTypeModalLabel">Add New Campaign Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal_campaign_type_name" class="form-label">Campaign Type Name<span class="text-danger">*</span></label>
                            <!-- Structured as array to match the validation/controller interface of your CampaignTypeController -->
                            <input type="text" class="form-control" name="campaign_type_name[]" id="modal_campaign_type_name" required placeholder="Enter Campaign Type Name">
                            <div class="invalid-feedback" id="modal_campaign_type_nameError">Please enter a campaign type name.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Add Campaign Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--model -->
    <!-- Timeline History Modal -->
<div class="modal fade" id="timelineHistoryModal" tabindex="-1" aria-labelledby="timelineHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="timelineHistoryModalLabel">Timeline History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light-subtle" style="max-height: 450px; overflow-y: auto;">
                <!-- Target container where the dynamic tracking markup renders -->
                <div id="timelineContentContainer"></div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-danger px-4" data-bs-redirect data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- details model-->
 <!-- Campaign Details View Modal -->
<div class="modal fade" id="viewCampaignModal" tabindex="-1" aria-labelledby="viewCampaignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="viewCampaignModalLabel">
                    <i class="fas fa-bullhorn text-primary me-2"></i>Campaign Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewCampaignBody" style="max-height: 70vh;">
                <div class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span> Loading details...</div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
        @include('include.footer')
    </div>
    @include('include.scroll-top')
    @include('include.footer_link')
    @include('include.datatable_js_link')
<!-- Add this right before your custom script tags if you want to use moment -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>
    <script>
        let editMode = false;
        let startPicker, endPicker;
        let designIndex = 1;
        function setFormToCreateMode() {
            editMode = false;
            $('#campaign_form')[0].reset();
            $('#campaign_id').val('');
            $('#campaignTabLabel').text('New Campaign');
            $('#submitBtn').text('Submit');
            $('.select2').val('').trigger('change');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
            if(startPicker) startPicker.clear();
            if(endPicker) endPicker.clear();
           designIndex = 1;
            $('#designContainer').html(`
                <div class="col-md-6 design-row" data-index="0">
                    <div class="card h-100 shadow-sm border-0 bg-light">
                        <div class="card-body position-relative d-flex flex-column justify-content-between p-3">
                            <div>
                                <div class="mb-3">
                                    <label class="form-label text-secondary small fw-semibold">Design Label</label>
                                    <input type="text" class="form-control form-control-sm" name="design_names[0]" placeholder="e.g., Main Social Banner">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-secondary small fw-semibold">Upload Images</label>
                                    <div class="custom-file-dropzone border border-dashed rounded text-center p-3 bg-white position-relative" style="cursor: pointer;">
                                        <i class="bi bi-cloud-arrow-up fs-4 text-primary d-block mb-1"></i>
                                        <span class="small text-muted d-block">Drag files here or <strong>browse</strong></span>
                                        <input type="file" class="form-control design-file-input opacity-0 position-absolute top-0 start-0 w-100 h-100" name="design_files[0][]" accept="image/*" multiple style="cursor: pointer;">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 live-preview-container d-flex flex-wrap gap-2" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            `);
        }

        function setFormToEditMode(data, designs) {
            editMode = true;
            $('#campaignTabLabel').text('Edit Campaign');
            $('#submitBtn').text('Update');
            
            $('#campaign_id').val(data.campaign_id);
            $('#campaign_name').val(data.campaign_name);
            $('#campaign_type_id').val(data.campaign_type_id).trigger('change');
            $('#assigned_to').val(data.assigned_to).trigger('change');
          if(data.start_date) {
    // Parse the YYYY-MM-DD database format directly using Flatpickr's parser
    let parsedStart = flatpickr.parseDate(data.start_date, "Y-m-d");
    startPicker.setDate(parsedStart);
} else {
    startPicker.clear();
}

if(data.end_date) {
    // Parse the YYYY-MM-DD database format directly using Flatpickr's parser
    let parsedEnd = flatpickr.parseDate(data.end_date, "Y-m-d");
    endPicker.setDate(parsedEnd);
} else {
    endPicker.clear();
}
            $('#expected_budget').val(data.expected_budget);
            $('#actual_cost').val(data.actual_cost);
            $('#description').val(data.description);
            $('#num_visitors').val(data.num_visitors);
            $('#machines_displayed').val(data.machines_displayed);
          
            $('#stall_number').val(data.stall_number);
            $('#venue_location').val(data.venue_location);

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
            $('#designContainer').empty();
            if (designs && designs.length > 0) {
                designs.forEach((design, index) => {
                    let deleteButton = index === 0 
                        ? '' 
                        : `<button type="button" class="btn btn-outline-danger btn-xs position-absolute top-0 end-0 m-2 remove-design-btn" style="z-index:10;"><i class="fas fa-trash-alt"></i></button>`;

                    let imageThumbs = '';
                    if (design.images && design.images.length > 0) {
                        design.images.forEach(img => {
                            imageThumbs += `
                                <div class="preview-thumb-wrapper">
                                    <img src="${img.url}">
                                </div>`;
                        });
                    }

                    $('#designContainer').append(`
                        <div class="col-md-6 design-row" data-index="${index}">
                            <div class="card h-100 shadow-sm border-0 bg-light position-relative">
                                ${deleteButton}
                                <div class="card-body d-flex flex-column justify-content-between p-3">
                                    <div>
                                        <div class="mb-3">
                                            <label class="form-label text-secondary small fw-semibold">Design Label</label>
                                            <input type="text" class="form-control form-control-sm" name="design_names[${index}]" value="${design.name}" placeholder="Enter Label">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-secondary small fw-semibold">Upload Images</label>
                                            <div class="custom-file-dropzone border border-dashed rounded text-center p-3 bg-white position-relative" style="cursor: pointer;">
                                                <i class="bi bi-cloud-arrow-up fs-4 text-primary d-block mb-1"></i>
                                                <span class="small text-muted d-block">Drag files here or <strong>browse</strong></span>
                                                <input type="file" class="form-control design-file-input opacity-0 position-absolute top-0 start-0 w-100 h-100" name="design_files[${index}][]" accept="image/*" multiple style="cursor: pointer;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 live-preview-container d-flex flex-wrap gap-2">
                                        ${imageThumbs}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                });
                designIndex = designs.length;
            } else {
                setFormToCreateMode();
            }
        }
       

        $(document).ready(function () {

           startPicker = flatpickr("#start_date", {
                dateFormat: "d-m-Y",
               
                onChange: function(selectedDates, dateStr, instance) {
                    // When start date changes, update the minimum selectable date for end date
                    if (dateStr) {
                        endPicker.set('minDate', dateStr);
                    } else {
                        endPicker.set('minDate', null); // Reset if start date is cleared
                    }
                }
            });

            endPicker = flatpickr("#end_date", {
                dateFormat: "d-m-Y",
              
                onChange: function(selectedDates, dateStr, instance) {
                    // Optional: When end date changes, update the maximum selectable date for start date
                    if (dateStr) {
                        startPicker.set('maxDate', dateStr);
                    } else {
                        startPicker.set('maxDate', null); // Reset if end date is cleared
                    }
                }
            });
            $('.select2').select2({
                width: '100%',
                placeholder: "Select an option",
                allowClear: true
            });

        //  $(document).on('change', '.design-file-input', function() {
        //     const files = this.files;
        //    const previewContainer = $(this).closest('.design-row').find('.live-preview-container');
        //     previewContainer.empty(); 

        //     if (files.length > 0) {
        //         previewContainer.css('display', 'flex');
        //         Array.from(files).forEach(file => {
        //             const reader = new FileReader();
        //             reader.onload = function (e) {
        //                previewContainer.append(`
        //                     <div class="preview-thumb-wrapper shadow-sm">
        //                         <img src="${e.target.result}">
        //                     </div>
        //                 `);
        //             }
        //             reader.readAsDataURL(file);
        //         });
        //     } else {
        //         previewContainer.hide();
        //     }
        // });

        // Renders live preview thumbnails (with a remove button) from files stored on the input via jQuery.data
function renderDesignPreview(inputEl) {
    const previewContainer = $(inputEl).closest('.design-row').find('.live-preview-container');
    previewContainer.empty();

    const files = $(inputEl).data('files') || [];

    if (files.length > 0) {
        previewContainer.css('display', 'flex');
        files.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewContainer.append(`
                    <div class="preview-thumb-wrapper shadow-sm" data-file-index="${idx}">
                        <img src="${e.target.result}">
                        <span class="remove-preview-img" title="Remove image">&times;</span>
                    </div>
                `);
            }
            reader.readAsDataURL(file);
        });
    } else {
        previewContainer.hide();
    }
}

// When new files are selected, store them (as an array) on the input and render preview
$(document).on('change', '.design-file-input', function () {
    const files = Array.from(this.files);
    $(this).data('files', files);
    renderDesignPreview(this);
});

// Remove a single newly-selected image before submit
$(document).on('click', '.remove-preview-img', function () {
    const wrapper = $(this).closest('.preview-thumb-wrapper');
    const removeIndex = parseInt(wrapper.data('file-index'));
    const designRow = $(this).closest('.design-row');
    const inputEl = designRow.find('.design-file-input')[0];

    let files = $(inputEl).data('files') || [];
    files.splice(removeIndex, 1); // drop the selected file
    $(inputEl).data('files', files);

    // Rebuild the actual input.files so the removed file is NOT submitted with the form
    const dataTransfer = new DataTransfer();
    files.forEach(f => dataTransfer.items.add(f));
    inputEl.files = dataTransfer.files;

    renderDesignPreview(inputEl);
});
            // Dynamic Rows Addition (+)
          $(document).on('click', '.add-design-btn', function() {
    let newCard = `
        <div class="col-md-6 design-row" data-index="${designIndex}">
            <div class="card h-100 shadow-sm border-0 bg-light position-relative">
                <button type="button" class="btn btn-outline-danger btn-xs position-absolute top-0 end-0 m-2 remove-design-btn" style="z-index: 10;"><i class="fas fa-trash-alt"></i></button>
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-semibold">Design Label</label>
                            <input type="text" class="form-control form-control-sm" name="design_names[${designIndex}]" placeholder="Enter Label">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-semibold">Upload Images</label>
                            <div class="custom-file-dropzone border border-dashed rounded text-center p-3 bg-white position-relative" style="cursor: pointer;">
                                <i class="bi bi-cloud-arrow-up fs-4 text-primary d-block mb-1"></i>
                                <span class="small text-muted d-block">Drag files here or <strong>browse</strong></span>
                                <input type="file" class="form-control design-file-input opacity-0 position-absolute top-0 start-0 w-100 h-100" name="design_files[${designIndex}][]" accept="image/*" multiple style="cursor: pointer;">
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 live-preview-container d-flex flex-wrap gap-2" style="display: none;"></div>
                </div>
            </div>
        </div>
    `;
    $('#designContainer').append(newCard);
    designIndex++;
});

            // Dynamic Rows Removal (-)
            $(document).on('click', '.remove-design-btn', function() {
                $(this).closest('.design-row').remove();
            });
            var canUpdate = {{ permissions_check('canUpdate') ? 'true' : 'false' }};
            var canDelete = {{ permissions_check('canDelete') ? 'true' : 'false' }};
            
            var table = $('#campaignData').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('campaign.list') }}",
                autoWidth: false,
                 order: [[1, 'desc']],
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                    { data: 'campaign_name', name: 'campaign_name', orderable: true },
                    { data: 'campaign_type_id', name: 'campaign_type_id', orderable: true },
                    { data: 'assigned_to', name: 'assigned_to', orderable: true },
                    { data: 'start_date', name: 'start_date', className: 'text-center', orderable: true },
                    { data: 'end_date', name: 'end_date', className: 'text-center', orderable: true },
                    { data: 'timeline_status', name: 'timeline_status', className: 'text-center', orderable: false }, // Added here
                    { data: 'expected_budget', name: 'expected_budget', className: 'text-end', orderable: true },
                    { data: 'actual_cost', name: 'actual_cost', className: 'text-end', orderable: true },
                    { data: 'action', orderable: false, searchable: false, visible: canUpdate || canDelete, className: "text-center", width: '15%' }
                ],
                responsive: true
            });
            $('#campaign_type_id').on('change', function() {
                if ($(this).val() === 'add_new_campaign_type') {
                    $('#modal_campaign_type_name').val('');
                    $('#modal_campaign_type_name').removeClass('is-invalid');
                    $('#quickCampaignTypeModal').modal('show');
                    // Deselect options on current base dropdown for smooth interface behavior
                    $(this).val('').trigger('change.select2');
                }
            });

            $('#quick_campaign_type_form').submit(function(e) {
                e.preventDefault();
                $('#modal_campaign_type_name').removeClass('is-invalid');
                let typeName = $('#modal_campaign_type_name').val().trim();

                if (!typeName) {
                    $('#modal_campaign_type_name').addClass('is-invalid');
                    return;
                }

                $('#modalSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                $.ajax({
                    url: "{{ route('campaign_type.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                            
                            // Re-populate dropdown and select new value
                            let newType = response.data[0]; // Returns array of created objects inside controller
                            
                            let option = new Option(newType.campaign_type_name, newType.campaign_type_id, true, true);
                            $('#campaign_type_id').append(option).trigger('change');

                            $('#quickCampaignTypeModal').modal('hide');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errMsg = errors['campaign_type_name.0'] ? errors['campaign_type_name.0'][0] : 'Something went wrong';
                            $('#modal_campaign_type_name').addClass('is-invalid');
                            $('#modal_campaign_type_nameError').text(errMsg).show();
                        } else {
                            iziToast.error({ title: 'Error', message: 'Failed to add campaign type.', position: 'topRight' });
                        }
                    },
                    complete: function() {
                        $('#modalSubmitBtn').prop('disabled', false).html('Add Campaign Type');
                    }
                });
            });

            // Tab switch handling
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#campaign-list') {
                    setFormToCreateMode();
                }
            });

            $(document).on('click', '.view-history', function () {
                const id = $(this).data('id');
                $('#timelineContentContainer').html('<div class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span> Loading timeline logs...</div>');
                $('#timelineHistoryModal').modal('show');
                $.ajax({
                    url: "{{ route('campaign.history', ':id') }}".replace(':id', id),
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success' && response.data.length > 0) {
                            let timelineHtml = '';
                            let currentGroupedDate = '';

                            response.data.forEach(log => {
                                // Inject a prominent date marker when the calendar day changes
                                if (currentGroupedDate !== log.date) {
                                    currentGroupedDate = log.date;
                                    timelineHtml += `<div class="mb-3 mt-2"><span class="badge rounded-pill bg-warning px-3 py-2 text-dark fw-bold shadow-sm">${log.date}</span></div>`;
                                }

                                // Map badge structural style elements depending on the captured event action
                                let actionIcon = 'bi-pencil-fill';
                                let actionColor = 'bg-primary';
                                if (log.action === 'Created') { actionIcon = 'bi-plus-lg'; actionColor = 'bg-success'; }
                                if (log.action === 'Deleted') { actionIcon = 'bi-trash-fill'; actionColor = 'bg-danger'; }

                                // Append standardized card list block components layout
                                timelineHtml += `
                                    <div class="d-flex align-items-start gap-3 mb-3 position-relative ps-2">
                                        <div class="rounded-circle ${actionColor} text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; flex-shrink: 0; z-index: 2;">
                                            <i class="bi ${actionIcon} small"></i>
                                        </div>
                                        <div class="card w-100 border-0 shadow-sm rounded-3">
                                            <div class="card-body p-3 d-flex flex-sm-row flex-column justify-content-between align-items-sm-center gap-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="text-muted small"><i class="bi bi-clock me-1"></i>${log.time}</span>
                                                    <p class="mb-0 text-dark fw-medium"><strong>${log.action}</strong> - ${log.description}</p>
                                                </div>
                                                <span class="badge bg-light text-secondary border px-3 py-2 align-self-start align-self-sm-auto">By ${log.user || 'System'}</span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            $('#timelineContentContainer').html(timelineHtml);
                        } else {
                            $('#timelineContentContainer').html('<div class="text-center py-4 text-muted"><i class="bi bi-exclamation-circle me-1"></i> No activity history tracking found for this campaign record.</div>');
                        }
                    },
                    error: function () {
                        $('#timelineContentContainer').html('<div class="text-center py-4 text-danger"><i class="bi bi-x-circle me-1"></i> Failed to fetch historical timeline summary data.</div>');
                    }
                });
            });


            $(document).on('click', '.view-campaign-details', function () {
    const id = $(this).data('id');
    $('#viewCampaignBody').html('<div class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span> Loading details...</div>');
    $('#viewCampaignModal').modal('show');

    $.ajax({
        url: "{{ route('campaign.view', ':id') }}".replace(':id', id),
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status === 'success') {
                const d = response.data;
                const designs = response.designs;

                let designsHtml = '';
                if (designs && designs.length > 0) {
                    designs.forEach(design => {
                        let imgsHtml = '';
                        if (design.images && design.images.length > 0) {
                            design.images.forEach(url => {
                                imgsHtml += `
                                    <div class="preview-thumb-wrapper shadow-sm" style="width:80px;height:80px;border-radius:6px;overflow:hidden;border:1px solid #dee2e6;">
                                        <a href="${url}" target="_blank">
                                            <img src="${url}" style="width:100%;height:100%;object-fit:cover;">
                                        </a>
                                    </div>`;
                            });
                        } else {
                            imgsHtml = '<span class="text-muted fs-12">No images uploaded</span>';
                        }
                        designsHtml += `
                            <div class="mb-3 p-3 border rounded-3 bg-light">
                                <h6 class="fw-bold fs-13 mb-2">${design.name || 'Untitled Design'}</h6>
                                <div class="d-flex flex-wrap gap-2">${imgsHtml}</div>
                            </div>`;
                    });
                } else {
                    designsHtml = '<div class="text-muted fs-13">No design details added.</div>';
                }

                const html = `
                    <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h5 class="fw-bold text-dark mb-0">${d.campaign_name}</h5>
                        ${d.timeline_status}
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Campaign Type</span><strong>${d.campaign_type_name}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Owner / Assigned To</span><strong>${d.assigned_to_name}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Stall Number</span><strong>${d.stall_number}</strong></div>

                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Start Date</span><strong>${d.start_date}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">End Date</span><strong>${d.end_date}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Number of Visitors</span><strong>${d.num_visitors}</strong></div>

                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Expected Budget</span><strong>${d.expected_budget}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Actual Cost</span><strong>${d.actual_cost}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Venue / Location</span><strong>${d.venue_location}</strong></div>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted fs-12 d-block mb-1">Description / Remarks</span>
                        <div class="p-2 border rounded bg-light fs-13">${d.description}</div>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted fs-12 d-block mb-1">Machines to be Displayed / Used</span>
                        <div class="p-2 border rounded bg-light fs-13">${d.machines_displayed}</div>
                    </div>
                    <hr>
                    <h6 class="fw-bold text-primary mb-3">Posters, Banners & Design Details</h6>
                    ${designsHtml}
                `;

                $('#viewCampaignBody').html(html);
            } else {
                $('#viewCampaignBody').html('<div class="text-center py-4 text-danger">Failed to load campaign details.</div>');
            }
        },
        error: function () {
            $('#viewCampaignBody').html('<div class="text-center py-4 text-danger">Failed to fetch campaign details.</div>');
        }
    });
});
            // Edit processing
            $(document).on('click', '.edit-campaign', function () {
                const id = $(this).data('id');
                $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');
                
                $.ajax({
                    url: "{{ route('campaign.edit', ':id') }}".replace(':id', id),
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            setFormToEditMode(response.data, response.designs);
                            $('a[href="#new-campaign"]').tab('show');
                        } else {
                            iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                            setFormToCreateMode();
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({ title: 'Error', message: 'Failed to fetch details.', position: 'topRight' });
                    },
                    complete: function () {
                        $('#submitBtn').prop('disabled', false).html(editMode ? 'Update' : 'Submit');
                    }
                });
            });

            // Delete processing
            $(document).on('click', '.delete-campaign', function () {
                const id = $(this).data('id');
                iziToast.question({
                    timeout: 20000, close: false, overlay: true, displayMode: 'once', id: 'question', zindex: 999,
                    title: 'Confirm', message: 'Are you sure you want to delete this Campaign?', position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            $.ajax({
                                url: "{{ route('campaign.destroy') }}",
                                type: "POST",
                                data: { _token: "{{ csrf_token() }}", campaign_id: id },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                                        table.ajax.reload();
                                    } else {
                                        iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                                    }
                                },
                                error: function (xhr) {
                                    iziToast.error({ title: 'Error', message: 'Failed to delete record.', position: 'topRight' });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function (instance, toast) { instance.hide({ transitionOut: 'fadeOut' }, toast, 'button'); }]
                    ]
                });
            });

            // Form Submit
            $('#campaign_form').submit(function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                let firstInvalidElement = null;
                let validationMessage = '';
                if (!$('#campaign_name').val().trim()) {
                    $('#campaign_name').addClass('is-invalid');
                    $('#campaign_name').siblings('.invalid-feedback').show();
                    validationMessage = 'Please enter a campaign name.';
                    firstInvalidElement = $('#campaign_name');
                }
                if (!firstInvalidElement && !$('#campaign_type_id').val()) {
                    $('#campaign_type_id').addClass('is-invalid');
                    $('#campaign_type_id').siblings('.invalid-feedback').show();
                    validationMessage = 'Please select a campaign type.';
                    firstInvalidElement = $('#campaign_type_id');
                }
                if (firstInvalidElement) {
                    // Show a warning matching the specific failed field
                    iziToast.warning({ 
                        title: 'Required Field', 
                        message: validationMessage, 
                        position: 'topRight' 
                    });

                    // Scroll cleanly to the invalid element and set focus
                    $('html, body').animate({
                        scrollTop: firstInvalidElement.offset().top - 150
                    }, 300, function() {
                        // If it's a Select2 dropdown, trigger it to open
                        if (firstInvalidElement.hasClass('select2')) {
                            firstInvalidElement.select2('open');
                        } else {
                            firstInvalidElement.focus();
                        }
                    });

                    return; // Stop form submission
                }
                

                $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');
                let formData = new FormData(this);
                $.ajax({
                    url: "{{ route('campaign.store') }}",
                    type: "POST",
                   data: formData,
                   processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function (response) {
                        iziToast.success({
                            title: 'Success', message: response.message, position: 'topRight', timeout: 1000,
                            onClosed: function () {
                                table.ajax.reload();
                                $('a[href="#campaign-list"]').tab('show');
                                setFormToCreateMode();
                            }
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let firstErrorField = null;

                            for (const field in errors) {
                                let element = $('#' + field);
                                element.addClass('is-invalid');
                                element.siblings('.invalid-feedback').text(errors[field][0]).show();
                                
                                if (!firstErrorField) {
                                    firstErrorField = element;
                                    validationMessage = errors[field][0];
                                }
                            }

                            if (firstErrorField) {
                                iziToast.error({ title: 'Validation Error', message: validationMessage, position: 'topRight' });
                                $('html, body').animate({
                                    scrollTop: firstErrorField.offset().top - 150
                                }, 300, function() {
                                    if (firstErrorField.hasClass('select2')) {
                                        firstErrorField.select2('open');
                                    } else {
                                        firstErrorField.focus();
                                    }
                                });
                            }
                        } else {
                            iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Something went wrong', position: 'topRight' });
                        }
                    },
                    complete: function () {
                        $('#submitBtn').prop('disabled', false).html(editMode ? 'Update' : 'Submit');
                    }
                });
            });

            $('#resetFormBtn').on('click', function() {
                setFormToCreateMode();
            });
        });
    </script>
</body>
</html>
