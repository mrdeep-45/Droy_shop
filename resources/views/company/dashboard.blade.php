@php
    $crm_master_submenu = DB::table('company_menus as m')
        ->join('company_submenus as s', 's.c_menu_id', '=', 'm.c_menu_id')
        ->where('m.menu_title', 'like', 'crm master%')
        ->orderBy('s.sub_menu_title', 'asc')
        ->select(
            'm.c_menu_id',
            'm.menu_title',
            's.submenu_order',
            's.sub_menu_title',
            's.sub_menu_icon',
            's.sub_menu_route'
        )
        ->get();

    function detectIcon($title)
    {
        $title = strtolower($title);
        if (str_contains($title, 'area')) return '<i class="fa-solid fa-map"></i>';
        if (str_contains($title, 'deal')) return '<i class="fa-solid fa-handshake"></i>';
        if (str_contains($title, 'direction')) return '<i class="fa-solid fa-compass"></i>';
        if (str_contains($title, 'source')) return '<i class="fa-solid fa-signal"></i>';
        if (str_contains($title, 'status')) return '<i class="fa-solid fa-list-check"></i>';
        if (str_contains($title, 'lead')) return '<i class="fa-solid fa-user-plus"></i>';
        return '<i class="fa-solid fa-cube"></i>';
    }

    $totalLeads = Schema::hasTable('tbl_lead') ? DB::table('tbl_lead')->count() : 0;
    $totalDeals = Schema::hasTable('tbl_deal') ? DB::table('tbl_deal')->count() : 0;
    $totalQuotations = Schema::hasTable('tbl_quotation') ? DB::table('tbl_quotation')->count() : 0;
    $totalInvoices = Schema::hasTable('tbl_main_invoice') ? DB::table('tbl_main_invoice')->count() : 0;
    $totalCustomers = Schema::hasTable('tbl_customer') ? DB::table('tbl_customer')->count() : 0;
@endphp

<!DOCTYPE html>
<html {!! get_html_data() !!}>

<head>
    @include('include.meta')
    @include('include.header_link')
    <style>
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Hero Card Header */
        .dashboard-hero-card {
            background: linear-gradient(135deg, #00454f 0%, #00606e 100%);
            border-radius: 1.25rem;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 69, 79, 0.25);
            border: 1px solid rgba(0, 180, 180, 0.2);
        }

        .dashboard-hero-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 242, 254, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Metric Cards */
        .metric-card {
            background: #ffffff;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 69, 79, 0.15);
        }

        .metric-card .icon-badge {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            transition: transform 0.3s ease;
        }

        .metric-card:hover .icon-badge {
            transform: scale(1.1) rotate(4deg);
        }

        .metric-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.2;
        }

        .metric-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Action Buttons & Cards */
        .quick-action-tile {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.85rem;
            padding: 1rem;
            transition: all 0.25s ease;
            text-decoration: none !important;
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .quick-action-tile:hover {
            border-color: #00b4b4;
            background: #f0fdfa;
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 180, 180, 0.15);
        }

        .quick-action-tile .tile-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        .section-title-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .section-title-box h6 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
        }
    </style>
</head>

