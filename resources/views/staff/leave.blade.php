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
                                        <a class="nav-link active"
                                            data-bs-toggle="tab"
                                            role="tab"
                                            href="#leave-list">
                                            List
                                        </a>
                                    </li>
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link"
                                            data-bs-toggle="tab"
                                            role="tab"
                                            href="#new-leave" aria-current="page"
                                            id="companyTabLabel1" aria-selected="false">
                                            Add Leave
                                        </a>
                                    </li>
                                    <li class="nav-item ">
                                        <a class="nav-link" data-bs-toggle="tab" role="tab"
                                            aria-current="page" id="companyTabLabel2" href="#view-leave"
                                            aria-selected="false">view Leave-Balance</a>
                                    </li>
                                    <li class="nav-item ">
                                                <a class="nav-link" data-bs-toggle="tab" role="tab"
                                                aria-current="page" id="companyTabLabel3" href="#view-rule"
                                                aria-selected="false">view Leave-Rule</a>
                                        </li>
                                        <li class="ms-auto align-self-center">
                                        <span class="badge rounded-pill bg-warning-light text-warning fw-normal p-2 px-3 border border-warning-subtle" style="font-size: 11px;">
                                            <i class="bi bi-info-circle me-1"></i> 
                                            <strong>Note:</strong> Casual Leave (CL) is applicable only after the probation period.
                                        </span>
                                    </li>
                                 
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    {{-- List Tab --}}
                                   
                                    <div class="tab-pane fade show active" id="leave-list" role="tabpanel">
                                        <div class="table-responsive">
                                            <table id="rawdata" class="table table-bordered menu-submenu-data"
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th style="width:1%;">#</th>
                                                                    <th style="width:10%">Leave type</th>
                                                                    <th style="width:10%">Leave Duration</th>
                                                                    <!--<th style="width:10%">Start Date</th>
                                                                    <th style="width:10%">End Date</th>-->
                                                                    <th style="width:10%">Total Days</th>
                                                                    <th style="width:10%">Leave Reason</th>
                                                                    <th style="width:10%">Notes</th>
                                                                    <th style="width:20%">Status</th>

                                                                    {{-- <th class="text-center" width="10%">Action</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    

                                    {{-- New / Edit Shift Tab --}}
                                    
                                    <div class="tab-pane text-muted px-1" id="new-leave" role="tabpanel">
                                        <form id="leave_form" enctype="multipart/form-data" novalidate>
                                            @csrf

                                            {{-- Hidden field for edit mode --}}
                                            <input type="hidden" id="leavemid" name="leavemid" value="">

                                            <div class="row">
                                                <div class="col-md-3">
                                                                <label class="form-label">Leave
                                                                    Duration(startdate-enddate) <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="date_range" id="date_range"
                                                                    class="form-control" autocomplete="off" readonly>
                                                                <input type="hidden" name="start_date" id="start_date">
                                                                <input type="hidden" name="end_date" id="end_date">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="total_days" class="form-label"> Total Days
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="number" name="total_days"
                                                                    class="form-control" readonly id="total_days">
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label for="leave_type" class="form-label"> Leave Type
                                                                    <span class="text-danger">*</span></label>
                                                                <select name="leave_type" id="leave_type"
                                                                    class="form-control select2" required disabled>
                                                                    <option value="">--Select--</option>
                                                                    <!--<option value="CL">Casual Leave (CL)</option>
                                                                    <option value="PL">Privilege Leave (PL)</option>
                                                                    <option value="SL">Sick Leave (SL)</option>
                                                                    <option value="LWP">Leave Without Pay (LWP)</option>-->
                                                                    <!-- Options will be dynamically populated by JS -->
                                                                </select>

                                                            </div>

                                                            <div class="col-md-3">
                                                                <label>Leave Day*</label><br>
                                                                <label><input type="radio" name="leave_day" value="full"
                                                                        checked> Full Day</label>
                                                                <label class="ms-3"><input type="radio" name="leave_day"
                                                                        value="half"> Half Day</label>
                                                            </div>
                                            </div> <br>
                                            <div class="row">
                                                 <div class="col-md-3">
                                                                <label for="contact_info" class="form-label"> Contact
                                                                    info <span class="text-danger">*</span></label>
                                                                <input type="text" name="contact_info"
                                                                    class="form-control"
                                                                    maxlength="10"
                                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                                    value="{{ session('staff_id')->phone ?? '' }}">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="reason" class="form-label">Leave Reason
                                                                    <span class="text-danger">*</span></label>

                                                                <input type="text" name="reason" class="form-control">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label for="notes" class="form-label"> Notes/
                                                                    Comments</label>
                                                                <textarea name="notes" class="form-control"
                                                                    rows="2"></textarea>
                                                            </div>
                                            </div>

                                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                                                <button type="reset"
                                                                class="btn btn-secondary me-md-2">Reset</button>
                                                <button type="submit" class="btn btn-primary" id="submitBtn"> <span id="submitText">Submit</span>
                                                                <span id="submitSpinner"
                                                                    class="spinner-border spinner-border-sm align-middle d-none"
                                                                    role="status" aria-hidden="true"></span></button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane text-muted border-0 px-1" id="view-leave"
                                                    role="tabpanel">
                                                    <div class="row row-cols-xxl-4 row-cols-xl-4 row-cols-md-2">
                                                        @foreach($leaveStats as $leave)
                                                            <div class="col card-background flex-fill">
                                                                <div class="card custom-card">
                                                                    <div class="card-body">
                                                                        <p class="fw-medium mb-1 text-muted">
                                                                            {{ $leave['type'] }} Leave
                                                                        </p>
                                                                        <div
                                                                            class="d-flex align-items-center justify-content-between">
                                                                            <div>
                                                                                <p class="mb-0"><strong>Available:</strong>
                                                                                    {{ $leave['available'] }} day(s)</p>
                                                                                <p class="mb-0"><strong>Used:</strong>
                                                                                    {{ $leave['used'] }} day(s)</p>
                                                                            </div>
                                                                            <span class="avatar bg-info-transparent">
                                                                                <i
                                                                                    class="bx bx-calendar-check fs-22 text-info"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="tab-pane text-muted border-0 px-1" id="view-leave"
                                                        role="tabpanel">

                                                    </div>

                                                </div>
                                                  <div class="tab-pane fade border-0 px-1" id="view-rule" role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-xl-12">
                                                            <div class="card custom-card overflow-hidden">
                                                                <div class="card-header justify-content-between">
                                                                    <div class="card-title">
                                                                        Leave Policy & Guidelines
                                                                    </div>
                                                                    <span class="badge bg-primary-transparent">Effective 2026</span>
                                                                </div>
                                                                <div class="card-body p-0">
                                                                    <ul class="list-group list-group-flush">
                                                                        <li class="list-group-item d-flex align-items-start gap-3 py-3">
                                                                            <div class="avatar avatar-sm bg-primary-transparent text-primary">
                                                                                <i class="bx bx-shield-quarter fs-18"></i>
                                                                            </div>
                                                                            <div class="flex-fill">
                                                                                <p class="fw-semibold mb-0">Probation Requirement</p>
                                                                                <span class="text-muted fs-12">Casual Leave (CL) can only be applied after the successful completion of the probation period.</span>
                                                                            </div>
                                                                        </li>

                                                                        <li class="list-group-item d-flex align-items-start gap-3 py-3">
                                                                            <div class="avatar avatar-sm bg-info-transparent text-info">
                                                                                <i class="bx bx-time-five fs-18"></i>
                                                                            </div>
                                                                            <div class="flex-fill">
                                                                                <p class="fw-semibold mb-0">Advance Notice for PL</p>
                                                                                <span class="text-muted fs-12">Privilege Leave (PL) applications must be submitted at least <strong class="text-dark">10 days</strong> in advance.</span>
                                                                            </div>
                                                                        </li>

                                                                        <li class="list-group-item d-flex align-items-start gap-3 py-3">
                                                                            <div class="avatar avatar-sm bg-warning-transparent text-warning">
                                                                                <i class="bx bx-wallet fs-18"></i>
                                                                            </div>
                                                                            <div class="flex-fill">
                                                                                <p class="fw-semibold mb-0">Balance Constraints</p>
                                                                                <span class="text-muted fs-12">Except for LWP (Leave Without Pay), you cannot apply for leave exceeding your currently available balance.</span>
                                                                            </div>
                                                                        </li>
                                                                        <li class="list-group-item d-flex align-items-start gap-3 py-3">
                                                                            <div class="avatar avatar-sm bg-secondary-transparent text-secondary">
                                                                                <i class="bx bx-calendar-event fs-18"></i>
                                                                            </div>
                                                                            <div class="flex-fill">
                                                                                <p class="fw-semibold mb-0">Policy Cycle Validity</p>
                                                                                <span class="text-muted fs-12">Leave must be requested within the active balance period. Requests outside your assigned start and end dates will be automatically rejected.</span>
                                                                            </div>
                                                                        </li>

                                                                        <li class="list-group-item d-flex align-items-start gap-3 py-3">
                                                                            <div class="avatar avatar-sm bg-danger-transparent text-danger">
                                                                                <i class="bx bx-error-alt fs-18"></i>
                                                                            </div>
                                                                            <div class="flex-fill">
                                                                                <p class="fw-semibold mb-0">Notice Period Restriction</p>
                                                                                <span class="text-muted fs-12">Paid leave is not encashable or applicable once a resignation has been tendered (Notice Period).</span>
                                                                            </div>
                                                                        </li>

                                                                        <li class="list-group-item d-flex align-items-start gap-3 py-3 border-bottom-0">
                                                                            <div class="avatar avatar-sm bg-warning-transparent text-warning">
                                                                                <i class="bx bx-calendar-x fs-18"></i>
                                                                            </div>
                                                                            <div class="flex-fill">
                                                                                <p class="fw-semibold mb-0">Daily Cut-off</p>
                                                                                <span class="text-muted fs-12">Full-day leave requests for the current date are not accepted after the work shift has commenced.</span>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="card-footer bg-light">
                                                                    <p class="mb-0 fs-11 text-muted">
                                                                        <i class="bx bx-help-circle me-1"></i> For further clarifications, please contact the HR department.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
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

    <script>

        let editMode = false;

        function setFormToEditMode(unit, leavemid) {
            editMode = true;
            $('#leave_form').attr('data-mode', 'edit');
            $('#submitBtn').text('Update');
            $('#leavemid').val(leavemid);  //  Just set value instead of removing/re-addings
            $('#leave_form').append(`<input type="hidden" name="leavemid" value="${leavemid}">`);
            $('#unit').val(unit);
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

        function setFormToCreateMode() {
            editMode = false;
            $('#leave_form').attr('data-mode', 'create');
            $('#submitBtn').text('Submit');
            $('#leavemid').val('');  // ✅ Clear it instead of removing
            $('#leave_form')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }
        $(document).ready(function () {
            // Initialize DataTable for countries list
            var leaveTable = $('#rawdata').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('leave.list') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'leave_type', name: 'leave_type' },
                    { data: 'leave_duration', name: 'leave_duration' },

                    { data: 'total_days', name: 'total_days' },
                    { data: 'reason', name: 'reason' },
                    { data: 'notes', name: 'notes' },
                    { data: 'status', name: 'status' },
                    // { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
                    emptyTable: 'No leave requests found',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total entries)'
                }
            });

            // Focus on input when new country tab is shown
            $('a[href="#new-leave"]').on('shown.bs.tab', function () {
                setTimeout(function () {
                    $('#unit').trigger('focus');
                }, 200);
            });

            // Reset form when switching tabs
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#new-leave') {
                    if (!editMode) setFormToCreateMode();
                }
                if (target === '#leave-list') {
                    setFormToCreateMode();
                    leaveTable.ajax.reload(null, false); // Refresh list when tab is shown
                }
            });
        $('#leave_form').submit(function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            let isUpdate = ($('#leavemid').val() || '').trim() !== "";

            let url = isUpdate ? "{{ route('leave.update') }}" : "{{ route('leave.store') }}";

            $('#submitSpinner').removeClass('d-none');
            $('#submitText').text('Processing...');
            $('#submitBtn').prop('disabled', true);

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    $('#submitSpinner').addClass('d-none');
                    $('#submitText').text('Submit');
                    $('#submitBtn').prop('disabled', false);

                    iziToast.success({
                        title: 'Success',
                        message: response.message,
                        position: 'topRight',
                        timeout: 3000,
                        onClosed: function () {
                            $('#leave_form')[0].reset();
                            $('#leavemid').val('');
                            // optionally reload DataTable

                            $('a[href="#leave-list"]').tab('show');
                        }
                    });
                },
                error: function (xhr) {
                    $('#submitSpinner').addClass('d-none');
                    $('#submitText').text('Submit');
                    $('#submitBtn').prop('disabled', false);

                    if (xhr.status === 422) {
                        let res = xhr.responseJSON;
                        $('input[name="' + res.field + '"], select[name="' + res.field + '"]').addClass('is-invalid');
                        $('input[name="' + res.field + '"], select[name="' + res.field + '"]').next('.invalid-feedback').text(res.message);
                        iziToast.warning({ title: 'Validation Error', message: res.message, position: 'topRight' });
                    } else {
                        iziToast.error({ title: 'Error', message: xhr.responseJSON.message, position: 'topRight' });
                    }
                }
            });
        });







        // Clear validation feedback on reset
        $('#leave_form').on('reset', function () {
            setTimeout(() => {
                $('#unit').removeClass('is-invalid').next('.invalid-feedback').text('');
            }, 0);
        });
        });
    </script>
    <script>
        //let startDate = null;
        //let endDate = null;
        const dateRangeInput = document.getElementById("date_range");
        const startHidden = document.getElementById("start_date");
        const endHidden = document.getElementById("end_date");
        const leaveBalanceStart = '{{ $leaveBalanceStart }}'; // e.g., 2026-01-01
        const leaveBalanceEnd = '{{ $leaveBalanceEnd }}'; // e.g., 2027-12-31
   /*
       const user = {
        is_probation_completed: {{ session('user.is_probation_completed') ? 'true' : 'false' }},
        is_in_notice_period: {{ session('user.is_in_notice_period') ? 'true' : 'false' }},
        
    };
    */

    function validateLeavePeriod() {
    const startDateVal = document.getElementById("start_date").value;
    const endDateVal = document.getElementById("end_date").value;

    if (!startDateVal || !endDateVal || !leaveBalanceStart || !leaveBalanceEnd) return true;

    const startDate = new Date(startDateVal);
    const endDate = new Date(endDateVal);
    const balanceStart = new Date(leaveBalanceStart);
    const balanceEnd = new Date(leaveBalanceEnd);

    // Check if leave is completely within leave balance period
    if (startDate < balanceStart || endDate > balanceEnd) {
        iziToast.warning({
            title: 'Invalid Leave Period',
            message: `Your selected leave (${startDateVal} → ${endDateVal}) is outside your leave balance period (${leaveBalanceStart} → ${leaveBalanceEnd}).`,
            position: 'topRight'
        });
        return false;
    }

    return true;
}

        const user = {
            is_probation_completed: @json($is_probation_completed),
    is_in_notice_period: @json($is_in_notice_period),
            hire_date: '{{ $hire_date ?? '' }}',
        };

        const leaveBalance = @json($leaveBalanceJs);

        console.log(leaveBalance);

        const leaveTypeSelect = document.getElementById('leave_type');
        const errorBox = document.createElement('div');
        errorBox.classList.add('text-danger', 'mt-1');
        leaveTypeSelect.parentElement.appendChild(errorBox);

        function showError(message) {
            errorBox.textContent = message;
            leaveTypeSelect.classList.add('is-invalid');
        }

        function clearError() {
            errorBox.textContent = '';
            leaveTypeSelect.classList.remove('is-invalid');
        }

        // Validate leave type conditions
        function validateLeaveType() {
            const leaveType = leaveTypeSelect.value;
            const startDateVal = document.getElementById("start_date").value;
            const totalDays = parseFloat(document.getElementById("total_days").value); //add new 
            const today = new Date().toISOString().split('T')[0];

            if (!leaveType || !startDateVal || !totalDays) return true;

            const startDate = new Date(startDateVal);
            const currentDate = new Date(today);

            if (['CL', 'PL', 'SL'].includes(leaveType)) {
                if (!user.is_probation_completed) {
                    showError(`${leaveType} is allowed only after probation.`);
                    return false;
                }

                if (user.is_in_notice_period) {
                    showError(`No paid leave (${leaveType}) is allowed during notice period.`);
                    return false;
                }
            }

            if (leaveType === 'PL') {
                const diffDays = Math.ceil((startDate - currentDate) / (1000 * 60 * 60 * 24));
                if (diffDays < 10) {
                    showError('Privilege Leave (PL) must be applied at least 10 days in advance.');
                    return false;
                }
            }

            // Check if requested days exceed balance
            if (leaveType !== 'LWP') {
                const availableBalance = leaveBalance[leaveType] ?? 0;
                if (totalDays > availableBalance) {
                    showError(`Insufficient ${leaveType} balance. You have only ${availableBalance} day(s) left.`);
                    return false;
                }
            }

            clearError();
            return true;
        }

        leaveTypeSelect.addEventListener('change', validateLeaveType);
        document.getElementById("start_date").addEventListener('change', validateLeaveType);

        // Validate before form submit
        document.getElementById('leave_form').addEventListener('submit', function (e) {
            if (!validateLeaveType()) {
                e.preventDefault();
            }

            // Validate leave balance period
            if (!validateLeavePeriod()) {
                e.preventDefault();
                return;
            }
        });

        // Leave types with conditions
        const leaveTypes = [
            { value: 'LWP', label: 'Leave Without Pay (LWP)', allowed: true },
            { value: 'CL', label: 'Casual Leave (CL)', allowed: user.is_probation_completed },
            { value: 'PL', label: 'Privilege Leave (PL)', allowed: user.is_probation_completed },
            { value: 'SL', label: 'Sick Leave (SL)', allowed: user.is_probation_completed },
        ];
        leaveTypes.forEach(lt => {
            if (lt.value === 'LWP') {
                let option = document.createElement('option');
                option.value = lt.value;
                option.text = lt.label;
                leaveTypeSelect.appendChild(option);
            } else if (lt.allowed && !user.is_in_notice_period) {
                let option = document.createElement('option');
                option.value = lt.value;
                option.text = lt.label;
                leaveTypeSelect.appendChild(option);
            }
        });
console.log(user);
console.log(typeof user.is_probation_completed); // should be boolean
console.log(typeof user.is_in_notice_period);    // should be boolean
        function calculateDays() {
            if (startDate && endDate && endDate >= startDate) {
                const diff = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('total_days').value = diff;
            } else {
                document.getElementById('total_days').value = '';
            }
        }


        flatpickr(dateRangeInput, {
            mode: "range",
            dateFormat: "d-m-Y",
            defaultDate: [new Date(), new Date()], // today as default for both
            disableMobile: true,
            minDate: "today",
            onChange: function (selectedDates) {
                if (selectedDates.length === 2) {
                    const start = selectedDates[0];
                    const end = selectedDates[1];

                    startHidden.value = flatpickr.formatDate(start, "Y-m-d");
                    endHidden.value = flatpickr.formatDate(end, "Y-m-d");

                    // calculate total days
                    const diff = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
                    document.getElementById('total_days').value = diff;
                    
                    leaveTypeSelect.disabled = false;
                    
                } else {
                    startHidden.value = "";
                    endHidden.value = "";
                    document.getElementById('total_days').value = "";
                    
                    leaveTypeSelect.disabled = true;
                    leaveTypeSelect.value = "";
                }
            }
        });
    </script>
</body>

</html>
