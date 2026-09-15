<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        /* Premium Toggles & Buttons for Payment Modal */
        #payTypeFullBtn,
        #payTypePartialBtn {
            border: none;
            color: #475569;
            background: transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #payTypeFullBtn.active,
        #payTypePartialBtn.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        #payTypeFullBtn:hover:not(.active),
        #payTypePartialBtn:hover:not(.active) {
            background: #f1f5f9;
            color: #1e293b;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 12px;
            margin-top: 25px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 700;
            padding: 12px;
            background: linear-gradient(135deg, #1e293b, #334155);
            border-radius: 8px;
            color: #f8fafc;
            text-uppercase: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .calendar-day {
            min-height: 140px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            background: #fff;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .calendar-day:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.15);
            border-color: #cbd5e1;
            z-index: 10;
        }

        .calendar-day.present {
            border-top: 4px solid #10b981;
            background: linear-gradient(to bottom, #f0fdf4, #ffffff);
        }

        .calendar-day.partial {
            border-top: 4px solid #215e61;
            background: linear-gradient(to bottom, #e6f4f5, #ffffff);
        }

        .calendar-day.absent {
            border-top: 4px solid #ef4444;
            background: linear-gradient(to bottom, #fef2f2, #ffffff);
        }

        .calendar-day.future {
            background: #f8fafc;
            color: #94a3b8;
            border: 1px dashed #cbd5e1;
            opacity: 0.65;
        }

        .day-number {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .day-status {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 9999px;
            display: inline-block;
        }

        .day-status.p-status {
            background: #d1fae5;
            color: #065f46;
        }

        .day-status.partial-status {
            background: #d6eff0;
            color: #174244;
        }

        .day-status.a-status {
            background: #fee2e2;
            color: #991b1b;
        }

        .day-time {
            font-size: 0.75rem;
            margin-top: 5px;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
        }

        .day-time i {
            width: 12px;
            color: #64748b;
        }

        .day-hours {
            font-size: 0.78rem;
            margin-top: 5px;
            font-weight: 700;
            color: #1e293b;
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .staff-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            background: #fff;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            transition: all 0.3s ease;
        }

        .staff-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        .staff-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3b82f6;
        }

        .mini-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 2px;
            cursor: pointer;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
        }

        .mini-dot.p-dot {
            background: #10b981;
        }

        .mini-dot.partial-dot {
            background: #215E61;
        }

        .mini-dot.a-dot {
            background: #ef4444;
        }

        .mini-dot.f-dot {
            background: #cbd5e1;
            color: #64748b;
        }

        .flatpickr-day.today:not(.selected) {
            background: none !important;
            border-color: transparent !important;
            color: inherit !important;
        }

        .modal-open .flatpickr-calendar {
            position: absolute !important;
        }

        .tab-loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Premium Image Preview Modal Styles */
        .modal-backdrop.show {
            backdrop-filter: blur(12px);
            background-color: rgba(15, 23, 42, 0.75) !important;
        }

        .premium-preview-modal .modal-content {
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            transform: scale(0.95);
        }

        .premium-preview-modal.show .modal-content {
            transform: scale(1);
        }

        .premium-preview-modal .btn-close-custom {
            width: 36px;
            height: 36px;
            border-radius: 50px;
            background: rgba(15, 23, 42, 0.05);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            transition: all 0.2s ease;
            outline: none;
        }

        .premium-preview-modal .btn-close-custom:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            transform: rotate(90deg);
        }

        .preview-img-container {
            position: relative;
            background: #0f172a;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.5);
        }

        .preview-img-container img {
            max-height: 460px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .preview-img-container img:hover {
            transform: scale(1.02);
        }

        .btn-view-calendar {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border: 1px solid #bfdbfe;
            color: #2563eb;
            font-weight: 600;
            font-size: 13px;
            border-radius: 9px;
            padding: 8px 12px;
            transition: background 0.4s ease, color 0.4s ease, border-color 0.4s ease, box-shadow 0.4s ease, transform 0.25s ease;
            width: 100%;
        }

        .btn-view-calendar:hover {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: #ffffff !important;
            border-color: #2563eb;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
            transform: translateY(-1px);
        }

        .btn-view-calendar:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
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
                    <div class="col-xxl-12 col-xl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="col card-background-mt flex-fill">
                                    <div class="card custom-card">
                                        <div class="card-body">

                                            <!-- Top Tabs -->
                                            <ul class="nav nav-tabs justify-content-start nav-tabs-footer mb-3"
                                                role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="attendanceTabLink"
                                                        data-bs-toggle="tab" role="tab" aria-current="page"
                                                        href="#attendance-tab" aria-selected="true">
                                                        Daily Wages Attendance List
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="salaryTabLink" data-bs-toggle="tab"
                                                        role="tab" aria-current="page" href="#salary-tab"
                                                        aria-selected="false">
                                                        Salary
                                                    </a>
                                                </li>
                                            </ul>

                                            <!-- Main Filters -->
                                            <div class="row mb-4 align-items-end">
                                                <div class="col-md-3">
                                                    <label for="staffFilter" class="form-label mb-1">Select
                                                        Staff</label>
                                                    <select id="staffFilter" class="form-control select2">
                                                        <option value="">Select an option</option>
                                                        @foreach ($staffList as $staff)
                                                            <option value="{{ $staff->dw_staff_id }}">
                                                                {{ $staff->staff_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="yearFilter" class="form-label mb-1">Year</label>
                                                    <select id="yearFilter" class="form-control select2">
                                                        @php
                                                            $currentYear = now()->year;
                                                        @endphp
                                                        @for ($y = $currentYear - 2; $y <= $currentYear + 1; $y++)
                                                            <option value="{{ $y }}"
                                                                {{ $y == $currentYear ? 'selected' : '' }}>
                                                                {{ $y }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="monthFilter" class="form-label mb-1">Month</label>
                                                    <select id="monthFilter" class="form-control">
                                                        @php
                                                            $currentMonth = now()->month;
                                                        @endphp
                                                        @for ($m = 1; $m <= 12; $m++)
                                                            <option value="{{ $m }}"
                                                                {{ $m == $currentMonth ? 'selected' : '' }}>
                                                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mt-3 d-flex align-items-end gap-2">
                                                    <button id="viewBtn" class="btn btn-primary me-2"
                                                        style="height: 38px;"><i class="fas fa-eye"></i> View</button>
                                                    <div class="dropdown">
                                                        <button class="btn btn-success" type="button"
                                                            id="exportDropdown" data-bs-toggle="dropdown"
                                                            aria-expanded="false" style="height: 38px;">
                                                            <i class="fas fa-file-export"></i> Export<i
                                                                class="bi bi-arrow-down-short"></i>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                                            <li><a class="dropdown-item" href="#"
                                                                    id="exportExcelBtn"><i
                                                                        class="fas fa-file-excel text-success me-2"></i>
                                                                    Export to Excel</a></li>
                                                            <li><a class="dropdown-item" href="#"
                                                                    id="exportPdfBtn"><i
                                                                        class="fas fa-file-pdf text-danger me-2"></i>
                                                                    Export to PDF</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <!-- Back Button: pushed to far right, visible only in single-staff calendar view -->
                                                <div class="col-auto ms-auto mt-3 d-flex align-items-end">
                                                    <button id="globalBackBtn" class="btn d-none"
                                                        style="height: 38px; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); border: 1px solid #cbd5e1; color: #475569; font-weight: 600; border-radius: 8px; transition: all 0.3s ease;"
                                                        onmouseover="this.style.background='linear-gradient(135deg, #334155, #1e293b)'; this.style.color='#fff'; this.style.borderColor='#334155';"
                                                        onmouseout="this.style.background='linear-gradient(135deg, #f1f5f9, #e2e8f0)'; this.style.color='#475569'; this.style.borderColor='#cbd5e1';">
                                                        <i class="fas fa-arrow-left me-1"></i> Back
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Tabs Content -->
                                            <div class="tab-content position-relative" style="min-height: 250px;">

                                                <!-- Loading Overlay -->
                                                <div id="tabLoader" class="tab-loading d-none">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>

                                                <!-- Tab 1: Attendance -->
                                                <div class="tab-pane border-0 show active" id="attendance-tab"
                                                    role="tabpanel">
                                                    <div id="attendanceContainer">
                                                        <!-- Will be populated dynamically -->
                                                    </div>
                                                    <div
                                                        class="mt-4 d-flex flex-wrap align-items-center justify-content-between">
                                                        <div
                                                            class="d-flex align-items-center gap-2 text-muted small mb-2">
                                                            <span>Show</span>
                                                            <select id="attendanceLimit"
                                                                class="form-select form-select-sm" style="width: auto;">
                                                                <option value="12" selected>12</option>
                                                                <option value="24">24</option>
                                                                <option value="48">48</option>
                                                                <option value="96">96</option>
                                                            </select>
                                                            <span>entries</span>
                                                            <span id="attendanceInfo" class="ms-3">Showing 0 to 0 of
                                                                0
                                                                staff</span>
                                                        </div>
                                                        <div id="attendancePagination" class="mb-2"></div>
                                                    </div>
                                                </div>

                                                <!-- Tab 2: Salary -->
                                                <div class="tab-pane border-0" id="salary-tab" role="tabpanel">
                                                    <div class="table-responsive">
                                                        <table id="salaryTable"
                                                            class="table table-bordered text-nowrap"
                                                            style="width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Staff</th>
                                                                    <th>Present</th>
                                                                    <th>Absent</th>
                                                                    <th>Working Hours</th>
                                                                    <th>Per Hour Rate</th>
                                                                    <th>Actual Net Salary</th>
                                                                    <th>Employee PF</th>
                                                                    <th>Gross Salary</th>
                                                                    <th>CTC</th>
                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="salaryTableBody">
                                                                <!-- Will be populated dynamically -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div
                                                        class="mt-4 d-flex flex-wrap align-items-center justify-content-between">
                                                        <div
                                                            class="d-flex align-items-center gap-2 text-muted small mb-2">
                                                            <span>Show</span>
                                                            <select id="salaryLimit"
                                                                class="form-select form-select-sm"
                                                                style="width: auto;">
                                                                <option value="10">10</option>
                                                                <option value="20">20</option>
                                                                <option value="50">50</option>
                                                                <option value="100">100</option>
                                                            </select>
                                                            <span>entries</span>
                                                            <span id="salaryInfo" class="ms-3">Showing 0 to 0 of 0
                                                                staff</span>
                                                        </div>
                                                        <div id="salaryPagination" class="mb-2"></div>
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
        const imgPath = "{{ $img_path }}";

        $(document).ready(function() {
            const canManualEntry = {{ $canManualEntry ? 'true' : 'false' }};
            let currentAttendancePage = 1;
            let currentSalaryPage = 1;
            let wasEditingFromDetails = false;

            // Helpers for cleaning manual logs
            function cleanRemark(remark) {
                if (!remark) return '';
                if (remark.includes('Manual by')) {
                    let parts = remark.split(' - ');
                    if (parts.length > 1) {
                        return parts.slice(1).join(' - ');
                    }
                    return '';
                }
                return remark;
            }

            function getLogSource(remark) {
                if (!remark) return 'Webcam Punch';
                if (remark.includes('Manual by')) {
                    let parts = remark.split(' - ');
                    return parts[0];
                }
                return 'Webcam Punch';
            }

            // Initialize Select2 on filters with try-catch safety
            try {
                if ($.fn.select2) {
                    $('#staffFilter, #yearFilter,#monthFilter').select2({
                        // minimumResultsForSearch: 10,
                        width: '100%'
                    });
                }
            } catch (e) {
                console.error("Select2 initialization failed:", e);
            }

            // Fetch initial view data on page load
            loadActiveTab();

            // When changing tabs, trigger reload of that specific tab
            $('#attendanceTabLink, #salaryTabLink').on('shown.bs.tab', function() {
                loadActiveTab();
            });

            // Trigger filters
            $('#viewBtn').on('click', function() {
                currentAttendancePage = 1;
                currentSalaryPage = 1;
                loadActiveTab();
            });

            // Export handlers
            $('#exportExcelBtn').on('click', function(e) {
                e.preventDefault();
                exportAttendance('excel');
            });

            $('#exportPdfBtn').on('click', function(e) {
                e.preventDefault();
                exportAttendance('pdf');
            });

            function exportAttendance(type) {
                const staffId = $('#staffFilter').val() || '';
                const year = $('#yearFilter').val();
                const month = $('#monthFilter').val();
                let url = '';

                if (type === 'excel') {
                    url = "{{ route('dailywages.attendance.export.excel') }}";
                } else {
                    url = "{{ route('dailywages.attendance.export.pdf') }}";
                }

                url += `?staff_id=${staffId}&year=${year}&month=${month}`;
                window.location.href = url;
            }

            // Trigger limit changes
            $('#attendanceLimit').on('change', function() {
                currentAttendancePage = 1;
                loadActiveTab();
            });
            $('#salaryLimit').on('change', function() {
                currentSalaryPage = 1;
                loadActiveTab();
            });

            function loadActiveTab(callback) {
                const activeTabId = $('#attendance-tab').hasClass('active') ? 'attendance-tab' : ($('#salary-tab')
                    .hasClass('active') ? 'salary-tab' : '');
                const staffId = $('#staffFilter').val();
                const year = $('#yearFilter').val();
                const month = $('#monthFilter').val();
                const attendanceLimit = $('#attendanceLimit').val() || 12;
                const salaryLimit = $('#salaryLimit').val() || 10;

                $('#tabLoader').removeClass('d-none');

                if (activeTabId === 'attendance-tab') {
                    $.ajax({
                        url: "{{ route('dailywages.attendance.data') }}",
                        type: "GET",
                        data: {
                            staff_id: staffId,
                            year: year,
                            month: month,
                            page: currentAttendancePage,
                            limit: attendanceLimit
                        },
                        success: function(res) {
                            try {
                                if (res.status) {
                                    renderAttendance(res, staffId);
                                    if (res.pagination) {
                                        $('#attendanceInfo').text(
                                            `Showing ${res.pagination.from} to ${res.pagination.to} of ${res.pagination.total} staff`
                                        ).show();
                                        $('#attendanceLimit').closest('.gap-2').show();
                                        buildPaginationHTML(res.pagination, '#attendancePagination',
                                            function(p) {
                                                currentAttendancePage = p;
                                                loadActiveTab();
                                            });
                                    } else {
                                        $('#attendanceInfo').hide();
                                        $('#attendanceLimit').closest('.gap-2').hide();
                                        $('#attendancePagination').empty();
                                    }
                                } else {
                                    iziToast.error({
                                        title: 'Error',
                                        message: res.message || 'Failed to load attendance.',
                                        position: 'topRight'
                                    });
                                }
                            } catch (err) {
                                console.error("Render attendance error:", err);
                                iziToast.error({
                                    title: 'Error',
                                    message: 'JS Error: ' + err.message,
                                    position: 'topRight'
                                });
                            }
                        },
                        error: function(xhr) {
                            iziToast.error({
                                title: 'Error',
                                message: 'Failed to fetch attendance data.',
                                position: 'topRight'
                            });
                        },
                        complete: function() {
                            $('#tabLoader').addClass('d-none');
                            if (typeof callback === 'function') {
                                callback();
                            }
                        }
                    });
                } else if (activeTabId === 'salary-tab') {
                    $.ajax({
                        url: "{{ route('dailywages.salary.data') }}",
                        type: "GET",
                        data: {
                            staff_id: staffId,
                            year: year,
                            month: month,
                            page: currentSalaryPage,
                            limit: salaryLimit
                        },
                        success: function(res) {
                            try {
                                if (res.status) {
                                    renderSalary(res.data);
                                    if (res.pagination) {
                                        $('#salaryInfo').text(
                                            `Showing ${res.pagination.from} to ${res.pagination.to} of ${res.pagination.total} staff`
                                        ).show();
                                        $('#salaryLimit').closest('.gap-2').show();
                                        buildPaginationHTML(res.pagination, '#salaryPagination',
                                            function(p) {
                                                currentSalaryPage = p;
                                                loadActiveTab();
                                            });
                                    } else {
                                        $('#salaryInfo').hide();
                                        $('#salaryLimit').closest('.gap-2').hide();
                                        $('#salaryPagination').empty();
                                    }
                                } else {
                                    iziToast.error({
                                        title: 'Error',
                                        message: res.message || 'Failed to load salary.',
                                        position: 'topRight'
                                    });
                                }
                            } catch (err) {
                                console.error("Render salary error:", err);
                                iziToast.error({
                                    title: 'Error',
                                    message: 'JS Error: ' + err.message,
                                    position: 'topRight'
                                });
                            }
                        },
                        error: function(xhr) {
                            iziToast.error({
                                title: 'Error',
                                message: 'Failed to fetch salary data.',
                                position: 'topRight'
                            });
                        },
                        complete: function() {
                            $('#tabLoader').addClass('d-none');
                            if (typeof callback === 'function') {
                                callback();
                            }
                        }
                    });
                } else {
                    $('#tabLoader').addClass('d-none');
                }
            }

            function buildPaginationHTML(pagination, containerSelector, onPageChange) {
                const container = $(containerSelector);
                container.empty();

                if (!pagination || pagination.last_page <= 1) {
                    return;
                }

                let html = '<nav><ul class="pagination mb-0">';

                // Previous Button
                const prevDisabled = pagination.current_page === 1 ? 'disabled' : '';
                html +=
                    `<li class="page-item ${prevDisabled}"><a class="page-link prev-page-btn" href="javascript:void(0);">Previous</a></li>`;

                // Page Numbers
                for (let i = 1; i <= pagination.last_page; i++) {
                    const activeClass = pagination.current_page === i ? 'active' : '';
                    html +=
                        `<li class="page-item ${activeClass}"><a class="page-link page-num-btn" data-page="${i}" href="javascript:void(0);">${i}</a></li>`;
                }

                // Next Button
                const nextDisabled = pagination.current_page === pagination.last_page ? 'disabled' : '';
                html +=
                    `<li class="page-item ${nextDisabled}"><a class="page-link next-page-btn" href="javascript:void(0);">Next</a></li>`;

                html += '</ul></nav>';
                container.html(html);

                // Click Handlers
                container.find('.prev-page-btn').on('click', function() {
                    if (pagination.current_page > 1) {
                        onPageChange(pagination.current_page - 1);
                    }
                });

                container.find('.next-page-btn').on('click', function() {
                    if (pagination.current_page < pagination.last_page) {
                        onPageChange(pagination.current_page + 1);
                    }
                });

                container.find('.page-num-btn').on('click', function() {
                    const targetPage = $(this).data('page');
                    if (targetPage !== pagination.current_page) {
                        onPageChange(targetPage);
                    }
                });
            }

            // Helper: format duration — show minutes if < 60, else hours
            function formatDuration(totalMinutes) {
                if (totalMinutes === null || totalMinutes === undefined) return '-';
                const mins = parseInt(totalMinutes);
                const hrs = Math.floor(mins / 60);
                const remainingMins = mins % 60;

                let result = '';
                if (hrs > 0) {
                    result += hrs + ' hr' + (hrs !== 1 ? 's' : '');
                }
                if (remainingMins > 0 || hrs === 0) {
                    if (result !== '') {
                        result += ' ';
                    }
                    result += remainingMins + ' min' + (remainingMins !== 1 ? 's' : '');
                }
                return result;
            }

            function renderAttendance(res, singleStaffSelected) {
                const container = $('#attendanceContainer');
                container.empty();

                if (res.data.length === 0) {
                    container.html('<div class="text-center py-5 text-muted">No registered staff found.</div>');
                    return;
                }

                // If a single staff member is selected, render the modern monthly calendar grid
                if (singleStaffSelected) {
                    const staff = res.data[0];
                    let html = `
                        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: #fff; border: 1px solid #f1f5f9 !important;">
                            <div class="card-body p-4">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom pb-4 mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative">
                                            <img src="${staff.face_image ? staff.face_image : imgPath + '/dw_faces/avatar1.jpeg'}" class="staff-avatar me-3 border border-2 border-primary" style="width: 54px; height: 54px; border-radius: 50%; object-fit: cover;" alt="avatar">
                                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-1.5" style="margin-right: 15px; margin-bottom: 2px;"></span>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.5px;">${staff.staff_name}</h4>
                                            <span class="badge bg-light text-secondary fw-semibold mt-1 px-2 py-1" style="border: 1px solid #e2e8f0; font-size: 11px;">${staff.staff_code}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-success-transparent fw-bold px-3 py-2 rounded-3" style="font-size: 12px; color: #059669 !important; background: #ecfdf5 !important;"><i class="fas fa-check-circle me-1"></i> Present: ${staff.present_count} days</span>
                                        <span class="badge bg-danger-transparent fw-bold px-3 py-2 rounded-3" style="font-size: 12px; color: #dc2626 !important; background: #fef2f2 !important;"><i class="fas fa-times-circle me-1"></i> Absent: ${staff.absent_count} days</span>
                                        <span class="badge bg-info-transparent fw-bold px-3 py-2 rounded-3" style="font-size: 12px; color: #2563eb !important; background: #eff6ff !important;"><i class="fas fa-clock me-1"></i> Total Hours: ${formatDuration(staff.total_minutes)}</span>
                                    </div>
                                </div>
                                
                                <h4 class="text-center fw-bold my-4 text-dark d-flex align-items-center justify-content-center gap-2" style="letter-spacing: -0.5px;">
                                    <i class="fas fa-calendar-alt text-primary"></i> ${res.month_name} ${res.year}
                                </h4>

                                <div class="calendar-grid">
                                    <div class="calendar-day-header">Sun</div>
                                    <div class="calendar-day-header">Mon</div>
                                    <div class="calendar-day-header">Tue</div>
                                    <div class="calendar-day-header">Wed</div>
                                    <div class="calendar-day-header">Thu</div>
                                    <div class="calendar-day-header">Fri</div>
                                    <div class="calendar-day-header">Sat</div>
                    `;

                    // Render leading empty spaces for start day offset
                    for (let i = 0; i < res.start_day_of_week; i++) {
                        html += `<div class="calendar-day future"></div>`;
                    }

                    // Render days
                    Object.values(staff.days).forEach(day => {
                        let statusClass = 'future';
                        let badgeHtml = '';
                        let timesHtml = '';
                        let extraData = '';

                        if (day.status === 'P') {
                            let isPartial = !day.time_out || day.time_out === '';
                            statusClass = isPartial ? 'partial cursor-pointer' : 'present cursor-pointer';
                            badgeHtml = isPartial ?
                                `<span class="day-status partial-status">In Only</span>` :
                                `<span class="day-status p-status">Present</span>`;

                            let faceImagesHtml = `
                                <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                    <img src="${day.face_image_in_url ? day.face_image_in_url : imgPath + '/dw_faces/avatar1.jpeg'}" class="rounded border previewable-image" style="width: 32px; height: 32px; object-fit: cover; cursor: pointer;" data-toggle="tooltip" title="In Capture" data-title="Time-In Photo">
                                    <img src="${day.face_image_out_url ? day.face_image_out_url : imgPath + '/dw_faces/avatar1.jpeg'}" class="rounded border previewable-image" style="width: 32px; height: 32px; object-fit: cover; cursor: pointer;" data-toggle="tooltip" title="Out Capture" data-title="Time-Out Photo">
                                </div>
                            `;

                            timesHtml = `
                                <div class="day-time"><i class="fas fa-sign-in-alt"></i> ${day.time_in || '--:--'}</div>
                                <div class="day-time"><i class="fas fa-sign-out-alt"></i> ${day.time_out || '--:--'}</div>
                                <div class="day-hours">${formatDuration(day.total_minutes)}</div>
                                ${faceImagesHtml}
                            `;

                            extraData = `
                                data-time-in="${day.time_in || ''}"
                                data-time-out="${day.time_out || ''}"
                                data-raw-time-in="${day.raw_time_in || ''}"
                                data-raw-time-out="${day.raw_time_out || ''}"
                                data-lunch-in="${day.lunch_in || ''}"
                                data-lunch-out="${day.lunch_out || ''}"
                                data-hours="${day.hours || 0}"
                                data-minutes="${day.total_minutes || 0}"
                                data-remark="${day.remark || ''}"
                                data-late-status="${day.late_status || ''}"
                                data-face-in="${day.face_image_in_url || ''}"
                                data-face-out="${day.face_image_out_url || ''}"
                                data-shift-type="${staff.shift_type || 'Office Shift'}"
                                data-shift-name="${staff.shift_name || 'General Shift'}"
                                data-shift-time-in="${staff.shift_time_in || '09:00 AM'}"
                                data-shift-time-out="${staff.shift_time_out || '06:00 PM'}"
                            `;
                        } else if (day.status === 'A') {
                            statusClass = 'absent cursor-pointer absent-calendar-cell';
                            badgeHtml = `<span class="day-status a-status">Absent</span>`;
                        }

                        html += `
                            <div class="calendar-day ${statusClass}" data-date="${day.date}" data-formatted-date="${day.formatted_date}" data-staff-id="${staff.dw_staff_id}" data-staff-name="${staff.staff_name}" ${extraData}>
                                <div class="day-number">${day.day}</div>
                                ${badgeHtml}
                                ${timesHtml}
                            </div>
                        `;
                    });

                    // Generate Log Table HTML
                    let logsHtml = `
                        <div class="card border mt-4">
                            <div class="card-header bg-light py-3">
                                <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <i class="fas fa-history text-primary"></i> Manual Attendance Change Logs (${res.month_name} ${res.year})
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                                        <thead class="bg-light text-secondary">
                                            <tr>
                                                <th style="padding: 12px 16px;">Staff / User</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Time In</th>
                                                <th>Time Out</th>
                                                <th>Logged By / Timestamp</th>
                                                <th>Remark / Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                    `;

                    let hasLogs = false;
                    let filteredLogs = res.logs || [];
                    if (singleStaffSelected && res.logs && res.logs.length > 0) {
                        filteredLogs = res.logs.filter(log => log.staff_code === staff.staff_code);
                    }
                    
                    if (filteredLogs.length > 0) {
                        hasLogs = true;
                        filteredLogs.forEach(log => {
                            let actionBadge = log.action === 'Manual Add' ?
                                '<span class="badge bg-success-transparent text-success fw-bold px-2 py-1 rounded" style="font-size: 10px;">Added</span>' :
                                '<span class="badge bg-info-transparent text-info fw-bold px-2 py-1 rounded" style="font-size: 10px;">Edited</span>';

                            logsHtml += `
                                <tr>
                                    <td style="padding: 12px 16px;">
                                        <div class="fw-bold text-dark">${log.staff_name}</div>
                                        <div class="text-muted small" style="font-size: 10px;">${log.staff_code}</div>
                                    </td>
                                    <td class="fw-bold text-dark">${log.date}</td>
                                    <td>${actionBadge}</td>
                                    <td class="text-dark fw-medium">${log.new_time_in} <span class="text-muted small d-block">Old: ${log.old_time_in}</span></td>
                                    <td class="text-dark fw-medium">${log.new_time_out} <span class="text-muted small d-block">Old: ${log.old_time_out}</span></td>
                                    <td>
                                        <div class="fw-semibold text-dark">${log.performed_by_name}</div>
                                        <div class="text-muted small" style="font-size: 10px;">${log.timestamp}</div>
                                    </td>
                                    <td><span class="text-secondary fw-semibold">${log.remark}</span></td>
                                </tr>
                            `;
                        });
                    }

                    if (!hasLogs) {
                        logsHtml +=
                            `<tr><td colspan="7" class="text-center py-5 text-muted fw-semibold">No manual attendance changes logged for this month.</td></tr>`;
                    }

                    logsHtml += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;

                    html += `
                                </div>
                            </div>
                        </div>
                        ${logsHtml}
                    `;
                    container.append(html);

                    // Show top Back button and wire its click
                    $('#globalBackBtn').removeClass('d-none');

                    // Click handler for top Back button
                    $('#globalBackBtn').off('click').on('click', function() {
                        $('#globalBackBtn').addClass('d-none');
                        $('#staffFilter').val('').trigger('change');
                        loadActiveTab();
                    });
                }
                // If "All" is selected, render a modern card listing with summaries and mini timeline dots
                else {
                    // Hide the top Back button when viewing all staff
                    $('#globalBackBtn').addClass('d-none');
                    let html = `<div class="row">`;
                    res.data.forEach(staff => {
                        let dotsHtml = '';
                        Object.values(staff.days).forEach(day => {
                            let dotClass = 'f-dot';
                            let dotText = '';
                            let tooltip = `Day ${day.day}`;
                            let extraAttrs = '';
                            if (day.status === 'P') {
                                let isPartial = !day.time_out || day.time_out === '';
                                dotClass = isPartial ? 'partial-dot' : 'p-dot';
                                dotText = 'P';
                                tooltip = isPartial ? `Day ${day.day}: Checked In Only` :
                                    `Day ${day.day}: Present (${formatDuration(day.total_minutes)})`;
                                extraAttrs = `
                                    data-time-in="${day.time_in || ''}"
                                    data-time-out="${day.time_out || ''}"
                                    data-raw-time-in="${day.raw_time_in || ''}"
                                    data-raw-time-out="${day.raw_time_out || ''}"
                                    data-lunch-in="${day.lunch_in || ''}"
                                    data-lunch-out="${day.lunch_out || ''}"
                                    data-hours="${day.hours || 0}"
                                    data-minutes="${day.total_minutes || 0}"
                                    data-remark="${day.remark || ''}"
                                    data-late-status="${day.late_status || ''}"
                                    data-face-in="${day.face_image_in_url || ''}"
                                    data-face-out="${day.face_image_out_url || ''}"
                                    data-shift-type="${staff.shift_type || 'Office Shift'}"
                                    data-shift-name="${staff.shift_name || 'General Shift'}"
                                    data-shift-time-in="${staff.shift_time_in || '09:00 AM'}"
                                    data-shift-time-out="${staff.shift_time_out || '06:00 PM'}"
                                `;
                            } else if (day.status === 'A') {
                                dotClass = 'a-dot';
                                dotText = 'A';
                                tooltip = `Day ${day.day}: Absent (Click to mark manually)`;
                            }
                            dotsHtml += `
                                <div class="d-flex flex-column align-items-center me-1 mb-2" style="width: 22px;">
                                    <span class="text-muted mb-1" style="font-size: 9px; font-weight: 600;">${day.day}</span>
                                    <span class="mini-dot ${dotClass}" data-date="${day.date}" data-formatted-date="${day.formatted_date}" data-staff-id="${staff.dw_staff_id}" data-staff-name="${staff.staff_name}" data-toggle="tooltip" title="${tooltip}" ${extraAttrs}>${dotText}</span>
                                </div>
                            `;
                        });

                        html += `
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <div style="
                                    background: #ffffff;
                                    border: 1px solid #e8ecf0;
                                    border-radius: 14px;
                                    padding: 20px;
                                    box-shadow: 0 1px 6px rgba(0,0,0,0.05);
                                    transition: all 0.25s ease;
                                    position: relative;
                                    overflow: hidden;
                                "
                                onmouseover="this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)'"
                                onmouseout="this.style.boxShadow='0 1px 6px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)'"
                                >
                                    <!-- Subtle top accent bar -->
                                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #3b82f6, #06b6d4);"></div>

                                    <!-- Header: Avatar + Name + Out Photo -->
                                    <div class="d-flex align-items-center justify-content-between mb-3 pt-1">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="position: relative;">
                                                <img src="${staff.today_face_in ? staff.today_face_in : imgPath + '/dw_faces/avatar1.jpeg'}"
                                                    class="previewable-image"
                                                    style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #d1fae5; cursor: pointer; box-shadow: 0 0 0 3px #ecfdf5;"
                                                    alt="In Capture"
                                                    data-toggle="tooltip" title="Today's Time-In Capture"
                                                    data-title="Today's Time-In Photo"
                                                    data-remark="${staff.today_remark || ''}">
                                                <span style="position: absolute; bottom: 1px; right: 1px; width: 10px; height: 10px; background: #10b981; border-radius: 50%; border: 2px solid white; display: block;"></span>
                                            </div>
                                            <div>
                                                <div style="font-size: 15px; font-weight: 700; color: #1e293b; line-height: 1.2;">${staff.staff_name}</div>
                                                <span style="font-size: 11px; color: #94a3b8; font-weight: 500; letter-spacing: 0.5px;">${staff.staff_code}</span>
                                            </div>
                                        </div>
                                        <img src="${staff.today_face_out ? staff.today_face_out : imgPath + '/dw_faces/avatar1.jpeg'}"
                                            class="previewable-image"
                                            style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #fee2e2; cursor: pointer; box-shadow: 0 0 0 3px #fef2f2; opacity: 0.9;"
                                            alt="Out Capture"
                                            data-toggle="tooltip" title="Today's Time-Out Capture"
                                            data-title="Today's Time-Out Photo"
                                            data-remark="${staff.today_remark || ''}">
                                    </div>
                                    <!-- Stats Row -->
                                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.2fr; gap: 6px; margin-bottom: 14px;">
                                        <div style="background: #f0fdf4; border-radius: 10px; padding: 10px 6px; text-align: center;">
                                            <div style="font-size: 18px; font-weight: 800; color: #059669; line-height: 1;">${staff.present_count}</div>
                                            <div style="font-size: 10px; color: #6ee7b7; font-weight: 600; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px;">Present</div>
                                        </div>
                                        <div style="background: #fef2f2; border-radius: 10px; padding: 10px 6px; text-align: center;">
                                            <div style="font-size: 18px; font-weight: 800; color: #dc2626; line-height: 1;">${staff.absent_count}</div>
                                            <div style="font-size: 10px; color: #fca5a5; font-weight: 600; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px;">Absent</div>
                                        </div>
                                        <div style="background: #eff6ff; border-radius: 10px; padding: 10px 4px; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                            <div style="font-size: 13px; font-weight: 800; color: #2563eb; line-height: 1.2; white-space: nowrap;">${formatDuration(staff.total_minutes)}</div>
                                            <div style="font-size: 10px; color: #93c5fd; font-weight: 600; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px;">Hours</div>
                                        </div>
                                    </div>

                                    <!-- Monthly Timeline -->
                                    <div style="border-top: 1px solid #f1f5f9; padding-top: 12px; margin-bottom: 14px;">
                                        <div style="font-size: 10px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px;">
                                            Monthly Attendance Timeline (${res.month_name} ${res.year})
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center">
                                            ${dotsHtml}
                                        </div>
                                    </div>

                                    <!-- View Calendar Button -->
                                    <button class="btn-view-calendar view-single-staff-calendar"
                                        data-id="${staff.dw_staff_id}">
                                        <i class="fas fa-calendar-alt me-1"></i> View Full Calendar
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                    container.append(html);

                    // Activate tooltips
                    $('[data-toggle="tooltip"]').tooltip();

                    // Click handler for full calendar button
                    $('.view-single-staff-calendar').on('click', function() {
                        const id = $(this).data('id');
                        $('#staffFilter').val(id).trigger('change');
                        loadActiveTab();
                    });
                }
            }

            function renderSalary(data) {
                const body = $('#salaryTableBody');
                body.empty();

                if (data.length === 0) {
                    body.append('<tr><td colspan="11" class="text-center py-4">No data found.</td></tr>');
                    return;
                }

                data.forEach(row => {
                    let statusBadge = row.status === 'Paid' ?
                        '<span class="badge bg-success-transparent">Paid</span>' :
                        '<span class="badge bg-warning-transparent">Pending</span>';

                    let actionBtn = '';
                    if (row.action_pay) {
                        actionBtn =
                            `<button class="btn btn-success btn-sm pay-now-btn" data-id="${row.dw_staff_id}">Pay Now</button>`;
                    } else if (row.status === 'Paid') {
                        actionBtn =
                            `<button class="btn btn-outline-info btn-sm view-pdf-btn" data-id="${row.dw_staff_id}"><i class="fas fa-file-pdf"></i> View PDF</button>`;
                    } else {
                        actionBtn =
                            `<button class="btn btn-success btn-sm pay-now-btn" disabled data-id="${row.dw_staff_id}">Pay Now</button>`;
                    }
                    actionBtn +=
                        `<button class="btn btn-primary btn-sm view-history-btn" data-id="${row.dw_staff_id}"><i class="fas fa-eye"></i> View</button>`;

                    body.append(`
                        <tr>
                            <td><strong>${row.staff_name}</strong></td>
                            <td>${row.present}</td>
                            <td>${row.absent}</td>
                            <td>${formatDuration(row.working_minutes)}</td>
                            <td>₹${row.per_hour_rate}</td>
                            <td>₹${row.net_salary}</td>
                            <td>₹${row.employee_pf}</td>
                            <td>₹${row.gross_salary}</td>
                            <td>₹${row.ctc}</td>
                            <td>${statusBadge}</td>
                            <td><div class="d-flex align-items-center gap-1" style="white-space: nowrap;">${actionBtn}</div></td>
                        </tr>
                    `);
                });

                let activeStaffId = null;
                let activeMonth = null;
                let activeYear = null;
                let activePaymentType = 'full_month';
                let fromDatePicker = null;
                let toDatePicker = null;

                function formatShortDate(dateStr) {
                    if (!dateStr) return '-';
                    let parts = dateStr.split('-');
                    if (parts.length !== 3) return dateStr;
                    if (parts[0].length === 4) {
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                    return dateStr;
                }

                function initModalDatePickers(year, month) {
                    if (fromDatePicker) fromDatePicker.destroy();
                    if (toDatePicker) toDatePicker.destroy();

                    let firstDay = new Date(year, month - 1, 1);
                    let lastDay = new Date(year, month, 0);

                    fromDatePicker = flatpickr("#payFromDate", {
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "d-m-Y",
                        minDate: firstDay,
                        maxDate: lastDay,
                        disableMobile: true,
                        onChange: function(selectedDates, dateStr) {
                            if (toDatePicker) {
                                toDatePicker.set('minDate', dateStr);
                            }
                            loadSalaryDetails();
                        }
                    });

                    toDatePicker = flatpickr("#payToDate", {
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "d-m-Y",
                        minDate: firstDay,
                        maxDate: lastDay,
                        disableMobile: true,
                        onChange: function() {
                            loadSalaryDetails();
                        }
                    });
                }

                function loadSalaryDetails() {
                    if (!activeStaffId) return;

                    let fromDate = $('#payFromDate').val();
                    let toDate = $('#payToDate').val();

                    if (activePaymentType === 'partial' && (!fromDate || !toDate)) {
                        return;
                    }

                    $('#confirmPayBtn').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('dailywages.salary.details') }}",
                        type: "GET",
                        data: {
                            staff_id: activeStaffId,
                            month: activeMonth,
                            year: activeYear,
                            payment_type: activePaymentType,
                            from_date: fromDate,
                            to_date: toDate
                        },
                        success: function(res) {
                            if (res.status) {
                                $('#payModalStaffName').text(res.staff_name);
                                $('#payModalStaffCode').text(res.staff_code);

                                $('#summaryPaymentType').text(activePaymentType === 'full_month' ?
                                    'Full Month' : 'Partial');

                                if (activePaymentType === 'full_month') {
                                    $('#summaryPeriodLabel').text('Month');
                                    const monthNames = ["January", "February", "March", "April", "May",
                                        "June", "July", "August", "September", "October",
                                        "November", "December"
                                    ];
                                    $('#summaryPeriodVal').text(monthNames[activeMonth - 1] + ' ' +
                                        activeYear);

                                    $('#summaryAlreadyPaidCol').show();
                                    $('#summaryRemainingCol').show();
                                    $('#summaryPresentDatesCol').hide();

                                    $('#summaryPresent').text(parseInt(res.present) + parseInt(res
                                        .already_paid_days));
                                    $('#summaryAlreadyPaid').text(res.already_paid_days);
                                    $('#summaryRemaining').text(res.present);
                                } else {
                                    $('#summaryPeriodLabel').text('Date Range');
                                    $('#summaryPeriodVal').text(formatShortDate(fromDate) + ' ➔ ' +
                                        formatShortDate(toDate));

                                    $('#summaryAlreadyPaidCol').hide();
                                    $('#summaryRemainingCol').hide();

                                    if (res.paid_dates_formatted && res.paid_dates_formatted.length >
                                        0) {
                                        $('#summaryPresentDates').text(res.paid_dates_formatted.join(
                                            ', '));
                                        $('#summaryPresentDatesCol').show();
                                    } else {
                                        $('#summaryPresentDates').text('None');
                                        $('#summaryPresentDatesCol').show();
                                    }

                                    $('#summaryPresent').text(res.present);
                                }

                                $('#summaryDuration').text(formatDuration(res.total_working_minutes));
                                $('#payModalRate').text('₹' + res.per_hour_rate + '/hr');

                                $('#payModalNet').text('₹' + res.net_salary);
                                $('#payModalPf').text('₹' + res.employee_pf);
                                $('#payModalGross').text('₹' + res.gross_salary);
                                $('#payModalCtc').text('₹' + res.ctc);

                                if (res.is_pf_applicable == 1) {
                                    $('#payModalUanNo').text(res.uan_no || '-');
                                    $('#payModalPfNo').text(res.pf_no || '-');
                                    $('#payModalEsiNo').text(res.esi_no || '-');
                                    $('#payModalPfDetailsSection').show();
                                } else {
                                    $('#payModalPfDetailsSection').hide();
                                }

                                // Render History
                                let historyHtml = '';
                                if (res.history && res.history.length > 0) {
                                    res.history.forEach(item => {
                                        historyHtml += `
                                            <div class="p-2 mb-2 border rounded" style="background: #f8fafc; font-size: 11px;">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <strong>${item.from_date} &rarr; ${item.to_date}</strong>
                                                    <span class="text-success fw-bold">₹${item.amount}</span>
                                                </div>
                                                <div class="d-flex justify-content-between text-muted mb-1">
                                                    <span>Days: ${item.present_days} | Hours: ${item.working_hours}</span>
                                                    <span>Date: ${item.payment_date}</span>
                                                </div>
                                                <div class="text-end">
                                                    <a href="{{ route('dailywages.salary.download_pdf') }}?salary_id=${item.id}" target="_blank" class="btn btn-link btn-xs p-0 text-decoration-none text-primary">
                                                        <i class="fas fa-download me-1"></i> Download PDF
                                                    </a>
                                                </div>
                                            </div>
                                        `;
                                    });
                                } else {
                                    historyHtml =
                                        '<div class="text-center py-3 text-muted small">No previous payments found.</div>';
                                }
                                $('#alreadyPaidHistoryContent').html(historyHtml);

                                if (res.present == 0) {
                                    $('#confirmPayBtn').prop('disabled', true);
                                    if (activePaymentType === 'partial') {
                                        iziToast.warning({
                                            title: 'Validation',
                                            message: 'No unpaid attendance records found in the selected range.',
                                            position: 'topRight'
                                        });
                                    }
                                } else {
                                    $('#confirmPayBtn').prop('disabled', false);
                                }

                                $('#confirmPayBtn').data('id', activeStaffId).data('month', activeMonth)
                                    .data('year', activeYear);
                            } else {
                                iziToast.error({
                                    title: 'Error',
                                    message: res.message || 'Unable to fetch details.',
                                    position: 'topRight'
                                });
                            }
                        },
                        error: function(xhr) {
                            iziToast.error({
                                title: 'Error',
                                message: xhr.responseJSON?.message ||
                                    'Failed to fetch details.',
                                position: 'topRight'
                            });
                        }
                    });
                }

                // Attach click events to pay now and view pdf
                $('.pay-now-btn').off('click').on('click', function() {
                    activeStaffId = $(this).data('id');
                    activeMonth = $('#monthFilter').val();
                    activeYear = $('#yearFilter').val();

                    // Reset to Full Month mode
                    activePaymentType = 'full_month';
                    $('#payTypeFullBtn').addClass('active btn-primary').removeClass('btn-outline-primary');
                    $('#payTypePartialBtn').removeClass('active btn-primary').addClass(
                        'btn-outline-primary');
                    $('#partialDateRangeCard').hide();
                    $('#payFromDate, #payToDate').val('');

                    initModalDatePickers(activeYear, activeMonth);
                    loadSalaryDetails();
                    $('#dailyWagesPayModal').modal('show');
                });

                // Toggle payment type selections
                $('#payTypeFullBtn').off('click').on('click', function() {
                    $('#payTypeFullBtn').addClass('active btn-primary').removeClass('btn-outline-primary');
                    $('#payTypePartialBtn').removeClass('active btn-primary').addClass(
                        'btn-outline-primary');
                    $('#partialDateRangeCard').slideUp();
                    activePaymentType = 'full_month';
                    loadSalaryDetails();
                });

                $('#payTypePartialBtn').off('click').on('click', function() {
                    $('#payTypePartialBtn').addClass('active btn-primary').removeClass(
                        'btn-outline-primary');
                    $('#payTypeFullBtn').removeClass('active btn-primary').addClass('btn-outline-primary');
                    $('#partialDateRangeCard').slideDown();
                    activePaymentType = 'partial';
                    $('#payFromDate, #payToDate').val('');
                    if (fromDatePicker) fromDatePicker.clear();
                    if (toDatePicker) toDatePicker.clear();
                    $('#confirmPayBtn').prop('disabled', true);
                });

                $('#confirmPayBtn').off('click').on('click', function() {
                    const id = $(this).data('id');
                    const month = $(this).data('month');
                    const year = $(this).data('year');
                    const btn = $(this);

                    btn.prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...'
                    );

                    $.ajax({
                        url: "{{ route('dailywages.salary.pay') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            staff_id: id,
                            month: month,
                            year: year,
                            payment_type: activePaymentType,
                            from_date: $('#payFromDate').val(),
                            to_date: $('#payToDate').val()
                        },
                        success: function(res) {
                            btn.prop('disabled', false).html(
                                '<i class="fas fa-check-circle me-1"></i> Pay Now');
                            $('#dailyWagesPayModal').modal('hide');
                            if (res.status) {
                                iziToast.success({
                                    title: 'Success',
                                    message: res.message,
                                    position: 'topRight'
                                });
                                loadActiveTab(); // Refresh salary table
                            } else {
                                iziToast.error({
                                    title: 'Error',
                                    message: res.message ||
                                        'Payment processing failed.',
                                    position: 'topRight'
                                });
                            }
                        },
                        error: function() {
                            btn.prop('disabled', false).html(
                                '<i class="fas fa-check-circle me-1"></i> Pay Now');
                            iziToast.error({
                                title: 'Error',
                                message: 'Something went wrong.',
                                position: 'topRight'
                            });
                        }
                    });
                });

                $('.view-pdf-btn').off('click').on('click', function() {
                    const id = $(this).data('id');
                    const month = $('#monthFilter').val();
                    const year = $('#yearFilter').val();
                    window.open(
                        `{{ route('dailywages.salary.download_pdf') }}?staff_id=${id}&month=${month}&year=${year}`,
                        '_blank');
                });

                // View history click handler
                $('.view-history-btn').off('click').on('click', function() {
                    const id = $(this).data('id');
                    const month = $('#monthFilter').val();
                    const year = $('#yearFilter').val();

                    $.ajax({
                        url: "{{ route('dailywages.salary.details') }}",
                        type: "GET",
                        data: {
                            staff_id: id,
                            month: month,
                            year: year,
                            payment_type: 'full_month'
                        },
                        success: function(res) {
                            if (res.status) {
                                $('#viewModalStaffName').text(res.staff_name);
                                $('#viewModalStaffCode').text(res.staff_code);
                                $('#viewModalRate').text('₹' + res.per_hour_rate + '/hr');
                                $('#viewModalUanNo').text(res.uan_no || '-');
                                $('#viewModalPfNo').text(res.pf_no || '-');
                                $('#viewModalEsiNo').text(res.esi_no || '-');

                                // Render History
                                let historyHtml = '';
                                if (res.history && res.history.length > 0) {
                                    res.history.forEach(item => {
                                        historyHtml += `
                                            <div class="p-3 mb-2 border rounded" style="background: #f8fafc; font-size: 12px;">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <strong>Period: ${item.from_date} &rarr; ${item.to_date}</strong>
                                                    <span class="text-success fw-bold">₹${item.amount}</span>
                                                </div>
                                                <div class="d-flex justify-content-between text-muted mb-2" style="font-size: 11px;">
                                                    <span>Present Days: ${item.present_days} | Hours: ${item.working_hours}</span>
                                                    <span>Paid On: ${item.payment_date}</span>
                                                </div>
                                                <div class="text-end">
                                                    <a href="{{ route('dailywages.salary.download_pdf') }}?salary_id=${item.id}" target="_blank" class="btn btn-outline-primary btn-sm px-2 py-1" style="font-size: 10px; border-radius: 6px;">
                                                        <i class="fas fa-download me-1"></i> Download PDF
                                                    </a>
                                                </div>
                                            </div>
                                        `;
                                    });
                                } else {
                                    historyHtml =
                                        '<div class="text-center py-4 text-muted small">No previous payments found.</div>';
                                }
                                $('#viewHistoryContent').html(historyHtml);
                                $('#dailyWagesViewModal').modal('show');
                            } else {
                                iziToast.error({
                                    title: 'Error',
                                    message: res.message || 'Unable to fetch history.',
                                    position: 'topRight'
                                });
                            }
                        },
                        error: function(xhr) {
                            iziToast.error({
                                title: 'Error',
                                message: 'Failed to fetch history details.',
                                position: 'topRight'
                            });
                        }
                    });
                });
            }
            let timeoutManuallyChanged = false;
            let programmaticChange = false;

            function applyTimeOutRestrictions(inDate) {
                if (!inDate) return;
                const minDate = new Date(inDate.getFullYear(), inDate.getMonth(), inDate.getDate(), 0, 0, 0, 0);
                const maxDate = new Date(inDate.getFullYear(), inDate.getMonth(), inDate.getDate() + 1, 23, 59, 59,
                    999);

                fpOut.set('minDate', minDate);
                fpOut.set('maxDate', maxDate);

                // If currently selected Time Out is outside the new range, clear it
                const outDateObj = fpOut.selectedDates[0];
                if (outDateObj) {
                    if (outDateObj < minDate || outDateObj > maxDate) {
                        fpOut.clear();
                        timeoutManuallyChanged = false;
                    }
                }
            }

            // Initialize flatpickr on Time In and Time Out (Date and Time)
            let fpIn = flatpickr("#manualTimeIn", {
                enableTime: true,
                dateFormat: "Y-m-d h:i K",
                time_24hr: false,
                allowInput: true,
                disableMobile: true,
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        const inDate = selectedDates[0];
                        applyTimeOutRestrictions(inDate);

                        if (programmaticChange) return;
                        if (!timeoutManuallyChanged) {
                            const outDateObj = fpOut.selectedDates[0];

                            programmaticChange = true;
                            if (outDateObj) {
                                // Sync date part, keep time part
                                const newOutDate = new Date(outDateObj.getTime());
                                newOutDate.setFullYear(inDate.getFullYear(), inDate.getMonth(), inDate
                                    .getDate());
                                fpOut.setDate(newOutDate, true);
                            } else {
                                // If Time Out is empty, set it to the same date with default time (e.g. 06:00 PM)
                                const defaultOut = new Date(inDate.getTime());
                                defaultOut.setHours(18, 0, 0, 0); // 06:00 PM
                                fpOut.setDate(defaultOut, true);
                            }
                            programmaticChange = false;
                        }
                    }
                }
            });

            let fpOut = flatpickr("#manualTimeOut", {
                enableTime: true,
                dateFormat: "Y-m-d h:i K",
                time_24hr: false,
                allowInput: true,
                disableMobile: true,
                onChange: function(selectedDates, dateStr, instance) {
                    if (programmaticChange) return;
                    timeoutManuallyChanged = true;
                }
            });

            // Delegated click handler to open manual attendance modal on clicking any absent dot/cell
            $(document).on('click', '.mini-dot.a-dot, .absent-calendar-cell', function() {
                if (!canManualEntry) {
                    iziToast.warning({
                        title: 'Unauthorized',
                        message: 'You do not have permission to add manual attendance.',
                        position: 'topRight'
                    });
                    return;
                }
                const date = $(this).data('date');
                const formattedDate = $(this).data('formatted-date');
                const staffId = $(this).data('staff-id');
                const staffName = $(this).data('staff-name');

                // Populate modal fields
                $('#manualStaffId').val(staffId);
                $('#manualDate').val(date);
                $('#manualStaffNameDisplay').text(staffName);
                $('#manualDateDisplay').text(formattedDate);

                // Reset synchronization flags
                timeoutManuallyChanged = false;
                programmaticChange = false;

                // Default both dates to the attendance date selected
                fpIn.setDate(date + ' 09:00 AM', true);
                fpOut.setDate(date + ' 06:00 PM', true);

                $('#manualRemark').val('');
                wasEditingFromDetails = false;

                // Open modal
                $('#manualAttendanceModal').modal('show');
            });

            // Delegated click handler to open details modal on clicking any present dot/cell
            $(document).on('click',
                '.mini-dot.p-dot, .mini-dot.partial-dot, .calendar-day.present, .calendar-day.partial',
                function(e) {
                    e.stopPropagation(); // Avoid triggering parent div click event

                    const date = $(this).data('date');
                    const formattedDate = $(this).data('formatted-date');
                    const staffId = $(this).data('staff-id');
                    const staffName = $(this).data('staff-name');
                    const timeIn = $(this).data('time-in');
                    const timeOut = $(this).data('time-out');
                    const rawTimeIn = $(this).data('raw-time-in');
                    const rawTimeOut = $(this).data('raw-time-out');
                    const lunchIn = $(this).data('lunch-in');
                    const lunchOut = $(this).data('lunch-out');
                    const hours = $(this).data('hours');
                    const minutes = $(this).data('minutes');
                    const remark = $(this).data('remark');
                    const lateStatus = $(this).data('late-status');
                    const faceIn = $(this).data('face-in');
                    const faceOut = $(this).data('face-out');
                    const shiftType = $(this).data('shift-type');
                    const shiftName = $(this).data('shift-name');
                    const shiftTimeIn = $(this).data('shift-time-in');
                    const shiftTimeOut = $(this).data('shift-time-out');

                    // Populate details modal fields
                    $('#detailStaffName').text(staffName);
                    $('#detailDate').text(formattedDate);
                    $('#detailShiftType').text(shiftType);
                    $('#detailShiftTiming').text(`${shiftName}: ${shiftTimeIn} - ${shiftTimeOut}`);
                    $('#detailWorkedHours').text(formatDuration(minutes));
                    $('#detailTimeIn').text(timeIn || '--:--');
                    $('#detailTimeOut').text(timeOut || '--:--');
                    $('#detailLunchIn').text(lunchIn || '-');
                    $('#detailLunchOut').text(lunchOut || '-');
                    $('#detailRemark').text(cleanRemark(remark) || 'None');
                    $('#detailLateStatus').text(lateStatus || '-');

                    // Set images or fallback to default
                    $('#detailInImage').attr('src', faceIn ? faceIn : '/assets/uploads/dw_faces/avatar1.jpeg')
                        .data('remark', remark)
                        .data('staff-name', staffName)
                        .data('formatted-date', formattedDate);
                    $('#detailOutImage').attr('src', faceOut ? faceOut :
                            '/assets/uploads/dw_faces/avatar1.jpeg')
                        .data('remark', remark)
                        .data('staff-name', staffName)
                        .data('formatted-date', formattedDate);

                    // Store current info on the edit button in details modal
                    const editBtn = $('#detailEditBtn');
                    if (editBtn.length) {
                        editBtn.data({
                            'staff-id': staffId,
                            'staff-name': staffName,
                            'date': date,
                            'formatted-date': formattedDate,
                            'time-in': timeIn,
                            'time-out': timeOut,
                            'raw-time-in': rawTimeIn,
                            'raw-time-out': rawTimeOut,
                            'remark': cleanRemark(remark)
                        });

                        if (canManualEntry) {
                            editBtn.show();
                        } else {
                            editBtn.hide();
                        }
                    }

                    // Show details modal
                    $('#attendanceDetailsModal').modal('show');
                });

            // Edit button click inside details modal
            $('#detailEditBtn').on('click', function() {
                const staffId = $(this).data('staff-id');
                const staffName = $(this).data('staff-name');
                const date = $(this).data('date');
                const formattedDate = $(this).data('formatted-date');
                const rawTimeIn = $(this).data('raw-time-in');
                const rawTimeOut = $(this).data('raw-time-out');
                const remark = $(this).data('remark');

                // Close details modal
                $('#attendanceDetailsModal').modal('hide');

                // Pre-populate Edit modal
                $('#manualStaffId').val(staffId);
                $('#manualDate').val(date);
                $('#manualStaffNameDisplay').text(staffName);
                $('#manualDateDisplay').text(formattedDate);

                // Reset synchronization flags
                timeoutManuallyChanged = false;
                programmaticChange = false;

                // Set actual selected time values in flatpickr inputs
                if (rawTimeIn) {
                    fpIn.setDate(rawTimeIn, true);
                } else {
                    fpIn.setDate(date + ' 09:00 AM', true);
                }

                if (rawTimeOut) {
                    fpOut.setDate(rawTimeOut, true);
                } else {
                    fpOut.setDate(date + ' 06:00 PM', true);
                }

                $('#manualRemark').val(remark !== 'None' ? remark : '');
                wasEditingFromDetails = true;

                // Open Edit modal
                $('#manualAttendanceModal').modal('show');
            });

            // Form submission logic
            $('#manualAttendanceForm').on('submit', function(e) {
                e.preventDefault();

                const timeIn = $('#manualTimeIn').val();
                const timeOut = $('#manualTimeOut').val();
                if (!timeIn) {
                    iziToast.warning({
                        title: 'Warning',
                        message: 'Please select Time In.',
                        position: 'topRight'
                    });
                    return;
                }
                if (!timeOut) {
                    iziToast.warning({
                        title: 'Warning',
                        message: 'Please select Time Out.',
                        position: 'topRight'
                    });
                    return;
                }

                $('#saveManualBtn').prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ route('dailywages.attendance.save_manual') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.status) {
                            iziToast.success({
                                title: 'Success',
                                message: res.message,
                                position: 'topRight'
                            });
                            $('#manualAttendanceModal').modal('hide');

                            // Reload active view, and reopen details modal if editing from details
                            loadActiveTab(function() {
                                if (wasEditingFromDetails) {
                                    const staffId = $('#manualStaffId').val();
                                    const date = $('#manualDate').val();
                                    let targetEl = $(
                                        `.mini-dot[data-staff-id="${staffId}"][data-date="${date}"], .calendar-day[data-staff-id="${staffId}"][data-date="${date}"]`
                                    ).first();
                                    if (targetEl.length) {
                                        targetEl.trigger('click');
                                    }
                                }
                            });
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: res.message,
                                position: 'topRight'
                            });
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr
                            .responseJSON.message : 'Failed to save attendance.';
                        iziToast.error({
                            title: 'Error',
                            message: msg,
                            position: 'topRight'
                        });
                    },
                    complete: function() {
                        $('#saveManualBtn').prop('disabled', false).text('Save Attendance');
                    }
                });
            });

            // Image Preview Modal Click Handler
            $(document).on('click', '.previewable-image', function(e) {
                e.stopPropagation(); // Prevent modal overlay clashes
                const src = $(this).attr('src');
                const title = $(this).data('title') || 'Attendance Photo';

                // Determine photo type badge color
                const badge = $('#previewPhotoBadge');
                badge.text(title);
                if (title.includes('In')) {
                    badge.removeClass('bg-danger-transparent text-danger').addClass(
                        'bg-success-transparent text-success');
                } else {
                    badge.removeClass('bg-success-transparent text-success').addClass(
                        'bg-danger-transparent text-danger');
                }

                // Dynamic Info extraction
                const staffCard = $(this).closest('.staff-card');
                const staffName = $(this).data('staff-name') || staffCard.find('h5').text() || $(
                    '#detailStaffName').text() || 'Daily Wages Staff';
                const staffCode = $(this).data('staff-code') || staffCard.find('.text-muted.small').first()
                    .text() || '';
                const formattedDate = $(this).data('formatted-date') || $('#detailDate').text() || 'Today';

                $('#previewInfoName').text(staffCode ? `${staffName} (${staffCode})` : staffName);
                $('#previewInfoDate').text(formattedDate);

                const remark = $(this).data('remark') || $(this).closest('[data-remark]').data('remark') ||
                    '';
                const isManual = remark && remark.includes('Manual by');

                if (src && !src.includes('avatar1.jpeg')) {
                    $('#previewImagePlaceholder').hide();
                    $('#previewImageSrc').attr('src', src).show();
                    $('#previewImageInfo').show();
                } else {
                    $('#previewImageSrc').hide();
                    if (isManual) {
                        const parts = remark.split(' - ');
                        const creatorDetails = parts.length > 0 ? parts[0] : 'Admin';
                        $('#previewPlaceholderText').text(
                            `This attendance was marked manually (${creatorDetails}).`);
                    } else {
                        $('#previewPlaceholderText').text("This person has not marked attendance yet.");
                    }
                    $('#previewImagePlaceholder').show();
                    $('#previewImageInfo').hide();
                }

                $('#imagePreviewModal').modal('show');
            });
        });
    </script>

    <!-- Manual Attendance Modal -->
    <div class="modal fade" id="manualAttendanceModal" tabindex="-1" aria-labelledby="manualAttendanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="manualAttendanceModalLabel">Save Attendance Manually
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="manualAttendanceForm">
                    @csrf
                    <div class="modal-body px-4 py-4" style="background: #f8fafc;">
                        <input type="hidden" id="manualStaffId" name="staff_id">
                        <input type="hidden" id="manualDate" name="date">

                        <!-- Staff Info Card -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <div class="card-body p-3">
                                <h6 class="text-muted fw-bold mb-3 text-uppercase"
                                    style="font-size: 11px; letter-spacing: 1px;">Staff Details</h6>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-secondary small">Staff Name:</span>
                                    <span id="manualStaffNameDisplay" class="fw-bold text-primary small"></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-secondary small">Date:</span>
                                    <span id="manualDateDisplay" class="fw-semibold text-dark small"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Inputs Card -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <div class="card-body p-3">
                                <h6 class="text-muted fw-bold mb-3 text-uppercase"
                                    style="font-size: 11px; letter-spacing: 1px;">Attendance Entry</h6>

                                <div class="mb-3">
                                    <label for="manualTimeIn"
                                        class="form-label mb-1 text-muted small fw-semibold">Time In</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"
                                            style="border-radius: 8px 0 0 8px;"><i
                                                class="fas fa-sign-in-alt"></i></span>
                                        <input type="text"
                                            class="form-control border-start-0 ps-0 text-dark fw-medium small"
                                            id="manualTimeIn" name="time_in" placeholder="Select Time In" required
                                            style="border-radius: 0 8px 8px 0;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="manualTimeOut"
                                        class="form-label mb-1 text-muted small fw-semibold">Time Out</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"
                                            style="border-radius: 8px 0 0 8px;"><i
                                                class="fas fa-sign-out-alt"></i></span>
                                        <input type="text"
                                            class="form-control border-start-0 ps-0 text-dark fw-medium small"
                                            id="manualTimeOut" name="time_out" placeholder="Select Time Out"
                                            style="border-radius: 0 8px 8px 0;">
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label for="manualRemark"
                                        class="form-label mb-1 text-muted small fw-semibold">Remark (Optional)</label>
                                    <textarea class="form-control text-dark small fw-medium" id="manualRemark" name="remark" rows="3"
                                        placeholder="Enter remark..." style="border-radius: 8px;"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-end gap-2 border-top">
                        <button type="button" class="btn btn-secondary border-0" data-bs-dismiss="modal"
                            style="background: #f1f5f9; color: #475569; border-radius: 8px;">Close</button>
                        <button type="submit" class="btn btn-primary border-0" id="saveManualBtn"
                            style="background: linear-gradient(135deg, #1d4ed8, #3b82f6) !important; border-radius: 8px;">Save
                            Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Attendance Details Modal -->
    <div class="modal fade" id="attendanceDetailsModal" tabindex="-1" aria-labelledby="attendanceDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center"
                        id="attendanceDetailsModalLabel">
                        Details for <span id="detailStaffName" class="mx-1 text-primary"></span> (Staff) <span
                            class="mx-2 text-muted">·</span> <span id="detailDate"
                            class="text-secondary small"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4" style="background: #f8fafc;">

                    <!-- Shift Information -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-body p-3">
                            <h6 class="text-muted fw-bold mb-3 text-uppercase"
                                style="font-size: 11px; letter-spacing: 1px;">Shift Information</h6>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary small">Shift Type:</span>
                                <span id="detailShiftType" class="fw-semibold text-dark small">Office Shift</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-secondary small">Shift Timing:</span>
                                <span id="detailShiftTiming" class="fw-semibold text-dark small">General: 9:30 AM -
                                    6:30 PM</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Worked Time Banner -->
                    <div class="card border-0 bg-primary text-white shadow-sm mb-4"
                        style="border-radius: 12px; background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;">
                        <div class="card-body text-center py-3">
                            <span class="text-white-50 text-uppercase fw-bold"
                                style="font-size: 10px; letter-spacing: 1px;">Total Worked Time</span>
                            <h2 id="detailWorkedHours" class="fw-bold mb-0 mt-1"
                                style="font-size: 2.2rem; letter-spacing: -0.5px;">9h 0m</h2>
                        </div>
                    </div>

                    <!-- Side-by-Side In/Out Cards -->
                    <div class="row g-4 mb-4">
                        <!-- Time In Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                                <div class="bg-light p-3 text-center border-bottom">
                                    <div class="text-muted small fw-bold mb-2">Time In Capture</div>
                                    <img id="detailInImage" src="/assets/uploads/dw_faces/avatar1.jpeg"
                                        class="rounded shadow-sm"
                                        style="width: 100px; height: 100px; object-fit: cover;" alt="In Capture">
                                </div>
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Time In:</span>
                                        <span id="detailTimeIn" class="fw-semibold text-dark small">09:30 AM</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Lunch Out:</span>
                                        <span id="detailLunchOut" class="fw-semibold text-dark small">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Status:</span>
                                        <span id="detailStatus" class="badge bg-success-transparent">Present</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Time Out Card -->
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                                <div class="bg-light p-3 text-center border-bottom">
                                    <div class="text-muted small fw-bold mb-2">Time Out Capture</div>
                                    <img id="detailOutImage" src="/assets/uploads/dw_faces/avatar1.jpeg"
                                        class="rounded shadow-sm"
                                        style="width: 100px; height: 100px; object-fit: cover;" alt="Out Capture">
                                </div>
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Time Out:</span>
                                        <span id="detailTimeOut" class="fw-semibold text-dark small">06:30 PM</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Lunch In:</span>
                                        <span id="detailLunchIn" class="fw-semibold text-dark small">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Late Status:</span>
                                        <span id="detailLateStatus" class="fw-semibold text-success small">On
                                            Time</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Remark -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body p-3">
                            <span class="text-muted small fw-bold d-block mb-1">Remark</span>
                            <p id="detailRemark" class="mb-0 text-dark small fw-medium">None</p>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-end gap-2 border-top">
                    <button type="button" class="btn btn-secondary border-0" data-bs-dismiss="modal"
                        style="background: #f1f5f9; color: #475569; border-radius: 8px;">Close</button>
                    @if ($canManualEntry)
                        <button type="button" class="btn btn-primary d-flex align-items-center gap-1 border-0"
                            id="detailEditBtn" style="background: #ef4444; border-radius: 8px;">
                            <i class="fas fa-edit"></i> Edit Attendance
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Image Preview Modal -->
    <div class="modal fade premium-preview-modal" id="imagePreviewModal" tabindex="-1"
        aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div
                    class="modal-header border-0 px-4 pt-4 pb-0 d-flex justify-content-between align-items-center bg-transparent">
                    <div class="d-flex flex-column">
                        <h5 class="modal-title fw-bold text-dark mb-1" id="imagePreviewModalLabel">Attendance Photo
                        </h5>
                        <span id="previewPhotoBadge" class="badge rounded-pill align-self-start fw-bold"
                            style="font-size: 10px;">Time In Photo</span>
                    </div>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="preview-img-container p-2">
                        <img id="previewImageSrc" src="" class="img-fluid rounded shadow"
                            style="display: none;" alt="Preview">
                        <div id="previewImagePlaceholder" class="text-center w-100 py-5" style="display: none;">
                            <i class="fas fa-user-clock fa-3x mb-3 text-warning"></i>
                            <h5 id="previewPlaceholderText" class="mb-0 text-white fw-semibold">This person has not
                                marked attendance yet.</h5>
                        </div>
                    </div>

                    <!-- Dynamic Info Section -->
                    <div id="previewImageInfo" class="mt-3 p-3 bg-light rounded d-flex flex-column gap-2"
                        style="font-size: 12px; border-left: 4px solid #3b82f6;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted fw-semibold">Employee:</span>
                            <span id="previewInfoName" class="text-dark fw-bold">-</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted fw-semibold">Date:</span>
                            <span id="previewInfoDate" class="text-dark fw-bold">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Wages Pay Now Modal -->
    <div class="modal fade" id="dailyWagesPayModal" tabindex="-1" aria-labelledby="dailyWagesPayModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg"
                style="border-radius: 20px; overflow: hidden; box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;">
                <!-- Header with premium gradient -->
                <div class="modal-header text-white px-4 py-3 border-0"
                    style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-white"
                        id="dailyWagesPayModalLabel"
                        style="font-size: 16px; letter-spacing: -0.3px; color: #ffffff !important;">
                        <i class="fas fa-wallet text-warning"></i> Process Salary Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 py-4" style="background: #f8fafc; max-height: 75vh; overflow-y: auto;">
                    <!-- Payment Type Selection -->
                    <div class="mb-4">
                        <label class="form-label mb-2 text-secondary small fw-bold text-uppercase"
                            style="letter-spacing: 0.5px; font-size: 10px;">Payment Type <span
                                class="text-danger">*</span></label>
                        <div class="d-flex gap-2 p-1 bg-light rounded-pill border">
                            <button type="button"
                                class="btn btn-sm flex-fill py-2 fw-bold active rounded-pill transition-all"
                                id="payTypeFullBtn" style="font-size: 12px;">
                                <i class="fas fa-calendar-alt me-1"></i> Full Month
                            </button>
                            <button type="button"
                                class="btn btn-sm flex-fill py-2 fw-bold rounded-pill transition-all"
                                id="payTypePartialBtn" style="font-size: 12px;">
                                <i class="fas fa-calendar-day me-1"></i> Partial Range
                            </button>
                        </div>
                    </div>

                    <!-- Date Range Selection (Partial Mode Only) -->
                    <div class="card border-0 shadow-sm mb-4" id="partialDateRangeCard"
                        style="display: none; border-radius: 16px; background: #fff; border: 1px solid #e2e8f0 !important;">
                        <div class="card-body p-3">
                            <label class="form-label mb-2 text-secondary small fw-bold text-uppercase"
                                style="letter-spacing: 0.5px; font-size: 10px;">Select Date Range <span
                                    class="text-danger">*</span></label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1 fw-semibold" style="font-size: 11px;">From
                                        Date</small>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i
                                                class="fas fa-calendar"></i></span>
                                        <input type="text"
                                            class="form-control form-control-sm border-start-0 ps-0 text-dark fw-bold"
                                            id="payFromDate" placeholder="dd-mm-yyyy" readonly
                                            style="background: #fff; cursor: pointer; font-size: 12px;">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1 fw-semibold" style="font-size: 11px;">To
                                        Date</small>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i
                                                class="fas fa-calendar"></i></span>
                                        <input type="text"
                                            class="form-control form-control-sm border-start-0 ps-0 text-dark fw-bold"
                                            id="payToDate" placeholder="dd-mm-yyyy" readonly
                                            style="background: #fff; cursor: pointer; font-size: 12px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Already Paid History Card -->
                    <div class="card border-0 shadow-sm mb-4" id="alreadyPaidHistoryCard"
                        style="border-radius: 16px; background: #fff; border: 1px solid #e2e8f0 !important;">
                        <div class="card-body p-3">
                            <h6 class="text-secondary fw-bold mb-3 text-uppercase d-flex align-items-center gap-1.5"
                                style="font-size: 10px; letter-spacing: 0.5px;">
                                <i class="fas fa-history text-info"></i> Already Paid Salary
                            </h6>
                            <div id="alreadyPaidHistoryContent"
                                style="max-height: 140px; overflow-y: auto; padding-right: 2px;">
                                <!-- Populate list of payments -->
                            </div>
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div class="card border-0 shadow-sm mb-4"
                        style="border-radius: 16px; background: #fff; border: 1px solid #e2e8f0 !important;">
                        <div class="card-body p-4">
                            <!-- Staff Header Profile style -->
                            <div class="d-flex align-items-center mb-4 bg-light p-3 rounded-3"
                                style="border-left: 4px solid #3b82f6;">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                    style="width: 44px; height: 44px; background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;">
                                    <i class="fas fa-user-tie fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" id="payModalStaffName"
                                        style="font-size: 15px;">-</h6>
                                    <span class="badge bg-secondary-transparent fw-semibold px-2 py-0.5 mt-1"
                                        id="payModalStaffCode"
                                        style="font-size: 10px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">-</span>
                                </div>
                            </div>

                            <!-- Grid details -->
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted d-block fw-semibold mb-0.5"
                                        style="font-size: 11px;">Payment Type</small>
                                    <span class="fw-bold text-dark" id="summaryPaymentType"
                                        style="font-size: 13px;">Full Month</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block fw-semibold mb-0.5" style="font-size: 11px;"
                                        id="summaryPeriodLabel">Month</small>
                                    <span class="fw-bold text-dark" id="summaryPeriodVal"
                                        style="font-size: 13px;">-</span>
                                </div>

                                <div class="col-6 border-top pt-3">
                                    <small class="text-muted d-block fw-semibold mb-0.5"
                                        style="font-size: 11px;">Present Days</small>
                                    <span class="badge bg-success-transparent fw-bold px-2 py-1" id="summaryPresent"
                                        style="font-size: 12px; background: #d1fae5; color: #065f46;">-</span>
                                </div>
                                <div class="col-6 border-top pt-3">
                                    <small class="text-muted d-block fw-semibold mb-0.5"
                                        style="font-size: 11px;">Working Duration</small>
                                    <span class="fw-bold text-dark" id="summaryDuration"
                                        style="font-size: 13px;">-</span>
                                </div>

                                <div class="col-6 border-top pt-3" id="summaryAlreadyPaidCol">
                                    <small class="text-muted d-block fw-semibold mb-0.5"
                                        style="font-size: 11px;">Already Paid Days</small>
                                    <span class="fw-bold text-warning" id="summaryAlreadyPaid"
                                        style="font-size: 13px;">-</span>
                                </div>
                                <div class="col-6 border-top pt-3" id="summaryRemainingCol">
                                    <small class="text-muted d-block fw-semibold mb-0.5"
                                        style="font-size: 11px;">Remaining Payable</small>
                                    <span class="fw-bold text-primary" id="summaryRemaining"
                                        style="font-size: 13px;">-</span>
                                </div>

                                <div class="col-6 border-top pt-3">
                                    <small class="text-muted d-block fw-semibold mb-0.5"
                                        style="font-size: 11px;">Hourly Rate</small>
                                    <span class="fw-bold text-success" id="payModalRate"
                                        style="font-size: 13px;">-</span>
                                </div>

                                <div class="col-12 border-top pt-3" id="summaryPresentDatesCol"
                                    style="display: none;">
                                    <small class="text-muted d-block fw-semibold mb-1"
                                        style="font-size: 11px;">Present Dates (Unpaid)</small>
                                    <div class="p-2.5 bg-light rounded text-dark fw-semibold" id="summaryPresentDates"
                                        style="font-size: 11.5px; line-height: 1.4; border: 1px dashed #cbd5e1;">-
                                    </div>
                                </div>
                            </div>

                            <!-- PF details if applicable -->
                            <div class="row g-3 border-top pt-3 mt-1" id="payModalPfDetailsSection"
                                style="display: none;">
                                <div class="col-4">
                                    <small class="text-muted d-block fw-semibold mb-0.5" style="font-size: 10px;">UAN
                                        No</small>
                                    <span class="fw-semibold text-dark" style="font-size: 11px;"
                                        id="payModalUanNo">-</span>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block fw-semibold mb-0.5" style="font-size: 10px;">PF
                                        No</small>
                                    <span class="fw-semibold text-dark" style="font-size: 11px;"
                                        id="payModalPfNo">-</span>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block fw-semibold mb-0.5" style="font-size: 10px;">ESI
                                        No</small>
                                    <span class="fw-semibold text-dark" style="font-size: 11px;"
                                        id="payModalEsiNo">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Break-up Card -->
                    <div class="card border-0 shadow-sm"
                        style="border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0 !important; background: #fff;">
                        <div class="card-body p-4">
                            <h6 class="text-secondary fw-bold mb-3 text-uppercase"
                                style="font-size: 10px; letter-spacing: 0.5px;">Salary Break-Up</h6>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Net Salary (Earnings):</span>
                                <span id="payModalNet" class="fw-bold text-dark small">-</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted small fw-semibold">Employee PF Deduction:</span>
                                <span id="payModalPf" class="fw-bold text-danger small">-</span>
                            </div>

                            <!-- Highlighted Grand Payout Box -->
                            <div class="p-3 rounded-3 d-flex justify-content-between align-items-center"
                                style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #a7f3d0;">
                                <div>
                                    <span class="text-success-800 fw-bold text-uppercase d-block"
                                        style="font-size: 9px; letter-spacing: 0.5px; color: #065f46;">Gross Payable
                                        Salary</span>
                                    <small class="text-muted-700" style="font-size: 10px; color: #047857;">Final
                                        amount to disburse</small>
                                </div>
                                <span id="payModalGross" class="fw-extrabold text-success"
                                    style="font-size: 20px; font-weight: 800; color: #047857 !important;">-</span>
                            </div>

                            <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                                <span class="text-muted small fw-semibold">Cost To Company (CTC):</span>
                                <span id="payModalCtc" class="fw-bold text-secondary small">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer with nice buttons -->
                <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-end gap-2 border-top">
                    <button type="button" class="btn btn-light fw-bold border" data-bs-dismiss="modal"
                        style="border-radius: 10px; font-size: 12.5px; padding: 8px 16px; background: #fff; color: #475569;">
                        Cancel
                    </button>
                    <button type="button"
                        class="btn btn-success fw-bold px-4 border-0 d-flex align-items-center gap-1.5 shadow-sm"
                        id="confirmPayBtn"
                        style="border-radius: 10px; font-size: 12.5px; padding: 8px 20px; background: linear-gradient(135deg, #10b981, #059669) !important;">
                        <i class="fas fa-check-circle"></i> Pay Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Wages View History Modal -->
    <div class="modal fade" id="dailyWagesViewModal" tabindex="-1" aria-labelledby="dailyWagesViewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="dailyWagesViewModalLabel">
                        <i class="fas fa-history text-primary me-2"></i>Staff Salary History
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4" style="background: #f8fafc; max-height: 75vh; overflow-y: auto;">
                    <!-- Staff Info Card -->
                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background: #fff;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary-transparent rounded-circle p-2 me-3"
                                    style="background: #eff6ff;">
                                    <i class="fas fa-user-tie text-primary fa-lg" style="color: #2563eb;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" id="viewModalStaffName">-</h6>
                                    <small class="text-muted" id="viewModalStaffCode">-</small>
                                </div>
                            </div>

                            <div class="row g-2 border-top pt-3">
                                <div class="col-6">
                                    <small class="text-secondary small d-block">Per Hour Rate</small>
                                    <span class="fw-bold text-success" id="viewModalRate">-</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-secondary small d-block">UAN No</small>
                                    <span class="fw-semibold text-dark" id="viewModalUanNo">-</span>
                                </div>
                                <div class="col-6 mt-2">
                                    <small class="text-secondary small d-block">PF No</small>
                                    <span class="fw-semibold text-dark" id="viewModalPfNo">-</span>
                                </div>
                                <div class="col-6 mt-2">
                                    <small class="text-secondary small d-block">ESI No</small>
                                    <span class="fw-semibold text-dark" id="viewModalEsiNo">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History Card -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #fff;">
                        <div class="card-body p-3">
                            <h6 class="text-muted fw-bold mb-3 text-uppercase"
                                style="font-size: 11px; letter-spacing: 1px;">Payment Records</h6>
                            <div id="viewHistoryContent" style="max-height: 250px; overflow-y: auto;">
                                <!-- Populate list of payments -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-end border-top">
                    <button type="button" class="btn btn-secondary border-0" data-bs-dismiss="modal"
                        style="background: #f1f5f9; color: #475569; border-radius: 8px;">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
