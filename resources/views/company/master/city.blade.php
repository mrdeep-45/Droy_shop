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
                                            href="#company-list">
                                                List
                                            </a>
                                        </li>
                                        @endif
                                        @if (permissions_check('canCreate'))
                                        <li class="nav-item">
                                            <a class="nav-link"
                                            data-bs-toggle="tab"
                                            role="tab" id="companyTabLabel1"
                                            href="#new-city">
                                                New City
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">

                                    @if (permissions_check('canView'))
                                    <div class="tab-pane fade show active" id="company-list">
                                        <div class="table-responsive">
                                            <table id="CityData" class="table table-bordered  menu-submenu-data"
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center" style="width:1%;">#</th>
                                                                  <th class="text-center" style="width:20%;">Country</th>
                                                            <th class="text-center" style="width:20%;">State</th>
                                                            <th class="text-center" style="width:15%;">City</th>
                                                            <th class="text-center" style="width:15%;">Created By</th>
                                                            <th class="text-center" style="width:15%;">Created At</th>
                                                            <th class="text-center" style="width:14%;">Action </th>
                                                                </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                        </div>
                                    </div>
                                    @endif

                                    @if (permissions_check('canCreate'))
                                    <div class="tab-pane text-muted px-1" id="new-city" role="tabpanel">
                                         <form id="city_form" enctype="multipart/form-data" novalidate>
                                                @csrf
                                                 <input type="hidden" name="c_id" value="{{ $countryId ?? '' }}">

                                                <div class="modern-card">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Country <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select" id="city_country_id" name="c_id"
                                                                required>
                                                                <option value="">Select Country
                                                                </option>
                                                                @foreach ($countrys as $country)
                                                                    <option value="{{ $country->c_id }}">
                                                                        {{ $country->country_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback" id="stockGroupError">
                                                            </div>
                                                        </div>
                                                     <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">State <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select" id="city_state_id" name="state_id"
                                                                required>
                                                                <option value="">Select State
                                                                </option>
                                                                @foreach ($states as $state)
                                                                    <option value="{{ $state->state_id }}">
                                                                        {{ $state->state_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div class="invalid-feedback" id="stockGroupError">
                                                            </div>
                                                        </div>
                                                    </div>
                                                     <div class="col-md-6" id="CityContainer">
                                                        <div class="mb-3 row category-row">
                                                            <div class="col-10">
                                                                <label class="form-label">City Name
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" name="city_name[]"
                                                                    placeholder="Enter City Name" required>
                                                                <div class="invalid-feedback certificateError">
                                                                </div>
                                                            </div>

                                                            <div class="col-2 d-flex align-items-end">
                                                                <button type="button"
                                                                    class="btn btn-success add-category-btn">+</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    </div>
                                                </div>
                                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
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
        $(document).ready(function () {
            var canUpdate = {{ permissions_check('canUpdate') ? 'true' : 'false' }};
            var canDelete = {{ permissions_check('canDelete') ? 'true' : 'false' }};

            $('#CityData').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('city.list') }}",
                    type: "GET",
                    dataSrc: 'data'
                }, dom: "<'row mb-2'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end gap-2'fB>>rtip"
                , buttons: [
                    {
                        extend: 'colvis',
                        text: 'Visible Columns',
                        className: 'btn btn-teal btn-sm'
                    },
                    {
                        text: 'Reset Active',
                        className: 'btn btn-danger btn-sm',
                        action: function (e, dt, node, config) {
                            dt.state.clear();
                            window.location.reload();
                        }
                    },
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
                            columns: ':visible',
                            orthogonal: 'export',
                            format: {
                                header: function (data, columnIdx) {
                                    if (columnIdx === 0) return 'Sr No.';
                                    return data;
                                },
                                body: function (data, row, column, node) {
                                    if (data === null || data === undefined) data = '';
                                    if (typeof data === 'object' && $(data).html) {
                                        data = $(data).html();
                                    }
                                    data = data.toString();

                                    return data
                                        .replace(/<br\s*\/?>/gi, "\n")
                                        .replace(/<\/li>/gi, "\n")
                                        .replace(/<[^>]+>/g, '')
                                        .trim();
                                }
                            }
                        },
                        customize: function (xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('row c', sheet).each(function () {
                                var text = $('is t', this).text();
                                if (text && text.includes('\n')) {
                                    $(this).attr('s', '55');
                                }
                            });
                        }
                    }
                ],
                columns: [
                    {
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1,
                        className: 'text-left',
                        width: '5%'
                    },
                    {
                        data: 'country_name',
                        className: 'text-left',
                        width: '15%'
                    },
                    {
                        data: 'state_name',
                        className: 'text-left',
                        width: '15%'
                    },
                    {
                        data: 'city_name',
                        render: (names) => {
                            const sorted = names.slice().sort((a, b) => a.localeCompare(b));
                            return `<ul class="mb-0 ps-3 list-unstyled">${sorted.map(n => `<li>• ${n}</li>`).join('')}</ul>`;
                        },
                        className: 'text-left',
                        width: '20%'
                    },
                    {
                        data: 'created_by',
                        className: 'text-center',
                        width: '15%'
                    },
                    {
                        data: 'created_at',
                        className: 'text-center',
                        width: '15%'
                    },
                    {
                        data: null,
                        className: 'text-center',
                        visible: canUpdate || canDelete,
                        width: '15%',
                        render: (data, type, row) => {
                            let buttons = `<div class="d-flex justify-content-center gap-1 text-nowrap">`;
                            
                            if (canUpdate) {
                                buttons += `
                                    <button class="btn btn-sm btn-primary edit-city" data-cid="${row.c_id}" data-sid="${row.state_id}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>`;
                            }

                            if (canDelete) {
                                if (row.is_used) {
                                    buttons += `
                                        <button class="btn btn-sm btn-danger" disabled style="opacity: 0.5; cursor: not-allowed;" title="Cannot delete: A City in this group is in use">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>`;
                                } else {
                                    buttons += `
                                        <button class="btn btn-sm btn-danger delete-city" data-cid="${row.c_id}" data-sid="${row.state_id}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>`;
                                }
                            }

                            buttons += `</div>`;
                            return buttons;
                        }
                    }
                ],
                responsive: true,
                pageLength: 10
            });
        });



        $('#city_country_id').select2({
            width: '100%',
            tags: true,
            placeholder: 'Select or type a Country',
            allowClear: true,
            language: {
                noResults: function () {
                    return "No results found";
                },
                inputTooShort: function () {
                    return "Type to search or add";
                }
            },
            createTag: function (params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: term,
                    text: term,
                    isNew: true
                };
            },
            templateResult: function (data) {
                if (data.isNew) {
                    return $('<span>' + data.text + ' <span class="text-muted">(New)</span></span>');
                }
                return data.text;
            }
        });
        $('#city_state_id').select2({
            width: '100%',
            tags: true,
            placeholder: 'Select or type a State',
            allowClear: true,
            language: {
                noResults: function () {
                    return "No results found";
                },
                inputTooShort: function () {
                    return "Type to search or add";
                }
            },
            createTag: function (params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: term,
                    text: term,
                    isNew: true
                };
            },
            templateResult: function (data) {
                if (data.isNew) {
                    return $('<span>' + data.text + ' <span class="text-muted">(New)</span></span>');
                }
                return data.text;
            }
        });



        // Add category row
        $(document).on('click', '.add-category-btn', function (e) {
            e.preventDefault();
            const newRow = `
            <div class="mb-3 row category-row">
                
                <div class="col-10">
                    
                    <input type="text" class="form-control" name="city_name[]" placeholder="Enter City Name" required>
                    <div class="invalid-feedback">Please enter a City name</div>
                </div>
                

                <div class="col-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-category-btn">−</button>
                </div>
            </div>
        `;
            $('#CityContainer').append(newRow);
        });

        $(document).on('click', '.edit-city', function () {
            const countryId = $(this).data('cid');
            const stateId = $(this).data('sid');

            $('a[href="#new-city"]').tab('show');
            $('#companyTabLabel1').text('Edit Cities');

            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');

            $.ajax({
                url: "{{ route('city.edit') }}",
                type: "GET",
                data: {
                    c_id: countryId,
                    state_id: stateId
                },
                dataType: "json",
                success: function (response) {
                    if (response.status === 'success') {
                        const cities = response.data.cities;
                        console.log('Cities:', cities);

                        $('#city_form')[0].reset();
                        $('#CityContainer').html('');
                        $('#city_form').attr('data-mode', 'edit');

                        cities.forEach((city, index) => {
                            const rowHtml = `
                        <div class="mb-3 row city-row">
                            <input type="hidden" name="city_id[]" value="${city.city_id}">
                            <div class="col-10">
                                <input type="text" class="form-control" name="city_name[]" value="${city.city_name}" required placeholder="City Name">
                                <div class="invalid-feedback cityError"></div>
                            </div>
                                                        <div class="col-2 d-flex align-items-end">
                                ${index === 0 ?
                                    `<button type="button" class="btn btn-success add-category-btn">+</button>` :
                                    (city.is_used ? `<button type="button" class="btn btn-danger" disabled style="opacity:0.5; cursor:not-allowed;" title="Cannot delete: City is in use">−</button>` : `<button type="button" class="btn btn-danger remove-category-btn" data-id="${city.city_id}">−</button>`)
                                }
                            </div>

                        </div>`;
                            $('#CityContainer').append(rowHtml);
                        });

                        // Pre-select country and state in dropdowns
                        let $countrySelect = $('#city_country_id');
                        $countrySelect.off('change', countryChangeHandler);
                        $countrySelect.val(response.data.c_id).trigger('change');
                        
                        if ($('#city_state_id').find("option[value='" + response.data.state_id + "']").length) {
                            $('#city_state_id').val(response.data.state_id).trigger('change');
                        } else {
                            let stateOption = new Option(response.data.state_name, response.data.state_id, true, true);
                            $('#city_state_id').append(stateOption).trigger('change');
                        }

                        $countrySelect.on('change', countryChangeHandler);

                        // Hidden inputs if needed
                        $('input[name="c_id"]').remove();
                        $('input[name="state_id"]').remove();
                        $('#city_form').append(`<input type="hidden" name="c_id" value="${response.data.c_id}">`);
                        $('#city_form').append(`<input type="hidden" name="state_id" value="${response.data.state_id}">`);

                        $('#submitBtn').text('Update');
                    } else {
                        iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                    }
                },
                error: function (xhr) {
                    iziToast.error({
                        title: 'Error',
                        message: xhr.responseJSON?.message || 'Failed to fetch cities',
                        position: 'topRight'
                    });
                },
                complete: function () {
                    $('#submitBtn').prop('disabled', false);
                }
            });
        });




        // Remove/Delete category row
        $(document).on('click', '.remove-category-btn', function (e) {
            e.preventDefault();
            const button = $(this);
            const categoryRow = button.closest('.category-row');
            const cityId = button.data('id');

            if (!cityId) {
                // Newly added row, just remove it from the DOM
                if ($('.category-row').length > 1) {
                    categoryRow.remove();
                } else {
                    iziToast.warning({
                        title: 'Warning',
                        message: 'You need at least one city',
                        position: 'topRight'
                    });
                }
            } else {
                // Existing row in DB, confirm before deleting
                iziToast.question({
                    timeout: 10000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'delete-city-confirm',
                    zindex: 999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete this city?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');

                            $.ajax({
                                url: "{{ route('city.destroy', ':id') }}".replace(':id', cityId),
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                dataType: "json",
                                success: function (response) {
                                    if (response.status === 'success') {
                                        iziToast.success({
                                            title: 'Deleted',
                                            message: response.message,
                                            position: 'topRight'
                                        });
                                        if ($('.category-row').length > 1) {
                                            categoryRow.remove();
                                        } else {
                                            categoryRow.find('input[name="city_name[]"]').val('');
                                            categoryRow.find('input[name="city_id[]"]').remove();
                                            button.remove();
                                        }
                                        $('#CityData').DataTable().ajax.reload(null, false);
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
                                        message: xhr.responseJSON?.message || 'Failed to delete city',
                                        position: 'topRight'
                                    });
                                }
                            });
                        }, true],
                        ['<button>NO</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }]
                    ]
                });
            }
        });


        $(document).on('click', '.delete-city', function () {
            const countryId = $(this).data('cid');
            const stateId = $(this).data('sid');

            iziToast.question({
                timeout: 20000,
                close: false,
                overlay: true,
                displayMode: 'once',
                id: 'delete-cities-confirm',
                zindex: 999,
                title: 'Confirm',
                message: 'Are you sure you want to delete all cities under this country and state?',
                position: 'center',
                buttons: [
                    ['<button><b>YES</b></button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');

                        $.ajax({
                            url: "{{ route('city.deleteByCountryAndState') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                c_id: countryId,
                                state_id: stateId
                            },
                            dataType: "json",
                            success: function (response) {
                                if (response.status === 'success') {
                                    iziToast.success({
                                        title: 'Success',
                                        message: response.message,
                                        position: 'topRight'
                                    });
                                    $('#CityData').DataTable().ajax.reload(null, false);
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
                                    message: xhr.responseJSON?.message || 'Failed to delete cities',
                                    position: 'topRight'
                                });
                            }
                        });
                    }, true],
                    ['<button>NO</button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }]
                ]
            });
        });


        // Handle tab switching
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('href');

            if (target === '#new-city') {
                // When switching to form tab, check if we're in edit mode
                if ($('#city_form').attr('data-mode') !== 'edit') {
                    resetForm();
                    $('#companyTabLabel1').text('New City');
                    $('#submitBtn').text('Submit');
                }
            }
        });

        // Function to reset form
        function resetForm() {
            $('#city_form')[0].reset();
            $('#city_form').removeAttr('data-mode');
            $('input[name="group_id"]').remove();
            $('.invalid-feedback').hide();
            $('.is-invalid').removeClass('is-invalid');

            // Reset category container to initial state
            $('#CityContainer').html(`
        <div class="mb-3 row category-row">
            <div class="col-10">
                <label class="form-label">City Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="city_name[]" placeholder="Enter City Name" required>
                <div class="invalid-feedback certificateError"></div>
            </div>
           
            <div class="col-2 d-flex align-items-end">
                <button type="button" class="btn btn-success add-category-btn">+</button>
            </div>
        </div>
    `);

            // Reset Select2
            let $countrySelect = $('#city_country_id');
            $countrySelect.off('change', countryChangeHandler);
            $countrySelect.val(null).trigger('change');
            $('#city_state_id').empty().append('<option value="">Select State</option>').val(null).trigger('change');
            $countrySelect.on('change', countryChangeHandler);
        }
        $('a[href="#company-list"]').on('click', function () {
            // Reset the form to "New City" state
            resetForm();
            $('#companyTabLabel1').text('New City');
            $('#submitBtn').text('Submit');
        });

        function countryChangeHandler() {
            var countryId = $(this).val();
            var $stateSelect = $('#city_state_id');
            
            if (countryId && $.isNumeric(countryId)) {
                $.ajax({
                    url: "{{ url('/states') }}/" + countryId,
                    type: "GET",
                    success: function(response) {
                        $stateSelect.empty().append('<option value="">Select State</option>');
                        if (response.success) {
                            $.each(response.data, function(key, state) {
                                $stateSelect.append('<option value="'+ state.id +'">'+ state.state_name +'</option>');
                            });
                        }
                    }
                });
            } else {
                $stateSelect.empty().append('<option value="">Select State</option>');
            }
        }
        
        $('#city_country_id').on('change', countryChangeHandler);




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

        // Handle create/edit form submit
        $('#city_form').submit(function (e) {
            e.preventDefault();

            // Client side validation
            let isValid = true;
            
            let country = $('#city_country_id').val();
            if (!country) {
                $('#city_country_id').next('.select2-container').addClass('is-invalid');
                iziToast.warning({ title: 'Warning', message: 'Please select a Country.', position: 'topRight' });
                isValid = false;
            } else {
                $('#city_country_id').next('.select2-container').removeClass('is-invalid');
            }

            if (isValid) {
                let state = $('#city_state_id').val();
                if (!state) {
                    $('#city_state_id').next('.select2-container').addClass('is-invalid');
                    iziToast.warning({ title: 'Warning', message: 'Please select a State.', position: 'topRight' });
                    isValid = false;
                } else {
                    $('#city_state_id').next('.select2-container').removeClass('is-invalid');
                }
            }

            if (isValid) {
                $('input[name="city_name[]"]').each(function() {
                    if (!$(this).val().trim()) {
                        $(this).addClass('is-invalid');
                        iziToast.warning({ title: 'Warning', message: 'Please enter a City Name.', position: 'topRight' });
                        isValid = false;
                        return false; // break loop
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
            }

            if (!isValid) return;

            const formData = new FormData(this);

            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

            $.ajax({
                url: "{{ route('city.store') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success(resp) {
                    iziToast.success({ title: 'Success', message: resp.message, position: 'topRight' });
                    $('#city_form')[0].reset();
                    $('#CityContainer').html('');
                    $('#submitBtn').text('Submit').prop('disabled', false);

                    $('a[href="#company-list"]').tab('show'); // Make sure this is the correct ID
                    $('#CityData').DataTable().ajax.reload(null, false);
                },
                error(xhr) {
                    $('#submitBtn').text('Submit').prop('disabled', false);
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            const idx = key.split('.')[1];
                            $('input[name="city_name[]"]').eq(idx)
                                .addClass('is-invalid')
                                .next('.invalid-feedback').text(errors[key]).show();
                        }
                    } else {
                        iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Failed to save', position: 'topRight' });
                    }
                }
            });
        });




    </script>
</body>

</html>
