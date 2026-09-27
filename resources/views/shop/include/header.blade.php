@php
    $loginType   = session('shop_login_type');
    $currentRoute= request()->route()?->getName() ?? '';

    // Get unread notification count
    $notifUserId = session('shop_admin_id') ?? session('vendor_id') ?? session('delivery_staff_id');
    $notifType   = match($loginType) {
        'ShopSuperAdmin','ShopAdmin' => 'admin',
        'Vendor'   => 'vendor',
        'DeliveryStaff' => 'delivery',
        default    => 'admin',
    };
    $unreadNotifs = \Illuminate\Support\Facades\DB::table('tbl_shop_notifications')
        ->where('user_type', $notifType)
        ->where('user_id', $notifUserId)
        ->where('is_read', 0)
        ->count();

    // User display info
    $displayName = session('shop_admin_name') ?? session('vendor_biz') ?? session('delivery_staff_name') ?? 'User';
    $roleLabel   = match($loginType) {
        'ShopSuperAdmin' => 'Super Admin',
        'ShopAdmin'      => 'Shop Admin',
        'Vendor'         => 'Vendor',
        'DeliveryStaff'  => 'Delivery Staff',
        default          => 'User',
    };
    $avatarLetter = strtoupper(substr($displayName, 0, 1));
@endphp

