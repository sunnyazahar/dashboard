@php
    if ($vessel) {
        if ($vessel->inactive_vessel) {
            $vesselStatus = 'Inactive';
        } elseif ($vessel->sanction_blocked || $vessel->financially_blocked) {
            $vesselStatus = 'Blocked';
        } else {
            $vesselStatus = 'Active';
        }
    } else {
        $vesselStatus = '—';
    }
@endphp
<div class="sidebar-info-tooltip">
    <div class="sidebar-info-tooltip-title">Vessel details</div>
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Vessel name', 'value' => $displayName])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Vessel name alias', 'value' => $vessel?->vessel_name_alias])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Customer vessel code', 'value' => $vessel?->customer_vessel_code])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'IMO', 'value' => $vessel?->vessel_imo])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Vessel type alias', 'value' => $vessel?->vessel_type_alias])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Customer', 'value' => $vessel?->customer?->customer_name])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Manager', 'value' => $vessel?->manager])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Account manager', 'value' => $vessel?->account_manager])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Shipyard', 'value' => $vessel?->shipyard])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Shipyard location', 'value' => $vessel?->shipyard_location])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Home consolidation port', 'value' => $vessel?->home_consolidation_port])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Home delivery port', 'value' => $vessel?->home_delivery_port])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Not in transit', 'value' => $vessel?->not_in_transit, 'bool' => true])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Sanction blocked', 'value' => $vessel?->sanction_blocked, 'bool' => true])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Financially blocked', 'value' => $vessel?->financially_blocked, 'bool' => true])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Pre-payment only', 'value' => $vessel?->pre_payment_only, 'bool' => true])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Internal shipment', 'value' => $vessel?->internal_shipment, 'bool' => true])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Except from hubs', 'value' => $vessel?->except_from_hubs, 'bool' => true])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'PO example', 'value' => $vessel?->po_example])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Remarks', 'value' => $vessel?->remarks])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Status', 'value' => $vesselStatus])
</div>
