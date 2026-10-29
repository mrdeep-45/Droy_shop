<!DOCTYPE html>
<html lang="en">
<head>
    @include('shop.include.meta')
    <title>{{ $page_title }} — DPShop Admin</title>
    @include('shop.include.header_link')
    <style>
        .role-badge-type {
            font-size: 0.72rem;
            padding: 2px 7px;
            border-radius: 4px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .perm-table thead th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
        }
        .perm-table tbody tr:hover {
            background-color: #f1f5f9;
        }
        .menu-row-main {
            background-color: #f8fafc;
            font-weight: 600;
        }
        .submenu-indent {
            padding-left: 28px !important;
        }
        .form-check-input {
            cursor: pointer;
            width: 1.15em;
            height: 1.15em;
        }
        .role-item {
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .role-item:hover {
            background-color: #f1f5f9;
        }
        .role-item.active {
            background-color: #eef2ff;
            border-left-color: #4f46e5;
            font-weight: 600;
        }
    </style>
</head>
<body>
@include('shop.include.loader')
@include('shop.include.header')

<main id="shopMain">

    <div class="shop-page-header d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4><i class="bx bx-shield-quarter text-shop-primary me-2"></i>{{ $page_title }}</h4>
            <div class="shop-breadcrumb">
                <a href="{{ route('shop.dashboard') }}">Home</a> / <a href="{{ route('shop.setting.index') }}">Settings</a> / <span>Roles & Permissions</span>
            </div>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="bx bx-plus-circle me-1"></i> Add New Role
        </button>
    </div>

    <div class="px-3 pb-4">
        <div class="row g-4">

            {{-- ── Left Column: Roles List ── --}}
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark"><i class="bx bx-user-check me-2 text-primary"></i>Available Roles</span>
                        <span class="badge bg-primary rounded-pill">{{ count($roles) }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="rolesList">
                            @foreach($roles as $idx => $r)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center role-item {{ $idx === 0 ? 'active' : '' }}"
                                     data-role-id="{{ $r->shop_role_id }}"
                                     data-role-name="{{ $r->role_name }}"
                                     data-role-type="{{ $r->role_type }}"
                                     onclick="selectRole({{ $r->shop_role_id }}, '{{ addslashes($r->role_name) }}', this)">
                                    <div>
                                        <div class="fw-semibold role-title">{{ $r->role_name }}</div>
                                        <span class="badge bg-secondary-subtle text-secondary role-badge-type">{{ $r->role_type }}</span>
                                    </div>
                                    @if(!in_array(strtolower($r->role_name), ['super admin', 'shop admin', 'vendor', 'delivery']))
                                        <button class="btn btn-sm btn-outline-danger border-0 p-1" onclick="deleteRole({{ $r->shop_role_id }}, event)" title="Delete Role">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Right Column: Permission Matrix ── --}}
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">
                                Permissions for: <span id="currentRoleTitle" class="text-primary">{{ $roles[0]->role_name ?? 'Select Role' }}</span>
                            </h5>
                            <small class="text-muted">Configure module-wise View, Create, Edit, and Delete privileges</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSelectAll(true)">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSelectAll(false)">Deselect All</button>
                            <button type="button" class="btn btn-primary" id="savePermissionsBtn" onclick="savePermissions()">
                                <i class="bx bx-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table perm-table align-middle mb-0" id="permissionTable">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Module / Menu</th>
                                        <th class="text-center" style="width: 15%;">View</th>
                                        <th class="text-center" style="width: 15%;">Create (Add)</th>
                                        <th class="text-center" style="width: 15%;">Update (Edit)</th>
                                        <th class="text-center" style="width: 15%;">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($menus as $menu)
                                        <tr class="menu-row-main" data-menu-id="{{ $menu->shop_menu_id }}">
                                            <td>
                                                <i class="{{ $menu->menu_icon ?? 'bx bx-cube' }} me-2 text-primary"></i>
                                                <span class="fw-semibold">{{ $menu->menu_title }}</span>
                                                @if($menu->menu_section)
                                                    <span class="badge bg-light text-muted ms-2 border" style="font-size:0.65rem;">{{ $menu->menu_section }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input perm-cb check-view"
                                                       data-menu-id="{{ $menu->shop_menu_id }}" data-submenu-id="" data-action="view">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input perm-cb check-create"
                                                       data-menu-id="{{ $menu->shop_menu_id }}" data-submenu-id="" data-action="create">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input perm-cb check-update"
                                                       data-menu-id="{{ $menu->shop_menu_id }}" data-submenu-id="" data-action="update">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input perm-cb check-delete"
                                                       data-menu-id="{{ $menu->shop_menu_id }}" data-submenu-id="" data-action="delete">
                                            </td>
                                        </tr>

                                        {{-- Submenus if any --}}
                                        @if($menu->is_submenu && count($menu->submenus) > 0)
                                            @foreach($menu->submenus as $sub)
                                                <tr data-menu-id="{{ $menu->shop_menu_id }}" data-submenu-id="{{ $sub->shop_submenu_id }}">
                                                    <td class="submenu-indent text-secondary">
                                                        <i class="bx bx-subdirectory-right me-2 text-muted"></i>{{ $sub->submenu_title }}
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input perm-cb check-view"
                                                               data-menu-id="{{ $menu->shop_menu_id }}" data-submenu-id="{{ $sub->shop_submenu_id }}" data-action="view">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input perm-cb check-create"
                                                               data-menu-id="{{ $menu->shop_menu_id }}" data-submenu-id="{{ $sub->shop_submenu_id }}" data-action="create">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input perm-cb check-update"
                                                               data-menu-id="{{ $menu->shop_menu_id }}" data-submenu-id="{{ $sub->shop_submenu_id }}" data-action="update">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input perm-cb check-delete"
                                                               data-menu-id="{{ $menu->shop_menu_id }}" data-submenu-id="{{ $sub->shop_submenu_id }}" data-action="delete">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-light py-3 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary px-4" onclick="savePermissions()">
                            <i class="bx bx-save me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── Create Role Modal ── --}}
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bx bx-shield-plus me-1"></i> Add New Shop Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="createRoleForm" onsubmit="handleCreateRole(event)">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-danger d-none" id="roleErrorAlert"></div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="role_name" placeholder="e.g. Inventory Manager, Catalog Editor" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Role Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="role_type" required>
                                <option value="admin">Admin Staff</option>
                                <option value="vendor">Vendor Staff</option>
                                <option value="delivery">Delivery Staff</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="createRoleBtn">Create Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

@include('shop.include.footer_link')

<script>
let currentRoleId = {{ $roles[0]->shop_role_id ?? 'null' }};

$(document).ready(function() {
    if (currentRoleId) {
        loadRolePermissions(currentRoleId);
    }

    // Auto-check View when Create, Update, or Delete is checked
    $(document).on('change', '.check-create, .check-update, .check-delete', function() {
        if ($(this).is(':checked')) {
            let row = $(this).closest('tr');
            row.find('.check-view').prop('checked', true);
        }
    });
});

function selectRole(roleId, roleName, elem) {
    currentRoleId = roleId;
    $('#rolesList .role-item').removeClass('active');
    $(elem).addClass('active');
    $('#currentRoleTitle').text(roleName);
    loadRolePermissions(roleId);
}

function loadRolePermissions(roleId) {
    // Reset all checkboxes first
    $('.perm-cb').prop('checked', false);

    $.ajax({
        url: "{{ url('shop/roles/permissions') }}/" + roleId,
        type: 'GET',
        success: function(res) {
            if (res.success && res.permissions) {
                res.permissions.forEach(function(p) {
                    let selector = 'tr[data-menu-id="' + p.menu_id + '"]';
                    if (p.submenu_id) {
                        selector += '[data-submenu-id="' + p.submenu_id + '"]';
                    } else {
                        selector += ':not([data-submenu-id])';
                    }

                    let row = $(selector);
                    if (row.length) {
                        if (p.can_view)   row.find('.check-view').prop('checked', true);
                        if (p.can_create) row.find('.check-create').prop('checked', true);
                        if (p.can_update) row.find('.check-update').prop('checked', true);
                        if (p.can_delete) row.find('.check-delete').prop('checked', true);
                    }
                });
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to load permissions for this role.', 'error');
        }
    });
}

function toggleSelectAll(check) {
    $('.perm-cb').prop('checked', check);
}

function savePermissions() {
    if (!currentRoleId) {
        Swal.fire('Warning', 'Please select a role first.', 'warning');
        return;
    }

    let permissions = [];
    $('#permissionTable tbody tr').each(function() {
        let row = $(this);
        let menuId = row.data('menu-id');
        let submenuId = row.data('submenu-id') || null;

        let canView   = row.find('.check-view').is(':checked') ? 1 : 0;
        let canCreate = row.find('.check-create').is(':checked') ? 1 : 0;
        let canUpdate = row.find('.check-update').is(':checked') ? 1 : 0;
        let canDelete = row.find('.check-delete').is(':checked') ? 1 : 0;

        if (canView || canCreate || canUpdate || canDelete) {
            permissions.push({
                menu_id: menuId,
                submenu_id: submenuId,
                can_view: canView,
                can_create: canCreate,
                can_update: canUpdate,
                can_delete: canDelete
            });
        }
    });

    let btn = $('#savePermissionsBtn');
    btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Saving...');

    $.ajax({
        url: "{{ route('shop.roles.permissions.save') }}",
        type: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            role_id: currentRoleId,
            permissions: permissions
        },
        success: function(res) {
            btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Save Changes');
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: res.message,
                    timer: 1800,
                    showConfirmButton: false
                });
            }
        },
        error: function(xhr) {
            btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Save Changes');
            let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to save permissions.';
            Swal.fire('Error', msg, 'error');
        }
    });
}

function handleCreateRole(e) {
    e.preventDefault();
    let form = $(e.target);
    let btn = $('#createRoleBtn');
    $('#roleErrorAlert').addClass('d-none');
    btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Creating...');

    $.ajax({
        url: "{{ route('shop.roles.create') }}",
        type: 'POST',
        data: form.serialize(),
        success: function(res) {
            btn.prop('disabled', false).text('Create Role');
            if (res.success) {
                $('#createRoleModal').modal('hide');
                form[0].reset();
                Swal.fire({
                    icon: 'success',
                    title: 'Role Created!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        },
        error: function(xhr) {
            btn.prop('disabled', false).text('Create Role');
            let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to create role.';
            $('#roleErrorAlert').removeClass('d-none').text(msg);
        }
    });
}

function deleteRole(roleId, e) {
    e.stopPropagation();
    Swal.fire({
        title: 'Delete this Role?',
        text: 'All assigned permissions for this role will also be removed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('shop.roles.delete') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    role_id: roleId
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Deleted!', res.message, 'success').then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error deleting role.';
                    Swal.fire('Error', msg, 'error');
                }
            });
        }
    });
}
</script>
</body>
</html>
