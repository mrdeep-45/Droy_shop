<style>
    #quotHistoryBody { background: #f4f6fa; }

    .quot-timeline {
        position: relative;
        padding: 22px 26px 22px 44px;
    }
    .quot-timeline::before {
        content: '';
        position: absolute;
        left: 24px; top: 26px; bottom: 26px;
        width: 2px;
        background: linear-gradient(#dfe3ec, #eef0f5);
    }

    .quot-rev { position: relative; margin-bottom: 18px; }
    .quot-rev:last-child { margin-bottom: 0; }

    .quot-rev-dot {
        position: absolute;
        left: -28px; top: 18px;
        width: 14px; height: 14px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #adb5bd;
        box-shadow: 0 0 0 3px #f4f6fa;
        z-index: 2;
    }
    .quot-rev.is-original .quot-rev-dot { border-color: #198754; }
    .quot-rev.is-revision .quot-rev-dot { border-color: #ffc107; }

    .quot-rev-card {
        background: #fff;
        border: 1px solid #e9ecf1;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(16,24,40,.04);
        transition: box-shadow .2s;
    }
    .quot-rev-card:hover { box-shadow: 0 6px 18px rgba(16,24,40,.08); }
    .quot-rev.is-original .quot-rev-card { border-top: 3px solid #198754; }
    .quot-rev.is-revision .quot-rev-card { border-top: 3px solid #ffc107; }

    .quot-rev-head {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; padding: 12px 16px; cursor: pointer; user-select: none;
    }
    .quot-rev-head:hover { background: #fbfcfe; }

    .quot-rev-id { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .quot-rev-no {
        font-family: ui-monospace, Menlo, Consolas, monospace;
        font-weight: 700; color: #1f2a44; font-size: 14px;
    }
    .quot-rev-date { color: #8a93a6; font-size: 12px; }

    .quot-rev-right { display: flex; align-items: center; gap: 14px; }
    .quot-rev-total { text-align: right; line-height: 1.1; }
    .quot-rev-total .lbl {
        display: block; font-size: 9.5px; text-transform: uppercase;
        letter-spacing: .5px; color: #9aa2b1;
    }
    .quot-rev-total .val { font-size: 16px; font-weight: 800; color: #0d6efd; }

    .quot-chip-pdf {
        width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
        border-radius: 8px; border: 1px solid #f1c0c0; color: #dc3545; background: #fff;
        text-decoration: none; transition: .15s;
    }
    .quot-chip-pdf:hover { background: #dc3545; color: #fff; }

    .quot-caret { color: #aeb6c4; transition: transform .2s; font-size: 13px; }
    .quot-rev-head[aria-expanded="true"] .quot-caret { transform: rotate(180deg); }

    .quot-rev-meta { display: flex; gap: 8px; flex-wrap: wrap; padding: 0 16px 12px 16px; }
    .quot-chip {
        font-size: 11.5px; color: #5b6472; background: #f2f4f8;
        border: 1px solid #e9ecf1; padding: 3px 9px; border-radius: 20px;
    }
    .quot-chip b { color: #1f2a44; }

    .quot-items-wrap { padding: 0 16px 16px 16px; }
    .history-table {
        font-size: 12.5px; border: 1px solid #eceef3;
        border-radius: 10px; overflow: hidden;
    }
    .history-table thead th {
        background: #1f2a44 !important; color: #cdd4e0 !important;
        font-weight: 600; text-transform: uppercase; font-size: 10px !important;
        letter-spacing: .4px; padding: 8px 10px;
    }
    .history-table tbody td { padding: 8px 10px; vertical-align: middle; }
    .history-table tbody tr:nth-child(even) { background: #fafbfd; }
    .history-table tbody tr:hover { background: #f3f7ff; }

    @media (max-width: 576px) {
        #quotationHistoryModal .modal-dialog {
            margin: 8px !important;
        }
        #quotationHistoryModal .modal-header {
            padding: 12px !important;
        }
        #quotationHistoryModal .modal-title {
            font-size: 14px !important;
            flex-wrap: wrap !important;
            line-height: 1.4 !important;
        }
        #quotationHistoryModal .modal-header small {
            font-size: 10px !important;
        }
        .quot-timeline {
            padding: 15px 10px 15px 30px !important;
        }
        .quot-timeline::before {
            left: 16px !important;
            top: 20px !important;
            bottom: 20px !important;
        }
        .quot-rev-dot {
            left: -20px !important;
            top: 15px !important;
            width: 10px !important;
            height: 10px !important;
            border-width: 2px !important;
        }
        .quot-rev-head {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
            padding: 10px 12px !important;
        }
        .quot-rev-id {
            width: 100% !important;
            justify-content: space-between !important;
            display: flex !important;
        }
        .quot-rev-right {
            width: 100% !important;
            justify-content: space-between !important;
            border-top: 1px solid #f1f3f7 !important;
            padding-top: 8px !important;
            display: flex !important;
        }
        .quot-rev-meta {
            padding: 0 12px 10px 12px !important;
            gap: 6px !important;
        }
        .quot-chip {
            font-size: 10px !important;
            padding: 2px 7px !important;
        }
        .quot-items-wrap {
            padding: 0 10px 12px 10px !important;
        }
        .history-table {
            font-size: 11px !important;
            min-width: 480px !important;
        }
        .history-table th,
        .history-table td {
            white-space: nowrap !important;
            padding: 6px 8px !important;
        }
        .history-table thead th {
            font-size: 9px !important;
        }
    }
</style>

<div class="modal fade" id="quotationHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-primary text-white py-3">
                <div>
                    <h5 class="modal-title mb-0 text-white d-flex align-items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        Quotation History -<span id="customer-name"> </span> -<span id="quot-no"> </span> 
                    </h5>
                    <small class="opacity-75 mt-4">All previous revisions of this quotation</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0 bg-light" id="quotHistoryBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>

            <div class="modal-footer bg-white border-top-0 py-2">
                <button class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script>
$(document).on('click', '.quot-hist', function () {
    var dealId = $(this).data('id');
    var currentQuotId = $(this).data('quot-id');
    var customerName = $(this).data('customer-name');
    var quotNo = $(this).data('quot-no');

    $('#customer-name').text(customerName);
    $('#quot-no').text(quotNo);

    function renderSpinner() {
        $('#quotHistoryBody').html(`
            <div class="text-center py-5 bg-white">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        `);
    }

    function renderEmptyState() {
        $('#quotHistoryBody').html(`
            <div class="text-center py-5 text-muted bg-white">
                <i class="fa-solid fa-inbox fa-3x mb-3 text-opacity-25 text-dark d-block"></i>
                <span class="fw-medium">No revision history found.</span>
            </div>
        `);
    }

    function renderErrorState() {
        $('#quotHistoryBody').html(`
            <div class="text-center py-5 text-danger bg-white">
                <i class="fa-solid fa-triangle-exclamation fa-2x mb-2 d-block"></i>
                Failed to load history. Please try again.
            </div>
        `);
    }

    function createRevisionBadge(revisionNo) {
        return revisionNo > 0
            ? `<span class="badge bg-warning text-dark px-2 py-1 small fw-bold">Rev. ${revisionNo}</span>`
            : `<span class="badge bg-success px-2 py-1 small fw-bold">Original</span>`;
    }

    function buildItemRows(items) {
        var rows = '';
        $.each(items, function (j, item) {
            rows += `
                <tr style="font-size:12.5px; vertical-align: middle;">
                    <td class="text-center text-muted fw-medium">${j + 1}</td>
                    <td class="fw-semibold text-dark">${item.item_name}</td>
                    <td class="text-center fw-medium">${item.qty}</td>
                    <td class="text-end fw-medium">${item.price}</td>
                    <td class="text-end text-muted">${item.gst_percent}% (${item.gst_amount})</td>
                    <td class="text-end fw-bold text-success">${item.line_total}</td>
                </tr>`;
        });
        return rows;
    }

    var pdfBase = "{{ route('quotation.pdf', ':id') }}";

    function buildAccordionItem(i, q, isFirst) {
        var collapseId = 'collapseRevision_' + i;
        var revBadge   = createRevisionBadge(q.revision_no);
        var itemRows   = buildItemRows(q.items);
        var pdfUrl     = pdfBase.replace(':id', encodeURIComponent(q.enc_id));
        var stateClass = q.revision_no > 0 ? 'is-revision' : 'is-original';
        var itemCount  = (q.items && q.items.length) ? q.items.length : 0;

        return `
        <div class="quot-rev ${stateClass}">
            <span class="quot-rev-dot"></span>
            <div class="quot-rev-card">
                <div class="quot-rev-head" role="button" data-bs-toggle="collapse"
                    data-bs-target="#${collapseId}" aria-expanded="${isFirst}" aria-controls="${collapseId}">
                    <div class="quot-rev-id">
                        ${revBadge}
                        <span class="quot-rev-no d-none">${q.quotation_no}</span>
                        <span class="quot-rev-date"><i class="fa-regular fa-clock me-1"></i>${q.date}</span>
                    </div>
                    <div class="quot-rev-right">
                        <div class="quot-rev-total">
                            <span class="lbl">Grand Total</span>
                            <span class="val">${q.grand_total}</span>
                        </div>
                        <a href="${pdfUrl}" target="_blank" class="quot-chip-pdf" title="View PDF"
                        onclick="event.stopPropagation();">
                            <i class="fa-solid fa-file-pdf"></i>
                        </a>
                        <i class="fa-solid fa-chevron-down quot-caret"></i>
                    </div>
                </div>

                <div class="quot-rev-meta">
                    <span class="quot-chip">Subtotal <b>${q.subtotal}</b></span>
                    <span class="quot-chip">GST <b>${q.gst_total}</b></span>
                    <span class="quot-chip">${itemCount} item${itemCount === 1 ? '' : 's'}</span>
                </div>

                <div id="${collapseId}" class="collapse ${isFirst ? 'show' : ''}"
                    data-bs-parent="#quotationHistoryAccordion">
                    <div class="quot-items-wrap">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 history-table">
                                <thead>
                                    <tr>
                                        <th style="width:4%" class="text-center">#</th>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">GST</th>
                                        <th class="text-end pe-3">Total</th>
                                    </tr>
                                </thead>
                                <tbody>${itemRows}</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    renderSpinner();
    $('#quotationHistoryModal').modal('show');

    $.get("{{ route('quotation.history') }}", { deal_id: dealId, quot_id: currentQuotId }, function (res) {
        if (!res.status || !res.data.length) {
            renderEmptyState();
            return;
        }

        var html = '<div class="quot-timeline" id="quotationHistoryAccordion">';
        $.each(res.data, function (i, q) {
            html += buildAccordionItem(i, q, i === 0);
        });
        html += '</div>';

        $('#quotHistoryBody').html(html);
    }).fail(function () {
        renderErrorState();
    });
});
</script>
