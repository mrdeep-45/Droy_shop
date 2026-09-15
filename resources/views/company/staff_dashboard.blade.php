<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    @include('include.datatable_css_link')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Mobile dashboard compact card layout styles */
        @media (max-width: 640px) {
            .desktop-only-table,
            #tableCallsToday_wrapper,
            #tableCallsUpcoming_wrapper,
            #tableCallsOverdue_wrapper,
            #tableFollowUps_wrapper,
            #tableRecentLeads_wrapper,
            #tableRecentQuotations_wrapper {
                display: none !important;
            }
            .mobile-only-cards {
                display: block !important;
                width: 100% !important;
            }
            .mobile-dashboard-card {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
                margin-bottom: 12px;
                padding: 1rem;
                transition: transform 0.2s, box-shadow 0.2s;
            }
            .mobile-dashboard-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            }
        }
        @media (min-width: 641px) {
            .desktop-only-table {
                display: block !important;
            }
            .mobile-only-cards {
                display: none !important;
            }
        }

        body.dashboard-body .page {
            opacity: 0;
        }

        body.dashboard-body.ready-active .page {
            opacity: 1;
            transition: opacity 0.4s ease-in-out;
        }

        #loader.force-visible {
            display: flex !important;
        }

        .btn-view-all {
            background-color: #111c43 !important;
            color: #ffffff !important;
            border: 1px solid #111c43 !important;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            transition: all 0.2s ease-in-out;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(17, 28, 67, 0.15);
        }

        .btn-view-all:hover {
            background-color: #2563eb !important;
            color: #ffffff !important;
            border-color: #2563eb !important;
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.25);
        }

        .btn-view-all i {
            transition: transform 0.2s ease-in-out;
            color: #ffffff !important;
        }

        .btn-view-all:hover i {
            transform: translateX(2px);
        }

        .btn-action-eye {
            background-color: #111c43 !important;
            color: #ffffff !important;
            border: 1px solid #111c43 !important;
            border-radius: 6px;
            padding: 4px 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 2px 4px rgba(17, 28, 67, 0.15);
        }

        .btn-action-eye:hover {
            background-color: #2563eb !important;
            color: #ffffff !important;
            border-color: #2563eb !important;
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.25);
            transform: translateY(-1px);
        }

        .btn-action-eye i {
            color: #ffffff !important;
        }

        /* Skeleton Loader Styles */
        .skeleton-loader {
            width: 100%;
            height: 20px;
            background: #eee;
            background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);
            border-radius: 4px;
            background-size: 200% 100%;
            animation: 1.5s shine linear infinite;
        }

        @keyframes shine {
            to {
                background-position-x: -200%;
            }
        }

        /* DataTables Customization for Rowspan Alignment */
        table.dataTable tbody td {
            vertical-align: middle;
        }

        .quick-action-card {
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
        }

        .quick-action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        .quick-action-card:hover .icon-tile {
            transform: scale(1.05);
        }

        .quick-action-card:active {
            transform: scale(0.98);
        }

        .btn-quick-action {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-quick-action:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            filter: brightness(1.08);
        }

        .btn-quick-action:active {
            transform: translateY(0) scale(0.98);
            filter: brightness(0.95);
        }

        .quick-action-bar {
            border-left: 5px solid var(--accent-color) !important;
            border-radius: 1rem !important;
        }

        .quick-action-blob {
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            filter: blur(20px);
            opacity: 0.15;
            top: -20px;
            right: -20px;
            background-color: var(--accent-color);
            pointer-events: none;
            z-index: 0;
        }

        .icon-tile {
            width: 48px;
            height: 48px;
            background-color: var(--accent-color);
            transition: transform 0.2s ease-out;
            z-index: 1;
        }

        /* Premium Gradient Card Styles */
        .premium-gradient-card {
            border: none !important;
            border-radius: 1.25rem !important;
            color: #ffffff !important;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
            position: relative;
            overflow: hidden;
        }

        .premium-gradient-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.15), 0 12px 16px -8px rgba(0, 0, 0, 0.08) !important;
        }

        .premium-gradient-card .icon-container-circle {
            width: 48px;
            height: 48px;
            border-radius: 50% !important;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .premium-gradient-card:hover .icon-container-circle {
            transform: scale(1.1) rotate(8deg);
        }

        .premium-gradient-card .card-value {
            color: #ffffff !important;
            font-weight: 800;
            font-size: 1.6rem;
            line-height: 1.2;
        }

        .premium-gradient-card .card-label {
            color: rgba(255, 255, 255, 0.85) !important;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .premium-gradient-card .card-hint {
            color: rgba(255, 255, 255, 0.7) !important;
            font-size: 0.7rem;
        }

        .premium-gradient-card .card-link {
            color: rgba(255, 255, 255, 0.95) !important;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .premium-gradient-card .card-link:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

        .premium-gradient-card .premium-blur-blob {
            position: absolute;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            top: -40px;
            right: -40px;
            filter: blur(15px);
            pointer-events: none;
        }

        /* Styling for the new list items */
        #upcomingCampaignsList .list-group-item {
            transition: background-color 0.2s ease, transform 0.2s ease;
            border-radius: 8px !important;
            margin-bottom: 4px;
        }

        #upcomingCampaignsList .list-group-item:hover {
            background-color: #f8fafc !important;
            transform: translateX(4px);
        }

        .bg-primary-transparent {
            background-color: rgba(59, 130, 246, 0.1);
        }

        /* Premium Metric Card Additions */
        #campaignExpectedBudget,
        #campaignActualCost {
            font-size: 1.1rem;
            letter-spacing: -0.5px;
        }

        .bg-light-blue {
            background-color: #eff6ff;
        }

        .progress-bar {
            transition: width 1s ease-in-out;
        }
    </style>
</head>

