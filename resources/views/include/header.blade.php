@php
    $dashboard = '#';
    if (session('login_type') == 'Company') {
        $dashboard = route('dashboard');
    } elseif (session('login_type') == 'Staff' || session('login_type') == 'Manager') {
        $dashboard = route('dashboard');
    } else {
        $dashboard = route('main.dashboard');
    }
@endphp


@php

    use App\Models\Company_submenu;
    use App\Models\Company_menu;
    $menuTree = [];

    if (session()->get('login_type') == 'Company') {
        $companyId = session()->get('company_id');
        $permissions = DB::table('company_permission')->where('company_id', $companyId)->get();

        $menuIds = $permissions->pluck('c_menu_id')->unique()->toArray();
        $submenuIds = $permissions->pluck('c_sub_menu_id')->filter()->unique()->toArray();

        $menus = Company_menu::leftJoin('menu_type', 'company_menus.menu_type_id', '=', 'menu_type.menu_type_id')
            ->select('company_menus.*', 'menu_type.type_name', 'menu_type.order_by')
            ->whereIn('company_menus.c_menu_id', $menuIds)
            ->orderBy('menu_type.order_by', 'asc')
            ->orderBy('company_menus.menu_order', 'asc')
            ->get()
            ->keyBy('c_menu_id');

        $submenus = Company_submenu::whereIn('c_sub_menu_id', $submenuIds)
            ->where('status', 0) // Add this line
            ->orderBy('submenu_order')
            ->get()
            ->groupBy('c_menu_id');

        foreach ($menus as $menuId => $menu) {
            $menuTree[] = [
                'id' => $menu->c_menu_id,
                'name' => $menu->menu_title,
                'icon' => $menu->menu_icon ?? 'bx bx-cube',
                'route' => $menu->menu_route,
                'has_submenu' => $menu->is_submenu == 1,
                'submenus' => $submenus->get($menuId) ?? [],
                'type_name' => $menu->type_name ?? 'General',
            ];
        }
    }

    if (session()->get('login_type') == 'Staff' || session()->get('login_type') == 'Manager') {
        $roleId = session()->get('role_id');
        $permissions = DB::table('role_menu_permissions')->where('role_id', $roleId)->get();

        $menuIds = $permissions->pluck('menu_id')->unique()->toArray();
        $submenuIds = $permissions->pluck('sub_menu_id')->filter()->unique()->toArray();

        $menus = Company_menu::leftJoin('menu_type', 'company_menus.menu_type_id', '=', 'menu_type.menu_type_id')
            ->select('company_menus.*', 'menu_type.type_name', 'menu_type.order_by')
            ->whereIn('company_menus.c_menu_id', $menuIds)
            ->orderBy('menu_type.order_by', 'asc')
            ->orderBy('company_menus.menu_order', 'asc')
            ->get()
            ->keyBy('c_menu_id');

        $submenus = Company_submenu::whereIn('c_sub_menu_id', $submenuIds)
            ->orderBy('submenu_order')
            ->get()
            ->groupBy('c_menu_id');

        foreach ($menus as $menuId => $menu) {
            $menuTree[] = [
                'id' => $menu->c_menu_id,
                'name' => $menu->menu_title,
                'icon' => $menu->menu_icon ?? 'fa fa-cube',
                'route' => $menu->menu_route,
                'has_submenu' => $menu->is_submenu == 1,
                'submenus' => $submenus->get($menuId) ?? [],
                'type_name' => $menu->type_name ?? 'General',
            ];
        }
        // dd($menuTree)->toArray();
    }

@endphp

@php
    use Illuminate\Support\Facades\Route;

    function isActiveRoute($route)
    {
        return (Route::has($route) && request()->routeIs($route)) || request()->is($route . '/*');
    }

    function isMenuActive($menu, $submenus)
    {
        if (isActiveRoute($menu['route'])) {
            return true;
        }

        foreach ($submenus as $submenu) {
            if (Route::has($submenu->sub_menu_route) && request()->routeIs($submenu->sub_menu_route)) {
                return true;
            }
        }
        return false;
    }
@endphp

