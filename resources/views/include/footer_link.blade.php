<script src="{{ $actual_url . '/admin-assets/libs/jquery/jquery.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/libs/@popperjs/core/umd/popper.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/libs/bootstrap/js/bootstrap.bundle.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/js/defaultmenu.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/libs/node-waves/waves.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/js/sticky.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/libs/simplebar/simplebar.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/js/simplebar.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/libs/@simonwep/pickr/pickr.es5.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/libs/jsvectormap/js/jsvectormap.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/libs/jsvectormap/maps/world-merc.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/libs/apexcharts/apexcharts.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/js/custom-switcher.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/js/custom.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/libs/izitoast/js/iziToast.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/libs/select2/select2.min.js' }}"></script>
<script src="{{ $actual_url . '/admin-assets/libs/flatpickr/flatpickr.min.js' }}"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>



<script>
    window.routes = window.routes || {};
    window.routes.leadTimeline = "{{ route('lead.timeline.get') }}";
    window.routes.composeEmailSend = "{{ route('compose.email.send') }}";
</script>
<script src="{{ $actual_url . '/admin-assets/js/leadTimeline.js' }}"></script>
<!-- Gmail Compose JS -->
<script src="{{ $actual_url . '/admin-assets/js/gmail-compose.js?v='.time() }}"></script>
<!-- Global Search Highlight Utility JS -->
<script src="{{ $actual_url . '/admin-assets/js/global-search-highlight.js?v='.time() }}"></script>
<script>
    function formatIndianCurrency(number) {
        if (number === null || number === undefined || number === '' || isNaN(number)) {
            return number;
        }

        number = parseFloat(number).toFixed(2);

        let [integer, decimal] = number.split('.');

        if (integer.length <= 3) {
            return '₹' + integer + '.' + decimal;
        }

        let lastThree = integer.slice(-3);
        let remaining = integer.slice(0, -3);

        remaining = remaining.replace(/\B(?=(\d{2})+(?!\d))/g, ',');

        return '₹' + remaining + ',' + lastThree + '.' + decimal;
    }

    $(document).ready(function () {
        let searchTimeout;



        $(document).on('click', function (e) {
            if (!$(e.target).closest('.custom-header-search').length) {
                $('#headersearch').removeClass('searchdrop');
            }
        });

        $('#typehead').on('focus', function () {
            $('#headersearch').addClass('searchdrop');
            if ($(this).val().trim().length < 2) {
                $('#search-results').html(`
                    <div class="text-center py-3 my-1">
                        <span class="avatar avatar-sm rounded-circle bg-primary-transparent text-primary mb-2" style="width:38px;height:38px;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa-solid fa-keyboard fs-15"></i>
                        </span>
                        <p class="text-muted fs-12 mb-0">Type at least <b>2 characters</b> to search Hexagon CRM</p>
                    </div>
                `);
            }
        });

        $('#typehead').on('input', function () {
            $('#headersearch').addClass('searchdrop');
            clearTimeout(searchTimeout);
            const query = $(this).val().trim();

            if (query.length < 2) {
                $('#search-results').html(`
                    <div class="text-center py-3 my-1">
                        <span class="avatar avatar-sm rounded-circle bg-primary-transparent text-primary mb-2" style="width:38px;height:38px;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fa-solid fa-keyboard fs-15"></i>
                        </span>
                        <p class="text-muted fs-12 mb-0">Type at least <b>2 characters</b> to search Hexagon CRM</p>
                    </div>
                `);
                return;
            }

            searchTimeout = setTimeout(function () {
                $.ajax({
                    url: '{{ route('quick.search') }}',
                    type: 'GET',
                    data: {
                        query: query
                    },
                    dataType: 'json',
                    success: function (response) {
                        let html = '';
                        let shortcutIndex = 1;

                        const ensureSearchQuery = (url) => {
                            if (!url || url === '#') return url;
                            if (url.includes('search=')) return url;
                            const sep = url.includes('?') ? '&' : '?';
                            return url + sep + 'search=' + encodeURIComponent(query);
                        };

                        // Helper to render icon safely (handles both HTML string and plain CSS class name)
                        const renderIcon = (icon) => {
                            if (!icon) return '<i class="bx bx-cube"></i>';
                            icon = icon.trim();
                            if (icon.startsWith('<')) {
                                return icon;
                            }
                            return `<i class="${icon}"></i>`;
                        };

                        const hasResults = (
                            (response.notes && response.notes.length > 0) ||
                            (response.calls && response.calls.length > 0) ||
                            (response.attachments && response.attachments.length > 0) ||
                            (response.leads && response.leads.length > 0) ||
                            (response.deals && response.deals.length > 0) ||
                            (response.customers && response.customers.length > 0) ||
                            (response.quotations && response.quotations.length > 0) ||
                            (response.invoices && response.invoices.length > 0) ||
                            (response.products && response.products.length > 0) ||
                            (response.staff && response.staff.length > 0) ||
                            (response.campaigns && response.campaigns.length > 0) ||
                            (response.masters && response.masters.length > 0) ||
                            (response.menus && response.menus.length > 0) ||
                            (response.submenus && response.submenus.length > 0)
                        );

                        if (hasResults) {
                            html += `<style>
                                #search-results { max-height: 380px !important; overflow-y: auto !important; overflow-x: hidden !important; padding-right: 4px !important; }
                                #search-results::-webkit-scrollbar { width: 5px !important; }
                                #search-results::-webkit-scrollbar-track { background: #f1f5f9 !important; border-radius: 4px !important; }
                                #search-results::-webkit-scrollbar-thumb { background: #cbd5e1 !important; border-radius: 4px !important; }
                                #search-results::-webkit-scrollbar-thumb:hover { background: #94a3b8 !important; }
                                #search-results ul { list-style: none; padding-left: 0; margin-bottom: 12px; }
                                .search-app { transition: all 0.15s ease; border-radius: 6px; margin-bottom: 4px; }
                                .search-app:hover { background-color: #f1f5f9; }
                                .active-search-item { background-color: #007b8a !important; }
                                .active-search-item a, .active-search-item span, .active-search-item small, .active-search-item div { color: #ffffff !important; }
                                .active-search-item .badge { background-color: #ffffff !important; color: #007b8a !important; }
                                .search-app a { display: flex; width: 100%; align-items: center; justify-content: space-between; text-decoration: none; padding: 10px 12px; color: #475569; }
                                .search-app-content { display: flex; align-items: flex-start; gap: 12px; width: 100%; }
                                .search-app i.search-icon { width: 34px !important; height: 34px !important; flex-shrink: 0; display: inline-flex !important; align-items: center !important; justify-content: center !important; font-size: 15px !important; border-radius: 50% !important; background-color: #e0f2fe !important; color: #0369a1 !important; margin-top: 2px; }
                                .active-search-item i.search-icon { background-color: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important; }
                                .search-item-title { font-size: 13px; font-weight: 600; color: #1e293b; line-height: 1.3; }
                                .search-item-sub { font-size: 11px; color: #64748b; margin-top: 2px; line-height: 1.3; }
                                .search-item-note-snippet { font-size: 12px; font-style: italic; color: #0f766e; background: #f0fdfa; padding: 4px 8px; border-radius: 4px; margin-top: 4px; border-left: 3px solid #0d9488; display: block; word-break: break-word; }
                                .active-search-item .search-item-note-snippet { background: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border-color: #ffffff !important; }
                                .search-item-call-snippet { font-size: 12px; font-style: italic; color: #1d4ed8; background: #eff6ff; padding: 4px 8px; border-radius: 4px; margin-top: 4px; border-left: 3px solid #2563eb; display: block; word-break: break-word; }
                                .active-search-item .search-item-call-snippet { background: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border-color: #ffffff !important; }
                            </style>`;

                            if (response.notes && response.notes.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-regular fa-note-sticky me-1"></i> CRM Notes & Activities</p>';
                                html += '<ul>';
                                response.notes.forEach(function (note) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${note.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(note.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${note.title} <small class="text-muted fw-normal ms-1">(${note.note_title || 'Note'})</small></div>
                                                        <span class="search-item-note-snippet">"${note.snippet}"</span>
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.calls && response.calls.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-solid fa-phone-volume me-1"></i> CRM Calls & Meetings</p>';
                                html += '<ul>';
                                response.calls.forEach(function (call) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${call.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(call.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${call.title} <small class="text-muted fw-normal ms-1">(${call.note_title || 'Call'})</small></div>
                                                        <span class="search-item-call-snippet">"${call.snippet}"</span>
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.attachments && response.attachments.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-solid fa-paperclip me-1"></i> CRM Attachments</p>';
                                html += '<ul>';
                                response.attachments.forEach(function (att) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${att.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(att.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${att.title} <small class="text-muted fw-normal ms-1">(${att.note_title || 'Attachment'})</small></div>
                                                        <span class="search-item-note-snippet"><i class="fa-solid fa-file me-1"></i>"${att.snippet}"</span>
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.leads && response.leads.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-solid fa-user-tie me-1"></i> Leads & Enquiries</p>';
                                html += '<ul>';
                                response.leads.forEach(function (lead) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${lead.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(lead.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${lead.title}</div>
                                                        ${lead.subtitle ? `<div class="search-item-sub">${lead.subtitle}</div>` : ''}
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.deals && response.deals.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-solid fa-briefcase me-1"></i> Deals</p>';
                                html += '<ul>';
                                response.deals.forEach(function (deal) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${deal.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(deal.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${deal.title}</div>
                                                        ${deal.subtitle ? `<div class="search-item-sub">${deal.subtitle}</div>` : ''}
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.customers && response.customers.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-solid fa-building me-1"></i> Customers</p>';
                                html += '<ul>';
                                response.customers.forEach(function (customer) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${customer.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(customer.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${customer.title}</div>
                                                        ${customer.subtitle ? `<div class="search-item-sub">${customer.subtitle}</div>` : ''}
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.quotations && response.quotations.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-solid fa-file-signature me-1"></i> Quotations</p>';
                                html += '<ul>';
                                response.quotations.forEach(function (quot) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${quot.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(quot.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${quot.title}</div>
                                                        ${quot.subtitle ? `<div class="search-item-sub">${quot.subtitle}</div>` : ''}
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.invoices && response.invoices.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-solid fa-file-invoice-dollar me-1"></i> Invoices & Proforma</p>';
                                html += '<ul>';
                                response.invoices.forEach(function (inv) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${inv.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(inv.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${inv.title}</div>
                                                        ${inv.subtitle ? `<div class="search-item-sub">${inv.subtitle}</div>` : ''}
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.products && response.products.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-solid fa-box-open me-1"></i> Products & Services</p>';
                                html += '<ul>';
                                response.products.forEach(function (prod) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${prod.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(prod.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${prod.title}</div>
                                                        ${prod.subtitle ? `<div class="search-item-sub">${prod.subtitle}</div>` : ''}
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.staff && response.staff.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-solid fa-users me-1"></i> Staff Members</p>';
                                html += '<ul>';
                                response.staff.forEach(function (stf) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${stf.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(stf.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${stf.title}</div>
                                                        ${stf.subtitle ? `<div class="search-item-sub">${stf.subtitle}</div>` : ''}
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.campaigns && response.campaigns.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-solid fa-bullhorn me-1"></i> Marketing Campaigns</p>';
                                html += '<ul>';
                                response.campaigns.forEach(function (camp) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${camp.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(camp.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${camp.title}</div>
                                                        ${camp.subtitle ? `<div class="search-item-sub">${camp.subtitle}</div>` : ''}
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.masters && response.masters.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;"><i class="fa-solid fa-sliders me-1"></i> Master Data (GST, Status, Categories, etc.)</p>';
                                html += '<ul>';
                                response.masters.forEach(function (mst) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${mst.url}">
                                                <div class="search-app-content">
                                                    ${renderIcon(mst.icon)}
                                                    <div style="flex:1; min-width:0;">
                                                        <div class="search-item-title">${mst.title}</div>
                                                        ${mst.subtitle ? `<div class="search-item-sub">${mst.subtitle}</div>` : ''}
                                                    </div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.menus && response.menus.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;">Menus</p>';
                                html += '<ul>';
                                response.menus.forEach(function (menu) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${menu.url}">
                                                <div class="search-app-content" style="align-items: center;">
                                                    ${renderIcon(menu.icon)}
                                                    <div class="search-item-title">${menu.title}</div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                            if (response.submenus && response.submenus.length > 0) {
                                html += '<p class="fw-bold text-uppercase text-muted mb-2 fs-11 tracking-wider" style="letter-spacing: 0.05em; padding-left: 8px;">Submenus</p>';
                                html += '<ul>';
                                response.submenus.forEach(function (submenu) {
                                    let shortcut = (shortcutIndex <= 9) ? `Alt+${shortcutIndex}` : '';
                                    let badge = shortcut ? `<span class="badge bg-primary-transparent text-primary ms-auto flex-shrink-0" style="font-size: 10px;">${shortcut}</span>` : '';
                                    html += `
                                        <li class="search-app">
                                            <a href="${submenu.url}">
                                                <div class="search-app-content" style="align-items: center;">
                                                    ${renderIcon(submenu.icon)}
                                                    <div class="search-item-title">${submenu.title} <small class="text-muted fw-normal">(${submenu.parent})</small></div>
                                                </div>
                                                ${badge}
                                            </a>
                                        </li>`;
                                    shortcutIndex++;
                                });
                                html += '</ul>';
                            }

                        } else {
                            html += `
                                <div class="text-center py-4 my-2" style="background: #fdf2f8; border-radius: 10px; border: 1px dashed #fbcfe8; padding: 24px 16px;">
                                    <div class="mb-2">
                                        <span class="avatar avatar-md rounded-circle" style="width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; background: #ffe4e6; color: #e11d48; margin: 0 auto;">
                                            <i class="fa-solid fa-magnifying-glass fs-18"></i>
                                        </span>
                                    </div>
                                    <h6 class="fw-semibold text-dark mb-1" style="font-size: 14px;">Result not found for "${query}"</h6>
                                    <p class="text-muted fs-12 mb-0" style="max-width: 280px; margin: 0 auto;">We searched across Leads, Customers, Quotes, Invoices, Notes, Calls, and Masters but found no matches.</p>
                                </div>`;
                        }

                        $('#search-results').html(html);
                    },
                    error: function (xhr) {
                        console.error('Search error:', xhr.responseText);
                    }
                });
            }, 300);
        });

        $(document).on('keydown', function (e) {

            if (e.altKey && e.which >= 49 && e.which <= 57) {
                let items = $('#search-results .search-app');
                let index = e.which - 49; // 49 is '1'
                if (items.length > index) {
                    e.preventDefault();
                    let href = items.eq(index).find('a').attr('href');
                    if (href) window.location.href = href;
                }
            }
        });

        $('#typehead').on('keydown', function (e) {
            let items = $('#search-results .search-app');
            if (items.length === 0) return;

            let activeIndex = items.index(items.filter('.active-search-item'));

            if (e.which === 40) { // Arrow Down
                e.preventDefault();
                items.removeClass('active-search-item');
                activeIndex = (activeIndex + 1) % items.length;
                items.eq(activeIndex).addClass('active-search-item');
            } else if (e.which === 38) { // Arrow Up
                e.preventDefault();
                items.removeClass('active-search-item');
                activeIndex = activeIndex - 1;
                if (activeIndex < 0) activeIndex = items.length - 1;
                items.eq(activeIndex).addClass('active-search-item');
            } else if (e.which === 13) { // Enter
                e.preventDefault();
                if (activeIndex >= 0) {
                    let href = items.eq(activeIndex).find('a').attr('href');
                    if (href) window.location.href = href;
                } else if (items.length > 0) {
                    let href = items.eq(0).find('a').attr('href');
                    if (href) window.location.href = href;
                }
            }
        });

        $(document).on('keydown', function (e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'F') {
                e.preventDefault();
                $('#typehead').focus();
                $('#headersearch').addClass('searchdrop');
            }
        });
    });
</script>
<style>
    .qr-code-image {
        animation: fadeInScale 0.5s ease-out;
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .min-h-200 {
        min-height: 200px;
    }

    #whatsappStatusBadge {
        font-weight: 600;
        border-radius: 4px;
    }
</style>

<script>
    let login_type = "{{ session('login_type') }}";

    if (login_type === 'Company') {
        $(document).ready(function () {

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            const qrContainer = $('#qrCodeContainer');
            const qrStatus = $('#qrStatus');
            const whatsappBadge = $('#whatsappStatusBadge');
            const qrModal = $('#qrModal');
            const logoutBtn = $('#logoutWhatsApp');
            const createSessionBtn = $('#createSessionBtn');
            let qrInterval = null;

            function startPolling() {
                if (!qrInterval) qrInterval = setInterval(fetchQR, 5000);
            }

            function stopPolling() {
                if (qrInterval) {
                    clearInterval(qrInterval);
                    qrInterval = null;
                }
            }

            function fetchQR() {
                $.ajax({
                    url: "{{ route('wa.status') }}",
                    method: 'GET',
                    success: function (data) {
                        qrContainer.empty();
                        qrStatus.empty();

                        // More robust parsing to catch different API wrapper formats
                        const status = data?.data?.status || data?.status || data?.state;
                        const qrData = data?.data?.qr || data?.qr;

                        logoutBtn.hide();
                        createSessionBtn.hide();

                        // Catch generic connection or reconnecting states
                        if (status === 'reconnecting' || status === 'connecting') {
                            qrStatus.html('<h5 class="mt-3">Initializing Session...</h5><p class="text-muted">Please wait...</p>');
                            whatsappBadge.text('Connecting...').removeClass('bg-danger bg-success bg-info text-white').addClass('bg-warning text-dark');
                        }
                        // Handle both 'qr_ready' and 'qr' statuses
                        else if (status === 'qr_ready' || status === 'qr') {
                            qrStatus.html('<h5 class="mb-3">Scan QR Code</h5>');
                            if (qrData) {
                                qrContainer.html(`
                                <img src="${qrData}" alt="WhatsApp QR" class="img-fluid qr-code-image" style="max-width: 250px; border: 2px solid #eee; border-radius: 8px;">
                                <button class="btn btn-outline-primary btn-sm mt-3" id="refreshQRBtn"><i class="fas fa-sync-alt"></i> Refresh QR</button>
                            `);
                            } else {
                                qrContainer.html('<div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Generating QR...</p>');
                            }
                            whatsappBadge.text('Scan QR').removeClass('bg-danger bg-success bg-warning text-dark').addClass('bg-info text-white');
                        }
                        // Handle offline, errors, or 500 server connection issues
                        else if (status === 'not_found' || status === 'error' || status === 500 || status === 'logged_out')  {
                            qrStatus.html('<h5 class="text-danger mt-3">No Active Session</h5><p class="text-muted">Click below to generate a new QR code.</p>');
                            whatsappBadge.text('Disconnected').removeClass('bg-success bg-info bg-warning text-dark text-white').addClass('bg-danger text-white');
                            createSessionBtn.show();
                        }
                        // Handle both 'connected' and 'open' (Baileys default) statuses
                        else if (status === 'connected' || status === 'open') {
                            qrStatus.html('<i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i><h5 class="mt-3 text-success">Connected Successfully!</h5>');
                            whatsappBadge.text('Connected').removeClass('bg-danger bg-info bg-warning text-dark').addClass('bg-success text-white');
                            logoutBtn.show();
                            stopPolling();
                        }
                    },
                    error: function () {
                        whatsappBadge.text('Error').removeClass('bg-success bg-info bg-warning text-dark').addClass('bg-danger text-white');
                    }
                });
            }

            function createSession() {
                let btn = createSessionBtn;
                btn.text('Creating...').prop('disabled', true);

                $.ajax({
                    url: "{{ route('wa.create') }}",
                    method: 'GET',
                    success: function (res) {
                        iziToast.success({ title: 'Success', message: 'Session created! Generating QR...', position: 'topRight' });
                        startPolling();
                        fetchQR();
                    },
                    error: function () {
                        iziToast.error({ title: 'Error', message: 'Failed to create session.', position: 'topRight' });
                    },
                    complete: function () {
                        btn.text('Create New Session').prop('disabled', false);
                    }
                });
            }

            qrModal.on('show.bs.modal', function () {
                startPolling();
                fetchQR();
            });

            qrModal.on('hide.bs.modal', function () {
                stopPolling();
            });

            createSessionBtn.on('click', function (e) {
                e.preventDefault();
                createSession();
            });

            $(document).on('click', '#refreshQRBtn', function (e) {
                e.preventDefault();
                createSession();
            });

            logoutBtn.on('click', function (e) {
                e.preventDefault();
                let btn = $(this);
                btn.text('Disconnecting...').prop('disabled', true);

                $.ajax({
                    url: "{{ route('wa.create') }}", // Calling create overrides the old session, effectively disconnecting it
                    method: 'GET',
                    success: function () {
                        iziToast.success({ title: 'Disconnected', message: 'WhatsApp session disconnected.', position: 'topRight' });
                        bootstrap.Modal.getInstance(document.getElementById('qrModal')).hide();
                        startPolling();
                        fetchQR();
                    },
                    complete: function () {
                        btn.text('Disconnect').prop('disabled', false);
                    }
                });
            });

            // Initialize background check on load
            fetchQR();
        });
    }
</script>

<!-- Tooltip Help Modal Component -->
@include('components.tooltip-help-modal')