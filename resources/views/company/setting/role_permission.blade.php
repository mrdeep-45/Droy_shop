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
        <div class="page-header-breadcrumb d-md-flex d-block align-items-center justify-content-between d-none">
            {{-- <h4 class="fw-medium mb-0">{{ $page_title }}</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-white-50">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $page_name }}</li>
            </ol> --}}
        </div>
        <div class="main-content app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="card-title text-primary mb-0">
                                    {{ $page_title }}
                                </div>

                                <ul class="nav nav-tabs justify-content-end nav-tabs-footer mb-0" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" role="tab"
                                            aria-current="page" href="#menu-list" aria-selected="true"><i
                                                class="fas fa-list me-1"></i> List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                            href="#new-menu" aria-selected="true"><i class="fas fa-plus me-1"></i> New
                                            Permission</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    {{-- Table --}}
                                    <div class="tab-pane fade border-0 show active text-muted px-1" id="menu-list"
                                        role="tabpanel">
                                        <div class="row">
                                            <div class="table-responsive">
                                                <table id="datatable-basic"
                                                    class="table table-bordered permissionsTable dataTable no-footer text-nowrap w-100"
                                                    style="width: 100%;" aria-describedby="datatable-basic_info">

                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Role</th>
                                                            <th>Menu</th>
                                                            <th>Submenu</th>
                                                            <th>Permissions</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane text-muted" id="new-menu" role="tabpanel">
                                        <form id="permissions-form" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-2 mb-2 d-none">
                                                    <label class="form-check-label fw-bold d-flex align-items-center"
                                                        for="role_type">User <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm" name="role_type"
                                                        id="role_type">
                                                        <option value="">Select User</option>
                                                        <option value="manager">
                                                            Manager
                                                        </option>
                                                        <option value="staff" selected>
                                                            Staff
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <label class="form-check-label fw-bold d-flex align-items-center"
                                                        for="Role">Role <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm" name="role_id"
                                                        id="RoleId">
                                                        <option value="">Select Role</option>

                                                    </select>
                                                </div>
                                                @foreach ($menus as $menu)
                                                    <div class="col-md-12 mb-3">
                                                        <div class="card shadow-sm">
                                                            <div
                                                                class="card-header bg-primary text-primary d-flex align-items-center">
                                                                <div class="form-check form-check-inline m-0">
                                                                    <input class="form-check-input menu-checkbox"
                                                                        type="checkbox" id="menu_{{ $menu->c_menu_id }}"
                                                                        name="menus[]" value="{{ $menu->c_menu_id }}"
                                                                        data-menu-id="{{ $menu->c_menu_id }}" hidden>
                                                                    <label
                                                                        class="form-check-label fw-bold d-flex align-items-center"
                                                                        for="menu_{{ $menu->c_menu_id }}">
                                                                        <i class="{{ $menu->menu_icon }} me-2 fs-5"></i>
                                                                        <span>{{ $menu->menu_title }}</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="card-body bg-light p-3">
                                                                <div class="row g-3">
                                                                    @if ($menu->is_submenu)
                                                                        @foreach ($menu->submenus as $submenu)
                                                                            <div class="col-md-12">
                                                                                <div
                                                                                    class="border rounded p-3 bg-white">
                                                                                    <div class="form-check mb-3">
                                                                                        <input
                                                                                            class="form-check-input submenu-checkbox"
                                                                                            type="checkbox"
                                                                                            id="submenu_{{ $submenu->c_sub_menu_id }}"
                                                                                            name="submenus[]"
                                                                                            value="{{ $submenu->c_sub_menu_id }}"
                                                                                            data-menu-id="{{ $menu->c_menu_id }}"
                                                                                            hidden>
                                                                                        <label
                                                                                            class="form-check-label fw-semibold text-primary d-flex align-items-center"
                                                                                            for="submenu_{{ $submenu->c_sub_menu_id }}">
                                                                                            <i
                                                                                                class="{{ $submenu->sub_menu_icon }} me-2 fs-6"></i>
                                                                                            <span>{{ $submenu->sub_menu_title }}</span>
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="row g-2 text-center">
                                                                                        <div class="col-md-2">
                                                                                            <div
                                                                                                class="form-check h-100 d-flex flex-column justify-content-between bg-info bg-opacity-10 p-2 rounded">
                                                                                                <input
                                                                                                    class="form-check-input align-self-center all-checkbox"
                                                                                                    type="checkbox"
                                                                                                    id="all_{{ $submenu->c_sub_menu_id }}"
                                                                                                    data-submenu-id="{{ $submenu->c_sub_menu_id }}">
                                                                                                <label
                                                                                                    class="form-check-label fw-bold text-info mt-1"
                                                                                                    for="all_{{ $submenu->c_sub_menu_id }}">All</label>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-2">
                                                                                            <div
                                                                                                class="form-check h-100 d-flex flex-column justify-content-between bg-success bg-opacity-10 p-2 rounded">
                                                                                                <input
                                                                                                    class="form-check-input align-self-center permission-checkbox"
                                                                                                    type="checkbox"
                                                                                                    id="view_{{ $submenu->c_sub_menu_id }}"
                                                                                                    name="permissions[{{ $submenu->c_sub_menu_id }}][view]"
                                                                                                    value="1">
                                                                                                <label
                                                                                                    class="form-check-label text-success mt-1"
                                                                                                    for="view_{{ $submenu->c_sub_menu_id }}">View</label>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-2">
                                                                                            <div
                                                                                                class="form-check h-100 d-flex flex-column justify-content-between bg-primary bg-opacity-10 p-2 rounded">
                                                                                                <input
                                                                                                    class="form-check-input align-self-center permission-checkbox"
                                                                                                    type="checkbox"
                                                                                                    id="create_{{ $submenu->c_sub_menu_id }}"
                                                                                                    name="permissions[{{ $submenu->c_sub_menu_id }}][create]"
                                                                                                    value="1">
                                                                                                <label
                                                                                                    class="form-check-label text-primary mt-1"
                                                                                                    for="create_{{ $submenu->c_sub_menu_id }}">Create</label>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-2">
                                                                                            <div
                                                                                                class="form-check h-100 d-flex flex-column justify-content-between bg-warning bg-opacity-10 p-2 rounded">
                                                                                                <input
                                                                                                    class="form-check-input align-self-center permission-checkbox"
                                                                                                    type="checkbox"
                                                                                                    id="update_{{ $submenu->c_sub_menu_id }}"
                                                                                                    name="permissions[{{ $submenu->c_sub_menu_id }}][update]"
                                                                                                    value="1">
                                                                                                <label
                                                                                                    class="form-check-label text-warning mt-1"
                                                                                                    for="update_{{ $submenu->c_sub_menu_id }}">Update</label>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-2">
                                                                                            <div
                                                                                                class="form-check h-100 d-flex flex-column justify-content-between bg-danger bg-opacity-10 p-2 rounded">
                                                                                                <input
                                                                                                    class="form-check-input align-self-center permission-checkbox"
                                                                                                    type="checkbox"
                                                                                                    id="delete_{{ $submenu->c_sub_menu_id }}"
                                                                                                    name="permissions[{{ $submenu->c_sub_menu_id }}][delete]"
                                                                                                    value="1">
                                                                                                <label
                                                                                                    class="form-check-label text-danger mt-1"
                                                                                                    for="delete_{{ $submenu->c_sub_menu_id }}">Delete</label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <div class="col-md-12">
                                                                            <div class="border rounded p-3 bg-white">
                                                                                <div class="row g-2 text-center">
                                                                                    <div class="col-md-2">
                                                                                        <div
                                                                                            class="form-check h-100 d-flex flex-column justify-content-between bg-info bg-opacity-10 p-2 rounded">
                                                                                            <input
                                                                                                class="form-check-input align-self-center all-checkbox"
                                                                                                type="checkbox"
                                                                                                id="all_menu_{{ $menu->c_menu_id }}"
                                                                                                data-menu-id="{{ $menu->c_menu_id }}">
                                                                                            <label
                                                                                                class="form-check-label fw-bold text-info mt-1"
                                                                                                for="all_menu_{{ $menu->c_menu_id }}">All</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div
                                                                                            class="form-check h-100 d-flex flex-column justify-content-between bg-success bg-opacity-10 p-2 rounded">
                                                                                            <input
                                                                                                class="form-check-input align-self-center permission-checkbox"
                                                                                                type="checkbox"
                                                                                                id="view_menu_{{ $menu->c_menu_id }}"
                                                                                                name="permissions_menu[{{ $menu->c_menu_id }}][view]"
                                                                                                value="1">
                                                                                            <label
                                                                                                class="form-check-label text-success mt-1"
                                                                                                for="view_menu_{{ $menu->c_menu_id }}">View</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div
                                                                                            class="form-check h-100 d-flex flex-column justify-content-between bg-primary bg-opacity-10 p-2 rounded">
                                                                                            <input
                                                                                                class="form-check-input align-self-center permission-checkbox"
                                                                                                type="checkbox"
                                                                                                id="create_menu_{{ $menu->c_menu_id }}"
                                                                                                name="permissions_menu[{{ $menu->c_menu_id }}][create]"
                                                                                                value="1">
                                                                                            <label
                                                                                                class="form-check-label text-primary mt-1"
                                                                                                for="create_menu_{{ $menu->c_menu_id }}">Create</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div
                                                                                            class="form-check h-100 d-flex flex-column justify-content-between bg-warning bg-opacity-10 p-2 rounded">
                                                                                            <input
                                                                                                class="form-check-input align-self-center permission-checkbox"
                                                                                                type="checkbox"
                                                                                                id="update_menu_{{ $menu->c_menu_id }}"
                                                                                                name="permissions_menu[{{ $menu->c_menu_id }}][update]"
                                                                                                value="1">
                                                                                            <label
                                                                                                class="form-check-label text-warning mt-1"
                                                                                                for="update_menu_{{ $menu->c_menu_id }}">Update</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div
                                                                                            class="form-check h-100 d-flex flex-column justify-content-between bg-danger bg-opacity-10 p-2 rounded">
                                                                                            <input
                                                                                                class="form-check-input align-self-center permission-checkbox"
                                                                                                type="checkbox"
                                                                                                id="delete_menu_{{ $menu->c_menu_id }}"
                                                                                                name="permissions_menu[{{ $menu->c_menu_id }}][delete]"
                                                                                                value="1">
                                                                                            <label
                                                                                                class="form-check-label text-danger mt-1"
                                                                                                for="delete_menu_{{ $menu->c_menu_id }}">Delete</label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <!-- Form submission buttons -->
                                            <div class="row mt-4">
                                                <div class="col-md-12 text-end">
                                                    <button type="reset" class="btn btn-outline-secondary me-2">
                                                        <i class="fas fa-undo me-1"></i> Reset
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save me-1"></i> Save Permissions
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
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
    @include('include/datatable_js_link')
    <script>
        $(document).ready(function() {

            $('#role_type').select2({
                placeholder: 'Select User',
                width: 'resolve'

            });

            function loadRoles(roleType, selectRoleId = null) {
                var roleSelect = $('#RoleId');
                roleSelect.empty().append('<option value="">Select Role</option>');

                if (!roleType) return;

                $.ajax({
                    url: "{{ route('get.roles.by.type') }}",
                    type: 'GET',
                    data: {
                        role_type: roleType
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success && res.roles.length) {
                            res.roles.forEach(function(role) {
                                roleSelect.append(
                                    new Option(role.role_name, role.role_id, false, false)
                                );
                            });
                        }

                        if (selectRoleId) {
                            roleSelect.val(selectRoleId).trigger('change');
                        }
                    },
                    error: function() {
                        iziToast.error({
                            title: 'Error',
                            message: 'Failed to load roles for selected user type',
                            position: 'topRight'
                        });
                    }
                });
            }

            // Initial load of roles on page load
            loadRoles('staff');

            // Trigger loadRoles on user type change
            $('#role_type').on('change', function() {
                loadRoles($(this).val());
            });

            $('#RoleId').select2({
                placeholder: 'Select Role',
                width: 'resolve',
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: 'NEW:' + term,
                        text: term + ' (new)',
                        newTag: true
                    };
                }
            });

            $('#RoleId').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue && selectedValue.startsWith('NEW:')) {
                    var roleName = selectedValue.substring(4);
                    // createNewRole(roleName);
                    return;
                }
                var roleId = selectedValue;
                if (!roleId) {
                    resetAllCheckboxes();
                    return;
                }
                $.ajax({
                    url: "{{ route('get.permissions', ':roleId') }}".replace(':roleId', roleId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            resetAllCheckboxes();
                            setMenuPermissions(response.data.permissions_menu);
                            setSubmenuPermissions(response.data.permissions);
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: response.message ||
                                    'Failed to load permissions',
                                position: 'topRight'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        iziToast.error({
                            title: 'Error',
                            message: 'An error occurred while fetching permissions',
                            position: 'topRight'
                        });
                    }
                });
            });

            $('#permissions-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var selectedValue = $('#RoleId').val();
                if (selectedValue && selectedValue.startsWith('NEW:')) {
                    var roleName = selectedValue.substring(4);
                    createNewRoleAndSavePermissions(roleName, form);
                } else {
                    submitPermissionsForm(form);
                }
            });

            $('.all-checkbox').on('change', function() {
                var targetId = $(this).data('submenu-id') || $(this).data('menu-id');
                var isChecked = $(this).is(':checked');
                if ($(this).data('submenu-id')) {
                    $('input[name^="permissions[' + targetId + ']"]').prop('checked', isChecked);
                } else {
                    $('input[name^="permissions_menu[' + targetId + ']"]').prop('checked', isChecked);
                }
            });

            $('.permission-checkbox').on('change', function() {
                var name = $(this).attr('name');
                var matches;
                if (matches = name.match(/permissions_menu\[(\d+)\]\[(\w+)\]/)) {
                    updateAllCheckboxState($('#all_menu_' + matches[1]), 'permissions_menu', matches[1]);
                } else if (matches = name.match(/permissions\[(\d+)\]\[(\w+)\]/)) {
                    updateAllCheckboxState($('#all_' + matches[1]), 'permissions', matches[1]);
                }
            });

            function createNewRoleAndSavePermissions(roleName, form) {
                var roleType = $('#role_type').val();
                if (!roleType) {
                    iziToast.error({
                        title: 'Error',
                        message: 'Please select a user type before creating role',
                        position: 'topRight'
                    });
                    resetSubmitButton(form);
                    return;
                }
                $.ajax({
                    url: "{{ route('role.create') }}",
                    type: 'POST',
                    data: {
                        role_name: roleName,
                        role_type: roleType
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        var submitBtn = form.find('button[type="submit"]');
                        submitBtn.prop('disabled', true);
                        submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Creating Role...');
                    },
                    success: function(response) {
                        if (response.success) {
                            var newOption = new Option(response.data.role_name, response.data.role_id,
                                true, true);
                            $('#RoleId').append(newOption).trigger('change');
                            form.find('input[name="role_id"]').val(response.data.role_id);
                            submitPermissionsForm(form);
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: response.message || 'Failed to create role',
                                position: 'topRight'
                            });
                            resetSubmitButton(form);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        iziToast.error({
                            title: 'Error',
                            message: 'An error occurred while creating the role',
                            position: 'topRight'
                        });
                        resetSubmitButton(form);
                    }
                });
            }

            function submitPermissionsForm(form) {
                var formData = form.serialize();
                var submitBtn = form.find('button[type="submit"]');
                var originalBtnText = submitBtn.html();
                submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
                $.ajax({
                    url: "{{ route('save.permissions') }}",
                    type: form.attr('method'),
                    data: formData,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.success) {
                            iziToast.success({
                                title: 'Success',
                                message: data.message || "Permissions saved successfully!",
                                position: 'topRight',
                                timeout: 700,
                                onClosed: function() {
                                    form[0].reset();
                                    form.find('select').val(null).trigger('change');
                                    $('a[href="#menu-list"]').tab('show');
                                    if (typeof PermissionsTable !== 'undefined' &&
                                        typeof PermissionsTable.ajax.reload === 'function'
                                    ) {
                                        PermissionsTable.ajax.reload(null, false);
                                    }
                                }
                            });
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: data.message || "Error saving permissions",
                                position: 'topRight'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        iziToast.error({
                            title: 'Error',
                            message: "An error occurred while saving permissions",
                            position: 'topRight'
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalBtnText);
                    }
                });
            }

            function resetAllCheckboxes() {
                $('.permission-checkbox, .all-checkbox').prop('checked', false);
            }

            function setMenuPermissions(permissions) {
                $.each(permissions, function(menuId, perms) {
                    if (perms.view) $('input[name="permissions_menu[' + menuId + '][view]"]').prop(
                        'checked', true);
                    if (perms.create) $('input[name="permissions_menu[' + menuId + '][create]"]').prop(
                        'checked', true);
                    if (perms.update) $('input[name="permissions_menu[' + menuId + '][update]"]').prop(
                        'checked', true);
                    if (perms.delete) $('input[name="permissions_menu[' + menuId + '][delete]"]').prop(
                        'checked', true);
                    updateAllCheckboxState($('#all_menu_' + menuId), 'permissions_menu', menuId);
                });
            }

            function setSubmenuPermissions(permissions) {
                $.each(permissions, function(submenuId, perms) {
                    if (perms.view) $('input[name="permissions[' + submenuId + '][view]"]').prop('checked',
                        true);
                    if (perms.create) $('input[name="permissions[' + submenuId + '][create]"]').prop(
                        'checked', true);
                    if (perms.update) $('input[name="permissions[' + submenuId + '][update]"]').prop(
                        'checked', true);
                    if (perms.delete) $('input[name="permissions[' + submenuId + '][delete]"]').prop(
                        'checked', true);
                    updateAllCheckboxState($('#all_' + submenuId), 'permissions', submenuId);
                });
            }

            function updateAllCheckboxState(allCheckbox, prefix, id) {
                var checkboxes = $('input[name^="' + prefix + '[' + id + ']"]');
                var allChecked = checkboxes.length > 0 && checkboxes.filter(':checked').length === checkboxes
                    .length;
                var anyChecked = checkboxes.filter(':checked').length > 0;
                allCheckbox.prop('checked', allChecked).prop('indeterminate', false);
            }

            function resetSubmitButton(form) {
                var submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', false);
                submitBtn.html('Save Permissions');
            }
            var PermissionsTable = $('.permissionsTable').DataTable({
                processing: false,
                serverSide: true,
                ajax: {
                    url: "{{ route('permissions.list') }}",
                    data: function(d) {
                        d.search = $('#searchInput').val();
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return data;
                    }
                }, {
                    data: 'role_name',
                    name: 'role_id'
                }, {
                    data: 'menu_name',
                    name: 'menu_id'
                }, {
                    data: 'submenu_name',
                    name: 'submenu_name',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'permissions',
                    name: 'permissions',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        if (meta.row === 0 || row.role_id !== PermissionsTable.row(meta.row - 1)
                            .data().role_id) {
                            return `
                                    <div class="text-center">
                                        <button class="btn btn-sm btn-primary mr-10 edit-btn" data-id="${row.submenu_data[0].id}" data-role-type="${row.role_type}" data-role-id="${row.role_id}">Edit</button>
                                        <button class="btn btn-sm btn-danger delete-btn" data-role-id="${row.role_id}">Delete</button>
                                    </div>`;
                        }
                        return '';
                    }
                }],
                order: [
                    [1, 'asc']
                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    var rows = api.rows({
                        page: 'current'
                    }).nodes();
                    var lastRoleId = null;
                    var groupIndex = 1;
                    api.rows({
                        page: 'current'
                    }).data().each(function(rowData, index) {
                        var currentRowNode = $(rows).eq(index);

                        if (lastRoleId !== rowData.role_id) {

                            currentRowNode.find('td:eq(0)').html(groupIndex++);
                            var rowspan = 1;
                            var data = api.rows({
                                page: 'current'
                            }).data();
                            for (var i = index + 1; i < data.length; i++) {
                                if (data[i].role_id === rowData.role_id) {
                                    rowspan++;
                                } else {
                                    break;
                                }
                            }
                            if (rowspan > 1) {
                                currentRowNode.find('td:eq(0)').attr('rowspan', rowspan).css(
                                    'vertical-align', 'middle');
                                currentRowNode.find('td:eq(1)').attr('rowspan', rowspan).css(
                                    'vertical-align', 'middle');
                                currentRowNode.find('td:eq(5)').attr('rowspan', rowspan).css(
                                    'vertical-align', 'middle');
                            }
                            lastRoleId = rowData.role_id;
                        } else {
                            currentRowNode.find('td:eq(0)').hide();
                            currentRowNode.find('td:eq(1)').hide();
                            currentRowNode.find('td:eq(5)').hide();
                        }
                    });
                }
            });

            $('#searchInput').keyup(function() {
                PermissionsTable.draw();
            });

            $(document).on('click', '.edit-btn', function() {
                var roleId = $(this).data('role-id');
                var role = $(this).data('role-type') || 'staff';

                $('.nav-link[href="#new-menu"]').text('Edit Permission');
                $('.nav-link[href="#new-menu"]').tab('show');

                $('#role_type').val(role).trigger('change.select2');
                loadRoles(role, roleId);
            });

            $('.nav-link[href="#menu-list"]').on('click', function() {
                $('.nav-link[href="#new-menu"]').text('New Permission');
                $('#permissions-form')[0].reset();
                $('.menu-checkbox, .submenu-checkbox, .permission-checkbox, .all-checkbox').prop('checked',
                    false);
                $('#RoleId').val(null).trigger('change');
            });

            $('.nav-link[href="#new-menu"]').text('New Permission');

            $(document).on('click', '.delete-btn', function() {
                var roleId = $(this).data('role-id');
                var $button = $(this);

                iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 999,
                    title: 'Confirm',
                    message: 'Are you sure you want to delete all permissions for this role?',
                    position: 'center',
                    buttons: [
                        ['<button><b>YES</b></button>', function(instance, toast) {
                            instance.hide({
                                transitionOut: 'fadeOut'
                            }, toast, 'button');

                            $.ajax({
                                url: "{{ route('permissions.delete') }}",
                                type: "POST",
                                data: {
                                    role_id: roleId,
                                    _token: "{{ csrf_token() }}"
                                },
                                beforeSend: function() {
                                    $button.prop('disabled', true).html(
                                        '<i class="fa fa-spinner fa-spin"></i> Deleting...'
                                    );
                                },
                                success: function(response) {
                                    if (response.success) {
                                        iziToast.success({
                                            title: 'Success',
                                            message: response.message,
                                            position: 'topRight'
                                        });
                                        PermissionsTable.ajax.reload(null,
                                            false);
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
                                        message: 'An error occurred while deleting permissions',
                                        position: 'topRight'
                                    });
                                },
                                complete: function() {
                                    $button.prop('disabled', false).html(
                                        'Delete');
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
        });


        $(document).on("change", ".permission-checkbox", function() {

            let name = $(this).attr("name");
            let checked = $(this).is(":checked");

            let isMenu = name.startsWith("permissions_menu");
            let prefix = isMenu ? "permissions_menu" : "permissions";

            let matches = name.match(/(permissions|permissions_menu)\[(\d+)\]\[(\w+)\]/);
            if (!matches) return;

            let id = matches[2];
            let type = matches[3];

            const toggle = (perm, state) => {
                $(`input[name="${prefix}[${id}][${perm}]"]`).prop("checked", state);
            };


            if (checked) {
                if (type === "create") {
                    toggle("view", true);
                }

                if (type === "update") {
                    toggle("view", true);
                    // toggle("create", true);
                }

                if (type === "delete") {
                    toggle("view", true);
                    // toggle("create", true);
                    // toggle("update", true);
                }
            }


            if (!checked) {
                if (type === "view") {
                    toggle("create", false);
                    toggle("update", false);
                    toggle("delete", false);
                }

                if (type === "create") {
                    // toggle("update", false);
                    // toggle("delete", false);
                }

                if (type === "update") {
                    // toggle("delete", false);
                }
            }
        });
    </script>
</body>

</html>