<body>
    @include('include.switcher')
    @include('include.loader')
    <div class="page">
        @include('include.header')

        <div class="page-header-breadcrumb d-md-flex d-block align-items-center justify-content-between">
            <h4 class="fw-medium mb-0">{{ $page_title }}</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-white-50">{{ $page_title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $page_name }}</li>
            </ol>
        </div>

        <div class="main-content app-content">
            <div class="container-fluid dashboard-container">

                <!-- Executive Hero Header -->
                <div class="dashboard-hero-card p-4 mb-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-3 mb-lg-0">
                            <span class="badge bg-white text-dark mb-2 px-3 py-1 fw-bold fs-11 text-uppercase" style="border-radius: 20px;">
                                <i class="fa-solid fa-shield-halved text-success me-1"></i> Executive Portal
                            </span>
                            <h3 class="fw-extrabold mb-1 text-white">Welcome back to Hexagon CRM</h3>
                            <p class="mb-0 text-white-50 fs-13">Monitor lead conversion, quotation performance, and active deals in real time.</p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                @if(Route::has('lead.index'))
                                    <a href="{{ route('lead.index') }}" class="btn btn-light btn-sm fw-bold px-3 py-2 animate-btn" style="border-radius: 8px;">
                                        <i class="fa-solid fa-plus me-1 text-primary"></i> Add Lead
                                    </a>
                                @endif
                                @if(Route::has('direct_quotation.index'))
                                    <a href="{{ route('direct_quotation.index') }}" class="btn btn-info btn-sm text-white fw-bold px-3 py-2 animate-btn" style="border-radius: 8px;">
                                        <i class="fa-solid fa-file-invoice me-1"></i> Create Quotation
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modern Metric Cards Grid -->
                <div class="row g-3 mb-4">
                    <!-- Total Leads -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="metric-card p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="icon-badge bg-primary-transparent text-primary">
                                    <i class="fa-solid fa-users-line"></i>
                                </div>
                                <span class="badge bg-success-transparent text-success fw-bold fs-11">
                                    <i class="fa-solid fa-arrow-trend-up me-1"></i>Active
                                </span>
                            </div>
                            <div class="metric-value mb-1">{{ number_format($totalLeads) }}</div>
                            <div class="metric-label">Total Leads</div>
                            <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">Pipeline records</span>
                                @if(Route::has('lead.index'))
                                    <a href="{{ route('lead.index') }}" class="fs-12 fw-bold text-primary text-decoration-none">
                                        View <i class="fa-solid fa-arrow-right fs-10"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Total Deals -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="metric-card p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="icon-badge bg-info-transparent text-info">
                                    <i class="fa-solid fa-handshake"></i>
                                </div>
                                <span class="badge bg-info-transparent text-info fw-bold fs-11">
                                    <i class="fa-solid fa-chart-pie me-1"></i>Deals
                                </span>
                            </div>
                            <div class="metric-value mb-1">{{ number_format($totalDeals) }}</div>
                            <div class="metric-label">Active Deals</div>
                            <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">Won & In Progress</span>
                                @if(Route::has('deal.index'))
                                    <a href="{{ route('deal.index') }}" class="fs-12 fw-bold text-info text-decoration-none">
                                        View <i class="fa-solid fa-arrow-right fs-10"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Total Quotations -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="metric-card p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="icon-badge bg-warning-transparent text-warning">
                                    <i class="fa-solid fa-file-signature"></i>
                                </div>
                                <span class="badge bg-warning-transparent text-warning fw-bold fs-11">
                                    <i class="fa-solid fa-paper-plane me-1"></i>Sent
                                </span>
                            </div>
                            <div class="metric-value mb-1">{{ number_format($totalQuotations) }}</div>
                            <div class="metric-label">Quotations Generated</div>
                            <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">Domestic & Export</span>
                                @if(Route::has('quotation.index'))
                                    <a href="{{ route('quotation.index') }}" class="fs-12 fw-bold text-warning text-decoration-none">
                                        View <i class="fa-solid fa-arrow-right fs-10"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Total Invoices -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="metric-card p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="icon-badge bg-success-transparent text-success">
                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                </div>
                                <span class="badge bg-success-transparent text-success fw-bold fs-11">
                                    <i class="fa-solid fa-circle-check me-1"></i>Invoices
                                </span>
                            </div>
                            <div class="metric-value mb-1">{{ number_format($totalInvoices) }}</div>
                            <div class="metric-label">Main Invoices</div>
                            <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">Billing records</span>
                                @if(Route::has('main_invoice.index'))
                                    <a href="{{ route('main_invoice.index') }}" class="fs-12 fw-bold text-success text-decoration-none">
                                        View <i class="fa-solid fa-arrow-right fs-10"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shortcuts & CRM Master Grid -->
                <div class="row g-4 mb-4">
                    <!-- Quick Actions Center -->
                    <div class="col-12 col-xl-6">
                        <div class="card custom-card border-0 shadow-sm h-100" style="border-radius: 1rem;">
                            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                                <div class="section-title-box mb-0">
                                    <i class="fa-solid fa-bolt text-warning fs-16"></i>
                                    <h6>Quick Actions</h6>
                                </div>
                                <span class="badge bg-light text-secondary border fs-11">Fast Navigation</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    @if(Route::has('lead.index'))
                                        <div class="col-12 col-sm-6">
                                            <a href="{{ route('lead.index') }}" class="quick-action-tile">
                                                <div class="tile-icon bg-primary text-white">
                                                    <i class="fa-solid fa-user-plus"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-13">Leads Hub</div>
                                                    <div class="fs-11 text-muted">Manage prospective clients</div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if(Route::has('deal.index'))
                                        <div class="col-12 col-sm-6">
                                            <a href="{{ route('deal.index') }}" class="quick-action-tile">
                                                <div class="tile-icon bg-info text-white">
                                                    <i class="fa-solid fa-handshake"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-13">Deals Kanban</div>
                                                    <div class="fs-11 text-muted">Track pipeline stages</div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if(Route::has('direct_quotation.index'))
                                        <div class="col-12 col-sm-6">
                                            <a href="{{ route('direct_quotation.index') }}" class="quick-action-tile">
                                                <div class="tile-icon bg-warning text-white">
                                                    <i class="fa-solid fa-file-invoice"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-13">Direct Quotation</div>
                                                    <div class="fs-11 text-muted">Create custom quote</div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if(Route::has('performa_invoice.index'))
                                        <div class="col-12 col-sm-6">
                                            <a href="{{ route('performa_invoice.index') }}" class="quick-action-tile">
                                                <div class="tile-icon bg-success text-white">
                                                    <i class="fa-solid fa-receipt"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-13">Performa Invoice</div>
                                                    <div class="fs-11 text-muted">Manage pro-forma billing</div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CRM Master Modules -->
                    <div class="col-12 col-xl-6">
                        <div class="card custom-card border-0 shadow-sm h-100" style="border-radius: 1rem;">
                            <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
                                <div class="section-title-box mb-0">
                                    <i class="fa-solid fa-cubes text-primary fs-16"></i>
                                    <h6>CRM Master Configurations</h6>
                                </div>
                                <span class="badge bg-light text-secondary border fs-11">{{ count($crm_master_submenu) }} Modules</span>
                            </div>
                            <div class="card-body p-3">
                                @if(count($crm_master_submenu) > 0)
                                    <div class="row g-2">
                                        @foreach($crm_master_submenu as $sub)
                                            @if(Route::has($sub->sub_menu_route))
                                                <div class="col-6 col-sm-4">
                                                    <a href="{{ route($sub->sub_menu_route) }}" class="p-2 border rounded d-flex align-items-center gap-2 text-dark text-decoration-none bg-light-hover transition-all">
                                                        <span class="fs-14 text-primary">{!! detectIcon($sub->sub_menu_title) !!}</span>
                                                        <span class="fs-12 fw-semibold text-truncate">{{ $sub->sub_menu_title }}</span>
                                                    </a>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-folder-open fs-24 mb-2 d-block text-secondary opacity-50"></i>
                                        <span class="fs-12">No CRM master configurations available.</span>
                                    </div>
                                @endif
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
</body>

</html>
