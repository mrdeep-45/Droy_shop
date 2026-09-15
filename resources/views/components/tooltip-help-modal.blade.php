<!-- Tooltip Edit Modal -->
<div class="modal fade" id="tooltipEditModal" tabindex="-1" aria-labelledby="tooltipEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tooltipEditModalLabel"><i class="fas fa-edit text-primary me-2"></i> Edit Tooltip: <span id="tooltipEditLabelDisplay"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tooltipEditForm" novalidate>
                    @csrf
                    <input type="hidden" id="tooltipEditId" name="tooltip_id">
                    <input type="hidden" id="tooltipEditKey" name="field_key">
                    <input type="hidden" id="tooltipEditLabel" name="field_label">
                    <!-- Defaulting to Global module name if created from here, can be adjusted -->
                    <input type="hidden" id="tooltipEditModule" name="module_name" value="Global">

                    <div class="mb-3">
                        <label for="tooltipEditText" class="form-label">Tooltip Text<span class="text-danger">*</span></label>
                        <textarea class="form-control" name="tooltip_text" id="tooltipEditText" rows="4" required placeholder="Enter the help text..."></textarea>
                        <div class="invalid-feedback tooltipTextError">Please enter Tooltip Text</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveTooltipBtn">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Check if the current user has tooltip edit permissions
    let canEditTooltip = {{ permissions_check('canUpdate', 'tooltips.index') ? 'true' : 'false' }};

    if (canEditTooltip) {
        // Handle click on edit icon
        $(document).on('click', '.edit-tooltip-btn', function(e) {
            e.preventDefault();
            const key = $(this).data('key');
            const label = $(this).data('label');
            
            // Set form basic data
            $('#tooltipEditKey').val(key);
            $('#tooltipEditLabel').val(label);
            $('#tooltipEditLabelDisplay').text(label);
            $('#tooltipEditText').val('');
            $('#tooltipEditId').val('');
            
            // Guess module name from key prefix (e.g. 'lead_remarks' -> 'Lead')
            let moduleGuess = 'Global';
            if(key.startsWith('lead_')) moduleGuess = 'Lead';
            else if(key.startsWith('deal_')) moduleGuess = 'Deal';
            else if(key.startsWith('quotation_')) moduleGuess = 'Quotation';
            else if(key.startsWith('sales_')) moduleGuess = 'Sales Order';
            else if(key.startsWith('purchase_')) moduleGuess = 'Purchase';
            $('#tooltipEditModule').val(moduleGuess);

            // Fetch current tooltip text via API
            $.ajax({
                url: "{{ url('tooltip/get') }}/" + key,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        $('#tooltipEditId').val(response.data.tooltip_id);
                        $('#tooltipEditText').val(response.data.tooltip_text);
                        $('#tooltipEditModule').val(response.data.module_name);
                    }
                },
                error: function(xhr) {
                    // 404 is expected if tooltip doesn't exist yet, we just leave fields blank for creation
                    if(xhr.status !== 404) {
                        console.error("Failed to fetch tooltip details.");
                    }
                },
                complete: function() {
                    $('.is-invalid').removeClass('is-invalid');
                    $('#tooltipEditModal').modal('show');
                }
            });
        });

        // Save Tooltip
        $('#saveTooltipBtn').click(function() {
            let tooltipText = $('#tooltipEditText').val().trim();
            if (!tooltipText) {
                $('#tooltipEditText').addClass('is-invalid');
                return;
            }
            $('#tooltipEditText').removeClass('is-invalid');

            let btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            let formData = {
                tooltip_id: $('#tooltipEditId').val(),
                field_key: $('#tooltipEditKey').val(),
                field_label: $('#tooltipEditLabel').val(),
                module_name: $('#tooltipEditModule').val(),
                tooltip_text: tooltipText,
                _token: $('input[name="_token"]').val()
            };

            $.ajax({
                url: "{{ route('tooltips.store') }}",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(response) {
                    iziToast.success({
                        title: 'Success',
                        message: response.message,
                        position: 'topRight'
                    });
                    
                    $('#tooltipEditModal').modal('hide');
                    
                    // Update UI immediately
                    let key = formData.field_key;
                    let infoIcon = $('#tooltip-icon-' + key);
                    
                    if(infoIcon.length > 0) {
                        // Tooltip icon already exists, update title and recreate tooltip
                        infoIcon.attr('title', tooltipText).attr('data-bs-original-title', tooltipText);
                        
                        // Re-initialize bootstrap tooltip if possible
                        try {
                            var tooltipInstance = bootstrap.Tooltip.getInstance(infoIcon[0]);
                            if(tooltipInstance) tooltipInstance.dispose();
                            new bootstrap.Tooltip(infoIcon[0]);
                        } catch (e) { console.error("Error re-initializing tooltip", e); }
                    } else {
                        // Tooltip icon didn't exist before (created just now)
                        // Reload the page to reflect new HTML or manually inject it
                        // (Usually a page reload is safest for injecting new HTML, but let's try to add it)
                        let editBtn = $('.edit-tooltip-btn[data-key="'+key+'"]');
                        if (editBtn.length > 0) {
                            $('<i class="fas fa-info-circle text-primary me-1" id="tooltip-icon-'+key+'" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="'+tooltipText.replace(/"/g, '&quot;')+'"></i>').insertBefore(editBtn);
                            
                            // Initialize new tooltip
                            try {
                                new bootstrap.Tooltip(document.getElementById('tooltip-icon-'+key));
                            } catch (e) { }
                        }
                    }
                },
                error: function(xhr) {
                    iziToast.error({
                        title: 'Error',
                        message: xhr.responseJSON?.message || 'Failed to save tooltip',
                        position: 'topRight'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).text('Save changes');
                }
            });
        });
    }
});
</script>
