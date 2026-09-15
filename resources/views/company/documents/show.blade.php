<!DOCTYPE html>
<html {!! get_html_data() !!}>
<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <link rel="stylesheet" href="{{ $actual_url . '/admin-assets/css/documents.css' }}">
    <style>
        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s;
            cursor: pointer;
        }

        .upload-zone:hover,
        .upload-zone.dragover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .file-name-display {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-top: 10px;
            word-break: break-all;
        }

        /* Thumbnail Preview Styles */
        .selected-file-preview {
            width: 65px;
            height: 65px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            position: relative;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .selected-file-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }

        .selected-file-preview .file-ext-icon {
            font-size: 24px;
            color: #3b82f6;
            margin-bottom: 2px;
        }

        .selected-file-preview .file-name-tiny {
            font-size: 9px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            text-align: center;
        }

        /* Preivew Modal Styles */
        .preview-img-container {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            margin-bottom: 15px;
        }

        .preview-img-container img {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 4px;
        }

        .file-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .file-card .file-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #eff6ff;
            color: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-right: 15px;
        }
    </style>
</head>
<body class="bg-slate-50">
    @include('include.switcher')
    @include('include.loader')
    <div class="page">
        @include('include.header')
        <div class="page-header-breadcrumb d-md-flex d-block align-items-center justify-content-between d-none"></div>
        <div class="main-content app-content">
            <div class="container-fluid">
                 <div class="d-flex align-items-center justify-content-between mb-4 mt-3">
                    <div class="d-none">
                        <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
                            {{-- <a href="{{ route('documents.index') }}"
                                class="btn btn-sm btn-icon btn-light rounded-circle me-3 shadow-sm"
                                title="Back to Categories">
                                <i class="fa fa-arrow-left"></i>
                            </a> --}}
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-2 d-flex align-items-center justify-content-center"
                                style="width: 36px; height: 36px;">
                                <i class="fa fa-folder-open"></i>
                            </div>
                            {{ $category->d_category }}
                        </h4>
                        <p class="text-muted mb-0 fs-13 ms-5 ps-4">Upload and manage documents for this directory.</p>
                    </div>
                </div>
                <div class="row g-4">
                     @if(session('login_type') !== 'Staff')
                    <div class="col-xl-4 col-lg-5">
                        
                            <div class="card custom-card border-0 shadow-sm transition-all duration-300" style="border-radius: 16px;" id="formCard">
                                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <div class="card-title fw-bold mb-0" id="formTitle">
                                      <i class="fa fa-cloud-upload text-primary me-2"></i>  Upload Document(s)
                                    </div>
                                     <button type="button"
                                    class="btn btn-sm btn-outline-danger d-none py-1 px-2 fs-11 fw-semibold rounded-pill"
                                    id="cancelEditBtn" onclick="resetUploadForm()"><i
                                        class="fa fa-times me-1"></i>Cancel Edit</button>
                                   
                                </div>
                                <div class="card-body p-4">
                                    <form id="DocumentUploadForm" enctype="multipart/form-data">
                                        <input type="hidden" name="encrypted_dc_id" value="{{ $encrypted_id }}">
                                        <!-- Hidden input for batch_id used in Edit mode -->
                                        <input type="hidden" name="batch_id" id="form_batch_id" value="">

                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-slate-700 fs-13 mb-1">Document Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control rounded-3 fs-14" name="file_name"
                                                id="form_file_name" placeholder="e.g., Financial Report 2026" >
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-slate-700 fs-13 mb-1">Assign To Staff</label>
                                            <select class="form-select rounded-3 fs-14" name="assign_to_staff" id="form_assign_to_staff">
                                                 <option value="All Staff">All Staff</option>
                                                 <option value="Export">Export</option>
                                                 <option value="Domestic">Domestic</option>
                                                 <option value="None">No Staff / None</option>
                                             </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-slate-700 fs-13 mb-1">Assign Specific Staff <span class="text-muted fw-normal fs-11">(Optional)</span></label>
                                            <select class="form-select select2-staff rounded-3 fs-14" name="assigned_staff_ids[]" id="form_assigned_staff_ids" multiple data-placeholder="Search and select staff..." style="width: 100%;">
                                                @isset($staffList)
                                                    @foreach($staffList as $staff)
                                                        <option value="{{ $staff->staff_id }}" data-type="{{ $staff->staff_type }}">
                                                            {{ $staff->staff_name }} {{ $staff->staff_type ? '(' . $staff->staff_type . ')' : '' }}
                                                        </option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-slate-700 fs-13 mb-1">File(s) <span
                                                    class="text-danger" id="fileRequiredAsterisk">*</span></label>
                                            <div class="upload-zone" id="uploadZone"
                                                onclick="document.getElementById('certificateFile').click()">
                                                <div id="uploadPrompt">
                                                    <i class="fa fa-file-pdf fs-1 text-slate-400 mb-2"></i>
                                                    <h6 class="fs-14 fw-semibold text-dark mb-1">Click or drag files here
                                                    </h6>
                                                    <p class="fs-12 text-muted mb-0">You can select multiple files at once.
                                                    </p>
                                                    <p class="fs-11 text-muted mb-0">Supported: PDF, JPG, PNG, DOC (Max:
                                                        10MB/file, Max: 5 files)</p>
                                                </div>
                                                <div class="file-name-display mt-3 text-center" id="fileNameDisplay"></div>
                                            </div>
                                            <!-- Required attribute is removed dynamically during edit if files are not strictly needed -->
                                            <input type="file" id="certificateFile" name="certificate[]" class="d-none"
                                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple>
                                            <div id="fileListManager" class="mt-2"></div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch custom-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="is_expiry_date" name="is_expiry_date" value="1">
                                                <label class="form-check-label fw-bold text-slate-700 fs-13"
                                                    for="is_expiry_date">Document has an expiry date?</label>
                                            </div>
                                        </div>

                                        <div class="mb-4 d-none" id="expiryDateContainer">
                                            <label class="form-label fw-bold text-slate-700 fs-13 mb-1">Expiry Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control rounded-3 fs-14" name="expiry_date"
                                                id="expiry_date">
                                        </div>

                                        <button type="submit"
                                            class="btn btn-primary w-100 rounded-pill fw-semibold shadow-sm py-2 transition-all"
                                            id="submitBtn">
                                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"
                                                aria-hidden="true"></span>
                                            <span id="submitBtnText"><i class="fa fa-upload me-1"></i> Upload Files</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                       
                    </div>
                     <!-- DataTable Column -->
                    <div class="col-xl-8 col-lg-7">
                         @else
                    <div class="col-xl-12 col-lg-12">
                    @endif
                        <div class="card custom-card border-0 shadow-sm h-100" style="border-radius: 16px;">
                            <div
                                class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                                <h6 class="card-title fw-bold mb-0"><i class="fa fa-list text-primary me-2"></i>Document
                                    List</h6>
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm" onclick="loadDocuments()"
                                    title="Refresh List"><i class="fa fa-refresh"></i></button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive p-3">
                                    <table class="table text-nowrap w-100" id="documentsTable">
                                        <thead class="bg-slate-50 text-slate-600 fs-12 text-uppercase">
                                            <tr>
                                                <th>#</th>
                                                <th>Document Name</th>
                                                <th>Expiry Status</th>
                                                <th>Uploaded On</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="documentsTableBody">
                                            <!-- Data injected via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      <!-- Document Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog  modal-lg modal-dialog-scrollable">
            <div class="modal-content"
                style="border-radius: 16px; border: none; box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.2);">
                <div class="modal-header custom-modal-header pb-3 pt-3 px-4 rounded-top-4 bg-light border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="previewModalTitle">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-slate-50" id="previewModalBody">
                    <!-- Files will be dynamically injected here -->
                </div>
                <div class="modal-footer border-top-0 pt-0 pe-4 pb-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold"
                        data-bs-dismiss="modal">Close</button>
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
        let globalDocuments = [];
        let expiryPicker = null;
        let customFileList = [];
         function filterStaffDropdown() {
                const selectedType = $('#form_assign_to_staff').val();
                
                $('#form_assigned_staff_ids option').each(function () {
                    const staffType = $(this).data('type') || '';
                    let isMatch = false;

                    if (selectedType === 'All Staff' || !selectedType) {
                        isMatch = true;
                    } else if (selectedType === 'Export') {
                        isMatch = (staffType === 'Export' || staffType === 'Both');
                    } else if (selectedType === 'Domestic') {
                        isMatch = (staffType === 'Domestic' || staffType === 'Both');
                    }

                    if (isMatch) {
                        $(this).prop('disabled', false);
                    } else {
                        $(this).prop('disabled', true);
                        if ($(this).is(':selected')) {
                            $(this).prop('selected', false);
                        }
                    }
                });

                $('#form_assigned_staff_ids').trigger('change.select2');
            }

            $('#form_assign_to_staff').on('change', function () {
                filterStaffDropdown();
            });

        $(document).ready(function () {

            expiryPicker = flatpickr("#expiry_date", {
                dateFormat: "d-m-Y",
                minDate: "today",
                allowInput: false,
                disableMobile: true
            });

            $('#form_assigned_staff_ids').select2({
                placeholder: "Search and select staff...",
                allowClear: true,
                width: '100%'
            });

            $('#form_assign_to_staff').select2({
                placeholder: "Select target...",
                allowClear: true,
                width: '100%'
            });

           

            let docTable = $('#documentsTable').DataTable({
                responsive: true,
                language: { searchPlaceholder: 'Search documents...', sSearch: '' },
                pageLength: 10
            });

            loadDocuments();

            // -------------------------------------------------------------
            // Event Delegation for DataTable Buttons
            // -------------------------------------------------------------
            $('#documentsTable tbody').on('click', '.view-doc-btn', function () {
                openPreviewModal($(this).data('batch'));
            });
            $('#documentsTable tbody').on('click', '.edit-doc-btn', function () {
                editDocument($(this).data('batch'));
            });
            $('#documentsTable tbody').on('click', '.delete-doc-btn', function () {
                deleteDocument($(this).data('batch'), $(this).data('name'));
            });

            // -------------------------------------------------------------
            // Drag and Drop UI
            // -------------------------------------------------------------
            const uploadZone = $('#uploadZone');
            const fileInput = $('#certificateFile');

            uploadZone.on('dragover', function (e) {
                e.preventDefault(); $(this).addClass('dragover');
            }).on('dragleave drop', function (e) {
                e.preventDefault(); $(this).removeClass('dragover');
            });

            uploadZone.on('drop', function (e) {
                const files = e.originalEvent.dataTransfer.files;
                if (files.length) addFilesToList(Array.from(files));
            });

            fileInput.on('change', function () {
                if (this.files.length) {
                    addFilesToList(Array.from(this.files));
                    $(this).val('');
                }
            });


            $('#is_expiry_date').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#expiryDateContainer').removeClass('d-none');
                    $('#expiry_date').attr('required', true);
                } else {
                    $('#expiryDateContainer').addClass('d-none');
                    $('#expiry_date').attr('required', false).val('');
                    expiryPicker.clear();
                }
            });


            $('#DocumentUploadForm').on('submit', function (e) {
                e.preventDefault();

                const isEdit = $('#form_batch_id').val() !== "";

                if (!isEdit && customFileList.length === 0) {
                    iziToast.error({ title: 'Error', message: 'Please select at least one file to upload.', position: 'topRight' });
                    return;
                }

                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true);
                submitBtn.find('.spinner-border').removeClass('d-none');
                submitBtn.find('i').addClass('d-none');

                var formData = new FormData(this);

                formData.delete('certificate[]');
                customFileList.forEach(file => {
                    formData.append('certificate[]', file);
                });

                let ajaxUrl = isEdit ? "{{ route('document.mngt.update') }}" : "{{ route('document.mngt.store') }}";

                $.ajax({
                    url: ajaxUrl,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        if (response.status) {
                            iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                            resetUploadForm();
                            loadDocuments();
                        } else {
                            iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                        }
                    },
                    error: function (xhr) {
                        let errMsg = isEdit ? 'Failed to update document.' : 'Failed to upload document.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errMsg = Object.values(xhr.responseJSON.errors)[0][0];
                        }
                        iziToast.error({ title: 'Error', message: errMsg, position: 'topRight' });
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false);
                        submitBtn.find('.spinner-border').addClass('d-none');
                        submitBtn.find('i').removeClass('d-none');
                    }
                });
            });
        });


        function addFilesToList(newFiles) {
            const maxFiles = 5;
            const maxSize = 10 * 1024 * 1024;

            for (let file of newFiles) {
                if (customFileList.length >= maxFiles) {
                    iziToast.error({ title: 'Limit Reached', message: `Maximum ${maxFiles} files allowed.`, position: 'topRight' });
                    break;
                }
                if (file.size > maxSize) {
                    iziToast.error({ title: 'Too Large', message: `"${file.name}" exceeds 10MB.`, position: 'topRight' });
                    continue;
                }
                // Avoid duplicate file names
                if (customFileList.find(f => f.name === file.name && f.size === file.size)) {
                    iziToast.warning({ title: 'Duplicate', message: `"${file.name}" is already added.`, position: 'topRight' });
                    continue;
                }
                customFileList.push(file);
            }

            renderFileListUI();
        }

        function removeFileFromList(index) {
            customFileList.splice(index, 1);
            renderFileListUI();
        }

        function loadDocuments() {
            $.ajax({
                url: "{{ route('document.mngt.list', $encrypted_id) }}",
                type: "GET",
                success: function (response) {
                    if (response.status) {
                        globalDocuments = response.documents;
                        let table = $('#documentsTable').DataTable();
                        table.clear();

                        response.documents.forEach((doc, index) => {
                            let badgeHtml = '<span class="badge bg-light text-dark shadow-sm">No Expiry</span>';

                            if (doc.is_expiry_date == 1 || doc.is_expiry_date == true) {
                                let today = new Date(); today.setHours(0, 0, 0, 0);
                                let expDate = new Date(doc.raw_expiry_date || doc.expiry_date || doc.formatted_expiry);
                                let diffDays = Math.ceil((expDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));

                                if (diffDays < 0) {
                                    badgeHtml = '<span class="badge bg-danger shadow-sm">Expired</span>';
                                } else if (diffDays <= 30) {
                                    badgeHtml = `<span class="badge bg-warning text-dark shadow-sm">Expiring in ${diffDays}d</span>`;
                                } else {
                                    badgeHtml = `<span class="badge bg-success shadow-sm">Valid (${doc.formatted_expiry})</span>`;
                                }
                            }

                            let fileCountBadge = doc.file_count > 1
                                ? `<span class="badge bg-secondary ms-2 fs-10">${doc.file_count} Files</span>` : '';

                            let assignLabel = doc.assign_to_staff || 'All Staff';
                            let assignBadge = `<br><small class="text-muted"><i class="fa fa-users me-1"></i>${assignLabel}</small>`;
                            if (doc.assigned_staff_ids && doc.assigned_staff_ids.length > 0) {
                                assignBadge += ` <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 fs-10">${doc.assigned_staff_ids.length} Specific Staff</span>`;
                            }

                            let actionBtnHtml = `
                            <button type="button" class="btn btn-sm btn-info text-white rounded-circle shadow-sm me-1 view-doc-btn" title="View" style="width:28px;height:28px;padding:3px;" data-batch="${doc.batch_id}"><i class="fa fa-eye fs-12"></i></button>
                        `;
                            if (response.canUpdate !== false) {
                                actionBtnHtml += `<button type="button" class="btn btn-sm btn-warning text-white rounded-circle shadow-sm me-1 edit-doc-btn" title="Edit" style="width:28px;height:28px;padding:3px;" data-batch="${doc.batch_id}"><i class="fa fa-pencil fs-12"></i></button>`;
                            }
                            if (response.canDelete !== false) {
                                actionBtnHtml += `<button type="button" class="btn btn-sm btn-danger text-white rounded-circle shadow-sm delete-doc-btn" title="Delete" style="width:28px;height:28px;padding:3px;" data-batch="${doc.batch_id}" data-name="${doc.file_name.replace(/"/g, '&quot;')}"><i class="fa fa-trash fs-12"></i></button>`;
                            }

                            table.row.add([
                                index + 1,
                                `<span class="fw-semibold text-dark">${doc.file_name}</span> ${fileCountBadge} ${assignBadge}`,
                                badgeHtml,
                                doc.formatted_date,
                                `<div class="text-end">${actionBtnHtml}</div>`
                            ]);
                        });

                        table.draw();
                    }
                }
            });
        }


        function editDocument(batchId) {
            const docBatch = globalDocuments.find(d => String(d.batch_id) === String(batchId));
            if (!docBatch) return;

            customFileList = [];

            $('#form_batch_id').val(docBatch.batch_id);
            $('#form_file_name').val(docBatch.file_name);

            const assignType = docBatch.assign_to_staff || 'All Staff';
            $('#form_assign_to_staff').val(assignType).trigger('change');
            filterStaffDropdown();

            if (docBatch.assigned_staff_ids && Array.isArray(docBatch.assigned_staff_ids)) {
                $('#form_assigned_staff_ids').val(docBatch.assigned_staff_ids.map(String)).trigger('change.select2');
            } else {
                $('#form_assigned_staff_ids').val([]).trigger('change.select2');
            }

            const isExpiry = (docBatch.is_expiry_date == 1 || docBatch.is_expiry_date === true);
            $('#is_expiry_date').prop('checked', isExpiry).trigger('change');

            if (isExpiry) {
                const dateVal = docBatch.raw_expiry_date || (docBatch.expiry_date ? docBatch.expiry_date.substring(0, 10) : null);
                if (dateVal) expiryPicker.setDate(dateVal, true);
            }

            $('#uploadPrompt').addClass('d-none');

            let html = `<div class="text-warning fw-semibold mb-2 fs-13"><i class="fa fa-pencil me-1"></i>Existing ${docBatch.files.length} file(s) — add new files below to append:</div>`;
                html += `<div class="d-flex flex-wrap justify-content-center gap-2 mb-2">`;

            docBatch.files.forEach(file => {
                    let thumbHtml = '';
                    if (file.is_image) {
                        thumbHtml = `<img src="${file.url}" alt="${file.name}" style="width:100%;height:100%;object-fit:cover;border-radius:4px;">`;
                    } else {
                        let iconClass = 'fa-file-alt';
                        if (file.name && file.name.toLowerCase().endsWith('.pdf')) iconClass = 'fa-file-pdf text-danger';
                        else if (file.name && (file.name.toLowerCase().endsWith('.doc') || file.name.toLowerCase().endsWith('.docx'))) iconClass = 'fa-file-word text-primary';

                        thumbHtml = `<i class="fa ${iconClass} file-ext-icon"></i>`;
                    }

                    html += `
                        <div class="position-relative" style="width:75px;height:75px;margin:2px;">
                            <div class="selected-file-preview bg-light w-100 h-100" title="${file.name || 'File'}" style="border:2px solid #f59e0b;">
                                ${thumbHtml}
                                <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(255,255,255,0.9);padding:1px 3px;font-size:8px;text-align:center;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;border-radius:0 0 4px 4px;">
                                    ${file.name || 'File'}
                                </div>
                            </div>
        <button type="button" onclick="deleteExistingFile('${file.id}', this)" title="Remove existing file"
                                style="position:absolute;top:-6px;right:-6px;width:18px;height:18px;border-radius:50%;background:#ef4444;border:none;color:#fff;font-size:9px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.2);z-index:99;padding:0;">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    `;
                });

                html += `</div>`;
            html += `<div class="fs-11 text-muted text-center mb-0" style="cursor:pointer;" onclick="event.stopPropagation(); document.getElementById('certificateFile').click();">
                        <i class="fa fa-plus-circle text-warning me-1"></i>Click or drag here to append new files
                     </div>`;
            $('#fileNameDisplay').html(html);

            $('#formTitle').html('<i class="fa fa-pencil text-warning me-2"></i>Edit Document(s)');
            $('#submitBtnText').html('<i class="fa fa-save me-1"></i> Update Details');
            $('#submitBtn').removeClass('btn-primary').addClass('btn-warning text-dark');
            $('#cancelEditBtn').removeClass('d-none');
            $('#certificateFile').attr('required', false);
            $('#fileRequiredAsterisk').addClass('d-none');

            $('html, body').animate({ scrollTop: $("#formCard").offset().top - 100 }, 500);
            $('#formCard').css('box-shadow', '0 0 0 0.25rem rgba(245, 158, 11, 0.25)');
            setTimeout(() => { $('#formCard').css('box-shadow', 'var(--bs-box-shadow-sm)'); }, 1000);
        }


        function renderFileListUI() {
            const isEdit = $('#form_batch_id').val() !== "";
            const displayDiv = $('#fileNameDisplay');
            const promptDiv = $('#uploadPrompt');

            if (!isEdit) {
                // Upload mode: normal full render
            if (customFileList.length === 0) {
                promptDiv.removeClass('d-none');
                displayDiv.html('');
                return;
            }

            promptDiv.addClass('d-none');
            let html = `<div class="text-primary fw-bold mb-2 fs-13">${customFileList.length} file(s) selected:</div>`;
            html += `<div class="d-flex flex-wrap justify-content-center gap-2">`;
            customFileList.forEach((file, index) => html += buildThumbHtml(file, index));
            html += `</div>`;
            html += `<div class="mt-2 text-muted fs-11" style="cursor:pointer;" onclick="event.stopPropagation(); document.getElementById('certificateFile').click();">
                    <i class="fa fa-plus-circle text-primary me-1"></i>Add more files
                 </div>`;
            displayDiv.html(html);

            } else {

                $('#newFilesSection').remove();

                if (customFileList.length === 0) return;

                let html = `<div id="newFilesSection" class="mt-2 pt-2 border-top">`;
                html += `<div class="text-success fw-semibold mb-2 fs-13"><i class="fa fa-plus-circle me-1"></i>${customFileList.length} new file(s) to append:</div>`;
                html += `<div class="d-flex flex-wrap justify-content-center gap-2">`;
                customFileList.forEach((file, index) => html += buildThumbHtml(file, index));
                html += `</div></div>`;

                displayDiv.append(html);
            }
        }

        function buildThumbHtml(file, index) {
            let thumbContent = '';
            if (file.type && file.type.startsWith('image/')) {
                const objectUrl = URL.createObjectURL(file);
                thumbContent = `<img src="${objectUrl}" alt="preview" style="width:100%;height:100%;object-fit:cover;border-radius:4px;">`;
            } else {
                let iconClass = 'fa-file-alt';
                if (file.type && file.type.includes('pdf')) iconClass = 'fa-file-pdf text-danger';
                else if (file.name && (file.name.endsWith('.doc') || file.name.endsWith('.docx'))) iconClass = 'fa-file-word text-primary';
                thumbContent = `<i class="fa ${iconClass} file-ext-icon"></i>`;
            }

            return `
    <div class="position-relative" style="width:75px;height:75px;margin:2px;">
        <div class="selected-file-preview bg-light w-100 h-100" title="${file.name}">
            ${thumbContent}
            <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(255,255,255,0.9);padding:1px 3px;font-size:8px;text-align:center;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;border-radius:0 0 4px 4px;">${file.name}</div>
        </div>
        <button type="button" onclick="removeFileFromList(${index})" title="Remove"
            style="position:absolute;top:-6px;right:-6px;width:18px;height:18px;border-radius:50%;background:#ef4444;border:none;color:#fff;font-size:9px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.2);z-index:99;padding:0;">
            <i class="fa fa-times"></i>
        </button>
    </div>
`;
        }


        function resetUploadForm() {
            $('#DocumentUploadForm')[0].reset();
            $('#form_batch_id').val('');
            customFileList = [];

            $('#form_assign_to_staff').val('All Staff').trigger('change');
            if (typeof filterStaffDropdown === 'function') filterStaffDropdown();
            $('#form_assigned_staff_ids').val([]).trigger('change.select2');

            $('#uploadPrompt').removeClass('d-none');
            $('#fileNameDisplay').html('');
            $('#newFilesSection').remove();

            $('#expiryDateContainer').addClass('d-none');
            $('#expiry_date').attr('required', false);
            expiryPicker.clear();

            $('#formTitle').html('<i class="fa fa-cloud-upload text-primary me-2"></i>Upload Document(s)');
            $('#submitBtnText').html('<i class="fa fa-upload me-1"></i> Upload Files');
            $('#submitBtn').removeClass('btn-warning text-dark').addClass('btn-primary');
            $('#cancelEditBtn').addClass('d-none');
            $('#certificateFile').attr('required', false); // managed manually via customFileList
            $('#fileRequiredAsterisk').removeClass('d-none');
        }


        function openPreviewModal(batchId) {
            const docBatch = globalDocuments.find(d => String(d.batch_id) === String(batchId));
            if (!docBatch) return;

            $('#previewModalTitle').text(docBatch.file_name);
            let bodyHtml = '';

            docBatch.files.forEach((file, i) => {
                let label = docBatch.files.length > 1 ? `<div class="mb-2 fw-semibold text-muted fs-12 text-uppercase">File ${i + 1}</div>` : '';

                if (file.is_image) {
                    bodyHtml += `${label}<div class="preview-img-container shadow-sm"><img src="${file.url}" alt="Preview" class="img-fluid" loading="lazy"></div>`;
                } else {
                    bodyHtml += `
                    ${label}
                    <div class="file-card shadow-sm">
                        <div class="d-flex align-items-center overflow-hidden">
                            <div class="file-icon flex-shrink-0"><i class="fa fa-file-pdf"></i></div>
                            <div class="text-truncate">
                                <h6 class="mb-0 fw-semibold fs-14 text-dark text-truncate" title="${file.name}">Document</h6>
                                <span class="fs-12 text-muted">Click open to view securely</span>
                            </div>
                        </div>
                        <a href="${file.url}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold flex-shrink-0 shadow-sm">Open <i class="fa fa-external-link ms-1"></i></a>
                    </div>
                `;
                }
            });

            $('#previewModalBody').html(bodyHtml);
            $('#previewModal').modal('show');
        }


        function deleteDocument(batchId, name) {
            iziToast.question({
                timeout: 20000, close: false, overlay: true, displayMode: 'once',
                id: 'question', zindex: 999, title: 'Confirm Deletion',
                message: `<div class="mt-2 text-slate-600 fs-13">Are you sure you want to delete <br><b class="text-danger fs-14">"${name}"</b>?</div>`,
                position: 'center',
                buttons: [
                    ['<button class="btn btn-danger btn-sm text-white px-3 py-1 rounded-pill fw-semibold shadow-sm">Delete</button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        $.ajax({
                            url: "{{ route('document.mngt.delete') }}",
                            type: "POST",
                            data: { batch_id: batchId, _token: $('meta[name="csrf-token"]').attr('content') },
                            success: function (response) {
                                if (response.status) {
                                    iziToast.success({ title: 'Deleted', message: response.message, position: 'topRight' });
                                    if ($('#form_batch_id').val() == batchId) resetUploadForm();
                                    loadDocuments();
                                } else {
                                    iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                                }
                            }
                        });
                    }, true],
                    ['<button class="btn btn-light btn-sm px-3 py-1 rounded-pill fw-semibold">Cancel</button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }]
                ]
            });
        }
    </script>
</body>
</html>
