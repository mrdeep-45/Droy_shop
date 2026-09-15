<style>
    #piHistoryBody { background: #f4f6fa; }

    .pi-timeline {
        position: relative;
        padding: 22px 26px 22px 44px;
    }
    .pi-timeline::before {
        content: '';
        position: absolute;
        left: 24px; top: 26px; bottom: 26px;
        width: 2px;
        background: linear-gradient(#dfe3ec, #eef0f5);
    }

    .pi-rev { position: relative; margin-bottom: 18px; }
    .pi-rev:last-child { margin-bottom: 0; }

    .pi-rev-dot {
        position: absolute;
        left: -28px; top: 18px;
        width: 14px; height: 14px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #adb5bd;
        box-shadow: 0 0 0 3px #f4f6fa;
        z-index: 2;
    }
    .pi-rev.is-original .pi-rev-dot { border-color: #198754; }
    .pi-rev.is-revision .pi-rev-dot { border-color: #ffc107; }

    .pi-rev-card {
        background: #fff;
        border: 1px solid #e9ecf1;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(16,24,40,.04);
        transition: box-shadow .2s;
    }
    .pi-rev-card:hover { box-shadow: 0 6px 18px rgba(16,24,40,.08); }
    .pi-rev.is-original .pi-rev-card { border-top: 3px solid #198754; }
    .pi-rev.is-revision .pi-rev-card { border-top: 3px solid #ffc107; }

    .pi-rev-head {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; padding: 12px 16px; cursor: pointer; user-select: none;
    }
    .pi-rev-head:hover { background: #fbfcfe; }

    .pi-rev-id { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .pi-rev-no {
        font-family: ui-monospace, Menlo, Consolas, monospace;
        font-weight: 700; color: #1f2a44; font-size: 14px;
    }
    .pi-rev-date { color: #8a93a6; font-size: 12px; }

    .pi-rev-right { display: flex; align-items: center; gap: 14px; }
    .pi-rev-total { text-align: right; line-height: 1.1; }
    .pi-rev-total .lbl {
        display: block; font-size: 9.5px; text-transform: uppercase;
        letter-spacing: .5px; color: #9aa2b1;
    }
    .pi-rev-total .val { font-size: 16px; font-weight: 800; color: #0d6efd; }

    .pi-chip-pdf {
        width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
        border-radius: 8px; border: 1px solid #f1c0c0; color: #dc3545; background: #fff;
        text-decoration: none; transition: .15s;
    }
    .pi-chip-pdf:hover { background: #dc3545; color: #fff; }

    .pi-caret { color: #aeb6c4; transition: transform .2s; font-size: 13px; }
    .pi-rev-head[aria-expanded="true"] .pi-caret { transform: rotate(180deg); }

    .pi-rev-meta { display: flex; gap: 8px; flex-wrap: wrap; padding: 0 16px 12px 16px; }
    .pi-chip {
        font-size: 11.5px; color: #5b6472; background: #f2f4f8;
        border: 1px solid #e9ecf1; padding: 3px 9px; border-radius: 20px;
    }
    .pi-chip b { color: #1f2a44; }

    .pi-items-wrap { padding: 0 16px 16px 16px; }
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
        #piHistoryModal .modal-dialog {
            margin: 8px !important;
        }
        .pi-timeline {
            padding: 15px 10px 15px 30px !important;
        }
        .pi-timeline::before {
            left: 16px !important;
            top: 20px !important;
            bottom: 20px !important;
        }
        .pi-rev-dot {
            left: -20px !important;
            top: 15px !important;
            width: 10px !important;
            height: 10px !important;
            border-width: 2px !important;
        }
        .pi-rev-head {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
            padding: 10px 12px !important;
        }
        .pi-rev-id {
            width: 100% !important;
            justify-content: space-between !important;
            display: flex !important;
        }
        .pi-rev-right {
            width: 100% !important;
            justify-content: space-between !important;
            border-top: 1px solid #f1f3f7 !important;
            padding-top: 8px !important;
            display: flex !important;
        }
        .pi-rev-meta {
            padding: 0 12px 10px 12px !important;
            gap: 6px !important;
        }
        .pi-chip {
            font-size: 10px !important;
            padding: 2px 7px !important;
        }
    }
</style>

<div class="modal fade" id="piHistoryModal" tabindex="-1" aria-labelledby="piHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <div>
                    <h5 class="modal-title mb-0 text-white fw-bold d-flex align-items-center" id="piHistoryModalLabel">
                        <i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>
                        Performa Invoice History - <span id="pi-history-invoice-no" class="text-warning ms-1"></span>
                    </h5>
                    <small class="opacity-75">All previous revisions of this performa invoice</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0 bg-light" id="piHistoryBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>

            <div class="modal-footer bg-white py-2">
                <button class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.pi-hist', function () {
    var invoiceId = $(this).data('id');
    var invoiceNo = $(this).data('invoice-no');

    $('#pi-history-invoice-no').text(invoiceNo);

    function renderSpinner() {
        $('#piHistoryBody').html(`
            <div class="text-center py-5 bg-white">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        `);
    }

    function renderEmptyState() {
        $('#piHistoryBody').html(`
            <div class="text-center py-5 text-muted bg-white">
                <i class="fa-solid fa-inbox fa-3x mb-3 text-opacity-25 text-dark d-block"></i>
                <span class="fw-medium">No revision history found.</span>
            </div>
        `);
    }

    function renderErrorState() {
        $('#piHistoryBody').html(`
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

    var pdfBase = "{{ route('performa_invoice.pdf', ':id') }}";

    function buildAccordionItem(i, q, isFirst) {
        var collapseId = 'collapsePiRevision_' + i;
        var revBadge   = createRevisionBadge(q.revision_no);
        var itemRows   = buildItemRows(q.items);
        var pdfUrl     = pdfBase.replace(':id', encodeURIComponent(q.enc_id));
        var stateClass = q.revision_no > 0 ? 'is-revision' : 'is-original';
        var itemCount  = (q.items && q.items.length) ? q.items.length : 0;

        var changesHtml = '';
        if (q.changes && q.changes.length > 0) {
            changesHtml = `
                <div class="mb-3 p-2.5 rounded-3 border" style="background-color: #fff9e6; border-color: #ffe58f !important;">
                    <div class="fw-bold text-dark mb-1 small d-flex align-items-center">
                        <i class="fa-solid fa-pen-to-square text-warning me-1.5"></i> Changes Made in Rev. ${q.revision_no}:
                    </div>
                    <div class="row g-2 ms-1">
                        ${q.changes.map(c => `
                            <div class="col-md-6 col-12 small">
                                <span class="fw-semibold text-dark">${c.field}:</span>
                                <span class="text-muted text-decoration-line-through me-1">${c.old}</span>
                                <i class="fa-solid fa-arrow-right text-warning mx-1" style="font-size:10px;"></i>
                                <span class="fw-bold text-success">${c.new}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>`;
        }

        return `
        <div class="pi-rev ${stateClass}">
            <span class="pi-rev-dot"></span>
            <div class="pi-rev-card">
                <div class="pi-rev-head" role="button" data-bs-toggle="collapse"
                    data-bs-target="#${collapseId}" aria-expanded="${isFirst}" aria-controls="${collapseId}">
                    <div class="pi-rev-id">
                        ${revBadge}
                        <span class="pi-rev-date"><i class="fa-regular fa-clock me-1"></i>${q.date}</span>
                    </div>
                    <div class="pi-rev-right">
                        <div class="pi-rev-total">
                            <span class="lbl">Grand Total</span>
                            <span class="val">${q.grand_total}</span>
                        </div>
                        <a href="${pdfUrl}" target="_blank" class="pi-chip-pdf" title="View PDF"
                        onclick="event.stopPropagation();">
                            <i class="fa-solid fa-file-pdf"></i>
                        </a>
                        <i class="fa-solid fa-chevron-down pi-caret"></i>
                    </div>
                </div>

                <div class="pi-rev-meta">
                    <span class="pi-chip">Subtotal <b>${q.subtotal}</b></span>
                    <span class="pi-chip">GST <b>${q.gst_total}</b></span>
                    <span class="pi-chip">${itemCount} item${itemCount === 1 ? '' : 's'}</span>
                </div>

                <div id="${collapseId}" class="collapse ${isFirst ? 'show' : ''}"
                    data-bs-parent="#piHistoryAccordion">
                    <div class="pi-items-wrap">
                        ${changesHtml}
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
    $('#piHistoryModal').modal('show');

    var historyUrl = "{{ route('performa_invoice.history', ':id') }}".replace(':id', invoiceId);

    $.get(historyUrl, function (res) {
        if (!res.status || !res.data || !res.data.length) {
            renderEmptyState();
            return;
        }

        var html = '<div class="pi-timeline" id="piHistoryAccordion">';
        $.each(res.data, function (i, q) {
            html += buildAccordionItem(i, q, i === 0);
        });
        html += '</div>';

        $('#piHistoryBody').html(html);
    }).fail(function () {
        renderErrorState();
    });
});
</script>
