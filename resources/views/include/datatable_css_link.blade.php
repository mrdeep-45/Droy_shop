<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">

<style>
    table.dataTable thead th,
table.table-bordered thead th,
.table thead th {
    background-color: #064e5a !important; /* Global matching sidebar dark teal */
    color: #ffffff !important;            /* Crisp white text for all headers */
    font-weight: 600 !important;
    text-transform: uppercase !important;
    font-size: 12px !important;
    letter-spacing: 0.5px;
    vertical-align: middle;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
}

/* Global fix to force standard up/down sorting arrows to render clearly in white */
table.dataTable thead .sorting:before,
table.dataTable thead .sorting:after,
table.dataTable thead .sorting_asc:before,
table.dataTable thead .sorting_asc:after,
table.dataTable thead .sorting_desc:before,
table.dataTable thead .sorting_desc:after {
    color: #ffffff !important;
    opacity: 0.8 !important;
}

.card-header .nav-tabs {
    border-bottom: none !important;
}

.card-header .nav-tabs .nav-item {
    margin-left: 5px;
}

/* Inactive/Unselected Tab Style */
.card-header .nav-tabs .nav-link {
    background-color: transparent !important;
    color: #064e5a !important; /* Sidebar teal font color */
    border: 1px solid #064e5a !important;
    border-radius: 4px !important;
    padding: 5px 12px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    transition: all 0.2s ease-in-out;
}

/* Active/Selected Tab Style */
.card-header .nav-tabs .nav-link.active {
    background-color: #064e5a !important; /* Sidebar teal background fill */
    color: #ffffff !important;             /* White text */
    border-color: #064e5a !important;
    box-shadow: 0 2px 4px rgba(6, 78, 90, 0.2);
}

/* Hover Action Style */
.card-header .nav-tabs .nav-link:hover:not(.active) {
    background-color: rgba(6, 78, 90, 0.08) !important;
    color: #064e5a !important;
}
/* Datatable Mobile Top Controls */
@media (max-width: 767px) {
    .dataTables_wrapper .row:first-child {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    .dataTables_wrapper .row:first-child > div {
        width: auto !important;
        padding: 0 5px !important;
        flex: 0 0 auto !important;
    }
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        text-align: left !important;
        margin-top: 0 !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        width: 130px !important;
        margin-left: 5px !important;
        display: inline-block !important;
    }
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        margin-bottom: 0 !important;
        display: flex !important;
        align-items: center !important;
        white-space: nowrap !important;
    }
}
</style>
