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
        #offices-table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            min-width: 1500px;
            background: #fff;
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
        .custom-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -5px;
            margin-left: -5px;
        }
        .filter-row {
            margin-bottom: 8px;
        }
        .custom-col {
            padding-right: 5px;
            padding-left: 5px;
        }
        .filter-group {
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
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
        .select2-selection__clear,
        .select2-selection__choice__remove {
            display: none !important;
        }
        .label {
            border-radius: 2px;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 10px;
            text-transform: uppercase;
            display: inline-block;
            min-width: 70px;
            text-align: center;
        }
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
            padding: 6px 10px 6px 6px;
            display: block;
            margin: 0;
            cursor: pointer;
            font-size: 14px;
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
            color: #495057 !important;
            line-height: normal !important;
            padding-left: 10px !important;
            padding-right: 25px !important;
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #666 transparent transparent transparent !important;
            margin-top: 0 !important;
            position: relative !important;
            top: auto !important;
            left: auto !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #f3f4f6 !important;
            border: 1px solid #ced4da !important;
            color: #495057 !important;
            font-size: 10px !important;
            margin-top: 4px !important;
            padding: 1px 5px !important;
        }
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

        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }

        body.cost-follow-up-list-page {
            overflow: hidden !important;
            height: 100vh;
        }
        body.cost-follow-up-list-page .pcoded-content {
            overflow: hidden !important;
        }
        body.cost-follow-up-list-page .pcoded-inner-content,
        body.cost-follow-up-list-page .main-body,
        body.cost-follow-up-list-page .page-wrapper,
        body.cost-follow-up-list-page .page-body {
            height: 100%;
            overflow: hidden !important;
            margin: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        .cost-list-card {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 104px);
            margin-bottom: 0 !important;
            overflow: hidden;
        }
        .cost-list-card > .card-block {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
            padding-bottom: 8px !important;
        }
        .cost-filters-fixed {
            flex-shrink: 0;
            background: #fff;
            position: relative;
            z-index: 40;
            padding-bottom: 6px;
        }
        .cost-table-area {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .cost-table-area .dataTables_wrapper {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            height: 100%;
            padding-bottom: 0 !important;
        }
        .cost-table-area .table-scroll-wrapper {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            width: 100%;
            position: relative;
        }
        .cost-table-area .dataTables_scroll {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            height: 100% !important;
            width: 100%;
        }
        .cost-table-area .dataTables_scrollHead {
            flex-shrink: 0 !important;
            position: relative !important;
            overflow: hidden !important;
            background: #fdfdfd;
            border-bottom: 2px solid #dee2e6;
            z-index: 5;
            margin-bottom: 0 !important;
        }
        .cost-table-area .dataTables_scrollBody {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow-x: auto !important;
            overflow-y: auto !important;
            margin-top: 0 !important;
            border-top: none !important;
        }
        .cost-table-area .dataTables_scrollBody > table > thead,
        .cost-table-area .dataTables_scrollBody thead {
            height: 0 !important;
            line-height: 0 !important;
            visibility: collapse !important;
        }
        .cost-table-area .dataTables_scrollBody thead tr,
        .cost-table-area .dataTables_scrollBody thead th {
            height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border: none !important;
            line-height: 0 !important;
            font-size: 0 !important;
            overflow: hidden !important;
        }
        .cost-table-area .dataTables_scrollBody thead th:before,
        .cost-table-area .dataTables_scrollBody thead th:after {
            display: none !important;
        }
        .dataTables_scrollHeadInner,
        .dataTables_scrollHead table {
            width: 100% !important;
        }
        #offices-table thead th {
            position: relative !important;
            top: auto !important;
            z-index: auto !important;
            background-color: #fdfdfd !important;
            color: #374151;
            font-size: 11px;
            font-weight: 600;
            padding: 10px 8px;
            border-bottom: 2px solid #dee2e6 !important;
            border-top: 1px solid #e5e7eb !important;
            white-space: nowrap;
            text-transform: none;
            box-shadow: none;
        }
        .pagination-sticky-footer {
            position: fixed !important;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 10px 20px;
            background: #ffffff;
            border-top: 1px solid #e9ecef;
            z-index: 1040;
            margin: 0 !important;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.03);
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0 !important;
            padding: 0;
            display: flex;
            justify-content: flex-end;
            float: none !important;
            width: 100%;
        }
        .dataTables_wrapper {
            padding-bottom: 0 !important;
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
                                        <div class="card cost-list-card mt-4">
                                            <div class="card-block">
                                                <div class="cost-filters-fixed">
                                                <div class="d-flex justify-content-between align-items-start pt-2">
                                                    <div style="width: 100%;">
                                                        <div class="row custom-row filter-row">
                                                            <div class="custom-col" style="flex: 0 0 50px;">
                                                                <select id="filter-multiselect" multiple="multiple">
                                                                    <option value="Account manager" selected>Account manager</option>
                                                                    <option value="Shipment no" selected>Shipment no</option>
                                                                    <option value="Customer" selected>Customer</option>
                                                                    <option value="Vessel" selected>Vessel</option>
                                                                    <option value="Port of destination" selected>Port of destination</option>
                                                                    <option value="Created by" selected>Created by</option>
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

                                                            <div class="custom-col">
                                                                <a class="clear-filters"><i class="ti-close"></i> Clear filters</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>

                                                <div class="cost-table-area">
                                                    <table id="offices-table"
                                                        class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Shipment no.</th>
                                                                <th>Customer</th>
                                                                <th>Vessel</th>
                                                                <th>Service</th>
                                                                <th>Service reference</th>
                                                                <th>Consignee</th>
                                                                <th>Dep.</th>
                                                                <th>Dest.</th>
                                                                <th>Dep. date</th>
                                                                <th>Arrival date</th>
                                                                <th>Del. date</th>
                                                                <th>Status</th>
                                                                <th>Rem. sent</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
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
            $('body').addClass('cost-follow-up-list-page');

            $('.select2').select2({
                placeholder: "Click here",
                allowClear: false,
                width: '100%'
            });

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
                onChange: function() {
                    toggleFilterVisibility();
                },
                onSelectAll: function() {
                    toggleFilterVisibility();
                },
                onDeselectAll: function() {
                    toggleFilterVisibility();
                }
            });

            $('#filter-multiselect').multiselect('selectAll', false);
            $('#filter-multiselect').multiselect('updateButtonText');

            function toggleFilterVisibility() {
                var selectedValues = [];
                $('#filter-multiselect option:selected').each(function() {
                    selectedValues.push($(this).val());
                });

                var allFilters = [
                    {val: 'Account manager', id: 'col-Account-manager'},
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

                if (typeof table !== 'undefined' && table.columns) {
                    setTimeout(adjustCostTableLayout, 50);
                }
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
                "scrollY": '50vh',
                "scrollX": true,
                "scrollCollapse": true,
                "columnDefs": [
                    { "targets": [13], "orderable": false }
                ],
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

            function getCostTableScrollHeight() {
                var $tableArea = $('.cost-table-area');
                var $scrollHead = $('.dataTables_scrollHead');
                var areaHeight = $tableArea.length ? $tableArea.innerHeight() : 0;
                var headHeight = $scrollHead.length ? $scrollHead.outerHeight() : 40;
                var available = areaHeight - headHeight - 2;

                if (available < 180) {
                    var topOffset = $scrollHead.length ? $scrollHead.offset().top : 220;
                    var paginationHeight = $('.pagination-sticky-footer').outerHeight() || 48;
                    available = window.innerHeight - topOffset - paginationHeight - 4;
                }

                return Math.max(180, available);
            }

            function adjustCostTableLayout() {
                var height = getCostTableScrollHeight();
                var $scrollBody = $('.dataTables_scrollBody');

                $scrollBody.css({
                    height: height + 'px',
                    maxHeight: height + 'px'
                });

                table.columns.adjust();
            }

            $(window).on('resize', function() {
                adjustCostTableLayout();
            });

            setTimeout(adjustCostTableLayout, 100);
            setTimeout(adjustCostTableLayout, 400);

            var searchRequest = null;
            var searchTimer = null;
            var searchUrl = @json(route('cost-follow-up.search'));

            function escapeHtml(value) {
                return String(value == null ? '' : value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function getActiveFilters() {
                return {
                    account_manager: $('#filter-account-manager').val() || [],
                    customer: $('#filter-customer').val() || [],
                    vessel: $('#filter-vessel').val() || [],
                    created_by: $('#filter-created-by').val() || [],
                    shipment_no: String($('#filter-shipment-no').val() || '').trim(),
                    port_destination: String($('#filter-port-destination').val() || '').trim()
                };
            }

            function hasActiveFilters(filters) {
                return (filters.account_manager && filters.account_manager.length)
                    || (filters.customer && filters.customer.length)
                    || (filters.vessel && filters.vessel.length)
                    || (filters.created_by && filters.created_by.length)
                    || filters.shipment_no !== ''
                    || filters.port_destination !== '';
            }

            function buildRowHtml(row) {
                var shipmentCell = '<div class="d-flex align-items-center">'
                    + '<a href="' + escapeHtml(row.edit_url) + '">' + escapeHtml(row.shipment_number) + '</a>'
                    + (row.has_open_irregularities ? '<i class="ti-alert text-danger ml-2" title="Open irregularities"></i>' : '')
                    + '</div>';

                var delDateStyle = row.del_overdue ? ' style="color: #ff5252; font-weight: 500;"' : '';

                return [
                    shipmentCell,
                    escapeHtml(row.customer),
                    escapeHtml(row.vessel),
                    escapeHtml(row.service),
                    escapeHtml(row.service_reference),
                    escapeHtml(row.consignee),
                    escapeHtml(row.departure),
                    escapeHtml(row.destination),
                    escapeHtml(row.etd),
                    escapeHtml(row.eta),
                    '<span' + delDateStyle + '>' + escapeHtml(row.del_date) + '</span>',
                    '<span class="' + escapeHtml(row.status_badge_class) + '" style="padding: 4px 8px; font-weight: 500;">' + escapeHtml(row.status) + '</span>',
                    '<span class="reminder-sent-date" data-shipment-id="' + escapeHtml(row.id) + '">' + escapeHtml(row.last_reminder_sent) + '</span>',
                    '<button type="button" class="btn btn-outline-teal py-1 px-2 send-reminder-btn" style="font-size: 11px; height: 26px; border-color: #ddd; background: #fff;"'
                        + ' data-shipment-id="' + escapeHtml(row.id) + '"'
                        + ' data-preview-url="' + escapeHtml(row.preview_url) + '"'
                        + ' data-record-url="' + escapeHtml(row.record_url) + '"'
                        + ' data-eml-url="' + escapeHtml(row.eml_url) + '"'
                        + ' data-eml-filename="' + escapeHtml(row.eml_filename) + '">Send reminder</button>'
                ];
            }

            function clearTableRows() {
                table.clear().draw(false);
                setTimeout(adjustCostTableLayout, 50);
            }

            function fetchFilteredShipments() {
                var filters = getActiveFilters();

                if (!hasActiveFilters(filters)) {
                    if (searchRequest && searchRequest.readyState !== 4) {
                        searchRequest.abort();
                    }
                    clearTableRows();
                    return;
                }

                if (searchRequest && searchRequest.readyState !== 4) {
                    searchRequest.abort();
                }

                searchRequest = $.ajax({
                    url: searchUrl,
                    method: 'GET',
                    data: filters,
                    dataType: 'json'
                }).done(function(response) {
                    var rows = (response && response.data) ? response.data : [];
                    table.clear();

                    rows.forEach(function(row) {
                        table.row.add(buildRowHtml(row));
                    });

                    table.draw(false);
                    setTimeout(adjustCostTableLayout, 50);
                }).fail(function(xhr) {
                    if (xhr.statusText === 'abort') {
                        return;
                    }
                    clearTableRows();
                });
            }

            function scheduleFetch() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(fetchFilteredShipments, 300);
            }

            $('#filter-shipment-no, #filter-port-destination').on('keyup', scheduleFetch);
            $('#filter-customer, #filter-vessel, #filter-account-manager, #filter-created-by').on('change', scheduleFetch);

            $('.clear-filters').on('click', function(e) {
                e.preventDefault();
                clearTimeout(searchTimer);
                $('.select2').val(null).trigger('change.select2');
                $('.filter-input:not(select)').val('');
                clearTableRows();
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

                window.location.href = 'mailto:?' + params.join('&');
            }

            function downloadReminderEml(emlUrl, filename) {
                var link = document.createElement('a');
                link.href = emlUrl;
                link.download = filename || 'pre-alert-reminder.eml';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            $(document).on('click', '.send-reminder-btn', function() {
                var $btn = $(this);
                var previewUrl = $btn.data('preview-url');
                var recordUrl = $btn.data('record-url');
                var emlUrl = $btn.data('eml-url');
                var emlFilename = $btn.data('eml-filename');
                var shipmentId = $btn.data('shipment-id');

                if (!previewUrl || !recordUrl) {
                    return;
                }

                $btn.prop('disabled', true);

                $.getJSON(previewUrl)
                    .done(function(preview) {
                        openReminderMailto(preview);
                        if (emlUrl) {
                            downloadReminderEml(emlUrl, emlFilename);
                        }

                        $.ajax({
                            url: recordUrl,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            }
                        }).done(function(response) {
                            var sentAt = response && response.sent_at ? response.sent_at : '';
                            if (sentAt) {
                                $('.reminder-sent-date[data-shipment-id="' + shipmentId + '"]').text(sentAt);
                            }
                        }).always(function() {
                            $btn.prop('disabled', false);
                        });
                    })
                    .fail(function() {
                        $btn.prop('disabled', false);
                        alert('Unable to prepare reminder email.');
                    });
            });
        });
    </script>
@endsection
