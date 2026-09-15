/**
 * Lead Timeline Module
 * A reusable module to display a lead's timeline in a modal.
 */
var LeadTimeline = (function($) {
    let config = {
        route: window.routes && window.routes.leadTimeline ? window.routes.leadTimeline : '/lead-timeline/get', // Fallback to direct route if variable is not set
        defaultParams: {}, // Any default parameters you want to always send
        pollInterval: 3000 // Poll every 3 seconds
    };

    const $modal = $('#leadTimelineModal');
    const $loader = $('#lead-timeline-loader');
    const $content = $('#lead-timeline-content');
    
    let pollTimer = null;
    let currentParams = null;

    /**
     * Shows a toast notification for errors
     */
    function showError(message) {
        if (typeof iziToast !== 'undefined') {
            iziToast.error({
                title: 'Error',
                message: message,
                position: 'topRight'
            });
        } else {
            alert(message);
        }
    }

    /**
     * Initializes the modal content
     */
    function resetModal() {
        $content.addClass('d-none').html('');
        $loader.removeClass('d-none');
    }

    /**
     * Fetch timeline logs via AJAX
     */
    function fetchTimeline(silent = false) {
        if (!currentParams) return;
        const requestData = $.extend({}, config.defaultParams, currentParams);

        if (!silent) {
            resetModal();
        }

        return $.ajax({
            url: config.route,
            type: 'GET',
            data: requestData,
            success: function(response) {
                // Capture currently open collapse IDs to prevent them from closing on reload
                const openCollapseIds = [];
                $content.find('.collapse.show').each(function() {
                    const id = $(this).attr('id');
                    if (id) openCollapseIds.push(id);
                });

                $loader.addClass('d-none');
                $content.html(response).removeClass('d-none');

                // Restore open collapse details
                openCollapseIds.forEach(function(id) {
                    $content.find('#' + id).addClass('show');
                });
            },
            error: function(xhr) {
                if (!silent) {
                    $loader.addClass('d-none');
                    $content.html('<div class="alert alert-danger">Failed to load timeline. Please try again.</div>').removeClass('d-none');
                    console.error('LeadTimeline Error:', xhr.responseText);
                    showError('Failed to load timeline data.');
                }
            }
        });
    }

    /**
     * Opens the timeline modal and fetches data
     * @param {Object} params - Parameters to send to the server (e.g. { leadId: 1 })
     */
    function open(params) {
        if (!params || (!params.leadId && !params.dealId)) {
            console.error('LeadTimeline: leadId or dealId is required to open the timeline.');
            showError('Invalid ID.');
            return;
        }

        currentParams = params;

        // Show modal and fetch initially
        const modalInstance = bootstrap.Modal.getOrCreateInstance($modal[0]);
        modalInstance.show();

        fetchTimeline(false);

        // Start polling
        stopPolling();
        pollTimer = setInterval(function() {
            fetchTimeline(true);
        }, config.pollInterval);
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    // Bind modal close event to stop polling
    $modal.on('hidden.bs.modal', function() {
        stopPolling();
        currentParams = null;
    });

    // Bind click events for downloading
    $(document).on('click', '#downloadTimelineExcel', function() {
        if (!currentParams) return;
        const queryParams = $.param(currentParams);
        window.open('/lead-timeline/download?format=excel&' + queryParams, '_blank');
    });

    $(document).on('click', '#downloadTimelinePdf', function() {
        if (!currentParams) return;
        const queryParams = $.param(currentParams);
        window.open('/lead-timeline/download?format=pdf&' + queryParams, '_blank');
    });

    // Expose public API
    return {
        open: open,
        // Allow overriding config
        setConfig: function(newConfig) {
            $.extend(config, newConfig);
        },
        refresh: function() {
            fetchTimeline(true);
        }
    };
})(jQuery);
