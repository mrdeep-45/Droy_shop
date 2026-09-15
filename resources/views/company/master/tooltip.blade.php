<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')</head>

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
                                            href="#tooltip-list">
                                                List
                                            </a>
                                        </li>
                                        @endif
                                        @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link"
                                            data-bs-toggle="tab"
                                            role="tab"
                                            href="#new-tooltip">
                                                New Tooltip
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                    <div class="tab-pane fade show active" id="tooltip-list">
                                        <div>
                                            <table id="tooltipData" class="table table-bordered menu-submenu-data"
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:1%;">#</th>
                                                                <th class="text-center" style="width:10%;">Module</th>
                                                                <th class="text-center" style="width:15%;">Field Label</th>
                                                                <th class="text-center" style="width:15%;">Field Key</th>
                                                                <th class="text-center" style="width:25%;">Tooltip Text</th>
                                                                <th class="text-center" style="width:10%;">Created By</th>
                                                                <th class="text-center" style="width:10%;">Created On</th>
                                                                <th class="text-center" style="width:14%;">Action</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                        </div>
                                    </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                    <div class="tab-pane text-muted px-1" id="new-tooltip" role="tabpanel">
                                         <form id="tooltip_form" enctype="multipart/form-data" novalidate>
                                                @csrf

                                             <!-- Card 1: Form Fields -->
                                             <div class="modern-card">

                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <label for="module_name" class="form-label">Module Name<span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="module_name" id="module_name"
                                                            required placeholder="e.g. Lead, Deal, Quotation">
                                                        <div class="invalid-feedback module_nameError"></div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="field_label" class="form-label">Field Label<span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="field_label" id="field_label"
                                                            required placeholder="e.g. Remarks / Notes">
                                                        <div class="invalid-feedback field_labelError"></div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="field_key" class="form-label">Field Key<span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="field_key" id="field_key"
                                                                required placeholder="e.g. lead_remarks_notes">
                                                            <button class="btn btn-outline-secondary" type="button" id="generateKeyBtn"
                                                                title="Auto-generate from Module + Label">
                                                                <i class="fas fa-magic"></i>
                                                            </button>
                                                        </div>
                                                        <div class="invalid-feedback field_keyError"></div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <label for="tooltip_text" class="form-label">Tooltip Text<span
                                                                class="text-danger">*</span></label>
                                                        <textarea class="form-control" name="tooltip_text" id="tooltip_text" rows="4"
                                                            required placeholder="Enter the help text to show as tooltip..."></textarea>
                                                        <div class="invalid-feedback tooltip_textError"></div>
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
        let editTooltipId = null;

        function setFormToCreateMode() {
            editMode = false;
            editTooltipId = null;
            $('#tooltip_form').removeAttr('data-mode');
            $('#submitBtn').text('Submit');

            // Clear input fields
            $('#module_name').val('');
            $('#field_label').val('');
            $('#field_key').val('');
            $('#tooltip_text').val('');

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        function setFormToEditMode(data) {
            editMode = true;
            editTooltipId = data.tooltip_id;
            $('#tooltip_form').attr('data-mode', 'edit');
            $('#submitBtn').text('Update');

            // Populate fields
            $('#module_name').val(data.module_name);
            $('#field_label').val(data.field_label);
            $('#field_key').val(data.field_key);
            $('#tooltip_text').val(data.tooltip_text);

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        // Auto-generate field_key from module_name + field_label
        function generateFieldKey() {
            var module = $('#module_name').val().trim();
            var label = $('#field_label').val().trim();
            if (module && label) {
                var key = (module + '_' + label)
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '_')
                    .replace(/^_+|_+$/g, '');
                $('#field_key').val(key);
            }
        }

        $(document).ready(function () {
            // DataTable Init
            var canUpdate = {{ permissions_check('canUpdate') ? 'true' : 'false' }};
            var canDelete = {{ permissions_check('canDelete') ? 'true' : 'false' }};
           var table = $('#tooltipData').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                ajax: "{{ route('tooltips.list') }}",
                autoWidth: false,

                columns: [
                    {
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: "5%"
                    },
                    {
                        data: 'module_name',
                        name: 'module_name',
                        title: 'Module',
                        width: "10%"
                    },
                    {
                        data: 'field_label',
                        name: 'field_label',
                        title: 'Field Label',
                        width: "15%"
                    },
                    {
                        data: 'field_key',
                        name: 'field_key',
                        title: 'Field Key',
                        width: "15%"
                    },
                    {
                        data: 'tooltip_text',
                        name: 'tooltip_text',
                        title: 'Tooltip Text',
                        width: "25%"
                    },
                    {
                        data: 'created_by',
                        name: 'created_by',
                        title: 'Created By',
                        width: "10%"
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        title: 'Created At',
                        width: "10%"
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        visible: canUpdate || canDelete,
                        className: "text-center desktop",
                        width: "14%"
                    }
                ],

                responsive: true
            });

            $('#refreshBtn').click(() => table.ajax.reload());

            // Auto-generate key button
            $('#generateKeyBtn').on('click', function() {
                generateFieldKey();
            });

            // Tab show event: update tab label etc on display
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#new-tooltip') {
                    if (editMode) {
                        $('#submitBtn').text('Update');
                    } else {
                        setFormToCreateMode();
                    }
                }
                // If switching to list, always reset form
                if (target === '#tooltip-list') {
                    setFormToCreateMode();
                }
            });

            // Handle edit button click
            $(document).on('click', '.edit-tooltip', function () {
                const tooltipId = $(this).data('id');
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Loading...');
                $.ajax({
                    url: "{{ route('tooltips.edit', ':id') }}".replace(':id', tooltipId),
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            setFormToEditMode(response.data);
                            $('a[href="#new-tooltip"]').tab('show');
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
                            message: xhr.responseJSON?.message || 'Failed to fetch tooltip data',
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
            $(document).on('click', '.delete-tooltip', function () {
                const tooltipId = $(this).data('id');
                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete this Tooltip?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                url: "{{ route('tooltips.destroy') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    tooltip_id: tooltipId
                                },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        iziToast.success({
                                            title: 'Success',
                                            message: response.message,
                                            position: 'topRight'
                                        });
                                        $('#tooltipData').DataTable().ajax
                                            .reload();
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
                                            'Failed to delete Tooltip.',
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
            $('#tooltip_form').submit(function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();

                let isValid = true;

                // Client-side validation
                var moduleName = $('#module_name').val().trim();
                var fieldLabel = $('#field_label').val().trim();
                var fieldKey = $('#field_key').val().trim();
                var tooltipText = $('#tooltip_text').val().trim();

                if (!moduleName) {
                    $('#module_name').addClass('is-invalid');
                    $('#module_name').next('.invalid-feedback').text('Please enter Module Name').show();
                    isValid = false;
                }
                if (!fieldLabel) {
                    $('#field_label').addClass('is-invalid');
                    $('#field_label').next('.invalid-feedback').text('Please enter Field Label').show();
                    isValid = false;
                }
                if (!fieldKey) {
                    $('#field_key').addClass('is-invalid');
                    // field_key is inside input-group, so find the invalid-feedback after the group
                    $('#field_key').closest('.col-md-4').find('.invalid-feedback').text('Please enter Field Key').show();
                    isValid = false;
                }
                if (!tooltipText) {
                    $('#tooltip_text').addClass('is-invalid');
                    $('#tooltip_text').next('.invalid-feedback').text('Please enter Tooltip Text').show();
                    isValid = false;
                }

                if (!isValid) {
                    iziToast.warning({
                        title: 'Warning',
                        message: 'Please fill all required fields.',
                        position: 'topRight'
                    });
                    return;
                }

                const formData = {
                    module_name: moduleName,
                    field_key: fieldKey,
                    field_label: fieldLabel,
                    tooltip_text: tooltipText,
                    _token: $('input[name="_token"]').val()
                };
                if (editMode && editTooltipId) {
                    formData.tooltip_id = editTooltipId;
                }

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    url: "{{ route('tooltips.store') }}",
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
                                $('#tooltipData').DataTable().ajax.reload();
                                $('a[href="#tooltip-list"]').tab('show');
                                setFormToCreateMode();
                            }
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const field in errors) {
                                var $input = $('[name="' + field + '"]');
                                if ($input.length) {
                                    $input.addClass('is-invalid');
                                    // Handle input-group case (field_key)
                                    var $feedback = $input.closest('.col-md-4, .col-md-12').find('.invalid-feedback');
                                    if ($feedback.length) {
                                        $feedback.text(errors[field][0]).show();
                                    }
                                }
                            }
                            iziToast.error({
                                title: 'Validation Error',
                                message: xhr.responseJSON?.message || 'Please fix the errors.',
                                position: 'topRight'
                            });
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
            $('#tooltip_form').on('reset', function () {
                setFormToCreateMode();
            });

            // Real-time validation for input fields
            $(document).on('input', '#module_name, #field_label, #field_key, #tooltip_text', function () {
                if ($(this).val().trim()) {
                    $(this).removeClass('is-invalid');
                    $(this).closest('.col-md-4, .col-md-12').find('.invalid-feedback').hide();
                }
            });

        });
    </script>
</body>

</html>