{{-- Sidebar Overlay (Mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleShopSidebar()"></div>

{{-- ═══════════════ SIDEBAR ═══════════════ --}}
<nav id="shopSidebar">

    {{-- Logo --}}
    <div class="shop-sidebar-logo">
        <a href="{{ route('shop.dashboard') }}" class="text-decoration-none">
            <span class="logo-text">Hexa<span>Shop</span></span>
            <span class="logo-badge">ADMIN</span>
        </a>
    </div>

    {{-- Navigation --}}
    <div class="py-2">

        @if(in_array($loginType, ['ShopSuperAdmin', 'ShopAdmin']))
        {{-- ── OVERVIEW ─── --}}
        <a href="{{ route('shop.dashboard') }}"
           class="shop-nav-link {{ $currentRoute === 'shop.dashboard' ? 'active' : '' }}">
            <i class="bx bxs-dashboard"></i> Dashboard
        </a>

        {{-- ── CATALOG ─── --}}
        <div class="sidebar-section-label">Catalog</div>

        <button class="shop-nav-link" data-bs-toggle="collapse" data-bs-target="#menuProducts" aria-expanded="{{ str_starts_with($currentRoute, 'shop.product') ? 'true' : 'false' }}">
            <i class="bx bx-package"></i> Products
            <i class="bx bx-chevron-right nav-arrow"></i>
        </button>
        <div class="collapse shop-submenu {{ str_starts_with($currentRoute, 'shop.product') ? 'show' : '' }}" id="menuProducts">
            <a href="{{ route('shop.product.index') }}" class="shop-nav-link {{ $currentRoute === 'shop.product.index' ? 'active' : '' }}">
                <i class="bx bx-circle"></i> All Products
            </a>
            <a href="{{ route('shop.product.index') }}#add" class="shop-nav-link">
                <i class="bx bx-circle"></i> Add Product
            </a>
        </div>

        <a href="{{ route('shop.category.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.category') ? 'active' : '' }}">
            <i class="bx bx-category"></i> Categories
        </a>

        <a href="{{ route('shop.brand.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.brand') ? 'active' : '' }}">
            <i class="bx bx-diamond"></i> Brands
        </a>

        <a href="{{ route('shop.unit.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.unit') ? 'active' : '' }}">
            <i class="bx bx-ruler"></i> Units
        </a>

        <a href="{{ route('shop.variant.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.variant') ? 'active' : '' }}">
            <i class="bx bx-grid-alt"></i> Variants
        </a>

        {{-- ── INVENTORY ─── --}}
        <div class="sidebar-section-label">Inventory</div>

        <a href="{{ route('shop.inventory.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.inventory') ? 'active' : '' }}">
            <i class="bx bx-box"></i> Stock Management
        </a>

        <a href="{{ route('shop.rawmaterial.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.rawmaterial') ? 'active' : '' }}">
            <i class="bx bx-layer"></i> Raw Materials
        </a>

        <a href="{{ route('shop.supplier.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.supplier') ? 'active' : '' }}">
            <i class="bx bx-store-alt"></i> Suppliers
        </a>

        <a href="{{ route('shop.purchase.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.purchase') ? 'active' : '' }}">
            <i class="bx bx-cart-add"></i> Purchases
        </a>

        {{-- ── SALES ─── --}}
        <div class="sidebar-section-label">Sales</div>

        <a href="{{ route('shop.order.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.order') ? 'active' : '' }}">
            <i class="bx bx-receipt"></i> Orders
            @if(($pendingOrderCount ?? 0) > 0)
                <span class="nav-badge badge bg-danger ms-1">{{ $pendingOrderCount }}</span>
            @endif
        </a>

        <a href="{{ route('shop.return.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.return') ? 'active' : '' }}">
            <i class="bx bx-undo"></i> Returns & Refunds
        </a>

        <a href="{{ route('shop.coupon.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.coupon') ? 'active' : '' }}">
            <i class="bx bx-tag"></i> Coupons & Offers
        </a>

        {{-- ── CUSTOMERS ─── --}}
        <div class="sidebar-section-label">Customers</div>

        <a href="{{ route('shop.customer.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.customer') ? 'active' : '' }}">
            <i class="bx bx-group"></i> Customer List
        </a>

        <a href="{{ route('shop.review.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.review') ? 'active' : '' }}">
            <i class="bx bx-star"></i> Reviews & Ratings
        </a>

        {{-- ── VENDORS ─── --}}
        <div class="sidebar-section-label">Vendors</div>

        <a href="{{ route('shop.vendor.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.vendor') ? 'active' : '' }}">
            <i class="bx bx-buildings"></i> Vendor Management
        </a>

        <a href="{{ route('shop.vendor_earning.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.vendor_earning') ? 'active' : '' }}">
            <i class="bx bx-wallet"></i> Vendor Earnings
        </a>

        {{-- ── DELIVERY ─── --}}
        <div class="sidebar-section-label">Delivery</div>

        <a href="{{ route('shop.delivery.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.delivery') ? 'active' : '' }}">
            <i class="bx bx-cycling"></i> Delivery Staff
        </a>

        <a href="{{ route('shop.delivery_assign.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.delivery_assign') ? 'active' : '' }}">
            <i class="bx bx-map-pin"></i> Assignments
        </a>

        {{-- ── MARKETING ─── --}}
        <div class="sidebar-section-label">Marketing</div>

        <a href="{{ route('shop.banner.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.banner') ? 'active' : '' }}">
            <i class="bx bx-image"></i> Banners & Sliders
        </a>

        <a href="{{ route('shop.cms.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.cms') ? 'active' : '' }}">
            <i class="bx bx-file-blank"></i> CMS Pages
        </a>

        <a href="{{ route('shop.blog.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.blog') ? 'active' : '' }}">
            <i class="bx bx-news"></i> Blog
        </a>

        {{-- ── REPORTS ─── --}}
        <div class="sidebar-section-label">Reports</div>

        <button class="shop-nav-link" data-bs-toggle="collapse" data-bs-target="#menuReports" aria-expanded="{{ str_starts_with($currentRoute, 'shop.report') ? 'true' : 'false' }}">
            <i class="bx bx-bar-chart-alt-2"></i> Reports & Analytics
            <i class="bx bx-chevron-right nav-arrow"></i>
        </button>
        <div class="collapse shop-submenu {{ str_starts_with($currentRoute, 'shop.report') ? 'show' : '' }}" id="menuReports">
            <a href="{{ route('shop.report.sales') }}" class="shop-nav-link {{ $currentRoute === 'shop.report.sales' ? 'active' : '' }}">
                <i class="bx bx-circle"></i> Sales Report
            </a>
            <a href="{{ route('shop.report.inventory') }}" class="shop-nav-link {{ $currentRoute === 'shop.report.inventory' ? 'active' : '' }}">
                <i class="bx bx-circle"></i> Inventory Report
            </a>
            <a href="{{ route('shop.report.vendor') }}" class="shop-nav-link {{ $currentRoute === 'shop.report.vendor' ? 'active' : '' }}">
                <i class="bx bx-circle"></i> Vendor Report
            </a>
            <a href="{{ route('shop.report.customer') }}" class="shop-nav-link {{ $currentRoute === 'shop.report.customer' ? 'active' : '' }}">
                <i class="bx bx-circle"></i> Customer Report
            </a>
        </div>

        {{-- ── SETTINGS ─── --}}
        <div class="sidebar-section-label">Settings</div>

        <a href="{{ route('shop.setting.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.setting') ? 'active' : '' }}">
            <i class="bx bx-cog"></i> Shop Settings
        </a>

        <a href="{{ route('shop.activity_log.index') }}"
           class="shop-nav-link {{ str_starts_with($currentRoute, 'shop.activity_log') ? 'active' : '' }}">
            <i class="bx bx-history"></i> Activity Logs
        </a>

        @elseif($loginType === 'Vendor')
        {{-- ── VENDOR SIDEBAR ─── --}}
        <a href="{{ route('vendor.dashboard') }}" class="shop-nav-link {{ $currentRoute === 'vendor.dashboard' ? 'active' : '' }}">
            <i class="bx bxs-dashboard"></i> Dashboard
        </a>
        <div class="sidebar-section-label">My Store</div>
        <a href="{{ route('vendor.product.index') }}" class="shop-nav-link {{ str_starts_with($currentRoute, 'vendor.product') ? 'active' : '' }}">
            <i class="bx bx-package"></i> My Products
        </a>
        <a href="{{ route('vendor.inventory.index') }}" class="shop-nav-link {{ str_starts_with($currentRoute, 'vendor.inventory') ? 'active' : '' }}">
            <i class="bx bx-box"></i> Inventory
        </a>
        <a href="{{ route('vendor.order.index') }}" class="shop-nav-link {{ str_starts_with($currentRoute, 'vendor.order') ? 'active' : '' }}">
            <i class="bx bx-receipt"></i> Orders
        </a>
        <a href="{{ route('vendor.earning.index') }}" class="shop-nav-link {{ str_starts_with($currentRoute, 'vendor.earning') ? 'active' : '' }}">
            <i class="bx bx-wallet"></i> Earnings
        </a>
        <a href="{{ route('vendor.report.index') }}" class="shop-nav-link {{ str_starts_with($currentRoute, 'vendor.report') ? 'active' : '' }}">
            <i class="bx bx-bar-chart-alt-2"></i> Reports
        </a>

        @elseif($loginType === 'DeliveryStaff')
        {{-- ── DELIVERY STAFF SIDEBAR ─── --}}
        <a href="{{ route('delivery.dashboard') }}" class="shop-nav-link {{ $currentRoute === 'delivery.dashboard' ? 'active' : '' }}">
            <i class="bx bxs-dashboard"></i> Dashboard
        </a>
        <div class="sidebar-section-label">Deliveries</div>
        <a href="{{ route('delivery.order.index') }}" class="shop-nav-link {{ str_starts_with($currentRoute, 'delivery.order') ? 'active' : '' }}">
            <i class="bx bx-package"></i> Assigned Orders
        </a>
        <a href="{{ route('delivery.history.index') }}" class="shop-nav-link {{ str_starts_with($currentRoute, 'delivery.history') ? 'active' : '' }}">
            <i class="bx bx-history"></i> Delivery History
        </a>
        @endif

        <div style="height:80px;"></div>{{-- bottom padding --}}
    </div>
</nav>

{{-- ═══════════════ TOPBAR ═══════════════ --}}
<header id="shopTopbar">
    {{-- Hamburger --}}
    <button class="topbar-icon-btn" onclick="toggleShopSidebar()" title="Toggle Sidebar">
        <i class="bx bx-menu"></i>
    </button>

    {{-- Search --}}
    <div class="topbar-search d-none d-md-block">
        <div class="input-group">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" class="form-control" id="shopGlobalSearch" placeholder="Search products, orders, customers...">
        </div>
    </div>

    <div class="ms-auto d-flex align-items-center gap-2">

        {{-- Quick Add Button (Admin only) --}}
        @if(in_array($loginType, ['ShopSuperAdmin', 'ShopAdmin']))
        <div class="dropdown">
            <button class="topbar-icon-btn" data-bs-toggle="dropdown" title="Quick Actions">
                <i class="bx bx-plus-circle"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:180px; border-radius:10px;">
                <li><span class="dropdown-header fw-bold text-muted" style="font-size:0.72rem;">QUICK ACTIONS</span></li>
                <li><a class="dropdown-item" href="{{ route('shop.product.index') }}#add"><i class="bx bx-package me-2 text-primary"></i>Add Product</a></li>
                <li><a class="dropdown-item" href="{{ route('shop.order.index') }}"><i class="bx bx-receipt me-2 text-success"></i>View Orders</a></li>
                <li><a class="dropdown-item" href="{{ route('shop.customer.index') }}"><i class="bx bx-group me-2 text-info"></i>Customers</a></li>
                <li><a class="dropdown-item" href="{{ route('shop.coupon.index') }}#add"><i class="bx bx-tag me-2 text-warning"></i>Add Coupon</a></li>
            </ul>
        </div>
        @endif

        {{-- Notifications --}}
        <div class="dropdown">
            <button class="topbar-icon-btn" data-bs-toggle="dropdown" title="Notifications" id="shopNotifBtn">
                <i class="bx bx-bell"></i>
                @if($unreadNotifs > 0)
                    <span class="topbar-badge">{{ $unreadNotifs > 9 ? '9+' : $unreadNotifs }}</span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0" style="min-width:320px; border-radius:12px; overflow:hidden;" id="shopNotifDropdown">
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                    <span class="fw-bold" style="font-size:0.88rem;">Notifications</span>
                    <a href="#" class="text-muted" style="font-size:0.75rem;" id="markAllReadBtn">Mark all read</a>
                </div>
                <div id="shopNotifList" style="max-height:300px; overflow-y:auto;">
                    <div class="text-center text-muted py-4" style="font-size:0.82rem;">
                        <i class="bx bx-bell-off" style="font-size:2rem; display:block; margin-bottom:6px;"></i>
                        No new notifications
                    </div>
                </div>
                <div class="text-center border-top py-2">
                    <a href="#" class="text-primary" style="font-size:0.8rem;">View all notifications</a>
                </div>
            </div>
        </div>

        {{-- User Menu --}}
        <div class="dropdown">
            <button class="user-avatar-btn" data-bs-toggle="dropdown">
                <div class="user-avatar">{{ $avatarLetter }}</div>
                <div class="user-info-text d-none d-sm-block">
                    <div class="user-name">{{ Str::limit($displayName, 18) }}</div>
                    <div class="user-role">{{ $roleLabel }}</div>
                </div>
                <i class="bx bx-chevron-down text-muted d-none d-sm-block" style="font-size:0.9rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:200px; border-radius:10px; margin-top:8px;">
                <li class="px-3 py-2 border-bottom">
                    <div class="fw-semibold" style="font-size:0.85rem;">{{ $displayName }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">{{ $roleLabel }}</div>
                </li>
                <li><a class="dropdown-item py-2" href="#"><i class="bx bx-user me-2"></i>My Profile</a></li>
                <li><a class="dropdown-item py-2" href="#"><i class="bx bx-lock me-2"></i>Change Password</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <button class="dropdown-item py-2 text-danger" id="shopLogoutBtn">
                        <i class="bx bx-log-out me-2"></i>Logout
                    </button>
                </li>
            </ul>
        </div>
    </div>
</header>

<button id="shopScrollTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
    <i class="bx bx-up-arrow-alt"></i>
</button>
