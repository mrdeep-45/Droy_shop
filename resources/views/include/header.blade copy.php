@php
if (session('login_type') == 'Company') {
$dashboard = route('dashboard');
} elseif (session('login_type') == 'Staff') {
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

$menus = Company_menu::whereIn('c_menu_id', $menuIds)->orderBy('menu_order')->get()->keyBy('c_menu_id');

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
];
}
}

if (session()->get('login_type') == 'Staff') {
$roleId = session()->get('role_id');
$permissions = DB::table('role_menu_permissions')->where('role_id', $roleId)->get();

$menuIds = $permissions->pluck('menu_id')->unique()->toArray();
$submenuIds = $permissions->pluck('sub_menu_id')->filter()->unique()->toArray();

$menus = Company_menu::whereIn('c_menu_id', $menuIds)->orderBy('menu_order')->get()->keyBy('c_menu_id');

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
];
}
// dd($menuTree)->toArray();
}

@endphp

<style>
    .slide > a > span.side-menu__icon {
    margin-top: -5px;
}
.slide.active > a.side-menu__item.active {
    font-weight: bold;
}
</style>
<header class="app-header">
    @php
    $company_data = get_company_details(1);
    // dd($company_data);
    $userLogo = $company_data && $company_data->logo ? $company_data->logo : null;
    $Alt = $company_data && $company_data->name ? $company_data->name : null;
    $logoUrl = $userLogo
    ? $actual_url.'/uploads/company/'.$userLogo
    : $actual_url.'/admin_assets/images/brand-logos/enli-logo.png';
    $loginType = session('login_type');
    $dashboardUrl = $loginType == 'Company' || $loginType == 'Staff' ? route('dashboard') : route('main.dashboard');
    @endphp


    <div class="main-header-container container-fluid">
        <div class="header-content-left">
            <div class="header-element">
                <div class="horizontal-logo">
                    <a href="{{ $dashboard }}" class="header-logo">
                        <img src="{{ $logoUrl }}" style="width:100%;height:43px;" alt="{{ $Alt }}" class="desktop-logo">
                        <img src="{{ $logoUrl }}" style="width:38px;height:33px;" alt="{{ $Alt }}" class="toggle-logo">
                        <img src="{{ $logoUrl }}" style="width:100%;height:56px;" alt="{{ $Alt }}" class="desktop-dark">
                        <img src="{{ $logoUrl }}" style="width:38px;height:33px;" alt="{{ $Alt }}" class="toggle-dark">
                    </a>
                </div>
            </div>
            <div class="header-element">
                <a aria-label="anchor" href="javascript:void(0);" class="sidemenu-toggle header-link" data-bs-toggle="sidebar">
                    <span class="open-toggle me-2">
                        <i class="bx bx-menu header-link-icon"></i>
                    </span>
                </a>
                <div class="main-header-center  d-none d-lg-block  header-link">
                    <input type="text" class="form-control form-control-lg" id="typehead" placeholder="Search for results..." autocomplete="off">
                    <button type="button" aria-label="button" class="btn pe-1"><i class="fe fe-search" aria-hidden="true"></i></button>
                    <div id="headersearch" class="header-search">
                        <div class="p-3">
                            <div class="">
                                <p class="fw-semibold text-muted mb-2 fs-13">Recent Searches</p>
                                <div class="ps-2">
                                    <a href="javascript:void(0)" class="search-tags"><i class="fe fe-search me-2"></i>People<span></span></a>
                                    <a href="javascript:void(0)" class="search-tags"><i class="fe fe-search me-2"></i>Pages<span></span></a>
                                    <a href="javascript:void(0)" class="search-tags"><i class="fe fe-search me-2"></i>Articles<span></span></a>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="fw-semibold text-muted mb-2 fs-13">Apps and pages</p>
                                <ul class="ps-2">
                                    <li class="p-1 d-flex align-items-center text-muted mb-2 search-app">
                                        <a href="full-calendar.html"><span><i class="bx bx-calendar me-2 fs-14 bg-primary-transparent p-2 rounded-circle"></i>Calendar</span></a>
                                    </li>
                                    <li class="p-1 d-flex align-items-center text-muted mb-2 search-app">
                                        <a href="mail.html"><span><i class="bx bx-envelope me-2 fs-14 bg-primary-transparent p-2 rounded-circle"></i>Mail</span></a>
                                    </li>
                                    <li class="p-1 d-flex align-items-center text-muted mb-2 search-app">
                                        <a href="buttons.html"><span><i class="bx bx-dice-1 me-2 fs-14 bg-primary-transparent p-2 rounded-circle"></i>Buttons</span></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-3">
                                <p class="fw-semibold text-muted mb-2 fs-13">Links</p>
                                <ul class="ps-2">
                                    <li class="p-1 align-items-center text-muted mb-1 search-app">
                                        <a href="javascript:void(0)" class="text-primary"><u>http://spruko/spruko.com</u></a>
                                    </li>
                                    <li class="p-1 align-items-center text-muted mb-1 search-app">
                                        <a href="javascript:void(0)" class="text-primary"><u>http://spruko/spruko.com</u></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="py-3 border-top px-0">
                            <div class="text-center">
                                <a href="javascript:void(0)" class="text-primary text-decoration-underline fs-15">View
                                    all</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-element header-search d-lg-none d-block ">
                <a aria-label="anchor" href="javascript:void(0);" class="header-link" data-bs-toggle="modal" data-bs-target="#searchModal">
                    <i class="bx bx-search-alt-2 header-link-icon"></i>
                </a>
            </div>
        </div>
        <div class="header-content-right">
            <div class="header-element mainuserProfile">
                <a href="javascript:void(0);" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="d-sm-flex wd-100p">
                            <div class="avatar avatar-sm"><img alt="avatar" class="rounded-circle" src="{{ $actual_url.'/admin-assets/images/faces/1.jpg'}}"></div>
                            <div class="ms-2 my-auto d-none d-xl-flex">
                                <h6 class=" font-weight-semibold mb-0 fs-13 user-name d-sm-block d-none">Enlighten</h6>
                            </div>
                        </div>
                    </div>
                </a>
                {{-- Top User --}}
                <ul class="dropdown-menu  border-0 main-header-dropdown  overflow-hidden header-profile-dropdown" aria-labelledby="mainHeaderProfile">
                    <li><a class="dropdown-item" href="profile.html"><i class="fs-13 me-2 bx bx-user"></i>Profile</a>
                    </li>
                    <li><a class="dropdown-item" href="mail.html"><i class="fs-13 me-2 bx bx-comment"></i>Message</a>
                    </li>
                    <li><a class="dropdown-item" href="mail-settings.html"><i class="fs-13 me-2 bx bx-cog"></i>Settings</a></li>
                    <li><a class="dropdown-item" href="faq's.html"><i class="fs-13 me-2 bx bx-help-circle"></i>Help</a>
                    </li>
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fs-13 me-2 bx bx-arrow-to-right"></i>Log Out</a></li>
                </ul>
            </div>
            <div class="header-element ">
                <a aria-label="anchor" href="javascript:void(0);" class="header-link switcher-icon ms-1" data-bs-toggle="offcanvas" data-bs-target="#switcher-canvas">
                    <i class="bx bx-cog bx-spin header-link-icon"></i>
                </a>
            </div>
        </div>
    </div>
