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
                                            href="#new-state">
                                                New State
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
                                            <table id="StateData" class="table table-bordered  menu-submenu-data"
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center" style="width:1%;">#</th>
                                                                   <th class="text-center" style="width:20%;">Country</th>
                                                            <th class="text-center" style="width:20%;">State</th>
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
                                    <div class="tab-pane text-muted px-1" id="new-state" role="tabpanel">
                                         <form id="state_form" enctype="multipart/form-data" novalidate>
                                                @csrf
                                                <input type="hidden" name="c_id" value="{{ $countryId ?? '' }}">

                                                <div class="modern-card">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Country <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select" id="c_id" name="c_id" required>
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
                                                        <div class="col-md-6" id="StateContainer">
                                                            <div class="mb-3 row category-row">
                                                                <div class="col-5">
                                                                    <label class="form-label">State Name
                                                                        <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" name="state_name[]"
                                                                        placeholder="Enter State Name" required>
                                                                    <div class="invalid-feedback certificateError">
                                                                    </div>
                                                                </div>
                                                                <div class="col-5">
                                                                    <label class="form-label">State Code</label>
                                                                    <input type="text" name="state_code[]" class="form-control"
                                                                        placeholder="State Code">
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

            $('#StateData').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('state.list') }}",
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
                                    if (columnIdx === 0) {
                                        return 'Sr No.';
                                    }
                                    return data;
                                },
                                body: function (data, row, column, node) {
                                    if (data === null || data === undefined) data = '';


                                    let strData = typeof data === 'object' && $(data).html ? $(data).html() : data.toString();

                                    return strData
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
                ]
                , columns: [
                    {
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1,
                        className: 'text-left',
                        width: '5%'
                    },
                    {
                        data: 'country_name',
                        className: 'text-left',
                        width: '35%'
                    },
                    {
                        data: 'state_name',
                        width: '30%',
                        render: (names, type, row) => {
                            if (!names) return '';
                            const stateCodes = row.state_code || [];

                            // Combine each state name with its corresponding code
                            const combined = names.map((name, i) => {
                                const code = stateCodes[i] || '';
                                return `${name} (${code})`;
                            });

                            if (type === 'display') {
                                // Sort alphabetically by state name (or combined string)
                                const sorted = combined.slice().sort((a, b) => a.localeCompare(b));
                                return `<ul class="mb-0 ps-3 list-unstyled">${sorted.map(n => `<li>• ${n}</li>`).join('')}</ul>`;
                            }
                            
                            return combined.join(' ');
                        },
                        className: 'text-left'
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
                                    <button class="btn btn-sm btn-primary edit-state" data-id="${row.c_id}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>`;
                            }

                            if (canDelete) {
                                if (row.is_used) {
                                    buttons += `
                                        <button class="btn btn-sm btn-danger" disabled style="opacity: 0.5; cursor: not-allowed;" title="Cannot delete: A State in this group is in use">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>`;
                                } else {
                                    buttons += `
                                        <button class="btn btn-sm btn-danger delete-state" data-cid="${row.c_id}">
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


        $('#c_id').select2({
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

        // Add category row
        $(document).on('click', '.add-category-btn', function (e) {
            e.preventDefault();
            const newRow = `
            <div class="mb-3 row category-row">
                
                <div class="col-5 mb-2">
                    <input type="text" class="form-control" name="state_name[]" placeholder="Enter State Name" required>
                    <div class="invalid-feedback">Please enter a State name</div>
                </div>
                <div class="col-5 mb-2">
                    <input type="text" class="form-control" name="state_code[]" placeholder="Enter State Code">
                </div>

                <div class="col-2 d-flex align-items-end mb-2">
                    <button type="button" class="btn btn-danger remove-category-btn">−</button>
                </div>

            </div>
        `;
            $('#StateContainer').append(newRow);
        });

        // Remove/Delete category row
        $(document).on('click', '.remove-category-btn', function (e) {
            e.preventDefault();
            const button = $(this);
            const categoryRow = button.closest('.category-row');
            const stateId = button.data('id');

            if (!stateId) {
                // Newly added row, just remove it from the DOM
                if ($('.category-row').length > 1) {
                    categoryRow.remove();
                } else {
                    iziToast.warning({
                        title: 'Warning',
                        message: 'You need at least one category',
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
                    id: 'delete-state-confirm',
                    zindex: 999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete this state?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');

                            $.ajax({
                                url: "{{ route('state.destroy', ':id') }}".replace(':id', stateId),
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
                                            categoryRow.find('input[name="state_name[]"]').val('');
                                            categoryRow.find('input[name="state_code[]"]').val('');
                                            categoryRow.find('input[name="state_id[]"]').remove();
                                            button.remove();
                                        }
                                        $('#StateData').DataTable().ajax.reload(null, false);
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
                                        message: xhr.responseJSON?.message || 'Failed to delete state',
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



        $(document).on('click', '.edit-state', function () {
            const countryId = $(this).data('id');

            $('a[href="#new-state"]').tab('show');
            $('#companyTabLabel1').text('Edit States');

            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');

            $.ajax({
                url: "{{ route('state.edit', ':id') }}".replace(':id', countryId),
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.status === 'success') {
                        const states = response.data.states;
                        console.log('States from server:', states);

                        // Reset form and clear old inputs
                        $('#state_form')[0].reset();
                        $('#StateContainer').html('');
                        $('#state_form').attr('data-mode', 'edit');

                        // Populate state inputs
                        states.forEach((state, index) => {
                            console.log('Appending state:', state);
                            const rowHtml = `
    <div class="mb-3 row category-row">
        <input type="hidden" name="state_id[]" value="${state.state_id}">
        <div class="col-5">
            <input type="text" class="form-control" name="state_name[]" value="${state.state_name}" required placeholder="State Name">
            <div class="invalid-feedback certificateError"></div>
        </div>
        <div class="col-5">
            <input type="text" class="form-control" name="state_code[]" value="${state.state_code ?? ''}" placeholder="State Code">
            <div class="invalid-feedback certificateError"></div>
        </div>
        <div class="col-2 d-flex align-items-end">
            ${index === 0 ?
                                    `<button type="button" class="btn btn-success add-category-btn">+</button>` :
                                    `<button type="button" class="btn btn-danger remove-category-btn" data-id="${state.state_id}">−</button>`
                                }
        </div>
    </div>`;

                            $('#StateContainer').append(rowHtml);
                        });

                        // Set country dropdown
                        $('#c_id').val(response.data.c_id).trigger('change.select2'); // use .trigger('change')

                        // Remove old hidden input for c_id if any, then add fresh one
                        $('input[name="c_id"]').remove();
                        $('#state_form').append(
                            `<input type="hidden" name="c_id" value="${response.data.c_id}">`
                        );

                        $('#submitBtn').text('Update');
                    } else {
                        iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                    }
                },
                error: function (xhr) {
                    iziToast.error({
                        title: 'Error',
                        message: xhr.responseJSON?.message || 'Failed to fetch states',
                        position: 'topRight'
                    });
                },
                complete: function () {
                    $('#submitBtn').prop('disabled', false);
                }
            });
        });


        // Category remove handler combined above



        $(document).on('click', '.delete-state', function () {
            const countryId = $(this).data('cid'); // Assuming button has data-cid attribute

            iziToast.question({
                timeout: 20000,
                close: false,
                overlay: true,
                displayMode: 'once',
                id: 'delete-states-confirm',
                zindex: 999,
                title: 'Confirm',
                message: 'Are you sure you want to delete all states under this country?',
                position: 'center',
                buttons: [
                    ['<button><b>YES</b></button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');

                        $.ajax({
                            url: "{{ route('state.deleteByCountry', ':id') }}".replace(':id', countryId),
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            dataType: "json",
                            success: function (response) {
                                if (response.status === 'success') {
                                    iziToast.success({
                                        title: 'Success',
                                        message: response.message,
                                        position: 'topRight'
                                    });
                                    $('#StateData').DataTable().ajax.reload(null, false);
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
                                    message: xhr.responseJSON?.message || 'Failed to delete states',
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

            if (target === '#new-state') {
                // When switching to form tab, check if we're in edit mode
                if ($('#state_form').attr('data-mode') !== 'edit') {
                    resetForm();
                    $('#companyTabLabel1').text('New State');
                    $('#submitBtn').text('Submit');
                }
            }
        });

        // Function to reset form
        function resetForm() {
            $('#state_form')[0].reset();
            $('#state_form').removeAttr('data-mode');
            $('input[name="group_id"]').remove();
            $('.invalid-feedback').hide();
            $('.is-invalid').removeClass('is-invalid');

            // Reset category container to initial state
            $('#StateContainer').html(`
        <div class="mb-3 row category-row">
            <div class="col-5">
                <label class="form-label">State Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="state_name[]" placeholder="Enter State Name" required>
                <div class="invalid-feedback certificateError"></div>
            </div>
            <div class="col-5">
                <label class="form-label">State Code</label>
                <input type="text" class="form-control" name="state_code[]" placeholder="Enter State code">
                <div class="invalid-feedback certificateError"></div>
            </div>
            <div class="col-2 d-flex align-items-end">
                <button type="button" class="btn btn-success add-category-btn">+</button>
            </div>
        </div>
    `);

            // Reset Select2
            $('#c_id').val(null).trigger('change');
        }
        $('a[href="#company-list"]').on('click', function () {
            // Reset the form to "New Stock Category" state
            resetForm();
            $('#companyTabLabel1').text('New State');
            $('#submitBtn').text('Submit');
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

        // Handle create/edit form submit
        $('#state_form').submit(function (e) {
            e.preventDefault();

            // Client side validation
            let isValid = true;
            
            let country = $('#c_id').val();
            if (!country) {
                $('#c_id').next('.select2-container').addClass('is-invalid');
                iziToast.warning({ title: 'Warning', message: 'Please select a Country.', position: 'topRight' });
                isValid = false;
            } else {
                $('#c_id').next('.select2-container').removeClass('is-invalid');
            }

            if (isValid) {
                $('input[name="state_name[]"]').each(function() {
                    if (!$(this).val().trim()) {
                        $(this).addClass('is-invalid');
                        iziToast.warning({ title: 'Warning', message: 'Please enter a State Name.', position: 'topRight' });
                        isValid = false;
                        return false; // break loop
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
            }

            if (!isValid) return;

            const formData = new FormData(this);
            const mode = $(this).attr('data-mode') || 'add';

            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

            $.ajax({
                url: "{{ route('state.store') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,

                success(resp) {
                    iziToast.success({ title: 'Success', message: resp.message, position: 'topRight' });

                    // Reset form and switch back to list tab
                    $('#state_form')[0].reset();
                    $('#StateContainer').html('');
                    $('#state_form').attr('data-mode', 'add').removeAttr('data-id');
                    $('#submitBtn').text('Submit').prop('disabled', false);

                    $('a[href="#company-list"]').tab('show');
                    $('#StateData').DataTable().ajax.reload(null, false);
                },

                error(xhr) {
                    $('#submitBtn').text('Submit').prop('disabled', false);
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            const idx = key.split('.')[1];
                            if (key.startsWith('state_name')) {
                                $('input[name="state_name[]"]').eq(idx)
                                    .addClass('is-invalid')
                                    .next('.invalid-feedback').text(errors[key][0]).show();
                            }
                            if (key.startsWith('state_code')) {
                                $('input[name="state_code[]"]').eq(idx)
                                    .addClass('is-invalid')
                                    .next('.invalid-feedback').text(errors[key][0]).show();
                            }
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
