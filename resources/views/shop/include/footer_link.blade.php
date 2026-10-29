{{-- ═══════════════════════════════════════════
     SHOP ADMIN PANEL — JS FOOTER LINKS
═══════════════════════════════════════════ --}}

{{-- jQuery (must be first) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- Bootstrap 5 Bundle --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- DataTables --}}
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

{{-- Select2 --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

{{-- Toastr --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

{{-- JSZip (for DataTables Excel export) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
// ── Global AJAX Setup ────────────────────────────────────────────────
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ── Toastr Config ────────────────────────────────────────────────────
toastr.options = {
    closeButton:       true,
    progressBar:       true,
    positionClass:     'toast-top-right',
    timeOut:           4000,
    extendedTimeOut:   1000,
    showEasing:        'swing',
    hideEasing:        'linear',
    showMethod:        'fadeIn',
    hideMethod:        'fadeOut',
    preventDuplicates: true,
};

// ── Sidebar Toggle ───────────────────────────────────────────────────
function toggleShopSidebar() {
    const sidebar  = document.getElementById('shopSidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const topbar   = document.getElementById('shopTopbar');
    const main     = document.getElementById('shopMain');
    const isDesktop= window.innerWidth >= 992;

    if (isDesktop) {
        sidebar.classList.toggle('collapsed-desktop');
        const isCollapsed = sidebar.classList.contains('collapsed-desktop');
        sidebar.style.width    = isCollapsed ? '0' : 'var(--shop-sidebar-width)';
        topbar.style.left      = isCollapsed ? '0' : 'var(--shop-sidebar-width)';
        main.style.marginLeft  = isCollapsed ? '0' : 'var(--shop-sidebar-width)';
    } else {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }
}

// ── Scroll-to-top button ─────────────────────────────────────────────
window.addEventListener('scroll', function() {
    const btn = document.getElementById('shopScrollTop');
    btn.style.display = window.scrollY > 400 ? 'flex' : 'none';
});

// ── Active link highlight ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all select2 with Bootstrap 5 theme
    $('select.shop-select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
    });

    // Load notifications
    loadShopNotifications();
});

// ── Notification Loader ──────────────────────────────────────────────
function loadShopNotifications() {
    $.get('{{ route("shop.notification.list") }}', function(data) {
        if (data.status === 'success' && data.data.length > 0) {
            let html = '';
            data.data.forEach(function(n) {
                html += `<a href="${n.link || '#'}" class="d-flex align-items-start gap-2 px-3 py-2 border-bottom text-decoration-none ${!n.is_read ? 'bg-light' : ''}" onclick="markNotifRead(${n.notification_id})">
                    <div class="rounded-circle bg-${n.color || 'primary'} text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;font-size:1rem;">
                        <i class="${n.icon || 'bx bx-bell'}"></i>
                    </div>
                    <div>
                        <div style="font-size:0.8rem;font-weight:600;color:#2d3748;">${n.title}</div>
                        <div style="font-size:0.75rem;color:#9aacbf;">${n.message}</div>
                        <div style="font-size:0.7rem;color:#b0bec5;margin-top:2px;">${n.time_ago}</div>
                    </div>
                </a>`;
            });
            $('#shopNotifList').html(html);
        }
    }).fail(function() { /* Silently fail */ });
}

function markNotifRead(id) {
    $.post('{{ route("shop.notification.read") }}', { id: id });
}

$('#markAllReadBtn').on('click', function(e) {
    e.preventDefault();
    $.post('{{ route("shop.notification.read_all") }}', function(r) {
        if (r.status === 'success') {
            loadShopNotifications();
            $('[data-id="shopNotifBtn"] .topbar-badge').remove();
        }
    });
});

// ── Logout ───────────────────────────────────────────────────────────
$('#shopLogoutBtn').on('click', function() {
    Swal.fire({
        title: 'Logout?',
        text:  'Are you sure you want to logout?',
        icon:  'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText:  'Yes, Logout',
        cancelButtonText:   'Cancel',
    }).then(function(result) {
        if (result.isConfirmed) {
            $.post('{{ route("shop.logout") }}', function(r) {
                if (r.success) window.location.href = r.redirect_url;
            });
        }
    });
});

// ── Global AJAX Success / Error Handlers ─────────────────────────────
function shopAjaxSuccess(response, callback) {
    hideShopLoader();
    if (response.status === 'success' || response.success === true) {
        toastr.success(response.message || 'Done!');
        if (typeof callback === 'function') callback(response);
    } else {
        toastr.error(response.message || 'An error occurred.');
    }
}

function shopAjaxError(xhr) {
    hideShopLoader();
    if (xhr.status === 422) {
        const errors = xhr.responseJSON?.errors || {};
        Object.keys(errors).forEach(function(key) {
            toastr.error(errors[key][0]);
            $(`[name="${key}"]`).addClass('is-invalid');
            $(`[name="${key}"]`).siblings('.invalid-feedback').text(errors[key][0]);
        });
    } else {
        toastr.error(xhr.responseJSON?.message || 'Something went wrong. Please try again.');
    }
}

// ── Shop DataTable Factory ───────────────────────────────────────────
/**
 * Creates a standard shop DataTable.
 * @param {string} tableId - The <table> element ID (with #)
 * @param {string} ajaxUrl - URL to fetch data from
 * @param {Array}  columns - Array of column definitions
 * @param {Object} extraOptions - Additional DataTables options
 */
function createShopDataTable(tableId, ajaxUrl, columns, extraOptions = {}) {
    let dtOptions = {
        processing:   true,
        serverSide:   true,
        responsive:   true,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        pageLength:   25,
        lengthMenu:   [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        ajax: {
            url:  ajaxUrl,
            type: 'GET',
            data: function(d) {
                // Merge any filter form data
                const filterData = $(tableId).closest('.shop-card').find('.shop-filter-form').serializeArray();
                filterData.forEach(function(item) { d[item.name] = item.value; });
            }
        },
        columns:      columns,
        order:        [[0, 'desc']],
        language: {
            processing:      '<div class="d-flex align-items-center gap-2 py-2"><span class="spinner-border spinner-border-sm text-primary"></span> <span>Loading...</span></div>',
            emptyTable:      '<div class="text-center py-4 text-muted"><i class="bx bx-data" style="font-size:2.5rem;"></i><br>No records found.</div>',
            zeroRecords:     'No matching records found.',
            info:            'Showing _START_ to _END_ of _TOTAL_ records',
            infoEmpty:       'Showing 0 to 0 of 0 records',
            infoFiltered:    '(filtered from _MAX_ total records)',
        },
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').each(function() {
                new bootstrap.Tooltip(this);
            });
        },
        ...extraOptions
    };
    return $(tableId).DataTable(dtOptions);
}

// ── Confirm Delete Utility ───────────────────────────────────────────
/**
 * Shows a SweetAlert confirm dialog then POSTs a delete request.
 * @param {string} url       - POST URL for the delete action
 * @param {Object} data      - POST data
 * @param {Function} onSuccess - Called after successful delete
 */
function shopConfirmDelete(url, data, onSuccess) {
    Swal.fire({
        title: 'Delete this record?',
        text:  'This action cannot be undone.',
        icon:  'warning',
        showCancelButton:    true,
        confirmButtonColor:  '#dc3545',
        cancelButtonColor:   '#6c757d',
        confirmButtonText:   '<i class="bx bx-trash me-1"></i> Yes, Delete',
        cancelButtonText:    'Cancel',
        reverseButtons:      true,
    }).then(function(result) {
        if (result.isConfirmed) {
            showShopLoader();
            $.post(url, data, function(response) {
                shopAjaxSuccess(response, onSuccess);
            }).fail(shopAjaxError);
        }
    });
}

// ── Global Search Handling ──────────────────────────────────────────
(function() {
    let searchTimer = null;
    const searchInput = $('#shopGlobalSearch');
    const resultBox   = $('#globalSearchResults');
    const spinner     = $('#searchSpinner');

    if (!searchInput.length) return;

    searchInput.on('input', function() {
        const query = $(this).val().trim();
        clearTimeout(searchTimer);

        if (query.length < 2) {
            resultBox.hide().empty();
            spinner.addClass('d-none');
            return;
        }

        spinner.removeClass('d-none');

        searchTimer = setTimeout(function() {
            $.ajax({
                url: "{{ route('shop.global_search') }}",
                data: { q: query },
                dataType: 'json',
                success: function(res) {
                    spinner.addClass('d-none');
                    if (!res.results || !res.results.length) {
                        resultBox.html('<div class="p-3 text-center text-muted small"><i class="bx bx-search-alt me-1"></i> No matching products, orders or customers found.</div>').show();
                        return;
                    }

                    let html = '<div class="py-2">';
                    let currentType = '';

                    res.results.forEach(item => {
                        if (item.type !== currentType) {
                            currentType = item.type;
                            html += `<div class="px-3 pt-2 pb-1 text-uppercase text-muted fw-bold" style="font-size:0.68rem; letter-spacing:0.5px;">${currentType}s</div>`;
                        }

                        let avatarHtml = `<div class="rounded d-flex align-items-center justify-content-center bg-light text-primary me-2" style="width:34px; height:34px; flex-shrink:0;">
                                            <i class="bx ${item.icon} fs-5"></i>
                                          </div>`;
                        if (item.image) {
                            avatarHtml = `<img src="${item.image}" alt="" class="rounded me-2 shadow-sm" style="width:34px; height:34px; object-fit:cover; flex-shrink:0;">`;
                        }

                        html += `<a href="${item.url}" class="dropdown-item d-flex align-items-center py-2 px-3 border-bottom border-light">
                                    ${avatarHtml}
                                    <div class="overflow-hidden">
                                        <div class="fw-semibold text-truncate text-dark" style="font-size:0.83rem;">${item.title}</div>
                                        <div class="text-muted text-truncate" style="font-size:0.75rem;">${item.subtitle}</div>
                                    </div>
                                 </a>`;
                    });

                    html += '</div>';
                    resultBox.html(html).show();
                },
                error: function() {
                    spinner.addClass('d-none');
                }
            });
        }, 300);
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.topbar-search').length) {
            resultBox.hide();
        }
    });

    searchInput.on('focus', function() {
        if ($(this).val().trim().length >= 2 && resultBox.children().length > 0) {
            resultBox.show();
        }
    });
})();
</script>
