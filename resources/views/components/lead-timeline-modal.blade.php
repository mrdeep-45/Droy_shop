<div class="modal fade" id="leadTimelineModal" tabindex="-1" aria-labelledby="leadTimelineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="leadTimelineModalLabel">Timeline History</h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary  d-flex align-items-center gap-1" type="button" id="timelineDownloadBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download"></i> Download
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="timelineDownloadBtn">
                            <li><a class="dropdown-item" href="javascript:void(0)" id="downloadTimelineExcel"><i class="far fa-file-excel text-success me-2"></i> Excel</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" id="downloadTimelinePdf"><i class="far fa-file-pdf text-danger me-2"></i> PDF</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body position-relative">
                <!-- Loading Spinner -->
                <div id="lead-timeline-loader" class="d-flex justify-content-center align-items-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                <!-- Dynamic Content Container -->
                <div id="lead-timeline-content" class="d-none">
                    <!-- Timeline entries will be injected here via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
