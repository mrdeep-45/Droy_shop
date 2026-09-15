<!DOCTYPE html>
<html {!! get_html_data() !!}>


<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include/datatable_css_link')
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

                            <div class="card-body">

                                <form id="companyForm" enctype="multipart/form-data" novalidate>
                                    @csrf
                                    <input type="hidden" id="company_id" name="company_id"
                                        value="{{ Session::get('company_id') }}">
                                    <div class="row">
                                        <!-- Required Fields -->
                                        <div class="col-md-6">
                                            <div class="mb-1">
                                                <label for="name" class="form-label mb-0">Company Name
                                                    <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name" name="name" required>
                                                <div class="invalid-feedback">Please provide a company name.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-1">
                                                <label for="email" class="form-label mb-0">Login Email <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    required>
                                                <div class="invalid-feedback">Please provide a valid email.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- Optional Fields -->
                                        <div class="col-md-6">
                                            <div class="mb-1">
                                                <label for="contact_no" class="form-label mb-0">Contact
                                                    Number</label>
                                                <input type="text" class="form-control" id="contact_no"
                                                    name="contact_no" maxlength="20">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-1">
                                                <label for="project_name" class="form-label mb-0">Project
                                                    Name</label>
                                                <input type="text" class="form-control" id="project_name"
                                                    name="project_name" maxlength="100">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- --}}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-1">
                                                <label for="contact_no" class="form-label mb-0">Contact
                                                    Email</label>
                                                <input type="email" class="form-control" id="contact_email"
                                                    name="contact_email">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-1">
                                                <label for="contact_phone" class="form-label mb-0">Quot.
                                                    Contact Number
                                                </label>
                                                <input type="number" class="form-control" id="contact_phone"
                                                    name="contact_phone" maxlength="12">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Logo with Image Validation -->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-1">
                                                <label for="logo" class="form-label mb-0">Company
                                                    Logo</label>
                                                <input type="file" class="form-control" id="logo" name="logo"
                                                    accept="image/*">
                                                <div class="invalid-feedback">Please upload a valid image
                                                    file (JPEG, PNG, JPG, GIF).</div>
                                                <small class="text-muted">Max size: 2MB | Supported formats:
                                                    JPEG, PNG, JPG, GIF</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <img src="" class="img-thumb mt-2" id="logoPreview" alt="Logo Preview"
                                                style="width: 20%; height: auto; display: none;">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-1">
                                                <label for="favicon" class="form-label mb-0">Favicon</label>
                                                <input type="file" name="favicon" id="favicon" class="form-control"
                                                    accept="image/*">
                                                <div class="invalid-feedback">Please upload a valid favicon
                                                    image file.</div>
                                                <small class="text-muted">Max size: 512KB | Supported
                                                    formats: ICO, PNG, JPG, GIF</small>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <img src="" class="img-thumb" id="faviconPreview" alt="Favicon Preview"
                                                style="width: 32px; height: 32px; display: none;">
                                        </div>
                                    </div>
                                    {{-- @if (Session::get('login_type') == 'Company') --}}
                                    <div class="accordion mb-3" id="menuPermissionsAccordion" style="display: none;">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingMenus">
                                                <button class="accordion-button bg-primary text-white" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseMenus"
                                                    aria-expanded="false" aria-controls="collapseMenus">
                                                    Menu Permissions
                                                </button>
                                            </h2>
                                            <div id="collapseMenus" class="accordion-collapse collapse show"
                                                aria-labelledby="headingMenus"
                                                data-bs-parent="#menuPermissionsAccordion">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        @foreach ($menus as $menu)
                                                            <div class="col-md-12 mb-3">
                                                                <div class="card mb-0">
                                                                    <div class="card-header">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input menu-checkbox"
                                                                                type="checkbox"
                                                                                id="menu_{{ $menu->c_menu_id }}"
                                                                                name="menus[]"
                                                                                value="{{ $menu->c_menu_id }}"
                                                                                data-menu-id="{{ $menu->c_menu_id }}">
                                                                            <label class="form-check-label fw-bold"
                                                                                for="menu_{{ $menu->c_menu_id }}">
                                                                                <i class="{{ $menu->menu_icon }} me-2"></i>
                                                                                {{ $menu->menu_title }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    @if ($menu->is_submenu)
                                                                        <div class="card-body">
                                                                            <div class="row">
                                                                                @foreach ($menu->submenus as $submenu)
                                                                                    <div class="col-md-4 mb-2">
                                                                                        <div class="form-check">
                                                                                            <input
                                                                                                class="form-check-input submenu-checkbox "
                                                                                                type="checkbox"
                                                                                                id="submenu_{{ $submenu->c_sub_menu_id }}"
                                                                                                name="submenus[]"
                                                                                                value="{{ $submenu->c_sub_menu_id }}"
                                                                                                data-menu-id="{{ $menu->c_menu_id }}">
                                                                                            <label class="form-check-label"
                                                                                                for="submenu_{{ $submenu->c_sub_menu_id }}">
                                                                                                <i
                                                                                                    class="{{ $submenu->sub_menu_icon }} me-2"></i>
                                                                                                {{ $submenu->sub_menu_title }}
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- @endif --}}
                                    <div class="accordion mb-3" id="companyDetailsAccordion">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingDetails">
                                                <button class="accordion-button bg-primary text-white" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseDetails"
                                                    aria-expanded="false" aria-controls="collapseDetails">
                                                    Additional Company Details
                                                </button>
                                            </h2>
                                            <div id="collapseDetails" class="accordion-collapse collapse show"
                                                aria-labelledby="headingDetails"
                                                data-bs-parent="#companyDetailsAccordion">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6 d-none">
                                                            <div class="mb-1">
                                                                <label for="company_type"
                                                                    class="form-label mb-0">Company
                                                                    Type</label>
                                                                <input type="text" class="form-control"
                                                                    id="company_type" name="company_type"
                                                                    maxlength="50">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-1">
                                                                <label for="address"
                                                                    class="form-label mb-0">Address</label>
                                                                <textarea class="form-control" id="address"
                                                                    name="address" rows="2"></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="mb-1">
                                                                <label for="country"
                                                                    class="form-label mb-0">Country</label>
                                                                <select class="form-control select2" id="country"
                                                                    name="country"></select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-1">
                                                                <label for="state" class="form-label mb-0">State</label>
                                                                <select class="form-control select2" id="state"
                                                                    name="state" disabled></select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-1">
                                                                <label for="city" class="form-label mb-0">City</label>
                                                                <select class="form-control select2" id="city"
                                                                    name="city" disabled></select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="postal_code" class="form-label mb-0">Postal
                                                                    Code</label>
                                                                <input type="text" class="form-control" id="postal_code"
                                                                    name="postal_code" maxlength="20">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="website"
                                                                    class="form-label mb-0">Website</label>
                                                                <input type="url" class="form-control" id="website"
                                                                    name="website" maxlength="100">
                                                                <div class="invalid-feedback"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="gst_no" class="form-label mb-0">GST</label>
                                                                <input type="text" class="form-control" id="gst_no"
                                                                    name="gst_no" maxlength="15">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="mb-1">
                                                                <label for="description"
                                                                    class="form-label mb-0">Description</label>
                                                                <textarea class="form-control" id="description"
                                                                    name="description" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <h6 class="text-primary mb-3 border-bottom pb-2">
                                                        <i class="bx bx-info-circle me-2"></i> Bank Details
                                                    </h6>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="account_holder_name"
                                                                    class="form-label mb-0">Account Holder Name
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    id="account_holder_name" name="account_holder_name">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="bank_name" class="form-label mb-0">Bank Name
                                                                </label>
                                                                <input type="text" class="form-control" id="bank_name"
                                                                    name="bank_name">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="account_number"
                                                                    class="form-label mb-0">Account No.
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    id="account_number" name="account_number">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="ifsc_code" class="form-label mb-0">IFSC Code
                                                                </label>
                                                                <input type="text" class="form-control" id="ifsc_code"
                                                                    name="ifsc_code">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="branch_name" class="form-label mb-0">Branch
                                                                    Name
                                                                </label>
                                                                <input type="text" class="form-control" id="branch_name"
                                                                    name="branch_name">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="account_type"
                                                                    class="form-label mb-0">Account Type
                                                                </label>
                                                                <input type="text" class="form-control"
                                                                    id="account_type" name="account_type">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="upi_id" class="form-label mb-0">UPI Id
                                                                </label>
                                                                <input type="text" class="form-control" id="upi_id"
                                                                    name="upi_id">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="upi_qr_code" class="form-label mb-0">UPI Qr
                                                                    Code
                                                                </label>
                                                                <input type="file" class="form-control" id="upi_qr_code"
                                                                    name="upi_qr_code">
                                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                                    <img src="" class="img-thumb" id="upiQrPreview"
                                                                        alt="UPI Qr Preview"
                                                                        style="max-width: 80px; max-height: 80px; display: none;">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger d-none"
                                                                        id="deleteUpiQrBtn" title="Delete UPI QR Code">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="mb-1">
                                                                <label for="signature" class="form-label mb-0">Signature
                                                                </label>
                                                                <input type="file" class="form-control" id="signature"
                                                                    name="signature">
                                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                                    <img src="" class="img-thumb" id="signaturePreview"
                                                                        alt="Signature Preview"
                                                                        style="max-width: 80px; max-height: 80px; display: none;">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger d-none"
                                                                        id="deleteSignatureBtn"
                                                                        title="Delete Signature">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                                    </div>
                                </form>
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
    @include('include/datatable_js_link')
    <script>
        const img_url = "{{ $img_path }}";
        $(document).ready(function () {
            function handleLocationDropdowns(response) {
                // Initialize country dropdown
                var countrySelect = $('#country');

                // Clear any existing options
                countrySelect.empty();

                // If we have a country ID, fetch and set it
                if (response.country) {
                    $.ajax({
                        url: "{{ route('get.country') }}",
                        data: {
                            fetch_one: true,
                            search: response.country
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data) {
                                var newOption = new Option(data.text, data.id, true, true);
                                countrySelect.append(newOption).trigger('change');

                                // Initialize state dropdown after country is set
                                initializeStateDropdown(response.country, response.state, response
                                    .city);
                            }
                        }
                    });
                }
            }

            function initializeStateDropdown(countryId, stateId, cityId) {
                var stateSelect = $('#state');

                // Clear any existing options
                stateSelect.empty().prop('disabled', !countryId);

                if (!countryId) return;

                // Initialize state dropdown with country ID
                stateSelect.select2({
                    ajax: {
                        url: "{{ route('get.state') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                search: params.term,
                                page: params.page || 1,
                                country_id: countryId
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: (params.page * 10) < data.total_count
                                }
                            };
                        }
                    },
                    placeholder: 'Select State',
                    minimumInputLength: 0
                });

                // If we have a state ID, fetch and set it
                if (stateId) {
                    $.ajax({
                        url: "{{ route('get.state') }}",
                        data: {
                            fetch_one: true,
                            search: stateId,
                            country_id: countryId
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data) {
                                var newOption = new Option(data.text, data.id, true, true);
                                stateSelect.append(newOption).trigger('change');

                                // Initialize city dropdown after state is set
                                initializeCityDropdown(stateId, cityId);
                            }
                        }
                    });
                }
            }

            function initializeCityDropdown(stateId, cityId) {
                var citySelect = $('#city');

                // Clear any existing options
                citySelect.empty().prop('disabled', !stateId);

                if (!stateId) return;

                // Initialize city dropdown with state ID
                citySelect.select2({
                    ajax: {
                        url: "{{ route('get.city') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                search: params.term,
                                page: params.page || 1,
                                state_id: stateId
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: (params.page * 10) < data.total_count
                                }
                            };
                        }
                    },
                    placeholder: 'Select City',
                    minimumInputLength: 0
                });

                // If we have a city ID, fetch and set it
                if (cityId) {
                    $.ajax({
                        url: "{{ route('get.city') }}",
                        data: {
                            fetch_one: true,
                            search: cityId,
                            state_id: stateId
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data) {
                                var newOption = new Option(data.text, data.id, true, true);
                                citySelect.append(newOption).trigger('change');
                            }
                        }
                    });
                }
            }

            $(document).ready(function () {
                var companyId = 1;

                // alert(companyId);

                // Show the new-company tab
                $('[href="#new-company"]').tab('show');

                // Fetch company data
                $.ajax({
                    url: "{{ route('companies.edit', ':id') }}".replace(':id', companyId),
                    type: 'GET',
                    success: function (response) {
                        // Fill form with company data
                        $('#company_id').val(response.company_id);
                        $('#name').val(response.name);
                        $('#email').val(response.email);
                        $('#contact_no').val(response.contact_no);
                        $('#contact_email').val(response.contact_email);
                        $('#contact_phone').val(response.contact_phone);
                        $('#project_name').val(response.project_name);
                        $('#company_type').val(response.company_type);
                        $('#address').val(response.address);
                        $('#city').val(response.city).trigger('change');
                        $('#state').val(response.state).trigger('change');
                        $('#country').val(response.country).trigger('change');

                        $('#postal_code').val(response.postal_code);
                        $('#website').val(response.website);
                        $('#gst_no').val(response.gst_no);
                        $('#description').val(response.description);
                        handleLocationDropdowns(response);
                        // Set logo preview if exists
                        // if (response.logo) {
                        //     var logoUrl = "{{ asset('assets/uploads/company') }}/" + response.logo;
                        //     $('#logoPreview').attr('src', logoUrl).show();
                        // }

                        // bank details
                        $("#account_holder_name").val(response.account_holder_name ?? '');
                        $("#bank_name").val(response.bank_name ?? '');
                        $("#account_number").val(response.account_number ?? '');
                        $("#ifsc_code").val(response.ifsc_code ?? '');
                        $("#branch_name").val(response.branch_name ?? '');
                        $("#account_type").val(response.account_type ?? '');
                        $("#upi_id").val(response.upi_id ?? '');

                        if (response.logo) {
                            var logoUrl = img_url + "/company/" + response.logo;
                            $('#logoPreview').attr('src', logoUrl).show();
                        } else {
                            $('#logoPreview').hide();
                        }

                        if (response.favicon) {
                            var faviconUrl = img_url + "/company/" + response.favicon;
                            $('#faviconPreview').attr('src', faviconUrl).show();
                        } else {
                            $('#faviconPreview').hide();
                        }
                        if (response.upi_qr_code) {
                            $("#upiQrPreview").attr("src", img_url + "/company/" + response.upi_qr_code).show();
                            $("#deleteUpiQrBtn").removeClass('d-none').show();
                        } else {
                            $("#upiQrPreview").hide();
                            $("#deleteUpiQrBtn").addClass('d-none').hide();
                        }

                        if (response.signature) {
                            $("#signaturePreview").attr("src", img_url + "/company/" + response.signature).show();
                            $("#deleteSignatureBtn").removeClass('d-none').show();
                        } else {
                            $("#signaturePreview").hide();
                            $("#deleteSignatureBtn").addClass('d-none').hide();
                        }
                        // Check menu permissions
                        $('.menu-checkbox, .submenu-checkbox').prop('checked', false);

                        if (response.menus && response.menus.length > 0) {
                            response.menus.forEach(function (menuId) {
                                $('#menu_' + menuId).prop('checked', true);
                            });
                        }

                        if (response.submenus && response.submenus.length > 0) {
                            response.submenus.forEach(function (submenuId) {
                                $('#submenu_' + submenuId).prop('checked', true);
                            });
                        }

                        // Change submit button text
                        $('#submitBtn').text('Update');
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });

            $('#companyForm').submit(function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                // Reset validation
                $('.is-invalid').removeClass('is-invalid');
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
                );
                $.ajax({
                    url: "{{ route('companies.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $('#submitBtn').prop('disabled', false).html('Submit');
                        iziToast.success({
                            title: 'Success',
                            message: 'Company created successfully!',
                            position: 'topRight',
                            transitionIn: 'bounceInLeft',
                            transitionOut: 'fadeOutRight',
                            timeout: 1000,
                            onClosed: function () {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        $('#submitBtn').prop('disabled', false).html('Submit');
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key).next('.invalid-feedback').text(value[0]);
                            });
                            // Show validation error notification
                            iziToast.warning({
                                title: 'Secure',
                                message: 'Please fix the form errors',
                                position: 'topRight'
                            });
                        } else {
                            // Show general error notification
                            iziToast.error({
                                title: 'Error',
                                message: 'An error occurred. Please try again.',
                                position: 'topRight'
                            });
                        }
                    }
                });
            });
            // Image validation
            $('#logo').change(function () {
                const file = this.files[0];
                if (file) {
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    const maxSize = 2048; // 2MB in KB
                    if (!validTypes.includes(file.type)) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text(
                            'Invalid file type. Only JPEG, PNG, JPG, GIF are allowed.');
                        $(this).val('');
                        // Show error notification
                        iziToast.warning({
                            title: 'Secure',
                            message: 'Invalid file type. Only images are allowed.',
                            position: 'topRight'
                        });
                    } else if (file.size > maxSize * 1024) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text('File size exceeds 2MB limit.');
                        $(this).val('');
                        // Show error notification
                        iziToast.warning({
                            title: 'Secure',
                            message: 'File size exceeds 2MB limit.',
                            position: 'topRight'
                        });
                    } else {
                        $(this).removeClass('is-invalid');
                        // Optional: Show preview
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#logoPreview').attr('src', e.target.result).show();
                        }
                        reader.readAsDataURL(file);
                    }
                }
            });
            $('#favicon').change(function () {
                const file = this.files[0];
                if (file) {
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    const maxSize = 2048; // 2MB in KB
                    if (!validTypes.includes(file.type)) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text(
                            'Invalid file type. Only JPEG, PNG, JPG, GIF are allowed.');
                        $(this).val('');
                        // Show error notification
                        iziToast.warning({
                            title: 'Secure',
                            message: 'Invalid file type. Only images are allowed.',
                            position: 'topRight'
                        });
                    } else if (file.size > maxSize * 1024) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').text('File size exceeds 2MB limit.');
                        $(this).val('');
                        // Show error notification
                        iziToast.warning({
                            title: 'Secure',
                            message: 'File size exceeds 2MB limit.',
                            position: 'topRight'
                        });
                    } else {
                        $(this).removeClass('is-invalid');
                        // Optional: Show preview
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#faviconPreview').attr('src', e.target.result).show();
                        }
                        reader.readAsDataURL(file);
                    }
                }
            });

            // Delete UPI QR Code
            $('#deleteUpiQrBtn').on('click', function () {
                var btn = $(this);
                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 9999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete the UPI QR Code?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({}, toast, 'button');
                            $.ajax({
                                url: "{{ route('companies.delete_upi_qr') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    if (response.success) {
                                        iziToast.success({
                                            title: 'Success',
                                            message: response.message,
                                            position: 'topRight'
                                        });
                                        $('#upiQrPreview').attr('src', '').hide();
                                        $('#upi_qr_code').val('');
                                        btn.addClass('d-none').hide();
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
                                        message: 'An error occurred while deleting the QR code.',
                                        position: 'topRight'
                                    });
                                }
                            });
                        }],
                        ['<button>NO</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }]
                    ]
                });
            });

            // Delete Signature
            $('#deleteSignatureBtn').on('click', function () {
                var btn = $(this);
                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 9999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete the Signature?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({}, toast, 'button');
                            $.ajax({
                                url: "{{ route('companies.delete_signature') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    if (response.success) {
                                        iziToast.success({
                                            title: 'Success',
                                            message: response.message,
                                            position: 'topRight'
                                        });
                                        $('#signaturePreview').attr('src', '').hide();
                                        $('#signature').val('');
                                        btn.addClass('d-none').hide();
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
                                        message: 'An error occurred while deleting the signature.',
                                        position: 'topRight'
                                    });
                                }
                            });
                        }],
                        ['<button>NO</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }]
                    ]
                });
            });

        });
        $(document).ready(function () {
            // Initialize Select2 for all dropdowns
            $('.select2').select2({
                ajax: {
                    delay: 250, // wait 250ms before triggering the request
                    cache: true
                }
            });

            // Country dropdown
            $('#country').select2({
                ajax: {
                    url: "{{ route('get.country') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term, // search term
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: (params.page * 10) < data.total_count
                            }
                        };
                    }
                },
                placeholder: 'Select Country',
                minimumInputLength: 0
            });

            // When country is selected, load states
            $('#country').on('change', function () {
                var countryId = $(this).val();
                $('#state').val(null).trigger('change').prop('disabled', !countryId);
                $('#city').val(null).trigger('change').prop('disabled', true);

                if (countryId) {
                    // Initialize state dropdown with country ID
                    $('#state').select2({
                        ajax: {
                            url: "{{ route('get.state') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    search: params.term,
                                    page: params.page || 1,
                                    country_id: countryId
                                };
                            },
                            processResults: function (data, params) {
                                params.page = params.page || 1;
                                return {
                                    results: data.results,
                                    pagination: {
                                        more: (params.page * 10) < data.total_count
                                    }
                                };
                            }
                        },
                        placeholder: 'Select State',
                        minimumInputLength: 0
                    });
                }
            });

            // When state is selected, load cities
            $('#state').on('change', function () {
                var stateId = $(this).val();
                $('#city').val(null).trigger('change').prop('disabled', !stateId);

                if (stateId) {
                    // Initialize city dropdown with state ID
                    $('#city').select2({
                        ajax: {
                            url: "{{ route('get.city') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    search: params.term,
                                    page: params.page || 1,
                                    state_id: stateId
                                };
                            },
                            processResults: function (data, params) {
                                params.page = params.page || 1;
                                return {
                                    results: data.results,
                                    pagination: {
                                        more: (params.page * 10) < data.total_count
                                    }
                                };
                            }
                        },
                        placeholder: 'Select City',
                        minimumInputLength: 0
                    });
                }
            });
        });
    </script>
</body>

</html>