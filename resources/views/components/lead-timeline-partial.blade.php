@if(isset($groupedLogs) && count($groupedLogs) > 0)
    @php
        $termsMap = \Illuminate\Support\Facades\DB::table('tbl_terms')->pluck('module', 'term_id')->toArray();
        $resolveTerms = function($value) use ($termsMap) {
            if (empty($value)) return '-';
            $ids = array_filter(explode(',', $value));
            $allNumeric = true;
            foreach ($ids as $termId) {
                if (!is_numeric($termId)) {
                    $allNumeric = false;
                    break;
                }
            }
            if ($allNumeric && !empty($ids)) {
                $names = [];
                foreach ($ids as $id) {
                    if (isset($termsMap[$id])) {
                        $names[] = $termsMap[$id];
                    } else {
                        $names[] = 'Term #' . $id;
                    }
                }
                return implode(', ', $names);
            }
            return $value;
        };
    @endphp
    <div class="position-relative" id="timelineContainer">
        <div class="timeline-line"></div>
        @foreach($groupedLogs as $dateStr => $logs)
            <div class="timeline-group">
                <div class="timeline-date-header">
                    <span class="timeline-date-badge">{{ $dateStr }}</span>
                </div>
                <div class="timeline-items-container">
                    @foreach($logs as $item)
                        <div class="timeline-item-row d-flex align-items-center justify-content-between">
                            <div class="timeline-marker-circle {{ $item['color'] }}">
                                {!! $item['icon'] !!}
                            </div>
                            <div class="d-flex align-items-center flex-grow-1">
                                <div class="timeline-item-time text-muted me-3">
                                    <i class="far fa-clock me-1"></i> {{ $item['time'] }}
                                </div>
                                <div class="timeline-item-description">
                                    <strong>{{ $item['title'] }}</strong> - 
                                    @if(!empty($item['file_url']))
                                        @if(preg_match('/(https?:\/\/[^\s\']+)/', $item['description']))
                                            @php
                                                $parts = explode($item['file_url'], $item['description'], 2);
                                                $prefix = $parts[0] ?? 'Added link attachment: ';
                                            @endphp
                                            {!! e($prefix) !!}<a href="{{ $item['file_url'] }}" target="_blank" class="text-primary fw-bold text-decoration-underline" title="Click to open link"><i class="fas fa-external-link-alt me-1"></i>{{ $item['file_url'] }}</a>
                                        @else
                                            <a href="{{ $item['file_url'] }}" target="_blank" class="text-primary fw-bold text-decoration-underline" title="Click to view/download file"><i class="fas fa-external-link-alt me-1"></i>{{ $item['description'] }}</a>
                                        @endif
                                    @else
                                        {{ $item['description'] }}
                                    @endif
                                </div>
                                
                                @if(!empty($item['is_quotation_log']) || !empty($item['is_performa_log']) || !empty($item['is_main_invoice_log']))
                                    @php
                                        $collapseId = 'collapseLog_' . $item['log_id'];
                                        $pdfUrl = null;
                                        if (!empty($item['is_quotation_log'])) {
                                            $pdfUrl = route('quotation.pdf', $item['enc_quotation_id']);
                                        } elseif (!empty($item['is_performa_log'])) {
                                            $pdfUrl = route('performa_invoice.pdf', $item['enc_performa_id']);
                                        } elseif (!empty($item['is_main_invoice_log'])) {
                                            $pdfUrl = route('main_invoice.pdf', $item['enc_main_id']);
                                        }
                                        $p = $item['parsed_payload'];
                                    @endphp
                                    <div class="mt-2 w-100">
                                        <div class="d-flex gap-2 flex-wrap align-items-center">
                                            @if($p)
                                                <button class="btn btn-xs btn-outline-secondary py-0 px-2 toggle-details-btn" style="font-size: 11px;" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" 
                                                        aria-controls="{{ $collapseId }}">
                                                    <i class="fas fa-info-circle me-1"></i> Details
                                                </button>
                                            @endif
                                            @if($pdfUrl)
                                                <a href="{{ $pdfUrl }}" target="_blank" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size: 11px;">
                                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                                </a>
                                            @endif
                                            @if($p && !empty($p['attachments']))
                                                @php
                                                    $attList = is_array($p['attachments']) ? $p['attachments'] : array_filter(explode(',', $p['attachments']));
                                                @endphp
                                                @foreach($attList as $attFile)
                                                    @php
                                                        $attTrim = is_array($attFile) ? ($attFile['file_name'] ?? '') : trim($attFile);
                                                        $attUrl = is_array($attFile) ? ($attFile['file_url'] ?? '#') : asset('assets/uploads/quotations/attachments/' . $attTrim);
                                                    @endphp
                                                    @if(!empty($attTrim))
                                                        <a href="{{ $attUrl }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 d-inline-flex align-items-center" style="font-size: 11px;" title="Click to open attachment: {{ $attTrim }}">
                                                            <i class="fas fa-paperclip me-1"></i> {{ strlen($attTrim) > 20 ? substr($attTrim, 0, 20) . '...' : $attTrim }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="collapse mt-2" id="{{ $collapseId }}">
                                            @if($p && (isset($p['quotation_no']) || isset($p['invoice_no'])))
                                                <div class="mt-2 border border-light-subtle rounded-3 p-3 bg-white shadow-sm" style="font-size: 12px;">
                                                    <div class="row gy-3 gx-2">
                                                        <div class="col-sm-6">
                                                            <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Document No</span>
                                                            <span class="fw-bold text-dark" style="font-size: 13px;">{{ $p['quotation_no'] ?? $p['invoice_no'] ?? 'N/A' }}</span>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Grand Total</span>
                                                            <span class="fw-bold text-primary" style="font-size: 13px;">{{ $p['currency'] ?? '₹' }} {{ number_format(floatval($p['grand_total'] ?? 0), 2) }}</span>
                                                        </div>
                                                        @if(!empty($p['customer_name']))
                                                            <div class="col-sm-6">
                                                                <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Customer Name</span>
                                                                <span class="fw-medium text-dark">{{ $p['customer_name'] }}</span>
                                                            </div>
                                                        @endif
                                                        @if(!empty($p['email']))
                                                            <div class="col-sm-6">
                                                                <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Email</span>
                                                                <span class="text-dark">{{ $p['email'] }}</span>
                                                            </div>
                                                        @endif
                                                        @if(!empty($p['quotation_type']) || !empty($p['invoice_type']))
                                                            <div class="col-sm-6">
                                                                <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Type</span>
                                                                <span class="badge bg-light text-dark border text-capitalize">{{ $p['quotation_type'] ?? $p['invoice_type'] ?? 'N/A' }}</span>
                                                            </div>
                                                        @endif
                                                        @if(!empty($p['quotation_date']) || !empty($p['invoice_date']))
                                                            <div class="col-sm-6">
                                                                <span class="text-muted d-block text-uppercase fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">Date</span>
                                                                <span class="text-dark">{{ $p['quotation_date'] ?? $p['invoice_date'] }}</span>
                                                            </div>
                                                        @endif
                                                        @if(!empty($p['attachments']))
                                                            @php
                                                                $attArray = is_array($p['attachments']) ? $p['attachments'] : array_filter(explode(',', $p['attachments']));
                                                            @endphp
                                                            @if(count($attArray) > 0)
                                                                <div class="col-12 mt-2 pt-2 border-top">
                                                                    <span class="text-muted d-block text-uppercase fw-semibold mb-1" style="font-size: 10px; letter-spacing: 0.5px;"><i class="fas fa-paperclip me-1 text-primary"></i> Quotation Attachments</span>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        @foreach($attArray as $attFile)
                                                                            @php
                                                                                $attTrim = is_array($attFile) ? ($attFile['file_name'] ?? '') : trim($attFile);
                                                                                $attUrl = is_array($attFile) ? ($attFile['file_url'] ?? '#') : asset('assets/uploads/quotations/attachments/' . $attTrim);
                                                                            @endphp
                                                                            @if(!empty($attTrim))
                                                                                <a href="{{ $attUrl }}" target="_blank" class="btn btn-xs btn-outline-primary py-1 px-2 d-inline-flex align-items-center" style="font-size: 11px; border-radius: 4px;" title="Click to open attachment">
                                                                                    <i class="fas fa-external-link-alt me-1"></i> {{ strlen($attTrim) > 30 ? substr($attTrim, 0, 30) . '...' : $attTrim }}
                                                                                </a>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($p && (isset($p['items_created']) || isset($p['items_updated']) || isset($p['items_deleted'])))
                                                <div class="mt-2 border border-light-subtle rounded-3 p-3 bg-white shadow-sm" style="font-size: 12px; max-height: 250px; overflow-y: auto;">
                                                    @if(isset($p['items_created']) && count($p['items_created']) > 0)
                                                        <div class="mb-3"><span class="badge bg-success mb-2"><i class="fas fa-plus me-1"></i> Added Items ({{ count($p['items_created']) }})</span>
                                                            @foreach($p['items_created'] as $i)
                                                                <div class="d-flex justify-content-between align-items-center small text-muted border-bottom pb-2 mb-2">
                                                                    <div class="d-flex flex-column">
                                                                        <span class="fw-semibold text-dark">Product ID: {{ $i['product_id'] }}</span>
                                                                        <span style="font-size: 11px;">Qty: {{ $i['qty'] }} @ {{ $i['unit_price'] }}</span>
                                                                    </div>
                                                                    <span class="fw-bold text-success">₹{{ number_format(floatval($i['total'] ?? 0), 2) }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if(isset($p['items_updated']) && count($p['items_updated']) > 0)
                                                        <div class="mb-3"><span class="badge bg-primary mb-2"><i class="fas fa-pen me-1"></i> Updated Items ({{ count($p['items_updated']) }})</span>
                                                            @foreach($p['items_updated'] as $i)
                                                                <div class="d-flex justify-content-between align-items-center small text-muted border-bottom pb-2 mb-2">
                                                                    <div class="d-flex flex-column">
                                                                        <span class="fw-semibold text-dark">Product ID: {{ $i['product_id'] }}</span>
                                                                        <span style="font-size: 11px;">Qty: {{ $i['qty'] }} @ {{ $i['unit_price'] }}</span>
                                                                    </div>
                                                                    <span class="fw-bold text-primary">₹{{ number_format(floatval($i['total'] ?? 0), 2) }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if(isset($p['items_deleted']) && count($p['items_deleted']) > 0)
                                                        <div class="mb-3"><span class="badge bg-danger mb-2"><i class="fas fa-trash me-1"></i> Removed Items ({{ count($p['items_deleted']) }})</span>
                                                            @foreach($p['items_deleted'] as $i)
                                                                <div class="small text-muted border-bottom pb-2 mb-2"><i class="fas fa-minus-circle text-danger me-1"></i> Item ID: {{ $i['item_id'] }} removed</div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if(empty($p['items_created']) && empty($p['items_updated']) && empty($p['items_deleted']))
                                                        <span class="text-muted"><i class="fas fa-info-circle me-1"></i> No items changed</span>
                                                    @endif
                                                </div>
                                            @elseif($p && (isset($p['before']) || isset($p['after'])))
                                                <div class="mt-2 border border-light-subtle rounded-3 p-3 bg-white shadow-sm" style="font-size: 12px; max-height: 250px; overflow-y: auto;">
                                                    <div class="row g-3">
                                                        @if(isset($p['before']) && is_array($p['before']) && count($p['before']) > 0)
                                                            <div class="col-sm-6">
                                                                <span class="badge bg-secondary mb-2">Previous Values</span>
                                                                @foreach($p['before'] as $bk => $bv)
                                                                    <div class="small text-muted mb-1">
                                                                        <strong>{{ ucwords(str_replace('_', ' ', $bk)) }}:</strong> 
                                                                        <span>{{ is_array($bv) ? json_encode($bv) : ($bk === 'terms_payment' ? $resolveTerms($bv) : (string)$bv) }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        @if(isset($p['after']) && is_array($p['after']) && count($p['after']) > 0)
                                                            <div class="col-sm-6">
                                                                <span class="badge bg-success mb-2">Updated Values</span>
                                                                @foreach($p['after'] as $ak => $av)
                                                                    <div class="small text-dark mb-1">
                                                                        <strong>{{ ucwords(str_replace('_', ' ', $ak)) }}:</strong> 
                                                                        <span class="fw-bold text-success">{{ is_array($av) ? json_encode($av) : ($ak === 'terms_payment' ? $resolveTerms($av) : (string)$av) }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($p && is_array($p))
                                                <div class="mt-2 border border-light-subtle rounded-3 p-3 bg-white shadow-sm" style="font-size: 12px; max-height: 200px; overflow-y: auto;">
                                                    <div class="row g-2">
                                                        @foreach($p as $k => $v)
                                                            <div class="col-sm-6">
                                                                <span class="text-muted text-uppercase fw-semibold" style="font-size: 10px;">{{ str_replace('_', ' ', $k) }}:</span>
                                                                <span class="fw-medium text-dark">{{ is_array($v) ? json_encode($v) : ($k === 'terms_payment' ? $resolveTerms($v) : (string)$v) }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="timeline-item-by ms-3">
                                <span class="badge rounded-pill bg-light text-dark border">By {{ $item['user'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center text-muted py-5">
        <i class="far fa-clock fa-2x mb-3 text-light"></i><br>
        No timeline records found.
    </div>
@endif

<style>
/* Timeline style */
.timeline-group {
    margin-bottom: 25px;
    position: relative;
}
.timeline-date-header {
    margin-bottom: 15px;
}
.timeline-date-badge {
    background-color: #f6ad55;
    color: #fff;
    padding: 8px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: 50px;
    display: inline-block;
}
.timeline-items-container {
    position: relative;
    padding-left: 30px;
}
.timeline-line {
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}
.timeline-item-row {
    position: relative;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 20px;
    margin-bottom: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.timeline-marker-circle {
    position: absolute;
    left: -27px;
    top: 50%;
    transform: translateY(-50%);
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    z-index: 2;
    border-radius: 50%;
}
.timeline-marker-circle.bg-success {
    background-color: #38a169 !important;
    color: #fff !important;
    box-shadow: 0 0 0 2px #38a169;
}
.timeline-marker-circle.bg-primary {
    background-color: #3182ce !important;
    color: #fff !important;
    box-shadow: 0 0 0 2px #3182ce;
}
.timeline-marker-circle.bg-info {
    background-color: #00b5ad !important;
    color: #fff !important;
    box-shadow: 0 0 0 2px #00b5ad;
}
.timeline-marker-circle.bg-warning {
    background-color: #dd6b20 !important;
    color: #fff !important;
    box-shadow: 0 0 0 2px #dd6b20;
}
.timeline-marker-circle.bg-danger {
    background-color: #e53e3e !important;
    color: #fff !important;
    box-shadow: 0 0 0 2px #e53e3e;
}
.timeline-marker-circle.bg-secondary {
    background-color: #718096 !important;
    color: #fff !important;
    box-shadow: 0 0 0 2px #718096;
}
.timeline-item-time {
    font-size: 0.85rem;
    font-weight: 500;
    color: #4a5568;
    min-width: 85px;
}
.timeline-item-description {
    font-size: 0.9rem;
    color: #2d3748;
}
.timeline-item-by .badge {
    font-size: 0.75rem;
    padding: 5px 12px;
    background-color: #f7fafc;
    border: 1px solid #edf2f7;
    color: #4a5568;
}
</style>
