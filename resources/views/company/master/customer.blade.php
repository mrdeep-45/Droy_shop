<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <style>
        .page-header-breadcrumb {
            position: relative;
            min-height: 0px !important;
            top: 0rem !important;
        }

        /* Select2 dropdown scrollbar */
        .select2-results__options::-webkit-scrollbar {
            width: 8px;
        }

        .select2-results__options::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .select2-results__options::-webkit-scrollbar-thumb {
            background: #7E9A6B;
            border-radius: 10px;
        }

        .select2-results__options::-webkit-scrollbar-thumb:hover {
            background: #6b8759;
        }

        /* Firefox */
        .select2-results__options {
            scrollbar-width: thin;
            scrollbar-color: #7E9A6B #f1f1f1;
        }
    </style>
</head>

<body>
    @include('include.switcher')
    @include('include.loader')
    <div class="page">
        @include('include.header')
        <div class="page-header-breadcrumb d-md-flex d-block align-items-center justify-content-between "></div>
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
                                            <a class="nav-link active" data-bs-toggle="tab" role="tab"
                                                href="#customer-list">
                                                List
                                            </a>
                                        </li>
                                    @endif
                                    @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link" id="customerTab" data-bs-toggle="tab" role="tab"
                                                href="#new-customer">
                                                New Customer
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                        <div class="tab-pane fade show active" id="customer-list">
                                            <div class="table-responsive">
                                                <table id="customerTable" class="table table-bordered align-middle w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>NAME</th>
                                                            <th>CONTACT DETAIL</th>
                                                            <th>LOCATION</th>
                                                            <th>PRODUCTS</th>
                                                            @if(session('login_type') === 'Company')
                                                            <th>CREATED BY</th>
                                                            @endif
                                                            @if(permissions_check('canUpdate') || permissions_check('canDelete'))
                                                            <th>ACTION</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                        <div class="tab-pane text-muted px-1" id="new-customer" role="tabpanel">

                                            <form id="customerForm" enctype="multipart/form-data" novalidate>
                                                <input type="hidden" name="customer_id" id="customer_id">
                                                <div class="row g-4">

                                                    <!-- Company Name -->
                                                    <div class="col-12 col-md-6 col-lg-4 field-group">
                                                        <label class="form-label fw-medium">
                                                            Company Name <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" name="company_name"
                                                            placeholder="Company Name">
                                                        <div class="invalid-feedback">Company Name is required</div>
                                                    </div>

                                                    <!-- Contact Person -->
                                                    <div class="col-6 col-md-6 col-lg-4 field-group">
                                                        <label class="form-label fw-medium">
                                                            Contact Person <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" name="contact_person"
                                                            placeholder="Contact Person" required>
                                                        <div class="invalid-feedback">Contact Person is required</div>
                                                    </div>

                                                    <!-- Email -->
                                                    <div class="col-6 col-md-6 col-lg-4 field-group">
                                                        <label class="form-label fw-medium">
                                                            Email <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="email" class="form-control" name="email"
                                                            placeholder="Email" required>
                                                        <div class="invalid-feedback">Email is required</div>
                                                    </div>

                                                    <!-- Phone -->
                                                    <div class="col-6 col-md-6 col-lg-4 field-group">
                                                        <label class="form-label fw-medium">
                                                            Phone <span class="text-danger">*</span>
                                                        </label>
                                                         <input type="text" class="form-control" name="phone"
                                                             placeholder="Phone" maxlength="15"
                                                             pattern="[0-9()+\- ]*"
                                                             oninput="this.value = this.value.replace(/[^0-9()+\- ]/g, '')"
                                                             required>
                                                        <div class="invalid-feedback">Please enter a valid phone number (max 15 digits).</div>
                                                    </div>
                                                    <!-- GST -->
                                                    <div class="col-6 col-md-6 col-lg-4 field-group">
                                                        <label class="form-label fw-medium">GST No.</label>
                                                        <input type="text" class="form-control" name="gst_no"
                                                            placeholder="24ABCDE1234F1Z5" maxlength="15"
                                                            style="text-transform:uppercase;"
                                                            pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$"
                                                            oninput="this.value=this.value.toUpperCase();">
                                                        <div class="invalid-feedback">
                                                            Please enter a valid GST Number.
                                                        </div>
                                                    </div>
                                                    <!-- PAN -->
                                                    <div class="col-6 col-md-6 col-lg-4 field-group">
                                                        <label class="form-label fw-medium">PAN No.</label>
                                                        <input type="text" class="form-control" name="pan_no"
                                                            placeholder="ABCDE1234F" maxlength="10"
                                                            style="text-transform: uppercase;"
                                                            pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"
                                                            oninput="this.value=this.value.toUpperCase();">
                                                        <div class="invalid-feedback">
                                                            Please enter a valid PAN Number.
                                                        </div>
                                                    </div>
                                                    <!-- City -->
                                                    <div class="col-6 col-md-6 col-lg-4 field-group">
                                                        <label class="form-label fw-medium">City</label>
                                                        <select class="form-select" id="city_id" name="city_id"></select>
                                                        <div class="invalid-feedback"></div>
                                                    </div>

                                                    <!-- State (locked, auto-filled from city) -->
                                                    <div class="col-6 col-md-6 col-lg-4 field-group">
                                                        <label class="form-label fw-medium">State</label>
                                                        <select class="form-select" id="state_id_display">
                                                            <option value="">-- Select State --</option>
                                                        </select>
                                                        <input type="hidden" id="state_id" name="state_id">
                                                        <div class="invalid-feedback"></div>
                                                    </div>

                                                    <!-- Country (locked, auto-filled from city) -->
                                                    <div class="col-6 col-md-6 col-lg-4 field-group">
                                                        <label class="form-label fw-medium">Country</label>
                                                        <select class="form-select" id="country_id_display">
                                                            <option value="">-- Select Country --</option>
                                                        </select>
                                                        <input type="hidden" id="country_id" name="country_id">
                                                        <div class="invalid-feedback"></div>
                                                    </div>

                                                    <!-- Address -->
                                                    <div class="col-12 field-group">
                                                         @include('components.tooltip-help', ['key' => 'customer_address', 'label' => 'Address', 'class' => 'form-label fs-12 fw-bold text-muted text-uppercase mb-1'])
                                                        {{-- <label class="form-label fw-medium">Address</label> --}}
                                                        <textarea class="form-control" rows="2" name="address"
                                                            placeholder="Address"></textarea>
                                                        <div class="invalid-feedback"></div>
                                                    </div>

                                                </div>

                                                <hr class="my-4">

                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" id="resetBtn" class="btn btn-success px-4">Reset</button>
                                                    <button type="submit" class="btn btn-dark px-4">Submit</button>
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
        $(document).ready(function () {

            const $form = $('#customerForm');

            // Automatically switch to New Customer tab if tab=new is in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'new') {
                const customerTab = $('#customerTab');
                if (customerTab.length) {
                    customerTab[0].click();
                }
            }

            /* -----------------------------
             * Select2 initialisation
             * ----------------------------- */
            $('#state_id_display, #country_id_display').select2({
                width: '100%',
                allowClear: true,
                placeholder: '-- Select --'
            });

            $('#customerTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('customer.list') }}",
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'company_name',
                    name: 'c.company_name'
                },
                {
                    data: 'contact_detail',
                    name: 'c.email'
                },
                {
                    data: 'location',
                    name: 'city.city_name'
                },
                {
                    data: 'products',
                    name: 'products',
                    searchable: false,
                    orderable: false
                }
                @if(session('login_type') === 'Company')
                ,{
                    data: 'created',
                    name: 'c.created_by'
                }
                @endif
                @if(permissions_check('canUpdate') || permissions_check('canDelete'))
                ,{
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false
                }
                @endif
                ],
                order: [
                    [1, 'asc']
                ]
            });

            $('#city_id').select2({
                width: '100%',
                placeholder: '-- Select or Type City --',
                createTag: function (params) {
                    var term = $.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                },
                ajax: {
                    url: "{{ route('customer.city.search') }}",
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    }
                }
            });

            /* -----------------------------
             * City -> State / Country auto-fill
             * ----------------------------- */
            function resetLocationFields() {
                $('#state_id_display').html('<option value="">-- Select State --</option>').trigger('change');
                $('#country_id_display').html('<option value="">-- Select Country --</option>').trigger('change');
                $('#state_id, #country_id').val('');
            }

            $('#city_id').on('change', function () {
                const cityId = $(this).val();

                if (!cityId) {
                    resetLocationFields();
                    return;
                }

                $.get("{{ route('customer.city.details', ':id') }}".replace(':id', cityId), function (res) {

                    $('#state_id_display')
                        .html(`<option value="${res.state_id}" selected>${res.state_name}</option>`)
                        .trigger('change');

                    $('#country_id_display')
                        .html(`<option value="${res.c_id}" selected>${res.country_name}</option>`)
                        .trigger('change');

                    $('#state_id').val(res.state_id);
                    $('#country_id').val(res.c_id);

                }).fail(function () {
                    resetLocationFields();
                });
            });

            // $('#city_id').on('select2:select', function(e) {

            //     var data = e.params.data;

            //     if (data.newTag) {

            //         $.get("#", function(res) {

            //             $('#state_id_display').empty();
            //             $('#country_id_display').empty();

            //             $('#state_id_display').append('<option value="">Select State</option>');
            //             $('#country_id_display').append('<option value="">Select Country</option>');

            //             $.each(res.states, function(i, item) {
            //                 $('#state_id_display').append(
            //                     `<option value="${item.id}">${item.text}</option>`
            //                 );
            //             });

            //             $.each(res.countries, function(i, item) {
            //                 $('#country_id_display').append(
            //                     `<option value="${item.id}">${item.text}</option>`
            //                 );
            //             });

            //             $('#state_id_display').prop('disabled', false).trigger('change');
            //             $('#country_id_display').prop('disabled', false).trigger('change');
            //         });

            //     } else {

            //         $.get("{{ route('customer.city.details', ':id') }}"
            //             .replace(':id', data.id),
            //             function(res) {

            //                 $('#state_id_display')
            //                     .html(
            //                         `<option value="${res.state_id}" selected>${res.state_name}</option>`
            //                     );

            //                 $('#country_id_display')
            //                     .html(
            //                         `<option value="${res.c_id}" selected>${res.country_name}</option>`
            //                     );

            //                 $('#state_id').val(res.state_id);
            //                 $('#country_id').val(res.c_id);

            //             });

            //     }

            // });



            function clearValidation() {
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').text('');
                $form.find('.select2-selection').removeClass('is-invalid');
            }

            function showValidationErrors(errors) {
                $.each(errors, function (field, messages) {

                    const $input = $form.find('[name="' + field + '"]');
                    $input.addClass('is-invalid');

                    $input.closest('.field-group').find('.invalid-feedback').text(messages[0]);

                    if ($input.hasClass('select2-hidden-accessible')) {
                        $input.next('.select2').find('.select2-selection').addClass('is-invalid');
                    }
                });
            }

            function resetForm() {
                $form.trigger('reset');
                $('#customer_id').val('');
                $('#city_id').val(null).trigger('change');
                resetLocationFields();
                clearValidation();
                $('button[type="submit"]').text('Submit');
                $('#customerTab').text('New Customer');
            }

            $('#resetBtn').on('click', function () {
                resetForm();
            });

            /* -----------------------------
             * Form Submit
             * ----------------------------- */
            $form.on('submit', function (e) {

                e.preventDefault();

                const $submitBtn = $form.find('button[type="submit"]');
                const originalText = $submitBtn.html();

                clearValidation();

                $.ajax({
                    url: "{{ route('customer.store') }}",
                    type: "POST",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    beforeSend: function () {
                        $submitBtn.prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Submitting...
                `);
                    },

                    success: function (response) {

                        $submitBtn.prop('disabled', false).html(originalText);

                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topRight',
                            timeout: 2000
                        });

                        resetForm();


                        $('a[href="#customer-list"]').tab('show');

                        $('#customerTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (xhr) {

                        $submitBtn.prop('disabled', false).html(originalText);

                        if (xhr.status === 422) {
                            showValidationErrors(xhr.responseJSON.errors);
                            return;
                        }

                        iziToast.error({
                            title: 'Error',
                            message: 'Something went wrong.',
                            position: 'topRight',
                            timeout: 2000
                        });

                        console.log(xhr.responseText);
                    }
                });

            });

            $(document).on('click', '.edit-btn', function () {

                let id = $(this).data('id');

                $.get("{{ route('customer.edit', ':id') }}".replace(':id', id), function (res) {

                    $('#customer_id').val(res.customer_id);

                    $('[name="company_name"]').val(res.company_name);
                    $('[name="contact_person"]').val(res.contact_person);
                    $('[name="email"]').val(res.email);
                    $('[name="phone"]').val(res.phone);
                    $('[name="gst_no"]').val(res.gst_no);
                    $('[name="pan_no"]').val(res.pan_no);
                    $('[name="address"]').val(res.address);

                    // City
                    var cityOption = new Option(res.city_name, res.city_id, true, true);
                    $('#city_id').append(cityOption).trigger('change');

                    // State
                    $('#state_id').val(res.state_id);
                    $('#state_id_display')
                        .html(`<option value="${res.state_id}" selected>${res.state_name}</option>`)
                        .trigger('change');

                    // Country
                    $('#country_id').val(res.country_id);
                    $('#country_id_display')
                        .html(
                            `<option value="${res.country_id}" selected>${res.country_name}</option>`
                        )
                        .trigger('change');

                    // Change button text
                    $('button[type="submit"]').text('Update');
                    $('#customerTab').text('Update Customer');

                    bootstrap.Tab.getOrCreateInstance(
                        document.querySelector('#customerTab')
                    ).show();
                    // Open form tab
                    bootstrap.Tab.getOrCreateInstance(
                        document.querySelector('a[href="#new-customer"]')
                    ).show();
                });
            });
            $(document).on('click', '.delete-btn', function () {

                let customer_id = $(this).data('id');

                iziToast.question({
                    timeout: false,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'deleteCustomer',
                    zindex: 9999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete this customer?',
                    position: 'center',
                    buttons: [
                        [
                            '<button><b>Yes, Delete</b></button>',
                            function (instance, toast) {

                                $.ajax({
                                    url: "{{ route('customer.delete') }}",
                                    type: "POST",
                                    data: {
                                        customer_id: customer_id,
                                        _token: $('meta[name="csrf-token"]').attr(
                                            'content')
                                    },
                                    success: function (response) {

                                        instance.hide({
                                            transitionOut: 'fadeOut'
                                        }, toast, 'button');

                                        iziToast.success({
                                            title: 'Success',
                                            message: response.message,
                                            position: 'topRight'
                                        });

                                        $('#customerTable').DataTable().ajax.reload(
                                            null, false);
                                    },
                                    error: function () {

                                        instance.hide({
                                            transitionOut: 'fadeOut'
                                        }, toast, 'button');

                                        iziToast.error({
                                            title: 'Error',
                                            message: 'Something went wrong.',
                                            position: 'topRight'
                                        });
                                    }
                                });

                            },
                            true
                        ],
                        [
                            '<button>Cancel</button>',
                            function (instance, toast) {

                                instance.hide({
                                    transitionOut: 'fadeOut'
                                }, toast, 'button');

                            }
                        ]
                    ]
                });

            });

            function resetForm() {

                $form.trigger('reset');

                $('#customer_id').val('');

                $('#city_id').empty().trigger('change');

                $('#customerTab').text('New Customer');
                $('button[type="submit"]').text('Submit');
                resetLocationFields();

                clearValidation();

                $('button[type="submit"]').text('Submit');
            }

            // Reset the form back to "New Customer" when clicking the List tab or switching tabs
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                // e.target is the newly active tab
                var activeTabHash = $(e.target).attr('href');
                
                // If the user navigates back to the list tab, clear/reset the form
                if (activeTabHash === '#customer-list') {
                    resetForm();
                }
            });

        });
    </script>
</body>

</html>