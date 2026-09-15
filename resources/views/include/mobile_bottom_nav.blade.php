@php
    $dashboardUrl = '#';
    if (session('login_type') == 'Company' || session('login_type') == 'Staff' || session('login_type') == 'Manager') {
        $dashboardUrl = Route::has('dashboard') ? route('dashboard') : url('/dashboard');
    } else {
        $dashboardUrl = Route::has('main.dashboard') ? route('main.dashboard') : url('/dashboard');
    }

    $leadUrl = Route::has('lead.index') ? route('lead.index') : url('/lead');
    $dealUrl = Route::has('deal.index') ? route('deal.index') : url('/deal');
    $quotationUrl = Route::has('quotation.index') ? route('quotation.index') : url('/quotation');
    $invoiceUrl = Route::has('main_invoice.index') ? route('main_invoice.index') : url('/main-invoice');

    $isLeads = request()->is('lead*');
    $isDeals = request()->is('deal*');
    $isDashboard = request()->is('dashboard*') || request()->is('main/dashboard*');
    $isQuotations = request()->is('quotation*') || request()->is('direct_quotation*');
    $isInvoice = request()->is('main-invoice*') || request()->is('performa-invoice*') || request()->is('invoice*');
@endphp

<style>
    /* Premium Floating Mobile Bottom Dock Bar */
    @media (max-width: 991.98px) {
        body {
            padding-bottom: 80px !important;
        }
        footer.footer {
            margin-bottom: 70px !important;
        }
    }

    .mobile-bottom-nav-wrap {
        position: fixed;
        bottom: 12px;
        left: 12px;
        right: 12px;
        z-index: 1040;
        display: none;
        justify-content: center;
        pointer-events: none;
    }

    @media (max-width: 991.98px) {
        .mobile-bottom-nav-wrap {
            display: flex !important;
        }
    }

    .mobile-bottom-nav {
        pointer-events: auto;
        width: 100%;
        max-width: 480px;
        height: 64px;
        background: rgba(0, 50, 60, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(0, 180, 180, 0.3);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 35, 45, 0.45), 0 2px 10px rgba(0, 180, 180, 0.15);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 8px;
        position: relative;
    }

    .mobile-nav-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.65);
        text-decoration: none !important;
        padding: 6px 0;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        height: 100%;
    }

    .mobile-nav-item i {
        font-size: 1.15rem;
        margin-bottom: 3px;
        transition: transform 0.25s ease, color 0.25s ease;
    }

    .mobile-nav-item .mobile-nav-label {
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.2px;
        line-height: 1;
        transition: color 0.25s ease;
    }

    .mobile-nav-item:hover,
    .mobile-nav-item:focus {
        color: #ffffff;
    }

    /* Active Nav Item Styling */
    .mobile-nav-item.active {
        color: #00f2fe !important;
    }

    .mobile-nav-item.active i {
        transform: translateY(-2px) scale(1.12);
        color: #00f2fe !important;
        filter: drop-shadow(0 0 6px rgba(0, 242, 254, 0.6));
    }

    .mobile-nav-item.active .mobile-nav-label {
        color: #00f2fe !important;
        font-weight: 700;
    }

    /* Active Pill Indicator */
    .mobile-nav-item.active::after {
        content: '';
        position: absolute;
        bottom: 5px;
        width: 16px;
        height: 3px;
        background: #00f2fe;
        border-radius: 4px;
        box-shadow: 0 0 8px rgba(0, 242, 254, 0.8);
    }

    /* Floating Center Dashboard Button */
    .mobile-nav-item.center-dashboard-item {
        position: relative;
        justify-content: flex-end;
        padding-bottom: 6px;
    }

    .mobile-nav-item.center-dashboard-item .center-icon-wrap {
        position: absolute;
        top: -18px;
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #00b4b4 0%, #00454f 100%);
        border: 3px solid #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 18px rgba(0, 70, 80, 0.5), 0 0 12px rgba(0, 180, 180, 0.35);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .mobile-nav-item.center-dashboard-item.active .center-icon-wrap {
        background: linear-gradient(135deg, #00f2fe 0%, #007b8a 100%);
        border-color: #ffffff;
        box-shadow: 0 8px 22px rgba(0, 242, 254, 0.6), 0 0 16px rgba(0, 242, 254, 0.4);
        transform: scale(1.08);
    }

    .mobile-nav-item.center-dashboard-item .center-icon-wrap i {
        font-size: 1.25rem;
        margin-bottom: 0;
        color: #ffffff !important;
        filter: none !important;
    }

    .mobile-nav-item.center-dashboard-item.active::after {
        display: none;
    }
</style>

<div class="mobile-bottom-nav-wrap">
    <nav class="mobile-bottom-nav">
        <!-- 1. Leads -->
        <a href="{{ $leadUrl }}" class="mobile-nav-item {{ $isLeads ? 'active' : '' }}">
            <i class="fa-solid fa-users-line"></i>
            <span class="mobile-nav-label">Leads</span>
        </a>

        <!-- 2. Deals -->
        <a href="{{ $dealUrl }}" class="mobile-nav-item {{ $isDeals ? 'active' : '' }}">
            <i class="fa-solid fa-handshake"></i>
            <span class="mobile-nav-label">Deals</span>
        </a>

        <!-- 3. Dashboard (Center) -->
        <a href="{{ $dashboardUrl }}" class="mobile-nav-item center-dashboard-item {{ $isDashboard ? 'active' : '' }}">
            <div class="center-icon-wrap">
                <i class="fa-solid fa-house"></i>
            </div>
            <span class="mobile-nav-label">Dashboard</span>
        </a>

        <!-- 4. Quotations -->
        <a href="{{ $quotationUrl }}" class="mobile-nav-item {{ $isQuotations ? 'active' : '' }}">
            <i class="fa-solid fa-file-signature"></i>
            <span class="mobile-nav-label">Quotations</span>
        </a>

        <!-- 5. Invoice -->
        <a href="{{ $invoiceUrl }}" class="mobile-nav-item {{ $isInvoice ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span class="mobile-nav-label">Invoice</span>
        </a>
    </nav>
</div>
