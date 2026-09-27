{{-- ═══════════════════════════════════════════
     SHOP ADMIN PANEL — CSS/HEAD LINKS
     Reuses existing Hexagon theme assets
═══════════════════════════════════════════ --}}

{{-- Google Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

{{-- Bootstrap 5 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

{{-- Font Awesome 6 --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

{{-- Boxicons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

{{-- Select2 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

{{-- DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

{{-- SweetAlert2 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

{{-- Toastr --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

{{-- Shop Admin Panel Custom CSS --}}
<style>
    :root {
        --shop-primary:       #0d6efd;
        --shop-primary-dark:  #0a58ca;
        --shop-sidebar-bg:    #141e2e;
        --shop-sidebar-width: 260px;
        --shop-topbar-height: 60px;
        --shop-text-light:    #a9b5cc;
        --shop-accent:        #20c997;
        --shop-accent2:       #6f42c1;
        --shop-card-shadow:   0 2px 20px rgba(0,0,0,0.08);
        --shop-border-radius: 12px;
    }

    * { box-sizing: border-box; }

    body {
        font-family: 'Inter', sans-serif;
        background: #f0f3f8;
        color: #2d3748;
        overflow-x: hidden;
    }

    /* ── Sidebar ──────────────────────────────── */
    #shopSidebar {
        position: fixed;
        top: 0; left: 0;
        width: var(--shop-sidebar-width);
        height: 100vh;
        background: var(--shop-sidebar-bg);
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 1040;
        transition: width 0.3s ease, transform 0.3s ease;
        scrollbar-width: thin;
        scrollbar-color: #2d3a52 transparent;
    }
    #shopSidebar::-webkit-scrollbar { width: 4px; }
    #shopSidebar::-webkit-scrollbar-thumb { background: #2d3a52; border-radius: 4px; }

    /* Logo Area */
    .shop-sidebar-logo {
        padding: 0 20px;
        height: var(--shop-topbar-height);
        display: flex;
        align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        background: #0f1824;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .shop-sidebar-logo .logo-text {
        font-size: 1.35rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.5px;
    }
    .shop-sidebar-logo .logo-text span { color: var(--shop-accent); }
    .shop-sidebar-logo .logo-badge {
        font-size: 0.6rem;
        background: var(--shop-accent);
        color: #fff;
        padding: 1px 6px;
        border-radius: 20px;
        margin-left: 6px;
        vertical-align: middle;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    /* Section Labels */
    .sidebar-section-label {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: #4a5568;
        text-transform: uppercase;
        padding: 16px 20px 6px;
        margin-top: 4px;
    }

    /* Menu Items */
    .shop-nav-link {
        display: flex;
        align-items: center;
        padding: 9px 20px;
        color: var(--shop-text-light);
        text-decoration: none;
        font-size: 0.845rem;
        font-weight: 500;
        border-radius: 0;
        transition: all 0.2s ease;
        position: relative;
        cursor: pointer;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }
    .shop-nav-link i {
        font-size: 1.05rem;
        margin-right: 10px;
        width: 20px;
        text-align: center;
        flex-shrink: 0;
    }
    .shop-nav-link:hover {
        color: #fff;
        background: rgba(255,255,255,0.06);
    }
    .shop-nav-link.active {
        color: #fff;
        background: linear-gradient(90deg, rgba(13,110,253,0.25), rgba(13,110,253,0.08));
        border-left: 3px solid var(--shop-primary);
    }
    .shop-nav-link .nav-arrow {
        margin-left: auto;
        font-size: 0.75rem;
        transition: transform 0.25s ease;
    }
    .shop-nav-link[aria-expanded="true"] .nav-arrow { transform: rotate(90deg); }

    /* Submenu */
    .shop-submenu {
        background: rgba(0,0,0,0.15);
        border-left: 2px solid rgba(255,255,255,0.05);
        margin-left: 16px;
    }
    .shop-submenu .shop-nav-link {
        padding: 7px 14px 7px 24px;
        font-size: 0.81rem;
        color: #8a97b0;
    }
    .shop-submenu .shop-nav-link i { font-size: 0.7rem; }
    .shop-submenu .shop-nav-link:hover { color: #fff; }
    .shop-submenu .shop-nav-link.active { color: var(--shop-accent); border-left-color: var(--shop-accent); background: transparent; }

    /* Badge on nav */
    .nav-badge {
        margin-left: auto;
        font-size: 0.65rem;
        padding: 2px 7px;
        border-radius: 20px;
        font-weight: 600;
    }

    /* ── Topbar ───────────────────────────────── */
    #shopTopbar {
        position: fixed;
        top: 0;
        left: var(--shop-sidebar-width);
        right: 0;
        height: var(--shop-topbar-height);
        background: #fff;
        display: flex;
        align-items: center;
        padding: 0 20px;
        z-index: 1030;
        box-shadow: 0 1px 8px rgba(0,0,0,0.08);
        transition: left 0.3s ease;
        gap: 12px;
    }
    .topbar-search {
        flex: 1;
        max-width: 380px;
    }
    .topbar-search .input-group-text {
        background: #f8f9fa;
        border-color: #e9ecef;
        color: #9aacbf;
    }
    .topbar-search .form-control {
        border-color: #e9ecef;
        background: #f8f9fa;
        font-size: 0.85rem;
    }
    .topbar-search .form-control:focus { box-shadow: none; border-color: var(--shop-primary); background: #fff; }

    .topbar-icon-btn {
        width: 36px; height: 36px;
        border-radius: 8px;
        border: none;
        background: #f0f3f8;
        color: #4a5568;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        text-decoration: none;
    }
    .topbar-icon-btn:hover { background: var(--shop-primary); color: #fff; }
    .topbar-badge {
        position: absolute; top: -3px; right: -3px;
        background: #dc3545; color: #fff;
        width: 16px; height: 16px; border-radius: 50%;
        font-size: 0.6rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
    }
    .user-avatar-btn {
        display: flex; align-items: center; gap: 8px;
        background: none; border: none; cursor: pointer;
        padding: 0;
    }
    .user-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, var(--shop-primary), var(--shop-accent2));
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 0.9rem;
    }
    .user-info-text .user-name { font-size: 0.82rem; font-weight: 600; color: #2d3748; line-height: 1.2; }
    .user-info-text .user-role { font-size: 0.7rem; color: #9aacbf; line-height: 1.2; }

    /* ── Main Content ─────────────────────────── */
    #shopMain {
        margin-left: var(--shop-sidebar-width);
        margin-top: var(--shop-topbar-height);
        padding: 24px;
        min-height: calc(100vh - var(--shop-topbar-height));
        transition: margin-left 0.3s ease;
    }

    /* ── Cards ────────────────────────────────── */
    .shop-card {
        background: #fff;
        border-radius: var(--shop-border-radius);
        box-shadow: var(--shop-card-shadow);
        border: none;
        overflow: hidden;
    }
    .shop-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f3f8;
        padding: 16px 20px;
        font-weight: 600;
        font-size: 0.9rem;
        color: #2d3748;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }
    .shop-card .card-body { padding: 20px; }

    /* ── KPI Cards ────────────────────────────── */
    .kpi-card {
        border-radius: var(--shop-border-radius);
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        box-shadow: var(--shop-card-shadow);
        border: none;
        position: relative;
        overflow: hidden;
        color: #fff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.15); }
    .kpi-card::after {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
    }
    .kpi-card.blue  { background: linear-gradient(135deg, #0d6efd, #4e9eff); }
    .kpi-card.green { background: linear-gradient(135deg, #20c997, #0fcf97); }
    .kpi-card.orange{ background: linear-gradient(135deg, #fd7e14, #ffa94d); }
    .kpi-card.purple{ background: linear-gradient(135deg, #6f42c1, #9b6fe8); }
    .kpi-card.red   { background: linear-gradient(135deg, #dc3545, #ff6b7a); }
    .kpi-card.cyan  { background: linear-gradient(135deg, #0dcaf0, #62dcf0); }
    .kpi-card.indigo{ background: linear-gradient(135deg, #4f46e5, #818cf8); }
    .kpi-card.teal  { background: linear-gradient(135deg, #14b8a6, #2dd4bf); }

    .kpi-icon {
        width: 52px; height: 52px;
        border-radius: 10px;
        background: rgba(255,255,255,0.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .kpi-body { flex: 1; }
    .kpi-value {
        font-size: 1.85rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 4px;
    }
    .kpi-label {
        font-size: 0.78rem;
        opacity: 0.85;
        font-weight: 500;
    }
    .kpi-sub {
        font-size: 0.72rem;
        opacity: 0.7;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .kpi-sub i { font-size: 0.85rem; }

    /* ── DataTable overrides ──────────────────── */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        font-size: 0.83rem;
    }
    table.dataTable { border-collapse: collapse !important; }
    table.dataTable thead th {
        background: #f8f9fa;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom: 2px solid #e9ecef !important;
    }
    table.dataTable tbody tr:hover { background: #f8fbff !important; }

    /* ── Page Header ─────────────────────────── */
    .shop-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .shop-page-header h4 {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1a202c;
        margin: 0;
    }
    .shop-breadcrumb {
        font-size: 0.8rem;
        color: #9aacbf;
        margin-top: 3px;
    }
    .shop-breadcrumb a { color: #9aacbf; text-decoration: none; }
    .shop-breadcrumb a:hover { color: var(--shop-primary); }

    /* ── Responsive ──────────────────────────── */
    @media (max-width: 991.98px) {
        #shopSidebar {
            transform: translateX(-100%);
            box-shadow: none;
        }
        #shopSidebar.show {
            transform: translateX(0);
            box-shadow: 4px 0 20px rgba(0,0,0,0.2);
        }
        #shopTopbar { left: 0; }
        #shopMain { margin-left: 0; }
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1039;
            display: none;
        }
        .sidebar-overlay.show { display: block; }
    }

    /* ── Utilities ───────────────────────────── */
    .text-shop-primary { color: var(--shop-primary) !important; }
    .bg-shop-primary   { background: var(--shop-primary) !important; }
    .btn-shop-primary  { background: var(--shop-primary); border-color: var(--shop-primary); color: #fff; }
    .btn-shop-primary:hover { background: var(--shop-primary-dark); border-color: var(--shop-primary-dark); color: #fff; }

    .modern-table th { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .tab-btn-group { display: flex; gap: 6px; flex-wrap: wrap; }
    .tab-btn {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        border: 2px solid #e9ecef;
        background: #fff;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .tab-btn.active, .tab-btn:hover {
        background: var(--shop-primary);
        border-color: var(--shop-primary);
        color: #fff;
    }

    /* Form styling */
    .shop-form-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 5px;
    }
    .shop-form-control {
        border-radius: 8px;
        border: 1.5px solid #e9ecef;
        font-size: 0.86rem;
        padding: 8px 12px;
        transition: border-color 0.2s;
    }
    .shop-form-control:focus {
        border-color: var(--shop-primary);
        box-shadow: 0 0 0 3px rgba(13,110,253,0.1);
    }

    /* Image preview */
    .img-preview-box {
        width: 80px; height: 80px;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
        overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        background: #f8f9fa;
        cursor: pointer;
    }
    .img-preview-box img { width: 100%; height: 100%; object-fit: cover; }

    /* Scroll to top */
    #shopScrollTop {
        position: fixed; bottom: 24px; right: 24px;
        width: 40px; height: 40px;
        background: var(--shop-primary);
        color: #fff; border: none;
        border-radius: 50%;
        display: none; align-items: center; justify-content: center;
        font-size: 1.1rem; cursor: pointer;
        box-shadow: 0 4px 15px rgba(13,110,253,0.4);
        z-index: 1000;
        transition: all 0.2s;
    }
    #shopScrollTop:hover { transform: translateY(-2px); }
</style>
