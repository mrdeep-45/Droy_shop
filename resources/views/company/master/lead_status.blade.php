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
                                            href="#lead-status-list">
                                                List
                                            </a>
                                        </li>
                                        @endif
                                        @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link"
                                            data-bs-toggle="tab"
                                            role="tab"
                                            href="#new-lead-status">
                                                New Lead Status
                                            </a>
                                        </li>
                                        @endif

                                        <li class="nav-item">
                                            <a class="nav-link"
                                            data-bs-toggle="tab"
                                            role="tab"
                                            href="#lead-status-details">
                                                <i class="fas fa-info-circle me-1"></i> Details
                                            </a>
                                        </li>
                                    </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                    <div class="tab-pane fade show active" id="lead-status-list">
                                        <div class="table-responsive">
                                           <table id="stockData" class="table table-bordered  menu-submenu-data text-nowrap w-100" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:1%;">#</th>
                                                            <th class="text-start" style="width:19%;">Lead Status</th>
                                                            <th class="text-center" style="width:19%;">Created By</th>
                                                            <th class="text-center" style="width:19%;">Created At</th>
                                                            <th class="text-center" style="width:10%;">Drag</th>
                                                            <th class="text-center" style="width:19%;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                        </div>
                                    </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                    <div class="tab-pane text-muted px-1" id="new-lead-status" role="tabpanel">
                                        <form id="raw_material_form" enctype="multipart/form-data" novalidate>
                                            @csrf

                                            <div id="groupNameContainer">
                                                <div class="row group-name-row col-md-6 mb-3">
                                                    <div class="col-md-10">
                                                        <label for="status_name" class="form-label">Status Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="status_name[]" required placeholder="Enter Status Name">
                                                        <div class="invalid-feedback status_nameError">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-success add-group-btn">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                    @endif
                                    <div class="tab-pane fade p-3" id="lead-status-details" role="tabpanel">
                                        <h6 class="fw-bold mb-3 text-primary">Lead Status Categorization Definitions</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover align-middle border">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 25%;">Status Name</th>
                                                        <th>Description / Meaning</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><span class="badge bg-primary fs-6">Open</span></td>
                                                        <td>Lead has been created and is awaiting the first interaction by Sales Person (All contacts).</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-info text-dark fs-6">Connected</span></td>
                                                        <td>Initial contact has been successfully established with the prospect.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-warning text-dark fs-6">Contact in Future</span></td>
                                                        <td>The prospect has requested to be contacted at a later date.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-success fs-6">Qualified</span></td>
                                                        <td>The lead meets the qualification criteria and is ready to be converted into an opportunity.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-danger fs-6">Not Qualified</span></td>
                                                        <td>The lead does not meet the qualification criteria.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-secondary fs-6">Junk</span></td>
                                                        <td>Invalid, duplicate, or irrelevant lead.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>



      <script>
        let editMode = false;

        function setFormToCreateMode() {
            editMode = false;
            $('#raw_material_form').removeAttr('data-mode');
            $('#companyTabLabel').text('New Status');
            $('#submitBtn').text('Submit');
            $('input[name="status_id[]"]').remove();
            // Clear input fields
            $('.group-name-row:not(:first)').remove();
            $('.group-name-row:first input').val('');
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        function setFormToEditMode(groupName, groupId) {
            editMode = true;
            $('#raw_material_form').attr('data-mode', 'edit');
            $('#companyTabLabel').text('Edit Status');
            $('#submitBtn').text('Update');
            // Remove any old hidden fields then add new one
            $('input[name="status_id[]"]').remove();
            $('#raw_material_form').append(
                `<input type="hidden" name="status_id[]" value="${groupId}">`
            );
            // Only single value
            $('.group-name-row:not(:first)').remove();
            $('.group-name-row:first input').val(groupName);
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        $(document).ready(function () {
            var canUpdate = {{ permissions_check('canUpdate') ? 'true' : 'false' }};
            var canDelete = {{ permissions_check('canDelete') ? 'true' : 'false' }};

            // DataTable Init
            var table = $('#stockData').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('leadstatus.list') }}",
                    type: "GET",
                    dataSrc: 'data'
                },
                columns: [
                    {
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'title',
                        className: 'text-start'
                    },
                    {
                        data: 'created_by',
                        //className: 'text-center'
                    },
                    {
                        data: 'created_at',
                       // className: 'text-center'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        visible: canUpdate,
                        render: function () {
                            return '<i class="fas fa-grip-vertical drag-handle" style="cursor:grab"></i>';
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        visible: canUpdate || canDelete,
                        render: function (data, type, row) {
                            let buttons = `<div class="d-flex gap-1 justify-content-center text-nowrap">`;
                            
                            if (canUpdate) {
                                buttons += `
                                    <button class="btn btn-sm btn-primary edit-group" data-id="${row.status_id}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>`;
                            }

                            if (canDelete) {
                                if (row.is_used) {
                                    buttons += `<button class="btn btn-sm btn-danger" disabled style="opacity: 0.5; cursor: not-allowed;" title="Cannot delete: Status is in use"><i class="fas fa-trash"></i> Delete</button>`;
                                } else {
                                    buttons += `<button class="btn btn-sm btn-danger delete-group" data-id="${row.status_id}"><i class="fas fa-trash"></i> Delete</button>`;
                                }
                            }

                            buttons += `</div>`;
                            return buttons;
                        }
                    }
                ],
                order: [], // Respect server-side ordering (descending by ID)
                responsive: true
            });

            let sortable;

            $('#stockData').on('draw.dt', function () {
                if (!canUpdate) return; // view-only: no drag
                const tbody = document.querySelector('#stockData tbody');

                if (sortable) sortable.destroy();

                sortable = new Sortable(tbody, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function () {
                        updateOrder();
                    }
                });
            });

            function updateOrder() {
                let orderData = [];

                $('#stockData tbody tr').each(function (index) {
                    let id = $(this).find('.edit-group').data('id'); // get ID from button
                    if (id) {
                        orderData.push({
                            id: id,
                            order_no: index + 1
                        });
                    }
                });

                $.ajax({
                    url: "{{ route('leadstatus.updateOrder') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        order: orderData
                    },
                    success: function () {
                        iziToast.success({
                            title: 'Updated',
                            message: 'Order updated successfully',
                            position: 'topRight'
                        });
                    }
                });
            }

            $('#refreshBtn').click(() => table.ajax.reload());

            // Add/remove group name field
            var groupNameContainer = document.getElementById('groupNameContainer');
            if (groupNameContainer) {
                groupNameContainer.addEventListener('click', function (e) {
                    if (e.target.classList.contains('add-group-btn')) {
                        const newField = document.createElement('div');
                        newField.className = 'row group-name-row col-md-6 mb-3';
                        newField.innerHTML = `
                        <div class="col-md-10">
                            <label class="form-label">Status Name</label>
                            <input type="text" class="form-control" name="status_name[]" required>
                            <div class="invalid-feedback status_nameError"></div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-group-btn">−</button>
                        </div>
                    `;
                        this.appendChild(newField);
                    }
                    if (e.target.classList.contains('remove-group-btn')) {
                        e.target.closest('.group-name-row').remove();
                    }
                });
            }

            // Tab show event: update tab label etc on display
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#new-lead-status') {
                    if (editMode) {
                        $('#companyTabLabel').text('Edit Lead Status');
                        $('#submitBtn').text('Update');
                    } else {
                        setFormToCreateMode(); // will also update label etc.
                    }
                }
                // If switching to list, always reset form
                if (target === '#lead-status-list') {
                    setFormToCreateMode();
                }
            });

            // Handle edit button click
            $(document).on('click', '.edit-group', function () {
                const groupId = $(this).data('id');
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Loading...');
                $.ajax({
                    url: '{{ route("leadstatus.edit", ":id") }}'.replace(':id', groupId),
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            setFormToEditMode(response.data.title, response.data.status_id);
                            $('a[href="#new-lead-status"]').tab('show');
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
                            message: xhr.responseJSON?.message ||
                                'Failed to fetch group data',
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
                    message: 'Are you sure you want to delete this Lead Status?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                url: '{{ route("leadstatus.destroy") }}',
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    status_id: groupId
                                },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        iziToast.success({
                                            title: 'Success',
                                            message: response.message,
                                            position: 'topRight'
                                        });
                                        $('#stockData').DataTable().ajax
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
                                            'Failed to delete Status.',
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
            $('#raw_material_form').submit(function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                let isValid = true;
                const status_names = [];
                $('input[name="status_name[]"]').each(function (index) {
                    const val = $(this).val().trim();
                    if (!val) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text('Please enter a status name').show();
                        isValid = false;
                    } else {
                        status_names.push(val);
                    }
                });
                if (!isValid) {
                    iziToast.warning({
                        title: 'Warning',
                        message: 'Please fill in all required fields.',
                        position: 'topRight'
                    });
                    return;
                }
                // Prepare form data
                const formData = {
                    status_name: status_names,
                    _token: $('input[name="_token"]').val()
                };
                if (editMode) formData.status_id = $('input[name="status_id[]"]').val();
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Processing...');
                $.ajax({
                    url: '{{ route("leadstatus.store") }}',
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
                                $('#stockData').DataTable().ajax.reload();
                                $('a[href="#lead-status-list"]').tab('show');
                                setFormToCreateMode(); // reset after submit
                            }
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const field in errors) {
                                if (field.startsWith('status_name.')) {
                                    const index = field.split('.')[1];
                                    $('input[name="status_name[]"]').eq(index).addClass(
                                        'is-invalid')
                                        .next('.invalid-feedback').text(errors[field][0])
                                        .show();
                                }
                            }
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: xhr.responseJSON?.message ||
                                    'Something went wrong',
                                position: 'topRight'
                            });
                        }
                    },
                    complete: function () {
                        $('#submitBtn').prop('disabled', false).html(editMode ? 'Update' :
                            'Submit');
                    }
                });
            });

            // Reset form handler: set back to create mode
            $('#raw_material_form').on('reset', function () {
                setFormToCreateMode();
            });

            // Real-time validation for status_name fields (all, since dynamic)
            $(document).on('input', 'input[name="status_name[]"]', function () {
                if ($(this).val().trim()) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').hide();
                }
            });
        });
    </script>
</body>

</html>
