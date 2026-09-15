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
                                <div class="card-title text-primary mb-0" id="pageTitleLabel">
                                    {{ $page_name }}
                                </div>
                                <ul class="nav nav-tabs nav-tabs-footer mb-0" role="tablist">
                                    @if (permissions_check('canView'))
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#industry-list">
                                            List
                                        </a>
                                    </li>
                                    @endif
                                    @if (permissions_check('canCreate'))
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" role="tab" href="#new-industry" id="companyTabLabel">
                                            New Industry Type
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                    <div class="tab-pane fade show active" id="industry-list">
                                        <div class="table-responsive">
                                            <table id="industryData" class="table table-bordered menu-submenu-data" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th style="width:1%;">#</th>
                                                        <th class="text-center" style="width:40%;">Industry Type Name</th>
                                                        <th class="text-center">Created by</th>
                                                        <th class="text-center">Created On</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                    <div class="tab-pane text-muted px-1" id="new-industry" role="tabpanel">
                                        <form id="industry_type_form" enctype="multipart/form-data" novalidate>
                                            @csrf

                                            <div class="modern-card">
                                                <div id="groupNameContainer">
                                                    <div class="row group-name-row col-md-12 mb-3">
                                                        <div class="col-md-5">
                                                            <label for="industry_type_name" class="form-label">Industry Type Name<span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="industry_type_name[]" required placeholder="Enter Industry Type Name">
                                                            <div class="invalid-feedback industry_type_nameError"></div>
                                                        </div>

                                                        <div class="col-md-1 d-flex align-items-end">
                                                            <button type="button" class="btn btn-success add-group-btn">+</button>
                                                        </div>
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
        @include('include.footer')
    </div>
    @include('include.scroll-top')
    @include('include.footer_link')
    @include('include.datatable_js_link')

    <script>
        let editMode = false;

        function setFormToCreateMode() {
            editMode = false;
            $('#industry_type_form').removeAttr('data-mode');
            $('#companyTabLabel').text('New Industry Type');
            $('#submitBtn').text('Submit');
            $('input[name="industry_type_id[]"]').remove();
            
            $('.group-name-row:not(:first)').remove();
            $('.group-name-row:first input[name="industry_type_name[]"]').val('');

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        function setFormToEditMode(groupName, groupId) {
            editMode = true;
            $('#industry_type_form').attr('data-mode', 'edit');
            $('#companyTabLabel').text('Edit Industry Type');
            $('#submitBtn').text('Update');
            
            $('input[name="industry_type_id[]"]').remove();
            $('#industry_type_form').append(
                `<input type="hidden" name="industry_type_id[]" value="${groupId}">`
            );
            
            $('.group-name-row:not(:first)').remove();
            $('.group-name-row:first input[name="industry_type_name[]"]').val(groupName);

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
            
            var table = $('#industryData').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('industry_type.list') }}",
                autoWidth: false,
                order: [[3, 'desc']],
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, width: "5%" },
                    { data: 'industry_type_name', name: 'industry_type_name', title: 'Industry Type Name', orderable: true, width: "25%" },
                    { data: 'created_by', name: 'created_by', title: 'Created By', orderable: true, width: "15%" },
                    { data: 'created_at', name: 'created_at', title: 'Created At', orderable: true, width: "15%" },
                    { data: 'action', orderable: false, searchable: false, visible: canUpdate || canDelete, className: "text-center", width: "20%" }
                ],
                responsive: true
            });

            // Dynamic Rows Addition
            var groupNameContainer = document.getElementById('groupNameContainer');
            if (groupNameContainer) {
                groupNameContainer.addEventListener('click', function (e) {
                    if (e.target.classList.contains('add-group-btn')) {
                        const newField = document.createElement('div');
                        newField.className = 'row group-name-row col-md-12 mb-3';
                        newField.innerHTML = `
                        <div class="col-md-5">
                            <label class="form-label">Industry Type Name</label>
                            <input type="text" class="form-control" name="industry_type_name[]" required placeholder="Enter Industry Type Name">
                            <div class="invalid-feedback industry_type_nameError"></div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-group-btn">−</button>
                        </div>`;
                        this.appendChild(newField);
                    }
                    if (e.target.classList.contains('remove-group-btn')) {
                        e.target.closest('.group-name-row').remove();
                    }
                });
            }

            // Automate tab-switch form clear/resets
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#new-industry') {
                    if (editMode) {
                        $('#companyTabLabel').text('Edit Industry Type');
                        $('#submitBtn').text('Update');
                    } else {
                        setFormToCreateMode();
                    }
                }
                if (target === '#industry-list') {
                    setFormToCreateMode();
                }
            });

            // Edit Action Fetching
            $(document).on('click', '.edit-group', function () {
                const groupId = $(this).data('id');
                $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');
                
                $.ajax({
                    url: "{{ route('industry_type.edit', ':id') }}".replace(':id', groupId),
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            setFormToEditMode(response.data.industry_type_name, response.data.industry_type_id);
                            $('a[href="#new-industry"]').tab('show');
                        } else {
                            iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                            setFormToCreateMode();
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Failed to fetch record data', position: 'topRight' });
                        setFormToCreateMode();
                    },
                    complete: function () {
                        $('#submitBtn').prop('disabled', false);
                    }
                });
            });

            // Delete processing
            $(document).on('click', '.delete-group', function () {
                const groupId = $(this).data('id');
                iziToast.question({
                    timeout: 20000, close: false, overlay: true, displayMode: 'once', id: 'question', zindex: 999,
                    title: 'Confirm', message: 'Are you sure you want to delete this Industry Type?', position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            $.ajax({
                                url: "{{ route('industry_type.destroy') }}",
                                type: "POST",
                                data: { _token: "{{ csrf_token() }}", industry_type_id: groupId },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        iziToast.success({ title: 'Success', message: response.message, position: 'topRight' });
                                        table.ajax.reload();
                                    } else {
                                        iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                                    }
                                },
                                error: function (xhr) {
                                    iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Failed to delete record.', position: 'topRight' });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function (instance, toast) { instance.hide({ transitionOut: 'fadeOut' }, toast, 'button'); }]
                    ]
                });
            });

            // Form Submit processing
            $('#industry_type_form').submit(function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                
                let isValid = true;
                const industry_names = [];
                
                $('input[name="industry_type_name[]"]').each(function () {
                    const val = $(this).val().trim();
                    if (!val) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text('Please enter an Industry Type Name').show();
                        iziToast.warning({ title: 'Warning', message: 'Please enter an Industry Type Name.', position: 'topRight' });
                        isValid = false;
                        return false;
                    }
                    industry_names.push(val);
                });

                if (!isValid) return;

                const formData = {
                    industry_type_name: industry_names,
                    _token: $('input[name="_token"]').val()
                };
                if (editMode) formData.industry_type_id = $('input[name="industry_type_id[]"]').val();

                $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    url: "{{ route('industry_type.store') }}",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        iziToast.success({
                            title: 'Success', message: response.message, position: 'topRight', timeout: 700,
                            onClosed: function () {
                                table.ajax.reload();
                                $('a[href="#industry-list"]').tab('show');
                                setFormToCreateMode();
                            }
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const field in errors) {
                                if (field.startsWith('industry_type_name.')) {
                                    const index = field.split('.')[1];
                                    $('input[name="industry_type_name[]"]').eq(index).addClass('is-invalid')
                                        .next('.invalid-feedback').text(errors[field][0]).show();
                                }
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

            // Master global keyboard listener
            $(document).on('keypress', function (e) {
                if (e.which === 13) {
                    if ($(e.target).is('.select2-search__field, input[type="search"]') || $(e.target).closest('#typehead').length) return;
                    if ($(e.target).is('button, a, textarea')) return;
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

            $(document).on('input', 'input[name="industry_type_name[]"]', function () {
                if ($(this).val().trim()) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').hide();
                }
            });
        });
    </script>
</body>
</html>