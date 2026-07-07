@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->
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
        /* Filter Bar Styling */
        .filter-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            padding: 10px 15px;
            background: #fff;
            border-bottom: 1px solid #eee;
            flex-wrap: nowrap;
            overflow-x: auto;
        }
        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .filter-label-custom {
            font-size: 11px;
            color: #666;
            margin-bottom: 0;
            white-space: nowrap;
        }
        .filter-input-custom {
            height: 30px;
            border: 1px solid #d1d5db;
            border-radius: 2px;
            width: 100%;
            padding: 4px 8px;
            font-size: 12px;
        }
        .input-group-custom {
            display: flex;
        }
        .btn-input-append {
            height: 30px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-left: none;
            padding: 0 8px;
            border-radius: 0 2px 2px 0;
            cursor: pointer;
            color: #999;
            display: flex;
            align-items: center;
            font-size: 12px;
        }
        .filter-input-custom.has-append {
            border-radius: 2px 0 0 2px;
        }
        .filter-checkbox-group {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 5px;
            white-space: nowrap;
        }
        .custom-checkbox-box {
            width: 16px;
            height: 16px;
            background: #1b5e6f;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 10px;
        }
        .filter-checkbox-group label {
            font-size: 12px;
            color: #333;
            margin-bottom: 0;
            cursor: pointer;
        }
        .btn-clear-filters {
            font-size: 12px;
            color: #01a9ac;
            text-decoration: none;
            margin-bottom: 5px;
            white-space: nowrap;
        }
        .btn-add-other {
            height: 30px;
            padding: 0 15px;
            background: #fff;
            color: #1b5e6f;
            border: 1px solid #1b5e6f;
            border-radius: 3px;
            font-size: 12px;
            margin-left: auto;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            text-decoration: none;
            white-space: nowrap;
        }

        /* Table Styling */
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
        .company-link {
            color: #01a9ac;
            text-decoration: none;
        }
        .company-link:hover {
            text-decoration: underline;
        }
        .country-flag {
            width: 18px;
            margin-right: 6px;
            vertical-align: text-top;
        }
        .action-icons {
            display: flex;
            gap: 10px;
            color: #ccc;
            justify-content: center;
        }
        .action-icons i {
            cursor: pointer;
            font-size: 14px;
        }
        .action-icons i:hover {
            color: #666;
        }

        /* Layout Adjustments */
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
                                    <!-- Page-header start -->
                                    <div class="page-header">
                                        
                                    </div>
                                    <!-- Page-header end -->

                                    <!-- Page-body start -->
                                    <div class="page-body">
                                        <div class="card">
                                            <!-- Filter Bar -->
                                            <div class="filter-row">
                                                <div class="filter-item" style="width: 250px;">
                                                    <label class="filter-label-custom">Name</label>
                                                    <div class="input-group-custom">
                                                        <input type="text" class="filter-input-custom has-append" placeholder="type here">
                                                        <button class="btn-input-append"><i class="ti-more-alt"></i></button>
                                                    </div>
                                                </div>
                                                <div class="filter-item" style="width: 100px;">
                                                    <label class="filter-label-custom">Code</label>
                                                    <input type="text" class="filter-input-custom" placeholder="type here">
                                                </div>
                                                <div class="filter-item" style="width: 220px;">
                                                    <label class="filter-label-custom">Address</label>
                                                    <input type="text" class="filter-input-custom" placeholder="type here">
                                                </div>
                                                <div class="filter-item" style="width: 120px;">
                                                    <label class="filter-label-custom">City</label>
                                                    <input type="text" class="filter-input-custom" placeholder="type here">
                                                </div>
                                                <div class="filter-item" style="width: 150px;">
                                                    <label class="filter-label-custom">Country</label>
                                                    <select class="filter-input-custom">
                                                        <option>Click here</option>
                                                    </select>
                                                </div>
                                                <div class="filter-checkbox-group">
                                                    <div class="custom-checkbox-box">
                                                        <i class="ti-check"></i>
                                                    </div>
                                                    <label>Hide inactive</label>
                                                </div>
                                                <a href="#" class="btn-clear-filters">Clear filters</a>
                                                <a href="{{ route('other-companies.create') }}" class="btn-add-other">Add other company</a>
                                            </div>

                                            <!-- Data Table -->
                                            <div class="table-responsive" style="padding: 10px;">
                                                <table id="other-companies-table" class="table-other-companies">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 20%;">Name</th>
                                                            <th style="width: 5%;">Code</th>
                                                            <th style="width: 10%;">Type</th>
                                                            <th style="width: 18%;">Address</th>
                                                            <th style="width: 8%;">City</th>
                                                            <th style="width: 12%;">Country</th>
                                                            <th style="width: 10%;">Phone number</th>
                                                            <th style="width: 12%;">Email</th>
                                                            <th style="width: 3%;">Status</th>
                                                            <th style="width: 2%;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($companies as $company)
                                                        <tr>
                                                            <td><a href="{{ route('other-companies.edit', $company->id) }}" class="company-link">{{ $company->company_name }}</a></td>
                                                            <td>{{ $company->code }}</td>
                                                            <td>{{ $company->company_type }}</td>
                                                            <td>{{ Str::limit($company->street_address, 25) }}</td>
                                                            <td>{{ $company->city }}</td>
                                                            <td>
                                                                @if($company->country)
                                                                    @if($company->country->flag_url)
                                                                        <img src="{{ $company->country->flag_url }}" class="country-flag" alt="">
                                                                    @endif
                                                                    {{ $company->country->name }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $company->phone_number }}</td>
                                                            <td>
                                                                @if($company->email)
                                                                    <a href="mailto:{{ $company->email }}" class="company-link">{{ Str::limit($company->email, 28) }}</a>
                                                                @endif
                                                            </td>
                                                            <td><span class="ti-check" style="color: green;"></span></td>
                                                            <td>
                                                                <div class="action-icons">
                                                                    <a href="{{ route('other-companies.edit', $company->id) }}"><i class="ti-pencil"></i></a>
                                                                    <form action="{{ route('other-companies.destroy', $company->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this company?')">
                                                                        @csrf @method('DELETE')
                                                                        <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;"><i class="ti-trash"></i></button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="10" style="text-align:center; padding: 30px; color: #999;">No companies found. <a href="{{ route('other-companies.create') }}">Add one</a>.</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
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
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/i18next/i18next.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-xhr-backend/i18nextXHRBackend.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('files/bower_components/i18next-browser-languagedetector/i18nextBrowserLanguageDetector.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-i18next/jquery-i18next.min.js') }}"></script>
    <!-- Custom js -->
    {{-- <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script> --}}
    <!-- Bootstrap Multiselect js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
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
                allowClear: true
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
                    {val: 'Office Name', id: 'col-Office-Name'},
                    {val: 'Short Name', id: 'col-Short-Name'},
                    {val: 'City', id: 'col-City'},
                    {val: 'Country', id: 'col-Country'},
                    {val: 'Phone', id: 'col-Phone'},
                    {val: 'Email', id: 'col-Email'}
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

            $('#other-companies-table').DataTable({
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