<style>
    .slide>a>span.side-menu__icon {
        margin-top: -5px;
    }

    .slide.active>a.side-menu__item.active {
        font-weight: bold;
    }

    /* Sidebar theme using Hexagon logo teal colors */
    :root {
        /* Deep teal gradient (logo dark teal) */
        --sidebar-bg: #00454f;
        /* Vibrant turquoise (logo accent) for active */
        --sidebar-active-bg: #007b8a;
        /* Slightly lighter teal for hover */
        --sidebar-hover-bg: #00606e;
        /* Bright turquoise highlight border */
        --sidebar-accent: #00b4b4;
    }

    #sidebar.app-sidebar {
        background: linear-gradient(180deg, #004f5a 0%, #003840 100%) !important;
        border-right: none !important;
        box-shadow: 2px 0 16px rgba(0, 70, 80, 0.35);
    }

    #sidebar.app-sidebar .main-sidebar-header {
        background: linear-gradient(135deg, #005f6b 0%, #004550 100%) !important;
        border-bottom: 1px solid rgba(0, 180, 180, 0.2) !important;
        border-right: none !important;
    }

    #sidebar.app-sidebar .main-sidebar {
        background: transparent !important;
    }

    .app-header {
        border-left: none !important;
        border-bottom: 1px solid rgba(0, 180, 180, 0.15) !important;
        background: #ffffff !important;
    }

    /* Force all text, labels, icons, and angles inside sidebar items to be white */
    #sidebar.app-sidebar .side-menu__item,
    #sidebar.app-sidebar .side-menu__item *,
    #sidebar.app-sidebar .side-menu__label,
    #sidebar.app-sidebar .side-menu__icon i,
    #sidebar.app-sidebar .side-menu__angle {
        color: rgba(255, 255, 255, 0.92) !important;
    }

    /* Sidebar menu item layout details */
    #sidebar.app-sidebar .side-menu__item {
        border-radius: 6px;
        margin: 3px 10px;
        padding: 10px 15px;
        transition: all 0.2s ease;
    }

    /* Hover state background */
    #sidebar.app-sidebar .side-menu__item:hover {
        background-color: var(--sidebar-hover-bg) !important;
        color: #ffffff !important;
    }

    /* Active / Open state background */
    #sidebar.app-sidebar .slide.active>.side-menu__item,
    #sidebar.app-sidebar .slide.open>.side-menu__item,
    #sidebar.app-sidebar .side-menu__item.active {
        background: linear-gradient(90deg, #007b8a 0%, #009aaa 100%) !important;
        border-left: 3px solid var(--sidebar-accent) !important;
        box-shadow: 0 2px 8px rgba(0, 180, 180, 0.25) !important;
        color: #ffffff !important;
    }

    /* Submenu list styling (child slide-menu) */
    #sidebar.app-sidebar .slide-menu.child1 {
        background-color: rgba(0, 0, 0, 0.15) !important;
        margin: 0 10px 5px 25px !important;
        padding: 5px 0 !important;
        border-radius: 4px;
        border-left: 1px dashed rgba(255, 255, 255, 0.25) !important;
    }

    /* Submenu items list resets */
    #sidebar.app-sidebar .slide-menu.child1 li {
        padding: 0 !important;
    }

    /* Submenu links styling */
    #sidebar.app-sidebar .slide-menu.child1 a.side-menu__item {
        font-size: 13px !important;
        margin: 2px 5px !important;
        padding: 6px 12px 6px 28px !important;
        /* Added left padding for bullet */
        background-color: transparent !important;
        position: relative !important;
    }

    /* Style and position the submenu bullet dot (white & aligned) */
    #sidebar.app-sidebar .slide-menu.child1 .side-menu__item:before {
        content: "" !important;
        position: absolute !important;
        left: 12px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 6px !important;
        height: 6px !important;
        border: 1px solid #ffffff !important;
        background-color: transparent !important;
        border-radius: 50% !important;
        opacity: 0.7 !important;
    }

    #sidebar.app-sidebar .slide-menu.child1 .side-menu__item:hover:before,
    #sidebar.app-sidebar .slide-menu.child1 .side-menu__item.active:before {
        background-color: #ffffff !important;
        border-color: #ffffff !important;
        opacity: 1 !important;
    }

    /* Submenu links hover / active background */
    #sidebar.app-sidebar .slide-menu.child1 a.side-menu__item:hover,
    #sidebar.app-sidebar .slide-menu.child1 a.side-menu__item.active {
        background-color: rgba(255, 255, 255, 0.1) !important;
        font-weight: bold !important;
    }

    /* Navbar Back Button style - updated for white header */
    .navbar-back-btn {
        width: 32px;
        height: 32px;
        border: 1.5px solid #ccecee;
        border-radius: 8px;
        color: #005f6b !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        margin: 0;
        text-decoration: none;
        vertical-align: middle;
        background-color: #f0fafa;
    }

    .navbar-back-btn:hover {
        background-color: #00b4b4 !important;
        border-color: #00b4b4 !important;
        color: #ffffff !important;
        transform: scale(1.05);
    }

    .navbar-back-btn i {
        font-size: 0.75rem;
    }

    .slide__category {
        padding: 20px 15px 5px 20px;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .slide__category .category-name {
        font-size: 10px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 1px;
        white-space: nowrap;
        padding: 2px 10px;
        border-radius: 20px;
    }

    .slide__category::after {
        content: '';
        flex: 1;
        height: 1px;
        background-color: var(--cat-line, rgba(255, 255, 255, 0.15));
        margin-left: 12px;
        margin-right: 15px;
    }

    /* Bootstrap Collapse transitions overrides for Ynex sidebar */
    #sidebar.app-sidebar .slide-menu.child1.collapse:not(.show) {
        display: none !important;
        background-color: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        height: 0 !important;
    }

    #sidebar.app-sidebar .slide-menu.child1.collapsing {
        display: block !important;
        background-color: transparent !important;
        padding: 0 !important;
        margin: 0 10px 0 25px !important;
        border: none !important;
        transition: height 0.35s ease-in-out !important;
    }

    #sidebar.app-sidebar .slide-menu.child1.collapse.show {
        display: block !important;
    }

    #sidebar.app-sidebar .side-menu__item:not(.collapsed) .side-menu__angle {
        transform: rotate(180deg) !important;
    }

    /* Sidebar Logo Area — teal gradient matching sidebar */
    #sidebar.app-sidebar .main-sidebar-header {
        background: linear-gradient(135deg, #005f6b 0%, #004550 100%) !important;
        border-bottom: 1px solid rgba(0, 180, 180, 0.25) !important;
        border-right: none !important;
        padding: 10px 14px !important;
        min-height: 60px;
        display: flex;
        align-items: center;
    }

    #sidebar.app-sidebar .main-sidebar-header .header-logo {
        display: flex;
        align-items: center;
        width: 100%;
        /* White background pill behind logo */
        background: rgba(255, 255, 255, 0.92);
        border-radius: 10px;
        padding: 6px 14px;
    }

    /* Header bar — teal gradient matching sidebar top */
    .app-header {
        border-left: none !important;
        border-bottom: 1px solid rgba(0, 180, 180, 0.2) !important;
        background: linear-gradient(90deg, #005f6b 0%, #007b8a 60%, #009aaa 100%) !important;
        box-shadow: 0 2px 12px rgba(0, 60, 70, 0.25) !important;
    }

    /* Hamburger icon — white on teal */
    .sidemenu-toggle .header-link-icon,
    .sidemenu-toggle i {
        color: rgba(255, 255, 255, 0.90) !important;
    }

    /* User dropdown section in header */
    .header-profile-dropdown {
        border-top: 3px solid #00b4b4 !important;
    }


    /* === LOGO DISPLAY CONTROL ===
       Only show desktop-logo by default. Hide toggle/dark duplicates.
    */
    #sidebar.app-sidebar .main-sidebar-header .header-logo img {
        display: none !important;
    }

    #sidebar.app-sidebar .main-sidebar-header .header-logo img.desktop-logo {
        display: block !important;
        max-height: 40px !important;
        width: auto !important;
        max-width: 150px !important;
        object-fit: contain !important;
    }

    [data-toggled="icon-overlay-close"] #sidebar.app-sidebar .main-sidebar-header .header-logo img.desktop-logo,
    [data-toggled="icon-text-close"] #sidebar.app-sidebar .main-sidebar-header .header-logo img.desktop-logo {
        display: none !important;
    }

    [data-toggled="icon-overlay-close"] #sidebar.app-sidebar .main-sidebar-header .header-logo img.toggle-logo,
    [data-toggled="icon-text-close"] #sidebar.app-sidebar .main-sidebar-header .header-logo img.toggle-logo {
        display: block !important;
        max-height: 32px !important;
        width: auto !important;
        object-fit: contain !important;
    }

    /* === HEADER SEARCH BAR — styled for teal bg === */
    .main-header-center {
        display: flex !important;
        align-items: center !important;
        position: relative !important;
    }

    .custom-header-search .form-control,
    .custom-header-search input[type="text"] {
        background: rgba(255, 255, 255, 0.15) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.30) !important;
        color: #ffffff !important;
    }

    .custom-header-search input[type="text"]::placeholder {
        color: rgba(255, 255, 255, 0.55) !important;
    }

    .custom-header-search input[type="text"]:focus {
        background: rgba(255, 255, 255, 0.22) !important;
        border-color: rgba(255, 255, 255, 0.6) !important;
        outline: none !important;
    }

    .custom-header-search>div {
        border: 1.5px solid rgba(255, 255, 255, 0.30) !important;
        background: rgba(255, 255, 255, 0.12) !important;
    }

    .custom-header-search>div>i {
        color: rgba(255, 255, 255, 0.75) !important;
    }

    #headersearch i {
        color: inherit;
    }

    /* Back button — white on teal */
    .navbar-back-btn {
        width: 32px;
        height: 32px;
        border: 1.5px solid rgba(255, 255, 255, 0.40);
        border-radius: 8px;
        color: rgba(255, 255, 255, 0.90) !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        margin: 0;
        text-decoration: none;
        vertical-align: middle;
        background-color: rgba(255, 255, 255, 0.12);
    }

    .navbar-back-btn:hover {
        background-color: rgba(255, 255, 255, 0.25) !important;
        border-color: rgba(255, 255, 255, 0.70) !important;
        color: #ffffff !important;
        transform: scale(1.05);
    }

    .navbar-back-btn i {
        font-size: 0.75rem;
    }

    .user-profile-card {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 4px 10px !important;
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
    }

    .user-profile-card:hover {
        background: rgba(255, 255, 255, 0.12) !important;
        border: none !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        transform: none !important;
    }

    .user-avatar-box {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #ffffff;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .user-avatar-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-text-info {
        display: flex;
        flex-direction: column;
        min-width: 90px;
    }

    .user-text-info .uname {
        font-size: 13.5px;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.3;
        white-space: nowrap;
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        letter-spacing: 0.2px;
    }

    .user-text-info .urole {
        display: inline-block;
        font-size: 10px;
        font-weight: 800;
        color: #005662;
        background: #ffffff;
        border-radius: 12px;
        padding: 2px 9px;
        margin-top: 3px;
        letter-spacing: 0.5px;
        width: fit-content;
        text-transform: uppercase;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.10);
    }

    .user-caret {
        color: #ffffff;
        font-size: 11px;
        margin-left: 4px;
        opacity: 0.9;
        transition: transform 0.2s ease;
    }

    .user-profile-card:hover .user-caret {
        transform: translateY(2px);
        opacity: 1;
    }
