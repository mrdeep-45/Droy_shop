@props(['field' => ''])

@php
    $tooltipText = getTooltip($field);
@endphp

@if($tooltipText)
<span class="tooltip-icon-wrapper d-inline-block ms-1" style="cursor: pointer;">
    <i class="fas fa-info-circle text-primary"
       data-bs-toggle="tooltip"
       data-bs-placement="top"
       data-bs-html="true"
       title="{{ $tooltipText }}"></i>
</span>
@endif
