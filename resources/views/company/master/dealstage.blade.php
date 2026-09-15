<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        /* Won Stage Toggle Switch */
        .won-toggle-track {
            display: inline-block;
            width: 42px;
            height: 22px;
            background: #d1d5db;
            border-radius: 999px;
            position: relative;
            transition: background 0.25s;
            vertical-align: middle;
            cursor: pointer;
        }
        .won-toggle-track.active {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }
        .won-toggle-thumb {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.25s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .won-toggle-track.active .won-toggle-thumb {
            transform: translateX(20px);
        }
        .won-toggle-label {
            user-select: none;
        }
        .won-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            background: linear-gradient(135deg,#6366f1,#8b5cf6);
            color: #fff;
        }
    </style>
</head>

<body>
    @include('include.switcher')
    @include('include.loader')
    <div class="page">

        @include('include.header')

        <div class="page-header-breadcrumb d-md-flex d-block align-items-center justify-content-between d-none">
            {{-- <h4 class="fw-medium mb-0">{{ $page_title }}</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-white-50">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $page_name }}</li>
            </ol> --}}
        </div>

        <div class="main-content app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12">

                        <div class="card custom-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="card-title text-primary mb-0">
                                    {{ $page_title }}
                                </div>

                                <ul class="nav nav-tabs justify-content-end nav-tabs-footer mb-0" role="tablist">

                                    @if(permissions_check('canView'))
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#deal-stage-list"
                                                role="tab"><i class="fas fa-list me-1"></i> List</a>
                                        </li>
                                    @endif

                                    @if(permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#new-deal-stage" role="tab"><i
                                                    class="fas fa-plus me-1"></i> New Dealstage</a>
                                        </li>
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#deal-stage-details" role="tab">
                                            <i class="fas fa-info-circle me-1"></i> Details
                                        </a>
                                    </li>
                                </ul>

                            </div>

                            <div class="card-body">
                                <div class="tab-content">

                                    {{-- LIST TAB --}}
                                    @if (permissions_check('canView'))
                                        <div class="tab-pane border-0 show active text-muted px-1 " id="deal-stage-list"
                                            role="tabpanel">
                                            <div class="row">
                                                <div class="table-responsive">
                                                    <table id="stockData" class="table table-bordered  menu-submenu-data"
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:1%;">#</th>
                                                                <th style="width:25%;">Dealstage</th>
                                                                <th style="width:20%;">Colour</th>
                                                                <th style="width:15%;">Created By</th>
                                                                <th style="width:15%;">Created At</th>
                                                                <th style="width:8%;">Drag</th>
                                                                <th style="width:20%;">Won Stage</th>
                                                                <th class="text-center">Action </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- FORM TAB --}}
                                    @if (permissions_check('canCreate'))
                                        <div class="tab-pane text-muted border-0 px-1 " id="new-deal-stage" role="tabpanel">
                                            {{-- <div class="d-flex justify-content-end">
                                                <small class="text-danger me-2">* Fields are mandatory</small>
                                            </div> --}}


                                            <form id="deal_stage_form" enctype="multipart/form-data" novalidate>
                                                @csrf
                                                {{-- <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="stage_name" class="form-label">Dealstage Name<span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="stage_name"
                                                            name="stage_name" required>
                                                        <div class="invalid-feedback" id="nameError"></div>
                                                    </div>
                                                </div> --}}
                                                <div id="groupNameContainer">
                                                    <div class="row group-name-row col-md-12 mb-3">
                                                        <div class="col-md-4">
                                                            <label for="stage_name" class="form-label">Dealstage Name <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="stage_name[]"
                                                                required placeholder="Enter Dealstage Name">
                                                            <div class="invalid-feedback nameError"></div>
                                                        </div>
                                                        <div class="col-md-3 ">

                                                            <label for="color" class="form-label">Color <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="color[]" required
                                                                placeholder="Enter color Name">
                                                            <div class="invalid-feedback colorError"></div>


                                                        </div>
                                                        <div class="col-md-1 d-flex align-items-end">
                                                            <button type="button"
                                                                class="btn btn-success add-group-btn">+</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                    <button type="submit" class="btn btn-primary"
                                                        id="submitBtn">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                    <div class="tab-pane fade p-3" id="deal-stage-details" role="tabpanel">
                                        <h6 class="fw-bold mb-3 text-primary">Deal Stage Categorization & Definitions</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover align-middle border">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 30%;">Deal Stage</th>
                                                        <th>Description / Definition</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><span class="badge bg-primary-transparent fs-13 text-primary border border-primary">Proposal & Price Quote</span></td>
                                                        <td>Proposal and commercial quotation have been submitted to the customer.[cite: 5]</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-info-transparent fs-13 text-info border border-info">Negotiation & Review</span></td>
                                                        <td>Commercial, technical, or contractual discussions are in progress.[cite: 5]</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-success-transparent fs-13 text-success border border-success">Closed Won – Machine</span></td>
                                                        <td>Order for a machine has been successfully secured.[cite: 5]</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-success-transparent fs-13 text-success border border-success">Closed Won – Spares</span></td>
                                                        <td>Order for spares has been successfully secured.[cite: 5]</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-warning-transparent fs-13 text-warning border border-warning">Running Order – Machine</span></td>
                                                        <td>Machine order has been confirmed and is under execution (manufacturing, dispatch, installation, etc.).[cite: 5]</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-warning-transparent fs-13 text-warning border border-warning">Running Order – Spares</span></td>
                                                        <td>Spare parts order has been confirmed and is under execution.[cite: 5]</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-danger-transparent fs-13 text-danger border border-danger">Closed Lost – Competition</span></td>
                                                        <td>Deal was lost to a competitor.[cite: 5]</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="badge bg-secondary-transparent fs-13 text-secondary border border-secondary">Closed Lost</span></td>
                                                        <td>Deal was lost due to reasons other than competition, such as budget constraints, project cancellation, no requirement, or no response.[cite: 5]</td>
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

    <script src="{{ $actual_url . '/admin-assets/libs/flatpickr/flatpickr.min.js'}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        var canUpdate = {{ permissions_check('canUpdate') ? 'true' : 'false' }};
        var canDelete = {{ permissions_check('canDelete') ? 'true' : 'false' }};
        let editMode = false;

        function setFormToCreateMode() {
            editMode = false;
            $('#deal_stage_form').removeAttr('data-mode');
            $('#companyTabLabel').text('New Dealstage');
            $('#submitBtn').text('Submit');
            $('input[name="id[]"]').remove();
            // Clear input fields
            $('.group-name-row:not(:first)').remove();
            $('.group-name-row:first input[name="stage_name[]"]').val('');
            $('.group-name-row:first input[name="color[]"]').val(''); // 
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        function setFormToEditMode(groupName, groupId, color) {
            editMode = true;
            $('#deal_stage_form').attr('data-mode', 'edit');
            $('#companyTabLabel').text('Edit Dealstage');
            $('#submitBtn').text('Update');
            // Remove any old hidden fields then add new one
            $('input[name="id[]"]').remove();
            $('#deal_stage_form').append(
                `<input type="hidden" name="id[]" value="${groupId}">`
            );
            // Only single value
            $('.group-name-row:not(:first)').remove();
            $('.group-name-row:first input[name="stage_name[]"]').val(groupName);
            $('.group-name-row:first input[name="color[]"]').val(color);
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

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
                url: "{{ route('dealstage.updateOrder') }}",
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

        $(document).ready(function () {
            // DataTable Init
            var table = $('#stockData').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dealstage.list') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        title: '#'
                    },
                    {
                        data: 'stage_name',
                        name: 'stage_name',
                        title: 'Stage Name'
                    },
                    {
                        data: 'color',
                        name: 'color',
                        title: 'Colour'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by',
                        title: 'Created By'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        title: 'Created At'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        visible: canUpdate,
                        render: function () {
                            return '<i class="fas fa-grip-vertical drag-handle" style="cursor:grab"></i>';
                        }
                    },
                     {
                        data: 'won_toggle',
                        name: 'won_toggle',
                        orderable: false,
                        searchable: false,
                        title: 'Won Stage'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        title: 'Actions',
                        visible: canUpdate || canDelete
                    }
                ],
                responsive: true
            });

            $('#refreshBtn').click(() => table.ajax.reload());

            // Add/remove group name field
            let groupNameContainer = document.getElementById('groupNameContainer');
            if (groupNameContainer) {
                groupNameContainer.addEventListener('click', function (e) {
                    if (e.target.classList.contains('add-group-btn')) {
                        const newField = document.createElement('div');
                        newField.className = 'row group-name-row col-md-12 mb-3';
                        newField.innerHTML = `
                            <div class="col-md-4">
                                <label class="form-label">Dealstage Name</label>
                                <input type="text" class="form-control" name="stage_name[]" required>
                                <div class="invalid-feedback nameError"></div>
                            </div>
                            <div class="col-md-3 ">
                                <label  class="form-label">Color</label>
                                <input type="text" class="form-control" name="color[]" required>
                                <div class="invalid-feedback colorError"></div>
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
                if (target === '#new-deal-stage') {
                    if (editMode) {
                        $('#companyTabLabel').text('Edit Dealstage');
                        $('#submitBtn').text('Update');
                    } else {
                        setFormToCreateMode(); // will also update label etc.
                    }
                }
                // If switching to list, always reset form
                if (target === '#deal-stage-list') {
                    setFormToCreateMode();
                }
            });

            // Handle edit button click
            $(document).on('click', '.edit-group', function () {
                const groupId = $(this).data('id');
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Loading...');
                $.ajax({
                    url: "{{ route('dealstage.edit', ':id') }}".replace(':id', groupId),
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            // Pass is_service value as third parameter
                            setFormToEditMode(response.data.stage_name, response.data.id, response.data.color);
                            $('a[href="#new-deal-stage"]').tab('show');
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
                    message: 'Are you sure you want to delete this Dealstage?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');
                            $.ajax({
                                url: "{{ route('dealstage.destroy') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    id: groupId
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
                                            'Failed to delete Dealstage.',
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
            $('#deal_stage_form').submit(function (e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').hide();
                let isValid = true;
                const names = [];
                $('input[name="stage_name[]"]').each(function (index) {
                    const val = $(this).val().trim();
                    if (!val) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text('Please enter a Dealstage').show();
                        isValid = false;
                    } else {
                        names.push(val);
                    }
                });
                if (!isValid) return;

                const colors = [];
                $('input[name="color[]"]').each(function (index) {
                    const val = $(this).val().trim();
                    if (!val) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text('Please enter a color').show();
                        isValid = false;
                    } else {
                        colors.push(val);
                    }
                });

                // stop if colors invalid (same behaviour as names)
                if (!isValid) return;

                // Prepare form data
                const formData = {
                    stage_name: names,
                    color: colors,
                    _token: $('input[name="_token"]').val()
                };
                if (editMode) formData.id = $('input[name="id[]"]').val();

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    url: "{{ route('dealstage.store') }}",
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
                                $('a[href="#deal-stage-list"]').tab('show');
                                setFormToCreateMode(); // reset after submit
                            }
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const field in errors) {
                                if (field.startsWith('stage_name.')) {
                                    const index = field.split('.')[1];
                                    $('input[name="stage_name[]"]').eq(index).addClass(
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

            // Reset form handler: set back to create mode
            $('#deal_stage_form').on('reset', function () {
                setFormToCreateMode();
            });

            // Real-time validation for name fields (all, since dynamic)
            // Real-time validation for name fields (all, since dynamic)
            $(document).on('input', 'input[name="stage_name[]"]', function () {
                if ($(this).val().trim()) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').hide();
                }
            });

            // Real-time validation for color fields (added)
            $(document).on('input', 'input[name="color[]"]', function () {
                if ($(this).val().trim()) {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').hide();
                }
            });

        });
    </script>
</body>

</html>