</header>
<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header">
        <a href="{{ $dashboard }}" class="header-logo">
            <img src="{{ $actual_url.'/admin-assets/images/brand-logos/desktop-logo.png'}}" alt="logo" class="desktop-logo">
            <img src="{{ $actual_url.'/admin-assets/images/brand-logos/toggle-logo.png'}}" alt="logo" class="toggle-logo">
            <img src="{{ $actual_url.'/admin-assets/images/brand-logos/desktop-dark.png'}}" alt="logo" class="desktop-dark">
            <img src="{{ $actual_url.'/admin-assets/images/brand-logos/toggle-dark.png'}}" alt="logo" class="toggle-dark">
        </a>
    </div>
    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>
            <ul class="main-menu">
                <li class="slide {{ request()->routeIs('*dashboard*') ? 'active' : '' }}">
                    <a href="{{ $dashboard }}" class="side-menu__item {{ request()->routeIs('*dashboard*') ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class='fs-13 fa fa-table-columns'></i>
                        </span>
                        <span class="side-menu__label">Dashboards</span>
                    </a>
                </li>

                @if (session('login_type') == "Super Admin")
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <span class="side-menu__icon">
                            <i class='fs-13 bx bx-desktop'></i>
                        </span>
                        <span class="side-menu__label">Settings</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="{{ route('company.role') }}">Company Role</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('company.role') }}" class="side-menu__item">Company Role</a>
                        </li>
                        <li class="slide side-menu__label1">
                            <a href="{{ route('company.register') }}">Company Register</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('company.register') }}" class="side-menu__item">Company Register</a>
                        </li>
                    </ul>
                </li>
                @endif

                @if (session()->get('login_type') == 'Company' || session()->get('login_type') == 'Staff')

                @foreach ($menuTree as $menu)
                @php
                $hasRoute = Route::has($menu['route']);
                $isActive = $hasRoute && (request()->is($menu['route'].'*') || request()->routeIs($menu['route']));

                if (! $isActive && isset($menu['submenus'])) {
                foreach ($menu['submenus'] as $submenu) {
                if (Route::has($submenu->sub_menu_route) && request()->routeIs($submenu->sub_menu_route)) {
                $isActive = true;
                break;
                }
                }
                }
                @endphp
                @if ($menu['has_submenu'])
                <li class="slide has-sub {{ $isActive ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item {{ $isActive ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class="fs-13 {{ $menu['icon'] }}"></i>
                        </span>
                        <span class="side-menu__label">{{ $menu['name'] }}</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        @foreach ($menu['submenus'] as $submenu)
                        @php
                        $subRouteExists = Route::has($submenu->sub_menu_route);
                        @endphp
                        <li class="slide">
                            <a href="{{ $subRouteExists ? route($submenu->sub_menu_route) : '#' }}" class="side-menu__item {{ $subRouteExists && request()->routeIs($submenu->sub_menu_route) ? 'active' : '' }}">
                                {{ $submenu->sub_menu_title }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @else
                <li class="slide">
                    <a href="{{ $hasRoute ? route($menu['route']) : '#' }}" class="side-menu__item {{ $isActive ? 'active' : '' }}">
                        <span class="side-menu__icon">
                            <i class="fs-13 {{ $menu['icon'] }}"></i>
                        </span>
                        <span class="side-menu__label">{{ $menu['name'] }}</span>
                    </a>
                </li>
                @endif
                @endforeach
                @endif

                @if (session()->get('login_type') == 'Company')
                <li class="slide has-sub {{ request()->is('menu/submenu') || request()->is('profile') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <span class="side-menu__icon">
                            <i class='fs-13 bx bx-cube'></i>
                        </span>
                        <span class="side-menu__label">Settings</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">Settings</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('menu/submenu') }}" class="side-menu__item {{ request()->is('menu/submenu') ? 'active' : '' }}">Role
                                Permission</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('profile') }}" class="side-menu__item {{ request()->is('profile') ? 'active' : '' }}">Profile</a>
                        </li>
                    </ul>
                </li>
                @endif

                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <span class=" side-menu__icon">
                            <i class='fs-13 fa fa-desktop'></i>
                        </span>
                        <span class="side-menu__label">Pages</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="{{ route('form') }}">Pages</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('form') }}" class="side-menu__item">Sample Page</a>
                        </li>
                    </ul>
                </li>
                <!-- 🔵 LOGOUT BUTTON MOVED TO LAST POSITION -->
                <li class="slide">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="side-menu__item">
                        <span class=" side-menu__icon">
                            <i class='fs-13 bx bx-log-out'></i>
                        </span>
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

