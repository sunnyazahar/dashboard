@php
    $address = $customer->primaryAddress;
@endphp
<div class="sidebar-info-tooltip">
    <div class="sidebar-info-tooltip-title">Customer details</div>
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Customer name', 'value' => $customer->customer_name])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'FM number', 'value' => $customer->customer_number])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Customer group', 'value' => $customer->group?->name])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Phone', 'value' => $customer->phone])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'E-mail', 'value' => $customer->email])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Internal shipment', 'value' => $customer->internal_shipment, 'bool' => true])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Street address', 'value' => $address?->street])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'City', 'value' => $address?->city])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'District/state', 'value' => $address?->state])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Zip code', 'value' => $address?->zip_code])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Country', 'value' => $address?->country?->name])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Port code', 'value' => $address?->port_code])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Account manager', 'value' => $customer->responsible?->accountManager?->name])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Responsible office', 'value' => $customer->responsible?->accountManager?->office?->office_short_name])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Sales manager', 'value' => $customer->responsible?->salesManager?->name])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'UN / LOCODE', 'value' => $customer->un_locode])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Remarks', 'value' => $customer->remarks])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Special considerations', 'value' => $customer->special_considerations])
    @include('Shipment.partials.sidebar-tooltip-row', ['label' => 'Status', 'value' => 'Active'])
</div>
