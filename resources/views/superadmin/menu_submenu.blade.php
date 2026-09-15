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
        <div class="page-header-breadcrumb d-md-flex d-block align-items-center justify-content-between ">
            <h4 class="fw-medium mb-0">{{ $page_title }}</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-white-50">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $page_name }}</li>
            </ol>
        </div>
        <div class="main-content app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">

                                    <ul class="nav nav-tabs justify-content-end nav-tabs-footer mb-0" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" role="tab"
                                                aria-current="page" href="#menu-list" aria-selected="true">List</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                                href="#new-menu" aria-selected="false">New Menu</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                                href="#menu-ordering" aria-selected="false">Menu
                                                Ordering</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                                href="#menu-types-tab" aria-selected="false">Menu Types</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane border-0 show active text-muted px-1" id="menu-list"
                                        role="tabpanel">
                                        <div class="row">
                                            <table id="datatable-basic"
                                                class="table table-bordered menu-submenu-data dataTable no-footer"
                                                style="width: 100%;" aria-describedby="datatable-basic_info">
                                                <thead>
                                                    <tr>
                                                        <th style="width:1%;">#</th>
                                                        <th style="width:20%">Menu</th>
                                                        <th style="width:69%">Submenu</th>
                                                        <th class="text-center" style="width:10%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane text-muted border-0 px-1" id="new-menu" role="tabpanel">
                                        <form id="MenuForm" class="needs-validation" novalidate>
                                            @csrf
                                            <input type="hidden" name="c_menu_id" id="c_menu_id" value="">
                                            <div class="row">
                                                <div class="col-md-2 col-6">
                                                    <div class="mb-2">
                                                        <label class="form-label">Menu Type<span
                                                                style="color:red;">*</span></label>
                                                        <select
                                                            class="js-example-placeholder-single js-states form-control"
                                                            id="menu_type_id" name="menu_type_id" required>
                                                            <option value="" selected disabled>Select Menu
                                                                Type
                                                            </option>
                                                            @foreach ($menu_type as $menu)
                                                                <option value="{{ $menu->menu_type_id }}">
                                                                    {{ $menu->type_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="invalid-feedback">Menu Type is required
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <div class="mb-2">
                                                        <label class="form-label">Menu Name <span
                                                                style="color:red;">*</span></label>
                                                        <input type="text" class="form-control" id="menu_title"
                                                            placeholder="Enter Menu Name" name="menu_title" required>
                                                        <div class="invalid-feedback">Menu Name is required
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6">
                                                    <div class="mb-2">
                                                        <label class="form-label">Menu Routes</label>
                                                        <input type="text" class="form-control" id="menu_route"
                                                            placeholder="Enter Menu Routes" name="menu_route">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-6">
                                                    <div class="mb-2">
                                                        <label class="form-label">Menu Icon</label>
                                                        <input type="text" class="form-control" id="menu_icon"
                                                            placeholder="Enter Menu Icon" name="menu_icon">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-12">
                                                    <div class="mb-2 mt-4">
                                                        <!-- Hidden input to send value as 0 when the checkbox is unchecked -->
                                                        <input type="hidden" name="is_submenu" value="0">
                                                        <input type="checkbox" id="is_submenu" class="form-check-input"
                                                            name="is_submenu" value="1">
                                                        <label for="is_submenu">Menu Have Submenu</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="submenu_fields" name="submenu_fields" style="display: none;">
                                                <div class="submenu_row">
                                                    <div class="row">
                                                        <div class="col-md-3 col-5">
                                                            <div class="mb-2">
                                                                <label class="form-label">Submenu Name <span
                                                                        style="color:red;">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Enter Submenu Name"
                                                                    name="sub_menu_title[]">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-5">
                                                            <div class="mb-2">
                                                                <label class="form-label">Submenu Routes <span
                                                                        style="color:red;">*</span></label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Enter Submenu Routes"
                                                                    name="sub_menu_route[]">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-2">
                                                            <button type="button"
                                                                class="mt-4 btn btn-sm btn-success add"><i
                                                                    class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12 text-end">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade border-0 px-1" id="menu-ordering" role="tabpanel">
                                        <div class="mt-3">
                                            <div class="row" id="sortable-menu"></div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade border-0 px-1" id="menu-types-tab" role="tabpanel">
                                        <div class="mt-3">
                                            <div class="row" id="sortable-menu-types"></div>
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
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>


    <script>
        $(document).ready(function () {
            //  $('.tooltip-init').tooltip();
            $('#menu_type_id').select2({
                width: '100%',
                tags: true
            });
            $('#is_submenu').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#submenu_fields').show();
                    $('#submenu_fields input[name="sub_menu_title[]"]').prop('required', true);
                    $('#submenu_fields input[name="sub_menu_route[]"]').prop('required', true);
                } else {
                    $('#submenu_fields').hide();
                    $('#submenu_fields input[name="sub_menu_title[]"]').prop('required', false);
                    $('#submenu_fields input[name="sub_menu_route[]"]').prop('required', false);
                }
            });
            if ($('#is_submenu').is(':checked')) {
                $('#submenu_fields').show();
                $('#submenu_fields input[name="sub_menu_title[]"]').prop('required', true);
                $('#submenu_fields input[name="sub_menu_route[]"]').prop('required', true);
            }
            // Add new submenu row
            $(document).on('click', '.add', function () {
                const html = `
            <div class="row submenu_row">
                <div class="col-md-3 col-5">
                    <div class="mb-2">
                        <label class="form-label">Submenu Name <span style="color:red;">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter Submenu Name" name="sub_menu_title[]" ${$('#is_submenu').is(':checked') ? 'required' : ''}>
                    </div>
                </div>
                <div class="col-md-3 col-5">
                    <div class="mb-2">
                        <label class="form-label">Submenu Routes <span style="color:red;">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter Submenu Routes" name="sub_menu_route[]" ${$('#is_submenu').is(':checked') ? 'required' : ''}>
                    </div>
                </div>
                <div class="col-md-3 col-2">
                    <button type="button" class="mt-4 btn btn-sm btn-danger remove"><i class="fa fa-minus"></i></button>
                </div>
            </div>
        `;
                $('#submenu_fields').append(html);
            });
            // $(document).on('click', '.remove', function () {
            //     $(this).closest('.submenu_row').remove();
            // });

            $(document).on('click', '.remove', function () {

                let row = $(this).closest('.submenu_row');
                let submenuId = row.data('id');
                let menuId = $('#c_menu_id').val();

                if (submenuId) {

                    $.ajax({
                        url: "{{ route('submenudelete') }}",
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            submenu_id: submenuId,
                            c_menu_id: menuId
                        },
                        success: function (response) {

                            if (response.success) {
                                row.remove();

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

                        }
                    });

                } else {
                    row.remove();
                }

            });

            var MenuData = $('.menu-submenu-data').DataTable({
                "processing": false
                , "serverSide": true
                , "pageLength": 10
                , "responsive": false
                , "ajax": {
                    url: "{{ route('menudatafetch') }}"
                    , type: 'GET'
                    , headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    , dataSrc: function (json) {
                        var dataArray = [];
                        $.each(json.data, function (index, item) {
                            const dataRow = [
                                item.sr_no, item.menu_title, item.sub_menu_title, item
                                    .Action
                            ];
                            dataArray.push(dataRow);
                        });
                        return dataArray;
                    }
                }
                , "columnDefs": [{
                    targets: 0
                    , orderable: false
                }]
                ,
            });
            $('.menu-submenu-data').on('draw.dt', function () {
                $('.tooltip-init').tooltip();
            });
            $('#MenuForm').on('submit', function (e) {
                e.preventDefault();
                if ($(this)[0].checkValidity() === false) {
                    $(this).addClass('was-validated');
                    return;
                }
                let formData = $(this).serialize();
                let url = "{{ route('menustore') }}";
                let method = "POST";
                $.ajax({
                    url: url
                    , type: method
                    , data: formData
                    , success: function (response) {
                        if (response.success) {
                            iziToast.success({
                                title: 'Success'
                                , message: response.message
                                , position: 'topRight'
                                , transitionIn: 'bounceInLeft'
                                , transitionOut: 'fadeOutRight'
                                , timeout: 1000
                                , onClosing: function () {
                                    setTimeout(function () {
                                        $('#MenuForm')[0].reset();
                                        $('#MenuForm').removeClass('was-validated');
                                        $('.nav-link[href="#menu-list"]').tab('show');
                                        MenuData.ajax.reload(null, false);
                                    }, 0);
                                }
                            });
                        } else {
                            iziToast.warning({
                                title: 'Warning'
                                , message: response.message
                                , position: 'topRight'
                                , transitionIn: 'bounceInLeft'
                                ,
                            });
                        }
                    }
                    , error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = "";
                        for (let key in errors) {
                            errorMessage += errors[key][0] + "<br>";
                        }
                        iziToast.error({
                            title: 'Error'
                            , message: errorMessage
                            , position: 'topRight'
                            , transitionOut: 'fadeOutRight'
                            , timeout: 5000
                        });
                    }
                });
            });
            $(document).on('click', '#getEdit', function (e) {
                e.preventDefault();
                var c_menu_id = $(this).data('c_menu_id');
                $('.nav-tabs a[href="#new-menu"]').tab('show');
                $('#MenuForm')[0].reset();
                $('#c_menu_id').val(c_menu_id);
                $('#submenu_fields').html(`
        <div class="submenu_row">
            <div class="row">
                <div class="col-md-3 col-5">
                    <div class="mb-2">
                        <label class="form-label">Submenu Name <span style="color:red;">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter Submenu Name" name="sub_menu_title[]">
                    </div>
                </div>
                <div class="col-md-3 col-5">
                    <div class="mb-2">
                        <label class="form-label">Submenu Routes <span style="color:red;">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter Submenu Routes" name="sub_menu_route[]">
                    </div>
                </div>
                <div class="col-md-3 col-2">
                    <button type="button" class="mt-4 btn btn-sm btn-success add"><i class="fa fa-plus"></i></button>
                </div>
            </div>
        </div>
    `);
                $.ajax({
                    url: "{{ route('getMenuData') }}"
                    , type: "GET"
                    , data: {
                        c_menu_id: c_menu_id
                    }
                    , success: function (response) {
                        if (response.success) {
                            $('#menu_type_id').val(response.menu.menu_type_id).trigger(
                                'change');
                            $('#menu_title').val(response.menu.menu_title);
                            $('#menu_route').val(response.menu.menu_route);
                            $('#menu_icon').val(response.menu.menu_icon);
                            if (response.menu.is_submenu == 1) {
                                $('#is_submenu').prop('checked', true);
                                $('#submenu_fields').show();
                                $('#submenu_fields').html('');
                                if (response.submenus.length > 0) {
                                    $.each(response.submenus, function (index, submenu) {
                                        let removeButton = index === 0 ?
                                            '<button type="button" class="mt-4 btn btn-sm btn-success add"><i class="fa fa-plus"></i></button>' :
                                            '<button type="button" class="mt-4 btn btn-sm btn-danger remove"><i class="fa fa-minus"></i></button>';
                                        let submenuHtml = `
                               <div class="submenu_row" data-id="${submenu.c_sub_menu_id}">
                                    <div class="row">
                                        <div class="col-md-3 col-5">
                                            <div class="mb-2">
                                                <label class="form-label">Submenu Name <span style="color:red;">*</span></label>
                                                <input type="text" class="form-control" placeholder="Enter Submenu Name"
                                                    name="sub_menu_title[]" value="${submenu.sub_menu_title}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-5">
                                            <div class="mb-2">
                                                <label class="form-label">Submenu Routes <span style="color:red;">*</span></label>
                                                <input type="text" class="form-control" placeholder="Enter Submenu Routes"
                                                    name="sub_menu_route[]" value="${submenu.sub_menu_route}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-2">
                                            ${removeButton}
                                        </div>
                                    </div>
                                </div>
                            `;
                                        $('#submenu_fields').append(submenuHtml);
                                    });
                                }
                            } else {
                                $('#is_submenu').prop('checked', false);
                                $('#submenu_fields').hide();
                            }
                            $('#MenuForm button[type="submit"]').text('Update');
                            $('html, body').animate({
                                scrollTop: $('#MenuForm').offset().top - 100
                            }, 500);
                        } else {
                            iziToast.error({
                                title: 'Error'
                                , message: response.message ||
                                    'Failed to fetch menu data'
                                , position: 'topRight'
                            });
                        }
                    }
                    , error: function (xhr) {
                        iziToast.error({
                            title: 'Error'
                            , message: 'An error occurred while fetching menu data'
                            , position: 'topRight'
                        });
                        console.error(xhr.responseText);
                    }
                });
            });
            $(document).on('click', '.delete-btn', function () {
                currentMenuIdToDelete = $(this).data('menu-id');
                const menuTitle = $(this).data('menu-title');
                $('#menuTitleToDelete').text(menuTitle);
            });
        });
        $(document).on('click', '#confirmDelete', function () {
            if (!currentMenuIdToDelete) return;
            $.ajax({
                url: "{{ route('deleteMenu') }}"
                , type: "POST"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , c_menu_id: currentMenuIdToDelete
                }
                , beforeSend: function () {
                    $('#confirmDelete').prop('disabled', true).html(
                        '<i class="bx bx-loader bx-spin"></i> Deleting...');
                }
                , success: function (response) {
                    if (response.success) {
                        iziToast.success({
                            title: 'Success'
                            , message: response.message
                            , position: 'topRight'
                            , transitionIn: 'bounceInLeft'
                            , transitionOut: 'fadeOutRight'
                            , timeout: 1000
                            , onClosing: function () {
                                setTimeout(function () {
                                    $('#MenuForm')[0].reset();
                                    $('#MenuForm').removeClass('was-validated');
                                    $('.nav-link[href="#menu-list"]').tab('show');
                                    $('.modal').modal('hide');
                                    $('.menu-submenu-data').DataTable().ajax.reload(
                                        null, false);
                                }, 0);
                            }
                        });
                    } else {
                        iziToast.error({
                            title: 'Error'
                            , message: response.message
                            , position: 'topRight'
                        });
                    }
                }
                , error: function (xhr) {
                    iziToast.error({
                        title: 'Error'
                        , message: 'An error occurred while deleting the menu'
                        , position: 'topRight'
                    });
                    console.error(xhr.responseText);
                }
                , complete: function () {
                    $('#confirmDelete').prop('disabled', false).text('Delete');
                    currentMenuIdToDelete = null;
                }
            });
        });

        function loadMenus() {
            $.ajax({
                url: "{{ route('menusorderingfetch') }}"
                , type: "GET"
                , success: function (data) {
                    $('#sortable-menu').html(''); // Clear previous data
                    if (!Array.isArray(data) || data.length === 0) {
                        $('#sortable-menu').html('<p>No menus found</p>'); // Handle no data scenario
                        return;
                    }
                    let groupedData = [];
                    let typeIndexMap = {};
                    $.each(data, function (index, item) {
                        let typeId = item.menu_type_id || 'unassigned';
                        if (typeIndexMap[typeId] === undefined) {
                            typeIndexMap[typeId] = groupedData.length;
                            groupedData.push({
                                typeId: typeId,
                                type_name: item.type_name || 'Unassigned',
                                menus: []
                            });
                        }
                        groupedData[typeIndexMap[typeId]].menus.push(item);
                    });

                    $.each(groupedData, function (index, group) {
                        let typeId = group.typeId;
                        let menusHtml = '';
                        $.each(group.menus, function (mIndex, item) {
                            let submenuHtml = '';
                            if (Array.isArray(item.submenus) && item.submenus.length > 0) {
                                $.each(item.submenus, function (subIndex, submenu) {
                                    submenuHtml += `
                                    <div class="p-2 border rounded mb-1 d-flex align-items-center justify-content-between sortable-submenu-item bg-white" data-id="${submenu.c_sub_menu_id}" style="cursor: grab;">
                                        ${submenu.sub_menu_title}
                                        <i class="fas fa-grip-lines text-muted"></i>
                                    </div>`;
                                });
                            }
                            menusHtml += `
                            <div class="border p-2 rounded shadow-sm menu-item mb-2 bg-white" data-id="${item.c_menu_id}" style="cursor: grab;">
                                <div class="card border-0 mb-0">
                                    <div class="card-body p-1">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="card-title mb-0 fw-bold text-primary">${item.menu_title}</h6>
                                            <i class="fas fa-hand-rock fa-md text-secondary"></i>
                                        </div>
                                        <div class="sortable-submenu ms-3" style="min-height: 5px;">
                                            ${submenuHtml}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `;
                        });

                        $('#sortable-menu').append(`
                        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card shadow-sm h-100 bg-light border-secondary">
                                <div class="card-header border-bottom py-2">
                                    <h5 class="card-title mb-0 fs-14 fw-bold text-uppercase text-primary">${group.type_name}</h5>
                                </div>
                                <div class="card-body p-2 sortable-menu-list" data-type-id="${typeId}" style="min-height: 100px;">
                                    ${menusHtml}
                                </div>
                            </div>
                        </div>
                        `);
                    });
                    initSortables(); // Re-initialize sortable logic after rendering
                }
                , error: function (xhr, status, error) {
                    console.error("Error loading menus: ", status, error);
                    $('#sortable-menu').html('<p>Failed to load menus</p>'); // Show error message if AJAX fails
                }
            });
        }

        function initSortables() {
            // Menu sorting
            $('.sortable-menu-list').sortable({
                connectWith: '.sortable-menu-list',
                update: function (event, ui) {
                    if (this === ui.item.parent()[0]) {
                        let order = [];
                        $('.sortable-menu-list').each(function () {
                            let typeId = $(this).data('type-id');
                            $(this).find('.menu-item').each(function (index) {
                                let itemData = {
                                    id: $(this).data('id'),
                                    order: index + 1
                                };
                                if (typeId !== 'unassigned') {
                                    itemData.menu_type_id = typeId;
                                }
                                order.push(itemData);
                            });
                        });
                        $.ajax({
                            url: "{{ route('menusorderingupdate') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                order: JSON.stringify(order)
                            },
                            success: function (response) {
                                console.log('Menu order updated', response);
                                iziToast.success({
                                    title: 'Success',
                                    message: 'Menu ordering updated',
                                    position: 'topRight'
                                });
                            },
                            error: function (xhr, status, error) {
                                console.error("Error updating menu order: ", status, error);
                            }
                        });
                    }
                }
            });
            // Submenu sorting
            $('.sortable-submenu').sortable({
                connectWith: '.sortable-submenu'
                , update: function (event, ui) {
                    let order = [];
                    $(this).children('.sortable-submenu-item').each(function (index) {
                        order.push({
                            id: $(this).data('id')
                            , order: index + 1
                        });
                    });
                    // Get the parent menu ID
                    let menuId = $(this).closest('.menu-item').data('id');
                    $.ajax({
                        url: "{{ route('submenus/ordering/update') }}"
                        , method: 'POST'
                        , data: {
                            _token: '{{ csrf_token() }}'
                            , order: JSON.stringify(order), // Stringify the array
                            c_menu_id: menuId
                        }
                        , success: function (response) {
                            console.log('Submenu order updated', response);
                        }
                        , error: function (xhr, status, error) {
                            console.error("Error updating submenu order: ", status, error);
                        }
                    });
                }
            });
        }
        // Load menus when tab is shown
        $('a[href="#menu-ordering"]').on('shown.bs.tab', function () {
            loadMenus();
        });

        $('a[href="#menu-types-tab"]').on('shown.bs.tab', function () {
            loadMenuTypes();
        });

        function loadMenuTypes() {
            $.ajax({
                url: "{{ route('menutypesorderingfetch') }}",
                type: "GET",
                success: function (data) {
                    $('#sortable-menu-types').html('');
                    if (!Array.isArray(data) || data.length === 0) {
                        $('#sortable-menu-types').html('<p>No menu types found</p>');
                        return;
                    }
                    $.each(data, function (index, item) {
                        $('#sortable-menu-types').append(`
                            <div class="col-sm-6 col-md-4 mb-3 type-item-container">
                                <div class="border p-2 rounded shadow-sm type-item" data-id="${item.menu_type_id}" style="cursor: grab;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <i class="fas fa-hand-rock fa-lg text-secondary me-2"></i>
                                            <span class="type-name-text fw-bold">${item.type_name}</span>
                                            <input type="text" class="form-control form-control-sm type-name-input d-none" value="${item.type_name}" />
                                        </div>
                                        <div class="ms-2 d-flex">
                                            <button class="btn btn-sm btn-primary edit-type-btn ms-1"><i class="fa fa-edit"></i></button>
                                            <button class="btn btn-sm btn-success save-type-btn ms-1 d-none"><i class="fa fa-save"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                    initMenuTypesSortable();
                }
            });
        }

        function initMenuTypesSortable() {
            $('#sortable-menu-types').sortable({
                update: function (event, ui) {
                    let order = [];
                    $('#sortable-menu-types > .type-item-container').each(function (index) {
                        order.push({
                            id: $(this).find('.type-item').data('id'),
                            order: index + 1
                        });
                    });
                    $.ajax({
                        url: "{{ route('menutypesorderingupdate') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            order: JSON.stringify(order)
                        },
                        success: function (response) {
                            iziToast.success({
                                title: 'Success',
                                message: 'Menu Types ordered successfully!',
                                position: 'topRight'
                            });
                        }
                    });
                }
            });
        }

        $(document).on('click', '.edit-type-btn', function () {
            let container = $(this).closest('.type-item');
            container.find('.type-name-text').addClass('d-none');
            container.find('.type-name-input').removeClass('d-none');
            $(this).addClass('d-none');
            container.find('.save-type-btn').removeClass('d-none');
        });

        $(document).on('click', '.save-type-btn', function () {
            let container = $(this).closest('.type-item');
            let typeId = container.data('id');
            let newName = container.find('.type-name-input').val();
            let btn = $(this);

            $.ajax({
                url: "{{ route('menutypesrename') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    menu_type_id: typeId,
                    type_name: newName
                },
                success: function (response) {
                    if (response.success) {
                        container.find('.type-name-text').text(newName).removeClass('d-none');
                        container.find('.type-name-input').addClass('d-none');
                        btn.addClass('d-none');
                        container.find('.edit-type-btn').removeClass('d-none');
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topRight'
                        });
                        // update the options in the select dropdown too
                        $('#menu_type_id option[value="' + typeId + '"]').text(newName);
                    } else {
                        iziToast.error({ title: 'Error', message: response.message, position: 'topRight' });
                    }
                }
            });
        });



    </script>
</body>

</html>