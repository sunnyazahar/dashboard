@php
    if (!empty($bool)) {
        $display = $value === null ? '—' : ($value ? 'Yes' : 'No');
    } elseif ($value === null || $value === '') {
        $display = '—';
    } else {
        $display = $value;
    }
@endphp
<div class="sidebar-tooltip-row">
    <span class="sidebar-tooltip-label">{{ $label }}</span>
    <span class="sidebar-tooltip-value">{{ $display }}</span>
</div>
