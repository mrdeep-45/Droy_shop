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
                                            href="#unit-list">
                                                List
                                            </a>
                                        </li>
                                        @endif
                                        @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link"
                                            data-bs-toggle="tab"
                                            role="tab"
                                            href="#new-unit">
                                                New Unit
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                    <div class="tab-pane fade show active" id="unit-list">
                                        <div>
                                            <table id="unitData" class="table table-bordered  menu-submenu-data"
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:1%;">#</th>
                                                                <th class="text-center" style="width:40%;">Unit</th>
                                                                <th class="text-center">Created by</th>
                                                                <th class="text-center">Created On</th>
                                                                <th class="text-center">Action </th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                        </div>
                                    </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                    <div class="tab-pane text-muted px-1" id="new-unit" role="tabpanel">
                                         <form id="unit_form" enctype="multipart/form-data" novalidate>
                                                @csrf

                                             <!-- Card 1: Form Fields -->
                                             <div class="modern-card">
                                                
                                                <div id="groupNameContainer">
                                                    <div class="row group-name-row col-md-12 mb-3">
                                                        <div class="col-md-4">
                                                            <label for="unit" class="form-label">Unit<span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="unit[]"
                                                                required placeholder="Enter Unit">
                                                            <div class="invalid-feedback unitError"></div>
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
            $('#unit_form').removeAttr('data-mode');
            $('#companyTabLabel').text('New Unit');
            $('#submitBtn').text('Submit');
            $('input[name="unit_id[]"]').remove();
            // Clear input fields
            $('.group-name-row:not(:first)').remove();
            $('.group-name-row:first input[name="unit[]"]').val('');

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        function setFormToEditMode(groupName, groupId) {
            editMode = true;
            $('#unit_form').attr('data-mode', 'edit');
            $('#companyTabLabel').text('Edit Unit');
            $('#submitBtn').text('Update');
            // Remove any old hidden fields then add new one
            $('input[name="unit_id[]"]').remove();
            $('#unit_form').append(
                `<input type="hidden" name="unit_id[]" value="${groupId}">`
            );
            // Only single value
            $('.group-name-row:not(:first)').remove();
            $('.group-name-row:first input[name="unit[]"]').val(groupName);


            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        $(document).ready(function () {
             $('.select2').select2({
    width: '100%',
    placeholder: "Select an option", // Required for allowClear
    allowClear: true
});
            // DataTable Init
            var canUpdate = {{ permissions_check('canUpdate') ? 'true' : 'false' }};
            var canDelete = {{ permissions_check('canDelete') ? 'true' : 'false' }};
           var table = $('#unitData').DataTable({
    processing: true,
    responsive: true,
    serverSide: true,
    ajax: "{{ route('unit.list') }}",
    autoWidth: false,

    columns: [
        {
            data: 'DT_RowIndex',
            orderable: false,
            searchable: false,
            width: "5%"
        },
        {
            data: 'unit',
            name: 'unit',
            title: 'Unit',
            className: 'text-center',
            width: "25%"
        },

        {
            data: 'created_by',
            name: 'created_by',
            title: 'Created By',
            className: 'text-center text-nowrap',
            width: "15%"
        },
        {
            data: 'created_at',
            name: 'created_at',
            title: 'Created At',
            className: 'text-center',
            width: "15%"
        },
        {
            data: 'action',
            orderable: false,
            searchable: false,
            visible: canUpdate || canDelete,
            className: "text-center desktop",
            width: "20%"
        }
    ],

    responsive: true
});

            $('#refreshBtn').click(() => table.ajax.reload());

            // Add/remove group name field
            var groupNameContainer = document.getElementById('groupNameContainer');
            if (groupNameContainer) {
                groupNameContainer.addEventListener('click', function (e) {
                    if (e.target.classList.contains('add-group-btn')) {
                        const newField = document.createElement('div');
                        newField.className = 'row group-name-row col-md-12 mb-3';
                        newField.innerHTML = `
                        <div class="col-md-4">
                            <label class="form-label">Unit</label>
                            <input type="text" class="form-control" name="unit[]" required>
                            <div class="invalid-feedback unitError"></div>
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
                if (target === '#new-unit') {
                    if (editMode) {
                        $('#companyTabLabel').text('Edit Unit');
                        $('#submitBtn').text('Update');
                    } else {
                        setFormToCreateMode(); // will also update label etc.
                    }
                }
                // If switching to list, always reset form
                if (target === '#unit-list') {
                    setFormToCreateMode();
                }
            });

            // Handle edit button click
            $(document).on('click', '.edit-group', function () {
                const groupId = $(this).data('id');
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Loading...');
                $.ajax({
                    url: "{{ route('unit.edit', ':id') }}".replace(':id', groupId),
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            // Pass projectstage value as third parameter
                            setFormToEditMode(response.data.unit, response.data.unit_id);
                            $('a[href="#new-unit"]').tab('show');
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
                    message: 'Are you sure you want to delete this Unit.?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                url: "{{ route('unit.destroy') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    unit_id: groupId
                                },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        iziToast.success({
                                            title: 'Success',
                                            message: response.message,
                                            position: 'topRight'
                                        });
                                        $('#unitData').DataTable().ajax
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
                                            'Failed to delete Unit.',
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
            $('#unit_form').submit(function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                let isValid = true;
                const units = [];
                
                $('input[name="unit[]"]').each(function (index) {
                    const val = $(this).val().trim();

                    if (!val) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text('Please enter a Unit').show();
                        iziToast.warning({
                            title: 'Warning',
                            message: 'Please enter a Unit.',
                            position: 'topRight'
                        });
                        isValid = false;
                        return false;
                    } else {
                        units.push(val);
                    }


                });

                if (!isValid) {
                    return;
                }
                const formData = {
                    unit: units,
                    _token: $('input[name="_token"]').val()
                };
                if (editMode) formData.unit_id = $('input[name="unit_id[]"]').val();

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    url: "{{ route('unit.store') }}",
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
                                $('#unitData').DataTable().ajax.reload();
                                $('a[href="#unit-list"]').tab('show');
                                setFormToCreateMode(); // reset after submit
                            }
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const field in errors) {
                                if (field.startsWith('unit.')) {
                                    const index = field.split('.')[1];
                                    $('input[name="unit[]"]').eq(index).addClass(
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
            $('#unit_form').on('reset', function () {
                setFormToCreateMode();
            });

            // Real-time validation for name fields (all, since dynamic)
            // Real-time validation for name fields (all, since dynamic)
            $(document).on('input', 'input[name="unit[]"]', function () {
                if ($(this).val().trim()) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').hide();
                }
            });



        });
    </script>
</body>

</html>
