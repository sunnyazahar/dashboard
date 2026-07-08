@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
    <!-- Bootstrap Multiselect css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}" />
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <style>
        /* High Density Table Styles */
        #offices-table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        #offices-table thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 100 !important;
            background-color: #fdfdfd !important;
            color: #374151;
            font-size: 11px;
            font-weight: 600;
            padding: 10px 8px;
            border-bottom: 2px solid #dee2e6 !important;
            border-top: 1px solid #e5e7eb !important;
            white-space: nowrap;
            text-transform: none;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1); 
        }
        #offices-table tbody td {
            padding: 6px 8px !important;
            font-size: 11px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
            white-space: nowrap !important;
        }
        #offices-table th, #offices-table td {
            white-space: nowrap !important; 
        }
        .btn-teal {
            background-color: #008080;
            border-color: #008080;
            color: white;
        }
        .btn-teal:hover {
            background-color: #006666;
            border-color: #006666;
        }
        .btn-outline-teal {
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-outline-teal:hover {
            background-color: #008080;
            color: white;
        }
        .filter-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 2px;
            display: block;
        }
        .filter-input {
            height: 32px;
            font-size: 13px;
            border-radius: 2px;
        }
        .clear-filters {
            font-size: 12px;
            color: #ff5252;
            text-decoration: none;
            cursor: pointer;
            margin-top: 25px;
            display: inline-block;
        }
        .card-header-actions .btn {
            font-size: 12px;
            padding: 6px 15px;
            border-radius: 2px;
        }
        .custom-col {
            padding: 2px;
            margin-bottom: 0;
        }
        .filter-row {
            margin-bottom: 4px !important;
        }
        .filter-group {
            display: flex;
            align-items: center;
            border: 1px solid #e2e8f0;
            padding: 0;
            border-radius: 4px;
            height: 32px;
            background: #fff;
            overflow: hidden;
            width: 100%;
        }
        .filter-group .filter-label {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 0;
            padding: 0 10px;
            white-space: nowrap;
            font-weight: 500;
            border-right: 1px solid #e2e8f0;
            height: 100%;
            display: flex;
            align-items: center;
            background: #f8fafc;
            min-width: fit-content;
        }
        .filter-group .filter-input {
            border: none !important;
            box-shadow: none !important;
            height: 100% !important;
            font-size: 11px;
            padding: 0 10px !important;
            background: transparent !important;
            width: 100%;
            color: #1e293b;
        }
        .filter-group .select2-container--default .select2-selection--single,
        .filter-group .select2-container--default .select2-selection--multiple {
            border: none !important;
            background: transparent !important;
            height: 30px !important;
        }
        .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 10px !important;
            font-size: 11px !important;
            color: #1e293b !important;
            line-height: 30px !important;
        }
        .filter-group .select2-container--default .select2-selection--multiple .select2-selection__rendered,
        .filter-group .select2-container--default .select2-search--inline .select2-search__field {
            font-size: 11px !important;
            padding-left: 5px !important;
        }
        .filter-group .select2-container--default .select2-search--inline .select2-search__field::placeholder {
            font-size: 11px !important;
            color: #94a3b8 !important;
        }
        .clear-filters {
            font-size: 11px;
            color: #008080;
            text-decoration: none;
            cursor: pointer;
            height: 32px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            font-weight: 500;
        }
        /* Bootstrap Multiselect Custom Styling */
        .multiselect-native-select .btn-group {
            width: 100%;
        }
        .multiselect-native-select .multiselect {
            width: 100%;
            text-align: left;
            height: 30px;
            padding: 4px 10px;
            font-size: 11px;
            background-color: #fff;
            border: 1px solid #ced4da;
            color: #495057;
        }
        .multiselect-native-select .multiselect-container {
            width: 235px;
            font-size: 11px;
        }
        .multiselect-native-select .multiselect-container li a label {
            padding: 5px 10px 5px 0;
            display: block;
            margin: 0;
            cursor: pointer;
        }
        .multiselect-native-select .multiselect-selected .form-check-label {
            color: #008080;
            font-weight: bold;
        }
        .multiselect-item.multiselect-all label {
            font-weight: bold;
            color: #333;
        }
        input.form-control.multiselect-search {
            font-size: 11px;
        }
        .multiselect-container .input-group {
            margin: 2px;
        }
        .input-group-addon {
            background-color: #01a9ac;
            color: #fff;
            max-height: 31px;
        }
        .multiselect-container>li {
            padding: 0px 5px;
        }
        .multiselect-item .input-group {
            width: 114%;
        }
        /* Select2 Custom Styling */
        .select2-container .select2-selection--single {
            height: 30px !important;
            font-size: 11px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #008080;
            border: 1px solid #006666;
            color: #fff;
            font-size: 10px;
            margin-top: 2px;
        }
        .select2-container--default .select2-selection--multiple {
            min-height: 30px;
            border: 1px solid #ced4da;
            border-radius: 2px;
        }
        /* Filter Toggle Button Styling */
        .btn-filter-toggle {
            height: 32px;
            width: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #008080;
            border: 1px solid #e2e8f0;
            background-color: #fff;
            border-radius: 4px;
        }
        .btn-filter-toggle:hover, .btn-filter-toggle:focus {
            background-color: #f8fafc;
            color: #006666;
            border-color: #cbd5e1;
        }
        
        .table-scroll-wrapper {
            overflow-x: auto;
            overflow-y: auto;
            max-height: calc(100vh - 150px);
            width: 100%;
            position: relative;
        }
        .pagination-sticky-footer {
            position: sticky;
            bottom: 0;
            padding: 10px 20px;
            background: #ffffff;
            border-top: 1px solid #e9ecef;
            z-index: 10;
            margin-top: 0 !important;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.03);
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0 !important;
            padding: 0;
            display: flex;
            justify-content: flex-end;
        }
        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }

        /* Custom Datepicker Styles */
        .ui-datepicker {
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 0;
            font-family: inherit;
            font-size: 11px;
            z-index: 9999 !important;
            width: 250px;
            border-radius: 6px;
            overflow: hidden;
        }
        .ui-datepicker-header {
            background: #008080;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ui-datepicker-title {
            font-weight: 700;
            text-align: center;
            width: 100%;
        }
        .ui-datepicker-prev, .ui-datepicker-next {
            cursor: pointer;
            color: #fff;
            background: rgba(255,255,255,0.1);
            padding: 4px 8px;
            border-radius: 4px;
            text-align: center;
        }
        .ui-datepicker-prev:hover, .ui-datepicker-next:hover {
            background: rgba(255,255,255,0.2);
        }
        .ui-datepicker-title select {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 3px;
            padding: 2px 4px;
            margin: 0 2px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            outline: none;
        }
        .ui-datepicker-title select option {
            background: #fff;
            color: #374151;
            font-weight: normal;
        }
        .ui-datepicker-calendar {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        .ui-datepicker-calendar th {
            color: #6b7280;
            font-weight: 600;
            padding: 8px 0;
            text-align: center;
        }
        .ui-datepicker-calendar td {
            padding: 2px;
            text-align: center;
        }
        .ui-datepicker-calendar td a {
            display: block;
            padding: 6px;
            text-decoration: none;
            color: #374151;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .ui-datepicker-calendar td a:hover {
            background: #f0fdfa;
            color: #008080;
        }
        .ui-datepicker-calendar .ui-state-active {
            background: #008080 !important;
            color: #fff !important;
            font-weight: 700;
        }
        .ui-datepicker-calendar .ui-datepicker-today a {
            border: 1px solid #008080;
            color: #008080;
        }
    </style>
@endsection

@section('content')
<!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

          @include('layouts.top-menu')
                @include('layouts.left-menu')
                     <!-- Page-body start -->
                      <br>
                      <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                        <!-- Main-body start -->
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <!-- Page-header start -->
                                    <div class="page-header">
                                        
                                    </div>
                                    <!-- Page-header end -->

                                    <!-- Page-body start -->
                                    <div class="page-body">
                                        <!-- Base Style - Compact start -->
                                        <div class="card">
                                            <div class="card-block">
                                                <div class="d-flex justify-content-between align-items-start pt-2">
                                                    <div style="width: 80%;">
                                                        <div class="row no-gutters filter-row">
                                                            <div class="mr-2" style="margin-top: 2px;">
                                                                <select id="filter-multiselect" multiple="multiple">
                                                                    <option value="Customer">Customer</option>
                                                                    <option value="Vessel">Vessel</option>
                                                                    <option value="Shipment no">Shipment no</option>
                                                                    <option value="Service reference number">Service reference number</option>
                                                                    <option value="PO number">PO number</option>
                                                                    <option value="Departure hub">Departure hub</option>
                                                                    <option value="Consignee">Consignee</option>
                                                                    <option value="Port of destination">Port of destination</option>
                                                                    <option value="Account manager">Account manager</option>
                                                                    <option value="Created by">Created by</option>
                                                                    <option value="Office">Office</option>
                                                                    <option value="Creation date">Creation date</option>
                                                                    <option value="Service">Service</option>
                                                                </select>
                                                            </div>
                                                            <div id="col-Customer" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Customer</span>
                                                                    <select class="form-control filter-input select2" multiple="multiple">
                                                                        @foreach ($customers as $customer)
                                                                            <option value="{{ $customer }}">{{ $customer }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Vessel" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Vessel</span>
                                                                    <select class="form-control filter-input select2" multiple="multiple">
                                                                        @foreach ($vessels as $vessel)
                                                                            <option value="{{ $vessel }}">{{ $vessel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Shipment-no" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Shipment no</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>
                                                            <div id="col-Service-reference-number" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Service reference</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>
                                                            <div id="col-PO-number" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">PO number</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Row 2 -->
                                                        <div class="row no-gutters filter-row">
                                                            <div id="col-Departure-hub" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Departure port code</span>
                                                                    <select class="form-control filter-input select2" multiple="multiple">
                                                                        @foreach ($departureOptions as $departure)
                                                                            <option value="{{ $departure }}">{{ $departure }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Consignee" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Consignee</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>
                                                            <div id="col-Port-of-destination" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Port of destination</span>
                                                                    <input type="text" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>
                                                            <div id="col-Account-manager" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Account manager</span>
                                                                    <select class="form-control filter-input select2" multiple="multiple">
                                                                        @foreach ($accountManagers as $manager)
                                                                            <option value="{{ $manager->name }}">{{ $manager->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Created-by" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Created by</span>
                                                                    <select class="form-control filter-input select2" multiple="multiple">
                                                                        @foreach ($creators as $creator)
                                                                            <option value="{{ $creator->name }}">{{ $creator->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Office" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Office</span>
                                                                    <select class="form-control filter-input select2" multiple="multiple">
                                                                        @foreach ($offices as $office)
                                                                            <option value="{{ $office->office_name }}">{{ $office->office_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div id="col-Creation-date" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Creation date</span>
                                                                    <div class="input-group p-0 m-0" style="border: none;">
                                                                        <input type="date" class="form-control filter-input datepicker" placeholder="dd/mm/yyyy" style="border: none; background: transparent;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="col-Service" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Service</span>
                                                                    <select class="form-control filter-input select2" multiple="multiple">
                                                                        @foreach ($services as $service)
                                                                            <option value="{{ $service }}">{{ $service }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <a class="clear-filters">Clear filters</a>
                                                        </div>
                                                </div>
                                                <div class="text-right" style="width: 20%; padding-top: 18px;">
                                                     <button class="btn btn-outline-teal"><i class="ti-download"></i> Export</button>
                                                     <a href="{{ route('create-shipment') }}" class="btn btn-teal ml-2">Create shipment</a>
                                                </div>
                                            </div>
                                                <div class="table-scroll-wrapper">
                                                    <table id="offices-table"
                                                        class="office-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Shipment No</th>
                                                                <th>Customer</th>
                                                                <th>Vessel</th>
                                                                <th>Service</th>
                                                                <th>Service Reference</th>
                                                                <th>Consignee</th>
                                                                <th>Departure</th>
                                                                <th>Destination</th>
                                                                <th>Deadline</th>
                                                                <th>PA Reminder</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($shipments as $shipment)
                                                            @php
                                                                $consigneeDisplay = $shipment->partyDisplay($shipment->consignee, $partyNames);
                                                                $customerNames = $shipment->customerNamesFromVessels($vesselCustomerMap ?? []);
                                                                $customerDisplay = $shipment->formatNamesDisplay($customerNames);
                                                                $customerDisplayShort = $shipment->formatNamesDisplayShort($customerNames);
                                                            @endphp
                                                            <tr
                                                                data-po-numbers="{{ $shipment->po_numbers_display }}"
                                                                data-account-manager="{{ $shipment->accountManager?->name ?? '' }}"
                                                                data-created-by="{{ $shipment->creator?->name ?? '' }}"
                                                                data-office="{{ $shipment->accountManager?->office?->office_name ?? '' }}"
                                                                data-creation-date="{{ $shipment->created_at?->format('Y-m-d') ?? '' }}"
                                                            >
                                                                <td>
                                                                    <a href="{{ route('shipments.edit', $shipment->id) }}" class="text-primary">{{ $shipment->shipment_number }}</a>
                                                                    @if ($shipment->hasOpenIrregularities())
                                                                        <i class="ti-alert text-danger ml-2" title="Open irregularities"></i>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($customerNames->count() > 2)
                                                                        <span title="{{ $customerDisplay }}" style="cursor: help;">{{ $customerDisplayShort }}</span>
                                                                    @else
                                                                        {{ $customerDisplay }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($shipment->vessel_names->count() > 2)
                                                                        <span title="{{ $shipment->vessel_display }}" style="cursor: help;">{{ $shipment->vessel_display_short }}</span>
                                                                    @else
                                                                        {{ $shipment->vessel_display }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $shipment->service ?? '—' }}</td>
                                                                <td>
                                                                    @if ($shipment->service_reference_values->count() > 2)
                                                                        <span title="{{ $shipment->service_reference_display }}" style="cursor: help;">{{ $shipment->service_reference_display_short }}</span>
                                                                    @else
                                                                        {{ $shipment->service_reference_display }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $consigneeDisplay }}</td>
                                                                <td>{{ $shipment->departure_port_code ?: '—' }}</td>
                                                                <td>{{ $shipment->destination_display }}</td>
                                                                <td>{{ $shipment->deadline_arrival?->format('d.m.Y') ?? '—' }}</td>
                                                                <td>{{ $shipment->pre_alert_reminder?->format('d.m.Y') ?? '—' }}</td>
                                                                <td><label class="{{ $shipment->statusBadgeClass() }}">{{ $shipment->status }}</label></td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="11" class="text-center py-4 text-muted">No shipments found.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>Shipment No</th>
                                                                <th>Customer</th>
                                                                <th>Vessel</th>
                                                                <th>Service</th>
                                                                <th>Service Reference</th>
                                                                <th>Consignee</th>
                                                                <th>Departure</th>
                                                                <th>Destination</th>
                                                                <th>Deadline</th>
                                                                <th>PA Reminder</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Base Style - Compact end -->
                                    </div>
                                    <!-- Page-body end -->
                                </div>
                            </div>
                            <div id="styleSelector">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/feature-detects/css-scrollbars.js') }}"></script>

    <!-- data-table js -->
    <script src="{{ asset('files/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/jszip.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{-- <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for standard filters
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: true,
                width: '100%'
            });

            // Initialize Bootstrap Multiselect for special filter toggle
            $('#filter-multiselect').multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                buttonWidth: '100%',
                maxHeight: 200,
                nonSelectedText: '',
                allSelectedText: '',
                nSelectedText: '',
                numberDisplayed: 0,
                buttonClass: 'btn btn-outline-teal btn-filter-toggle',
                templates: {
                    button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><i class="ti-filter"></i></button>'
                },
                onChange: function(option, checked) {
                    toggleFilterVisibility();
                },
                onSelectAll: function() {
                    toggleFilterVisibility();
                },
                onDeselectAll: function() {
                    toggleFilterVisibility();
                }
            });

            function toggleFilterVisibility() {
                var selectedOptions = $('#filter-multiselect option:selected');
                var selectedValues = [];
                selectedOptions.each(function() {
                    selectedValues.push($(this).val());
                });

                // Map of Option Value -> element ID
                // Note: User provided option values are like "Customer", "Service reference number", etc.
                // We constructed IDs like "col-Customer", "col-Service-reference-number"
                
                var allFilters = [
                    {val: 'Customer', id: 'col-Customer'},
                    {val: 'Vessel', id: 'col-Vessel'},
                    {val: 'Shipment no', id: 'col-Shipment-no'},
                    {val: 'Service reference number', id: 'col-Service-reference-number'},
                    {val: 'PO number', id: 'col-PO-number'},
                    {val: 'Departure hub', id: 'col-Departure-hub'},
                    {val: 'Consignee', id: 'col-Consignee'},
                    {val: 'Port of destination', id: 'col-Port-of-destination'},
                    {val: 'Account manager', id: 'col-Account-manager'},
                    {val: 'Created by', id: 'col-Created-by'},
                    {val: 'Office', id: 'col-Office'},
                    {val: 'Creation date', id: 'col-Creation-date'},
                    {val: 'Service', id: 'col-Service'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.includes(filter.val)) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }
            
            // Initial call to set visibility state
            toggleFilterVisibility();

            var table = $('#offices-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": true,
                "ordering": true,
                "autoWidth": false
            });

            var filterMap = {
                '#col-Shipment-no input': 0,
                '#col-Service-reference-number input': 4,
                '#col-Departure-hub select': 6,
                '#col-Consignee input': 5,
                '#col-Port-of-destination input': 7,
                '#col-Service select': 3
            };

            $.each(filterMap, function(selector, colIndex) {
                $(selector).on('change keyup', function() {
                    var val = $(this).val();
                    if (Array.isArray(val)) {
                        var searchVal = val.length > 0 ? val.map(function(item) {
                            return $.fn.dataTable.util.escapeRegex(item);
                        }).join('|') : '';
                        table.column(colIndex).search(searchVal, true, false).draw();
                    } else {
                        table.column(colIndex).search(val).draw();
                    }
                });
            });

            $('#col-Customer select, #col-Vessel select').on('change', function() {
                table.draw();
            });

            function applyAttributeFilter(selector, attributeName) {
                $(selector).on('change keyup', function() {
                    table.draw();
                });
            }

            applyAttributeFilter('#col-Account-manager select', 'account-manager');
            applyAttributeFilter('#col-Created-by select', 'created-by');
            applyAttributeFilter('#col-Office select', 'office');
            applyAttributeFilter('#col-PO-number input', 'po-numbers');
            applyAttributeFilter('#col-Creation-date input', 'creation-date');

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'offices-table') {
                    return true;
                }

                var row = table.row(dataIndex).node();
                if (!row) {
                    return true;
                }

                var $row = $(row);

                var customers = $('#col-Customer select').val() || [];
                if (customers.length > 0) {
                    var rowCustomer = data[1] || '';
                    if (!customers.some(function(customer) {
                        return rowCustomer.indexOf(customer) !== -1;
                    })) {
                        return false;
                    }
                }

                var vessels = $('#col-Vessel select').val() || [];
                if (vessels.length > 0) {
                    var rowVessel = data[2] || '';
                    if (!vessels.some(function(vessel) {
                        return rowVessel.indexOf(vessel) !== -1;
                    })) {
                        return false;
                    }
                }

                var poFilter = $('#col-PO-number input').val().toLowerCase().trim();
                if (poFilter) {
                    var poNumbers = ($row.data('po-numbers') || '').toString().toLowerCase();
                    if (poNumbers.indexOf(poFilter) === -1) {
                        return false;
                    }
                }

                var creationDate = $('#col-Creation-date input').val();
                if (creationDate && $row.data('creation-date') !== creationDate) {
                    return false;
                }

                var accountManagers = $('#col-Account-manager select').val() || [];
                if (accountManagers.length > 0 && accountManagers.indexOf($row.data('account-manager')) === -1) {
                    return false;
                }

                var createdBy = $('#col-Created-by select').val() || [];
                if (createdBy.length > 0 && createdBy.indexOf($row.data('created-by')) === -1) {
                    return false;
                }

                var offices = $('#col-Office select').val() || [];
                if (offices.length > 0 && offices.indexOf($row.data('office')) === -1) {
                    return false;
                }

                return true;
            });

            $('.clear-filters').on('click', function(e) {
                e.preventDefault();
                $('.select2').val(null).trigger('change');
                $('.filter-input:not(select)').val('').trigger('keyup');
                table.columns().search('').draw();
            });
        });
    </script>
@endsection
