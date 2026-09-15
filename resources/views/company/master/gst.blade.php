<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        .gst-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .gst-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            border-color: #38cab3; /* Theme accent color */
        }
        .gst-card .card-body {
            padding: 1.5rem;
        }
        .gst-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            margin-bottom: 1rem;
        }
        .gst-meta {
            font-size: 0.8rem;
            color: #64748b;
        }
        .gst-meta-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.2rem;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        .gst-actions {
            border-top: 1px solid #e2e8f0;
            padding-top: 1rem;
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            gap: 0.5rem;
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
                                <ul class="nav nav-tabs nav-tabs-footer mb-0" role="tablist">
                                        @if (permissions_check('canView'))
                                        <li class="nav-item">
                                            <a class="nav-link active"
                                            data-bs-toggle="tab"
                                            role="tab"
                                            href="#gst-list">
                                                List
                                            </a>
                                        </li>
                                        @endif
                                        @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link"
                                            data-bs-toggle="tab"
                                            role="tab"
                                            href="#new-gst">
                                                New Gst(%)
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                    <div class="tab-pane fade show active" id="gst-list">
                                        
                                        <!-- Header: Stats & Search -->
                                        <div class="row mb-4 align-items-center">
                                            <div class="col-md-6 mb-3 mb-md-0">
                                                <div class="d-flex align-items-center">
                                                    <h6 class="mb-0 fw-bold text-muted">Total GST Rates: <span id="totalGstCount" class="badge bg-primary rounded-pill ms-2">0</span></h6>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <div class="input-group" style="max-width: 300px; margin-left: auto;">
                                                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                                    <input type="text" id="gstSearchInput" class="form-control" placeholder="Search by GST %...">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cards Container -->
                                        <div class="row" id="gstCardContainer">
                                            <!-- Cards will be injected here via JS -->
                                            <div class="col-12 text-center text-muted py-5">
                                                <span class="spinner-border spinner-border-sm me-2"></span> Loading...
                                            </div>
                                        </div>

                                    </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                    <div class="tab-pane text-muted px-1" id="new-gst" role="tabpanel">
                                         <form id="gst_form" enctype="multipart/form-data" novalidate>
                                                @csrf

                                             <!-- Card 1: Form Fields -->
                                             <div class="modern-card">
                                                
                                                <div id="groupNameContainer">
                                                    <div class="row group-name-row col-md-12 mb-3">
                                                        <div class="col-md-4">
                                                            <label for="gst_no" class="form-label">Gst(%)<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="gst_no[]"
                                                                required placeholder="Enter Gst(%)">
                                                            <div class="invalid-feedback gst_noError"></div>
                                                        </div>

                                                        <div class="col-md-1 d-flex align-items-end">
                                                            <button type="button"
                                                                class="btn btn-success add-group-btn">+</button>
                                                        </div>
                                                    </div>
                                                </div>
                                             </div>
                                                
                                             <div class="d-flex justify-content-end gap-2 mt-2">
                                                 <button type="submit" class="btn btn-primary"
                                                     id="submitBtn">Submit</button>
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
        @include('include.footer')
    </div>
    @include('include.scroll-top')
    @include('include.footer_link')
    @include('include.datatable_js_link')



     <script>
        let editMode = false;

        function setFormToCreateMode() {
            editMode = false;
            $('#gst_form').removeAttr('data-mode');
            $('#companyTabLabel').text('New Gst(%)');
            $('#submitBtn').text('Submit');
            $('input[name="gst_id[]"]').remove();
            // Clear input fields
            $('.group-name-row:not(:first)').remove();
            $('.group-name-row:first input[name="gst_no[]"]').val('');

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        function setFormToEditMode(groupName, groupId) {
            editMode = true;
            $('#gst_form').attr('data-mode', 'edit');
            $('#companyTabLabel').text('Edit Gst(%)');
            $('#submitBtn').text('Update');
            // Remove any old hidden fields then add new one
            $('input[name="gst_id[]"]').remove();
            $('#gst_form').append(
                `<input type="hidden" name="gst_id[]" value="${groupId}">`
            );
            // Only single value
            $('.group-name-row:not(:first)').remove();
            $('.group-name-row:first input[name="gst_no[]"]').val(groupName);


            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        $(document).ready(function () {
             $('.select2').select2({
    width: '100%',
    placeholder: "Select an option", // Required for allowClear
    allowClear: true
});
            // Card View Init
            var canUpdate = {{ permissions_check('canUpdate') ? 'true' : 'false' }};
            var canDelete = {{ permissions_check('canDelete') ? 'true' : 'false' }};
            
            let allGstData = [];

            function loadGstCards() {
                $.ajax({
                    url: "{{ route('gst.list') }}",
                    type: "GET",
                    data: {
                        length: -1, // Fetch all records
                        start: 0,
                        draw: 1
                    },
                    success: function(res) {
                        // DataTables backend response handling
                        allGstData = res.data || [];
                        
                        // Sort by GST percentage ascending
                        allGstData.sort(function(a, b) {
                            return parseFloat(a.gst_no) - parseFloat(b.gst_no);
                        });
                        
                        $('#totalGstCount').text(allGstData.length);
                        renderGstCards(allGstData);
                    },
                    error: function(xhr) {
                        console.error('Failed to load GST data', xhr);
                        $('#gstCardContainer').html('<div class="col-12 text-center text-danger py-5">Failed to load data.</div>');
                    }
                });
            }

            function renderGstCards(data) {
                const container = $('#gstCardContainer');
                container.empty();
                
                if (data.length === 0) {
                    container.html('<div class="col-12 text-center text-muted py-5">No GST records found.</div>');
                    return;
                }

                let html = '';
                data.forEach(function(row) {
                    html += `
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                        <div class="card gst-card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="gst-value">${row.gst_no}%</div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="gst-meta text-start">
                                        <div class="gst-meta-label">Created By</div>
                                        <div>${row.created_by}</div>
                                    </div>
                                    <div class="gst-meta text-end">
                                        <div class="gst-meta-label">Created On</div>
                                        <div>${row.created_at}</div>
                                    </div>
                                </div>
                                
                                <div class="mt-auto gst-actions text-center" style="display: ${canUpdate || canDelete ? 'flex' : 'none'}">
                                    ${row.action}
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                });
                container.html(html);
            }

            // Initial load
            loadGstCards();

            // Search functionality
            $('#gstSearchInput').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                const filtered = allGstData.filter(function(row) {
                    return row.gst_no.toString().toLowerCase().includes(searchTerm);
                });
                renderGstCards(filtered);
            });

            $('#refreshBtn').click(() => loadGstCards());

            // Add/remove group name field
            var groupNameContainer = document.getElementById('groupNameContainer');
            if (groupNameContainer) {
                groupNameContainer.addEventListener('click', function (e) {
                    if (e.target.classList.contains('add-group-btn')) {
                        const newField = document.createElement('div');
                        newField.className = 'row group-name-row col-md-12 mb-3';
                        newField.innerHTML = `
                        <div class="col-md-4">
                            <label class="form-label">Gst(%)</label>
                            <input type="text" class="form-control" name="gst_no[]" required>
                            <div class="invalid-feedback gst_noError"></div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-group-btn">−</button>
                        </div>
                    `;
                        this.appendChild(newField);
                        $('#groupNameContainer .select2').select2({
                            width: '100%'
                        });
                    }
                    if (e.target.classList.contains('remove-group-btn')) {
                        e.target.closest('.group-name-row').remove();
                    }
                });
            }

            // Tab show event: update tab label etc on display
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#new-gst') {
                    if (editMode) {
                        $('#companyTabLabel').text('Edit Gst(%)');
                        $('#submitBtn').text('Update');
                    } else {
                        setFormToCreateMode(); // will also update label etc.
                    }
                }
                // If switching to list, always reset form
                if (target === '#gst-list') {
                    setFormToCreateMode();
                }
            });

            // Handle edit button click
            $(document).on('click', '.edit-group', function () {
                const groupId = $(this).data('id');
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Loading...');
                $.ajax({
                    url: "{{ route('gst.edit', ':id') }}".replace(':id', groupId),
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            // Pass projectstage value as third parameter
                            setFormToEditMode(response.data.gst_no, response.data.gst_id);
                            $('a[href="#new-gst"]').tab('show');
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: response.message,
                                position: 'topRight'
                            });
                            setFormToCreateMode();
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({
                            title: 'Error',
                            message: xhr.responseJSON?.message || 'Failed to fetch group data',
                            position: 'topRight'
                        });
                        setFormToCreateMode();
                    },
                    complete: function () {
                        $('#submitBtn').prop('disabled', false);
                    }
                });
            });


            // Handle delete
            $(document).on('click', '.delete-group', function () {
                const groupId = $(this).data('id');
                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete this Gst(%).?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                url: "{{ route('gst.destroy') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    gst_id: groupId
                                },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        iziToast.success({
                                            title: 'Success',
                                            message: response.message,
                                            position: 'topRight'
                                        });
                                        loadGstCards();
                                    } else {
                                        iziToast.error({
                                            title: 'Error',
                                            message: response.message,
                                            position: 'topRight'
                                        });
                                    }
                                },
                                error: function (xhr) {
                                    iziToast.error({
                                        title: 'Error',
                                        message: xhr.responseJSON
                                            ?.message ||
                                            'Failed to delete Gst(%).',
                                        position: 'topRight'
                                    });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function (instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                        }]
                    ]
                });
            });

            // Handle form submission (add and edit)
            $('#gst_form').submit(function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                let isValid = true;
                const gst_nos = [];
                
                $('input[name="gst_no[]"]').each(function (index) {
                    const val = $(this).val().trim();

                    if (!val) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text('Please enter a Gst(%)').show();
                        iziToast.warning({
                            title: 'Warning',
                            message: 'Please enter a GST(%).',
                            position: 'topRight'
                        });
                        isValid = false;
                        return false;
                    } else {
                        gst_nos.push(val);
                    }


                });

                if (!isValid) {
                    return;
                }
                const formData = {
                    gst_no: gst_nos,
                    _token: $('input[name="_token"]').val()
                };
                if (editMode) formData.gst_id = $('input[name="gst_id[]"]').val();

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    url: "{{ route('gst.store') }}",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            timeout: 700,
                            onClosed: function () {
                                loadGstCards();
                                $('a[href="#gst-list"]').tab('show');
                                setFormToCreateMode(); // reset after submit
                            }
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const field in errors) {
                                if (field.startsWith('gst_no.')) {
                                    const index = field.split('.')[1];
                                    $('input[name="gst_no[]"]').eq(index).addClass(
                                        'is-invalid')
                                        .next('.invalid-feedback').text(errors[field][0])
                                        .show();
                                }
                            }
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: xhr.responseJSON?.message || 'Something went wrong',
                                position: 'topRight'
                            });
                        }
                    },
                    complete: function () {
                        $('#submitBtn').prop('disabled', false).html(editMode ? 'Update' : 'Submit');
                    }
                });
            });

            // Submit form on Enter keypress globally when the form tab is active
            $(document).on('keypress', function (e) {
                if (e.which === 13) {
                    if ($(e.target).is('.select2-search__field, input[type="search"]') || $(e.target).closest('#typehead').length) {
                        return;
                    }
                    if ($(e.target).is('button, a, textarea')) {
                        return;
                    }
                    let $btn = $('#submitBtn');
                    if ($btn.is(':visible')) {
                        e.preventDefault();
                        $btn.click();
                    }
                }
            });

            // Reset form handler: set back to create mode
            $('#gst_form').on('reset', function () {
                setFormToCreateMode();
            });

            // Real-time validation for name fields (all, since dynamic)
            // Real-time validation for name fields (all, since dynamic)
            $(document).on('input', 'input[name="gst_no[]"]', function () {
                if ($(this).val().trim()) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').hide();
                }
            });



        });
    </script>
</body>

</html>
