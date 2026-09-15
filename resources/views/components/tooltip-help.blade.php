@props(['key', 'label', 'class' => 'form-label'])

@php
    // Fetch tooltip from DB using the helper we added previously
    $tooltipText = getTooltip($key);
    // Check if the current user has permission to update tooltips
    $canEdit = permissions_check('canUpdate', 'tooltips.index');
@endphp

<label class="{{ $class }}">
    {{ $label }}

    @if($tooltipText || $canEdit)
        <span class="tooltip-icon-wrapper d-inline-block ms-1" style="cursor: pointer;">
            @if($tooltipText)
                <i class="fas fa-info-circle text-primary"
                   data-bs-toggle="tooltip"
                   data-bs-placement="top"
                   data-bs-html="true"
                   id="tooltip-icon-{{ $key }}"
                   title="{{ $tooltipText }}"></i>
            @endif

            @if($canEdit)
                <i class="fas fa-edit text-muted ms-1 edit-tooltip-btn"
                   style="font-size: 0.85em;"
                   data-key="{{ $key }}"
                   data-label="{{ $label }}"
                   title="Edit Tooltip"></i>
            @endif
        </span>
    @endif
</label>
