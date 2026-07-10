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
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/sweetalert.css') }}" />
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

        /* Custom Pagination Styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2em 0.5em !important;
            margin-left: 2px !important;
            border: 1px solid #eee !important;
            border-radius: 4px !important;
            font-size: 11px !important;
            background: #fff !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #3b82f6 !important;
            color: white !important;
            border-color: #3b82f6 !important;
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 11px !important;
            color: #666 !important;
            padding-top: 10px !important;
        }
        .dataTables_wrapper .dataTables_paginate {
            padding-top: 10px !important;
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
                                                    <div class="d-flex align-items-end" style="gap: 15px;">
                                                        <div style="width: 200px;">
                                                            <span class="filter-label">Search</span>
                                                            <input type="text" class="form-control filter-input" placeholder="type here">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center" style="gap: 8px;">
                                                        <a href="#" style="border: 1px solid #ced4da; padding: 4px 10px; border-radius: 2px; color: #666; font-size: 14px;">
                                                            <i class="ti-download"></i>
                                                        </a>
                                                        <a class="btn btn-primary" href="{{ route('suppliers.create') }}" 
                                                           style="font-size: 11px; padding: 6px 15px; border-radius: 2px; background: #fff; color: #1b5e6f; border: 1px solid #1b5e6f; font-weight: 600;">
                                                           Add supplier
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="dt-responsive">
                                                    <table id="suppliers-table" class="table-other-companies">
                                                        <thead>
                                                            <tr>
                                                                <th>Supplier name</th>
                                                                <th>Address</th>
                                                                <th>City</th>
                                                                <th>Country</th>
                                                                <th>Phone number</th>
                                                                <th>Email</th>
                                                                <th style="width: 50px;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($suppliers as $supplier)
                                                                <tr>
                                                                    <td>{{ $supplier->supplier_name }}</td>
                                                                    <td style="font-size: 10px; color: #555;">{{ $supplier->supplier_address }}</td>
                                                                    <td>{{ $supplier->city }}</td>
                                                                    <td>
                                                                        @if($supplier->country && $supplier->country->flag_url)
                                                                            <img src="{{ $supplier->country->flag_url }}" width="16" height="12" alt="{{ $supplier->country->name }}" style="margin-right: 5px; vertical-align: middle; border: 1px solid #eee;">
                                                                             {{ $supplier->country->name }}
                                                                        @elseif($supplier->country)
                                                                            {{ $supplier->country->name }}
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $supplier->phone_number }}</td>
                                                                    <td>{{ $supplier->email }}</td>
                                                                    <td class="text-right">
                                                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" style="color: #666; font-size: 14px; margin-right: 5px;">
                                                                            <i class="ti-pencil"></i>
                                                                        </a>
                                                                        <a href="javascript:void(0)" class="delete-supplier" data-id="{{ $supplier->id }}" data-name="{{ $supplier->supplier_name }}" style="font-size: 14px; color: #ff5252;" title="Delete supplier">
                                                                            <i class="ti-trash"></i>
                                                                        </a>
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
    <script type="text/javascript" src="{{ asset('files/assets/js/sweetalert.js') }}"></script>

    <script>
        $(document).ready(function() {
            var table = $('#suppliers-table').DataTable({
                "lengthChange": false,
                "pageLength": 25,
                "responsive": true,
                "searching": true,
                "ordering": true,
                "dom": 'rt<"d-flex justify-content-between align-items-center"ip>',
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    },
                    "info": "Showing 1 to _MENU_ of _TOTAL_ entries"
                }
            });

            // Bind search input
            $('.filter-input').keyup(function() {
                table.search($(this).val()).draw();
            });

            // Delete supplier AJAX
            $(document).on('click', '.delete-supplier', function() {
                var id = $(this).data('id');
                var name = $(this).data('name') || 'this supplier';
                var $row = $(this).closest('tr');

                swal({
                    title: 'Delete supplier?',
                    text: 'Are you sure you want to delete "' + name + '"? This can be restored later.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    closeOnConfirm: false,
                    closeOnCancel: true,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (!isConfirm) {
                        return;
                    }

                    $.ajax({
                        url: '{{ url('/Suppliers') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                swal({
                                    title: 'Deleted',
                                    text: response.message || 'Supplier deleted successfully.',
                                    type: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                $row.fadeOut(400, function() {
                                    table.row($row).remove().draw(false);
                                });
                            } else {
                                swal('Error', response.message || 'Error deleting supplier.', 'error');
                            }
                        },
                        error: function(xhr) {
                            var message = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred while deleting the supplier.';
                            swal('Error', message, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endsection
