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
                                            href="#country-list">
                                                List
                                            </a>
                                        </li>
                                        @endif
                                        @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link"
                                            data-bs-toggle="tab"
                                            role="tab"
                                            href="#new-country">
                                                New Country
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                    <div class="tab-pane fade show active" id="country-list">
                                        <div>
                                            <table id="rawdata" class="table table-bordered  menu-submenu-data"
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:1%;">#</th>
                                                                   <th style="width:20%">Name</th>
                                                            <th style="width:20%">Created By</th>
                                                            <th style="width:20%">Created At</th>
                                                             <th class="text-center" width="23%">Action</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                        </div>
                                    </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                    <div class="tab-pane text-muted px-1" id="new-country" role="tabpanel">
                                         <form id="country_form" enctype="multipart/form-data" novalidate>
                                                @csrf
                                                 <input type="hidden" name="c_id" id="c_id" value="">

                                                <div class="modern-card">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="country_name" class="form-label">Country
                                                                <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="country_name"
                                                                name="country_name" placeholder="Enter Country Name">
                                                            <div class="invalid-feedback"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                                                     <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                                                    <button type="submit" class="btn btn-primary"
                                                        id="submitBtn"><span id="submitText">Submit</span><span id="submitSpinner"
                                                            class="spinner-border spinner-border-sm align-middle d-none"
                                                            role="status" aria-hidden="true"></span></button>
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

        function setFormToEditMode(country_name, c_id) {
            editMode = true;
            $('#country_form').attr('data-mode', 'edit');
            $('#submitBtn').text('Update');
            $('input[name="c_id"]').remove();
            $('#country_form').append(`<input type="hidden" name="c_id" value="${c_id}">`);
            $('#country_name').val(country_name);
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }
         function setFormToCreateMode() {
            editMode = false;
            $('#country_form').attr('data-mode', 'create');
            $('#submitBtn').text('Submit');
            $('input[name="c_id"]').remove();
            $('#country_form')[0].reset();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').hide();
        }

         $(document).ready(function () {
                 var canUpdate = {{ permissions_check('canUpdate') ? 'true' : 'false' }};
            var canDelete = {{ permissions_check('canDelete') ? 'true' : 'false' }};
            var countryTable = $('#rawdata').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('CountryList') }}",
                dom: "<'row mb-2'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end gap-2'fB>>rtip",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Export Excel',
                        className: 'btn btn-success btn-sm',
                        action: function (e, dt, button, config) {
                            var self = this;
                            var oldStart = dt.settings()[0]._iDisplayStart;
                            var totalCount = dt.page.info().recordsDisplay;

                            dt.one('preXhr', function (e, s, data) {
                                data.start = 0;
                                data.length = totalCount;
                            });

                            dt.one('draw', function (e, settings, json, xhr) {
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                                dt.one('preXhr', function (e, s, data) {
                                    data.start = oldStart;
                                    data.length = 10;
                                });
                                setTimeout(dt.ajax.reload, 0);
                            });
                            dt.ajax.reload();
                        },
                        exportOptions: {
                            columns: [0, 1],
                            format: {
                                header: function (data, columnIdx) {
                                    if (columnIdx === 0) {
                                        return 'Sr No.';
                                    }
                                    return data;
                                }
                            }
                        }
                    }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-left', width: "5%" },
                    { data: 'country_name', name: 'country_name', className: 'text-left', width: "40%" },
                    { data: 'created_by', name: 'created_by', className: 'text-center text-nowrap', width: "20%" },
                    { data: 'created_at', name: 'created_at', className: 'text-center', width: "20%" },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center desktop',
                        visible: canUpdate || canDelete,
                        width: "15%"
                    }
                ],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
                    emptyTable: 'No countries found',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total entries)'
                }
            });

             // Focus on input when new country tab is shown
            $('a[href="#new-country"]').on('shown.bs.tab', function () {
                setTimeout(function () {
                    $('#country_name').trigger('focus');
                }, 200);
            });

                  $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                    const target = $(e.target).attr('href');
                    if (target === '#new-country') {
                        if (!editMode) setFormToCreateMode();
                    }
                    if (target === '#country-list') {
                        setFormToCreateMode();
                    }
                });

                 $(document).on('click', '.edit-btn', function () {
                const c_id = $(this).data('id');
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Loading...');
                $.ajax({
                    url: "{{ route('country.edit', ['id' => 'c_id']) }}".replace('c_id', c_id),

                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            setFormToEditMode(response.data.country_name, response.data.c_id);
                            $('a[href="#new-country"]').tab('show');
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
                                'Failed to fetch Country data',
                            position: 'topRight'
                        });
                        setFormToCreateMode();
                    },
                    complete: function () {
                        $('#submitBtn').prop('disabled', false);
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

            $('#country_form').submit(function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            let isUpdate = $('input[name="c_id"]').length > 0 && $('input[name="c_id"]').val().trim() !== "";
            //let isUpdate = $('#c_id').length > 0 && $('#c_id').val() !== "";
            //let isUpdate = $('#c_id').val() !== ""; // check if c_id has value
            let url = isUpdate ? "{{ route('country.update') }}" : "{{ route('countryStore') }}";

            // Client side validation
            let isValid = true;
            let countryName = $('#country_name').val().trim();
            if (!countryName) {
                $('#country_name').addClass('is-invalid');
                $('#country_name').next('.invalid-feedback').text('Country Name is required.').show();
                iziToast.warning({
                    title: 'Warning',
                    message: 'Please enter a Country Name.',
                    position: 'topRight'
                });
                isValid = false;
            } else {
                $('#country_name').removeClass('is-invalid');
                $('#country_name').next('.invalid-feedback').hide();
            }
            if (!isValid) return;

            // Show spinner & disable button
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
                            $('#country_form')[0].reset();
                            $('#c_id').val(''); // clear hidden input on success
                            $('a[href="#country-list"]').tab('show'); // your tab logic
                            countryTable.ajax.reload(null, false); // reload datatable
                        }
                    });
                },
                error: function (xhr) {
                    $('#submitSpinner').addClass('d-none');
                    $('#submitText').text('Submit');
                    $('#submitBtn').prop('disabled', false);

                    if (xhr.status === 422) {
                        let res = xhr.responseJSON;
                        $('#' + res.field).addClass('is-invalid');
                        $('#' + res.field).next('.invalid-feedback').text(res.message);
                        iziToast.warning({ title: 'Validation Error', message: res.message, position: 'topRight' });
                    } else {
                        iziToast.error({ title: 'Error', message: xhr.responseJSON.message, position: 'topRight' });
                    }
                }
            });
        });

         $(document).on('click', '.delete-btn', function () {

        const countryId = $(this).data('id');

        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Confirm',
            message: 'Are you sure you want to delete this Country?',
            position: 'center',
            buttons: [
                ['<button><b>YES</b></button>', function (instance, toast) {

                    instance.hide({}, toast, 'button');

                    $.ajax({
                        url: "{{ route('countryDelete') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            c_id: countryId
                        },
                        success: function (response) {

                            if (response.status === 'success') {

                                iziToast.success({
                                    title: 'Success',
                                    message: response.message,
                                    position: 'topRight'
                                });

                                $('#rawdata').DataTable().ajax.reload(null, false);

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
                                message: xhr.responseJSON?.message || 'Delete failed',
                                position: 'topRight'
                            });
                        }
                    });

                }, true],

                ['<button>NO</button>', function (instance, toast) {
                    instance.hide({}, toast, 'button');
                }]
            ]
        });
    });

          $('#country_form').on('reset', function () {
            setTimeout(() => {
                $('#country_name').removeClass('is-invalid').next('.invalid-feedback').text('');
            }, 0);
        });


         });
    </script>
</body>

</html>