<body class="dashboard-body">
    @include('include.switcher')
    @include('include.loader')
    <div class="page">
        @include('include.header')
        <div class="main-content app-content">
            <div class="container-fluid">


                <!-- Filter & Quick Actions - Two Separate Cards -->
                <div class="row g-3 mb-3 mt-3">

                    <!-- Left Card: Filter Data -->
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem !important;">
                            <div class="card-body py-3 px-4">
                                <!-- Title -->
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-filter text-success fs-14"></i>
                                    <span class="fw-bold text-dark fs-13 text-uppercase"
                                        style="letter-spacing: 0.06em;">Filter Data</span>
                                </div>
                                <!-- Fields -->
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <div class="input-group input-group-sm" style="width: 145px;">
                                        <input type="text" id="filterFromDate" class="form-control bg-white"
                                            placeholder="From Date" readonly
                                            style="border-radius: 6px 0 0 6px; font-size: 0.75rem;">
                                        <button class="btn btn-outline-light border text-muted px-2" type="button"
                                            id="btnFromDateCal" style="border-radius: 0 6px 6px 0;">
                                            <i class="far fa-calendar-alt fs-11"></i>
                                        </button>
                                    </div>
                                    <div class="input-group input-group-sm" style="width: 145px;">
                                        <input type="text" id="filterToDate" class="form-control bg-white"
                                            placeholder="To Date" readonly
                                            style="border-radius: 6px 0 0 6px; font-size: 0.75rem;">
                                        <button class="btn btn-outline-light border text-muted px-2" type="button"
                                            id="btnToDateCal" style="border-radius: 0 6px 6px 0;">
                                            <i class="far fa-calendar-alt fs-11"></i>
                                        </button>
                                    </div>
                                    <button
                                        class="btn btn-sm btn-primary d-flex align-items-center gap-1 px-3 fw-semibold"
                                        id="btnApplyDashboardFilter"
                                        style="border-radius: 6px; height: 30px; font-size: 0.75rem;">
                                        <i class="fas fa-search fs-10"></i> Apply
                                    </button>
                                    <button
                                        class="btn btn-sm text-white d-flex align-items-center gap-1 px-3 fw-semibold"
                                        id="btnResetDashboardFilter"
                                        style="border-radius: 6px; background-color: #475569; border-color: #475569; height: 30px; font-size: 0.75rem;">
                                        <i class="fas fa-redo-alt fs-10"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Card: Quick Actions -->
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem !important;">
                            <div class="card-body py-3 px-4">
                                <!-- Title -->
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-lightning-fill text-warning fs-14"></i>
                                    <span class="fw-bold text-dark fs-13 text-uppercase"
                                        style="letter-spacing: 0.06em;">Quick Actions</span>
                                </div>
                                <!-- Buttons -->
                                <style>
                                    @media (max-width: 575.98px) {
                                        .quick-actions-grid {
                                            display: grid !important;
                                            grid-template-columns: repeat(3, 1fr);
                                            gap: 8px !important;
                                        }

                                        .quick-actions-grid .btn-quick-action {
                                            width: 100%;
                                            justify-content: center;
                                            padding-left: 2px !important;
                                            padding-right: 2px !important;
                                            font-size: 11px !important;
                                        }

                                        .quick-actions-grid .btn-quick-action i {
                                            margin-right: 2px !important;
                                        }
                                    }
                                </style>
                                <div class="d-flex align-items-center flex-wrap gap-2 quick-actions-grid">
                                    @if(permissions_check('canView', 'lead.index'))
                                        <a href="{{ route('lead.index') }}?tab=new"
                                            class="btn btn-sm text-white d-flex align-items-center gap-1 px-2-5 py-1-5 fw-semibold btn-quick-action"
                                            style="border-radius: 6px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; font-size: 0.75rem; box-shadow: 0 2px 6px rgba(245,158,11,0.25);">
                                            <i class="bi bi-cart-fill fs-11"></i> Lead
                                        </a>
                                    @endif
                                    @if(permissions_check('canView', 'deal.index'))
                                        <a href="{{ route('deal.index') }}?tab=new"
                                            class="btn btn-sm text-white d-flex align-items-center gap-1 px-2-5 py-1-5 fw-semibold btn-quick-action"
                                            style="border-radius: 6px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none; font-size: 0.75rem; box-shadow: 0 2px 6px rgba(59,130,246,0.25);">
                                            <i class="bi bi-file-earmark-text-fill fs-11"></i> Deal
                                        </a>
                                    @endif
                                    @if(permissions_check('canView', 'quotation.index'))
                                        <a href="{{ route('quotation.index') }}"
                                            class="btn btn-sm text-white d-flex align-items-center gap-1 px-2-5 py-1-5 fw-semibold btn-quick-action"
                                            style="border-radius: 6px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none; font-size: 0.75rem; box-shadow: 0 2px 6px rgba(99,102,241,0.25);">
                                            <i class="bi bi-file-earmark-richtext-fill fs-11"></i> Quotation
                                        </a>
                                    @endif
                                    @if(false)
                                        <a href="#"
                                            class="btn btn-sm text-white d-flex align-items-center gap-1 px-2-5 py-1-5 fw-semibold btn-quick-action"
                                            style="border-radius: 6px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; font-size: 0.75rem; box-shadow: 0 2px 6px rgba(16,185,129,0.25);">
                                            <i class="bi bi-file-earmark-fill fs-11"></i> Performa
                                        </a>
                                    @endif
                                    @if(false)
                                        <a href="#"
                                            class="btn btn-sm text-white d-flex align-items-center gap-1 px-2-5 py-1-5 fw-semibold btn-quick-action"
                                            style="border-radius: 6px; background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); border: none; font-size: 0.75rem; box-shadow: 0 2px 6px rgba(244,63,94,0.25);">
                                            <i class="bi bi-credit-card-fill fs-11"></i> Customer
                                        </a>
                                    @endif
                                    @if(permissions_check('canView', 'products.index'))
                                        <a href="{{ route('products.index') }}?tab=new"
                                            class="btn btn-sm text-white d-flex align-items-center gap-1 px-2-5 py-1-5 fw-semibold btn-quick-action"
                                            style="border-radius: 6px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border: none; font-size: 0.75rem; box-shadow: 0 2px 6px rgba(139,92,246,0.25);">
                                            <i class="bi bi-cash-stack fs-11"></i> Product
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <!-- Section: Master Data -->
                <div class="row g-2 mt-1">
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-database text-primary fs-16"></i>
                            <h5 class="fw-bold text-dark mb-0 fs-15">Master Data</h5>
                        </div>
                    </div>



                    <!-- Leads Count Card -->
                    @if(permissions_check('canView', 'lead.index'))
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-6">
                            <a href="{{ route('lead.index') }}" class="text-decoration-none d-block">
                                <div class="card premium-gradient-card mb-0"
                                    style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                                    <div class="premium-blur-blob"></div>
                                    <div
                                        class="card-body p-3 d-flex align-items-center justify-content-between position-relative z-1">
                                        <div>
                                            <span class="card-label d-block">Total Leads</span>
                                            <h3 class="card-value mb-0 mt-1" id="countTotalLeads"><i
                                                    class="fas fa-spinner fa-spin text-white opacity-50 fs-16"></i></h3>
                                            <span class="card-link d-block mt-2 text-white">View Leads &rarr;</span>
                                        </div>
                                        <div class="icon-container-circle flex-shrink-0">
                                            <i class="bi bi-person-lines-fill text-white fs-5" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif

                    <!-- Deals Count Card -->
                    @if(permissions_check('canView', 'deal.index'))
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-6">
                            <a href="{{ route('deal.index') }}" class="text-decoration-none d-block">
                                <div class="card premium-gradient-card mb-0"
                                    style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                                    <div class="premium-blur-blob"></div>
                                    <div
                                        class="card-body p-3 d-flex align-items-center justify-content-between position-relative z-1">
                                        <div>
                                            <span class="card-label d-block">Total Deals</span>
                                            <h3 class="card-value mb-0 mt-1" id="countTotalDeals"><i
                                                    class="fas fa-spinner fa-spin text-white opacity-50 fs-16"></i></h3>

                                            <span class="card-link d-block mt-2 text-white">View Deals &rarr;</span>
                                        </div>
                                        <div class="icon-container-circle flex-shrink-0">
                                            <i class="bi bi-briefcase-fill text-white fs-5" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif

                    <!-- Quotations Count Card -->
                    @if(permissions_check('canView', 'quotation.index'))
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-6">
                            <a href="{{ route('quotation.index') }}" class="text-decoration-none d-block">
                                <div class="card premium-gradient-card mb-0"
                                    style="background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%);">
                                    <div class="premium-blur-blob"></div>
                                    <div
                                        class="card-body p-3 d-flex align-items-center justify-content-between position-relative z-1">
                                        <div>
                                            <span class="card-label d-block">Quotations</span>
                                            <h3 class="card-value mb-0 mt-1" id="countTotalQuotations"><i
                                                    class="fas fa-spinner fa-spin text-white opacity-50 fs-16"></i></h3>
                                            <span class="card-link d-block mt-2 text-white">View Quotes &rarr;</span>
                                        </div>
                                        <div class="icon-container-circle flex-shrink-0">
                                            <i class="bi bi-file-earmark-text-fill text-white fs-5" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif

                    <!-- Products Count Card -->
                    {{-- @if(permissions_check('canView', 'products.index'))
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-6">
                            <a href="{{ route('products.index') }}" class="text-decoration-none d-block">
                                <div class="card premium-gradient-card mb-0"
                                    style="background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%);">
                                    <div class="premium-blur-blob"></div>
                                    <div
                                        class="card-body p-3 d-flex align-items-center justify-content-between position-relative z-1">
                                        <div>
                                            <span class="card-label d-block">Products</span>
                                            <h3 class="card-value mb-0 mt-1" id="countTotalProducts"><i
                                                    class="fas fa-spinner fa-spin text-white opacity-50 fs-16"></i></h3>
                                            <span class="card-link d-block mt-2 text-white">View Products &rarr;</span>
                                        </div>
                                        <div class="icon-container-circle flex-shrink-0">
                                            <i class="bi bi-box-seam-fill text-white fs-5" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif

                    <!-- Customers Count Card -->
                    @if(false)
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-6">
                            <div class="card premium-gradient-card mb-0"
                                style="background: linear-gradient(135deg, #fcad25 0%, #ff9100 100%);">
                                <div class="premium-blur-blob"></div>
                                <div
                                    class="card-body p-3 d-flex align-items-center justify-content-between position-relative z-1">
                                    <div>
                                        <span class="card-label d-block">Customers</span>
                                        <h3 class="card-value mb-0 mt-1" id="countTotalCustomers">0</h3>
                                        <a href="#" class="card-link d-block mt-2">View Customers &rarr;</a>
                                    </div>
                                    <div class="icon-container-circle flex-shrink-0">
                                        <i class="bi bi-people-fill text-white fs-5" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif --}}

                    <!-- Staff Count Card -->
                    {{-- @if(permissions_check('canView', 'staff.index'))
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-6">
                            <a href="{{ route('staff.index') }}" class="text-decoration-none d-block">
                                <div class="card premium-gradient-card mb-0"
                                    style="background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%);">
                                    <div class="premium-blur-blob"></div>
                                    <div
                                        class="card-body p-3 d-flex align-items-center justify-content-between position-relative z-1">
                                        <div>
                                            <span class="card-label d-block">Total Staff</span>
                                            <h3 class="card-value mb-0 mt-1" id="countTotalStaff"><i
                                                    class="fas fa-spinner fa-spin text-white opacity-50 fs-16"></i></h3>

                                            <span class="card-link d-block mt-2 text-white">View Staff &rarr;</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif --}}
                </div>{{-- end Master Data row --}}

                <!-- Next Upcoming Campaigns (after Master Data) -->

                @if(permissions_check('canView', 'campaign.index'))
                <div class="row g-2 mt-2">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm mb-0" style="border-radius: 1.25rem !important;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-3 fs-14"><i class="fas fa-calendar-alt me-2 text-primary"></i>Next Upcoming Campaigns</h6>
                                <ul class="list-group list-group-flush" id="upcomingCampaignsList">
                                    <li class="list-group-item border-0 px-0 text-muted fs-12">Loading...</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif



                <!-- Section: Performance Analytics -->
                <div class="row g-2 mt-2">
                    <div class="col-12">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fas fa-chart-line text-success fs-16"></i>
                                <h5 class="fw-bold text-dark mb-0 fs-15">Performance Analytics</h5>
                            </div>
                    </div>

                    <!-- Line Chart: Lead & Deal Conversion Trends -->
                    <div class="col-xxl-8 col-xl-8 col-lg-7 col-12">
                            <div class="card border-0 shadow-sm position-relative overflow-hidden mb-0"
                                style="border-radius: 1.25rem !important;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-14">Lead & Deal Trends</h6>
                                            <p class="text-muted fs-11 mb-0">Daily volume of leads received and deals
                                                created</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-4">
                                            <div class="text-end">
                                                <h4 class="fw-extrabold text-primary mb-0 fs-20" id="perfLeadsCount"><i
                                                        class="fas fa-spinner fa-spin text-primary opacity-50 fs-14"></i>
                                                </h4>
                                                <span class="fs-10 text-muted text-uppercase fw-semibold"
                                                    style="letter-spacing: 0.5px;">Total Leads</span>
                                            </div>
                                            <div class="text-end">
                                                <h4 class="fw-extrabold text-success mb-0 fs-20" id="perfDealsCount"><i
                                                        class="fas fa-spinner fa-spin text-success opacity-50 fs-14"></i>
                                                </h4>
                                                <span class="fs-10 text-muted text-uppercase fw-semibold"
                                                    style="letter-spacing: 0.5px;">Total Deals</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="height: 250px; position: relative;">
                                        <canvas id="chartLeadTrends"></canvas>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <!-- Donut Chart: Deals Stage Distribution -->
                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-12">
                            <div class="card border-0 shadow-sm position-relative overflow-hidden mb-0"
                                style="border-radius: 1.25rem !important;">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <h6 class="fw-bold text-dark mb-1 fs-14">Pipeline Stage Distribution</h6>
                                        <p class="text-muted fs-11 mb-0">Deal stages in selected time period</p>
                                    </div>
                                    <div
                                        style="height: 250px; position: relative; display: flex; align-items: center; justify-content: center;">
                                        <canvas id="chartDealStages"></canvas>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>{{-- end Performance Analytics row --}}

                <div class="row mt-3">
                    <!-- Scheduled Calls Section -->
                    <div class="col-xxl-12">
                            <div class="card border-0 shadow-sm position-relative overflow-hidden quick-action-bar"
                                style="--accent-color: #3b82f6; border-radius: 1rem !important;">
                                <div class="quick-action-blob"></div>
                                <div class="card-header justify-content-between d-flex align-items-center flex-wrap gap-2 py-3"
                                    style="background-color: transparent;">
                                    <div class="card-title text-primary mb-0 fw-bold fs-15">
                                        <i class="fas fa-phone-alt me-2 text-primary"></i> Scheduled Calls
                                    </div>
                                    <ul class="nav nav-pills card-header-pills gap-1" id="callsTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button
                                                class="nav-link active px-3 py-1-5 fw-semibold fs-12 position-relative"
                                                id="calls-today-tab" data-bs-toggle="pill" data-bs-target="#calls-today"
                                                type="button" role="tab" aria-controls="calls-today"
                                                aria-selected="true" style="border-radius: 6px;">
                                                Today <span class="badge bg-danger ms-1" id="badgeCallsToday">0</span>
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link px-3 py-1-5 fw-semibold fs-12"
                                                id="calls-upcoming-tab" data-bs-toggle="pill"
                                                data-bs-target="#calls-upcoming" type="button" role="tab"
                                                aria-controls="calls-upcoming" aria-selected="false"
                                                style="border-radius: 6px;">
                                                Upcoming <span class="badge bg-primary ms-1"
                                                    id="badgeCallsUpcoming">0</span>
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link px-3 py-1-5 fw-semibold fs-12"
                                                id="calls-overdue-tab" data-bs-toggle="pill"
                                                data-bs-target="#calls-overdue" type="button" role="tab"
                                                aria-controls="calls-overdue" aria-selected="false"
                                                style="border-radius: 6px;">
                                                Overdue <span class="badge bg-warning text-dark ms-1"
                                                    id="badgeCallsOverdue">0</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="callsTabContent">
                                        <!-- Today Calls -->
                                        <div class="tab-pane fade show active" id="calls-today" role="tabpanel"
                                            aria-labelledby="calls-today-tab">
                                            <div class="table-responsive desktop-only-table">
                                                <table class="table table-hover table-striped align-middle"
                                                    id="tableCallsToday" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">#</th>
                                                            <th width="20%">CALL TIME</th>
                                                            <th width="25%">SUBJECT</th>
                                                            <th width="25%">AGENDA</th>
                                                            <th width="15%">REFERENCE MODULE</th>
                                                            <th width="10%" class="text-center">ACTIONS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="bodyCallsToday">
                                                        <!-- Loaded dynamically -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mobile-only-cards" id="mobileCallsToday"></div>
                                        </div>
                                        <!-- Upcoming Calls -->
                                        <div class="tab-pane fade" id="calls-upcoming" role="tabpanel"
                                            aria-labelledby="calls-upcoming-tab">
                                            <div class="table-responsive desktop-only-table">
                                                <table class="table table-hover table-striped align-middle"
                                                    id="tableCallsUpcoming" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">#</th>
                                                            <th width="20%">CALL DATE & TIME</th>
                                                            <th width="25%">SUBJECT</th>
                                                            <th width="25%">AGENDA</th>
                                                            <th width="15%">REFERENCE MODULE</th>
                                                            <th width="10%" class="text-center">ACTIONS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="bodyCallsUpcoming">
                                                        <!-- Loaded dynamically -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mobile-only-cards" id="mobileCallsUpcoming"></div>
                                        </div>
                                        <!-- Overdue Calls -->
                                        <div class="tab-pane fade" id="calls-overdue" role="tabpanel"
                                            aria-labelledby="calls-overdue-tab">
                                            <div class="table-responsive desktop-only-table">
                                                <table class="table table-hover table-striped align-middle"
                                                    id="tableCallsOverdue" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">#</th>
                                                            <th width="20%">SCHEDULED DATE & TIME</th>
                                                            <th width="25%">SUBJECT</th>
                                                            <th width="25%">AGENDA</th>
                                                            <th width="15%">REFERENCE MODULE</th>
                                                            <th width="10%" class="text-center">ACTIONS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="bodyCallsOverdue">
                                                        <!-- Loaded dynamically -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mobile-only-cards" id="mobileCallsOverdue"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>{{-- end Scheduled Calls row --}}

                <!-- Section: Follow-ups and Recent Leads Grid -->
                <div class="row mt-3">
                    <!-- Left Column: Upcoming Follow-ups -->
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-12 mb-4">
                            <div class="card border-0 shadow-sm position-relative overflow-hidden h-100 quick-action-bar"
                                style="--accent-color: #3b82f6; border-radius: 1rem !important;">
                                <div class="quick-action-blob"></div>
                                <div class="card-header justify-content-between d-flex align-items-center py-3"
                                    style="background-color: transparent;">
                                    <div class="card-title text-primary mb-0 fw-bold fs-15">
                                        <i class="fas fa-history me-2 text-primary"></i> Upcoming Follow-ups
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive desktop-only-table">
                                        <table class="table table-hover table-striped align-middle" id="tableFollowUps"
                                            width="100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="20%">DATE</th>
                                                    <th width="45%">LEAD / COMPANY</th>
                                                    <th width="15%">REMARKS</th>
                                                    <th width="15%" class="text-center">ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bodyFollowUps">
                                                <!-- Loaded dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mobile-only-cards" id="mobileFollowUps"></div>
                                </div>
                            </div>
                    </div>

                    <!-- Right Column: Recent Leads Assigned -->
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-12 mb-4" id="recentLeadsContainer">
                            <div class="card border-0 shadow-sm position-relative overflow-hidden h-100 quick-action-bar"
                                style="--accent-color: #3b82f6; border-radius: 1rem !important;">
                                <div class="quick-action-blob"></div>
                                <div class="card-header justify-content-between d-flex align-items-center py-3"
                                    style="background-color: transparent;">
                                    <div class="card-title text-primary mb-0 fw-bold fs-15">
                                        <i class="fas fa-user-clock me-2 text-primary"></i> Recent Leads Assigned
                                    </div>
                                    <a href="{{ route('lead.index') }}" class="btn-view-all">View All <i
                                            class="bi bi-arrow-right"></i></a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive desktop-only-table">
                                        <table class="table table-hover table-striped align-middle"
                                            id="tableRecentLeads" width="100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="55%">LEAD / COMPANY</th>
                                                    <th width="20%">DATE</th>
                                                    <th width="20%" class="text-center">ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bodyRecentLeads">
                                                <!-- Loaded dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mobile-only-cards" id="mobileRecentLeads"></div>
                                </div>
                            </div>
                    </div>
                </div>{{-- end Follow-ups + Recent Leads row --}}

                <!-- Section: Quotations and Activities Grid -->
                <div class="row">
                    <!-- Left Column: Recent Quotations -->
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-12 mb-4">
                            <div class="card border-0 shadow-sm position-relative overflow-hidden h-100 quick-action-bar"
                                style="--accent-color: #3b82f6; border-radius: 1rem !important;">
                                <div class="quick-action-blob"></div>
                                <div class="card-header justify-content-between d-flex align-items-center py-3"
                                    style="background-color: transparent;">
                                    <div class="card-title text-primary mb-0 fw-bold fs-15">
                                        <i class="fas fa-file-invoice-dollar me-2 text-primary"></i> Recent Quotations
                                    </div>
                                    <a href="{{ route('quotation.index') }}" class="btn-view-all">View All <i
                                            class="bi bi-arrow-right"></i></a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive desktop-only-table">
                                        <table class="table table-hover table-striped align-middle"
                                            id="tableRecentQuotations" width="100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="30%">QUOTATION NO</th>
                                                    <th width="40%">CUSTOMER NAME</th>
                                                    <th width="13%" class="text-end">AMOUNT</th>
                                                    <th width="12%" class="text-center">DATE</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bodyRecentQuotations">
                                                <!-- Loaded dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mobile-only-cards" id="mobileRecentQuotations"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Recent Activities -->
                        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-12 mb-4" id="recentActivitiesContainer">
                            <div class="card border-0 shadow-sm position-relative overflow-hidden h-100 quick-action-bar"
                                style="--accent-color: #3b82f6; border-radius: 1rem !important;">
                                <div class="quick-action-blob"></div>
                                <div class="card-header justify-content-between d-flex align-items-center py-3"
                                    style="background-color: transparent;">
                                    <div class="card-title text-primary mb-0 fw-bold fs-15">
                                        <i class="fas fa-list-ul me-2 text-primary"></i> Recent Activities
                                    </div>
                                </div>
                                <div class="card-body p-0" style="max-height: 380px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush" id="recentActivitiesList">
                                        <!-- Loaded dynamically -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Campaign Overview (after Recent Quotations & Recent Activities) -->
                    @if(permissions_check('canView', 'campaign.index'))
                        <div class="row g-2 mt-2">
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-bullhorn text-primary fs-16"></i>
                                        <h5 class="fw-bold text-dark mb-0 fs-15">Campaign Overview</h5>
                                    </div>
                                    <a href="{{ route('campaign.index') }}" class="btn-view-all">View All <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-6">
                                <div class="card premium-gradient-card mb-0" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                                    <div class="premium-blur-blob"></div>
                                    <div class="card-body p-3 d-flex align-items-center justify-content-between position-relative z-1">
                                        <div>
                                            <span class="card-label d-block">Total Campaigns</span>
                                            <h3 class="card-value mb-0 mt-1" id="countCampaignTotal"><i class="fas fa-spinner fa-spin text-white opacity-50 fs-16"></i></h3>
                                        </div>
                                        <div class="icon-container-circle flex-shrink-0">
                                            <i class="bi bi-megaphone-fill text-white fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-6">
                                <div class="card premium-gradient-card mb-0" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                    <div class="premium-blur-blob"></div>
                                    <div class="card-body p-3 d-flex align-items-center justify-content-between position-relative z-1">
                                        <div>
                                            <span class="card-label d-block">Active Campaigns</span>
                                            <h3 class="card-value mb-0 mt-1" id="countCampaignActive"><i class="fas fa-spinner fa-spin text-white opacity-50 fs-16"></i></h3>
                                        </div>
                                        <div class="icon-container-circle flex-shrink-0">
                                            <i class="bi bi-play-circle-fill text-white fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-6">
                                <div class="card premium-gradient-card mb-0" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                    <div class="premium-blur-blob"></div>
                                    <div class="card-body p-3 d-flex align-items-center justify-content-between position-relative z-1">
                                        <div>
                                            <span class="card-label d-block">Upcoming Campaigns</span>
                                            <h3 class="card-value mb-0 mt-1" id="countCampaignUpcoming"><i class="fas fa-spinner fa-spin text-white opacity-50 fs-16"></i></h3>
                                        </div>
                                        <div class="icon-container-circle flex-shrink-0">
                                            <i class="bi bi-hourglass-split text-white fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-6">
                                <div class="card premium-gradient-card mb-0" style="background: linear-gradient(135deg, #64748b 0%, #334155 100%);">
                                    <div class="premium-blur-blob"></div>
                                    <div class="card-body p-3 d-flex align-items-center justify-content-between position-relative z-1">
                                        <div>
                                            <span class="card-label d-block">Completed Campaigns</span>
                                            <h3 class="card-value mb-0 mt-1" id="countCampaignCompleted"><i class="fas fa-spinner fa-spin text-white opacity-50 fs-16"></i></h3>
                                        </div>
                                        <div class="icon-container-circle flex-shrink-0">
                                            <i class="bi bi-check-circle-fill text-white fs-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>


            <!-- Campaign Details View Modal (Dashboard) -->

            <div class="modal fade" id="dashboardCampaignViewModal" tabindex="-1"
                aria-labelledby="dashboardCampaignViewModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-dark" id="dashboardCampaignViewModalLabel">
                                <i class="fas fa-bullhorn text-primary me-2"></i>Campaign Details
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="dashboardCampaignViewBody" style="max-height: 70vh;">
                            <div class="text-center py-4"><span
                                    class="spinner-border spinner-border-sm text-primary"></span> Loading details...
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Follow-up Details View Modal -->
            <div class="modal fade" id="dashboardFollowUpViewModal" tabindex="-1"
                aria-labelledby="dashboardFollowUpViewModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-lg">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                        <div class="modal-header bg-light py-3">
                            <h5 class="modal-title fw-bold text-dark fs-15" id="dashboardFollowUpViewModalLabel">
                                <i class="fas fa-calendar-check text-primary me-2"></i>Follow-up Details
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4" id="dashboardFollowUpViewBody">
                            <!-- Populated Dynamically -->
                        </div>
                        <div class="modal-footer bg-light py-2">
                            <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            @include('include.footer')
        </div>
        @include('include.scroll-top')
        @include('include.footer_link')
        @include('include.datatable_js_link')

        <!-- Modal Complete Note Follow-up -->
        <div class="modal fade" id="modalCompleteNote" tabindex="-1" aria-labelledby="modalCompleteNoteLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title" id="modalCompleteNoteLabel">
                            <i class="fas fa-check-double text-primary me-2"></i>Follow-up Result
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="formCompleteNote">
                            @csrf
                            <input type="hidden" name="id" id="completeNoteId">
                            <input type="hidden" name="is_completed" id="completeNoteStatus">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Result/Outcome Notes </label>
                                <textarea class="form-control bg-light" name="result" id="completeNoteResult" rows="3"
                                    placeholder="Enter the result of this follow-up..."></textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="btnSubmitNoteResult">
                                    <i class="fas fa-check me-1"></i> Save Result
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call Result Modal -->
        <div class="modal fade" id="callResultModal" tabindex="-1" aria-labelledby="callResultModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="callResultForm" novalidate>
                        @csrf
                        <input type="hidden" name="id" id="result_call_id" value="">
                        <input type="hidden" name="call_status" id="result_call_status" value="">

                        <div class="modal-header">
                            <h5 class="modal-title" id="callResultModalLabel"><i
                                    class="fas fa-comment-dots text-primary me-2"></i>Call Result</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="callResultInput" class="form-label form-label-bold">Call Result <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="call_result" id="callResultInput" rows="5"
                                    placeholder="Type call result details here..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="btnSubmitCallResult"><i
                                    class="fas fa-check me-1"></i> Save Result</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                // Check and apply instant scroll restoration container size to avoid page flash
                const navigationType = window.performance && window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0]
                    ? window.performance.getEntriesByType('navigation')[0].type
                    : null;
                const isBackNavigation = navigationType === 'back_forward';
                const referrer = document.referrer;
                const isFromSubPage = referrer && (
                    referrer.includes('/lead/details') ||
                    referrer.includes('/deal/details') ||
                    referrer.includes('/quotation') ||
                    referrer.includes('/invoice')
                );
                const campaignOverviewUrl = "{{ route('dashboard.campaign-overview') }}";
                function loadCampaignOverview() {
                    $.get(campaignOverviewUrl, function (res) {
                        if (res.status === 'success') {
                            const d = res.data;
                            $('#countCampaignTotal').text(d.total_campaigns);
                            $('#countCampaignActive').text(d.active_campaigns);
                            $('#countCampaignUpcoming').text(d.upcoming_campaigns);
                            $('#countCampaignCompleted').text(d.completed_campaigns);

                            $('#campaignExpectedBudget').text('₹' + d.total_expected_budget);
                            $('#campaignActualCost').text('₹' + d.total_actual_cost);
                            $('#campaignBudgetUtilization').text(d.budget_utilization + '%');
                            $('#campaignTotalVisitors').text(d.total_visitors);

                            let barColor = '#10b981'; // Green
                            if (d.budget_utilization > 100) barColor = '#ef4444'; // Red
                            else if (d.budget_utilization > 80) barColor = '#f59e0b'; // Yellow

                            $('#campaignBudgetProgressBar')
                                .removeClass('bg-success bg-warning bg-danger')
                                .addClass(barColor)
                                .css('width', Math.min(d.budget_utilization, 100) + '%');

                            // Replace the existing upcomingHtml generation loop inside loadCampaignOverview:
                            let upcomingHtml = '';
                            if (d.upcoming_list && d.upcoming_list.length > 0) {
                                d.upcoming_list.forEach(c => {
                                    upcomingHtml += `
        <li class="list-group-item border-0 px-0 py-3 d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-transparent text-primary" style="width: 40px; height: 40px; flex-shrink: 0;">
                <i class="bi bi-calendar-event-fill fs-18"></i>
            </div>
            <div class="flex-grow-1">
                <span class="fw-bold text-dark fs-13 d-block view-dashboard-campaign" data-id="${c.campaign_id}" style="cursor:pointer;">${c.campaign_name}</span>
                <span class="fs-11 text-muted"><i class="bi bi-clock-fill me-1"></i>${c.start_date} to ${c.end_date}</span>
            </div>
            <button class="btn btn-sm btn-light border-0 text-primary view-dashboard-campaign" data-id="${c.campaign_id}" title="View Details">
                <i class="bi bi-chevron-right"></i>
            </button>
        </li>`;
                                });
                            } else {
                                upcomingHtml = '<li class="list-group-item border-0 px-0 text-muted fs-12 text-center py-4">No upcoming campaigns found</li>';
                            }
                            $('#upcomingCampaignsList').html(upcomingHtml);
                        }
                    }).fail(function () {
                        iziToast.error({ title: 'Error', message: 'Failed to load campaign overview', position: 'topRight' });
                    });
                }
                const savedScroll = sessionStorage.getItem('dashboard_scroll_pos');
                if ((isBackNavigation || isFromSubPage) && savedScroll && parseInt(savedScroll) > 0) {
                    $('#loader').addClass('force-visible');
                    $('html, body').css('min-height', '5000px');
                    $(window).scrollTop(parseInt(savedScroll));
                } else {
                    $('body').addClass('ready-active');
                }

                // Save scroll position on scroll
                var scrollTimer;
                $(window).on('scroll', function () {
                    clearTimeout(scrollTimer);
                    scrollTimer = setTimeout(function () {
                        sessionStorage.setItem('dashboard_scroll_pos', $(window).scrollTop());
                    }, 100);
                });

                var callsTable;
                function loadDashboardCalls() {
                    $.get("{{ route('dashboard.scheduled-calls') }}", function (res) {
                        if (res.success) {
                            // Destroy existing DataTables if initialized
                            if ($.fn.DataTable.isDataTable('#tableCallsToday')) {
                                $('#tableCallsToday').DataTable().destroy();
                            }
                            if ($.fn.DataTable.isDataTable('#tableCallsUpcoming')) {
                                $('#tableCallsUpcoming').DataTable().destroy();
                            }
                            if ($.fn.DataTable.isDataTable('#tableCallsOverdue')) {
                                $('#tableCallsOverdue').DataTable().destroy();
                            }

                            const todayObj = new Date();
                            const yyyy = todayObj.getFullYear();
                            const mm = String(todayObj.getMonth() + 1).padStart(2, '0');
                            const dd = String(todayObj.getDate()).padStart(2, '0');
                            const todayStr = `${yyyy}-${mm}-${dd}`;

                            let todayCalls = [];
                            let upcomingCalls = [];
                            let overdueCalls = [];

                            res.data.forEach(call => {
                                if (call.call_date === todayStr) {
                                    todayCalls.push(call);
                                } else if (call.call_date > todayStr) {
                                    upcomingCalls.push(call);
                                } else if (call.call_date < todayStr) {
                                    if (call.call_status == 0) {
                                        overdueCalls.push(call);
                                    } else {
                                        todayCalls.push(call);
                                    }
                                }
                            });

                            // Update Tab Badges
                            $('#badgeCallsToday').text(todayCalls.length);
                            $('#badgeCallsUpcoming').text(upcomingCalls.length);
                            $('#badgeCallsOverdue').text(overdueCalls.length);

                            // Helper function to build row HTML
                            function buildCallRows(callsList, showFullDate = false) {
                                if (callsList.length === 0) {
                                    return '<tr><td colspan="6" class="text-center text-muted p-3">No scheduled calls found</td></tr>';
                                }

                                let html = '';
                                callsList.forEach((call, index) => {
                                    let statusBadge = '';
                                    if (call.call_status == 0) {
                                        statusBadge = '<span class="badge bg-warning mt-1">Pending</span>';
                                    } else if (call.call_status == 1) {
                                        statusBadge = '<span class="badge bg-success mt-1">Completed</span>';
                                    } else if (call.call_status == 2) {
                                        statusBadge = '<span class="badge bg-danger mt-1">Cancelled</span>';
                                    }

                                    let actionButtons = '';
                                    if (call.call_status == 0) {
                                        actionButtons = `
                                          <button class="btn btn-sm btn-success btn-complete-call px-2 me-1" data-id="${call.id}" title="Complete Call">
                                              <i class="fas fa-check"></i>
                                          </button>
                                          <button class="btn btn-sm btn-danger btn-cancel-call px-2 me-1" data-id="${call.id}" title="Cancel Call">
                                              <i class="fas fa-times"></i>
                                          </button>
                                      `;
                                    }

                                    let agendaHtml = `<div>${call.call_agenda || '-'}</div>`;
                                    if (call.call_result) {
                                        agendaHtml += `<div class="text-muted mt-1" style="font-size: 0.85em; color: #888; font-style: italic;">Result: ${call.call_result}</div>`;
                                    }

                                    let displayTime = '';
                                    if (showFullDate) {
                                        displayTime = call.formatted_date_time;
                                    } else {
                                        if (call.call_time) {
                                            try {
                                                const timeParts = call.call_time.split(':');
                                                let hours = parseInt(timeParts[0]);
                                                const minutes = timeParts[1];
                                                const ampm = hours >= 12 ? 'PM' : 'AM';
                                                hours = hours % 12;
                                                hours = hours ? hours : 12;
                                                displayTime = `${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
                                            } catch (e) {
                                                displayTime = call.call_time;
                                            }
                                        } else {
                                            displayTime = '-';
                                        }
                                    }

                                    let refLink = call.reference_url && call.reference_url !== '#' && canAccessLeadDetails ?
                                        `<a href="${call.reference_url}" class="fw-bold text-dark text-decoration-none hover-primary">${call.reference_module}</a>` :
                                        `<span class="fw-bold text-dark">${call.reference_module}</span>`;
                                    let refHtml = refLink;
                                    let createdByName = call.created_by_name || 'System';
                                    let assignedStaffName = call.assigned_staff_name || 'Unassigned';
                                    refHtml += `<br><span class="badge bg-secondary-transparent text-secondary mt-1 me-1" style="font-size: 0.65rem;" title="Created By: ${createdByName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-plus fs-9 me-1"></i>${createdByName}</span>`;
                                    if (assignedStaffName !== 'Unassigned') {
                                        refHtml += `<span class="badge bg-primary-transparent text-primary mt-1" style="font-size: 0.65rem;" title="Assigned To: ${assignedStaffName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-check fs-9 me-1"></i>${assignedStaffName}</span>`;
                                    }

                                    html += `
                                      <tr>
                                          <td>${index + 1}</td>
                                          <td><strong>${displayTime}</strong></td>
                                          <td>
                                              <div><strong>${call.subject}</strong></div>
                                              <div class="mt-1">${statusBadge}</div>
                                          </td>
                                          <td>${agendaHtml}</td>
                                          <td>${refHtml}</td>
                                          <td class="text-center">
                                              <div class="d-flex align-items-center justify-content-center">
                                                  ${actionButtons}
                                              </div>
                                          </td>
                                      </tr>
                                  `;
                                });
                                return html;
                            }

                            // Helper function to build card HTML for mobile
                            function buildCallCards(callsList, statusType, showFullDate = false) {
                                if (callsList.length === 0) {
                                    return '<div class="text-center text-muted py-3 fs-12">No scheduled calls found</div>';
                                }

                                let html = '';
                                const limitCalls = callsList.slice(0, 5);
                                limitCalls.forEach((call) => {
                                    let statusBadge = '';
                                    if (call.call_status == 0) {
                                        statusBadge = '<span class="badge bg-warning text-dark ms-1">Pending</span>';
                                    } else if (call.call_status == 1) {
                                        statusBadge = '<span class="badge bg-success text-white ms-1">Completed</span>';
                                    } else if (call.call_status == 2) {
                                        statusBadge = '<span class="badge bg-danger text-white ms-1">Cancelled</span>';
                                    }

                                    let actionButtons = '';
                                    if (call.call_status == 0) {
                                        actionButtons = `
                                          <button class="btn btn-sm btn-success btn-complete-call px-2 me-1" data-id="${call.id}" title="Complete Call">
                                              <i class="fas fa-check"></i>
                                          </button>
                                          <button class="btn btn-sm btn-danger btn-cancel-call px-2 me-1" data-id="${call.id}" title="Cancel Call">
                                              <i class="fas fa-times"></i>
                                          </button>
                                        `;
                                    }

                                    let displayTime = '';
                                    if (showFullDate) {
                                        displayTime = call.formatted_date_time;
                                    } else {
                                        if (call.call_time) {
                                            try {
                                                const timeParts = call.call_time.split(':');
                                                let hours = parseInt(timeParts[0]);
                                                const minutes = timeParts[1];
                                                const ampm = hours >= 12 ? 'PM' : 'AM';
                                                hours = hours % 12;
                                                hours = hours ? hours : 12;
                                                displayTime = `${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
                                            } catch (e) {
                                                displayTime = call.call_time;
                                            }
                                        } else {
                                            displayTime = '-';
                                        }
                                    }

                                    let refLink = call.reference_url && call.reference_url !== '#' && canAccessLeadDetails ?
                                        `<a href="${call.reference_url}" class="fw-bold text-primary text-decoration-none hover-primary">${call.reference_module}</a>` :
                                        `<span class="fw-bold text-dark">${call.reference_module}</span>`;
                                        
                                    let statusTypeBadge = '';
                                    if (statusType === 'today') {
                                        statusTypeBadge = '<span class="badge bg-danger">Today</span>';
                                    } else if (statusType === 'upcoming') {
                                        statusTypeBadge = '<span class="badge bg-primary">Upcoming</span>';
                                    } else if (statusType === 'overdue') {
                                        statusTypeBadge = '<span class="badge bg-warning text-dark">Overdue</span>';
                                    }

                                    const eyeBtn = call.reference_url && call.reference_url !== '#' && canAccessLeadDetails
                                        ? `<a href="${call.reference_url}" class="btn btn-sm btn-light border text-primary px-2.5 py-1" title="View Details" style="border-radius: 6px; font-size: 11px;"><i class="fas fa-eye"></i> View</a>`
                                        : `<span class="btn btn-sm btn-light border text-muted px-2.5 py-1" style="cursor:not-allowed;opacity:0.4;font-size: 11px;" title="No Link"><i class="fas fa-eye"></i> View</span>`;

                                    html += `
                                      <div class="card mobile-dashboard-card p-3 mb-2 shadow-sm border" style="border-radius: 12px;">
                                          <div class="d-flex justify-content-between align-items-start mb-1-5">
                                              <div class="fw-bold text-dark fs-13">
                                                  📞 ${call.subject}
                                              </div>
                                              <div class="d-flex align-items-center">
                                                  ${statusTypeBadge}
                                                  ${statusBadge}
                                              </div>
                                          </div>
                                          <div class="text-muted fs-12 mb-1">
                                              🕒 ${displayTime}
                                          </div>
                                          <div class="text-muted fs-12 mb-1">
                                              📋 ${call.call_agenda || '-'}
                                          </div>
                                          <div class="text-muted fs-12 mb-2">
                                              📁 Ref: ${refLink}
                                          </div>
                                          <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                              <div class="d-flex align-items-center">
                                                  ${actionButtons}
                                              </div>
                                              <div>
                                                  ${eyeBtn}
                                              </div>
                                          </div>
                                      </div>
                                    `;
                                });
                                return html;
                            }

                            // Populate table bodies
                            $('#bodyCallsToday').html(buildCallRows(todayCalls, false));
                            $('#bodyCallsUpcoming').html(buildCallRows(upcomingCalls, true));
                            $('#bodyCallsOverdue').html(buildCallRows(overdueCalls, true));

                            // Populate mobile cards
                            $('#mobileCallsToday').html(buildCallCards(todayCalls, 'today', false));
                            $('#mobileCallsUpcoming').html(buildCallCards(upcomingCalls, 'upcoming', true));
                            $('#mobileCallsOverdue').html(buildCallCards(overdueCalls, 'overdue', true));

                            // Re-initialize Datatables for each table
                            const dataTableConfig = {
                                autoWidth: false,
                                responsive: true,
                                order: [],
                                columnDefs: [
                                    { orderable: false, targets: [2, 3, 4, 5] },
                                    { className: 'align-middle', targets: '_all' }
                                ]
                            };

                            if (todayCalls.length > 0) $('#tableCallsToday').DataTable(dataTableConfig);
                            if (upcomingCalls.length > 0) $('#tableCallsUpcoming').DataTable(dataTableConfig);
                            if (overdueCalls.length > 0) $('#tableCallsOverdue').DataTable(dataTableConfig);

                            // Re-initialize Bootstrap tooltips for call badge elements
                            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                                var existingTooltip = bootstrap.Tooltip.getInstance(el);
                                if (existingTooltip) existingTooltip.dispose();
                                new bootstrap.Tooltip(el, { trigger: 'hover', placement: el.dataset.bsPlacement || 'top' });
                            });
                        }
                    });
                }

                // Date Range Helper and Filter Initialization
                const todayDate = new Date();
                const firstDayOfMonth = new Date(todayDate.getFullYear(), todayDate.getMonth(), 1);

                const fpFrom = flatpickr("#filterFromDate", {
                    dateFormat: "d-m-Y",
                    disableMobile: true
                });

                const fpTo = flatpickr("#filterToDate", {
                    dateFormat: "d-m-Y",
                    disableMobile: true
                });

                // Calendar buttons triggers
                $('#btnFromDateCal').on('click', function () {
                    fpFrom.open();
                });

                $('#btnToDateCal').on('click', function () {
                    fpTo.open();
                });

                // Apply Filter Button
                $('#btnApplyDashboardFilter').on('click', function () {
                    loadDashboardData(true);
                });

                // Reset Button
                $('#btnResetDashboardFilter').on('click', function () {
                    fpFrom.clear();
                    fpTo.clear();
                    loadDashboardData(false);
                });

                const dashboardDataUrl = "{{ route('dashboard.data') }}";
                const leadDetailsRouteBase = "{{ url('/lead/details') }}/";
                const quotationPdfRouteBase = "{{ url('/quotation/pdf') }}/";
                let dashboardData = {};
                // Permission gate: only users with create/update/delete (not view-only) can open lead details
                const canAccessLeadDetails = {{ (checkPermissions('lead.index')['hasAnyPermission'] ?? false) ? 'true' : 'false' }};

                let leadTrendsChart = null;
                let dealStagesChart = null;

                function loadDashboardData(isButtonClick = false) {
                    const start_date = $('#filterFromDate').val();
                    const end_date = $('#filterToDate').val();

                    const $btn = $('#btnApplyDashboardFilter');
                    const originalHtml = '<i class="fas fa-search fs-10"></i> Apply Filter';

                    if (isButtonClick) {
                        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin fs-10 me-1"></i> Filtering...');
                        $('.card-body, .table-responsive').css({
                            'opacity': '0.5',
                            'transition': 'opacity 0.15s ease-in-out'
                        });
                    }

                    let formattedStart = '';
                    let formattedEnd = '';

                    if (start_date) {
                        formattedStart = start_date.replace(/-/g, '/');
                    }
                    if (end_date) {
                        formattedEnd = end_date.replace(/-/g, '/');
                    }

                    $.get(dashboardDataUrl, {
                        start_date: formattedStart,
                        end_date: formattedEnd
                    }, function (res) {
                        if (isButtonClick) {
                            $btn.prop('disabled', false).html(originalHtml);
                            $('.card-body, .table-responsive').css('opacity', '1');
                        }
                        if (res.success) {
                            dashboardData = res.data;
                            console.log("Loaded dashboard data:", res.data);

                            // Render Master Data Counts
                            if (res.data.counts) {
                                $('#countTotalLeads').text(res.data.counts.total_leads || 0);
                                $('#countTotalDeals').text(res.data.counts.total_deals || 0);
                                $('#countTotalQuotations').text(res.data.counts.total_quotations || 0);
                                $('#countTotalProducts').text(res.data.counts.total_products || 0);
                                $('#countTotalCustomers').text(res.data.counts.total_customers || 0);
                                $('#countTotalStaff').text(res.data.counts.total_staff || 0);
                            }

                            // Render Performance Analytics Summary Metrics
                            if (res.data.performance) {
                                $('#perfLeadsCount').text(res.data.performance.leads_count || 0);
                                $('#perfDealsCount').text(res.data.counts ? res.data.counts.total_deals : 0);

                                let salesAmount = parseFloat(res.data.performance.sales_amount) || 0;
                                $('#perfSalesAmount').text('₹' + salesAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                                let convRate = parseFloat(res.data.performance.conversion_rate) || 0;
                                $('#perfConversionRate').text(convRate.toFixed(2) + '%');
                            }

                            // Render Graphical Charts
                            if (res.data.charts) {
                                const chartData = res.data.charts;

                                // 1. Render Line Chart for Lead & Deal Trends
                                const labels = chartData.trends.map(t => t.label);
                                const leadsData = chartData.trends.map(t => t.leads);
                                const dealsData = chartData.trends.map(t => t.deals);

                                if (leadTrendsChart) {
                                    leadTrendsChart.destroy();
                                }

                                const ctxTrends = document.getElementById('chartLeadTrends').getContext('2d');
                                leadTrendsChart = new Chart(ctxTrends, {
                                    type: 'line',
                                    data: {
                                        labels: labels,
                                        datasets: [
                                            {
                                                label: 'Leads Received',
                                                data: leadsData,
                                                borderColor: '#3b82f6',
                                                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                                                borderWidth: 2.5,
                                                pointBackgroundColor: '#3b82f6',
                                                pointHoverRadius: 5,
                                                tension: 0.3,
                                                fill: true
                                            },
                                            {
                                                label: 'Deals Created',
                                                data: dealsData,
                                                borderColor: '#10b981',
                                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                                borderWidth: 2.5,
                                                pointBackgroundColor: '#10b981',
                                                pointHoverRadius: 5,
                                                tension: 0.3,
                                                fill: true
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: false
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                grid: {
                                                    color: '#f1f5f9'
                                                },
                                                ticks: {
                                                    font: { size: 11 },
                                                    color: '#64748b'
                                                }
                                            },
                                            x: {
                                                grid: {
                                                    display: false
                                                },
                                                ticks: {
                                                    font: { size: 11 },
                                                    color: '#64748b'
                                                }
                                            }
                                        }
                                    }
                                });

                                // 2. Render Donut Chart for Deal Stage Distribution
                                const stageLabels = chartData.stage_distribution.map(s => s.stage_name);
                                const stageCounts = chartData.stage_distribution.map(s => s.count);

                                if (dealStagesChart) {
                                    dealStagesChart.destroy();
                                }

                                const totalDeals = stageCounts.reduce((a, b) => a + b, 0);
                                let donutData = stageCounts;
                                let donutColors = ['#6366f1', '#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#a855f7', '#14b8a6'];
                                if (totalDeals === 0) {
                                    donutData = [1];
                                    donutColors = ['#e2e8f0'];
                                }

                                const ctxStages = document.getElementById('chartDealStages').getContext('2d');
                                dealStagesChart = new Chart(ctxStages, {
                                    type: 'doughnut',
                                    data: {
                                        labels: totalDeals === 0 ? ['No Deals'] : stageLabels.map((l, idx) => `${l}: ${stageCounts[idx]}`),
                                        datasets: [{
                                            data: donutData,
                                            backgroundColor: donutColors,
                                            borderWidth: totalDeals === 0 ? 0 : 2,
                                            borderColor: '#ffffff',
                                            hoverOffset: totalDeals === 0 ? 0 : 15,
                                            hoverBorderWidth: 0
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        cutout: '72%',
                                        plugins: {
                                            legend: {
                                                position: 'bottom',
                                                labels: {
                                                    boxWidth: 10,
                                                    font: { size: 11, weight: '600' },
                                                    color: '#475569',
                                                    padding: 8
                                                }
                                            },
                                            tooltip: {
                                                enabled: true
                                            }
                                        }
                                    },
                                    plugins: [{
                                        id: 'doughnut3dAndCenterText',
                                        beforeDatasetsDraw: function (chart) {
                                            const ctx = chart.ctx;
                                            ctx.save();
                                            ctx.shadowColor = 'rgba(0, 0, 0, 0.16)';
                                            ctx.shadowBlur = 12;
                                            ctx.shadowOffsetX = 3;
                                            ctx.shadowOffsetY = 6;
                                        },
                                        afterDatasetsDraw: function (chart) {
                                            chart.ctx.restore();
                                        },
                                        beforeDraw: function (chart) {
                                            const chartArea = chart.chartArea;
                                            if (!chartArea) return;

                                            const ctx = chart.ctx;
                                            const textX = (chartArea.left + chartArea.right) / 2;
                                            const textY = (chartArea.top + chartArea.bottom) / 2;

                                            ctx.save();

                                            const activeElements = chart.getActiveElements();
                                            let valueText = "";
                                            let labelText = "TOTAL DEALS";

                                            if (activeElements.length > 0 && totalDeals > 0) {
                                                const activeElement = activeElements[0];
                                                const datasetIndex = activeElement.datasetIndex;
                                                const index = activeElement.index;
                                                const label = chart.data.labels[index];
                                                const val = chart.data.datasets[datasetIndex].data[index];
                                                valueText = val;
                                                labelText = label.split(':')[0].toUpperCase();
                                            } else {
                                                valueText = totalDeals;
                                                labelText = "TOTAL DEALS";
                                            }

                                            ctx.font = "bold 24px Inter, system-ui, -apple-system, sans-serif";
                                            ctx.fillStyle = "#1e293b";
                                            ctx.textAlign = "center";
                                            ctx.textBaseline = "middle";
                                            ctx.fillText(valueText, textX, textY - 8);

                                            ctx.font = "700 9px Inter, system-ui, -apple-system, sans-serif";
                                            ctx.fillStyle = "#64748b";
                                            ctx.fillText(labelText, textX, textY + 14);

                                            ctx.restore();
                                        }
                                    }]
                                });
                            }

                            // Render Upcoming Follow-ups
                            if ($.fn.DataTable.isDataTable('#tableFollowUps')) {
                                $('#tableFollowUps').DataTable().destroy();
                            }

                            window.dashboardFollowUps = res.data.follow_ups || [];

                            let followUpsHtml = '';
                            if (res.data.follow_ups && res.data.follow_ups.length > 0) {
                                res.data.follow_ups.forEach((fu, index) => {
                                    let leadName = 'N/A';
                                    let leadUrl = '#';
                                    if (fu.lead) {
                                        let refBadge = '';
                                        if (fu.reference_type === 'deal') {
                                            refBadge = ' <span class="badge bg-warning-transparent text-warning ms-1" style="font-size: 0.65rem; padding: 2px 4px;">Deal</span>';
                                        } else {
                                            refBadge = ' <span class="badge bg-info-transparent text-info ms-1" style="font-size: 0.65rem; padding: 2px 4px;">Lead</span>';
                                        }
                                        const displayName = fu.lead.company_name || fu.lead.contact_person_name || 'Unnamed Lead';
                                        if (fu.lead.encrypted_id && canAccessLeadDetails) {
                                            leadUrl = leadDetailsRouteBase + fu.lead.encrypted_id;
                                        }
                                        leadName = canAccessLeadDetails && leadUrl !== '#'
                                            ? `<a href="${leadUrl}" class="fw-bold text-dark text-decoration-none hover-primary">${displayName}</a>${refBadge}`
                                            : `<span class="fw-bold text-dark">${displayName}</span>${refBadge}`;
                                        if (fu.lead.enquiry_no) {
                                            leadName += `<br><span class="badge bg-light text-dark mt-1" style="font-size: 0.75rem;">Enq: ${fu.lead.enquiry_no}</span>`;
                                        }
                                    }

                                    let createdByName = fu.created_by_name || 'N/A';
                                    let assignedStaffName = fu.assigned_staff ? fu.assigned_staff.name : 'Unassigned';
                                    leadName += `<br><span class="badge bg-secondary-transparent text-secondary mt-1 me-1" style="font-size: 0.65rem;" title="Created By: ${createdByName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-plus fs-9 me-1"></i>${createdByName}</span>`;
                                    if (assignedStaffName !== 'Unassigned') {
                                        leadName += `<span class="badge bg-primary-transparent text-primary mt-1" style="font-size: 0.65rem;" title="Assigned To: ${assignedStaffName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-check fs-9 me-1"></i>${assignedStaffName}</span>`;
                                    }

                                    let formattedFuDate = fu.follow_up_date;
                                    if (fu.follow_up_date) {
                                        try {
                                            const d = new Date(fu.follow_up_date);
                                            const day = String(d.getDate()).padStart(2, '0');
                                            const month = String(d.getMonth() + 1).padStart(2, '0');
                                            const year = d.getFullYear();
                                            formattedFuDate = `${day}-${month}-${year}`;
                                        } catch (e) { }
                                    }

                                    let remarksText = fu.remarks || '-';
                                    let truncatedRemarks = remarksText;
                                    let escapedRemarks = remarksText.replace(/"/g, '&quot;');
                                    if (fu.remarks && fu.remarks.length > 60) {
                                        truncatedRemarks = fu.remarks.substring(0, 60) + '...';
                                    }

                                    followUpsHtml += `
                                     <tr>
                                         <td>${index + 1}</td>
                                         <td><strong class="text-primary">${formattedFuDate}</strong></td>
                                         <td>${leadName}</td>
                                         <td>
                                             <span class="text-dark cursor-pointer show-followup-details fw-semibold text-decoration-underline" 
                                                   style="font-size: 0.85rem; cursor: pointer;" 
                                                   data-index="${index}" 
                                                   title="Click to view details: ${escapedRemarks}">
                                                 ${truncatedRemarks}
                                             </span>
                                         </td>
                                         <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <button class="btn btn-sm btn-success complete-note-btn px-2 me-1" data-id="${fu.id}" data-status="1" title="Mark as Completed">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger complete-note-btn px-2 me-1" data-id="${fu.id}" data-status="2" title="Mark as Cancelled">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                         </td>
                                     </tr>
                                 `;
                                });
                            } else {
                                followUpsHtml = '<tr><td colspan="5" class="text-center text-muted p-3">No upcoming follow-ups found</td></tr>';
                            }

                            $('#bodyFollowUps').html(followUpsHtml);

                             let followUpsMobileHtml = '';
                             if (res.data.follow_ups && res.data.follow_ups.length > 0) {
                                 const limitFollowUps = res.data.follow_ups.slice(0, 5);
                                 limitFollowUps.forEach((fu) => {
                                     let originalIndex = res.data.follow_ups.indexOf(fu);
                                     let leadName = 'N/A';
                                     let leadUrl = '#';
                                     let refBadge = '';
                                     let displayName = 'Unnamed Lead';
                                     
                                     if (fu.lead) {
                                         if (fu.reference_type === 'deal') {
                                             refBadge = '<span class="badge bg-warning-transparent text-warning" style="font-size: 0.65rem; padding: 2px 4px;">Deal</span>';
                                         } else {
                                             refBadge = '<span class="badge bg-info-transparent text-info" style="font-size: 0.65rem; padding: 2px 4px;">Lead</span>';
                                         }
                                         displayName = fu.lead.company_name || fu.lead.contact_person_name || 'Unnamed Lead';
                                         if (fu.lead.encrypted_id && canAccessLeadDetails) {
                                             leadUrl = leadDetailsRouteBase + fu.lead.encrypted_id;
                                         }
                                         leadName = canAccessLeadDetails && leadUrl !== '#'
                                             ? `<a href="${leadUrl}" class="fw-bold text-dark text-decoration-none hover-primary">${displayName}</a>`
                                             : `<span class="fw-bold text-dark">${displayName}</span>`;
                                     }

                                     let createdByName = fu.created_by_name || 'N/A';
                                     let assignedStaffName = fu.assigned_staff ? fu.assigned_staff.name : 'Unassigned';

                                     let formattedFuDate = fu.follow_up_date;
                                     if (fu.follow_up_date) {
                                         try {
                                             const d = new Date(fu.follow_up_date);
                                             const day = String(d.getDate()).padStart(2, '0');
                                             const month = String(d.getMonth() + 1).padStart(2, '0');
                                             const year = d.getFullYear();
                                             formattedFuDate = `${day}-${month}-${year}`;
                                         } catch (e) { }
                                     }

                                     let actionButtons = `
                                         <button class="btn btn-sm btn-success complete-note-btn px-2 me-1" data-id="${fu.id}" data-status="1" title="Mark as Completed">
                                             <i class="fas fa-check"></i>
                                         </button>
                                         <button class="btn btn-sm btn-danger complete-note-btn px-2" data-id="${fu.id}" data-status="2" title="Mark as Cancelled">
                                             <i class="fas fa-times"></i>
                                         </button>
                                     `;

                                     const eyeBtn = canAccessLeadDetails && leadUrl !== '#'
                                         ? `<a href="${leadUrl}" class="btn btn-sm btn-light border text-primary px-2.5 py-1" title="View Details" style="border-radius: 6px; font-size: 11px;"><i class="fas fa-eye"></i> View</a>`
                                         : `<span class="btn btn-sm btn-light border text-muted px-2.5 py-1" style="cursor:not-allowed;opacity:0.4;font-size: 11px;" title="No Link"><i class="fas fa-eye"></i> View</span>`;

                                     let enqNoHtml = fu.lead && fu.lead.enquiry_no ? `<span class="text-muted" style="font-size: 11px;">Enq: ${fu.lead.enquiry_no}</span>` : '';

                                     followUpsMobileHtml += `
                                       <div class="card mobile-dashboard-card p-3 mb-2 shadow-sm border" style="border-radius: 12px;">
                                           <div class="d-flex justify-content-between align-items-start mb-1-5">
                                               <div>
                                                   <div class="fw-bold text-dark fs-14">${leadName}</div>
                                                   ${enqNoHtml}
                                               </div>
                                               ${refBadge}
                                           </div>
                                           <div class="text-muted fs-12 mb-2">
                                               📅 ${formattedFuDate}
                                           </div>
                                           <div class="text-dark fs-12 mb-2 p-2 bg-light rounded remarks-text show-followup-details cursor-pointer" 
                                                style="font-size: 0.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; max-height: 40px; line-height: 1.3; cursor: pointer;" 
                                                data-index="${originalIndex}" 
                                                title="Click to view details">
                                               ${fu.remarks || '-'}
                                           </div>
                                          <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                              <div class="fs-10 text-muted">
                                                  <span class="badge bg-secondary-transparent text-secondary me-1" title="Created By: ${createdByName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-plus fs-8 me-1"></i>${createdByName}</span>
                                                  ${assignedStaffName !== 'Unassigned' ? `<span class="badge bg-primary-transparent text-primary" title="Assigned To: ${assignedStaffName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-check fs-8 me-1"></i>${assignedStaffName}</span>` : ''}
                                              </div>
                                              <div class="d-flex align-items-center gap-1">
                                                  ${actionButtons}
                                                  ${eyeBtn}
                                              </div>
                                          </div>
                                      </div>
                                    `;
                                });
                            } else {
                                followUpsMobileHtml = '<div class="text-center text-muted py-3 fs-12">No upcoming follow-ups found</div>';
                            }
                            $('#mobileFollowUps').html(followUpsMobileHtml);

                            if (res.data.follow_ups && res.data.follow_ups.length > 0) {
                                $('#tableFollowUps').DataTable({
                                    autoWidth: false,
                                    responsive: true,
                                    order: [],
                                    columnDefs: [
                                        { orderable: false, targets: [2, 3, 4] },
                                        { className: 'align-middle', targets: '_all' }
                                    ]
                                });
                            }

                            // Render Recent Leads Assigned
                            if ($.fn.DataTable.isDataTable('#tableRecentLeads')) {
                                $('#tableRecentLeads').DataTable().destroy();
                            }

                            let recentLeadsHtml = '';
                            if (res.data.recent_leads && res.data.recent_leads.length > 0) {
                                res.data.recent_leads.forEach((lead, index) => {
                                    let detailsUrl = lead.encrypted_id && canAccessLeadDetails ? leadDetailsRouteBase + lead.encrypted_id : '#';
                                    const displayName = lead.company_name || lead.contact_person_name || 'Unnamed Lead';
                                    let leadName = canAccessLeadDetails && detailsUrl !== '#'
                                        ? `<a href="${detailsUrl}" class="fw-bold text-dark text-decoration-none hover-primary">${displayName}</a>`
                                        : `<span class="fw-bold text-dark">${displayName}</span>`;

                                    // Lead / Deal badge
                                    const typeBadge = lead.status == 1
                                        ? `<span class="badge ms-1" style="font-size:0.65rem;padding:2px 6px;background:rgba(245,158,11,0.15);color:#d97706;border:1px solid #fbbf24;"><i class="fas fa-handshake me-1"></i>Deal</span>`
                                        : `<span class="badge ms-1" style="font-size:0.65rem;padding:2px 6px;background:rgba(59,130,246,0.12);color:#2563eb;border:1px solid #93c5fd;"><i class="fas fa-seedling me-1"></i>Lead</span>`;
                                    leadName += typeBadge;

                                    if (lead.enquiry_no) {
                                        leadName += `<br><span class="badge bg-light text-dark mt-1" style="font-size: 0.75rem;">Enq: ${lead.enquiry_no}</span>`;
                                    }
                                    let createdByName = lead.created_by_name || 'N/A';
                                    let staffName = lead.assigned_staff ? lead.assigned_staff.name : 'Unassigned';
                                    leadName += `<br><span class="badge bg-secondary-transparent text-secondary mt-1 me-1" style="font-size: 0.65rem;" title="Created By: ${createdByName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-plus fs-9 me-1"></i>${createdByName}</span>`;
                                    if (staffName !== 'Unassigned') {
                                        leadName += `<span class="badge bg-primary-transparent text-primary mt-1" style="font-size: 0.65rem;" title="Assigned To: ${staffName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-check fs-9 me-1"></i>${staffName}</span>`;
                                    }

                                    const eyeBtn = canAccessLeadDetails && detailsUrl !== '#'
                                        ? `<a href="${detailsUrl}" class="btn-action-eye" title="View Lead Details"><i class="fas fa-eye fs-12"></i></a>`
                                        : `<span class="btn-action-eye text-muted" style="cursor:not-allowed;opacity:0.4;" title="No permission"><i class="fas fa-eye fs-12"></i></span>`;

                                    recentLeadsHtml += `
                                      <tr>
                                          <td>${index + 1}</td>
                                          <td>${leadName}</td>
                                          <td><strong class="text-primary">${lead.formatted_created_at || '-'}</strong></td>
                                          <td class="text-center">${eyeBtn}</td>
                                      </tr>
                                  `;
                                });
                            } else {
                                recentLeadsHtml = '<tr><td colspan="4" class="text-center text-muted p-3">No recent leads found</td></tr>';
                            }

                            $('#bodyRecentLeads').html(recentLeadsHtml);

                            let recentLeadsMobileHtml = '';
                            if (res.data.recent_leads && res.data.recent_leads.length > 0) {
                                const limitRecentLeads = res.data.recent_leads.slice(0, 5);
                                limitRecentLeads.forEach((lead) => {
                                    let detailsUrl = lead.encrypted_id && canAccessLeadDetails ? leadDetailsRouteBase + lead.encrypted_id : '#';
                                    const displayName = lead.contact_person_name || 'Unnamed Lead';
                                    const companyNameHtml = lead.company_name ? `<div class="text-dark fs-12 mt-1"><i class="far fa-building me-1"></i>${lead.company_name}</div>` : '';
                                    
                                    let leadName = canAccessLeadDetails && detailsUrl !== '#'
                                        ? `<a href="${detailsUrl}" class="fw-bold text-dark text-decoration-none hover-primary">${displayName}</a>`
                                        : `<span class="fw-bold text-dark">${displayName}</span>`;

                                    const typeBadge = lead.status == 1
                                        ? `<span class="badge" style="font-size:0.65rem;padding:2px 6px;background:rgba(245,158,11,0.15);color:#d97706;border:1px solid #fbbf24;"><i class="fas fa-handshake me-1"></i>Deal</span>`
                                        : `<span class="badge" style="font-size:0.65rem;padding:2px 6px;background:rgba(59,130,246,0.12);color:#2563eb;border:1px solid #93c5fd;"><i class="fas fa-seedling me-1"></i>Lead</span>`;

                                    let createdByName = lead.created_by_name || 'N/A';
                                    let staffName = lead.assigned_staff ? lead.assigned_staff.name : 'Unassigned';

                                    const eyeBtn = canAccessLeadDetails && detailsUrl !== '#'
                                        ? `<a href="${detailsUrl}" class="btn btn-sm btn-light border text-primary px-2.5 py-1" title="View Lead Details" style="border-radius: 6px; font-size: 11px;"><i class="fas fa-eye"></i> View</a>`
                                        : `<span class="btn btn-sm btn-light border text-muted px-2.5 py-1" style="cursor:not-allowed;opacity:0.4;font-size: 11px;" title="No permission"><i class="fas fa-eye"></i> View</span>`;

                                    let enqNoHtml = lead.enquiry_no ? `<span class="text-muted" style="font-size: 11px;">Enq: ${lead.enquiry_no}</span>` : '';

                                    recentLeadsMobileHtml += `
                                      <div class="card mobile-dashboard-card p-3 mb-2 shadow-sm border" style="border-radius: 12px;">
                                          <div class="d-flex justify-content-between align-items-start mb-2">
                                              <div>
                                                  <div class="fw-bold text-dark fs-14">${leadName}</div>
                                                  ${enqNoHtml}
                                              </div>
                                              ${typeBadge}
                                          </div>
                                          ${companyNameHtml}
                                          <div class="text-muted fs-12 mb-2">
                                              📅 ${lead.formatted_created_at || '-'}
                                          </div>
                                          <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                              <div class="fs-10 text-muted">
                                                  <span class="badge bg-secondary-transparent text-secondary me-1" title="Created By: ${createdByName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-plus fs-8 me-1"></i>${createdByName}</span>
                                                  ${staffName !== 'Unassigned' ? `<span class="badge bg-primary-transparent text-primary" title="Assigned To: ${staffName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-check fs-8 me-1"></i>${staffName}</span>` : ''}
                                              </div>
                                              <div>
                                                  ${eyeBtn}
                                              </div>
                                          </div>
                                      </div>
                                    `;
                                });
                            } else {
                                recentLeadsMobileHtml = '<div class="text-center text-muted py-3 fs-12">No recent leads found</div>';
                            }
                            $('#mobileRecentLeads').html(recentLeadsMobileHtml);

                            if (res.data.recent_leads && res.data.recent_leads.length > 0) {
                                $('#tableRecentLeads').DataTable({
                                    autoWidth: false,
                                    responsive: true,
                                    order: [],
                                    columnDefs: [
                                        { orderable: false, targets: [1, 3] },
                                        { className: 'align-middle', targets: '_all' }
                                    ]
                                });
                            }

                            // Render Recent Quotations
                            if ($.fn.DataTable.isDataTable('#tableRecentQuotations')) {
                                $('#tableRecentQuotations').DataTable().destroy();
                            }

                            let recentQuotationsHtml = '';
                            if (res.data.recent_quotations && res.data.recent_quotations.length > 0) {
                                res.data.recent_quotations.forEach((quot, index) => {
                                    let pdfUrl = quot.encrypted_id ? quotationPdfRouteBase + quot.encrypted_id : '#';
                                    let customerName = quot.customer_name || 'N/A';
                                    let createdByName = quot.created_by_name || 'N/A';
                                    let assignedStaffName = quot.assigned_staff_name || 'Unassigned';
                                    let customerCell = `<strong>${customerName}</strong><br><span class="badge bg-secondary-transparent text-secondary mt-1 me-1" style="font-size: 0.65rem;" title="Created By: ${createdByName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-plus fs-9 me-1"></i>${createdByName}</span>`;
                                    if (assignedStaffName !== 'Unassigned') {
                                        customerCell += `<span class="badge bg-primary-transparent text-primary mt-1" style="font-size: 0.65rem;" title="Assigned To: ${assignedStaffName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-check fs-9 me-1"></i>${assignedStaffName}</span>`;
                                    }

                                    recentQuotationsHtml += `
                                      <tr>
                                          <td>${index + 1}</td>
                                          <td>
                                              <a href="${pdfUrl}" target="_blank" class="text-primary fw-bold" title="Download PDF">
                                                  <i class="fas fa-file-pdf text-danger me-1"></i> ${quot.quotation_no}
                                              </a>
                                          </td>
                                          <td>${customerCell}</td>
                                          <td class="text-end fw-bold text-success">₹${quot.formatted_grand_total || '0.00'}</td>
                                          <td class="text-center">${quot.formatted_created_at || '-'}</td>
                                      </tr>
                                  `;
                                });
                            } else {
                                recentQuotationsHtml = '<tr><td colspan="5" class="text-center text-muted p-3">No recent quotations found</td></tr>';
                            }

                            $('#bodyRecentQuotations').html(recentQuotationsHtml);

                            let recentQuotationsMobileHtml = '';
                            if (res.data.recent_quotations && res.data.recent_quotations.length > 0) {
                                const limitRecentQuotations = res.data.recent_quotations.slice(0, 5);
                                limitRecentQuotations.forEach((quot) => {
                                    let pdfUrl = quot.encrypted_id ? quotationPdfRouteBase + quot.encrypted_id : '#';
                                    let customerName = quot.customer_name || 'N/A';
                                    let createdByName = quot.created_by_name || 'N/A';
                                    let assignedStaffName = quot.assigned_staff_name || 'Unassigned';

                                    const eyeBtn = quot.encrypted_id
                                         ? `<a href="${pdfUrl}" target="_blank" class="btn btn-sm text-white d-inline-flex align-items-center justify-content-center" title="Download PDF" style="background-color: #00454f; border-color: #00454f; width: 32px; height: 32px; padding: 0; border-radius: 4px;"><i class="fas fa-eye" style="font-size: 13px;"></i></a>`
                                         : `<span class="btn btn-sm btn-light border text-muted d-inline-flex align-items-center justify-content-center" style="cursor:not-allowed;opacity:0.4;width: 32px; height: 32px; padding: 0; border-radius: 4px;" title="No PDF"><i class="fas fa-eye" style="font-size: 13px;"></i></span>`;

                                     recentQuotationsMobileHtml += `
                                       <div class="card mobile-dashboard-card mb-2 shadow-sm border" style="border-radius: 8px; padding: 10px 12px; font-size: 12px;">
                                           <div class="d-flex justify-content-between align-items-center mb-1">
                                               <div>
                                                   <span class="text-muted" style="font-size: 11px;">Quotation No:</span>
                                                   <strong class="text-dark">${quot.quotation_no}</strong>
                                               </div>
                                           </div>
                                           <div class="mb-1">
                                               <span class="text-muted" style="font-size: 11px;">Customer:</span>
                                               <strong class="text-dark">${customerName}</strong>
                                           </div>
                                           <div class="d-flex justify-content-between align-items-center mb-1">
                                               <div>
                                                   <span class="text-muted" style="font-size: 11px;">Date:</span>
                                                   <strong class="text-dark">${quot.formatted_created_at || '-'}</strong>
                                               </div>
                                               <div>
                                                   <span class="text-muted" style="font-size: 11px;">Total:</span>
                                                   <strong class="text-success" style="font-size: 13px;">₹${quot.formatted_grand_total || '0.00'}</strong>
                                               </div>
                                           </div>
                                           ${quot.is_direct == 0 ? `
                                           <div class="mb-2">
                                               <span class="badge bg-info-transparent text-info" style="font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 4px;">Direct Quotation</span>
                                           </div>
                                           ` : ''}
                                           <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-1">
                                               <div class="fs-10 text-muted">
                                                   <span class="badge bg-secondary-transparent text-secondary me-1" style="font-size: 0.65rem;" title="Created By: ${createdByName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-plus fs-8 me-1"></i>${createdByName}</span>
                                                   ${assignedStaffName !== 'Unassigned' ? `<span class="badge bg-primary-transparent text-primary" style="font-size: 0.65rem;" title="Assigned To: ${assignedStaffName}" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-user-check fs-8 me-1"></i>${assignedStaffName}</span>` : ''}
                                               </div>
                                               <div>
                                                   ${eyeBtn}
                                               </div>
                                           </div>
                                       </div>
                                     `;
                                });
                            } else {
                                recentQuotationsMobileHtml = '<div class="text-center text-muted py-3 fs-12">No recent quotations found</div>';
                            }
                            $('#mobileRecentQuotations').html(recentQuotationsMobileHtml);

                            if (res.data.recent_quotations && res.data.recent_quotations.length > 0) {
                                $('#tableRecentQuotations').DataTable({
                                    autoWidth: false,
                                    responsive: true,
                                    order: [],
                                    columnDefs: [
                                        { orderable: false, targets: [1, 2, 3] },
                                        { className: 'align-middle', targets: '_all' }
                                    ]
                                });
                            }

                            // Re-initialize Bootstrap tooltips for dynamically injected badges
                            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                                var existingTooltip = bootstrap.Tooltip.getInstance(el);
                                if (existingTooltip) existingTooltip.dispose();
                                new bootstrap.Tooltip(el, { trigger: 'hover', placement: el.dataset.bsPlacement || 'top' });
                            });

                            // Render Recent Activities
                            let activitiesHtml = '';
                            if (res.data.activities && res.data.activities.length > 0) {
                                res.data.activities.forEach(act => {
                                    let iconClass = 'fa-bell';
                                    let bgClass = 'rgba(59, 130, 246, 0.1)';
                                    let textClass = 'text-primary';

                                    if (act.type === 'deal') {
                                        iconClass = 'fa-handshake';
                                        bgClass = 'rgba(59, 130, 246, 0.1)';
                                        textClass = 'text-primary';
                                    } else if (act.type === 'quotation') {
                                        iconClass = 'fa-file-invoice-dollar';
                                        bgClass = 'rgba(16, 185, 129, 0.1)';
                                        textClass = 'text-success';
                                    } else if (act.type === 'performa-invoice') {
                                        iconClass = 'fa-file-invoice';
                                        bgClass = 'rgba(245, 158, 11, 0.1)';
                                        textClass = 'text-warning';
                                    } else if (act.type === 'lead') {
                                        iconClass = 'fa-seedling';
                                        bgClass = 'rgba(139, 92, 246, 0.1)';
                                        textClass = 'text-purple';
                                    }

                                    activitiesHtml += `
                                      <li class="list-group-item border-0 p-3 d-flex align-items-start gap-3 border-bottom" style="background: transparent;">
                                          <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; border-radius: 50%; background-color: ${bgClass};">
                                              <i class="fas ${iconClass} ${textClass} fs-14"></i>
                                          </div>
                                          <div class="flex-grow-1">
                                              <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                                  <h6 class="mb-0 fw-bold text-dark fs-12 text-uppercase">${act.action}</h6>
                                                  <span class="fs-10 text-muted fw-semibold">${act.formatted_time || ''}</span>
                                              </div>
                                              <p class="mb-0 text-muted fs-11 mt-1">
                                                  <strong>${act.title}</strong>: ${act.description || ''} <br>
                                                  <span class="text-dark fw-medium" style="font-size: 0.7rem;"><i class="fas fa-user-edit me-1 fs-9"></i>By ${act.performed_by || 'User'}</span>
                                              </p>
                                          </div>
                                      </li>
                                  `;
                                });
                            } else {
                                activitiesHtml = `
                                  <div class="text-center text-muted p-4">
                                      <i class="fas fa-tasks fs-24 mb-2 text-muted" style="opacity: 0.5;"></i>
                                      <p class="mb-0 fs-12">No recent activities log found</p>
                                  </div>
                              `;
                            }

                            $('#recentActivitiesList').html(activitiesHtml);

                            // Restore scroll position if navigated back from details/sub-pages
                            const navigationType = window.performance && window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0]
                                ? window.performance.getEntriesByType('navigation')[0].type
                                : null;
                            const isBackNavigation = navigationType === 'back_forward';
                            const referrer = document.referrer;
                            const isFromSubPage = referrer && (
                                referrer.includes('/lead/details') ||
                                referrer.includes('/deal/details') ||
                                referrer.includes('/quotation') ||
                                referrer.includes('/invoice')
                            );

                            if (isBackNavigation || isFromSubPage) {
                                const savedScroll = sessionStorage.getItem('dashboard_scroll_pos');
                                if (savedScroll) {
                                    $(window).scrollTop(parseInt(savedScroll));
                                    setTimeout(function () {
                                        $(window).scrollTop(parseInt(savedScroll));
                                        $('html, body').css('min-height', '');
                                        $('#loader').removeClass('force-visible').fadeOut(300, function () {
                                            $(this).addClass('d-none');
                                            $('body').addClass('ready-active');
                                        });
                                    }, 50);
                                } else {
                                    $('html, body').css('min-height', '');
                                    $('#loader').removeClass('force-visible').fadeOut(300, function () {
                                        $(this).addClass('d-none');
                                        $('body').addClass('ready-active');
                                    });
                                }
                            } else {
                                $('html, body').css('min-height', '');
                                $('#loader').removeClass('force-visible').fadeOut(300, function () {
                                    $(this).addClass('d-none');
                                    $('body').addClass('ready-active');
                                });
                            }

                        } else {
                            iziToast.error({ title: 'Error', message: res.message || 'Failed to load dashboard data', position: 'topRight' });
                        }
                    }).fail(function () {
                        if (isButtonClick) {

                        } else {
                            iziToast.error({ title: 'Error', message: res.message || 'Failed to load dashboard data', position: 'topRight' });
                        }
                    }).fail(function () {
                        if (isButtonClick) {
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                        $('.card-body, .table-responsive').css('opacity', '1');
                        iziToast.error({ title: 'Error', message: 'Failed to communicate with dashboard API', position: 'topRight' });
                    });
                }

                // Note Follow-up Status logic
                $(document).on('click', '.complete-note-btn', function () {
                    let noteId = $(this).data('id');
                    let status = $(this).data('status');
                    $('#completeNoteId').val(noteId);
                    $('#completeNoteStatus').val(status);
                    $('#completeNoteResult').val('');
                    $('#modalCompleteNote').modal('show');
                });

                $('#formCompleteNote').on('submit', function (e) {
                    e.preventDefault();
                    let formData = $(this).serialize();
                    $('#btnSubmitNoteResult').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
                    $.post("{{ route('crm.activity.update-note-status') }}", formData, function (res) {
                        $('#btnSubmitNoteResult').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Result');
                        if (res.success) {
                            $('#modalCompleteNote').modal('hide');
                            iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                            loadDashboardData();
                        } else {
                            iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                        }
                    }).fail(function (xhr) {
                        $('#btnSubmitNoteResult').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Result');
                        iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Server error', position: 'topRight' });
                    });
                });

                $(document).on('click', '.show-followup-details', function () {
                    const index = $(this).data('index');
                    const fu = window.dashboardFollowUps && window.dashboardFollowUps[index];
                    if (!fu) return;

                    let leadName = 'N/A';
                    if (fu.lead) {
                        leadName = fu.lead.company_name || fu.lead.contact_person_name || 'Unnamed Lead';
                        if (fu.lead.enquiry_no) {
                            leadName += ` (Enq: ${fu.lead.enquiry_no})`;
                        }
                    }

                    let formattedFuDate = fu.follow_up_date || 'N/A';
                    if (fu.follow_up_date) {
                        try {
                            const d = new Date(fu.follow_up_date);
                            const day = String(d.getDate()).padStart(2, '0');
                            const month = String(d.getMonth() + 1).padStart(2, '0');
                            const year = d.getFullYear();
                            formattedFuDate = `${day}-${month}-${year}`;
                        } catch (e) { }
                    }

                    const refType = fu.reference_type === 'deal' ? 'Deal' : 'Lead';
                    const createdByName = fu.created_by_name || 'N/A';
                    const assignedStaffName = fu.assigned_staff ? fu.assigned_staff.name : 'Unassigned';

                    const html = `
                        <div class="row g-3" style="font-size: 13px;">
                            <div class="col-sm-6">
                                <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Lead / Company</span>
                                <span class="fw-bold text-dark fs-14">${leadName}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Reference Type</span>
                                <span class="badge bg-light text-dark border text-capitalize">${refType}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Follow-up Date</span>
                                <span class="fw-bold text-primary">${formattedFuDate}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Created By</span>
                                <span class="fw-medium text-dark">${createdByName}</span>
                            </div>
                            <div class="col-sm-12">
                                <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Assigned Staff</span>
                                <span class="fw-medium text-dark">${assignedStaffName}</span>
                            </div>
                            <div class="col-sm-12">
                                <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Remarks</span>
                                <div class="p-3 bg-light rounded border text-dark text-break mt-1" style="font-size: 13px; max-height: 250px; overflow-y: auto; white-space: pre-wrap; line-height: 1.5;">${fu.remarks || '-'}</div>
                            </div>
                        </div>
                    `;

                    $('#dashboardFollowUpViewBody').html(html);
                    $('#dashboardFollowUpViewModal').modal('show');
                });

                // Initial load
                loadDashboardData();
                loadDashboardCalls();
                loadCampaignOverview();

                const campaignViewUrlBase = "{{ route('campaign.view', ':id') }}";

                $(document).on('click', '.view-dashboard-campaign', function () {
                    const id = $(this).data('id');
                    $('#dashboardCampaignViewBody').html('<div class="text-center py-4"><span class="spinner-border spinner-border-sm text-primary"></span> Loading details...</div>');
                    $('#dashboardCampaignViewModal').modal('show');

                    $.ajax({
                        url: campaignViewUrlBase.replace(':id', id),
                        type: "GET",
                        dataType: "json",
                        success: function (response) {
                            if (response.status === 'success') {
                                const d = response.data;
                                const designs = response.designs;

                                let designsHtml = '';
                                if (designs && designs.length > 0) {
                                    designs.forEach(design => {
                                        let imgsHtml = '';
                                        if (design.images && design.images.length > 0) {
                                            design.images.forEach(url => {
                                                imgsHtml += `
                                    <div class="preview-thumb-wrapper shadow-sm" style="width:80px;height:80px;border-radius:6px;overflow:hidden;border:1px solid #dee2e6;display:inline-block;">
                                        <a href="${url}" target="_blank">
                                            <img src="${url}" style="width:100%;height:100%;object-fit:cover;">
                                        </a>
                                    </div>`;
                                            });
                                        } else {
                                            imgsHtml = '<span class="text-muted fs-12">No images uploaded</span>';
                                        }
                                        designsHtml += `
                            <div class="mb-3 p-3 border rounded-3 bg-light">
                                <h6 class="fw-bold fs-13 mb-2">${design.name || 'Untitled Design'}</h6>
                                <div class="d-flex flex-wrap gap-2">${imgsHtml}</div>
                            </div>`;
                                    });
                                } else {
                                    designsHtml = '<div class="text-muted fs-13">No design details added.</div>';
                                }

                                const html = `
                    <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h5 class="fw-bold text-dark mb-0">${d.campaign_name}</h5>
                        ${d.timeline_status}
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Campaign Type</span><strong>${d.campaign_type_name}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Owner / Assigned To</span><strong>${d.assigned_to_name}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Stall Number</span><strong>${d.stall_number}</strong></div>

                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Start Date</span><strong>${d.start_date}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">End Date</span><strong>${d.end_date}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Number of Visitors</span><strong>${d.num_visitors}</strong></div>

                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Expected Budget</span><strong>${d.expected_budget}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Actual Cost</span><strong>${d.actual_cost}</strong></div>
                        <div class="col-md-4"><span class="text-muted fs-12 d-block">Venue / Location</span><strong>${d.venue_location}</strong></div>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted fs-12 d-block mb-1">Description / Remarks</span>
                        <div class="p-2 border rounded bg-light fs-13">${d.description}</div>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted fs-12 d-block mb-1">Machines to be Displayed / Used</span>
                        <div class="p-2 border rounded bg-light fs-13">${d.machines_displayed}</div>
                    </div>
                    <hr>
                    <h6 class="fw-bold text-primary mb-3">Posters, Banners & Design Details</h6>
                    ${designsHtml}
                `;

                                $('#dashboardCampaignViewBody').html(html);
                            } else {
                                $('#dashboardCampaignViewBody').html('<div class="text-center py-4 text-danger">Failed to load campaign details.</div>');
                            }
                        },
                        error: function () {
                            $('#dashboardCampaignViewBody').html('<div class="text-center py-4 text-danger">Failed to fetch campaign details.</div>');
                        }
                    });
                });

                // Click Complete Call button
                $(document).on('click', '.btn-complete-call', function () {
                    const id = $(this).data('id');
                    $('#result_call_id').val(id);
                    $('#result_call_status').val(1); // 1 = Completed
                    $('#callResultInput').val('');
                    $('#callResultModalLabel').html('<i class="fas fa-check text-success me-2"></i>Complete Call - Result');
                    $('#callResultModal').modal('show');
                });

                // Click Cancel Call button
                $(document).on('click', '.btn-cancel-call', function () {
                    const id = $(this).data('id');
                    $('#result_call_id').val(id);
                    $('#result_call_status').val(2); // 2 = Rejected/Cancelled
                    $('#callResultInput').val('');
                    $('#callResultModalLabel').html('<i class="fas fa-times text-danger me-2"></i>Cancel Call - Result');
                    $('#callResultModal').modal('show');
                });

                // Call Result Form Submit
                $('#callResultForm').submit(function (e) {
                    e.preventDefault();

                    const id = $('#result_call_id').val();
                    const callStatus = $('#result_call_status').val();
                    const callResult = $('#callResultInput').val().trim();

                    if (!callResult) {
                        iziToast.error({ title: 'Validation Error', message: 'Call Result message is required.', position: 'topRight' });
                        return false;
                    }

                    $('#btnSubmitCallResult').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

                    $.post("{{ route('deal.details.update-call-status') }}", {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        call_status: callStatus,
                        call_result: callResult
                    }, function (res) {
                        $('#btnSubmitCallResult').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Result');
                        if (res.success) {
                            $('#callResultModal').modal('hide');
                            iziToast.success({ title: 'Success', message: res.message, position: 'topRight' });
                            loadDashboardCalls();
                        } else {
                            iziToast.error({ title: 'Error', message: res.message, position: 'topRight' });
                        }
                    }).fail(function (xhr) {
                        $('#btnSubmitCallResult').prop('disabled', false).html('<i class="fas fa-check me-1"></i> Save Result');
                        iziToast.error({ title: 'Error', message: xhr.responseJSON?.message || 'Server error', position: 'topRight' });
                    });
                });

                // Handle Delete Call
                $(document).on('click', '.delete-call-btn', function () {
                    const id = $(this).data('id');
                    iziToast.question({
                        timeout: false,
                        close: false,
                        overlay: true,
                        displayMode: 'once',
                        title: 'Confirm Delete',
                        message: 'Are you sure you want to delete this scheduled call?',
                        position: 'center',
                        buttons: [
                            ['<button><b>Yes, Delete</b></button>', function (instance, toast) {
                                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                                $.post("{{ route('deal.details.delete-call') }}", {
                                    _token: "{{ csrf_token() }}",
                                    id: id
                                }, function (res) {
                                    if (res.success) {
                                        iziToast.success({
                                            title: 'Deleted',
                                            message: res.message,
                                            position: 'topRight'
                                        });
                                        loadDashboardCalls();
                                    }
                                });
                            }, true],
                            ['<button>Cancel</button>', function (instance, toast) {
                                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            }],
                        ],
                    });
                });

                $(document).on('click', '.btn-copy-webhook-dash', function (e) {
                    e.preventDefault();
                    const url = $(this).data('url');
                    navigator.clipboard.writeText(url).then(function () {
                        iziToast.success({
                            title: 'Success',
                            message: 'Webhook URL copied successfully!',
                            position: 'topRight',
                            timeout: 2000
                        });
                    }).catch(function () {
                        iziToast.error({
                            title: 'Error',
                            message: 'Failed to copy Webhook URL.',
                            position: 'topRight'
                        });
                    });
                });
            });
        </script>


</body>

</html>