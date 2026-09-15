<style>
    .bifurcation-col {
        max-height: 480px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        transition: box-shadow .2s ease;
    }

    .bifurcation-col:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, .1);
    }

    .bifurcation-header {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .bifurcation-body {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    .bifurcation-body::-webkit-scrollbar {
        width: 6px;
    }

    .bifurcation-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .bifurcation-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .bifurcation-item {
        transition: background-color .15s ease;
    }

    .bifurcation-item:hover {
        background-color: #f8fafc;
    }

    .bifurcation-empty {
        min-height: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
</style>

<div class="row g-4">
    <div class="col-md-4">
        <div class="bifurcation-col border overflow-hidden bg-white d-flex flex-column">
            <div
                class="bifurcation-header bg-primary text-white p-3 flex-shrink-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-bullhorn me-2"></i>Leads</h6>
                <span class="badge bg-white text-primary rounded-pill">{{ $leads->count() }}</span>
            </div>
            <div class="bifurcation-body p-0 flex-grow-1" style="overflow-y: auto;">
                @if($leads->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($leads as $lead)
                            <li class="list-group-item bifurcation-item px-3 py-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-primary">{{ $lead->enquiry_no }}</strong>
                                    <small class="text-muted"><i
                                            class="fas fa-calendar-alt me-1"></i>{{ date('d M Y', strtotime($lead->enquiry_date)) }}</small>
                                </div>
                                @if($lead->company_name)
                                    <div class="mb-2 text-dark small fw-bold text-truncate">
                                        <i class="fas fa-building text-muted me-1"></i>{{ $lead->company_name }}
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Qty: <b class="text-dark">{{ $lead->quantity }}</b></span>
                                    <span class="text-muted">Price: <b
                                            class="text-dark">₹{{ number_format($lead->price, 2) }}</b></span>
                                </div>
                                <div class="text-end small mt-1 pt-1 border-top">
                                    Total: <b class="text-success">₹{{ number_format($lead->quantity * $lead->price, 2) }}</b>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="bifurcation-empty text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                        <span class="small">No leads found</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="bifurcation-col border overflow-hidden bg-white d-flex flex-column">
            <div
                class="bifurcation-header bg-info text-white p-3 flex-shrink-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-file-invoice me-2"></i>Quotations</h6>
                <span class="badge bg-white text-info rounded-pill">{{ $quots->count() }}</span>
            </div>
            <div class="bifurcation-body p-0 flex-grow-1" style="overflow-y: auto;">
                @if($quots->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($quots as $quot)
                            <li class="list-group-item bifurcation-item px-3 py-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-info">{{ $quot->quotation_no }}</strong>
                                    <small class="text-muted"><i
                                            class="fas fa-calendar-alt me-1"></i>{{ date('d M Y', strtotime($quot->quotation_date)) }}</small>
                                </div>
                                @if($quot->company_name)
                                    <div class="mb-1 text-dark small fw-bold text-truncate">
                                        <i class="fas fa-building text-muted me-1"></i>{{ $quot->company_name }}
                                    </div>
                                @endif
                                @if($quot->enquiry_no)
                                    <div class="mb-2 small">
                                        <span class="badge bg-primary-transparent text-primary">Lead: {{ $quot->enquiry_no }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Qty: <b class="text-dark">{{ $quot->qty }}</b></span>
                                    <span class="text-muted">Price: <b
                                            class="text-dark">₹{{ number_format($quot->unit_price, 2) }}</b></span>
                                </div>
                                @if($quot->discount_price > 0)
                                    <div class="text-danger small"><i class="fas fa-tag me-1"></i>Disc/Unit:
                                        ₹{{ number_format($quot->discount_price, 2) }}</div>
                                @endif
                                <div class="text-end small mt-1 pt-1 border-top">
                                    Total: <b class="text-success">₹{{ number_format($quot->total, 2) }}</b>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="bifurcation-empty text-muted">
                        <i class="fas fa-file-invoice fa-2x mb-2 opacity-50"></i>
                        <span class="small">No quotations found</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="bifurcation-col border overflow-hidden bg-white d-flex flex-column">
            <div
                class="bifurcation-header bg-success text-white p-3 flex-shrink-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-file-invoice-dollar me-2"></i>Proforma Invoices</h6>
                <span class="badge bg-white text-success rounded-pill">{{ $pis->count() }}</span>
            </div>
            <div class="bifurcation-body p-0 flex-grow-1" style="overflow-y: auto;">
                @if($pis->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($pis as $pi)
                            <li class="list-group-item bifurcation-item px-3 py-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-success">{{ $pi->invoice_no }}</strong>
                                    <small class="text-muted"><i
                                            class="fas fa-calendar-alt me-1"></i>{{ date('d M Y', strtotime($pi->invoice_date)) }}</small>
                                </div>
                                @if($pi->company_name)
                                    <div class="mb-1 text-dark small fw-bold text-truncate">
                                        <i class="fas fa-building text-muted me-1"></i>{{ $pi->company_name }}
                                    </div>
                                @endif
                                <div class="mb-2 small">
                                    @if($pi->q_no)
                                        <span class="badge bg-info-transparent text-info me-1">Quot: {{ $pi->q_no }}</span>
                                    @endif
                                    @if($pi->enquiry_no)
                                        <span class="badge bg-primary-transparent text-primary">Lead: {{ $pi->enquiry_no }}</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Qty: <b class="text-dark">{{ $pi->qty }}</b></span>
                                    <span class="text-muted">Price: <b
                                            class="text-dark">₹{{ number_format($pi->unit_price, 2) }}</b></span>
                                </div>
                                @if($pi->discount_price > 0)
                                    <div class="text-danger small"><i class="fas fa-tag me-1"></i>Disc/Unit:
                                        ₹{{ number_format($pi->discount_price, 2) }}</div>
                                @endif
                                <div class="text-end small mt-1 pt-1 border-top">
                                    Total: <b class="text-success">₹{{ number_format($pi->total, 2) }}</b>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="bifurcation-empty text-muted">
                        <i class="fas fa-file-invoice-dollar fa-2x mb-2 opacity-50"></i>
                        <span class="small">No invoices found</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