</style>
<header class="app-header">
    @php
        $company_data = get_company_details(1);
        // dd($company_data);
        $userLogo = $company_data && $company_data->logo ? $company_data->logo : null;
        $favLogo = $company_data && $company_data->favicon ? $company_data->favicon : null;
        $Alt = $company_data && $company_data->name ? $company_data->name : null;
        $logoUrl = $userLogo
            ? $actual_url . '/uploads/company/' . $userLogo
            : $actual_url . '/admin_assets/images/brand-logos/enli-logo.png';
        $favUrl = $favLogo
            ? $actual_url . '/uploads/company/' . $favLogo
            : $actual_url . '/admin_assets/images/brand-logos/enli-logo.png';
        $loginType = session('login_type');
        $dashboardUrl = $loginType == 'Company' || $loginType == 'Staff' ? route('dashboard') : route('main.dashboard');
    @endphp

    @php

        function matchRoute($route)
        {
            // If it's a named route → match it
            if (Route::has($route)) {
                if (request()->routeIs($route) || request()->routeIs($route . '*')) {
                    return true;
                }
            }

            // If it's a URL path like "company/role"
            if (request()->is($route) || request()->is($route . '/*')) {
                return true;
            }

            return false;
        }

        function menuActive($menu)
        {
            if (matchRoute($menu['route'])) {
                return true;
            }

            if (!empty($menu['submenus'])) {
                foreach ($menu['submenus'] as $submenu) {
                    if (matchRoute($submenu->sub_menu_route)) {
                        return true;
                    }
                }
            }

            return false;
        }
    @endphp
    @php
        $settingsActive = request()->routeIs('company.role') || request()->routeIs('company.register');
    @endphp



    <style>
        @media (max-width: 991.98px) {
            .app-sidebar .main-sidebar-header {
                display: flex !important;
            }

            .main-sidebar {
                margin-block-start: 4rem !important;
            }
        }
    </style>

    <div class="main-header-container container-fluid">
        <div class="header-content-left" style="flex: 1; max-width: 800px; display: flex; align-items: center;">
            <div class="header-element">
                <div class="horizontal-logo d-none">
                    <a href="{{ $dashboard }}" class="header-logo">
                        <img src="{{ $logoUrl }}" style="width:100%;height:43px;" alt="{{ $Alt }}" class="desktop-logo">
                        <img src="{{ $logoUrl }}" style="width:38px;height:33px;" alt="{{ $Alt }}" class="toggle-logo">
                        <img src="{{ $logoUrl }}" style="width:100%;height:56px;" alt="{{ $Alt }}" class="desktop-dark">
                        <img src="{{ $logoUrl }}" style="width:38px;height:33px;" alt="{{ $Alt }}" class="toggle-dark">
                    </a>
                </div>
            </div>
            <div class="header-element" style="flex: 1; width: 100%; display: flex; align-items: center;">
                <a aria-label="anchor" href="javascript:void(0);" class="sidemenu-toggle header-link ms-2"
                    data-bs-toggle="sidebar">
                    <span class="open-toggle">
                        <i class="fs-13 fa fa-bars header-link-icon"></i>
                    </span>
                </a>
                <div class="d-none d-md-inline-flex align-items-center justify-content-center ms-3 me-3"
                    style="height: 100%;">
                    <a href="javascript:void(0);"
                        onclick="if(document.referrer && document.referrer.includes(window.location.hostname)){ window.location.href = document.referrer; } else { window.history.back(); }"
                        class="navbar-back-btn" title="Go Back">
                        <i class="fas fa-reply"></i>
                    </a>
                </div>
                <div class="custom-header-search d-flex align-items-center ms-2 ms-md-3"
                    style="position: relative; flex: 1; width: 100%; max-width: 700px; min-width: 360px;">
                    <style>
                        @media (max-width: 767.98px) {
                            .custom-header-search {
                                max-width: 210px !important;
                            }

                            .custom-header-search input::placeholder {
                                font-size: 11px !important;
                            }

                            #headersearch {
                                min-width: 290px !important;
                                max-width: 90vw !important;
                                left: -15px !important;
                            }
                        }
                    </style>
                    <div
                        style="display:flex; align-items:center; border:1.5px solid #b8dfe2; border-radius:9px; background:#f4fbfb; overflow:hidden; height:38px; width:100%; min-width:140px;">
                        <i class="fa fa-magnifying-glass"
                            style="padding:0 10px; color:#007b8a; font-size:14px; flex-shrink:0;"></i>
                        <input type="text" id="typehead" autocomplete="off"
                            placeholder="Global Search (Leads, Customers, Quotes, Invoices...)"
                            style="flex:1; border:none; background:transparent; outline:none; font-size:13px; color:#003840; padding:0; height:100%;">
                    </div>
                    <style>
                        #search-results {
                            max-height: 380px !important;
                            overflow-y: auto !important;
                            overflow-x: hidden !important;
                            padding-right: 4px !important;
                        }

                        #search-results::-webkit-scrollbar {
                            width: 5px !important;
                        }

                        #search-results::-webkit-scrollbar-track {
                            background: #f1f5f9 !important;
                            border-radius: 4px !important;
                        }

                        #search-results::-webkit-scrollbar-thumb {
                            background: #cbd5e1 !important;
                            border-radius: 4px !important;
                        }

                        #search-results::-webkit-scrollbar-thumb:hover {
                            background: #94a3b8 !important;
                        }
                    </style>
                    <div id="headersearch" class="header-search"
                        style="position: absolute; top: 100%; left: 0; width: 100%; min-width: 380px; background: #ffffff !important; opacity: 1 !important; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 9999; margin-top: 5px;">
                        <div class="p-2 p-md-3">
                            <div id="search-results">
                                <p class="fw-semibold text-center text-muted mb-2 fs-13">Global Search Results</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-content-right">
            <a href="#" class="nav-link nav-link-lg d-flex align-items-center d-none" data-bs-toggle="modal"
                data-bs-target="#qrModal" title="WhatsApp Status">
                <svg style="width: 24px; height: 24px; color: #25D366;" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M20.52 3.48A11.83 11.83 0 0012.01 0C5.39 0 .02 5.37.02 11.99c0 2.12.55 4.2 1.6 6.04L0 24l6.2-1.63a11.93 11.93 0 005.8 1.48h.01c6.62 0 11.99-5.37 11.99-11.99 0-3.2-1.25-6.2-3.48-8.38zM12 21.5c-1.82 0-3.6-.5-5.15-1.45l-.37-.22-3.68.97.98-3.59-.24-.38A9.47 9.47 0 012.5 12c0-5.25 4.25-9.5 9.5-9.5 2.54 0 4.92.99 6.71 2.79A9.45 9.45 0 0121.5 12c0 5.25-4.25 9.5-9.5 9.5zm5.27-7.23c-.29-.15-1.7-.84-1.97-.93-.27-.1-.47-.15-.67.15-.2.29-.77.93-.94 1.12-.17.2-.34.22-.63.07-.29-.15-1.23-.45-2.35-1.43-.87-.77-1.46-1.72-1.63-2.01-.17-.29-.02-.45.13-.6.13-.13.29-.34.43-.5.15-.17.2-.29.29-.48.1-.19.05-.35-.02-.5-.07-.15-.67-1.6-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.19 0-.5.07-.76.35-.26.29-1 1-1 2.44s1.03 2.83 1.17 3.03c.15.19 2.02 3.08 4.9 4.32.68.3 1.22.48 1.64.62.69.22 1.32.19 1.82.12.55-.08 1.7-.7 1.94-1.37.24-.67.24-1.25.17-1.37-.07-.12-.27-.19-.56-.34z" />
                </svg>
                <span id="whatsappStatusBadge" class="badge bg-warning text-dark"
                    style="font-size: 11px; padding: 4px 8px; border-radius: 12px; margin-left: 8px;">Checking</span>
            </a>
            <div class="header-element mainuserProfile">
                <a href="javascript:void(0);" class="header-link dropdown-toggle user-profile-card"
                    id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <div class="user-avatar-box">
                        <img alt="avatar" src="{{ $favUrl }}">
                    </div>
                    <div class="user-text-info">
                        <span class="uname">{{ get_session_name() }}</span>
                        <span class="urole">{{ Session::get('login_type') }}</span>
                    </div>
                    <i class="fa fa-chevron-down user-caret"></i>
                </a>

                {{-- Top User --}}
                <ul class="dropdown-menu  border-0 main-header-dropdown  overflow-hidden header-profile-dropdown"
                    aria-labelledby="mainHeaderProfile">
                    @if (session('login_type') == 'Company')
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i
                                    class="fs-13 me-2 fa fa-user"></i>Profile</a>
                        </li>
                    @endif
                    <li><a class="dropdown-item" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                class="fs-13 me-2 fa fa-arrow-to-right"></i>Log Out</a></li>
                </ul>
            </div>
            <div class="header-element ">
                <a aria-label="anchor" href="javascript:void(0);" class="header-link switcher-icon ms-1 d-none"
                    data-bs-toggle="offcanvas" data-bs-target="#switcher-canvas">
                    <i class="fa fa-cog bx-spin header-link-icon"></i>
                </a>
            </div>
        </div>
    </div>
