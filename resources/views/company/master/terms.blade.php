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
                                <ul class="nav nav-tabs justify-content-end nav-tabs-footer mb-0" role="tablist">
                                    @if (permissions_check('canView'))
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#terms-list"
                                                aria-selected="true">List</a>
                                        </li>
                                    @endif
                                    @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" role="tab" href="#terms-form"
                                                aria-selected="false"><span id="formTabLabel">New Terms</span></a>
                                        </li>
                                    @endif
                                </ul>

                            </div>
                            <div class="card-body p-0">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                        <div class="tab-pane border-0 show active text-muted px-1" id="terms-list"
                                            role="tabpanel">
                                            <div class="row">
                                                <table id="termsTable" class="table table-bordered" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:5%;">#</th>
                                                            <th>Module</th>
                                                            <th>Terms</th>
                                                            @if (permissions_check('canUpdate') || permissions_check('canDelete'))
                                                                <th class="text-center">Actions</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                        <div class="tab-pane text-muted border-0 px-1" id="terms-form" role="tabpanel">
                                            <div class="d-flex justify-content-end">
                                                <small class="text-danger me-2">* Fields are mandatory</small>
                                            </div>
                                            <form id="termsForm" novalidate>
                                                @csrf
                                                <div class="modern-card">
                                                    <div id="termsContainer">
                                                        <div class="terms-row mb-3 position-relative">
                                                            <div class="row g-2">
                                                                <div class="col-md-4 mb-2">
                                                                    <label class="form-label">Module <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" name="module[]"
                                                                        placeholder="e.g. Quotation">
                                                                    <div class="invalid-feedback"></div>
                                                                </div>
                                                                <div class="col-md-12 mb-2">
                                                                    <label class="form-label">Terms & Conditions
                                                                        <span class="text-danger">*</span></label>
                                                                    <textarea class="form-control" name="terms[]" rows="4"
                                                                        placeholder="Enter Terms & Conditions"></textarea>
                                                                    <div class="invalid-feedback"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-grid gap-2 d-md-flex justify-content-md-between mt-2">
                                                    <button type="button" class="btn btn-outline-success" id="addRowBtn">
                                                        <i class="fas fa-plus me-1"></i> Add Another
                                                    </button>
                                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                                        <span id="submitText">Submit</span>
                                                        <span id="submitSpinner"
                                                            class="spinner-border spinner-border-sm align-middle d-none"
                                                            role="status" aria-hidden="true"></span>
                                                    </button>
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
        var termsListUrl = "{{ route('terms.list') }}";
        var termsStoreUrl = "{{ route('terms.store') }}";
        var termsEditUrl = "{{ route('terms.edit', ':id') }}";
        var termsDeleteUrl = "{{ route('terms.destroy') }}";
        var csrfToken = "{{ csrf_token() }}";
    </script>

    <script>
        var editMode = false;

        var rowTemplate = `
            <div class="terms-row mb-3 position-relative border-top pt-3 mt-3">
                <button type="button" class="btn btn-sm btn-danger remove-row-btn position-absolute" style="top:10px;right:10px;z-index:1;">
                    <i class="fas fa-times"></i>
                </button>
                <div class="row g-2">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Module <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="module[]" placeholder="e.g. Quotation">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Terms & Conditions <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="terms[]" rows="4" placeholder="Enter Terms & Conditions"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>`;

        function setCreateMode() {
            editMode = false;
            $('#formTabLabel').text('New Terms');
            $('#submitText').text('Submit');
            $('#addRowBtn').show();
            $('input[name="id[]"]').remove();
            $('.terms-row:not(:first)').remove();
            $('.terms-row:first input[name="module[]"]').val('');
            $('.terms-row:first textarea[name="terms[]"]').val('');
            $('.terms-row:first .remove-row-btn').remove();
            clearValidation();
        }

        function setEditMode(data) {
            editMode = true;
            $('#formTabLabel').text('Edit Terms');
            $('#submitText').text('Update');
            $('#addRowBtn').hide();
            $('.terms-row:not(:first)').remove();
            var row = $('.terms-row:first');
            row.find('.remove-row-btn').remove();
            if (!row.find('input[name="id[]"]').length) {
                row.prepend('<input type="hidden" name="id[]" value="' + data.term_id + '">');
            } else {
                row.find('input[name="id[]"]').val(data.term_id);
            }
            row.find('input[name="module[]"]').val(data.module);
            row.find('textarea[name="terms[]"]').val(data.terms);
            clearValidation();
        }

        function clearValidation() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();
        }

        $(document).ready(function () {

            var table = $('#termsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: termsListUrl,
                order: [[1, 'asc']],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center', width: "5%" },
                    { data: 'module', name: 'module', orderable: true, searchable: true, width: "20%" },
                    { data: 'terms', name: 'terms', orderable: true, searchable: true, width: "60%" },

                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center', width: "15%" }
                ],
                responsive: true
            });

            $('#addRowBtn').on('click', function () {
                $('#termsContainer').append(rowTemplate);
            });

            $(document).on('click', '.remove-row-btn', function () {
                $(this).closest('.terms-row').remove();
            });

            $('a[href="#terms-form"]').on('shown.bs.tab', function () {
                if (!editMode) setCreateMode();
            });

            $('a[href="#terms-list"]').on('shown.bs.tab', function () {
                setCreateMode();
            });

            $(document).on('click', '.edit-terms', function () {
                var id = $(this).data('id');
                var url = termsEditUrl.replace(':id', id);

                $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            setEditMode(response.data);
                            $('a[href="#terms-form"]').tab('show');
                        } else {
                            iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                            setCreateMode();
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Failed to fetch data', position: 'topRight' });
                        setCreateMode();
                    },
                    complete: function () {
                        $('#submitBtn').prop('disabled', false).html(
                            '<span id="submitText">' + (editMode ? 'Update' : 'Submit') + '</span>' +
                            '<span id="submitSpinner" class="spinner-border spinner-border-sm align-middle d-none" role="status" aria-hidden="true"></span>'
                        );
                    }
                });
            });

            $(document).on('click', '.delete-terms', function () {
                var id = $(this).data('id');
                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete this Terms entry?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            $.ajax({
                                url: termsDeleteUrl,
                                type: 'POST',
                                data: { _token: csrfToken, id: id },
                                dataType: 'json',
                                success: function (response) {
                                    if (response.status === 'success') {
                                        iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                                        table.ajax.reload();
                                    } else {
                                        iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                                    }
                                },
                                error: function (xhr) {
                                    iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Failed to delete', position: 'topRight' });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }]
                    ]
                });
            });

            // Submit form on Enter keypress globally when the form tab is active
            $(document).on('keypress', function (e) {
                if (e.which === 13) {
                    if ($(e.target).is('.select2-search__field, input[type="search"]') || $(e.target).closest('#typehead').length) {
                        return;
                    }
                    // For textareas we might want enter to add a newline, so we check if target is textarea
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

            $('#termsForm').on('submit', function (e) {
                e.preventDefault();
                clearValidation();

                var isValid = true;

                $('.terms-row').each(function () {
                    var moduleVal = $(this).find('input[name="module[]"]').val().trim();
                    var termsVal = $(this).find('textarea[name="terms[]"]').val().trim();

                    if (!moduleVal) {
                        $(this).find('input[name="module[]"]').addClass('is-invalid').next('.invalid-feedback').text('Module is required.').show();
                        iziToast.warning({
                            title: 'Warning',
                            message: 'Module is required.',
                            position: 'topRight'
                        });
                        isValid = false;
                        return false;
                    }
                    if (!termsVal) {
                        $(this).find('textarea[name="terms[]"]').addClass('is-invalid').next('.invalid-feedback').text('Terms & Conditions is required.').show();
                        iziToast.warning({
                            title: 'Warning',
                            message: 'Terms & Conditions is required.',
                            position: 'topRight'
                        });
                        isValid = false;
                        return false;
                    }
                });

                if (!isValid) return;

                var formData = new FormData(this);
                $('#submitSpinner').removeClass('d-none');
                $('#submitText').text('Processing...');
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    url: termsStoreUrl,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        $('#submitSpinner').addClass('d-none');
                        $('#submitText').text(editMode ? 'Update' : 'Submit');
                        $('#submitBtn').prop('disabled', false);
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            timeout: 700,
                            onClosed: function () {
                                table.ajax.reload();
                                $('a[href="#terms-list"]').tab('show');
                                setCreateMode();
                            }
                        });
                    },
                    error: function (xhr) {
                        $('#submitSpinner').addClass('d-none');
                        $('#submitText').text(editMode ? 'Update' : 'Submit');
                        $('#submitBtn').prop('disabled', false);
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            for (var field in errors) {
                                var parts = field.split('.');
                                var fieldName = parts[0];
                                var index = parseInt(parts[1]);
                                var row = $('.terms-row').eq(index);
                                if (fieldName === 'module') {
                                    row.find('input[name="module[]"]').addClass('is-invalid').next('.invalid-feedback').text(errors[field][0]).show();
                                } else if (fieldName === 'terms') {
                                    row.find('textarea[name="terms[]"]').addClass('is-invalid').next('.invalid-feedback').text(errors[field][0]).show();
                                }
                            }
                        } else {
                            iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Something went wrong', position: 'topRight' });
                        }
                    }
                });
            });

            $(document).on('input', 'input[name="module[]"], textarea[name="terms[]"]', function () {
                if ($(this).val().trim()) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').hide();
                }
            });

        });
    </script>
</body>

</html>
