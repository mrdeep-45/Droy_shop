<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        .section-header {
            background-color: #1B2C3F;
            color:#fff;
            padding: 6px 12px;
            border-radius: 4px 4px 0 0;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 0;
        }
        .section-body {
            border: 1px solid #dee2e6;
            border-top: none;
            padding: 14px 12px 8px;
            border-radius: 0 0 4px 4px;
            margin-bottom: 16px;
        }
        .modal-content{
            border-radius:24px;
            overflow:hidden;
        }
        #cameraPreview {
            width: 100%;
            max-width: 280px;
            height: auto;
            min-height: 200px;
            background: #000;
            border-radius: 6px;
            display: block;
        }
        #facePreview {
            width: 100%;
            max-width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            background: #f0f0f0;
        }
        #facePreviewPlaceholder {
            width: 100%;
            max-width: 200px;
            height: 200px;
            background: #f0f0f0;
            border: 1px solid #dee2e6;
            border-radius: 6px;
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
                                <ul class="nav nav-tabs nav-tabs-footer mb-0" role="tablist">
                                    @if (permissions_check('canView'))
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#dw-list">List</a>
                                    </li>
                                    @endif
                                    @if (permissions_check('canCreate'))
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" role="tab" href="#new-dw-staff" id="newDwStaffTab">New Staff</a>
                                    </li>
                                    @endif
                                    @if (permissions_check('canDelete'))
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" role="tab" href="#deleted-dw-staff">Delete</a>
                                    </li>
                                    @endif
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">

                                    {{-- ============ LIST TAB ============ --}}
                                    @if (permissions_check('canView'))
                                    <div class="tab-pane fade show active" id="dw-list">
                                        <div class="table-responsive">
                                            <table id="dwStaffData" class="table table-bordered menu-submenu-data" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Code</th>
                                                        <th>Name</th>
                                                        <th>Phone</th>
                                                        <th>Hire Date</th>
                                                        <th>Per Hour Rate</th>
                                                        <th class="text-center" style="width:18%;">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- ============ DELETED STAFF TAB ============ --}}
                                    @if (permissions_check('canDelete'))
                                    <div class="tab-pane fade" id="deleted-dw-staff">
                                        <div class="table-responsive">
                                            <table id="deletedDwStaffData" class="table table-bordered menu-submenu-data" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Code</th>
                                                        <th>Name</th>
                                                        <th>Phone</th>
                                                        <th>Hire Date</th>
                                                        <th>Per Hour Rate</th>
                                                        <th class="text-center" style="width:18%;">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- ============ NEW / EDIT STAFF TAB ============ --}}
                                    @if (permissions_check('canCreate'))
                                    <div class="tab-pane" id="new-dw-staff" role="tabpanel">
                                        <form id="dw_staff_form" enctype="multipart/form-data" novalidate>
                                            @csrf
                                            <input type="hidden" id="dw_staff_id" name="dw_staff_id" value="">

                                            {{-- ── Basic Information ── --}}
                                            <div class="section-header">Basic Information</div>
                                            <div class="section-body">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <label for="staff_code" class="form-label mb-1">Staff Code <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="staff_code" name="staff_code"
                                                            value="{{ $next_code }}" readonly>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="staff_name" class="form-label mb-1">Staff Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="staff_name" name="staff_name"
                                                            placeholder="Enter full name">
                                                        <div class="invalid-feedback" id="staff_name-error"></div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="contact_no" class="form-label mb-1">Phone <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="contact_no" name="contact_no"
                                                            placeholder="10-digit mobile number" maxlength="15">
                                                        <div class="invalid-feedback" id="contact_no-error"></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 mb-2">
                                                        <label for="address" class="form-label mb-1">Address <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" id="address" name="address" rows="2"
                                                            placeholder="Enter address"></textarea>
                                                        <div class="invalid-feedback" id="address-error"></div>
                                                    </div>
                                                </div>
                                             </div>
 
                                             {{-- ── Employment Details ── --}}
                                             <div class="section-header">Employment Details</div>
                                             <div class="section-body">
                                                 <div class="row align-items-end">
                                                     <div class="col-md-3 mb-3">
                                                         <label for="hire_date" class="form-label mb-1">Hire Date <span class="text-danger">*</span></label>
                                                         <input type="text" class="form-control" id="hire_date" name="hire_date" placeholder="YYYY-MM-DD">
                                                         <div class="invalid-feedback" id="hire_date-error"></div>
                                                     </div>
                                                     <div class="col-md-3 mb-3">
                                                         <label for="per_hour_rate" class="form-label mb-1">Per Hour Rate <span class="text-danger">*</span></label>
                                                         <input type="number" class="form-control" id="per_hour_rate" name="per_hour_rate"
                                                             min="0" step="0.01" placeholder="0.00">
                                                         <div class="invalid-feedback" id="per_hour_rate-error"></div>
                                                     </div>
                                                    <div class="col-md-3 mb-3 d-flex align-items-end pb-3">
                                                        <div class="form-check">
                                                            <input type="hidden" name="is_pf_applicable" value="0">
                                                            <input type="checkbox" class="form-check-input" id="is_pf_applicable"
                                                                name="is_pf_applicable" value="1">
                                                            <label class="form-check-label" for="is_pf_applicable">PF Applicable</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-2" id="pf_details_container" style="display: none;">
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label mb-1" for="uan_no">UAN No <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="uan_no" name="uan_no" placeholder="Enter UAN Number">
                                                        <div class="invalid-feedback" id="uan_no-error"></div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label mb-1" for="pf_no">PF No <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="pf_no" name="pf_no" placeholder="Enter PF Number">
                                                        <div class="invalid-feedback" id="pf_no-error"></div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label mb-1" for="esi_no">ESI No <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="esi_no" name="esi_no" placeholder="Enter ESI Number">
                                                        <div class="invalid-feedback" id="esi_no-error"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ── Shift Information ── --}}
                                            <div class="section-header">Shift Information</div>
                                            <div class="section-body">
                                                <div class="row">
                                                    <div class="col-md-12 mb-2">
                                                        <label class="form-label mb-1 d-block">Shift Timing <span class="text-danger">*</span></label>
                                                        <div class="d-flex flex-column flex-sm-row flex-wrap gap-2 gap-sm-4">
                                                            @foreach ($shifts as $shift)
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="shift_timing"
                                                                        id="shift_{{ $shift->shift_id }}" value="{{ $shift->shift_id }}">
                                                                    <label class="form-check-label" for="shift_{{ $shift->shift_id }}">{{ $shift->display_name }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="invalid-feedback d-block" id="shift_timing-error"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ── Face Registration ── --}}
                                            <div class="section-header">Face Registration</div>
                                            <div class="section-body">
                                                <div class="row">
                                                    {{-- Camera --}}
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label mb-1">Capture Face (Camera)</label>
                                                        <video id="cameraPreview" autoplay playsinline></video>
                                                        <button type="button" class="btn btn-info btn-sm mt-2 me-1" id="startCameraBtn">
                                                            <i class="fas fa-video"></i> Start Camera
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm mt-2" id="captureFaceBtn">
                                                            <i class="fas fa-camera"></i> Capture Face
                                                        </button>
                                                        <input type="hidden" name="face_data" id="face_data">
                                                        <canvas id="faceCanvas" style="display:none;"></canvas>
                                                    </div>
                                                     {{-- Upload --}}
                                                     <div class="col-md-4 mb-3">
                                                         <label for="upload_face" class="form-label mb-1">Upload Face Image</label>
                                                         <input type="file" class="form-control" id="upload_face" name="upload_face"
                                                             accept="image/jpg,image/jpeg,image/png">
                                                         <small class="text-muted">Upload clear single-face image</small>
                                                         <div class="invalid-feedback" id="upload_face-error"></div>
                                                     </div>
                                                     {{-- Preview --}}
                                                     <div class="col-md-4 mb-3">
                                                         <label class="form-label mb-1">Preview</label><br>
                                                         <img id="facePreview" src="" alt="Preview" style="display:none;">
                                                         <div id="facePreviewPlaceholder"></div>
                                                         <button type="button" class="btn btn-danger btn-sm mt-2" id="clearFaceBtn" style="display:none;">
                                                             <i class="fas fa-times"></i> Clear Image
                                                         </button>
                                                     </div>
                                                 </div>
                                             </div>
 
                                             {{-- ── Family Details ── --}}
                                             <div class="section-header">Family Details</div>
                                             <div class="section-body">
                                                 <div class="row">
                                                     <div class="col-md-5 mb-2">
                                                         <label for="family_member_name" class="form-label mb-1">Family Member Name</label>
                                                         <input type="text" class="form-control" id="family_member_name"
                                                             name="family_member_name" placeholder="Enter name">
                                                     </div>
                                                     <div class="col-md-4 mb-2">
                                                         <label for="family_contact_no" class="form-label mb-1">Contact No</label>
                                                         <input type="text" class="form-control" id="family_contact_no"
                                                             name="family_contact_no" placeholder="Enter contact number" maxlength="15">
                                                     </div>
                                                </div>
                                            </div>

                                            {{-- Buttons --}}
                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                <button type="button" class="btn btn-danger" id="resetDwBtn">Reset</button>
                                                <button type="submit" class="btn btn-primary" id="dwSubmitBtn">Submit</button>
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
    <!-- Daily Wages Staff View Modal -->
    <div class="modal fade" id="dailyWagesStaffViewModal" tabindex="-1" aria-labelledby="dailyWagesStaffViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <!-- Header with premium gradient and profile visual -->
                <div class="modal-header text-white px-4 py-4 border-0 position-relative" style="background: linear-gradient(135deg,#0f172a 0%,#1e293b 40%,#334155 100%); min-height: 190px;">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    
                    <div class="d-flex align-items-center gap-4 mt-3 w-100">
                        <div class="position-relative">
                            <img id="viewStaffAvatar" src="{{ $img_path }}/dw_faces/avatar1.jpeg" class="rounded-circle border border-4 border-white shadow-sm" style="width: 100px; height: 100px; object-fit: cover;" alt="Avatar">
                        </div>
                        <div class="text-start">
                            <h4 class="fw-bold text-white mb-2" id="viewStaffTitleName" style="letter-spacing: -0.5px;">-</h4>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="badge bg-white text-dark fw-bold px-2.5 py-1" id="viewStaffTitleCode" style="font-size: 11px;">-</span>
                                <span class="badge fw-bold px-2.5 py-1" id="viewStaffTitleStatus" style="font-size: 11px;">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-body px-4 py-4" style="background: #f8fafc; max-height: 65vh; overflow-y: auto;">
                    <div class="row g-4">
                        <!-- Section 1: Basic Information -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border: 1px solid #e2e8f0 !important; background: #fff;">
                                <div class="card-body p-4">
                                    <h6 class="text-primary fw-bold mb-4 text-uppercase d-flex align-items-center gap-2" style="font-size: 11px; letter-spacing: 0.5px;">
                                        <i class="fas fa-info-circle fa-lg"></i> Basic Information
                                    </h6>
                                    
                                    <div class="d-flex flex-column gap-3">
                                        <div class="row align-items-center">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #eff6ff !important; color: #2563eb !important;">
                                                    <i class="fas fa-id-badge" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">Staff Code</span>
                                            </div>
                                            <div class="col-6 fw-bold text-dark text-end text-sm-start" id="viewStaffCode">-</div>
                                        </div>
                                        
                                        <div class="row align-items-center border-top pt-2.5">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #eff6ff !important; color: #2563eb !important;">
                                                    <i class="fas fa-user-tag" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">Staff Name</span>
                                            </div>
                                            <div class="col-6 fw-bold text-dark text-end text-sm-start" id="viewStaffName">-</div>
                                        </div>
                                        
                                        <div class="row align-items-center border-top pt-2.5">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #eff6ff !important; color: #2563eb !important;">
                                                    <i class="fas fa-phone-alt" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">Contact Number</span>
                                            </div>
                                            <div class="col-6 fw-bold text-dark text-end text-sm-start" id="viewStaffContact">-</div>
                                        </div>
                                        
                                        <div class="row border-top pt-2.5">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #eff6ff !important; color: #2563eb !important;">
                                                    <i class="fas fa-map-marker-alt" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">Address</span>
                                            </div>
                                            <div class="col-6 fw-semibold text-dark text-end text-sm-start" id="viewStaffAddress" style="font-size: 12.5px; line-height: 1.4;">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Employment Details -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border: 1px solid #e2e8f0 !important; background: #fff;">
                                <div class="card-body p-4">
                                    <h6 class="text-success fw-bold mb-4 text-uppercase d-flex align-items-center gap-2" style="font-size: 11px; letter-spacing: 0.5px;">
                                        <i class="fas fa-briefcase fa-lg"></i> Employment Details
                                    </h6>
                                    
                                    <div class="d-flex flex-column gap-3">
                                        <div class="row align-items-center">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #f0fdf4 !important; color: #16a34a !important;">
                                                    <i class="fas fa-calendar-check" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">Hire Date</span>
                                            </div>
                                            <div class="col-6 fw-bold text-dark text-end text-sm-start" id="viewStaffHireDate">-</div>
                                        </div>
                                        
                                        <div class="row align-items-center border-top pt-2.5">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #f0fdf4 !important; color: #16a34a !important;">
                                                    <i class="fas fa-rupee-sign" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">Per Hour Rate</span>
                                            </div>
                                            <div class="col-6 fw-bold text-success text-end text-sm-start" id="viewStaffRate">-</div>
                                        </div>
                                        
                                        <div class="row align-items-center border-top pt-2.5">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #f0fdf4 !important; color: #16a34a !important;">
                                                    <i class="fas fa-business-time" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">Shift Name</span>
                                            </div>
                                            <div class="col-6 fw-bold text-dark text-end text-sm-start" id="viewStaffShiftName">-</div>
                                        </div>
                                        
                                        <div class="row align-items-center border-top pt-2.5">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #f0fdf4 !important; color: #16a34a !important;">
                                                    <i class="fas fa-clock" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">Shift Timing</span>
                                            </div>
                                            <div class="col-6 fw-bold text-dark text-end text-sm-start" id="viewStaffShiftTiming">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: PF & ESI Details -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border: 1px solid #e2e8f0 !important; background: #fff;">
                                <div class="card-body p-4">
                                    <h6 class="text-warning fw-bold mb-4 text-uppercase d-flex align-items-center gap-2" style="font-size: 11px; letter-spacing: 0.5px; color: #d97706 !important;">
                                        <i class="fas fa-shield-alt fa-lg"></i> PF & ESI Details
                                    </h6>
                                    
                                    <div class="d-flex flex-column gap-3">
                                        <div class="row align-items-center">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #fef3c7 !important; color: #d97706 !important;">
                                                    <i class="fas fa-file-invoice" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">PF Applicable</span>
                                            </div>
                                            <div class="col-6 text-end text-sm-start" id="viewStaffPfApplicable">-</div>
                                        </div>
                                        
                                        <div class="row align-items-center border-top pt-2.5">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #fef3c7 !important; color: #d97706 !important;">
                                                    <i class="fas fa-fingerprint" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">UAN Number</span>
                                            </div>
                                            <div class="col-6 fw-bold text-dark text-end text-sm-start" id="viewStaffUan">-</div>
                                        </div>
                                        
                                        <div class="row align-items-center border-top pt-2.5">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #fef3c7 !important; color: #d97706 !important;">
                                                    <i class="fas fa-id-card-alt" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">PF Number</span>
                                            </div>
                                            <div class="col-6 fw-bold text-dark text-end text-sm-start" id="viewStaffPfNo">-</div>
                                        </div>
                                        
                                        <div class="row align-items-center border-top pt-2.5">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #fef3c7 !important; color: #d97706 !important;">
                                                    <i class="fas fa-briefcase-medical" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">ESI Number</span>
                                            </div>
                                            <div class="col-6 fw-bold text-dark text-end text-sm-start" id="viewStaffEsiNo">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Family Details -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; border: 1px solid #e2e8f0 !important; background: #fff;">
                                <div class="card-body p-4">
                                    <h6 class="text-info fw-bold mb-4 text-uppercase d-flex align-items-center gap-2" style="font-size: 11px; letter-spacing: 0.5px; color: #0891b2 !important;">
                                        <i class="fas fa-users fa-lg"></i> Family Details
                                    </h6>
                                    
                                    <div class="d-flex flex-column gap-3">
                                        <div class="row align-items-center">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #ecfeff !important; color: #0891b2 !important;">
                                                    <i class="fas fa-user-friends" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">Member Name</span>
                                            </div>
                                            <div class="col-6 fw-bold text-dark text-end text-sm-start" id="viewStaffFamilyName">-</div>
                                        </div>
                                        
                                        <div class="row align-items-center border-top pt-2.5">
                                            <div class="col-6 d-flex align-items-center gap-2">
                                                <div class="bg-light text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #ecfeff !important; color: #0891b2 !important;">
                                                    <i class="fas fa-phone" style="font-size: 13px;"></i>
                                                </div>
                                                <span class="text-secondary small fw-semibold">Contact Number</span>
                                            </div>
                                            <div class="col-6 fw-bold text-dark text-end text-sm-start" id="viewStaffFamilyContact">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-end border-top">
                    <button type="button" class="btn btn-secondary border-0 fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 10px; font-size: 12.5px; padding: 8px 20px; background: #f1f5f9; color: #475569;">
                        Close
                    </button>
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
        const actualUrl = "{{ $actual_url }}";
        const imgPath = "{{ $img_path }}";

        function getStaffImageUrl(path) {
            if (!path) return imgPath + '/dw_faces/avatar1.jpeg';
            if (path.includes('assets/uploads/dw_faces/')) {
                let parts = path.split('/');
                return imgPath + '/dw_faces/' + parts[parts.length - 1];
            }
            if (path.includes('dw_faces/')) {
                let parts = path.split('/');
                return imgPath + '/dw_faces/' + parts[parts.length - 1];
            }
            return imgPath + '/dw_faces/' + path;
        }

        const shiftsMap = {
            @foreach($shifts as $shift)
                "{{ $shift->shift_id }}": {
                    name: "{{ $shift->shift_name }}",
                    type: "{{ $shift->shift_type }}",
                    time_in: "{{ date('g:i A', strtotime($shift->time_in)) }}",
                    time_out: "{{ date('g:i A', strtotime($shift->time_out)) }}"
                },
            @endforeach
        };

        // ── Input Restrictions ──
        $(document).ready(function() {
            // Trim on blur
            $('input[type="text"], textarea').on('blur', function() {
                $(this).val($.trim($(this).val()));
            });

            // Name fields: Alphabets and spaces only
            $('#staff_name, #family_member_name').on('input', function() {
                this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            });

            // Contact No: Numeric only
            $('#contact_no, #family_contact_no').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Amount: Positive numeric only
            $('#per_hour_rate').on('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '');
                // Allow only one decimal point
                if ((this.value.match(/\./g) || []).length > 1) {
                    this.value = this.value.replace(/\.$/, '');
                }
            });
        });

        let dwEditMode = false;

        // ── Reset form to create mode ──────────────────────────────────────
        function resetDwForm() {
            dwEditMode = false;
            $('#dw_staff_id').val('');
            $('#dwSubmitBtn').text('Submit');
            $('#newDwStaffTab').text('New Staff');
            $('#dw_staff_form')[0].reset();

            // Reload auto-generated code via AJAX
            $.get("{{ route('dailywages.next.code') }}", function (res) {
                $('#staff_code').val(res.code);
            });

            // Clear flatpickr
            if (window.hireDatePicker) {
                window.hireDatePicker.clear();
            }

            // Reset face preview
            $('#facePreview').hide().attr('src', '');
            $('#facePreviewPlaceholder').show();
            $('#clearFaceBtn').hide();
            // Reset camera capture hidden field
            $('#face_data').val('');
            $('#upload_face').val('');

            $('#pf_details_container').hide();

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();
        }

        // ── Populate form for edit ─────────────────────────────────────────
        function populateDwForm(data) {
            dwEditMode = true;
            $('#dw_staff_id').val(data.dw_staff_id);
            $('#staff_code').val(data.staff_code);
            $('#staff_name').val(data.staff_name);
            $('#contact_no').val(data.contact_no);
            $('#address').val(data.address);
            $('#hire_date').val(data.hire_date);
            if (window.hireDatePicker) {
                window.hireDatePicker.setDate(data.hire_date);
            }
            $('#per_hour_rate').val(data.per_hour_rate);
            $('#is_pf_applicable').prop('checked', data.is_pf_applicable == 1);
            
            $('#uan_no').val(data.uan_no || '');
            $('#pf_no').val(data.pf_no || '');
            $('#esi_no').val(data.esi_no || '');
            if (data.is_pf_applicable == 1) {
                $('#pf_details_container').show();
            } else {
                $('#pf_details_container').hide();
            }

            $('input[name="shift_timing"][value="' + data.shift_timing + '"]').prop('checked', true);
            $('#family_member_name').val(data.family_member_name || '');
            $('#family_contact_no').val(data.family_contact_no || '');
            $('#dwSubmitBtn').text('Update');
            $('#newDwStaffTab').text('Edit Staff');

            // Show existing face image if any
            let existingFacePath = data.upload_face || data.face_image;
            if (existingFacePath) {
                $('#facePreview').attr('src', getStaffImageUrl(existingFacePath)).show();
                $('#facePreviewPlaceholder').hide();
                $('#clearFaceBtn').show();
            } else {
                $('#facePreview').hide().attr('src', '');
                $('#facePreviewPlaceholder').show();
                $('#clearFaceBtn').hide();
            }

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();
        }

        $(document).ready(function () {

            $('#is_pf_applicable').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#pf_details_container').slideDown();
                } else {
                    $('#pf_details_container').slideUp();
                    $('#uan_no, #pf_no, #esi_no').val('').removeClass('is-invalid');
                    $('#uan_no-error, #pf_no-error, #esi_no-error').hide().text('');
                }
            });

            window.hireDatePicker = flatpickr("#hire_date", {
                dateFormat: "Y-m-d",
                allowInput: true
            });

            var canUpdate = {{ permissions_check('canUpdate') ? 'true' : 'false' }};
            var canDelete = {{ permissions_check('canDelete') ? 'true' : 'false' }};

            // ── DataTable ────────────────────────────────────────────────
            var table = $('#dwStaffData').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dailywages.list') }}",
                autoWidth: false,
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                    { data: 'staff_code',  name: 'staff_code',  title: 'Code' },
                    { data: 'staff_name',  name: 'staff_name',  title: 'Name' },
                    { data: 'contact_no',  name: 'contact_no',  title: 'Phone' },
                    { data: 'hire_date_fmt', name: 'hire_date', title: 'Hire Date', orderable: true },
                    { data: 'per_hour_rate', name: 'per_hour_rate', title: 'Per Hour Rate' },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        visible: canUpdate || canDelete,
                        className: 'text-center',
                        width: '18%'
                    }
                ],
                responsive: true
            });

            var deletedTable = $('#deletedDwStaffData').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dailywages.deleted.list') }}",
                autoWidth: false,
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                    { data: 'staff_code',  name: 'staff_code',  title: 'Code' },
                    { data: 'staff_name',  name: 'staff_name',  title: 'Name' },
                    { data: 'contact_no',  name: 'contact_no',  title: 'Phone' },
                    { data: 'hire_date_fmt', name: 'hire_date', title: 'Hire Date', orderable: true },
                    { data: 'per_hour_rate', name: 'per_hour_rate', title: 'Per Hour Rate' },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        visible: canDelete,
                        className: 'text-center',
                        width: '18%'
                    }
                ],
                responsive: true
            });

            // ── Tab switching ─────────────────────────────────────────────
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#new-dw-staff' && !dwEditMode) {
                    resetDwForm();
                }
                if (target === '#dw-list') {
                    resetDwForm();
                    table.ajax.reload();
                }
                if (target === '#deleted-dw-staff') {
                    resetDwForm();
                    deletedTable.ajax.reload();
                }
            });

            // ── Camera: start on tab open ─────────────────────────────────
            let mediaStream = null;

            $('a[href="#new-dw-staff"]').on('shown.bs.tab', function () {
                startCamera();
            });
            $('a[href="#dw-list"]').on('shown.bs.tab', function () {
                stopCamera();
            });
            $('a[href="#deleted-dw-staff"]').on('shown.bs.tab', function () {
                stopCamera();
            });

            function startCamera() {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({ video: true })
                        .then(function (stream) {
                            mediaStream = stream;
                            const video = document.getElementById('cameraPreview');
                            video.srcObject = stream;
                            video.play().catch(e => console.error("Error playing video:", e));
                        })
                        .catch(function (err) {
                            console.error("Camera error:", err);
                            iziToast.warning({ title: 'Camera Access', message: 'Could not access the camera. Please allow permissions.', position: 'topRight' });
                        });
                } else {
                    console.error("getUserMedia not supported.");
                    iziToast.warning({ title: 'Camera Access', message: 'Camera access is not supported or requires a secure (HTTPS) connection.', position: 'topRight' });
                }
            }

            function stopCamera() {
                if (mediaStream) {
                    mediaStream.getTracks().forEach(t => t.stop());
                    mediaStream = null;
                }
            }

            $('#startCameraBtn').on('click', function() {
                startCamera();
            });

            // ── Capture face from camera ───────────────────────────────────
            $('#captureFaceBtn').on('click', function () {
                const btn = $(this);
                const video  = document.getElementById('cameraPreview');
                const canvas = document.getElementById('faceCanvas');
                canvas.width  = video.videoWidth  || 280;
                canvas.height = video.videoHeight || 200;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                const dataUrl = canvas.toDataURL('image/jpeg');
                
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Validating...');
                
                // Real-time validation
                $.ajax({
                    url: "{{ route('dailywages.validate.face') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        face_data: dataUrl
                    },
                    success: function (res) {
                        if (res.status) {
                            iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                            $('#face_data').val(dataUrl);
                            $('#facePreview').attr('src', dataUrl).show();
                            $('#facePreviewPlaceholder').hide();
                            $('#clearFaceBtn').show();
                            stopCamera(); // Turn off camera after successful capture
                        }
                    },
                    error: function (xhr) {
                        let msg = xhr.responseJSON?.message || 'face is not detected in the uploaded file.';
                        iziToast.error({ title: 'Error', message: msg, position: 'topRight' });
                        $('#face_data').val('');
                        $('#facePreview').hide().attr('src', '');
                        $('#facePreviewPlaceholder').show();
                        $('#clearFaceBtn').hide();
                    },
                    complete: function () {
                        btn.prop('disabled', false).html('<i class="fas fa-camera"></i> Capture Face');
                    }
                });
            });

            // ── Upload face preview ────────────────────────────────────────
            $('#upload_face').on('change', function () {
                const file = this.files[0];
                if (file) {
                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!validTypes.includes(file.type)) {
                        iziToast.error({ title: 'Error', message: 'Only accept image in jpg, jpeg and png format.', position: 'topRight' });
                        $(this).val('');
                        return;
                    }

                    const input = $(this);
                    input.prop('disabled', true);
                    if ($('#face_upload_loader').length === 0) {
                        input.after('<div id="face_upload_loader" class="text-primary mt-1 small"><i class="fas fa-spinner fa-spin"></i> Validating face...</div>');
                    }
                    
                    let formData = new FormData();
                    formData.append('_token', "{{ csrf_token() }}");
                    formData.append('upload_face', file);

                    $.ajax({
                        url: "{{ route('dailywages.validate.face') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (res) {
                            if (res.status) {
                                iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                                const reader = new FileReader();
                                reader.onload = function (e) {
                                    $('#facePreview').attr('src', e.target.result).show();
                                    $('#facePreviewPlaceholder').hide();
                                    $('#clearFaceBtn').show();
                                };
                                reader.readAsDataURL(file);
                                stopCamera(); // Turn off camera after successful upload
                            }
                        },
                        error: function (xhr) {
                            let msg = xhr.responseJSON?.message || 'face is not detected in the uploaded file.';
                            iziToast.error({ title: 'Error', message: msg, position: 'topRight' });
                            input.val('');
                            $('#facePreview').hide().attr('src', '');
                            $('#facePreviewPlaceholder').show();
                            $('#clearFaceBtn').hide();
                        },
                        complete: function () {
                            input.prop('disabled', false);
                            $('#face_upload_loader').remove();
                        }
                    });
                }
            });

            // ── Clear Face Button ──────────────────────────────────────────
            $('#clearFaceBtn').on('click', function () {
                $('#upload_face').val('');
                $('#face_data').val('');
                $('#facePreview').hide().attr('src', '');
                $('#facePreviewPlaceholder').show();
                $(this).hide();
                startCamera();
            });

            // ── Reset button ──────────────────────────────────────────────
            $('#resetDwBtn').on('click', function () {
                resetDwForm();
            });

            // ── Edit button ───────────────────────────────────────────────
            $(document).on('click', '.edit-dw-staff', function () {
                const id = $(this).data('id');
                $('#dwSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');

                $.ajax({
                    url: "{{ route('dailywages.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status) {
                            populateDwForm(res.data);
                            $('a[href="#new-dw-staff"]').tab('show');
                        } else {
                            iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Failed to load.', position: 'topRight' });
                    },
                    complete: function () {
                        $('#dwSubmitBtn').prop('disabled', false).text(dwEditMode ? 'Update' : 'Submit');
                    }
                });
            });

            // ── Delete button ─────────────────────────────────────────────
            $(document).on('click', '.delete-dw-staff', function () {
                const id = $(this).data('id');
                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'dw-delete-question',
                    zindex: 999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete this staff?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            $.ajax({
                                url: "{{ route('dailywages.destroy') }}",
                                type: 'POST',
                                data: { _token: "{{ csrf_token() }}", dw_staff_id: id },
                                success: function (res) {
                                    if (res.status) {
                                        iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                                        table.ajax.reload();
                                        deletedTable.ajax.reload();
                                    } else {
                                        iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                                    }
                                },
                                error: function (xhr) {
                                    iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Failed.', position: 'topRight' });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }]
                    ]
                });
            });

            // ── Restore button ────────────────────────────────────────────
            $(document).on('click', '.restore-dw-staff', function () {
                const id = $(this).data('id');
                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'dw-restore-question',
                    zindex: 999,
                    title: 'Confirm',
                    message: 'Are you sure you want to restore this staff?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            $.ajax({
                                url: "{{ route('dailywages.restore') }}",
                                type: 'POST',
                                data: { _token: "{{ csrf_token() }}", dw_staff_id: id },
                                success: function (res) {
                                    if (res.status) {
                                        iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                                        table.ajax.reload();
                                        deletedTable.ajax.reload();
                                    } else {
                                        iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                                    }
                                },
                                error: function (xhr) {
                                    iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Failed.', position: 'topRight' });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }]
                    ]
                });
            });

            // ── Form Submit ───────────────────────────────────────────────
            $('#dw_staff_form').on('submit', function (e) {
                e.preventDefault();

                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('').hide();
                $('#shift_timing-error').text('').hide();

                let isValid = true;
                
                function showError(selector, message) {
                    $(selector).addClass('is-invalid');
                    $(selector + '-error').text(message).show();
                    isValid = false;
                }

                // Client-side validation
                let staffName = $('#staff_name').val().trim();
                if (!staffName) {
                    showError('#staff_name', 'Staff Name is required.');
                } else if (!/^[a-zA-Z\s]+$/.test(staffName)) {
                    showError('#staff_name', 'Name should accept only alphabets and spaces.');
                }

                let contactNo = $('#contact_no').val().trim();
                if (!contactNo) {
                    showError('#contact_no', 'Phone number is required.');
                } else if (!/^[0-9]+$/.test(contactNo)) {
                    showError('#contact_no', 'Mobile number should accept only numeric input.');
                } else if (contactNo.length < 10 || contactNo.length > 15) {
                    showError('#contact_no', 'Mobile number must be between 10 and 15 digits.');
                }

                let address = $('#address').val().trim();
                if (!address) {
                    showError('#address', 'Address is required.');
                }

                let hireDate = $('#hire_date').val();
                if (!hireDate || isNaN(Date.parse(hireDate))) {
                    showError('#hire_date', 'Please select a valid Hire Date.');
                }

                let perHourRate = $('#per_hour_rate').val();
                if (!perHourRate) {
                    showError('#per_hour_rate', 'Per Hour Rate is required.');
                } else if (isNaN(perHourRate) || Number(perHourRate) < 0) {
                    showError('#per_hour_rate', 'Enter a valid positive amount.');
                }

                // Family Member validation (Optional but validated if provided)
                let familyName = $('#family_member_name').val().trim();
                if (familyName && !/^[a-zA-Z\s]+$/.test(familyName)) {
                    showError('#family_member_name', 'Family member name should accept only alphabets and spaces.');
                }

                let familyContact = $('#family_contact_no').val().trim();
                if (familyContact) {
                    if (!/^[0-9]+$/.test(familyContact)) {
                        showError('#family_contact_no', 'Family contact number should accept only numeric input.');
                    } else if (familyContact.length < 10 || familyContact.length > 15) {
                        showError('#family_contact_no', 'Family contact number must be between 10 and 15 digits.');
                    }
                }

                // PF Details validation (Required only if PF Applicable is checked)
                if ($('#is_pf_applicable').is(':checked')) {
                    let uanNo = $('#uan_no').val().trim();
                    if (!uanNo) {
                        showError('#uan_no', 'UAN number is required.');
                    }
                    let pfNo = $('#pf_no').val().trim();
                    if (!pfNo) {
                        showError('#pf_no', 'PF number is required.');
                    }
                    let esiNo = $('#esi_no').val().trim();
                    if (!esiNo) {
                        showError('#esi_no', 'ESI number is required.');
                    }
                }

                // Shift timing validation (radio)
                if (!$('input[name="shift_timing"]:checked').val()) {
                    $('#shift_timing-error').text('Please select shift timing.').show();
                    $('input[name="shift_timing"]').addClass('is-invalid');
                    isValid = false;
                }

                // Face validation
                let faceData = $('#face_data').val();
                let uploadFace = $('#upload_face').val();
                let staffId = $('#dw_staff_id').val();
                
                if (!staffId && !faceData && !uploadFace) {
                    showError('#upload_face', 'Please capture or upload a face image.');
                }
                
                if (uploadFace) {
                    let ext = uploadFace.split('.').pop().toLowerCase();
                    if ($.inArray(ext, ['png', 'jpg', 'jpeg']) == -1) {
                        showError('#upload_face', 'Invalid file type. Only JPG, JPEG and PNG are allowed.');
                    }
                }

                if (!isValid) {
                    iziToast.warning({
                        title: 'Validation Warning',
                        message: 'Please fill all required fields correctly.',
                        position: 'topRight'
                    });

                    let firstInvalid = $('.is-invalid').first();
                    if (firstInvalid.length) {
                        firstInvalid[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(function() {
                            firstInvalid.focus();
                        }, 300);
                    }
                    return;
                }

                const formData = new FormData(this);

                // If a face capture was done (not file upload), pass as base64
                // The file upload is already in formData automatically.

                $('#dwSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    url: "{{ route('dailywages.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        iziToast.success({
                            title: 'Success',
                            message: res.message,
                            position: 'topRight',
                            timeout: 700,
                            onClosed: function () {
                                table.ajax.reload();
                                deletedTable.ajax.reload();
                                $('a[href="#dw-list"]').tab('show');
                                resetDwForm();
                            }
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const field in errors) {
                                const el = $('[name="' + field + '"]');
                                el.addClass('is-invalid');
                                $('#' + field + '-error').text(errors[field][0]).show();
                            }
                            iziToast.warning({ title: 'Warning', message: 'Please fix the errors.', position: 'topRight' });
                            
                            let firstInvalid = $('.is-invalid').first();
                            if (firstInvalid.length) {
                                firstInvalid[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                                setTimeout(function() {
                                    firstInvalid.focus();
                                }, 300);
                            }
                        } else {
                            iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Something went wrong.', position: 'topRight' });
                        }
                    },
                    complete: function () {
                        $('#dwSubmitBtn').prop('disabled', false).text(dwEditMode ? 'Update' : 'Submit');
                    }
                });
            });

            // ── View Staff details click handler ───────────────────────────
            $(document).on('click', '.view-dw-staff', function () {
                const id = $(this).data('id');
                
                $.ajax({
                    url: "{{ route('dailywages.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status) {
                            const data = res.data;
                            
                            // Header Info
                            $('#viewStaffTitleName').text(data.staff_name || '-');
                            $('#viewStaffTitleCode').text(data.staff_code || '-');
                            
                            let statusBadge = '';
                            if (data.status === 0) {
                                statusBadge = '<span class="bg-success text-white px-2.5 py-1 rounded-pill" style="font-size: 10px;">Active</span>';
                            } else {
                                statusBadge = '<span class="bg-danger text-white px-2.5 py-1 rounded-pill" style="font-size: 10px;">Inactive</span>';
                            }
                            $('#viewStaffTitleStatus').html(statusBadge);
                            
                            // Avatar
                            let avatarPath = data.upload_face || data.face_image;
                            $('#viewStaffAvatar').attr('src', getStaffImageUrl(avatarPath));
                            
                            // Section 1: Basic Info
                            $('#viewStaffCode').text(data.staff_code || '-');
                            $('#viewStaffName').text(data.staff_name || '-');
                            $('#viewStaffContact').text(data.contact_no || '-');
                            $('#viewStaffAddress').text(data.address || '-');
                            
                            // Section 2: Employment Details
                            let formattedHireDate = '-';
                            if (data.hire_date) {
                                let dateParts = data.hire_date.split('-');
                                if (dateParts.length === 3) {
                                    formattedHireDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
                                }
                            }
                            $('#viewStaffHireDate').text(formattedHireDate);
                            $('#viewStaffRate').text('₹' + data.per_hour_rate + '/hr');
                            
                            // Shift Details
                            if (shiftsMap && shiftsMap[data.shift_timing]) {
                                let shift = shiftsMap[data.shift_timing];
                                $('#viewStaffShiftName').text(shift.name || '-');
                                $('#viewStaffShiftTiming').text(`${shift.time_in} - ${shift.time_out}`);
                            } else {
                                $('#viewStaffShiftName').text('-');
                                $('#viewStaffShiftTiming').text('-');
                            }
                            
                            // Section 3: PF & ESI Details
                            if (data.is_pf_applicable == 1) {
                                $('#viewStaffPfApplicable').html('<span class="badge bg-success-transparent fw-bold px-2 py-1" style="background: #d1fae5; color: #065f46; font-size: 11px;">Yes</span>');
                                $('#viewStaffUan').text(data.uan_no || '-');
                                $('#viewStaffPfNo').text(data.pf_no || '-');
                                $('#viewStaffEsiNo').text(data.esi_no || '-');
                            } else {
                                $('#viewStaffPfApplicable').html('<span class="badge bg-secondary-transparent fw-bold px-2 py-1" style="background: #f1f5f9; color: #475569; font-size: 11px;">No</span>');
                                $('#viewStaffUan').text('-');
                                $('#viewStaffPfNo').text('-');
                                $('#viewStaffEsiNo').text('-');
                            }
                            
                            // Section 4: Family Details
                            $('#viewStaffFamilyName').text(data.family_member_name || '-');
                            $('#viewStaffFamilyContact').text(data.family_contact_no || '-');
                            
                            $('#dailyWagesStaffViewModal').modal('show');
                        } else {
                            iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Failed to load details.', position: 'topRight' });
                    }
                });
            });

        });
    </script>
</body>

</html>
