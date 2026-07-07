@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">
    <!-- Bootstrap Multiselect css -->
    <link rel="stylesheet"
        href="{{ asset('files/bower_components/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}" />
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/dist/css/select2.min.css') }}" />
    <style>
        .table-other-companies {
            width: 100%;
            border-collapse: collapse;
        }

        .table-other-companies th {
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: 600;
            color: #1b5e6f;
            border-bottom: 1px solid #eee;
            border-right: 1px solid #eee;
            background: #f8fafd;
        }

        .table-other-companies td {
            padding: 8px 10px;
            font-size: 11px;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            border-right: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .table-other-companies tr:hover td {
            background-color: #f9fafb;
        }

        .filter-label {
            font-size: 10px;
            font-weight: 600;
            margin-bottom: 2px;
            display: block;
        }

        .filter-input {
            height: 28px;
            font-size: 11px;
            border-radius: 2px;
            border: 1px solid #ced4da;
            padding: 4px 8px;
        }

        .clear-filters {
            font-size: 11px;
            color: #3b82f6;
            text-decoration: none;
            cursor: pointer;
        }

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 28px !important;
            font-size: 11px !important;
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
            border-radius: 2px !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            padding-left: 8px !important;
            padding-right: 20px !important;
            color: #495057 !important;
            background-color: transparent !important;
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 26px !important;
            top: 1px !important;
        }

        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }

        .main-body .page-wrapper {
            padding: 5px !important;
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
                            <!-- Page-body start -->
                            <div class="page-body">
                                <!-- Base Style - Compact start -->
                                <div class="card" style="border-radius: 0; box-shadow: none; border: 1px solid #eef2f7;">
                                    <div class="card-block mt-5" style="padding: 15px;">
                                        <div class="d-flex justify-content-between align-items-end mb-3">
                                            <div class="d-flex align-items-end"
                                                style="gap: 15px; flex-grow: 1; flex-wrap: wrap;">
                                                <div style="width: 150px;">
                                                    <span class="filter-label">Search</span>
                                                    <input type="text" class="form-control filter-input"
                                                        placeholder="type here">
                                                </div>
                                                <div style="width: 180px;">
                                                    <span class="filter-label">Responsible offices</span>
                                                    <select class="form-control filter-input select2">
                                                        <option>Click here</option>
                                                        <option>SIN</option>
                                                        <option>RTM</option>
                                                        <option>ATH</option>
                                                        <option>OSA</option>
                                                        <option>OSL</option>
                                                        <option>HAM</option>
                                                        <option>HOU</option>
                                                    </select>
                                                </div>
                                                <div class="d-flex align-items-center"
                                                    style="border: 1px solid #ced4da; padding: 0 8px; border-radius: 2px; height: 28px;">
                                                    <span style="font-size: 11px; margin-right: 8px;">Hide inactive</span>
                                                    <input type="checkbox" checked style="width: 14px; height: 14px;">
                                                </div>
                                                <div style="width: 150px;">
                                                    <span class="filter-label">Account managers</span>
                                                    <select class="form-control filter-input select2">
                                                        <option>Click here</option>
                                                    </select>
                                                </div>
                                                <div style="width: 150px;">
                                                    <span class="filter-label">Sales managers</span>
                                                    <select class="form-control filter-input select2">
                                                        <option>Click here</option>
                                                    </select>
                                                </div>
                                                <div style="width: 120px;">
                                                    <span class="filter-label">Country</span>
                                                    <select class="form-control filter-input select2">
                                                        <option>Click here</option>
                                                    </select>
                                                </div>
                                                <div style="padding-bottom: 5px;">
                                                    <a class="clear-filters">Clear filters</a>
                                                </div>
                                            </div>
                                            <div class="d-flex" style="gap: 5px;">
                                                <button class="btn btn-outline-secondary"
                                                    style="height: 28px; padding: 0 10px; border-radius: 2px;"><i
                                                        class="ti-download"></i></button>
                                                <a href="{{ route('customers.create') }}" class="btn btn-outline-primary"
                                                    style="height: 28px; font-size: 11px; padding: 5 12px; border-radius: 2px;">Add
                                                    customer</a>
                                            </div>
                                        </div>

                                        <div class="dt-responsive">
                                            <table id="offices-table" class="table-other-companies">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 25%;">Customer name</th>
                                                        <th style="width: 15%;">Main contact</th>
                                                        <th style="width: 15%;">Responsible office</th>
                                                        <th style="width: 20%;">Account manager</th>
                                                        <th style="width: 10%;">Status</th>
                                                        <th style="width: 5%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($customers as $customer)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('customers.edit', $customer->id) }}"
                                                                    style="color: #3b82f6; font-weight: 500;">
                                                                    {{ $customer->customer_name }}
                                                                </a>
                                                            </td>
                                                            <td>{{ $customer->responsible->accountManager->office->phone_number ?? '—' }}</td>
                                                            <td>{{ $customer->responsible->accountManager->office->office_short_name ?? '—' }}</td>
                                                            <td>{{ $customer->responsible->accountManager->name ?? '—' }}</td>
                                                            <td>
                                                                <span class="label label-success">Active</span>
                                                            </td>
                                                            <td class="text-right">
                                                                <a href="{{ route('customers.edit', $customer->id) }}"
                                                                    style="color: #ccc;"><i class="ti-pencil"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
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
    <script type="text/javascript"
        src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/modernizr/feature-detects/css-scrollbars.js') }}"></script>

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
    <script
        src="{{ asset('files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{--
    <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript"
        src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2 for standard filters
            $('.select2').select2({
                placeholder: "Click here",
                allowClear: true
            });

            $('#offices-table').DataTable({
                "lengthChange": false,
                "pageLength": 25,
                "responsive": false,
                "searching": false,
                "ordering": true,
                "autoWidth": false,
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });
        });
    </script>
@endsection