</header>
<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header">
        <a href="{{ $dashboard }}" class="header-logo">
            <img src="{{ $logoUrl }}" style="max-height:40px; width:auto; object-fit:contain;" alt="{{$Alt}}"
                class="desktop-logo">
            <img src="{{ $logoUrl }}" style="max-height:32px; width:auto; object-fit:contain;" alt="{{$Alt}}"
                class="toggle-logo">
            <img src="{{ $logoUrl }}" style="max-height:40px; width:auto; object-fit:contain;" alt="{{$Alt}}"
                class="desktop-dark">
            <img src="{{ $logoUrl }}" style="max-height:32px; width:auto; object-fit:contain;" alt="{{$Alt}}"
                class="toggle-dark">
        </a>
    </div>
    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>
            <ul class="main-menu" id="main-menu-container">

                <!-- DASHBOARD -->
                <li class="slide {{ request()->routeIs('*dashboard*') ? 'active' : '' }}">
                    <a href="{{ $dashboard }}"
                        class="side-menu__item {{ request()->routeIs('*dashboard*') ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class='fs-13 fa fa-table-columns'></i>
                        </span>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                @if (session('login_type') == 'Super Admin')
                    <li class="slide {{ $settingsActive ? 'open active' : '' }}">
                        <a href="#submenu-super-settings"
                            class="side-menu__item {{ $settingsActive ? '' : 'collapsed' }} {{ $settingsActive ? 'active' : '' }}"
                            data-bs-toggle="collapse" role="button" aria-expanded="{{ $settingsActive ? 'true' : 'false' }}"
                            aria-controls="submenu-super-settings">
                            <span class="side-menu__icon"><i class='fs-13 fa fa-gear'></i></span>
                            <span class="side-menu__label">Settings</span>
                            <i class="fa-sharp fa-regular fa-caret-down side-menu__angle"></i>
                        </a>

                        <ul class="slide-menu child1 collapse {{ $settingsActive ? 'show' : '' }}"
                            id="submenu-super-settings" data-bs-parent="#main-menu-container">
                            <li class="slide side-menu__label1">
                                <a href="{{ route('company.role') }}"
                                    class="{{ request()->routeIs('company.role') ? 'active' : '' }}">
                                    Company Role
                                </a>
                            </li>
                            <li class="slide">
                                <a href="{{ route('company.role') }}"
                                    class="side-menu__item {{ request()->routeIs('company.role') ? 'active' : '' }}">
                                    Company Role
                                </a>
                            </li>

                            <li class="slide side-menu__label1">
                                <a href="{{ route('company.register') }}"
                                    class=" {{ request()->routeIs('company.register') ? 'active' : '' }}">
                                    Company Register
                                </a>
                            </li>
                            <li class="slide">
                                <a href="{{ route('company.register') }}"
                                    class="side-menu__item {{ request()->routeIs('company.register') ? 'active' : '' }}">
                                    Company Register
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif


                @if (
                        session()->get('login_type') == 'Company' ||
                        session()->get('login_type') == 'Staff' ||
                        session()->get('login_type') == 'Manager'
                    )

                    @php
                        $groupedMenus = collect($menuTree)->groupBy('type_name');
                    @endphp

                    @php
                        $colorIndex = 0;
                    @endphp
                    @foreach ($groupedMenus as $type_name => $menusGroup)
                        @if($type_name)
                            @php
                                $categoryColors = [
                                    // ['bg' => '#d4e3fc', 'text' => '#1a3b70', 'line' => '#d4e3fc'], // Light Blue
                                    // ['bg' => '#d1f2e0', 'text' => '#145938', 'line' => '#d1f2e0'], // Light Green
                                    ['bg' => '#fcebc7', 'text' => '#705312', 'line' => '#fcebc7'], // Light Yellow
                                    ['bg' => '#ecd4f7', 'text' => '#501d6e', 'line' => '#ecd4f7'], // Light Purple
                                    ['bg' => '#fcd9df', 'text' => '#7a1b2c', 'line' => '#fcd9df'], // Light Red/Pink
                                    ['bg' => '#ccf0ed', 'text' => '#115c57', 'line' => '#ccf0ed'], // Light Teal
                                    ['bg' => '#fce0cc', 'text' => '#7d3f11', 'line' => '#fce0cc'], // Light Orange
                                    ['bg' => '#dcf2d5', 'text' => '#2f5c22', 'line' => '#dcf2d5'], // Light Olive
                                    ['bg' => '#fcd4f2', 'text' => '#7a1465', 'line' => '#fcd4f2'], // Light Magenta
                                    ['bg' => '#d4f0fc', 'text' => '#155978', 'line' => '#d4f0fc'], // Light Cyan
                                    ['bg' => '#fcf2d4', 'text' => '#7a6114', 'line' => '#fcf2d4'], // Light Gold
                                    ['bg' => '#fcd4d4', 'text' => '#7a1414', 'line' => '#fcd4d4'], // Light Crimson
                                    ['bg' => '#e0d4fc', 'text' => '#29147a', 'line' => '#e0d4fc'], // Light Indigo
                                    ['bg' => '#d4fcd9', 'text' => '#147a24', 'line' => '#d4fcd9'], // Light Mint
                                    ['bg' => '#fcdbd4', 'text' => '#7a2d14', 'line' => '#fcdbd4'], // Light Rust
                                ];
                                $theme = $categoryColors[$colorIndex % 15];
                                $colorIndex++;
                            @endphp
                            <li class="slide__category" style="--cat-line: {{ $theme['line'] }};">
                                <span class="category-name"
                                    style="background-color: {{ $theme['bg'] }}; color: {{ $theme['text'] }};">
                                    {{ $type_name }}
                                </span>
                            </li>
                        @endif

                        @foreach ($menusGroup as $menu)
                            @php
                                $isActive = isMenuActive($menu, $menu['submenus']);
                            @endphp

                            @if ($menu['has_submenu'])
                                <!-- MENU WITH SUBMENU -->
                                <li class="slide {{ $isActive ? 'open active' : '' }}">
                                    <a href="#submenu-{{ $menu['id'] }}"
                                        class="side-menu__item {{ $isActive ? '' : 'collapsed' }} {{ $isActive ? 'active' : '' }}"
                                        data-bs-toggle="collapse" role="button" aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                                        aria-controls="submenu-{{ $menu['id'] }}">
                                        <span class="side-menu__icon">
                                            <i class="fs-13 {{ $menu['icon'] }}"></i>
                                        </span>
                                        <span class="side-menu__label">{{ $menu['name'] }}</span>
                                        <i class="fa-sharp fa-regular fa-caret-down side-menu__angle"></i>
                                    </a>

                                    <ul class="slide-menu child1 collapse {{ $isActive ? 'show' : '' }}" id="submenu-{{ $menu['id'] }}"
                                        data-bs-parent="#main-menu-container">
                                        @foreach ($menu['submenus'] as $submenu)
                                            @php
                                                $subActive =
                                                    Route::has($submenu->sub_menu_route) &&
                                                    request()->routeIs($submenu->sub_menu_route);
                                            @endphp

                                            <li class="slide side-menu__label1">
                                                <a href="{{ Route::has($submenu->sub_menu_route) ? route($submenu->sub_menu_route) : '#' }}"
                                                    class=" {{ $subActive ? 'active' : '' }}">
                                                    {{ $submenu->sub_menu_title }}
                                                </a>
                                            </li>

                                            <li class="slide">
                                                <a href="{{ Route::has($submenu->sub_menu_route) ? route($submenu->sub_menu_route) : '#' }}"
                                                    class="side-menu__item {{ $subActive ? 'active' : '' }}">
                                                    {{ $submenu->sub_menu_title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>

                                </li>
                            @else
                                <!-- MENU WITHOUT SUBMENU -->
                                @php $topActive = isActiveRoute($menu['route']); @endphp

                                <li class="slide {{ $topActive ? 'active' : '' }}">
                                    <a href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
                                        class="side-menu__item {{ $topActive ? 'active' : '' }}">
                                        <span class="side-menu__icon">
                                            <i class="fs-13 {{ $menu['icon'] }}"></i>
                                        </span>
                                        <span class="side-menu__label">{{ $menu['name'] }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endforeach
                @endif

                <!-- COMPANY SETTINGS -->
                @if (session()->get('login_type') == 'Company')
                    @php
                        $companySettingsActive = request()->is('menu.submenu') || request()->is('profile') || request()->is('password.change') || request()->is('mail_config');
                    @endphp
                    <li class="slide {{ $companySettingsActive ? 'open active' : '' }}">
                        <a href="#submenu-company-settings"
                            class="side-menu__item {{ $companySettingsActive ? '' : 'collapsed' }} {{ $companySettingsActive ? 'active' : '' }}"
                            data-bs-toggle="collapse" role="button"
                            aria-expanded="{{ $companySettingsActive ? 'true' : 'false' }}"
                            aria-controls="submenu-company-settings">
                            <span class="side-menu__icon"><i class='fs-13 fa fa-gear'></i></span>
                            <span class="side-menu__label">Settings</span>
                            <i class="fa-sharp fa-regular fa-caret-down side-menu__angle"></i>
                        </a>

                        <ul class="slide-menu child1 collapse {{ $companySettingsActive ? 'show' : '' }}"
                            id="submenu-company-settings" data-bs-parent="#main-menu-container">
                            <li class="slide side-menu__label1">
                                <a href="{{ route('menu.submenu') }}"
                                    class=" {{ request()->is('menu.submenu') ? 'active' : '' }}">
                                    Role Permission
                                </a>
                            </li>
                            <li class="slide">
                                <a href="{{ route('menu.submenu') }}"
                                    class="side-menu__item {{ request()->is('menu.submenu') ? 'active' : '' }}">
                                    Role Permission
                                </a>
                            </li>

                            <li class="slide side-menu__label1">
                                <a href="{{ route('password.change') }}"
                                    class=" {{ request()->is('password.change') ? 'active' : '' }}">
                                    Change Password
                                </a>
                            </li>
                            <li class="slide">
                                <a href="{{ route('password.change') }}"
                                    class="side-menu__item {{ request()->is('password.change') ? 'active' : '' }}">
                                    Change Password
                                </a>
                            </li>

                            <li class="slide side-menu__label1">
                                <a href="{{ route('mail_config') }}"
                                    class=" {{ request()->is('mail_config') ? 'active' : '' }}">
                                    Configuration
                                </a>
                            </li>
                            <li class="slide">
                                <a href="{{ route('mail_config') }}"
                                    class="side-menu__item {{ request()->is('mail_config') ? 'active' : '' }}">
                                    Configuration
                                </a>
                            </li>

                            <li class="slide side-menu__label1">
                                <a href="{{ route('profile') }}" class=" {{ request()->is('profile') ? 'active' : '' }}">
                                    Profile
                                </a>
                            </li>
                            <li class="slide">
                                <a href="{{ route('profile') }}"
                                    class="side-menu__item {{ request()->is('profile') ? 'active' : '' }}">
                                    Profile
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- LOGOUT -->
                <li class="slide">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="side-menu__item">
                        <span class=" side-menu__icon"><i
                                class='fs-13 fa-duotone fa-solid fa-arrow-left-from-bracket'></i></span>
                        <span class="side-menu__label">Logout</span>
                    </a>
                </li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </ul>

            <div class="slide-right" id="slide-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                </svg>
            </div>
        </nav>
    </div>
</aside>

<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel"><i class="fab fa-whatsapp text-success me-2"></i> WhatsApp
                    Connection</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qrStatus" class="mb-3"></div>
                <div id="qrCodeContainer"
                    class="d-flex flex-column align-items-center justify-content-center min-h-200">
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="createSessionBtn" style="display: none;">Create New
                    Session</button>
                <button type="button" class="btn btn-danger" id="logoutWhatsApp"
                    style="display: none;">Disconnect</button>
            </div>
        </div>
    </div>
</div>