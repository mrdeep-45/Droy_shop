<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
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
                                            <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#source-list">
                                                List
                                            </a>
                                        </li>
                                    @endif
                                    @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" role="tab" href="#new-source">
                                                New Lead Source
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                        <div class="tab-pane border-0 show active text-muted px-1 " id="source-list"
                                            role="tabpanel">
                                            <div class="row">
                                                <div>
                                                    <table id="stockData" class="table table-bordered w-100"
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:5%;">#</th>
                                                                <th class="text-center" style="width:25%">Source Type</th>
                                                                <th>Source Names</th>
                                                                <th class="text-center" style="width:20%">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                        <div class="tab-pane text-muted px-1" id="new-source" role="tabpanel">
                                            <form id="lead_source_form" enctype="multipart/form-data" novalidate>
                                                @csrf
                                                <div id="sourceFormContainer" class="col-xl-8 col-lg-9 col-md-11 px-0">
                                                    <!-- Row 1: Source Type, First Source Name, + Button -->
                                                    <div class="row mb-3 align-items-end first-source-row">
                                                        <div class="col-12 col-md-5 mb-2 mb-md-0">
                                                            <label for="source_type_select" class="form-label">Source Type <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select select2" id="source_type_select" name="source_type" required>
                                                                <option value="">Select Source Type</option>
                                                                <option value="Online">Online</option>
                                                                <option value="Offline">Offline</option>
                                                            </select>
                                                            <div class="invalid-feedback source_typeError">
                                                            </div>
                                                        </div>
                                                        <div class="col-10 col-md-5 mb-2 mb-md-0">
                                                            <label class="form-label">Source Name <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="source_name[]"
                                                                required placeholder="Enter Source Name">
                                                            <div class="invalid-feedback source_nameError">
                                                            </div>
                                                        </div>
                                                        <div class="col-2 col-md-2 d-flex justify-content-start mb-2 mb-md-0">
                                                            <button type="button"
                                                                class="btn btn-success add-source-name-btn d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; padding: 0; font-size: 1.25rem;">+</button>
                                                        </div>
                                                    </div>
                                                    <!-- Additional Dynamic Source Name Rows Container -->
                                                    <div id="additionalSourcesContainer"></div>
                                                </div>
                                                <div class="d-grid gap-2 d-md-flex justify-content-md-end col-xl-8 col-lg-9 col-md-11 px-0 mt-3">
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

        function alignSourceRowHeights() {
            $('#stockData tbody tr').each(function() {
                const $row = $(this);
                const nameItems = $row.find('.source-name-item');
                const actionItems = $row.find('.source-action-item');
                nameItems.each(function(index) {
                    const $name = $(this);
                    const $action = actionItems.eq(index);
                    if ($action.length) {
                        $name.css('height', 'auto');
                        $action.css('height', 'auto');
                        const h1 = $name.outerHeight();
                        const h2 = $action.outerHeight();
                        const maxH = Math.max(h1, h2);
                        $name.css('height', maxH + 'px');
                        $action.css('height', maxH + 'px');
                    }
                });
            });
        }

        function setFormToCreateMode() {
            editMode = false;
            $('#lead_source_form').removeAttr('data-mode');
            $('#companyTabLabel').text('New Source');
            $('#submitBtn').text('Submit');
            $('input[name="source_id[]"]').remove();
            // Clear input fields
            $('#additionalSourcesContainer').empty();
            $('.first-source-row input[name="source_name[]"]').val('');
            $('#source_type_select').val('').trigger('change');
            $('.add-source-name-btn').show();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
            $('.select2-container').removeClass('is-invalid');
        }

        function setFormToEditMode(sources, sourceType) {
            editMode = true;
            $('#lead_source_form').attr('data-mode', 'edit');
            $('#companyTabLabel').text('Edit Source');
            $('#submitBtn').text('Update');
            
            // Remove any old hidden fields then add new ones
            $('input[name="source_id[]"]').remove();
            
            $('#additionalSourcesContainer').empty();
            $('#source_type_select').val(sourceType).trigger('change');
            $('.add-source-name-btn').show();
            
            if (sources && sources.length > 0) {
                // Populate first source
                $('.first-source-row input[name="source_name[]"]').val(sources[0].title);
                $('.first-source-row').append(
                    `<input type="hidden" name="source_id[]" value="${sources[0].source_id}">`
                );
                
                // Populate subsequent sources
                for (let i = 1; i < sources.length; i++) {
                    const newField = document.createElement('div');
                    newField.className = 'row mb-3 align-items-end additional-source-row';
                    newField.innerHTML = `
                        <div class="col-md-5 d-none d-md-block">
                            <!-- Desktop alignment placeholder -->
                        </div>
                        <div class="col-10 col-md-5 mb-2 mb-md-0">
                            <label class="form-label d-md-none">Source Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="source_name[]" required placeholder="Enter Source Name" value="${sources[i].title}">
                            <input type="hidden" name="source_id[]" value="${sources[i].source_id}">
                            <div class="invalid-feedback source_nameError"></div>
                        </div>
                        <div class="col-2 col-md-2 d-flex justify-content-start mb-2 mb-md-0">
                            <button type="button" class="btn btn-danger remove-source-name-btn d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; padding: 0; font-size: 1.25rem;">−</button>
                        </div>
                    `;
                    document.getElementById('additionalSourcesContainer').appendChild(newField);
                }
            }
            
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
            $('.select2-container').removeClass('is-invalid');
        }

        $(document).ready(function () {
            var canUpdate = {{ permissions_check('canUpdate') ? 'true' : 'false' }};
            var canDelete = {{ permissions_check('canDelete') ? 'true' : 'false' }};

            // DataTable Init
            var table = $('#stockData').DataTable({
                processing: true,
                serverSide: false,
                responsive: true,
                ajax: {
                    url: "{{ route('leadsource.list') }}",
                    type: "GET",
                    dataSrc: 'data'
                },
                columns: [
                    {
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'source_type',
                        className: 'text-center align-middle'
                    },
                    {
                        data: null,
                        className: 'align-middle',
                       render: (data, type, row) => {
    return row.sources.map(s => `
        <div class="py-1 d-flex align-items-start">
            <span class="me-2">•</span>
            <span>${s.title}</span>
        </div>
    `).join('');
}
                    },
                    {
                        data: null,
                        className: 'text-center align-middle',
                        visible: canUpdate || canDelete,
                        render: (data, type, row) => {
                            let buttons = `<div class="d-flex gap-1 justify-content-center align-items-center text-nowrap">`;
                            if (canUpdate) {
                                buttons += `
                                    <button class="btn btn-sm btn-primary edit-group py-1" data-id="${row.source_type}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>`;
                            }
                            if (canDelete) {
                                buttons += `
                                    <button class="btn btn-sm btn-danger delete-group py-1" data-id="${row.source_type}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>`;
                            }
                            buttons += `</div>`;
                            return buttons;
                        }
                    }
                ],
                order: [],
                responsive: true
            });

            $('#refreshBtn').click(() => table.ajax.reload());

            // Initialize first select2
            $('.select2').select2({
                width: '100%'
            });

            // Add/remove source name fields
            var sourceFormContainer = document.getElementById('sourceFormContainer');
            if (sourceFormContainer) {
                sourceFormContainer.addEventListener('click', function (e) {
                    if (e.target.classList.contains('add-source-name-btn')) {
                        const newField = document.createElement('div');
                        newField.className = 'row mb-3 align-items-end additional-source-row';
                        newField.innerHTML = `
                        <div class="col-md-5 d-none d-md-block">
                            <!-- Desktop alignment placeholder -->
                        </div>
                        <div class="col-10 col-md-5 mb-2 mb-md-0">
                            <label class="form-label d-md-none">Source Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="source_name[]" required placeholder="Enter Source Name">
                            <div class="invalid-feedback source_nameError"></div>
                        </div>
                        <div class="col-2 col-md-2 d-flex justify-content-start mb-2 mb-md-0">
                            <button type="button" class="btn btn-danger remove-source-name-btn d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; padding: 0; font-size: 1.25rem;">−</button>
                        </div>
                    `;
                        document.getElementById('additionalSourcesContainer').appendChild(newField);
                    }
                    if (e.target.classList.contains('remove-source-name-btn')) {
                        e.target.closest('.additional-source-row').remove();
                    }
                });
            }

            // Tab show event: update tab label etc on display
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#new-source') {
                    if (editMode) {
                        $('#companyTabLabel').text('Edit Source');
                        $('#submitBtn').text('Update');
                    } else {
                        setFormToCreateMode(); // will also update label etc.
                    }
                }
                // If switching to list, always reset form
                if (target === '#source-list') {
                    setFormToCreateMode();
                }
            });

            // Handle edit button click
            $(document).on('click', '.edit-group', function () {
                const groupId = $(this).data('id');
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Loading...');
                $.ajax({
                    url: "{{ route('leadsource.edit', ':id') }}".replace(':id', groupId)
                    , type: "GET"
                    , dataType: "json"
                    , success: function (response) {
                        if (response.status === 'success') {
                            setFormToEditMode(response.data.sources, response.data.source_type);
                            $('a[href="#new-source"]').tab('show');
                        } else {
                            iziToast.error({
                                title: 'Error'
                                , message: response.message
                                , position: 'topRight'
                            });
                            setFormToCreateMode();
                        }
                    }
                    , error: function (xhr) {
                        iziToast.error({
                            title: 'Error'
                            , message: xhr.responseJSON?.message ||
                                'Failed to fetch group data'
                            , position: 'topRight'
                        });
                        setFormToCreateMode();
                    }
                    , complete: function () {
                        $('#submitBtn').prop('disabled', false);
                    }
                });
            });

            // Handle delete
            $(document).on('click', '.delete-group', function () {
                const groupId = $(this).data('id');
                iziToast.question({
                    timeout: 20000
                    , close: false
                    , overlay: true
                    , displayMode: 'once'
                    , id: 'question'
                    , zindex: 999
                    , title: 'Confirm'
                    , message: 'Are you sure you want to delete this Lead Source?'
                    , position: 'center'
                    , buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                url: "{{ route('leadsource.destroy') }}"
                                , type: "POST"
                                , data: {
                                    _token: "{{ csrf_token() }}"
                                    , source_id: groupId
                                }
                                , success: function (response) {
                                    if (response.status === 'success') {
                                        iziToast.success({
                                            title: 'Success'
                                            , message: response.message
                                            , position: 'topRight'
                                        });
                                        table.ajax.reload();
                                    } else {
                                        iziToast.error({
                                            title: 'Error'
                                            , message: response.message
                                            , position: 'topRight'
                                        });
                                    }
                                }
                                , error: function (xhr) {
                                    iziToast.error({
                                        title: 'Error'
                                        , message: xhr.responseJSON?.message ||
                                            'Failed to delete Status.'
                                        , position: 'topRight'
                                    });
                                }
                            });
                        }, true]
                        , ['<button>NO</button>', function (instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                        }]
                    ]
                });
            });

            // Handle form submission (add and edit)
            $('#lead_source_form').submit(function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                $('.select2-container').removeClass('is-invalid');
                let isValid = true;
                const source_names = [];
                $('input[name="source_name[]"]').each(function (index) {
                    const val = $(this).val().trim();
                    if (!val) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text('Please enter a Source name').show();
                        isValid = false;
                    } else {
                        source_names.push(val);
                    }
                });
                const source_type = $('#source_type_select').val();
                if (!source_type) {
                    $('#source_type_select').addClass('is-invalid');
                    $('#source_type_select').next('.select2-container').addClass('is-invalid');
                    $('#source_type_select').parent().find('.invalid-feedback').text('Please select a Source Type').show();
                    isValid = false;
                }
                if (!isValid) return;
                // Prepare form data
                const source_ids = [];
                $('input[name="source_id[]"]').each(function () {
                    source_ids.push($(this).val());
                });
                const formData = {
                    source_name: source_names
                    , source_type: source_type
                    , source_id: source_ids
                    , _token: $('input[name="_token"]').val()
                };
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Processing...');
                $.ajax({
                    url: "{{ route('leadsource.store') }}"
                    , type: "POST"
                    , data: formData
                    , dataType: "json"
                    , success: function (response) {
                        iziToast.success({
                            title: 'Success'
                            , message: response.message
                            , position: 'topRight'
                            , timeout: 700
                            , onClosed: function () {
                                $('#stockData').DataTable().ajax.reload();
                                $('a[href="#source-list"]').tab('show');
                                setFormToCreateMode(); // reset after submit
                            }
                        });
                    }
                    , error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            // Show ALL Laravel validation errors using IziToast
                            Object.values(errors).forEach(errArray => {
                                iziToast.info({
                                    title: 'Info',
                                    message: errArray[0],
                                    position: 'topRight'
                                });
                            });

                            // Handle validation errors for input highlights
                            for (const field in errors) {
                                if (field.startsWith('source_name.')) {
                                    const index = field.split('.')[1];
                                    $('input[name="source_name[]"]').eq(index)
                                        .addClass('is-invalid')
                                        .next('.invalid-feedback')
                                        .text(errors[field][0])
                                        .show();
                                }
                                if (field === 'source_type' || field === 'source_type.0') {
                                    const selectElem = $('#source_type_select');
                                    selectElem.addClass('is-invalid');
                                    selectElem.next('.select2-container').addClass('is-invalid');
                                    selectElem.parent().find('.invalid-feedback')
                                        .text(errors[field][0])
                                        .show();
                                }
                            }

                            return;
                        }

                        // Show general error
                        iziToast.error({
                            title: 'Error',
                            message: xhr.responseJSON?.message || 'Something went wrong',
                            position: 'topRight'
                        });
                    }

                    , complete: function () {
                        $('#submitBtn').prop('disabled', false).html(editMode ? 'Update' :
                            'Submit');
                    }
                });
            });

            // Reset form handler: set back to create mode
            $('#lead_source_form').on('reset', function () {
                setFormToCreateMode();
            });

            // Real-time validation for source_name fields (all, since dynamic)
            $(document).on('input', 'input[name="source_name[]"]', function () {
                if ($(this).val().trim()) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').hide();
                }
            });

            // Real-time validation for source_type fields
            $(document).on('change', '#source_type_select', function () {
                if ($(this).val()) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.select2-container').removeClass('is-invalid');
                    $(this).parent().find('.invalid-feedback').hide();
                }
            });
        });

    </script>
</body>

</html>