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
    <!-- Date-range picker css  -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}" />
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
        .filter-group {
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
            padding: 0 10px;
            border-radius: 4px;
            height: 32px;
            background: #fff;
            overflow: hidden;
        }
        .filter-group .filter-label {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 0;
            padding-right: 10px;
            margin-right: 10px;
            white-space: nowrap;
            font-weight: 500;
            border-right: 1px solid #ced4da;
            height: 100%;
            display: flex;
            align-items: center;
        }
        .filter-group .filter-input {
            border: none !important;
            box-shadow: none !important;
            height: 100% !important;
            font-size: 12px;
            padding: 0 !important;
            background: transparent !important;
            width: 100%;
        }
        .filter-group .select2-container--default .select2-selection--single,
        .filter-group .select2-container--default .select2-selection--multiple {
            border: none !important;
            background: transparent !important;
        }
        .filter-group .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
        }
        .filter-group i {
            color: #008080;
            font-size: 14px;
        }
        .custom-col {
            padding-right: 5px;
            padding-left: 5px;
            margin-bottom: 10px;
        }
        .clear-filters {
            font-size: 12px;
            color: #008080;
            text-decoration: none;
            cursor: pointer;
            margin-left: 10px;
            align-self: center;
            display: flex;
            align-items: center;
        }
        .filter-input {
            height: 30px;
            font-size: 11px;
            border-radius: 2px;
        }
        .label {
            border-radius: 4px;
            font-size: 100%;
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
        .select2-container--default .select2-selection--single {
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            height: 30px !important;
            display: flex !important;
            align-items: center !important;
            outline: none !important;
        }
        .select2-container--default .select2-selection--multiple {
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            min-height: 30px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            background-color: transparent !important;
            color: #4b5563 !important;
            line-height: normal !important;
            padding-left: 10px !important;
            padding-right: 25px !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            right: 8px !important;
            width: 20px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #666 transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 4px 0 4px !important;
            height: 0 !important;
            left: 50% !important;
            margin-left: -4px !important;
            margin-top: -2px !important;
            position: absolute !important;
            top: 50% !important;
            width: 0 !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f3f4f6 !important;
            border: 1px solid #ced4da !important;
            color: #4b5563 !important;
            font-size: 10px !important;
            margin-top: 4px !important;
            padding: 1px 5px !important;
        }
        /* Filter Toggle Button Styling */
        .btn-filter-toggle {
            height: 30px;
            padding: 4px 10px;
            font-size: 14px;
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-filter-toggle:hover, .btn-filter-toggle:focus, .btn-filter-toggle:active {
            background-color: #008080 !important;
            color: white !important;
            border-color: #008080 !important;
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
        td a {
            color: rgb(24, 100, 131) !important;
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
                                                    <div style="width: 100%;">
                                                        <div class="row no-gutters">
                                                            <div class="mr-2" style="margin-top: 2px;">
                                                                <select id="filter-multiselect" multiple="multiple">
                                                                    <option value="Account manager">Account manager</option>
                                                                    <option value="Show ETL shipments">Show ETL shipments</option>
                                                                    <option value="Shipment no">Shipment no</option>
                                                                    <option value="Customer">Customer</option>
                                                                    <option value="Vessel">Vessel</option>
                                                                    <option value="Port of destination">Port of destination</option>
                                                                    <option value="Created by">Created by</option>
                                                                </select>
                                                            </div>

                                                            <div id="col-Account-manager" class="custom-col" style="flex: 0 0 220px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Account manager</span>
                                                                    <select id="filter-account-manager" class="form-control filter-input select2" multiple="multiple">
                                                                        @foreach ($accountManagers as $manager)
                                                                            <option value="{{ $manager->name }}">{{ $manager->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Show-ETL-shipments" class="custom-col" style="flex: 0 0 160px;">
                                                                <div class="filter-group" style="border: none; background: transparent;">
                                                                    <div class="d-flex align-items-center" style="font-size: 11px; color: #64748b; font-weight: 500;">
                                                                        <span>Show ETL shipments</span>
                                                                        <input type="checkbox" id="filter-show-etl" class="ml-2" style="width: 14px; height: 14px; margin-top: 0;">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div id="col-Shipment-no" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Shipment no</span>
                                                                    <input type="text" id="filter-shipment-no" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>

                                                            <div id="col-Customer" class="custom-col" style="flex: 0 0 250px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Customer</span>
                                                                    <select id="filter-customer" class="form-control filter-input select2" multiple="multiple">
                                                                        @foreach ($customers as $customer)
                                                                            <option value="{{ $customer }}">{{ $customer }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Vessel" class="custom-col" style="flex: 0 0 200px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Vessel</span>
                                                                    <select id="filter-vessel" class="form-control filter-input select2" multiple="multiple">
                                                                        @foreach ($vessels as $vessel)
                                                                            <option value="{{ $vessel }}">{{ $vessel }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div id="col-Port-of-destination" class="custom-col" style="flex: 0 0 220px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Port of destination</span>
                                                                    <input type="text" id="filter-port-destination" class="form-control filter-input" placeholder="type here">
                                                                </div>
                                                            </div>

                                                            <div id="col-Created-by" class="custom-col" style="flex: 0 0 220px;">
                                                                <div class="filter-group">
                                                                    <span class="filter-label">Created by</span>
                                                                    <select id="filter-created-by" class="form-control filter-input select2" multiple="multiple">
                                                                        @foreach ($creators as $creator)
                                                                            <option value="{{ $creator->name }}">{{ $creator->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <a class="clear-filters">Clear filters</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                <div class="table-scroll-wrapper">
                                                    <table id="offices-table"
                                                        class="office-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Shipment no</th>
                                                                <th>Customer</th>
                                                                <th>Vessel</th>
                                                                <th>Service</th>
                                                                <th>Consignee</th>
                                                                <th>Departure</th>
                                                                <th>Destination</th>
                                                                <th>Weight</th>
                                                                <th>Deadline arrival</th>
                                                                <th>PA reminder</th>
                                                                <th>Handled by</th>
                                                                <th>Rem. sent</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($shipments as $shipment)
                                                            @php
                                                                $departureDisplay = $shipment->partyDisplay($shipment->departure, $partyNames);
                                                                $consigneeDisplay = $shipment->partyDisplay($shipment->consignee, $partyNames);
                                                                $paReminder = $shipment->pre_alert_reminder;
                                                                $paReminderOverdue = $paReminder && $paReminder->startOfDay()->lt(now()->startOfDay());
                                                            @endphp
                                                            <tr
                                                                data-account-manager="{{ $shipment->accountManager?->name ?? '' }}"
                                                                data-created-by="{{ $shipment->creator?->name ?? '' }}"
                                                                data-has-etl="{{ $shipment->hasEtlStock() ? '1' : '0' }}"
                                                            >
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <a href="{{ route('shipments.edit', $shipment->id) }}">{{ $shipment->shipment_number }}</a>
                                                                        @if ($shipment->hasOpenIrregularities())
                                                                            <i class="ti-alert text-danger ml-2" title="Open irregularities"></i>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>{{ $shipment->customer_display }}</td>
                                                                <td>{{ $shipment->vessel_display }}</td>
                                                                <td>{{ $shipment->service ?? '—' }}</td>
                                                                <td>{{ $consigneeDisplay }}</td>
                                                                <td>{{ $departureDisplay }}</td>
                                                                <td>{{ $shipment->destination_display }}</td>
                                                                <td>{{ $shipment->total_weight_display }}</td>
                                                                <td>{{ $shipment->deadline_arrival?->format('d.m.Y') ?? '—' }}</td>
                                                                <td @if($paReminderOverdue) style="color: #ff5252;" @endif>{{ $paReminder?->format('d.m.Y') ?? '—' }}</td>
                                                                <td>{{ $shipment->accountManager?->name ?? '—' }}</td>
                                                                <td class="reminder-sent-count" data-shipment-id="{{ $shipment->id }}">{{ $shipment->reminder_sent_count }}</td>
                                                                <td>
                                                                    <button type="button"
                                                                        class="btn btn-outline-teal py-1 pl-2 pr-2 send-reminder-btn"
                                                                        style="font-size: 10px; height: 24px; border-color: #ddd; color: #666;"
                                                                        data-shipment-id="{{ $shipment->id }}"
                                                                        data-preview-url="{{ route('shipments.pre-alert-reminder-mail.preview', $shipment->id) }}"
                                                                        data-record-url="{{ route('shipments.pre-alert-reminder-mail.send', $shipment->id) }}"
                                                                        data-eml-url="{{ route('shipments.pre-alert-reminder-mail', $shipment->id) }}"
                                                                        data-eml-filename="pre-alert-reminder-{{ $shipment->shipment_number }}.eml">Send reminder</button>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="13" class="text-center py-4 text-muted">No shipments found.</td>
                                                            </tr>
                                                            @endforelse
                                                        </tbody>
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
    <!-- date-range-picker js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/moment/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

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

                var allFilters = [
                    {val: 'Account manager', id: 'col-Account-manager'},
                    {val: 'Show ETL shipments', id: 'col-Show-ETL-shipments'},
                    {val: 'Shipment no', id: 'col-Shipment-no'},
                    {val: 'Customer', id: 'col-Customer'},
                    {val: 'Vessel', id: 'col-Vessel'},
                    {val: 'Port of destination', id: 'col-Port-of-destination'},
                    {val: 'Created by', id: 'col-Created-by'}
                ];

                allFilters.forEach(function(filter) {
                    if (selectedValues.includes(filter.val)) {
                        $('#' + filter.id).show();
                    } else {
                        $('#' + filter.id).hide();
                    }
                });
            }
            
            toggleFilterVisibility();

            var table = $('#offices-table').DataTable({
                "dom": '<"table-scroll-wrapper"rt><"pagination-sticky-footer"p>',
                "lengthChange": false,
                "pageLength": 100,
                "responsive": false,
                "searching": true,
                "ordering": true,
                "autoWidth": false,
                "scrollX": true,
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

            var filterMap = {
                '#filter-shipment-no': 0,
                '#filter-port-destination': 6
            };

            $.each(filterMap, function(selector, colIndex) {
                $(selector).on('keyup change', function() {
                    table.column(colIndex).search($(this).val()).draw();
                });
            });

            $('#filter-customer, #filter-vessel, #filter-account-manager, #filter-created-by, #filter-show-etl').on('change keyup', function() {
                table.draw();
            });

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'offices-table') {
                    return true;
                }

                var row = table.row(dataIndex).node();
                if (!row) {
                    return true;
                }

                var $row = $(row);
                var showEtlOnly = $('#filter-show-etl').is(':checked');
                var rowHasEtl = $row.data('has-etl') === 1 || $row.data('has-etl') === '1';

                if (showEtlOnly && !rowHasEtl) {
                    return false;
                }

                var customers = $('#filter-customer').val() || [];
                if (customers.length > 0) {
                    var rowCustomer = data[1] || '';
                    if (!customers.some(function(customer) {
                        return rowCustomer.indexOf(customer) !== -1;
                    })) {
                        return false;
                    }
                }

                var vessels = $('#filter-vessel').val() || [];
                if (vessels.length > 0) {
                    var rowVessel = data[2] || '';
                    if (!vessels.some(function(vessel) {
                        return rowVessel.indexOf(vessel) !== -1;
                    })) {
                        return false;
                    }
                }

                var accountManagers = $('#filter-account-manager').val() || [];
                if (accountManagers.length > 0 && accountManagers.indexOf($row.data('account-manager')) === -1) {
                    return false;
                }

                var createdBy = $('#filter-created-by').val() || [];
                if (createdBy.length > 0 && createdBy.indexOf($row.data('created-by')) === -1) {
                    return false;
                }

                return true;
            });

            $('.clear-filters').on('click', function(e) {
                e.preventDefault();
                $('.select2').val(null).trigger('change');
                $('.filter-input:not(select)').val('').trigger('keyup');
                $('#filter-show-etl').prop('checked', false);
                table.columns().search('').draw();
            });

            function openReminderMailto(preview) {
                if (!preview) {
                    return;
                }

                var params = [];
                if (preview.to) {
                    params.push('to=' + encodeURIComponent(preview.to));
                }
                if (preview.cc) {
                    params.push('cc=' + encodeURIComponent(preview.cc));
                }
                if (preview.subject) {
                    params.push('subject=' + encodeURIComponent(preview.subject));
                }
                if (preview.body) {
                    var body = preview.body;
                    if (body.length > 1800) {
                        body = body.substring(0, 1800) + '...';
                    }
                    params.push('body=' + encodeURIComponent(body));
                }

                var mailtoLink = document.createElement('a');
                mailtoLink.href = 'mailto:' + (params.length ? '?' + params.join('&') : '');
                mailtoLink.style.display = 'none';
                document.body.appendChild(mailtoLink);
                mailtoLink.click();
                document.body.removeChild(mailtoLink);
            }

            $(document).on('click', '.send-reminder-btn', function(e) {
                e.preventDefault();

                var $btn = $(this);
                var previewUrl = $btn.data('preview-url');
                var recordUrl = $btn.data('record-url');
                var shipmentId = $btn.data('shipment-id');
                var originalText = $btn.text();

                if (!previewUrl) {
                    return;
                }

                $btn.prop('disabled', true).text('Preparing...');

                $.ajax({
                    url: previewUrl,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                    .done(function(response) {
                        if (!response || !response.success || !response.preview) {
                            alert((response && response.message) || 'Could not prepare reminder email.');
                            return;
                        }

                        if (!recordUrl) {
                            openReminderMailto(response.preview);
                            return;
                        }

                        $.ajax({
                            url: recordUrl,
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                            .done(function(recordResponse) {
                                if (recordResponse && recordResponse.success) {
                                    $('.reminder-sent-count[data-shipment-id="' + shipmentId + '"]').text(recordResponse.reminder_sent_count);
                                }
                                openReminderMailto(response.preview);
                            })
                            .fail(function() {
                                openReminderMailto(response.preview);
                            });
                    })
                    .fail(function(xhr) {
                        var message = 'Could not prepare reminder email.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert(message);
                    })
                    .always(function() {
                        $btn.prop('disabled', false).text(originalText);
                    });
            });
        });
    </script>
@endsection
