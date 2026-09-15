<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <style>
        .flatpickr-day.today:not(.selected) {
            background: none !important;
            border-color: transparent !important;
            color: inherit !important;
        }

        .modal-open .flatpickr-calendar {
            position: absolute !important;
        }

        .btn-blue {
            background-color: #71a8ce !important;
            padding: 3px 10px !important;
            border-radius: 10px !important;
            color: #fff !important;
        }

        .btn-blue:hover {
            background-color: #5a8fb5 !important;
            color: #fff !important;
        }

        body.wait-cursor,
        body.wait-cursor * {
            cursor: wait !important;
        }

        /* Timeline Container Elements */
.timeline-date-badge {
    background-color: #f0ad4e;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    display: inline-block;
    margin-bottom: 15px;
}
.timeline-item {
    position: relative;
    padding-left: 45px;
    margin-bottom: 20px;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: 17px;
    top: 32px;
    bottom: -28px;
    width: 2px;
    background-color: #e9ecef;
}
.timeline-item:last-child::before {
    display: none;
}
.timeline-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    z-index: 2;
}
.bg-create { background-color: #28a745; }
.bg-update { background-color: #007bff; }
.bg-delete { background-color: #dc3545; }
.bg-reset { background-color: #ffc107; }

.timeline-card {
    background: #fff;
    border: 1px solid #e3e6f0;
    border-radius: 6px;
    padding: 10px 15px;
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
                            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                                <div class="card-title text-primary mb-0">
                                    {{ $page_name }}
                                </div>
                                <ul class="nav nav-tabs justify-content-end nav-tabs-footer mb-0" role="tablist">
                                    <li class="nav-item" id="exportBtnPlaceholder"></li>
                                    @if (permissions_check('canView'))
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" role="tab"
                                                aria-current="page" href="#staff-list" aria-selected="true">List</a>
                                        </li>
                                    @endif
                                    @if (permissions_check('canCreate'))
                                        <li class="nav-item ">
                                            <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                                href="#new-staff" aria-selected="false">New
                                                Staff</a>
                                        </li>
                                    @endif
                                  
                                    <li class="nav-item d-none">
                                        <a href="{{ route('staff.export.excel') }}" class="btn btn-success"
                                            id="exportExcelBtn">
                                            <i class="fas fa-file-excel"></i> Export to Excel
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body p-0">
                                <div class="tab-content">
                                    @if (permissions_check('canView'))
                                        <div class="tab-pane border-0 show active text-muted px-1 " id="staff-list"
                                            role="tabpanel">
                                            <div class="row">
                                                <table id="rawdata" class="table table-bordered" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Name</th>
                                                            <th>Email</th>
                                                            <th>Phone</th>
                                                            <th>Position</th>
                                                            <th>Department</th>
                                                            <th>Staff Type</th>

                                                            <!-- HIDDEN EXPORT COLUMNS -->
                                                            <th>Staff_code</th>
                                                            <th>Salute</th>
                                                            <!-- <th>role_name</th> -->
                                                            <th>Address</th>
                                                            <th>status</th>

                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                    @if (permissions_check('canCreate'))
                                        <div class="tab-pane text-muted border-0 px-1 " id="new-staff" role="tabpanel">
                                            <form id="staff_form" enctype="multipart/form-data" novalidate>
                                                @csrf

                                                <!-- Section 1: Basic Information -->
                                                <div class="card mb-1">
                                                    <div class="card-header bg-primary text-white px-2 py-1">
                                                        <h5 class="mb-0">Basic Information</h5>
                                                    </div>
                                                    <div class="card-body px-1 py-2">
                                                        <div class="row">
                                                            <div class="col-md-3 mb-3">
                                                                <label for="salute" class="form-label mb-0">Salutation
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-select select2" id="salute"
                                                                    name="salute" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Mr">Mr</option>
                                                                    <option value="Mrs">Mrs</option>
                                                                    <option value="Miss">Miss</option>
                                                                    <option value="Ms">Ms</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="salute-error">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 mb-3">
                                                                <label for="staff_name" class="form-label mb-0">Staff
                                                                    Name <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="staff_name" name="staff_name" required>
                                                                <div class="invalid-feedback" id="staff_name-error">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 mb-3">
                                                                <label for="staff_email" class="form-label mb-0">Email
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="email" class="form-control"
                                                                    id="staff_email" name="staff_email" required>
                                                                <div class="invalid-feedback" id="staff_email-error">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 mb-3">
                                                                <label for="phone" class="form-label mb-0">Phone
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    id="phone" name="phone" required>
                                                                <div class="invalid-feedback" id="phone-error">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-12 mb-3">
                                                                <label for="address" class="form-label mb-0">Address
                                                                    <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                                                                <div class="invalid-feedback" id="address-error">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Section 2: Employment Details -->
                                                <div class="card mb-1">
                                                    <div class="card-header bg-primary text-white px-2 py-1">
                                                        <h5 class="mb-0">Employment Details</h5>
                                                    </div>
                                                    <div class="card-body px-1 py-2">
                                                        <div class="row">
                                                            <div class="col-md-3 mb-3">
                                                                <label for="position" class="form-label mb-0">Position
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    id="position" name="position">
                                                            </div>
                                                            <div class="col-md-3 mb-3">
                                                                <label for="department"
                                                                    class="form-label mb-0">Department
                                                                </label>
                                                                <select class="form-select select2" id="department"
                                                                    name="department">
                                                                    <option value="">Select
                                                                        Department</option>
                                                                    <option value="HR">Human Resources
                                                                    </option>
                                                                    <option value="IT">Information
                                                                        Technology
                                                                    </option>
                                                                    <option value="Finance">Finance
                                                                    </option>
                                                                    <option value="Marketing">Marketing
                                                                    </option>
                                                                    <option value="Operations">Operations
                                                                    </option>
                                                                    <option value="PU-Department">PU
                                                                        Deparment</option>
                                                                    <option value="Roto-Machine">Roto
                                                                        Machine Department</option>
                                                                    <option value="Dispatch">Dispatch
                                                                        Department</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 mb-3">
                                                                <label for="role_id" class="form-label mb-0">Role
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-select select2" id="role_id"
                                                                    name="role_id" required>
                                                                    <option value="">Select Role
                                                                    </option>
                                                                    @foreach ($role as $data)
                                                                        <option value="{{ $data->role_id }}">
                                                                            {{ $data->role_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="invalid-feedback" id="role_id-error">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 mb-3">
                                                                <label for="staff_type" class="form-label mb-0">Staff Type
                                                                    <span class="text-danger">*</span></label>
                                                                <select class="form-select select2" id="staff_type"
                                                                    name="staff_type" required>
                                                                    <option value="">Select Type
                                                                    </option>
                                                                    <option value="Domestic">Domestic</option>
                                                                    <option value="Export">Export</option>
                                                                </select>
                                                                <div class="invalid-feedback" id="staff_type-error">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Form Actions -->
                                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                    <button type="reset" class="btn btn-secondary me-md-2"
                                                        id="resetBtn">Reset</button>
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
      <!--model -->
      <!-- Timeline Logs Modal Structure -->
        <div class="modal fade" id="timelineModal" tabindex="-1" aria-labelledby="timelineModalLabel" aria-hidden="true">
            <div class="modal-dialog  modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timelineModalLabel">Timeline History</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-light" style="max-height: 450px; overflow-y: auto;">
                        <div id="timelineContainer" class="position-relative">
                            <!-- Dynamic standard rendering goes here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        @include('include.footer')
    </div>
    @include('include.scroll-top')
    @include('include.footer_link')
    @include('include.datatable_js_link')

    <script src="{{ $actual_url . '/admin-assets/libs/flatpickr/flatpickr.min.js' }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
        const BaseUrl = "{{ $public_path }}";

        // Function to handle showing/hiding shift options
       


        $(document).ready(function() {
            $(document).ajaxStart(function() {
                $('body').addClass('wait-cursor');
            }).ajaxStop(function() {
                $('body').removeClass('wait-cursor');
            });
           
            $(document).on('click', '.reset-password', function() {

                const staffId = $(this).data('id');
                const staffName = $(this).data('name');

                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'reset-password',
                    zindex: 999,
                    title: 'Confirm',
                    message: 'Are you sure you want to reset password for ' + staffName +
                        ' to default (123456)?',
                    position: 'center',

                    buttons: [

                        ['<button><b>YES</b></button>', function(instance, toast) {

                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');

                            $.ajax({
                                url: "{{ route('staff.reset.password') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    id: staffId
                                },

                                success: function(response) {

                                    if (response.status) {

                                        iziToast.success({
                                            title: 'Success',
                                            message: response.message,
                                            position: 'topRight'
                                        });

                                    } else {

                                        iziToast.error({
                                            title: 'Error',
                                            message: response.message,
                                            position: 'topRight'
                                        });

                                    }
                                },

                                error: function() {

                                    iziToast.error({
                                        title: 'Error',
                                        message: 'Failed to reset password.',
                                        position: 'topRight'
                                    });

                                }
                            });

                        }, true],

                        ['<button>NO</button>', function(instance, toast) {

                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');

                        }]
                    ]
                });

            });

            

           

           

           

         

            // Start video stream
         

            $('.form-control').on('input', function() {
                $(this).removeClass('is-invalid');
                $('#' + this.id + '-error').text('');
            });

            // ==============================================================
            // HELPER: Clear the face preview canvas
            // ==============================================================
           

            // ==============================================================
            // ENHANCED FACE CAPTURE WITH LOADER
            // ==============================================================
            

            $('.select2').select2({
                width: '100%',
                placeholder: $(this).data('placeholder') || 'Select an option',
                allowClear: Boolean($(this).data('allow-clear'))
            });

            $('#rawdata').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('staff.list') }}",
                    type: "GET",
                    cache: false
                },
                order: [],
                dom: "<'row mb-2'<'col-md-6'l><'col-md-6 d-flex justify-content-end'fB>>rtip",
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Export Excel',
                    className: 'btn btn-blue btn-sm',
                    exportOptions: {
                        columns: [7, 8, 1, 2, 3, 4, 5, 6, 9, 10]
                    }
                }],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'staff_email'
                    },
                    {
                        data: 'phone'
                    },
                    {
                        data: 'position'
                    },
                    {
                        data: 'department'
                    },
                    {
                        data: 'staff_type'
                    },
                    {
                        data: 'staff_code',
                        visible: false
                    },
                    {
                        data: 'salute',
                        visible: false
                    },
                    {
                        data: 'address',
                        visible: true
                    },
                    {
                        data: 'status',
                        visible: true
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        visible: true
                    }
                ],
            });
            $('#rawdata').DataTable().buttons().container().find('button').removeClass('btn-secondary');
            $('#exportBtnPlaceholder').append($('.dt-buttons'));
            

            
           $(document).on('click', '.view-logs', function() {
    var staffId = $(this).data('id');
    $('#timelineContainer').html('<div class="text-center py-4"><span class="spinner-border spinner-border-sm" role="status"></span> Loading activity trails...</div>');
    $('#timelineModal').modal('show');

    $.ajax({
        url: "{{ route('staff.logs') }}",
        type: "GET",
        data: { staff_id: staffId },
        success: function(response) {
            if (response.status && Object.keys(response.data).length > 0) {
                var html = '';
                
                // Group loop by date metrics values
                $.each(response.data, function(date, logs) {
                    html += '<div class="timeline-date-badge">' + date + '</div>';
                    
                    $.each(logs, function(index, log) {
                        var iconClass = 'bg-update', icon = 'fa-edit';
                        if(log.action === 'Created') { iconClass = 'bg-create'; icon = 'fa-plus'; }
                        else if(log.action === 'Deleted') { iconClass = 'bg-delete'; icon = 'fa-trash-alt'; }
                        else if(log.action === 'Password Reset') { iconClass = 'bg-reset'; icon = 'fa-key'; }

                        var timeStr = new Date(log.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                        html += '<div class="timeline-item">';
                        html += '  <div class="timeline-icon ' + iconClass + '"><i class="fas ' + icon + '"></i></div>';
                        html += '  <div class="timeline-card d-flex align-items-center justify-content-between">';
                        html += '    <div>';
                        html += '      <small class="text-muted me-2"><i class="far fa-clock"></i> ' + timeStr + '</small>';
                        html += '      <strong>' + log.action + '</strong> - <span class="text-secondary">' + log.description + '</span>';
                        html += '    </div>';
                        html += '    <span class="badge bg-light text-dark border">By ' + log.updater_name + '</span>';
                        html += '  </div>';
                        html += '</div>';
                    });
                });
                $('#timelineContainer').html(html);
            } else {
                $('#timelineContainer').html('<div class="alert alert-warning text-center">No logs recorded against this profile track.</div>');
            }
        },
        error: function() {
            $('#timelineContainer').html('<div class="alert alert-danger text-center">Error reading action metrics from security logs.</div>');
        }
    });
});

            

           


           

            $(document).on('click', '.delete-staff', function() {

                const staffId = $(this).data('id');

                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete this Staff?',
                    position: 'center',

                    buttons: [

                        ['<button><b>YES</b></button>', function(instance, toast) {

                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');

                            $.ajax({
                                url: "{{ route('staff.destroy') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    id: staffId
                                },

                                success: function(response) {

                                    if (response.status === 'success') {

                                        iziToast.success({
                                            title: 'Success',
                                            message: response.message,
                                            position: 'topRight'
                                        });

                                        $('#rawdata').DataTable().ajax.reload(
                                            null, false);

                                    } else {

                                        iziToast.error({
                                            title: 'Error',
                                            message: response.message,
                                            position: 'topRight'
                                        });

                                    }
                                },

                                error: function(xhr) {

                                    iziToast.error({
                                        title: 'Error',
                                        message: xhr.responseJSON
                                            ?.message ||
                                            'Failed to delete staff.',
                                        position: 'topRight'
                                    });

                                }

                            });

                        }, true],

                        ['<button>NO</button>', function(instance, toast) {

                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');

                        }]
                    ]
                });

            });

            $(document).on('click', '.edit-staff', function() {
                var staffId = $(this).data('id');
                $.ajax({
                    url: '{{ route('staff.get.data') }}',
                    type: 'GET',
                    data: {
                        staff_id: staffId
                    },
                    success: function(response) {
                        if (response.status) {
                            var staff = response.data;
                            $('a[href="#new-staff"]').tab('show');
                            $('a[href="#new-staff"]').text('Edit Staff');
                            $('#submitText').text('Update');
                            $('#resetBtn').hide();

                            if (!$('#staff_id').length) {
                                $('#staff_form').append(
                                    '<input type="hidden" id="staff_id" name="staff_id">');
                            }

                            $('#staff_id').val(staff.staff_id);
                            $('#salute').val(staff.salute).trigger('change');
                            $('#staff_name').val(staff.staff_name);
                            $('#staff_email').val(staff.staff_email);
                            $('#phone').val(staff.phone);
                            $('#address').val(staff.address);
                            $('#position').val(staff.position);
                            $('#department').val(staff.department).trigger('change');
                            $('#staff_type').val(staff.staff_type).trigger('change');

                            

                          

                            $('#role_id').val(staff.role_id).trigger('change');
                        }
                    }
                });
            });

            $('a[href="#staff-list"]').on('click', function() {
                $('a[href="#new-staff"]').text('New Staff');
                $('#staff_form')[0].reset();
                if (hireDatePicker) {
                    hireDatePicker.clear();
                }

              
                $('#salute, #department, #staff_type, #role_id').val(
                    null).trigger('change');
                $('#submitText').text('Submit');
                $('#resetBtn').show();
                $('#staff_id').remove();

                // Clear face image preview completely when switching tabs
                
               
            });

            $('#staff_form').on('submit', function(e) {
                e.preventDefault();
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                var formData = new FormData(this);
                var staffId = $('#staff_id').val();
                var url = staffId ? '{{ route('staff.update') }}' : '{{ route('staff.store') }}';

                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
                );

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            iziToast.success({
                                title: 'Success',
                                message: response.message,
                                position: 'topRight'
                            });
                            $('#staff_form')[0].reset();
                           
                            $('#staff_id').val('');

                            // =============================================
                            // CHANGED: RESET THE CANVAS PREVIEW ON SUCCESS
                            // =============================================
                          
                           

                            $('a[href="#new-staff"]').tab('show');
                            $('a[href="#new-staff"]').text('New Staff');
                            $('#submitText').text('Submit');
                            $('.nav-tabs a[href="#staff-list"]').tab('show');
                            $('#rawdata').DataTable().ajax.reload(null, false);
                        } else {
                            if (response.face_match) {
                                iziToast.warning({
                                    title: 'Warning',
                                    message: response.message,
                                    position: 'topRight'
                                });
                            } else {
                                iziToast.error({
                                    title: 'Error',
                                    message: response.message,
                                    position: 'topRight'
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var firstKey = Object.keys(errors)[0];
                            var firstMessage = errors[firstKey][0];

                            // Sirf fields ko highlight (red border) karo, per-field text mat dikhao
                            $.each(errors, function(key) {
                                $('#' + key).addClass('is-invalid');
                            });

                            let firstInvalid = $('#' + firstKey);
                            if (firstInvalid.length) {
                                firstInvalid[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                                setTimeout(function() {
                                    firstInvalid.focus();
                                }, 300);
                            }

                            // Sirf ek hi message dikhana hai
                            iziToast.warning({
                                title: 'Validation Warning',
                                message: firstMessage,
                                position: 'topRight'
                            });
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: xhr.responseJSON.message ||
                                    'An error occurred. Please try again.',
                                position: 'topRight'
                            });
                        }
                    },
                    complete: function() {
                        $('#submitBtn').prop('disabled', false).text('Submit');
                    }
                });
            });
            $(document).on('input', '#phone, #family_contact_no', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

           
        });
    </script>
</body>

</html>
